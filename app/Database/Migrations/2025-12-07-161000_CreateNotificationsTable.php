<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificationsTable extends Migration
{
    public function up()
    {
        // Notifications table for storing all notifications
        $this->forge->addField([
            'id' => [
                'type' => 'UUID',
            ],
            'user_id' => [
                'type' => 'UUID',
            ],
            'company_id' => [
                'type' => 'UUID',
                'null' => true,
            ],
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'invoice, quote, payment, system, alert, reminder'
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'link' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'is_read' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'read_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'priority' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'normal',
                'comment' => 'low, normal, high, urgent'
            ],
            'data' => [
                'type' => 'JSONB',
                'null' => true,
                'comment' => 'Additional data for the notification'
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
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('notifications');
        
        // Create index for faster queries
        $this->db->query('CREATE INDEX idx_notifications_user_read ON notifications(user_id, is_read)');
        $this->db->query('CREATE INDEX idx_notifications_user_created ON notifications(user_id, created_at DESC)');
    }

    public function down()
    {
        $this->forge->dropTable('notifications', true);
    }
}
