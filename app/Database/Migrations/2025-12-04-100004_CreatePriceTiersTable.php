<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePriceTiersTable extends Migration
{
    public function up()
    {
        $fields = [
            'id' => ['type' => 'CHAR', 'constraint' => 36],
            'product_id' => ['type' => 'CHAR', 'constraint' => 36],
            'min_quantity' => ['type' => 'INTEGER'],
            'price_ht' => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ];

        $this->forge->addField($fields);
        $this->forge->addKey('id', true);
        $this->forge->createTable('price_tiers', true);
    }

    public function down()
    {
        $this->forge->dropTable('price_tiers', true);
    }
}
