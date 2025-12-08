<?php

namespace App\Controllers;

use App\Models\AuditLogModel;
use App\Models\UserModel;

/**
 * Controller for viewing and exporting audit logs
 */
class AuditController extends BaseController
{
    protected AuditLogModel $auditModel;
    protected UserModel $userModel;

    public function __construct()
    {
        $this->auditModel = new AuditLogModel();
        $this->userModel = new UserModel();
    }

    /**
     * Display audit logs with filters
     */
    public function index()
    {
        $session = session();
        $companyId = $session->get('company_id');

        // Get filter parameters
        $filters = [
            'action' => $this->request->getGet('action'),
            'user_id' => $this->request->getGet('user_id'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
        ];

        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 50;

        // Build query
        $builder = $this->auditModel->builder();
        $builder->select('audit_logs.*, users.email as user_email, users.first_name, users.last_name');
        $builder->join('users', 'users.id = audit_logs.user_id', 'left');
        $builder->where('audit_logs.company_id', $companyId);

        if (!empty($filters['action'])) {
            $builder->like('audit_logs.action', $filters['action']);
        }

        if (!empty($filters['user_id'])) {
            $builder->where('audit_logs.user_id', $filters['user_id']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('audit_logs.created_at >=', $filters['date_from'] . ' 00:00:00');
        }

        if (!empty($filters['date_to'])) {
            $builder->where('audit_logs.created_at <=', $filters['date_to'] . ' 23:59:59');
        }

        $builder->orderBy('audit_logs.created_at', 'DESC');

        // Get total count for pagination
        $totalLogs = $builder->countAllResults(false);
        $totalPages = ceil($totalLogs / $perPage);

        // Get paginated results
        $logs = $builder->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();

        // Get unique actions for filter dropdown
        $actionsResult = $this->auditModel->builder()
            ->select('DISTINCT(action) as action')
            ->where('company_id', $companyId)
            ->get()
            ->getResultArray();
        $actions = array_column($actionsResult, 'action');

        // Get users for filter dropdown
        $users = $this->userModel->builder()
            ->select('users.id, users.email, users.first_name, users.last_name')
            ->join('user_company', 'user_company.user_id = users.id')
            ->where('user_company.company_id', $companyId)
            ->get()
            ->getResultArray();

        return view('admin/audit/index', [
            'logs' => $logs,
            'filters' => $filters,
            'actions' => $actions,
            'users' => $users,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalLogs' => $totalLogs,
        ]);
    }

    /**
     * Export audit logs as CSV
     */
    public function exportCsv()
    {
        $session = session();
        $companyId = $session->get('company_id');

        // Get filter parameters
        $filters = [
            'action' => $this->request->getGet('action'),
            'user_id' => $this->request->getGet('user_id'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
        ];

        // Build query
        $builder = $this->auditModel->builder();
        $builder->select('audit_logs.*, users.email as user_email');
        $builder->join('users', 'users.id = audit_logs.user_id', 'left');
        $builder->where('audit_logs.company_id', $companyId);

        if (!empty($filters['action'])) {
            $builder->like('audit_logs.action', $filters['action']);
        }

        if (!empty($filters['user_id'])) {
            $builder->where('audit_logs.user_id', $filters['user_id']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('audit_logs.created_at >=', $filters['date_from'] . ' 00:00:00');
        }

        if (!empty($filters['date_to'])) {
            $builder->where('audit_logs.created_at <=', $filters['date_to'] . ' 23:59:59');
        }

        $builder->orderBy('audit_logs.created_at', 'DESC');
        $logs = $builder->get()->getResultArray();

        // Generate CSV
        $filename = 'audit_logs_' . date('Y-m-d_H-i-s') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // BOM for Excel UTF-8
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Headers
        fputcsv($output, [
            'Date',
            'Utilisateur',
            'Action',
            'Type Entité',
            'ID Entité',
            'Adresse IP',
            'Agent Utilisateur'
        ], ';');

        // Data
        foreach ($logs as $log) {
            fputcsv($output, [
                $log['created_at'],
                $log['user_email'] ?? 'Système',
                $this->translateAction($log['action']),
                $log['entity_type'] ?? '-',
                $log['entity_id'] ?? '-',
                $log['ip_address'] ?? '-',
                substr($log['user_agent'] ?? '-', 0, 100),
            ], ';');
        }

        fclose($output);
        exit;
    }

    /**
     * View single log entry details
     */
    public function show($id)
    {
        $log = $this->auditModel->builder()
            ->select('audit_logs.*, users.email as user_email, users.first_name, users.last_name')
            ->join('users', 'users.id = audit_logs.user_id', 'left')
            ->where('audit_logs.id', $id)
            ->get()
            ->getRowArray();

        if (!$log) {
            return redirect()->to('/admin/audit')->with('error', 'Log non trouvé.');
        }

        // Verify company access
        $session = session();
        if ($log['company_id'] !== $session->get('company_id')) {
            return redirect()->to('/admin/audit')->with('error', 'Accès non autorisé.');
        }

        return view('admin/audit/show', [
            'log' => $log,
        ]);
    }

    /**
     * Translate action code to French
     */
    private function translateAction(string $action): string
    {
        $translations = [
            'login' => 'Connexion',
            'logout' => 'Déconnexion',
            'login_failed' => 'Échec connexion',
            'user.create' => 'Création utilisateur',
            'user.update' => 'Modification utilisateur',
            'user.delete' => 'Suppression utilisateur',
            'user.invite' => 'Invitation utilisateur',
            'user.suspend' => 'Suspension utilisateur',
            'user.activate' => 'Réactivation utilisateur',
            'user.remove' => 'Retrait utilisateur',
            'role.change' => 'Changement de rôle',
            'password.reset' => 'Réinitialisation mot de passe',
            'password.change' => 'Changement mot de passe',
            'company.switch' => 'Changement entreprise',
            'invitation.accept' => 'Invitation acceptée',
        ];

        return $translations[$action] ?? $action;
    }
}
