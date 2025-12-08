<?php

namespace App\Models;

use CodeIgniter\Model;

class UserProfileModel extends Model
{
    protected $table = 'user_profiles';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id',
        'user_id',
        'first_name',
        'last_name',
        'phone',
        'profile_photo',
        'locale',
        'timezone'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'user_id' => 'required|is_not_unique[users.id]',
        'first_name' => 'permit_empty|max_length[255]',
        'last_name' => 'permit_empty|max_length[255]',
        'phone' => 'permit_empty|max_length[20]',
        'profile_photo' => 'permit_empty|max_length[255]',
        'locale' => 'required|in_list[fr_FR,en_US,es_ES]',
        'timezone' => 'required|max_length[50]'
    ];

    protected $validationMessages = [
        'user_id' => [
            'required' => 'L\'identifiant utilisateur est requis.',
            'is_not_unique' => 'L\'utilisateur n\'existe pas.'
        ],
        'locale' => [
            'in_list' => 'La langue sélectionnée n\'est pas valide.'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateId'];

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
     * Get user profile by user ID
     */
    public function getByUserId(string $userId)
    {
        return $this->where('user_id', $userId)->first();
    }

    /**
     * Create or update profile for a user
     */
    public function upsertProfile(string $userId, array $data)
    {
        $existing = $this->getByUserId($userId);

        if ($existing) {
            return $this->update($existing['id'], $data);
        }

        $data['user_id'] = $userId;
        return $this->insert($data);
    }

    /**
     * Format phone number (basic French format)
     */
    public function formatPhone(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Format French numbers (10 digits)
        if (strlen($phone) === 10) {
            return substr($phone, 0, 2) . ' ' .
                substr($phone, 2, 2) . ' ' .
                substr($phone, 4, 2) . ' ' .
                substr($phone, 6, 2) . ' ' .
                substr($phone, 8, 2);
        }

        return $phone;
    }
}
