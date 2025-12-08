<?php

namespace App\Models;

use CodeIgniter\Model;

class PermissionModel extends Model
{
    protected $table      = 'permissions';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;
    protected $createdField = 'created_at';

    protected $allowedFields = [
        'name',
        'description',
        'module'
    ];

    /**
     * Get permissions for a role
     */
    public function getForRole(int $roleId): array
    {
        return $this->select('permissions.*')
            ->join('role_permissions', 'role_permissions.permission_id = permissions.id')
            ->where('role_permissions.role_id', $roleId)
            ->findAll();
    }

    /**
     * Get permissions for a user (via their role)
     */
    public function getForUser(string $userId): array
    {
        $userModel = new UserModel();
        $user = $userModel->find($userId);
        
        if (!$user || empty($user['role'])) {
            return [];
        }

        $roleModel = new RoleModel();
        $role = $roleModel->findByName($user['role']);
        
        if (!$role) {
            return [];
        }

        return $this->getForRole($role['id']);
    }

    /**
     * Check if user has a specific permission
     */
    public function userHasPermission(string $userId, string $permissionName): bool
    {
        $permissions = $this->getForUser($userId);
        foreach ($permissions as $perm) {
            if ($perm['name'] === $permissionName) {
                return true;
            }
        }
        return false;
    }
}
