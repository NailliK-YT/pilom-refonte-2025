<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Adds 2FA (Two-Factor Authentication) fields to users table
 */
class Add2FAFieldsToUsersTable extends Migration
{
    public function up()
    {
        // Add 2FA fields to users table
        $fields = [
            'two_factor_secret' => [
                'type' => 'VARCHAR',
                'constraint' => 255, // Encrypted secret will be longer than 64 chars
                'null' => true,
            ],
            'two_factor_enabled' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'null' => false,
            ],
            'two_factor_backup_codes' => [
                'type' => 'TEXT', // JSON array of backup codes (encrypted)
                'null' => true,
            ],
            'two_factor_enabled_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'two_factor_recovery_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ];

        $this->forge->addColumn('users', $fields);

        // Add index for users with 2FA enabled
        $this->db->query('CREATE INDEX idx_users_2fa_enabled ON users(two_factor_enabled) WHERE two_factor_enabled = TRUE');
    }

    public function down()
    {
        // Drop index first
        $this->db->query('DROP INDEX IF EXISTS idx_users_2fa_enabled');

        // Drop columns
        $this->forge->dropColumn('users', [
            'two_factor_secret',
            'two_factor_enabled',
            'two_factor_backup_codes',
            'two_factor_enabled_at',
            'two_factor_recovery_at'
        ]);
    }
}
