<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAccountDeletionRequestsTable extends Migration
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
            'requested_at' => [
                'type' => 'TIMESTAMP',
                'null' => false,
            ],
            'scheduled_deletion_at' => [
                'type' => 'TIMESTAMP',
                'null' => false,
            ],
            'reason' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'pending',
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
        $this->forge->addKey('user_id');
        $this->forge->addKey(['status', 'scheduled_deletion_at']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('account_deletion_requests');
    }

    public function down()
    {
        $this->forge->dropTable('account_deletion_requests');
    }
}
