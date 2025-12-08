<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>Copier un Rôle<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Copier le Rôle <?= ucfirst(esc($sourceRole['name'])) ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .copy-role-page {
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
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .card-header h2 {
        margin: 0;
        font-size: 1.125rem;
    }

    .card-body {
        padding: 1.5rem;
    }

    .source-info {
        background: var(--bg-tertiary, #f8f9fa);
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    .source-info h3 {
        margin: 0 0 0.5rem;
        font-size: 0.875rem;
        color: var(--text-secondary, #64748b);
    }

    .source-info .role-name {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-primary, #1e293b);
    }

    .source-info .role-desc {
        font-size: 0.875rem;
        color: var(--text-secondary, #64748b);
        margin-top: 0.25rem;
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

    .btn-success {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .btn-success:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
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
<div class="copy-role-page">
    <a href="<?= base_url('admin/permissions') ?>" class="back-link">
        <svg viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd"
                d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                clip-rule="evenodd" />
        </svg>
        Retour à la matrice
    </a>

    <?php if (session()->getFlashdata('error')): ?>
        <div
            style="background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #dc2626; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= base_url('admin/permissions/copy/' . $sourceRole['id']) ?>">
        <?= csrf_field() ?>

        <div class="form-card">
            <div class="card-header">
                <h2>Copier le Rôle</h2>
            </div>

            <div class="card-body">
                <div class="source-info">
                    <h3>Rôle source</h3>
                    <div class="role-name"><?= ucfirst(esc($sourceRole['name'])) ?></div>
                    <?php if (!empty($sourceRole['description'])): ?>
                        <div class="role-desc"><?= esc($sourceRole['description']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="name">Nom du nouveau rôle *</label>
                    <input type="text" name="name" id="name" class="form-control" required
                        placeholder="Ex: <?= esc($sourceRole['name']) ?>_copy"
                        value="<?= old('name', $sourceRole['name'] . '_copy') ?>">
                    <span class="hint">Le nom sera automatiquement converti en minuscules.</span>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="3"
                        placeholder="Description des responsabilités et accès de ce rôle..."><?= old('description', $sourceRole['description'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="card-footer">
                <a href="<?= base_url('admin/permissions') ?>" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-success">
                    <svg viewBox="0 0 20 20" fill="currentColor">
                        <path d="M7 9a2 2 0 012-2h6a2 2 0 012 2v6a2 2 0 01-2 2H9a2 2 0 01-2-2V9z" />
                        <path d="M5 3a2 2 0 00-2 2v6a2 2 0 002 2V5h8a2 2 0 00-2-2H5z" />
                    </svg>
                    Créer la copie
                </button>
            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>