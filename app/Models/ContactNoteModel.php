<?php

namespace App\Models;

use CodeIgniter\Model;

class ContactNoteModel extends Model
{
    protected $table      = 'contact_notes';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'contact_id',
        'user_id',
        'content'
    ];

    /**
     * Get notes for a contact
     */
    public function getForContact(int $contactId): array
    {
        return $this->where('contact_id', $contactId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
}
