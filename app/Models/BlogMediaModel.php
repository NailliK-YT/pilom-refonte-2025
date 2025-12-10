<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * BlogMediaModel
 * 
 * Manages blog media uploads
 */
class BlogMediaModel extends Model
{
    protected $table = 'blog_media';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'id',
        'uploaded_by',
        'filename',
        'original_filename',
        'file_path',
        'file_type',
        'mime_type',
        'file_size',
        'width',
        'height',
        'thumbnail_path',
        'medium_path',
        'webp_path',
        'alt_text',
        'caption',
        'title',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateId'];

    protected function generateId(array $data): array
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = $this->generateUUID();
        }
        return $data;
    }

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
     * Get all media with pagination
     */
    public function getAll(int $limit = 30, int $offset = 0, ?string $type = null): array
    {
        $builder = $this->orderBy('created_at', 'DESC');

        if ($type) {
            $builder->where('file_type', $type);
        }

        return $builder->limit($limit, $offset)->findAll();
    }

    /**
     * Get images only
     */
    public function getImages(int $limit = 30, int $offset = 0): array
    {
        return $this->where('file_type', 'image')
            ->orderBy('created_at', 'DESC')
            ->limit($limit, $offset)
            ->findAll();
    }

    /**
     * Search media by name
     */
    public function search(string $query, int $limit = 20): array
    {
        return $this->groupStart()
            ->like('filename', $query)
            ->orLike('original_filename', $query)
            ->orLike('title', $query)
            ->groupEnd()
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get storage usage
     */
    public function getStorageUsage(): array
    {
        $total = $this->selectSum('file_size', 'total_size')
            ->selectCount('id', 'count')
            ->first();

        return [
            'count' => (int) ($total['count'] ?? 0),
            'size_bytes' => (int) ($total['total_size'] ?? 0),
            'size_mb' => round(($total['total_size'] ?? 0) / 1024 / 1024, 2),
        ];
    }

    /**
     * Get recent uploads
     */
    public function getRecent(int $limit = 10): array
    {
        return $this->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Delete media and files
     */
    public function deleteWithFiles(string $id): bool
    {
        $media = $this->find($id);

        if (!$media) {
            return false;
        }

        // Delete physical files
        $paths = [
            $media['file_path'],
            $media['thumbnail_path'],
            $media['medium_path'],
            $media['webp_path'],
        ];

        foreach ($paths as $path) {
            if ($path && file_exists(FCPATH . $path)) {
                @unlink(FCPATH . $path);
            }
        }

        return $this->delete($id);
    }
}
