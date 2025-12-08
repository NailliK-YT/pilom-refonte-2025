<?php

namespace App\Models;

use CodeIgniter\Model;

class FrequenceModel extends Model
{
    protected $table = 'frequences';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id',
        'nom',
        'jours'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'nom' => 'required|min_length[3]|max_length[50]|is_unique[frequences.nom]',
        'jours' => 'required|integer|greater_than[0]'
    ];
    protected $validationMessages = [
        'nom' => [
            'required' => 'Le nom de la fréquence est obligatoire',
            'is_unique' => 'Cette fréquence existe déjà'
        ],
        'jours' => [
            'required' => 'Le nombre de jours est obligatoire',
            'greater_than' => 'Le nombre de jours doit être supérieur à 0'
        ]
    ];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateId'];
    protected $beforeUpdate = [];

    /**
     * Generate UUID for new records
     */
    protected function generateId(array $data)
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = $this->generateUUID();
        }

        return $data;
    }

    /**
     * Generate a UUID v4
     */
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

    /**
     * Get all frequencies formatted for select dropdown
     * 
     * @return array Associative array with id as key and formatted label as value
     */
    public function getForSelect(): array
    {
        $frequences = $this->findAll();
        $result = [];

        foreach ($frequences as $freq) {
            $label = $freq['nom'];
            if ($freq['jours'] == 7) {
                $label .= ' (chaque semaine)';
            } elseif ($freq['jours'] == 30) {
                $label .= ' (chaque mois)';
            } elseif ($freq['jours'] == 365) {
                $label .= ' (chaque année)';
            } else {
                $label .= ' (tous les ' . $freq['jours'] . ' jours)';
            }
            $result[$freq['id']] = $label;
        }

        return $result;
    }

    /**
     * Get frequency by name
     * 
     * @param string $nom Frequency name
     * @return array|null
     */
    public function getByName(string $nom): ?array
    {
        return $this->where('nom', $nom)->first();
    }
}
