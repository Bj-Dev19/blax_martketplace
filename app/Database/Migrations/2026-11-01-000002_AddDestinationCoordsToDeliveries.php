<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Persisted destination coordinates for deliveries. Tenant delivery detail and
 * customer tracking pages read these first so a geocoded customer pin is only
 * resolved once instead of on every page load.
 */
class AddDestinationCoordsToDeliveries extends Migration
{
    public function up()
    {
        $columns = [
            'destination_latitude' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,7',
                'null'       => true,
            ],
            'destination_longitude' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,7',
                'null'       => true,
            ],
        ];

        foreach ($columns as $name => $def) {
            if (!$this->db->fieldExists($name, 'deliveries')) {
                $this->forge->addColumn('deliveries', [$name => $def]);
            }
        }
    }

    public function down()
    {
        foreach (['destination_longitude', 'destination_latitude'] as $name) {
            if ($this->db->fieldExists($name, 'deliveries')) {
                $this->forge->dropColumn('deliveries', $name);
            }
        }
    }
}
