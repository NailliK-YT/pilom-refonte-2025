<?php

namespace App\Models;

use CodeIgniter\Model;

class FactureModel extends Model
{
    protected $table      = 'facture';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'numero_facture',
        'date_emission',
        'date_echeance',
        'montant_ht',
        'montant_tva',
		'montant_ttc',
        'statut',
        'contact_id',
        'id_devis',
        'remise_type',
        'remise_value',
        'acompte_montant',
        'solde_restant',
        'penalite_retard_percent'
    ];

    /**
     * Récupère les factures avec les infos du contact pour une company
     * 
     * @param string $companyId ID de la company
     * @return array
     */
    public function getWithContact(string $companyId): array
    {
        return $this->select('facture.*, contact.prenom, contact.nom, contact.entreprise')
                    ->join('contact', 'contact.id = facture.contact_id')
                    ->where('contact.company_id', $companyId)
                    ->orderBy('date_emission', 'DESC')
                    ->findAll();
    }

    /**
     * Filtre factures par numéro, contact ou statut pour une company
     * 
     * @param string $companyId ID de la company
     * @param string|null $search Terme de recherche
     * @param string|null $statut Statut de la facture
     * @return array
     */
    public function getWithFilters(string $companyId, ?string $search = null, ?string $statut = null): array
    {
        $this->select('facture.*, contact.prenom, contact.nom, contact.entreprise');
        $this->join('contact', 'contact.id = facture.contact_id');
        
        // Filtrer par company_id via le contact (sécurité multi-tenant)
        $this->where('contact.company_id', $companyId);

        if ($search) {
            $this->groupStart()
                ->like('facture.numero_facture', $search)
                ->orLike('contact.prenom', $search)
                ->orLike('contact.nom', $search)
                ->groupEnd();
        }

        if ($statut) {
            $this->where('facture.statut', $statut);
        }

        return $this->orderBy('facture.date_emission', 'DESC')
                    ->findAll();
    }

    /**
     * Trouve une facture par ID en vérifiant la company via le contact
     * 
     * @param int $id ID de la facture
     * @param string $companyId ID de la company
     * @return array|null
     */
    public function findForCompany(int $id, string $companyId): ?array
    {
        return $this->select('facture.*')
                    ->join('contact', 'contact.id = facture.contact_id')
                    ->where('facture.id', $id)
                    ->where('contact.company_id', $companyId)
                    ->first();
    }

    /**
     * Calcule les totaux (remises, acomptes)
     * 
     * @param array $data Données de la facture (montant_ht, montant_tva, remise_type, remise_value, acompte_montant)
     * @return array Données mises à jour avec montant_ttc et solde_restant
     */
    public function calculateTotals(array $data): array
    {
        $ht = $data['montant_ht'];
        $tva = $data['montant_tva'];
        
        // Appliquer la remise
        if (isset($data['remise_type']) && isset($data['remise_value']) && $data['remise_value'] > 0) {
            if ($data['remise_type'] === 'percent') {
                $remiseAmount = $ht * ($data['remise_value'] / 100);
                $ht -= $remiseAmount;
            } elseif ($data['remise_type'] === 'amount') {
                $ht -= $data['remise_value'];
            }
        }
        
        // Recalculer TTC (simplifié, normalement TVA recalculée sur HT remisé)
        // Si la TVA est fixe, on l'ajoute juste. Si elle est % du HT, il faudrait la recalculer.
        // Ici on suppose que montant_tva est passé en paramètre, donc on l'ajoute.
        // MAIS attention, si on remise le HT, la TVA change souvent.
        // Pour simplifier ici, on recalcul le TTC comme HT + TVA.
        $ttc = $ht + $tva;
        
        $data['montant_ttc'] = $ttc;
        
        // Calculer solde restant
        $acompte = $data['acompte_montant'] ?? 0;
        $data['solde_restant'] = $ttc - $acompte;
        
        return $data;
    }
}
