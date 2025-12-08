<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLoginHistoryTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'UUID',
                'null' => false,
            ],
            'user_id' => [
                'type' => 'UUID',
                'null' => false,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'login_at' => [
                'type' => 'TIMESTAMP',
                'null' => false,
            ],
            'success' => [
                'type' => 'BOOLEAN',
                'default' => true,
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_id', 'login_at']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('login_history');
    }

    public function down()
    {
        $this->forge->dropTable('login_history');
    }
}
