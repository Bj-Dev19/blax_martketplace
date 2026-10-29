<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Distance-based shipping for delivery printing requests.
 * Mirrors the shipping columns already used by orders so printing and
 * product orders share the exact same calculateDeliveryQuote() math.
 */
class AddShippingFieldsToPrintingRequests extends Migration
{
    public function up()
    {
        $columns = [
            'shipping_fee' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
                'null'       => false,
            ],
            'distance_km' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,2',
                'null'       => true,
            ],
            'shipping_address_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'           => true,
            ],
        ];

        foreach ($columns as $name => $def) {
            if (!$this->db->fieldExists($name, 'printing_requests')) {
                $this->forge->addColumn('printing_requests', [$name => $def]);
            }
        }
    }

    public function down()
    {
        foreach (['shipping_address_id', 'distance_km', 'shipping_fee'] as $name) {
            if ($this->db->fieldExists($name, 'printing_requests')) {
                $this->forge->dropColumn('printing_requests', $name);
            }
        }
    }
}
