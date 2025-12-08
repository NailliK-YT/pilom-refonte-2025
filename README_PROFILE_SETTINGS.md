# User Profile & Company Settings Management - Technical Documentation

## Overview

This module implements comprehensive user profile and company settings management for the Pilom application, covering features F12 and F13 from the specifications. It provides users with complete control over their personal information, company settings, notification preferences, and account security.

## Features Implemented

### 1. **User Profile Management**
- Personal information (first name, last name, phone)
- Profile photo upload and management
- Email display (read-only)
- Locale and timezone preferences

### 2. **Password Management**
- Secure password change with old password verification
- Real-time password strength indicator
- Password requirements validation (8+ chars, uppercase, lowercase, number)

### 3. **Company Settings**
- Company information (name, address, contact details)
- Logo upload and management
- Legal identifiers (SIRET, SIREN, VAT number)
- Legal mentions and terms & conditions

### 4. **Invoicing Configuration**
- Default VAT rate selection
- Invoice numbering (prefix + auto-increment)
- Banking information (IBAN, BIC)

### 5. **Account Security**
- Login history tracking (IP, device, browser, status)
- Security overview dashboard
- Account deletion with 30-day grace period

### 6. **Notification Preferences**
- Email notifications (general, invoices, quotes, payments, marketing)
- Push notifications toggle
- In-app notifications toggle

## Database Schema

### New Tables

#### `user_profiles`
- `id` (UUID, PK)
- `user_id` (UUID, FK → users.id, unique)
- `first_name`, `last_name`, `phone`
- `profile_photo` (file path)
- `locale`, `timezone`
- Timestamps

#### `company_settings`
- `id` (UUID, PK)
- `company_id` (UUID, FK → companies.id, unique)
- Address fields (`address`, `postal_code`, `city`, `country`)
- Contact (`phone`, `email`, `website`)
- Legal (`siret`, `siren`, `vat_number`)
- `logo` (file path)
- `legal_mentions`, `terms_conditions` (TEXT)
- Banking (`iban`, `bic`)
- Invoicing (`invoice_prefix`, `invoice_next_number`, `default_vat_rate`)
- Timestamps

#### `notification_preferences`
- `id` (UUID, PK)
- `user_id` (UUID, FK → users.id, unique)
- Boolean fields for each notification type
- Timestamps

#### `login_history`
- `id` (UUID, PK)
- `user_id` (UUID, FK → users.id)
- `ip_address`, `user_agent`
- `login_at` (TIMESTAMP)
- `success` (BOOLEAN)

#### `account_deletion_requests`
- `id` (UUID, PK)
- `user_id` (UUID, FK → users.id)
- `requested_at`, `scheduled_deletion_at`
- `reason` (TEXT)
- `status` (pending/cancelled/completed)
- Timestamps

## File Structure

```
app/
├── Controllers/
│   ├── ProfileController.php
│   ├── CompanySettingsController.php
│   ├── AccountController.php
│   └── NotificationController.php
├── Models/
│   ├── UserProfileModel.php
│   ├── CompanySettingsModel.php
│   ├── NotificationPreferencesModel.php
│   ├── LoginHistoryModel.php
│   └── AccountDeletionModel.php
├── Views/
│   ├── profile/
│   │   ├── layout.php
│   │   ├── index.php
│   │   └── password.php
│   ├── settings/
│   │   ├── layout.php
│   │   ├── company_info.php
│   │   ├── legal.php
│   │   └── invoicing.php
│   ├── account/
│   │   ├── security.php
│   │   ├── login_history.php
│   │   └── deletion.php
│   └── notifications/
│       └── preferences.php
├── Helpers/
│   └── FileUploadHelper.php
├── Database/
│   ├── Migrations/
│   │   ├── 2025-12-05-100001_CreateUserProfilesTable.php
│   │   ├── 2025-12-05-100002_CreateCompanySettingsTable.php
│   │   ├── 2025-12-05-100003_CreateNotificationPreferencesTable.php
│   │   ├── 2025-12-05-100004_CreateLoginHistoryTable.php
│   │   └── 2025-12-05-100005_CreateAccountDeletionRequestsTable.php
│   └── Seeds/
│       ├── UserProfilesSeeder.php
│       ├── CompanySettingsSeeder.php
│       └── NotificationPreferencesSeeder.php
└── Language/
    └── fr/
        ├── Profile.php
        ├── Settings.php
        ├── Account.php
        └── Notifications.php

public/
├── css/
│   └── profile.css
└── js/
    ├── profile.js
    └── settings.js
```

## Installation & Setup

### 1. Run Migrations

```bash
php spark migrate
```

This will create the 5 new tables.

### 2. Run Seeders (Optional)

```bash
php spark db:seed UserProfilesSeeder
php spark db:seed CompanySettingsSeeder
php spark db:seed NotificationPreferencesSeeder
```

### 3. Create Upload Directories

Ensure these directories exist and are writable:

```bash
writable/uploads/profiles/
writable/uploads/logos/
```

PowerShell:
```powershell
New-Item -ItemType Directory -Path "writable\uploads\profiles" -Force
New-Item -ItemType Directory -Path "writable\uploads\logos" -Force
```

## Routes

All routes require authentication (`auth` filter).

### Profile Management
- `GET /profile` - Profile form
- `POST /profile/update` - Update profile
- `GET /profile/password` - Password change form
- `POST /profile/change-password` - Change password
- `POST /profile/upload-photo` - Upload photo (AJAX)
- `POST /profile/delete-photo` - Delete photo (AJAX)

### Company Settings
- `GET /settings/company` - Company info form
- `POST /settings/company/update` - Update company info
- `GET /settings/company/legal` - Legal info form
- `POST /settings/company/update-legal` - Update legal info
- `GET /settings/company/invoicing` - Invoicing form
- `POST /settings/company/update-invoicing` - Update invoicing
- `POST /settings/company/upload-logo` - Upload logo (AJAX)
- `POST /settings/company/delete-logo` - Delete logo (AJAX)

### Account Management
- `GET /account/security` - Security overview
- `GET /account/login-history` - Login history
- `GET /account/deletion` - Account deletion
- `POST /account/request-deletion` - Request deletion
- `POST /account/cancel-deletion` - Cancel deletion

### Notifications
- `GET /notifications/preferences` - Preferences form
- `POST /notifications/update-preferences` - Update preferences

## Security Features

### CSRF Protection
All forms include CSRF tokens via `csrf_field()` helper.

### Input Validation
- Server-side validation using CodeIgniter validation rules
- Real-time client-side validation with JavaScript

### File Upload Security
- File type validation (image/* only)
- Size limit: 2MB
- File extension check (jpg, jpeg, png, webp)
- Automatic image resizing

### Password Security
- Old password verification required
- Minimum 8 characters
- Strength indicator (weak/medium/strong)
- Bcrypt hashing (handled by CodeIgniter)

### Permission Checks
- Users can only edit their own profiles
- Company settings restricted to company members
- Login history only accessible by account owner

## Validation Rules

### SIRET Validation
- Exactly 14 numeric digits
- Luhn algorithm checksum validation
- Implemented in both backend (PHP) and frontend (JavaScript)

### IBAN Validation
- Length: 15-34 characters
- French IBAN: exactly 27 characters
- Format check (2 letters + digits)
- Implemented in `CompanySettingsModel::validateIban()`

### Email Validation
- Standard RFC email validation
- Uniqueness check (for user email)

### Phone Formatting
- Automatic formatting (French: XX XX XX XX XX)
- Implemented in `UserProfileModel::formatPhone()`

## File Upload Specifications

### Profile Photos
- Max size: 2MB
- Allowed formats: JPG, JPEG, PNG, WebP
- Processed dimensions: 300x300px (square crop)
- Storage: `writable/uploads/profiles/`
- Naming: `profile_{userId}_{timestamp}.{ext}`

### Company Logos
- Max size: 2MB
- Allowed formats: JPG, JPEG, PNG, WebP
- Processed dimensions: Max 500x200px (maintain aspect ratio)
- Storage: `writable/uploads/logos/`
- Naming: `logo_{companyId}_{timestamp}.{ext}`

## Account Deletion Process

1. **Request**: User submits deletion request with optional reason
2. **Grace Period**: 30 days from request date
3. **Cancellation**: User can cancel anytime during grace period
4. **Processing**: After 30 days, account is permanently deleted via cron job
5. **Data Cleanup**: User, profile, preferences, login history all deleted

### Cron Job Setup (Future)

Create a scheduled task to process pending deletions:

```php
// Add to a cron controller
$deletionModel = new AccountDeletionModel();
$requests = $deletionModel->getRequestsReadyForDeletion();

foreach ($requests as $request) {
    $deletionModel->processDeletion($request['id']);
}
```

## Testing

### Manual Testing Checklist

1. **Profile Management**
   - [ ] Update first name, last name, phone
   - [ ] Upload profile photo (valid image)
   - [ ] Try uploading invalid file (PDF, > 2MB)
   - [ ] Delete profile photo
   - [ ] Change locale and timezone

2. **Password Change**
   - [ ] Enter wrong current password (should fail)
   - [ ] Enter weak password (check indicator)
   - [ ] Enter strong password (verify checkmarks)
   - [ ] Confirm password mismatch (should fail)
   - [ ] Successfully change password

3. **Company Settings**
   - [ ] Update company name
   - [ ] Upload logo
   - [ ] Fill in address fields
   - [ ] Enter valid SIRET (check validation)
   - [ ] Enter invalid SIRET (should show error)
   - [ ] Update legal mentions  and CGV

4. **Invoicing**
   - [ ] Set default VAT rate
   - [ ] Configure invoice prefix
   - [ ] Set next invoice number
   - [ ] Enter IBAN and BIC
   - [ ] Verify invoice preview updates

5. **Account Security**
   - [ ] View login history
   - [ ] Request account deletion
   - [ ] Cancel deletion request

6. **Notifications**
   - [ ] Toggle various notification types
   - [ ] Save and verify persistence

### Security Testing

- [ ] Submit form without CSRF token (should fail)
- [ ] Try to edit another user's profile (should deny)
- [ ] Inject `<script>` tags in text fields (should escape)
- [ ] Try SQL injection in inputs (should sanitize)
- [ ] Upload PHP file as image (should reject)

## Troubleshooting

### Upload folder permissions
If uploads fail, ensure folders are writable:
```bash
chmod 755 writable/uploads/profiles/
chmod 755 writable/uploads/logos/
```

### Image processing not working
Ensure GD or ImageMagick is installed and enabled in PHP.

### CSRF validation failures
Clear browser cache and session data.

## Future Enhancements

- [ ] Two-factor authentication (2FA)
- [ ] Session management (view/revoke active sessions)
- [ ] Export personal data (GDPR compliance)
- [ ] Activity audit log
- [ ] Email notifications for security events
- [ ] API endpoints for mobile apps

## Support

For issues or questions, refer to the main Pilom documentation or contact the development team.
