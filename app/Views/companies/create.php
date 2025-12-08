<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>Créer une Entreprise<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Nouvelle Entreprise<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .create-company-page {
        max-width: 600px;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--primary-color, #3b82f6);
        text-decoration: none;
        font-size: 0.875rem;
        margin-bottom: 1.5rem;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    .back-link svg {
        width: 16px;
        height: 16px;
    }

    .form-card {
        background: var(--bg-primary, white);
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .card-header {
        padding: 1.25rem;
        border-bottom: 1px solid var(--border-color, #e2e8f0);
        background: linear-gradient(135deg, var(--primary-color, #3b82f6), #1d4ed8);
        color: white;
    }

    .card-header h2 {
        margin: 0;
        font-size: 1.125rem;
    }

    .card-body {
        padding: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-group label {
        display: block;
        font-weight: 500;
        color: var(--text-primary, #1e293b);
        margin-bottom: 0.375rem;
        font-size: 0.875rem;
    }

    .form-group .hint {
        font-size: 0.75rem;
        color: var(--text-secondary, #64748b);
        margin-top: 0.25rem;
    }

    .form-control {
        width: 100%;
        padding: 0.625rem 0.875rem;
        border: 1px solid var(--border-color, #e2e8f0);
        border-radius: 0.5rem;
        font-size: 0.875rem;
        transition: border-color 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-color, #3b82f6);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .section-title {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--primary-color, #3b82f6);
        margin: 1.5rem 0 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--primary-color, #3b82f6);
    }

    .card-footer {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--border-color, #e2e8f0);
        background: var(--bg-tertiary, #f8f9fa);
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1.25rem;
        border-radius: 0.5rem;
        font-weight: 500;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        text-decoration: none;
    }

    .btn-primary {
        background: var(--primary-color, #3b82f6);
        color: white;
    }

    .btn-primary:hover {
        background: #2563eb;
    }

    .btn-secondary {
        background: var(--bg-primary, white);
        color: var(--text-primary, #1e293b);
        border: 1px solid var(--border-color, #e2e8f0);
    }

    .btn-secondary:hover {
        background: var(--bg-tertiary, #f8f9fa);
    }

    .btn svg {
        width: 16px;
        height: 16px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="create-company-page">
    <a href="<?= base_url('companies') ?>" class="back-link">
        <svg viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd"
                d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                clip-rule="evenodd" />
        </svg>
        Retour à mes entreprises
    </a>

    <?php if (session()->getFlashdata('error')): ?>
        <div
            style="background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #dc2626; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= base_url('companies/create') ?>">
        <?= csrf_field() ?>

        <div class="form-card">
            <div class="card-header">
                <h2>Créer une Entreprise</h2>
            </div>

            <div class="card-body">
                <div class="form-group">
                    <label for="name">Nom de l'entreprise *</label>
                    <input type="text" name="name" id="name" class="form-control" required
                        placeholder="Ma Nouvelle Entreprise" value="<?= old('name') ?>">
                </div>

                <div class="form-group">
                    <label for="business_sector_id">Secteur d'activité</label>
                    <select name="business_sector_id" id="business_sector_id" class="form-control">
                        <option value="">— Sélectionner —</option>
                        <?php foreach ($businessSectors as $sector): ?>
                            <option value="<?= $sector['id'] ?>"><?= esc($sector['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <h3 class="section-title">Informations légales (optionnel)</h3>

                <div class="form-group">
                    <label for="siret">SIRET</label>
                    <input type="text" name="siret" id="siret" class="form-control" placeholder="12345678901234"
                        maxlength="14" value="<?= old('siret') ?>">
                    <span class="hint">14 chiffres</span>
                </div>

                <h3 class="section-title">Adresse (optionnel)</h3>

                <div class="form-group">
                    <label for="address">Adresse</label>
                    <input type="text" name="address" id="address" class="form-control"
                        placeholder="123 rue de l'Exemple" value="<?= old('address') ?>">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="postal_code">Code postal</label>
                        <input type="text" name="postal_code" id="postal_code" class="form-control" placeholder="75000"
                            maxlength="5" value="<?= old('postal_code') ?>">
                    </div>

                    <div class="form-group">
                        <label for="city">Ville</label>
                        <input type="text" name="city" id="city" class="form-control" placeholder="Paris"
                            value="<?= old('city') ?>">
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <a href="<?= base_url('companies') ?>" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">
                    <svg viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                            clip-rule="evenodd" />
                    </svg>
                    Créer l'entreprise
                </button>
            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>