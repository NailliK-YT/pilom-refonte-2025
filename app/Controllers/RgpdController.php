<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\AuditLogModel;
use App\Models\UserCompanyModel;

/**
 * Controller for RGPD-compliant user data export
 */
class RgpdController extends BaseController
{
    protected UserModel $userModel;
    protected AuditLogModel $auditModel;
    protected UserCompanyModel $userCompanyModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->auditModel = new AuditLogModel();
        $this->userCompanyModel = new UserCompanyModel();
    }

    /**
     * Export all user data as JSON (RGPD Article 20 - Right to data portability)
     */
    public function exportMyData()
    {
        $session = session();
        $userId = $session->get('user_id');

        if (!$userId) {
            return redirect()->to('/login');
        }

        // Get user profile
        $user = $this->userModel->find($userId);
        unset($user['password_hash'], $user['verification_token'], $user['password_reset_token']);

        // Get user companies
        $companies = $this->userCompanyModel->getUserCompanies($userId);

        // Get user activity (last 1000 entries)
        $activity = $this->auditModel->builder()
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit(1000)
            ->get()
            ->getResultArray();

        // Clean activity data
        foreach ($activity as &$log) {
            unset($log['id']); // Internal ID not relevant
        }

        // Prepare export data
        $exportData = [
            'export_date' => date('c'),
            'export_type' => 'RGPD_FULL_EXPORT',
            'user_profile' => $user,
            'company_memberships' => $companies,
            'activity_log' => $activity,
        ];

        // Log the export action
        $this->auditModel->log('rgpd.data_export', [
            'entity_type' => 'user',
            'entity_id' => $userId,
        ]);

        // Generate filename
        $filename = 'pilom_data_export_' . $user['email'] . '_' . date('Y-m-d') . '.json';
        $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $filename);

        // Send as download
        return $this->response
            ->setHeader('Content-Type', 'application/json; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody(json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Request account deletion (RGPD Article 17 - Right to erasure)
     */
    public function requestDeletion()
    {
        $session = session();
        $userId = $session->get('user_id');

        if (!$userId) {
            return redirect()->to('/login');
        }

        // Get user
        $user = $this->userModel->find($userId);

        // Log the deletion request
        $this->auditModel->log('rgpd.deletion_request', [
            'entity_type' => 'user',
            'entity_id' => $userId,
        ]);

        // For now, we just mark as pending deletion (actual deletion should be handled by admin)
        // In production, this would trigger an email to admin and set a 30-day countdown
        $this->userModel->update($userId, [
            'status' => 'pending_deletion',
        ]);

        return redirect()->to('/account')
            ->with('success', 'Votre demande de suppression a été enregistrée. Un administrateur vous contactera sous 30 jours.');
    }

    /**
     * View RGPD info page
     */
    public function index()
    {
        $session = session();
        $userId = $session->get('user_id');

        if (!$userId) {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($userId);

        // Get activity count
        $activityCount = $this->auditModel->builder()
            ->where('user_id', $userId)
            ->countAllResults();

        // Get companies count
        $companies = $this->userCompanyModel->getUserCompanies($userId);

        return view('account/rgpd', [
            'user' => $user,
            'activityCount' => $activityCount,
            'companiesCount' => count($companies),
        ]);
    }
}
