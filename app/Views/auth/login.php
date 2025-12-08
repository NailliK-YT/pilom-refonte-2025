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

        <form action="<?= base_url('login') ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" required value="<?= old('email') ?>">
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Se connecter</button>
        </form>

        <p style="text-align: center; margin-top: 20px;">
            Pas encore de compte ? <a href="<?= base_url('register') ?>" style="color: var(--primary-color);">S'inscrire</a>
        </p>
    </div>
<?= $this->endSection() ?>
