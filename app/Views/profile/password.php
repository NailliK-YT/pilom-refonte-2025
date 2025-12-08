<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>Mot de passe<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/profile.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Profile Sub-navigation -->
<div class="profile-tabs">
    <a href="<?= base_url('profile') ?>" class="profile-tab">
        <svg viewBox="0 0 20 20" fill="currentColor" width="18" height="18">
            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
        </svg>
        Informations
    </a>
    <a href="<?= base_url('profile/password') ?>" class="profile-tab active">
        <svg viewBox="0 0 20 20" fill="currentColor" width="18" height="18">
            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
        </svg>
        Mot de passe
    </a>
    <a href="<?= base_url('notifications/preferences') ?>" class="profile-tab">
        <svg viewBox="0 0 20 20" fill="currentColor" width="18" height="18">
            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
        </svg>
        Notifications
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Changer le mot de passe</h2>
    </div>

    <form action="<?= base_url('profile/change-password') ?>" method="post" class="password-form">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="current_password">Mot de passe actuel <span class="required">*</span></label>
            <input type="password" class="form-control" id="current_password" name="current_password" required>
        </div>

        <div class="form-group">
            <label for="new_password">Nouveau mot de passe <span class="required">*</span></label>
            <input type="password" class="form-control" id="new_password" name="new_password" required minlength="8">
            <small class="form-text">Minimum 8 caractères.</small>

            <!-- Password strength indicator -->
            <div class="password-strength" id="passwordStrength">
                <div class="strength-bar">
                    <div class="strength-fill" id="strengthFill"></div>
                </div>
                <span class="strength-text" id="strengthText">Saisissez un mot de passe</span>
            </div>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirmer le nouveau mot de passe <span class="required">*</span></label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            <small class="form-text validation-message" id="confirmMessage"></small>
        </div>

        <div class="password-requirements">
            <h4>Votre mot de passe doit contenir :</h4>
            <ul>
                <li id="req-length">
                    <span class="req-icon">⚪</span> Au moins 8 caractères
                </li>
                <li id="req-uppercase">
                    <span class="req-icon">⚪</span> Au moins une lettre majuscule
                </li>
                <li id="req-lowercase">
                    <span class="req-icon">⚪</span> Au moins une lettre minuscule
                </li>
                <li id="req-number">
                    <span class="req-icon">⚪</span> Au moins un chiffre
                </li>
            </ul>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                Changer le mot de passe
            </button>
            <a href="<?= base_url('profile') ?>" class="btn btn-outline">Annuler</a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/profile.js') ?>"></script>
<?= $this->endSection() ?>