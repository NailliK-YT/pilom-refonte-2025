<?php

namespace App\Models;

use CodeIgniter\Model;

class AccountDeletionModel extends Model
{
    protected $table = 'account_deletion_requests';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id',
        'user_id',
        'requested_at',
        'scheduled_deletion_at',
        'reason',
        'status'
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
        'requested_at' => 'required',
        'scheduled_deletion_at' => 'required',
        'status' => 'required|in_list[pending,cancelled,completed]'
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
     * Create a deletion request with 30-day grace period
     */
    public function requestDeletion(string $userId, ?string $reason = null): bool
    {
        // Check if there's already a pending request
        $existing = $this->getPendingRequest($userId);
        if ($existing) {
            return false;
        }

        $now = date('Y-m-d H:i:s');
        $scheduledDate = date('Y-m-d H:i:s', strtotime('+30 days'));

        $data = [
            'user_id' => $userId,
            'requested_at' => $now,
            'scheduled_deletion_at' => $scheduledDate,
            'reason' => $reason,
            'status' => 'pending'
        ];

        return $this->insert($data) !== false;
    }

    /**
     * Get pending deletion request for a user
     */
    public function getPendingRequest(string $userId)
    {
        return $this->where('user_id', $userId)
            ->where('status', 'pending')
            ->first();
    }

    /**
     * Cancel a deletion request
     */
    public function cancelRequest(string $userId): bool
    {
        $request = $this->getPendingRequest($userId);

        if (!$request) {
            return false;
        }

        return $this->update($request['id'], ['status' => 'cancelled']);
    }

    /**
     * Get all deletion requests ready for processing
     */
    public function getRequestsReadyForDeletion(): array
    {
        return $this->where('status', 'pending')
            ->where('scheduled_deletion_at <=', date('Y-m-d H:i:s'))
            ->findAll();
    }

    /**
     * Mark a request as completed
     */
    public function markCompleted(string $requestId): bool
    {
        return $this->update($requestId, ['status' => 'completed']);
    }

    /**
     * Get days remaining before deletion
     */
    public function getDaysRemaining(string $userId): ?int
    {
        $request = $this->getPendingRequest($userId);

        if (!$request) {
            return null;
        }

        $scheduledDate = strtotime($request['scheduled_deletion_at']);
        $now = time();
        $diff = $scheduledDate - $now;

        return max(0, ceil($diff / (60 * 60 * 24)));
    }

    /**
     * Process account deletion (to be called by cron job)
     * This permanently deletes the user and all related data
     */
    public function processDeletion(string $requestId): bool
    {
        $request = $this->find($requestId);

        if (!$request || $request['status'] !== 'pending') {
            return false;
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $userId = $request['user_id'];

            // Delete related records (cascade should handle most, but explicit deletion for safety)
            $db->table('user_profiles')->where('user_id', $userId)->delete();
            $db->table('notification_preferences')->where('user_id', $userId)->delete();
            $db->table('login_history')->where('user_id', $userId)->delete();

            // Delete user
            $db->table('users')->where('id', $userId)->delete();

            // Mark request as completed
            $this->markCompleted($requestId);

            $db->transComplete();

            return $db->transStatus();
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Account deletion failed: ' . $e->getMessage());
            return false;
        }
    }
}
