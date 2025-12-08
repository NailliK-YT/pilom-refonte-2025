<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>Modifier les Permissions<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Permissions du rôle <?= ucfirst(esc($role['name'])) ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .edit-permissions-page {
        max-width: 800px;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--primary-color, #3b82f6);
        text-decoration: none;
        font-size: 0.875rem;
        margin-bottom: 1rem;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    .back-link svg {
        width: 16px;
        height: 16px;
    }

    .role-card {
        background: var(--bg-primary, white);
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.25rem;
        border-bottom: 1px solid var(--border-color, #e2e8f0);
        background: linear-gradient(135deg, var(--primary-color, #3b82f6), #1d4ed8);
        color: white;
    }

    .card-header h2 {
        font-size: 1.125rem;
        margin: 0;
    }

    .card-header .count {
        background: rgba(255, 255, 255, 0.2);
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
    }

    .card-body {
        padding: 1.5rem;
    }

    .module-group {
        margin-bottom: 1.5rem;
    }

    .module-group:last-child {
        margin-bottom: 0;
    }

    .module-title {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--primary-color, #3b82f6);
        margin-bottom: 0.75rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--primary-color, #3b82f6);
    }

    .permissions-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .permission-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        background: var(--bg-tertiary, #f8f9fa);
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .permission-item:hover {
        background: var(--bg-secondary, #f1f5f9);
    }

    .permission-item.selected {
        background: rgba(59, 130, 246, 0.1);
        border: 1px solid var(--primary-color, #3b82f6);
    }

    .permission-checkbox {
        width: 18px;
        height: 18px;
        accent-color: var(--primary-color, #3b82f6);
        cursor: pointer;
    }

    .permission-info {
        flex: 1;
    }

    .permission-name {
        font-weight: 500;
        color: var(--text-primary, #1e293b);
        font-size: 0.875rem;
    }

    .permission-desc {
        font-size: 0.75rem;
        color: var(--text-secondary, #64748b);
    }

    .card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
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

    .quick-actions {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .quick-btn {
        padding: 0.375rem 0.75rem;
        font-size: 0.75rem;
        background: var(--bg-tertiary, #f1f5f9);
        border: 1px solid var(--border-color, #e2e8f0);
        border-radius: 0.375rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .quick-btn:hover {
        background: var(--bg-secondary, #e2e8f0);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$moduleTranslations = [
    'contacts' => 'Contacts',
    'devis' => 'Devis',
    'factures' => 'Factures',
    'depenses' => 'Dépenses',
    'settings' => 'Paramètres',
    'users' => 'Utilisateurs',
    'documents' => 'Documents',
    'statistics' => 'Statistiques',
    'company' => 'Entreprise',
    'other' => 'Autres',
];
?>
<div class="edit-permissions-page">
    <a href="<?= base_url('admin/permissions') ?>" class="back-link">
        <svg viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd"
                d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                clip-rule="evenodd" />
        </svg>
        Retour à la matrice
    </a>

    <form method="post" action="<?= base_url('admin/permissions/role/' . $role['id']) ?>">
        <?= csrf_field() ?>

        <div class="role-card">
            <div class="card-header">
                <h2>Rôle: <?= ucfirst(esc($role['name'])) ?></h2>
                <span class="count" id="selectedCount"><?= count($permissionIds) ?> sélectionnées</span>
            </div>

            <div class="card-body">
                <div class="quick-actions">
                    <button type="button" class="quick-btn" onclick="selectAll()">Tout sélectionner</button>
                    <button type="button" class="quick-btn" onclick="deselectAll()">Tout désélectionner</button>
                </div>

                <?php foreach ($permissionsByModule as $module => $permissions): ?>
                    <div class="module-group">
                        <h3 class="module-title"><?= $moduleTranslations[$module] ?? ucfirst($module) ?></h3>
                        <div class="permissions-list">
                            <?php foreach ($permissions as $perm): ?>
                                <?php $isSelected = in_array($perm['id'], $permissionIds); ?>
                                <label class="permission-item <?= $isSelected ? 'selected' : '' ?>">
                                    <input type="checkbox" name="permissions[]" value="<?= $perm['id'] ?>"
                                        class="permission-checkbox" <?= $isSelected ? 'checked' : '' ?>
                                        onchange="updateCount(); this.closest('.permission-item').classList.toggle('selected', this.checked)">
                                    <div class="permission-info">
                                        <div class="permission-name"><?= esc($perm['name']) ?></div>
                                        <?php if (!empty($perm['description'])): ?>
                                            <div class="permission-desc"><?= esc($perm['description']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="card-footer">
                <a href="<?= base_url('admin/permissions') ?>" class="btn btn-secondary">
                    Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <svg viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                    Enregistrer les modifications
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    function updateCount() {
        const count = document.querySelectorAll('.permission-checkbox:checked').length;
        document.getElementById('selectedCount').textContent = count + ' sélectionnées';
    }

    function selectAll() {
        document.querySelectorAll('.permission-checkbox').forEach(cb => {
            cb.checked = true;
            cb.closest('.permission-item').classList.add('selected');
        });
        updateCount();
    }

    function deselectAll() {
        document.querySelectorAll('.permission-checkbox').forEach(cb => {
            cb.checked = false;
            cb.closest('.permission-item').classList.remove('selected');
        });
        updateCount();
    }
</script>
<?= $this->endSection() ?>