<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DashboardModel;
use App\Models\NotificationModel;

class Dashboard extends BaseController
{
    protected DashboardModel $dashboardModel;
    protected NotificationModel $notificationModel;

    public function __construct()
    {
        $this->dashboardModel = new DashboardModel();
        $this->notificationModel = new NotificationModel();
    }

    /**
     * Main dashboard view
     */
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $companyId = session()->get('current_company_id');
        $userId = session()->get('user_id');

        // If no company in session, try to get user's primary company
        if (empty($companyId)) {
            $db = \Config\Database::connect();
            $userCompany = $db->table('user_company')
                ->where('user_id', $userId)
                ->orderBy('is_primary', 'DESC')
                ->get()
                ->getRowArray();
            
            if ($userCompany) {
                $companyId = $userCompany['company_id'];
                session()->set('current_company_id', $companyId);
            } else {
                // No company associated - show empty dashboard
                return view('dashboard/index', [
                    'kpi' => ['revenue' => 0, 'revenue_change' => 0, 'pending_invoices' => 0, 'treasury_balance' => 0, 'active_customers' => 0],
                    'revenueEvolution' => ['labels' => [], 'data' => []],
                    'revenueByCategory' => ['labels' => [], 'data' => []],
                    'invoiceStatus' => ['labels' => [], 'data' => []],
                    'recentInvoices' => [],
                    'notifications' => []
                ]);
            }
        }

        // Get all dashboard data
        $data = [
            'kpi' => $this->dashboardModel->getAllKpiData($companyId),
            'revenueEvolution' => $this->dashboardModel->getRevenueEvolution($companyId, 6),
            'revenueByCategory' => $this->dashboardModel->getRevenueByCategory($companyId),
            'invoiceStatus' => $this->dashboardModel->getInvoiceStatusData($companyId),
            'recentInvoices' => $this->dashboardModel->getRecentInvoices($companyId, 5),
            'notifications' => $this->dashboardModel->getImportantNotifications($userId, 5)
        ];

        return view('dashboard/index', $data);
    }

    /**
     * AJAX endpoint for KPI data refresh
     */
    public function getKpiData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'Non autorisé'])->setStatusCode(401);
        }

        $companyId = session()->get('current_company_id');
        $kpi = $this->dashboardModel->getAllKpiData($companyId);

        return $this->response->setJSON($kpi);
    }

    /**
     * AJAX endpoint for chart data
     */
    public function getChartData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'Non autorisé'])->setStatusCode(401);
        }

        $companyId = session()->get('current_company_id');
        $period = $this->request->getGet('period') ?? 6;

        $data = [
            'revenueEvolution' => $this->dashboardModel->getRevenueEvolution($companyId, (int) $period),
            'revenueByCategory' => $this->dashboardModel->getRevenueByCategory($companyId),
            'invoiceStatus' => $this->dashboardModel->getInvoiceStatusData($companyId)
        ];

        return $this->response->setJSON($data);
    }

    /**
     * AJAX endpoint for recent invoices
     */
    public function getRecentInvoices()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'Non autorisé'])->setStatusCode(401);
        }

        $companyId = session()->get('current_company_id');
        $invoices = $this->dashboardModel->getRecentInvoices($companyId, 5);

        return $this->response->setJSON(['invoices' => $invoices]);
    }

    /**
     * AJAX endpoint for notifications
     */
    public function getNotifications()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'Non autorisé'])->setStatusCode(401);
        }

        $userId = session()->get('user_id');
        $notifications = $this->dashboardModel->getImportantNotifications($userId, 5);

        return $this->response->setJSON(['notifications' => $notifications]);
    }
}
