<?php

namespace App\Models;

use CodeIgniter\Model;

class ContactAttachmentModel extends Model
{
    protected $table      = 'contact_attachments';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;
    protected $createdField = 'created_at';

    protected $allowedFields = [
        'contact_id',
        'filename',
        'original_name',
        'path',
        'mime_type',
        'size'
    ];

    /**
     * Get attachments for a contact
     */
    public function getForContact(int $contactId): array
    {
        return $this->where('contact_id', $contactId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
}
