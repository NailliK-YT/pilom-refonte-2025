<?php

namespace App\Models;

use CodeIgniter\Model;

class DevisModel extends Model
{
    protected $table      = 'devis';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'numero_devis',
        'date_emission',
        'date_validite',
        'montant_ht',
        'montant_tva',
        'montant_ttc',
        'statut',
        'contact_id'
    ];

    /**
     * Récupère les devis avec le contact associé pour une company
     * 
     * @param string $companyId ID de la company
     * @return array
     */
    public function getWithContact(string $companyId): array
    {
        return $this->select('devis.*, contact.prenom, contact.nom, contact.entreprise')
                    ->join('contact', 'contact.id = devis.contact_id')
                    ->where('contact.company_id', $companyId)
                    ->orderBy('date_emission', 'DESC')
                    ->findAll();
    }

    /**
     * Filtre par statut ou numéro de devis pour une company
     * 
     * @param string $companyId ID de la company
     * @param string|null $search Terme de recherche
     * @param string|null $statut Statut du devis
     * @return array
     */
	public function getWithFilters(string $companyId, ?string $search = null, ?string $statut = null): array
	{
		$this->select('devis.*, contact.prenom, contact.nom, contact.entreprise');
		$this->join('contact', 'contact.id = devis.contact_id');
		
		// Filtrer par company_id via le contact (sécurité multi-tenant)
		$this->where('contact.company_id', $companyId);

		if ($search) {
			$this->groupStart()
				->like('devis.numero_devis', $search)
				->orLike('contact.prenom', $search)
				->orLike('contact.nom', $search)
				->groupEnd();
		}

		if ($statut) {
			$this->where('devis.statut', $statut);
		}

		return $this->orderBy('devis.date_emission', 'DESC')
					->findAll();
	}

    /**
     * Trouve un devis par ID en vérifiant la company via le contact
     * 
     * @param int $id ID du devis
     * @param string $companyId ID de la company
     * @return array|null
     */
    public function findForCompany(int $id, string $companyId): ?array
    {
        return $this->select('devis.*')
                    ->join('contact', 'contact.id = devis.contact_id')
                    ->where('devis.id', $id)
                    ->where('contact.company_id', $companyId)
                    ->first();
    }
}
