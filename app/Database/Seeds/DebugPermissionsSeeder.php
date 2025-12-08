<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Debug seeder to check permissions setup
 */
class DebugPermissionsSeeder extends Seeder
{
    public function run()
    {
        echo "\n=== DEBUG PERMISSIONS ===\n\n";

        // 1. Get admin user
        $admin = $this->db->table('users')
            ->where('email', 'admin@pilom.fr')
            ->get()
            ->getRow();

        if (!$admin) {
            echo "❌ Admin user not found!\n";
            return;
        }

        echo "✓ Admin user found: {$admin->id}\n";
        echo "  - company_id: " . ($admin->company_id ?? 'NULL') . "\n";
        echo "  - role: " . ($admin->role ?? 'NULL') . "\n\n";

        // 2. Get user_company record
        $userCompany = $this->db->table('user_company')
            ->where('user_id', $admin->id)
            ->get()
            ->getRow();

        if (!$userCompany) {
            echo "❌ No user_company record found!\n";
        } else {
            echo "✓ user_company record found:\n";
            echo "  - company_id: {$userCompany->company_id}\n";
            echo "  - role_id: {$userCompany->role_id}\n";
            echo "  - status: {$userCompany->status}\n\n";
        }

        // 3. Get admin role
        $adminRole = $this->db->table('roles')
            ->where('name', 'admin')
            ->get()
            ->getRow();

        if (!$adminRole) {
            echo "❌ Admin role not found!\n";
        } else {
            echo "✓ Admin role: id={$adminRole->id}, name={$adminRole->name}\n\n";
        }

        // 4. Get role_permissions for admin
        $rolePerms = $this->db->table('role_permissions')
            ->select('role_permissions.*, permissions.name as perm_name')
            ->join('permissions', 'permissions.id = role_permissions.permission_id')
            ->where('role_permissions.role_id', $adminRole->id ?? 1)
            ->get()
            ->getResultArray();

        echo "✓ Permissions for admin role ({$adminRole->id}):\n";
        foreach ($rolePerms as $rp) {
            echo "  - {$rp['perm_name']} (id: {$rp['permission_id']})\n";
        }

        // 5. Check users.view specifically
        $usersView = $this->db->table('permissions')
            ->where('name', 'users.view')
            ->get()
            ->getRow();

        if ($usersView) {
            echo "\n✓ users.view permission exists: id={$usersView->id}\n";

            // Check if assigned to admin role
            $assigned = $this->db->table('role_permissions')
                ->where('role_id', $adminRole->id ?? 1)
                ->where('permission_id', $usersView->id)
                ->get()
                ->getRow();

            if ($assigned) {
                echo "✓ users.view is assigned to admin role\n";
            } else {
                echo "❌ users.view is NOT assigned to admin role!\n";
                // Fix it
                $this->db->table('role_permissions')->insert([
                    'role_id' => $adminRole->id,
                    'permission_id' => $usersView->id,
                ]);
                echo "✓ Fixed: users.view now assigned to admin role\n";
            }
        } else {
            echo "\n❌ users.view permission does not exist!\n";
        }

        echo "\n=== END DEBUG ===\n";
    }
}
