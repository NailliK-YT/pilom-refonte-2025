<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ReglementSeeder extends Seeder
{
    public function run()
    {
        $factures = $this->db->table('facture')->get()->getResultArray();
        
        if (empty($factures)) {
            echo "❌ Erreur: Aucune facture trouvée.\n";
            return;
        }
        
        $modesPaiement = ['virement', 'cheque', 'especes', 'carte_bancaire', 'prelevement', 'paypal'];
        $data = [];
        
        // Créer des règlements pour les factures payées et partiellement payées
        foreach ($factures as $facture) {
            if (in_array($facture['statut'], ['payée', 'partiellement_payée'])) {
                $montantFacture = $facture['montant_ttc'];
                $dateFacture = $facture['date_emission'];
                
                if ($facture['statut'] === 'payée') {
                    // Paiement complet
                    $joursApres = rand(5, 60);
                    $dateReglement = date('Y-m-d', strtotime($dateFacture . " +$joursApres days"));
                    $mode = $modesPaiement[array_rand($modesPaiement)];
                    
                    $data[] = [
                        'facture_id'     => $facture['id'],
                        'date_reglement' => $dateReglement,
                        'montant'        => $montantFacture,
                        'mode_paiement'  => $mode,
                        'reference'      => strtoupper(substr($mode, 0, 3)) . '-' . date('Ymd', strtotime($dateReglement)) . '-' . rand(1000, 9999),
                        'created_at'     => date('Y-m-d H:i:s', strtotime($dateReglement)),
                        'updated_at'     => date('Y-m-d H:i:s'),
                    ];
                    
                } else {
                    // Paiements partiels (2-3 paiements)
                    $nbPaiements = rand(2, 3);
                    $montantParPaiement = round($montantFacture / $nbPaiements, 2);
                    
                    for ($i = 0; $i < $nbPaiements; $i++) {
                        $joursApres = rand(5 + ($i * 25), 25 + ($i * 25));
                        $dateReglement = date('Y-m-d', strtotime($dateFacture . " +$joursApres days"));
                        $mode = $modesPaiement[array_rand($modesPaiement)];
                        
                        // Ajuster le dernier paiement
                        $montant = ($i === $nbPaiements - 1) ? 
                            round($montantFacture - ($montantParPaiement * ($nbPaiements - 1)), 2) : 
                            $montantParPaiement;
                        
                        $data[] = [
                            'facture_id'     => $facture['id'],
                            'date_reglement' => $dateReglement,
                            'montant'        => $montant,
                            'mode_paiement'  => $mode,
                            'reference'      => strtoupper(substr($mode, 0, 3)) . '-' . date('Ymd', strtotime($dateReglement)) . '-' . rand(1000, 9999),
                            'created_at'     => date('Y-m-d H:i:s', strtotime($dateReglement)),
                            'updated_at'     => date('Y-m-d H:i:s'),
                        ];
                    }
                }
            }
        }

        if (!empty($data)) {
            $this->db->table('reglement')->insertBatch($data);
            echo "✓ " . count($data) . " règlements créés avec succès\n";
        } else {
            echo "⚠️  Aucun règlement à créer (aucune facture payée)\n";
        }
    }
}
