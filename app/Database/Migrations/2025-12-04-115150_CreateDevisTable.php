<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDevisTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'SERIAL',
                'auto_increment' => true,
            ],
            'numero_devis' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'date_emission' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'date_validite' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'montant_ht' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
            ],
            'montant_tva' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
            ],
            'montant_ttc' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
            ],
            'statut' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'brouillon',
                'null'       => false,
            ],
            'contact_id' => [
                'type' => 'INT',
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

        $this->forge->addKey('id', true); // clé primaire
        $this->forge->addForeignKey('contact_id', 'contact', 'id', 'CASCADE', 'CASCADE'); // FK
        $this->forge->createTable('devis');
    }

    public function down()
    {
        $this->forge->dropTable('devis');
    }
}
