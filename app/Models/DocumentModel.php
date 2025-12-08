<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentModel extends Model
{
    protected $table      = 'documents';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'company_id',
        'folder_id',
        'uploaded_by',
        'name',
        'original_name',
        'path',
        'mime_type',
        'size',
        'version',
        'is_shared',
        'share_token'
    ];

    /**
     * Get documents for a company
     */
    public function getForCompany(string $companyId, ?int $folderId = null): array
    {
        $builder = $this->where('company_id', $companyId);
        
        if ($folderId === null) {
            $builder->where('folder_id IS NULL');
        } else {
            $builder->where('folder_id', $folderId);
        }
        
        return $builder->orderBy('name', 'ASC')->findAll();
    }

    /**
     * Search documents
     */
    public function search(string $companyId, string $query): array
    {
        return $this->where('company_id', $companyId)
                    ->like('name', $query)
                    ->orLike('original_name', $query)
                    ->findAll();
    }

    /**
     * Generate share token
     */
    public function generateShareToken(int $documentId): ?string
    {
        $token = bin2hex(random_bytes(32));
        $this->update($documentId, [
            'is_shared' => true,
            'share_token' => $token
        ]);
        return $token;
    }

    /**
     * Find by share token
     */
    public function findByShareToken(string $token): ?array
    {
        return $this->where('share_token', $token)->where('is_shared', true)->first();
    }
}
