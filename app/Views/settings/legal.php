<?= $this->extend('settings/layout') ?>

<?= $this->section('settings_content') ?>

<div class="card-header">
    <h2 class="card-title">Mentions légales et CGV</h2>
</div>

<form action="<?= base_url('settings/company/update-legal') ?>" method="post" class="legal-form">
    <?= csrf_field() ?>

        <div class="form-group">
            <label for="legal_mentions">Mentions légales</label>
            <textarea class="form-control wysiwyg" id="legal_mentions" name="legal_mentions"
                rows="10"><?= esc($settings['legal_mentions'] ?? old('legal_mentions')) ?></textarea>
            <small class="form-text">Informations affichées dans le footer de votre site.</small>
        </div>

        <div class="form-group">
            <label for="terms_conditions">Conditions générales de vente (CGV)</label>
            <textarea class="form-control wysiwyg" id="terms_conditions" name="terms_conditions"
                rows="10"><?= esc($settings['terms_conditions'] ?? old('terms_conditions')) ?></textarea>
            <small class="form-text">Affichées lors de la validation d'une commande.</small>
        </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
            Enregistrer
        </button>
    </div>
</form>

<?= $this->endSection() ?>