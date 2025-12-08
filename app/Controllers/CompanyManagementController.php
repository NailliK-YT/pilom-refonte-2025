<?php

namespace App\Controllers;

use App\Models\UserCompanyModel;
use App\Models\AuditLogModel;
use CodeIgniter\I18n\Time;

/**
 * Controller for advanced multi-company management
 */
class CompanyManagementController extends BaseController
{
    protected UserCompanyModel $userCompanyModel;
    protected AuditLogModel $auditModel;

    public function __construct()
    {
        $this->userCompanyModel = new UserCompanyModel();
        $this->auditModel = new AuditLogModel();
    }

    /**
     * Show list of user's companies
     */
    public function index()
    {
        $session = session();
        $userId = $session->get('user_id');
        $currentCompanyId = $session->get('company_id');

        $companies = $this->userCompanyModel->getUserCompanies($userId);

        return view('companies/index', [
            'companies' => $companies,
            'currentCompanyId' => $currentCompanyId,
        ]);
    }

    /**
     * Show form to create a new company
     */
    public function create()
    {
        // Get business sectors for dropdown
        $businessSectors = db_connect()->table('business_sectors')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        return view('companies/create', [
            'businessSectors' => $businessSectors,
        ]);
    }

    /**
     * Store a new company
     */
    public function store()
    {
        $session = session();
        $userId = $session->get('user_id');
        $db = db_connect();

        $name = $this->request->getPost('name');
        $businessSectorId = $this->request->getPost('business_sector_id');
        $siret = $this->request->getPost('siret');
        $address = $this->request->getPost('address');
        $city = $this->request->getPost('city');
        $postalCode = $this->request->getPost('postal_code');

        if (empty($name)) {
            return redirect()->back()->with('error', 'Le nom de l\'entreprise est requis.');
        }

        $db->transStart();

        // Generate UUID for company
        $companyId = $this->generateUUID();

        // Create the company
        $db->table('companies')->insert([
            'id' => $companyId,
            'name' => $name,
            'business_sector_id' => $businessSectorId ?: null,
            'siret' => $siret,
            'address' => $address,
            'city' => $city,
            'postal_code' => $postalCode,
            'subscription_plan' => 'free',
            'max_users' => 1,
            'created_at' => Time::now()->toDateTimeString(),
            'updated_at' => Time::now()->toDateTimeString(),
        ]);

        // Get admin role ID
        $adminRole = $db->table('roles')->where('name', 'admin')->get()->getRow();
        $adminRoleId = $adminRole ? $adminRole->id : 1;

        // Add current user as admin of new company
        $db->table('user_company')->insert([
            'id' => $this->generateUUID(),
            'user_id' => $userId,
            'company_id' => $companyId,
            'role_id' => $adminRoleId,
            'is_primary' => false,
            'status' => 'active',
            'created_at' => Time::now()->toDateTimeString(),
            'updated_at' => Time::now()->toDateTimeString(),
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Erreur lors de la création de l\'entreprise.');
        }

        // Log the action
        $this->auditModel->log('company.create', [
            'entity_type' => 'company',
            'entity_id' => $companyId,
            'new_values' => json_encode(['name' => $name]),
        ]);

        return redirect()->to('/companies')
            ->with('success', "Entreprise '{$name}' créée avec succès. Vous en êtes l'administrateur.");
    }

    /**
     * Set a company as primary
     */
    public function setPrimary($companyId)
    {
        $session = session();
        $userId = $session->get('user_id');
        $db = db_connect();

        // Verify user belongs to this company
        $membership = $db->table('user_company')
            ->where('user_id', $userId)
            ->where('company_id', $companyId)
            ->get()
            ->getRow();

        if (!$membership) {
            return redirect()->to('/companies')->with('error', 'Vous n\'appartenez pas à cette entreprise.');
        }

        $db->transStart();

        // Remove primary from all other companies
        $db->table('user_company')
            ->where('user_id', $userId)
            ->update(['is_primary' => false]);

        // Set this company as primary
        $db->table('user_company')
            ->where('user_id', $userId)
            ->where('company_id', $companyId)
            ->update(['is_primary' => true]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour.');
        }

        // Log the action
        $this->auditModel->log('company.set_primary', [
            'entity_type' => 'company',
            'entity_id' => $companyId,
        ]);

        return redirect()->to('/companies')
            ->with('success', 'Entreprise principale modifiée.');
    }

    /**
     * Show transfer ownership form
     */
    public function transferForm($companyId)
    {
        $session = session();
        $userId = $session->get('user_id');
        $db = db_connect();

        // Get company
        $company = $db->table('companies')->where('id', $companyId)->get()->getRowArray();
        if (!$company) {
            return redirect()->to('/companies')->with('error', 'Entreprise non trouvée.');
        }

        // Verify user is admin of this company
        $adminRole = $db->table('roles')->where('name', 'admin')->get()->getRow();
        $membership = $db->table('user_company')
            ->where('user_id', $userId)
            ->where('company_id', $companyId)
            ->where('role_id', $adminRole->id)
            ->get()
            ->getRow();

        if (!$membership) {
            return redirect()->to('/companies')->with('error', 'Vous n\'êtes pas administrateur de cette entreprise.');
        }

        // Get other users in this company
        $users = $this->userCompanyModel->getCompanyUsers($companyId);
        // Filter out current user
        $users = array_filter($users, fn($u) => $u['user_id'] !== $userId);

        return view('companies/transfer', [
            'company' => $company,
            'users' => $users,
        ]);
    }

    /**
     * Transfer ownership to another user
     */
    public function transfer($companyId)
    {
        $session = session();
        $userId = $session->get('user_id');
        $newOwnerId = $this->request->getPost('new_owner_id');
        $db = db_connect();

        if (empty($newOwnerId)) {
            return redirect()->back()->with('error', 'Veuillez sélectionner le nouveau propriétaire.');
        }

        // Get admin role
        $adminRole = $db->table('roles')->where('name', 'admin')->get()->getRow();
        $userRole = $db->table('roles')->where('name', 'user')->get()->getRow();

        // Verify current user is admin
        $currentMembership = $db->table('user_company')
            ->where('user_id', $userId)
            ->where('company_id', $companyId)
            ->where('role_id', $adminRole->id)
            ->get()
            ->getRow();

        if (!$currentMembership) {
            return redirect()->to('/companies')->with('error', 'Vous n\'êtes pas administrateur de cette entreprise.');
        }

        // Verify new owner is member of company
        $newOwnerMembership = $db->table('user_company')
            ->where('user_id', $newOwnerId)
            ->where('company_id', $companyId)
            ->get()
            ->getRow();

        if (!$newOwnerMembership) {
            return redirect()->back()->with('error', 'Cet utilisateur n\'appartient pas à l\'entreprise.');
        }

        $db->transStart();

        // Demote current owner to user
        $db->table('user_company')
            ->where('user_id', $userId)
            ->where('company_id', $companyId)
            ->update(['role_id' => $userRole->id]);

        // Promote new owner to admin
        $db->table('user_company')
            ->where('user_id', $newOwnerId)
            ->where('company_id', $companyId)
            ->update(['role_id' => $adminRole->id]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Erreur lors du transfert.');
        }

        // Log the action
        $this->auditModel->log('company.transfer_ownership', [
            'entity_type' => 'company',
            'entity_id' => $companyId,
            'old_values' => json_encode(['owner' => $userId]),
            'new_values' => json_encode(['owner' => $newOwnerId]),
        ]);

        return redirect()->to('/companies')
            ->with('success', 'Propriété transférée avec succès.');
    }

    /**
     * Leave a company
     */
    public function leave($companyId)
    {
        $session = session();
        $userId = $session->get('user_id');
        $currentCompanyId = $session->get('company_id');
        $db = db_connect();

        // Get membership
        $membership = $db->table('user_company')
            ->where('user_id', $userId)
            ->where('company_id', $companyId)
            ->get()
            ->getRow();

        if (!$membership) {
            return redirect()->to('/companies')->with('error', 'Vous n\'appartenez pas à cette entreprise.');
        }

        // Check if user is the only admin
        $adminRole = $db->table('roles')->where('name', 'admin')->get()->getRow();
        $adminCount = $db->table('user_company')
            ->where('company_id', $companyId)
            ->where('role_id', $adminRole->id)
            ->countAllResults();

        if ($membership->role_id == $adminRole->id && $adminCount <= 1) {
            return redirect()->to('/companies')
                ->with('error', 'Vous êtes le seul administrateur. Transférez la propriété avant de quitter.');
        }

        // Remove user from company
        $db->table('user_company')
            ->where('user_id', $userId)
            ->where('company_id', $companyId)
            ->delete();

        // Log the action
        $this->auditModel->log('company.leave', [
            'entity_type' => 'company',
            'entity_id' => $companyId,
        ]);

        // If leaving current company, switch to another
        if ($companyId === $currentCompanyId) {
            $otherCompany = $db->table('user_company')
                ->where('user_id', $userId)
                ->get()
                ->getRow();

            if ($otherCompany) {
                $session->set('company_id', $otherCompany->company_id);
            } else {
                $session->remove('company_id');
            }
        }

        return redirect()->to('/companies')
            ->with('success', 'Vous avez quitté l\'entreprise.');
    }

    /**
     * Generate UUID v4
     */
    private function generateUUID(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
