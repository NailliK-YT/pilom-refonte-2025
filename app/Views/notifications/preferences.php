<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>Notifications<?= $this->endSection() ?>

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
    <a href="<?= base_url('profile/password') ?>" class="profile-tab">
        <svg viewBox="0 0 20 20" fill="currentColor" width="18" height="18">
            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
        </svg>
        Mot de passe
    </a>
    <a href="<?= base_url('notifications/preferences') ?>" class="profile-tab active">
        <svg viewBox="0 0 20 20" fill="currentColor" width="18" height="18">
            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
        </svg>
        Notifications
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Préférences de notification</h2>
    </div>

    <form action="<?= base_url('notifications/update-preferences') ?>" method="post" class="preferences-form">
        <?= csrf_field() ?>

        <div class="preferences-section">
            <h3 class="form-section-title">
                <svg viewBox="0 0 20 20" fill="currentColor" width="18" height="18" style="color: var(--primary-color);">
                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                </svg>
                Notifications par email
            </h3>

            <div class="preference-item">
                <label class="switch-label">
                    <input type="checkbox" name="email_notifications" value="1" <?= ($preferences['email_notifications'] ?? true) ? 'checked' : '' ?>>
                    <span class="switch-slider"></span>
                    <span class="switch-text">Activer les notifications par email</span>
                </label>
                <small>Recevoir les notifications générales par email</small>
            </div>

            <div class="preference-item">
                <label class="switch-label">
                    <input type="checkbox" name="email_invoices" value="1" <?= ($preferences['email_invoices'] ?? true) ? 'checked' : '' ?>>
                    <span class="switch-slider"></span>
                    <span class="switch-text">Factures</span>
                </label>
                <small>Recevoir un email lors de la création ou mise à jour d'une facture</small>
            </div>

            <div class="preference-item">
                <label class="switch-label">
                    <input type="checkbox" name="email_quotes" value="1" <?= ($preferences['email_quotes'] ?? true) ? 'checked' : '' ?>>
                    <span class="switch-slider"></span>
                    <span class="switch-text">Devis</span>
                </label>
                <small>Recevoir un email lors de la création ou mise à jour d'un devis</small>
            </div>

            <div class="preference-item">
                <label class="switch-label">
                    <input type="checkbox" name="email_payments" value="1" <?= ($preferences['email_payments'] ?? true) ? 'checked' : '' ?>>
                    <span class="switch-slider"></span>
                    <span class="switch-text">Paiements</span>
                </label>
                <small>Recevoir un email lors de la réception d'un paiement</small>
            </div>

            <div class="preference-item">
                <label class="switch-label">
                    <input type="checkbox" name="email_marketing" value="1" <?= ($preferences['email_marketing'] ?? false) ? 'checked' : '' ?>>
                    <span class="switch-slider"></span>
                    <span class="switch-text">Marketing et nouveautés</span>
                </label>
                <small>Recevoir les actualités produit et offres spéciales</small>
            </div>
        </div>

        <div class="preferences-section">
            <h3 class="form-section-title">
                <svg viewBox="0 0 20 20" fill="currentColor" width="18" height="18" style="color: var(--primary-color);">
                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                </svg>
                Notifications push
            </h3>

            <div class="preference-item">
                <label class="switch-label">
                    <input type="checkbox" name="push_notifications" value="1" <?= ($preferences['push_notifications'] ?? true) ? 'checked' : '' ?>>
                    <span class="switch-slider"></span>
                    <span class="switch-text">Activer les notifications push</span>
                </label>
                <small>Recevoir des notifications sur votre appareil</small>
            </div>
        </div>

        <div class="preferences-section">
            <h3 class="form-section-title">
                <svg viewBox="0 0 20 20" fill="currentColor" width="18" height="18" style="color: var(--primary-color);">
                    <path fill-rule="evenodd" d="M7 2a2 2 0 00-2 2v12a2 2 0 002 2h6a2 2 0 002-2V4a2 2 0 00-2-2H7zm3 14a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                </svg>
                Notifications dans l'application
            </h3>

            <div class="preference-item">
                <label class="switch-label">
                    <input type="checkbox" name="inapp_notifications" value="1" <?= ($preferences['inapp_notifications'] ?? true) ? 'checked' : '' ?>>
                    <span class="switch-slider"></span>
                    <span class="switch-text">Activer les notifications dans l'application</span>
                </label>
                <small>Afficher les notifications lorsque vous êtes connecté</small>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                Enregistrer les préférences
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/profile.js') ?>"></script>
<?= $this->endSection() ?>