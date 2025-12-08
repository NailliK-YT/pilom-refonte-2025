<?php

namespace App\Libraries;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

/**
 * Export Service
 * 
 * Handles data exports to various formats (CSV, Excel, PDF)
 */
class ExportService
{
    protected string $tempPath;

    public function __construct()
    {
        $this->tempPath = WRITEPATH . 'exports/';

        // Ensure directory exists
        if (!is_dir($this->tempPath)) {
            mkdir($this->tempPath, 0755, true);
        }
    }

    /**
     * Export expenses to CSV
     * 
     * @param array $depenses Array of expenses
     * @param string $filename Output filename
     * @return string Path to generated CSV file
     */
    public function exportToCSV(array $depenses, string $filename = 'depenses.csv'): string
    {
        $filepath = $this->tempPath . $filename;
        $handle = fopen($filepath, 'w');

        // Add BOM for Excel UTF-8 compatibility
        fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Headers
        $headers = [
            'Date',
            'Description',
            'Catégorie',
            'Fournisseur',
            'Montant HT',
            'TVA (%)',
            'Montant TVA',
            'Montant TTC',
            'Méthode de paiement',
            'Statut',
            'Créé par'
        ];

        fputcsv($handle, $headers, ';'); // Use semicolon for French Excel

        // Data rows
        foreach ($depenses as $depense) {
            $row = [
                $depense['date'],
                $depense['description'],
                $depense['categorie_nom'] ?? '',
                $depense['fournisseur_nom'] ?? '',
                number_format($depense['montant_ht'], 2, ',', ''),
                $depense['tva_rate'] ?? '',
                number_format(($depense['montant_ttc'] - $depense['montant_ht']), 2, ',', ''),
                number_format($depense['montant_ttc'], 2, ',', ''),
                get_methode_paiement_label($depense['methode_paiement']),
                ucfirst($depense['statut']),
                $depense['user_email'] ?? ''
            ];

            fputcsv($handle, $row, ';');
        }

        fclose($handle);

        return $filepath;
    }

    /**
     * Export expenses to Excel
     * 
     * @param array $depenses Array of expenses
     * @param string $filename Output filename
     * @param array $options Export options
     * @return string Path to generated Excel file
     */
    public function exportToExcel(array $depenses, string $filename = 'depenses.xlsx', array $options = []): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('Pilom')
            ->setTitle('Export Dépenses')
            ->setSubject('Dépenses')
            ->setDescription('Export des dépenses');

        // Header style
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4e51c0']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ];

        // Headers
        $headers = [
            'A1' => 'Date',
            'B1' => 'Description',
            'C1' => 'Catégorie',
            'D1' => 'Fournisseur',
            'E1' => 'Montant HT',
            'F1' => 'TVA (%)',
            'G1' => 'Montant TVA',
            'H1' => 'Montant TTC',
            'I1' => 'Méthode de paiement',
            'J1' => 'Statut',
            'K1' => 'Créé par'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
            $sheet->getStyle($cell)->applyFromArray($headerStyle);
        }

        // Auto-size columns
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Data rows
        $row = 2;
        foreach ($depenses as $depense) {
            $sheet->setCellValue('A' . $row, $depense['date']);
            $sheet->setCellValue('B' . $row, $depense['description']);
            $sheet->setCellValue('C' . $row, $depense['categorie_nom'] ?? '');
            $sheet->setCellValue('D' . $row, $depense['fournisseur_nom'] ?? '');
            $sheet->setCellValue('E' . $row, $depense['montant_ht']);
            $sheet->setCellValue('F' . $row, $depense['tva_rate'] ?? '');
            $sheet->setCellValue('G' . $row, ($depense['montant_ttc'] - $depense['montant_ht']));
            $sheet->setCellValue('H' . $row, $depense['montant_ttc']);
            $sheet->setCellValue('I' . $row, get_methode_paiement_label($depense['methode_paiement']));
            $sheet->setCellValue('J' . $row, ucfirst($depense['statut']));
            $sheet->setCellValue('K' . $row, $depense['user_email'] ?? '');

            $row++;
        }

        // Add totals row if there are expenses
        if (count($depenses) > 0) {
            $row++;
            $sheet->setCellValue('D' . $row, 'TOTAL');
            $sheet->setCellValue('E' . $row, '=SUM(E2:E' . ($row - 2) . ')');
            $sheet->setCellValue('G' . $row, '=SUM(G2:G' . ($row - 2) . ')');
            $sheet->setCellValue('H' . $row, '=SUM(H2:H' . ($row - 2) . ')');

            $sheet->getStyle('D' . $row . ':K' . $row)->getFont()->setBold(true);
        }

        // Number format for amounts
        $sheet->getStyle('E2:H' . $row)->getNumberFormat()
            ->setFormatCode('#,##0.00 €');

        // Write file
        $filepath = $this->tempPath . $filename;
        $writer = new Xlsx($spreadsheet);
        $writer->save($filepath);

        return $filepath;
    }

    /**
     * Export expenses to PDF
     * 
     * @param array $depenses Array of expenses
     * @param string $filename Output filename
     * @param array $options Export options (title, period, etc.)
     * @return string Path to generated PDF file
     */
    public function exportToPDF(array $depenses, string $filename = 'depenses.pdf', array $options = []): string
    {
        // Create new PDF document
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator('Pilom');
        $pdf->SetAuthor('Pilom');
        $pdf->SetTitle('Export Dépenses');
        $pdf->SetSubject('Dépenses');

        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Set margins
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(TRUE, 15);

        // Add a page
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('helvetica', '', 10);

        // Title
        $title = $options['title'] ?? 'Export des Dépenses';
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, $title, 0, 1, 'C');
        $pdf->Ln(5);

        // Period info if provided
        if (isset($options['period'])) {
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Cell(0, 6, 'Période: ' . $options['period'], 0, 1, 'C');
            $pdf->Ln(5);
        }

        // Table header
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor(78, 81, 192); // Primary color
        $pdf->SetTextColor(255, 255, 255);

        $pdf->Cell(25, 7, 'Date', 1, 0, 'C', 1);
        $pdf->Cell(50, 7, 'Description', 1, 0, 'C', 1);
        $pdf->Cell(30, 7, 'Catégorie', 1, 0, 'C', 1);
        $pdf->Cell(30, 7, 'Fournisseur', 1, 0, 'C', 1);
        $pdf->Cell(25, 7, 'Montant TTC', 1, 0, 'C', 1);
        $pdf->Cell(20, 7, 'Statut', 1, 1, 'C', 1);

        // Table data
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(245, 245, 245);

        $fill = false;
        $totalTTC = 0;

        foreach ($depenses as $depense) {
            $pdf->Cell(25, 6, date('d/m/Y', strtotime($depense['date'])), 1, 0, 'C', $fill);
            $pdf->Cell(50, 6, substr($depense['description'], 0, 40), 1, 0, 'L', $fill);
            $pdf->Cell(30, 6, $depense['categorie_nom'] ?? '', 1, 0, 'L', $fill);
            $pdf->Cell(30, 6, substr($depense['fournisseur_nom'] ?? '', 0, 20), 1, 0, 'L', $fill);
            $pdf->Cell(25, 6, number_format($depense['montant_ttc'], 2, ',', ' ') . ' €', 1, 0, 'R', $fill);
            $pdf->Cell(20, 6, ucfirst($depense['statut']), 1, 1, 'C', $fill);

            $totalTTC += $depense['montant_ttc'];
            $fill = !$fill;
        }

        // Total row
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(135, 7, 'TOTAL', 1, 0, 'R');
        $pdf->Cell(25, 7, number_format($totalTTC, 2, ',', ' ') . ' €', 1, 0, 'R');
        $pdf->Cell(20, 7, '', 1, 1, 'C');

        // Output file
        $filepath = $this->tempPath . $filename;
        $pdf->Output($filepath, 'F');

        return $filepath;
    }

    /**
     * Create ZIP archive of justificatifs for expense period
     * 
     * @param array $depenses Array of expenses with justificatif_path
     * @param string $filename Output filename
     * @return string|null Path to ZIP file or null on failure
     */
    public function createJustificatifsArchive(array $depenses, string $filename = 'justificatifs.zip'): ?string
    {
        $zip = new \ZipArchive();
        $zipPath = $this->tempPath . $filename;

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
            log_message('error', 'Cannot create ZIP archive: ' . $zipPath);
            return null;
        }

        $fileCount = 0;
        $uploadBasePath = WRITEPATH . 'uploads/depenses/';

        foreach ($depenses as $depense) {
            if (empty($depense['justificatif_path'])) {
                continue;
            }

            $filePath = $uploadBasePath . $depense['justificatif_path'];

            if (!file_exists($filePath)) {
                log_message('warning', 'Justificatif not found: ' . $filePath);
                continue;
            }

            // Create meaningful filename in archive
            $date = date('Y-m-d', strtotime($depense['date']));
            $category = $depense['categorie_nom'] ?? 'Sans_categorie';
            $category = $this->sanitizeFilename($category);
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
            $archiveFilename = $date . '_' . $category . '_' . $depense['id'] . '.' . $extension;

            $zip->addFile($filePath, $archiveFilename);
            $fileCount++;
        }

        $zip->close();

        if ($fileCount === 0) {
            unlink($zipPath);
            return null;
        }

        log_message('info', "Created ZIP archive with $fileCount files: " . $zipPath);
        return $zipPath;
    }

    /**
     * Sanitize filename for safe usage
     * 
     * @param string $filename Filename to sanitize
     * @return string Sanitized filename
     */
    protected function sanitizeFilename(string $filename): string
    {
        // Remove accents
        $filename = iconv('UTF-8', 'ASCII//TRANSLIT', $filename);

        // Remove special characters
        $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename);

        // Remove multiple underscores
        $filename = preg_replace('/_+/', '_', $filename);

        return trim($filename, '_');
    }

    /**
     * Clean up old export files
     * 
     * @param int $hours Delete files older than X hours
     * @return int Number of deleted files
     */
    public function cleanupOldExports(int $hours = 24): int
    {
        $count = 0;
        $cutoffTime = time() - ($hours * 3600);

        $files = glob($this->tempPath . '*');

        foreach ($files as $file) {
            if (is_file($file) && filemtime($file) < $cutoffTime) {
                try {
                    unlink($file);
                    $count++;
                } catch (\Exception $e) {
                    log_message('error', 'Export cleanup error: ' . $e->getMessage());
                }
            }
        }

        log_message('info', "Cleaned up $count old export files");
        return $count;
    }
}
