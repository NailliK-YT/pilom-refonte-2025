<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>Tableau de Bord Admin<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Tableau de Bord Administration<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .admin-dashboard {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    /* Stats Cards Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .stat-card {
        background: var(--bg-primary, white);
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stat-icon svg {
        width: 24px;
        height: 24px;
        color: white;
    }

    .stat-icon.blue {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    }

    .stat-icon.green {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .stat-icon.yellow {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .stat-icon.red {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .stat-content h3 {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-primary, #1e293b);
        margin: 0;
    }

    .stat-content p {
        font-size: 0.75rem;
        color: var(--text-secondary, #64748b);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin: 0;
    }

    /* Two column layout */
    .dashboard-columns {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
    }

    @media (max-width: 1024px) {
        .dashboard-columns {
            grid-template-columns: 1fr;
        }
    }

    /* Cards */
    .dashboard-card {
        background: var(--bg-primary, white);
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border-color, #e2e8f0);
    }

    .card-header h2 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary, #1e293b);
        margin: 0;
    }

    .card-header a {
        font-size: 0.75rem;
        color: var(--primary-color, #3b82f6);
        text-decoration: none;
    }

    .card-header a:hover {
        text-decoration: underline;
    }

    .card-body {
        padding: 1.25rem;
    }

    /* Activity Feed */
    .activity-feed {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .activity-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.75rem 0;
        border-bottom: 1px solid var(--border-color, #e2e8f0);
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .activity-icon svg {
        width: 14px;
        height: 14px;
        color: white;
    }

    .activity-icon.login {
        background: #10b981;
    }

    .activity-icon.logout {
        background: #6b7280;
    }

    .activity-icon.create {
        background: #3b82f6;
    }

    .activity-icon.update {
        background: #f59e0b;
    }

    .activity-icon.delete {
        background: #ef4444;
    }

    .activity-icon.default {
        background: #94a3b8;
    }

    .activity-content {
        flex: 1;
        min-width: 0;
    }

    .activity-text {
        font-size: 0.875rem;
        color: var(--text-primary, #1e293b);
        margin: 0;
    }

    .activity-text strong {
        font-weight: 600;
    }

    .activity-time {
        font-size: 0.75rem;
        color: var(--text-secondary, #64748b);
        margin: 0.25rem 0 0;
    }

    /* Chart Container */
    .chart-container {
        height: 200px;
        display: flex;
        align-items: flex-end;
        justify-content: space-around;
        gap: 0.5rem;
        padding: 1rem 0;
    }

    .chart-bar {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
    }

    .bar {
        width: 40px;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        border-radius: 4px 4px 0 0;
        min-height: 4px;
        transition: height 0.3s ease;
    }

    .bar-label {
        font-size: 0.625rem;
        color: var(--text-secondary, #64748b);
        text-transform: uppercase;
    }

    .bar-value {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-primary, #1e293b);
    }

    /* Role Distribution */
    .role-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .role-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid var(--border-color, #e2e8f0);
    }

    .role-item:last-child {
        border-bottom: none;
    }

    .role-name {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: var(--text-primary, #1e293b);
    }

    .role-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .role-dot.admin {
        background: #ef4444;
    }

    .role-dot.user {
        background: #3b82f6;
    }

    .role-dot.comptable {
        background: #10b981;
    }

    .role-dot.default {
        background: #94a3b8;
    }

    .role-count {
        font-weight: 600;
        color: var(--text-primary, #1e293b);
    }

    /* Security Alert */
    .security-alert {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        background: rgba(239, 68, 68, 0.1);
        border-radius: 0.5rem;
        margin-bottom: 1rem;
    }

    .security-alert.warning {
        background: rgba(245, 158, 11, 0.1);
    }

    .security-alert svg {
        width: 20px;
        height: 20px;
        color: #ef4444;
        flex-shrink: 0;
    }

    .security-alert.warning svg {
        color: #f59e0b;
    }

    .security-alert p {
        margin: 0;
        font-size: 0.875rem;
        color: var(--text-primary, #1e293b);
    }

    .empty-state {
        text-align: center;
        padding: 2rem;
        color: var(--text-secondary, #64748b);
    }

    .empty-state svg {
        width: 40px;
        height: 40px;
        margin-bottom: 0.5rem;
        opacity: 0.5;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-dashboard">
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue">
                <svg viewBox="0 0 20 20" fill="currentColor">
                    <path
                        d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                </svg>
            </div>
            <div class="stat-content">
                <h3><?= $totalUsers ?></h3>
                <p>Utilisateurs</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon green">
                <svg viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
            </div>
            <div class="stat-content">
                <h3><?= $activeUsers ?></h3>
                <p>Actifs</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon yellow">
                <svg viewBox="0 0 20 20" fill="currentColor">
                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                </svg>
            </div>
            <div class="stat-content">
                <h3><?= $pendingInvitations ?></h3>
                <p>Invitations</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon red">
                <svg viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z"
                        clip-rule="evenodd" />
                </svg>
            </div>
            <div class="stat-content">
                <h3><?= $suspendedUsers ?></h3>
                <p>Suspendus</p>
            </div>
        </div>
    </div>

    <!-- Security Alert if failed logins -->
    <?php if ($failedLogins > 0): ?>
        <div class="security-alert <?= $failedLogins > 5 ? '' : 'warning' ?>">
            <svg viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd" />
            </svg>
            <p>
                <strong><?= $failedLogins ?> tentative(s) de connexion échouée(s)</strong> dans les dernières 24 heures.
                <a href="<?= base_url('admin/dashboard/security') ?>">Voir les alertes de sécurité →</a>
            </p>
        </div>
    <?php endif; ?>

    <div class="dashboard-columns">
        <!-- Left Column -->
        <div class="left-column">
            <!-- Activity Feed -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2>Activité Récente</h2>
                    <a href="<?= base_url('admin/audit') ?>">Voir tout</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($recentActivity)): ?>
                        <ul class="activity-feed">
                            <?php foreach ($recentActivity as $activity): ?>
                                <?php
                                $iconClass = 'default';
                                if (strpos($activity['action'], 'login') !== false)
                                    $iconClass = 'login';
                                elseif (strpos($activity['action'], 'logout') !== false)
                                    $iconClass = 'logout';
                                elseif (strpos($activity['action'], 'create') !== false || strpos($activity['action'], 'invite') !== false)
                                    $iconClass = 'create';
                                elseif (strpos($activity['action'], 'update') !== false)
                                    $iconClass = 'update';
                                elseif (strpos($activity['action'], 'delete') !== false || strpos($activity['action'], 'remove') !== false)
                                    $iconClass = 'delete';
                                ?>
                                <li class="activity-item">
                                    <div class="activity-icon <?= $iconClass ?>">
                                        <svg viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="activity-content">
                                        <p class="activity-text">
                                            <strong><?= esc(trim(($activity['first_name'] ?? '') . ' ' . ($activity['last_name'] ?? '')) ?: $activity['user_email'] ?? 'Système') ?></strong>
                                            a effectué : <?= esc($activity['action']) ?>
                                        </p>
                                        <p class="activity-time">
                                            <?= date('d/m/Y H:i', strtotime($activity['created_at'])) ?>
                                        </p>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="empty-state">
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                    clip-rule="evenodd" />
                            </svg>
                            <p>Aucune activité récente</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Login Chart -->
            <div class="dashboard-card" style="margin-top: 1.5rem;">
                <div class="card-header">
                    <h2>Connexions (7 derniers jours)</h2>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <?php
                        $maxCount = max(array_column($loginStats, 'count')) ?: 1;
                        foreach ($loginStats as $stat):
                            $height = ($stat['count'] / $maxCount) * 150;
                            ?>
                            <div class="chart-bar">
                                <span class="bar-value"><?= $stat['count'] ?></span>
                                <div class="bar" style="height: <?= max($height, 4) ?>px;"></div>
                                <span class="bar-label"><?= $stat['label'] ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="right-column">
            <!-- Users by Role -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2>Répartition par Rôle</h2>
                    <a href="<?= base_url('admin/users') ?>">Gérer</a>
                </div>
                <div class="card-body">
                    <ul class="role-list">
                        <?php foreach ($usersByRole as $role => $count): ?>
                            <li class="role-item">
                                <span class="role-name">
                                    <span class="role-dot <?= $role ?>"></span>
                                    <?= ucfirst(esc($role)) ?>
                                </span>
                                <span class="role-count"><?= $count ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Recent Logins -->
            <div class="dashboard-card" style="margin-top: 1.5rem;">
                <div class="card-header">
                    <h2>Dernières Connexions</h2>
                </div>
                <div class="card-body">
                    <?php if (!empty($recentLogins)): ?>
                        <ul class="activity-feed">
                            <?php foreach ($recentLogins as $login): ?>
                                <li class="activity-item">
                                    <div class="activity-icon login">
                                        <svg viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M3 3a1 1 0 011 1v12a1 1 0 11-2 0V4a1 1 0 011-1zm7.707 3.293a1 1 0 010 1.414L9.414 9H17a1 1 0 110 2H9.414l1.293 1.293a1 1 0 01-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="activity-content">
                                        <p class="activity-text">
                                            <?= esc(trim(($login['first_name'] ?? '') . ' ' . ($login['last_name'] ?? '')) ?: $login['user_email']) ?>
                                        </p>
                                        <p class="activity-time">
                                            <?= date('d/m H:i', strtotime($login['created_at'])) ?>
                                            · <?= esc($login['ip_address'] ?? '-') ?>
                                        </p>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="empty-state">
                            <p>Aucune connexion récente</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Company Info -->
            <?php if (!empty($company)): ?>
                <div class="dashboard-card" style="margin-top: 1.5rem;">
                    <div class="card-header">
                        <h2>Entreprise</h2>
                        <a href="<?= base_url('settings/company') ?>">Modifier</a>
                    </div>
                    <div class="card-body">
                        <p style="margin: 0 0 0.5rem; font-weight: 600;"><?= esc($company['name']) ?></p>
                        <p style="margin: 0; font-size: 0.875rem; color: var(--text-secondary);">
                            Plan: <?= ucfirst(esc($company['subscription_plan'] ?? 'free')) ?>
                            <br>
                            Max utilisateurs: <?= esc($company['max_users'] ?? '∞') ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>