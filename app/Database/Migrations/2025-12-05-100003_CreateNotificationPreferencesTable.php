<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificationPreferencesTable extends Migration
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
            'email_notifications' => [
                'type' => 'BOOLEAN',
                'default' => true,
                'null' => false,
            ],
            'email_invoices' => [
                'type' => 'BOOLEAN',
                'default' => true,
                'null' => false,
            ],
            'email_quotes' => [
                'type' => 'BOOLEAN',
                'default' => true,
                'null' => false,
            ],
            'email_payments' => [
                'type' => 'BOOLEAN',
                'default' => true,
                'null' => false,
            ],
            'email_marketing' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'null' => false,
            ],
            'push_notifications' => [
                'type' => 'BOOLEAN',
                'default' => true,
                'null' => false,
            ],
            'inapp_notifications' => [
                'type' => 'BOOLEAN',
                'default' => true,
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
        $this->forge->addUniqueKey('user_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('notification_preferences');
    }

    public function down()
    {
        $this->forge->dropTable('notification_preferences');
    }
}
