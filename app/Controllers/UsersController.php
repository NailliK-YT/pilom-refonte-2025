<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\UserCompanyModel;
use App\Models\RoleModel;
use App\Models\CompanyModel;
use App\Models\AuditLogModel;
use App\Models\PermissionModel;

/**
 * UsersController - Manages users within a company (admin functions)
 */
class UsersController extends BaseController
{
    protected $userModel;
    protected $userCompanyModel;
    protected $roleModel;
    protected $companyModel;
    protected $auditModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->userCompanyModel = new UserCompanyModel();
        $this->roleModel = new RoleModel();
        $this->companyModel = new CompanyModel();
        $this->auditModel = new AuditLogModel();
        helper(['form', 'url']);
    }

    /**
     * List all users in the current company
     */
    public function index()
    {
        $companyId = session()->get('company_id');

        if (!$companyId) {
            return redirect()->to('/dashboard')->with('error', 'Aucune entreprise sélectionnée.');
        }

        $users = $this->userCompanyModel->getCompanyUsers($companyId);
        $roles = $this->roleModel->findAll();
        $company = $this->companyModel->find($companyId);

        // Check subscription limits
        $maxUsers = $company['max_users'] ?? 1;
        $currentUserCount = $this->userCompanyModel->countCompanyUsers($companyId);
        $canInvite = $currentUserCount < $maxUsers;

        return view('admin/users/index', [
            'users' => $users,
            'roles' => $roles,
            'company' => $company,
            'canInvite' => $canInvite,
            'maxUsers' => $maxUsers,
            'currentUserCount' => $currentUserCount,
        ]);
    }

    /**
     * Show invitation form
     */
    public function create()
    {
        $companyId = session()->get('company_id');

        if (!$companyId) {
            return redirect()->to('/dashboard')->with('error', 'Aucune entreprise sélectionnée.');
        }

        // Check subscription limits
        $company = $this->companyModel->find($companyId);
        $maxUsers = $company['max_users'] ?? 1;
        $currentUserCount = $this->userCompanyModel->countCompanyUsers($companyId);

        if ($currentUserCount >= $maxUsers) {
            return redirect()->to('/admin/users')
                ->with('error', 'Limite d\'utilisateurs atteinte. Passez à un forfait supérieur pour inviter plus d\'utilisateurs.');
        }

        $roles = $this->roleModel->findAll();

        return view('admin/users/invite', [
            'roles' => $roles,
            'company' => $company,
        ]);
    }

    /**
     * Process invitation
     */
    public function store()
    {
        $companyId = session()->get('company_id');
        $userId = session()->get('user_id');

        if (!$companyId) {
            return redirect()->to('/dashboard')->with('error', 'Aucune entreprise sélectionnée.');
        }

        // Validation
        $rules = [
            'email' => 'required|valid_email',
            'role_id' => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $roleId = (int) $this->request->getPost('role_id');
        $message = $this->request->getPost('message');

        // Create invitation
        $result = $this->userCompanyModel->createInvitation($email, $companyId, $roleId, $userId);

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('error', $result['error']);
        }

        // Log the invitation
        $this->auditModel->logUserInvited($email, $companyId, $roleId, $userId);

        // Send invitation email (simplified - just log for now)
        $this->sendInvitationEmail($email, $result['token'], $companyId, $message);

        return redirect()->to('/admin/users')
            ->with('success', 'Invitation envoyée à ' . $email);
    }

    /**
     * Show edit role form
     */
    public function edit($userCompanyId)
    {
        $companyId = session()->get('company_id');

        $userCompany = $this->userCompanyModel
            ->select('user_company.*, users.email, users.first_name, users.last_name, users.avatar')
            ->join('users', 'users.id = user_company.user_id')
            ->where('user_company.company_id', $companyId)
            ->find($userCompanyId);

        if (!$userCompany) {
            return redirect()->to('/admin/users')->with('error', 'Utilisateur non trouvé.');
        }

        $roles = $this->roleModel->findAll();

        return view('admin/users/edit', [
            'userCompany' => $userCompany,
            'roles' => $roles,
        ]);
    }

    /**
     * Update user role
     */
    public function update($userId)
    {
        $companyId = session()->get('company_id');
        $currentUserId = session()->get('user_id');

        // Prevent self-demotion for admins
        if ($userId === $currentUserId) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas modifier votre propre rôle.');
        }

        $rules = [
            'role_id' => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $newRoleId = (int) $this->request->getPost('role_id');

        // Get current role for logging
        $currentUserCompany = $this->userCompanyModel->getUserRoleInCompany($userId, $companyId);
        $oldRoleId = $currentUserCompany['role_id'] ?? 0;

        // Update role
        $success = $this->userCompanyModel->updateUserRole($userId, $companyId, $newRoleId);

        if ($success) {
            // Log the change
            $this->auditModel->logPermissionChange($userId, $companyId, $oldRoleId, $newRoleId);

            // Clear permission cache
            $permissionModel = new PermissionModel();
            $permissionModel->clearPermissionCache($userId, $companyId);

            return redirect()->to('/admin/users')->with('success', 'Rôle mis à jour avec succès.');
        }

        return redirect()->back()->with('error', 'Erreur lors de la mise à jour du rôle.');
    }

    /**
     * Suspend a user
     */
    public function suspend($userId)
    {
        $companyId = session()->get('company_id');
        $currentUserId = session()->get('user_id');

        if ($userId === $currentUserId) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas vous suspendre vous-même.');
        }

        $success = $this->userCompanyModel->suspendUser($userId, $companyId);

        if ($success) {
            $this->auditModel->logUserSuspended($userId, $companyId);

            // Clear permission cache
            $permissionModel = new PermissionModel();
            $permissionModel->clearPermissionCache($userId, $companyId);

            return redirect()->to('/admin/users')->with('success', 'Utilisateur suspendu.');
        }

        return redirect()->back()->with('error', 'Erreur lors de la suspension.');
    }

    /**
     * Activate a suspended user
     */
    public function activate($userId)
    {
        $companyId = session()->get('company_id');

        $success = $this->userCompanyModel->activateUser($userId, $companyId);

        if ($success) {
            $this->auditModel->log('user.activated', [
                'entity_type' => 'user',
                'entity_id' => $userId,
                'company_id' => $companyId,
            ]);

            return redirect()->to('/admin/users')->with('success', 'Utilisateur réactivé.');
        }

        return redirect()->back()->with('error', 'Erreur lors de la réactivation.');
    }

    /**
     * Remove user from company
     */
    public function remove($userId)
    {
        $companyId = session()->get('company_id');
        $currentUserId = session()->get('user_id');

        if ($userId === $currentUserId) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas vous retirer vous-même.');
        }

        $success = $this->userCompanyModel->removeUserFromCompany($userId, $companyId);

        if ($success) {
            $this->auditModel->log('user.removed', [
                'entity_type' => 'user',
                'entity_id' => $userId,
                'company_id' => $companyId,
            ]);

            return redirect()->to('/admin/users')->with('success', 'Utilisateur retiré de l\'entreprise.');
        }

        return redirect()->back()->with('error', 'Erreur lors du retrait.');
    }

    /**
     * Accept invitation (for new users or existing users joining new company)
     */
    public function acceptInvitation($token)
    {
        $result = $this->userCompanyModel->acceptInvitation($token);

        if (!$result['success']) {
            return redirect()->to('/login')->with('error', $result['error']);
        }

        // If user is already logged in, redirect to dashboard
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard')
                ->with('success', 'Vous avez rejoint l\'entreprise avec succès !');
        }

        // Otherwise show a page to complete registration or login
        return view('admin/users/invitation_accepted', [
            'invitation' => $result['invitation'],
        ]);
    }

    /**
     * Send invitation email (simplified version - logs to file for now)
     */
    private function sendInvitationEmail(string $email, string $token, string $companyId, ?string $message = null): void
    {
        $company = $this->companyModel->find($companyId);
        $companyName = $company['name'] ?? 'Notre entreprise';

        $inviteUrl = base_url("invitation/accept/{$token}");

        // For now, just log the invitation (in production, send actual email)
        log_message('info', "=== INVITATION EMAIL ===");
        log_message('info', "To: {$email}");
        log_message('info', "Subject: Invitation à rejoindre {$companyName} sur Pilom");
        log_message('info', "Link: {$inviteUrl}");
        if ($message) {
            log_message('info', "Message: {$message}");
        }
        log_message('info', "========================");

        // TODO: Implement actual email sending with CodeIgniter Email library
        // $email = \Config\Services::email();
        // $email->setTo($email);
        // $email->setSubject("Invitation à rejoindre {$companyName} sur Pilom");
        // $email->setMessage($this->renderInvitationEmail($inviteUrl, $companyName, $message));
        // $email->send();
    }
}
