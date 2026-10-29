<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use App\Models\DeliveryModel;

require_once __DIR__ . '/support/MarketplaceFixtures.php';

/**
 * Real-time delivery propagation over the unified polling bus.
 *
 * Covers the delivery change signal + payload added to the tenant / admin /
 * customer `realtimeCheck()` endpoints, the `getRealtimeDeliverySnapshot()`
 * shape (including GPS-churn immunity), the fast-path `changed: false`
 * round-trip, and the print-request delivery join.
 */
class RealtimeDeliveryTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use MarketplaceFixtures;

    private const FX_ORDER    = 9000021;
    private const FX_PRINTING = 9000021;

    protected function tearDown(): void
    {
        $this->fxCleanup();
        \Config\Services::resetSingle('renderer');
        parent::tearDown();
    }

    private function asTenant(int $userId, int $shopId): self
    {
        $hash = csrf_hash();
        $_COOKIE['csrf_cookie_name'] = $hash;

        return $this->withSession([
            'user_id'    => $userId,
            'user_name'  => 'Test Tenant',
            'user_email' => 'tenant@test.com',
            'user_role'  => 'shop_owner',
            'role'       => 'shop_owner',
            'shop_id'    => $shopId,
            'isLoggedIn' => true,
        ])->withHeaders(['X-CSRF-TOKEN' => $hash]);
    }

    private function asAdmin(int $userId): self
    {
        $hash = csrf_hash();
        $_COOKIE['csrf_cookie_name'] = $hash;

        return $this->withSession([
            'user_id'    => $userId,
            'user_name'  => 'Super Admin',
            'user_email' => 'admin@blax.com',
            'user_role'  => 'admin',
            'role'       => 'admin',
            'isLoggedIn' => true,
        ])->withHeaders(['X-CSRF-TOKEN' => $hash]);
    }

    private function asCustomer(int $userId): self
    {
        $hash = csrf_hash();
        $_COOKIE['csrf_cookie_name'] = $hash;

        return $this->withSession([
            'user_id'    => $userId,
            'user_name'  => 'Test Customer',
            'user_email' => 'customer@test.com',
            'user_role'  => 'customer',
            'role'       => 'customer',
            'isLoggedIn' => true,
        ])->withHeaders(['X-CSRF-TOKEN' => $hash]);
    }

    /**
     * Seed a shop, its owner, a customer and one active shipped delivery.
     *
     * @return array{shopId: int, ownerId: int, customerId: int, tracking: string, deliveryId: int}
     */
    private function seedActiveDelivery(string $status = 'shipped'): array
    {
        $base     = $this->fxSeedBase();
        $shopId   = (int) $base['shop']['id'];
        $ownerId  = (int) $base['owner']['id'];
        $custId   = (int) $base['customer']['id'];

        $this->fxSeedOrder(self::FX_ORDER, $status === 'delivered' ? 'delivered' : 'shipped');

        $tracking   = 'TRK-RT-' . substr((string) uniqid(), -8);
        $deliveryId = $this->fxSeedDelivery([
            'deliverable_type'    => 'order',
            'deliverable_id'      => self::FX_ORDER,
            'tracking_id'         => $tracking,
            'destination_address' => 'Cannery Site, Polomolok',
            'current_lat'         => 6.2200,
            'current_lng'         => 125.0700,
            'location_updated_at' => date('Y-m-d H:i:s'),
            'status'              => $status,
        ]);

        return [
            'shopId'     => $shopId,
            'ownerId'    => $ownerId,
            'customerId' => $custId,
            'tracking'   => $tracking,
            'deliveryId' => $deliveryId,
        ];
    }

    private function findRow(array $rows, int $deliveryId): ?array
    {
        foreach ($rows as $row) {
            if ((int) ($row['id'] ?? 0) === $deliveryId) {
                return $row;
            }
        }
        return null;
    }

    private function assertHasKeys(array $expected, array $payload, string $context = ''): void
    {
        foreach ($expected as $key) {
            $this->assertArrayHasKey($key, $payload, "Missing key '{$key}'" . ($context !== '' ? " in {$context}" : ''));
        }
    }

    /**
     * The snapshot feeding all three realtimeCheck endpoints: scoped rows,
     * an md5 id+status signature (immune to GPS churn) and the high-water id.
     */
    public function testRealtimeDeliverySnapshotShapeAndGpsChurnImmunity(): void
    {
        $fx = $this->seedActiveDelivery();
        $deliveryModel = new DeliveryModel();

        $snap = $deliveryModel->getRealtimeDeliverySnapshot($fx['shopId'], null);
        $this->assertHasKeys(['sig', 'max_id', 'deliveries'], $snap, 'snapshot');
        $this->assertMatchesRegularExpression('/^[0-9a-f]{32}$/', $snap['sig'], 'Delivery signal must be an md5 hash.');
        $this->assertGreaterThanOrEqual($fx['deliveryId'], (int) $snap['max_id']);

        $row = $this->findRow($snap['deliveries'], $fx['deliveryId']);
        $this->assertNotNull($row, 'Shop-scoped snapshot must include the active shipment.');
        foreach (['id', 'tracking_id', 'ref_number', 'status', 'deliverable_type', 'destination_address', 'current_lat', 'current_lng', 'customer_name', 'shop_name'] as $key) {
            $this->assertArrayHasKey($key, $row, "Delivery payload must expose '{$key}'.");
        }
        $this->assertSame($fx['tracking'], $row['tracking_id']);
        $this->assertSame('ORD-FX-' . self::FX_ORDER, $row['ref_number']);
        $this->assertSame('shipped', $row['status']);

        // Rider position updates must NOT change the signal (no poll thrash while GPS broadcasts).
        $beforeSig = $snap['sig'];
        $deliveryModel->update($fx['deliveryId'], [
            'current_lat'         => 6.2311,
            'current_lng'         => 125.0811,
            'location_updated_at' => date('Y-m-d H:i:s'),
        ]);

        $afterMove = $deliveryModel->getRealtimeDeliverySnapshot($fx['shopId'], null);
        $this->assertSame($beforeSig, $afterMove['sig'], 'GPS-only updates must not change the delivery signal.');
        $movedRow = $this->findRow($afterMove['deliveries'], $fx['deliveryId']);
        $this->assertEqualsWithDelta(6.2311, (float) $movedRow['current_lat'], 0.0001, 'The freshest rider position must still reach the map listeners.');

        // A status transition MUST change the signal so queues and maps reconcile.
        $deliveryModel->update($fx['deliveryId'], ['status' => 'in_transit']);
        $afterStatus = $deliveryModel->getRealtimeDeliverySnapshot($fx['shopId'], null);
        $this->assertNotSame($beforeSig, $afterStatus['sig'], 'Status transitions must change the delivery signal.');
        $this->assertSame('in_transit', $this->findRow($afterStatus['deliveries'], $fx['deliveryId'])['status']);
    }

    public function testTenantRealtimeCheckCarriesDeliveryPayloadAndFastPaths(): void
    {
        $fx = $this->seedActiveDelivery();

        // Slow path: full payload including the delivery signal and active rows.
        $res1 = $this->asTenant($fx['ownerId'], $fx['shopId'])
            ->get('tenant/realtime/check?last_order_id=0&last_printing_id=0&last_notif_id=0&last_max_delivery_id=0');
        $res1->assertStatus(200);
        $json1 = json_decode($res1->getJSON(), true);

        $this->assertTrue($json1['success']);
        $this->assertTrue($json1['changed']);
        $this->assertHasKeys(['delivery_sig', 'max_delivery_id', 'new_deliveries', 'active_deliveries'], $json1, 'tenant payload');
        $this->assertMatchesRegularExpression('/^[0-9a-f]{32}$/', $json1['delivery_sig']);
        $this->assertNotNull($this->findRow($json1['active_deliveries'], $fx['deliveryId']), 'Active deliveries must reach the tenant deliveries page.');
        $this->assertContains($fx['deliveryId'], array_column($json1['new_deliveries'], 'id'), 'Deliveries newer than last_max_delivery_id must surface as new (toast).');

        // Fast path: everything unchanged, including the delivery signal.
        $qs = sprintf(
            'last_order_id=%d&last_printing_id=%d&last_notif_id=%d&last_delivery_sig=%s',
            (int) $json1['max_order_id'],
            (int) $json1['max_printing_id'],
            (int) $json1['max_notif_id'],
            urlencode((string) $json1['delivery_sig'])
        );
        $res2 = $this->asTenant($fx['ownerId'], $fx['shopId'])->get('tenant/realtime/check?' . $qs);
        $res2->assertStatus(200);
        $json2 = json_decode($res2->getJSON(), true);
        $this->assertTrue($json2['success']);
        $this->assertFalse($json2['changed'], 'An unchanged delivery signal must keep the heartbeat on the fast path.');
        $this->assertSame($json1['delivery_sig'], $json2['delivery_sig']);
        $this->assertSame((int) $json1['max_delivery_id'], (int) $json2['max_delivery_id']);

        // A stale delivery signal must break the fast path on its own.
        $res3 = $this->asTenant($fx['ownerId'], $fx['shopId'])->get(
            'tenant/realtime/check?last_order_id=' . (int) $json1['max_order_id']
            . '&last_printing_id=' . (int) $json1['max_printing_id']
            . '&last_notif_id=' . (int) $json1['max_notif_id']
            . '&last_delivery_sig=stale-signal'
        );
        $res3->assertStatus(200);
        $json3 = json_decode($res3->getJSON(), true);
        $this->assertTrue($json3['changed'], 'Delivery changes alone must force a full payload.');
        $this->assertNotNull($this->findRow($json3['active_deliveries'], $fx['deliveryId']));
    }

    public function testAdminRealtimeCheckCarriesPlatformWideDeliveries(): void
    {
        $fx = $this->seedActiveDelivery();

        $res1 = $this->asAdmin(self::FX_USER_ADMIN)->get('admin/realtime/check?last_order_id=0&last_printing_id=0&last_notif_id=0&last_max_delivery_id=0');
        $res1->assertStatus(200);
        $json1 = json_decode($res1->getJSON(), true);

        $this->assertTrue($json1['success']);
        $this->assertHasKeys(['delivery_sig', 'max_delivery_id', 'new_deliveries', 'active_deliveries'], $json1, 'admin payload');

        $row = $this->findRow($json1['active_deliveries'], $fx['deliveryId']);
        $this->assertNotNull($row, 'The admin live-dispatch map must receive the shipment.');
        $this->assertNotEmpty($row['shop_name'], 'Platform-wide payloads must identify the owning shop.');
        $this->assertContains($fx['deliveryId'], array_column($json1['new_deliveries'], 'id'));

        $qs = sprintf(
            'last_order_id=%d&last_printing_id=%d&last_notif_id=%d&last_delivery_sig=%s',
            (int) $json1['max_order_id'],
            (int) $json1['max_printing_id'],
            (int) $json1['max_notif_id'],
            urlencode((string) $json1['delivery_sig'])
        );
        $res2 = $this->asAdmin(self::FX_USER_ADMIN)->get('admin/realtime/check?' . $qs);
        $res2->assertStatus(200);
        $json2 = json_decode($res2->getJSON(), true);
        $this->assertFalse($json2['changed']);
        $this->assertSame($json1['delivery_sig'], $json2['delivery_sig']);
    }

    /**
     * Customer tracking pages must see shipped -> in_transit without a refresh.
     */
    public function testCustomerRealtimeCheckReflectsDeliveryTransitions(): void
    {
        $fx = $this->seedActiveDelivery();

        $res1 = $this->asCustomer($fx['customerId'])->get('customer/realtime/check');
        $res1->assertStatus(200);
        $json1 = json_decode($res1->getJSON(), true);

        $this->assertTrue($json1['success']);
        $this->assertTrue($json1['changed']);
        $this->assertHasKeys(['delivery_sig', 'max_delivery_id', 'active_deliveries'], $json1, 'customer payload');
        $row = $this->findRow($json1['active_deliveries'], $fx['deliveryId']);
        $this->assertNotNull($row, 'The customer must receive their own active delivery.');
        $this->assertSame('shipped', $row['status']);

        // Fast path with every known signal.
        $qs = sprintf(
            'last_notif_id=%d&last_order_sig=%s&last_print_sig=%s&last_delivery_sig=%s',
            (int) $json1['max_notif_id'],
            urlencode((string) $json1['order_sig']),
            urlencode((string) $json1['print_sig']),
            urlencode((string) $json1['delivery_sig'])
        );
        $res2 = $this->asCustomer($fx['customerId'])->get('customer/realtime/check?' . $qs);
        $res2->assertStatus(200);
        $json2 = json_decode($res2->getJSON(), true);
        $this->assertFalse($json2['changed'], 'An unchanged delivery status must not wake the customer poll.');
        $this->assertSame($json1['delivery_sig'], $json2['delivery_sig']);

        // Courier marks the parcel in transit -> signal changes -> payload is re-sent.
        (new DeliveryModel())->update($fx['deliveryId'], ['status' => 'in_transit']);
        $res3 = $this->asCustomer($fx['customerId'])->get('customer/realtime/check?' . $qs);
        $res3->assertStatus(200);
        $json3 = json_decode($res3->getJSON(), true);
        $this->assertTrue($json3['changed'], 'A delivery transition must surface without a page refresh.');
        $this->assertNotSame($json1['delivery_sig'], $json3['delivery_sig']);
        $this->assertSame('in_transit', $this->findRow($json3['active_deliveries'], $fx['deliveryId'])['status']);
    }

    /**
     * Printing deliveries ride the same bus as product orders (deliverable_type join).
     */
    public function testTenantRealtimeCheckIncludesPrintingRequestDeliveries(): void
    {
        $base   = $this->fxSeedBase();
        $shopId = (int) $base['shop']['id'];

        $this->fxSeedPrintingRequest(self::FX_PRINTING, 'ready_for_delivery');
        $deliveryId = $this->fxSeedDelivery([
            'deliverable_type'    => 'printing_request',
            'deliverable_id'      => self::FX_PRINTING,
            'tracking_id'         => 'TRK-RT-PR-' . substr((string) uniqid(), -8),
            'destination_address' => 'Poblacion, Polomolok',
            'status'              => 'in_transit',
        ]);

        $res = $this->asTenant((int) $base['owner']['id'], $shopId)
            ->get('tenant/realtime/check?last_order_id=0&last_printing_id=0&last_notif_id=0&last_max_delivery_id=0');
        $res->assertStatus(200);
        $json = json_decode($res->getJSON(), true);

        $row = $this->findRow($json['active_deliveries'], $deliveryId);
        $this->assertNotNull($row, 'Printing deliveries must flow through the same realtime bus.');
        $this->assertSame('printing_request', $row['deliverable_type']);
        $this->assertSame('PR-FX-' . self::FX_PRINTING, $row['ref_number']);
    }

    /**
     * The workbench card builder needs fulfillment + money fields on the wire, otherwise a
     * doorstep job cannot be told apart from a pick-up job without a page refresh (which is
     * exactly how "only Ready for Pick-up shows up" looked from the tenant's side).
     */
    public function testTenantRealtimePrintingPayloadCarriesFulfillmentAndFee(): void
    {
        $base   = $this->fxSeedBase();
        $shopId = (int) $base['shop']['id'];

        $this->fxSeedPrintingRequest(self::FX_PRINTING, 'in_production', [
            'fulfillment_method' => 'delivery',
            'shipping_fee'       => 70.00,
            'distance_km'        => 5.80,
        ]);

        $res = $this->asTenant((int) $base['owner']['id'], $shopId)
            ->get('tenant/realtime/check?last_order_id=0&last_printing_id=0&last_notif_id=0&last_max_delivery_id=0');
        $res->assertStatus(200);
        $json = json_decode($res->getJSON(), true);

        $pr = $this->findRow($json['new_printing'], self::FX_PRINTING);
        $this->assertNotNull($pr, 'Printing requests must reach the tenant poll.');
        $this->assertHasKeys(
            ['fulfillment_method', 'shipping_fee', 'distance_km', 'status', 'request_number', 'customer_name'],
            $pr,
            'printing payload'
        );
        $this->assertSame('delivery', $pr['fulfillment_method'], 'The card must render "Mark In Transit", not "Ready for Pick-up".');
        $this->assertSame('in_production', $pr['status'], 'Status decides workbench eligibility; a stale default would mis-file the job.');
        $this->assertEqualsWithDelta(70.00, (float) $pr['shipping_fee'], 0.001);
        $this->assertEqualsWithDelta(5.80, (float) $pr['distance_km'], 0.001);
    }
}
