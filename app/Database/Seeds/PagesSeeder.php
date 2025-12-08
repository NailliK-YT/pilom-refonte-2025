<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PagesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Fonctionnalités
            [
                'slug' => 'fonctionnalites/facturation',
                'title' => 'Logiciel de Facturation',
                'content' => '
                    <div class="page-header">
                        <h1>Facturation simplifiée</h1>
                        <p class="page-subtitle">Créez des factures professionnelles en quelques clics et faites-vous payer plus rapidement.</p>
                    </div>
                    <div class="page-content about-content">
                        <div class="about-section">
                            <img src="/assets/images/screenshots/factures.png" alt="Interface de facturation Pilom" style="width: 100%; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin-bottom: 40px;">
                            <h2>Gagnez du temps sur votre facturation</h2>
                            <p>Fini Excel et Word. Avec Pilom, créez des factures conformes et élégantes en moins de 2 minutes. Notre éditeur intuitif vous permet de personnaliser vos documents à votre image, d\'ajouter votre logo et de définir vos conditions de paiement.</p>
                        </div>
                        <div class="values-grid">
                            <div class="value-card">
                                <div class="value-icon">⚡</div>
                                <h3>Rapide</h3>
                                <p>Transformation de devis en facture en un clic. Duplication de factures existantes pour gagner du temps.</p>
                            </div>
                            <div class="value-card">
                                <div class="value-icon">🎨</div>
                                <h3>Personnalisable</h3>
                                <p>Ajoutez votre logo, choisissez vos couleurs et adaptez la mise en page à votre image de marque.</p>
                            </div>
                            <div class="value-card">
                                <div class="value-icon">✅</div>
                                <h3>Conforme</h3>
                                <p>Mentions légales obligatoires automatiques, calcul de la TVA, numérotation séquentielle respectée.</p>
                            </div>
                        </div>
                    </div>',
                'meta_title' => 'Logiciel de Facturation en ligne - Pilom',
                'meta_description' => 'Découvrez notre module de facturation complet et intuitif.',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'slug' => 'fonctionnalites/devis',
                'title' => 'Gestion des Devis',
                'content' => '
                    <div class="page-header">
                        <h1>Devis professionnels</h1>
                        <p class="page-subtitle">Convainquez vos prospects avec des devis clairs et professionnels.</p>
                    </div>
                    <div class="page-content about-content">
                        <div class="about-section">
                            <img src="/assets/images/screenshots/devis.png" alt="Interface de devis Pilom" style="width: 100%; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin-bottom: 40px;">
                            <h2>Signez plus de contrats</h2>
                            <p>Envoyez des devis qui font la différence. Suivez leur statut en temps réel (envoyé, accepté, refusé) et relancez au bon moment. Une fois accepté, transformez votre devis en facture en un seul clic.</p>
                        </div>
                        <div class="values-grid">
                            <div class="value-card">
                                <div class="value-icon">🔄</div>
                                <h3>Conversion facile</h3>
                                <p>Transformez vos devis en factures sans ressaisie, évitant ainsi les erreurs et gagnant du temps.</p>
                            </div>
                            <div class="value-card">
                                <div class="value-icon">📱</div>
                                <h3>Accessible</h3>
                                <p>Créez vos devis depuis n\'importe où, sur ordinateur ou tablette, chez le client ou au bureau.</p>
                            </div>
                            <div class="value-card">
                                <div class="value-icon">📋</div>
                                <h3>Suivi précis</h3>
                                <p>Sachez exactement où vous en êtes avec chaque prospect grâce aux statuts de devis clairs.</p>
                            </div>
                        </div>
                    </div>',
                'meta_title' => 'Logiciel de Devis en ligne - Pilom',
                'meta_description' => 'Créez des devis clairs et professionnels rapidement.',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'slug' => 'fonctionnalites/contacts',
                'title' => 'Gestion des Contacts',
                'content' => '
                    <div class="page-header">
                        <h1>CRM intégré</h1>
                        <p class="page-subtitle">Centralisez toutes les informations de vos clients et fournisseurs.</p>
                    </div>
                    <div class="page-content about-content">
                        <div class="about-section">
                            <img src="/assets/images/screenshots/contacts.png" alt="Gestion des contacts Pilom" style="width: 100%; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin-bottom: 40px;">
                            <h2>Connaissez mieux vos clients</h2>
                            <p>Accédez à l\'historique complet de chaque client : devis, factures, paiements, notes. Ne perdez plus aucune information importante et offrez un service personnalisé à vos clients.</p>
                        </div>
                        <div class="values-grid">
                            <div class="value-card">
                                <div class="value-icon">📇</div>
                                <h3>Centralisation</h3>
                                <p>Toutes les coordonnées, historiques et documents liés à un client au même endroit.</p>
                            </div>
                            <div class="value-card">
                                <div class="value-icon">🔍</div>
                                <h3>Recherche rapide</h3>
                                <p>Retrouvez n\'importe quel client ou fournisseur en quelques secondes.</p>
                            </div>
                        </div>
                    </div>',
                'meta_title' => 'Gestion des Contacts CRM - Pilom',
                'meta_description' => 'Centralisez vos contacts clients et fournisseurs.',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'slug' => 'fonctionnalites/depenses',
                'title' => 'Suivi des Dépenses',
                'content' => '
                    <div class="page-header">
                        <h1>Gestion des dépenses</h1>
                        <p class="page-subtitle">Gardez le contrôle sur vos coûts et optimisez votre rentabilité.</p>
                    </div>
                    <div class="page-content about-content">
                        <div class="about-section">
                            <img src="/assets/images/screenshots/depenses.png" alt="Suivi des dépenses Pilom" style="width: 100%; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin-bottom: 40px;">
                            <h2>Ne perdez plus vos justificatifs</h2>
                            <p>Enregistrez vos achats et notes de frais simplement. Visualisez la répartition de vos dépenses par catégorie pour mieux gérer votre budget et identifier les postes d\'économie.</p>
                        </div>
                        <div class="values-grid">
                            <div class="value-card">
                                <div class="value-icon">📊</div>
                                <h3>Catégorisation</h3>
                                <p>Classez vos dépenses pour une comptabilité claire et des analyses pertinentes.</p>
                            </div>
                            <div class="value-card">
                                <div class="value-icon">📎</div>
                                <h3>Justificatifs</h3>
                                <p>Attachez vos factures et reçus directement à chaque dépense pour ne rien perdre.</p>
                            </div>
                        </div>
                    </div>',
                'meta_title' => 'Logiciel de Gestion des Dépenses - Pilom',
                'meta_description' => 'Gardez un œil sur vos dépenses et optimisez votre budget.',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'slug' => 'fonctionnalites/tresorerie',
                'title' => 'Trésorerie',
                'content' => '
                    <div class="page-header">
                        <h1>Suivi de Trésorerie</h1>
                        <p class="page-subtitle">Pilotez votre activité avec une vision claire de vos finances.</p>
                    </div>
                    <div class="page-content about-content">
                        <div class="about-section">
                            <img src="/assets/images/screenshots/dashboard.png" alt="Tableau de bord trésorerie Pilom" style="width: 100%; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin-bottom: 40px;">
                            <h2>Anticipez l\'avenir</h2>
                            <p>Suivez vos encaissements et décaissements en temps réel. Le tableau de bord vous donne une vision synthétique de la santé financière de votre entreprise, vous permettant de prendre les bonnes décisions au bon moment.</p>
                        </div>
                        <div class="values-grid">
                            <div class="value-card">
                                <div class="value-icon">📈</div>
                                <h3>Temps réel</h3>
                                <p>Votre solde de trésorerie mis à jour instantanément à chaque facture payée ou dépense réglée.</p>
                            </div>
                            <div class="value-card">
                                <div class="value-icon">👁️</div>
                                <h3>Visibilité</h3>
                                <p>Comprenez d\'où vient votre argent et où il va grâce aux graphiques intuitifs.</p>
                            </div>
                        </div>
                    </div>',
                'meta_title' => 'Logiciel de Trésorerie - Pilom',
                'meta_description' => 'Visualisez votre trésorerie en temps réel.',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            // Profils
            [
                'slug' => 'pour/artisan',
                'title' => 'Pour les Artisans',
                'content' => '
                    <div class="page-header">
                        <h1>Solution pour Artisans</h1>
                        <p class="page-subtitle">Passez moins de temps au bureau et plus sur vos chantiers.</p>
                    </div>
                    <div class="page-content about-content">
                        <div class="about-section">
                            <img src="/assets/images/screenshots/dashboard.png" alt="Pilom pour Artisans" style="width: 100%; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin-bottom: 40px;">
                            <h2>Simple et efficace</h2>
                            <p>Pilom est conçu pour les artisans qui veulent aller à l\'essentiel. Faites vos devis sur place, facturez dès la fin des travaux et suivez vos règlements sans prise de tête.</p>
                        </div>
                    </div>',
                'meta_title' => 'Logiciel de Facturation pour Artisan - Pilom',
                'meta_description' => 'La solution idéale pour les artisans du bâtiment et autres.',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'slug' => 'pour/consultant',
                'title' => 'Pour les Consultants',
                'content' => '
                    <div class="page-header">
                        <h1>Solution pour Consultants</h1>
                        <p class="page-subtitle">Valorisez votre expertise avec une gestion administrative impeccable.</p>
                    </div>
                    <div class="page-content about-content">
                        <div class="about-section">
                            <img src="/assets/images/screenshots/factures.png" alt="Pilom pour Consultants" style="width: 100%; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin-bottom: 40px;">
                            <h2>Facturez vos honoraires simplement</h2>
                            <p>Gérez vos missions, suivez vos temps et facturez vos prestations en toute sérénité. Pilom vous donne une image professionnelle auprès de vos clients grands comptes.</p>
                        </div>
                    </div>',
                'meta_title' => 'Logiciel de Facturation pour Consultant - Pilom',
                'meta_description' => 'Gérez vos missions et facturez vos honoraires facilement.',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'slug' => 'pour/freelance',
                'title' => 'Pour les Freelances',
                'content' => '
                    <div class="page-header">
                        <h1>Solution pour Freelances</h1>
                        <p class="page-subtitle">L\'outil tout-en-un pour gérer votre activité d\'indépendant.</p>
                    </div>
                    <div class="page-content about-content">
                        <div class="about-section">
                            <img src="/assets/images/screenshots/dashboard.png" alt="Pilom pour Freelances" style="width: 100%; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin-bottom: 40px;">
                            <h2>Libérez-vous des contraintes</h2>
                            <p>Pilom s\'occupe de la paperasse pour que vous puissiez vous concentrer sur vos clients et vos projets. Suivez votre chiffre d\'affaires et anticipez vos charges.</p>
                        </div>
                    </div>',
                'meta_title' => 'Logiciel de Facturation pour Freelance - Pilom',
                'meta_description' => 'Simplifiez votre vie de freelance avec Pilom.',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'slug' => 'pour/pme',
                'title' => 'Pour les PME',
                'content' => '
                    <div class="page-header">
                        <h1>Solution pour PME</h1>
                        <p class="page-subtitle">Structurez votre gestion commerciale et financière.</p>
                    </div>
                    <div class="page-content about-content">
                        <div class="about-section">
                            <img src="/assets/images/screenshots/dashboard.png" alt="Pilom pour PME" style="width: 100%; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin-bottom: 40px;">
                            <h2>Une vision à 360°</h2>
                            <p>Donnez à votre équipe les outils pour collaborer efficacement. Suivez la performance de votre entreprise avec des indicateurs précis et prenez des décisions éclairées.</p>
                        </div>
                    </div>',
                'meta_title' => 'Logiciel de Gestion pour PME - Pilom',
                'meta_description' => 'Une suite complète pour gérer votre PME.',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'slug' => 'pour/auto-entrepreneur',
                'title' => 'Pour Auto-entrepreneurs',
                'content' => '
                    <div class="page-header">
                        <h1>Solution Auto-entrepreneur</h1>
                        <p class="page-subtitle">Respectez vos obligations légales en toute simplicité.</p>
                    </div>
                    <div class="page-content about-content">
                        <div class="about-section">
                            <img src="/assets/images/screenshots/factures.png" alt="Pilom pour Auto-entrepreneurs" style="width: 100%; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin-bottom: 40px;">
                            <h2>Facturation conforme</h2>
                            <p>Des factures avec la mention "TVA non applicable" automatique. Suivez votre chiffre d\'affaires pour ne pas dépasser les seuils de la micro-entreprise.</p>
                        </div>
                    </div>',
                'meta_title' => 'Logiciel Facturation Auto-entrepreneur - Pilom',
                'meta_description' => 'Facturation conforme pour micro-entreprises.',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'slug' => 'pour/profession-liberale',
                'title' => 'Pour Professions Libérales',
                'content' => '
                    <div class="page-header">
                        <h1>Solution Profession Libérale</h1>
                        <p class="page-subtitle">Gérez votre activité en toute sérénité.</p>
                    </div>
                    <div class="page-content about-content">
                        <div class="about-section">
                            <img src="/assets/images/screenshots/dashboard.png" alt="Pilom pour Professions Libérales" style="width: 100%; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin-bottom: 40px;">
                            <h2>Gestion simplifiée</h2>
                            <p>Facturez vos patients ou clients facilement. Suivez vos recettes et dépenses pour votre comptabilité et gagnez du temps administratif.</p>
                        </div>
                    </div>',
                'meta_title' => 'Logiciel pour Profession Libérale - Pilom',
                'meta_description' => 'Adapté aux besoins des professions libérales.',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            // Pages Institutionnelles
            [
                'slug' => 'about',
                'title' => 'À propos',
                'content' => '
                    <div class="page-header">
                        <h1>À propos de Pilom</h1>
                        <p class="page-subtitle">Notre mission est de simplifier la vie des entrepreneurs.</p>
                    </div>
                    <div class="page-content about-content">
                        <div class="about-section">
                            <h2>Notre Histoire</h2>
                            <p>Pilom est né d\'un constat simple : les entrepreneurs perdent trop de temps avec l\'administratif. Nous avons voulu créer un outil simple, beau et efficace pour leur redonner ce temps précieux.</p>
                        </div>
                        <div class="values-grid">
                            <div class="value-card">
                                <div class="value-icon">❤️</div>
                                <h3>Simplicité</h3>
                                <p>Nous croyons que la gestion ne devrait pas être compliquée.</p>
                            </div>
                            <div class="value-card">
                                <div class="value-icon">🛡️</div>
                                <h3>Fiabilité</h3>
                                <p>Vos données sont précieuses, nous les protégeons.</p>
                            </div>
                            <div class="value-card">
                                <div class="value-icon">🚀</div>
                                <h3>Innovation</h3>
                                <p>Nous améliorons Pilom chaque jour pour vous.</p>
                            </div>
                        </div>
                    </div>',
                'meta_title' => 'À propos de Pilom - Notre Mission',
                'meta_description' => 'Découvrez l\'équipe et la mission derrière Pilom.',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'slug' => 'faq',
                'title' => 'Foire Aux Questions',
                'content' => '
                    <div class="page-header">
                        <h1>Foire Aux Questions</h1>
                        <p class="page-subtitle">Retrouvez les réponses à vos questions les plus fréquentes.</p>
                    </div>
                    <div class="page-content faq-content">
                        <div class="faq-list">
                            <div class="faq-item">
                                <button class="faq-question">
                                    Est-ce que Pilom est gratuit ?
                                    <span class="faq-icon">▼</span>
                                </button>
                                <div class="faq-answer">
                                    <p>Nous proposons une période d\'essai gratuite de 14 jours. Ensuite, vous pouvez choisir parmi nos abonnements adaptés à votre taille.</p>
                                </div>
                            </div>
                            <div class="faq-item">
                                <button class="faq-question">
                                    Mes données sont-elles sécurisées ?
                                    <span class="faq-icon">▼</span>
                                </button>
                                <div class="faq-answer">
                                    <p>Oui, absolument. Toutes vos données sont chiffrées et stockées sur des serveurs sécurisés en France.</p>
                                </div>
                            </div>
                            <div class="faq-item">
                                <button class="faq-question">
                                    Puis-je exporter mes données ?
                                    <span class="faq-icon">▼</span>
                                </button>
                                <div class="faq-answer">
                                    <p>Oui, vous pouvez exporter vos factures, devis et contacts à tout moment au format CSV ou PDF.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        document.querySelectorAll(".faq-question").forEach(button => {
                            button.addEventListener("click", () => {
                                const item = button.parentElement;
                                const answer = button.nextElementSibling;
                                item.classList.toggle("active");
                                if (item.classList.contains("active")) {
                                    answer.style.maxHeight = answer.scrollHeight + "px";
                                } else {
                                    answer.style.maxHeight = 0;
                                }
                            });
                        });
                    </script>',
                'meta_title' => 'FAQ - Questions Fréquentes Pilom',
                'meta_description' => 'Toutes les réponses sur l\'utilisation de Pilom.',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'slug' => 'mentions-legales',
                'title' => 'Mentions Légales',
                'content' => '
                    <div class="page-header">
                        <h1>Mentions Légales</h1>
                    </div>
                    <div class="page-content legal-content">
                        <div class="legal-section">
                            <h2>Éditeur du site</h2>
                            <p>Le site Pilom est édité par la société Pilom SAS, au capital de 10 000 euros.</p>
                            <p>Siège social : 123 Avenue de la République, 75011 Paris</p>
                            <p>RCS Paris B 123 456 789</p>
                        </div>
                        <div class="legal-section">
                            <h2>Hébergement</h2>
                            <p>Le site est hébergé par OVH SAS, 2 rue Kellermann - 59100 Roubaix - France.</p>
                        </div>
                    </div>',
                'meta_title' => 'Mentions Légales - Pilom',
                'meta_description' => 'Mentions légales et informations juridiques.',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'slug' => 'cgu',
                'title' => 'Conditions Générales d\'Utilisation',
                'content' => '
                    <div class="page-header">
                        <h1>Conditions Générales d\'Utilisation</h1>
                    </div>
                    <div class="page-content legal-content">
                        <div class="legal-section">
                            <h2>1. Objet</h2>
                            <p>Les présentes CGU ont pour objet de définir les modalités de mise à disposition des services du site Pilom.</p>
                        </div>
                        <div class="legal-section">
                            <h2>2. Accès au service</h2>
                            <p>Le service est accessible gratuitement à tout utilisateur disposant d\'un accès à internet. Tous les coûts afférents à l\'accès au service sont à la charge de l\'utilisateur.</p>
                        </div>
                    </div>',
                'meta_title' => 'CGU - Conditions Générales d\'Utilisation - Pilom',
                'meta_description' => 'Consultez nos conditions générales d\'utilisation.',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'slug' => 'confidentialite',
                'title' => 'Politique de Confidentialité',
                'content' => '
                    <div class="page-header">
                        <h1>Politique de Confidentialité</h1>
                    </div>
                    <div class="page-content legal-content">
                        <div class="legal-section">
                            <h2>Collecte des données</h2>
                            <p>Nous collectons les informations que vous nous fournissez lors de votre inscription : nom, prénom, email, nom de l\'entreprise.</p>
                        </div>
                        <div class="legal-section">
                            <h2>Utilisation des données</h2>
                            <p>Vos données sont utilisées pour la gestion de votre compte et l\'accès à nos services. Elles ne sont jamais revendues à des tiers.</p>
                        </div>
                    </div>',
                'meta_title' => 'Politique de Confidentialité - Pilom',
                'meta_description' => 'Notre engagement pour la protection de vos données.',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Add UUIDs
        foreach ($data as &$row) {
            $row['id'] = $this->generateUuid();
        }

        // Using Query Builder
        $this->db->table('pages')->ignore(true)->insertBatch($data);
    }

    private function generateUuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
