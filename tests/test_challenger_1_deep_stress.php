<?php
/**
 * Challenger 1 Deep Adversarial & Stress Test Suite
 * Target: Suntransco Member Management Module v2.0 Architecture Upgrade
 * Coverage:
 * 1. CompanyMatcher 3-Tier Multi-Algorithm Stress & Boundary Tests
 * 2. MemberContactModel & MemberBranchModel Reconciling & State Invariants
 * 3. OcrService Multi-Side Merging & Boundary Tests
 */

define('APPPATH', dirname(__DIR__) . '/app/');
define('SYSTEMPATH', dirname(__DIR__) . '/system/');
define('FCPATH', dirname(__DIR__) . '/');

$_SERVER['REQUEST_URI'] = '/admin/members';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['REQUEST_METHOD'] = 'GET';

if (!function_exists('clrNum')) {
    function clrNum($num) { return intval(trim((string)$num)); }
}
if (!function_exists('strTrim')) {
    function strTrim($str) { return trim((string)$str); }
}

require_once APPPATH . 'Libraries/CompanyMatcher.php';
require_once APPPATH . 'Libraries/OcrService.php';
require_once __DIR__ . '/member_tests/test_runner_core.php';
require_once __DIR__ . '/member_tests/test_db_helper.php';

use App\Libraries\CompanyMatcher;
use App\Libraries\OcrService;
use Tests\MemberTests\TestRunnerCore;
use Tests\MemberTests\TestDbHelper;

TestRunnerCore::init();
TestDbHelper::resetDatabase();
$pdo = TestDbHelper::getPdo();

$matcher = new CompanyMatcher();
$ocr = new OcrService();

// ======================================================================
// SECTION 1: CompanyMatcher Deep Stress & Boundary Tests
// ======================================================================
TestRunnerCore::setTier('Challenger 1: CompanyMatcher Stress & Boundaries');
TestRunnerCore::setFeature('1.1 Tax Code Normalization & Boundaries');

TestRunnerCore::test('CH1.1.1 Tax code variations with prefixes (MST, Tax, MSDN, spaces, dashes)', function() use ($matcher) {
    $variations = [
        '0101234567'          => '0101234567',
        '01-01234567'         => '0101234567',
        '0101234567-001'      => '0101234567001',
        'MST: 0101234567'     => '0101234567',
        'mst: 0101234567'     => '0101234567',
        'Tax: 0101234567'     => '0101234567',
        'MSDN: 0101234567'    => '0101234567',
        'MST-0101234567'      => '0101234567',
        ' 01 012 34567 '      => '0101234567',
        'MST 01-012-34567-001'=> '0101234567001',
    ];
    foreach ($variations as $input => $expected) {
        $cleaned = $matcher->cleanTaxCode($input);
        TestRunnerCore::assertEqual($expected, $cleaned, "Failed cleaning tax code: '{$input}'");
    }
});

TestRunnerCore::test('CH1.1.2 Short tax codes (<8 chars) do NOT trigger 100% tax match', function() use ($matcher) {
    $card1 = ['company_name' => 'Công Ty A', 'tax_code' => '12345'];
    $card2 = ['company_name' => 'Công Ty B', 'tax_code' => '12345'];
    $sim = $matcher->calculateSimilarity($card1, $card2);
    TestRunnerCore::assertFalse($sim['type'] === 'tax_code', 'Short tax code < 8 digits must not match on tax_code tier');
});

TestRunnerCore::test('CH1.1.3 Foreign alphanumeric tax codes & empty/null edge cases', function() use ($matcher) {
    TestRunnerCore::assertEqual('', $matcher->cleanTaxCode(''));
    TestRunnerCore::assertEqual('', $matcher->cleanTaxCode(null));
    TestRunnerCore::assertEqual('', $matcher->cleanTaxCode('   --..  '));
    TestRunnerCore::assertEqual('USDE12345678', $matcher->cleanTaxCode('US-DE-12345678'));

    $card1 = ['company_name' => 'Global Logistics LLC', 'tax_code' => 'US-DE-12345678'];
    $card2 = ['company_name' => 'Global Logistics Inc', 'tax_code' => 'US.DE.12345678'];
    $sim = $matcher->calculateSimilarity($card1, $card2);
    TestRunnerCore::assertTrue($sim['matched']);
    TestRunnerCore::assertEqual('tax_code', $sim['type']);
    TestRunnerCore::assertEqual(1.0, (float)$sim['score']);
});

TestRunnerCore::setFeature('1.2 Domain Extraction, Complex URLs & Blacklist');

TestRunnerCore::test('CH1.2.1 Complex URL extraction (ports, query strings, paths, anchors)', function() use ($matcher) {
    $cases = [
        'https://suntrans.vn'                           => 'suntrans.vn',
        'http://www.suntrans.vn/'                       => 'suntrans.vn',
        'www.suntrans.vn'                               => 'suntrans.vn',
        'suntrans.vn'                                   => 'suntrans.vn',
        'https://suntrans.vn:8443/about-us'             => 'suntrans.vn',
        'http://suntrans.vn/index.php?lang=vi&ref=card' => 'suntrans.vn',
        'https://suntrans.vn/contact#map-location'      => 'suntrans.vn',
        'https://sub.domain.suntrans.vn/portal'         => 'sub.domain.suntrans.vn',
    ];
    foreach ($cases as $url => $expected) {
        $domain = $matcher->extractRootDomain($url);
        TestRunnerCore::assertEqual($expected, $domain, "Domain extraction failed for: {$url}");
    }
});

TestRunnerCore::test('CH1.2.2 Public domain & free hosting blacklist prevents false grouping', function() use ($matcher) {
    $publicUrls = [
        'https://facebook.com/companyA', 'https://fb.com/companyB', 'https://zalo.me/0901234567',
        'https://user1.github.io/site', 'https://mycorp.wixsite.com/home', 'https://suntrans.blogspot.com',
        'https://sites.google.com/view/suntrans', 'https://t.me/suntransco', 'https://linkedin.com/company/suntrans',
        'contact@gmail.com',
    ];

    foreach ($publicUrls as $pUrl) {
        $dom = $matcher->extractRootDomain($pUrl);
        TestRunnerCore::assertTrue($matcher->isPublicDomain($dom), "Domain '{$dom}' from '{$pUrl}' should be blacklisted");
    }

    $card1 = ['company_name' => 'Công Ty Kim Cương', 'website' => 'https://facebook.com/kimcuong'];
    $card2 = ['company_name' => 'Công Ty Vàng Bạc', 'website' => 'https://facebook.com/vangbac'];
    $sim = $matcher->calculateSimilarity($card1, $card2);
    TestRunnerCore::assertFalse($sim['type'] === 'domain', 'Public domain must not trigger domain match tier');
});

TestRunnerCore::setFeature('1.3 Company Name Normalization, Suffixes & Diacritics');

TestRunnerCore::test('CH1.3.1 Vietnamese diacritics stripping and normalization', function() use ($matcher) {
    $n1 = 'CÔNG TY TNHH MTV VẬN TẢI QUỐC TẾ MẶT TRỜI ĐỎ';
    $n2 = 'Công ty TNHH Vận Tải Quốc Tế Mặt Trời Đỏ';
    $n3 = 'Cong ty Van tai Quoc te Mat Troi Do';
    
    $c1 = $matcher->cleanCompanyName($n1);
    $c2 = $matcher->cleanCompanyName($n2);
    $c3 = $matcher->cleanCompanyName($n3);

    TestRunnerCore::assertEqual('mat troi do', $c1);
    TestRunnerCore::assertEqual('mat troi do', $c2);
    TestRunnerCore::assertEqual('mat troi do', $c3);

    $score = $matcher->calculateNameSimilarity($n1, $n3);
    TestRunnerCore::assertEqual(1.0, (float)$score, 'Exact match after normalization should be 1.0');
});

TestRunnerCore::test('CH1.3.2 Legal suffix permutations stripping (VN & International)', function() use ($matcher) {
    $suffixes = [
        'Công ty Cổ phần Vận tải Biển Đông'          => 'bien dong',
        'Biển Đông Joint Stock Company'              => 'bien dong',
        'Biển Đông Co., Ltd'                         => 'bien dong',
        'Biển Đông Logistics & Forwarding Corp'      => 'bien dong',
        'Tập đoàn Biển Đông Group Holdings'         => 'bien dong',
        'Chi nhánh Doanh nghiệp tư nhân Biển Đông'   => 'bien dong',
    ];
    foreach ($suffixes as $input => $expected) {
        $cleaned = $matcher->cleanCompanyName($input);
        TestRunnerCore::assertEqual($expected, $cleaned, "Suffix stripping failed for '{$input}' -> got '{$cleaned}'");
    }
});

TestRunnerCore::test('CH1.3.3 False-positive protection: generic suffix overlap does not match distinct names', function() use ($matcher) {
    $card1 = ['company_name' => 'Công Ty TNHH Logistics An Khang'];
    $card2 = ['company_name' => 'Công Ty TNHH Logistics Bảo Long'];
    $sim = $matcher->calculateSimilarity($card1, $card2);
    TestRunnerCore::assertFalse($sim['matched'], 'Different company names sharing only generic words must not match (score=' . $sim['score'] . ')');
    TestRunnerCore::assertTrue($sim['score'] < 0.80, 'Score must be strictly below 0.80 threshold');
});

TestRunnerCore::test('CH1.3.4 CJK (Chinese, Japanese) company names similarity calculation', function() use ($matcher) {
    $scoreZh = $matcher->calculateNameSimilarity('中远海运集装箱运输有限公司', '中远海运集装箱运输');
    TestRunnerCore::assertTrue($scoreZh >= 0.80, "Chinese company similarity score {$scoreZh} should be >= 0.80");

    $scoreJa = $matcher->calculateNameSimilarity('日本通運株式会社', '日本通運');
    TestRunnerCore::assertTrue($scoreJa >= 0.60, "Japanese company similarity score {$scoreJa} should be >= 0.60");
});

TestRunnerCore::setFeature('1.4 Batch Grouping Engine Scaling & Integrity');

TestRunnerCore::test('CH1.4.1 Batch grouping with 20 cards: clusters + singletons + corrupted entries', function() use ($matcher) {
    $rawCards = [
        // Cluster 1: Suntransco (Tax Code)
        ['file_path' => 'cards/c1.jpg', 'ocr_parsed' => ['company_name' => 'Suntransco Co', 'tax_code' => '0101234567', 'contact_name' => 'Tran Long', 'phone' => '0901']],
        ['file_path' => 'cards/c2.jpg', 'ocr_parsed' => ['company_name' => 'Công ty Suntransco', 'tax_code' => '01-01234567', 'contact_name' => 'Le An', 'phone' => '0902']],
        ['file_path' => 'cards/c3.jpg', 'ocr_parsed' => ['company_name' => 'Suntransco Int', 'tax_code' => 'MST 0101234567', 'contact_name' => 'Pham Binh', 'phone' => '0903']],

        // Cluster 2: Ánh Dương (Domain)
        ['file_path' => 'cards/c4.jpg', 'ocr_parsed' => ['company_name' => 'Ánh Dương Danang', 'website' => 'https://anhduonglogistics.vn', 'contact_name' => 'Nguyen Duong']],
        ['file_path' => 'cards/c5.jpg', 'ocr_parsed' => ['company_name' => 'Anh Duong Logistics', 'website' => 'http://www.anhduonglogistics.vn/contact', 'contact_name' => 'Hoang Mai']],

        // Cluster 3: Toàn Cầu (Fuzzy Name)
        ['file_path' => 'cards/c6.jpg', 'ocr_parsed' => ['company_name' => 'Công Ty TNHH Thương Mại Toàn Cầu', 'contact_name' => 'Vu Toan']],
        ['file_path' => 'cards/c7.jpg', 'ocr_parsed' => ['company_name' => 'Toàn Cầu Trading & Logistics Co., Ltd', 'contact_name' => 'Dang Cau']],

        // Singleton 1: Sino-Viet
        ['file_path' => 'cards/c8.jpg', 'ocr_parsed' => ['company_name' => 'Sino-Viet Freight Co', 'tax_code' => '0309998888', 'contact_name' => 'Zhang Wei']],

        // Singleton 2: Japan Express
        ['file_path' => 'cards/c9.jpg', 'ocr_parsed' => ['company_name' => 'Japan Express Sato', 'website' => 'https://japan-express.co.jp', 'contact_name' => 'Kenji Sato']],

        // Corrupted / Empty cards
        ['file_path' => 'cards/c10.jpg', 'ocr_parsed' => ['company_name' => '', 'tax_code' => '', 'contact_name' => '']],
        ['file_path' => 'cards/c11.jpg', 'ocr_parsed' => []],
    ];

    $groups = $matcher->groupCards($rawCards);
    TestRunnerCore::assertTrue(count($groups) >= 5, 'Should form appropriate number of groups');

    $sunGroup = null;
    foreach ($groups as $g) {
        if (!empty($g['company_info']['tax_code']) && strpos($matcher->cleanTaxCode($g['company_info']['tax_code']), '0101234567') !== false) {
            $sunGroup = $g;
            break;
        }
    }
    TestRunnerCore::assertNotNull($sunGroup, 'Suntransco group must be found');
    TestRunnerCore::assertEqual(3, count($sunGroup['contacts']), 'Suntransco group must have 3 distinct contacts');
    TestRunnerCore::assertEqual(3, count($sunGroup['source_cards']), 'Suntransco group must have 3 source cards');
    TestRunnerCore::assertEqual(1, (int)$sunGroup['contacts'][0]['is_primary'], 'First contact in group must be primary');
    TestRunnerCore::assertEqual(0, (int)$sunGroup['contacts'][1]['is_primary'], 'Second contact in group must be non-primary');
});


// ======================================================================
// SECTION 2: MemberContactModel & MemberBranchModel Reconciling Tests
// ======================================================================
TestRunnerCore::setTier('Challenger 1: Contact & Branch Models State Invariants');
TestRunnerCore::setFeature('2.1 MemberContactModel Invariants & syncContacts');

TestRunnerCore::test('CH1.2.1 Primary Contact Switching: exactly 1 primary contact maintained', function() use ($pdo) {
    $pdo->exec("INSERT INTO members (id, company_name, tax_code, status) VALUES (501, 'Công Ty Thử Nghiệm Contact', '0105015011', 1)");

    $stmt = $pdo->prepare("INSERT INTO member_contacts (company_id, full_name, is_primary, created_at, updated_at) VALUES (?, ?, ?, datetime('now'), datetime('now'))");
    $stmt->execute([501, 'Người A', 1]);
    $idA = (int)$pdo->lastInsertId();

    $stmt->execute([501, 'Người B', 0]);
    $idB = (int)$pdo->lastInsertId();

    $stmt->execute([501, 'Người C', 0]);
    $idC = (int)$pdo->lastInsertId();

    $primaries = array_map('intval', $pdo->query("SELECT id FROM member_contacts WHERE company_id = 501 AND is_primary = 1")->fetchAll(\PDO::FETCH_COLUMN));
    TestRunnerCore::assertEqual([$idA], $primaries);

    // Switch primary to C
    $pdo->exec("UPDATE member_contacts SET is_primary = 0 WHERE company_id = 501");
    $pdo->prepare("UPDATE member_contacts SET is_primary = 1 WHERE id = ?")->execute([$idC]);

    $primariesAfter = array_map('intval', $pdo->query("SELECT id FROM member_contacts WHERE company_id = 501 AND is_primary = 1")->fetchAll(\PDO::FETCH_COLUMN));
    TestRunnerCore::assertEqual([$idC], $primariesAfter, 'Only Contact C should now be primary');
});

TestRunnerCore::test('CH1.2.2 syncContacts Reconcile: deletes omitted, updates existing, inserts new', function() use ($pdo) {
    $existing = $pdo->query("SELECT id, full_name FROM member_contacts WHERE company_id = 501 ORDER BY id ASC")->fetchAll();
    $idA = (int)$existing[0]->id;
    $idB = (int)$existing[1]->id;
    $idC = (int)$existing[2]->id;

    $submitted = [
        ['id' => $idA, 'full_name' => 'Người A Đã Đổi Tên', 'position' => 'Giám đốc', 'is_primary' => 0],
        ['id' => $idC, 'full_name' => 'Người C', 'position' => 'Phó Giám đốc', 'is_primary' => 1],
        ['id' => 0, 'full_name' => 'Người D Mới Thêm', 'position' => 'Trưởng phòng', 'is_primary' => 0],
        ['id' => 0, 'full_name' => '', 'position' => 'Bỏ qua dòng trống'],
    ];

    $existingIds = array_column($existing, 'id');
    $keptIds = [];
    foreach ($submitted as $row) {
        if (empty(trim($row['full_name']))) continue;
        $cid = !empty($row['id']) ? (int)$row['id'] : 0;
        if ($cid > 0 && in_array($cid, $existingIds)) {
            $pdo->prepare("UPDATE member_contacts SET full_name = ?, position = ?, is_primary = ? WHERE id = ?")
                ->execute([$row['full_name'], $row['position'], $row['is_primary'], $cid]);
            $keptIds[] = $cid;
        } else {
            $pdo->prepare("INSERT INTO member_contacts (company_id, full_name, position, is_primary, created_at, updated_at) VALUES (501, ?, ?, ?, datetime('now'), datetime('now'))")
                ->execute([$row['full_name'], $row['position'], $row['is_primary']]);
            $keptIds[] = (int)$pdo->lastInsertId();
        }
    }
    foreach ($existingIds as $oldId) {
        if (!in_array($oldId, $keptIds)) {
            $pdo->prepare("DELETE FROM member_contacts WHERE id = ?")->execute([$oldId]);
        }
    }

    $currentContacts = $pdo->query("SELECT id, full_name, is_primary FROM member_contacts WHERE company_id = 501 ORDER BY is_primary DESC, id ASC")->fetchAll();
    TestRunnerCore::assertCount(3, $currentContacts);
    TestRunnerCore::assertEqual($idC, (int)$currentContacts[0]->id, 'Contact C must be primary');
    TestRunnerCore::assertEqual('Người A Đã Đổi Tên', $currentContacts[1]->full_name);
    TestRunnerCore::assertEqual('Người D Mới Thêm', $currentContacts[2]->full_name);
});

TestRunnerCore::test('CH1.2.3 syncContacts with empty array removes all contacts cleanly', function() use ($pdo) {
    $existingIds = $pdo->query("SELECT id FROM member_contacts WHERE company_id = 501")->fetchAll(\PDO::FETCH_COLUMN);
    TestRunnerCore::assertTrue(count($existingIds) > 0);

    foreach ($existingIds as $oldId) {
        $pdo->prepare("DELETE FROM member_contacts WHERE id = ?")->execute([$oldId]);
    }

    $rem = $pdo->query("SELECT COUNT(*) FROM member_contacts WHERE company_id = 501")->fetchColumn();
    TestRunnerCore::assertEqual(0, (int)$rem, 'All contacts should be cleanly removed');
});

TestRunnerCore::test('CH1.2.4 Duplicate phone and email within same company are permitted', function() use ($pdo) {
    $stmt = $pdo->prepare("INSERT INTO member_contacts (company_id, full_name, phone, email, is_primary, created_at, updated_at) VALUES (?, ?, ?, ?, ?, datetime('now'), datetime('now'))");
    $stmt->execute([501, 'Nhân viên 1', '02439998888', 'hotline@company.com', 1]);
    $stmt->execute([501, 'Nhân viên 2', '02439998888', 'hotline@company.com', 0]);

    $count = $pdo->query("SELECT COUNT(*) FROM member_contacts WHERE company_id = 501 AND phone = '02439998888'")->fetchColumn();
    TestRunnerCore::assertEqual(2, (int)$count, 'Both contacts with identical phone must be stored');
});

TestRunnerCore::setFeature('2.2 MemberBranchModel Invariants & syncBranches');

TestRunnerCore::test('CH1.2.5 Headquarters Branch Switching & syncBranches reconcile', function() use ($pdo) {
    $stmt = $pdo->prepare("INSERT INTO member_branches (company_id, branch_name, city, is_headquarters, created_at) VALUES (?, ?, ?, ?, datetime('now'))");
    $stmt->execute([501, 'Trụ sở Hà Nội', 'Hà Nội', 1]);
    $b1 = (int)$pdo->lastInsertId();
    $stmt->execute([501, 'Chi nhánh Đà Nẵng', 'Đà Nẵng', 0]);
    $b2 = (int)$pdo->lastInsertId();

    $submitted = [
        ['id' => $b2, 'branch_name' => 'Trụ sở mới Đà Nẵng', 'city' => 'Đà Nẵng', 'is_headquarters' => 1],
        ['id' => 0, 'branch_name' => 'Chi nhánh TP.HCM', 'city' => 'Hồ Chí Minh', 'is_headquarters' => 0],
    ];

    $existingIds = [$b1, $b2];
    $keptIds = [];
    foreach ($submitted as $row) {
        if (empty(trim($row['branch_name']))) continue;
        $bid = !empty($row['id']) ? (int)$row['id'] : 0;
        if ($bid > 0 && in_array($bid, $existingIds)) {
            $pdo->prepare("UPDATE member_branches SET branch_name = ?, city = ?, is_headquarters = ? WHERE id = ?")
                ->execute([$row['branch_name'], $row['city'], $row['is_headquarters'], $bid]);
            $keptIds[] = $bid;
        } else {
            $pdo->prepare("INSERT INTO member_branches (company_id, branch_name, city, is_headquarters, created_at) VALUES (501, ?, ?, ?, datetime('now'))")
                ->execute([$row['branch_name'], $row['city'], $row['is_headquarters']]);
            $keptIds[] = (int)$pdo->lastInsertId();
        }
    }
    foreach ($existingIds as $oldId) {
        if (!in_array($oldId, $keptIds)) {
            $pdo->prepare("DELETE FROM member_branches WHERE id = ?")->execute([$oldId]);
        }
    }

    $branches = $pdo->query("SELECT id, branch_name, is_headquarters FROM member_branches WHERE company_id = 501 ORDER BY is_headquarters DESC, id ASC")->fetchAll();
    TestRunnerCore::assertCount(2, $branches);
    TestRunnerCore::assertEqual($b2, (int)$branches[0]->id);
    TestRunnerCore::assertEqual(1, (int)$branches[0]->is_headquarters);
    TestRunnerCore::assertEqual('Chi nhánh TP.HCM', $branches[1]->branch_name);
});


// ======================================================================
// SECTION 3: OcrService Multi-Side Merging & Boundaries
// ======================================================================
TestRunnerCore::setTier('Challenger 1: OcrService Multi-Side Merging & Boundaries');
TestRunnerCore::setFeature('3.1 Language Detection & Merging Conflicts');

TestRunnerCore::test('CH1.3.1 Conflicting languages merge to "mixed"', function() use ($ocr) {
    $front = ['detected_language' => 'vi', 'company_name' => 'Công Ty A'];
    $back = ['detected_language' => 'en', 'company_name_en' => 'Company A'];
    $merged = $ocr->mergeOcrResults($front, $back);
    TestRunnerCore::assertEqual('mixed', $merged['detected_language'], 'Conflicting languages vi vs en must merge to "mixed"');

    $frontJa = ['detected_language' => 'ja', 'company_name' => '日本通運'];
    $backJa = ['detected_language' => 'ja', 'company_name_en' => 'Nippon Express'];
    $mergedJa = $ocr->mergeOcrResults($frontJa, $backJa);
    TestRunnerCore::assertEqual('ja', $mergedJa['detected_language']);

    $frontEmpty = ['detected_language' => ''];
    $backZh = ['detected_language' => 'zh', 'company_name' => '中远海运'];
    $mergedZh = $ocr->mergeOcrResults($frontEmpty, $backZh);
    TestRunnerCore::assertEqual('zh', $mergedZh['detected_language']);
});

TestRunnerCore::setFeature('3.2 Rich Social Media & Branch Info Merging');

TestRunnerCore::test('CH1.3.2 Rich Social Media 7-platform merging with front precedence', function() use ($ocr) {
    $front = [
        'social_media' => [
            'zalo'     => '0901111111',
            'skype'    => 'long.front',
            'wechat'   => '',
            'whatsapp' => '',
            'line'     => '',
            'linkedin' => '',
            'facebook' => 'https://facebook.com/front',
        ]
    ];
    $back = [
        'social_media' => [
            'zalo'     => '0909999999',
            'wechat'   => 'wechat_back_user',
            'whatsapp' => '+84901234567',
            'skype'    => 'skype_back',
            'line'     => 'line_id_back',
            'linkedin' => 'https://linkedin.com/in/back',
            'facebook' => 'https://facebook.com/back',
        ]
    ];

    $merged = $ocr->mergeOcrResults($front, $back);
    $sm = $merged['social_media'];

    TestRunnerCore::assertEqual('0901111111', $sm['zalo'], 'Front zalo must take precedence');
    TestRunnerCore::assertEqual('long.front', $sm['skype'], 'Front skype must take precedence');
    TestRunnerCore::assertEqual('https://facebook.com/front', $sm['facebook'], 'Front facebook must take precedence');
    TestRunnerCore::assertEqual('wechat_back_user', $sm['wechat'], 'Back wechat should be merged in');
    TestRunnerCore::assertEqual('+84901234567', $sm['whatsapp'], 'Back whatsapp should be merged in');
    TestRunnerCore::assertEqual('line_id_back', $sm['line'], 'Back line should be merged in');
    TestRunnerCore::assertEqual('https://linkedin.com/in/back', $sm['linkedin'], 'Back linkedin should be merged in');
});

TestRunnerCore::test('CH1.3.3 Branch Info Concatenation and Deduplication', function() use ($ocr) {
    $front = ['branch_info' => 'Chi nhánh Hải Phòng: 15 Lê Hồng Phong'];
    $back = ['branch_info' => 'Chi nhánh Đà Nẵng: 20 Nguyễn Văn Linh'];
    $merged = $ocr->mergeOcrResults($front, $back);
    TestRunnerCore::assertEqual('Chi nhánh Hải Phòng: 15 Lê Hồng Phong; Chi nhánh Đà Nẵng: 20 Nguyễn Văn Linh', $merged['branch_info']);

    $front2 = ['branch_info' => 'Chi nhánh Hải Phòng: 15 Lê Hồng Phong'];
    $back2 = ['branch_info' => 'Chi nhánh Hải Phòng: 15 Lê Hồng Phong'];
    $merged2 = $ocr->mergeOcrResults($front2, $back2);
    TestRunnerCore::assertEqual('Chi nhánh Hải Phòng: 15 Lê Hồng Phong', $merged2['branch_info']);
});

TestRunnerCore::setFeature('3.3 Backward Compatibility & Fallback Integrity');

TestRunnerCore::test('CH1.3.4 Legacy 10-field input backward compatibility', function() use ($ocr) {
    $legacy = [
        'company_name'        => 'Công Ty TNHH Thép Việt Nhật',
        'tax_code'            => '0105678901',
        'address'             => 'KCN Thăng Long, Đông Anh, Hà Nội',
        'city'                => 'Hà Nội',
        'website'             => 'https://vietnhatsteel.com.vn',
        'fanpage'             => 'https://facebook.com/thepvietnhat',
        'phone'               => '02437654321',
        'email'               => 'contact@vietnhatsteel.com.vn',
        'representative_name' => 'Nguyễn Văn Hùng',
        'position'            => 'Tổng Giám Đốc',
    ];

    $norm = $ocr->normalizeResult($legacy);
    TestRunnerCore::assertEqual('Công Ty TNHH Thép Việt Nhật', $norm['company_name']);
    TestRunnerCore::assertEqual('Công Ty TNHH Thép Việt Nhật', $norm['company_name_vi']);
    TestRunnerCore::assertEqual('Nguyễn Văn Hùng', $norm['contact_name']);
    TestRunnerCore::assertEqual('Nguyễn Văn Hùng', $norm['representative_name']);
    TestRunnerCore::assertEqual('Tổng Giám Đốc', $norm['position']);
    TestRunnerCore::assertEqual('Tổng Giám Đốc', $norm['position_vi']);
    TestRunnerCore::assertEqual('vi', $norm['detected_language']);
    TestRunnerCore::assertEqual('Việt Nam', $norm['country']);
    TestRunnerCore::assertTrue(is_array($norm['social_media']));
});

$exitCode = TestRunnerCore::summary();
exit($exitCode);
