<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Creates the audit_logs table for security and RGPD compliance
 * Tracks all sensitive user actions
 */
class CreateAuditLogTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGSERIAL',
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'UUID',
                'null' => true,
            ],
            'company_id' => [
                'type' => 'UUID',
                'null' => true,
            ],
            'action' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'entity_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'entity_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => true,
            ],
            'old_values' => [
                'type' => 'JSONB',
                'null' => true,
            ],
            'new_values' => [
                'type' => 'JSONB',
                'null' => true,
            ],
            'ip_address' => [
                'type' => 'INET',
                'null' => true,
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('audit_logs');

        // Create indexes for querying
        $this->db->query('CREATE INDEX idx_audit_logs_user_id ON audit_logs(user_id)');
        $this->db->query('CREATE INDEX idx_audit_logs_company_id ON audit_logs(company_id)');
        $this->db->query('CREATE INDEX idx_audit_logs_action ON audit_logs(action)');
        $this->db->query('CREATE INDEX idx_audit_logs_created_at ON audit_logs(created_at)');
        $this->db->query('CREATE INDEX idx_audit_logs_entity ON audit_logs(entity_type, entity_id)');
    }

    public function down()
    {
        $this->forge->dropTable('audit_logs');
    }
}
