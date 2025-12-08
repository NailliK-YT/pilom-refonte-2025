<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductsTable extends Migration
{
    public function up()
    {
        $fields = [
            'id' => ['type' => 'UUID'],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'description' => ['type' => 'TEXT', 'null' => true],
            'reference' => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'price_ht' => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'tva_id' => ['type' => 'UUID'],
            'category_id' => ['type' => 'UUID'],
            'company_id' => ['type' => 'UUID'],
            'image_path' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'is_archived' => ['type' => 'BOOLEAN', 'default' => false],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ];

        $this->forge->addField($fields);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('products', true);
    }

    public function down()
    {
        $this->forge->dropTable('products', true);
    }
}
