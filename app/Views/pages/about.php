<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>À propos de Pilom</h1>
    <p class="page-subtitle">Une solution née de l'expérience terrain des entrepreneurs</p>
</div>

<div class="about-content">
    <section class="about-section">
        <h2>Notre Mission</h2>
        <p>
            Pilom est né d'un constat simple : les entrepreneurs passent trop de temps sur les tâches administratives 
            au détriment de leur cœur de métier. Notre mission est de simplifier la gestion quotidienne des petites 
            entreprises et des indépendants grâce à des outils intuitifs et puissants.
        </p>
    </section>

    <section class="about-section">
        <h2>Nos Valeurs</h2>
        <div class="values-grid">
            <div class="value-card">
                <div class="value-icon">🎯</div>
                <h3>Simplicité</h3>
                <p>Des outils intuitifs qui vont droit au but, sans complexité inutile.</p>
            </div>
            <div class="value-card">
                <div class="value-icon">🔒</div>
                <h3>Sécurité</h3>
                <p>Vos données sont protégées avec les plus hauts standards de sécurité.</p>
            </div>
            <div class="value-card">
                <div class="value-icon">🤝</div>
                <h3>Proximité</h3>
                <p>Une équipe à l'écoute, réactive et toujours prête à vous aider.</p>
            </div>
            <div class="value-card">
                <div class="value-icon">🚀</div>
                <h3>Innovation</h3>
                <p>Des fonctionnalités en constante évolution pour répondre à vos besoins.</p>
            </div>
        </div>
    </section>

    <section class="about-section">
        <h2>Notre Équipe</h2>
        <p>
            Pilom est porté par une équipe passionnée, composée d'anciens entrepreneurs et de développeurs 
            expérimentés. Nous comprenons vos défis quotidiens car nous les avons vécus.
        </p>
    </section>

    <section class="about-cta">
        <h2>Prêt à simplifier votre gestion ?</h2>
        <p>Essayez Pilom gratuitement pendant 14 jours, sans engagement.</p>
        <a href="<?= base_url('register') ?>" class="btn btn-primary btn-lg">Commencer gratuitement</a>
    </section>
</div>

<?= $this->endSection() ?>
