<?php

namespace App\Controllers;

use App\Models\FactureModel;
use App\Models\DepenseModel;
use App\Models\ContactModel;
use App\Models\DevisModel;
use App\Models\ReglementModel;

class StatisticsController extends BaseController
{
    /**
     * Dashboard with charts and KPIs
     */
    public function index()
    {
        $companyId = session()->get('company_id');
        
        $factureModel = new FactureModel();
        $depenseModel = new DepenseModel();
        $contactModel = new ContactModel();
        $devisModel = new DevisModel();
        $reglementModel = new ReglementModel();

        // Get current year stats
        $currentYear = date('Y');
        $currentMonth = date('m');

        // Monthly revenue
        $monthlyRevenue = $this->getMonthlyRevenue($companyId, $currentYear);
        
        // Monthly expenses
        $monthlyExpenses = $this->getMonthlyExpenses($companyId, $currentYear);

        // KPIs
        $totalInvoiced = $factureModel->selectSum('montant_ttc')
            ->join('contact', 'contact.id = facture.contact_id')
            ->where('contact.company_id', $companyId)
            ->where('EXTRACT(YEAR FROM facture.date_emission)', $currentYear)
            ->first()['montant_ttc'] ?? 0;

        $totalExpenses = $depenseModel->selectSum('montant_ttc')
            ->where('company_id', $companyId)
            ->where('EXTRACT(YEAR FROM depenses.date)', $currentYear)
            ->first()['montant_ttc'] ?? 0;

        $totalContacts = $contactModel->where('company_id', $companyId)->countAllResults();
        
        $pendingInvoices = $factureModel->join('contact', 'contact.id = facture.contact_id')
            ->where('contact.company_id', $companyId)
            ->where('facture.statut', 'envoyee')
            ->countAllResults();

        $data = [
            'title' => 'Statistiques & Rapports',
            'monthlyRevenue' => $monthlyRevenue,
            'monthlyExpenses' => $monthlyExpenses,
            'totalInvoiced' => $totalInvoiced,
            'totalExpenses' => $totalExpenses,
            'margin' => $totalInvoiced - $totalExpenses,
            'totalContacts' => $totalContacts,
            'pendingInvoices' => $pendingInvoices,
            'currentYear' => $currentYear,
        ];

        return view('statistics/index', $data);
    }

    /**
     * Revenue report
     */
    public function revenue()
    {
        $companyId = session()->get('company_id');
        $year = $this->request->getGet('year') ?? date('Y');

        $data = [
            'title' => 'Rapport CA',
            'monthlyRevenue' => $this->getMonthlyRevenue($companyId, $year),
            'year' => $year,
        ];

        return view('statistics/revenue', $data);
    }

    /**
     * Expenses report
     */
    public function expenses()
    {
        $companyId = session()->get('company_id');
        $year = $this->request->getGet('year') ?? date('Y');
        
        $depenseModel = new DepenseModel();

        // By category
        $byCategory = $depenseModel->select('categories_depenses.nom as category, SUM(depenses.montant_ttc) as total')
            ->join('categories_depenses', 'categories_depenses.id = depenses.category_id', 'left')
            ->where('depenses.company_id', $companyId)
            ->where('EXTRACT(YEAR FROM depenses.date)', $year)
            ->groupBy('categories_depenses.id')
            ->findAll();

        $data = [
            'title' => 'Rapport Dépenses',
            'monthlyExpenses' => $this->getMonthlyExpenses($companyId, $year),
            'byCategory' => $byCategory,
            'year' => $year,
        ];

        return view('statistics/expenses', $data);
    }

    /**
     * Margins report
     */
    public function margins()
    {
        $companyId = session()->get('company_id');
        $year = $this->request->getGet('year') ?? date('Y');

        $revenue = $this->getMonthlyRevenue($companyId, $year);
        $expenses = $this->getMonthlyExpenses($companyId, $year);

        $margins = [];
        for ($i = 1; $i <= 12; $i++) {
            $margins[$i] = ($revenue[$i] ?? 0) - ($expenses[$i] ?? 0);
        }

        $data = [
            'title' => 'Rapport Marges',
            'monthlyRevenue' => $revenue,
            'monthlyExpenses' => $expenses,
            'monthlyMargins' => $margins,
            'year' => $year,
        ];

        return view('statistics/margins', $data);
    }

    /**
     * Get monthly revenue for a year
     */
    private function getMonthlyRevenue(string $companyId, string $year): array
    {
        $factureModel = new FactureModel();
        $results = $factureModel->select('EXTRACT(MONTH FROM facture.date_emission) as month, SUM(facture.montant_ttc) as total')
            ->join('contact', 'contact.id = facture.contact_id')
            ->where('contact.company_id', $companyId)
            ->where('EXTRACT(YEAR FROM facture.date_emission)', $year)
            ->groupBy('EXTRACT(MONTH FROM facture.date_emission)')
            ->findAll();

        $monthly = array_fill(1, 12, 0);
        foreach ($results as $row) {
            $monthly[(int)$row['month']] = (float)$row['total'];
        }
        return $monthly;
    }

    /**
     * Get monthly expenses for a year
     */
    private function getMonthlyExpenses(string $companyId, string $year): array
    {
        $depenseModel = new DepenseModel();
        $results = $depenseModel->select('EXTRACT(MONTH FROM depenses.date) as month, SUM(depenses.montant_ttc) as total')
            ->where('depenses.company_id', $companyId)
            ->where('EXTRACT(YEAR FROM depenses.date)', $year)
            ->groupBy('EXTRACT(MONTH FROM depenses.date)')
            ->findAll();

        $monthly = array_fill(1, 12, 0);
        foreach ($results as $row) {
            $monthly[(int)$row['month']] = (float)$row['total'];
        }
        return $monthly;
    }

    /**
     * Export statistics to CSV
     */
    public function exportCSV()
    {
        $companyId = session()->get('company_id');
        $year = $this->request->getGet('year') ?? date('Y');

        $revenue = $this->getMonthlyRevenue($companyId, $year);
        $expenses = $this->getMonthlyExpenses($companyId, $year);

        $filename = 'statistiques_' . $year . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($output, ['Mois', 'CA', 'Dépenses', 'Marge'], ';');
        
        $months = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        
        for ($i = 1; $i <= 12; $i++) {
            fputcsv($output, [
                $months[$i],
                $revenue[$i],
                $expenses[$i],
                $revenue[$i] - $expenses[$i],
            ], ';');
        }
        
        fclose($output);
        exit;
    }
}
