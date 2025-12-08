<?php

namespace App\Models;

use CodeIgniter\Model;

class FolderModel extends Model
{
    protected $table      = 'folders';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'company_id',
        'parent_id',
        'name'
    ];

    /**
     * Get folders for a company
     */
    public function getForCompany(string $companyId, ?int $parentId = null): array
    {
        $builder = $this->where('company_id', $companyId);
        
        if ($parentId === null) {
            $builder->where('parent_id IS NULL');
        } else {
            $builder->where('parent_id', $parentId);
        }
        
        return $builder->orderBy('name', 'ASC')->findAll();
    }

    /**
     * Get folder tree for a company
     */
    public function getFolderTree(string $companyId): array
    {
        $folders = $this->where('company_id', $companyId)->findAll();
        return $this->buildTree($folders);
    }

    private function buildTree(array $folders, ?int $parentId = null): array
    {
        $tree = [];
        foreach ($folders as $folder) {
            if ($folder['parent_id'] == $parentId) {
                $folder['children'] = $this->buildTree($folders, $folder['id']);
                $tree[] = $folder;
            }
        }
        return $tree;
    }
}
