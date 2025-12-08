<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Fix admin user role_id in user_company table
 */
class FixAdminRoleSeeder extends Seeder
{
    public function run()
    {
        // Get admin user
        $admin = $this->db->table('users')
            ->where('email', 'admin@pilom.fr')
            ->get()
            ->getRow();

        if (!$admin) {
            echo "❌ Admin user not found!\n";
            return;
        }

        // Get admin role
        $adminRole = $this->db->table('roles')
            ->where('name', 'admin')
            ->get()
            ->getRow();

        if (!$adminRole) {
            echo "❌ Admin role not found!\n";
            return;
        }

        // Update user_company to use admin role
        $this->db->table('user_company')
            ->where('user_id', $admin->id)
            ->update(['role_id' => $adminRole->id]);

        echo "✓ Admin user role_id updated to {$adminRole->id} (admin) in user_company\n";

        // Also update the users table role
        $this->db->table('users')
            ->where('id', $admin->id)
            ->update(['role' => 'admin']);

        echo "✓ Admin user role updated in users table\n";
    }
}
