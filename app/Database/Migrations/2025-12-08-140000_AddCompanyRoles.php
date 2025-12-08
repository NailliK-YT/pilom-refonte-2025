<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Add company_id to roles table to allow company-specific custom roles
 */
class AddCompanyRoles extends Migration
{
    public function up()
    {
        // Add company_id to roles table (nullable for system roles)
        $this->forge->addColumn('roles', [
            'company_id' => [
                'type' => 'UUID',
                'null' => true,
                'after' => 'id',
            ],
            'is_system' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'after' => 'description',
            ],
            'copied_from' => [
                'type' => 'INT',
                'null' => true,
                'after' => 'is_system',
            ],
        ]);

        // Add foreign key constraint
        $this->db->query('ALTER TABLE roles ADD CONSTRAINT fk_roles_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE');

        // Mark existing roles as system roles
        $this->db->table('roles')
            ->where('company_id IS NULL', null, false)
            ->update(['is_system' => true]);
    }

    public function down()
    {
        // Remove foreign key
        $this->db->query('ALTER TABLE roles DROP CONSTRAINT IF EXISTS fk_roles_company');

        // Remove columns
        $this->forge->dropColumn('roles', ['company_id', 'is_system', 'copied_from']);
    }
}
