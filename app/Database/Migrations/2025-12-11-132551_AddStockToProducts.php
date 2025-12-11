<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStockToProducts extends Migration
{
    public function up()
    {
        // Ajout des champs de stock
        $fields = [
            'stock_quantity' => [
                'type'    => 'INT',
                'default' => 0,
				'null'    => true,
                'after'   => 'is_archived'
            ],
            'stock_alert_threshold' => [
                'type'    => 'INT',
                'default' => 5,
				'null'    => true,
                'after'   => 'stock_quantity'
            ],
        ];

        $this->forge->addColumn('products', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('products', ['stock_quantity', 'stock_alert_threshold']);
    }
}
