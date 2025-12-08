<?= $this->extend('layouts/public') ?>

<?= $this->section('title') ?>Réinitialiser le mot de passe<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo">
                <a href="<?= base_url() ?>">
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
                </a>
            </div>
            <h1>Nouveau mot de passe</h1>
            <p>Créez un nouveau mot de passe sécurisé pour votre compte.</p>
        </div>

        <?php if (session()->has('errors')): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach (session('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('reset-password') ?>" method="post" class="auth-form">
            <?= csrf_field() ?>
            <input type="hidden" name="token" value="<?= esc($token) ?>">

            <div class="form-group">
                <label for="password">Nouveau mot de passe</label>
                <div class="password-input-wrapper">
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••"
                        minlength="8" required autofocus>
                    <button type="button" class="toggle-password" onclick="togglePassword('password')">
                        <svg class="eye-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                            <path fill-rule="evenodd"
                                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                <div class="password-strength" id="passwordStrength">
                    <div class="strength-bar"></div>
                    <span class="strength-text"></span>
                </div>
            </div>

            <div class="form-group">
                <label for="password_confirm">Confirmer le mot de passe</label>
                <div class="password-input-wrapper">
                    <input type="password" id="password_confirm" name="password_confirm" class="form-control"
                        placeholder="••••••••" minlength="8" required>
                    <button type="button" class="toggle-password" onclick="togglePassword('password_confirm')">
                        <svg class="eye-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                            <path fill-rule="evenodd"
                                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                <p class="form-hint" id="matchHint"></p>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                <svg viewBox="0 0 20 20" fill="currentColor" width="18" height="18">
                    <path fill-rule="evenodd"
                        d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                        clip-rule="evenodd" />
                </svg>
                Réinitialiser le mot de passe
            </button>
        </form>

        <div class="auth-footer">
            <a href="<?= base_url('login') ?>">← Retour à la connexion</a>
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
        max-width: 420px;
        padding: 2rem;
    }

    .auth-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .auth-logo {
        margin-bottom: 1.5rem;
    }

    .auth-logo a {
        display: inline-block;
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

    .auth-form .form-group {
        margin-bottom: 1.25rem;
    }

    .auth-form label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #1e293b;
    }

    .password-input-wrapper {
        position: relative;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 3rem 0.75rem 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        font-size: 1rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    }

    .toggle-password {
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.25rem;
        color: #94a3b8;
        transition: color 0.2s;
    }

    .toggle-password:hover {
        color: #64748b;
    }

    .toggle-password svg {
        width: 20px;
        height: 20px;
    }

    .password-strength {
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .strength-bar {
        flex: 1;
        height: 4px;
        background: #e2e8f0;
        border-radius: 2px;
        overflow: hidden;
        position: relative;
    }

    .strength-bar::after {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 0%;
        transition: width 0.3s, background 0.3s;
    }

    .strength-weak .strength-bar::after {
        width: 33%;
        background: #ef4444;
    }

    .strength-medium .strength-bar::after {
        width: 66%;
        background: #f59e0b;
    }

    .strength-strong .strength-bar::after {
        width: 100%;
        background: #10b981;
    }

    .strength-text {
        font-size: 0.75rem;
        color: #64748b;
    }

    .form-hint {
        margin-top: 0.5rem;
        font-size: 0.75rem;
    }

    .form-hint.match {
        color: #10b981;
    }

    .form-hint.no-match {
        color: #ef4444;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.875rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.35);
    }

    .btn-block {
        width: 100%;
    }

    .auth-footer {
        margin-top: 1.5rem;
        text-align: center;
    }

    .auth-footer a {
        color: #64748b;
        text-decoration: none;
        font-size: 0.875rem;
        transition: color 0.2s;
    }

    .auth-footer a:hover {
        color: #3b82f6;
    }

    .alert {
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .alert-danger {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.2);
        color: #dc2626;
    }

    .alert ul {
        margin: 0;
        padding-left: 1.25rem;
    }
</style>

<script>
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        input.type = input.type === 'password' ? 'text' : 'password';
    }

    document.getElementById('password').addEventListener('input', function () {
        const password = this.value;
        const strengthDiv = document.getElementById('passwordStrength');
        const strengthText = strengthDiv.querySelector('.strength-text');

        // Reset classes
        strengthDiv.classList.remove('strength-weak', 'strength-medium', 'strength-strong');

        if (password.length === 0) {
            strengthText.textContent = '';
            return;
        }

        // Calculate strength
        let strength = 0;
        if (password.length >= 8) strength++;
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
        if (/\d/.test(password)) strength++;
        if (/[^a-zA-Z0-9]/.test(password)) strength++;

        if (strength <= 1) {
            strengthDiv.classList.add('strength-weak');
            strengthText.textContent = 'Faible';
        } else if (strength <= 2) {
            strengthDiv.classList.add('strength-medium');
            strengthText.textContent = 'Moyen';
        } else {
            strengthDiv.classList.add('strength-strong');
            strengthText.textContent = 'Fort';
        }

        // Check match
        checkPasswordMatch();
    });

    document.getElementById('password_confirm').addEventListener('input', checkPasswordMatch);

    function checkPasswordMatch() {
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('password_confirm').value;
        const hint = document.getElementById('matchHint');

        if (confirm.length === 0) {
            hint.textContent = '';
            hint.className = 'form-hint';
            return;
        }

        if (password === confirm) {
            hint.textContent = '✓ Les mots de passe correspondent';
            hint.className = 'form-hint match';
        } else {
            hint.textContent = '✗ Les mots de passe ne correspondent pas';
            hint.className = 'form-hint no-match';
        }
    }
</script>
<?= $this->endSection() ?>