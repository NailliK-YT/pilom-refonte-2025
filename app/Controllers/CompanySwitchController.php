<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserCompanyModel;
use App\Models\CompanyModel;

/**
 * CompanySwitchController - Handles company selection for multi-company users
 */
class CompanySwitchController extends BaseController
{
    protected $userCompanyModel;
    protected $companyModel;

    public function __construct()
    {
        $this->userCompanyModel = new UserCompanyModel();
        $this->companyModel = new CompanyModel();
        helper(['form', 'url']);
    }

    /**
     * Show company selection page
     */
    public function select()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return redirect()->to('/login');
        }

        $companies = $this->userCompanyModel->getUserCompanies($userId);

        // If only one company, auto-select and redirect
        if (count($companies) === 1) {
            return $this->switch($companies[0]['company_id']);
        }

        // If no companies, show error
        if (empty($companies)) {
            return view('company/no_companies', [
                'message' => 'Vous n\'êtes associé à aucune entreprise.'
            ]);
        }

        return view('company/select', [
            'companies' => $companies,
        ]);
    }

    /**
     * Switch to a different company
     */
    public function switch($companyId)
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return redirect()->to('/login');
        }

        // Verify user has access to this company
        $userCompany = $this->userCompanyModel->getUserRoleInCompany($userId, $companyId);

        if (!$userCompany) {
            return redirect()->to('/select-company')
                ->with('error', 'Vous n\'avez pas accès à cette entreprise.');
        }

        // Get company details
        $company = $this->companyModel->find($companyId);

        if (!$company) {
            return redirect()->to('/select-company')
                ->with('error', 'Entreprise non trouvée.');
        }

        // Update session with new company context
        session()->set([
            'company_id' => $companyId,
            'company_name' => $company['name'],
            'company_logo' => $company['logo'] ?? null,
            'user_role' => $userCompany['role_name'],
            'user_role_id' => $userCompany['role_id'],
        ]);

        // Clear permission cache to force reload with new company context
        $permissionModel = new \App\Models\PermissionModel();
        $permissionModel->clearPermissionCache($userId);

        // Redirect to dashboard with success message
        $redirectTo = session()->get('redirect_after_select') ?? '/dashboard';
        session()->remove('redirect_after_select');

        return redirect()->to($redirectTo)
            ->with('success', 'Vous travaillez maintenant sur ' . $company['name']);
    }

    /**
     * Get current company info (AJAX)
     */
    public function current()
    {
        $companyId = session()->get('company_id');

        if (!$companyId) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Aucune entreprise sélectionnée'
            ]);
        }

        $company = $this->companyModel->find($companyId);
        $userRole = session()->get('user_role');

        return $this->response->setJSON([
            'success' => true,
            'company' => [
                'id' => $company['id'],
                'name' => $company['name'],
                'logo' => $company['logo'],
            ],
            'role' => $userRole,
        ]);
    }
}
