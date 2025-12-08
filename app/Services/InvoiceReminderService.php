<?php

namespace App\Services;

use App\Models\FactureModel;
use App\Models\NotificationModel;

class InvoiceReminderService
{
    protected $factureModel;
    protected $notificationModel;

    public function __construct()
    {
        $this->factureModel = new FactureModel();
        $this->notificationModel = new NotificationModel();
    }

    /**
     * Vérifie les factures en retard et envoie des notifications
     * 
     * @return int Nombre de rappels envoyés
     */
    public function sendReminders(): int
    {
        $overdueInvoices = $this->factureModel->where('statut', 'envoyee')
                                              ->where('date_echeance <', date('Y-m-d'))
                                              ->findAll();

        $count = 0;
        foreach ($overdueInvoices as $invoice) {
            // Logique pour envoyer une notification ou un email
            // Pour l'instant, on crée juste une notification système
            $this->notificationModel->insert([
                'user_id' => $invoice['contact_id'], // Supposons que le contact est lié à un user, sinon à adapter
                'type' => 'invoice_overdue',
                'message' => "La facture {$invoice['numero_facture']} est en retard de paiement.",
                'read_status' => false
            ]);
            
            // On pourrait aussi mettre à jour le statut de la facture à 'en_retard'
            // $this->factureModel->update($invoice['id'], ['statut' => 'en_retard']);
            
            $count++;
        }

        return $count;
    }
    
    /**
     * Calcule le montant total avec pénalités de retard
     * 
     * @param array $invoice Données de la facture
     * @return float Nouveau montant total
     */
    public function calculateLatePenalty(array $invoice): float
    {
        if ($invoice['penalite_retard_percent'] > 0 && $invoice['date_echeance'] < date('Y-m-d')) {
            $penalty = $invoice['montant_ttc'] * ($invoice['penalite_retard_percent'] / 100);
            return $invoice['montant_ttc'] + $penalty;
        }
        return $invoice['montant_ttc'];
    }
}
