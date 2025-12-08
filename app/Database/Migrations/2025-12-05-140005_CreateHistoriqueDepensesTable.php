<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateHistoriqueDepensesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'UUID',
                'null' => false,
            ],
            'depense_id' => [
                'type' => 'UUID',
                'null' => false,
            ],
            'champ_modifie' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'ancienne_valeur' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'nouvelle_valeur' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'modifie_par' => [
                'type' => 'UUID',
                'null' => false,
            ],
            'date_modification' => [
                'type' => 'TIMESTAMP',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('depense_id');
        $this->forge->addKey('date_modification');
        $this->forge->addForeignKey('depense_id', 'depenses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('modifie_par', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('historique_depenses');
    }

    public function down()
    {
        $this->forge->dropTable('historique_depenses');
    }
}
