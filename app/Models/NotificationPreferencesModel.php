<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationPreferencesModel extends Model
{
    protected $table = 'notification_preferences';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id',
        'user_id',
        'email_notifications',
        'email_invoices',
        'email_quotes',
        'email_payments',
        'email_marketing',
        'push_notifications',
        'inapp_notifications'
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
        'email_notifications' => 'required|in_list[0,1]',
        'email_invoices' => 'required|in_list[0,1]',
        'email_quotes' => 'required|in_list[0,1]',
        'email_payments' => 'required|in_list[0,1]',
        'email_marketing' => 'required|in_list[0,1]',
        'push_notifications' => 'required|in_list[0,1]',
        'inapp_notifications' => 'required|in_list[0,1]'
    ];

    protected $validationMessages = [];
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
     * Get preferences by user ID
     */
    public function getByUserId(string $userId)
    {
        return $this->where('user_id', $userId)->first();
    }

    /**
     * Create default preferences for a new user
     */
    public function createDefaultPreferences(string $userId): bool
    {
        // Check if preferences already exist
        if ($this->getByUserId($userId)) {
            return true;
        }

        $data = [
            'user_id' => $userId,
            'email_notifications' => true,
            'email_invoices' => true,
            'email_quotes' => true,
            'email_payments' => true,
            'email_marketing' => false,
            'push_notifications' => true,
            'inapp_notifications' => true
        ];

        return $this->insert($data) !== false;
    }

    /**
     * Update preferences for a user
     */
    public function updatePreferences(string $userId, array $preferences): bool
    {
        $existing = $this->getByUserId($userId);

        if (!$existing) {
            // Create if doesn't exist
            $preferences['user_id'] = $userId;
            return $this->insert($preferences) !== false;
        }

        return $this->update($existing['id'], $preferences);
    }

    /**
     * Check if user should receive email notifications for a specific type
     */
    public function shouldNotify(string $userId, string $type): bool
    {
        $preferences = $this->getByUserId($userId);

        if (!$preferences) {
            // Default to true if no preferences set
            return true;
        }

        return match ($type) {
            'email' => (bool) $preferences['email_notifications'],
            'invoice' => (bool) $preferences['email_invoices'],
            'quote' => (bool) $preferences['email_quotes'],
            'payment' => (bool) $preferences['email_payments'],
            'marketing' => (bool) $preferences['email_marketing'],
            'push' => (bool) $preferences['push_notifications'],
            'inapp' => (bool) $preferences['inapp_notifications'],
            default => true
        };
    }
}
