<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * BlogSeeder
 * 
 * Seeds the blog with sample articles, tags, and comments
 */
class BlogSeeder extends Seeder
{
    public function run()
    {
        // Get existing categories
        $categories = $this->db->table('blog_categories')->get()->getResultArray();
        $categoryIds = array_column($categories, 'id');

        if (empty($categoryIds)) {
            echo "No categories found. Run migrations first.\n";
            return;
        }

        // Get first user as author
        $author = $this->db->table('users')->limit(1)->get()->getRowArray();
        $authorId = $author['id'] ?? null;

        if (!$authorId) {
            echo "No user found. Create a user first.\n";
            return;
        }

        // Create tags
        $tags = $this->createTags();

        // Create articles
        $articles = $this->createArticles($authorId, $categoryIds, $tags);

        // Create sample comments
        $this->createComments($articles);

        echo "Blog seeded successfully!\n";
        echo "- " . count($tags) . " tags created\n";
        echo "- " . count($articles) . " articles created\n";
    }

    private function generateUUID(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    private function createTags(): array
    {
        $tagNames = [
            'productivité',
            'comptabilité',
            'marketing',
            'juridique',
            'fiscalité',
            'organisation',
            'clients',
            'devis',
            'factures',
            'trésorerie',
            'digital',
            'croissance'
        ];

        $tags = [];
        foreach ($tagNames as $name) {
            $id = $this->generateUUID();
            $slug = strtolower(str_replace(['é', 'è', 'ê'], 'e', $name));

            $this->db->table('blog_tags')->insert([
                'id' => $id,
                'name' => $name,
                'slug' => $slug,
            ]);

            $tags[$name] = $id;
        }

        return $tags;
    }

    private function createArticles(string $authorId, array $categoryIds, array $tags): array
    {
        $articles = [
            [
                'title' => 'Comment bien gérer sa trésorerie en tant qu\'indépendant',
                'slug' => 'gerer-tresorerie-independant',
                'excerpt' => 'Découvrez les meilleures pratiques pour maintenir une trésorerie saine et anticiper vos besoins financiers.',
                'content' => $this->getArticleContent1(),
                'meta_title' => 'Gérer sa trésorerie en indépendant : guide complet',
                'meta_description' => 'Apprenez à gérer efficacement votre trésorerie d\'indépendant. Conseils pratiques, outils et méthodes pour une gestion financière sereine.',
                'tags' => ['trésorerie', 'comptabilité', 'organisation'],
                'category_index' => 0,
                'is_featured' => true,
            ],
            [
                'title' => '10 conseils pour créer des devis qui convertissent',
                'slug' => '10-conseils-devis-convertissent',
                'excerpt' => 'Un devis bien fait est la première étape vers une relation client réussie. Voici comment optimiser les vôtres.',
                'content' => $this->getArticleContent2(),
                'meta_title' => '10 conseils pour des devis qui convertissent | Pilom',
                'meta_description' => 'Améliorez votre taux de conversion avec nos 10 conseils pour créer des devis professionnels et convaincants.',
                'tags' => ['devis', 'clients', 'marketing'],
                'category_index' => 1,
                'is_featured' => true,
            ],
            [
                'title' => 'Les obligations légales de facturation en France',
                'slug' => 'obligations-legales-facturation-france',
                'excerpt' => 'Tout ce que vous devez savoir sur les mentions obligatoires et les règles de facturation pour les professionnels.',
                'content' => $this->getArticleContent3(),
                'meta_title' => 'Facturation : les obligations légales en France',
                'meta_description' => 'Guide complet des obligations légales de facturation en France. Mentions obligatoires, délais, pénalités.',
                'tags' => ['factures', 'juridique', 'fiscalité'],
                'category_index' => 1,
                'is_featured' => false,
            ],
            [
                'title' => 'Digitaliser son activité : par où commencer ?',
                'slug' => 'digitaliser-activite-par-ou-commencer',
                'excerpt' => 'Le numérique peut transformer votre activité. Découvrez les étapes clés pour une transition réussie.',
                'content' => $this->getArticleContent4(),
                'meta_title' => 'Digitalisation : guide pour les indépendants',
                'meta_description' => 'Comment digitaliser votre activité d\'indépendant ? Les outils essentiels et les étapes pour une transition numérique réussie.',
                'tags' => ['digital', 'productivité', 'croissance'],
                'category_index' => 2,
                'is_featured' => true,
            ],
            [
                'title' => 'Comment fidéliser ses clients : stratégies gagnantes',
                'slug' => 'fideliser-clients-strategies-gagnantes',
                'excerpt' => 'Fidéliser coûte moins cher que conquérir. Voici les stratégies qui fonctionnent pour les indépendants.',
                'content' => $this->getArticleContent5(),
                'meta_title' => 'Fidélisation client : stratégies pour indépendants',
                'meta_description' => 'Découvrez les meilleures stratégies de fidélisation client pour les indépendants et les petites entreprises.',
                'tags' => ['clients', 'marketing', 'croissance'],
                'category_index' => 2,
                'is_featured' => false,
            ],
        ];

        $createdArticles = [];
        $publishDate = strtotime('-30 days');

        foreach ($articles as $index => $articleData) {
            $id = $this->generateUUID();
            $wordCount = str_word_count(strip_tags($articleData['content']));
            $readingTime = max(1, ceil($wordCount / 200));

            // Progressive publish dates
            $currentPublishDate = date('Y-m-d H:i:s', strtotime('+' . ($index * 5) . ' days', $publishDate));

            $article = [
                'id' => $id,
                'author_id' => $authorId,
                'title' => $articleData['title'],
                'slug' => $articleData['slug'],
                'excerpt' => $articleData['excerpt'],
                'content' => $articleData['content'],
                'status' => 'published',
                'published_at' => $currentPublishDate,
                'meta_title' => $articleData['meta_title'],
                'meta_description' => $articleData['meta_description'],
                'allow_comments' => true,
                'is_featured' => $articleData['is_featured'],
                'reading_time' => $readingTime,
                'word_count' => $wordCount,
                'view_count' => rand(50, 500),
                'created_at' => $currentPublishDate,
                'updated_at' => $currentPublishDate,
            ];

            $this->db->table('blog_articles')->insert($article);

            // Link to category
            $this->db->table('blog_articles_categories')->insert([
                'article_id' => $id,
                'category_id' => $categoryIds[$articleData['category_index'] % count($categoryIds)],
            ]);

            // Link to tags
            foreach ($articleData['tags'] as $tagName) {
                if (isset($tags[$tagName])) {
                    $this->db->table('blog_articles_tags')->insert([
                        'article_id' => $id,
                        'tag_id' => $tags[$tagName],
                    ]);
                }
            }

            $createdArticles[] = $id;
        }

        return $createdArticles;
    }

    private function createComments(array $articleIds): void
    {
        $comments = [
            [
                'author_name' => 'Marie Dupont',
                'author_email' => 'marie@example.com',
                'content' => 'Super article ! Très utile pour ma situation d\'auto-entrepreneur. Merci pour ces conseils pratiques.',
            ],
            [
                'author_name' => 'Jean Martin',
                'author_email' => 'jean.martin@example.com',
                'content' => 'Je cherchais justement ce type d\'informations. Très bien expliqué et facile à appliquer.',
            ],
            [
                'author_name' => 'Sophie Leroy',
                'author_email' => 'sophie.leroy@example.com',
                'content' => 'Excellent contenu ! J\'ai partagé sur mes réseaux. Continuez comme ça.',
            ],
        ];

        foreach ($articleIds as $index => $articleId) {
            // Add 1-2 comments per article
            $commentCount = ($index % 2) + 1;

            for ($i = 0; $i < $commentCount; $i++) {
                $comment = $comments[$i % count($comments)];

                $this->db->table('blog_comments')->insert([
                    'id' => $this->generateUUID(),
                    'article_id' => $articleId,
                    'author_name' => $comment['author_name'],
                    'author_email' => $comment['author_email'],
                    'content' => $comment['content'],
                    'status' => 'approved',
                    'ip_address' => '127.0.0.1',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 10) . ' days')),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    private function getArticleContent1(): string
    {
        return '<h2>L\'importance d\'une bonne gestion de trésorerie</h2>
<p>En tant qu\'indépendant, votre trésorerie est le nerf de la guerre. Une gestion rigoureuse vous permet d\'anticiper les périodes difficiles et de saisir les opportunités quand elles se présentent.</p>

<h3>1. Établissez un prévisionnel de trésorerie</h3>
<p>Le prévisionnel de trésorerie est votre meilleur allié. Il vous permet de visualiser vos entrées et sorties d\'argent sur plusieurs mois et d\'anticiper les creux.</p>

<h3>2. Séparez vos comptes personnels et professionnels</h3>
<p>C\'est la base d\'une gestion saine. Avoir un compte dédié à votre activité vous permet de mieux suivre vos flux financiers.</p>

<h3>3. Constituez une réserve de sécurité</h3>
<p>Idéalement, gardez l\'équivalent de 3 mois de charges fixes en réserve. Cette épargne de précaution vous protège en cas d\'imprévu.</p>

<h3>4. Facturez rapidement</h3>
<p>N\'attendez pas pour envoyer vos factures. Plus vite vous facturez, plus vite vous êtes payé. Utilisez un outil comme Pilom pour automatiser ce processus.</p>

<blockquote>
<p>« La trésorerie, c\'est comme l\'oxygène : on n\'y pense pas quand on en a, mais c\'est vital quand on en manque. »</p>
</blockquote>

<h3>5. Négociez les délais de paiement</h3>
<p>Avec vos fournisseurs, essayez d\'obtenir des délais de paiement plus longs. Avec vos clients, raccourcissez les délais au maximum.</p>

<p>En suivant ces conseils, vous poserez les bases d\'une gestion financière solide qui vous permettra de vous concentrer sur votre cœur de métier.</p>';
    }

    private function getArticleContent2(): string
    {
        return '<h2>Le devis : votre premier outil de vente</h2>
<p>Un devis n\'est pas qu\'un document administratif. C\'est votre premier argument commercial. Voici comment le rendre irrésistible.</p>

<h3>1. Personnalisez chaque devis</h3>
<p>Évitez les devis génériques. Montrez à votre prospect que vous avez compris ses besoins spécifiques.</p>

<h3>2. Structurez clairement vos prestations</h3>
<p>Découpez votre offre en blocs distincts. Cela permet au client de comprendre la valeur de chaque élément.</p>

<h3>3. Mettez en avant les bénéfices</h3>
<p>Ne vous contentez pas de lister des services. Expliquez ce que le client va gagner : temps, argent, tranquillité d\'esprit.</p>

<h3>4. Proposez des options</h3>
<p>Offrez plusieurs formules : basique, standard, premium. Le client choisit et se sent en contrôle.</p>

<h3>5. Fixez une date de validité</h3>
<p>Une offre limitée dans le temps incite à la décision. 30 jours est un délai raisonnable.</p>

<h3>6. Soignez la présentation</h3>
<p>Un devis professionnel inspirent confiance. Utilisez votre logo, une mise en page aérée et des couleurs cohérentes.</p>

<h3>7. Incluez des témoignages</h3>
<p>Si possible, ajoutez un ou deux avis de clients satisfaits. La preuve sociale est un puissant levier.</p>

<h3>8. Détaillez les conditions</h3>
<p>Soyez transparent sur les délais, les modalités de paiement et les conditions d\'annulation.</p>

<h3>9. Facilitez la signature</h3>
<p>Proposez la signature électronique pour accélérer le processus.</p>

<h3>10. Faites le suivi</h3>
<p>N\'attendez pas passivement. Relancez poliment 3-4 jours après l\'envoi pour répondre aux questions.</p>';
    }

    private function getArticleContent3(): string
    {
        return '<h2>Tout savoir sur la facturation conforme</h2>
<p>En France, la facture est un document commercial et fiscal encadré par la loi. Voici les règles essentielles à respecter.</p>

<h3>Les mentions obligatoires</h3>
<p>Chaque facture doit comporter :</p>
<ul>
<li>La date d\'émission</li>
<li>Un numéro unique et chronologique</li>
<li>L\'identité du vendeur (nom, adresse, SIRET)</li>
<li>L\'identité de l\'acheteur</li>
<li>La désignation précise des produits ou services</li>
<li>La quantité et le prix unitaire HT</li>
<li>Le taux de TVA applicable</li>
<li>Le montant total HT et TTC</li>
<li>La date de paiement et les pénalités de retard</li>
</ul>

<h3>Les délais de paiement</h3>
<p>Le délai de paiement par défaut est de 30 jours. Il peut être négocié jusqu\'à 60 jours maximum (ou 45 jours fin de mois).</p>

<h3>La conservation des factures</h3>
<p>Vous devez conserver vos factures pendant 10 ans. La version numérique est acceptée si elle est sécurisée.</p>

<h3>Les pénalités de retard</h3>
<p>En cas de retard de paiement, vous pouvez appliquer :</p>
<ul>
<li>Des pénalités calculées sur le taux BCE + 10 points</li>
<li>Une indemnité forfaitaire de 40 € pour frais de recouvrement</li>
</ul>

<p>Avec un outil comme Pilom, toutes ces mentions sont automatiquement intégrées à vos factures, vous garantissant une conformité totale.</p>';
    }

    private function getArticleContent4(): string
    {
        return '<h2>La transformation numérique à la portée de tous</h2>
<p>Digitaliser son activité peut sembler intimidant, mais c\'est accessible à tous. Voici un guide étape par étape.</p>

<h3>Étape 1 : Évaluez vos besoins</h3>
<p>Quelles tâches vous prennent le plus de temps ? Où perdez-vous de l\'efficacité ? Cette analyse guidera vos choix.</p>

<h3>Étape 2 : Choisissez les bons outils</h3>
<p>Commencez par les essentiels :</p>
<ul>
<li><strong>Facturation</strong> : un logiciel comme Pilom pour gérer devis et factures</li>
<li><strong>Comptabilité</strong> : pour suivre vos finances en temps réel</li>
<li><strong>Communication</strong> : email professionnel et messagerie instantanée</li>
<li><strong>Stockage</strong> : solution cloud pour vos documents</li>
</ul>

<h3>Étape 3 : Formez-vous</h3>
<p>Prenez le temps de bien maîtriser chaque outil. Des tutoriels gratuits sont disponibles partout.</p>

<h3>Étape 4 : Digitalisez progressivement</h3>
<p>N\'essayez pas de tout changer du jour au lendemain. Procédez par étapes, un processus à la fois.</p>

<h3>Étape 5 : Automatisez</h3>
<p>Une fois à l\'aise, explorez les automatisations : relances automatiques, synchronisation des données, rapports programmés.</p>

<h3>Les bénéfices concrets</h3>
<ul>
<li>Gain de temps sur les tâches administratives</li>
<li>Réduction des erreurs</li>
<li>Meilleure visibilité sur votre activité</li>
<li>Image plus professionnelle</li>
</ul>';
    }

    private function getArticleContent5(): string
    {
        return '<h2>Fidéliser : l\'investissement le plus rentable</h2>
<p>Acquérir un nouveau client coûte 5 à 7 fois plus cher que de fidéliser un client existant. Voici comment transformer vos clients en ambassadeurs.</p>

<h3>Dépassez les attentes</h3>
<p>Ne vous contentez pas de satisfaire, surprenez. Un petit plus inattendu crée un souvenir mémorable.</p>

<h3>Restez en contact</h3>
<p>N\'attendez pas que le client revienne. Envoyez des nouvelles régulièrement : newsletter, vœux, informations utiles.</p>

<h3>Demandez des retours</h3>
<p>Sollicitez l\'avis de vos clients. Cela montre que vous valorisez leur opinion et vous permet de vous améliorer.</p>

<h3>Créez un programme de fidélité</h3>
<p>Récompensez la loyauté : remise sur la prochaine commande, service offert, avantages exclusifs.</p>

<h3>Personnalisez la relation</h3>
<p>Souvenez-vous des préférences de vos clients. Un CRM simple peut vous y aider.</p>

<h3>Traitez les problèmes rapidement</h3>
<p>Un client mécontent bien géré devient souvent votre meilleur ambassadeur. Réagissez vite et généreusement.</p>

<h3>Demandez des recommandations</h3>
<p>N\'hésitez pas à solliciter vos clients satisfaits pour des recommandations. Le bouche-à-oreille reste le meilleur marketing.</p>

<blockquote>
<p>« Un client fidèle n\'est pas seulement quelqu\'un qui revient, c\'est quelqu\'un qui vous recommande. »</p>
</blockquote>';
    }
}
