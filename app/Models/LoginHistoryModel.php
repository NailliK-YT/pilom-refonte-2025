<?php

namespace App\Models;

use CodeIgniter\Model;

class LoginHistoryModel extends Model
{
    protected $table = 'login_history';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id',
        'user_id',
        'ip_address',
        'user_agent',
        'login_at',
        'success'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = false;

    // Validation
    protected $validationRules = [
        'user_id' => 'required|is_not_unique[users.id]',
        'login_at' => 'required',
        'success' => 'required|in_list[0,1]'
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
     * Log a login attempt
     */
    public function logLogin(string $userId, string $ipAddress, string $userAgent, bool $success = true): bool
    {
        $data = [
            'user_id' => $userId,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'login_at' => date('Y-m-d H:i:s'),
            'success' => $success
        ];

        return $this->insert($data) !== false;
    }

    /**
     * Get login history for a user
     */
    public function getHistory(string $userId, int $limit = 20): array
    {
        return $this->where('user_id', $userId)
            ->orderBy('login_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get recent failed login attempts
     */
    public function getFailedAttempts(string $userId, int $minutes = 30): array
    {
        $since = date('Y-m-d H:i:s', strtotime("-{$minutes} minutes"));

        return $this->where('user_id', $userId)
            ->where('success', false)
            ->where('login_at >=', $since)
            ->orderBy('login_at', 'DESC')
            ->findAll();
    }

    /**
     * Get successful logins count in time period
     */
    public function getSuccessfulLoginsCount(string $userId, int $days = 30): int
    {
        $since = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        return $this->where('user_id', $userId)
            ->where('success', true)
            ->where('login_at >=', $since)
            ->countAllResults();
    }

    /**
     * Parse user agent to get device/browser info
     */
    public function parseUserAgent(string $userAgent): array
    {
        $info = [
            'browser' => 'Unknown',
            'platform' => 'Unknown',
            'device' => 'Desktop'
        ];

        // Detect browser
        if (preg_match('/firefox/i', $userAgent)) {
            $info['browser'] = 'Firefox';
        } elseif (preg_match('/chrome/i', $userAgent) && !preg_match('/edg/i', $userAgent)) {
            $info['browser'] = 'Chrome';
        } elseif (preg_match('/safari/i', $userAgent) && !preg_match('/chrome/i', $userAgent)) {
            $info['browser'] = 'Safari';
        } elseif (preg_match('/edg/i', $userAgent)) {
            $info['browser'] = 'Edge';
        }

        // Detect platform
        if (preg_match('/windows/i', $userAgent)) {
            $info['platform'] = 'Windows';
        } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
            $info['platform'] = 'macOS';
        } elseif (preg_match('/linux/i', $userAgent)) {
            $info['platform'] = 'Linux';
        } elseif (preg_match('/android/i', $userAgent)) {
            $info['platform'] = 'Android';
            $info['device'] = 'Mobile';
        } elseif (preg_match('/iphone|ipad/i', $userAgent)) {
            $info['platform'] = 'iOS';
            $info['device'] = preg_match('/ipad/i', $userAgent) ? 'Tablet' : 'Mobile';
        }

        return $info;
    }
}
