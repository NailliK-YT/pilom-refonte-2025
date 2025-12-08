<?php

namespace App\Controllers;

use App\Models\RoleModel;
use App\Models\PermissionModel;
use App\Models\AuditLogModel;

/**
 * Controller for managing role permissions
 */
class PermissionsController extends BaseController
{
    protected RoleModel $roleModel;
    protected PermissionModel $permissionModel;
    protected AuditLogModel $auditModel;

    public function __construct()
    {
        $this->roleModel = new RoleModel();
        $this->permissionModel = new PermissionModel();
        $this->auditModel = new AuditLogModel();
    }

    /**
     * Display permission matrix for all roles
     */
    public function index()
    {
        $session = session();
        $companyId = $session->get('company_id');

        // Get system roles and company-specific roles
        $roles = $this->roleModel->builder()
            ->groupStart()
            ->where('company_id IS NULL', null, false)
            ->orWhere('company_id', $companyId)
            ->groupEnd()
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        // Get all permissions grouped by module
        $allPermissions = $this->permissionModel->findAll();
        $permissionsByModule = [];
        foreach ($allPermissions as $perm) {
            $module = $perm['module'] ?? 'other';
            $permissionsByModule[$module][] = $perm;
        }

        // Get permissions for each role
        $rolePermissions = [];
        foreach ($roles as $role) {
            $perms = $this->permissionModel->getForRole($role['id']);
            $rolePermissions[$role['id']] = array_column($perms, 'id');
        }

        return view('admin/permissions/index', [
            'roles' => $roles,
            'permissionsByModule' => $permissionsByModule,
            'rolePermissions' => $rolePermissions,
        ]);
    }

    /**
     * Show form to create a new role
     */
    public function create()
    {
        $session = session();
        $companyId = $session->get('company_id');

        // Get all system roles for copying
        $systemRoles = $this->roleModel->builder()
            ->groupStart()
            ->where('company_id IS NULL', null, false)
            ->orWhere('company_id', $companyId)
            ->groupEnd()
            ->get()
            ->getResultArray();

        return view('admin/permissions/create_role', [
            'systemRoles' => $systemRoles,
        ]);
    }

    /**
     * Store a new custom role
     */
    public function store()
    {
        $session = session();
        $companyId = $session->get('company_id');
        $db = db_connect();

        $name = $this->request->getPost('name');
        $description = $this->request->getPost('description');
        $copyFrom = $this->request->getPost('copy_from');

        // Validate
        if (empty($name)) {
            return redirect()->back()->with('error', 'Le nom du rôle est requis.');
        }

        // Check if role name already exists for this company
        $existing = $this->roleModel->builder()
            ->where('name', strtolower($name))
            ->groupStart()
            ->where('company_id', $companyId)
            ->orWhere('company_id IS NULL', null, false)
            ->groupEnd()
            ->get()
            ->getRow();

        if ($existing) {
            return redirect()->back()->with('error', 'Un rôle avec ce nom existe déjà.');
        }

        $db->transStart();

        // Create the role
        $roleData = [
            'name' => strtolower($name),
            'description' => $description,
        ];

        // Only add company_id if the column exists (after migration)
        try {
            $this->roleModel->insert(array_merge($roleData, [
                'company_id' => $companyId,
            ]));
            $roleId = $this->roleModel->getInsertID();
        } catch (\Exception $e) {
            // Fallback if company_id column doesn't exist yet
            $this->roleModel->insert($roleData);
            $roleId = $this->roleModel->getInsertID();
        }

        // Copy permissions if requested
        if ($copyFrom) {
            $sourcePermissions = $this->permissionModel->getForRole($copyFrom);
            foreach ($sourcePermissions as $perm) {
                $db->table('role_permissions')->insert([
                    'role_id' => $roleId,
                    'permission_id' => $perm['id'],
                ]);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Erreur lors de la création du rôle.');
        }

        // Log the action
        $this->auditModel->log('role.create', [
            'entity_type' => 'role',
            'entity_id' => $roleId,
            'new_values' => json_encode(['name' => $name, 'copied_from' => $copyFrom]),
        ]);

        return redirect()->to('/admin/permissions')
            ->with('success', "Rôle '{$name}' créé avec succès.");
    }

    /**
     * Copy an existing role
     */
    public function copy($sourceRoleId)
    {
        $sourceRole = $this->roleModel->find($sourceRoleId);
        if (!$sourceRole) {
            return redirect()->to('/admin/permissions')->with('error', 'Rôle source non trouvé.');
        }

        return view('admin/permissions/copy_role', [
            'sourceRole' => $sourceRole,
        ]);
    }

    /**
     * Store copied role
     */
    public function storeCopy($sourceRoleId)
    {
        $session = session();
        $companyId = $session->get('company_id');
        $db = db_connect();

        $sourceRole = $this->roleModel->find($sourceRoleId);
        if (!$sourceRole) {
            return redirect()->to('/admin/permissions')->with('error', 'Rôle source non trouvé.');
        }

        $newName = $this->request->getPost('name');
        $description = $this->request->getPost('description') ?: $sourceRole['description'];

        if (empty($newName)) {
            return redirect()->back()->with('error', 'Le nom du nouveau rôle est requis.');
        }

        $db->transStart();

        // Create new role
        $roleData = [
            'name' => strtolower($newName),
            'description' => $description,
        ];

        try {
            $this->roleModel->insert(array_merge($roleData, [
                'company_id' => $companyId,
            ]));
            $newRoleId = $this->roleModel->getInsertID();
        } catch (\Exception $e) {
            $this->roleModel->insert($roleData);
            $newRoleId = $this->roleModel->getInsertID();
        }

        // Copy all permissions
        $sourcePermissions = $this->permissionModel->getForRole($sourceRoleId);
        foreach ($sourcePermissions as $perm) {
            $db->table('role_permissions')->insert([
                'role_id' => $newRoleId,
                'permission_id' => $perm['id'],
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Erreur lors de la copie du rôle.');
        }

        // Log the action
        $this->auditModel->log('role.copy', [
            'entity_type' => 'role',
            'entity_id' => $newRoleId,
            'new_values' => json_encode([
                'name' => $newName,
                'copied_from' => $sourceRole['name'],
            ]),
        ]);

        return redirect()->to('/admin/permissions')
            ->with('success', "Rôle '{$sourceRole['name']}' copié vers '{$newName}' avec succès.");
    }

    /**
     * Delete a custom role
     */
    public function deleteRole($roleId)
    {
        $session = session();
        $companyId = $session->get('company_id');

        $role = $this->roleModel->find($roleId);
        if (!$role) {
            return redirect()->to('/admin/permissions')->with('error', 'Rôle non trouvé.');
        }

        // Cannot delete system roles (those without company_id)
        if (empty($role['company_id'])) {
            return redirect()->to('/admin/permissions')->with('error', 'Impossible de supprimer un rôle système.');
        }

        // Check if role is in use
        $usersWithRole = db_connect()->table('user_company')
            ->where('role_id', $roleId)
            ->countAllResults();

        if ($usersWithRole > 0) {
            return redirect()->to('/admin/permissions')
                ->with('error', "Impossible de supprimer ce rôle : {$usersWithRole} utilisateur(s) l'utilisent encore.");
        }

        // Delete the role (cascade will delete role_permissions)
        $this->roleModel->delete($roleId);

        // Log the action
        $this->auditModel->log('role.delete', [
            'entity_type' => 'role',
            'entity_id' => $roleId,
            'old_values' => json_encode(['name' => $role['name']]),
        ]);

        return redirect()->to('/admin/permissions')
            ->with('success', "Rôle '{$role['name']}' supprimé.");
    }

    /**
     * Edit permissions for a specific role
     */
    public function editRole($roleId)
    {
        $role = $this->roleModel->find($roleId);
        if (!$role) {
            return redirect()->to('/admin/permissions')->with('error', 'Rôle non trouvé.');
        }

        // Get all permissions grouped by module
        $allPermissions = $this->permissionModel->findAll();
        $permissionsByModule = [];
        foreach ($allPermissions as $perm) {
            $module = $perm['module'] ?? 'other';
            $permissionsByModule[$module][] = $perm;
        }

        // Get current permissions for this role
        $currentPermissions = $this->permissionModel->getForRole($roleId);
        $permissionIds = array_column($currentPermissions, 'id');

        return view('admin/permissions/edit_role', [
            'role' => $role,
            'permissionsByModule' => $permissionsByModule,
            'permissionIds' => $permissionIds,
        ]);
    }

    /**
     * Update permissions for a role
     */
    public function updateRole($roleId)
    {
        $role = $this->roleModel->find($roleId);
        if (!$role) {
            return redirect()->to('/admin/permissions')->with('error', 'Rôle non trouvé.');
        }

        $session = session();
        $db = db_connect();

        // Get selected permissions
        $selectedPermissions = $this->request->getPost('permissions') ?? [];

        // Get current permissions
        $currentPermissions = $this->permissionModel->getForRole($roleId);
        $currentIds = array_column($currentPermissions, 'id');

        // Calculate changes for audit log
        $added = array_diff($selectedPermissions, $currentIds);
        $removed = array_diff($currentIds, $selectedPermissions);

        // Start transaction
        $db->transStart();

        // Remove all current permissions
        $db->table('role_permissions')->where('role_id', $roleId)->delete();

        // Add selected permissions
        foreach ($selectedPermissions as $permId) {
            $db->table('role_permissions')->insert([
                'role_id' => $roleId,
                'permission_id' => (int) $permId,
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour des permissions.');
        }

        // Log the change
        $this->auditModel->log('role.permissions_update', [
            'entity_type' => 'role',
            'entity_id' => $roleId,
            'old_values' => json_encode(['permission_ids' => $currentIds]),
            'new_values' => json_encode(['permission_ids' => $selectedPermissions]),
        ]);

        // Clear permission cache
        $cache = \Config\Services::cache();

        return redirect()->to('/admin/permissions')
            ->with('success', "Permissions du rôle '{$role['name']}' mises à jour. " . count($added) . " ajoutée(s), " . count($removed) . " retirée(s).");
    }
}
