<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - PILOM</title>
    
    <!-- Charte graphique : Police Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS dédié à la page de connexion -->
    <link rel="stylesheet" href="<?= base_url('css/login.css') ?>">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <a href="<?= base_url('/') ?>" class="header-logo">
                <svg class="header-logo-icon" width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16 2L2 9V23L16 30L30 23V9L16 2Z" fill="#4E51C0"/>
                    <circle cx="16" cy="16" r="6" fill="white"/>
                </svg>
                <span class="header-logo-text">pilom</span>
            </a>
            <nav class="nav">
                <a href="<?= base_url('/') ?>#features">Fonctionnalités</a>
                <a href="<?= base_url('/') ?>#pricing">Tarifs</a>
                <a href="<?= base_url('/contact') ?>">Contact</a>
            </nav>
            <div class="header-actions">
                <a href="<?= base_url('login') ?>" class="btn-link">Connexion</a>
                <a href="<?= base_url('register') ?>" class="btn-primary">Essayer gratuitement</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="login-container">
        <div class="login-card">
            <!-- Titre principal -->
            <h1 class="login-title">Connexion</h1>
            <p class="login-subtitle">Accédez à votre espace de gestion</p>

            <!-- Messages d'erreur globaux -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-error">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <!-- Message de succès (ex: après inscription) -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= esc(session()->getFlashdata('success')) ?>
                </div>
            <?php endif; ?>

            <!-- Formulaire de connexion -->
            <?= form_open('login/attempt', ['class' => 'login-form']) ?>
                
                <!-- Protection CSRF -->
                <?= csrf_field() ?>

                <!-- Champ Adresse email -->
                <div class="form-group">
                    <?= form_label('Adresse email', 'email', ['class' => 'form-label']) ?>
                    <?= form_input([
                        'name'        => 'email',
                        'id'          => 'email',
                        'type'        => 'email',
                        'class'       => 'form-input' . (isset(session()->getFlashdata('errors')['email']) ? ' error' : ''),
                        'placeholder' => 'votre@entreprise.fr',
                        'value'       => old('email'),
                        'required'    => true,
                    ]) ?>
                    <?php if (isset(session()->getFlashdata('errors')['email'])): ?>
                        <span class="error-message"><?= esc(session()->getFlashdata('errors')['email']) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Champ Mot de passe -->
                <div class="form-group">
                    <div class="label-row">
                        <?= form_label('Mot de passe', 'password', ['class' => 'form-label']) ?>
                        <a href="<?= base_url('/forgot-password') ?>" class="forgot-link">Mot de passe oublié ?</a>
                    </div>
                    <?= form_password([
                        'name'        => 'password',
                        'id'          => 'password',
                        'class'       => 'form-input' . (isset(session()->getFlashdata('errors')['password']) ? ' error' : ''),
                        'placeholder' => 'Entrez votre mot de passe',
                        'required'    => true,
                    ]) ?>
                    <?php if (isset(session()->getFlashdata('errors')['password'])): ?>
                        <span class="error-message"><?= esc(session()->getFlashdata('errors')['password']) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Case à cocher "Se souvenir de moi" -->
                <div class="form-group">
                    <label class="checkbox-container">
                        <?= form_checkbox([
                            'name'  => 'remember',
                            'id'    => 'remember',
                            'value' => '1',
                            'checked' => old('remember'),
                        ]) ?>
                        <span class="checkbox-label">Se souvenir de moi</span>
                    </label>
                </div>

                <!-- Bouton de soumission (noir) -->
                <?= form_submit('submit', 'Se connecter', ['class' => 'btn-submit']) ?>

            <?= form_close() ?>

            <!-- Lien inscription -->
            <div class="register-link">
                Vous n'avez pas encore de compte ? 
                <a href="<?= base_url('/register') ?>">Créer un compte</a>
            </div>
        </div>

        <!-- Footer -->
        <footer class="login-footer">
            <p>
                Besoin d'aide ? Consultez notre 
                <a href="<?= base_url('/help') ?>">centre d'aide</a>
            </p>
            <div class="footer-links">
                <a href="<?= base_url('/privacy') ?>">Confidentialité</a>
                <span>•</span>
                <a href="<?= base_url('/terms') ?>">Conditions</a>
                <span>•</span>
                <a href="<?= base_url('/contact') ?>">Contact</a>
            </div>
        </footer>
    </main>
</body>
</html>
