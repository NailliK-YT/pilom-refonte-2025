<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * UrlRedirectModel
 * 
 * Modèle pour la gestion des redirections 301.
 * Permet de rediriger les anciennes URLs vers les nouvelles.
 */
class UrlRedirectModel extends Model
{
    protected $table = 'url_redirects';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'old_url',
        'new_url',
        'redirect_code',
        'hits',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = null;

    protected $validationRules = [
        'old_url' => 'required|max_length[500]|is_unique[url_redirects.old_url,id,{id}]',
        'new_url' => 'required|max_length[500]',
        'redirect_code' => 'permit_empty|in_list[301,302,307,308]',
    ];

    protected $validationMessages = [
        'old_url' => [
            'required' => 'L\'ancienne URL est obligatoire',
            'is_unique' => 'Une redirection existe déjà pour cette URL',
        ],
        'new_url' => [
            'required' => 'La nouvelle URL est obligatoire',
        ],
    ];

    /**
     * Recherche une redirection pour une URL donnée
     * 
     * @param string $url URL à rechercher
     * @return array|null
     */
    public function findRedirect(string $url): ?array
    {
        // Normalise l'URL (supprime le slash final)
        $url = rtrim($url, '/');

        return $this->where('old_url', $url)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Incrémente le compteur de hits pour une redirection
     * 
     * @param string $id ID de la redirection
     * @return bool
     */
    public function incrementHits(string $id): bool
    {
        return $this->set('hits', 'hits + 1', false)
            ->where('id', $id)
            ->update();
    }

    /**
     * Crée une nouvelle redirection
     * 
     * @param string $oldUrl Ancienne URL
     * @param string $newUrl Nouvelle URL
     * @param int $code Code de redirection (301 par défaut)
     * @return bool
     */
    public function createRedirect(string $oldUrl, string $newUrl, int $code = 301): bool
    {
        return $this->insert([
            'old_url' => rtrim($oldUrl, '/'),
            'new_url' => $newUrl,
            'redirect_code' => $code,
            'is_active' => true,
        ]) !== false;
    }

    /**
     * Récupère toutes les redirections actives
     * 
     * @return array
     */
    public function getActiveRedirects(): array
    {
        return $this->where('is_active', true)
            ->orderBy('hits', 'DESC')
            ->findAll();
    }

    /**
     * Récupère les redirections les plus utilisées
     * 
     * @param int $limit Nombre de résultats
     * @return array
     */
    public function getTopRedirects(int $limit = 10): array
    {
        return $this->where('is_active', true)
            ->orderBy('hits', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Désactive une redirection
     * 
     * @param string $id ID de la redirection
     * @return bool
     */
    public function deactivate(string $id): bool
    {
        return $this->update($id, ['is_active' => false]);
    }

    /**
     * Active une redirection
     * 
     * @param string $id ID de la redirection
     * @return bool
     */
    public function activate(string $id): bool
    {
        return $this->update($id, ['is_active' => true]);
    }

    /**
     * Vérifie si une URL est déjà redirigée
     * 
     * @param string $url URL à vérifier
     * @return bool
     */
    public function isRedirected(string $url): bool
    {
        return $this->where('old_url', rtrim($url, '/'))
            ->countAllResults() > 0;
    }
}
