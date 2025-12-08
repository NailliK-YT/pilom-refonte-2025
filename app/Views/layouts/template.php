<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $this->renderSection('title', true) ?> - Mon Site</title>

    <!-- Meta tags -->
    <?= $this->renderSection('meta') ?>

    <!-- Styles -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/navigation.css') ?>">
    <?= $this->renderSection('styles') ?>
</head>

<body>
    <!-- Header avec menu -->
    <header class="site-header">
        <div class="container">
            <div class="logo">
                <a href="<?= site_url('/') ?>">Mon Site</a>
            </div>
            <?= $this->include('partials/_menu') ?>
        </div>
    </header>

    <!-- Contenu principal -->
    <main class="site-main">
        <div class="container">
            <?= $this->renderSection('content') ?>
        </div>
    </main>

    <!-- Footer -->
    <?= $this->include('partials/_footer') ?>

    <!-- Scripts -->
    <script src="<?= base_url('assets/js/main.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>