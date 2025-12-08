<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DepenseSeeder extends Seeder
{
    public function run()
    {
        $company = $this->db->table('companies')->get()->getFirstRow();
        $user = $this->db->table('users')->where('company_id', $company->id ?? null)->get()->getFirstRow();
        $tvaRate = $this->db->table('tva_rates')->where('is_default', true)->get()->getFirstRow();
        $categorie = $this->db->table('categories_depenses')->limit(1)->get()->getFirstRow();
        $fournisseur = $this->db->table('fournisseurs')->limit(1)->get()->getFirstRow();
        
        if (!$company || !$user || !$tvaRate || !$categorie) {
            echo "⚠️  Données de base manquantes pour créer les dépenses\n";
            return;
        }

        $depenses = [];
        $methodesPaiement = ['especes', 'cheque', 'virement', 'cb'];
        $statuts = ['brouillon', 'valide', 'archive'];
        
        // Générer 20 dépenses avec des données variées
        for ($i = 1; $i <= 20; $i++) {
            $montantHT = mt_rand(10, 5000) + (mt_rand(0, 99) / 100);
            $tvaAmount = $montantHT * ($tvaRate->rate / 100);
            $montantTTC = $montantHT + $tvaAmount;
            
            $date = date('Y-m-d', strtotime("-" . mt_rand(0, 365) . " days"));
            
            $depenses[] = [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'user_id' => $user->id,
                'date' => $date,
                'montant_ht' => round($montantHT, 2),
                'montant_ttc' => round($montantTTC, 2),
                'tva_id' => $tvaRate->id,
                'description' => $this->getRandomDescription($i),
                'categorie_id' => $categorie->id,
                'fournisseur_id' => $fournisseur ? $fournisseur->id : null,
                'justificatif_path' => null,
                'statut' => $statuts[array_rand($statuts)],
                'recurrent' => mt_rand(0, 10) > 7, // 30% de chance d'être récurrent
                'frequence_id' => null,
                'methode_paiement' => $methodesPaiement[array_rand($methodesPaiement)],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }

        $this->db->table('depenses')->insertBatch($depenses);
        echo "✓ " . count($depenses) . " dépenses créées\n";
    }

    private function getRandomDescription(int $index): string
    {
        $descriptions = [
            'Achat fournitures de bureau',
            'Frais de déplacement client Paris',
            'Repas d\'affaires avec prospect',
            'Abonnement logiciel SaaS mensuel',
            'Maintenance serveur',
            'Frais postaux et affranchissement',
            'Formation professionnelle en ligne',
            'Hébergement web annuel',
            'Assurance responsabilité civile',
            'Carburant véhicule de service',
            'Achat matériel informatique',
            'Prestation consultant externe',
            'Publicité Google Ads',
            'Abonnement téléphonie mobile',
            'Frais bancaires trimestriels',
            'Fournitures nettoyage locaux',
            'Licence logiciel comptabilité',
            'Entretien photocopieuse',
            'Achat de domaine internet',
            'Participation salon professionnel',
        ];
        
        return $descriptions[($index - 1) % count($descriptions)];
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
}

