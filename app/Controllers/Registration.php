<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\CompanyModel;
use App\Models\BusinessSectorModel;
use App\Models\RegistrationSessionModel;

class Registration extends BaseController
{
    protected $userModel;
    protected $companyModel;
    protected $businessSectorModel;
    protected $sessionModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->companyModel = new CompanyModel();
        $this->businessSectorModel = new BusinessSectorModel();
        $this->sessionModel = new RegistrationSessionModel();
        helper(['form', 'url']);
    }

    /**
     * Step 1: Account Information
     */
    public function step1()
    {
        // Get existing session if available
        $sessionToken = session()->get('registration_token');
        $sessionData = null;

        if ($sessionToken) {
            $sessionData = $this->sessionModel->getSession($sessionToken);
        }

        if ($this->request->is('post')) {
            // Validation rules for step 1
            $rules = [
                'email' => 'required|valid_email|is_unique[users.email]',
                'company_name' => 'required|min_length[2]|max_length[255]',
                'password' => 'required|min_length[8]',
                'password_confirm' => 'required|matches[password]'
            ];

            if (!$this->validate($rules)) {
                return view('registration/step1', [
                    'validation' => $this->validator,
                    'sessionData' => $sessionData
                ]);
            }

            // Prepare data for session
            $data = [
                'email' => $this->request->getPost('email'),
                'company_name' => $this->request->getPost('company_name'),
                'password' => $this->request->getPost('password'),
            ];

            // Save or update session
            if ($sessionToken && $sessionData) {
                $this->sessionModel->updateSession($sessionToken, 2, $data);
            } else {
                $sessionToken = $this->sessionModel->createSession([
                    'step' => 2,
                    'data' => $data
                ]);
                session()->set('registration_token', $sessionToken);
            }

            return redirect()->to('/register/step2');
        }

        return view('registration/step1', ['sessionData' => $sessionData]);
    }

    /**
     * Step 2: Business Sector Selection
     */
    public function step2()
    {
        // Check if step 1 is completed
        $sessionToken = session()->get('registration_token');
        if (!$sessionToken) {
            return redirect()->to('/register/step1');
        }

        $sessionData = $this->sessionModel->getSession($sessionToken);
        if (!$sessionData || $sessionData['step'] < 2) {
            return redirect()->to('/register/step1');
        }

        // Get all business sectors
        $sectors = $this->businessSectorModel->getAllSectors();

        if ($this->request->is('post')) {
            $rules = [
                'business_sector_id' => 'required|is_not_unique[business_sectors.id]'
            ];

            if (!$this->validate($rules)) {
                return view('registration/step2', [
                    'validation' => $this->validator,
                    'sessionData' => $sessionData,
                    'sectors' => $sectors
                ]);
            }

            // Update session data
            $data = $sessionData['data'];
            $data['business_sector_id'] = $this->request->getPost('business_sector_id');

            $this->sessionModel->updateSession($sessionToken, 3, $data);

            return redirect()->to('/register/step3');
        }

        return view('registration/step2', [
            'sessionData' => $sessionData,
            'sectors' => $sectors
        ]);
    }

    /**
     * Step 3: Review and Confirm
     */
    public function step3()
    {
        // Check if previous steps are completed
        $sessionToken = session()->get('registration_token');
        if (!$sessionToken) {
            return redirect()->to('/register/step1');
        }

        $sessionData = $this->sessionModel->getSession($sessionToken);
        if (!$sessionData || $sessionData['step'] < 3) {
            return redirect()->to('/register/step2');
        }

        // Get sector name for display
        $sectorId = $sessionData['data']['business_sector_id'] ?? null;
        $sector = null;
        if ($sectorId) {
            $sector = $this->businessSectorModel->find($sectorId);
        }

        return view('registration/step3', [
            'sessionData' => $sessionData,
            'sector' => $sector
        ]);
    }

    /**
     * Complete Registration
     */
    public function complete()
    {
        if (!$this->request->is('post')) {
            return redirect()->to('/register/step1');
        }

        // Verify acceptance of terms
        $rules = [
            'accept_terms' => 'required'
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata('error', 'Vous devez accepter les conditions d\'utilisation.');
            return redirect()->back()->withInput();
        }

        // Get session data
        $sessionToken = session()->get('registration_token');
        if (!$sessionToken) {
            return redirect()->to('/register/step1');
        }

        $sessionData = $this->sessionModel->getSession($sessionToken);
        if (!$sessionData || $sessionData['step'] < 3) {
            return redirect()->to('/register/step1');
        }

        $data = $sessionData['data'];

        // Start database transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Create company first
            $companyData = [
                'name' => $data['company_name'],
                'business_sector_id' => $data['business_sector_id']
            ];
            $companyId = $this->companyModel->insert($companyData);

            if (!$companyId) {
                throw new \Exception('Failed to create company');
            }

            // Get the actual company ID (for UUID, insert returns the ID)
            $company = $this->companyModel->find($companyId);
            if (!$company) {
                // If find doesn't work, get insertID
                $companyId = $this->companyModel->getInsertID();
            } else {
                $companyId = $company['id'];
            }

            // Create user
            $userData = [
                'email' => $data['email'],
                'password_hash' => $data['password'],
                'company_id' => $companyId,
                'role' => 'user',
                'is_verified' => false
            ];

            $userId = $this->userModel->insert($userData);

            if (!$userId) {
                throw new \Exception('Failed to create user');
            }

            // Get the actual user ID
            $user = $this->userModel->find($userId);
            if (!$user) {
                $userId = $this->userModel->getInsertID();
            } else {
                $userId = $user['id'];
            }

            // Generate verification token
            $verificationToken = $this->userModel->generateVerificationToken($userId);

            // Send verification email
            $this->sendVerificationEmail($data['email'], $verificationToken);

            // Complete transaction
            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            // Delete registration session
            $this->sessionModel->where('session_token', $sessionToken)->delete();
            session()->remove('registration_token');

            // Redirect to success page
            session()->setFlashdata('success_email', $data['email']);
            return redirect()->to('/register/success');

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Registration error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer.');
            return redirect()->back();
        }
    }

    /**
     * Success page
     */
    public function success()
    {
        $email = session()->getFlashdata('success_email');
        if (!$email) {
            return redirect()->to('/register/step1');
        }

        return view('registration/success', ['email' => $email]);
    }

    /**
     * Verify email with token
     */
    public function verifyEmail($token)
    {
        if ($this->userModel->verifyEmail($token)) {
            session()->setFlashdata('success', 'Votre email a été vérifié avec succès. Vous pouvez maintenant vous connecter.');
            return redirect()->to('/login');
        } else {
            session()->setFlashdata('error', 'Le lien de vérification est invalide ou a expiré.');
            return redirect()->to('/login');
        }
    }

    /**
     * AJAX: Validate email availability
     */
    public function validateEmailAjax()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $email = $this->request->getPost('email');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON([
                'valid' => false,
                'message' => 'Format d\'email invalide'
            ]);
        }

        $exists = $this->userModel->where('email', $email)->first();

        if ($exists) {
            return $this->response->setJSON([
                'valid' => false,
                'message' => 'Cet email est déjà utilisé'
            ]);
        }

        return $this->response->setJSON([
            'valid' => true,
            'message' => 'Email disponible'
        ]);
    }

    /**
     * AJAX: Validate password strength
     */
    public function validatePasswordAjax()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $password = $this->request->getPost('password');
        $strength = 0;
        $messages = [];

        if (strlen($password) >= 8) {
            $strength += 25;
        } else {
            $messages[] = 'Au moins 8 caractères';
        }

        if (preg_match('/[a-z]/', $password)) {
            $strength += 25;
        } else {
            $messages[] = 'Une lettre minuscule';
        }

        if (preg_match('/[A-Z]/', $password)) {
            $strength += 25;
        } else {
            $messages[] = 'Une lettre majuscule';
        }

        if (preg_match('/[0-9]/', $password)) {
            $strength += 25;
        } else {
            $messages[] = 'Un chiffre';
        }

        return $this->response->setJSON([
            'strength' => $strength,
            'valid' => $strength >= 50,
            'messages' => $messages
        ]);
    }

    /**
     * AJAX: Save progress
     */
    public function saveProgress()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $sessionToken = session()->get('registration_token');
        $step = $this->request->getPost('step');
        $data = $this->request->getPost('data');

        if (!$sessionToken) {
            // Create new session
            $sessionToken = $this->sessionModel->createSession([
                'step' => $step,
                'data' => $data
            ]);
            session()->set('registration_token', $sessionToken);
        } else {
            // Update existing session
            $this->sessionModel->updateSession($sessionToken, $step, $data);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Progression sauvegardée'
        ]);
    }

    /**
     * Send verification email
     */
    private function sendVerificationEmail(string $email, string $token)
    {
        $emailService = \Config\Services::email();

        $verificationLink = base_url("verify-email/{$token}");

        $message = view('emails/verification', [
            'verification_link' => $verificationLink
        ]);

        $emailService->setTo($email);
        $emailService->setSubject('Vérifiez votre email - Pilom');
        $emailService->setMessage($message);

        // Try to send, but don't fail if it doesn't work
        try {
            $emailService->send();
        } catch (\Exception $e) {
            log_message('error', 'Email sending failed: ' . $e->getMessage());
            // Continue anyway - user can still use the app
        }
    }
}
