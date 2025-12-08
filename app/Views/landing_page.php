<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
    <section class="hero">
        <div class="hero-content">
            <h1>Gérez votre entreprise en toute simplicité</h1>
            <p>Facturation, devis, clients et trésorerie. Tout ce dont vous avez besoin pour piloter votre activité, au même endroit.</p>
            <div class="hero-buttons">
                <a href="<?= base_url('register') ?>" class="btn btn-primary">Commencer gratuitement</a>
                <a href="#demo" class="btn btn-outline">Voir la démo</a>
            </div>
        </div>
        <div class="hero-image">
            <img src="<?= base_url('assets/images/screenshots/dashboard.png') ?>" alt="Tableau de bord Pilom" style="box-shadow: 0 20px 40px rgba(0,0,0,0.1); border-radius: 10px;">
        </div>
    </section>

    <section id="features" class="features">
        <h2>Tout pour réussir</h2>
        <p>Des outils puissants conçus pour les entrepreneurs modernes</p>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📄</div>
                <h3>Facturation Rapide</h3>
                <p>Créez des factures professionnelles en quelques clics et faites-vous payer plus vite.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👥</div>
                <h3>Gestion Clients</h3>
                <p>Centralisez toutes les informations de vos clients et suivez vos échanges.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Tableau de Bord</h3>
                <p>Visualisez votre chiffre d'affaires et votre trésorerie en temps réel.</p>
            </div>
        </div>
    </section>
<?= $this->endSection() ?>
