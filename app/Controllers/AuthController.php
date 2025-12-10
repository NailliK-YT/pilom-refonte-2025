<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\AuditLogModel;
use App\Models\UserCompanyModel;
use App\Models\LoginAttemptModel;

/**
 * AuthController - Enhanced with password reset, audit logging, and user status checks
 */
class AuthController extends BaseController
{
    protected $userModel;
    protected $auditModel;
    protected $loginAttemptModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->auditModel = new AuditLogModel();
        $this->loginAttemptModel = new LoginAttemptModel();
        helper(['form', 'url', 'cookie']);
    }

    /**
     * Affiche la page de connexion
     */
    public function login()
    {
        // Si l'utilisateur est déjà connecté, rediriger vers le dashboard
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login_view');
    }

    /**
     * Traite la tentative de connexion
     */
    public function attemptLogin()
    {
        // Règles de validation
        $rules = [
            'email' => [
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => 'L\'adresse email est obligatoire.',
                    'valid_email' => 'Veuillez fournir une adresse email valide.',
                ],
            ],
            'password' => [
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'Le mot de passe est obligatoire.',
                    'min_length' => 'Le mot de passe doit contenir au moins 6 caractères.',
                ],
            ],
        ];

        // Validation des données
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Récupération des données
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $remember = $this->request->getPost('remember');

        // Recherche de l'utilisateur
        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            $this->auditModel->logLogin('', false, 'User not found: ' . $email);
            // Record failed attempt for brute-force protection
            $this->loginAttemptModel->recordAttempt(
                $this->request->getIPAddress(),
                $email,
                $this->request->getUserAgent()->getAgentString()
            );
            return redirect()->back()->withInput()->with('error', 'Email ou mot de passe incorrect.');
        }

        // Check user status
        if (isset($user['status']) && $user['status'] === 'suspended') {
            $this->auditModel->logLogin($user['id'], false, 'Account suspended');
            return redirect()->back()->withInput()
                ->with('error', 'Votre compte a été suspendu. Contactez votre administrateur.');
        }

        if (isset($user['status']) && $user['status'] === 'deleted') {
            $this->auditModel->logLogin($user['id'], false, 'Account deleted');
            return redirect()->back()->withInput()->with('error', 'Email ou mot de passe incorrect.');
        }

        // Vérification du mot de passe
        if (!$this->userModel->verifyPassword($password, $user['password_hash'])) {
            $this->auditModel->logLogin($user['id'], false, 'Invalid password');
            // Record failed attempt for brute-force protection
            $blocked = $this->loginAttemptModel->recordAttempt(
                $this->request->getIPAddress(),
                $email,
                $this->request->getUserAgent()->getAgentString()
            );

            $errorMessage = 'Email ou mot de passe incorrect.';
            if ($blocked) {
                $blockMinutes = LoginAttemptModel::getBlockDurationMinutes();
                $errorMessage = "Trop de tentatives. Compte bloqué pour {$blockMinutes} minutes.";
            }
            return redirect()->back()->withInput()->with('error', $errorMessage);
        }

        // Reset login attempts on successful authentication
        $this->loginAttemptModel->resetAttempts($this->request->getIPAddress(), $email);

        // Update last login
        $this->userModel->updateLastLogin($user['id']);

        // Log successful login
        $this->auditModel->logLogin($user['id'], true);

        // Get user's companies
        $userCompanyModel = new UserCompanyModel();
        $companies = $userCompanyModel->getUserCompanies($user['id']);

        // Création de la session
        $sessionData = [
            'user_id' => $user['id'],
            'user' => $user,  // Ajout de l'utilisateur complet pour compatibilité
            'email' => $user['email'],
            'first_name' => $user['first_name'] ?? null,
            'last_name' => $user['last_name'] ?? null,
            'isLoggedIn' => true,
        ];

        // Set company context if user has exactly one company
        if (count($companies) === 1) {
            $sessionData['company_id'] = $companies[0]['company_id'];
            $sessionData['company_name'] = $companies[0]['company_name'];
            $sessionData['user_role'] = $companies[0]['role_name'];
        } elseif (!empty($user['company_id'])) {
            // Fallback to legacy company_id
            $sessionData['company_id'] = $user['company_id'];
            $sessionData['role'] = $user['role'] ?? 'user';
        }

        // ===== TWO-FACTOR AUTHENTICATION CHECK =====
        // Check if 2FA is FULLY configured (enabled + secret exists)
        // Note: PostgreSQL may return 't' or '1' for boolean true
        $has2FAConfigured = !empty($user['two_factor_enabled'])
            && !empty($user['two_factor_secret']);

        if ($has2FAConfigured) {
            // Store the pending session data temporarily
            session()->set([
                '2fa_pending' => true,
                '2fa_pending_user_id' => $user['id'],
                '2fa_pending_session' => $sessionData,
            ]);

            return redirect()->to('/auth/2fa-verify');
        }

        // If admin/owner without 2FA, remind them to set it up
        $userRole = $sessionData['user_role'] ?? $user['role'] ?? 'user';
        if (in_array($userRole, ['admin', 'owner']) && empty($user['two_factor_enabled'])) {
            session()->set($sessionData);
            session()->setFlashdata('warning', 'Pour une sécurité optimale, veuillez activer l\'authentification à deux facteurs.');
            return redirect()->to('/require-2fa');
        }
        // ===== END 2FA CHECK =====

        session()->set($sessionData);

        // Gestion du "Se souvenir de moi"
        if ($remember) {
            $this->setRememberMeCookie($user['id']);
        }

        // If user has multiple companies, redirect to company selection
        if (count($companies) > 1) {
            return redirect()->to('/select-company')
                ->with('success', 'Connexion réussie ! Veuillez sélectionner une entreprise.');
        }

        // Redirection vers le dashboard
        return redirect()->to('/dashboard')->with('success', 'Connexion réussie !');
    }

    /**
     * Déconnexion de l'utilisateur
     */
    public function logout()
    {
        $userId = session()->get('user_id');

        // Log logout
        if ($userId) {
            $this->auditModel->logLogout($userId);
        }

        // Suppression de la session
        session()->destroy();

        // Suppression du cookie "Se souvenir de moi"
        delete_cookie('remember_token');

        return redirect()->to('/login')->with('success', 'Vous avez été déconnecté avec succès.');
    }

    /**
     * Définit le cookie "Se souvenir de moi"
     */
    private function setRememberMeCookie(string $userId)
    {
        // Génération d'un token unique
        $token = bin2hex(random_bytes(32));

        // Stockage du token dans la base de données
        $this->userModel->update($userId, ['remember_token' => hash('sha256', $token)]);

        // Création du cookie (valable 30 jours)
        $cookie = [
            'name' => 'remember_token',
            'value' => $token,
            'expire' => 2592000, // 30 jours en secondes
            'secure' => true,    // HTTPS uniquement
            'httponly' => true,  // Protection XSS
            'samesite' => 'Strict', // Protection CSRF
        ];

        set_cookie($cookie);
    }

    /**
     * Page "Mot de passe oublié"
     */
    public function forgotPassword()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/forgot_password_view');
    }

    /**
     * Envoie le lien de réinitialisation
     */
    public function sendResetLink()
    {
        $rules = [
            'email' => 'required|valid_email',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');

        // Log the request (for security)
        $this->auditModel->logPasswordResetRequested($email);

        // Generate token (will return null if user doesn't exist)
        $token = $this->userModel->generatePasswordResetToken($email);

        if ($token) {
            // Send the reset email (simplified - just log for now)
            $this->sendPasswordResetEmail($email, $token);
        }

        // Always show same message to prevent email enumeration
        return redirect()->to('/forgot-password')
            ->with('success', 'Si cette adresse existe dans notre système, vous recevrez un email avec les instructions.');
    }

    /**
     * Affiche le formulaire de réinitialisation
     */
    public function resetPassword($token)
    {
        // Verify token is valid
        $user = $this->userModel->where('password_reset_token', $token)
            ->where('password_reset_expires >', date('Y-m-d H:i:s'))
            ->first();

        if (!$user) {
            return redirect()->to('/forgot-password')
                ->with('error', 'Le lien de réinitialisation est invalide ou expiré.');
        }

        return view('auth/reset_password_view', [
            'token' => $token,
        ]);
    }

    /**
     * Traite la réinitialisation du mot de passe
     */
    public function updatePassword()
    {
        $rules = [
            'token' => 'required',
            'password' => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];

        $messages = [
            'password' => [
                'min_length' => 'Le mot de passe doit contenir au moins 8 caractères.',
            ],
            'password_confirm' => [
                'matches' => 'Les mots de passe ne correspondent pas.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');

        $result = $this->userModel->resetPassword($token, $password);

        if (!$result['success']) {
            return redirect()->to('/forgot-password')
                ->with('error', $result['error']);
        }

        // Log the password reset
        $this->auditModel->logPasswordResetCompleted($result['user_id']);

        return redirect()->to('/login')
            ->with('success', 'Votre mot de passe a été réinitialisé. Vous pouvez maintenant vous connecter.');
    }

    /**
     * Envoie l'email de réinitialisation (version simplifiée)
     */
    private function sendPasswordResetEmail(string $email, string $token): void
    {
        $resetUrl = base_url("reset-password/{$token}");

        // For now, just log the email (in production, send actual email)
        log_message('info', "=== PASSWORD RESET EMAIL ===");
        log_message('info', "To: {$email}");
        log_message('info', "Subject: Réinitialisation de votre mot de passe Pilom");
        log_message('info', "Reset URL: {$resetUrl}");
        log_message('info', "Valid for: 1 hour");
        log_message('info', "============================");

        // TODO: Implement actual email sending
        // $emailService = \Config\Services::email();
        // $emailService->setTo($email);
        // $emailService->setSubject('Réinitialisation de votre mot de passe Pilom');
        // $emailService->setMessage(view('emails/password_reset', ['resetUrl' => $resetUrl]));
        // $emailService->send();
    }
}

