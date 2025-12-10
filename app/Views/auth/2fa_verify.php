<?= $this->extend('layouts/auth') ?>

<?= $this->section('title') ?>Vérification 2FA<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </div>
                <h1 class="auth-title">Vérification en deux étapes</h1>
                <p class="auth-subtitle">Entrez le code de votre application d'authentification</p>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('auth/2fa-verify') ?>" method="post" class="auth-form" id="verify-form">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="code" class="visually-hidden">Code de vérification</label>
                    <input type="text" name="code" id="code" class="code-input" maxlength="9" inputmode="text"
                        autocomplete="one-time-code" placeholder="000000" pattern="[A-Za-z0-9\-]{6,9}" required
                        autofocus>
                    <p class="form-hint">Code à 6 chiffres ou code de secours (XXXX-XXXX)</p>
                </div>

                <button type="submit" class="btn btn-primary btn-block" id="verify-btn">
                    <span class="btn-text">Vérifier</span>
                    <span class="btn-loader" style="display: none;">
                        <svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor" class="spinner-icon">
                            <path d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z"
                                opacity=".25" />
                            <path
                                d="M12,4a8,8,0,0,1,7.89,6.7A1.53,1.53,0,0,0,21.38,12h0a1.5,1.5,0,0,0,1.48-1.75,11,11,0,0,0-21.72,0A1.5,1.5,0,0,0,2.62,12h0a1.53,1.53,0,0,0,1.49-1.3A8,8,0,0,1,12,4Z" />
                        </svg>
                        Vérification...
                    </span>
                </button>
            </form>

            <div class="auth-links">
                <details class="help-section">
                    <summary>Vous n'avez pas accès à votre code ?</summary>
                    <div class="help-content">
                        <p>Utilisez l'un de vos <strong>codes de secours</strong> à la place.</p>
                        <p>Si vous n'avez plus accès à vos codes de secours, contactez le support.</p>
                    </div>
                </details>

                <a href="<?= base_url('logout') ?>" class="back-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m12 19-7-7 7-7"></path>
                        <path d="M19 12H5"></path>
                    </svg>
                    Retour à la connexion
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .auth-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    }

    .auth-container {
        width: 100%;
        max-width: 420px;
    }

    .auth-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.1);
    }

    .auth-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .auth-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 1rem;
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .auth-title {
        margin: 0 0 0.5rem;
        font-size: 1.5rem;
        color: var(--text-primary, #1e293b);
    }

    .auth-subtitle {
        margin: 0;
        color: var(--text-secondary, #64748b);
        font-size: 0.95rem;
    }

    .alert-danger {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #dc2626;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
    }

    .auth-form {
        margin-bottom: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .code-input {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        padding: 1rem;
        font-size: 1.5rem;
        text-align: center;
        letter-spacing: 3px;
        border: 2px solid var(--border-color, #e2e8f0);
        border-radius: 12px;
        font-family: 'Courier New', monospace;
        transition: all 0.2s;
        text-transform: uppercase;
    }

    .code-input:focus {
        outline: none;
        border-color: var(--primary-color, #3b82f6);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .code-input::placeholder {
        letter-spacing: 3px;
        color: #cbd5e1;
    }

    .form-hint {
        margin: 0.5rem 0 0;
        font-size: 0.8rem;
        color: var(--text-secondary, #64748b);
        text-align: center;
    }

    .btn-primary {
        width: 100%;
        padding: 1rem;
        font-size: 1rem;
        font-weight: 600;
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: white;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .btn-block {
        display: block;
        width: 100%;
    }

    .btn-loader {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .spinner-icon {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .auth-links {
        border-top: 1px solid var(--border-color, #e2e8f0);
        padding-top: 1.5rem;
    }

    .help-section {
        margin-bottom: 1rem;
    }

    .help-section summary {
        cursor: pointer;
        font-size: 0.9rem;
        color: var(--primary-color, #3b82f6);
    }

    .help-content {
        margin-top: 0.75rem;
        padding: 1rem;
        background: var(--bg-secondary, #f8fafc);
        border-radius: 8px;
        font-size: 0.875rem;
        color: var(--text-secondary, #64748b);
    }

    .help-content p {
        margin: 0 0 0.5rem;
    }

    .help-content p:last-child {
        margin-bottom: 0;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        color: var(--text-secondary, #64748b);
        text-decoration: none;
        transition: color 0.2s;
    }

    .back-link:hover {
        color: var(--primary-color, #3b82f6);
    }

    .visually-hidden {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        border: 0;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const codeInput = document.getElementById('code');
        const verifyForm = document.getElementById('verify-form');
        const verifyBtn = document.getElementById('verify-btn');

        // Add loading state on form submit
        verifyForm.addEventListener('submit', function () {
            const btnText = verifyBtn.querySelector('.btn-text');
            const btnLoader = verifyBtn.querySelector('.btn-loader');

            btnText.style.display = 'none';
            btnLoader.style.display = 'inline-flex';
            verifyBtn.disabled = true;
        });

        codeInput.addEventListener('input', function (e) {
            let value = this.value.toUpperCase();

            // If it looks like a backup code (contains letters), allow it
            if (/[A-Z]/.test(value)) {
                // Format as XXXX-XXXX if needed
                value = value.replace(/[^A-Z0-9]/g, '');
                if (value.length > 4 && !value.includes('-')) {
                    value = value.slice(0, 4) + '-' + value.slice(4, 8);
                }
                this.value = value.slice(0, 9);
            } else {
                // Regular 6-digit TOTP code
                this.value = value.replace(/\D/g, '').slice(0, 6);
            }
        });
    });
</script>
<?= $this->endSection() ?>