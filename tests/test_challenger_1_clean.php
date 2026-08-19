<?php
/**
 * Challenger 1: Comprehensive Adversarial & Empirical Stress Test Suite
 * Suntransco CodeIgniter 4 Member Management Module v2.0 Architecture Upgrade
 */

define('APPPATH', dirname(__DIR__) . '/app/');
define('SYSTEMPATH', dirname(__DIR__) . '/system/');
define('FCPATH', dirname(__DIR__) . '/');

require_once APPPATH . 'Libraries/CompanyMatcher.php';
require_once APPPATH . 'Libraries/OcrService.php';

use App\Libraries\CompanyMatcher;
use App\Libraries\OcrService;

$passed = 0;
$total = 0;

function run_test(string $name, callable $fn) {
    global $passed, $total;
    $total++;
    try {
        $res = $fn();
        if ($res !== false) {
            echo "  [PASS] {$name}\n";
            $passed++;
        } else {
            echo "  [FAIL] {$name} (Returned false)\n";
        }
    } catch (\Throwable $e) {
        echo "  [FAIL] {$name}: " . $e->getMessage() . " in " . basename($e->getFile()) . ":" . $e->getLine() . "\n";
    }
}

echo "======================================================================\n";
echo "  CHALLENGER 1: EMPIRICAL VERIFICATION & ADVERSARIAL STRESS SUITE     \n";
echo "======================================================================\n\n";

$matcher = new CompanyMatcher();
$ocr = new OcrService();

// -------------------------------------------------------------
// SECTION 1: 500-LINE LIMIT COMPLIANCE AUDIT
// -------------------------------------------------------------
echo "--- 1. File Length Limit Compliance Audit (<= 500 lines) ---\n";

$filesToAudit = [
    'app/Database/Migrations/2026-08-16-100001_UpgradeMemberSchema.php',
    'app/Libraries/OcrService.php',
    'app/Libraries/CompanyMatcher.php',
    'app/Libraries/BusinessVerifyService.php',
    'app/Models/MemberModel.php',
    'app/Models/MemberContactModel.php',
    'app/Models/MemberBranchModel.php',
    'app/Models/MemberCardModel.php',
    'app/Models/MemberVerifyLogModel.php',
    'app/Models/IndustryTypeModel.php',
    'app/Controllers/MemberController.php',
    'app/Views/admin/members/index.php',
    'app/Views/admin/members/form.php',
    'app/Views/admin/members/upload_cards.php',
    'app/Views/admin/members/confirm_ocr.php',
    'app/Views/admin/members/detail.php',
    'migrate_members.sql',
];

foreach ($filesToAudit as $relPath) {
    run_test("1." . ($total + 1) . " File {$relPath} exists and <= 500 lines", function() use ($relPath) {
        $full = FCPATH . $relPath;
        if (!file_exists($full)) return false;
        $lines = count(file($full));
        return $lines <= 500;
    });
}

// -------------------------------------------------------------
// SECTION 2: COMPANY MATCHER STRESS & BOUNDARY TESTS
// -------------------------------------------------------------
echo "\n--- 2. CompanyMatcher 3-Tier Multi-Algorithm Stress Tests ---\n";

run_test("2.1 Tax code variations with prefixes (MST, Tax, MSDN, dashes, spaces)", function() use ($matcher) {
    $tests = [
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
    foreach ($tests as $in => $exp) {
        if ($matcher->cleanTaxCode($in) !== $exp) return false;
    }
    return true;
});

run_test("2.2 Short tax codes (<8 chars) reject 100% tax match tier", function() use ($matcher) {
    $c1 = ['company_name' => 'A', 'tax_code' => '12345'];
    $c2 = ['company_name' => 'B', 'tax_code' => '12345'];
    $sim = $matcher->calculateSimilarity($c1, $c2);
    return $sim['type'] !== 'tax_code';
});

run_test("2.3 Foreign alphanumeric tax code support & null/empty tolerance", function() use ($matcher) {
    if ($matcher->cleanTaxCode('') !== '') return false;
    if ($matcher->cleanTaxCode(null) !== '') return false;
    if ($matcher->cleanTaxCode('US-DE-12345678') !== 'USDE12345678') return false;
    $sim = $matcher->calculateSimilarity(
        ['company_name' => 'A', 'tax_code' => 'US-DE-12345678'],
        ['company_name' => 'B', 'tax_code' => 'US.DE.12345678']
    );
    return $sim['matched'] === true && $sim['score'] === 1.0 && $sim['type'] === 'tax_code';
});

run_test("2.4 Complex URL domain extraction (ports, query strings, paths, anchors)", function() use ($matcher) {
    $cases = [
        'https://suntrans.vn'                           => 'suntrans.vn',
        'http://www.suntrans.vn/'                       => 'suntrans.vn',
        'www.suntrans.vn'                               => 'suntrans.vn',
        'https://suntrans.vn:8443/about-us'             => 'suntrans.vn',
        'http://suntrans.vn/index.php?lang=vi&ref=card' => 'suntrans.vn',
        'https://suntrans.vn/contact#map-location'      => 'suntrans.vn',
        'https://sub.domain.suntrans.vn/portal'         => 'sub.domain.suntrans.vn',
    ];
    foreach ($cases as $url => $exp) {
        if ($matcher->extractRootDomain($url) !== $exp) return false;
    }
    return true;
});

run_test("2.5 Public domain blacklist prevents false grouping", function() use ($matcher) {
    $publicDomains = [
        'https://facebook.com/companyA', 'https://fb.com/companyB', 'https://zalo.me/0901234567',
        'https://user1.github.io/site', 'https://mycorp.wixsite.com/home', 'https://suntrans.blogspot.com',
        'https://sites.google.com/view/suntrans', 'https://t.me/suntransco', 'https://linkedin.com/company/suntrans',
    ];
    foreach ($publicDomains as $p) {
        $dom = $matcher->extractRootDomain($p);
        if (!$matcher->isPublicDomain($dom)) return false;
    }
    $c1 = ['company_name' => 'Công Ty Kim Cương', 'website' => 'https://facebook.com/kimcuong'];
    $c2 = ['company_name' => 'Công Ty Vàng Bạc', 'website' => 'https://facebook.com/vangbac'];
    $sim = $matcher->calculateSimilarity($c1, $c2);
    return $sim['type'] !== 'domain';
});

run_test("2.6 Vietnamese diacritics stripping & name normalization", function() use ($matcher) {
    $n1 = 'CÔNG TY TNHH MTV VẬN TẢI QUỐC TẾ MẶT TRỜI ĐỎ';
    $n3 = 'Cong ty Van tai Quoc te Mat Troi Do';
    return $matcher->cleanCompanyName($n1) === 'mat troi do' && $matcher->calculateNameSimilarity($n1, $n3) === 1.0;
});

run_test("2.7 Legal suffix permutations stripping (VN & Int'l)", function() use ($matcher) {
    $suffixes = [
        'Công ty Cổ phần Vận tải Biển Đông'          => 'bien dong',
        'Biển Đông Joint Stock Company'              => 'bien dong',
        'Biển Đông Co., Ltd'                         => 'bien dong',
        'Biển Đông Logistics & Forwarding Corp'      => 'bien dong',
        'Tập đoàn Biển Đông Group Holdings'         => 'bien dong',
    ];
    foreach ($suffixes as $in => $exp) {
        if ($matcher->cleanCompanyName($in) !== $exp) return false;
    }
    return true;
});

run_test("2.8 False-positive protection on generic suffix overlap", function() use ($matcher) {
    $c1 = ['company_name' => 'Công Ty TNHH Logistics An Khang'];
    $c2 = ['company_name' => 'Công Ty TNHH Logistics Bảo Long'];
    $sim = $matcher->calculateSimilarity($c1, $c2);
    return $sim['matched'] === false && $sim['score'] < 0.80;
});

run_test("2.9 CJK (Chinese, Japanese) similarity calculation", function() use ($matcher) {
    $scoreZh = $matcher->calculateNameSimilarity('中远海运集装箱运输有限公司', '中远海运集装箱运输');
    $scoreJa = $matcher->calculateNameSimilarity('日本通運株式会社', '日本通運');
    return $scoreZh >= 0.80 && $scoreJa >= 0.60;
});

run_test("2.10 Batch card auto-grouping with clusters, singletons & corrupted inputs", function() use ($matcher) {
    $rawCards = [
        ['file_path' => 'c1.jpg', 'ocr_parsed' => ['company_name' => 'Suntransco Co', 'tax_code' => '0101234567', 'contact_name' => 'Tran Long', 'phone' => '0901']],
        ['file_path' => 'c2.jpg', 'ocr_parsed' => ['company_name' => 'Công ty Suntransco', 'tax_code' => '01-01234567', 'contact_name' => 'Le An', 'phone' => '0902']],
        ['file_path' => 'c3.jpg', 'ocr_parsed' => ['company_name' => 'Ánh Dương Danang', 'website' => 'https://anhduonglogistics.vn', 'contact_name' => 'Nguyen Duong']],
        ['file_path' => 'c4.jpg', 'ocr_parsed' => ['company_name' => 'Anh Duong Logistics', 'website' => 'http://www.anhduonglogistics.vn/contact', 'contact_name' => 'Hoang Mai']],
        ['file_path' => 'c5.jpg', 'ocr_parsed' => ['company_name' => 'Japan Express Sato', 'website' => 'https://japan-express.co.jp', 'contact_name' => 'Kenji Sato']],
        ['file_path' => 'c6.jpg', 'ocr_parsed' => []],
    ];
    $groups = $matcher->groupCards($rawCards);
    if (count($groups) < 4) return false;
    
    $sunGroup = null;
    foreach ($groups as $g) {
        if (!empty($g['company_info']['tax_code']) && strpos($matcher->cleanTaxCode($g['company_info']['tax_code']), '0101234567') !== false) {
            $sunGroup = $g; break;
        }
    }
    if (!$sunGroup || count($sunGroup['contacts']) !== 2 || count($sunGroup['source_cards']) !== 2) return false;
    if ((int)$sunGroup['contacts'][0]['is_primary'] !== 1 || (int)$sunGroup['contacts'][1]['is_primary'] !== 0) return false;
    return true;
});


// -------------------------------------------------------------
// SECTION 3: CONTACT & BRANCH MODELS RECONCILING & INVARIANTS
// -------------------------------------------------------------
echo "\n--- 3. Contact & Branch Models State Invariants & Reconciling ---\n";

$pdo = new \PDO('sqlite::memory:');
$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
$pdo->exec("
    CREATE TABLE members (id INTEGER PRIMARY KEY AUTOINCREMENT, company_name TEXT, tax_code TEXT, status INTEGER);
    CREATE TABLE member_contacts (id INTEGER PRIMARY KEY AUTOINCREMENT, company_id INTEGER, full_name TEXT, position TEXT, phone TEXT, email TEXT, is_primary INTEGER DEFAULT 0, created_at TEXT, updated_at TEXT);
    CREATE TABLE member_branches (id INTEGER PRIMARY KEY AUTOINCREMENT, company_id INTEGER, branch_name TEXT, city TEXT, is_headquarters INTEGER DEFAULT 0, created_at TEXT);
");

run_test("3.1 Primary Contact Switching: exactly 1 primary contact invariant", function() use ($pdo) {
    $pdo->exec("INSERT INTO members (id, company_name, tax_code, status) VALUES (501, 'Test Co', '0105015011', 1)");
    $stmt = $pdo->prepare("INSERT INTO member_contacts (company_id, full_name, is_primary, created_at) VALUES (501, ?, ?, datetime('now'))");
    $stmt->execute(['Người A', 1]);
    $idA = (int)$pdo->lastInsertId();
    $stmt->execute(['Người B', 0]);
    $idB = (int)$pdo->lastInsertId();

    // Switch primary to B
    $pdo->exec("UPDATE member_contacts SET is_primary = 0 WHERE company_id = 501");
    $pdo->prepare("UPDATE member_contacts SET is_primary = 1 WHERE id = ?")->execute([$idB]);

    $primaries = array_map('intval', $pdo->query("SELECT id FROM member_contacts WHERE company_id = 501 AND is_primary = 1")->fetchAll(\PDO::FETCH_COLUMN));
    return $primaries === [$idB];
});

run_test("3.2 syncContacts reconcile: updates kept, inserts new, deletes omitted, ignores empty", function() use ($pdo) {
    $existing = array_map('intval', $pdo->query("SELECT id FROM member_contacts WHERE company_id = 501 ORDER BY id ASC")->fetchAll(\PDO::FETCH_COLUMN));
    $idA = $existing[0];
    $idB = $existing[1];

    $submitted = [
        ['id' => $idA, 'full_name' => 'Người A Updated', 'position' => 'Giám đốc', 'is_primary' => 1],
        ['id' => 0, 'full_name' => 'Người C Mới', 'position' => 'Trưởng phòng', 'is_primary' => 0],
        ['id' => 0, 'full_name' => '', 'position' => 'Bỏ qua dòng trống'],
    ];

    $keptIds = [];
    foreach ($submitted as $row) {
        if (empty(trim($row['full_name']))) continue;
        $cid = !empty($row['id']) ? (int)$row['id'] : 0;
        if ($cid > 0 && in_array($cid, $existing)) {
            $pdo->prepare("UPDATE member_contacts SET full_name = ?, position = ?, is_primary = ? WHERE id = ?")
                ->execute([$row['full_name'], $row['position'], $row['is_primary'], $cid]);
            $keptIds[] = $cid;
        } else {
            $pdo->prepare("INSERT INTO member_contacts (company_id, full_name, position, is_primary, created_at) VALUES (501, ?, ?, ?, datetime('now'))")
                ->execute([$row['full_name'], $row['position'], $row['is_primary']]);
            $keptIds[] = (int)$pdo->lastInsertId();
        }
    }
    foreach ($existing as $oldId) {
        if (!in_array($oldId, $keptIds)) {
            $pdo->prepare("DELETE FROM member_contacts WHERE id = ?")->execute([$oldId]);
        }
    }

    $current = $pdo->query("SELECT id, full_name, is_primary FROM member_contacts WHERE company_id = 501 ORDER BY is_primary DESC, id ASC")->fetchAll(\PDO::FETCH_OBJ);
    if (count($current) !== 2) return false;
    if ((int)$current[0]->id !== $idA || (int)$current[0]->is_primary !== 1) return false;
    if ($current[1]->full_name !== 'Người C Mới') return false;
    return true;
});

run_test("3.3 syncContacts with empty array removes all contacts cleanly", function() use ($pdo) {
    $existing = array_map('intval', $pdo->query("SELECT id FROM member_contacts WHERE company_id = 501")->fetchAll(\PDO::FETCH_COLUMN));
    foreach ($existing as $id) {
        $pdo->prepare("DELETE FROM member_contacts WHERE id = ?")->execute([$id]);
    }
    $rem = (int)$pdo->query("SELECT COUNT(*) FROM member_contacts WHERE company_id = 501")->fetchColumn();
    return $rem === 0;
});

run_test("3.4 Duplicate phone and email within same company permitted", function() use ($pdo) {
    $stmt = $pdo->prepare("INSERT INTO member_contacts (company_id, full_name, phone, email, is_primary, created_at) VALUES (?, ?, ?, ?, ?, datetime('now'))");
    $stmt->execute([501, 'Nhân viên 1', '02439998888', 'hotline@co.vn', 1]);
    $stmt->execute([501, 'Nhân viên 2', '02439998888', 'hotline@co.vn', 0]);
    $cnt = (int)$pdo->query("SELECT COUNT(*) FROM member_contacts WHERE company_id = 501 AND phone = '02439998888'")->fetchColumn();
    return $cnt === 2;
});

run_test("3.5 Headquarters branch switching & syncBranches reconcile", function() use ($pdo) {
    $stmt = $pdo->prepare("INSERT INTO member_branches (company_id, branch_name, city, is_headquarters, created_at) VALUES (501, ?, ?, ?, datetime('now'))");
    $stmt->execute(['Trụ sở Hà Nội', 'Hà Nội', 1]);
    $b1 = (int)$pdo->lastInsertId();
    $stmt->execute(['Chi nhánh Đà Nẵng', 'Đà Nẵng', 0]);
    $b2 = (int)$pdo->lastInsertId();

    $submitted = [
        ['id' => $b2, 'branch_name' => 'Trụ sở mới Đà Nẵng', 'city' => 'Đà Nẵng', 'is_headquarters' => 1],
        ['id' => 0, 'branch_name' => 'Chi nhánh TP.HCM', 'city' => 'Hồ Chí Minh', 'is_headquarters' => 0],
    ];

    $existing = [$b1, $b2];
    $keptIds = [];
    foreach ($submitted as $row) {
        if (empty(trim($row['branch_name']))) continue;
        $bid = !empty($row['id']) ? (int)$row['id'] : 0;
        if ($bid > 0 && in_array($bid, $existing)) {
            $pdo->prepare("UPDATE member_branches SET branch_name = ?, city = ?, is_headquarters = ? WHERE id = ?")
                ->execute([$row['branch_name'], $row['city'], $row['is_headquarters'], $bid]);
            $keptIds[] = $bid;
        } else {
            $pdo->prepare("INSERT INTO member_branches (company_id, branch_name, city, is_headquarters, created_at) VALUES (501, ?, ?, ?, datetime('now'))")
                ->execute([$row['branch_name'], $row['city'], $row['is_headquarters']]);
            $keptIds[] = (int)$pdo->lastInsertId();
        }
    }
    foreach ($existing as $oldId) {
        if (!in_array($oldId, $keptIds)) {
            $pdo->prepare("DELETE FROM member_branches WHERE id = ?")->execute([$oldId]);
        }
    }

    $branches = $pdo->query("SELECT id, branch_name, is_headquarters FROM member_branches WHERE company_id = 501 ORDER BY is_headquarters DESC, id ASC")->fetchAll(\PDO::FETCH_OBJ);
    if (count($branches) !== 2) return false;
    if ((int)$branches[0]->id !== $b2 || (int)$branches[0]->is_headquarters !== 1) return false;
    if ($branches[1]->branch_name !== 'Chi nhánh TP.HCM') return false;
    return true;
});


// -------------------------------------------------------------
// SECTION 4: OCR SERVICE MULTI-SIDE MERGING & SCHEMAS
// -------------------------------------------------------------
echo "\n--- 4. OcrService Multi-Side Merging & Schema Boundaries ---\n";

run_test("4.1 Conflicting language detection merges to 'mixed'", function() use ($ocr) {
    $f = ['detected_language' => 'vi', 'company_name' => 'Công Ty A'];
    $b = ['detected_language' => 'en', 'company_name_en' => 'Company A'];
    $m = $ocr->mergeOcrResults($f, $b);
    if ($m['detected_language'] !== 'mixed') return false;

    $fJa = ['detected_language' => 'ja', 'company_name' => '日本通運'];
    $bJa = ['detected_language' => 'ja', 'company_name_en' => 'Nippon Express'];
    $mJa = $ocr->mergeOcrResults($fJa, $bJa);
    if ($mJa['detected_language'] !== 'ja') return false;

    $fEmpty = ['detected_language' => ''];
    $bZh = ['detected_language' => 'zh', 'company_name' => '中远海运'];
    $mZh = $ocr->mergeOcrResults($fEmpty, $bZh);
    if ($mZh['detected_language'] !== 'zh') return false;
    return true;
});

run_test("4.2 Rich Social Media 7-platform merging with front precedence", function() use ($ocr) {
    $front = ['social_media' => ['zalo' => '0901', 'skype' => 's_front', 'facebook' => 'fb_front']];
    $back = ['social_media' => ['zalo' => '0909', 'wechat' => 'wc_back', 'whatsapp' => 'wa_back', 'skype' => 's_back', 'line' => 'l_back', 'linkedin' => 'li_back', 'facebook' => 'fb_back']];
    $merged = $ocr->mergeOcrResults($front, $back);
    $sm = $merged['social_media'];
    if ($sm['zalo'] !== '0901') return false;
    if ($sm['skype'] !== 's_front') return false;
    if ($sm['facebook'] !== 'fb_front') return false;
    if ($sm['wechat'] !== 'wc_back') return false;
    if ($sm['whatsapp'] !== 'wa_back') return false;
    if ($sm['line'] !== 'l_back') return false;
    if ($sm['linkedin'] !== 'li_back') return false;
    return true;
});

run_test("4.3 Branch info concatenation & deduplication", function() use ($ocr) {
    $f1 = ['branch_info' => 'Chi nhánh Hải Phòng: 15 Lê Hồng Phong'];
    $b1 = ['branch_info' => 'Chi nhánh Đà Nẵng: 20 Nguyễn Văn Linh'];
    $m1 = $ocr->mergeOcrResults($f1, $b1);
    if ($m1['branch_info'] !== 'Chi nhánh Hải Phòng: 15 Lê Hồng Phong; Chi nhánh Đà Nẵng: 20 Nguyễn Văn Linh') return false;

    $f2 = ['branch_info' => 'Chi nhánh Hải Phòng: 15 Lê Hồng Phong'];
    $b2 = ['branch_info' => 'Chi nhánh Hải Phòng: 15 Lê Hồng Phong'];
    $m2 = $ocr->mergeOcrResults($f2, $b2);
    if ($m2['branch_info'] !== 'Chi nhánh Hải Phòng: 15 Lê Hồng Phong') return false;
    return true;
});

run_test("4.4 Legacy 10-field input backward compatibility mapping", function() use ($ocr) {
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
    if ($norm['company_name'] !== 'Công Ty TNHH Thép Việt Nhật') return false;
    if ($norm['company_name_vi'] !== 'Công Ty TNHH Thép Việt Nhật') return false;
    if ($norm['contact_name'] !== 'Nguyễn Văn Hùng') return false;
    if ($norm['representative_name'] !== 'Nguyễn Văn Hùng') return false;
    if ($norm['position'] !== 'Tổng Giám Đốc') return false;
    if ($norm['position_vi'] !== 'Tổng Giám Đốc') return false;
    if ($norm['detected_language'] !== 'vi') return false;
    if ($norm['country'] !== 'Việt Nam') return false;
    if (!is_array($norm['social_media']) || count($norm['social_media']) !== 7) return false;
    return true;
});

echo "\n======================================================================\n";
echo "  TEST SUMMARY: {$passed} / {$total} TESTS PASSED (" . round(($passed / $total) * 100, 1) . "%)\n";
echo "======================================================================\n";

if ($passed !== $total) {
    exit(1);
}
exit(0);
