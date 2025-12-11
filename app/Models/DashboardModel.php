<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\BaseBuilder;

/**
 * Dashboard Model
 * 
 * Provides data aggregation methods for the centralized dashboard.
 * All methods are multi-tenant aware and require a company_id.
 */
class DashboardModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    /**
     * Get current month revenue for a company
     */
    public function getCurrentMonthRevenue(string $companyId): float
    {
        $result = $this->db->query("
            SELECT COALESCE(SUM(f.montant_ttc), 0) AS revenue
            FROM facture f
            JOIN contact c ON c.id = f.contact_id
            WHERE c.company_id = ?
            AND EXTRACT(MONTH FROM f.date_emission) = EXTRACT(MONTH FROM CURRENT_DATE)
            AND EXTRACT(YEAR FROM f.date_emission) = EXTRACT(YEAR FROM CURRENT_DATE)
            AND f.statut != 'annulee'
        ", [$companyId])->getRow();

        return (float) ($result->revenue ?? 0);
    }

    /**
     * Get previous month revenue for a company
     */
    public function getPreviousMonthRevenue(string $companyId): float
    {
        $result = $this->db->query("
            SELECT COALESCE(SUM(f.montant_ttc), 0) AS revenue
            FROM facture f
            JOIN contact c ON c.id = f.contact_id
            WHERE c.company_id = ?
            AND EXTRACT(MONTH FROM f.date_emission) = EXTRACT(MONTH FROM CURRENT_DATE - INTERVAL '1 month')
            AND EXTRACT(YEAR FROM f.date_emission) = EXTRACT(YEAR FROM CURRENT_DATE - INTERVAL '1 month')
            AND f.statut != 'annulee'
        ", [$companyId])->getRow();

        return (float) ($result->revenue ?? 0);
    }

    /**
     * Calculate revenue change percentage
     */
    public function getRevenueChangePercent(string $companyId): float
    {
        $current = $this->getCurrentMonthRevenue($companyId);
        $previous = $this->getPreviousMonthRevenue($companyId);

        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    /**
     * Get pending invoices count for a company
     */
    public function getPendingInvoicesCount(string $companyId): int
    {
        $result = $this->db->query("
            SELECT COUNT(*) AS count
            FROM facture f
            JOIN contact c ON c.id = f.contact_id
            WHERE c.company_id = ?
            AND f.statut IN ('en_attente', 'envoyee')
        ", [$companyId])->getRow();

        return (int) ($result->count ?? 0);
    }

    /**
     * Get current treasury balance for a company
     */
    public function getCurrentTreasuryBalance(string $companyId): float
    {
        $result = $this->db->query("
            SELECT balance_after AS balance
            FROM treasury_entries
            WHERE company_id = ?
            ORDER BY transaction_date DESC, created_at DESC
            LIMIT 1
        ", [$companyId])->getRow();

        return (float) ($result->balance ?? 0);
    }

    /**
     * Get active customers count this month for a company
     */
    public function getActiveCustomersCount(string $companyId): int
    {
        $result = $this->db->query("
            SELECT COUNT(DISTINCT f.contact_id) AS count
            FROM facture f
            JOIN contact c ON c.id = f.contact_id
            WHERE c.company_id = ?
            AND EXTRACT(MONTH FROM f.date_emission) = EXTRACT(MONTH FROM CURRENT_DATE)
            AND EXTRACT(YEAR FROM f.date_emission) = EXTRACT(YEAR FROM CURRENT_DATE)
        ", [$companyId])->getRow();

        return (int) ($result->count ?? 0);
    }

    /**
     * Get revenue evolution for the last N months
     */
    public function getRevenueEvolution(string $companyId, int $months = 6): array
    {
        $results = $this->db->query("
            SELECT 
                TO_CHAR(f.date_emission, 'YYYY-MM') AS month,
                TO_CHAR(f.date_emission, 'Mon') AS month_label,
                COALESCE(SUM(f.montant_ttc), 0) AS revenue
            FROM facture f
            JOIN contact c ON c.id = f.contact_id
            WHERE c.company_id = ?
            AND f.date_emission >= CURRENT_DATE - INTERVAL '{$months} months'
            AND f.statut != 'annulee'
            GROUP BY TO_CHAR(f.date_emission, 'YYYY-MM'), TO_CHAR(f.date_emission, 'Mon')
            ORDER BY month
        ", [$companyId])->getResultArray();

        // Fill in missing months with 0
        $data = [];
        $labels = [];
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = date('Y-m', strtotime("-{$i} months"));
            $label = date('M', strtotime("-{$i} months"));
            $labels[] = $label;
            $data[$date] = 0;
        }

        foreach ($results as $row) {
            if (isset($data[$row['month']])) {
                $data[$row['month']] = (float) $row['revenue'];
            }
        }

        return [
            'labels' => $labels,
            'data' => array_values($data)
        ];
    }

    /**
     * Get revenue by category for current month
     */
    public function getRevenueByCategory(string $companyId): array
    {
        // First check if there's category data available
        $results = $this->db->query("
            SELECT 
                COALESCE(cat.name, 'Non catégorisé') AS category,
                COALESCE(SUM(f.montant_ttc), 0) AS revenue
            FROM facture f
            JOIN contact c ON c.id = f.contact_id
            LEFT JOIN categories cat ON cat.company_id = c.company_id
            WHERE c.company_id = ?
            AND EXTRACT(MONTH FROM f.date_emission) = EXTRACT(MONTH FROM CURRENT_DATE)
            AND EXTRACT(YEAR FROM f.date_emission) = EXTRACT(YEAR FROM CURRENT_DATE)
            AND f.statut != 'annulee'
            GROUP BY COALESCE(cat.name, 'Non catégorisé')
            ORDER BY revenue DESC
            LIMIT 5
        ", [$companyId])->getResultArray();

        if (empty($results)) {
            return [
                'labels' => ['Aucune donnée'],
                'data' => [0]
            ];
        }

        $labels = [];
        $data = [];

        foreach ($results as $row) {
            $labels[] = $row['category'];
            $data[] = (float) $row['revenue'];
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Get invoice status distribution
     */
    public function getInvoiceStatusData(string $companyId): array
    {
        $results = $this->db->query("
            SELECT 
                f.statut AS status,
                COUNT(*) AS count
            FROM facture f
            JOIN contact c ON c.id = f.contact_id
            WHERE c.company_id = ?
            GROUP BY f.statut
        ", [$companyId])->getResultArray();

        $statusLabels = [
            'brouillon' => 'Brouillon',
            'en_attente' => 'En attente',
            'envoyee' => 'Envoyée',
            'payee' => 'Payée',
            'annulee' => 'Annulée',
            'partiellement_payee' => 'Partielle'
        ];

        $labels = [];
        $data = [];

        foreach ($results as $row) {
            $labels[] = $statusLabels[$row['status']] ?? ucfirst($row['status']);
            $data[] = (int) $row['count'];
        }

        if (empty($labels)) {
            return [
                'labels' => ['Aucune facture'],
                'data' => [0]
            ];
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Get recent invoices with contact info
     */
    public function getRecentInvoices(string $companyId, int $limit = 5): array
    {
        return $this->db->query("
            SELECT 
                f.id,
                f.numero_facture,
                COALESCE(c.entreprise, CONCAT(c.prenom, ' ', c.nom)) AS client_name,
                f.montant_ttc,
                f.date_emission,
                f.statut
            FROM facture f
            JOIN contact c ON c.id = f.contact_id
            WHERE c.company_id = ?
            ORDER BY f.date_emission DESC
            LIMIT ?
        ", [$companyId, $limit])->getResultArray();
    }

    /**
     * Get important notifications for user
     */
    public function getImportantNotifications(string $userId, int $limit = 5): array
    {
        return $this->db->query("
            SELECT 
                id,
                type,
                title,
                message,
                link,
                priority,
                created_at
            FROM notifications
            WHERE user_id = ?
            AND is_read = false
            ORDER BY 
                CASE priority 
                    WHEN 'urgent' THEN 1 
                    WHEN 'high' THEN 2 
                    ELSE 3 
                END,
                created_at DESC
            LIMIT ?
        ", [$userId, $limit])->getResultArray();
    }

    /**
     * Get all KPI data in one call
     */
    public function getAllKpiData(string $companyId): array
    {
        return [
            'revenue' => $this->getCurrentMonthRevenue($companyId),
            'revenue_change' => $this->getRevenueChangePercent($companyId),
            'pending_invoices' => $this->getPendingInvoicesCount($companyId),
            'treasury_balance' => $this->getCurrentTreasuryBalance($companyId),
            'active_customers' => $this->getActiveCustomersCount($companyId)
        ];
    }
}
