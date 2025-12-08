<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanySettingsModel extends Model
{
    protected $table = 'company_settings';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id',
        'company_id',
        'address',
        'postal_code',
        'city',
        'country',
        'phone',
        'email',
        'website',
        'siret',
        'siren',
        'vat_number',
        'default_vat_rate',
        'logo',
        'legal_mentions',
        'terms_conditions',
        'iban',
        'bic',
        'invoice_prefix',
        'invoice_next_number'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'company_id' => 'required|is_not_unique[companies.id]',
        'postal_code' => 'permit_empty|max_length[10]',
        'city' => 'permit_empty|max_length[100]',
        'country' => 'required|max_length[100]',
        'phone' => 'permit_empty|max_length[20]',
        'email' => 'permit_empty|valid_email|max_length[255]',
        'website' => 'permit_empty|valid_url|max_length[255]',
        'siret' => 'permit_empty|exact_length[14]|numeric',
        'siren' => 'permit_empty|exact_length[9]|numeric',
        'vat_number' => 'permit_empty|max_length[20]',
        'default_vat_rate' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
        'iban' => 'permit_empty|max_length[34]',
        'bic' => 'permit_empty|max_length[11]',
        'invoice_prefix' => 'required|max_length[10]|alpha_numeric',
        'invoice_next_number' => 'required|integer|greater_than[0]'
    ];

    protected $validationMessages = [
        'company_id' => [
            'required' => 'L\'identifiant de l\'entreprise est requis.',
            'is_not_unique' => 'L\'entreprise n\'existe pas.'
        ],
        'email' => [
            'valid_email' => 'L\'adresse email n\'est pas valide.'
        ],
        'website' => [
            'valid_url' => 'L\'URL du site web n\'est pas valide.'
        ],
        'siret' => [
            'exact_length' => 'Le SIRET doit contenir exactement 14 chiffres.',
            'numeric' => 'Le SIRET doit contenir uniquement des chiffres.'
        ],
        'siren' => [
            'exact_length' => 'Le SIREN doit contenir exactement 9 chiffres.',
            'numeric' => 'Le SIREN doit contenir uniquement des chiffres.'
        ],
        'invoice_prefix' => [
            'alpha_numeric' => 'Le préfixe de facture doit être alphanumérique.'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateId'];

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
     * Get settings by company ID
     */
    public function getByCompanyId(string $companyId)
    {
        return $this->where('company_id', $companyId)->first();
    }

    /**
     * Create or update settings for a company
     */
    public function upsertSettings(string $companyId, array $data)
    {
        $existing = $this->getByCompanyId($companyId);

        if ($existing) {
            return $this->update($existing['id'], $data);
        }

        $data['company_id'] = $companyId;
        return $this->insert($data);
    }

    /**
     * Validate SIRET number (Luhn algorithm)
     */
    public function validateSiret(string $siret): bool
    {
        if (strlen($siret) !== 14 || !ctype_digit($siret)) {
            return false;
        }

        // Luhn algorithm
        $sum = 0;
        for ($i = 0; $i < 14; $i++) {
            $digit = (int) $siret[$i];
            if ($i % 2 === 1) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            $sum += $digit;
        }

        return $sum % 10 === 0;
    }

    /**
     * Validate IBAN format (basic check)
     */
    public function validateIban(string $iban): bool
    {
        // Remove spaces and convert to uppercase
        $iban = strtoupper(str_replace(' ', '', $iban));

        // Check length (15-34 characters)
        if (strlen($iban) < 15 || strlen($iban) > 34) {
            return false;
        }

        // Check first 2 characters are letters
        if (!ctype_alpha(substr($iban, 0, 2))) {
            return false;
        }

        // For French IBAN, should be 27 characters
        if (substr($iban, 0, 2) === 'FR' && strlen($iban) !== 27) {
            return false;
        }

        return true;
    }

    /**
     * Generate next invoice number and update
     */
    public function getNextInvoiceNumber(string $companyId): string
    {
        $settings = $this->getByCompanyId($companyId);

        if (!$settings) {
            return 'INV-0001';
        }

        $prefix = $settings['invoice_prefix'];
        $number = $settings['invoice_next_number'];
        $invoiceNumber = $prefix . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);

        // Increment for next time
        $this->update($settings['id'], [
            'invoice_next_number' => $number + 1
        ]);

        return $invoiceNumber;
    }
}
