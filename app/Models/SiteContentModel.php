<?php

namespace App\Models;

use CodeIgniter\Model;

class SiteContentModel extends Model
{
    protected $table            = 'site_contents';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'page',
        'content_key',
        'label',
        'content_type',
        'text_value',
        'image_url',
        'sort_order',
        'updated_by',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getByPage(string $page): array
    {
        return $this->where('page', $page)->orderBy('sort_order', 'ASC')->orderBy('content_key', 'ASC')->findAll();
    }

    public function getContentMap(string $page): array
    {
        $rows = $this->getByPage($page);
        $map = [];
        foreach ($rows as $r) {
            $map[$r['content_key']] = $r;
        }
        return $map;
    }

    public function getAllGrouped(): array
    {
        $rows = $this->orderBy('page', 'ASC')->orderBy('sort_order', 'ASC')->findAll();
        $grouped = [];
        foreach ($rows as $r) {
            $grouped[$r['page']][] = $r;
        }
        return $grouped;
    }

    /**
     * Request-level memoized cache.
     * @var array<string, array>
     */
    private static array $requestCache = [];

    /**
     * Clear site content caches (both in-memory and local disk cache).
     */
    public static function clearCache(): void
    {
        self::$requestCache = [];
        try {
            cache()->delete('blax_site_contents_home_map');
            cache()->delete('blax_site_contents_all_key_map');
            cache()->delete('blax_platform_deduction_percent');
            cache()->delete('blax_shipping_fee_settings');
        } catch (\Throwable $e) {
            // Ignore cache engine failure
        }
    }

    /**
     * Fetch homepage content map cleanly combining home_banners,
     * announcement_bar, and footer_info, ensuring home_banners entries
     * take precedence and avoid collisions with other pages (e.g. printing_services, categories).
     *
     * Cached in local /tmp cache for 600s + request memoization.
     *
     * @return array<string, array>
     */
    public function getHomeContentMap(): array
    {
        if (isset(self::$requestCache['home_map'])) {
            return self::$requestCache['home_map'];
        }

        try {
            $cached = cache('blax_site_contents_home_map');
            if (is_array($cached)) {
                self::$requestCache['home_map'] = $cached;
                return $cached;
            }
        } catch (\Throwable $e) {}

        // First load global components (announcement_bar, footer_info)
        $globalRows = $this->whereIn('page', ['announcement_bar', 'footer_info'])
                           ->orderBy('sort_order', 'ASC')
                           ->findAll();
        $map = [];
        foreach ($globalRows as $r) {
            $map[$r['content_key']] = $r;
        }

        // Then overlay home_banners so homepage-specific keys are never overwritten
        $homeRows = $this->where('page', 'home_banners')
                         ->orderBy('sort_order', 'ASC')
                         ->findAll();
        foreach ($homeRows as $r) {
            $map[$r['content_key']] = $r;
        }

        try {
            cache()->save('blax_site_contents_home_map', $map, 600);
        } catch (\Throwable $e) {}

        self::$requestCache['home_map'] = $map;
        return $map;
    }

    /**
     * Fetch all site content entries indexed by content_key.
     * home_banners takes precedence over generic/other page keys.
     *
     * Cached in local /tmp cache for 600s + request memoization.
     *
     * @return array<string, array>
     */
    public function getAllKeyMap(): array
    {
        if (isset(self::$requestCache['all_key_map'])) {
            return self::$requestCache['all_key_map'];
        }

        try {
            $cached = cache('blax_site_contents_all_key_map');
            if (is_array($cached)) {
                self::$requestCache['all_key_map'] = $cached;
                return $cached;
            }
        } catch (\Throwable $e) {}

        $rows = $this->orderBy('sort_order', 'ASC')->findAll();
        $map = [];
        foreach ($rows as $r) {
            if ($r['page'] !== 'home_banners') {
                $map[$r['content_key']] = $r;
            }
        }
        foreach ($rows as $r) {
            if ($r['page'] === 'home_banners') {
                $map[$r['content_key']] = $r;
            }
        }

        try {
            cache()->save('blax_site_contents_all_key_map', $map, 600);
        } catch (\Throwable $e) {}

        self::$requestCache['all_key_map'] = $map;
        return $map;
    }

    public function getPlatformDeductionPercent(): float
    {
        if (isset(self::$requestCache['deduction_percent'])) {
            return self::$requestCache['deduction_percent'];
        }

        try {
            $cached = cache('blax_platform_deduction_percent');
            if (is_numeric($cached)) {
                $val = (float) $cached;
                self::$requestCache['deduction_percent'] = $val;
                return $val;
            }
        } catch (\Throwable $e) {}

        $row = $this->where('page', 'platform')->where('content_key', 'withdrawal_deduction_percent')->first();
        $rate = 3.00;
        if ($row && is_numeric($row['text_value'])) {
            $rate = (float) $row['text_value'];
        }

        try {
            cache()->save('blax_platform_deduction_percent', $rate, 600);
        } catch (\Throwable $e) {}

        self::$requestCache['deduction_percent'] = $rate;
        return $rate;
    }

    public function setPlatformDeductionPercent(float $percent): bool
    {
        self::clearCache();
        $percent = round(max(0, min(50, $percent)), 2);
        $row = $this->where('page', 'platform')->where('content_key', 'withdrawal_deduction_percent')->first();
        if ($row) {
            return (bool) $this->update($row['id'], [
                'text_value' => number_format($percent, 2, '.', ''),
            ]);
        }
        return (bool) $this->insert([
            'page'         => 'platform',
            'content_key'  => 'withdrawal_deduction_percent',
            'label'        => 'Withdrawal Deduction Percentage',
            'content_type' => 'text',
            'text_value'   => number_format($percent, 2, '.', ''),
            'sort_order'   => 1,
        ]);
    }

    /**
     * Retrieve Admin-configured distance-based shipping fee settings.
     *
     * @return array{base_shipping_fee: float, included_distance_km: float, additional_fee_per_km: float, max_delivery_distance_km: float}
     */
    public function getShippingFeeSettings(): array
    {
        if (isset(self::$requestCache['shipping_fee_settings'])) {
            return self::$requestCache['shipping_fee_settings'];
        }

        try {
            $cached = cache('blax_shipping_fee_settings');
            if (is_array($cached) && isset($cached['base_shipping_fee'])) {
                self::$requestCache['shipping_fee_settings'] = $cached;
                return $cached;
            }
        } catch (\Throwable $e) {}

        $defaults = [
            'base_shipping_fee'        => 50.00,
            'included_distance_km'     => 2.00,
            'additional_fee_per_km'    => 10.00,
            'max_delivery_distance_km' => 25.00,
            // TEST-ONLY simulated distance override — disabled by default so live
            // quotes always resolve real route kilometers.
            'simulate_distance_km'     => 0,
            'override_distance_km'     => null,
        ];

        $keyMap = [
            'shipping_base_fee'             => 'base_shipping_fee',
            'shipping_included_km'          => 'included_distance_km',
            'shipping_per_km_fee'           => 'additional_fee_per_km',
            'shipping_max_distance_km'      => 'max_delivery_distance_km',
            'shipping_simulate_distance_km' => 'simulate_distance_km',
            'shipping_override_distance_km' => 'override_distance_km',
        ];

        try {
            $rows = $this->where('page', 'platform')
                ->whereIn('content_key', array_keys($keyMap))
                ->findAll();

            foreach ($rows as $row) {
                $cKey = $row['content_key'] ?? '';
                if (isset($keyMap[$cKey]) && is_numeric($row['text_value'] ?? null)) {
                    $defaults[$keyMap[$cKey]] = round((float) $row['text_value'], 2);
                }
            }
        } catch (\Throwable $e) {}

        try {
            cache()->save('blax_shipping_fee_settings', $defaults, 600);
        } catch (\Throwable $e) {}

        self::$requestCache['shipping_fee_settings'] = $defaults;
        return $defaults;
    }

    /**
     * Save Admin-configured distance-based shipping fee settings.
     */
    public function saveShippingFeeSettings(array $settings, ?int $updatedBy = null): bool
    {
        self::clearCache();

        $entries = [
            'shipping_base_fee' => [
                'label' => 'Base Shipping Fee (PHP)',
                'value' => round(max(0.0, (float) ($settings['base_shipping_fee'] ?? 50.00)), 2),
                'sort'  => 10,
            ],
            'shipping_included_km' => [
                'label' => 'Shipping Fee KM Limit (km)',
                'value' => round(max(0.5, (float) ($settings['included_distance_km'] ?? 2.00)), 2),
                'sort'  => 11,
            ],
            'shipping_per_km_fee' => [
                'label' => 'Additional Shipping Fee per KM Limit (PHP)',
                'value' => round(max(0.0, (float) ($settings['additional_fee_per_km'] ?? 10.00)), 2),
                'sort'  => 12,
            ],
            'shipping_max_distance_km' => [
                'label' => 'Maximum Delivery Distance (km)',
                'value' => round(max(0.5, (float) ($settings['max_delivery_distance_km'] ?? 25.00)), 2),
                'sort'  => 13,
            ],
        ];

        foreach ($entries as $contentKey => $meta) {
            $formatted = number_format($meta['value'], 2, '.', '');
            $existing = $this->where('page', 'platform')->where('content_key', $contentKey)->first();
            if ($existing) {
                $updateData = [
                    'text_value' => $formatted,
                    'label'      => $meta['label'],
                ];
                if ($updatedBy !== null) {
                    $updateData['updated_by'] = $updatedBy;
                }
                $this->update($existing['id'], $updateData);
            } else {
                $this->insert([
                    'page'         => 'platform',
                    'content_key'  => $contentKey,
                    'label'        => $meta['label'],
                    'content_type' => 'text',
                    'text_value'   => $formatted,
                    'sort_order'   => $meta['sort'],
                    'updated_by'   => $updatedBy,
                ]);
            }
        }

        // TEST-ONLY simulated distance override (explicit opt-in flag, never implied).
        // Stored as plain '0'/'1' because "0.00" would evaluate truthy as a boolean.
        $simEntries = [
            'shipping_simulate_distance_km' => [
                'label' => 'TEST ONLY: Simulated Distance Override Enabled',
                'value' => !empty($settings['simulate_distance_km']) ? '1' : '0',
                'sort'  => 14,
            ],
            'shipping_override_distance_km' => [
                'label' => 'TEST ONLY: Simulated Distance (km)',
                'value' => (isset($settings['override_distance_km']) && is_numeric($settings['override_distance_km']))
                    ? number_format(max(0.0, (float) $settings['override_distance_km']), 2, '.', '')
                    : '0.00',
                'sort'  => 15,
            ],
        ];

        foreach ($simEntries as $contentKey => $meta) {
            $existing = $this->where('page', 'platform')->where('content_key', $contentKey)->first();
            if ($existing) {
                $updateData = [
                    'text_value' => $meta['value'],
                    'label'      => $meta['label'],
                ];
                if ($updatedBy !== null) {
                    $updateData['updated_by'] = $updatedBy;
                }
                $this->update($existing['id'], $updateData);
            } else {
                $this->insert([
                    'page'         => 'platform',
                    'content_key'  => $contentKey,
                    'label'        => $meta['label'],
                    'content_type' => 'text',
                    'text_value'   => $meta['value'],
                    'sort_order'   => $meta['sort'],
                    'updated_by'   => $updatedBy,
                ]);
            }
        }

        self::clearCache();
        return true;
    }
}

