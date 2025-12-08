<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - Pilom</title>
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/profile.css') ?>">
</head>

<body>
    <div class="container">
        <div class="profile-container">
            <!-- Navigation Tabs -->
            <div class="profile-nav">
                <a href="<?= base_url('profile') ?>"
                    class="<?= uri_string() == 'profile' || uri_string() == 'profile/' ? 'active' : '' ?>">
                    Profil
                </a>
                <a href="<?= base_url('profile/password') ?>"
                    class="<?= uri_string() == 'profile/password' ? 'active' : '' ?>">
                    Mot de passe
                </a>
                <a href="<?= base_url('account/security') ?>"
                    class="<?= strpos(uri_string(), 'account') !== false ? 'active' : '' ?>">
                    Sécurité
                </a>
                <a href="<?= base_url('notifications/preferences') ?>"
                    class="<?= strpos(uri_string(), 'notifications') !== false ? 'active' : '' ?>">
                    Notifications
                </a>
            </div>

            <!-- Content Area -->
            <div class="profile-content">
                <?php if (session()->has('success')): ?>
                    <div class="alert alert-success">
                        <?= session('success') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->has('error')): ?>
                    <div class="alert alert-danger">
                        <?= session('error') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->has('errors')): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach (session('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?= $this->renderSection('content') ?>
            </div>
        </div>
    </div>

    <script src="<?= base_url('js/profile.js') ?>"></script>
</body>

</html>