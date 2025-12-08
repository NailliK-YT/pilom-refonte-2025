<?php

namespace App\Controllers;

use App\Models\FactureModel;
use App\Models\ContactModel;
use App\Models\DevisModel;

class FactureController extends BaseController
{
    protected $factureModel;
    protected $contactModel;
    protected $devisModel;

    public function __construct()
    {
        $this->factureModel = new FactureModel();
        $this->contactModel = new ContactModel();
        $this->devisModel   = new DevisModel();
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
     * Liste des factures
     */
    public function index()
    {
        $companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

        $search = $this->request->getGet('search');
        $statut = $this->request->getGet('statut');

        // Filtrer par company_id pour la sécurité multi-tenant
        $factures = $this->factureModel->getWithFilters($companyId, $search, $statut);

        return view('factures/factures', [
            'title'    => 'Factures',
            'factures' => $factures,
            'search'   => $search,
            'statut'   => $statut,
            'user'     => session()->get('user'),
        ]);
    }

    /**
     * Formulaire création
     */
    public function create()
    {
        $companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

        // Récupérer uniquement les contacts et devis de la company
		$contact = $this->contactModel->getByCompany($companyId);
		$devis = $this->devisModel->getWithContact($companyId);

        return view('factures/facture_create', [
            'title'    => 'Créer une facture',
            'contacts' => $contact,
            'devis'    => $devis
        ]);
    }

    /**
     * Sauvegarde une nouvelle facture
     */
    public function store()
    {
        $companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

        $validation = \Config\Services::validation();

        $rules = [
            'numero_facture' => 'required|max_length[50]',
            'date_emission'  => 'required|valid_date',
            'date_echeance'  => 'required|valid_date',
            'montant'        => 'required|decimal',
            'statut'         => 'required',
            'contact_id'     => 'required|integer',
            'devis_id'       => 'permit_empty|integer'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Vérifier que le contact appartient à la company
        $contactId = $this->request->getPost('contact_id');
        $contact = $this->contactModel->findForCompany($contactId, $companyId);
        if (!$contact) {
            return redirect()->back()->withInput()->with('error', 'Contact invalide.');
        }

        // Calculs
        $montantTTC = floatval($this->request->getPost('montant'));
        $montantHT  = round($montantTTC / 1.20, 2);
        $montantTVA = round($montantTTC - $montantHT, 2);

        $data = [
            'numero_facture' => $this->request->getPost('numero_facture'),
            'date_emission'  => $this->request->getPost('date_emission'),
            'date_echeance'  => $this->request->getPost('date_echeance'),
            'montant_ht'     => $montantHT,
            'montant_tva'    => $montantTVA,
            'montant_ttc'    => $montantTTC,
            'statut'         => $this->request->getPost('statut'),
            'contact_id'     => $contactId,
            'id_devis'       => $this->request->getPost('id_devis'),
        ];

        $this->factureModel->insert($data);

        return redirect()->to('/factures')->with('success', 'Facture créée avec succès.');
    }

    /**
     * Formulaire Edition
     */
    public function edit($id)
    {
        $companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

        // Vérifier que la facture appartient à la company via le contact
        $facture = $this->factureModel->findForCompany($id, $companyId);
        if (!$facture) {
            return redirect()->to('/factures')->with('error', 'Facture introuvable.');
        }

        // Récupérer uniquement les contacts et devis de la company
		$contact = $this->contactModel->getByCompany($companyId);
		$devis = $this->devisModel->getWithContact($companyId);

        return view('factures/facture_edit', [
            'title'    => 'Modifier une facture',
            'facture'  => $facture,
            'contacts' => $contact,
            'devis'    => $devis,
        ]);
    }

    /**
     * Mise à jour d'une facture
     */
    public function update($id)
    {
        $companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

        // Vérifier que la facture appartient à la company
        $facture = $this->factureModel->findForCompany($id, $companyId);
        if (!$facture) {
            return redirect()->to('/factures')->with('error', 'Facture introuvable.');
        }

        $validation = \Config\Services::validation();

        $rules = [
            'numero_facture' => 'required|max_length[50]',
            'date_emission'  => 'required|valid_date',
            'date_echeance'  => 'required|valid_date',
            'montant'        => 'required|decimal',
            'statut'         => 'required',
            'contact_id'     => 'required|integer',
            'id_devis'       => 'permit_empty|integer'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Vérifier que le contact appartient à la company
        $contactId = $this->request->getPost('contact_id');
        $contact = $this->contactModel->findForCompany($contactId, $companyId);
        if (!$contact) {
            return redirect()->back()->withInput()->with('error', 'Contact invalide.');
        }

        // Calcul montant
        $montantTTC = floatval($this->request->getPost('montant'));
        $montantHT  = round($montantTTC / 1.20, 2);
        $montantTVA = round($montantTTC - $montantHT, 2);

        $data = [
            'numero_facture' => $this->request->getPost('numero_facture'),
            'date_emission'  => $this->request->getPost('date_emission'),
            'date_echeance'  => $this->request->getPost('date_echeance'),
            'montant_ht'     => $montantHT,
            'montant_tva'    => $montantTVA,
            'montant_ttc'    => $montantTTC,
            'statut'         => $this->request->getPost('statut'),
            'contact_id'     => $contactId,
            'id_devis'       => $this->request->getPost('id_devis'),
        ];

        $this->factureModel->update($id, $data);

        return redirect()->to('/factures')->with('success', 'Facture mise à jour avec succès.');
    }

    /**
     * Voir une facture
     */
    public function show($id)
    {
        $companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

        // Vérifier que la facture appartient à la company
        $facture = $this->factureModel->findForCompany($id, $companyId);
        if (!$facture) {
            return redirect()->to('/factures')->with('error', 'Facture introuvable.');
        }

        $contact = $this->contactModel->find($facture['contact_id']);

        // On réutilise la vue d'impression mais sans l'auto-print si possible, 
        // ou on crée une vue show spécifique. Pour l'instant, on utilise facture_print.
        return view('factures/facture_print', [
            'facture' => $facture,
            'contact' => $contact,
            'autoPrint' => false // On pourra utiliser cette variable dans la vue
        ]);
    }

    /**
     * Suppression
     */
    public function delete($id)
    {
        $companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

        // Vérifier que la facture appartient à la company
        $facture = $this->factureModel->findForCompany($id, $companyId);
        if (!$facture) {
            return redirect()->to('/factures')->with('error', 'Facture introuvable.');
        }

        $this->factureModel->delete($id);

        return redirect()->to('/factures')->with('success', 'Facture supprimée.');
    }

    /**
     * Envoi de la facture par email
     */
    public function send($id)
    {
        $companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

        // Vérifier que la facture appartient à la company
        $facture = $this->factureModel->findForCompany($id, $companyId);
        if (!$facture) {
            return redirect()->to('/factures')->with('error', 'Facture introuvable.');
        }

        $contact = $this->contactModel->find($facture['contact_id']);
        if (!$contact || empty($contact['email'])) {
            return redirect()->to('/factures')->with('error', 'Contact introuvable ou email manquant.');
        }

        // Simulation d'envoi d'email (à remplacer par le service Email de CI4)
        // $email = \Config\Services::email();
        // $email->setTo($contact['email']);
        // $email->setSubject("Facture #{$facture['numero_facture']}");
        // $email->setMessage("Bonjour, veuillez trouver ci-joint votre facture.");
        // if ($email->send()) { ... }

        // Pour l'instant, on simule le succès
        return redirect()->to('/factures')->with('success', "Facture envoyée à {$contact['email']} (Simulation).");
    }

    /**
     * Génération PDF (Vue imprimable)
     */
    public function pdf($id)
    {
        $companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

        // Vérifier que la facture appartient à la company
        $facture = $this->factureModel->findForCompany($id, $companyId);
        if (!$facture) {
            return redirect()->to('/factures')->with('error', 'Facture introuvable.');
        }

        $contact = $this->contactModel->find($facture['contact_id']);

        return view('factures/facture_print', [
            'facture' => $facture,
            'contact' => $contact,
            'autoPrint' => true
        ]);
    }

    /**
     * Relance par email
     */
    public function reminder($id)
    {
        $companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

        // Vérifier que la facture appartient à la company
        $facture = $this->factureModel->findForCompany($id, $companyId);
        if (!$facture) {
            return redirect()->to('/factures')->with('error', 'Facture introuvable.');
        }

        $contact = $this->contactModel->find($facture['contact_id']);
        if (!$contact || empty($contact['email'])) {
            return redirect()->to('/factures')->with('error', 'Contact introuvable ou email manquant.');
        }

        // Simulation de relance
        return redirect()->to('/factures')->with('success', "Relance envoyée à {$contact['email']} (Simulation).");
    }
}
