<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>Configurer 2FA<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <div class="page-header">
        <div class="warning-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
        </div>
        <h1>Configuration de sécurité requise</h1>
        <p class="subtitle">
            En tant qu'administrateur, vous devez activer l'authentification à deux facteurs
        </p>
    </div>

    <div class="required-2fa-container">
        <div class="info-card">
            <h3>Pourquoi la 2FA est-elle obligatoire ?</h3>
            <ul>
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Protège les données sensibles de vos clients et entreprises
                </li>
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Empêche les accès non autorisés même si votre mot de passe est compromis
                </li>
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Conforme aux meilleures pratiques de sécurité pour les comptes administrateurs
                </li>
            </ul>
        </div>

        <a href="<?= base_url('account/2fa/setup') ?>" class="btn btn-primary btn-lg">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
            Configurer l'authentification à deux facteurs
        </a>
    </div>
</div>

<style>
    .required-2fa-container {
        max-width: 600px;
        margin: 0 auto;
        text-align: center;
    }

    .warning-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 1rem;
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #b45309;
    }

    .page-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .info-card {
        background: var(--bg-card, white);
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
        text-align: left;
    }

    .info-card h3 {
        margin: 0 0 1rem;
        font-size: 1.1rem;
    }

    .info-card ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .info-card li {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.75rem 0;
        border-bottom: 1px solid var(--border-color, #e2e8f0);
        color: var(--text-secondary, #64748b);
    }

    .info-card li:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .info-card li svg {
        flex-shrink: 0;
        color: #16a34a;
        margin-top: 2px;
    }

    .btn-lg {
        padding: 1rem 2rem;
        font-size: 1.1rem;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
    }
</style>
<?= $this->endSection() ?>