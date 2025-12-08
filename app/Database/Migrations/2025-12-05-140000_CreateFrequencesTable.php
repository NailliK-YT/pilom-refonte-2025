<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFrequencesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'UUID',
                'null' => false,
            ],
            'nom' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'jours' => [
                'type' => 'INTEGER',
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
        $this->forge->createTable('frequences');

        // Add constraint for positive days
        $this->db->query('ALTER TABLE frequences ADD CONSTRAINT check_jours_positive CHECK (jours > 0)');
    }

    public function down()
    {
        $this->forge->dropTable('frequences');
    }
}
