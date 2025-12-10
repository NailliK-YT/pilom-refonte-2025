<?= $this->extend('layouts/public') ?>

<?= $this->section('title') ?>Trop de tentatives<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="auth-page">
    <div class="auth-container">
        <div class="auth-card rate-limited-card">
            <div class="auth-header">
                <div class="auth-icon error-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <h1 class="auth-title">Accès temporairement bloqué</h1>
            </div>

            <div class="auth-body">
                <div class="alert alert-warning">
                    <p>
                        <strong>Trop de tentatives de connexion échouées.</strong>
                    </p>
                    <p>
                        Pour protéger votre compte, l'accès a été temporairement bloqué.
                        Veuillez réessayer dans <strong id="countdown"><?= esc($minutes) ?></strong>
                        minute<?= $minutes > 1 ? 's' : '' ?>.
                    </p>
                </div>

                <div class="info-box">
                    <h3>Pourquoi ce blocage ?</h3>
                    <p>
                        Cette mesure de sécurité protège votre compte contre les tentatives d'accès non autorisées.
                        Si vous avez oublié votre mot de passe, vous pouvez le réinitialiser.
                    </p>
                </div>

                <div class="auth-actions">
                    <a href="<?= base_url('forgot-password') ?>" class="btn btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        Mot de passe oublié
                    </a>
                    <a href="<?= base_url() ?>" class="btn btn-outline">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m12 19-7-7 7-7"></path>
                            <path d="M19 12H5"></path>
                        </svg>
                        Retour à l'accueil
                    </a>
                </div>
            </div>

            <div class="auth-footer">
                <p class="text-muted">
                    Vous serez débloqué à <?= date('H:i', strtotime($blocked_until)) ?>
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    .rate-limited-card {
        max-width: 480px;
        margin: 2rem auto;
        text-align: center;
    }

    .auth-icon.error-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 1.5rem;
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #dc2626;
    }

    .alert-warning {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border: 1px solid #f59e0b;
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        color: #92400e;
    }

    .alert-warning p {
        margin: 0;
    }

    .alert-warning p+p {
        margin-top: 0.5rem;
    }

    .info-box {
        background: var(--bg-secondary, #f8fafc);
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        text-align: left;
    }

    .info-box h3 {
        font-size: 0.9rem;
        font-weight: 600;
        margin: 0 0 0.5rem;
        color: var(--text-primary, #1e293b);
    }

    .info-box p {
        font-size: 0.875rem;
        color: var(--text-secondary, #64748b);
        margin: 0;
        line-height: 1.5;
    }

    .auth-actions {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .auth-actions .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.875rem 1.5rem;
        font-weight: 500;
        border-radius: 10px;
        transition: all 0.2s ease;
    }

    .btn-secondary {
        background: var(--primary-gradient, linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%));
        color: white;
        border: none;
    }

    .btn-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .btn-outline {
        background: transparent;
        color: var(--text-secondary, #64748b);
        border: 1px solid var(--border-color, #e2e8f0);
    }

    .btn-outline:hover {
        background: var(--bg-secondary, #f8fafc);
        color: var(--text-primary, #1e293b);
    }

    .auth-footer {
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border-color, #e2e8f0);
    }

    #countdown {
        font-weight: 700;
        color: #dc2626;
    }
</style>

<script>
    // Countdown timer
    (function () {
        const countdownEl = document.getElementById('countdown');
        let minutes = <?= (int) $minutes ?>;

        function updateCountdown() {
            if (minutes > 0) {
                minutes--;
                countdownEl.textContent = minutes;

                if (minutes <= 0) {
                    // Redirect to login page when unblocked
                    window.location.href = '<?= base_url('login') ?>';
                }
            }
        }

        // Update every minute
        setInterval(updateCountdown, 60000);
    })();
</script>
<?= $this->endSection() ?>