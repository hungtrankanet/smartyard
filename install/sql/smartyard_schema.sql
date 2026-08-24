-- ============================================================
-- SMART YARD PETRO — High Concurrency Database Schema (Phase 1)
-- Optimized for 10,000+ concurrent requests, RBAC Scope & Immutable Transactions
-- ============================================================

CREATE TABLE IF NOT EXISTS `ci_sessions` (
    `id` varchar(128) NOT NULL,
    `ip_address` varchar(45) NOT NULL,
    `timestamp` int(10) unsigned DEFAULT 0 NOT NULL,
    `data` blob NOT NULL,
    PRIMARY KEY (`id`, `ip_address`),
    KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `smartyard_regions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) NOT NULL UNIQUE,
    `name` VARCHAR(150) NOT NULL,
    `description` TEXT NULL,
    `map_layout_json` LONGTEXT NULL,
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_region_code` (`code`),
    INDEX `idx_region_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `smartyard_warehouses` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `region_id` INT NOT NULL,
    `code` VARCHAR(50) NOT NULL UNIQUE,
    `name` VARCHAR(150) NOT NULL,
    `total_area` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `allocated_area` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `used_area` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `available_area` DECIMAL(12,2) GENERATED ALWAYS AS (`allocated_area` - `used_area`) STORED,
    `image_3d_url` VARCHAR(255) NULL,
    `map_pos_x` INT NOT NULL DEFAULT 100,
    `map_pos_y` INT NOT NULL DEFAULT 100,
    `map_width` INT NOT NULL DEFAULT 140,
    `map_height` INT NOT NULL DEFAULT 90,
    `status` ENUM('active', 'maintenance', 'inactive') DEFAULT 'active',
    `threshold_low` DECIMAL(5,2) DEFAULT 30.00,
    `threshold_med` DECIMAL(5,2) DEFAULT 60.00,
    `threshold_high` DECIMAL(5,2) DEFAULT 80.00,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_wh_region` (`region_id`),
    INDEX `idx_wh_code` (`code`),
    INDEX `idx_wh_status` (`status`),
    INDEX `idx_wh_area` (`used_area`, `allocated_area`),
    CONSTRAINT `fk_wh_region` FOREIGN KEY (`region_id`) REFERENCES `smartyard_regions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `smartyard_user_scopes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `warehouse_id` INT NOT NULL,
    `max_allocated_area` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `can_view` TINYINT(1) NOT NULL DEFAULT 1,
    `can_import` TINYINT(1) NOT NULL DEFAULT 0,
    `can_export` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_user_warehouse` (`user_id`, `warehouse_id`),
    INDEX `idx_scope_user` (`user_id`),
    INDEX `idx_scope_warehouse` (`warehouse_id`),
    CONSTRAINT `fk_scope_warehouse` FOREIGN KEY (`warehouse_id`) REFERENCES `smartyard_warehouses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `smartyard_projects` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `project_code` VARCHAR(50) NOT NULL UNIQUE,
    `project_name` VARCHAR(200) NOT NULL,
    `description` TEXT NULL,
    `status` ENUM('active', 'completed', 'hold') DEFAULT 'active',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_proj_code` (`project_code`),
    INDEX `idx_proj_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `smartyard_lots` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `warehouse_id` INT NOT NULL,
    `project_id` INT NOT NULL,
    `lot_code` VARCHAR(80) NOT NULL UNIQUE,
    `item_name` VARCHAR(255) NOT NULL,
    `initial_area` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `remaining_area` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `status` ENUM('STORED', 'PARTIAL', 'EXPORTED') DEFAULT 'STORED',
    `imported_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `notes` TEXT NULL,
    `file_attachment` VARCHAR(255) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_lot_warehouse` (`warehouse_id`),
    INDEX `idx_lot_project` (`project_id`),
    INDEX `idx_lot_code` (`lot_code`),
    INDEX `idx_lot_status` (`status`),
    CONSTRAINT `fk_lot_warehouse` FOREIGN KEY (`warehouse_id`) REFERENCES `smartyard_warehouses`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_lot_project` FOREIGN KEY (`project_id`) REFERENCES `smartyard_projects`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `smartyard_transactions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `warehouse_id` INT NOT NULL,
    `lot_id` INT NOT NULL,
    `project_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `transaction_type` ENUM('IMPORT', 'EXPORT', 'ADJUSTMENT') NOT NULL,
    `area` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `warehouse_used_before` DECIMAL(12,2) NOT NULL,
    `warehouse_used_after` DECIMAL(12,2) NOT NULL,
    `lot_remaining_before` DECIMAL(12,2) NOT NULL,
    `lot_remaining_after` DECIMAL(12,2) NOT NULL,
    `note` TEXT NULL,
    `ip_address` VARCHAR(45) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_tx_wh` (`warehouse_id`),
    INDEX `idx_tx_lot` (`lot_id`),
    INDEX `idx_tx_proj` (`project_id`),
    INDEX `idx_tx_user` (`user_id`),
    INDEX `idx_tx_type` (`transaction_type`),
    INDEX `idx_tx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `smartyard_audit_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `action` VARCHAR(80) NOT NULL,
    `object_type` VARCHAR(80) NOT NULL,
    `object_id` VARCHAR(80) NOT NULL,
    `before_data` LONGTEXT NULL,
    `after_data` LONGTEXT NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` VARCHAR(255) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_audit_user` (`user_id`),
    INDEX `idx_audit_action` (`action`),
    INDEX `idx_audit_object` (`object_type`, `object_id`),
    INDEX `idx_audit_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `smartyard_ai_conversations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `query_text` TEXT NOT NULL,
    `response_text` LONGTEXT NOT NULL,
    `warehouses_checked_json` TEXT NULL,
    `is_scope_violation` TINYINT(1) DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_ai_user` (`user_id`),
    INDEX `idx_ai_violation` (`is_scope_violation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `smartyard_config` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `config_key` VARCHAR(100) NOT NULL UNIQUE,
    `config_value` TEXT NOT NULL,
    `description` VARCHAR(255) NULL,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_cfg_key` (`config_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- INITIAL SEED DATA FOR DEMO & TESTING
-- ============================================================

INSERT INTO `smartyard_config` (`config_key`, `config_value`, `description`) VALUES
('threshold_low', '30', 'Ngưỡng % sử dụng mức Thấp (Xanh)'),
('threshold_med', '60', 'Ngưỡng % sử dụng mức Trung bình (Vàng)'),
('threshold_high', '80', 'Ngưỡng % sử dụng mức Cao (Cam)'),
('allow_over_allocation', '0', 'Cho phép nhập vượt diện tích (0: Cấm, 1: Cảnh báo)')
ON DUPLICATE KEY UPDATE `config_value` = VALUES(`config_value`);

INSERT INTO `smartyard_regions` (`id`, `code`, `name`, `description`) VALUES
(1, 'REG-BAC', 'Khu vực Miền Bắc', 'Trung tâm logistics Đình Vũ - Hải Phòng'),
(2, 'REG-TRUNG', 'Khu vực Miền Trung', 'Tổng kho Cảng Đà Nẵng & Dung Quất'),
(3, 'REG-NAM', 'Khu vực Miền Nam', 'Cụm kho Cát Lái & Cái Mép - Thị Vải')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

INSERT INTO `smartyard_warehouses` (`id`, `region_id`, `code`, `name`, `total_area`, `allocated_area`, `used_area`, `image_3d_url`, `map_pos_x`, `map_pos_y`, `map_width`, `map_height`, `status`) VALUES
(1, 1, 'KHO-A01', 'Kho Hóa chất A01', 2000.00, 1800.00, 450.00, 'assets/smartyard/3d/warehouse_a01.jpg', 60, 60, 160, 100, 'active'),
(2, 1, 'KHO-A02', 'Kho Vật tư Tổng hợp A02', 3000.00, 2500.00, 1800.00, 'assets/smartyard/3d/warehouse_a02.jpg', 260, 60, 160, 100, 'active'),
(3, 1, 'KHO-A03', 'Kho Thiết bị Cơ khí A03', 1500.00, 1200.00, 1020.00, 'assets/smartyard/3d/warehouse_a03.jpg', 460, 60, 160, 100, 'active'),
(4, 2, 'KHO-B01', 'Kho Vật tư Dung Quất B01', 4000.00, 3500.00, 700.00, 'assets/smartyard/3d/warehouse_b01.jpg', 80, 80, 170, 110, 'active'),
(5, 2, 'KHO-B02', 'Kho Ống Thép B02', 2500.00, 2000.00, 1750.00, 'assets/smartyard/3d/warehouse_b02.jpg', 290, 80, 170, 110, 'active'),
(6, 3, 'KHO-C01', 'Kho Hóa dầu Cát Lái C01', 5000.00, 4500.00, 1200.00, 'assets/smartyard/3d/warehouse_c01.jpg', 60, 60, 180, 120, 'active'),
(7, 3, 'KHO-C02', 'Kho Phụ tùng Cái Mép C02', 3500.00, 3000.00, 2600.00, 'assets/smartyard/3d/warehouse_c02.jpg', 280, 60, 180, 120, 'active')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

INSERT INTO `smartyard_projects` (`id`, `project_code`, `project_name`, `description`, `status`) VALUES
(1, 'PRJ-PETRO-2026', 'Dự án Nâng cấp Nhà máy Lọc dầu Dung Quất', 'Giai đoạn mở rộng phân xưởng cracking', 'active'),
(2, 'PRJ-OFFSHORE-01', 'Dự án Giàn khoan Lô B Ô Môn', 'Cung cấp ống và van cao áp', 'active'),
(3, 'PRJ-SOLAR-03', 'Dự án Kho Năng lượng Sạch Nghi Sơn', 'Lưu trữ phụ tùng pin năng lượng', 'active')
ON DUPLICATE KEY UPDATE `project_name` = VALUES(`project_name`);

INSERT INTO `smartyard_lots` (`id`, `warehouse_id`, `project_id`, `lot_code`, `item_name`, `initial_area`, `remaining_area`, `status`, `imported_at`, `notes`) VALUES
(1, 1, 1, 'LOT-PETRO-001', 'Hóa chất xúc tác Axit Nitric', 200.00, 200.00, 'STORED', NOW(), 'Bảo quản nhiệt độ mát'),
(2, 1, 1, 'LOT-PETRO-002', 'Phụ gia tẩy rửa bồn bể', 250.00, 250.00, 'STORED', NOW(), 'Tiêu chuẩn an toàn PCCC'),
(3, 2, 2, 'LOT-OFFSHORE-001', 'Cụm Van Điều áp Khí Gas 1000 PSI', 800.00, 800.00, 'STORED', NOW(), 'Nhập từ Đức'),
(4, 2, 2, 'LOT-OFFSHORE-002', 'Khớp nối mềm Inox 316L', 1000.00, 1000.00, 'STORED', NOW(), 'Dự án Giàn khoan Lô B'),
(5, 3, 1, 'LOT-PETRO-003', 'Máy nén khí trục vít công suất lớn', 600.00, 600.00, 'STORED', NOW(), 'Hàng nặng đặt sàn chịu lực'),
(6, 3, 3, 'LOT-SOLAR-001', 'Bộ biến tần Inverter 250kW', 420.00, 420.00, 'STORED', NOW(), 'Hàng điện tử cao cấp')
ON DUPLICATE KEY UPDATE `item_name` = VALUES(`item_name`);
