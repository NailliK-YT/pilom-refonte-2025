<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDepensesRecurrencesTable extends Migration
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
                'comment' => 'Reference to the template expense',
            ],
            'date_debut' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'date_fin' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'prochaine_occurrence' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'statut' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
                'default' => 'actif',
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
        $this->forge->addKey('depense_id');
        $this->forge->addKey('prochaine_occurrence');
        $this->forge->addKey('statut');
        $this->forge->addForeignKey('depense_id', 'depenses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('depenses_recurrences');

        // Add constraint for status values
        $this->db->query("ALTER TABLE depenses_recurrences ADD CONSTRAINT check_recurrence_statut CHECK (statut IN ('actif', 'suspendu', 'termine'))");
    }

    public function down()
    {
        $this->forge->dropTable('depenses_recurrences');
    }
}
