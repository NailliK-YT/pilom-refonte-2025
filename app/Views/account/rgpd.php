<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>Mes Données RGPD<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Protection de vos Données<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .rgpd-page {
        max-width: 800px;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .info-card {
        background: var(--bg-primary, white);
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
    }

    .info-card h2 {
        font-size: 1.125rem;
        margin: 0 0 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-card h2 svg {
        width: 20px;
        height: 20px;
        color: var(--primary-color, #3b82f6);
    }

    .info-card p {
        color: var(--text-secondary, #64748b);
        margin: 0 0 1rem;
        line-height: 1.6;
    }

    .data-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin: 1rem 0;
    }

    .stat-item {
        background: var(--bg-tertiary, #f8f9fa);
        padding: 1rem;
        border-radius: 0.5rem;
        text-align: center;
    }

    .stat-item .number {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-color, #3b82f6);
    }

    .stat-item .label {
        font-size: 0.75rem;
        color: var(--text-secondary, #64748b);
    }

    .action-card {
        background: var(--bg-primary, white);
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .action-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border-color, #e2e8f0);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .action-header svg {
        width: 24px;
        height: 24px;
    }

    .action-header.export svg {
        color: #10b981;
    }

    .action-header.delete svg {
        color: #ef4444;
    }

    .action-header h3 {
        margin: 0;
        font-size: 1rem;
    }

    .action-body {
        padding: 1.5rem;
    }

    .action-body p {
        color: var(--text-secondary, #64748b);
        margin: 0 0 1rem;
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
        text-decoration: none;
        border: none;
    }

    .btn-export {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .btn-export:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
    }

    .btn-danger {
        background: var(--bg-tertiary, #f8f9fa);
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    .btn-danger:hover {
        background: rgba(239, 68, 68, 0.1);
    }

    .btn svg {
        width: 16px;
        height: 16px;
    }

    .warning-box {
        background: rgba(245, 158, 11, 0.1);
        border: 1px solid #f59e0b;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .warning-box p {
        margin: 0;
        color: #92400e;
        font-size: 0.875rem;
    }

    .rights-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .rights-list li {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.75rem 0;
        border-bottom: 1px solid var(--border-color, #e2e8f0);
    }

    .rights-list li:last-child {
        border-bottom: none;
    }

    .rights-list svg {
        width: 20px;
        height: 20px;
        color: #10b981;
        flex-shrink: 0;
        margin-top: 0.125rem;
    }

    .rights-list strong {
        display: block;
        margin-bottom: 0.25rem;
    }

    .rights-list span {
        font-size: 0.875rem;
        color: var(--text-secondary, #64748b);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="rgpd-page">
    <?php if (session()->getFlashdata('success')): ?>
        <div
            style="background: rgba(16, 185, 129, 0.1); border: 1px solid #10b981; color: #059669; padding: 1rem; border-radius: 0.5rem;">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <!-- Your Data Summary -->
    <div class="info-card">
        <h2>
            <svg viewBox="0 0 20 20" fill="currentColor">
                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                <path fill-rule="evenodd"
                    d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                    clip-rule="evenodd" />
            </svg>
            Vos Données chez Pilom
        </h2>
        <p>
            Conformément au Règlement Général sur la Protection des Données (RGPD), vous avez le droit d'accéder à
            toutes vos données personnelles, de les exporter et de demander leur suppression.
        </p>

        <div class="data-stats">
            <div class="stat-item">
                <div class="number"><?= $companiesCount ?></div>
                <div class="label">Entreprise(s)</div>
            </div>
            <div class="stat-item">
                <div class="number"><?= number_format($activityCount) ?></div>
                <div class="label">Actions enregistrées</div>
            </div>
            <div class="stat-item">
                <div class="number"><?= date('d/m/Y', strtotime($user['created_at'])) ?></div>
                <div class="label">Membre depuis</div>
            </div>
        </div>
    </div>

    <!-- Your Rights -->
    <div class="info-card">
        <h2>
            <svg viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd" />
            </svg>
            Vos Droits RGPD
        </h2>
        <ul class="rights-list">
            <li>
                <svg viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clip-rule="evenodd" />
                </svg>
                <div>
                    <strong>Droit d'accès (Article 15)</strong>
                    <span>Accédez à toutes les données que nous détenons sur vous</span>
                </div>
            </li>
            <li>
                <svg viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clip-rule="evenodd" />
                </svg>
                <div>
                    <strong>Droit à la portabilité (Article 20)</strong>
                    <span>Exportez vos données dans un format structuré et lisible</span>
                </div>
            </li>
            <li>
                <svg viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clip-rule="evenodd" />
                </svg>
                <div>
                    <strong>Droit à l'effacement (Article 17)</strong>
                    <span>Demandez la suppression de votre compte et de vos données</span>
                </div>
            </li>
        </ul>
    </div>

    <!-- Export Data -->
    <div class="action-card">
        <div class="action-header export">
            <svg viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                    clip-rule="evenodd" />
            </svg>
            <h3>Exporter mes données</h3>
        </div>
        <div class="action-body">
            <p>
                Téléchargez toutes vos données personnelles dans un fichier JSON structuré.
                Ce fichier contient votre profil, vos adhésions aux entreprises et votre historique d'activité.
            </p>
            <a href="<?= base_url('account/rgpd/export') ?>" class="btn btn-export">
                <svg viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                </svg>
                Télécharger mes données (JSON)
            </a>
        </div>
    </div>

    <!-- Delete Account -->
    <div class="action-card">
        <div class="action-header delete">
            <svg viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                    clip-rule="evenodd" />
            </svg>
            <h3>Supprimer mon compte</h3>
        </div>
        <div class="action-body">
            <div class="warning-box">
                <p>
                    <strong>⚠️ Attention :</strong> Cette action est irréversible. Toutes vos données personnelles
                    seront supprimées définitivement sous 30 jours.
                </p>
            </div>
            <p>
                Si vous souhaitez supprimer votre compte et toutes les données associées, vous pouvez en faire la
                demande ci-dessous.
                Un administrateur traitera votre demande conformément à la réglementation RGPD.
            </p>
            <form method="post" action="<?= base_url('account/rgpd/delete') ?>"
                onsubmit="return confirm('Êtes-vous sûr de vouloir demander la suppression de votre compte ? Cette action est irréversible.');">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-danger">
                    <svg viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    Demander la suppression
                </button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>