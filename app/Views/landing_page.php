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

	<!-- Demo Video Section -->
    <section id="demo" class="demo-section">
        <div class="demo-container">

            <div class="demo-text">
                <h2>Découvrez Pilom en action</h2>
                <p class="subtitle">
                    Une démonstration complète de la création d’un devis, d’une facture et du suivi des règlements.
                </p>
            </div>

            <div class="demo-video-wrapper">
                <iframe 
                    class="demo-video"
                    src="https://www.youtube.com/embed/VIDEO_ID?rel=0&modestbranding=1"
                    title="Démonstration Pilom"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
                </iframe>
            </div>

        </div>
    </section>

	<!-- Features Section -->
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

	<!-- Testimonials Section -->
    <section class="testimonials">
        <h2>Ils nous font confiance</h2>
        <p class="subtitle">Découvrez ce que nos utilisateurs pensent de Pilom</p>

        <div class="testimonials-grid">

            <div class="testimonial-card">
                <p class="quote">
                    “Pilom a transformé ma gestion quotidienne. Je gagne au moins 5 heures par semaine sur l’administratif.”
                </p>
                <p class="author">Marie Dubois</p>
                <p class="role">Plombière - MD Plomberie</p>
            </div>

            <div class="testimonial-card">
                <p class="quote">
                    “Simple, efficace et exactement ce dont mon entreprise avait besoin. Le suivi de trésorerie est remarquable.”
                </p>
                <p class="author">Thomas Martin</p>
                <p class="role">Consultant - TM Consulting</p>
            </div>

            <div class="testimonial-card">
                <p class="quote">
                    “Interface intuitive qui ne nécessite aucune formation. Même ma comptable est impressionnée par l’organisation.”
                </p>
                <p class="author">Sophie Laurent</p>
                <p class="role">Gérante - Café du Coin</p>
            </div>

        </div>
    </section>
<?= $this->endSection() ?>
