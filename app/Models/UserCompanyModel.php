<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * UserCompanyModel - Manages the relationship between users and companies
 * Supports multi-company user management with role per company
 */
class UserCompanyModel extends Model
{
    protected $table = 'user_company';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'id',
        'user_id',
        'company_id',
        'role_id',
        'is_primary',
        'invited_by',
        'invitation_token',
        'invitation_expires',
        'invited_at',
        'accepted_at',
        'status'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'user_id' => 'required',
        'company_id' => 'required',
        'role_id' => 'required|integer',
        'status' => 'in_list[pending,active,suspended,removed]'
    ];

    protected $beforeInsert = ['generateId'];

    /**
     * Generate UUID for new records
     */
    protected function generateId(array $data): array
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = $this->generateUUID();
        }
        return $data;
    }

    /**
     * Get all companies for a user with role information
     */
    public function getUserCompanies(string $userId): array
    {
        return $this->select('user_company.*, companies.name as company_name, companies.logo as company_logo, 
                              roles.name as role_name, roles.description as role_description')
            ->join('companies', 'companies.id = user_company.company_id')
            ->join('roles', 'roles.id = user_company.role_id')
            ->where('user_company.user_id', $userId)
            ->where('user_company.status', 'active')
            ->findAll();
    }

    /**
     * Get all users for a company with role information
     */
    public function getCompanyUsers(string $companyId): array
    {
        return $this->select('user_company.*, users.email, users.first_name, users.last_name, 
                              users.avatar, users.last_login, users.status as user_status,
                              roles.name as role_name, roles.description as role_description')
            ->join('users', 'users.id = user_company.user_id')
            ->join('roles', 'roles.id = user_company.role_id')
            ->where('user_company.company_id', $companyId)
            ->whereIn('user_company.status', ['active', 'pending'])
            ->orderBy('roles.id', 'ASC')
            ->orderBy('users.first_name', 'ASC')
            ->findAll();
    }

    /**
     * Get user's role in a specific company
     */
    public function getUserRoleInCompany(string $userId, string $companyId): ?array
    {
        return $this->select('user_company.*, roles.name as role_name, roles.description as role_description')
            ->join('roles', 'roles.id = user_company.role_id')
            ->where('user_company.user_id', $userId)
            ->where('user_company.company_id', $companyId)
            ->where('user_company.status', 'active')
            ->first();
    }

    /**
     * Create an invitation for a user to join a company
     */
    public function createInvitation(string $email, string $companyId, int $roleId, string $invitedBy): array
    {
        $userModel = new UserModel();
        $existingUser = $userModel->findByEmail($email);

        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+7 days'));

        if ($existingUser) {
            // Check if already in company
            $existing = $this->where('user_id', $existingUser['id'])
                ->where('company_id', $companyId)
                ->first();

            if ($existing) {
                return ['success' => false, 'error' => 'Cet utilisateur fait déjà partie de l\'entreprise.'];
            }

            // Create invitation for existing user
            $this->insert([
                'user_id' => $existingUser['id'],
                'company_id' => $companyId,
                'role_id' => $roleId,
                'invited_by' => $invitedBy,
                'invitation_token' => $token,
                'invitation_expires' => $expires,
                'invited_at' => date('Y-m-d H:i:s'),
                'status' => 'pending',
                'is_primary' => false,
            ]);

            return [
                'success' => true,
                'existing_user' => true,
                'token' => $token,
                'user_id' => $existingUser['id'],
            ];
        }

        // Create a placeholder for new user invitation
        // User will be created when they accept the invitation
        $placeholderId = $this->generateUUID();

        return [
            'success' => true,
            'existing_user' => false,
            'token' => $token,
            'placeholder_id' => $placeholderId,
            'invitation_data' => [
                'email' => $email,
                'company_id' => $companyId,
                'role_id' => $roleId,
                'invited_by' => $invitedBy,
                'token' => $token,
                'expires' => $expires,
            ]
        ];
    }

    /**
     * Accept an invitation
     */
    public function acceptInvitation(string $token): array
    {
        $invitation = $this->where('invitation_token', $token)
            ->where('invitation_expires >', date('Y-m-d H:i:s'))
            ->where('status', 'pending')
            ->first();

        if (!$invitation) {
            return ['success' => false, 'error' => 'Invitation invalide ou expirée.'];
        }

        $this->update($invitation['id'], [
            'status' => 'active',
            'invitation_token' => null,
            'invitation_expires' => null,
            'accepted_at' => date('Y-m-d H:i:s'),
        ]);

        return ['success' => true, 'invitation' => $invitation];
    }

    /**
     * Update user's role in a company
     */
    public function updateUserRole(string $userId, string $companyId, int $roleId): bool
    {
        $record = $this->where('user_id', $userId)
            ->where('company_id', $companyId)
            ->first();

        if (!$record) {
            return false;
        }

        return $this->update($record['id'], ['role_id' => $roleId]);
    }

    /**
     * Suspend a user from a company
     */
    public function suspendUser(string $userId, string $companyId): bool
    {
        $record = $this->where('user_id', $userId)
            ->where('company_id', $companyId)
            ->first();

        if (!$record) {
            return false;
        }

        return $this->update($record['id'], ['status' => 'suspended']);
    }

    /**
     * Activate a suspended user in a company
     */
    public function activateUser(string $userId, string $companyId): bool
    {
        $record = $this->where('user_id', $userId)
            ->where('company_id', $companyId)
            ->first();

        if (!$record) {
            return false;
        }

        return $this->update($record['id'], ['status' => 'active']);
    }

    /**
     * Remove a user from a company
     */
    public function removeUserFromCompany(string $userId, string $companyId): bool
    {
        $record = $this->where('user_id', $userId)
            ->where('company_id', $companyId)
            ->first();

        if (!$record) {
            return false;
        }

        // Don't delete, just mark as removed for audit trail
        return $this->update($record['id'], ['status' => 'removed']);
    }

    /**
     * Get the primary company for a user
     */
    public function getPrimaryCompany(string $userId): ?array
    {
        return $this->select('user_company.*, companies.name as company_name')
            ->join('companies', 'companies.id = user_company.company_id')
            ->where('user_company.user_id', $userId)
            ->where('user_company.is_primary', true)
            ->where('user_company.status', 'active')
            ->first();
    }

    /**
     * Set a company as primary for a user
     */
    public function setPrimaryCompany(string $userId, string $companyId): bool
    {
        // First, unset all as primary
        $this->where('user_id', $userId)->set(['is_primary' => false])->update();

        // Then set the specified one as primary
        $record = $this->where('user_id', $userId)
            ->where('company_id', $companyId)
            ->first();

        if (!$record) {
            return false;
        }

        return $this->update($record['id'], ['is_primary' => true]);
    }

    /**
     * Count active users in a company
     */
    public function countCompanyUsers(string $companyId): int
    {
        return $this->where('company_id', $companyId)
            ->where('status', 'active')
            ->countAllResults();
    }

    /**
     * Check if user is admin in company
     */
    public function isUserAdmin(string $userId, string $companyId): bool
    {
        $roleInfo = $this->getUserRoleInCompany($userId, $companyId);
        return $roleInfo && $roleInfo['role_name'] === 'admin';
    }

    /**
     * Generate a UUID v4
     */
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
