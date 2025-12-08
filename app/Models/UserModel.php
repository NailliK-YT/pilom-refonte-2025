<?php

namespace App\Models;

use CodeIgniter\Model;

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
        'role'
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
        'email' => 'required|valid_email|is_unique[users.email]',
        'password_hash' => 'required',
        'role' => 'permit_empty|in_list[admin,user,accountant]'
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['hashPassword', 'generateId'];
    protected $beforeUpdate = ['hashPassword'];

    /**
     * Hash password before insert/update
     */
    protected function hashPassword(array $data)
    {
        if (!isset($data['data']['password_hash'])) {
            return $data;
        }

        $data['data']['password_hash'] = password_hash($data['data']['password_hash'], PASSWORD_DEFAULT);

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
     * Trouve un utilisateur par email
     */
    public function findByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Vérifie si le mot de passe correspond
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
