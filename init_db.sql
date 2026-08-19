-- ============================================================
-- TOP BEST GLOBAL - Database Initialization & Admin Setup
-- Auto-executed by MySQL Docker Container on first startup
-- ============================================================

USE `topbestglobal_db`;

-- 1. Insert or Update Default Super Admin Account
-- Email: admin@topbestglobal.com / Password: TopBestGlobal@2026
INSERT INTO `users` (
    `id`, `username`, `slug`, `email`, `email_status`, `token`, 
    `password`, `role_id`, `user_type`, `status`, `created_at`, `last_seen`
) VALUES (
    1, 
    'admin', 
    'admin', 
    'admin@topbestglobal.com', 
    1, 
    'token_topbestglobal_admin_init', 
    '$2y$10$ZowjKpKbIQLuYd9Y88JsVO9mwk7C.cy1ZYBVXPtLZOpEAwujVVO3C', 
    1, 
    'registered', 
    1, 
    NOW(), 
    NOW()
) ON DUPLICATE KEY UPDATE 
    `email` = 'admin@topbestglobal.com',
    `password` = '$2y$10$ZowjKpKbIQLuYd9Y88JsVO9mwk7C.cy1ZYBVXPtLZOpEAwujVVO3C',
    `role_id` = 1,
    `status` = 1,
    `email_status` = 1;

-- 2. Update General Settings for TOP BEST GLOBAL
UPDATE `general_settings` SET 
    `application_name` = 'TOP BEST GLOBAL',
    `site_title` = 'TOP BEST GLOBAL - Logistics & E-Carrier Platform',
    `site_description` = 'Nền tảng logistics số hóa & vận tải đa phương thức hàng đầu - Kết nối 500+ doanh nghiệp sản xuất, xuất nhập khẩu toàn cầu.',
    `keywords` = 'vận tải quốc tế, logistics toàn cầu, cước vận tải biển, vận tải hàng không, khai báo hải quan, fcl, lcl, xuất nhập khẩu, top best global, topbestglobal, b2b logistics, freight forwarding',
    `copyright` = '© 2026 TOP BEST GLOBAL Corporation. All rights reserved.',
    `mail_contact` = 'admin@topbestglobal.com',
    `mail_contact_status` = 1
WHERE `id` = 1;

-- 3. Update Settings Table for Theme
UPDATE `settings` SET 
    `site_title` = 'TOP BEST GLOBAL - Logistics & E-Carrier Platform',
    `site_description` = 'Nền tảng logistics số hóa & vận tải đa phương thức hàng đầu - Kết nối 500+ doanh nghiệp sản xuất, xuất nhập khẩu toàn cầu.',
    `keywords` = 'vận tải quốc tế, logistics toàn cầu, cước vận tải biển, vận tải hàng không, khai báo hải quan, fcl, lcl, xuất nhập khẩu, top best global, topbestglobal, b2b logistics, freight forwarding',
    `contact_address` = '20 Đường Hoàng Minh Giám, Phường Đúc Nhuận, TP.HCM',
    `contact_email` = 'contact@topbestglobal.com',
    `contact_phone` = '+84.28.39971199',
    `facebook_url` = 'https://facebook.com/topbestglobal'
WHERE `lang_id` IN (1, 2);

-- 4. Enable Theme suntransco as Active Theme
UPDATE `themes` SET `is_active` = 1 WHERE `theme` = 'suntransco';
UPDATE `themes` SET `is_active` = 0 WHERE `theme` != 'suntransco';

-- 5. Ensure Language defaults
UPDATE `languages` SET `status` = 1 WHERE `short_form` IN ('vi', 'en');
