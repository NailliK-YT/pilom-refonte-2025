-- Test queries for Profile & Settings implementation

-- Test 1: Check tables exist and count records
SELECT 'user_profiles' as table_name, COUNT(*) as record_count FROM user_profiles
UNION ALL
SELECT 'company_settings', COUNT(*) FROM company_settings
UNION ALL
SELECT 'notification_preferences', COUNT(*) FROM notification_preferences
UNION ALL
SELECT 'login_history', COUNT(*) FROM login_history
UNION ALL
SELECT 'account_deletion_requests', COUNT(*) FROM account_deletion_requests;

-- Test 2: Sample user profile data
SELECT 
    up.id,
    up.first_name,
    up.last_name,
    up.phone,
    u.email,
    up.locale,
    up.timezone
FROM user_profiles up
JOIN users u ON u.id = up.user_id
LIMIT 5;

-- Test 3: Sample company settings
SELECT 
    cs.id,
    c.name as company_name,
    cs.siret,
    cs.vat_number,
    cs.default_vat_rate,
    cs.invoice_prefix,
    cs.invoice_next_number
FROM company_settings cs
JOIN companies c ON c.id = cs.company_id
LIMIT 5;

-- Test 4: Sample notification preferences
SELECT 
    np.id,
    u.email,
    np.email_notifications,
    np.email_invoices,
    np.push_notifications
FROM notification_preferences np
JOIN users u ON u.id = np.user_id
LIMIT 5;

-- Test 5: Check foreign keys are working
SELECT 
    tc.table_name, 
    kcu.column_name,
    ccu.table_name AS foreign_table_name,
    ccu.column_name AS foreign_column_name
FROM information_schema.table_constraints AS tc 
JOIN information_schema.key_column_usage AS kcu
    ON tc.constraint_name = kcu.constraint_name
    AND tc.table_schema = kcu.table_schema
JOIN information_schema.constraint_column_usage AS ccu
    ON ccu.constraint_name = tc.constraint_name
    AND ccu.table_schema = tc.table_schema
WHERE tc.constraint_type = 'FOREIGN KEY' 
    AND tc.table_name IN ('user_profiles', 'company_settings', 'notification_preferences', 'login_history', 'account_deletion_requests')
ORDER BY tc.table_name;
