<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder to add additional permissions for user management
 */
class UserPermissionsSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // Check if permissions already exist
        $existing = $this->db->table('permissions')
            ->where('name', 'users.invite')
            ->get()
            ->getRow();

        if ($existing) {
            echo "✓ Les permissions utilisateurs existent déjà.\n";
            return;
        }

        // Additional permissions for user management
        $newPermissions = [
            ['name' => 'users.view', 'description' => 'Voir la liste des utilisateurs', 'module' => 'users', 'created_at' => $now],
            ['name' => 'users.manage', 'description' => 'Gérer les utilisateurs (modifier rôles)', 'module' => 'users', 'created_at' => $now],
            ['name' => 'users.invite', 'description' => 'Inviter des utilisateurs', 'module' => 'users', 'created_at' => $now],
            ['name' => 'users.suspend', 'description' => 'Suspendre des utilisateurs', 'module' => 'users', 'created_at' => $now],
            ['name' => 'users.remove', 'description' => 'Retirer des utilisateurs', 'module' => 'users', 'created_at' => $now],
            ['name' => 'company.settings', 'description' => 'Modifier les paramètres entreprise', 'module' => 'company', 'created_at' => $now],
            ['name' => 'company.billing', 'description' => 'Gérer l\'abonnement', 'module' => 'company', 'created_at' => $now],
        ];

        $this->db->table('permissions')->insertBatch($newPermissions);
        echo "✓ Nouvelles permissions ajoutées.\n";

        // Assign new permissions to admin role
        $adminRoleId = 1; // admin is role_id 1 by default
        $newPerms = $this->db->table('permissions')
            ->whereIn('name', ['users.view', 'users.manage', 'users.invite', 'users.suspend', 'users.remove', 'company.settings', 'company.billing'])
            ->get()
            ->getResultArray();

        $adminPerms = [];
        foreach ($newPerms as $perm) {
            $adminPerms[] = ['role_id' => $adminRoleId, 'permission_id' => $perm['id']];
        }

        if (!empty($adminPerms)) {
            $this->db->table('role_permissions')->insertBatch($adminPerms);
            echo "✓ Permissions assignées au rôle admin.\n";
        }
    }
}
