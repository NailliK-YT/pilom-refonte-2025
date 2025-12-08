<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Creates the user_company pivot table for multi-company user management
 * Allows users to belong to multiple companies with different roles
 */
class AddUserCompanyTable extends Migration
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
            'company_id' => [
                'type' => 'UUID',
                'null' => false,
            ],
            'role_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ],
            'is_primary' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'null' => false,
            ],
            'invited_by' => [
                'type' => 'UUID',
                'null' => true,
            ],
            'invitation_token' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'invitation_expires' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'invited_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'accepted_at' => [
                'type' => 'TIMESTAMP',
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
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('role_id', 'roles', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addUniqueKey(['user_id', 'company_id'], 'unique_user_company');
        $this->forge->createTable('user_company');

        // Create indexes for performance
        $this->db->query('CREATE INDEX idx_user_company_user_id ON user_company(user_id)');
        $this->db->query('CREATE INDEX idx_user_company_company_id ON user_company(company_id)');
        $this->db->query('CREATE INDEX idx_user_company_status ON user_company(status)');

        // Migrate existing users to user_company table
        $this->migrateExistingUsers();
    }

    public function down()
    {
        $this->forge->dropTable('user_company');
    }

    /**
     * Migrate existing users from users.company_id + users.role to user_company
     */
    private function migrateExistingUsers()
    {
        // Get all users with company_id
        $users = $this->db->table('users')
            ->where('company_id IS NOT NULL')
            ->get()
            ->getResultArray();

        if (empty($users)) {
            return;
        }

        // Get roles mapping
        $roles = $this->db->table('roles')->get()->getResultArray();
        $roleMap = [];
        foreach ($roles as $role) {
            $roleMap[$role['name']] = $role['id'];
        }

        // Default to 'user' role if role not found
        $defaultRoleId = $roleMap['user'] ?? 2;

        foreach ($users as $user) {
            $roleId = $defaultRoleId;

            // Map existing role field to role_id
            if (!empty($user['role']) && isset($roleMap[$user['role']])) {
                $roleId = $roleMap[$user['role']];
            }

            $this->db->table('user_company')->insert([
                'id' => $this->generateUUID(),
                'user_id' => $user['id'],
                'company_id' => $user['company_id'],
                'role_id' => $roleId,
                'is_primary' => true,
                'status' => 'active',
                'accepted_at' => $user['created_at'] ?? date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    private function generateUUID(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}
