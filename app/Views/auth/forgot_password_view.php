<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - PILOM</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/auth.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="logo">PILOM</div>
            <nav class="nav">
                <a href="<?= base_url('/') ?>">Fonctionnalités</a>
                <a href="<?= base_url('/tarifs') ?>">Tarifs</a>
                <a href="<?= base_url('/contact') ?>">Contact</a>
            </nav>
            <div class="header-actions">
                <a href="<?= base_url('/login') ?>" class="btn-link">Connexion</a>
                <a href="<?= base_url('/register') ?>" class="btn-primary">Essayer gratuitement</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="login-container">
        <div class="login-card">
            <!-- Logo -->
            <div class="logo-container">
                <div class="logo-badge">PILOM</div>
            </div>

            <!-- Titre -->
            <h1 class="login-title">Mot de passe oublié</h1>
            <p class="login-subtitle">Cette fonctionnalité sera bientôt disponible</p>

            <div class="alert alert-error">
                La réinitialisation de mot de passe n'est pas encore implémentée. Veuillez contacter votre administrateur.
            </div>

            <!-- Lien retour connexion -->
            <div class="register-link" style="border-top: none; padding-top: 0;">
                <a href="<?= base_url('/login') ?>">← Retour à la connexion</a>
            </div>
        </div>
    </main>
</body>
</html>
