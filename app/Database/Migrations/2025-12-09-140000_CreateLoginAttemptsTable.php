<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Creates login_attempts table for brute-force protection
 */
class CreateLoginAttemptsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'UUID',
                'null' => false,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45, // IPv6 max length
                'null' => false,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'attempts_count' => [
                'type' => 'INTEGER',
                'default' => 1,
                'null' => false,
            ],
            'first_attempt_at' => [
                'type' => 'TIMESTAMP',
                'null' => false,
            ],
            'last_attempt_at' => [
                'type' => 'TIMESTAMP',
                'null' => false,
            ],
            'blocked_until' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('login_attempts');

        // Add unique constraint and indexes
        $this->db->query('CREATE UNIQUE INDEX idx_login_attempts_ip_email ON login_attempts(ip_address, COALESCE(email, \'\'))');
        $this->db->query('CREATE INDEX idx_login_attempts_ip ON login_attempts(ip_address)');
        $this->db->query('CREATE INDEX idx_login_attempts_email ON login_attempts(email) WHERE email IS NOT NULL');
        $this->db->query('CREATE INDEX idx_login_attempts_blocked ON login_attempts(blocked_until) WHERE blocked_until IS NOT NULL');
    }

    public function down()
    {
        $this->forge->dropTable('login_attempts');
    }
}
