<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ContactSeeder extends Seeder
{
    public function run()
    {
        $company = $this->db->table('companies')->get()->getFirstRow();
        if (!$company) {
            throw new \RuntimeException("❌ Erreur: Aucune entreprise trouvée. Exécutez CompanySeeder d'abord.");
        }
        
        echo "✓ Entreprise trouvée : {$company->name} (ID: {$company->id})\n";

        $prenoms = ['Sophie', 'Jean', 'Marie', 'Pierre', 'Isabelle', 'Lucas', 'Emma', 'Thomas', 'Julie', 'Nicolas', 
                    'Camille', 'Alexandre', 'Sarah', 'Maxime', 'Laura', 'Antoine', 'Chloé', 'Julien', 'Léa', 'Benjamin',
                    'Manon', 'Mathieu', 'Clara', 'Romain', 'Océane', 'Hugo', 'Alice', 'Arthur', 'Inès', 'Louis'];
        
        $noms = ['Martin', 'Bernard', 'Dubois', 'Thomas', 'Robert', 'Richard', 'Petit', 'Durand', 'Leroy', 'Moreau',
                 'Simon', 'Laurent', 'Lefebvre', 'Michel', 'Garcia', 'David', 'Bertrand', 'Roux', 'Vincent', 'Fournier',
                 'Morel', 'Girard', 'André', 'Mercier', 'Dupont', 'Lambert', 'Bonnet', 'François', 'Martinez', 'Legrand'];
        
        $entreprises = [
            'TechCorp SARL', 'Innovation Plus SAS', 'Services & Co', 'Digital Solutions', 'Web Agency Pro',
            'Consulting Partners', 'Business Solutions', 'Smart Tech', 'Creative Studio', 'Data Analytics SA',
            'Cloud Services', 'Agence Marketing 360', 'IT Experts', 'Design Lab', 'E-commerce Solutions',
            'Mobile Apps Dev', 'Cyber Security Pro', 'AI Research Lab', 'Blockchain Corp', 'Green Energy SA',
            'Transport Express', 'Food & Drinks Co', 'Fashion Design', 'Construction BTP', 'Medical Care',
            'Education Online', 'Real Estate Pro', 'Finance Advisors', 'Legal Services', 'HR Solutions'
        ];
        
        $villes = [
            ['Paris', '75001'], ['Lyon', '69002'], ['Bordeaux', '33000'], ['Marseille', '13001'], 
            ['Toulouse', '31000'], ['Nice', '06000'], ['Nantes', '44000'], ['Strasbourg', '67000'],
            ['Montpellier', '34000'], ['Lille', '59000'], ['Rennes', '35000'], ['Reims', '51100']
        ];
        
        $rues = [
            'rue de la République', 'avenue des Champs', 'boulevard Victor Hugo', 'place de la Liberté',
            'rue du Commerce', 'avenue Jean Jaurès', 'boulevard Haussmann', 'rue Nationale',
            'avenue de la Paix', 'rue Saint-Michel', 'boulevard Gambetta', 'place du Marché',
            'rue de Rivoli', 'avenue Foch', 'boulevard Voltaire', 'rue Lafayette'
        ];
        
        $data = [];
        
        // Générer 35 contacts variés
        for ($i = 0; $i < 35; $i++) {
            $prenom = $prenoms[$i % count($prenoms)];
            $nom = $noms[$i % count($noms)];
            $ville = $villes[$i % count($villes)];
            $rue = $rues[$i % count($rues)];
            
            // 50% clients, 35% prospects, 15% fournisseurs
            if ($i < 18) {
                $type = 'client';
            } elseif ($i < 30) {
                $type = 'prospect';
            } else {
                $type = 'fournisseur';
            }
            
            // Déterminer le statut selon le type
            if ($type === 'client') {
                $rand = $i % 20;
                if ($rand < 15) $statut = 'actif';
                elseif ($rand < 17) $statut = 'inactif';
                else $statut = 'archive';
            } elseif ($type === 'prospect') {
                $rand = $i % 10;
                if ($rand < 6) $statut = 'actif';
                else $statut = 'en_negociation';
            } else {
                $statut = 'actif';
            }
            
            $data[] = [
                'prenom'        => $prenom,
                'nom'           => $nom,
                'email'         => strtolower($prenom) . '.' . strtolower($nom) . $i . '@example.com',
                'telephone'     => '0' . rand(1, 9) . ' ' . rand(10, 99) . ' ' . rand(10, 99) . ' ' . rand(10, 99) . ' ' . rand(10, 99),
                'adresse'       => rand(1, 150) . ' ' . $rue . ', ' . $ville[1] . ' ' . $ville[0],
                'entreprise'    => $entreprises[$i % count($entreprises)],
                'company_id'    => $company->id,
                'type'          => $type,
                'statut'        => $statut,
                'date_creation' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 365) . ' days')),
            ];
        }

        $this->db->table('contact')->insertBatch($data);
        echo "✓ " . count($data) . " contacts créés avec succès\n";
    }
}
