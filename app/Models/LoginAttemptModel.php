<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * LoginAttemptModel - Manages brute-force protection for login attempts
 */
class LoginAttemptModel extends Model
{
    protected $table = 'login_attempts';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id',
        'ip_address',
        'email',
        'attempts_count',
        'first_attempt_at',
        'last_attempt_at',
        'blocked_until',
        'user_agent'
    ];

    protected bool $allowEmptyInserts = false;
    protected $useTimestamps = false;

    // Configuration
    private const MAX_ATTEMPTS = 5;
    private const BLOCK_DURATION_MINUTES = 15;
    private const WINDOW_MINUTES = 30;

    // Callbacks - disabled, we handle ID/timestamps in recordAttempt directly
    protected $allowCallbacks = false;
    protected $beforeInsert = [];

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
     * Record a failed login attempt
     * 
     * @param string $ip IP address
     * @param string|null $email Email attempted
     * @param string|null $userAgent User agent string
     * @return bool Whether the user is now blocked
     */
    public function recordAttempt(string $ip, ?string $email = null, ?string $userAgent = null): bool
    {
        $now = date('Y-m-d H:i:s');
        $windowStart = date('Y-m-d H:i:s', strtotime("-" . self::WINDOW_MINUTES . " minutes"));

        // Use fresh builder for each query to avoid state pollution
        $builder = $this->db->table($this->table);
        $builder->where('ip_address', $ip);
        $builder->where('first_attempt_at >=', $windowStart);

        if ($email !== null && $email !== '') {
            $builder->where('email', $email);
        } else {
            $builder->where('email IS NULL', null, false);
        }

        $existingRecord = $builder->get()->getRowArray();

        // DEBUG LOGGING - remove after testing
        log_message('error', "[LoginAttempt] IP: {$ip}, Email: " . ($email ?? 'NULL'));
        log_message('error', "[LoginAttempt] Existing: " . ($existingRecord ? "count=" . $existingRecord['attempts_count'] : "NONE"));

        if ($existingRecord) {
            // Update existing record
            $newCount = (int) $existingRecord['attempts_count'] + 1;
            $blockedUntil = null;

            log_message('error', "[LoginAttempt] NewCount: {$newCount}, MAX: " . self::MAX_ATTEMPTS);

            // Check if should block (>= MAX means at 5 attempts, block)
            if ($newCount >= self::MAX_ATTEMPTS) {
                $blockedUntil = date('Y-m-d H:i:s', strtotime("+" . self::BLOCK_DURATION_MINUTES . " minutes"));
                log_message('error', "[LoginAttempt] BLOCKING until: {$blockedUntil}");
            }

            $this->db->table($this->table)
                ->where('id', $existingRecord['id'])
                ->update([
                    'attempts_count' => $newCount,
                    'last_attempt_at' => $now,
                    'blocked_until' => $blockedUntil,
                    'user_agent' => $userAgent ?? $existingRecord['user_agent']
                ]);

            return $blockedUntil !== null;
        }

        // Create new record
        $this->db->table($this->table)->insert([
            'id' => $this->generateUUID(),
            'ip_address' => $ip,
            'email' => $email,
            'attempts_count' => 1,
            'first_attempt_at' => $now,
            'last_attempt_at' => $now,
            'user_agent' => $userAgent
        ]);

        return false;
    }

    /**
     * Check if an IP/email combination is currently blocked
     * 
     * @param string $ip IP address
     * @param string|null $email Email to check
     * @return bool Whether blocked
     */
    public function isBlocked(string $ip, ?string $email = null): bool
    {
        $now = date('Y-m-d H:i:s');

        // Use fresh builder to avoid state pollution
        $builder = $this->db->table($this->table);
        $builder->where('ip_address', $ip);
        $builder->where('blocked_until >', $now);

        if ($email !== null && $email !== '') {
            $builder->groupStart();
            $builder->where('email', $email);
            $builder->orWhere('email IS NULL', null, false);
            $builder->groupEnd();
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * Get the blocked_until timestamp for an IP/email
     * 
     * @param string $ip IP address
     * @param string|null $email Email
     * @return string|null The timestamp or null if not blocked
     */
    public function getBlockedUntil(string $ip, ?string $email = null): ?string
    {
        $now = date('Y-m-d H:i:s');

        // Use fresh builder
        $builder = $this->db->table($this->table);
        $builder->select('blocked_until');
        $builder->where('ip_address', $ip);
        $builder->where('blocked_until >', $now);

        if ($email !== null && $email !== '') {
            $builder->groupStart();
            $builder->where('email', $email);
            $builder->orWhere('email IS NULL', null, false);
            $builder->groupEnd();
        }

        $builder->orderBy('blocked_until', 'DESC');
        $result = $builder->get()->getRowArray();

        return $result ? $result['blocked_until'] : null;
    }

    /**
     * Get remaining attempts for an IP/email
     * 
     * @param string $ip IP address
     * @param string|null $email Email
     * @return int Remaining attempts
     */
    public function getRemainingAttempts(string $ip, ?string $email = null): int
    {
        $windowStart = date('Y-m-d H:i:s', strtotime("-" . self::WINDOW_MINUTES . " minutes"));

        // Use fresh builder
        $builder = $this->db->table($this->table);
        $builder->select('attempts_count');
        $builder->where('ip_address', $ip);
        $builder->where('first_attempt_at >=', $windowStart);

        if ($email !== null && $email !== '') {
            $builder->groupStart();
            $builder->where('email', $email);
            $builder->orWhere('email IS NULL', null, false);
            $builder->groupEnd();
        }

        $result = $builder->get()->getRowArray();

        if (!$result) {
            return self::MAX_ATTEMPTS;
        }

        return max(0, self::MAX_ATTEMPTS - (int) $result['attempts_count']);
    }

    /**
     * Reset attempts after successful login
     * 
     * @param string $ip IP address
     * @param string|null $email Email
     * @return void
     */
    public function resetAttempts(string $ip, ?string $email = null): void
    {
        $query = $this->where('ip_address', $ip);

        if ($email) {
            $query->groupStart()
                ->where('email', $email)
                ->orWhere('email IS NULL', null, false)
                ->groupEnd();
        }

        $query->delete();
    }

    /**
     * Clean up expired records (for cron job)
     * 
     * @return int Number of records deleted
     */
    public function cleanupExpired(): int
    {
        $expiryTime = date('Y-m-d H:i:s', strtotime("-" . (self::WINDOW_MINUTES + self::BLOCK_DURATION_MINUTES) . " minutes"));

        return $this->where('last_attempt_at <', $expiryTime)
            ->where('blocked_until IS NULL OR blocked_until <', date('Y-m-d H:i:s'))
            ->delete();
    }

    /**
     * Get configuration values
     */
    public static function getMaxAttempts(): int
    {
        return self::MAX_ATTEMPTS;
    }

    public static function getBlockDurationMinutes(): int
    {
        return self::BLOCK_DURATION_MINUTES;
    }
}
