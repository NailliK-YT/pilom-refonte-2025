<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Enhances the users table with additional profile and security fields
 */
class EnhanceUsersTable extends Migration
{
    public function up()
    {
        // Add new fields to users table
        $fields = [
            'first_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'email'
            ],
            'last_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'first_name'
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'last_name'
            ],
            'avatar' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'phone'
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'active',
                'null' => false,
                'after' => 'role'
            ],
            'last_login' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'after' => 'status'
            ],
            'password_reset_token' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
                'after' => 'verification_token_expires'
            ],
            'password_reset_expires' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'after' => 'password_reset_token'
            ],
        ];

        $this->forge->addColumn('users', $fields);

        // Add index on status for filtering
        $this->db->query('CREATE INDEX idx_users_status ON users(status)');
    }

    public function down()
    {
        // Drop added columns
        $this->forge->dropColumn('users', [
            'first_name',
            'last_name',
            'phone',
            'avatar',
            'status',
            'last_login',
            'password_reset_token',
            'password_reset_expires'
        ]);
    }
}
