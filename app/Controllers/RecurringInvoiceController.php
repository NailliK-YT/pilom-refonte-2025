<?php

namespace App\Controllers;

use App\Models\RecurringInvoiceModel;
use App\Models\ContactModel;
use App\Models\FactureModel;

class RecurringInvoiceController extends BaseController
{
    protected $recurringModel;
    protected $contactModel;
    protected $factureModel;

    public function __construct()
    {
        $this->recurringModel = new RecurringInvoiceModel();
        $this->contactModel = new ContactModel();
        $this->factureModel = new FactureModel();
        helper(['form']);
    }

    /**
     * List all recurring invoices
     */
    public function index()
    {
        $companyId = session()->get('company_id');
        
        $recurring = $this->recurringModel
            ->select('recurring_invoices.*, contact.nom as contact_nom')
            ->join('contact', 'contact.id = recurring_invoices.contact_id')
            ->where('contact.company_id', $companyId)
            ->findAll();

        $data = [
            'title' => 'Factures Récurrentes',
            'recurring' => $recurring
        ];

        return view('recurring_invoices/index', $data);
    }

    /**
     * Create new recurring invoice
     */
    public function create()
    {
        $companyId = session()->get('company_id');
        
        if ($this->request->getMethod() === 'post') {
            return $this->store();
        }

        $contacts = $this->contactModel->where('company_id', $companyId)->findAll();

        $data = [
            'title' => 'Nouvelle Facture Récurrente',
            'contacts' => $contacts,
            'validation' => \Config\Services::validation()
        ];

        return view('recurring_invoices/create', $data);
    }

    /**
     * Store new recurring invoice
     */
    private function store()
    {
        $rules = [
            'contact_id' => 'required',
            'frequency' => 'required|in_list[monthly,quarterly,yearly]',
            'amount' => 'required|decimal',
            'description' => 'permit_empty|max_length[500]',
            'next_invoice_date' => 'required|valid_date'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'contact_id' => $this->request->getPost('contact_id'),
            'frequency' => $this->request->getPost('frequency'),
            'amount' => $this->request->getPost('amount'),
            'description' => $this->request->getPost('description'),
            'next_invoice_date' => $this->request->getPost('next_invoice_date'),
            'status' => 'active',
            'created_by' => session()->get('user_id')
        ];

        if ($this->recurringModel->insert($data)) {
            return redirect()->to('/recurring-invoices')->with('success', 'Facture récurrente créée avec succès.');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la création.');
    }

    /**
     * Edit recurring invoice
     */
    public function edit($id)
    {
        $companyId = session()->get('company_id');
        
        $recurring = $this->recurringModel
            ->select('recurring_invoices.*, contact.company_id')
            ->join('contact', 'contact.id = recurring_invoices.contact_id')
            ->where('recurring_invoices.id', $id)
            ->first();

        if (!$recurring || $recurring['company_id'] !== $companyId) {
            return redirect()->to('/recurring-invoices')->with('error', 'Facture récurrente introuvable.');
        }

        if ($this->request->getMethod() === 'post') {
            return $this->update($id);
        }

        $contacts = $this->contactModel->where('company_id', $companyId)->findAll();

        $data = [
            'title' => 'Modifier Facture Récurrente',
            'recurring' => $recurring,
            'contacts' => $contacts,
            'validation' => \Config\Services::validation()
        ];

        return view('recurring_invoices/edit', $data);
    }

    /**
     * Update recurring invoice
     */
    private function update($id)
    {
        $rules = [
            'frequency' => 'required|in_list[monthly,quarterly,yearly]',
            'amount' => 'required|decimal',
            'description' => 'permit_empty|max_length[500]',
            'next_invoice_date' => 'required|valid_date'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'frequency' => $this->request->getPost('frequency'),
            'amount' => $this->request->getPost('amount'),
            'description' => $this->request->getPost('description'),
            'next_invoice_date' => $this->request->getPost('next_invoice_date')
        ];

        if ($this->recurringModel->update($id, $data)) {
            return redirect()->to('/recurring-invoices')->with('success', 'Facture récurrente mise à jour.');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la mise à jour.');
    }

    /**
     * Pause recurring invoice
     */
    public function pause($id)
    {
        $this->recurringModel->update($id, ['status' => 'paused']);
        return redirect()->to('/recurring-invoices')->with('success', 'Facture récurrente mise en pause.');
    }

    /**
     * Resume recurring invoice
     */
    public function resume($id)
    {
        $this->recurringModel->update($id, ['status' => 'active']);
        return redirect()->to('/recurring-invoices')->with('success', 'Facture récurrente réactivée.');
    }

    /**
     * Cancel recurring invoice
     */
    public function cancel($id)
    {
        $this->recurringModel->update($id, ['status' => 'cancelled']);
        return redirect()->to('/recurring-invoices')->with('success', 'Facture récurrente annulée.');
    }
}
