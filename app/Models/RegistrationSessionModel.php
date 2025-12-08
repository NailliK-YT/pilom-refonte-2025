<?php

namespace App\Models;

use CodeIgniter\Model;

class RegistrationSessionModel extends Model
{
    protected $table = 'registration_sessions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['id', 'session_token', 'step', 'data', 'expires_at'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'session_token' => 'required',
        'step' => 'required|integer|greater_than[0]|less_than[4]',
        'data' => 'required'
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
     * Create a new registration session
     */
    public function createSession(array $sessionData): string
    {
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+7 days'));

        $data = [
            'session_token' => $token,
            'step' => $sessionData['step'] ?? 1,
            'data' => json_encode($sessionData['data'] ?? []),
            'expires_at' => $expiresAt,
        ];

        $this->insert($data);

        return $token;
    }

    /**
     * Get session by token
     */
    public function getSession(string $token): ?array
    {
        $session = $this->where('session_token', $token)
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->first();

        if ($session) {
            $session['data'] = json_decode($session['data'], true);
        }

        return $session;
    }

    /**
     * Update existing session
     */
    public function updateSession(string $token, int $step, array $sessionData): bool
    {
        $session = $this->where('session_token', $token)->first();

        if (!$session) {
            return false;
        }

        return $this->update($session['id'], [
            'step' => $step,
            'data' => json_encode($sessionData),
            'expires_at' => date('Y-m-d H:i:s', strtotime('+7 days')),
        ]);
    }

    /**
     * Clean expired sessions
     */
    public function cleanExpiredSessions(): int
    {
        return $this->where('expires_at <', date('Y-m-d H:i:s'))->delete();
    }
}
