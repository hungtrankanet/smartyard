<?php
/**
 * TestDbHelper: SQLite in-memory test database and mock environment for Member Module
 * 
 * Upgraded for v2.0 Architecture:
 * - members (with company_name_en, company_name_local, detected_language, metadata)
 * - member_contacts (1-to-N contact representatives)
 * - member_branches (1-to-N corporate branch locations)
 * - member_cards (linked with member_id and contact_id)
 * - member_verify_logs (verification history)
 * - industry_types (standard 15 logistics/trade categories)
 * 
 * Maximum 500 lines constraint strictly enforced.
 */

namespace Tests\MemberTests;

class TestDbHelper
{
    private static ?\PDO $pdo = null;

    public static function getPdo(): \PDO
    {
        if (self::$pdo === null) {
            self::$pdo = new \PDO('sqlite::memory:');
            self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            self::$pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
            self::initSchema();
        }
        return self::$pdo;
    }

    public static function resetDatabase(): void
    {
        self::$pdo = null;
        self::getPdo();
    }

    public static function initSchema(): void
    {
        $pdo = self::$pdo;

        // Enable foreign key enforcement in SQLite
        $pdo->exec("PRAGMA foreign_keys = ON;");

        // 1. industry_types
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS industry_types (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255) NOT NULL,
                name_slug VARCHAR(255) NOT NULL,
                icon VARCHAR(100) NULL,
                description TEXT NULL,
                sort_order INTEGER DEFAULT 0,
                created_at DATETIME NULL
            );
            CREATE INDEX IF NOT EXISTS idx_industry_slug ON industry_types(name_slug);
            CREATE INDEX IF NOT EXISTS idx_industry_sort ON industry_types(sort_order);
        ");

        // 2. members (v2.0 with multilingual and metadata columns)
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS members (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                company_name VARCHAR(255) NOT NULL,
                company_name_en VARCHAR(255) NULL,
                company_name_local VARCHAR(255) NULL,
                detected_language VARCHAR(10) DEFAULT 'vi',
                tax_code VARCHAR(50) NULL,
                address VARCHAR(255) NULL,
                city VARCHAR(100) NULL,
                website VARCHAR(255) NULL,
                fanpage VARCHAR(255) NULL,
                phone VARCHAR(50) NULL,
                email VARCHAR(100) NULL,
                representative_name VARCHAR(150) NULL,
                position VARCHAR(100) NULL,
                industry_type_id INTEGER NULL,
                member_type VARCHAR(20) DEFAULT 'member',
                status INTEGER DEFAULT 1,
                verify_status VARCHAR(20) DEFAULT 'pending',
                last_verified_at DATETIME NULL,
                next_verify_at DATETIME NULL,
                note TEXT NULL,
                metadata TEXT NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                FOREIGN KEY (industry_type_id) REFERENCES industry_types(id) ON DELETE SET NULL
            );
            CREATE INDEX IF NOT EXISTS idx_members_industry ON members(industry_type_id);
            CREATE INDEX IF NOT EXISTS idx_members_verify ON members(verify_status);
            CREATE INDEX IF NOT EXISTS idx_members_type ON members(member_type);
            CREATE INDEX IF NOT EXISTS idx_members_status ON members(status);
            CREATE INDEX IF NOT EXISTS idx_members_next_verify ON members(next_verify_at);
            CREATE INDEX IF NOT EXISTS idx_members_company ON members(company_name);
            CREATE INDEX IF NOT EXISTS idx_members_company_en ON members(company_name_en);
            CREATE INDEX IF NOT EXISTS idx_members_company_local ON members(company_name_local);
            CREATE INDEX IF NOT EXISTS idx_members_tax_code ON members(tax_code);
            CREATE INDEX IF NOT EXISTS idx_members_detected_lang ON members(detected_language);
        ");

        // 3. member_contacts (v2.0 1-to-N contacts)
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS member_contacts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                company_id INTEGER NOT NULL,
                full_name VARCHAR(255) NOT NULL,
                full_name_en VARCHAR(255) NULL,
                full_name_local VARCHAR(255) NULL,
                position VARCHAR(150) NULL,
                position_en VARCHAR(150) NULL,
                department VARCHAR(150) NULL,
                phone VARCHAR(50) NULL,
                phone_2 VARCHAR(50) NULL,
                email VARCHAR(100) NULL,
                email_2 VARCHAR(100) NULL,
                is_primary INTEGER DEFAULT 0,
                metadata TEXT NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                FOREIGN KEY (company_id) REFERENCES members(id) ON DELETE CASCADE
            );
            CREATE INDEX IF NOT EXISTS idx_contacts_company ON member_contacts(company_id);
            CREATE INDEX IF NOT EXISTS idx_contacts_full_name ON member_contacts(full_name);
            CREATE INDEX IF NOT EXISTS idx_contacts_full_name_en ON member_contacts(full_name_en);
            CREATE INDEX IF NOT EXISTS idx_contacts_primary ON member_contacts(is_primary);
            CREATE INDEX IF NOT EXISTS idx_contacts_phone ON member_contacts(phone);
            CREATE INDEX IF NOT EXISTS idx_contacts_email ON member_contacts(email);
            CREATE INDEX IF NOT EXISTS idx_contacts_company_primary ON member_contacts(company_id, is_primary);
        ");

        // 4. member_branches (v2.0 1-to-N branches/hubs)
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS member_branches (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                company_id INTEGER NOT NULL,
                branch_name VARCHAR(255) NOT NULL,
                country VARCHAR(100) NULL,
                city VARCHAR(100) NULL,
                address TEXT NULL,
                phone VARCHAR(50) NULL,
                email VARCHAR(100) NULL,
                is_headquarters INTEGER DEFAULT 0,
                metadata TEXT NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                FOREIGN KEY (company_id) REFERENCES members(id) ON DELETE CASCADE
            );
            CREATE INDEX IF NOT EXISTS idx_branches_company ON member_branches(company_id);
            CREATE INDEX IF NOT EXISTS idx_branches_country ON member_branches(country);
            CREATE INDEX IF NOT EXISTS idx_branches_city ON member_branches(city);
            CREATE INDEX IF NOT EXISTS idx_branches_hq ON member_branches(is_headquarters);
            CREATE INDEX IF NOT EXISTS idx_branches_company_hq ON member_branches(company_id, is_headquarters);
        ");

        // 5. member_cards (v2.0 linked to member_id and contact_id)
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS member_cards (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                member_id INTEGER NOT NULL,
                contact_id INTEGER NULL,
                file_path VARCHAR(255) NOT NULL,
                side VARCHAR(20) DEFAULT 'single',
                ocr_raw TEXT NULL,
                ocr_parsed TEXT NULL,
                ocr_status VARCHAR(20) DEFAULT 'pending',
                created_at DATETIME NULL,
                FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
                FOREIGN KEY (contact_id) REFERENCES member_contacts(id) ON DELETE SET NULL
            );
            CREATE INDEX IF NOT EXISTS idx_cards_member ON member_cards(member_id);
            CREATE INDEX IF NOT EXISTS idx_cards_contact ON member_cards(contact_id);
            CREATE INDEX IF NOT EXISTS idx_cards_ocr ON member_cards(ocr_status);
        ");

        // 6. member_verify_logs
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS member_verify_logs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                member_id INTEGER NOT NULL,
                check_type VARCHAR(20) NOT NULL,
                result VARCHAR(50) NOT NULL,
                detail TEXT NULL,
                checked_at DATETIME NULL,
                FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
            );
            CREATE INDEX IF NOT EXISTS idx_logs_member ON member_verify_logs(member_id);
            CREATE INDEX IF NOT EXISTS idx_logs_type ON member_verify_logs(check_type);
            CREATE INDEX IF NOT EXISTS idx_logs_date ON member_verify_logs(checked_at);
        ");

        self::seedIndustries();
    }

    public static function seedIndustries(): void
    {
        $pdo = self::$pdo;
        $now = date('Y-m-d H:i:s');
        $industries = [
            ['Xuất Nhập Khẩu & Thương Mại Quốc Tế', 'xuat-nhap-khau-thuong-mai-quoc-te', 'fa fa-globe', 'Hoạt động xuất nhập khẩu hàng hóa, thương mại và mậu dịch quốc tế', 1],
            ['Vận Tải & Logistics', 'van-tai-logistics', 'fa fa-truck', 'Vận tải đường bộ, đường biển, hàng không, kho bãi và giao nhận', 2],
            ['Sản Xuất & Chế Biến', 'san-xuat-che-bien', 'fa fa-industry', 'Nhà máy, xưởng chế biến, gia công và sản xuất hàng công nghiệp', 3],
            ['Phân Phối & Bán Lẻ', 'phan-phoi-ban-le', 'fa fa-shopping-cart', 'Hệ thống đại lý phân phối, siêu thị, chuỗi cửa hàng bán lẻ', 4],
            ['Công Nghệ & Phần Mềm', 'cong-nghe-phan-mem', 'fa fa-laptop', 'Giải pháp chuyển đổi số, phần mềm quản trị, IT và viễn thông', 5],
            ['Tài Chính & Ngân Hàng', 'tai-chinh-ngan-hang', 'fa fa-university', 'Dịch vụ ngân hàng, bảo hiểm, tài chính và thanh toán quốc tế', 6],
            ['Bất Động Sản & Xây Dựng', 'bat-dong-san-xay-dung', 'fa fa-building', 'Bất động sản công nghiệp, kho bãi xây dựng, dự án hạ tầng', 7],
            ['Thực Phẩm & Đồ Uống', 'thuc-pham-do-uong', 'fa fa-cutlery', 'F&B, chế biến thực phẩm, nông sản đóng gói và đồ uống', 8],
            ['Y Tế & Dược Phẩm', 'y-te-duoc-pham', 'fa fa-medkit', 'Thiết bị y tế, dược phẩm, dịch vụ chăm sóc sức khỏe', 9],
            ['Giáo Dục & Đào Tạo', 'giao-duc-dao-tao', 'fa fa-graduation-cap', 'Trường học, trung tâm đào tạo nghiệp vụ logistics & quản lý', 10],
            ['Dịch Vụ Chuyên Nghiệp (Luật, Kế Toán, Tư Vấn)', 'dich-vu-chuyen-nghiep-luat-ke-toan-tu-van', 'fa fa-briefcase', 'Tư vấn pháp lý hải quan, kiểm toán thuế, tư vấn doanh nghiệp', 11],
            ['Nông Nghiệp & Thủy Sản', 'nong-nghiep-thuy-san', 'fa fa-leaf', 'Nuôi trồng, đánh bắt thủy hải sản, chế biến nông sản xuất khẩu', 12],
            ['Năng Lượng & Môi Trường', 'nang-luong-moi-truong', 'fa fa-bolt', 'Năng lượng tái tạo, xử lý chất thải, giải pháp xanh', 13],
            ['Du Lịch & Khách Sạn', 'du-lich-khach-san', 'fa fa-plane', 'Lữ hành quốc tế, chuỗi khách sạn, nghỉ dưỡng và sự kiện', 14],
            ['Khác', 'khac', 'fa fa-ellipsis-h', 'Các ngành nghề kinh doanh và dịch vụ thương mại khác', 15],
        ];

        $stmt = $pdo->prepare("
            INSERT INTO industry_types (name, name_slug, icon, description, sort_order, created_at)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        foreach ($industries as $ind) {
            $stmt->execute([$ind[0], $ind[1], $ind[2], $ind[3], $ind[4], $now]);
        }
    }
}
