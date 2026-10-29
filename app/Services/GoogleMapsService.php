<?php

namespace App\Services;

use Config\GoogleMaps;

class GoogleMapsService
{
    private string $apiKey;
    private GoogleMaps $config;

    public function __construct(?string $apiKey = null)
    {
        $this->config = config('GoogleMaps') ?? new GoogleMaps();
        $this->apiKey = $apiKey ?: $this->config->apiKey;
    }

    /**
     * Get API key
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * Geocode an address string to lat/lng coordinates using Google Geocoding API.
     *
     * @param string $addressText Full or partial address text
     * @return array{lat: float, lng: float, place_id: string|null, formatted_address: string|null}|null
     */
    public function geocodeAddress(string $addressText): ?array
    {
        $addressText = trim($addressText);
        if ($addressText === '') {
            return null;
        }

        if (empty($this->apiKey)) {
            $centroid = \App\Models\DeliveryModel::getBarangayCoordinate($addressText);
            if ($centroid) {
                return [
                    'lat'               => $centroid['lat'],
                    'lng'               => $centroid['lng'],
                    'place_id'          => null,
                    'formatted_address' => $addressText . ', South Cotabato, Philippines',
                ];
            }
            return null;
        }

        // Add South Cotabato, Philippines bias if not already present
        $searchAddress = $addressText;
        if (!preg_match('/polomolok|tupi/i', $searchAddress)) {
            $searchAddress .= ', Polomolok, South Cotabato';
        }
        if (!preg_match('/philippines/i', $searchAddress)) {
            $searchAddress .= ', Philippines';
        }

        $boundsParam = sprintf(
            '%F,%F|%F,%F',
            $this->config->bounds['south'],
            $this->config->bounds['west'],
            $this->config->bounds['north'],
            $this->config->bounds['east']
        );

        $params = [
            'address' => $searchAddress,
            'key'     => $this->apiKey,
            'region'  => $this->config->region,
            'bounds'  => $boundsParam,
        ];

        $url = 'https://maps.googleapis.com/maps/api/geocode/json?' . http_build_query($params);

        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 8);
            $response = curl_exec($ch);
            $err = curl_error($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($err || $httpCode !== 200 || !$response) {
                $centroid = \App\Models\DeliveryModel::getBarangayCoordinate($addressText);
                if ($centroid) {
                    return [
                        'lat'               => $centroid['lat'],
                        'lng'               => $centroid['lng'],
                        'place_id'          => null,
                        'formatted_address' => $addressText,
                    ];
                }
                return null;
            }

            $data = json_decode($response, true);
            if (!is_array($data) || ($data['status'] ?? '') !== 'OK' || empty($data['results'])) {
                $centroid = \App\Models\DeliveryModel::getBarangayCoordinate($addressText);
                if ($centroid) {
                    return [
                        'lat'               => $centroid['lat'],
                        'lng'               => $centroid['lng'],
                        'place_id'          => null,
                        'formatted_address' => $addressText,
                    ];
                }
                return null;
            }

            $result = $data['results'][0];
            $location = $result['geometry']['location'] ?? null;

            if (!$location || !isset($location['lat'], $location['lng'])) {
                return null;
            }

            return [
                'lat'               => (float) $location['lat'],
                'lng'               => (float) $location['lng'],
                'place_id'          => $result['place_id'] ?? null,
                'formatted_address' => $result['formatted_address'] ?? null,
            ];
        } catch (\Throwable $e) {
            log_message('error', '[GoogleMapsService] Geocoding exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Compute a real road route between origin and destination.
     * Tries Google Routes API first if apiKey exists, then falls back to OSRM road engine.
     *
     * @param array{lat: float, lng: float} $origin
     * @param array{lat: float, lng: float} $destination
     * @param string $travelMode "DRIVE" or "TWO_WHEELER"
     * @return array{encodedPolyline: string, points: array, distanceMeters: int, duration: string, durationMinutes: int}|null
     */
    public function computeRoute(array $origin, array $destination, string $travelMode = 'DRIVE'): ?array
    {
        $originLat = (float) ($origin['lat'] ?? $origin['latitude'] ?? 0);
        $originLng = (float) ($origin['lng'] ?? $origin['longitude'] ?? 0);
        $destLat   = (float) ($destination['lat'] ?? $destination['latitude'] ?? 0);
        $destLng   = (float) ($destination['lng'] ?? $destination['longitude'] ?? 0);

        if ($originLat == 0 || $originLng == 0 || $destLat == 0 || $destLng == 0) {
            return null;
        }

        // Check route cache first (key rounded to 3 decimal places ≈ ~111m precision)
        $cacheKey = sprintf(
            'route_%s_%.3f_%.3f_%.3f_%.3f',
            strtolower($travelMode),
            $originLat,
            $originLng,
            $destLat,
            $destLng
        );

        $cached = cache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        // 1. If Google Maps API Key is configured, attempt Google Routes API
        if (!empty($this->apiKey)) {
            $googleResult = $this->queryGoogleRoutes($originLat, $originLng, $destLat, $destLng, $travelMode);
            if ($googleResult !== null) {
                cache()->save($cacheKey, $googleResult, 86400); // Cache for 24 hours
                return $googleResult;
            }
        }

        // 2. Fallback to OSRM (public demo server — not for heavy production use)
        log_message('warning', '[GoogleMapsService] Falling back to OSRM public demo server for route computation. Set GOOGLE_MAPS_API_KEY for production use.');

        $osrmResult = $this->queryOsrmRoutes($originLat, $originLng, $destLat, $destLng);
        if ($osrmResult !== null) {
            cache()->save($cacheKey, $osrmResult, 86400); // Cache for 24 hours
            return $osrmResult;
        }

        return null;
    }

    private function queryGoogleRoutes(float $oLat, float $oLng, float $dLat, float $dLng, string $travelMode): ?array
    {
        $url = 'https://routes.googleapis.com/directions/v2:computeRoutes';

        $payload = [
            'origin' => [
                'location' => [
                    'latLng' => [
                        'latitude'  => $oLat,
                        'longitude' => $oLng,
                    ],
                ],
            ],
            'destination' => [
                'location' => [
                    'latLng' => [
                        'latitude'  => $dLat,
                        'longitude' => $dLng,
                    ],
                ],
            ],
            'travelMode'        => $travelMode,
            'routingPreference' => 'TRAFFIC_UNAWARE',
        ];

        $headers = [
            'Content-Type: application/json',
            'X-Goog-Api-Key: ' . $this->apiKey,
            'X-Goog-FieldMask: routes.duration,routes.distanceMeters,routes.polyline.encodedPolyline',
        ];

        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_TIMEOUT, 4);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                $data = json_decode($response, true);
                if (!empty($data['routes'][0])) {
                    $firstRoute = $data['routes'][0];
                    $poly = $firstRoute['polyline']['encodedPolyline'] ?? '';
                    $dist = (int) ($firstRoute['distanceMeters'] ?? 0);
                    $durStr = (string) ($firstRoute['duration'] ?? '0s');
                    $durSec = (int) preg_replace('/[^0-9]/', '', $durStr);
                    $distKm = round($dist / 1000, 1);
                    $distText = ($distKm >= 1 ? $distKm . ' km' : $dist . ' m');
                    $durMins = max(1, (int) round($durSec / 60));
                    $durText = $durMins >= 60 ? floor($durMins / 60) . ' hr ' . ($durMins % 60) . ' mins' : $durMins . ' mins';

                    return [
                        'encodedPolyline'  => $poly,
                        'points'           => self::decodePolyline($poly),
                        'distanceMeters'   => $dist,
                        'distance_meters'  => $dist,
                        'distance_text'    => $distText,
                        'duration'         => $durStr,
                        'duration_seconds' => $durSec,
                        'duration_text'    => $durText,
                        'durationMinutes'  => $durMins,
                        'source'           => 'google',
                    ];
                }
            }
        } catch (\Throwable $e) {
            log_message('warning', '[GoogleMapsService] Google Routes API failed: ' . $e->getMessage());
        }

        return null;
    }

    private function queryOsrmRoutes(float $oLat, float $oLng, float $dLat, float $dLng): ?array
    {
        $url = sprintf(
            'https://router.project-osrm.org/route/v1/driving/%F,%F;%F,%F?overview=full&geometries=geojson',
            $oLng,
            $oLat,
            $dLng,
            $dLat
        );

        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            curl_setopt($ch, CURLOPT_USERAGENT, 'BlaxMarketplace/1.0');
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                $data = json_decode($response, true);
                if (($data['code'] ?? '') === 'Ok' && !empty($data['routes'][0])) {
                    $route = $data['routes'][0];
                    $coords = $route['geometry']['coordinates'] ?? [];
                    
                    $points = [];
                    foreach ($coords as $c) {
                        if (isset($c[0], $c[1])) {
                            $points[] = [
                                'lat' => (float) $c[1],
                                'lng' => (float) $c[0],
                            ];
                        }
                    }

                    if (empty($points)) {
                        $points = [
                            ['lat' => $oLat, 'lng' => $oLng],
                            ['lat' => $dLat, 'lng' => $dLng],
                        ];
                    }

                    $dist = (int) round($route['distance'] ?? 0);
                    $durSec = (int) round($route['duration'] ?? 0);
                    $distKm = round($dist / 1000, 1);
                    $distText = ($distKm >= 1 ? $distKm . ' km' : $dist . ' m');
                    $durMins = max(1, (int) round($durSec / 60));
                    $durText = $durMins >= 60 ? floor($durMins / 60) . ' hr ' . ($durMins % 60) . ' mins' : $durMins . ' mins';

                    return [
                        'encodedPolyline'  => self::encodePolyline($points),
                        'points'           => $points,
                        'distanceMeters'   => $dist,
                        'distance_meters'  => $dist,
                        'distance_text'    => $distText,
                        'duration'         => $durSec . 's',
                        'duration_seconds' => $durSec,
                        'duration_text'    => $durText,
                        'durationMinutes'  => $durMins,
                        'source'           => 'osrm',
                    ];
                }
            }
        } catch (\Throwable $e) {
            log_message('error', '[GoogleMapsService] OSRM Routes exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Encode array of ['lat' => ..., 'lng' => ...] points to Google encoded polyline format.
     */
    public static function encodePolyline(array $points): string
    {
        $encoded = '';
        $lastLat = 0;
        $lastLng = 0;
        foreach ($points as $p) {
            $lat = (int) round($p['lat'] * 1e5);
            $lng = (int) round($p['lng'] * 1e5);
            $dLat = $lat - $lastLat;
            $dLng = $lng - $lastLng;
            $lastLat = $lat;
            $lastLng = $lng;

            $encoded .= self::encodePolylineValue($dLat) . self::encodePolylineValue($dLng);
        }
        return $encoded;
    }

    private static function encodePolylineValue(int $val): string
    {
        $val = $val < 0 ? ~($val << 1) : ($val << 1);
        $chunks = '';
        while ($val >= 0x20) {
            $chunks .= chr((0x20 | ($val & 0x1f)) + 63);
            $val >>= 5;
        }
        $chunks .= chr($val + 63);
        return $chunks;
    }

    /**
     * Decode Google encoded polyline string to array of ['lat' => ..., 'lng' => ...]
     */
    public static function decodePolyline(string $encoded): array
    {
        $len = strlen($encoded);
        $index = 0;
        $points = [];
        $lat = 0;
        $lng = 0;

        while ($index < $len) {
            $b = 0;
            $shift = 0;
            $result = 0;
            do {
                if ($index >= $len) break 2;
                $b = ord($encoded[$index++]) - 63;
                $result |= ($b & 0x1f) << $shift;
                $shift += 5;
            } while ($b >= 0x20);
            $dlat = (($result & 1) ? ~($result >> 1) : ($result >> 1));
            $lat += $dlat;

            $shift = 0;
            $result = 0;
            do {
                if ($index >= $len) break 2;
                $b = ord($encoded[$index++]) - 63;
                $result |= ($b & 0x1f) << $shift;
                $shift += 5;
            } while ($b >= 0x20);
            $dlng = (($result & 1) ? ~($result >> 1) : ($result >> 1));
            $lng += $dlng;

            $points[] = [
                'lat' => $lat * 1e-5,
                'lng' => $lng * 1e-5,
            ];
        }

        return $points;
    }

    /**
     * Helper to compose an address line from parts.
     */
    public function buildAddressString(array $parts): string
    {
        $filtered = array_filter(array_map('trim', $parts));
        return implode(', ', $filtered);
    }

    /**
     * Default coordinates for Polomolok town center
     */
    public function getDefaultCoordinates(): array
    {
        return [
            'lat' => $this->config->defaultLat,
            'lng' => $this->config->defaultLng,
        ];
    }

    /**
     * Calculate great-circle distance in meters between two coordinates using the Haversine formula.
     */
    public static function haversineDistanceMeters(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000.0; // meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) * sin($dLat / 2)
           + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
           * sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(max(0.0, 1.0 - $a)));
        return $earthRadius * $c;
    }

    /**
     * Resolve reliable coordinates for a shop or customer shipping address.
     * Strict precedence contract (used by both live maps and shipping quotes):
     *   1. The entity's own stored, in-bounds coordinate (never overwritten by a centroid)
     *   2. Barangay centroid from explicit barangay field, then address parts
     *   3. City center (Tupi) only when no barangay can be identified
     *   4. Polomolok default center strictly as the last resort
     *
     * @return array{lat: float, lng: float, source: string}
     */
    public function resolveEntityCoordinates(array $entity): array
    {
        $lat = (float) ($entity['latitude'] ?? $entity['lat'] ?? 0);
        $lng = (float) ($entity['longitude'] ?? $entity['lng'] ?? 0);

        // 1. Stored precise coordinate wins outright
        if ($lat != 0.0 && $lng != 0.0 && \App\Models\DeliveryModel::isPolomolokCoordinate($lat, $lng)) {
            return ['lat' => $lat, 'lng' => $lng, 'source' => 'pinned'];
        }

        $city = trim((string) ($entity['city'] ?? ''));

        // 2. Barangay / address_line2 / full address text / address_line / street lookup,
        //    ordered from most to least likely to contain a clean barangay name
        $candidates = [
            $entity['barangay'] ?? '',
            $entity['address_line2'] ?? '',
            $entity['destination_address'] ?? '',
            $entity['full_address'] ?? '',
            $entity['address_line'] ?? '',
            $entity['address_line1'] ?? '',
        ];

        foreach ($candidates as $text) {
            $text = trim((string) $text);
            if ($text === '') {
                continue;
            }
            // Strip municipality suffix if present (e.g., "Poblacion, Polomolok")
            $parts = array_map('trim', explode(',', $text));
            foreach ($parts as $part) {
                if ($part === '' || strcasecmp($part, 'Polomolok') === 0 || strcasecmp($part, 'Tupi') === 0 || strcasecmp($part, 'South Cotabato') === 0) {
                    continue;
                }
                $centroid = \App\Models\DeliveryModel::getBarangayCoordinate($part, $city);
                if ($centroid !== null) {
                    return ['lat' => (float) $centroid['lat'], 'lng' => (float) $centroid['lng'], 'source' => 'barangay_centroid'];
                }
            }
            $centroid = \App\Models\DeliveryModel::getBarangayCoordinate($text, $city);
            if ($centroid !== null) {
                return ['lat' => (float) $centroid['lat'], 'lng' => (float) $centroid['lng'], 'source' => 'barangay_centroid'];
            }
        }

        // 3. City center only when the city itself is known and no barangay matched
        if (stripos($city, 'tupi') !== false) {
            return [
                'lat'    => \App\Models\DeliveryModel::TUPI_CENTER_LAT,
                'lng'    => \App\Models\DeliveryModel::TUPI_CENTER_LNG,
                'source' => 'city_center',
            ];
        }

        // 4. Strict last resort — flagged via source so callers can surface it as approximate
        $def = $this->getDefaultCoordinates();
        return ['lat' => (float) $def['lat'], 'lng' => (float) $def['lng'], 'source' => 'default_center'];
    }

    /**
     * Calculate actual road distance and shipping fee between a tenant shop and a customer delivery address
     * using Google Maps Routes API (with OSRM and road-adjusted Haversine fallback) and Admin's configured rates.
     *
     * @param array $shop     Shop record (latitude, longitude, barangay, address_line, shop_name)
     * @param array $address  Customer shipping_addresses record (latitude, longitude, address_line1, address_line2, city)
     * @param array|null $settings Optional preloaded settings from SiteContentModel::getShippingFeeSettings()
     * @return array{
     *   deliverable: bool,
     *   distance_meters: int,
     *   distance_km: float,
     *   distance_text: string,
     *   duration_text: string,
     *   shipping_fee: float,
     *   base_shipping_fee: float,
     *   included_distance_km: float,
     *   additional_fee_per_km: float,
     *   extra_distance_km: float,
     *   extra_distance_fee: float,
     *   max_delivery_distance_km: float,
     *   route_source: string,
     *   error: string|null
     * }
     */
    public function calculateDeliveryQuote(array $shop, array $address, ?array $settings = null): array
    {
        if ($settings === null) {
            $settings = (new \App\Models\SiteContentModel())->getShippingFeeSettings();
        }

        $baseFee     = round(max(0.0, (float) ($settings['base_shipping_fee'] ?? 50.00)), 2);
        $includedKm  = round(max(0.5, (float) ($settings['included_distance_km'] ?? 2.00)), 2);
        $perKmFee    = round(max(0.0, (float) ($settings['additional_fee_per_km'] ?? 10.00)), 2);
        $maxDistKm   = round(max(0.5, (float) ($settings['max_delivery_distance_km'] ?? 25.00)), 2);

        // Enforce Polomolok-only delivery eligibility
        $addrCity = trim((string) ($address['city'] ?? ''));
        $rawLat   = (float) ($address['latitude'] ?? $address['lat'] ?? 0);
        $rawLng   = (float) ($address['longitude'] ?? $address['lng'] ?? 0);

        $isPolomolokAddress = true;
        if ($addrCity !== '' && stripos($addrCity, 'Polomolok') === false) {
            $isPolomolokAddress = false;
        } elseif ($rawLat != 0.0 && $rawLng != 0.0 && !\App\Models\DeliveryModel::isPolomolokCoordinate($rawLat, $rawLng)) {
            $isPolomolokAddress = false;
        }

        $origin = $this->resolveEntityCoordinates($shop);
        $dest   = $this->resolveEntityCoordinates($address);

        // TEST-ONLY distance override: disabled by default. Live quotes always use real
        // route km unless an admin explicitly enables the simulated toggle in Shipping
        // Settings; merely having a stored override value is never enough.
        $simulateEnabled = !empty($settings['simulate_distance_km'])
            && filter_var($settings['simulate_distance_km'], FILTER_VALIDATE_BOOLEAN);

        if ($simulateEnabled && isset($settings['override_distance_km']) && is_numeric($settings['override_distance_km'])) {
            $distanceKm     = round(max(0.0, (float) $settings['override_distance_km']), 2);
            $distanceMeters = (int) round($distanceKm * 1000);
            $estMins        = max(5, (int) round($distanceKm * 2.5));
            $durationText   = $estMins . ' mins';
            $routeSource    = 'simulated_distance';
        } else {
            $route = $this->computeRoute(
                ['lat' => $origin['lat'], 'lng' => $origin['lng']],
                ['lat' => $dest['lat'], 'lng' => $dest['lng']],
                'DRIVE'
            );

            if ($route !== null && !empty($route['distanceMeters'])) {
                $distanceMeters = (int) $route['distanceMeters'];
                $durationText   = (string) ($route['duration_text'] ?? '15 mins');
                $routeSource    = (string) ($route['source'] ?? 'google_routes');
            } else {
                // Road-winding adjusted Haversine fallback (1.25x straight-line distance)
                $straightMeters = self::haversineDistanceMeters($origin['lat'], $origin['lng'], $dest['lat'], $dest['lng']);
                $distanceMeters = (int) round($straightMeters * 1.25);
                $estMins        = max(5, (int) round(($distanceMeters / 1000) * 2.5));
                $durationText   = $estMins . ' mins';
                $routeSource    = 'estimated_road';
            }

            $distanceKm = round($distanceMeters / 1000, 2);
        }

        $distanceText = number_format($distanceKm, 1) . ' km';

        $withinRadius = ($distanceKm <= $maxDistKm);
        $deliverable  = $isPolomolokAddress && $withinRadius;

        // Distance credibility guard. A 0 km result is only trustworthy when the two points
        // are genuinely distinct street-level locations. When shop and destination collapse onto
        // the exact same resolved coordinate, one of them is almost certainly a barangay/city
        // centroid masquerading as a pinned address (shop and customer records sharing the same
        // municipal fallback point). The real distance is then UNKNOWN, not "zero blocks away",
        // so we flag it instead of silently quoting a confident base fee.
        $shopName = trim((string) ($shop['shop_name'] ?? 'the shop'));
        $originExact = round((float) $origin['lat'], 6) . ',' . round((float) $origin['lng'], 6);
        $destExact   = round((float) $dest['lat'], 6) . ',' . round((float) $dest['lng'], 6);

        $distanceApproximate = ($originExact === $destExact && $distanceMeters < 50);
        $accuracyNotice      = null;
        if ($distanceApproximate) {
            $distanceText   = '≈ same point';
            $accuracyNotice = 'The shop and this delivery address resolve to the identical map '
                . 'point, so the real distance to ' . $shopName . ' could not be measured (one of '
                . 'the two records only has a barangay/municipality-level location). A base '
                . 'shipping fee is applied; pin the exact location for an accurate '
                . 'distance-based fee.';
        }

        // KM-Limit Step Calculation:
        // Within $includedKm (e.g. <= 2.0 km), fee stays at $baseFee (does not increase).
        // When $distanceKm exceeds $includedKm, automatically add $perKmFee per exceeded $includedKm step bracket.
        $rawExtraKm = max(0.0, round($distanceKm - $includedKm, 2));
        if ($distanceKm <= $includedKm || $rawExtraKm <= 0.0) {
            $extraSteps = 0;
            $extraKm    = 0.0;
            $extraFee   = 0.00;
        } else {
            $stepSize   = $includedKm > 0.0 ? $includedKm : 1.0;
            $extraSteps = (int) ceil(($rawExtraKm - 1e-6) / $stepSize);
            $extraKm    = $rawExtraKm;
            $extraFee   = round($extraSteps * $perKmFee, 2);
        }

        $shippingFee = round($baseFee + $extraFee, 2);

        $error = null;
        if (!$isPolomolokAddress) {
            $error = 'Doorstep delivery and additional shipping fee calculation are available around Polomolok, South Cotabato only. Please select a Polomolok address or choose Store Pick-up.';
        } elseif (!$withinRadius) {
            $error = sprintf(
                'Delivery address is %.1f km away from %s, which exceeds the maximum Polomolok delivery distance of %.1f km. Please choose Store Pick-up or select a closer address.',
                $distanceKm,
                $shopName,
                $maxDistKm
            );
        }

        return [
            'deliverable'              => $deliverable,
            'is_polomolok'             => $isPolomolokAddress,
            'distance_meters'          => $distanceMeters,
            'distance_km'              => $distanceKm,
            'distance_text'            => $distanceText,
            'duration_text'            => $durationText,
            'shipping_fee'             => $shippingFee,
            'base_shipping_fee'        => $baseFee,
            'included_distance_km'     => $includedKm,
            'additional_fee_per_km'    => $perKmFee,
            'extra_steps'              => $extraSteps,
            'extra_distance_km'        => $extraKm,
            'extra_distance_fee'       => $extraFee,
            'max_delivery_distance_km' => $maxDistKm,
            'route_source'             => $routeSource,
            'distance_approximate'     => $distanceApproximate,
            'accuracy_notice'          => $accuracyNotice,
            'origin_coords'            => $origin,
            'destination_coords'       => $dest,
            'error'                    => $error,
        ];
    }
}

