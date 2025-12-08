<?php

namespace App\Controllers;

use App\Models\DevisModel;
use App\Models\ContactModel;
use App\Models\FactureModel;

class DevisController extends BaseController
{
    protected $devisModel;
    protected $contactModel;
	protected $factureModel;

    public function __construct()
    {
        $this->devisModel   = new DevisModel();
        $this->contactModel = new ContactModel();
		$this->factureModel = new FactureModel();
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
     * Liste des devis avec filtres
     */
    public function index()
    {
        $companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

        $search = $this->request->getGet('search');
        $statut = $this->request->getGet('statut');

        // Filtrer par company_id pour la sécurité multi-tenant
        $devis = $this->devisModel->getWithFilters($companyId, $search, $statut);

        $data = [
            'title' => 'Devis',
            'devis' => $devis,
            'search'=> $search,
            'statut'=> $statut,
            'user'  => session()->get('user'),
        ];

        return view('devis/devis', $data);
    }

    /**
     * Formulaire création d'un nouveau devis
     */
    public function create()
    {
        $companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

        // Récupérer uniquement les contacts de la company
        $contacts = $this->contactModel->getByCompany($companyId);

        return view('devis/devis_create', [
            'title' => 'Créer un devis',
            'contacts' => $contacts
        ]);
    }

    /**
     * Sauvegarde un nouveau devis
     */
	public function store()
	{
		$companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

		$validation = \Config\Services::validation();

		$rules = [
			'numero_devis'   => 'required|max_length[50]',
			'date_emission'  => 'required|valid_date',
			'date_validite'  => 'required|valid_date',
			'montant'        => 'required|decimal',
			'statut'         => 'required',
			'contact_id'     => 'required|integer',
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

		// Calculs automatiques
		$montantTTC = floatval($this->request->getPost('montant'));
		$montantHT  = round($montantTTC / 1.20, 2);
		$montantTVA = round($montantTTC - $montantHT, 2);

		$data = [
			'numero_devis'   => $this->request->getPost('numero_devis'),
			'date_emission'  => $this->request->getPost('date_emission'),
			'date_validite'  => $this->request->getPost('date_validite'),
			'montant_ht'     => $montantHT,
			'montant_tva'    => $montantTVA,
			'montant_ttc'    => $montantTTC,
			'statut'         => $this->request->getPost('statut'),
			'contact_id'     => $contactId,
			'company_id'     => $companyId,  // AJOUT: Associer le devis à la company
		];

		$this->devisModel->insert($data);

		return redirect()->to('/devis')->with('success', 'Devis créé avec succès.');
	}


    /**
     * Formulaire édition d'un devis
     */
    public function edit($id)
    {
        $companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

        // Vérifier que le devis appartient à la company
        $devis = $this->devisModel->findForCompany($id, $companyId);
        if (!$devis) {
            return redirect()->to('/devis')->with('error', 'Devis introuvable.');
        }

        // Récupérer uniquement les contacts de la company
        $contacts = $this->contactModel->getByCompany($companyId);

        return view('devis/devis_edit', [
            'title' => 'Modifier un devis',
            'devis' => $devis,
            'contacts' => $contacts
        ]);
    }

    /**
     * Mise à jour d'un devis
     */
	public function update($id)
	{
		$companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

		// Vérifier que le devis appartient à la company
		$devis = $this->devisModel->findForCompany($id, $companyId);
		if (!$devis) {
			return redirect()->to('/devis')->with('error', 'Devis introuvable.');
		}

		$validation = \Config\Services::validation();

		// Règles : seul le montant TTC est fourni
		$rules = [
			'numero_devis'   => 'required|max_length[50]',
			'date_emission'  => 'required|valid_date',
			'date_validite'  => 'required|valid_date',
			'montant'        => 'required|decimal',
			'statut'         => 'required',
			'contact_id'     => 'required|integer',
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

		// Récupération des valeurs du formulaire
		$numeroDevis   = $this->request->getPost('numero_devis');
		$dateEmission  = $this->request->getPost('date_emission');
		$dateValidite  = $this->request->getPost('date_validite');
		$statut        = $this->request->getPost('statut');

		// Calcul du montant HT et TVA (supposons TVA 20%)
		$montantTTC = floatval($this->request->getPost('montant'));
		$montantHT  = round($montantTTC / 1.20, 2);
		$montantTVA = round($montantTTC - $montantHT, 2);

		$data = [
			'numero_devis'  => $numeroDevis,
			'date_emission' => $dateEmission,
			'date_validite' => $dateValidite,
			'montant_ht'    => $montantHT,
			'montant_tva'   => $montantTVA,
			'montant_ttc'   => $montantTTC,
			'statut'        => $statut,
			'contact_id'    => $contactId,
		];

		$this->devisModel->update($id, $data);

		return redirect()->to('/devis')->with('success', 'Devis mis à jour avec succès.');
	}

    /**
     * Suppression d'un devis
     */
    public function delete($id)
    {
        $companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

        // Vérifier que le devis appartient à la company
        $devis = $this->devisModel->findForCompany($id, $companyId);
        if (!$devis) {
            return redirect()->to('/devis')->with('error', 'Devis introuvable.');
        }

        $this->devisModel->delete($id);

        return redirect()->to('/devis')->with('success', 'Devis supprimé avec succès.');
    }

	/**
	 * Convertir un devis en facture
	 */
	public function convertirEnFacture($id)
	{
		$companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

		// Récupérer le devis en vérifiant qu'il appartient à la company
		$devis = $this->devisModel->findForCompany($id, $companyId);
		if (!$devis) {
			return redirect()->to('/devis')->with('error', 'Devis introuvable.');
		}

		// Vérifier si une facture existe déjà pour ce devis
		$factureExistante = $this->factureModel->where('id_devis', $id)->first();
		if ($factureExistante) {
			return redirect()->to('/devis')->with('error', 'Une facture existe déjà pour ce devis.');
		}

		// Génération d'un numéro de facture automatique
		$today = date('Ymd');
		$random = str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
		$numeroFacture = "FAC-$today-$random";

		// Calculs HT/TVA/TTC (ici repris directement du devis)
		$montantHT  = floatval($devis['montant_ht']);
		$montantTVA = floatval($devis['montant_tva']);
		$montantTTC = floatval($devis['montant_ttc']);

		// Préparer les données pour la facture
		$dataFacture = [
			'numero_facture' => $numeroFacture,
			'date_emission'  => date('Y-m-d'), // date du jour
			'date_echeance'  => date('Y-m-d', strtotime('+30 days')), // échéance +30 jours
			'montant_ht'     => $montantHT,
			'montant_tva'    => $montantTVA,
			'montant_ttc'    => $montantTTC,
			'statut'         => 'brouillon', // statut initial
			'contact_id'     => $devis['contact_id'],
			'id_devis'       => $devis['id'],
			'company_id'     => $companyId,  // AJOUT: Associer la facture à la company
		];

		// Insérer la facture
		$this->factureModel->insert($dataFacture);

		return redirect()->to('/devis')->with('success', 'Le devis a été converti en facture avec succès.');
	}

}
