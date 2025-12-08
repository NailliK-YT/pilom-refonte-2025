<?php

namespace App\Models;

use CodeIgniter\Model;

class ContactModel extends Model
{
    protected $table      = 'contact';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'prenom', 'nom', 'email', 'telephone', 'adresse', 'entreprise', 
        'company_id', 'type', 'statut', 'date_creation'
    ];

    protected $returnType     = 'array';
    protected $useTimestamps  = false;

    /**
     * Récupère les contacts avec filtres pour une company spécifique
     * 
     * @param string $companyId ID de la company
     * @param string|null $search Terme de recherche
     * @param string|null $type Type de contact
     * @param string|null $statut Statut du contact
     * @return array
     */
    public function getWithFilters(string $companyId, ?string $search = null, ?string $type = null, ?string $statut = null): array
    {
        $builder = $this->builder();

        // Filtrer par company_id (obligatoire pour la sécurité multi-tenant)
        $builder->where('company_id', $companyId);

        if ($search) {
            $builder->groupStart()
                    ->like('nom', $search)
                    ->orLike('prenom', $search)
                    ->orLike('email', $search)
                    ->orLike('entreprise', $search)
                    ->groupEnd();
        }

        if ($type && $type !== 'Tous') {
            $builder->where('type', $type);
        }

        if ($statut && $statut !== 'Tous') {
            $builder->where('statut', $statut);
        }

        $builder->orderBy('nom', 'ASC')->orderBy('prenom', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Récupère tous les contacts d'une company
     * 
     * @param string $companyId ID de la company
     * @return array
     */
    public function getByCompany(string $companyId): array
    {
        return $this->where('company_id', $companyId)
                    ->orderBy('nom', 'ASC')
                    ->orderBy('prenom', 'ASC')
                    ->findAll();
    }

    /**
     * Trouve un contact par ID en vérifiant la company
     * 
     * @param int $id ID du contact
     * @param string $companyId ID de la company
     * @return array|null
     */
    public function findForCompany(int $id, string $companyId): ?array
    {
        return $this->where('id', $id)
                    ->where('company_id', $companyId)
                    ->first();
    }
}
