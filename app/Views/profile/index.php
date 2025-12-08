<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>Mon Profil<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/profile.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Profile Sub-navigation -->
<div class="profile-tabs">
    <a href="<?= base_url('profile') ?>" class="profile-tab active">
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
    <a href="<?= base_url('notifications/preferences') ?>" class="profile-tab">
        <svg viewBox="0 0 20 20" fill="currentColor" width="18" height="18">
            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
        </svg>
        Notifications
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Informations personnelles</h2>
    </div>

    <form action="<?= base_url('profile/update') ?>" method="post" class="profile-form">
        <?= csrf_field() ?>

        <div class="profile-photo-section">
            <div class="photo-preview">
                <?php if (!empty($profile['profile_photo'])): ?>
                    <img src="<?= base_url($profile['profile_photo']) ?>" alt="Photo de profil" id="profilePhotoPreview">
                <?php else: ?>
                    <div class="photo-placeholder">
                        <span><?= strtoupper(substr($user['email'], 0, 1)) ?></span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="photo-actions">
                <div class="photo-info">
                    <h4>Photo de profil</h4>
                    <p>Formats acceptés : JPG, PNG, GIF. Max 2MB.</p>
                </div>
                <div class="photo-buttons">
                    <label for="photoUpload" class="btn btn-outline">
                        <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
                            <path fill-rule="evenodd" d="M4 5a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2h-1.586a1 1 0 01-.707-.293l-1.121-1.121A2 2 0 0011.172 3H8.828a2 2 0 00-1.414.586L6.293 4.707A1 1 0 015.586 5H4zm6 9a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                        </svg>
                        Changer
                    </label>
                    <input type="file" id="photoUpload" name="photo" accept="image/*" style="display: none;">
                    <?php if (!empty($profile['profile_photo'])): ?>
                        <button type="button" class="btn btn-outline danger-text" id="deletePhoto">Supprimer</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="form-divider"></div>

        <div class="form-row">
            <div class="form-group">
                <label for="first_name" class="form-label">Prénom</label>
                <input type="text" class="form-control" id="first_name" name="first_name"
                    value="<?= esc($profile['first_name'] ?? old('first_name')) ?>" placeholder="Votre prénom">
            </div>

            <div class="form-group">
                <label for="last_name" class="form-label">Nom</label>
                <input type="text" class="form-control" id="last_name" name="last_name"
                    value="<?= esc($profile['last_name'] ?? old('last_name')) ?>" placeholder="Votre nom">
            </div>
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" value="<?= esc($user['email']) ?>" disabled>
            <span class="form-text">L'adresse email ne peut pas être modifiée.</span>
        </div>

        <div class="form-group">
            <label for="phone" class="form-label">Téléphone</label>
            <input type="tel" class="form-control" id="phone" name="phone"
                value="<?= esc($profile['phone'] ?? old('phone')) ?>" placeholder="06 12 34 56 78">
        </div>

        <div class="form-divider"></div>
        
        <h3 class="form-section-title">Préférences régionales</h3>

        <div class="form-row">
            <div class="form-group">
                <label for="locale" class="form-label">Langue</label>
                <select class="form-control form-select" id="locale" name="locale">
                    <option value="fr_FR" <?= ($profile['locale'] ?? 'fr_FR') == 'fr_FR' ? 'selected' : '' ?>>🇫🇷 Français</option>
                    <option value="en_US" <?= ($profile['locale'] ?? '') == 'en_US' ? 'selected' : '' ?>>🇬🇧 English</option>
                    <option value="es_ES" <?= ($profile['locale'] ?? '') == 'es_ES' ? 'selected' : '' ?>>🇪🇸 Español</option>
                </select>
            </div>

            <div class="form-group">
                <label for="timezone" class="form-label">Fuseau horaire</label>
                <select class="form-control form-select" id="timezone" name="timezone">
                    <option value="Europe/Paris" <?= ($profile['timezone'] ?? 'Europe/Paris') == 'Europe/Paris' ? 'selected' : '' ?>>Europe/Paris (UTC+1)</option>
                    <option value="Europe/London" <?= ($profile['timezone'] ?? '') == 'Europe/London' ? 'selected' : '' ?>>Europe/London (UTC+0)</option>
                    <option value="America/New_York" <?= ($profile['timezone'] ?? '') == 'America/New_York' ? 'selected' : '' ?>>America/New_York (UTC-5)</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                Enregistrer les modifications
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/profile.js') ?>"></script>
<?= $this->endSection() ?>