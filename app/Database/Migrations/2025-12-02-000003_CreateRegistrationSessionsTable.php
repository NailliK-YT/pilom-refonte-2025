<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRegistrationSessionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'UUID',
                'null' => false,
            ],
            'session_token' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => false,
            ],
            'step' => [
                'type' => 'INT',
                'constraint' => 1,
                'null' => false,
                'default' => 1,
            ],
            'data' => [
                'type' => 'JSONB',
                'null' => false,
            ],
            'expires_at' => [
                'type' => 'TIMESTAMP',
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
        $this->forge->addUniqueKey('session_token');
        $this->forge->createTable('registration_sessions');
    }

    public function down()
    {
        $this->forge->dropTable('registration_sessions');
    }
}
