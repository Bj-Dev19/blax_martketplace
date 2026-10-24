<?php

namespace App\Models;

use CodeIgniter\Model;

class ShopCalendarScheduleModel extends Model
{
    protected $table            = 'shop_calendar_schedules';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'shop_id',
        'schedule_date',
        'is_closed',
        'note',
        'color_theme',
        'open_time',
        'close_time',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public const ALLOWED_COLORS = ['amber', 'rose', 'emerald', 'sky'];

    private static bool $schemaChecked = false;

    public function __construct(?\CodeIgniter\Database\ConnectionInterface $db = null, ?\CodeIgniter\Validation\ValidationInterface $validation = null)
    {
        parent::__construct($db, $validation);
        $this->ensureSchema();
    }

    public static function resetSchemaCheck(): void
    {
        self::$schemaChecked = false;
    }

    /**
     * Ensure the `shop_calendar_schedules` table and `shops.hide_when_closed` column exist.
     */
    public function ensureSchema(): void
    {
        if (self::$schemaChecked) {
            return;
        }

        // In non-sqlite environments (MySQL/Aiven), schema is guaranteed by migrations.
        // Avoid executing 3 synchronous DDL/SHOW queries on every single HTTP request.
        $db     = $this->db ?? \Config\Database::connect();
        $driver = strtolower((string) ($db->DBDriver ?? 'mysqli'));
        if (! str_contains($driver, 'sqlite')) {
            self::$schemaChecked = true;
            return;
        }

        try {
            if (str_contains($driver, 'sqlite')) {
                $db->query("
                    CREATE TABLE IF NOT EXISTS `shop_calendar_schedules` (
                        `id` INTEGER PRIMARY KEY AUTOINCREMENT,
                        `shop_id` INTEGER NOT NULL,
                        `schedule_date` TEXT NOT NULL,
                        `is_closed` INTEGER NOT NULL DEFAULT 1,
                        `note` TEXT NULL DEFAULT NULL,
                        `color_theme` TEXT NOT NULL DEFAULT 'amber',
                        `open_time` TEXT NULL DEFAULT NULL,
                        `close_time` TEXT NULL DEFAULT NULL,
                        `created_at` TEXT NULL DEFAULT NULL,
                        `updated_at` TEXT NULL DEFAULT NULL,
                        UNIQUE (`shop_id`, `schedule_date`)
                    )
                ");
            } else {
                $db->query("
                    CREATE TABLE IF NOT EXISTS `shop_calendar_schedules` (
                        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                        `shop_id` INT UNSIGNED NOT NULL,
                        `schedule_date` DATE NOT NULL,
                        `is_closed` TINYINT(1) NOT NULL DEFAULT 1,
                        `note` VARCHAR(255) NULL DEFAULT NULL,
                        `color_theme` VARCHAR(24) NOT NULL DEFAULT 'amber',
                        `open_time` TIME NULL DEFAULT NULL,
                        `close_time` TIME NULL DEFAULT NULL,
                        `created_at` DATETIME NULL DEFAULT NULL,
                        `updated_at` DATETIME NULL DEFAULT NULL,
                        PRIMARY KEY (`id`),
                        UNIQUE KEY `uq_shop_schedule_date` (`shop_id`, `schedule_date`),
                        KEY `idx_schedule_date` (`schedule_date`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
                ");
            }

            if ($db->tableExists('shops') && !$db->fieldExists('hide_when_closed', 'shops')) {
                if (str_contains($driver, 'sqlite')) {
                    $db->query("ALTER TABLE `shops` ADD COLUMN `hide_when_closed` INTEGER NOT NULL DEFAULT 0");
                } else {
                    $db->query("ALTER TABLE `shops` ADD COLUMN `hide_when_closed` TINYINT(1) NOT NULL DEFAULT 0 AFTER `offers_pickup`");
                }
            }

            if (method_exists($db, 'resetDataCache')) {
                $db->resetDataCache();
            }

            self::$schemaChecked = true;
        } catch (\Throwable $e) {
            log_message('error', '[ShopCalendarScheduleModel::ensureSchema] ' . $e->getMessage());
        }
    }

    /**
     * Return all calendar schedule entries for a shop, keyed by YYYY-MM-DD.
     * Optionally filter by year-month prefix (e.g. "2026-10").
     *
     * @return array<string, array<string, mixed>>
     */
    public function getSchedulesForShop(int $shopId, ?string $yearMonth = null): array
    {
        $builder = $this->where('shop_id', $shopId);
        if ($yearMonth !== null && preg_match('/^\d{4}-\d{2}$/', $yearMonth)) {
            $builder->like('schedule_date', $yearMonth . '-', 'after');
        }

        $rows = $builder->orderBy('schedule_date', 'ASC')->findAll();
        $map  = [];
        foreach ($rows as $row) {
            $dateKey = (string) ($row['schedule_date'] ?? '');
            if ($dateKey !== '') {
                $map[$dateKey] = [
                    'id'            => (int) $row['id'],
                    'shop_id'       => (int) $row['shop_id'],
                    'schedule_date' => $dateKey,
                    'is_closed'     => (int) ($row['is_closed'] ?? 1),
                    'note'          => trim((string) ($row['note'] ?? '')),
                    'color_theme'   => in_array($row['color_theme'] ?? '', self::ALLOWED_COLORS, true) ? $row['color_theme'] : 'amber',
                    'open_time'     => $row['open_time'] ?? null,
                    'close_time'    => $row['close_time'] ?? null,
                ];
            }
        }

        return $map;
    }

    /**
     * Create or update a calendar schedule entry for a specific shop and date.
     * If not closed and note is empty, removes the override entry.
     */
    public function upsertScheduleDate(
        int $shopId,
        string $scheduleDate,
        bool $isClosed,
        string $note = '',
        string $colorTheme = 'amber',
        ?string $openTime = null,
        ?string $closeTime = null
    ): bool {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $scheduleDate)) {
            return false;
        }

        $note       = mb_substr(trim($note), 0, 255);
        $colorTheme = in_array($colorTheme, self::ALLOWED_COLORS, true) ? $colorTheme : 'amber';

        if (!$isClosed && $note === '' && $openTime === null && $closeTime === null) {
            return $this->deleteScheduleDate($shopId, $scheduleDate);
        }

        if ($isClosed && $note === '') {
            $note = 'Closed for the day';
        }

        $existing = $this->where('shop_id', $shopId)
            ->where('schedule_date', $scheduleDate)
            ->first();

        $payload = [
            'shop_id'       => $shopId,
            'schedule_date' => $scheduleDate,
            'is_closed'     => $isClosed ? 1 : 0,
            'note'          => $note,
            'color_theme'   => $colorTheme,
            'open_time'     => $isClosed ? null : $openTime,
            'close_time'    => $isClosed ? null : $closeTime,
        ];

        if ($existing) {
            return (bool) $this->update((int) $existing['id'], $payload);
        }

        return (bool) $this->insert($payload);
    }

    /**
     * Delete a calendar schedule entry for a specific shop and date.
     */
    public function deleteScheduleDate(int $shopId, string $scheduleDate): bool
    {
        return (bool) $this->where('shop_id', $shopId)
            ->where('schedule_date', $scheduleDate)
            ->delete();
    }

    /**
     * Create or update calendar schedule entries across a date range [startDate, endDate].
     */
    public function upsertScheduleDateRange(
        int $shopId,
        string $startDate,
        string $endDate,
        bool $isClosed,
        string $note = '',
        string $colorTheme = 'amber',
        ?string $openTime = null,
        ?string $closeTime = null
    ): bool {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
            return false;
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
            $endDate = $startDate;
        }

        $startTs = strtotime($startDate);
        $endTs   = strtotime($endDate);
        if ($startTs === false) {
            return false;
        }
        if ($endTs === false || $endTs < $startTs) {
            $endTs = $startTs;
        }

        // Cap range to at most 90 days to prevent runaway loops
        $maxTs = strtotime('+90 days', $startTs);
        if ($endTs > $maxTs) {
            $endTs = $maxTs;
        }

        $success = true;
        $curr = $startTs;
        while ($curr <= $endTs) {
            $dateStr = date('Y-m-d', $curr);
            $res = $this->upsertScheduleDate($shopId, $dateStr, $isClosed, $note, $colorTheme, $openTime, $closeTime);
            if (!$res) {
                $success = false;
            }
            $curr = strtotime('+1 day', $curr);
        }

        return $success;
    }

    /**
     * Delete calendar schedule entries across a date range [startDate, endDate].
     */
    public function deleteScheduleDateRange(int $shopId, string $startDate, string $endDate): bool
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
            return false;
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate) || $endDate === $startDate) {
            return $this->deleteScheduleDate($shopId, $startDate);
        }

        return (bool) $this->where('shop_id', $shopId)
            ->where('schedule_date >=', $startDate)
            ->where('schedule_date <=', $endDate)
            ->delete();
    }

    /**
     * Evaluate operational status for a single shop on the given date/time (defaults to today).
     * Priority:
     *   1. Calendar Schedule override (`shop_calendar_schedules` for YYYY-MM-DD)
     *   2. Weekly Operating Hours (`shop_business_hours` for day_of_week 0..6)
     *
     * @return array{
     *   shop_id: int,
     *   date: string,
     *   is_closed_today: bool,
     *   is_open: bool,
     *   source: string,
     *   note: string,
     *   color_theme: string,
     *   status_label: string,
     *   open_time: string|null,
     *   close_time: string|null,
     *   hide_when_closed: bool
     * }
     */
    public function getShopOperationalStatus(int $shopId, ?string $dateTime = null): array
    {
        $map = $this->getOperationalStatusMapForShops([$shopId], $dateTime);
        return $map[$shopId] ?? [
            'shop_id'          => $shopId,
            'date'             => date('Y-m-d'),
            'is_closed_today'  => false,
            'is_open'          => true,
            'source'           => 'default',
            'note'             => '',
            'color_theme'      => 'emerald',
            'status_label'     => 'Open Today',
            'open_time'        => '09:00:00',
            'close_time'       => '18:00:00',
            'hide_when_closed' => false,
        ];
    }

    /**
     * Batch-evaluate operational status for multiple shops without N+1 queries.
     *
     * @param array<int, int|string> $shopIds
     * @return array<int, array<string, mixed>>
     */
    public function getOperationalStatusMapForShops(array $shopIds, ?string $dateTime = null): array
    {
        $cleanIds = array_values(array_unique(array_filter(array_map('intval', $shopIds), static fn(int $id) => $id > 0)));
        if (empty($cleanIds)) {
            return [];
        }

        $ts        = $dateTime !== null ? strtotime($dateTime) : time();
        if ($ts === false) {
            $ts = time();
        }
        $dateStr   = date('Y-m-d', $ts);
        $dayOfWeek = (int) date('w', $ts);

        $db = $this->db ?? \Config\Database::connect();

        // 1. Fetch shop flags (hide_when_closed)
        $shopFlags = [];
        try {
            $shopRows = $db->table('shops')
                ->select('id, hide_when_closed')
                ->whereIn('id', $cleanIds)
                ->get()
                ->getResultArray();
            foreach ($shopRows as $sr) {
                $shopFlags[(int) $sr['id']] = !empty($sr['hide_when_closed']);
            }
        } catch (\Throwable $e) {
            // Fallback if column not yet present
        }

        // 2. Fetch today's calendar schedule overrides
        $calRows = $this->whereIn('shop_id', $cleanIds)
            ->where('schedule_date', $dateStr)
            ->findAll();
        $calByShop = [];
        foreach ($calRows as $cr) {
            $calByShop[(int) $cr['shop_id']] = $cr;
        }

        // 3. Fetch today's weekly business hours
        $weeklyByShop = [];
        if ($db->tableExists('shop_business_hours')) {
            $wRows = $db->table('shop_business_hours')
                ->whereIn('shop_id', $cleanIds)
                ->where('day_of_week', $dayOfWeek)
                ->get()
                ->getResultArray();
            foreach ($wRows as $wr) {
                $weeklyByShop[(int) $wr['shop_id']] = $wr;
            }
        }

        $result = [];
        foreach ($cleanIds as $sid) {
            $hideWhenClosed = $shopFlags[$sid] ?? false;
            $cal            = $calByShop[$sid] ?? null;
            $weekly         = $weeklyByShop[$sid] ?? null;

            $defaultOpen  = $weekly['open_time'] ?? '09:00:00';
            $defaultClose = $weekly['close_time'] ?? '18:00:00';

            if ($cal !== null) {
                $isClosedToday = ((int) ($cal['is_closed'] ?? 1)) === 1;
                $note          = trim((string) ($cal['note'] ?? ''));
                $colorTheme    = in_array($cal['color_theme'] ?? '', self::ALLOWED_COLORS, true)
                    ? $cal['color_theme']
                    : ($isClosedToday ? 'amber' : 'emerald');

                if ($isClosedToday) {
                    $statusLabel = $note !== '' ? ('Closed Today • ' . $note) : 'Closed Today';
                    $result[$sid] = [
                        'shop_id'          => $sid,
                        'date'             => $dateStr,
                        'is_closed_today'  => true,
                        'is_open'          => false,
                        'source'           => 'calendar',
                        'note'             => $note !== '' ? $note : 'Scheduled Closure',
                        'color_theme'      => $colorTheme,
                        'status_label'     => $statusLabel,
                        'open_time'        => null,
                        'close_time'       => null,
                        'hide_when_closed' => $hideWhenClosed,
                    ];
                } else {
                    $openTime    = !empty($cal['open_time']) ? $cal['open_time'] : ($defaultOpen ?: '09:00:00');
                    $closeTime   = !empty($cal['close_time']) ? $cal['close_time'] : ($defaultClose ?: '18:00:00');
                    $statusLabel = $note !== '' ? ('Open Today • ' . $note) : 'Open Today';
                    $result[$sid] = [
                        'shop_id'          => $sid,
                        'date'             => $dateStr,
                        'is_closed_today'  => false,
                        'is_open'          => true,
                        'source'           => 'calendar',
                        'note'             => $note,
                        'color_theme'      => $colorTheme,
                        'status_label'     => $statusLabel,
                        'open_time'        => $openTime,
                        'close_time'       => $closeTime,
                        'hide_when_closed' => $hideWhenClosed,
                    ];
                }
                continue;
            }

            // Fallback to weekly operating hours
            if ($weekly !== null && ((int) ($weekly['is_closed'] ?? 0)) === 1) {
                $dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                $dayName  = $dayNames[$dayOfWeek] ?? 'Today';
                $result[$sid] = [
                    'shop_id'          => $sid,
                    'date'             => $dateStr,
                    'is_closed_today'  => true,
                    'is_open'          => false,
                    'source'           => 'weekly_hours',
                    'note'             => "Regularly closed on {$dayName}s",
                    'color_theme'      => 'rose',
                    'status_label'     => "Closed Today ({$dayName})",
                    'open_time'        => null,
                    'close_time'       => null,
                    'hide_when_closed' => $hideWhenClosed,
                ];
                continue;
            }

            $result[$sid] = [
                'shop_id'          => $sid,
                'date'             => $dateStr,
                'is_closed_today'  => false,
                'is_open'          => true,
                'source'           => $weekly !== null ? 'weekly_hours' : 'default',
                'note'             => '',
                'color_theme'      => 'emerald',
                'status_label'     => 'Open Today',
                'open_time'        => $defaultOpen ?: '09:00:00',
                'close_time'       => $defaultClose ?: '18:00:00',
                'hide_when_closed' => $hideWhenClosed,
            ];
        }

        return $result;
    }

    /**
     * Enrich a list of shop records with `operational_status`, `is_closed_today`, and `closure_note`.
     * Optionally filters out closed shops that have `hide_when_closed` enabled.
     *
     * @param array<int, array<string, mixed>> $shops
     * @return array<int, array<string, mixed>>
     */
    public function enrichShopsWithStatus(array $shops, bool $respectHideWhenClosed = false, ?string $dateTime = null): array
    {
        if (empty($shops)) {
            return [];
        }

        $ids = array_column($shops, 'id');
        $map = $this->getOperationalStatusMapForShops($ids, $dateTime);

        $enriched = [];
        foreach ($shops as $shop) {
            $sid    = (int) ($shop['id'] ?? 0);
            $status = $map[$sid] ?? $this->getShopOperationalStatus($sid, $dateTime);

            if ($respectHideWhenClosed && !empty($status['is_closed_today']) && !empty($status['hide_when_closed'])) {
                continue;
            }

            $shop['operational_status'] = $status;
            $shop['is_closed_today']    = (bool) ($status['is_closed_today'] ?? false);
            $shop['closure_note']       = (string) ($status['note'] ?? '');
            $shop['schedule_color']     = (string) ($status['color_theme'] ?? 'amber');
            $enriched[]                 = $shop;
        }

        return $enriched;
    }
}
