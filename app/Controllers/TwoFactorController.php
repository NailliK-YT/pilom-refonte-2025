<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\AuditLogModel;
use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\BaconQrCodeProvider;

/**
 * TwoFactorController - Manages 2FA setup and verification
 */
class TwoFactorController extends BaseController
{
    private TwoFactorAuth $tfa;
    private UserModel $userModel;
    private AuditLogModel $auditModel;
    private const BACKUP_CODES_COUNT = 10;
    private const ISSUER = 'Pilom';

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->auditModel = new AuditLogModel();

        // Initialize TwoFactorAuth with QR code provider
        // v3.x API: first arg is QRCodeProvider, issuer is named parameter
        // Use SVG format to avoid requiring imagick PHP extension
        $qrProvider = new BaconQrCodeProvider(format: 'svg');
        $this->tfa = new TwoFactorAuth(
            qrcodeprovider: $qrProvider,
            issuer: self::ISSUER,
            digits: 6,
            period: 30
        );

        helper(['form', 'url']);
    }

    /**
     * Display 2FA settings page
     */
    public function index()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        return view('settings/2fa_settings', [
            'enabled' => $user['two_factor_enabled'] ?? false,
            'enabled_at' => $user['two_factor_enabled_at'] ?? null
        ]);
    }

    /**
     * Initiate 2FA setup - generate secret and show QR code
     */
    public function setup()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        // Check if 2FA is FULLY configured (both enabled AND secret exists)
        $isFullyConfigured = !empty($user['two_factor_enabled']) && !empty($user['two_factor_secret']);

        if ($isFullyConfigured) {
            return redirect()->to('/account/security')
                ->with('info', 'L\'authentification à deux facteurs est déjà activée.');
        }

        // Generate a new secret
        $secret = $this->tfa->createSecret();

        // Store temporarily in session (not yet activated)
        session()->set('2fa_temp_secret', $secret);

        // Generate the QR Code data URI
        $qrCodeDataUri = $this->tfa->getQRCodeImageAsDataUri(
            $user['email'],
            $secret
        );

        return view('settings/2fa_setup', [
            'qrCodeUrl' => $qrCodeDataUri,
            'secret' => $secret,
            'email' => $user['email']
        ]);
    }

    /**
     * Verify the TOTP code and activate 2FA
     */
    public function verify()
    {
        $code = $this->request->getPost('code');
        $secret = session()->get('2fa_temp_secret');

        if (!$secret) {
            return redirect()->to('/settings/2fa/setup')
                ->with('error', 'Session expirée. Veuillez recommencer le processus.');
        }

        // Validate code format
        if (!preg_match('/^\d{6}$/', $code)) {
            return redirect()->back()
                ->with('error', 'Le code doit contenir 6 chiffres.');
        }

        // Verify the TOTP code
        if (!$this->tfa->verifyCode($secret, $code)) {
            return redirect()->back()
                ->with('error', 'Code invalide. Veuillez réessayer.');
        }

        // Generate backup codes
        $backupCodes = $this->generateBackupCodes();

        // Activate 2FA
        $userId = session()->get('user_id');
        $this->userModel->update($userId, [
            'two_factor_secret' => $this->encryptSecret($secret),
            'two_factor_enabled' => true,
            'two_factor_backup_codes' => $this->encryptBackupCodes($backupCodes),
            'two_factor_enabled_at' => date('Y-m-d H:i:s')
        ]);

        // Log the action
        $this->auditModel->log('two_factor_enabled', ['user_id' => $userId, 'details' => 'Two-factor authentication enabled']);

        // Clean session
        session()->remove('2fa_temp_secret');

        return view('settings/2fa_backup_codes', [
            'backupCodes' => $backupCodes
        ]);
    }

    /**
     * Disable 2FA (with confirmation dialog on frontend)
     */
    public function disable()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/account/security')
                ->with('error', 'Utilisateur non trouvé.');
        }

        // Disable 2FA
        $this->userModel->update($userId, [
            'two_factor_secret' => null,
            'two_factor_enabled' => false,
            'two_factor_backup_codes' => null,
            'two_factor_enabled_at' => null
        ]);

        // Log the action
        $this->auditModel->log('two_factor_disabled', ['user_id' => $userId, 'details' => 'Two-factor authentication disabled']);

        return redirect()->to('/account/security')
            ->with('success', 'L\'authentification à deux facteurs a été désactivée.');
    }

    /**
     * Regenerate backup codes
     */
    public function regenerateBackupCodes()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user['two_factor_enabled']) {
            return redirect()->to('/account/security')
                ->with('error', 'L\'authentification à deux facteurs n\'est pas activée.');
        }

        // Generate new backup codes
        $backupCodes = $this->generateBackupCodes();

        // Update in database
        $this->userModel->update($userId, [
            'two_factor_backup_codes' => $this->encryptBackupCodes($backupCodes),
            'two_factor_recovery_at' => date('Y-m-d H:i:s')
        ]);

        // Log the action
        $this->auditModel->log('backup_codes_regenerated', ['user_id' => $userId, 'details' => 'Backup codes regenerated']);

        return view('settings/2fa_backup_codes', [
            'backupCodes' => $backupCodes,
            'regenerated' => true
        ]);
    }

    // ==================== LOGIN VERIFICATION ====================

    /**
     * Show 2FA verification form during login
     */
    public function loginVerify()
    {
        if (!session()->get('2fa_pending')) {
            return redirect()->to('/login');
        }

        return view('auth/2fa_verify');
    }

    /**
     * Process 2FA code during login
     */
    public function loginVerifyPost()
    {
        $pendingUserId = session()->get('2fa_pending_user_id');
        if (!$pendingUserId) {
            return redirect()->to('/login');
        }

        $code = $this->request->getPost('code');
        $user = $this->userModel->find($pendingUserId);

        if (!$user) {
            session()->remove(['2fa_pending', '2fa_pending_user_id']);
            return redirect()->to('/login')
                ->with('error', 'Session invalide. Veuillez vous reconnecter.');
        }

        // Decrypt secret
        $secret = $this->decryptSecret($user['two_factor_secret']);

        // Check TOTP code first
        if ($this->tfa->verifyCode($secret, $code)) {
            $this->completeLogin($user);
            return redirect()->to('/dashboard')
                ->with('success', 'Connexion réussie !');
        }

        // Check backup code
        if ($this->verifyBackupCode($pendingUserId, $code)) {
            $this->completeLogin($user);
            return redirect()->to('/dashboard')
                ->with('warning', 'Code de secours utilisé. Pensez à en régénérer de nouveaux.');
        }

        return redirect()->back()
            ->with('error', 'Code invalide. Veuillez réessayer.');
    }

    // ==================== ADMIN 2FA ENFORCEMENT ====================

    /**
     * Check if user needs to set up 2FA (for admins)
     */
    public function requireSetup()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        // Only require for admin role
        $role = session()->get('user_role') ?? $user['role'] ?? 'user';
        if ($role !== 'admin' && $role !== 'owner') {
            return redirect()->to('/dashboard');
        }

        if ($user['two_factor_enabled']) {
            return redirect()->to('/dashboard');
        }

        return view('settings/2fa_required');
    }

    // ==================== HELPER METHODS ====================

    /**
     * Generate random backup codes
     */
    private function generateBackupCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < self::BACKUP_CODES_COUNT; $i++) {
            // Format: XXXX-XXXX (8 chars with hyphen)
            $codes[] = strtoupper(bin2hex(random_bytes(2))) . '-' . strtoupper(bin2hex(random_bytes(2)));
        }
        return $codes;
    }

    /**
     * Encrypt the TOTP secret for storage
     */
    private function encryptSecret(string $secret): string
    {
        // Encrypt and then base64 encode for safe PostgreSQL storage
        $encrypted = \Config\Services::encrypter()->encrypt($secret);
        return base64_encode($encrypted);
    }

    /**
     * Decrypt the stored TOTP secret
     */
    private function decryptSecret(string $encrypted): string
    {
        // Decode base64 first, then decrypt
        $decoded = base64_decode($encrypted);
        return \Config\Services::encrypter()->decrypt($decoded);
    }

    /**
     * Encrypt backup codes for storage
     */
    private function encryptBackupCodes(array $codes): string
    {
        // Hash each code (we won't decrypt, just compare hashes)
        $hashedCodes = array_map(function ($code) {
            return password_hash(strtoupper(str_replace('-', '', $code)), PASSWORD_DEFAULT);
        }, $codes);

        return json_encode($hashedCodes);
    }

    /**
     * Verify and consume a backup code
     */
    private function verifyBackupCode(string $userId, string $code): bool
    {
        $user = $this->userModel->find($userId);
        if (empty($user['two_factor_backup_codes'])) {
            return false;
        }

        $storedCodes = json_decode($user['two_factor_backup_codes'], true);
        if (!is_array($storedCodes)) {
            return false;
        }

        // Normalize the submitted code
        $normalizedCode = strtoupper(str_replace('-', '', $code));

        // Check each hashed code
        foreach ($storedCodes as $index => $hashedCode) {
            if (password_verify($normalizedCode, $hashedCode)) {
                // Remove used code
                unset($storedCodes[$index]);
                $this->userModel->update($userId, [
                    'two_factor_backup_codes' => json_encode(array_values($storedCodes))
                ]);

                // Log backup code usage
                $this->auditModel->log('backup_code_used', ['user_id' => $userId, 'details' => 'Backup code was used for authentication']);

                return true;
            }
        }

        return false;
    }

    /**
     * Complete the login process after 2FA verification
     */
    private function completeLogin(array $user): void
    {
        // Restore the full session data that was stored during pending state
        $sessionData = session()->get('2fa_pending_session');

        if ($sessionData) {
            session()->set($sessionData);
        } else {
            // Fallback: create basic session
            session()->set([
                'user_id' => $user['id'],
                'user' => $user,
                'email' => $user['email'],
                'first_name' => $user['first_name'] ?? null,
                'last_name' => $user['last_name'] ?? null,
                'isLoggedIn' => true,
            ]);
        }

        // Clean up 2FA pending state
        session()->remove(['2fa_pending', '2fa_pending_user_id', '2fa_pending_session']);

        // Log successful 2FA login
        $this->auditModel->log('login_2fa', ['user_id' => $user['id'], 'details' => 'User logged in with 2FA']);
    }
}
