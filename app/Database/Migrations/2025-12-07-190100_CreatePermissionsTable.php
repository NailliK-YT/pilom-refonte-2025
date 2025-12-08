<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePermissionsTable extends Migration
{
    public function up()
    {
        // Permissions table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => false,
            ],
            'description' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'module' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('name');
        $this->forge->createTable('permissions');

        // Role-Permission pivot table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'role_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'permission_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('role_id', 'roles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('permission_id', 'permissions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('role_permissions');

        // Insert default permissions
        $permissions = [
            // Contacts
            ['name' => 'contacts.view', 'description' => 'Voir les contacts', 'module' => 'contacts'],
            ['name' => 'contacts.create', 'description' => 'Créer des contacts', 'module' => 'contacts'],
            ['name' => 'contacts.edit', 'description' => 'Modifier des contacts', 'module' => 'contacts'],
            ['name' => 'contacts.delete', 'description' => 'Supprimer des contacts', 'module' => 'contacts'],
            // Devis
            ['name' => 'devis.view', 'description' => 'Voir les devis', 'module' => 'devis'],
            ['name' => 'devis.create', 'description' => 'Créer des devis', 'module' => 'devis'],
            ['name' => 'devis.edit', 'description' => 'Modifier des devis', 'module' => 'devis'],
            ['name' => 'devis.delete', 'description' => 'Supprimer des devis', 'module' => 'devis'],
            // Factures
            ['name' => 'factures.view', 'description' => 'Voir les factures', 'module' => 'factures'],
            ['name' => 'factures.create', 'description' => 'Créer des factures', 'module' => 'factures'],
            ['name' => 'factures.edit', 'description' => 'Modifier des factures', 'module' => 'factures'],
            ['name' => 'factures.delete', 'description' => 'Supprimer des factures', 'module' => 'factures'],
            // Depenses
            ['name' => 'depenses.view', 'description' => 'Voir les dépenses', 'module' => 'depenses'],
            ['name' => 'depenses.create', 'description' => 'Créer des dépenses', 'module' => 'depenses'],
            ['name' => 'depenses.edit', 'description' => 'Modifier des dépenses', 'module' => 'depenses'],
            ['name' => 'depenses.delete', 'description' => 'Supprimer des dépenses', 'module' => 'depenses'],
            // Settings
            ['name' => 'settings.view', 'description' => 'Voir les paramètres', 'module' => 'settings'],
            ['name' => 'settings.edit', 'description' => 'Modifier les paramètres', 'module' => 'settings'],
            // Users (admin only)
            ['name' => 'users.view', 'description' => 'Voir les utilisateurs', 'module' => 'users'],
            ['name' => 'users.manage', 'description' => 'Gérer les utilisateurs', 'module' => 'users'],
            // Documents
            ['name' => 'documents.view', 'description' => 'Voir les documents', 'module' => 'documents'],
            ['name' => 'documents.upload', 'description' => 'Uploader des documents', 'module' => 'documents'],
            ['name' => 'documents.delete', 'description' => 'Supprimer des documents', 'module' => 'documents'],
            // Statistics
            ['name' => 'statistics.view', 'description' => 'Voir les statistiques', 'module' => 'statistics'],
        ];

        $now = date('Y-m-d H:i:s');
        foreach ($permissions as &$perm) {
            $perm['created_at'] = $now;
        }
        $this->db->table('permissions')->insertBatch($permissions);

        // Assign all permissions to admin role (role_id = 1)
        $allPerms = $this->db->table('permissions')->get()->getResultArray();
        $adminPerms = [];
        foreach ($allPerms as $perm) {
            $adminPerms[] = ['role_id' => 1, 'permission_id' => $perm['id']];
        }
        $this->db->table('role_permissions')->insertBatch($adminPerms);

        // Assign limited permissions to user role (role_id = 2)
        $userPermNames = ['contacts.view', 'contacts.create', 'contacts.edit', 'devis.view', 'devis.create', 'devis.edit', 'factures.view', 'documents.view', 'documents.upload'];
        $userPerms = $this->db->table('permissions')->whereIn('name', $userPermNames)->get()->getResultArray();
        $userRolePerms = [];
        foreach ($userPerms as $perm) {
            $userRolePerms[] = ['role_id' => 2, 'permission_id' => $perm['id']];
        }
        if (!empty($userRolePerms)) {
            $this->db->table('role_permissions')->insertBatch($userRolePerms);
        }

        // Assign comptable permissions (role_id = 3)
        $comptablePermNames = ['factures.view', 'factures.create', 'factures.edit', 'depenses.view', 'depenses.create', 'depenses.edit', 'statistics.view'];
        $comptablePerms = $this->db->table('permissions')->whereIn('name', $comptablePermNames)->get()->getResultArray();
        $comptableRolePerms = [];
        foreach ($comptablePerms as $perm) {
            $comptableRolePerms[] = ['role_id' => 3, 'permission_id' => $perm['id']];
        }
        if (!empty($comptableRolePerms)) {
            $this->db->table('role_permissions')->insertBatch($comptableRolePerms);
        }
    }

    public function down()
    {
        $this->forge->dropTable('role_permissions');
        $this->forge->dropTable('permissions');
    }
}
