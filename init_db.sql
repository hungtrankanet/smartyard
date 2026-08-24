-- ============================================================
-- SMART YARD PETRO - Database Initialization & Admin Setup
-- Auto-executed on first database setup
-- ============================================================

USE `smartyard`;

-- 1. Insert or Update Default Super Admin Account
-- Email: admin@smartyard.vn / Password: SmartYardPetro@2026
INSERT INTO `users` (
    `id`, `username`, `slug`, `email`, `email_status`, `token`, 
    `password`, `role_id`, `user_type`, `status`, `created_at`, `last_seen`
) VALUES (
    1, 
    'admin', 
    'admin', 
    'admin@smartyard.vn', 
    1, 
    'token_smartyard_admin_init', 
    '$2y$10$ZowjKpKbIQLuYd9Y88JsVO9mwk7C.cy1ZYBVXPtLZOpEAwujVVO3C', 
    1, 
    'registered', 
    1, 
    NOW(), 
    NOW()
) ON DUPLICATE KEY UPDATE 
    `email` = 'admin@smartyard.vn',
    `password` = '$2y$10$ZowjKpKbIQLuYd9Y88JsVO9mwk7C.cy1ZYBVXPtLZOpEAwujVVO3C',
    `role_id` = 1,
    `status` = 1,
    `email_status` = 1;

-- 2. Update General Settings for SMART YARD PETRO
UPDATE `general_settings` SET 
    `application_name` = 'SMART YARD PETRO',
    `site_title` = 'SMART YARD PETRO - Nền Tảng Quản Lý Kho Thông Minh',
    `site_description` = 'Nền tảng quản lý trực quan hệ thống kho, sơ đồ 2D/3D, diện tích và lô hàng theo dự án.',
    `keywords` = 'smart yard, quan ly kho, so do kho 2d, 3d warehouse, quan ly dien tich, kho dau khi, petro yard',
    `copyright` = '© 2026 Smart Yard Petro. All rights reserved.',
    `mail_contact` = 'admin@smartyard.vn',
    `mail_contact_status` = 1
WHERE `id` = 1;

-- 3. Update Settings Table for Theme
UPDATE `settings` SET 
    `site_title` = 'SMART YARD PETRO - Nền Tảng Quản Lý Kho Thông Minh',
    `site_description` = 'Nền tảng quản lý trực quan hệ thống kho, sơ đồ 2D/3D, diện tích và lô hàng theo dự án.',
    `keywords` = 'smart yard, quan ly kho, so do kho 2d, 3d warehouse, quan ly dien tich, kho dau khi, petro yard',
    `contact_address` = 'Khu Công Nghiệp & Cảng Dầu Khí',
    `contact_email` = 'contact@smartyard.vn',
    `contact_phone` = '+84.28.39971199',
    `facebook_url` = 'https://smartyard.vn'
WHERE `lang_id` IN (1, 2);
