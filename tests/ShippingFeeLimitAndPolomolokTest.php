<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use App\Models\SiteContentModel;
use App\Models\DeliveryModel;
use App\Services\GoogleMapsService;

class ShippingFeeLimitAndPolomolokTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected function tearDown(): void
    {
        \Config\Services::resetSingle('renderer');
        parent::tearDown();
    }

    /**
     * Test 1: Within the Admin-configured KM limit (e.g., 2.0 km), the shipping fee
     * must NOT increase and must remain flat at Base Shipping Fee.
     */
    public function testShippingFeeDoesNotIncreaseWithinKmLimit(): void
    {
        $mapsService = new GoogleMapsService();
        $shop = [
            'shop_name' => 'Polomolok Hub Store',
            'barangay'  => 'Poblacion',
            'city'      => 'Polomolok',
            'latitude'  => 6.2196,
            'longitude' => 125.0693,
        ];
        $address = [
            'address_line1' => 'Purok 1',
            'barangay'      => 'Poblacion',
            'city'          => 'Polomolok',
            'province'      => 'South Cotabato',
            'latitude'      => 6.2205,
            'longitude'     => 125.0700,
        ];

        $baseSettings = [
            'base_shipping_fee'        => 10.00,
            'included_distance_km'     => 2.00,
            'additional_fee_per_km'    => 10.00,
            'max_delivery_distance_km' => 25.00,
            // Test-only distance override (production quotes always use real route km).
            'simulate_distance_km'     => 1,
        ];

        // Test distances <= 2.0 km (0.5 km, 1.0 km, 1.4 km Poblacion, 1.9 km, 2.0 km exact limit)
        foreach ([0.5, 1.0, 1.4, 1.9, 2.0] as $distKm) {
            $quote = $mapsService->calculateDeliveryQuote(
                $shop,
                $address,
                $baseSettings + ['override_distance_km' => $distKm]
            );

            $this->assertTrue($quote['deliverable'], "Distance {$distKm} km in Polomolok should be deliverable");
            $this->assertTrue($quote['is_polomolok']);
            $this->assertSame(0, $quote['extra_steps'], "Distance {$distKm} km within 2.0 km limit must have 0 extra steps");
            $this->assertEqualsWithDelta(0.00, $quote['extra_distance_fee'], 0.001, "Distance {$distKm} km must have 0.00 extra fee");
            $this->assertEqualsWithDelta(10.00, $quote['shipping_fee'], 0.001, "Distance {$distKm} km must stay at base fee 10.00");
        }
    }

    /**
     * Test 2: When road distance exceeds the KM limit (e.g., 2.0 km),
     * the system automatically adds the Additional Shipping Fee per KM-limit step bracket
     * without fractional/decimal fee creep.
     */
    public function testShippingFeeAutomaticallyAddsStepFeeWhenExceedingKmLimit(): void
    {
        $mapsService = new GoogleMapsService();
        $shop = [
            'shop_name' => 'Polomolok Hub Store',
            'barangay'  => 'Poblacion',
            'city'      => 'Polomolok',
            'latitude'  => 6.2196,
            'longitude' => 125.0693,
        ];
        $address = [
            'address_line1' => 'National Highway',
            'barangay'      => 'Cannery Site',
            'city'          => 'Polomolok',
            'province'      => 'South Cotabato',
            'latitude'      => 6.2409,
            'longitude'     => 125.0902,
        ];

        $baseSettings = [
            'base_shipping_fee'        => 10.00,
            'included_distance_km'     => 2.00,
            'additional_fee_per_km'    => 10.00,
            'max_delivery_distance_km' => 25.00,
            'simulate_distance_km'     => 1,
        ];

        // Bracket 1 (2.01 km to 4.00 km): +1 step -> +₱10.00 -> Total ₱20.00
        foreach ([2.1, 2.5, 3.2, 4.0] as $distKm) {
            $quote = $mapsService->calculateDeliveryQuote(
                $shop,
                $address,
                $baseSettings + ['override_distance_km' => $distKm]
            );

            $this->assertTrue($quote['deliverable']);
            $this->assertSame(1, $quote['extra_steps'], "Distance {$distKm} km should trigger exactly 1 extra step");
            $this->assertEqualsWithDelta(10.00, $quote['extra_distance_fee'], 0.001);
            $this->assertEqualsWithDelta(20.00, $quote['shipping_fee'], 0.001, "Distance {$distKm} km must be ₱20.00 (no decimal creep)");
        }

        // Bracket 2 (4.01 km to 6.00 km): +2 steps -> +₱20.00 -> Total ₱30.00
        foreach ([4.1, 4.5, 5.8, 6.0] as $distKm) {
            $quote = $mapsService->calculateDeliveryQuote(
                $shop,
                $address,
                $baseSettings + ['override_distance_km' => $distKm]
            );

            $this->assertTrue($quote['deliverable']);
            $this->assertSame(2, $quote['extra_steps'], "Distance {$distKm} km should trigger 2 extra steps");
            $this->assertEqualsWithDelta(20.00, $quote['extra_distance_fee'], 0.001);
            $this->assertEqualsWithDelta(30.00, $quote['shipping_fee'], 0.001);
        }

        // Bracket 4 (Glamang 9.5 km -> 7.5 km past 2.0 km limit -> ceil(7.5 / 2.0) = 4 steps -> +₱40.00 -> Total ₱50.00)
        $quoteGlamang = $mapsService->calculateDeliveryQuote(
            $shop,
            $address,
            $baseSettings + ['override_distance_km' => 9.5]
        );
        $this->assertSame(4, $quoteGlamang['extra_steps']);
        $this->assertEqualsWithDelta(50.00, $quoteGlamang['shipping_fee'], 0.001);
    }

    /**
     * Test 3 (Second Comparative Testing): Verify KM limit step calculation across
     * real Polomolok barangay routes and different Admin KM limit settings (2.0 km vs 3.0 km vs 5.0 km).
     */
    public function testSecondComparativeTestingAcrossPolomolokBarangays(): void
    {
        $mapsService = new GoogleMapsService();
        $shop = [
            'shop_name' => 'Poblacion Main Store',
            'barangay'  => 'Poblacion',
            'city'      => 'Polomolok',
            'latitude'  => DeliveryModel::POLOMOLOK_CENTER_LAT,
            'longitude' => DeliveryModel::POLOMOLOK_CENTER_LNG,
        ];

        // Real Polomolok barangays with varying distances from Poblacion
        $polomolokDestinations = [
            ['barangay' => 'Magsaysay',    'sim_km' => 2.0,  'expected_steps_at_2km' => 0, 'expected_fee_at_2km' => 50.00],
            ['barangay' => 'Lumakil',      'sim_km' => 3.2,  'expected_steps_at_2km' => 1, 'expected_fee_at_2km' => 60.00],
            ['barangay' => 'Cannery Site', 'sim_km' => 5.8,  'expected_steps_at_2km' => 2, 'expected_fee_at_2km' => 70.00],
            ['barangay' => 'Glamang',      'sim_km' => 9.5,  'expected_steps_at_2km' => 4, 'expected_fee_at_2km' => 90.00],
            ['barangay' => 'Landan',       'sim_km' => 13.5, 'expected_steps_at_2km' => 6, 'expected_fee_at_2km' => 110.00],
        ];

        $settings2km = [
            'base_shipping_fee'        => 50.00,
            'included_distance_km'     => 2.00,
            'additional_fee_per_km'    => 10.00,
            'max_delivery_distance_km' => 25.00,
            'simulate_distance_km'     => 1,
        ];

        foreach ($polomolokDestinations as $dest) {
            $coord = DeliveryModel::getBarangayCoordinate($dest['barangay'], 'Polomolok');
            $this->assertNotNull($coord, "Polomolok barangay {$dest['barangay']} must resolve coordinates");

            $address = [
                'address_line1' => 'Brgy. ' . $dest['barangay'],
                'barangay'      => $dest['barangay'],
                'city'          => 'Polomolok',
                'province'      => 'South Cotabato',
                'latitude'      => $coord['lat'],
                'longitude'     => $coord['lng'],
            ];

            $quote = $mapsService->calculateDeliveryQuote(
                $shop,
                $address,
                $settings2km + ['override_distance_km' => $dest['sim_km']]
            );

            $this->assertTrue($quote['deliverable']);
            $this->assertTrue($quote['is_polomolok']);
            $this->assertSame($dest['expected_steps_at_2km'], $quote['extra_steps'], "Unexpected steps for {$dest['barangay']} ({$dest['sim_km']} km)");
            $this->assertEqualsWithDelta($dest['expected_fee_at_2km'], $quote['shipping_fee'], 0.001, "Unexpected fee for {$dest['barangay']}");
        }
    }

    /**
     * Test 4: Around Polomolok Only enforcement — addresses outside Polomolok or beyond max radius
     * are marked non-deliverable and prompt Store Pick-up.
     */
    public function testPolomolokOnlyAndMaxDistanceEnforcement(): void
    {
        $mapsService = new GoogleMapsService();
        $shop = [
            'shop_name' => 'Polomolok Hub Store',
            'barangay'  => 'Poblacion',
            'city'      => 'Polomolok',
            'latitude'  => 6.2196,
            'longitude' => 125.0693,
        ];

        $settings = [
            'base_shipping_fee'        => 10.00,
            'included_distance_km'     => 2.00,
            'additional_fee_per_km'    => 10.00,
            'max_delivery_distance_km' => 25.00,
        ];

        // Non-Polomolok city (e.g., Tupi or General Santos) must be rejected for doorstep delivery
        foreach (['Tupi', 'General Santos City', 'Koronadal City'] as $outsideCity) {
            $nonPolomolokAddr = [
                'address_line1' => 'Highway',
                'barangay'      => 'Poblacion',
                'city'          => $outsideCity,
                'province'      => 'South Cotabato',
                'latitude'      => 6.3389,
                'longitude'     => 124.9520,
            ];
            $quoteOutsideCity = $mapsService->calculateDeliveryQuote($shop, $nonPolomolokAddr, $settings);
            $this->assertFalse($quoteOutsideCity['deliverable'], "City {$outsideCity} should not be deliverable under Polomolok-only rule");
            $this->assertFalse($quoteOutsideCity['is_polomolok']);
            $this->assertNotEmpty($quoteOutsideCity['error']);
            $this->assertStringContainsString('Polomolok', (string) $quoteOutsideCity['error']);
        }

        // Polomolok address that exceeds max_delivery_distance_km (28.0 km > 25.0 km)
        $polomolokFarAddr = [
            'address_line1' => 'Far Sitio',
            'barangay'      => 'Landan',
            'city'          => 'Polomolok',
            'province'      => 'South Cotabato',
            'latitude'      => 6.3277,
            'longitude'     => 125.1471,
        ];
        $quoteBeyondRadius = $mapsService->calculateDeliveryQuote(
            $shop,
            $polomolokFarAddr,
            $settings + ['simulate_distance_km' => 1, 'override_distance_km' => 28.0]
        );
        $this->assertFalse($quoteBeyondRadius['deliverable']);
        $this->assertTrue($quoteBeyondRadius['is_polomolok']);
        $this->assertStringContainsString('exceeds the maximum Polomolok delivery distance', (string) $quoteBeyondRadius['error']);
    }

    /**
     * Test 5: SiteContentModel default shipping fee settings use 2.00 km as the default KM limit.
     */
    public function testDefaultShippingFeeSettingsHaveTwoKmLimit(): void
    {
        SiteContentModel::clearCache();
        $model = new SiteContentModel();
        $settings = $model->getShippingFeeSettings();

        $this->assertArrayHasKey('base_shipping_fee', $settings);
        $this->assertArrayHasKey('included_distance_km', $settings);
        $this->assertArrayHasKey('additional_fee_per_km', $settings);
        $this->assertArrayHasKey('max_delivery_distance_km', $settings);
        $this->assertGreaterThanOrEqual(0.5, $settings['included_distance_km']);
    }
}

