<?php

namespace App\Controllers;

use App\Models\ContactModel;

class ContactController extends BaseController
{
    protected $contactModel;

    public function __construct()
    {
        $this->contactModel = new ContactModel();
    }

    /**
     * Vérifie que l'utilisateur est connecté et retourne le company_id
     * 
     * @return string|null Le company_id ou null si redirection
     */
    protected function checkAuthAndGetCompanyId(): ?string
    {
        if (!session()->get('isLoggedIn')) {
            redirect()->to('/login')->send();
            return null;
        }
        
        $companyId = session()->get('company_id');
        if (!$companyId) {
            redirect()->to('/login')->with('error', 'Session invalide. Veuillez vous reconnecter.')->send();
            return null;
        }
        
        return $companyId;
    }

    /**
     * Liste des contacts avec filtres
     */
    public function index()
    {
        $companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

        $search = $this->request->getGet('search');
        $type   = $this->request->getGet('type');
        $statut = $this->request->getGet('statut');

        // Normaliser les valeurs vides
        $search = !empty($search) ? trim($search) : null;
        $type   = !empty($type) ? trim($type) : null;
        $statut = !empty($statut) ? trim($statut) : null;

        // Filtrer par company_id pour la sécurité multi-tenant
        $contacts = $this->contactModel->getWithFilters($companyId, $search, $type, $statut);

        $data = [
            'title'    => 'Contacts',
            'contacts' => $contacts,
            'search'   => $search,
            'type'     => $type,
            'statut'   => $statut,
            'user'     => session()->get('user'),
        ];

        return view('contacts/contacts', $data);
    }

    /**
     * Formulaire d'ajout d'un contact
     */
    public function create()
    {
        $companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

        return view('contacts/contact_create', ['title' => 'Ajouter un contact']);
    }

    /**
     * Sauvegarde un nouveau contact
     */
    public function store()
    {
        $companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

        $validation = \Config\Services::validation();

        $rules = [
            'nom'        => 'required|max_length[100]',
            'prenom'     => 'required|max_length[100]',
            'email'      => 'permit_empty|valid_email|max_length[255]',
            'telephone'  => 'permit_empty|max_length[30]',
            'adresse'    => 'permit_empty|max_length[255]',
            'entreprise' => 'permit_empty|max_length[255]',
            'type'       => 'required',
            'statut'     => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'nom'           => $this->request->getPost('nom'),
            'prenom'        => $this->request->getPost('prenom'),
            'email'         => $this->request->getPost('email'),
            'telephone'     => $this->request->getPost('telephone'),
            'adresse'       => $this->request->getPost('adresse'),
            'entreprise'    => $this->request->getPost('entreprise'),
            'company_id'    => $companyId,  // AJOUT: Associer le contact à la company
            'type'          => $this->request->getPost('type'),
            'statut'        => $this->request->getPost('statut'),
            'date_creation' => date('Y-m-d H:i:s'),
        ];

        if ($this->contactModel->insert($data)) {
            return redirect()->to('/contacts')->with('success', 'Contact ajouté avec succès.');
        }

        return redirect()->back()->with('error', 'Erreur lors de l\'ajout du contact.');
    }

    /**
     * Formulaire d'édition
     */
    public function edit($id)
    {
        $companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

        // Vérifier que le contact appartient à la company de l'utilisateur
        $contact = $this->contactModel->findForCompany($id, $companyId);
        if (!$contact) {
            return redirect()->to('/contacts')->with('error', 'Contact introuvable.');
        }

        return view('contacts/contact_edit', [
            'title'   => 'Modifier un contact',
            'contact' => $contact,
        ]);
    }

    /**
     * Mise à jour d'un contact
     */
    public function update($id)
    {
        $companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

        // Vérifier que le contact appartient à la company de l'utilisateur
        $contact = $this->contactModel->findForCompany($id, $companyId);
        if (!$contact) {
            return redirect()->to('/contacts')->with('error', 'Contact introuvable.');
        }

        $rules = [
            'nom'        => 'required|max_length[100]',
            'prenom'     => 'required|max_length[100]',
            'email'      => 'permit_empty|valid_email|max_length[255]',
            'telephone'  => 'permit_empty|max_length[30]',
            'adresse'    => 'permit_empty|max_length[255]',
            'entreprise' => 'permit_empty|max_length[255]',
            'type'       => 'required',
            'statut'     => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', \Config\Services::validation()->getErrors());
        }

        $data = [
            'nom'        => $this->request->getPost('nom'),
            'prenom'     => $this->request->getPost('prenom'),
            'email'      => $this->request->getPost('email'),
            'telephone'  => $this->request->getPost('telephone'),
            'adresse'    => $this->request->getPost('adresse'),
            'entreprise' => $this->request->getPost('entreprise'),
            'type'       => $this->request->getPost('type'),
            'statut'     => $this->request->getPost('statut'),
        ];

        if ($this->contactModel->update($id, $data)) {
            return redirect()->to('/contacts')->with('success', 'Contact modifié avec succès.');
        }

        return redirect()->back()->with('error', 'Erreur lors de la mise à jour.');
    }

    /**
     * Suppression d'un contact
     */
    public function delete($id)
    {
        $companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

        // Vérifier que le contact appartient à la company de l'utilisateur
        $contact = $this->contactModel->findForCompany($id, $companyId);
        if (!$contact) {
            return redirect()->to('/contacts')->with('error', 'Contact introuvable.');
        }

        if ($this->contactModel->delete($id)) {
            return redirect()->to('/contacts')->with('success', 'Contact supprimé.');
        }

        return redirect()->to('/contacts')->with('error', 'Erreur lors de la suppression.');
    }

    /**
     * Export contacts to CSV
     */
    public function exportCSV()
    {
        $companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

        $contacts = $this->contactModel->where('company_id', $companyId)->findAll();

        $filename = 'contacts_export_' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        
        // BOM for Excel UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Headers
        fputcsv($output, ['Nom', 'Prénom', 'Email', 'Téléphone', 'Adresse', 'Entreprise', 'Type', 'Statut'], ';');
        
        foreach ($contacts as $contact) {
            fputcsv($output, [
                $contact['nom'],
                $contact['prenom'],
                $contact['email'],
                $contact['telephone'],
                $contact['adresse'],
                $contact['entreprise'],
                $contact['type'],
                $contact['statut'],
            ], ';');
        }
        
        fclose($output);
        exit;
    }

    /**
     * Import contacts from CSV
     */
    public function importCSV()
    {
        $companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

        if ($this->request->getMethod() !== 'POST') {
            return view('contacts/import', ['title' => 'Importer des contacts']);
        }

        $file = $this->request->getFile('csv_file');
        
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'Fichier invalide.');
        }

        $handle = fopen($file->getTempName(), 'r');
        $header = fgetcsv($handle, 0, ';'); // Skip header
        
        $imported = 0;
        $errors = 0;

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            if (count($row) < 4) continue;
            
            $data = [
                'nom'        => $row[0] ?? '',
                'prenom'     => $row[1] ?? '',
                'email'      => $row[2] ?? '',
                'telephone'  => $row[3] ?? '',
                'adresse'    => $row[4] ?? '',
                'entreprise' => $row[5] ?? '',
                'type'       => $row[6] ?? 'client',
                'statut'     => $row[7] ?? 'actif',
                'company_id' => $companyId,
                'date_creation' => date('Y-m-d H:i:s'),
            ];

            if ($this->contactModel->insert($data)) {
                $imported++;
            } else {
                $errors++;
            }
        }

        fclose($handle);

        return redirect()->to('/contacts')->with('success', "$imported contacts importés. $errors erreurs.");
    }

    /**
     * Add note to contact
     */
    public function addNote($contactId)
    {
        $companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

        $contact = $this->contactModel->findForCompany($contactId, $companyId);
        if (!$contact) {
            return redirect()->back()->with('error', 'Contact introuvable.');
        }

        $noteModel = new \App\Models\ContactNoteModel();
        $noteModel->insert([
            'contact_id' => $contactId,
            'user_id' => session()->get('user_id'),
            'content' => $this->request->getPost('content'),
        ]);

        return redirect()->back()->with('success', 'Note ajoutée.');
    }

    /**
     * Delete note
     */
    public function deleteNote($noteId)
    {
        $companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

        $noteModel = new \App\Models\ContactNoteModel();
        $noteModel->delete($noteId);

        return redirect()->back()->with('success', 'Note supprimée.');
    }

    /**
     * Upload attachment to contact
     */
    public function uploadAttachment($contactId)
    {
        $companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

        $contact = $this->contactModel->findForCompany($contactId, $companyId);
        if (!$contact) {
            return redirect()->back()->with('error', 'Contact introuvable.');
        }

        $file = $this->request->getFile('attachment');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $uploadPath = WRITEPATH . 'uploads/contacts/' . $companyId;
            
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $file->move($uploadPath, $newName);

            $attachmentModel = new \App\Models\ContactAttachmentModel();
            $attachmentModel->insert([
                'contact_id' => $contactId,
                'filename' => $newName,
                'original_name' => $file->getClientName(),
                'path' => 'uploads/contacts/' . $companyId . '/' . $newName,
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ]);

            return redirect()->back()->with('success', 'Pièce jointe ajoutée.');
        }

        return redirect()->back()->with('error', 'Erreur lors de l\'upload.');
    }

    /**
     * Delete attachment
     */
    public function deleteAttachment($attachmentId)
    {
        $companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

        $attachmentModel = new \App\Models\ContactAttachmentModel();
        $attachment = $attachmentModel->find($attachmentId);

        if ($attachment) {
            $filePath = WRITEPATH . $attachment['path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $attachmentModel->delete($attachmentId);
        }

        return redirect()->back()->with('success', 'Pièce jointe supprimée.');
    }
}
