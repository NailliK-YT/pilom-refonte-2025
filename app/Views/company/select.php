<?= $this->extend('layouts/public') ?>

<?= $this->section('title') ?>Sélectionner une entreprise<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="auth-container">
    <div class="auth-card company-select-card">
        <div class="auth-header">
            <div class="auth-logo">
                <svg width="48" height="48" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16 2L2 9V23L16 30L30 23V9L16 2Z" fill="url(#gradient)" />
                    <circle cx="16" cy="16" r="5" fill="white" />
                    <defs>
                        <linearGradient id="gradient" x1="2" y1="2" x2="30" y2="30">
                            <stop offset="0%" stop-color="#3b82f6" />
                            <stop offset="100%" stop-color="#2563eb" />
                        </linearGradient>
                    </defs>
                </svg>
            </div>
            <h1>Sélectionnez une entreprise</h1>
            <p>Vous avez accès à plusieurs entreprises. Choisissez celle sur laquelle vous souhaitez travailler.</p>
        </div>

        <div class="companies-grid">
            <?php foreach ($companies as $company): ?>
                <form action="<?= base_url('switch-company/' . $company['company_id']) ?>" method="post"
                    class="company-option">
                    <?= csrf_field() ?>
                    <button type="submit" class="company-card">
                        <div class="company-logo">
                            <?php if (!empty($company['company_logo'])): ?>
                                <img src="<?= base_url($company['company_logo']) ?>" alt="<?= esc($company['company_name']) ?>">
                            <?php else: ?>
                                <span><?= strtoupper(substr($company['company_name'], 0, 2)) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="company-info">
                            <h3><?= esc($company['company_name']) ?></h3>
                            <span class="company-role role-<?= esc($company['role_name']) ?>">
                                <?= esc(ucfirst($company['role_name'])) ?>
                            </span>
                        </div>
                        <svg class="arrow-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </form>
            <?php endforeach; ?>
        </div>

        <div class="auth-footer">
            <a href="<?= base_url('logout') ?>">
                <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
                    <path fill-rule="evenodd"
                        d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z"
                        clip-rule="evenodd" />
                </svg>
                Se déconnecter
            </a>
        </div>
    </div>
</div>

<style>
    .auth-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        background: linear-gradient(135deg, #f0f4ff 0%, #e8f0fe 100%);
    }

    .auth-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
        width: 100%;
        max-width: 480px;
        padding: 2rem;
    }

    .company-select-card {
        max-width: 520px;
    }

    .auth-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .auth-logo {
        margin-bottom: 1.5rem;
    }

    .auth-header h1 {
        margin: 0 0 0.5rem;
        font-size: 1.5rem;
        color: #1e293b;
    }

    .auth-header p {
        margin: 0;
        color: #64748b;
        font-size: 0.875rem;
    }

    .companies-grid {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .company-option {
        margin: 0;
    }

    .company-card {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 0.75rem;
        cursor: pointer;
        transition: all 0.2s;
        text-align: left;
    }

    .company-card:hover {
        border-color: #3b82f6;
        background: rgba(59, 130, 246, 0.05);
        transform: translateX(4px);
    }

    .company-logo {
        width: 48px;
        height: 48px;
        border-radius: 0.5rem;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        flex-shrink: 0;
    }

    .company-logo img {
        width: 100%;
        height: 100%;
        border-radius: 0.5rem;
        object-fit: cover;
    }

    .company-info {
        flex: 1;
    }

    .company-info h3 {
        margin: 0 0 0.25rem;
        font-size: 1rem;
        color: #1e293b;
    }

    .company-role {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .role-admin {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
    }

    .role-user {
        background: rgba(59, 130, 246, 0.1);
        color: #2563eb;
    }

    .role-comptable {
        background: rgba(16, 185, 129, 0.1);
        color: #059669;
    }

    .arrow-icon {
        width: 20px;
        height: 20px;
        color: #94a3b8;
        transition: color 0.2s;
    }

    .company-card:hover .arrow-icon {
        color: #3b82f6;
    }

    .auth-footer {
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e2e8f0;
        text-align: center;
    }

    .auth-footer a {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #64748b;
        text-decoration: none;
        font-size: 0.875rem;
        transition: color 0.2s;
    }

    .auth-footer a:hover {
        color: #1e293b;
    }
</style>
<?= $this->endSection() ?>