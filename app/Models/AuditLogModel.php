<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * AuditLogModel - Manages audit logging for security and RGPD compliance
 * Tracks all sensitive user actions
 */
class AuditLogModel extends Model
{
    protected $table = 'audit_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'user_id',
        'company_id',
        'action',
        'entity_type',
        'entity_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'created_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';

    /**
     * Log an action
     */
    public function log(string $action, array $context = []): bool
    {
        $session = session();
        $request = \Config\Services::request();

        $data = [
            'user_id' => $context['user_id'] ?? $session->get('user_id'),
            'company_id' => $context['company_id'] ?? $session->get('company_id'),
            'action' => $action,
            'entity_type' => $context['entity_type'] ?? null,
            'entity_id' => $context['entity_id'] ?? null,
            'old_values' => isset($context['old_values']) ? json_encode($context['old_values']) : null,
            'new_values' => isset($context['new_values']) ? json_encode($context['new_values']) : null,
            'ip_address' => $request->getIPAddress(),
            'user_agent' => $request->getUserAgent()->getAgentString(),
        ];

        return $this->insert($data) !== false;
    }

    /**
     * Log a login event
     */
    public function logLogin(string $userId, bool $success = true, ?string $reason = null): bool
    {
        return $this->log($success ? 'auth.login' : 'auth.login_failed', [
            'user_id' => $userId,
            'new_values' => $reason ? ['reason' => $reason] : null,
        ]);
    }

    /**
     * Log a logout event
     */
    public function logLogout(string $userId): bool
    {
        return $this->log('auth.logout', ['user_id' => $userId]);
    }

    /**
     * Log a user creation
     */
    public function logUserCreated(string $userId, array $userData): bool
    {
        // Remove sensitive data
        unset($userData['password_hash'], $userData['password']);

        return $this->log('user.created', [
            'entity_type' => 'user',
            'entity_id' => $userId,
            'new_values' => $userData,
        ]);
    }

    /**
     * Log a user update
     */
    public function logUserUpdated(string $userId, array $oldData, array $newData): bool
    {
        // Remove sensitive data
        unset($oldData['password_hash'], $oldData['password']);
        unset($newData['password_hash'], $newData['password']);

        return $this->log('user.updated', [
            'entity_type' => 'user',
            'entity_id' => $userId,
            'old_values' => $oldData,
            'new_values' => $newData,
        ]);
    }

    /**
     * Log a permission change
     */
    public function logPermissionChange(string $targetUserId, string $companyId, int $oldRoleId, int $newRoleId): bool
    {
        return $this->log('user.role_changed', [
            'entity_type' => 'user_company',
            'entity_id' => $targetUserId,
            'company_id' => $companyId,
            'old_values' => ['role_id' => $oldRoleId],
            'new_values' => ['role_id' => $newRoleId],
        ]);
    }

    /**
     * Log a user invitation
     */
    public function logUserInvited(string $email, string $companyId, int $roleId, string $invitedBy): bool
    {
        return $this->log('user.invited', [
            'company_id' => $companyId,
            'user_id' => $invitedBy,
            'new_values' => ['email' => $email, 'role_id' => $roleId],
        ]);
    }

    /**
     * Log a user suspension
     */
    public function logUserSuspended(string $targetUserId, string $companyId, ?string $reason = null): bool
    {
        return $this->log('user.suspended', [
            'entity_type' => 'user',
            'entity_id' => $targetUserId,
            'company_id' => $companyId,
            'new_values' => $reason ? ['reason' => $reason] : null,
        ]);
    }

    /**
     * Log a password reset request
     */
    public function logPasswordResetRequested(string $email): bool
    {
        return $this->log('auth.password_reset_requested', [
            'new_values' => ['email' => $email],
        ]);
    }

    /**
     * Log a password reset completion
     */
    public function logPasswordResetCompleted(string $userId): bool
    {
        return $this->log('auth.password_reset_completed', [
            'user_id' => $userId,
        ]);
    }

    /**
     * Get activity logs for a user
     */
    public function getUserActivity(string $userId, int $limit = 50): array
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get activity logs for a company
     */
    public function getCompanyActivity(string $companyId, int $limit = 50): array
    {
        return $this->select('audit_logs.*, users.email, users.first_name, users.last_name')
            ->join('users', 'users.id = audit_logs.user_id', 'left')
            ->where('audit_logs.company_id', $companyId)
            ->orderBy('audit_logs.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get login history for a user
     */
    public function getLoginHistory(string $userId, int $limit = 20): array
    {
        return $this->where('user_id', $userId)
            ->whereIn('action', ['auth.login', 'auth.login_failed', 'auth.logout'])
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get security-related events for a user
     */
    public function getSecurityEvents(string $userId, int $limit = 50): array
    {
        return $this->where('user_id', $userId)
            ->whereIn('action', [
                'auth.login',
                'auth.login_failed',
                'auth.logout',
                'auth.password_reset_requested',
                'auth.password_reset_completed',
                'user.suspended',
                'user.role_changed'
            ])
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Export audit logs for RGPD compliance
     */
    public function exportForUser(string $userId): array
    {
        return $this->where('user_id', $userId)
            ->orWhere('entity_id', $userId)
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }

    /**
     * Get action description in French
     */
    public static function getActionLabel(string $action): string
    {
        $labels = [
            'auth.login' => 'Connexion réussie',
            'auth.login_failed' => 'Tentative de connexion échouée',
            'auth.logout' => 'Déconnexion',
            'auth.password_reset_requested' => 'Demande de réinitialisation du mot de passe',
            'auth.password_reset_completed' => 'Mot de passe réinitialisé',
            'user.created' => 'Utilisateur créé',
            'user.updated' => 'Profil modifié',
            'user.invited' => 'Invitation envoyée',
            'user.suspended' => 'Utilisateur suspendu',
            'user.activated' => 'Utilisateur réactivé',
            'user.removed' => 'Utilisateur retiré',
            'user.role_changed' => 'Rôle modifié',
            'company.updated' => 'Entreprise modifiée',
            'company.settings_updated' => 'Paramètres modifiés',
        ];

        return $labels[$action] ?? $action;
    }
}
