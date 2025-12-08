<?php

namespace App\Libraries;

use App\Models\NotificationModel;
use App\Models\NotificationPreferencesModel;
use Config\Services;

class NotificationService
{
    protected NotificationModel $notificationModel;
    protected NotificationPreferencesModel $preferencesModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
        $this->preferencesModel = new NotificationPreferencesModel();
    }

    /**
     * Send notification based on user preferences
     */
    public function send(
        string $userId, 
        string $type, 
        string $title, 
        string $message, 
        array $options = []
    ): array {
        $results = [
            'inapp' => false,
            'email' => false,
            'push' => false
        ];

        $preferences = $this->preferencesModel->getByUserId($userId);

        // Always try in-app if preferences allow
        if (!$preferences || $preferences['inapp_notifications']) {
            $results['inapp'] = $this->notificationModel->createNotification(
                $userId,
                $type,
                $title,
                $message,
                $options
            );
        }

        // Send email if preferences allow
        if ($this->shouldSendEmail($preferences, $type)) {
            $results['email'] = $this->sendEmail($userId, $title, $message, $options);
        }

        // Push notifications (placeholder - would need Firebase/OneSignal integration)
        if ($preferences && $preferences['push_notifications']) {
            $results['push'] = $this->sendPush($userId, $title, $message);
        }

        return $results;
    }

    /**
     * Check if email should be sent based on preferences and type
     */
    protected function shouldSendEmail(?array $preferences, string $type): bool
    {
        if (!$preferences) {
            return false;
        }

        if (!$preferences['email_notifications']) {
            return false;
        }

        return match ($type) {
            'invoice' => (bool) $preferences['email_invoices'],
            'quote' => (bool) $preferences['email_quotes'],
            'payment' => (bool) $preferences['email_payments'],
            default => true
        };
    }

    /**
     * Send email notification
     */
    protected function sendEmail(string $userId, string $title, string $message, array $options = []): bool
    {
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);

        if (!$user || empty($user['email'])) {
            return false;
        }

        try {
            $email = Services::email();
            
            $email->setFrom('noreply@pilom.fr', 'Pilom');
            $email->setTo($user['email']);
            $email->setSubject($title);
            
            // Use email template if available
            $emailContent = $this->renderEmailTemplate($title, $message, $options);
            $email->setMessage($emailContent);
            $email->setMailType('html');

            return $email->send();
        } catch (\Exception $e) {
            log_message('error', 'Email notification failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Render email template
     */
    protected function renderEmailTemplate(string $title, string $message, array $options = []): string
    {
        $link = $options['link'] ?? null;
        $baseUrl = base_url();

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: 'Inter', Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #4E51C0; color: white; padding: 20px; text-align: center; }
                .content { padding: 30px 20px; background: #f8fafc; }
                .message { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
                .button { display: inline-block; background: #4E51C0; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin-top: 15px; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1 style='margin: 0;'>Pilom</h1>
                </div>
                <div class='content'>
                    <div class='message'>
                        <h2 style='color: #1f2937; margin-top: 0;'>{$title}</h2>
                        <p>{$message}</p>
                        " . ($link ? "<a href='{$baseUrl}{$link}' class='button'>Voir les détails</a>" : "") . "
                    </div>
                </div>
                <div class='footer'>
                    <p>Cet email a été envoyé par Pilom. Vous pouvez modifier vos préférences de notification dans votre compte.</p>
                    <p><a href='{$baseUrl}notifications/preferences'>Gérer mes notifications</a></p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Send push notification (placeholder)
     */
    protected function sendPush(string $userId, string $title, string $message): bool
    {
        // TODO: Integrate with Firebase Cloud Messaging or OneSignal
        // This is a placeholder that would need actual push notification service integration
        log_message('info', "Push notification would be sent to user {$userId}: {$title}");
        return false;
    }

    /**
     * Send invoice created notification
     */
    public function notifyInvoiceCreated(string $userId, string $invoiceNumber, ?string $companyId = null): array
    {
        return $this->send(
            $userId,
            'invoice',
            'Nouvelle facture créée',
            "La facture {$invoiceNumber} a été créée avec succès.",
            [
                'company_id' => $companyId,
                'link' => '/factures',
                'priority' => 'normal'
            ]
        );
    }

    /**
     * Send payment received notification
     */
    public function notifyPaymentReceived(string $userId, float $amount, string $reference, ?string $companyId = null): array
    {
        return $this->send(
            $userId,
            'payment',
            'Paiement reçu',
            "Un paiement de " . number_format($amount, 2, ',', ' ') . " € a été reçu ({$reference}).",
            [
                'company_id' => $companyId,
                'link' => '/reglements',
                'priority' => 'high'
            ]
        );
    }

    /**
     * Send treasury alert notification
     */
    public function notifyTreasuryAlert(string $userId, string $alertName, float $balance, ?string $companyId = null): array
    {
        return $this->send(
            $userId,
            'alert',
            'Alerte trésorerie',
            "L'alerte \"{$alertName}\" a été déclenchée. Solde actuel : " . number_format($balance, 2, ',', ' ') . " €",
            [
                'company_id' => $companyId,
                'link' => '/treasury',
                'priority' => 'urgent'
            ]
        );
    }
}
