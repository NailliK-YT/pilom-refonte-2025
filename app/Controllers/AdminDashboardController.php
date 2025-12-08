<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\UserCompanyModel;
use App\Models\AuditLogModel;
use App\Models\RoleModel;

/**
 * Admin Dashboard Controller - Statistics and overview for administrators
 */
class AdminDashboardController extends BaseController
{
    protected UserModel $userModel;
    protected UserCompanyModel $userCompanyModel;
    protected AuditLogModel $auditModel;
    protected RoleModel $roleModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->userCompanyModel = new UserCompanyModel();
        $this->auditModel = new AuditLogModel();
        $this->roleModel = new RoleModel();
    }

    /**
     * Main admin dashboard with statistics
     */
    public function index()
    {
        $session = session();
        $companyId = $session->get('company_id');

        // Get company users statistics
        $users = $this->userCompanyModel->getCompanyUsers($companyId);
        $totalUsers = count($users);
        $activeUsers = count(array_filter($users, fn($u) => $u['status'] === 'active'));
        $pendingInvitations = count(array_filter($users, fn($u) => $u['status'] === 'pending'));
        $suspendedUsers = count(array_filter($users, fn($u) => $u['status'] === 'suspended'));

        // Get users by role
        $usersByRole = [];
        foreach ($users as $user) {
            $roleName = $user['role_name'] ?? 'unknown';
            $usersByRole[$roleName] = ($usersByRole[$roleName] ?? 0) + 1;
        }

        // Get recent activity (last 10 events)
        $recentActivity = $this->auditModel->builder()
            ->select('audit_logs.*, users.email as user_email, users.first_name, users.last_name')
            ->join('users', 'users.id = audit_logs.user_id', 'left')
            ->where('audit_logs.company_id', $companyId)
            ->orderBy('audit_logs.created_at', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        // Get login statistics for last 7 days
        $loginStats = $this->getLoginStatistics($companyId, 7);

        // Get company info
        $company = $this->getCompanyInfo($companyId);

        // Get recent logins (last 5)
        $recentLogins = $this->auditModel->builder()
            ->select('audit_logs.*, users.email as user_email, users.first_name, users.last_name')
            ->join('users', 'users.id = audit_logs.user_id', 'left')
            ->where('audit_logs.company_id', $companyId)
            ->where('audit_logs.action', 'login')
            ->orderBy('audit_logs.created_at', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        // Get failed logins (security alert)
        $failedLogins = $this->auditModel->builder()
            ->where('company_id', $companyId)
            ->where('action', 'login_failed')
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
            ->countAllResults();

        return view('admin/dashboard/index', [
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'pendingInvitations' => $pendingInvitations,
            'suspendedUsers' => $suspendedUsers,
            'usersByRole' => $usersByRole,
            'recentActivity' => $recentActivity,
            'loginStats' => $loginStats,
            'company' => $company,
            'recentLogins' => $recentLogins,
            'failedLogins' => $failedLogins,
        ]);
    }

    /**
     * Get login statistics for a period
     */
    private function getLoginStatistics(string $companyId, int $days): array
    {
        $stats = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $count = $this->auditModel->builder()
                ->where('company_id', $companyId)
                ->where('action', 'login')
                ->where('DATE(created_at)', $date)
                ->countAllResults();

            $stats[] = [
                'date' => $date,
                'label' => date('D', strtotime($date)),
                'count' => $count,
            ];
        }

        return $stats;
    }

    /**
     * Get company information
     */
    private function getCompanyInfo(string $companyId): ?array
    {
        return db_connect()->table('companies')
            ->where('id', $companyId)
            ->get()
            ->getRowArray();
    }

    /**
     * Security alerts page
     */
    public function security()
    {
        $session = session();
        $companyId = $session->get('company_id');

        // Failed logins in last 7 days
        $failedLogins = $this->auditModel->builder()
            ->select('audit_logs.*, users.email as user_email')
            ->join('users', 'users.id = audit_logs.user_id', 'left')
            ->where('audit_logs.company_id', $companyId)
            ->where('audit_logs.action', 'login_failed')
            ->where('audit_logs.created_at >=', date('Y-m-d H:i:s', strtotime('-7 days')))
            ->orderBy('audit_logs.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Suspicious IPs (multiple failed logins from same IP)
        $suspiciousIPs = $this->auditModel->builder()
            ->select('ip_address, COUNT(*) as attempts')
            ->where('company_id', $companyId)
            ->where('action', 'login_failed')
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
            ->groupBy('ip_address')
            ->having('COUNT(*) >', 3)
            ->get()
            ->getResultArray();

        // Password resets in last 7 days
        $passwordResets = $this->auditModel->builder()
            ->select('audit_logs.*, users.email as user_email')
            ->join('users', 'users.id = audit_logs.user_id', 'left')
            ->where('audit_logs.company_id', $companyId)
            ->whereIn('audit_logs.action', ['password.reset', 'password.change'])
            ->where('audit_logs.created_at >=', date('Y-m-d H:i:s', strtotime('-7 days')))
            ->orderBy('audit_logs.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Role changes in last 7 days
        $roleChanges = $this->auditModel->builder()
            ->select('audit_logs.*, users.email as user_email')
            ->join('users', 'users.id = audit_logs.user_id', 'left')
            ->where('audit_logs.company_id', $companyId)
            ->where('audit_logs.action', 'role.change')
            ->where('audit_logs.created_at >=', date('Y-m-d H:i:s', strtotime('-7 days')))
            ->orderBy('audit_logs.created_at', 'DESC')
            ->get()
            ->getResultArray();

        return view('admin/dashboard/security', [
            'failedLogins' => $failedLogins,
            'suspiciousIPs' => $suspiciousIPs,
            'passwordResets' => $passwordResets,
            'roleChanges' => $roleChanges,
        ]);
    }
}
