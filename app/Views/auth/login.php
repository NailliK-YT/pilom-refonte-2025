<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<div class="auth-container">
    <h2 style="text-align: center; margin-bottom: 30px;">Connexion</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <form action="<?= base_url('login') ?>" method="post" id="login-form">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" required value="<?= old('email') ?>">
        </div>

        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%;" id="login-btn">
            <span class="btn-text">Se connecter</span>
            <span class="btn-loader" style="display: none;">
                <svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                    style="animation: spin 1s linear infinite;">
                    <path d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z"
                        opacity=".25" />
                    <path
                        d="M12,4a8,8,0,0,1,7.89,6.7A1.53,1.53,0,0,0,21.38,12h0a1.5,1.5,0,0,0,1.48-1.75,11,11,0,0,0-21.72,0A1.5,1.5,0,0,0,2.62,12h0a1.53,1.53,0,0,0,1.49-1.3A8,8,0,0,1,12,4Z" />
                </svg>
                Vérification...
            </span>
        </button>
    </form>

    <p style="text-align: center; margin-top: 20px;">
        Pas encore de compte ? <a href="<?= base_url('register') ?>" style="color: var(--primary-color);">S'inscrire</a>
    </p>
</div>

<style>
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .btn-loader {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
</style>

<script>
    document.getElementById('login-form').addEventListener('submit', function () {
        const btn = document.getElementById('login-btn');
        const btnText = btn.querySelector('.btn-text');
        const btnLoader = btn.querySelector('.btn-loader');

        btnText.style.display = 'none';
        btnLoader.style.display = 'inline-flex';
        btn.disabled = true;
    });
</script>
<?= $this->endSection() ?>