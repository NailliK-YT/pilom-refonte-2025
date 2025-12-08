<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFactureTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'numero_facture' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'date_emission' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'date_echeance' => [
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
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'id_devis' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,   // une facture peut exister sans devis
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true); // clé primaire

        $this->forge->addForeignKey('contact_id', 'contact', 'id', 'CASCADE', 'CASCADE'); // FK
        $this->forge->addForeignKey('id_devis', 'devis', 'id', 'SET NULL', 'CASCADE'); // FK

        $this->forge->createTable('facture');
    }

    public function down()
    {
        $this->forge->dropTable('facture');
    }
}
