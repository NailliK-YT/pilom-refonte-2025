<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>Alertes de Sécurité<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Alertes de Sécurité<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .security-page {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .alert-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .alert-card {
        background: var(--bg-primary, white);
        border-radius: 0.75rem;
        padding: 1.25rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border-left: 4px solid;
    }

    .alert-card.danger {
        border-left-color: #ef4444;
    }

    .alert-card.warning {
        border-left-color: #f59e0b;
    }

    .alert-card.info {
        border-left-color: #3b82f6;
    }

    .alert-card h3 {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0 0 0.25rem;
        color: var(--text-primary, #1e293b);
    }

    .alert-card p {
        font-size: 0.75rem;
        color: var(--text-secondary, #64748b);
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .section-card {
        background: var(--bg-primary, white);
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border-color, #e2e8f0);
        background: var(--bg-tertiary, #f8f9fa);
    }

    .section-header h2 {
        font-size: 1rem;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-header h2 svg {
        width: 18px;
        height: 18px;
    }

    .section-header.danger h2 {
        color: #ef4444;
    }

    .section-header.warning h2 {
        color: #f59e0b;
    }

    .section-header.info h2 {
        color: #3b82f6;
    }

    .section-body {
        padding: 0;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table th,
    .data-table td {
        padding: 0.75rem 1rem;
        text-align: left;
        border-bottom: 1px solid var(--border-color, #e2e8f0);
    }

    .data-table th {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-secondary, #64748b);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .data-table tr:last-child td {
        border-bottom: none;
    }

    .data-table tr:hover {
        background: var(--bg-tertiary, #f8f9fa);
    }

    .ip-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
        border-radius: 0.25rem;
        font-family: monospace;
        font-size: 0.75rem;
    }

    .attempts-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.5rem;
        background: #ef4444;
        color: white;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .empty-state {
        text-align: center;
        padding: 2rem;
        color: var(--text-secondary, #64748b);
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
        color: var(--primary-color, #3b82f6);
        text-decoration: none;
        font-size: 0.875rem;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    .back-link svg {
        width: 16px;
        height: 16px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="security-page">
    <a href="<?= base_url('admin/dashboard') ?>" class="back-link">
        <svg viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd"
                d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                clip-rule="evenodd" />
        </svg>
        Retour au tableau de bord
    </a>

    <!-- Alert Summary -->
    <div class="alert-summary">
        <div class="alert-card danger">
            <h3><?= count($failedLogins) ?></h3>
            <p>Échecs de connexion (7j)</p>
        </div>
        <div class="alert-card warning">
            <h3><?= count($suspiciousIPs) ?></h3>
            <p>IPs suspectes (24h)</p>
        </div>
        <div class="alert-card info">
            <h3><?= count($passwordResets) ?></h3>
            <p>Réinitialisations MDP (7j)</p>
        </div>
    </div>

    <!-- Suspicious IPs -->
    <?php if (!empty($suspiciousIPs)): ?>
        <div class="section-card">
            <div class="section-header danger">
                <h2>
                    <svg viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    IPs Suspectes (> 3 tentatives en 24h)
                </h2>
            </div>
            <div class="section-body">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Adresse IP</th>
                            <th>Tentatives</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($suspiciousIPs as $ip): ?>
                            <tr>
                                <td><span class="ip-badge"><?= esc($ip['ip_address']) ?></span></td>
                                <td><span class="attempts-badge"><?= $ip['attempts'] ?> tentatives</span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- Failed Logins -->
    <div class="section-card">
        <div class="section-header warning">
            <h2>
                <svg viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd" />
                </svg>
                Échecs de Connexion (7 derniers jours)
            </h2>
        </div>
        <div class="section-body">
            <?php if (!empty($failedLogins)): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Email</th>
                            <th>Adresse IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($failedLogins as $login): ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($login['created_at'])) ?></td>
                                <td><?= esc($login['user_email'] ?? 'Inconnu') ?></td>
                                <td><span class="ip-badge"><?= esc($login['ip_address'] ?? '-') ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <p>Aucun échec de connexion dans les 7 derniers jours 🎉</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Password Resets -->
    <div class="section-card">
        <div class="section-header info">
            <h2>
                <svg viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                        clip-rule="evenodd" />
                </svg>
                Réinitialisations de Mot de Passe (7 jours)
            </h2>
        </div>
        <div class="section-body">
            <?php if (!empty($passwordResets)): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Utilisateur</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($passwordResets as $reset): ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($reset['created_at'])) ?></td>
                                <td><?= esc($reset['user_email'] ?? 'Inconnu') ?></td>
                                <td><?= $reset['action'] === 'password.reset' ? 'Demande de reset' : 'Changement' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <p>Aucune réinitialisation de mot de passe</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Role Changes -->
    <div class="section-card">
        <div class="section-header info">
            <h2>
                <svg viewBox="0 0 20 20" fill="currentColor">
                    <path
                        d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                </svg>
                Changements de Rôle (7 jours)
            </h2>
        </div>
        <div class="section-body">
            <?php if (!empty($roleChanges)): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Par</th>
                            <th>Entité</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($roleChanges as $change): ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($change['created_at'])) ?></td>
                                <td><?= esc($change['user_email'] ?? 'Système') ?></td>
                                <td><?= esc($change['entity_id'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <p>Aucun changement de rôle récent</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>