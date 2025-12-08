<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * PermissionModel - Enhanced with company context and caching
 */
class PermissionModel extends Model
{
    protected $table = 'permissions';
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
     * Get permissions for a user (via their role in user_company)
     * Falls back to the legacy role column if user_company not set
     */
    public function getForUser(string $userId): array
    {
        // First try to get from user_company (new system)
        $userCompanyModel = new UserCompanyModel();
        $session = session();
        $companyId = $session->get('company_id');

        if ($companyId) {
            $userCompany = $userCompanyModel->getUserRoleInCompany($userId, $companyId);
            if ($userCompany) {
                return $this->getForRole($userCompany['role_id']);
            }
        }

        // Fallback to legacy role column
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
     * Get permissions for a user in a specific company
     */
    public function getForUserInCompany(string $userId, string $companyId): array
    {
        $cache = \Config\Services::cache();
        $cacheKey = "user_perms_{$userId}_{$companyId}";

        // Try cache first
        $cached = $cache->get($cacheKey);
        if ($cached !== null && is_array($cached)) {
            return $cached;
        }

        $userCompanyModel = new UserCompanyModel();
        $userCompany = $userCompanyModel->getUserRoleInCompany($userId, $companyId);

        if (!$userCompany) {
            $cache->save($cacheKey, [], 300); // Cache 5 minutes
            return [];
        }

        $permissions = $this->getForRole($userCompany['role_id']);
        $cache->save($cacheKey, $permissions, 300); // Cache 5 minutes

        return $permissions;
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

    /**
     * Check if user has permission in a specific company
     */
    public function userCanInCompany(string $userId, string $companyId, string $permissionName): bool
    {
        $permissions = $this->getForUserInCompany($userId, $companyId);

        foreach ($permissions as $perm) {
            if ($perm['name'] === $permissionName) {
                return true;
            }

            // Check for wildcard permissions (e.g., "users.*" matches "users.view")
            $permParts = explode('.', $perm['name']);
            $requestedParts = explode('.', $permissionName);

            if (count($permParts) === 2 && $permParts[1] === '*' && $permParts[0] === $requestedParts[0]) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has any of the specified permissions
     */
    public function userHasAnyPermission(string $userId, string $companyId, array $permissionNames): bool
    {
        foreach ($permissionNames as $permission) {
            if ($this->userCanInCompany($userId, $companyId, $permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user has all of the specified permissions
     */
    public function userHasAllPermissions(string $userId, string $companyId, array $permissionNames): bool
    {
        foreach ($permissionNames as $permission) {
            if (!$this->userCanInCompany($userId, $companyId, $permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Clear permission cache for a user
     */
    public function clearPermissionCache(string $userId, ?string $companyId = null): void
    {
        $cache = \Config\Services::cache();

        if ($companyId) {
            $cache->delete("user_perms_{$userId}_{$companyId}");
        } else {
            // Clear all company caches for this user
            $userCompanyModel = new UserCompanyModel();
            $companies = $userCompanyModel->getUserCompanies($userId);

            foreach ($companies as $company) {
                $cache->delete("user_perms_{$userId}_{$company['company_id']}");
            }
        }
    }

    /**
     * Get all permissions grouped by module
     */
    public function getGroupedByModule(): array
    {
        $permissions = $this->orderBy('module', 'ASC')->orderBy('name', 'ASC')->findAll();

        $grouped = [];
        foreach ($permissions as $perm) {
            $module = $perm['module'] ?? 'general';
            if (!isset($grouped[$module])) {
                $grouped[$module] = [];
            }
            $grouped[$module][] = $perm;
        }

        return $grouped;
    }

    /**
     * Assign a permission to a role
     */
    public function assignToRole(int $roleId, int $permissionId): bool
    {
        // Check if already assigned
        $existing = $this->db->table('role_permissions')
            ->where('role_id', $roleId)
            ->where('permission_id', $permissionId)
            ->get()
            ->getRow();

        if ($existing) {
            return true;
        }

        return $this->db->table('role_permissions')->insert([
            'role_id' => $roleId,
            'permission_id' => $permissionId,
        ]);
    }

    /**
     * Remove a permission from a role
     */
    public function removeFromRole(int $roleId, int $permissionId): bool
    {
        return $this->db->table('role_permissions')
            ->where('role_id', $roleId)
            ->where('permission_id', $permissionId)
            ->delete();
    }

    /**
     * Get module labels in French
     */
    public static function getModuleLabel(string $module): string
    {
        $labels = [
            'contacts' => 'Gestion des contacts',
            'devis' => 'Devis',
            'factures' => 'Factures',
            'depenses' => 'Dépenses',
            'settings' => 'Paramètres',
            'users' => 'Utilisateurs',
            'documents' => 'Documents',
            'statistics' => 'Statistiques',
            'company' => 'Entreprise',
            'general' => 'Général',
        ];

        return $labels[$module] ?? ucfirst($module);
    }
}
