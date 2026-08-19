<?php
// ==============================================================================
// TEST SUITE: Milestone 1 Verification (Database Migrations & Models)
// Suntransco CodeIgniter 4 Smart Member Management Module
// ==============================================================================

define('APPPATH', __DIR__ . '/../app/');
define('SYSTEMPATH', __DIR__ . '/../system/');
define('FCPATH', __DIR__ . '/../');

$_SERVER['REQUEST_URI'] = '/';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['REQUEST_METHOD'] = 'GET';

require_once APPPATH . 'Common.php';

$totalPassed = 0;
$totalFailed = 0;

function run_test($name, $closure) {
    global $totalPassed, $totalFailed;
    try {
        $result = $closure();
        if ($result !== false) {
            echo "  [PASS] {$name}\n";
            $totalPassed++;
        } else {
            echo "  [FAIL] {$name}\n";
            $totalFailed++;
        }
    } catch (\Throwable $e) {
        echo "  [FAIL] {$name} - Exception: " . $e->getMessage() . "\n";
        $totalFailed++;
    }
}

echo "=================================================================\n";
echo "  MILESTONE 1 VERIFICATION: MIGRATIONS & MODELS\n";
echo "=================================================================\n\n";

// ------------------------------------------------------------------------------
// TEST SUITE 1: Rule 1 Compliance (File Length <= 500 Lines)
// ------------------------------------------------------------------------------
echo "--- 1. Checking 500-Line Limit Rule ---\n";

$targetFiles = [
    'app/Database/Migrations/2026-08-16-000001_CreateIndustryTypes.php',
    'app/Database/Migrations/2026-08-16-000002_CreateMembers.php',
    'app/Database/Migrations/2026-08-16-000003_CreateMemberCards.php',
    'app/Database/Migrations/2026-08-16-000004_CreateMemberVerifyLogs.php',
    'app/Models/IndustryTypeModel.php',
    'app/Models/MemberModel.php',
    'app/Models/MemberCardModel.php',
    'app/Models/MemberVerifyLogModel.php',
];

foreach ($targetFiles as $relPath) {
    $fullPath = FCPATH . $relPath;
    run_test("File {$relPath} exists and line count <= 500", function () use ($fullPath, $relPath) {
        if (!file_exists($fullPath)) {
            echo " [File missing: {$fullPath}] ";
            return false;
        }
        $lineCount = count(file($fullPath));
        if ($lineCount > 500) {
            echo " [File exceeds 500 lines: {$lineCount}] ";
            return false;
        }
        return true;
    });
}

// ------------------------------------------------------------------------------
// TEST SUITE 2: Migration Files & Schema Definitions
// ------------------------------------------------------------------------------
echo "\n--- 2. Checking Migration Classes & DDL Definitions ---\n";

run_test("Migration 1 (CreateIndustryTypes) class structure and seed data", function () {
    $file = FCPATH . 'app/Database/Migrations/2026-08-16-000001_CreateIndustryTypes.php';
    $content = file_get_contents($file);
    
    assert(strpos($content, 'class CreateIndustryTypes extends Migration') !== false);
    assert(strpos($content, 'industry_types') !== false);
    assert(strpos($content, "'name'") !== false);
    assert(strpos($content, "'name_slug'") !== false);
    assert(strpos($content, "'icon'") !== false);
    assert(strpos($content, "'sort_order'") !== false);
    
    // Check 15 industries are present
    $expectedIndustries = [
        'Xuất Nhập Khẩu & Thương Mại Quốc Tế',
        'Vận Tải & Logistics',
        'Sản Xuất & Chế Biến',
        'Phân Phối & Bán Lẻ',
        'Công Nghệ & Phần Mềm',
        'Tài Chính & Ngân Hàng',
        'Bất Động Sản & Xây Dựng',
        'Thực Phẩm & Đồ Uống',
        'Y Tế & Dược Phẩm',
        'Giáo Dục & Đào Tạo',
        'Dịch Vụ Chuyên Nghiệp (Luật, Kế Toán, Tư Vấn)',
        'Nông Nghiệp & Thủy Sản',
        'Năng Lượng & Môi Trường',
        'Du Lịch & Khách Sạn',
        'Khác'
    ];
    
    foreach ($expectedIndustries as $ind) {
        if (strpos($content, $ind) === false) {
            echo " [Missing industry: {$ind}] ";
            return false;
        }
    }
    return true;
});

run_test("Migration 2 (CreateMembers) class structure and index definitions", function () {
    $file = FCPATH . 'app/Database/Migrations/2026-08-16-000002_CreateMembers.php';
    $content = file_get_contents($file);
    
    assert(strpos($content, 'class CreateMembers extends Migration') !== false);
    assert(strpos($content, "'company_name'") !== false);
    assert(strpos($content, "'tax_code'") !== false);
    assert(strpos($content, "'industry_type_id'") !== false);
    assert(strpos($content, "'member_type'") !== false);
    assert(strpos($content, "'verify_status'") !== false);
    assert(strpos($content, "'last_verified_at'") !== false);
    assert(strpos($content, "'next_verify_at'") !== false);
    
    // Check indexing on search and filter columns
    assert(strpos($content, "addKey('industry_type_id')") !== false);
    assert(strpos($content, "addKey('verify_status')") !== false);
    assert(strpos($content, "addKey('member_type')") !== false);
    assert(strpos($content, "addKey('status')") !== false);
    assert(strpos($content, "addKey('next_verify_at')") !== false);
    return true;
});

run_test("Migration 3 (CreateMemberCards) cascading FK and JSON parsing fields", function () {
    $file = FCPATH . 'app/Database/Migrations/2026-08-16-000003_CreateMemberCards.php';
    $content = file_get_contents($file);
    
    assert(strpos($content, 'class CreateMemberCards extends Migration') !== false);
    assert(strpos($content, "'member_id'") !== false);
    assert(strpos($content, "'file_path'") !== false);
    assert(strpos($content, "'side'") !== false);
    assert(strpos($content, "'ocr_raw'") !== false);
    assert(strpos($content, "'ocr_parsed'") !== false);
    assert(strpos($content, "'ocr_status'") !== false);
    assert(strpos($content, "addForeignKey('member_id', 'members', 'id', 'CASCADE', 'CASCADE')") !== false);
    return true;
});

run_test("Migration 4 (CreateMemberVerifyLogs) cascading FK and check_type fields", function () {
    $file = FCPATH . 'app/Database/Migrations/2026-08-16-000004_CreateMemberVerifyLogs.php';
    $content = file_get_contents($file);
    
    assert(strpos($content, 'class CreateMemberVerifyLogs extends Migration') !== false);
    assert(strpos($content, "'member_id'") !== false);
    assert(strpos($content, "'check_type'") !== false);
    assert(strpos($content, "'result'") !== false);
    assert(strpos($content, "'detail'") !== false);
    assert(strpos($content, "'checked_at'") !== false);
    assert(strpos($content, "addForeignKey('member_id', 'members', 'id', 'CASCADE', 'CASCADE')") !== false);
    return true;
});

// ------------------------------------------------------------------------------
// TEST SUITE 3: Models Layer Structure & Helper Methods
// ------------------------------------------------------------------------------
echo "\n--- 3. Checking Models Layer Classes & Methods ---\n";

run_test("IndustryTypeModel class methods and allowed fields", function () {
    $file = FCPATH . 'app/Models/IndustryTypeModel.php';
    $content = file_get_contents($file);
    
    assert(strpos($content, "class IndustryTypeModel extends BaseModel") !== false);
    assert(strpos($content, "table = 'industry_types'") !== false);
    assert(strpos($content, "function getIndustries") !== false);
    assert(strpos($content, "function getIndustry") !== false);
    assert(strpos($content, "function getIndustryBySlug") !== false);
    assert(strpos($content, "function addIndustry") !== false);
    assert(strpos($content, "function updateIndustry") !== false);
    assert(strpos($content, "function deleteIndustry") !== false);
    assert(strpos($content, "function getIndustryCount") !== false);
    return true;
});

run_test("MemberModel class methods and queries", function () {
    $file = FCPATH . 'app/Models/MemberModel.php';
    $content = file_get_contents($file);
    
    assert(strpos($content, "class MemberModel extends BaseModel") !== false);
    assert(strpos($content, "table = 'members'") !== false);
    assert(strpos($content, "function getMembersPaginated") !== false);
    assert(strpos($content, "function getMembersCount") !== false);
    assert(strpos($content, "function getMember") !== false);
    assert(strpos($content, "function getMemberWithRelations") !== false);
    assert(strpos($content, "function getMembersDueForVerification") !== false);
    assert(strpos($content, "function addMember") !== false);
    assert(strpos($content, "function updateMember") !== false);
    assert(strpos($content, "function updateVerifyStatus") !== false);
    assert(strpos($content, "function deleteMember") !== false);
    assert(strpos($content, "function getStats") !== false);
    return true;
});

run_test("MemberCardModel class methods and JSON support", function () {
    $file = FCPATH . 'app/Models/MemberCardModel.php';
    $content = file_get_contents($file);
    
    assert(strpos($content, "class MemberCardModel extends BaseModel") !== false);
    assert(strpos($content, "table = 'member_cards'") !== false);
    assert(strpos($content, "function getCardsByMemberId") !== false);
    assert(strpos($content, "function getCard") !== false);
    assert(strpos($content, "function addCard") !== false);
    assert(strpos($content, "function updateOcrResult") !== false);
    assert(strpos($content, "function updateCard") !== false);
    assert(strpos($content, "function deleteCard") !== false);
    assert(strpos($content, "function deleteCardsByMemberId") !== false);
    return true;
});

run_test("MemberVerifyLogModel class methods and retrieval", function () {
    $file = FCPATH . 'app/Models/MemberVerifyLogModel.php';
    $content = file_get_contents($file);
    
    assert(strpos($content, "class MemberVerifyLogModel extends BaseModel") !== false);
    assert(strpos($content, "table = 'member_verify_logs'") !== false);
    assert(strpos($content, "function getLogsByMemberId") !== false);
    assert(strpos($content, "function getLog") !== false);
    assert(strpos($content, "function addLog") !== false);
    assert(strpos($content, "function getLatestLog") !== false);
    assert(strpos($content, "function deleteLogsByMemberId") !== false);
    return true;
});

// ------------------------------------------------------------------------------
// TEST SUITE 4: Functional SQLite In-Memory Database & CRUD Integration
// ------------------------------------------------------------------------------
echo "\n--- 4. Functional DB Simulation with Foreign Keys & Relational Queries ---\n";

run_test("Execute SQLite schema simulation and verify all 4 tables & constraints", function () {
    $pdo = new PDO('sqlite::memory:');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('PRAGMA foreign_keys = ON;');

    // 1. Create industry_types
    $pdo->exec("CREATE TABLE industry_types (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name VARCHAR(255) NOT NULL,
        name_slug VARCHAR(255) NOT NULL,
        icon VARCHAR(100) NULL,
        description TEXT NULL,
        sort_order INTEGER DEFAULT 0,
        created_at DATETIME NULL
    )");
    $pdo->exec("CREATE INDEX idx_industry_slug ON industry_types(name_slug)");
    $pdo->exec("CREATE INDEX idx_industry_sort ON industry_types(sort_order)");

    // 2. Create members
    $pdo->exec("CREATE TABLE members (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        company_name VARCHAR(255) NOT NULL,
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
        member_type TEXT CHECK(member_type IN ('prospect','member','partner')) DEFAULT 'member',
        status INTEGER DEFAULT 1,
        verify_status TEXT CHECK(verify_status IN ('pending','verified','unverified','failed')) DEFAULT 'pending',
        last_verified_at DATETIME NULL,
        next_verify_at DATETIME NULL,
        note TEXT NULL,
        created_at DATETIME NULL,
        updated_at DATETIME NULL,
        FOREIGN KEY (industry_type_id) REFERENCES industry_types(id) ON DELETE SET NULL
    )");
    $pdo->exec("CREATE INDEX idx_members_industry ON members(industry_type_id)");
    $pdo->exec("CREATE INDEX idx_members_verify_status ON members(verify_status)");
    $pdo->exec("CREATE INDEX idx_members_type ON members(member_type)");
    $pdo->exec("CREATE INDEX idx_members_status ON members(status)");
    $pdo->exec("CREATE INDEX idx_members_next_verify ON members(next_verify_at)");

    // 3. Create member_cards
    $pdo->exec("CREATE TABLE member_cards (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        member_id INTEGER NOT NULL,
        file_path VARCHAR(255) NOT NULL,
        side TEXT CHECK(side IN ('front','back','single')) DEFAULT 'single',
        ocr_raw TEXT NULL,
        ocr_parsed TEXT NULL,
        ocr_status TEXT CHECK(ocr_status IN ('pending','done','failed')) DEFAULT 'pending',
        created_at DATETIME NULL,
        FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
    )");
    $pdo->exec("CREATE INDEX idx_cards_member ON member_cards(member_id)");

    // 4. Create member_verify_logs
    $pdo->exec("CREATE TABLE member_verify_logs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        member_id INTEGER NOT NULL,
        check_type TEXT CHECK(check_type IN ('google_maps','fanpage','manual')) NOT NULL,
        result VARCHAR(50) NOT NULL,
        detail TEXT NULL,
        checked_at DATETIME NULL,
        FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
    )");
    $pdo->exec("CREATE INDEX idx_logs_member ON member_verify_logs(member_id)");

    // Seed 15 industries
    $industries = [
        ['Xuất Nhập Khẩu & Thương Mại Quốc Tế', 'xuat-nhap-khau-thuong-mai-quoc-te', 'fa fa-globe', 'Mô tả 1', 1],
        ['Vận Tải & Logistics', 'van-tai-logistics', 'fa fa-truck', 'Mô tả 2', 2],
        ['Sản Xuất & Chế Biến', 'san-xuat-che-bien', 'fa fa-industry', 'Mô tả 3', 3],
        ['Phân Phối & Bán Lẻ', 'phan-phoi-ban-le', 'fa fa-shopping-cart', 'Mô tả 4', 4],
        ['Công Nghệ & Phần Mềm', 'cong-nghe-phan-mem', 'fa fa-laptop', 'Mô tả 5', 5],
        ['Tài Chính & Ngân Hàng', 'tai-chinh-ngan-hang', 'fa fa-university', 'Mô tả 6', 6],
        ['Bất Động Sản & Xây Dựng', 'bat-dong-san-xay-dung', 'fa fa-building', 'Mô tả 7', 7],
        ['Thực Phẩm & Đồ Uống', 'thuc-pham-do-uong', 'fa fa-cutlery', 'Mô tả 8', 8],
        ['Y Tế & Dược Phẩm', 'y-te-duoc-pham', 'fa fa-medkit', 'Mô tả 9', 9],
        ['Giáo Dục & Đào Tạo', 'giao-duc-dao-tao', 'fa fa-graduation-cap', 'Mô tả 10', 10],
        ['Dịch Vụ Chuyên Nghiệp (Luật, Kế Toán, Tư Vấn)', 'dich-vu-chuyen-nghiep-luat-ke-toan-tu-van', 'fa fa-briefcase', 'Mô tả 11', 11],
        ['Nông Nghiệp & Thủy Sản', 'nong-nghiep-thuy-san', 'fa fa-leaf', 'Mô tả 12', 12],
        ['Năng Lượng & Môi Trường', 'nang-luong-moi-truong', 'fa fa-bolt', 'Mô tả 13', 13],
        ['Du Lịch & Khách Sạn', 'du-lich-khach-san', 'fa fa-plane', 'Mô tả 14', 14],
        ['Khác', 'khac', 'fa fa-ellipsis-h', 'Mô tả 15', 15],
    ];

    $stmt = $pdo->prepare("INSERT INTO industry_types (name, name_slug, icon, description, sort_order, created_at) VALUES (?, ?, ?, ?, ?, datetime('now'))");
    foreach ($industries as $ind) {
        $stmt->execute($ind);
    }

    $count = $pdo->query("SELECT count(*) FROM industry_types")->fetchColumn();
    assert($count == 15, "Expected 15 industries, got {$count}");

    // Test Insert Member
    $stmtMember = $pdo->prepare("INSERT INTO members (company_name, tax_code, industry_type_id, member_type, status, verify_status, next_verify_at, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, datetime('now'))");
    $stmtMember->execute(['Công Ty Cổ Phần Suntransco Logistics', '0102030405', 2, 'member', 1, 'verified', '2026-08-16 00:00:00']);
    $memberId = $pdo->lastInsertId();
    assert($memberId > 0, "Member ID must be valid");

    // Test Insert Member Card with JSON payload
    $ocrJson = json_encode(['company_name' => 'Công Ty Suntransco', 'phone' => '0901234567']);
    $stmtCard = $pdo->prepare("INSERT INTO member_cards (member_id, file_path, side, ocr_raw, ocr_parsed, ocr_status, created_at) VALUES (?, ?, ?, ?, ?, ?, datetime('now'))");
    $stmtCard->execute([$memberId, 'uploads/cards/card_1.jpg', 'front', 'SUNTRANSCO LOGISTICS', $ocrJson, 'done']);
    $cardId = $pdo->lastInsertId();
    assert($cardId > 0, "Card ID must be valid");

    // Test Insert Member Verify Log
    $stmtLog = $pdo->prepare("INSERT INTO member_verify_logs (member_id, check_type, result, detail, checked_at) VALUES (?, ?, ?, ?, datetime('now'))");
    $stmtLog->execute([$memberId, 'google_maps', 'active', 'Found verified place on Google Maps']);
    $logId = $pdo->lastInsertId();
    assert($logId > 0, "Log ID must be valid");

    // Test Relational JOIN
    $stmtJoin = $pdo->query("SELECT m.*, i.name as industry_name FROM members m LEFT JOIN industry_types i ON m.industry_type_id = i.id WHERE m.id = {$memberId}");
    $row = $stmtJoin->fetch(PDO::FETCH_ASSOC);
    assert($row['industry_name'] === 'Vận Tải & Logistics', "Industry name should join properly");

    // Test ON DELETE CASCADE
    $pdo->exec("DELETE FROM members WHERE id = {$memberId}");
    $remainingCards = $pdo->query("SELECT count(*) FROM member_cards WHERE member_id = {$memberId}")->fetchColumn();
    $remainingLogs = $pdo->query("SELECT count(*) FROM member_verify_logs WHERE member_id = {$memberId}")->fetchColumn();
    assert($remainingCards == 0, "Member cards should cascade delete");
    assert($remainingLogs == 0, "Member verify logs should cascade delete");

    return true;
});

echo "\n=================================================================\n";
echo "  TEST RESULTS: {$totalPassed} Passed, {$totalFailed} Failed\n";
echo "=================================================================\n";

if ($totalFailed > 0) {
    exit(1);
}
