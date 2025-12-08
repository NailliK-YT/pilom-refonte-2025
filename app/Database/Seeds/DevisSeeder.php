<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DevisSeeder extends Seeder
{
    public function run()
    {
        $contacts = $this->db->table('contact')->get()->getResultArray();
        
        if (count($contacts) < 5) {
            echo "❌ Erreur: Pas assez de contacts pour créer les devis.\n";
            return;
        }

        $statuts = ['brouillon', 'envoyé', 'accepté', 'refusé', 'expiré'];
        $services = [
            'Développement site web vitrine',
            'Application mobile iOS/Android',
            'Maintenance et support annuel',
            'Refonte graphique identité visuelle',
            'Migration infrastructure cloud',
            'Formation équipe développement',
            'Audit sécurité et conformité RGPD',
            'Campagne SEO et marketing digital',
            'Plateforme e-commerce complète',
            'Développement API REST',
            'Infrastructure réseau entreprise',
            'Support technique niveau 2',
            'Consulting stratégique IT',
            'Design UI/UX application',
            'Hébergement et infogérance',
            'Intégration ERP/CRM',
            'Formation utilisateurs',
            'Développement module spécifique',
            'Refonte base de données',
            'Optimisation performance'
        ];
        
        $conditions = [
            'Paiement à 30 jours fin de mois',
            'Paiement comptant',
            'Paiement à réception de facture',
            'Paiement en 3 fois sans frais',
            '50% à la commande, 50% à la livraison',
            'Paiement à 45 jours date de facture',
            'Prélèvement mensuel',
            'Paiement à 60 jours fin de mois'
        ];
        
        $data = [];
        $currentYear = date('Y');
        
        // Générer 25 devis variés
        for ($i = 1; $i <= 25; $i++) {
            $contactIndex = array_rand($contacts);
            $contact = $contacts[$contactIndex];
            $joursEmission = rand(1, 180);
            $dateEmission = date('Y-m-d', strtotime("-$joursEmission days"));
            $dateValidite = date('Y-m-d', strtotime($dateEmission . ' +30 days'));
            
            // Déterminer le statut intelligent
            $now = time();
            $validiteTime = strtotime($dateValidite);
            
            if ($joursEmission <= 7) {
                $statut = rand(0, 1) ? 'brouillon' : 'envoyé';
            } elseif ($validiteTime < $now) {
                $possibilities = ['expiré', 'accepté', 'refusé'];
                $statut = $possibilities[array_rand($possibilities)];
            } else {
                $possibilities = ['envoyé', 'accepté', 'refusé'];
                $statut = $possibilities[array_rand($possibilities)];
            }
            
            $montantHT = rand(50, 2000) * 10;
            $montantTVA = round($montantHT * 0.20, 2);
            $montantTTC = $montantHT + $montantTVA;
            
            $data[] = [
                'numero_devis'   => 'DEV-' . $currentYear . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'date_emission'  => $dateEmission,
                'date_validite'  => $dateValidite,
                'montant_ht'     => $montantHT,
                'montant_tva'    => $montantTVA,
                'montant_ttc'    => $montantTTC,
                'statut'         => $statut,
                'contact_id'     => is_array($contact) ? $contact['id'] : $contact->id,
                'created_at'     => date('Y-m-d H:i:s', strtotime($dateEmission)),
                'updated_at'     => date('Y-m-d H:i:s'),
            ];
        }

        $this->db->table('devis')->insertBatch($data);
        echo "✓ " . count($data) . " devis créés avec succès\n";
    }
}
