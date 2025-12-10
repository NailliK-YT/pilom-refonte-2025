<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * BlogNewsletterModel
 * 
 * Manages newsletter subscribers (email collection for future use)
 */
class BlogNewsletterModel extends Model
{
    protected $table = 'blog_newsletter_subscribers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'id',
        'email',
        'is_confirmed',
        'confirmation_token',
        'confirmed_at',
        'is_active',
        'unsubscribed_at',
        'unsubscribe_token',
        'ip_address',
        'source',
    ];

    protected $useTimestamps = false;
    protected $createdField = 'created_at';

    protected $validationRules = [
        'email' => 'required|valid_email|max_length[255]|is_unique[blog_newsletter_subscribers.email]',
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'Cette adresse email est déjà inscrite.',
        ],
    ];

    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateId', 'generateTokens'];

    protected function generateId(array $data): array
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = $this->generateUUID();
        }
        return $data;
    }

    protected function generateTokens(array $data): array
    {
        if (!isset($data['data']['confirmation_token'])) {
            $data['data']['confirmation_token'] = bin2hex(random_bytes(32));
        }
        if (!isset($data['data']['unsubscribe_token'])) {
            $data['data']['unsubscribe_token'] = bin2hex(random_bytes(32));
        }
        return $data;
    }

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
     * Subscribe an email
     */
    public function subscribe(string $email, ?string $source = null, ?string $ipAddress = null): array
    {
        // Check if already exists
        $existing = $this->where('email', $email)->first();

        if ($existing) {
            if ($existing['is_active']) {
                return ['success' => false, 'message' => 'Cette adresse email est déjà inscrite.'];
            }

            // Reactivate
            $this->update($existing['id'], [
                'is_active' => true,
                'unsubscribed_at' => null,
            ]);

            return ['success' => true, 'message' => 'Votre inscription a été réactivée.'];
        }

        $id = $this->generateUUID();

        $this->insert([
            'id' => $id,
            'email' => $email,
            'is_confirmed' => true, // No email verification for now
            'confirmed_at' => date('Y-m-d H:i:s'),
            'is_active' => true,
            'ip_address' => $ipAddress,
            'source' => $source ?? 'blog',
        ]);

        return ['success' => true, 'message' => 'Merci pour votre inscription !'];
    }

    /**
     * Unsubscribe by token
     */
    public function unsubscribeByToken(string $token): bool
    {
        $subscriber = $this->where('unsubscribe_token', $token)->first();

        if (!$subscriber) {
            return false;
        }

        return $this->update($subscriber['id'], [
            'is_active' => false,
            'unsubscribed_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get active subscribers
     */
    public function getActiveSubscribers(): array
    {
        return $this->where('is_active', true)
            ->where('is_confirmed', true)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Count active subscribers
     */
    public function countActive(): int
    {
        return $this->where('is_active', true)
            ->where('is_confirmed', true)
            ->countAllResults();
    }

    /**
     * Export subscribers as CSV
     */
    public function exportCsv(): string
    {
        $subscribers = $this->getActiveSubscribers();

        $csv = "Email,Inscrit le,Source\n";

        foreach ($subscribers as $sub) {
            $csv .= sprintf(
                "%s,%s,%s\n",
                $sub['email'],
                $sub['created_at'],
                $sub['source'] ?? ''
            );
        }

        return $csv;
    }

    /**
     * Get stats
     */
    public function getStats(): array
    {
        return [
            'total' => $this->countAllResults(false),
            'active' => $this->countActive(),
            'unsubscribed' => $this->where('is_active', false)->countAllResults(false),
        ];
    }
}
