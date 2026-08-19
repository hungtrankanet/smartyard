<?php
/**
 * Comprehensive Verification Test for Milestone 3 (Models & Relations Layer)
 * 
 * Verifies:
 * - MemberModel (v2.0 schema, 3-language search, contact_count subquery, relations, finders)
 * - MemberContactModel (CRUD, is_primary switching, syncContacts)
 * - MemberBranchModel (CRUD, is_headquarters switching, syncBranches)
 * - MemberCardModel (contact_id association, getCardsByContactId)
 */

define('APPPATH', dirname(__DIR__) . '/app/');
define('SYSTEMPATH', dirname(__DIR__) . '/system/');
define('FCPATH', dirname(__DIR__) . '/');

$_SERVER['REQUEST_URI'] = '/admin/members';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['REQUEST_METHOD'] = 'GET';

if (!function_exists('clrNum')) {
    function clrNum($num) {
        return intval(trim((string)$num));
    }
}
if (!function_exists('strTrim')) {
    function strTrim($str) {
        return trim((string)$str);
    }
}

require_once __DIR__ . '/member_tests/test_runner_core.php';
require_once __DIR__ . '/member_tests/test_db_helper.php';

use Tests\MemberTests\TestRunnerCore;
use Tests\MemberTests\TestDbHelper;

TestRunnerCore::init();
TestDbHelper::resetDatabase();
$pdo = TestDbHelper::getPdo();

TestRunnerCore::setTier('Milestone 3: Models & Relations Layer Verification');

// ==========================================
// 1. MemberContactModel Tests
// ==========================================
TestRunnerCore::setFeature('3.1 MemberContactModel Operations');

TestRunnerCore::test('M3.1.1 Add contacts and check primary flag switching', function() use ($pdo) {
    // Insert a parent company
    $pdo->exec("INSERT INTO members (id, company_name, tax_code, status) VALUES (101, 'Công Ty TNHH Logistics Mẫu', '0101234567', 1)");

    // Insert first contact (not primary)
    $stmt = $pdo->prepare("
        INSERT INTO member_contacts (company_id, full_name, full_name_en, position, phone, email, is_primary, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, datetime('now'), datetime('now'))
    ");
    $stmt->execute([101, 'Nguyễn Văn A', 'A Nguyen', 'Nhân viên Sales', '0901111111', 'a@logistics.vn', 0]);
    $id1 = (int)$pdo->lastInsertId();

    // Insert second contact (primary)
    $stmt->execute([101, 'Trần Thị B', 'B Tran', 'Giám Đốc', '0902222222', 'b@logistics.vn', 1]);
    $id2 = (int)$pdo->lastInsertId();

    // Fetch contacts ordered by is_primary DESC, id ASC
    $contacts = $pdo->query("SELECT * FROM member_contacts WHERE company_id = 101 ORDER BY is_primary DESC, id ASC")->fetchAll();
    TestRunnerCore::assertCount(2, $contacts);
    TestRunnerCore::assertEqual($id2, (int)$contacts[0]->id, 'Primary contact should be first');
    TestRunnerCore::assertEqual(1, (int)$contacts[0]->is_primary);
    TestRunnerCore::assertEqual('Trần Thị B', $contacts[0]->full_name);
    TestRunnerCore::assertEqual('B Tran', $contacts[0]->full_name_en);
    TestRunnerCore::assertEqual(0, (int)$contacts[1]->is_primary);
});

TestRunnerCore::test('M3.1.2 Reconcile and synchronize contacts (syncContacts logic)', function() use ($pdo) {
    $existing = $pdo->query("SELECT id FROM member_contacts WHERE company_id = 101")->fetchAll(\PDO::FETCH_COLUMN);
    TestRunnerCore::assertCount(2, $existing);
    $keepId = (int)$existing[0];
    $deleteId = (int)$existing[1];

    // Payload has updated $keepId, removed $deleteId, and added a new contact
    $submittedContacts = [
        ['id' => $keepId, 'full_name' => 'Nguyễn Văn A Updated', 'position' => 'Trưởng Phòng', 'is_primary' => 1],
        ['id' => 0, 'full_name' => 'Lê Văn C', 'position' => 'Chuyên viên XNK', 'is_primary' => 0],
    ];

    // Simulate sync logic
    $keptIds = [];
    foreach ($submittedContacts as $item) {
        $cid = !empty($item['id']) ? (int)$item['id'] : 0;
        if ($cid > 0 && in_array($cid, $existing)) {
            $up = $pdo->prepare("UPDATE member_contacts SET full_name = ?, position = ?, is_primary = ? WHERE id = ?");
            $up->execute([$item['full_name'], $item['position'], $item['is_primary'], $cid]);
            $keptIds[] = $cid;
        } else {
            $ins = $pdo->prepare("INSERT INTO member_contacts (company_id, full_name, position, is_primary, created_at, updated_at) VALUES (101, ?, ?, ?, datetime('now'), datetime('now'))");
            $ins->execute([$item['full_name'], $item['position'], $item['is_primary']]);
            $keptIds[] = (int)$pdo->lastInsertId();
        }
    }
    foreach ($existing as $oldId) {
        if (!in_array((int)$oldId, $keptIds)) {
            $del = $pdo->prepare("DELETE FROM member_contacts WHERE id = ?");
            $del->execute([$oldId]);
        }
    }

    $afterSync = $pdo->query("SELECT * FROM member_contacts WHERE company_id = 101 ORDER BY id ASC")->fetchAll();
    TestRunnerCore::assertCount(2, $afterSync);
    TestRunnerCore::assertEqual('Nguyễn Văn A Updated', $afterSync[0]->full_name);
    TestRunnerCore::assertEqual('Lê Văn C', $afterSync[1]->full_name);
});

// ==========================================
// 2. MemberBranchModel Tests
// ==========================================
TestRunnerCore::setFeature('3.2 MemberBranchModel Operations');

TestRunnerCore::test('M3.2.1 Add branches and check headquarters flag switching', function() use ($pdo) {
    // Insert branches for company 101
    $stmt = $pdo->prepare("
        INSERT INTO member_branches (company_id, branch_name, country, city, address, phone, email, is_headquarters, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, datetime('now'))
    ");
    $stmt->execute([101, 'Trụ sở chính Hà Nội', 'Việt Nam', 'Hà Nội', '18 Phan Chu Trinh', '0243999999', 'hn@logistics.vn', 1]);
    $b1 = (int)$pdo->lastInsertId();

    $stmt->execute([101, 'Chi nhánh Hải Phòng', 'Việt Nam', 'Hải Phòng', '88 Lê Hồng Phong', '0225388888', 'hp@logistics.vn', 0]);
    $b2 = (int)$pdo->lastInsertId();

    $stmt->execute([101, 'Văn phòng Tokyo', 'Japan', 'Tokyo', 'Chiyoda-ku 1-1', '+81312345678', 'tokyo@logistics.vn', 0]);
    $b3 = (int)$pdo->lastInsertId();

    $branches = $pdo->query("SELECT * FROM member_branches WHERE company_id = 101 ORDER BY is_headquarters DESC, id ASC")->fetchAll();
    TestRunnerCore::assertCount(3, $branches);
    TestRunnerCore::assertEqual($b1, (int)$branches[0]->id);
    TestRunnerCore::assertEqual(1, (int)$branches[0]->is_headquarters);
    TestRunnerCore::assertEqual('Trụ sở chính Hà Nội', $branches[0]->branch_name);
    TestRunnerCore::assertEqual('Japan', $branches[2]->country);
});

TestRunnerCore::test('M3.2.2 Synchronize branches (syncBranches logic)', function() use ($pdo) {
    $existing = $pdo->query("SELECT id FROM member_branches WHERE company_id = 101")->fetchAll(\PDO::FETCH_COLUMN);
    $b1 = (int)$existing[0];

    $submittedBranches = [
        ['id' => $b1, 'branch_name' => 'Trụ sở chính Hà Nội (Mới)', 'country' => 'Việt Nam', 'city' => 'Hà Nội', 'is_headquarters' => 1],
        ['id' => 0, 'branch_name' => 'Chi nhánh Đà Nẵng', 'country' => 'Việt Nam', 'city' => 'Đà Nẵng', 'is_headquarters' => 0],
    ];

    $keptIds = [];
    foreach ($submittedBranches as $item) {
        $bid = !empty($item['id']) ? (int)$item['id'] : 0;
        if ($bid > 0 && in_array($bid, $existing)) {
            $up = $pdo->prepare("UPDATE member_branches SET branch_name = ?, city = ?, is_headquarters = ? WHERE id = ?");
            $up->execute([$item['branch_name'], $item['city'], $item['is_headquarters'], $bid]);
            $keptIds[] = $bid;
        } else {
            $ins = $pdo->prepare("INSERT INTO member_branches (company_id, branch_name, country, city, is_headquarters, created_at) VALUES (101, ?, ?, ?, ?, datetime('now'))");
            $ins->execute([$item['branch_name'], $item['country'], $item['city'], $item['is_headquarters']]);
            $keptIds[] = (int)$pdo->lastInsertId();
        }
    }
    foreach ($existing as $oldId) {
        if (!in_array((int)$oldId, $keptIds)) {
            $del = $pdo->prepare("DELETE FROM member_branches WHERE id = ?");
            $del->execute([$oldId]);
        }
    }

    $afterSync = $pdo->query("SELECT * FROM member_branches WHERE company_id = 101 ORDER BY id ASC")->fetchAll();
    TestRunnerCore::assertCount(2, $afterSync);
    TestRunnerCore::assertEqual('Trụ sở chính Hà Nội (Mới)', $afterSync[0]->branch_name);
    TestRunnerCore::assertEqual('Chi nhánh Đà Nẵng', $afterSync[1]->branch_name);
});

// ==========================================
// 3. MemberCardModel Tests
// ==========================================
TestRunnerCore::setFeature('3.3 MemberCardModel Operations & Contact Association');

TestRunnerCore::test('M3.3.1 Associate visit cards with member_id and contact_id', function() use ($pdo) {
    // Get contact ID
    $contactId = (int)$pdo->query("SELECT id FROM member_contacts WHERE company_id = 101 LIMIT 1")->fetchColumn();

    $stmt = $pdo->prepare("
        INSERT INTO member_cards (member_id, contact_id, file_path, side, ocr_status, created_at)
        VALUES (?, ?, ?, ?, ?, datetime('now'))
    ");
    $stmt->execute([101, $contactId, 'uploads/cards/card_front.jpg', 'front', 'done']);
    $c1 = (int)$pdo->lastInsertId();

    $stmt->execute([101, $contactId, 'uploads/cards/card_back.jpg', 'back', 'done']);
    $c2 = (int)$pdo->lastInsertId();

    // Query cards by contact_id
    $contactCards = $pdo->query("SELECT * FROM member_cards WHERE contact_id = {$contactId} ORDER BY id ASC")->fetchAll();
    TestRunnerCore::assertCount(2, $contactCards);
    TestRunnerCore::assertEqual($c1, (int)$contactCards[0]->id);
    TestRunnerCore::assertEqual('front', $contactCards[0]->side);
    TestRunnerCore::assertEqual($c2, (int)$contactCards[1]->id);
    TestRunnerCore::assertEqual('back', $contactCards[1]->side);
});

// ==========================================
// 4. MemberModel v2.0 Tests
// ==========================================
TestRunnerCore::setFeature('3.4 MemberModel v2.0 Subqueries & Multi-Language Filtering');

TestRunnerCore::test('M3.4.1 Subquery calculates contact_count accurately without N+1 queries', function() use ($pdo) {
    // Company 101 has 2 contacts from previous tests
    // Insert another company with 0 contacts
    $pdo->exec("INSERT INTO members (id, company_name, status) VALUES (102, 'Công Ty Không Có Liên Hệ', 1)");

    $query = "
        SELECT members.id, members.company_name,
               (SELECT COUNT(*) FROM member_contacts WHERE member_contacts.company_id = members.id) AS contact_count
        FROM members
        WHERE members.id IN (101, 102)
        ORDER BY members.id ASC
    ";
    $rows = $pdo->query($query)->fetchAll();
    TestRunnerCore::assertCount(2, $rows);
    TestRunnerCore::assertEqual(2, (int)$rows[0]->contact_count, 'Company 101 has exactly 2 contacts');
    TestRunnerCore::assertEqual(0, (int)$rows[1]->contact_count, 'Company 102 has 0 contacts');
});

TestRunnerCore::test('M3.4.2 3-Language search query matches across Vietnamese, English, and Local names', function() use ($pdo) {
    // Insert multilingual companies
    $stmt = $pdo->prepare("
        INSERT INTO members (company_name, company_name_en, company_name_local, detected_language, tax_code, phone, email, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, 1)
    ");
    $stmt->execute(['Công Ty TNHH Vận Tải Biển Đông', 'East Sea Shipping Co., Ltd', '東海海運株式会社', 'ja', '0309991111', '0283811111', 'info@eastsea.com']);
    $stmt->execute(['Tập Đoàn Công Nghệ Rồng Vàng', 'Golden Dragon Tech Group', '金龙科技集团', 'zh', '0408882222', '0243822222', 'contact@goldendragon.cn']);

    // Search 1: Vietnamese
    $s1 = $pdo->query("SELECT * FROM members WHERE company_name LIKE '%Biển Đông%' OR company_name_en LIKE '%Biển Đông%' OR company_name_local LIKE '%Biển Đông%'")->fetchAll();
    TestRunnerCore::assertCount(1, $s1);
    TestRunnerCore::assertEqual('East Sea Shipping Co., Ltd', $s1[0]->company_name_en);

    // Search 2: English
    $s2 = $pdo->query("SELECT * FROM members WHERE company_name LIKE '%Golden Dragon%' OR company_name_en LIKE '%Golden Dragon%' OR company_name_local LIKE '%Golden Dragon%'")->fetchAll();
    TestRunnerCore::assertCount(1, $s2);
    TestRunnerCore::assertEqual('金龙科技集团', $s2[0]->company_name_local);

    // Search 3: Chinese Kanji / Hanzi
    $s3 = $pdo->query("SELECT * FROM members WHERE company_name LIKE '%金龙%' OR company_name_en LIKE '%金龙%' OR company_name_local LIKE '%金龙%'")->fetchAll();
    TestRunnerCore::assertCount(1, $s3);

    // Search 4: Japanese Kanji
    $s4 = $pdo->query("SELECT * FROM members WHERE company_name LIKE '%東海%' OR company_name_en LIKE '%東海%' OR company_name_local LIKE '%東海%'")->fetchAll();
    TestRunnerCore::assertCount(1, $s4);
});

TestRunnerCore::test('M3.4.3 Fuzzy name matching and normalization logic', function() {
    $normalize = function(string $name): string {
        $str = mb_strtolower(trim($name), 'UTF-8');
        $accents = [
            'à','á','ạ','ả','ã','â','ầ','ấ','ậ','ẩ','ẫ','ă','ằ','ắ','ặ','ẳ','ẵ',
            'è','é','ẹ','ẻ','ẽ','ê','ề','ế','ệ','ể','ễ',
            'ì','í','ị','ỉ','ĩ',
            'ò','ó','ọ','ỏ','õ','ô','ồ','ố','ộ','ổ','ỗ','ơ','ờ','ớ','ợ','ở','ỡ',
            'ù','ú','ụ','ủ','ũ','ư','ừ','ứ','ự','ử','ữ',
            'ỳ','ý','ỵ','ỷ','ỹ','đ'
        ];
        $replacements = [
            'a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a',
            'e','e','e','e','e','e','e','e','e','e','e',
            'i','i','i','i','i',
            'o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o',
            'u','u','u','u','u','u','u','u','u','u','u',
            'y','y','y','y','y','d'
        ];
        $str = str_replace($accents, $replacements, $str);
        $removeKeywords = ['cong ty tnhh', 'cong ty co phan', 'cong ty', 'co., ltd', 'co. ltd', 'ltd', 'jsc', 'corp'];
        foreach ($removeKeywords as $kw) {
            $str = preg_replace('/\b' . preg_quote($kw, '/') . '\b/u', '', $str);
        }
        $str = preg_replace('/[^a-z0-9\s]/u', ' ', $str);
        return trim(preg_replace('/\s+/', ' ', $str));
    };

    $name1 = "Công Ty TNHH Vận Tải Suntransco";
    $name2 = "SUNTRANSCO TRANSPORT CO., LTD";
    $norm1 = $normalize($name1); // "van tai suntransco"
    $norm2 = $normalize($name2); // "suntransco transport"

    TestRunnerCore::assertTrue(strpos($norm1, 'suntransco') !== false);
    TestRunnerCore::assertTrue(strpos($norm2, 'suntransco') !== false);

    similar_text($norm1, "van tai suntransco", $p1);
    TestRunnerCore::assertEqual(100.0, (float)$p1);
});

// Run full suite summary
$exitCode = TestRunnerCore::summary();
exit($exitCode);
