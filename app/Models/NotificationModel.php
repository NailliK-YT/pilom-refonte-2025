<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id',
        'user_id',
        'company_id',
        'type',
        'title',
        'message',
        'link',
        'is_read',
        'read_at',
        'priority',
        'data'
    ];

    protected bool $allowEmptyInserts = false;
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateId'];

    protected function generateId(array $data)
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );
        }
        return $data;
    }

    /**
     * Get notifications for a user
     */
    public function getForUser(string $userId, int $limit = 50, bool $unreadOnly = false): array
    {
        $builder = $this->where('user_id', $userId);
        
        if ($unreadOnly) {
            $builder->where('is_read', false);
        }
        
        return $builder->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get unread count for a user
     */
    public function getUnreadCount(string $userId): int
    {
        return $this->where('user_id', $userId)
            ->where('is_read', false)
            ->countAllResults();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(string $id): bool
    {
        return $this->update($id, [
            'is_read' => true,
            'read_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead(string $userId): bool
    {
        return $this->where('user_id', $userId)
            ->where('is_read', false)
            ->set([
                'is_read' => true,
                'read_at' => date('Y-m-d H:i:s')
            ])
            ->update();
    }

    /**
     * Create a notification
     */
    public function createNotification(string $userId, string $type, string $title, string $message, array $options = []): bool
    {
        $data = [
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'company_id' => $options['company_id'] ?? null,
            'link' => $options['link'] ?? null,
            'priority' => $options['priority'] ?? 'normal',
            'data' => isset($options['data']) ? json_encode($options['data']) : null
        ];

        return $this->insert($data) !== false;
    }

    /**
     * Create invoice notification
     */
    public function notifyInvoiceCreated(string $userId, string $invoiceNumber, ?string $companyId = null): bool
    {
        return $this->createNotification(
            $userId,
            'invoice',
            'Nouvelle facture créée',
            "La facture {$invoiceNumber} a été créée avec succès.",
            [
                'company_id' => $companyId,
                'link' => '/factures',
                'priority' => 'normal'
            ]
        );
    }

    /**
     * Create payment notification
     */
    public function notifyPaymentReceived(string $userId, float $amount, string $invoiceNumber, ?string $companyId = null): bool
    {
        return $this->createNotification(
            $userId,
            'payment',
            'Paiement reçu',
            "Un paiement de " . number_format($amount, 2, ',', ' ') . " € a été enregistré pour la facture {$invoiceNumber}.",
            [
                'company_id' => $companyId,
                'link' => '/reglements',
                'priority' => 'high'
            ]
        );
    }

    /**
     * Create treasury alert notification
     */
    public function notifyTreasuryAlert(string $userId, string $alertName, float $currentBalance, ?string $companyId = null): bool
    {
        return $this->createNotification(
            $userId,
            'alert',
            'Alerte trésorerie',
            "L'alerte \"{$alertName}\" a été déclenchée. Solde actuel : " . number_format($currentBalance, 2, ',', ' ') . " €",
            [
                'company_id' => $companyId,
                'link' => '/treasury',
                'priority' => 'urgent'
            ]
        );
    }

    /**
     * Create system notification
     */
    public function notifySystem(string $userId, string $title, string $message, ?string $link = null): bool
    {
        return $this->createNotification(
            $userId,
            'system',
            $title,
            $message,
            [
                'link' => $link,
                'priority' => 'normal'
            ]
        );
    }

	/**
	 * Notifier une activité suspecte
	 */
	public function notifySuspiciousActivity(string $userId, string $activityDescription, ?string $companyId = null): bool
	{
		return $this->createNotification(
			$userId,
			'alert',
			'Activité suspecte détectée',
			"Une activité suspecte a été détectée : {$activityDescription}",
			[
				'company_id' => $companyId,
				'link' => '/security', // page de surveillance/admin
				'priority' => 'urgent'
			]
		);
	}

    /**
     * Delete old read notifications (cleanup)
     */
    public function cleanupOld(int $daysOld = 30): int
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$daysOld} days"));
        
        return $this->where('is_read', true)
            ->where('read_at <', $cutoffDate)
            ->delete();
    }
}
