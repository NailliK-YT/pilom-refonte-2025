<?= $this->extend('settings/layout') ?>

<?= $this->section('settings_content') ?>

<div class="card-header">
    <h2 class="card-title">Informations de l'entreprise</h2>
</div>

<form action="<?= base_url('settings/company/update') ?>" method="post" class="company-form" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="logo-section">
        <div class="logo-preview">
            <?php if (!empty($settings['logo'])): ?>
                <img src="<?= base_url($settings['logo']) ?>" alt="Logo de l'entreprise" id="logoPreview">
            <?php else: ?>
                <div class="logo-placeholder">
                    <svg viewBox="0 0 20 20" fill="currentColor" width="32" height="32">
                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                    </svg>
                </div>
            <?php endif; ?>
        </div>
        <div class="logo-actions">
            <div class="logo-info">
                <h4>Logo de l'entreprise</h4>
                <p>Formats acceptés : PNG, JPG, SVG. Taille recommandée : 200x100px</p>
            </div>
            <div class="logo-buttons">
                <label for="logoUpload" class="btn btn-outline">
                    <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    Télécharger
                </label>
                <input type="file" id="logoUpload" name="logo" accept="image/png,image/jpeg,image/jpg,image/svg+xml" style="display: none;">
                <?php if (!empty($settings['logo'])): ?>
                    <button type="button" class="btn btn-outline danger-text" id="deleteLogo">Supprimer</button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="form-divider"></div>

    <div class="form-group">
        <label for="company_name" class="form-label">Nom de l'entreprise <span class="required">*</span></label>
        <input type="text" class="form-control" id="company_name" name="company_name"
            value="<?= esc($company['name'] ?? old('company_name')) ?>" placeholder="Ex: PILOM SAS" required>
    </div>

    <div class="form-group">
        <label for="address" class="form-label">Adresse</label>
        <textarea class="form-control" id="address" name="address" rows="2"
            placeholder="Ex: 123 Avenue des Champs-Élysées"><?= esc($settings['address'] ?? old('address')) ?></textarea>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="postal_code" class="form-label">Code postal</label>
            <input type="text" class="form-control" id="postal_code" name="postal_code"
                value="<?= esc($settings['postal_code'] ?? old('postal_code')) ?>" placeholder="Ex: 75008">
        </div>

        <div class="form-group">
            <label for="city" class="form-label">Ville</label>
            <input type="text" class="form-control" id="city" name="city"
                value="<?= esc($settings['city'] ?? old('city')) ?>" placeholder="Ex: Paris">
        </div>
    </div>

    <div class="form-group">
        <label for="country" class="form-label">Pays</label>
        <select class="form-control form-select" id="country" name="country">
            <option value="France" <?= ($settings['country'] ?? 'France') == 'France' ? 'selected' : '' ?>>🇫🇷 France</option>
            <option value="Belgique" <?= ($settings['country'] ?? '') == 'Belgique' ? 'selected' : '' ?>>🇧🇪 Belgique</option>
            <option value="Suisse" <?= ($settings['country'] ?? '') == 'Suisse' ? 'selected' : '' ?>>🇨🇭 Suisse</option>
            <option value="Canada" <?= ($settings['country'] ?? '') == 'Canada' ? 'selected' : '' ?>>🇨🇦 Canada</option>
        </select>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="phone" class="form-label">Téléphone</label>
            <input type="tel" class="form-control" id="phone" name="phone"
                value="<?= esc($settings['phone'] ?? old('phone')) ?>" placeholder="Ex: +33 1 23 45 67 89">
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email"
                value="<?= esc($settings['email'] ?? old('email')) ?>" placeholder="contact@pilom.fr">
        </div>
    </div>

    <div class="form-group">
        <label for="website" class="form-label">Site web</label>
        <input type="url" class="form-control" id="website" name="website"
            value="<?= esc($settings['website'] ?? old('website')) ?>" placeholder="https://www.pilom.fr">
    </div>

    <div class="form-divider"></div>

    <h3 class="form-section-title">Informations légales</h3>

    <div class="form-row">
        <div class="form-group">
            <label for="siret" class="form-label">SIRET</label>
            <input type="text" class="form-control" id="siret" name="siret"
                value="<?= esc($settings['siret'] ?? old('siret')) ?>" maxlength="14" pattern="[0-9]{14}"
                placeholder="12345678901234">
            <span class="form-text">14 chiffres sans espaces</span>
        </div>

        <div class="form-group">
            <label for="vat_number" class="form-label">Numéro de TVA</label>
            <input type="text" class="form-control" id="vat_number" name="vat_number"
                value="<?= esc($settings['vat_number'] ?? old('vat_number')) ?>" placeholder="FR12345678901">
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

<?= $this->endSection() ?>