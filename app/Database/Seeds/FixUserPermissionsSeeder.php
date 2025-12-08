<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Fix missing users.view and users.manage permissions
 */
class FixUserPermissionsSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // Check if users.view exists
        $existing = $this->db->table('permissions')
            ->where('name', 'users.view')
            ->get()
            ->getRow();

        if ($existing) {
            echo "✓ users.view existe déjà.\n";
        } else {
            $this->db->table('permissions')->insert([
                'name' => 'users.view',
                'description' => 'Voir la liste des utilisateurs',
                'module' => 'users',
                'created_at' => $now,
            ]);
            echo "✓ Permission users.view ajoutée.\n";
        }

        // Check if users.manage exists
        $existingManage = $this->db->table('permissions')
            ->where('name', 'users.manage')
            ->get()
            ->getRow();

        if ($existingManage) {
            echo "✓ users.manage existe déjà.\n";
        } else {
            $this->db->table('permissions')->insert([
                'name' => 'users.manage',
                'description' => 'Gérer les utilisateurs (modifier rôles)',
                'module' => 'users',
                'created_at' => $now,
            ]);
            echo "✓ Permission users.manage ajoutée.\n";
        }

        // Assign to admin role
        $adminRoleId = 1;

        $permsToAssign = $this->db->table('permissions')
            ->whereIn('name', ['users.view', 'users.manage'])
            ->get()
            ->getResultArray();

        foreach ($permsToAssign as $perm) {
            // Check if already assigned
            $exists = $this->db->table('role_permissions')
                ->where('role_id', $adminRoleId)
                ->where('permission_id', $perm['id'])
                ->get()
                ->getRow();

            if (!$exists) {
                $this->db->table('role_permissions')->insert([
                    'role_id' => $adminRoleId,
                    'permission_id' => $perm['id'],
                ]);
                echo "✓ Permission {$perm['name']} assignée au rôle admin.\n";
            } else {
                echo "✓ Permission {$perm['name']} déjà assignée au rôle admin.\n";
            }
        }

        echo "\n✅ Correction terminée!\n";
    }
}
