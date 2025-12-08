<?php

namespace App\Controllers;

use App\Models\CompanyModel;
use App\Models\CompanySettingsModel;
use App\Helpers\FileUploadHelper;

class CompanySettingsController extends BaseController
{
    protected $companyModel;
    protected $settingsModel;

    public function __construct()
    {
        $this->companyModel = new CompanyModel();
        $this->settingsModel = new CompanySettingsModel();
        helper('form');
    }

    /**
     * Display company info form
     */
    public function index()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Vous devez être connecté.');
        }

        // Get user's company
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);

        if (!$user['company_id']) {
            return redirect()->to('/dashboard')->with('error', 'Aucune entreprise associée.');
        }

        $company = $this->companyModel->find($user['company_id']);
        $settings = $this->settingsModel->getByCompanyId($user['company_id']);

        $data = [
            'title' => 'Paramètres de l\'entreprise',
            'company' => $company,
            'settings' => $settings,
            'validation' => \Config\Services::validation()
        ];

        return view('settings/company_info', $data);
    }

    /**
     * Update company information
     */
    public function update()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Vous devez être connecté.');
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);

        if (!$user['company_id']) {
            return redirect()->to('/dashboard')->with('error', 'Aucune entreprise associée.');
        }

        // Validation rules
        $rules = [
            'company_name' => 'required|min_length[2]|max_length[255]',
            'address' => 'permit_empty',
            'postal_code' => 'permit_empty|max_length[10]',
            'city' => 'permit_empty|max_length[100]',
            'country' => 'required|max_length[100]',
            'phone' => 'permit_empty|max_length[20]',
            'email' => 'permit_empty|valid_email',
            'website' => 'permit_empty|valid_url',
            'siret' => 'permit_empty|exact_length[14]|numeric',
            'vat_number' => 'permit_empty|max_length[20]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Update company name
        $this->companyModel->update($user['company_id'], [
            'name' => $this->request->getPost('company_name')
        ]);

        // Prepare settings data
        $settingsData = [
            'address' => $this->request->getPost('address'),
            'postal_code' => $this->request->getPost('postal_code'),
            'city' => $this->request->getPost('city'),
            'country' => $this->request->getPost('country'),
            'phone' => $this->request->getPost('phone'),
            'email' => $this->request->getPost('email'),
            'website' => $this->request->getPost('website'),
            'siret' => $this->request->getPost('siret'),
            'vat_number' => $this->request->getPost('vat_number')
        ];

        // Calculate SIREN from SIRET
        if (!empty($settingsData['siret'])) {
            $settingsData['siren'] = substr($settingsData['siret'], 0, 9);
        }

        // Upsert settings
        if ($this->settingsModel->upsertSettings($user['company_id'], $settingsData)) {
            return redirect()->to('/settings/company')->with('success', 'Informations mises à jour avec succès.');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la mise à jour.');
    }

    /**
     * Display legal information form
     */
    public function legal()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Vous devez être connecté.');
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);

        if (!$user['company_id']) {
            return redirect()->to('/dashboard')->with('error', 'Aucune entreprise associée.');
        }

        $settings = $this->settingsModel->getByCompanyId($user['company_id']);

        $data = [
            'title' => 'Informations légales',
            'settings' => $settings,
            'validation' => \Config\Services::validation()
        ];

        return view('settings/legal', $data);
    }

    /**
     * Update legal information
     */
    public function updateLegal()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Vous devez être connecté.');
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);

        if (!$user['company_id']) {
            return redirect()->to('/dashboard')->with('error', 'Aucune entreprise associée.');
        }

        $data = [
            'legal_mentions' => $this->request->getPost('legal_mentions'),
            'terms_conditions' => $this->request->getPost('terms_conditions')
        ];

        if ($this->settingsModel->upsertSettings($user['company_id'], $data)) {
            return redirect()->to('/settings/company/legal')->with('success', 'Informations légales mises à jour.');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la mise à jour.');
    }

    /**
     * Display invoicing settings form
     */
    public function invoicing()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Vous devez être connecté.');
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);

        if (!$user['company_id']) {
            return redirect()->to('/dashboard')->with('error', 'Aucune entreprise associée.');
        }

        $settings = $this->settingsModel->getByCompanyId($user['company_id']);

        // Get VAT rates for dropdown
        $tvaModel = new \App\Models\TvaRateModel();
        $tvaRates = $tvaModel->getActive();

        $data = [
            'title' => 'Paramètres de facturation',
            'settings' => $settings,
            'tvaRates' => $tvaRates,
            'validation' => \Config\Services::validation()
        ];

        return view('settings/invoicing', $data);
    }

    /**
     * Update invoicing settings
     */
    public function updateInvoicing()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Vous devez être connecté.');
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);

        if (!$user['company_id']) {
            return redirect()->to('/dashboard')->with('error', 'Aucune entreprise associée.');
        }

        // Validation rules
        $rules = [
            'default_vat_rate' => 'required|decimal',
            'iban' => 'permit_empty|max_length[34]',
            'bic' => 'permit_empty|max_length[11]',
            'invoice_prefix' => 'required|alpha_numeric|max_length[10]',
            'invoice_next_number' => 'required|integer|greater_than[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'default_vat_rate' => $this->request->getPost('default_vat_rate'),
            'iban' => $this->request->getPost('iban'),
            'bic' => $this->request->getPost('bic'),
            'invoice_prefix' => $this->request->getPost('invoice_prefix'),
            'invoice_next_number' => $this->request->getPost('invoice_next_number')
        ];

        // Validate IBAN if provided
        if (!empty($data['iban']) && !$this->settingsModel->validateIban($data['iban'])) {
            return redirect()->back()->withInput()->with('error', 'Le format de l\'IBAN n\'est pas valide.');
        }

        if ($this->settingsModel->upsertSettings($user['company_id'], $data)) {
            return redirect()->to('/settings/company/invoicing')->with('success', 'Paramètres de facturation mis à jour.');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la mise à jour.');
    }

    /**
     * Upload company logo (AJAX)
     */
    public function uploadLogo()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'error' => 'Non authentifié.']);
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);

        if (!$user['company_id']) {
            return $this->response->setJSON(['success' => false, 'error' => 'Aucune entreprise associée.']);
        }

        $file = $this->request->getFile('logo');

        if (!$file) {
            return $this->response->setJSON(['success' => false, 'error' => 'Aucun fichier fourni.']);
        }

        // Upload and process logo
        $result = FileUploadHelper::uploadCompanyLogo($file, $user['company_id']);

        if (!$result['success']) {
            return $this->response->setJSON($result);
        }

        // Get old logo to delete
        $settings = $this->settingsModel->getByCompanyId($user['company_id']);
        $oldLogo = $settings['logo'] ?? null;

        // Update settings with new logo path
        if ($this->settingsModel->upsertSettings($user['company_id'], ['logo' => $result['path']])) {
            // Delete old logo if exists
            if ($oldLogo) {
                FileUploadHelper::deleteFile($oldLogo);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Logo mis à jour.',
                'url' => base_url($result['path'])
            ]);
        }

        return $this->response->setJSON(['success' => false, 'error' => 'Erreur lors de la mise à jour.']);
    }

    /**
     * Delete company logo
     */
    public function deleteLogo()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'error' => 'Non authentifié.']);
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);

        if (!$user['company_id']) {
            return $this->response->setJSON(['success' => false, 'error' => 'Aucune entreprise associée.']);
        }

        $settings = $this->settingsModel->getByCompanyId($user['company_id']);

        if (!$settings || !$settings['logo']) {
            return $this->response->setJSON(['success' => false, 'error' => 'Aucun logo à supprimer.']);
        }

        // Delete file
        FileUploadHelper::deleteFile($settings['logo']);

        // Update database
        if ($this->settingsModel->upsertSettings($user['company_id'], ['logo' => null])) {
            return $this->response->setJSON(['success' => true, 'message' => 'Logo supprimé.']);
        }

        return $this->response->setJSON(['success' => false, 'error' => 'Erreur lors de la suppression.']);
    }

    /**
     * Display document templates settings
     */
    public function documents()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Vous devez être connecté.');
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);

        if (!$user['company_id']) {
            return redirect()->to('/settings/company')->with('error', 'Aucune entreprise associée.');
        }

        $settings = $this->settingsModel->getByCompanyId($user['company_id']);

        $data = [
            'title' => 'Personnalisation des documents',
            'settings' => $settings ?? [],
            'validation' => \Config\Services::validation()
        ];

        return view('settings/document_templates', $data);
    }

    /**
     * Update document template settings
     */
    public function updateDocuments()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Vous devez être connecté.');
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);

        if (!$user['company_id']) {
            return redirect()->to('/settings/company')->with('error', 'Aucune entreprise associée.');
        }

        $documentSettings = [
            // Template
            'document_template' => $this->request->getPost('document_template'),
            'document_color_primary' => $this->request->getPost('document_color_primary'),
            'document_color_secondary' => $this->request->getPost('document_color_secondary'),
            // Invoice
            'invoice_prefix' => $this->request->getPost('invoice_prefix'),
            'invoice_next_number' => (int) $this->request->getPost('invoice_next_number'),
            'invoice_number_format' => $this->request->getPost('invoice_number_format'),
            // Quote
            'quote_prefix' => $this->request->getPost('quote_prefix'),
            'quote_next_number' => (int) $this->request->getPost('quote_next_number'),
            'quote_validity_days' => (int) $this->request->getPost('quote_validity_days'),
            // Payment
            'default_payment_terms' => (int) $this->request->getPost('default_payment_terms'),
            'late_payment_penalty_rate' => $this->request->getPost('late_payment_penalty_rate') ?: null,
            'early_payment_discount_rate' => $this->request->getPost('early_payment_discount_rate') ?: null,
            'early_payment_discount_days' => $this->request->getPost('early_payment_discount_days') ?: null,
            'payment_conditions_text' => $this->request->getPost('payment_conditions_text'),
            // Footer
            'invoice_footer_text' => $this->request->getPost('invoice_footer_text'),
            'quote_footer_text' => $this->request->getPost('quote_footer_text'),
        ];

        if ($this->settingsModel->upsertSettings($user['company_id'], $documentSettings)) {
            return redirect()->to('/settings/company/documents')
                ->with('success', 'Paramètres de documents mis à jour.');
        }

        return redirect()->back()->with('error', 'Erreur lors de la mise à jour.');
    }
}

