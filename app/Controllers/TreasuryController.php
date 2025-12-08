<?php

namespace App\Controllers;

use App\Models\TreasuryModel;
use App\Models\TreasuryAlertModel;
use App\Models\UserModel;

class TreasuryController extends BaseController
{
    protected $treasuryModel;
    protected $alertModel;

    public function __construct()
    {
        $this->treasuryModel = new TreasuryModel();
        $this->alertModel = new TreasuryAlertModel();
        helper(['form', 'number']);
    }

    /**
     * Get company ID from session
     */
    private function getCompanyId(): ?string
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return null;
        }
        
        $userModel = new UserModel();
        $user = $userModel->find($userId);
        
        return $user['company_id'] ?? null;
    }

    /**
     * Treasury dashboard
     */
    public function index()
    {
        $companyId = $this->getCompanyId();
        if (!$companyId) {
            return redirect()->to('/login')->with('error', 'Vous devez être connecté.');
        }

        // Get summary statistics
        $summary = $this->treasuryModel->getSummary($companyId);
        
        // Get monthly data for chart (last 12 months)
        $monthlyData = $this->treasuryModel->getMonthlyData($companyId, 12);
        
        // Get recent transactions
        $recentEntries = $this->treasuryModel->getEntriesForCompany($companyId);
        $recentEntries = array_slice($recentEntries, 0, 10);
        
        // Get active alerts
        $alerts = $this->alertModel->getByCompanyId($companyId);
        
        // Check for triggered alerts
        $triggeredAlerts = $this->alertModel->checkAlerts($companyId, $summary['current_balance']);

        $data = [
            'title' => 'Trésorerie',
            'summary' => $summary,
            'monthlyData' => $monthlyData,
            'recentEntries' => $recentEntries,
            'alerts' => $alerts,
            'triggeredAlerts' => $triggeredAlerts,
            'chartLabels' => json_encode(array_keys($monthlyData)),
            'chartEntries' => json_encode(array_column($monthlyData, 'entries')),
            'chartExits' => json_encode(array_column($monthlyData, 'exits'))
        ];

        return view('treasury/index', $data);
    }

    /**
     * Add manual entry
     */
    public function create()
    {
        $companyId = $this->getCompanyId();
        if (!$companyId) {
            return redirect()->to('/login')->with('error', 'Vous devez être connecté.');
        }

        $data = [
            'title' => 'Nouvelle entrée de trésorerie',
            'validation' => \Config\Services::validation()
        ];

        return view('treasury/create', $data);
    }

    /**
     * Store new entry
     */
    public function store()
    {
        $companyId = $this->getCompanyId();
        if (!$companyId) {
            return redirect()->to('/login')->with('error', 'Vous devez être connecté.');
        }

        $rules = [
            'type' => 'required|in_list[entry,exit]',
            'amount' => 'required|decimal',
            'transaction_date' => 'required|valid_date',
            'description' => 'permit_empty|max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $entryData = [
            'company_id' => $companyId,
            'type' => $this->request->getPost('type'),
            'category' => $this->request->getPost('category') ?: 'other',
            'amount' => abs((float) $this->request->getPost('amount')),
            'description' => $this->request->getPost('description'),
            'transaction_date' => $this->request->getPost('transaction_date'),
            'created_by' => session()->get('user_id')
        ];

        if ($this->treasuryModel->insert($entryData)) {
            return redirect()->to('/treasury')->with('success', 'Entrée ajoutée avec succès.');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de l\'ajout.');
    }

    /**
     * Manage alerts
     */
    public function alerts()
    {
        $companyId = $this->getCompanyId();
        if (!$companyId) {
            return redirect()->to('/login')->with('error', 'Vous devez être connecté.');
        }

        $data = [
            'title' => 'Alertes de trésorerie',
            'alerts' => $this->alertModel->getByCompanyId($companyId),
            'currentBalance' => $this->treasuryModel->getCurrentBalance($companyId),
            'validation' => \Config\Services::validation()
        ];

        return view('treasury/alerts', $data);
    }

    /**
     * Store new alert
     */
    public function storeAlert()
    {
        $companyId = $this->getCompanyId();
        if (!$companyId) {
            return redirect()->to('/login')->with('error', 'Vous devez être connecté.');
        }

        $rules = [
            'name' => 'required|min_length[2]|max_length[100]',
            'threshold_type' => 'required|in_list[below,above]',
            'threshold_amount' => 'required|decimal'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $alertData = [
            'company_id' => $companyId,
            'name' => $this->request->getPost('name'),
            'threshold_type' => $this->request->getPost('threshold_type'),
            'threshold_amount' => $this->request->getPost('threshold_amount'),
            'is_active' => true
        ];

        if ($this->alertModel->insert($alertData)) {
            return redirect()->to('/treasury/alerts')->with('success', 'Alerte créée avec succès.');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la création.');
    }

    /**
     * Delete alert
     */
    public function deleteAlert($id)
    {
        $companyId = $this->getCompanyId();
        if (!$companyId) {
            return redirect()->to('/login')->with('error', 'Vous devez être connecté.');
        }

        $alert = $this->alertModel->find($id);
        if (!$alert || $alert['company_id'] !== $companyId) {
            return redirect()->to('/treasury/alerts')->with('error', 'Alerte introuvable.');
        }

        $this->alertModel->delete($id);
        return redirect()->to('/treasury/alerts')->with('success', 'Alerte supprimée.');
    }

    /**
     * API endpoint for chart data
     */
    public function chartData()
    {
        $companyId = $this->getCompanyId();
        if (!$companyId) {
            return $this->response->setJSON(['error' => 'Non autorisé']);
        }

        $months = $this->request->getGet('months') ?? 12;
        $monthlyData = $this->treasuryModel->getMonthlyData($companyId, (int) $months);

        return $this->response->setJSON([
            'labels' => array_keys($monthlyData),
            'entries' => array_column($monthlyData, 'entries'),
            'exits' => array_column($monthlyData, 'exits')
        ]);
    }
}
