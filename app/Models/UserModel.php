<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * UserModel - Enhanced with profile fields, password reset, and multi-company support
 */
class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id',
        'email',
        'password_hash',
        'remember_token',
        'company_id',
        'is_verified',
        'verification_token',
        'verification_token_expires',
        'role',
        'first_name',
        'last_name',
        'phone',
        'avatar',
        'status',
        'last_login',
        'password_reset_token',
        'password_reset_expires'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'email' => 'required|valid_email',
        'status' => 'permit_empty|in_list[active,suspended,deleted]'
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['hashPassword', 'generateId', 'setDefaultStatus'];
    protected $beforeUpdate = ['hashPassword'];

    /**
     * Hash password before insert/update
     */
    protected function hashPassword(array $data)
    {
        if (!isset($data['data']['password_hash'])) {
            return $data;
        }

        // Only hash if it's not already hashed
        if (strlen($data['data']['password_hash']) < 60 || !str_starts_with($data['data']['password_hash'], '$2')) {
            $data['data']['password_hash'] = password_hash($data['data']['password_hash'], PASSWORD_DEFAULT);
        }

        return $data;
    }

    /**
     * Generate UUID for new records
     */
    protected function generateId(array $data)
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = $this->generateUUID();
        }

        return $data;
    }

    /**
     * Set default status for new users
     */
    protected function setDefaultStatus(array $data)
    {
        if (!isset($data['data']['status'])) {
            $data['data']['status'] = 'active';
        }
        return $data;
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

    /**
     * Get user's full name
     */
    public function getFullName(array $user): string
    {
        $firstName = $user['first_name'] ?? '';
        $lastName = $user['last_name'] ?? '';

        $fullName = trim("$firstName $lastName");
        return $fullName ?: $user['email'];
    }

    /**
     * Generate verification token for email confirmation
     */
    public function generateVerificationToken(string $userId): string
    {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));

        $this->update($userId, [
            'verification_token' => $token,
            'verification_token_expires' => $expires,
        ]);

        return $token;
    }

    /**
     * Verify email using token
     */
    public function verifyEmail(string $token): bool
    {
        $user = $this->where('verification_token', $token)
            ->where('verification_token_expires >', date('Y-m-d H:i:s'))
            ->first();

        if (!$user) {
            return false;
        }

        return $this->update($user['id'], [
            'is_verified' => true,
            'verification_token' => null,
            'verification_token_expires' => null,
        ]);
    }

    /**
     * Generate password reset token
     */
    public function generatePasswordResetToken(string $email): ?string
    {
        $user = $this->findByEmail($email);

        if (!$user) {
            return null;
        }

        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $this->update($user['id'], [
            'password_reset_token' => $token,
            'password_reset_expires' => $expires,
        ]);

        return $token;
    }

    /**
     * Reset password using token
     */
    public function resetPassword(string $token, string $newPassword): array
    {
        $user = $this->where('password_reset_token', $token)
            ->where('password_reset_expires >', date('Y-m-d H:i:s'))
            ->first();

        if (!$user) {
            return ['success' => false, 'error' => 'Token invalide ou expiré.'];
        }

        $updated = $this->update($user['id'], [
            'password_hash' => $newPassword, // Will be hashed by callback
            'password_reset_token' => null,
            'password_reset_expires' => null,
        ]);

        return ['success' => $updated, 'user_id' => $user['id']];
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin(string $userId): bool
    {
        return $this->update($userId, [
            'last_login' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get user with company information
     */
    public function getUserWithCompany(string $userId)
    {
        return $this->select('users.*, companies.name as company_name, business_sectors.name as sector_name')
            ->join('companies', 'companies.id = users.company_id', 'left')
            ->join('business_sectors', 'business_sectors.id = companies.business_sector_id', 'left')
            ->find($userId);
    }

    /**
     * Get all users for a company
     */
    public function getUsersForCompany(string $companyId): array
    {
        return $this->where('company_id', $companyId)
            ->where('status !=', 'deleted')
            ->orderBy('first_name', 'ASC')
            ->findAll();
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Verify password matches
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Suspend a user
     */
    public function suspendUser(string $userId): bool
    {
        return $this->update($userId, ['status' => 'suspended']);
    }

    /**
     * Activate a user
     */
    public function activateUser(string $userId): bool
    {
        return $this->update($userId, ['status' => 'active']);
    }

    /**
     * Check if user account is active
     */
    public function isActive(string $userId): bool
    {
        $user = $this->find($userId);
        return $user && $user['status'] === 'active';
    }

    /**
     * Update user profile
     */
    public function updateProfile(string $userId, array $data): bool
    {
        // Only allow specific profile fields
        $allowedProfileFields = ['first_name', 'last_name', 'phone', 'avatar'];
        $profileData = array_intersect_key($data, array_flip($allowedProfileFields));

        if (empty($profileData)) {
            return false;
        }

        return $this->update($userId, $profileData);
    }

    /**
     * Change user password
     */
    public function changePassword(string $userId, string $currentPassword, string $newPassword): array
    {
        $user = $this->find($userId);

        if (!$user) {
            return ['success' => false, 'error' => 'Utilisateur non trouvé.'];
        }

        if (!$this->verifyPassword($currentPassword, $user['password_hash'])) {
            return ['success' => false, 'error' => 'Mot de passe actuel incorrect.'];
        }

        $updated = $this->update($userId, ['password_hash' => $newPassword]);

        return ['success' => $updated];
    }
}
