<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCompanySettingsTable extends Migration
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
            'address' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'postal_code' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'city' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'country' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'default' => 'France',
                'null' => false,
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'website' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'siret' => [
                'type' => 'VARCHAR',
                'constraint' => 14,
                'null' => true,
            ],
            'siren' => [
                'type' => 'VARCHAR',
                'constraint' => 9,
                'null' => true,
            ],
            'vat_number' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'default_vat_rate' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 20.00,
                'null' => false,
            ],
            'logo' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'legal_mentions' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'terms_conditions' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'iban' => [
                'type' => 'VARCHAR',
                'constraint' => 34,
                'null' => true,
            ],
            'bic' => [
                'type' => 'VARCHAR',
                'constraint' => 11,
                'null' => true,
            ],
            'invoice_prefix' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'default' => 'INV',
                'null' => false,
            ],
            'invoice_next_number' => [
                'type' => 'INTEGER',
                'default' => 1,
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
        $this->forge->addUniqueKey('company_id');
        $this->forge->addUniqueKey('siret');
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('company_settings');
    }

    public function down()
    {
        $this->forge->dropTable('company_settings');
    }
}
