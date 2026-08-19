-- ============================================================
-- Smart Member Management Module - Database Setup
-- Chạy: mysql -u topbestglobal_user -p topbestglobal_db < migrate_members.sql
-- ============================================================

-- 1. Bảng Ngành Nghề (industry_types)
CREATE TABLE IF NOT EXISTS `industry_types` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `name_slug` VARCHAR(255) NOT NULL,
  `icon` VARCHAR(100) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `sort_order` INT(11) DEFAULT 0,
  `created_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name_slug` (`name_slug`),
  KEY `sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed 15 ngành nghề
INSERT INTO `industry_types` (`name`, `name_slug`, `icon`, `description`, `sort_order`, `created_at`) VALUES
('Xuất Nhập Khẩu & Thương Mại Quốc Tế', 'xuat-nhap-khau-thuong-mai-quoc-te', 'fa fa-globe', 'Hoạt động xuất nhập khẩu hàng hóa, thương mại và mậu dịch quốc tế', 1, NOW()),
('Vận Tải & Logistics', 'van-tai-logistics', 'fa fa-truck', 'Vận tải đường bộ, đường biển, hàng không, kho bãi và giao nhận', 2, NOW()),
('Sản Xuất & Chế Biến', 'san-xuat-che-bien', 'fa fa-industry', 'Nhà máy, xưởng chế biến, gia công và sản xuất hàng công nghiệp', 3, NOW()),
('Phân Phối & Bán Lẻ', 'phan-phoi-ban-le', 'fa fa-shopping-cart', 'Hệ thống đại lý phân phối, siêu thị, chuỗi cửa hàng bán lẻ', 4, NOW()),
('Công Nghệ & Phần Mềm', 'cong-nghe-phan-mem', 'fa fa-laptop', 'Giải pháp chuyển đổi số, phần mềm quản trị, IT và viễn thông', 5, NOW()),
('Tài Chính & Ngân Hàng', 'tai-chinh-ngan-hang', 'fa fa-university', 'Dịch vụ ngân hàng, bảo hiểm, tài chính và thanh toán quốc tế', 6, NOW()),
('Bất Động Sản & Xây Dựng', 'bat-dong-san-xay-dung', 'fa fa-building', 'Bất động sản công nghiệp, kho bãi xây dựng, dự án hạ tầng', 7, NOW()),
('Thực Phẩm & Đồ Uống', 'thuc-pham-do-uong', 'fa fa-cutlery', 'F&B, chế biến thực phẩm, nông sản đóng gói và đồ uống', 8, NOW()),
('Y Tế & Dược Phẩm', 'y-te-duoc-pham', 'fa fa-medkit', 'Thiết bị y tế, dược phẩm, dịch vụ chăm sóc sức khỏe', 9, NOW()),
('Giáo Dục & Đào Tạo', 'giao-duc-dao-tao', 'fa fa-graduation-cap', 'Trường học, trung tâm đào tạo nghiệp vụ logistics & quản lý', 10, NOW()),
('Dịch Vụ Chuyên Nghiệp (Luật, Kế Toán, Tư Vấn)', 'dich-vu-chuyen-nghiep-luat-ke-toan-tu-van', 'fa fa-briefcase', 'Tư vấn pháp lý hải quan, kiểm toán thuế, tư vấn doanh nghiệp', 11, NOW()),
('Nông Nghiệp & Thủy Sản', 'nong-nghiep-thuy-san', 'fa fa-leaf', 'Nuôi trồng, đánh bắt thủy hải sản, chế biến nông sản xuất khẩu', 12, NOW()),
('Năng Lượng & Môi Trường', 'nang-luong-moi-truong', 'fa fa-bolt', 'Năng lượng tái tạo, xử lý chất thải, giải pháp xanh', 13, NOW()),
('Du Lịch & Khách Sạn', 'du-lich-khach-san', 'fa fa-plane', 'Lữ hành quốc tế, chuỗi khách sạn, nghỉ dưỡng và sự kiện', 14, NOW()),
('Khác', 'khac', 'fa fa-ellipsis-h', 'Các ngành nghề kinh doanh và dịch vụ thương mại khác', 15, NOW());

-- 2. Bảng Hội Viên (members)
CREATE TABLE IF NOT EXISTS `members` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_name` VARCHAR(255) NOT NULL,
  `tax_code` VARCHAR(50) DEFAULT NULL,
  `address` VARCHAR(255) DEFAULT NULL,
  `city` VARCHAR(100) DEFAULT NULL,
  `website` VARCHAR(255) DEFAULT NULL,
  `fanpage` VARCHAR(255) DEFAULT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `representative_name` VARCHAR(150) DEFAULT NULL,
  `position` VARCHAR(100) DEFAULT NULL,
  `industry_type_id` INT(11) UNSIGNED DEFAULT NULL,
  `member_type` ENUM('prospect','member','partner') DEFAULT 'member',
  `status` TINYINT(1) DEFAULT 1,
  `verify_status` ENUM('pending','verified','unverified','failed') DEFAULT 'pending',
  `last_verified_at` DATETIME DEFAULT NULL,
  `next_verify_at` DATETIME DEFAULT NULL,
  `note` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `industry_type_id` (`industry_type_id`),
  KEY `verify_status` (`verify_status`),
  KEY `member_type` (`member_type`),
  KEY `status` (`status`),
  KEY `next_verify_at` (`next_verify_at`),
  KEY `company_name` (`company_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Bảng Ảnh Visit Card (member_cards)
CREATE TABLE IF NOT EXISTS `member_cards` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` INT(11) UNSIGNED NOT NULL,
  `file_path` VARCHAR(500) DEFAULT NULL,
  `side` ENUM('front','back','single') DEFAULT 'single',
  `ocr_raw` TEXT DEFAULT NULL,
  `ocr_parsed` JSON DEFAULT NULL,
  `ocr_status` ENUM('pending','done','failed') DEFAULT 'pending',
  `created_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  CONSTRAINT `fk_member_cards_member` FOREIGN KEY (`member_id`) REFERENCES `members`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Bảng Lịch Sử Xác Minh (member_verify_logs)
CREATE TABLE IF NOT EXISTS `member_verify_logs` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` INT(11) UNSIGNED NOT NULL,
  `check_type` ENUM('google_maps','fanpage','manual') DEFAULT 'manual',
  `result` VARCHAR(50) DEFAULT NULL,
  `detail` TEXT DEFAULT NULL,
  `checked_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  CONSTRAINT `fk_member_verify_logs_member` FOREIGN KEY (`member_id`) REFERENCES `members`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Suntransco Member Management Module v2.0 - Upgrade Schema
-- ============================================================

-- 5. Nâng cấp bảng members: Thêm cột đa ngôn ngữ & metadata
ALTER TABLE `members`
  ADD COLUMN `company_name_en` VARCHAR(255) DEFAULT NULL AFTER `company_name`,
  ADD COLUMN `company_name_local` VARCHAR(255) DEFAULT NULL AFTER `company_name_en`,
  ADD COLUMN `detected_language` VARCHAR(10) DEFAULT 'vi' AFTER `company_name_local`,
  ADD COLUMN `metadata` JSON DEFAULT NULL AFTER `note`,
  ADD KEY `idx_members_company_en` (`company_name_en`),
  ADD KEY `idx_members_company_local` (`company_name_local`),
  ADD KEY `idx_members_tax_code` (`tax_code`),
  ADD KEY `idx_members_detected_lang` (`detected_language`);

-- 6. Bảng Người Liên Hệ Doanh Nghiệp (member_contacts)
CREATE TABLE IF NOT EXISTS `member_contacts` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` INT(11) UNSIGNED NOT NULL,
  `full_name` VARCHAR(255) NOT NULL,
  `full_name_en` VARCHAR(255) DEFAULT NULL,
  `full_name_local` VARCHAR(255) DEFAULT NULL,
  `position` VARCHAR(150) DEFAULT NULL,
  `position_en` VARCHAR(150) DEFAULT NULL,
  `department` VARCHAR(150) DEFAULT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `phone_2` VARCHAR(50) DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `email_2` VARCHAR(100) DEFAULT NULL,
  `is_primary` TINYINT(1) DEFAULT 0,
  `metadata` JSON DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  KEY `full_name` (`full_name`),
  KEY `full_name_en` (`full_name_en`),
  KEY `is_primary` (`is_primary`),
  KEY `phone` (`phone`),
  KEY `email` (`email`),
  KEY `idx_contacts_company_primary` (`company_id`, `is_primary`),
  CONSTRAINT `fk_contacts_company` FOREIGN KEY (`company_id`) REFERENCES `members`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Bảng Chi Nhánh & Văn Phòng (member_branches)
CREATE TABLE IF NOT EXISTS `member_branches` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` INT(11) UNSIGNED NOT NULL,
  `branch_name` VARCHAR(255) NOT NULL,
  `country` VARCHAR(100) DEFAULT NULL,
  `city` VARCHAR(100) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `is_headquarters` TINYINT(1) DEFAULT 0,
  `metadata` JSON DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  KEY `country` (`country`),
  KEY `city` (`city`),
  KEY `is_headquarters` (`is_headquarters`),
  KEY `idx_branches_company_hq` (`company_id`, `is_headquarters`),
  CONSTRAINT `fk_branches_company` FOREIGN KEY (`company_id`) REFERENCES `members`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Cập nhật bảng member_cards: Thêm liên kết contact_id
ALTER TABLE `member_cards`
  ADD COLUMN `contact_id` INT(11) UNSIGNED DEFAULT NULL AFTER `member_id`,
  ADD KEY `contact_id` (`contact_id`),
  ADD CONSTRAINT `fk_member_cards_contact` FOREIGN KEY (`contact_id`) REFERENCES `member_contacts`(`id`) ON DELETE SET NULL;

-- 9. Data Migration Script: Chuyển dữ liệu v1 sang member_contacts & member_branches
INSERT INTO `member_contacts` (`company_id`, `full_name`, `position`, `phone`, `email`, `is_primary`, `created_at`, `updated_at`)
SELECT 
  `id` AS `company_id`,
  COALESCE(NULLIF(TRIM(`representative_name`), ''), CONCAT(`company_name`, ' Contact')) AS `full_name`,
  `position`,
  `phone`,
  `email`,
  1 AS `is_primary`,
  COALESCE(`created_at`, NOW()),
  COALESCE(`updated_at`, NOW())
FROM `members`
WHERE (`representative_name` IS NOT NULL AND TRIM(`representative_name`) != '')
   OR (`phone` IS NOT NULL AND TRIM(`phone`) != '')
   OR (`email` IS NOT NULL AND TRIM(`email`) != '');

-- Gán contact_id cho các thẻ hiện có
UPDATE `member_cards` mc
JOIN `member_contacts` mct ON mct.company_id = mc.member_id AND mct.is_primary = 1
SET mc.contact_id = mct.id
WHERE mc.contact_id IS NULL;

-- Tạo Trụ sở chính trong member_branches từ địa chỉ cũ
INSERT INTO `member_branches` (`company_id`, `branch_name`, `country`, `city`, `address`, `phone`, `email`, `is_headquarters`, `created_at`, `updated_at`)
SELECT 
  `id` AS `company_id`,
  'Trụ sở chính' AS `branch_name`,
  'Vietnam' AS `country`,
  `city`,
  `address`,
  `phone`,
  `email`,
  1 AS `is_headquarters`,
  COALESCE(`created_at`, NOW()),
  COALESCE(`updated_at`, NOW())
FROM `members`
WHERE (`address` IS NOT NULL AND TRIM(`address`) != '')
   OR (`city` IS NOT NULL AND TRIM(`city`) != '');

-- 12. Bảng phục vụ Member Portal & Đặc quyền đối tác
CREATE TABLE IF NOT EXISTS `member_otps` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(150) NOT NULL,
  `otp_code` VARCHAR(10) NOT NULL,
  `action` VARCHAR(50) DEFAULT 'register',
  `expires_at` DATETIME NOT NULL,
  `is_used` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `email_action` (`email`, `action`),
  KEY `otp_code` (`otp_code`),
  KEY `expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `member_posts` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` INT(11) UNSIGNED NOT NULL,
  `user_id` INT(11) UNSIGNED DEFAULT NULL,
  `title` VARCHAR(255) NOT NULL,
  `title_slug` VARCHAR(255) NOT NULL,
  `summary` TEXT DEFAULT NULL,
  `content` LONGTEXT DEFAULT NULL,
  `image_default` VARCHAR(500) DEFAULT NULL,
  `status` ENUM('pending','approved','rejected') DEFAULT 'pending',
  `reject_reason` TEXT DEFAULT NULL,
  `views_count` INT(11) DEFAULT 0,
  `approved_at` DATETIME DEFAULT NULL,
  `approved_by` INT(11) UNSIGNED DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `member_post_unique` (`member_id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  KEY `title_slug` (`title_slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `member_profile_views` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `viewed_member_id` INT(11) UNSIGNED NOT NULL,
  `viewer_user_id` INT(11) UNSIGNED DEFAULT NULL,
  `viewer_member_id` INT(11) UNSIGNED DEFAULT NULL,
  `ip_address` VARCHAR(50) DEFAULT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `viewed_member_id` (`viewed_member_id`),
  KEY `viewer_member_id` (`viewer_member_id`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `member_messages` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sender_user_id` INT(11) UNSIGNED DEFAULT NULL,
  `sender_member_id` INT(11) UNSIGNED DEFAULT NULL,
  `receiver_member_id` INT(11) UNSIGNED NOT NULL,
  `sender_name` VARCHAR(150) DEFAULT NULL,
  `sender_company` VARCHAR(255) DEFAULT NULL,
  `sender_phone` VARCHAR(50) DEFAULT NULL,
  `sender_email` VARCHAR(150) DEFAULT NULL,
  `subject` VARCHAR(255) DEFAULT NULL,
  `message` TEXT NOT NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `receiver_member_id` (`receiver_member_id`),
  KEY `sender_member_id` (`sender_member_id`),
  KEY `is_read` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bổ sung member_id vào bảng users nếu chưa có
SET @dbname = DATABASE();
SET @tablename = 'users';
SET @columnname = 'member_id';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  'ALTER TABLE `users` ADD COLUMN `member_id` INT(11) UNSIGNED DEFAULT NULL AFTER `id`, ADD KEY `member_id` (`member_id`)'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- DONE! Member Portal tables setup complete.
