<?php

namespace App\Models;

use CodeIgniter\Model;

class TreasuryAlertModel extends Model
{
    protected $table = 'treasury_alerts';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id',
        'company_id',
        'name',
        'threshold_type',
        'threshold_amount',
        'is_active',
        'last_triggered_at'
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
     * Get alerts for a company
     */
    public function getByCompanyId(string $companyId): array
    {
        return $this->where('company_id', $companyId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get active alerts for a company
     */
    public function getActiveAlerts(string $companyId): array
    {
        return $this->where('company_id', $companyId)
            ->where('is_active', true)
            ->findAll();
    }

    /**
     * Check if any alerts should be triggered
     */
    public function checkAlerts(string $companyId, float $currentBalance): array
    {
        $triggeredAlerts = [];
        $activeAlerts = $this->getActiveAlerts($companyId);
        
        foreach ($activeAlerts as $alert) {
            $threshold = (float) $alert['threshold_amount'];
            $shouldTrigger = false;
            
            if ($alert['threshold_type'] === 'below' && $currentBalance < $threshold) {
                $shouldTrigger = true;
            } elseif ($alert['threshold_type'] === 'above' && $currentBalance > $threshold) {
                $shouldTrigger = true;
            }
            
            if ($shouldTrigger) {
                // Update last triggered
                $this->update($alert['id'], ['last_triggered_at' => date('Y-m-d H:i:s')]);
                $triggeredAlerts[] = $alert;
            }
        }
        
        return $triggeredAlerts;
    }
}
