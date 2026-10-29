<?php

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\PrintingRequestModel;
use App\Models\SiteContentModel;
use App\Services\GoogleMapsService;

require_once __DIR__ . '/support/MarketplaceFixtures.php';

/**
 * Distance-based shipping quotes, including the new delivery printing-request
 * surface and the test-only simulated-distance gate.
 *
 * Quote contract: shipping_fee = base + ceil((km - included) / included) * perKm,
 * flat at base while within the included km limit, and real route km unless an
 * admin explicitly enables the simulated override.
 */
class ShippingFeeCalculationTest extends CIUnitTestCase
{
    use MarketplaceFixtures;

    private const PRINTING_ID       = 9000031;
    private const PRINTING_PICKUP   = 9000032;

    protected function tearDown(): void
    {
        $this->fxCleanup();
        SiteContentModel::clearCache();
        \Config\Services::resetSingle('renderer');
        parent::tearDown();
    }

    private function shop(array $overrides = []): array
    {
        return array_merge([
            'shop_name'  => 'FX Polomolok Print Hub',
            'barangay'   => 'Poblacion',
            'city'       => 'Polomolok',
            'address_line' => 'National Highway, Poblacion, Polomolok',
            'latitude'   => 6.2196,
            'longitude'  => 125.0693,
        ], $overrides);
    }

    private function address(array $overrides = []): array
    {
        return array_merge([
            'address_line1' => 'Purok 3, National Highway',
            'address_line2' => 'Cannery Site',
            'barangay'      => 'Cannery Site',
            'city'          => 'Polomolok',
            'province'      => 'South Cotabato',
            'latitude'      => 6.2415,
            'longitude'     => 125.0902,
        ], $overrides);
    }

    private function rates(array $overrides = []): array
    {
        return array_merge([
            'base_shipping_fee'        => 50.00,
            'included_distance_km'     => 2.00,
            'additional_fee_per_km'    => 10.00,
            'max_delivery_distance_km' => 25.00,
        ], $overrides);
    }

    /**
     * Expected fee computed independently from the documented bracket formula.
     */
    private function expectedFee(float $km, float $base, float $included, float $perKm): float
    {
        $extraKm = max(0.0, round($km - $included, 2));
        $steps   = $extraKm > 0.0 ? (int) ceil(($extraKm - 1e-6) / $included) : 0;

        return round($base + $steps * $perKm, 2);
    }

    /**
     * C4: a stored override value alone must never fake the distance.
     */
    public function testRealRouteIsUsedWhenSimulateFlagIsAbsentOrOff(): void
    {
        $maps = new GoogleMapsService();

        foreach ([null, 0, '0', false, ''] as $offValue) {
            $quote = $maps->calculateDeliveryQuote(
                $this->shop(),
                $this->address(),
                $this->rates(['simulate_distance_km' => $offValue, 'override_distance_km' => 9.5])
            );
            $this->assertNotSame(
                'simulated_distance',
                $quote['route_source'],
                'Override values must be ignored unless simulate_distance_km is explicitly enabled (' . var_export($offValue, true) . ').'
            );
            $this->assertGreaterThan(0.0, (float) $quote['distance_km'], 'Real quotes must resolve a positive route distance.');
        }
    }

    public function testSimulatedOverrideAppliesOnlyWhenExplicitlyEnabled(): void
    {
        $maps = new GoogleMapsService();

        foreach ([1, '1', true, 'true'] as $onValue) {
            $quote = $maps->calculateDeliveryQuote(
                $this->shop(),
                $this->address(),
                $this->rates(['simulate_distance_km' => $onValue, 'override_distance_km' => 9.5])
            );
            $this->assertSame('simulated_distance', $quote['route_source'], 'Simulated km must apply when the test toggle is on (' . var_export($onValue, true) . ').');
            $this->assertEqualsWithDelta(9.5, (float) $quote['distance_km'], 0.001);
        }
    }

    /**
     * The bracket formula holds for every tested distance, with no decimal creep.
     */
    public function testFeeFollowsBasePlusCeiledKmStepBracket(): void
    {
        $maps  = new GoogleMapsService();
        $rates = $this->rates(['base_shipping_fee' => 35.00, 'included_distance_km' => 2.0, 'additional_fee_per_km' => 12.00]);

        foreach ([0.4, 1.0, 2.0, 2.1, 3.5, 4.1, 6.0, 9.5, 13.4, 24.9] as $km) {
            $quote = $maps->calculateDeliveryQuote(
                $this->shop(),
                $this->address(),
                $rates + ['simulate_distance_km' => 1, 'override_distance_km' => $km]
            );

            $this->assertTrue($quote['deliverable'], "{$km} km inside Polomolok must stay deliverable.");
            $this->assertEqualsWithDelta(
                $this->expectedFee($km, 35.00, 2.0, 12.00),
                (float) $quote['shipping_fee'],
                0.001,
                "Fee mismatch at {$km} km."
            );
            $this->assertSame(
                $km <= 2.0 ? 0 : (int) ceil((round($km - 2.0, 2) - 1e-6) / 2.0),
                (int) $quote['extra_steps'],
                "Step bracket mismatch at {$km} km."
            );
        }
    }

    /**
     * Non-Polomolok addresses and addresses past the max radius are undeliverable.
     */
    public function testUndeliverableAddressesAreFlagged(): void
    {
        $maps  = new GoogleMapsService();
        $rates = $this->rates();

        foreach (['Tupi', 'General Santos City', 'Koronadal City'] as $city) {
            $quote = $maps->calculateDeliveryQuote(
                $this->shop(),
                $this->address(['city' => $city, 'latitude' => 6.3389, 'longitude' => 124.9520]),
                $rates + ['simulate_distance_km' => 1, 'override_distance_km' => 3.0]
            );
            $this->assertFalse($quote['deliverable'], "{$city} is outside the Polomolok service area.");
            $this->assertFalse($quote['is_polomolok']);
            $this->assertStringContainsString('Polomolok', (string) $quote['error']);
        }

        $beyondMax = $maps->calculateDeliveryQuote(
            $this->shop(),
            $this->address(),
            $rates + ['simulate_distance_km' => 1, 'override_distance_km' => 28.0]
        );
        $this->assertFalse($beyondMax['deliverable']);
        $this->assertTrue($beyondMax['is_polomolok']);
        $this->assertStringContainsString('exceeds the maximum Polomolok delivery distance', (string) $beyondMax['error']);
    }

    /**
     * The quote must honour the customer's stored precise pin instead of a
     * barangay centroid (and never silently fall back to the town center).
     */
    public function testQuotePrefersStoredCoordinateOverCentroid(): void
    {
        $maps = new GoogleMapsService();

        $pinned = $maps->calculateDeliveryQuote(
            $this->shop(),
            $this->address(['latitude' => 6.2500123, 'longitude' => 125.0800456]),
            $this->rates()
        );
        $this->assertSame('pinned', $pinned['destination_coords']['source'], 'A stored in-area coordinate must win over centroid guessing.');
        $this->assertEqualsWithDelta(6.2500123, (float) $pinned['destination_coords']['lat'], 0.000001);

        $centroid = $maps->calculateDeliveryQuote(
            $this->shop(),
            $this->address(['latitude' => null, 'longitude' => null, 'barangay' => 'Landan', 'address_line1' => 'Sitio Sabang', 'address_line2' => 'Landan']),
            $this->rates()
        );
        $this->assertSame('barangay_centroid', $centroid['destination_coords']['source'], 'Without a stored pin the barangay centroid is used before any town-center snap.');
    }

    /**
     * C1: the distance-based shipping columns exist and round-trip on printing requests.
     */
    public function testDeliveryPrintingRequestStoresQuoteFields(): void
    {
        $this->fxRequireSchema(['printing_requests']);
        $db = $this->fxDb();
        $this->assertTrue($db->fieldExists('shipping_fee', 'printing_requests'), 'Migration must add printing_requests.shipping_fee.');
        $this->assertTrue($db->fieldExists('distance_km', 'printing_requests'));
        $this->assertTrue($db->fieldExists('shipping_address_id', 'printing_requests'));

        $base = $this->fxSeedBase();
        $this->fxSeedAddress(9000041);

        $maps  = new GoogleMapsService();
        $quote = $maps->calculateDeliveryQuote(
            $base['shop'],
            $this->fxSeededRow('shipping_addresses', 9000041),
            $this->rates(['simulate_distance_km' => 1, 'override_distance_km' => 5.8])
        );
        $this->assertTrue($quote['deliverable']);

        // Mirrors Customer::submitPrintingRequest(): total folds in the quoted fee.
        $printSubtotal = round((5 * 1.00) * 2, 2);
        $shippingFee   = round((float) $quote['shipping_fee'], 2);
        $total         = round($printSubtotal + $shippingFee, 2);

        $model = new PrintingRequestModel();
        $model->skipValidation(true);
        $id = $model->insert([
            'request_number'     => 'PR-SHIP-' . self::PRINTING_ID,
            'customer_id'        => (int) $base['customer']['id'],
            'shop_id'            => (int) $base['shop']['id'],
            'file_name'          => 'shipping_quote.pdf',
            'file_url'           => 'https://example.test/shipping_quote.pdf',
            'document_type'      => 'pdf',
            'page_count'         => 5,
            'paper_size'         => 'A4',
            'color_mode'         => 'bw',
            'copies'             => 2,
            'binding_option'     => 'none',
            'fulfillment_method' => 'delivery',
            'total_price'        => $total,
            'down_payment'       => round($total * 0.5, 2),
            'shipping_fee'       => $shippingFee,
            'distance_km'        => round((float) $quote['distance_km'], 2),
            'shipping_address_id' => 9000041,
            'status'             => 'new',
            'created_at'         => date('Y-m-d H:i:s'),
        ]);
        $this->assertIsNumeric($id);
        $this->assertGreaterThan(0, (int) $id, 'Printing request insert must return an id.');
        $this->fxSeeded['printing_requests'][] = (int) $id;

        $row = $model->find($id);
        $this->assertEqualsWithDelta(70.00, (float) $row['shipping_fee'], 0.001, '5.8 km at 2 km steps: 50 + 2 * 10.');
        $this->assertEqualsWithDelta(5.8, (float) $row['distance_km'], 0.001);
        $this->assertSame(9000041, (int) $row['shipping_address_id']);
        $this->assertEqualsWithDelta($printSubtotal + 70.00, (float) $row['total_price'], 0.001, 'total_price must carry the quoted shipping fee.');
    }

    /**
     * Store pick-up printing requests pay no shipping.
     */
    public function testPickupPrintingRequestHasZeroShipping(): void
    {
        $base = $this->fxSeedBase();

        $id = $this->fxSeedPrintingRequest(self::PRINTING_PICKUP, 'new', [
            'fulfillment_method' => 'pickup',
            'total_price'        => 15.00,
        ]);

        $row = $this->fxSeededRow('printing_requests', $id);
        $this->assertSame('pickup', $row['fulfillment_method']);
        $this->assertEqualsWithDelta(0.00, (float) $row['shipping_fee'], 0.001, 'Pick-up orders default to zero shipping.');
        $this->assertNull($row['distance_km']);
        $this->assertNull($row['shipping_address_id']);
        $this->assertEqualsWithDelta(15.00, (float) $row['total_price'], 0.001);
    }

    /**
     * Distance credibility guard: when shop and destination resolve to the exact same
     * point (the shape of real rows 2161/2162, where a barangay-only address shared the
     * shop's municipal coordinate), a 0.00 km quote is an UNKNOWN distance dressed up as
     * "zero blocks away". It must be flagged rather than presented as a confident result.
     */
    public function testIdenticalOriginAndDestinationAreFlaggedAsApproximate(): void
    {
        $maps = new GoogleMapsService();

        $collapsed = $maps->calculateDeliveryQuote(
            $this->shop(['latitude' => 6.2214549, 'longitude' => 125.0630767]),
            $this->address(['latitude' => 6.2214549, 'longitude' => 125.0630767, 'address_line1' => 'Malabarbas', 'address_line2' => '', 'barangay' => 'Malabarbas']),
            $this->rates()
        );

        $this->assertTrue($collapsed['distance_approximate'], 'Identical origin/destination coordinates must be flagged as an unmeasured distance.');
        $this->assertNotNull($collapsed['accuracy_notice'], 'An unmeasured distance must carry an explanation for the customer.');
        $this->assertStringContainsString('base shipping fee', (string) $collapsed['accuracy_notice']);
        $this->assertLessThan(50, (int) $collapsed['distance_meters']);
        $this->assertNotSame('0.0 km', $collapsed['distance_text'], 'A fake 0 km must not be shown as a measured distance.');
        // Flagging is informational only: it must not silently change what the customer pays.
        $this->assertEqualsWithDelta(
            $this->expectedFee((float) $collapsed['distance_km'], 50.00, 2.00, 10.00),
            (float) $collapsed['shipping_fee'],
            0.001,
            'The guard must keep the fee formula intact rather than inventing a surcharge.'
        );

        // Positive controls: genuinely separated points are never flagged.
        $distinct = $maps->calculateDeliveryQuote(
            $this->shop(['latitude' => 6.2214549, 'longitude' => 125.0630767]),
            $this->address(['latitude' => 6.2314549, 'longitude' => 125.0630767]),
            $this->rates()
        );
        $this->assertFalse($distinct['distance_approximate'], 'Points ~1.1 km apart are a real measurement.');
        $this->assertNull($distinct['accuracy_notice']);
        $this->assertStringContainsString('km', $distinct['distance_text']);

        $sameButFar = $maps->calculateDeliveryQuote(
            $this->shop(),
            $this->address(),
            $this->rates(['simulate_distance_km' => 1, 'override_distance_km' => 9.5])
        );
        $this->assertFalse($sameButFar['distance_approximate'], 'A simulated distance is not the same failure mode.');
    }

    /**
     * C4 persistence: the toggle is off by default and survives an admin save
     * as an explicit '0'/'1' flag (never implied by a stored override value).
     */
    public function testShippingSettingsKeepSimulateToggleDisabledByDefault(): void
    {
        $this->fxRequireSchema(['site_contents']);
        $db = $this->fxDb();

        // Snapshot then remove any pre-existing shipping keys so defaults are observable.
        $existing = $db->table('site_contents')
            ->where('page', 'platform')
            ->like('content_key', 'shipping_', 'after')
            ->get()->getResultArray();
        $backup = [];
        foreach ($existing as $row) {
            $backup[] = $row;
            $db->table('site_contents')->where('id', $row['id'])->delete();
        }
        SiteContentModel::clearCache();

        try {
            $model = new SiteContentModel();
            $defaults = $model->getShippingFeeSettings();
            $this->assertArrayHasKey('simulate_distance_km', $defaults);
            $this->assertEmpty($defaults['simulate_distance_km'], 'Live quotes must default to real route km.');

            $model->saveShippingFeeSettings([
                'base_shipping_fee'        => 40.00,
                'included_distance_km'     => 3.00,
                'additional_fee_per_km'    => 15.00,
                'max_delivery_distance_km' => 20.00,
                'simulate_distance_km'     => 1,
                'override_distance_km'     => 7.5,
            ]);
            SiteContentModel::clearCache();

            $on = $model->getShippingFeeSettings();
            $this->assertEqualsWithDelta(7.5, (float) $on['override_distance_km'], 0.001);
            $this->assertNotEmpty($on['simulate_distance_km']);

            // Turning the toggle off keeps the stored value but must not fake distances.
            $model->saveShippingFeeSettings([
                'base_shipping_fee'        => 40.00,
                'included_distance_km'     => 3.00,
                'additional_fee_per_km'    => 15.00,
                'max_delivery_distance_km' => 20.00,
                'simulate_distance_km'     => 0,
                'override_distance_km'     => 7.5,
            ]);
            SiteContentModel::clearCache();

            $off = $model->getShippingFeeSettings();
            $this->assertEmpty($off['simulate_distance_km'], 'The flag must persist as disabled ("0"), not truthy "0.00".');

            $maps  = new GoogleMapsService();
            $quote = $maps->calculateDeliveryQuote($this->shop(), $this->address(), $off);
            $this->assertNotSame('simulated_distance', $quote['route_source'], 'A saved override with the toggle off must never fake the distance.');
        } finally {
            $db->table('site_contents')->where('page', 'platform')->like('content_key', 'shipping_', 'after')->delete();
            foreach ($backup as $row) {
                unset($row['id']);
                $db->table('site_contents')->insert($row);
            }
            SiteContentModel::clearCache();
        }
    }
}
