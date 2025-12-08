<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
    <div class="auth-container">
        <h2 style="text-align: center; margin-bottom: 30px;">Inscription</h2>

        <?php if (isset($validation)): ?>
            <div class="alert alert-danger">
                <?= $validation->listErrors() ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('register') ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" required value="<?= old('email') ?>">
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" name="password" id="password" class="form-control" required minlength="8">
                <small style="color: #666;">Minimum 8 caractères</small>
            </div>

            <div class="form-group">
                <label for="password_confirm">Confirmer le mot de passe</label>
                <input type="password" name="password_confirm" id="password_confirm" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">S'inscrire</button>
        </form>

        <p style="text-align: center; margin-top: 20px;">
            Déjà un compte ? <a href="<?= base_url('login') ?>" style="color: var(--primary-color);">Se connecter</a>
        </p>
    </div>
<?= $this->endSection() ?>
