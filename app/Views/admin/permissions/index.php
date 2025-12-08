<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>Gestion des Permissions<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Matrice des Permissions<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .permissions-page {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .page-header h1 {
        font-size: 1.25rem;
        margin: 0;
    }

    .matrix-card {
        background: var(--bg-primary, white);
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .matrix-container {
        overflow-x: auto;
    }

    .permissions-matrix {
        width: 100%;
        border-collapse: collapse;
        min-width: 600px;
    }

    .permissions-matrix th,
    .permissions-matrix td {
        padding: 0.75rem;
        text-align: center;
        border-bottom: 1px solid var(--border-color, #e2e8f0);
    }

    .permissions-matrix th {
        background: var(--bg-tertiary, #f8f9fa);
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-secondary, #64748b);
    }

    .permissions-matrix th:first-child {
        text-align: left;
        min-width: 200px;
    }

    .permissions-matrix td:first-child {
        text-align: left;
    }

    .permissions-matrix tr:hover {
        background: var(--bg-tertiary, #f8f9fa);
    }

    .module-header {
        background: var(--bg-tertiary, #f1f5f9) !important;
        font-weight: 600;
        color: var(--primary-color, #3b82f6);
    }

    .module-header td {
        padding: 0.5rem 0.75rem !important;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-size: 0.75rem;
    }

    .perm-name {
        font-weight: 500;
        color: var(--text-primary, #1e293b);
    }

    .perm-desc {
        font-size: 0.75rem;
        color: var(--text-secondary, #64748b);
    }

    .check-icon {
        width: 20px;
        height: 20px;
        color: #10b981;
    }

    .x-icon {
        width: 20px;
        height: 20px;
        color: #e2e8f0;
    }

    .role-header {
        position: relative;
    }

    .role-name {
        display: block;
        font-weight: 600;
        color: var(--text-primary, #1e293b);
        margin-bottom: 0.25rem;
    }

    .role-count {
        font-size: 0.625rem;
        color: var(--text-secondary, #64748b);
    }

    .edit-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.5rem;
        background: var(--primary-color, #3b82f6);
        color: white;
        border-radius: 0.25rem;
        font-size: 0.625rem;
        text-decoration: none;
        margin-top: 0.375rem;
    }

    .edit-btn:hover {
        background: #2563eb;
    }

    .edit-btn svg {
        width: 10px;
        height: 10px;
    }

    .legend {
        display: flex;
        gap: 1.5rem;
        padding: 1rem;
        background: var(--bg-tertiary, #f8f9fa);
        border-top: 1px solid var(--border-color, #e2e8f0);
        font-size: 0.75rem;
        color: var(--text-secondary, #64748b);
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .module-translations {
        display: none;
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
<div class="permissions-page">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"
            style="background: rgba(16, 185, 129, 0.1); border: 1px solid #10b981; color: #059669; padding: 1rem; border-radius: 0.5rem;">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-error"
            style="background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #dc2626; padding: 1rem; border-radius: 0.5rem;">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div style="display: flex; justify-content: flex-end; margin-bottom: 1rem;">
        <a href="<?= base_url('admin/permissions/create') ?>" class="edit-btn"
            style="padding: 0.5rem 1rem; font-size: 0.875rem;">
            <svg viewBox="0 0 20 20" fill="currentColor" style="width: 14px; height: 14px;">
                <path fill-rule="evenodd"
                    d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                    clip-rule="evenodd" />
            </svg>
            Créer un rôle
        </a>
    </div>

    <div class="matrix-card">
        <div class="matrix-container">
            <table class="permissions-matrix">
                <thead>
                    <tr>
                        <th>Permission</th>
                        <?php foreach ($roles as $role): ?>
                            <th class="role-header">
                                <span class="role-name"><?= ucfirst(esc($role['name'])) ?></span>
                                <span class="role-count"><?= count($rolePermissions[$role['id']] ?? []) ?>
                                    permissions</span>
                                <a href="<?= base_url('admin/permissions/role/' . $role['id']) ?>" class="edit-btn">
                                    <svg viewBox="0 0 20 20" fill="currentColor">
                                        <path
                                            d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                    Modifier
                                </a>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($permissionsByModule as $module => $permissions): ?>
                        <tr class="module-header">
                            <td colspan="<?= count($roles) + 1 ?>">
                                <?= $moduleTranslations[$module] ?? ucfirst($module) ?>
                            </td>
                        </tr>
                        <?php foreach ($permissions as $perm): ?>
                            <tr>
                                <td>
                                    <div class="perm-name"><?= esc($perm['name']) ?></div>
                                    <div class="perm-desc"><?= esc($perm['description'] ?? '') ?></div>
                                </td>
                                <?php foreach ($roles as $role): ?>
                                    <td>
                                        <?php if (in_array($perm['id'], $rolePermissions[$role['id']] ?? [])): ?>
                                            <svg class="check-icon" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        <?php else: ?>
                                            <svg class="x-icon" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="legend">
            <div class="legend-item">
                <svg class="check-icon" viewBox="0 0 20 20" fill="currentColor" style="width:14px;height:14px;">
                    <path fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clip-rule="evenodd" />
                </svg>
                Permission accordée
            </div>
            <div class="legend-item">
                <svg class="x-icon" viewBox="0 0 20 20" fill="currentColor"
                    style="width:14px;height:14px;color:#94a3b8;">
                    <path fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                </svg>
                Permission non accordée
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>