-- ============================================================
-- TOP BEST GLOBAL Awards System - High Concurrency DB Schema
-- Designed for 10,000+ Concurrent Requests & Atomic Operations
-- ============================================================

USE `topbestglobal_db`;

-- 1. Seasons
CREATE TABLE IF NOT EXISTS `tb_award_seasons` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `theme_year` INT NOT NULL DEFAULT 2026,
    `description` TEXT NULL,
    `banner_image` VARCHAR(255) NULL,
    `nomination_start_at` DATETIME NULL,
    `nomination_end_at` DATETIME NULL,
    `voting_start_at` DATETIME NULL,
    `voting_end_at` DATETIME NULL,
    `gala_date` DATETIME NULL,
    `status` VARCHAR(50) NOT NULL DEFAULT 'draft',
    `is_active` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    KEY `idx_status_active` (`status`, `is_active`),
    KEY `idx_theme_year` (`theme_year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Categories
CREATE TABLE IF NOT EXISTS `tb_award_categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `season_id` INT UNSIGNED NOT NULL DEFAULT 1,
    `parent_id` INT UNSIGNED NOT NULL DEFAULT 0,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `industry_sector` VARCHAR(100) NULL,
    `description` TEXT NULL,
    `icon` VARCHAR(255) NULL,
    `order_num` INT NOT NULL DEFAULT 0,
    `jury_weight` DECIMAL(5,2) NOT NULL DEFAULT 70.00,
    `public_weight` DECIMAL(5,2) NOT NULL DEFAULT 30.00,
    `status` VARCHAR(50) NOT NULL DEFAULT 'active',
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    KEY `idx_season_status_order` (`season_id`, `status`, `order_num`),
    KEY `idx_slug` (`slug`),
    KEY `idx_industry_sector` (`industry_sector`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Nomination Candidates
CREATE TABLE IF NOT EXISTS `tb_nomination_candidates` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `season_id` INT UNSIGNED NOT NULL DEFAULT 1,
    `category_id` INT UNSIGNED NOT NULL,
    `candidate_code` VARCHAR(64) NOT NULL UNIQUE,
    `name` VARCHAR(255) NOT NULL,
    `organization_name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `candidate_type` VARCHAR(50) NOT NULL DEFAULT 'enterprise',
    `avatar` VARCHAR(255) NULL,
    `cover_image` VARCHAR(255) NULL,
    `bio_summary` TEXT NULL,
    `dossier_content` LONGTEXT NULL,
    `tax_code` VARCHAR(50) NULL,
    `contact_person` VARCHAR(150) NULL,
    `contact_email` VARCHAR(150) NULL,
    `contact_phone` VARCHAR(50) NULL,
    `website` VARCHAR(255) NULL,
    `stage` VARCHAR(50) NOT NULL DEFAULT 'preliminary',
    `public_votes_count` BIGINT UNSIGNED NOT NULL DEFAULT 0,
    `jury_score_avg` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
    `composite_score` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
    `final_rank` INT NOT NULL DEFAULT 0,
    `award_title` VARCHAR(255) NULL,
    `certificate_serial` VARCHAR(100) NULL UNIQUE,
    `digital_badge_url` VARCHAR(255) NULL,
    `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
    `status` VARCHAR(50) NOT NULL DEFAULT 'approved',
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    KEY `idx_season_cat_stage_status` (`season_id`, `category_id`, `stage`, `status`),
    KEY `idx_cat_composite` (`category_id`, `composite_score`),
    KEY `idx_cat_votes` (`category_id`, `public_votes_count`),
    KEY `idx_stage_featured` (`stage`, `is_featured`),
    KEY `idx_slug` (`slug`),
    KEY `idx_certificate_serial` (`certificate_serial`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Voting OTPs
CREATE TABLE IF NOT EXISTS `tb_voting_otps` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL,
    `otp_code` VARCHAR(10) NOT NULL,
    `candidate_id` INT UNSIGNED NOT NULL,
    `token` VARCHAR(100) NOT NULL UNIQUE,
    `ip_address` VARCHAR(45) NOT NULL,
    `user_agent` TEXT NULL,
    `device_fingerprint` VARCHAR(64) NULL,
    `is_verified` TINYINT(1) NOT NULL DEFAULT 0,
    `verified_at` DATETIME NULL,
    `expires_at` DATETIME NOT NULL,
    `created_at` DATETIME NULL,
    KEY `idx_email_candidate_verified` (`email`, `candidate_id`, `is_verified`),
    KEY `idx_ip_created` (`ip_address`, `created_at`),
    KEY `idx_fp_created` (`device_fingerprint`, `created_at`),
    KEY `idx_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Voting Audit Logs
CREATE TABLE IF NOT EXISTS `tb_voting_audit_logs` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `candidate_id` INT UNSIGNED NOT NULL,
    `season_id` INT UNSIGNED NOT NULL DEFAULT 1,
    `category_id` INT UNSIGNED NOT NULL,
    `voter_email` VARCHAR(255) NOT NULL,
    `otp_id` BIGINT UNSIGNED NULL,
    `ip_address` VARCHAR(45) NOT NULL,
    `device_fingerprint` VARCHAR(64) NULL,
    `user_agent` TEXT NULL,
    `risk_score` INT NOT NULL DEFAULT 0,
    `verification_status` VARCHAR(50) NOT NULL DEFAULT 'verified',
    `integrity_hash` VARCHAR(64) NOT NULL,
    `created_at` DATETIME NOT NULL,
    KEY `idx_candidate_created` (`candidate_id`, `created_at`),
    KEY `idx_voter_candidate` (`voter_email`, `candidate_id`),
    KEY `idx_season_category` (`season_id`, `category_id`),
    KEY `idx_ip_created` (`ip_address`, `created_at`),
    KEY `idx_fp_created` (`device_fingerprint`, `created_at`),
    KEY `idx_integrity_hash` (`integrity_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Jury Evaluations
CREATE TABLE IF NOT EXISTS `tb_jury_evaluations` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `candidate_id` INT UNSIGNED NOT NULL,
    `season_id` INT UNSIGNED NOT NULL DEFAULT 1,
    `category_id` INT UNSIGNED NOT NULL,
    `jury_user_id` INT UNSIGNED NOT NULL,
    `criteria_1_score` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `criteria_2_score` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `criteria_3_score` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `criteria_4_score` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `total_score` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
    `notes` TEXT NULL,
    `is_submitted` TINYINT(1) NOT NULL DEFAULT 0,
    `submitted_at` DATETIME NULL,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    KEY `idx_candidate_jury` (`candidate_id`, `jury_user_id`),
    KEY `idx_season_category` (`season_id`, `category_id`),
    KEY `idx_jury_submitted` (`jury_user_id`, `is_submitted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default Initial Season & Sample Categories
INSERT INTO `tb_award_seasons` (`id`, `title`, `slug`, `theme_year`, `description`, `status`, `is_active`, `created_at`, `updated_at`)
VALUES (1, 'TOP BEST GLOBAL AWARDS 2026', 'top-best-global-awards-2026', 2026, 'Chương trình vinh danh Thương hiệu, Doanh nghiệp & Lãnh đạo tiêu biểu Quốc gia 2026', 'voting', 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`), `status` = VALUES(`status`), `is_active` = VALUES(`is_active`);

INSERT INTO `tb_award_categories` (`id`, `season_id`, `name`, `slug`, `industry_sector`, `description`, `icon`, `order_num`, `jury_weight`, `public_weight`, `status`, `created_at`, `updated_at`)
VALUES 
(1, 1, 'Top Thương Hiệu Công Nghệ & Chuyển Đổi Số Xuất Sắc', 'thuong-hieu-cong-nghe-chuyen-doi-so', 'Technology', 'Vinh danh các doanh nghiệp tiên phong phát triển giải pháp số và trí tuệ nhân tạo', 'fa fa-microchip', 1, 70.00, 30.00, 'active', NOW(), NOW()),
(2, 1, 'Top Doanh Nghiệp Phát Triển Bền Vững & Tiên Phong ESG', 'doanh-nghiep-phat-trien-ben-vung-esg', 'ESG', 'Tôn vinh các đơn vị tiên phong bảo vệ môi trường, quản trị minh bạch và trách nhiệm xã hội', 'fa fa-leaf', 2, 70.00, 30.00, 'active', NOW(), NOW()),
(3, 1, 'Top Thương Hiệu Y Tế, Dược Phẩm & Chăm Sóc Sức Khỏe', 'thuong-hieu-y-te-duoc-pham', 'Healthcare', 'Ghi nhận đóng góp đột phá trong nghiên cứu y khoa, chăm sóc sức khỏe cộng đồng', 'fa fa-heartbeat', 3, 70.00, 30.00, 'active', NOW(), NOW()),
(4, 1, 'Top Thương Hiệu Giáo Dục & Đào Tạo Tiêu Biểu', 'thuong-hieu-giao-duc-dao-tao', 'Education', 'Vinh danh các tổ chức giáo dục kiến tạo nguồn nhân lực chất lượng cao', 'fa fa-graduation-cap', 4, 70.00, 30.00, 'active', NOW(), NOW()),
(5, 1, 'Top Lãnh Đạo Đổi Mới Sáng Tạo Của Năm', 'lanh-dao-doi-moi-sang-tao', 'Leadership', 'Giải thưởng cá nhân dành cho CEO, Nhà sáng lập xuất sắc dẫn dắt tổ chức đột phá', 'fa fa-user-tie', 5, 70.00, 30.00, 'active', NOW(), NOW())
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `industry_sector` = VALUES(`industry_sector`);
