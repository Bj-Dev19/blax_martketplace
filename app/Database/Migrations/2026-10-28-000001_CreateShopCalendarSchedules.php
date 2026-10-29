<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Store calendar closures / one-off hour overrides for shops.
 *
 * This table was originally created lazily at runtime by
 * ShopCalendarScheduleModel::ensureSchema(). That runtime branch now only
 * runs on sqlite (so HTTP requests stop paying 3 DDL/SHOW round-trips),
 * which left MySQL/Aiven environments without the table.
 */
class CreateShopCalendarSchedules extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('shop_calendar_schedules')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'BIGINT',
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'shop_id' => [
                    'type'     => 'BIGINT',
                    'unsigned' => true,
                    'null'     => false,
                ],
                'schedule_date' => [
                    'type' => 'DATE',
                    'null' => false,
                ],
                'is_closed' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 1,
                ],
                'note' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'default'    => null,
                ],
                'color_theme' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 24,
                    'default'    => 'amber',
                ],
                'open_time' => [
                    'type'    => 'TIME',
                    'null'    => true,
                    'default' => null,
                ],
                'close_time' => [
                    'type'    => 'TIME',
                    'null'    => true,
                    'default' => null,
                ],
                'created_at' => [
                    'type'       => 'DATETIME',
                    'null'       => true,
                    'default'    => null,
                ],
                'updated_at' => [
                    'type'       => 'DATETIME',
                    'null'       => true,
                    'default'    => null,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['shop_id', 'schedule_date'], 'uq_shop_schedule_date');
            $this->forge->addKey('schedule_date');
            $this->forge->createTable('shop_calendar_schedules', true);
        }

        if ($this->db->tableExists('shops') && !$this->db->fieldExists('hide_when_closed', 'shops')) {
            $this->forge->addColumn('shops', [
                'hide_when_closed' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                    'null'       => false,
                    'after'      => 'offers_pickup',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('shops') && $this->db->fieldExists('hide_when_closed', 'shops')) {
            $this->forge->dropColumn('shops', 'hide_when_closed');
        }

        if ($this->db->tableExists('shop_calendar_schedules')) {
            $this->forge->dropTable('shop_calendar_schedules', true);
        }
    }
}
