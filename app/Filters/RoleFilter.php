<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PermissionModel;
use App\Models\UserCompanyModel;

/**
 * RoleFilter - Enhanced permission checking with company context
 */
class RoleFilter implements FilterInterface
{
    /**
     * Check if user has required permission in current company context
     *
     * @param RequestInterface $request
     * @param array|null $arguments - Permission name(s) required
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $userId = $session->get('user_id');
        $companyId = $session->get('company_id');

        // Check if user is logged in
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter.');
        }

        // Check company context
        $userCompanyModel = new UserCompanyModel();
        $companies = $userCompanyModel->getUserCompanies($userId);

        // If no company in session and user has multiple companies, redirect to selection
        if (!$companyId && count($companies) > 1) {
            return redirect()->to('/select-company')
                ->with('info', 'Veuillez sélectionner une entreprise.');
        }

        // If no company in session but user has exactly one, auto-select it
        if (!$companyId && count($companies) === 1) {
            $session->set('company_id', $companies[0]['company_id']);
            $session->set('company_name', $companies[0]['company_name']);
            $session->set('user_role', $companies[0]['role_name']);
            $companyId = $companies[0]['company_id'];
        }

        // If still no company (user not associated with any), check legacy company_id
        if (!$companyId) {
            $companyId = $session->get('user')['company_id'] ?? null;
            if ($companyId) {
                $session->set('company_id', $companyId);
            }
        }

        // No specific permissions required, just authentication
        if (empty($arguments)) {
            return;
        }

        // Check permissions
        $permissionModel = new PermissionModel();

        // Check if user has ANY of the required permissions
        foreach ($arguments as $permission) {
            if ($companyId) {
                if ($permissionModel->userCanInCompany($userId, $companyId, $permission)) {
                    return; // User has permission, allow access
                }
            } else {
                // Fallback to legacy check
                if ($permissionModel->userHasPermission($userId, $permission)) {
                    return;
                }
            }
        }

        // User doesn't have any required permission
        return redirect()->to('/dashboard')
            ->with('error', 'Vous n\'avez pas les droits nécessaires pour accéder à cette page.');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing to do after
    }
}

