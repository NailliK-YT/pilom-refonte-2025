<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>Gestion des Utilisateurs<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Utilisateurs de l'entreprise<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .users-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .users-stats {
        display: flex;
        gap: 1.5rem;
        flex-wrap: wrap;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: var(--bg-tertiary, #f8f9fa);
        border-radius: 0.5rem;
        font-size: 0.875rem;
    }

    .stat-item .number {
        font-weight: 600;
        color: var(--primary-color, #3b82f6);
    }

    .btn-invite {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border: none;
        border-radius: 0.5rem;
        font-weight: 500;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        text-decoration: none;
    }

    .btn-invite:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.35);
    }

    .btn-invite:disabled {
        background: #94a3b8;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .users-table-container {
        background: var(--bg-primary, white);
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .users-table {
        width: 100%;
        border-collapse: collapse;
    }

    .users-table th,
    .users-table td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid var(--border-color, #e2e8f0);
    }

    .users-table th {
        background: var(--bg-tertiary, #f8f9fa);
        font-weight: 600;
        color: var(--text-secondary, #64748b);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .users-table tr:hover {
        background: var(--bg-tertiary, #f8f9fa);
    }

    .user-cell {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .user-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .user-info {
        display: flex;
        flex-direction: column;
    }

    .user-name {
        font-weight: 500;
        color: var(--text-primary, #1e293b);
    }

    .user-email {
        font-size: 0.875rem;
        color: var(--text-secondary, #64748b);
    }

    .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .role-admin {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
    }

    .role-user {
        background: rgba(59, 130, 246, 0.1);
        color: #2563eb;
    }

    .role-comptable {
        background: rgba(16, 185, 129, 0.1);
        color: #059669;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .status-active {
        background: rgba(16, 185, 129, 0.1);
        color: #059669;
    }

    .status-pending {
        background: rgba(245, 158, 11, 0.1);
        color: #d97706;
    }

    .status-suspended {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
    }

    .actions-cell {
        display: flex;
        gap: 0.5rem;
    }

    .action-btn {
        padding: 0.5rem;
        border: none;
        background: transparent;
        cursor: pointer;
        border-radius: 0.375rem;
        color: var(--text-secondary, #64748b);
        transition: all 0.2s;
    }

    .action-btn:hover {
        background: var(--bg-tertiary, #f1f5f9);
        color: var(--text-primary, #1e293b);
    }

    .action-btn.danger:hover {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
    }

    .action-btn svg {
        width: 16px;
        height: 16px;
    }

    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 4rem 2rem;
        text-align: center;
    }

    .empty-state svg {
        width: 64px;
        height: 64px;
        color: var(--text-secondary, #94a3b8);
        margin-bottom: 1rem;
    }

    .empty-state h3 {
        margin: 0 0 0.5rem;
        color: var(--text-primary, #1e293b);
    }

    .empty-state p {
        color: var(--text-secondary, #64748b);
        margin: 0 0 1.5rem;
    }

    .limit-warning {
        background: rgba(245, 158, 11, 0.1);
        border: 1px solid rgba(245, 158, 11, 0.3);
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .limit-warning svg {
        width: 20px;
        height: 20px;
        color: #d97706;
        flex-shrink: 0;
    }

    .limit-warning p {
        margin: 0;
        color: #92400e;
        font-size: 0.875rem;
    }

    @media (max-width: 768px) {
        .users-header {
            flex-direction: column;
            align-items: stretch;
        }

        .users-stats {
            order: 2;
        }

        .btn-invite {
            text-align: center;
            justify-content: center;
        }

        .users-table {
            display: block;
            overflow-x: auto;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="users-page">
    <div class="users-header">
        <div class="users-stats">
            <div class="stat-item">
                <span>Utilisateurs actifs :</span>
                <span class="number"><?= $currentUserCount ?></span>
            </div>
            <div class="stat-item">
                <span>Limite du forfait :</span>
                <span class="number"><?= $maxUsers ?></span>
            </div>
        </div>

        <?php if ($canInvite): ?>
            <a href="<?= base_url('admin/users/invite') ?>" class="btn-invite">
                <svg viewBox="0 0 20 20" fill="currentColor" width="18" height="18">
                    <path
                        d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" />
                </svg>
                Inviter un utilisateur
            </a>
        <?php else: ?>
            <button class="btn-invite" disabled title="Limite atteinte">
                <svg viewBox="0 0 20 20" fill="currentColor" width="18" height="18">
                    <path
                        d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" />
                </svg>
                Limite atteinte
            </button>
        <?php endif; ?>
    </div>

    <?php if (!$canInvite): ?>
        <div class="limit-warning">
            <svg viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd" />
            </svg>
            <p>Vous avez atteint la limite d'utilisateurs de votre forfait (<?= $maxUsers ?>). <a
                    href="<?= base_url('settings/company/billing') ?>">Passez à un forfait supérieur</a> pour inviter plus
                d'utilisateurs.</p>
        </div>
    <?php endif; ?>

    <div class="users-table-container">
        <?php if (!empty($users)): ?>
            <table class="users-table">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                        <th>Dernière connexion</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <?php
                        $isCurrentUser = $user['user_id'] === session()->get('user_id');
                        $fullName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
                        $displayName = $fullName ?: $user['email'];
                        $initial = strtoupper(substr($displayName, 0, 1));
                        ?>
                        <tr>
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar">
                                        <?php if (!empty($user['avatar'])): ?>
                                            <img src="<?= base_url($user['avatar']) ?>" alt="<?= esc($displayName) ?>">
                                        <?php else: ?>
                                            <?= $initial ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="user-info">
                                        <span class="user-name">
                                            <?= esc($displayName) ?>
                                            <?php if ($isCurrentUser): ?>
                                                <small style="color: var(--primary-color)">(vous)</small>
                                            <?php endif; ?>
                                        </span>
                                        <span class="user-email"><?= esc($user['email']) ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="role-badge role-<?= esc($user['role_name']) ?>">
                                    <?php if ($user['role_name'] === 'admin'): ?>
                                        <svg viewBox="0 0 20 20" fill="currentColor" width="12" height="12">
                                            <path fill-rule="evenodd"
                                                d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    <?php endif; ?>
                                    <?= esc(ucfirst($user['role_name'])) ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-<?= esc($user['status']) ?>">
                                    <?php
                                    $statusLabels = [
                                        'active' => 'Actif',
                                        'pending' => 'En attente',
                                        'suspended' => 'Suspendu',
                                    ];
                                    echo $statusLabels[$user['status']] ?? ucfirst($user['status']);
                                    ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($user['last_login'])): ?>
                                    <span title="<?= esc($user['last_login']) ?>">
                                        <?= date('d/m/Y H:i', strtotime($user['last_login'])) ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: var(--text-secondary)">Jamais</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="actions-cell">
                                    <?php if (!$isCurrentUser): ?>
                                        <a href="<?= base_url('admin/users/edit/' . $user['user_id']) ?>" class="action-btn"
                                            title="Modifier le rôle">
                                            <svg viewBox="0 0 20 20" fill="currentColor">
                                                <path
                                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </a>

                                        <?php if ($user['status'] === 'active'): ?>
                                            <form action="<?= base_url('admin/users/suspend/' . $user['user_id']) ?>" method="post"
                                                style="display: inline;"
                                                onsubmit="return confirm('Voulez-vous vraiment suspendre cet utilisateur ?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="action-btn danger" title="Suspendre">
                                                    <svg viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </form>
                                        <?php elseif ($user['status'] === 'suspended'): ?>
                                            <form action="<?= base_url('admin/users/activate/' . $user['user_id']) ?>" method="post"
                                                style="display: inline;">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="action-btn" title="Réactiver">
                                                    <svg viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <form action="<?= base_url('admin/users/remove/' . $user['user_id']) ?>" method="post"
                                            style="display: inline;"
                                            onsubmit="return confirm('Voulez-vous vraiment retirer cet utilisateur de l\'entreprise ?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="action-btn danger" title="Retirer de l'entreprise">
                                                <svg viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span style="color: var(--text-secondary); font-size: 0.875rem;">—</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <svg viewBox="0 0 20 20" fill="currentColor">
                    <path
                        d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                </svg>
                <h3>Aucun utilisateur</h3>
                <p>Vous êtes le seul utilisateur de cette entreprise pour le moment.</p>
                <?php if ($canInvite): ?>
                    <a href="<?= base_url('admin/users/invite') ?>" class="btn-invite">
                        Inviter un utilisateur
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>