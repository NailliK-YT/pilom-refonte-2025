<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FactureSeeder extends Seeder
{
    public function run()
    {
        $contacts = $this->db->table('contact')->get()->getResultArray();
        
        if (empty($contacts)) {
            echo "❌ Erreur: Aucun contact trouvé.\n";
            return;
        }
        
        $devis = $this->db->table('devis')->where('statut', 'accepté')->get()->getResultArray();
        
        $statuts = ['brouillon', 'envoyée', 'payée', 'en_retard', 'annulée', 'partiellement_payée'];
        $services = [
            'Développement site web',
            'Maintenance mensuelle',
            'Formation équipe',
            'Consulting stratégique',
            'Support technique premium',
            'Hébergement cloud',
            'Licence logiciel annuelle',
            'Design graphique',
            'Migration données',
            'Audit sécurité',
            'Optimisation SEO',
            'Application mobile',
            'Intégration API',
            'Infrastructure IT',
            'Développement module'
        ];
        
        $conditions = [
            'Paiement à 30 jours fin de mois',
            'Paiement à réception',
            'Paiement à 45 jours',
            'Paiement comptant',
            'Paiement en 3 fois',
            'Paiement à 60 jours'
        ];
        
        $data = [];
        $currentYear = date('Y');
        
        // Générer 30 factures
        for ($i = 1; $i <= 30; $i++) {
            $contactIndex = array_rand($contacts);
            $contact = $contacts[$contactIndex];
            $joursEmission = rand(1, 365);
            $dateEmission = date('Y-m-d', strtotime("-$joursEmission days"));
            $dateEcheance = date('Y-m-d', strtotime($dateEmission . ' +30 days'));
            
            // Lier à un devis accepté (30% du temps)
            $idDevis = (rand(1, 10) <= 3 && !empty($devis)) ? $devis[array_rand($devis)]['id'] : null;
            
            // Déterminer le statut intelligent
            $now = time();
            $echeanceTime = strtotime($dateEcheance);
            
            if ($joursEmission <= 7) {
                $statut = rand(0, 2) === 0 ? 'envoyée' : 'brouillon';
            } elseif ($joursEmission <= 20) {
                $possibilities = ['envoyée', 'payée'];
                $statut = $possibilities[array_rand($possibilities)];
            } elseif ($echeanceTime < $now) {
                // Factures échues
                $rand = rand(0, 100);
                if ($rand < 60) $statut = 'payée';
                elseif ($rand < 75) $statut = 'en_retard';
                elseif ($rand < 90) $statut = 'partiellement_payée';
                else $statut = 'annulée';
            } else {
                // Factures non échues
                $possibilities = ['envoyée', 'payée'];
                $statut = $possibilities[array_rand($possibilities)];
            }
            
            $montantHT = rand(50, 2500) * 10;
            $montantTVA = round($montantHT * 0.20, 2);
            $montantTTC = $montantHT + $montantTVA;
            
            $data[] = [
                'numero_facture' => 'FAC-' . $currentYear . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'date_emission'  => $dateEmission,
                'date_echeance'  => $dateEcheance,
                'montant_ht'     => $montantHT,
                'montant_tva'    => $montantTVA,
                'montant_ttc'    => $montantTTC,
                'statut'         => $statut,
                'contact_id'     => is_array($contact) ? $contact['id'] : $contact->id,
                'id_devis'       => $idDevis,
                'created_at'     => date('Y-m-d H:i:s', strtotime($dateEmission)),
                'updated_at'     => date('Y-m-d H:i:s'),
            ];
        }

        $this->db->table('facture')->insertBatch($data);
        echo "✓ " . count($data) . " factures créées avec succès\n";
    }
}
