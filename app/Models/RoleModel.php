<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table      = 'roles';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'name',
        'description'
    ];

    /**
     * Get role with its permissions
     */
    public function getWithPermissions(int $roleId): ?array
    {
        $role = $this->find($roleId);
        if (!$role) {
            return null;
        }

        $permissions = $this->db->table('permissions')
            ->select('permissions.*')
            ->join('role_permissions', 'role_permissions.permission_id = permissions.id')
            ->where('role_permissions.role_id', $roleId)
            ->get()
            ->getResultArray();

        $role['permissions'] = $permissions;
        return $role;
    }

    /**
     * Get role by name
     */
    public function findByName(string $name): ?array
    {
        return $this->where('name', $name)->first();
    }
}
