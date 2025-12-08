<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateReglementTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'facture_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => false,
            ],
            'date_reglement' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'montant' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
            ],
            'mode_paiement' => [
                'type'       => 'varchar',
                'constraint' => '20',
                'default'    => 'virement',
				'null'       => false
            ],
            'reference' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
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

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('facture_id', 'facture', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('reglement');
    }

    public function down()
    {
        $this->forge->dropTable('reglement');
    }
}
