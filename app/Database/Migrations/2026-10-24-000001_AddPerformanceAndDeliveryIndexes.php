<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPerformanceAndDeliveryIndexes extends Migration
{
    public function up()
    {
        $db       = $this->db;
        $driver   = strtolower((string) ($db->DBDriver ?? 'mysqli'));
        $isSqlite = str_contains($driver, 'sqlite');

        // 1. Ensure shop_calendar_schedules table exists
        if (! $db->tableExists('shop_calendar_schedules')) {
            if ($isSqlite) {
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
        }

        // 2. Ensure shops.hide_when_closed exists
        if ($db->tableExists('shops') && ! $db->fieldExists('hide_when_closed', 'shops')) {
            if ($isSqlite) {
                $db->query('ALTER TABLE `shops` ADD COLUMN `hide_when_closed` INTEGER NOT NULL DEFAULT 0');
            } else {
                $db->query('ALTER TABLE `shops` ADD COLUMN `hide_when_closed` TINYINT(1) NOT NULL DEFAULT 0 AFTER `offers_pickup`');
            }
        }

        // Helper to safely add an index if not exists
        $safeAddIndex = function (string $table, string $indexName, string $columns) use ($db, $isSqlite) {
            if (! $db->tableExists($table)) {
                return;
            }

            try {
                if ($isSqlite) {
                    $db->query("CREATE INDEX IF NOT EXISTS `{$indexName}` ON `{$table}` ({$columns})");
                } else {
                    $existing = $db->query("SHOW INDEX FROM `{$table}` WHERE Key_name = '{$indexName}'")->getResultArray();
                    if (empty($existing)) {
                        $db->query("ALTER TABLE `{$table}` ADD INDEX `{$indexName}` ({$columns})");
                    }
                }
            } catch (\Throwable $e) {
                log_message('notice', "Index {$indexName} on {$table} notice: " . $e->getMessage());
            }
        };

        // 3. Deliveries table performance indexes
        $safeAddIndex('deliveries', 'idx_deliveries_status_created', '`status`, `created_at`');
        $safeAddIndex('deliveries', 'idx_deliveries_deliverable_status', '`deliverable_type`, `deliverable_id`, `status`');

        // 4. Shops table performance indexes
        $safeAddIndex('shops', 'idx_shops_status_rating', '`status`, `rating_average`, `rating_count`');
        $safeAddIndex('shops', 'idx_shops_hide_closed', '`hide_when_closed`, `status`');

        // 5. Cart items table index for cleaner expiration lookups
        $safeAddIndex('cart_items', 'idx_cart_items_cart_expires', '`cart_id`, `expires_at`');
    }

    public function down()
    {
        $db       = $this->db;
        $driver   = strtolower((string) ($db->DBDriver ?? 'mysqli'));
        $isSqlite = str_contains($driver, 'sqlite');

        $safeDropIndex = function (string $table, string $indexName) use ($db, $isSqlite) {
            if (! $db->tableExists($table)) {
                return;
            }

            try {
                if ($isSqlite) {
                    $db->query("DROP INDEX IF EXISTS `{$indexName}`");
                } else {
                    $existing = $db->query("SHOW INDEX FROM `{$table}` WHERE Key_name = '{$indexName}'")->getResultArray();
                    if (! empty($existing)) {
                        $db->query("ALTER TABLE `{$table}` DROP INDEX `{$indexName}`");
                    }
                }
            } catch (\Throwable $e) {}
        };

        $safeDropIndex('deliveries', 'idx_deliveries_status_created');
        $safeDropIndex('deliveries', 'idx_deliveries_deliverable_status');
        $safeDropIndex('shops', 'idx_shops_status_rating');
        $safeDropIndex('shops', 'idx_shops_hide_closed');
        $safeDropIndex('cart_items', 'idx_cart_items_cart_expires');
    }
}
