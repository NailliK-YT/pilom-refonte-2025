<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * SeoMetaModel
 * 
 * Modèle pour la gestion des métadonnées SEO personnalisées.
 * Permet de stocker des meta titles/descriptions spécifiques par entité.
 */
class SeoMetaModel extends Model
{
    protected $table = 'seo_meta';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'entity_type',
        'entity_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'twitter_title',
        'twitter_description',
        'canonical_url',
        'robots_directive',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'entity_type' => 'required|max_length[50]',
        'entity_id' => 'required',
        'meta_title' => 'permit_empty|max_length[70]',
        'meta_description' => 'permit_empty|max_length[170]',
        'meta_keywords' => 'permit_empty|max_length[500]',
        'og_title' => 'permit_empty|max_length[70]',
        'og_description' => 'permit_empty|max_length[200]',
        'og_image' => 'permit_empty|max_length[255]',
        'twitter_title' => 'permit_empty|max_length[70]',
        'twitter_description' => 'permit_empty|max_length[200]',
        'canonical_url' => 'permit_empty|max_length[255]',
        'robots_directive' => 'permit_empty|max_length[50]',
    ];

    protected $validationMessages = [
        'meta_title' => [
            'max_length' => 'Le meta title ne doit pas dépasser 70 caractères',
        ],
        'meta_description' => [
            'max_length' => 'La meta description ne doit pas dépasser 170 caractères',
        ],
    ];

    /**
     * Récupère les métadonnées SEO pour une entité
     * 
     * @param string $entityType Type d'entité (page, product, category, etc.)
     * @param string $entityId ID de l'entité
     * @return array|null
     */
    public function getForEntity(string $entityType, string $entityId): ?array
    {
        return $this->where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->first();
    }

    /**
     * Sauvegarde ou met à jour les métadonnées SEO pour une entité
     * 
     * @param string $entityType Type d'entité
     * @param string $entityId ID de l'entité
     * @param array $data Données SEO à sauvegarder
     * @return bool
     */
    public function saveForEntity(string $entityType, string $entityId, array $data): bool
    {
        $existing = $this->getForEntity($entityType, $entityId);

        $data['entity_type'] = $entityType;
        $data['entity_id'] = $entityId;

        if ($existing) {
            return $this->update($existing['id'], $data);
        }

        return $this->insert($data) !== false;
    }

    /**
     * Supprime les métadonnées SEO pour une entité
     * 
     * @param string $entityType Type d'entité
     * @param string $entityId ID de l'entité
     * @return bool
     */
    public function deleteForEntity(string $entityType, string $entityId): bool
    {
        return $this->where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->delete();
    }

    /**
     * Récupère toutes les métadonnées SEO d'un type d'entité
     * 
     * @param string $entityType Type d'entité
     * @return array
     */
    public function getAllByType(string $entityType): array
    {
        return $this->where('entity_type', $entityType)
            ->orderBy('updated_at', 'DESC')
            ->findAll();
    }

    /**
     * Vérifie si une entité a des métadonnées SEO personnalisées
     * 
     * @param string $entityType Type d'entité
     * @param string $entityId ID de l'entité
     * @return bool
     */
    public function hasCustomMeta(string $entityType, string $entityId): bool
    {
        return $this->where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->countAllResults() > 0;
    }

    /**
     * Obtient les statistiques SEO (pages avec/sans meta personnalisées)
     * 
     * @return array
     */
    public function getStats(): array
    {
        $stats = [];

        $result = $this->select('entity_type, COUNT(*) as count')
            ->groupBy('entity_type')
            ->findAll();

        foreach ($result as $row) {
            $stats[$row['entity_type']] = (int) $row['count'];
        }

        return $stats;
    }
}
