<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCoverPhotoUrlToShops extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('cover_photo_url', 'shops')) {
            $this->forge->addColumn('shops', [
                'cover_photo_url' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 500,
                    'null'       => true,
                    'after'      => 'logo_url',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('cover_photo_url', 'shops')) {
            $this->forge->dropColumn('shops', 'cover_photo_url');
        }
    }
}
