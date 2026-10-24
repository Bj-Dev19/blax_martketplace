<?php

namespace App\Libraries;

use App\Models\ProductModel;
use App\Models\OrderModel;
use App\Libraries\CohereClient;
use Config\Database;

/**
 * SeasonalAiService
 *
 * Provides intelligent, inventory-aware seasonal demand forecasting and
 * restock recommendations for merchants in Blax Marketplace (Polomolok/South Cotabato).
 * Powered by Cohere Generative AI (command-a-03-2025).
 */
class SeasonalAiService
{
    protected ?CohereClient $cohereClient = null;

    /**
     * Master catalog of seasonal calendar definitions.
     */
    protected static array $seasons = [
        'school' => [
            'key'                  => 'school',
            'name'                 => 'Back-to-School & Academics Rush',
            'short_name'           => 'School Season',
            'icon'                 => 'school',
            'badge_color'          => 'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-700',
            'accent_hex'           => '#004ac6',
            'period_label'         => 'June – August (Primary) & January (Mid-Year)',
            'velocity_multiplier'  => 2.8,
            'description'          => 'Massive local student surge in notebooks, writing instruments, bond paper, graphing pads, and school project printing.',
            'target_categories'    => ['school supplies', 'stationery', 'paper', 'office supplies', 'printing', 'art supplies'],
            'keywords'             => [
                'notebook', 'spiral', 'composition', 'pad', 'intermediate', 'yellow', 'bond', 'paper', 'a4', 'short', 'long',
                'ballpen', 'pen', 'gel pen', 'pencil', 'crayon', 'color', 'folder', 'envelope', 'eraser', 'ruler', 'sharpener',
                'bag', 'uniform', 'scissor', 'glue', 'paste', 'binder', 'index', 'drawing', 'art', 'cartolina', 'manila',
                'marker', 'highlighter', 'correction', 'staple', 'scientific'
            ],
            'expansion_ideas'      => [
                [
                    'name'         => 'Math Geometry Compass & Ruler Set',
                    'category'     => 'School Supplies',
                    'demand_surge' => '+320%',
                    'est_price'    => 85.00,
                    'reason'       => 'Mandatory equipment for Grade 7-12 mathematics across Polomolok schools.'
                ],
                [
                    'name'         => 'Correction Tape with 6m Refill Pack',
                    'category'     => 'Stationery',
                    'demand_surge' => '+280%',
                    'est_price'    => 45.00,
                    'reason'       => 'High-turnover impulse consumable heavily purchased during examination and lecture weeks.'
                ],
                [
                    'name'         => 'Heavy-Duty 2-Pocket Portfolio Folders',
                    'category'     => 'Paper & Filing',
                    'demand_surge' => '+240%',
                    'est_price'    => 28.00,
                    'reason'       => 'Standard student requirement for periodic portfolio clearances and subject submissions.'
                ]
            ]
        ],
        'holiday' => [
            'key'                  => 'holiday',
            'name'                 => 'Christmas & Year-End Holiday Celebrations',
            'short_name'           => 'Holiday Season',
            'icon'                 => 'celebration',
            'badge_color'          => 'bg-purple-100 text-purple-800 border-purple-300 dark:bg-purple-900/30 dark:text-purple-300 dark:border-purple-700',
            'accent_hex'           => '#7c3aed',
            'period_label'         => 'October – December',
            'velocity_multiplier'  => 2.6,
            'description'          => 'Surge in corporate and family giveaways, personalized sublimation mugs, calendars, planners, holiday packaging, greeting tags, and festive tarpaulins.',
            'target_categories'    => ['custom gifts', 'printing', 'packaging', 'crafts', 'merchandise', 'stationery'],
            'keywords'             => [
                'gift', 'mug', 'calendar', 'planner', 'tarpaulin', 'photo', 'frame', 'sticker', 'box', 'ribbon', 'greeting',
                'card', 'packaging', 'tag', 'souvenir', 't-shirt', 'print', 'craft', 'tote', 'keychain', 'giveaway', 'festive',
                'christmas', 'holiday', 'wrapping'
            ],
            'expansion_ideas'      => [
                [
                    'name'         => 'Customized Desk Calendar Standees (12-Month)',
                    'category'     => 'Custom Print',
                    'demand_surge' => '+410%',
                    'est_price'    => 120.00,
                    'reason'       => 'Highest volume corporate giveaway for local clinics, shops, and institutions in South Cotabato.'
                ],
                [
                    'name'         => 'Sublimation Ceramic Mugs (White / Color Rim)',
                    'category'     => 'Corporate Giveaways',
                    'demand_surge' => '+360%',
                    'est_price'    => 95.00,
                    'reason'       => 'Standard corporate gift exchange and family Christmas party souvenir favorite.'
                ],
                [
                    'name'         => 'Metallic Gel Pen & Greeting Gift Sets',
                    'category'     => 'Craft Supplies',
                    'demand_surge' => '+220%',
                    'est_price'    => 140.00,
                    'reason'       => 'Popular gift item for students and teachers during year-end school celebrations.'
                ]
            ]
        ],
        'valentines' => [
            'key'                  => 'valentines',
            'name'                 => 'Valentine\'s Day & Expressions of Love',
            'short_name'           => 'Valentine\'s Season',
            'icon'                 => 'favorite',
            'badge_color'          => 'bg-rose-100 text-rose-800 border-rose-300 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-700',
            'accent_hex'           => '#e11d48',
            'period_label'         => 'January – February (Peak Feb 1–15)',
            'velocity_multiplier'  => 2.3,
            'description'          => 'Rapid spike in bespoke greeting cards, romantic photo prints, couple apparel prints, gift wraps, and novelty craft packaging.',
            'target_categories'    => ['custom gifts', 'photo printing', 'greeting cards', 'crafts', 'packaging'],
            'keywords'             => [
                'valentine', 'heart', 'rose', 'love', 'card', 'greeting', 'photo', 'frame', 'mug', 'pillow', 'chocolate box',
                'ribbon', 'tag', 'flower wrap', 'bouquet paper', 'craft', 'sticker'
            ],
            'expansion_ideas'      => [
                [
                    'name'         => 'Waterproof Floral Bouquet Wrapping Paper (20s Pack)',
                    'category'     => 'Packaging & Floral',
                    'demand_surge' => '+480%',
                    'est_price'    => 160.00,
                    'reason'       => 'High-demand material supplied to local florist pop-ups and gift shops in Polomolok.'
                ],
                [
                    'name'         => 'Custom Polaroid-Style Instax Photo Print Bundles (10s)',
                    'category'     => 'Photo Printing',
                    'demand_surge' => '+390%',
                    'est_price'    => 120.00,
                    'reason'       => 'High margin keepsake gift heavily ordered by Gen-Z and couple customers.'
                ],
                [
                    'name'         => 'Embossed Heart Greeting Cards with Envelopes',
                    'category'     => 'Stationery',
                    'demand_surge' => '+270%',
                    'est_price'    => 45.00,
                    'reason'       => 'Essential impulse register add-on for retail shoppers purchasing gifts.'
                ]
            ]
        ],
        'mothers_fathers' => [
            'key'                  => 'mothers_fathers',
            'name'                 => 'Mother\'s & Father\'s Day Tributes',
            'short_name'           => 'Parents\' Season',
            'icon'                 => 'diversity_1',
            'badge_color'          => 'bg-pink-100 text-pink-800 border-pink-300 dark:bg-pink-950/40 dark:text-pink-300 dark:border-pink-700',
            'accent_hex'           => '#db2777',
            'period_label'         => 'May – June',
            'velocity_multiplier'  => 2.1,
            'description'          => 'Annual surges in personalized family plaques, framed portraits, custom aprons/t-shirts, greeting cards, and celebratory banner prints.',
            'target_categories'    => ['custom gifts', 'photo printing', 'frames', 'awards', 'merchandise'],
            'keywords'             => [
                'mom', 'dad', 'mother', 'father', 'family', 'portrait', 'frame', 'canvas', 'acrylic', 't-shirt', 'mug',
                'plaque', 'greeting', 'card', 'banner', 'tarpaulin', 'ribbon'
            ],
            'expansion_ideas'      => [
                [
                    'name'         => 'Laser-Engraved Wooden Desktop Plaque',
                    'category'     => 'Personalized Gifts',
                    'demand_surge' => '+340%',
                    'est_price'    => 280.00,
                    'reason'       => 'Top-selling bespoke tribute for working parents and community elders.'
                ],
                [
                    'name'         => 'Premium Glass-Front Magnetic Photo Display Frame',
                    'category'     => 'Frames',
                    'demand_surge' => '+290%',
                    'est_price'    => 195.00,
                    'reason'       => 'Essential item for showcasing newly printed family milestone photography.'
                ],
                [
                    'name'         => 'Custom Family Celebration Tarpaulin (4x3 ft)',
                    'category'     => 'Large Format Print',
                    'demand_surge' => '+240%',
                    'est_price'    => 220.00,
                    'reason'       => 'Popular local requirement for home catering and barangay family gatherings.'
                ]
            ]
        ],
        'halloween' => [
            'key'                  => 'halloween',
            'name'                 => 'Halloween & All Saints\' Celebrations',
            'short_name'           => 'Halloween Season',
            'icon'                 => 'theater_comedy',
            'badge_color'          => 'bg-orange-100 text-orange-800 border-orange-300 dark:bg-orange-950/40 dark:text-orange-300 dark:border-orange-700',
            'accent_hex'           => '#ea580c',
            'period_label'         => 'October 15 – November 2',
            'velocity_multiplier'  => 2.0,
            'description'          => 'Seasonal demand for trick-or-treat candy packaging, theme party masks, costume craft supplies, cemetery tribute stickers, and candles.',
            'target_categories'    => ['party supplies', 'crafts', 'packaging', 'stickers', 'printing'],
            'keywords'             => [
                'halloween', 'trick', 'treat', 'party', 'mask', 'costume', 'craft', 'candy bag', 'sticker', 'all saints',
                'cemetery', 'candle', 'tarpaulin', 'orange', 'black', 'cutout'
            ],
            'expansion_ideas'      => [
                [
                    'name'         => 'Pre-Printed Halloween Treat Pouches (50s)',
                    'category'     => 'Packaging',
                    'demand_surge' => '+310%',
                    'est_price'    => 110.00,
                    'reason'       => 'Fast-selling consumable for neighborhood and mall trick-or-treat activations.'
                ],
                [
                    'name'         => 'Waterproof Die-Cut Memorial Vinyl Stickers (Pack of 5)',
                    'category'     => 'Specialty Print',
                    'demand_surge' => '+280%',
                    'est_price'    => 75.00,
                    'reason'       => 'Heavily purchased during Undas week for family tombstone displays and lanterns.'
                ],
                [
                    'name'         => 'Neon Cardstock & Specialty Black Poster Board (10s)',
                    'category'     => 'Paper & Board',
                    'demand_surge' => '+210%',
                    'est_price'    => 85.00,
                    'reason'       => 'Core material for student costume crafts and school event hallway displays.'
                ]
            ]
        ],
        'easter' => [
            'key'                  => 'easter',
            'name'                 => 'Easter, Spring & Fiesta Celebrations',
            'short_name'           => 'Easter & Spring',
            'icon'                 => 'egg',
            'badge_color'          => 'bg-yellow-100 text-yellow-800 border-yellow-300 dark:bg-yellow-950/40 dark:text-yellow-300 dark:border-yellow-700',
            'accent_hex'           => '#ca8a04',
            'period_label'         => 'March – April',
            'velocity_multiplier'  => 1.8,
            'description'          => 'High turnover of Easter egg hunt materials, community fiesta banners, pastel crafting paper, church program booklets, and souvenirs.',
            'target_categories'    => ['party supplies', 'crafts', 'printing', 'paper', 'stationery'],
            'keywords'             => [
                'easter', 'spring', 'fiesta', 'egg', 'hunt', 'pastel', 'banner', 'bunting', 'program', 'booklet',
                'ribbon', 'craft', 'cartolina', 'crepe paper', 'stickers'
            ],
            'expansion_ideas'      => [
                [
                    'name'         => 'Easter Egg Hunt DIY Craft Basket Kit (12s)',
                    'category'     => 'Craft Kits',
                    'demand_surge' => '+330%',
                    'est_price'    => 140.00,
                    'reason'       => 'Standard family and church youth group activity package across Polomolok.'
                ],
                [
                    'name'         => 'Heavy-Duty Colorful Triangle Bunting Flags (10m)',
                    'category'     => 'Party & Fiesta',
                    'demand_surge' => '+260%',
                    'est_price'    => 90.00,
                    'reason'       => 'Widely installed along streetways and venue entrances for summer fiesta celebrations.'
                ],
                [
                    'name'         => 'Pastel Multicolored Copy Paper (500 Sheets A4)',
                    'category'     => 'Paper',
                    'demand_surge' => '+220%',
                    'est_price'    => 195.00,
                    'reason'       => 'Popular for church Easter programs, community announcements, and children crafts.'
                ]
            ]
        ],
        'school' => [
            'key'                  => 'school',
            'name'                 => 'Back-to-School & Academics Rush',
            'short_name'           => 'School Season',
            'icon'                 => 'school',
            'badge_color'          => 'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-700',
            'accent_hex'           => '#004ac6',
            'period_label'         => 'June – August (Primary) & January (Mid-Year)',
            'velocity_multiplier'  => 2.8,
            'description'          => 'Massive student surge in notebooks, writing instruments, bond paper, graphing pads, and school project printing.',
            'target_categories'    => ['school supplies', 'stationery', 'paper', 'office supplies', 'printing', 'art supplies'],
            'keywords'             => [
                'notebook', 'spiral', 'composition', 'pad', 'intermediate', 'yellow', 'bond', 'paper', 'a4', 'short', 'long',
                'ballpen', 'pen', 'gel pen', 'pencil', 'crayon', 'color', 'folder', 'envelope', 'eraser', 'ruler', 'sharpener',
                'bag', 'uniform', 'scissor', 'glue', 'paste', 'binder', 'index', 'drawing', 'art', 'cartolina', 'manila',
                'marker', 'highlighter', 'correction', 'staple', 'scientific'
            ],
            'expansion_ideas'      => [
                [
                    'name'         => 'Math Geometry Compass & Ruler Set',
                    'category'     => 'School Supplies',
                    'demand_surge' => '+320%',
                    'est_price'    => 85.00,
                    'reason'       => 'Mandatory equipment for Grade 7-12 mathematics across Polomolok schools.'
                ],
                [
                    'name'         => 'Correction Tape with 6m Refill Pack',
                    'category'     => 'Stationery',
                    'demand_surge' => '+280%',
                    'est_price'    => 45.00,
                    'reason'       => 'High-turnover impulse consumable heavily purchased during lecture weeks.'
                ],
                [
                    'name'         => 'Heavy-Duty 2-Pocket Portfolio Folders',
                    'category'     => 'Paper & Filing',
                    'demand_surge' => '+240%',
                    'est_price'    => 28.00,
                    'reason'       => 'Standard student requirement for periodic portfolio clearances and subject submissions.'
                ]
            ]
        ],
        'business' => [
            'key'                  => 'business',
            'name'                 => 'Business Permits & Year-End Tax Compliance',
            'short_name'           => 'Business Permit Season',
            'icon'                 => 'domain',
            'badge_color'          => 'bg-indigo-100 text-indigo-800 border-indigo-300 dark:bg-indigo-900/30 dark:text-indigo-300 dark:border-indigo-700',
            'accent_hex'           => '#4f46e5',
            'period_label'         => 'January – February',
            'velocity_multiplier'  => 1.9,
            'description'          => 'Annual renewal rush for commercial permit tarpaulins, carbonless duplicate receipts, and ledger files.',
            'target_categories'    => ['business forms', 'office filing', 'stamps', 'signage', 'printing'],
            'keywords'             => [
                'receipt', 'invoice', 'columnar', 'ledger', 'permit', 'stamp', 'dater', 'carbonless', 'stamp pad',
                'envelope', 'brown envelope', 'filing', 'box', 'signboard', 'official', 'binder'
            ],
            'expansion_ideas'      => [
                [
                    'name'         => 'Pre-Inked Custom Business Verification Stamp',
                    'category'     => 'Office Equipment',
                    'demand_surge' => '+310%',
                    'est_price'    => 350.00,
                    'reason'       => 'Crucial operational requirement for renewed merchant business permits in January.'
                ],
                [
                    'name'         => 'Carbonless 2-Ply Duplicate Official Receipt Booklets',
                    'category'     => 'Business Forms',
                    'demand_surge' => '+260%',
                    'est_price'    => 140.00,
                    'reason'       => 'Standard annual replenishment for local Polomolok merchant commercial transactions.'
                ],
                [
                    'name'         => 'Heavy-Duty Expanding File Wallet (Long Document)',
                    'category'     => 'Filing & Storage',
                    'demand_surge' => '+210%',
                    'est_price'    => 85.00,
                    'reason'       => 'Required for safely compiling municipal and barangay permit application documents.'
                ]
            ]
        ]
    ];

    public function __construct(?CohereClient $cohereClient = null)
    {
        try {
            $this->cohereClient = $cohereClient ?? service('cohere');
        } catch (\Throwable $e) {
            $this->cohereClient = null;
        }
    }

    /**
     * Automatically determine the primary active season based on general consumer demand patterns.
     */
    public function detectActiveSeason(): string
    {
        $month = (int) date('n');
        $day   = (int) date('j');

        // October (mid-late) through December: Holiday & Year-End Celebrations
        if ($month === 11 || $month === 12 || ($month === 10 && $day >= 15)) {
            return 'holiday';
        }
        // January: Business Permit & Compliance
        if ($month === 1) {
            return 'business';
        }
        // February: Valentine's Day Season
        if ($month === 2) {
            return 'valentines';
        }
        // March & April: Easter & Spring Celebrations
        if ($month === 3 || $month === 4) {
            return 'easter';
        }
        // May & June: Mother's & Father's Day
        if ($month === 5 || ($month === 6 && $day <= 20)) {
            return 'mothers_fathers';
        }
        // Late June, July, August: Back-to-School Season
        if ($month === 7 || $month === 8 || ($month === 6 && $day > 20)) {
            return 'school';
        }
        // September to mid-October: Pre-holiday prep / Halloween
        if ($month === 9 || ($month === 10 && $day < 15)) {
            return 'halloween';
        }

        return 'holiday';
    }

    /**
     * Get all available season definitions.
     */
    public function getSeasonsList(): array
    {
        return self::$seasons;
    }

    /**
     * Invalidate any cached seasonal AI analyses for a specific shop.
     * Called whenever a merchant adjusts stock, creates products, or completes orders.
     */
    public function invalidateShopCache(int $shopId): void
    {
        try {
            foreach (array_keys(self::$seasons) as $sKey) {
                cache()->delete("cohere_seasonal_v1_{$shopId}_{$sKey}");
            }
        } catch (\Throwable $e) {}
    }

    /**
     * Generate full Seasonal AI Restock Analysis for a specific shop.
     *
     * @param int         $shopId
     * @param string|null $seasonKey ('school'|'holiday'|'graduation'|'exams'|'business'|'valentines'|'easter'|'mothers_fathers'|'halloween')
     * @param bool        $allowExternalAi Whether to make live external Cohere API calls on cache miss
     * @param bool        $forceRefresh Whether to bypass and invalidate cached AI data for real-time calculation
     * @return array
     */
    public function getSeasonalAnalysis(int $shopId, ?string $seasonKey = null, bool $allowExternalAi = true, bool $forceRefresh = false): array
    {
        $activeDetected = $this->detectActiveSeason();
        $selectedKey    = ($seasonKey && isset(self::$seasons[$seasonKey])) ? $seasonKey : $activeDetected;
        $seasonDef      = self::$seasons[$selectedKey];

        $db = Database::connect();

        // 1. Fetch active products for this shop with primary image and category
        $products = $db->table('products p')
            ->select('p.id, p.sku, p.name, p.description, p.price, p.stock_quantity, p.low_stock_threshold, p.is_bestseller, c.name as category_name, pi.image_url')
            ->join('categories c', 'c.id = p.category_id', 'left')
            ->join('product_images pi', 'pi.product_id = p.id AND pi.is_primary = 1', 'left')
            ->where('p.shop_id', $shopId)
            ->where('p.deleted_at', null)
            ->get()
            ->getResultArray();

        // 2. Fetch 60-day sales velocity per product
        $twoMonthsAgo = date('Y-m-d 00:00:00', strtotime('-60 days'));
        $salesRows = $db->table('order_items oi')
            ->select('oi.product_id, SUM(oi.quantity) as total_qty, COUNT(DISTINCT oi.order_id) as order_count')
            ->join('orders o', 'o.id = oi.order_id')
            ->where('o.shop_id', $shopId)
            ->where('o.placed_at >=', $twoMonthsAgo)
            ->where('LOWER(o.status) !=', 'cancelled')
            ->groupBy('oi.product_id')
            ->get()
            ->getResultArray();

        $velocityMap = [];
        foreach ($salesRows as $sr) {
            $velocityMap[(int) $sr['product_id']] = (int) $sr['total_qty'];
        }

        // 3. Score and forecast each product based on real-time inventory
        $recommendations       = [];
        $totalPotentialUplift  = 0.0;
        $criticalCount         = 0;
        $highPriorityCount     = 0;
        $healthyCount          = 0;

        $multiplier = (float) $seasonDef['velocity_multiplier'];
        $keywords   = $seasonDef['keywords'];

        foreach ($products as $p) {
            $pId        = (int) $p['id'];
            $name       = (string) $p['name'];
            $desc       = (string) ($p['description'] ?? '');
            $catName    = (string) ($p['category_name'] ?? '');
            $stock      = (int) ($p['stock_quantity'] ?? 0);
            $threshold  = max(5, (int) ($p['low_stock_threshold'] ?? 10));
            $price      = (float) ($p['price'] ?? 0.0);
            $qtySold60d = (int) ($velocityMap[$pId] ?? 0);
            $qtySold30d = (int) ceil($qtySold60d / 2);

            // Compute textual relevance to the season
            $matchCount = 0;
            $haystack   = strtolower($name . ' ' . $desc . ' ' . $catName);
            foreach ($keywords as $kw) {
                if (str_contains($haystack, strtolower($kw))) {
                    $matchCount++;
                }
            }

            // Also check category match
            $categoryMatch = false;
            foreach ($seasonDef['target_categories'] as $tCat) {
                if (str_contains(strtolower($catName), $tCat)) {
                    $categoryMatch = true;
                    $matchCount += 2;
                    break;
                }
            }

            $isSeasonalMatch = ($matchCount > 0) || ((int) ($p['is_bestseller'] ?? 0) === 1);
            // Products with seasonal relevance receive the full seasonal demand surge.
            // Other shop products receive a standard baseline safety multiplier (1.25x).
            $effectiveMultiplier = $isSeasonalMatch ? $multiplier : 1.25;

            // Effective velocity baseline
            $baseVelocity = max(3, $qtySold30d > 0 ? $qtySold30d : (int) ceil($threshold * 0.7));

            // Forecasted monthly demand during this season
            $seasonalVelocity = (int) ceil($baseVelocity * $effectiveMultiplier);

            // Target Seasonal Buffer: 60-day safety stock buffer
            $targetBuffer = max(15, (int) ceil($seasonalVelocity * 1.35));

            // Needed restock units
            $restockUnits = max(0, $targetBuffer - $stock);

            // Days of stock remaining at seasonal rate
            $dailySeasonalRate = max(0.2, $seasonalVelocity / 30);
            $daysRemaining     = round($stock / $dailySeasonalRate, 1);

            // Urgency Classification
            $urgency = 'healthy';
            if ($stock <= 0) {
                $urgency = 'critical';
                $criticalCount++;
            } elseif ($stock <= $threshold || $daysRemaining <= 7) {
                $urgency = 'critical';
                $criticalCount++;
            } elseif ($stock < ($targetBuffer * 0.5) || $daysRemaining <= 18) {
                $urgency = 'high';
                $highPriorityCount++;
            } elseif ($stock < $targetBuffer) {
                $urgency = 'moderate';
            } else {
                $healthyCount++;
            }

            // Potential revenue uplift if restocked to target buffer
            $estUplift = $restockUnits * $price;
            if ($urgency !== 'healthy') {
                $totalPotentialUplift += $estUplift;
            }

            // Generate initial baseline AI rationale narrative
            $aiReason = $this->generateRationale(
                $name,
                $seasonDef['short_name'],
                $effectiveMultiplier,
                $stock,
                $targetBuffer,
                $restockUnits,
                $daysRemaining,
                $urgency
            );

            $recommendations[] = [
                'id'                 => $pId,
                'name'               => $name,
                'sku'                => $p['sku'] ?? 'SKU-' . $pId,
                'category'           => $catName ?: 'General Supplies',
                'price'              => $price,
                'current_stock'      => $stock,
                'threshold'          => $threshold,
                'target_buffer'      => $targetBuffer,
                'restock_units'      => $restockUnits,
                'days_remaining'     => $daysRemaining,
                'sales_velocity'     => round($dailySeasonalRate, 1),
                'urgency'            => $urgency,
                'is_seasonal_match'  => $isSeasonalMatch,
                'est_revenue_uplift' => $estUplift,
                'image_url'          => function_exists('product_image_url') ? product_image_url($p['image_url'] ?? null) : ($p['image_url'] ?? null),
                'ai_rationale'       => $aiReason,
                'rationale'          => $aiReason,
                'stock_pct'          => min(100, $targetBuffer > 0 ? round(($stock / $targetBuffer) * 100) : 0),
            ];
        }

        // Sort: Critical first, then High, then Moderate, then Highest Potential Revenue Uplift
        usort($recommendations, function ($a, $b) {
            $urgencyRank = ['critical' => 1, 'high' => 2, 'moderate' => 3, 'healthy' => 4];
            $rankA = $urgencyRank[$a['urgency']] ?? 5;
            $rankB = $urgencyRank[$b['urgency']] ?? 5;
            if ($rankA !== $rankB) {
                return $rankA <=> $rankB;
            }
            return $b['est_revenue_uplift'] <=> $a['est_revenue_uplift'];
        });

        // 4. Enrich via Cohere Generative AI (command-a-03-2025)
        $expansionIdeas = $seasonDef['expansion_ideas'] ?? [];
        $cohereAdvice = $this->enrichWithCohereAi(
            $shopId,
            $selectedKey,
            $seasonDef,
            $recommendations,
            $expansionIdeas,
            $allowExternalAi,
            $forceRefresh
        );

        $isCohereActive = ($this->cohereClient && $this->cohereClient->isConfigured());

        return [
            'season'                   => $seasonDef,
            'is_currently_active'      => ($selectedKey === $activeDetected),
            'detected_active_season'   => $activeDetected,
            'selected_season_key'      => $selectedKey,
            'available_seasons'        => self::$seasons,
            'total_analyzed_items'     => count($recommendations),
            'critical_stockout_count'  => $criticalCount,
            'high_priority_count'      => $highPriorityCount,
            'healthy_count'            => $healthyCount,
            'items_needing_restock'    => $criticalCount + $highPriorityCount,
            'est_seasonal_opp_revenue' => $totalPotentialUplift,
            'recommendations'          => $recommendations,
            'expansion_ideas'          => $expansionIdeas,
            'cohere_powered'           => $isCohereActive,
            'cohere_model'             => $isCohereActive ? ($this->cohereClient->config()->chatModel ?? 'command-a-03-2025') : null,
            'cohere_summary'           => $seasonDef['cohere_executive_summary'] ?? null,
            'is_realtime'              => true,
            'computed_at'              => date('Y-m-d H:i:s'),
        ];
    }

    /**
     * Generate concise spotlight summary specifically for the Tenant Dashboard widget.
     * Guaranteed instant (0ms external latency) by using deterministic local heuristics.
     *
     * @param int $shopId
     * @return array
     */
    public function getDashboardSpotlight(int $shopId): array
    {
        $analysis = $this->getSeasonalAnalysis($shopId, null, false);

        // Filter top 3 highest urgency items that need restock
        $urgentItems = array_values(array_filter($analysis['recommendations'], function ($r) {
            return in_array($r['urgency'], ['critical', 'high'], true) && $r['restock_units'] > 0;
        }));

        $topThree = array_slice($urgentItems, 0, 3);

        return [
            'season_name'          => $analysis['season']['name'],
            'short_name'           => $analysis['season']['short_name'],
            'icon'                 => $analysis['season']['icon'],
            'badge_color'          => $analysis['season']['badge_color'],
            'critical_count'       => $analysis['critical_stockout_count'],
            'high_priority_count'  => $analysis['high_priority_count'],
            'est_opp_revenue'      => $analysis['est_seasonal_opp_revenue'],
            'top_items'            => $topThree,
            'total_needing_restock'=> $analysis['items_needing_restock'],
            'cohere_powered'       => $analysis['cohere_powered'] ?? false,
            'cohere_summary'       => $analysis['cohere_summary'] ?? null,
        ];
    }

    /**
     * Enrich seasonal predictions, product rationales, and expansion opportunities
     * using Cohere Generative AI (command-a-03-2025).
     */
    protected function enrichWithCohereAi(
        int $shopId,
        string $seasonKey,
        array &$seasonDef,
        array &$recommendations,
        array &$expansionIdeas,
        bool $allowExternalAi = true,
        bool $forceRefresh = false
    ): ?array {
        if (! $this->cohereClient || ! $this->cohereClient->isConfigured()) {
            return null;
        }

        $cacheKey = "cohere_seasonal_v1_{$shopId}_{$seasonKey}";
        if ($forceRefresh) {
            try {
                cache()->delete($cacheKey);
            } catch (\Throwable $e) {}
        } else {
            try {
                $cached = cache($cacheKey);
                if (is_array($cached) && !empty($cached['executive_summary'])) {
                    $this->applyCohereData($cached, $seasonDef, $recommendations, $expansionIdeas);
                    return $cached;
                }
            } catch (\Throwable $e) {
                // Cache lookup fail-safe
            }
        }

        if (! $allowExternalAi) {
            return null;
        }

        // Prepare top items needing restock for the model prompt
        $itemsForAi = [];
        foreach ($recommendations as $r) {
            if ($r['restock_units'] > 0 || in_array($r['urgency'], ['critical', 'high'], true)) {
                $itemsForAi[] = [
                    'product_name'   => $r['name'],
                    'category'       => $r['category'],
                    'current_stock'  => $r['current_stock'],
                    'target_buffer'  => $r['target_buffer'],
                    'units_to_order' => $r['restock_units'],
                    'days_left'      => $r['days_remaining'],
                    'urgency'        => $r['urgency'],
                ];
                if (count($itemsForAi) >= 5) {
                    break;
                }
            }
        }

        $multiplier = $seasonDef['velocity_multiplier'] ?? 2.0;
        $seasonName = $seasonDef['name'] ?? $seasonDef['short_name'];
        $period     = $seasonDef['period_label'] ?? '';

        $prompt = "You are the Senior Retail Demand Analyst for Blax Marketplace in Polomolok, South Cotabato, Philippines.\n"
            . "Provide strategic inventory intelligence for a local merchant during '{$seasonName}' ({$period}, {$multiplier}x demand velocity surge).\n\n"
            . "Merchant's Top Restock Items:\n" . json_encode($itemsForAi, JSON_PRETTY_PRINT) . "\n\n"
            . "Generate:\n"
            . "1. executive_summary: A 2-sentence actionable retail advisory for this merchant for {$seasonName} in Polomolok.\n"
            . "2. product_rationales: Persuasive, concise 1-2 sentence rationale per product explaining why replenishment is critical at this seasonal velocity in Polomolok.\n"
            . "3. expansion_ideas: 3 high-demand items for this season to expand their store catalog.\n";

        $schema = [
            'type' => 'object',
            'properties' => [
                'executive_summary' => ['type' => 'string'],
                'product_rationales' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'product_name' => ['type' => 'string'],
                            'rationale'    => ['type' => 'string'],
                        ],
                        'required' => ['product_name', 'rationale'],
                    ],
                ],
                'expansion_ideas' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'name'         => ['type' => 'string'],
                            'category'     => ['type' => 'string'],
                            'demand_surge' => ['type' => 'string'],
                            'est_price'    => ['type' => 'number'],
                            'reason'       => ['type' => 'string'],
                        ],
                        'required' => ['name', 'category', 'demand_surge', 'est_price', 'reason'],
                    ],
                ],
            ],
            'required' => ['executive_summary'],
        ];

        try {
            $messages = [
                ['role' => 'user', 'content' => $prompt]
            ];
            $aiResult = $this->cohereClient->chat($messages, $schema);

            if (is_array($aiResult) && !empty($aiResult['executive_summary'])) {
                // Cache for 24 hours (86400 seconds) to ensure lightning-fast dashboard performance
                try {
                    cache()->save($cacheKey, $aiResult, 86400);
                } catch (\Throwable $e) {}

                $this->applyCohereData($aiResult, $seasonDef, $recommendations, $expansionIdeas);
                return $aiResult;
            }
        } catch (\Throwable $e) {
            log_message('warning', 'Cohere Seasonal AI enrichment fallback: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Apply Cohere AI generated data to the active seasonal state.
     */
    protected function applyCohereData(
        array $cohereData,
        array &$seasonDef,
        array &$recommendations,
        array &$expansionIdeas
    ): void {
        if (!empty($cohereData['executive_summary'])) {
            $seasonDef['cohere_executive_summary'] = $cohereData['executive_summary'];
        }

        if (!empty($cohereData['product_rationales']) && is_array($cohereData['product_rationales'])) {
            $rationaleMap = [];
            foreach ($cohereData['product_rationales'] as $pr) {
                if (!empty($pr['product_name']) && !empty($pr['rationale'])) {
                    $rationaleMap[strtolower(trim($pr['product_name']))] = trim($pr['rationale']);
                }
            }

            foreach ($recommendations as &$r) {
                $nameKey = strtolower(trim($r['name']));
                if (isset($rationaleMap[$nameKey])) {
                    $r['ai_rationale'] = $rationaleMap[$nameKey];
                } else {
                    // Check partial match
                    foreach ($rationaleMap as $target => $customReason) {
                        if (str_contains($nameKey, $target) || str_contains($target, $nameKey)) {
                            $r['ai_rationale'] = $customReason;
                            break;
                        }
                    }
                }
            }
            unset($r);
        }

        if (!empty($cohereData['expansion_ideas']) && is_array($cohereData['expansion_ideas']) && count($cohereData['expansion_ideas']) >= 2) {
            $expansionIdeas = array_slice($cohereData['expansion_ideas'], 0, 3);
        }
    }

    /**
     * Formulate an intuitive, business-grounded baseline AI narrative.
     */
    protected function generateRationale(
        string $productName,
        string $seasonName,
        float $multiplier,
        int $stock,
        int $target,
        int $restockUnits,
        float $daysRemaining,
        string $urgency
    ): string {
        $multiplierPct = round(($multiplier - 1.0) * 100);

        if ($urgency === 'critical') {
            if ($stock <= 0) {
                return "Out of Stock! Demand surges by +{$multiplierPct}% in {$seasonName}. Reorder +{$restockUnits} units immediately to prevent missed customer orders.";
            }
            return "Critical Stockout Risk! At {$seasonName} velocity (+{$multiplierPct}%), your {$stock} remaining units will run out in ~{$daysRemaining} days. Restock +{$restockUnits} units now.";
        }

        if ($urgency === 'high') {
            return "Pre-Season Buffer Needed: Demand accelerates by +{$multiplierPct}%. Adding +{$restockUnits} units secures 60 days of uninterrupted seasonal fulfillment.";
        }

        if ($urgency === 'moderate') {
            return "Steady demand anticipated. Recommend maintaining a buffer of {$target} units (replenish +{$restockUnits} units before supplier peak price adjustments).";
        }

        return "Healthy Seasonal Level: Your current stock of {$stock} units comfortably satisfies projected {$seasonName} demand without locking extra capital.";
    }
}
