<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTvaRatesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'UUID',
                'null' => false,
            ],
            'company_id' => [
                'type' => 'UUID',
                'null' => false,
            ],
            'rate' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => false,
            ],
            'label' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'is_default' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'null' => false,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('company_id');
        $this->forge->addKey('is_default');
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tva_rates');

        $this->db->query('ALTER TABLE tva_rates ADD CONSTRAINT check_rate_range CHECK (rate >= 0 AND rate <= 100)');
    }

    public function down()
    {
        $this->forge->dropTable('tva_rates');
    }
}
