<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>Sécurité<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/profile.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Security Sub-navigation -->
<div class="profile-tabs">
    <a href="<?= base_url('account/security') ?>" class="profile-tab active">
        <svg viewBox="0 0 20 20" fill="currentColor" width="18" height="18">
            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        Vue d'ensemble
    </a>
    <a href="<?= base_url('account/login-history') ?>" class="profile-tab">
        <svg viewBox="0 0 20 20" fill="currentColor" width="18" height="18">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
        </svg>
        Historique connexions
    </a>
    <a href="<?= base_url('account/deletion') ?>" class="profile-tab">
        <svg viewBox="0 0 20 20" fill="currentColor" width="18" height="18">
            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        Suppression compte
    </a>
</div>

<?php if (isset($deletionRequest) && $deletionRequest): ?>
    <div class="alert alert-warning">
        <svg viewBox="0 0 20 20" fill="currentColor" width="20" height="20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        <div>
            <strong>Suppression planifiée</strong><br>
            Votre compte sera supprimé dans <strong><?= $daysRemaining ?? 0 ?> jour(s)</strong>.
            <a href="<?= base_url('account/deletion') ?>" style="text-decoration: underline;">Gérer la suppression</a>
        </div>
    </div>
<?php endif; ?>

<div class="security-grid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <svg viewBox="0 0 20 20" fill="currentColor" width="20" height="20" style="color: var(--primary-color);">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                </svg>
                Dernière connexion
            </h3>
        </div>
        <?php if (isset($lastLogin) && $lastLogin): ?>
            <div class="security-info">
                <div class="info-row">
                    <span class="info-label">Date</span>
                    <span class="info-value"><?= date('d/m/Y à H:i', strtotime($lastLogin['login_at'])) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Adresse IP</span>
                    <span class="info-value"><?= esc($lastLogin['ip_address']) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Appareil</span>
                    <span class="info-value"><?= esc(substr($lastLogin['user_agent'], 0, 60)) ?>...</span>
                </div>
            </div>
        <?php else: ?>
            <p class="text-muted">Aucune connexion récente enregistrée.</p>
        <?php endif; ?>
        <div class="card-footer">
            <a href="<?= base_url('account/login-history') ?>" class="btn btn-outline">
                <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                </svg>
                Voir l'historique complet
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <svg viewBox="0 0 20 20" fill="currentColor" width="20" height="20" style="color: var(--primary-color);">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                </svg>
                Mot de passe
            </h3>
        </div>
        <p>Modifiez régulièrement votre mot de passe pour sécuriser votre compte.</p>
        <p class="text-muted" style="font-size: 0.875rem;">Un mot de passe fort contient au moins 8 caractères avec des majuscules, minuscules, chiffres et symboles.</p>
        <div class="card-footer">
            <a href="<?= base_url('profile/password') ?>" class="btn btn-primary">
                <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                </svg>
                Changer le mot de passe
            </a>
        </div>
    </div>

    <div class="card danger-zone">
        <div class="card-header">
            <h3 class="card-title" style="color: var(--danger);">
                <svg viewBox="0 0 20 20" fill="currentColor" width="20" height="20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                Zone de danger
            </h3>
        </div>
        <p>La suppression de votre compte est irréversible. Toutes vos données seront définitivement perdues.</p>
        <div class="card-footer">
            <a href="<?= base_url('account/deletion') ?>" class="btn btn-danger">
                <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                Supprimer le compte
            </a>
        </div>
    </div>
</div>

<style>
.security-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 1.5rem;
}

.security-info {
    margin-bottom: 1rem;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--border-color, #e2e8f0);
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    font-size: 0.875rem;
    color: var(--text-muted, #6b7280);
}

.info-value {
    font-weight: 500;
    color: var(--dark-gray, #1e293b);
    text-align: right;
    max-width: 60%;
    word-break: break-word;
}

.card-footer {
    padding-top: 1rem;
    margin-top: 1rem;
    border-top: 1px solid var(--border-color, #e2e8f0);
}

.danger-zone {
    border-color: var(--danger, #dc2626);
    background: var(--danger-light, #fee2e2);
}

.card-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
</style>

<?= $this->endSection() ?>