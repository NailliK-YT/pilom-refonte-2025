<?php

namespace App\Controllers;

use App\Models\ReglementModel;
use App\Models\FactureModel;
use App\Models\TreasuryModel;
use App\Models\ContactModel;
use App\Libraries\NotificationService;

class ReglementController extends BaseController
{
    protected $reglementModel;
    protected $factureModel;
    protected $treasuryModel;
	protected $contactModel;
    protected $notificationService;

    public function __construct()
    {
        $this->reglementModel = new ReglementModel();
        $this->factureModel   = new FactureModel();
        $this->treasuryModel  = new TreasuryModel();
		$this->contactModel   = new ContactModel();
        $this->notificationService = new NotificationService();
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
     * Liste des règlements
     */
    public function index()
    {
        $companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

        $search = $this->request->getGet('search');
        $modePaiement = $this->request->getGet('mode');

        // Filtrer par company_id pour la sécurité multi-tenant
        $reglements = $this->reglementModel->getWithFilters($companyId, $search, $modePaiement);

        return view('reglements/reglements', [
            'title'      => 'Règlements',
            'reglements' => $reglements,
            'search'     => $search,
            'mode'       => $modePaiement,
            'user'       => session()->get('user'),
        ]);
    }

    /**
     * Formulaire création d'un règlement
     */
    public function create()
    {
        $companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

        // Récupérer uniquement les factures de la company
        $factures = $this->factureModel->getWithContact($companyId);

        return view('reglements/reglement_create', [
            'title'    => 'Ajouter un règlement',
            'factures' => $factures,
        ]);
    }

    /**
     * Sauvegarde un nouveau règlement
     */
    public function store()
    {
        $companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

        $validation = \Config\Services::validation();

        $rules = [
            'facture_id'     => 'required|integer',
            'date_reglement' => 'required|valid_date',
            'montant'        => 'required|decimal',
            'mode_paiement'  => 'required|in_list[espèces,chèque,virement,CB]',
            'reference'      => 'permit_empty|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Vérifier que la facture appartient à la company
        $factureId = $this->request->getPost('facture_id');
        $facture = $this->factureModel->findForCompany($factureId, $companyId);
        if (!$facture) {
            return redirect()->back()->withInput()->with('error', 'Facture invalide.');
        }

        $data = [
            'facture_id'     => $factureId,
            'date_reglement' => $this->request->getPost('date_reglement'),
            'montant'        => $this->request->getPost('montant'),
            'mode_paiement'  => $this->request->getPost('mode_paiement'),
            'reference'      => $this->request->getPost('reference'),
        ];

        $reglementId = $this->reglementModel->insert($data);

        if ($reglementId) {
            // Ajouter à la trésorerie
            $this->treasuryModel->addFromInvoice(
                $companyId,
                (float) $data['montant'],
                $reglementId, // Using reglement ID as reference
                "Règlement facture " . ($facture['numero'] ?? $factureId),
                $data['date_reglement']
            );

            // Envoyer notification
            $userId = session()->get('user_id');
            $this->notificationService->notifyPaymentReceived(
                $userId,
                (float) $data['montant'],
                "Facture " . ($facture['numero'] ?? $factureId),
                $companyId
            );

            // Vérifier les alertes de trésorerie
            $alertModel = new \App\Models\TreasuryAlertModel();
            $alertModel->checkAlerts($companyId, $this->treasuryModel->getCurrentBalance($companyId));
        }

        return redirect()->to('/reglements')->with('success', 'Règlement ajouté avec succès.');
    }

    /**
     * Formulaire édition d'un règlement
     */
    public function edit($id)
    {
        $companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

        // Vérifier que le règlement appartient à la company
        $reglement = $this->reglementModel->findForCompany($id, $companyId);
        if (!$reglement) {
            return redirect()->to('/reglements')->with('error', 'Règlement introuvable.');
        }

        // Récupérer uniquement les factures de la company
        $factures = $this->factureModel->getWithContact($companyId);

        return view('reglements/reglement_edit', [
            'title'     => 'Modifier un règlement',
            'reglement' => $reglement,
            'factures'  => $factures,
        ]);
    }

    /**
     * Mise à jour d'un règlement
     */
    public function update($id)
    {
        $companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

        // Vérifier que le règlement appartient à la company
        $reglement = $this->reglementModel->findForCompany($id, $companyId);
        if (!$reglement) {
            return redirect()->to('/reglements')->with('error', 'Règlement introuvable.');
        }

        $validation = \Config\Services::validation();

        $rules = [
            'facture_id'     => 'required|integer',
            'date_reglement' => 'required|valid_date',
            'montant'        => 'required|decimal',
            'mode_paiement'  => 'required|in_list[espèces,chèque,virement,CB]',
            'reference'      => 'permit_empty|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Vérifier que la nouvelle facture appartient à la company
        $factureId = $this->request->getPost('facture_id');
        $facture = $this->factureModel->findForCompany($factureId, $companyId);
        if (!$facture) {
            return redirect()->back()->withInput()->with('error', 'Facture invalide.');
        }

        $data = [
            'facture_id'     => $factureId,
            'date_reglement' => $this->request->getPost('date_reglement'),
            'montant'        => $this->request->getPost('montant'),
            'mode_paiement'  => $this->request->getPost('mode_paiement'),
            'reference'      => $this->request->getPost('reference'),
        ];

        $this->reglementModel->update($id, $data);

        return redirect()->to('/reglements')->with('success', 'Règlement mis à jour avec succès.');
    }

    /**
     * Suppression d'un règlement
     */
    public function delete($id)
    {
        $companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

        // Vérifier que le règlement appartient à la company
        $reglement = $this->reglementModel->findForCompany($id, $companyId);
        if (!$reglement) {
            return redirect()->to('/reglements')->with('error', 'Règlement introuvable.');
        }

        $this->reglementModel->delete($id);

        return redirect()->to('/reglements')->with('success', 'Règlement supprimé avec succès.');
    }

	/**
     * Génération recu (Vue imprimable)
     */
    public function recu($id)
    {
        $companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

        // Vérifier que le règlement appartient à la company
        $reglement = $this->reglementModel->findForCompany($id, $companyId);
        if (!$reglement) {
            return redirect()->to('/reglements')->with('error', 'Règlement introuvable.');
        }

        $facture = $this->factureModel->find($reglement['facture_id']);
		$contact = $this->contactModel->find($facture['contact_id']);

        return view('reglements/reglement_print', [
            'reglement' => $reglement,
            'facture' => $facture,
			'contact' => $contact,
            'autoPrint' => true
        ]);
    }


	/**
     * Export Bancaire (CSV)
     */
	public function export()
	{
        $companyId = $this->checkAuthAndGetCompanyId();
        if (!$companyId) return;

		$data = $this->reglementModel->getForBankExport($companyId);

		// Construction du CSV
		$filename = 'export_bancaire_' . date('Y-m-d_H-i-s') . '.csv';
		$csv = fopen('php://temp', 'r+');

		// En-têtes CSV
		fputcsv($csv, [
			'Date',
			'Montant',
			'Mode de paiement',
			'Référence',
			'N° Facture',
			'Client',
			'Entreprise'
		], ';');

		// Lignes du CSV
		foreach ($data as $row) {
			fputcsv($csv, [
				$row['date_reglement'],
				number_format($row['montant'], 2, ',', ''),
				$row['mode_paiement'],
				$row['reference'],
				$row['numero_facture'],
				$row['prenom'] . ' ' . $row['nom'],
				$row['entreprise'],
			], ';');
		}

		rewind($csv);
		$output = stream_get_contents($csv);
		fclose($csv);

		// Envoi du fichier
		return $this->response
			->setHeader('Content-Type', 'text/csv')
			->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
			->setBody($output);
	}

}
