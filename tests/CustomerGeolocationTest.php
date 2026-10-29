<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use App\Models\DeliveryModel;
use App\Services\GoogleMapsService;

require_once __DIR__ . '/support/MarketplaceFixtures.php';

/**
 * Customer geolocation accuracy: no more "fake" pins.
 *
 * The stored precise coordinate of the customer's address must win over a
 * barangay centroid, and the barangay centroid must win over the Polomolok
 * town-center snap. The town center is only ever a last resort, and is flagged
 * so it can never be mistaken for a real pin.
 */
class CustomerGeolocationTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use MarketplaceFixtures;

    private const FX_ORDER = 9000051;

    protected function tearDown(): void
    {
        $this->fxCleanup();
        \Config\Services::resetSingle('renderer');
        parent::tearDown();
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
        ])->withHeaders([
            'X-CSRF-TOKEN'       => $hash,
            'X-Requested-With'   => 'XMLHttpRequest',
            'Accept'             => 'application/json',
        ]);
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

    private function hasGoogleKey(): bool
    {
        return (new GoogleMapsService())->getApiKey() !== '';
    }

    /**
     * B3: the resolution contract, most precise source first.
     */
    public function testResolveEntityCoordinatesPrecedence(): void
    {
        $maps = new GoogleMapsService();

        // 1. The entity's own stored in-area coordinate always wins.
        $pinned = $maps->resolveEntityCoordinates([
            'barangay'  => 'Poblacion',
            'city'      => 'Polomolok',
            'latitude'  => 6.2401234,
            'longitude' => 125.0909876,
        ]);
        $this->assertSame('pinned', $pinned['source']);
        $this->assertEqualsWithDelta(6.2401234, (float) $pinned['lat'], 0.000001);
        $this->assertEqualsWithDelta(125.0909876, (float) $pinned['lng'], 0.000001);

        // 2. A stale/out-of-area stored coordinate is never trusted as a pin;
        //    the barangay centroid is used instead of snapping to the town center.
        $centroid = $maps->resolveEntityCoordinates([
            'barangay'  => 'Cannery Site',
            'city'      => 'Polomolok',
            'latitude'  => 14.5995,
            'longitude' => 120.9842,
        ]);
        $this->assertSame('barangay_centroid', $centroid['source']);
        $expected = DeliveryModel::getBarangayCoordinate('Cannery Site', 'Polomolok');
        $this->assertNotNull($expected);
        $this->assertEqualsWithDelta($expected['lat'], (float) $centroid['lat'], 0.0001);
        $isTownCenter = abs((float) $centroid['lat'] - DeliveryModel::POLOMOLOK_CENTER_LAT) < 0.0002
            && abs((float) $centroid['lng'] - DeliveryModel::POLOMOLOK_CENTER_LNG) < 0.0002;
        $this->assertFalse($isTownCenter, 'A resolvable barangay must never collapse onto the town center.');

        // 3. Barangay names embedded in free-text address fields also resolve.
        $fromText = $maps->resolveEntityCoordinates([
            'city'                => 'Polomolok',
            'destination_address' => 'Sitio Sabang, Landan, Polomolok, South Cotabato',
        ]);
        $this->assertSame('barangay_centroid', $fromText['source']);
        $landan = DeliveryModel::getBarangayCoordinate('Landan', 'Polomolok');
        $this->assertEqualsWithDelta($landan['lat'], (float) $fromText['lat'], 0.0001);

        // 4. Known city with an unmatched barangay -> that city's center, not Polomolok's.
        $cityCenter = $maps->resolveEntityCoordinates([
            'city'         => 'Tupi',
            'address_line1' => 'Karsada Mercado',
            'barangay'     => 'Not A Real Barangay',
        ]);
        $this->assertSame('city_center', $cityCenter['source']);
        $this->assertEqualsWithDelta(DeliveryModel::TUPI_CENTER_LAT, (float) $cityCenter['lat'], 0.0001);

        // 5. Strict last resort, clearly flagged by source.
        $lastResort = $maps->resolveEntityCoordinates([
            'city'          => 'Polomolok',
            'address_line1' => 'Nowhere Special',
            'barangay'      => 'Not A Real Barangay',
        ]);
        $this->assertSame('default_center', $lastResort['source']);
        $def = $maps->getDefaultCoordinates();
        $this->assertEqualsWithDelta($def['lat'], (float) $lastResort['lat'], 0.0001);
    }

    /**
     * B1: saving an address with an exact pin stores that pin verbatim.
     */
    public function testAddressSaveStoresThePrecisePostedPin(): void
    {
        $base = $this->fxSeedBase();

        $res = $this->asCustomer((int) $base['customer']['id'])->post('customer/addresses/save', [
            'label'          => 'Home',
            'recipient_name' => 'Juan Cruz',
            'phone'          => '09171234567',
            'address_line1'  => 'House 12, Purok 3, National Highway',
            'barangay'       => 'Cannery Site',
            'city'           => 'Polomolok',
            'latitude'       => '6.2431500',
            'longitude'      => '125.0882400',
            'place_id'       => 'FxPlaceId123',
            'is_default'     => '1',
        ]);
        $res->assertStatus(200);
        $payload = json_decode((string) $res->getJSON(), true);
        $this->assertTrue($payload['success'] ?? false, (string) ($payload['error'] ?? 'save failed'));

        $row = $this->latestAddress((int) $base['customer']['id']);
        $this->fxSeeded['shipping_addresses'][] = (int) $row['id'];

        $this->assertEqualsWithDelta(6.24315, (float) $row['latitude'], 0.00001, 'The posted street pin must be stored, not replaced by a centroid.');
        $this->assertEqualsWithDelta(125.08824, (float) $row['longitude'], 0.00001);
        $this->assertSame('FxPlaceId123', $row['place_id']);
        $this->assertNotEmpty($row['geocoded_at']);
        $this->assertTrue(DeliveryModel::isPolomolokCoordinate((float) $row['latitude'], (float) $row['longitude']));

        // The same pin then drives the map and the shipping quote.
        $maps    = new GoogleMapsService();
        $resolved = $maps->resolveEntityCoordinates($row);
        $this->assertSame('pinned', $resolved['source'], 'Maps must use the customer\'s stored coordinate.');

        $quote = $maps->calculateDeliveryQuote($base['shop'], $row, [
            'base_shipping_fee'        => 50.00,
            'included_distance_km'     => 2.00,
            'additional_fee_per_km'    => 10.00,
            'max_delivery_distance_km' => 25.00,
        ]);
        $this->assertSame('pinned', $quote['destination_coords']['source']);
        $this->assertTrue($quote['deliverable']);
    }

    /**
     * B1: a coordinate outside the service area is never trusted as a pin.
     */
    public function testAddressSaveRejectsForeignCoordinatePin(): void
    {
        $base = $this->fxSeedBase();

        $res = $this->asCustomer((int) $base['customer']['id'])->post('customer/addresses/save', [
            'label'          => 'Bogus Pin',
            'recipient_name' => 'Juan Cruz',
            'phone'          => '09171234567',
            'address_line1'  => 'Purok 1, national highway',
            'barangay'       => 'Landan',
            'city'           => 'Polomolok',
            'latitude'       => '14.5995000',
            'longitude'      => '120.9842000',
        ]);
        $res->assertStatus(200);
        $payload = json_decode((string) $res->getJSON(), true);
        $this->assertTrue($payload['success'] ?? false, (string) ($payload['error'] ?? 'save failed'));

        $row = $this->latestAddress((int) $base['customer']['id']);
        $this->fxSeeded['shipping_addresses'][] = (int) $row['id'];

        $this->assertNotNull($row['latitude'], 'A fallback coordinate must still be stored for the address.');
        $this->assertTrue(
            DeliveryModel::isPolomolokCoordinate((float) $row['latitude'], (float) $row['longitude']),
            'Manila coordinates must never be persisted as the customer pin.'
        );
        $this->assertNotEquals(14.5995, round((float) $row['latitude'], 4));

        if (!$this->hasGoogleKey()) {
            $centroid = DeliveryModel::getBarangayCoordinate('Landan', 'Polomolok');
            $this->assertNotNull($centroid);
            $this->assertEqualsWithDelta($centroid['lat'], (float) $row['latitude'], 0.0001, 'Offline, the barangay centroid is the documented last resort.');
            $this->assertNull($row['place_id'], 'A centroid fallback is not a real pin and must not look like one.');
        }
    }

    /**
     * B1: with no pin posted, the saved coordinate stays inside the service area
     * and is never the generic town center when the barangay is known.
     */
    public function testAddressSaveWithoutPinStaysOnRealBarangayGeometry(): void
    {
        $base = $this->fxSeedBase();

        $res = $this->asCustomer((int) $base['customer']['id'])->post('customer/addresses/save', [
            'label'          => 'House',
            'recipient_name' => 'Juan Cruz',
            'phone'          => '09171234567',
            'address_line1'  => 'Rillo Lane, Purok 4',
            'barangay'       => 'Glamang',
            'city'           => 'Polomolok',
        ]);
        $res->assertStatus(200);
        $payload = json_decode((string) $res->getJSON(), true);
        $this->assertTrue($payload['success'] ?? false, (string) ($payload['error'] ?? 'save failed'));

        $row = $this->latestAddress((int) $base['customer']['id']);
        $this->fxSeeded['shipping_addresses'][] = (int) $row['id'];

        $this->assertNotNull($row['latitude']);
        $this->assertTrue(DeliveryModel::isPolomolokCoordinate((float) $row['latitude'], (float) $row['longitude']));
        $this->assertNotEmpty($row['geocoded_at']);

        $isTownCenter = abs((float) $row['latitude'] - DeliveryModel::POLOMOLOK_CENTER_LAT) < 0.0002
            && abs((float) $row['longitude'] - DeliveryModel::POLOMOLOK_CENTER_LNG) < 0.0002;
        $this->assertFalse($isTownCenter, 'A known barangay must not silently snap onto the generic town center.');

        if (!$this->hasGoogleKey()) {
            $centroid = DeliveryModel::getBarangayCoordinate('Glamang', 'Polomolok');
            $this->assertNotNull($centroid);
            $this->assertEqualsWithDelta($centroid['lat'], (float) $row['latitude'], 0.0001);
            $this->assertNull($row['place_id'], 'A centroid fallback is not a real pin and must not look like one.');
        }
    }

    /**
     * B2: the tenant delivery map must pin the customer's stored address
     * coordinate (and cache it on the delivery), not a guessed location.
     */
    public function testDeliveryDetailUsesTheCustomersStoredCoordinate(): void
    {
        $base = $this->fxSeedBase();
        $this->fxSeedAddress(9000041, [
            'latitude'  => 6.2500123,
            'longitude' => 125.0800456,
            'place_id'  => 'FxStoredPin',
        ]);
        $this->fxSeedOrder(self::FX_ORDER, 'shipped', [
            'customer_id'         => (int) $base['customer']['id'],
            'shipping_address_id' => 9000041,
        ]);

        $deliveryId = $this->fxSeedDelivery([
            'deliverable_type'    => 'order',
            'deliverable_id'      => self::FX_ORDER,
            'destination_address' => 'Rillo Lane, Purok 4, Glamang, Polomolok',
            'status'              => 'shipped',
        ]);

        $res = $this->asTenant((int) $base['owner']['id'], (int) $base['shop']['id'])
            ->get('tenant/deliveries/' . $deliveryId);
        $res->assertStatus(200);
        $body = (string) $res->getBody();

        $this->assertStringContainsString('6.250012', $body, 'Destination marker must use the stored street pin.');
        $this->assertStringContainsString('125.080046', $body, 'Destination marker must use the stored street pin.');
        $this->assertStringNotContainsString('Address pending geocode', $body, 'A precise stored pin must not be labelled approximate.');

        $cached = $this->fxDeliveryRow($deliveryId);
        $this->assertEqualsWithDelta(6.2500123, (float) $cached['destination_latitude'], 0.00001, 'The resolved pin should be cached on the delivery row.');
        $this->assertEqualsWithDelta(125.0800456, (float) $cached['destination_longitude'], 0.00001);
    }

    /**
     * B2: when nothing can be geocoded, the map still renders a position but the
     * UI must flag it as pending geocode instead of faking precision.
     */
    public function testDeliveryDetailFlagsUnresolvableDestinationAsApproximate(): void
    {
        $base = $this->fxSeedBase();
        $this->fxSeedOrder(self::FX_ORDER, 'shipped', ['customer_id' => (int) $base['customer']['id']]);

        $deliveryId = $this->fxSeedDelivery([
            'deliverable_type'    => 'order',
            'deliverable_id'      => self::FX_ORDER,
            'destination_address' => '',
            'status'              => 'shipped',
        ]);

        $res = $this->asTenant((int) $base['owner']['id'], (int) $base['shop']['id'])
            ->get('tenant/deliveries/' . $deliveryId);
        $res->assertStatus(200);

        $this->assertStringContainsString(
            'Address pending geocode',
            (string) $res->getBody(),
            'Fallback positions must be visibly marked as approximate.'
        );
    }

    private function latestAddress(int $userId): array
    {
        $row = $this->fxDb()->table('shipping_addresses')
            ->where('user_id', $userId)
            ->orderBy('id', 'DESC')
            ->get()->getRowArray();
        $this->assertNotNull($row, 'The address must have been persisted.');

        return $row;
    }
}
