<?php

namespace App\Models;

use CodeIgniter\Model;

class ReglementModel extends Model
{
    protected $table      = 'reglement';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'facture_id',
        'date_reglement',
        'montant',
        'mode_paiement',
        'reference'
    ];

    /**
     * Récupère les règlements avec les informations de la facture et du contact pour une company
     * 
     * @param string $companyId ID de la company
     * @return array
     */
    public function getWithFactureAndContact(string $companyId): array
    {
        return $this->select('reglement.*, facture.numero_facture, contact.prenom, contact.nom, contact.entreprise')
                    ->join('facture', 'facture.id = reglement.facture_id')
                    ->join('contact', 'contact.id = facture.contact_id')
                    ->where('contact.company_id', $companyId)
                    ->orderBy('reglement.date_reglement', 'DESC')
                    ->findAll();
    }

    /**
     * Filtre les règlements par facture, contact ou mode de paiement pour une company
     * 
     * @param string $companyId ID de la company
     * @param string|null $search Terme de recherche
     * @param string|null $modePaiement Mode de paiement
     * @return array
     */
    public function getWithFilters(string $companyId, ?string $search = null, ?string $modePaiement = null): array
    {
        $this->select('reglement.*, facture.numero_facture, contact.prenom, contact.nom, contact.entreprise')
             ->join('facture', 'facture.id = reglement.facture_id')
             ->join('contact', 'contact.id = facture.contact_id');
        
        // Filtrer par company_id via le contact (sécurité multi-tenant)
        $this->where('contact.company_id', $companyId);

        if ($search) {
            $this->groupStart()
                 ->like('facture.numero_facture', $search)
                 ->orLike('contact.prenom', $search)
                 ->orLike('contact.nom', $search)
                 ->groupEnd();
        }

        if ($modePaiement) {
            $this->where('reglement.mode_paiement', $modePaiement);
        }

        return $this->orderBy('reglement.date_reglement', 'DESC')
                    ->findAll();
    }

    /**
     * Trouve un règlement par ID en vérifiant la company via le contact
     * 
     * @param int $id ID du règlement
     * @param string $companyId ID de la company
     * @return array|null
     */
    public function findForCompany(int $id, string $companyId): ?array
    {
        return $this->select('reglement.*')
                    ->join('facture', 'facture.id = reglement.facture_id')
                    ->join('contact', 'contact.id = facture.contact_id')
                    ->where('reglement.id', $id)
                    ->where('contact.company_id', $companyId)
                    ->first();
    }

	/**
	 * Récupère les règlements formatés pour l’export bancaire
	 */
	public function getForBankExport(string $companyId): array
	{
		return $this->select('
				reglement.date_reglement,
				reglement.montant,
				reglement.mode_paiement,
				reglement.reference,
				facture.numero_facture,
				contact.nom,
				contact.prenom,
				contact.entreprise
			')
			->join('facture', 'facture.id = reglement.facture_id')
			->join('contact', 'contact.id = facture.contact_id')
			->where('contact.company_id', $companyId)
			->orderBy('reglement.date_reglement', 'ASC')
			->findAll();
	}

}
