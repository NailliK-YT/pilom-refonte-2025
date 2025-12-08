<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>Mes Entreprises<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Gestion des Entreprises<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .companies-page {
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

    .btn-create {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1.25rem;
        background: linear-gradient(135deg, var(--primary-color, #3b82f6), #1d4ed8);
        color: white;
        border-radius: 0.5rem;
        font-weight: 500;
        font-size: 0.875rem;
        text-decoration: none;
        transition: all 0.2s;
    }

    .btn-create:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .btn-create svg {
        width: 16px;
        height: 16px;
    }

    .companies-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
    }

    .company-card {
        background: var(--bg-primary, white);
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: all 0.2s;
        position: relative;
    }

    .company-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .company-card.current {
        border: 2px solid var(--primary-color, #3b82f6);
    }

    .company-card.primary::before {
        content: '★ Principale';
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        background: #f59e0b;
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.625rem;
        font-weight: 600;
    }

    .company-header {
        padding: 1.25rem;
        border-bottom: 1px solid var(--border-color, #e2e8f0);
    }

    .company-name {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-primary, #1e293b);
        margin: 0 0 0.25rem;
    }

    .company-role {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        background: rgba(59, 130, 246, 0.1);
        color: var(--primary-color, #3b82f6);
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .company-body {
        padding: 1rem 1.25rem;
    }

    .company-info {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: var(--text-secondary, #64748b);
    }

    .company-info-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .company-info-item svg {
        width: 14px;
        height: 14px;
        opacity: 0.6;
    }

    .company-footer {
        display: flex;
        gap: 0.5rem;
        padding: 1rem 1.25rem;
        border-top: 1px solid var(--border-color, #e2e8f0);
        background: var(--bg-tertiary, #f8f9fa);
        flex-wrap: wrap;
    }

    .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.375rem 0.625rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }

    .action-btn svg {
        width: 12px;
        height: 12px;
    }

    .btn-switch {
        background: var(--primary-color, #3b82f6);
        color: white;
    }

    .btn-switch:hover {
        background: #2563eb;
    }

    .btn-primary-company {
        background: #f59e0b;
        color: white;
    }

    .btn-primary-company:hover {
        background: #d97706;
    }

    .btn-transfer {
        background: rgba(139, 92, 246, 0.1);
        color: #7c3aed;
        border: 1px solid #8b5cf6;
    }

    .btn-transfer:hover {
        background: rgba(139, 92, 246, 0.2);
    }

    .btn-leave {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
        border: 1px solid #ef4444;
    }

    .btn-leave:hover {
        background: rgba(239, 68, 68, 0.2);
    }

    .current-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        background: #10b981;
        color: white;
        border-radius: 0.25rem;
        font-size: 0.625rem;
        font-weight: 600;
        margin-left: 0.5rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="companies-page">
    <?php if (session()->getFlashdata('success')): ?>
        <div
            style="background: rgba(16, 185, 129, 0.1); border: 1px solid #10b981; color: #059669; padding: 1rem; border-radius: 0.5rem;">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div
            style="background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #dc2626; padding: 1rem; border-radius: 0.5rem;">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="page-header">
        <div>
            <h2 style="margin: 0; font-size: 1.25rem;">Mes Entreprises</h2>
            <p style="margin: 0.25rem 0 0; color: var(--text-secondary, #64748b); font-size: 0.875rem;">
                Gérez vos entreprises et basculez entre elles
            </p>
        </div>
        <a href="<?= base_url('companies/create') ?>" class="btn-create">
            <svg viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                    clip-rule="evenodd" />
            </svg>
            Créer une entreprise
        </a>
    </div>

    <div class="companies-grid">
        <?php foreach ($companies as $company): ?>
            <?php
            $isCurrent = $company['company_id'] === $currentCompanyId;
            $isPrimary = !empty($company['is_primary']);
            $isAdmin = strtolower($company['role_name'] ?? '') === 'admin';
            ?>
            <div class="company-card <?= $isCurrent ? 'current' : '' ?> <?= $isPrimary ? 'primary' : '' ?>">
                <div class="company-header">
                    <h3 class="company-name">
                        <?= esc($company['company_name']) ?>
                        <?php if ($isCurrent): ?>
                            <span class="current-badge">Actuelle</span>
                        <?php endif; ?>
                    </h3>
                    <span class="company-role"><?= ucfirst(esc($company['role_name'] ?? 'Membre')) ?></span>
                </div>

                <div class="company-body">
                    <div class="company-info">
                        <?php if (!empty($company['business_sector_name'])): ?>
                            <div class="company-info-item">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 01-1 1h-2a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <?= esc($company['business_sector_name']) ?>
                            </div>
                        <?php endif; ?>
                        <div class="company-info-item">
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path
                                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1z" />
                            </svg>
                            Membre depuis
                            <?= date('d/m/Y', strtotime($company['joined_at'] ?? $company['created_at'] ?? 'now')) ?>
                        </div>
                    </div>
                </div>

                <div class="company-footer">
                    <?php if (!$isCurrent): ?>
                        <form method="post" action="<?= base_url('switch-company/' . $company['company_id']) ?>"
                            style="display: inline;">
                            <?= csrf_field() ?>
                            <button type="submit" class="action-btn btn-switch">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                                        clip-rule="evenodd" />
                                </svg>
                                Basculer
                            </button>
                        </form>
                    <?php endif; ?>

                    <?php if (!$isPrimary): ?>
                        <form method="post" action="<?= base_url('companies/primary/' . $company['company_id']) ?>"
                            style="display: inline;">
                            <?= csrf_field() ?>
                            <button type="submit" class="action-btn btn-primary-company">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                Définir principale
                            </button>
                        </form>
                    <?php endif; ?>

                    <?php if ($isAdmin): ?>
                        <a href="<?= base_url('companies/transfer/' . $company['company_id']) ?>"
                            class="action-btn btn-transfer">
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path
                                    d="M8 5a1 1 0 100 2h5.586l-1.293 1.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L13.586 5H8zM12 15a1 1 0 100-2H6.414l1.293-1.293a1 1 0 10-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L6.414 15H12z" />
                            </svg>
                            Transférer
                        </a>
                    <?php endif; ?>

                    <?php if (count($companies) > 1): ?>
                        <form method="post" action="<?= base_url('companies/leave/' . $company['company_id']) ?>"
                            onsubmit="return confirm('Êtes-vous sûr de vouloir quitter cette entreprise ?');"
                            style="display: inline;">
                            <?= csrf_field() ?>
                            <button type="submit" class="action-btn btn-leave">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M3 3a1 1 0 00-1 1v12a1 1 0 001 1h12a1 1 0 001-1V7.414l-5-5H4a1 1 0 00-1-1V3zm6.854 7.854a.5.5 0 01-.708 0L7.5 9.207V12a.5.5 0 01-1 0V9.207l-1.646 1.647a.5.5 0 01-.708-.708l2.5-2.5a.5.5 0 01.708 0l2.5 2.5a.5.5 0 010 .708z"
                                        clip-rule="evenodd" />
                                </svg>
                                Quitter
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?= $this->endSection() ?>