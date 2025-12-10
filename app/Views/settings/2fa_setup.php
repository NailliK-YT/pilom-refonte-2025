<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>Configurer 2FA<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <div class="page-header">
        <h1>Configurer l'authentification à deux facteurs</h1>
        <p class="subtitle">Scannez le QR code avec votre application d'authentification</p>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <div class="setup-2fa-container">
        <div class="setup-steps">
            <div class="step">
                <span class="step-number">1</span>
                <div class="step-content">
                    <h3>Téléchargez une application d'authentification</h3>
                    <p>Si ce n'est pas déjà fait, installez une application comme :</p>
                    <ul class="app-list">
                        <li>
                            <strong>Google Authenticator</strong>
                            <span class="app-links">
                                <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2"
                                    target="_blank">Android</a> |
                                <a href="https://apps.apple.com/app/google-authenticator/id388497605"
                                    target="_blank">iOS</a>
                            </span>
                        </li>
                        <li>
                            <strong>Microsoft Authenticator</strong>
                            <span class="app-links">
                                <a href="https://play.google.com/store/apps/details?id=com.azure.authenticator"
                                    target="_blank">Android</a> |
                                <a href="https://apps.apple.com/app/microsoft-authenticator/id983156458"
                                    target="_blank">iOS</a>
                            </span>
                        </li>
                        <li>
                            <strong>Authy</strong>
                            <span class="app-links">
                                <a href="https://authy.com/download/" target="_blank">Toutes plateformes</a>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="step">
                <span class="step-number">2</span>
                <div class="step-content">
                    <h3>Scannez ce QR code</h3>
                    <p>Ouvrez votre application et scannez le code ci-dessous :</p>

                    <div class="qr-code-container">
                        <img src="<?= esc($qrCodeUrl) ?>" alt="QR Code 2FA" class="qr-code-image">
                    </div>

                    <details class="manual-entry">
                        <summary>Impossible de scanner ? Entrez le code manuellement</summary>
                        <div class="manual-code">
                            <label>Compte :</label>
                            <code><?= esc($email) ?></code>
                            <label>Clé secrète :</label>
                            <code class="secret-key"><?= esc($secret) ?></code>
                            <p class="hint">Type : TOTP (basé sur le temps)</p>
                        </div>
                    </details>
                </div>
            </div>

            <div class="step">
                <span class="step-number">3</span>
                <div class="step-content">
                    <h3>Entrez le code de vérification</h3>
                    <p>Entrez le code à 6 chiffres affiché dans votre application :</p>

                    <form action="<?= base_url('account/2fa/verify') ?>" method="post" class="verify-form">
                        <?= csrf_field() ?>
                        <div class="code-input-group">
                            <input type="text" name="code" id="verification-code" maxlength="6" pattern="\d{6}"
                                inputmode="numeric" autocomplete="one-time-code" placeholder="000000" required
                                autofocus>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Activer l'authentification à deux facteurs
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="setup-sidebar">
            <div class="info-card">
                <h4>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    Qu'est-ce que la 2FA ?
                </h4>
                <p>
                    L'authentification à deux facteurs (2FA) ajoute une couche de sécurité supplémentaire à votre
                    compte.
                    En plus de votre mot de passe, vous devrez entrer un code temporaire généré par votre téléphone.
                </p>
            </div>

            <div class="info-card warning">
                <h4>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                    Important
                </h4>
                <p>
                    Après activation, vous recevrez des <strong>codes de secours</strong>.
                    Conservez-les précieusement dans un endroit sûr. Ils vous permettront de récupérer l'accès
                    à votre compte si vous perdez votre téléphone.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    .setup-2fa-container {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 2rem;
        max-width: 1200px;
    }

    @media (max-width: 992px) {
        .setup-2fa-container {
            grid-template-columns: 1fr;
        }

        .setup-sidebar {
            order: -1;
        }
    }

    .setup-steps {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    .step {
        display: flex;
        gap: 1.5rem;
        background: var(--bg-card, white);
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .step-number {
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        background: var(--primary-gradient, linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%));
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .step-content {
        flex: 1;
    }

    .step-content h3 {
        margin: 0 0 0.5rem;
        font-size: 1.1rem;
        color: var(--text-primary, #1e293b);
    }

    .step-content p {
        color: var(--text-secondary, #64748b);
        margin: 0 0 1rem;
    }

    .app-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .app-list li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem;
        background: var(--bg-secondary, #f8fafc);
        border-radius: 8px;
        margin-bottom: 0.5rem;
    }

    .app-links {
        font-size: 0.85rem;
    }

    .app-links a {
        color: var(--primary-color, #3b82f6);
    }

    .qr-code-container {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        display: inline-block;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        margin: 1rem 0;
    }

    .qr-code-image {
        display: block;
        width: 200px;
        height: 200px;
    }

    .manual-entry {
        margin-top: 1rem;
    }

    .manual-entry summary {
        cursor: pointer;
        color: var(--primary-color, #3b82f6);
        font-size: 0.9rem;
    }

    .manual-code {
        background: var(--bg-secondary, #f8fafc);
        padding: 1rem;
        border-radius: 8px;
        margin-top: 0.5rem;
    }

    .manual-code label {
        display: block;
        font-size: 0.8rem;
        color: var(--text-secondary, #64748b);
        margin-bottom: 0.25rem;
    }

    .manual-code code {
        display: block;
        background: white;
        padding: 0.5rem;
        border-radius: 4px;
        font-family: monospace;
        margin-bottom: 0.75rem;
        word-break: break-all;
    }

    .manual-code .secret-key {
        font-size: 1.1rem;
        letter-spacing: 2px;
    }

    .manual-code .hint {
        font-size: 0.8rem;
        color: var(--text-secondary, #64748b);
        margin: 0;
    }

    .verify-form {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        max-width: 300px;
    }

    .code-input-group input {
        width: 100%;
        padding: 1rem;
        font-size: 1.5rem;
        text-align: center;
        letter-spacing: 8px;
        border: 2px solid var(--border-color, #e2e8f0);
        border-radius: 12px;
        font-family: monospace;
        transition: border-color 0.2s;
    }

    .code-input-group input:focus {
        outline: none;
        border-color: var(--primary-color, #3b82f6);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .btn-lg {
        padding: 1rem 1.5rem;
        font-size: 1rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .setup-sidebar {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .info-card {
        background: var(--bg-card, white);
        padding: 1.25rem;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .info-card h4 {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin: 0 0 0.75rem;
        font-size: 0.95rem;
        color: var(--text-primary, #1e293b);
    }

    .info-card p {
        font-size: 0.875rem;
        color: var(--text-secondary, #64748b);
        line-height: 1.6;
        margin: 0;
    }

    .info-card.warning {
        border-left: 4px solid #f59e0b;
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    }

    .info-card.warning h4 {
        color: #b45309;
    }

    .info-card.warning p {
        color: #92400e;
    }
</style>

<script>
    // Auto-focus and format code input
    document.addEventListener('DOMContentLoaded', function () {
        const codeInput = document.getElementById('verification-code');

        codeInput.addEventListener('input', function (e) {
            // Remove non-digits
            this.value = this.value.replace(/\D/g, '').slice(0, 6);

            // Auto-submit when 6 digits entered
            if (this.value.length === 6) {
                // Small delay to let user see the full code
                setTimeout(() => {
                    this.form.submit();
                }, 300);
            }
        });
    });
</script>
<?= $this->endSection() ?>