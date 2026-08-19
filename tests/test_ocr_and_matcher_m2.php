<?php
/**
 * Test Suite for OcrService v2.0 & CompanyMatcher (Milestone 2)
 */

define('APPPATH', dirname(__DIR__) . '/app/');
define('SYSTEMPATH', dirname(__DIR__) . '/system/');
define('FCPATH', dirname(__DIR__) . '/');

if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool {
        return $needle === '' || strpos($haystack, $needle) !== false;
    }
}
if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool {
        return $needle === '' || strpos($haystack, $needle) === 0;
    }
}
if (!function_exists('str_ends_with')) {
    function str_ends_with(string $haystack, string $needle): bool {
        return $needle === '' || substr($haystack, -strlen($needle)) === $needle;
    }
}

require_once APPPATH . 'Libraries/OcrService.php';
require_once APPPATH . 'Libraries/CompanyMatcher.php';

use App\Libraries\OcrService;
use App\Libraries\CompanyMatcher;

echo "\n======================================================================\n";
echo "  OCR SERVICE v2.0 & COMPANY MATCHER COMPREHENSIVE VERIFICATION TEST  \n";
echo "======================================================================\n\n";

$ocr = new OcrService();
$matcher = new CompanyMatcher();

$passed = 0;
$total = 0;

function runTest(string $name, callable $fn) {
    global $passed, $total;
    $total++;
    try {
        $fn();
        echo "  [PASS] {$name}\n";
        $passed++;
    } catch (\Throwable $e) {
        echo "  [FAIL] {$name}: " . $e->getMessage() . "\n";
        echo "         at " . $e->getFile() . ":" . $e->getLine() . "\n";
    }
}

// -------------------------------------------------------------
// SECTION 1: OCR SERVICE v2.0 TESTS
// -------------------------------------------------------------
echo "--- 1. Testing OcrService v2.0 ---\n";

runTest("1.1 Empty input returns normalized 20+ fields schema", function() use ($ocr) {
    $empty = $ocr->extractBusinessCard('');
    if (count($empty) < 20) throw new Exception("Expected at least 20 keys, got " . count($empty));
    if (!isset($empty['company_name_vi'], $empty['company_name_en'], $empty['social_media'], $empty['branch_info'])) {
        throw new Exception("Missing required v2.0 keys in empty result");
    }
    if (!is_array($empty['social_media']) || count($empty['social_media']) !== 7) {
        throw new Exception("social_media must be an array of 7 platforms");
    }
    if ($empty['detected_language'] !== 'vi') throw new Exception("Default language should be 'vi'");
});

runTest("1.2 5 Rich Mock Stub Profiles coverage", function() use ($ocr) {
    $p1 = $ocr->extractBusinessCard('cards/card_suntrans.jpg');
    if ($p1['tax_code'] !== '0101234567' || empty($p1['company_name_en'])) {
        throw new Exception("Profile 1 (Suntransco) invalid");
    }

    $p2 = $ocr->extractBusinessCard('cards/anhduong_front.png');
    if ($p2['tax_code'] !== '0400123456' || $p2['city'] !== 'Đà Nẵng') {
        throw new Exception("Profile 2 (Ánh Dương) invalid");
    }

    $p3 = $ocr->extractBusinessCard('cards/toancau_global.jpg');
    if ($p3['detected_language'] !== 'mixed' || empty($p3['company_name_local'])) {
        throw new Exception("Profile 3 (Toàn Cầu) invalid");
    }

    $p4 = $ocr->extractBusinessCard('cards/sino_viet_freight.jpg');
    if ($p4['detected_language'] !== 'zh' || empty($p4['social_media']['wechat'])) {
        throw new Exception("Profile 4 (Sino-Viet) invalid");
    }

    $p5 = $ocr->extractBusinessCard('cards/japan_express_sato.jpg');
    if ($p5['detected_language'] !== 'ja' || empty($p5['social_media']['line'])) {
        throw new Exception("Profile 5 (Japan Express) invalid");
    }
});

runTest("1.3 Backward compatibility with legacy 10-field input", function() use ($ocr) {
    $legacy = [
        'company_name'        => 'Công Ty TNHH Giao Nhận ABC',
        'tax_code'            => '0109999888',
        'address'             => '123 Cầu Giấy, Hà Nội',
        'city'                => 'Hà Nội',
        'website'             => 'https://abc.vn',
        'fanpage'             => '',
        'phone'               => '02431112222',
        'email'               => 'info@abc.vn',
        'representative_name' => 'Nguyễn Văn Nam',
        'position'            => 'Giám Đốc',
    ];

    $norm = $ocr->normalizeResult($legacy);
    if ($norm['contact_name'] !== 'Nguyễn Văn Nam') {
        throw new Exception("contact_name was not populated from representative_name");
    }
    if ($norm['representative_name'] !== 'Nguyễn Văn Nam') {
        throw new Exception("representative_name not preserved");
    }
    if ($norm['company_name_vi'] !== 'Công Ty TNHH Giao Nhận ABC') {
        throw new Exception("company_name_vi was not populated from company_name");
    }
    if (!is_array($norm['social_media']) || !isset($norm['social_media']['zalo'])) {
        throw new Exception("social_media array not initialized");
    }
});

runTest("1.4 Merge front and back cards with social media and branch info", function() use ($ocr) {
    $front = [
        'company_name'      => 'Suntransco Logistics',
        'detected_language' => 'vi',
        'contact_name'      => 'Trần Hoàng Long',
        'position'          => 'CEO',
        'phone'             => '02439876543',
        'social_media'      => ['zalo' => '0912334455', 'facebook' => 'fb.com/long'],
        'branch_info'       => 'Chi nhánh Hải Phòng: 15 Lê Hồng Phong',
    ];
    $back = [
        'company_name_en'   => 'Suntransco International Transport',
        'detected_language' => 'en',
        'email'             => 'info@suntrans.vn',
        'social_media'      => ['wechat' => 'suntrans_long', 'whatsapp' => '+84912334455'],
        'branch_info'       => 'VPĐD TP.HCM: 88 Nguyễn Huệ',
    ];

    $merged = $ocr->mergeOcrResults($front, $back);
    if ($merged['company_name'] !== 'Suntransco Logistics') throw new Exception("Company name failed to merge");
    if ($merged['company_name_en'] !== 'Suntransco International Transport') throw new Exception("Company name EN failed to merge");
    if ($merged['detected_language'] !== 'mixed') throw new Exception("Differing languages should merge to 'mixed'");
    if ($merged['social_media']['zalo'] !== '0912334455' || $merged['social_media']['wechat'] !== 'suntrans_long') {
        throw new Exception("Social media array was not cleanly merged");
    }
    if (!str_contains($merged['branch_info'], 'Hải Phòng') || !str_contains($merged['branch_info'], 'TP.HCM')) {
        throw new Exception("branch_info was not concatenated");
    }
});

// -------------------------------------------------------------
// SECTION 2: COMPANY MATCHER TESTS
// -------------------------------------------------------------
echo "\n--- 2. Testing CompanyMatcher ---\n";

runTest("2.1 Tier 1: Exact Tax Code Match (100%)", function() use ($matcher) {
    $card1 = ['tax_code' => '0101234567-001', 'company_name' => 'Công ty A'];
    $card2 = ['tax_code' => '0101234567001', 'company_name' => 'Công ty B'];
    $card3 = ['tax_code' => 'MST: 0101234567-001', 'company_name' => 'Công ty C'];

    $sim1 = $matcher->calculateSimilarity($card1, $card2);
    if (!$sim1['matched'] || $sim1['score'] !== 1.0 || $sim1['type'] !== 'tax_code') {
        throw new Exception("Tax code match failed for format differences");
    }

    $sim2 = $matcher->calculateSimilarity($card1, $card3);
    if (!$sim2['matched'] || $sim2['score'] !== 1.0) {
        throw new Exception("Tax code prefix stripping failed");
    }
});

runTest("2.2 Tier 2: Website Root Domain Match (95%)", function() use ($matcher) {
    $card1 = ['tax_code' => '', 'website' => 'https://suntrans.vn/contact-us', 'company_name' => 'Suntrans'];
    $card2 = ['tax_code' => '', 'website' => 'http://www.suntrans.vn', 'company_name' => 'Suntransco Co'];

    $sim = $matcher->calculateSimilarity($card1, $card2);
    if (!$sim['matched'] || $sim['score'] !== 0.95 || $sim['type'] !== 'domain') {
        throw new Exception("Domain match failed: " . json_encode($sim));
    }
});

runTest("2.3 Tier 2 Domain Filter: Public hosting & social domains ignored", function() use ($matcher) {
    $card1 = ['tax_code' => '', 'website' => 'https://facebook.com/suntransco', 'company_name' => 'Company Alpha'];
    $card2 = ['tax_code' => '', 'website' => 'https://facebook.com/anhduong', 'company_name' => 'Company Beta'];

    $sim = $matcher->calculateSimilarity($card1, $card2);
    if ($sim['type'] === 'domain' && $sim['matched']) {
        throw new Exception("Public Facebook domain should not match different companies");
    }
});

runTest("2.4 Tier 3: Multilingual Fuzzy Company Name Match (>= 80%)", function() use ($matcher) {
    $card1 = ['company_name' => 'Công Ty TNHH Vận Tải Quốc Tế Suntransco'];
    $card2 = ['company_name' => 'CÔNG TY CỔ PHẦN VẬN TẢI QUỐC TẾ SUNTRANSCO'];
    $card3 = ['company_name_en' => 'Suntransco International Transport Co., Ltd', 'company_name' => 'Suntransco Transport'];

    $sim1 = $matcher->calculateSimilarity($card1, $card2);
    if (!$sim1['matched'] || $sim1['score'] < 0.80 || $sim1['type'] !== 'fuzzy_name') {
        throw new Exception("Fuzzy name match failed between TNHH and Cổ phần: " . json_encode($sim1));
    }

    $sim2 = $matcher->calculateSimilarity($card1, $card3);
    if (!$sim2['matched'] || $sim2['score'] < 0.80) {
        throw new Exception("Fuzzy name match failed across EN/VI fields: " . json_encode($sim2));
    }
});

runTest("2.5 Unstructured branch_info parsing into structured branches", function() use ($matcher) {
    $addr = '18 Phan Chu Trinh, Hoàn Kiếm, Hà Nội';
    $city = 'Hà Nội';
    $country = 'Việt Nam';
    $branchInfo = 'Chi nhánh Hải Phòng: 15 Lê Hồng Phong, Ngô Quyền, Hải Phòng; VPĐD TP.HCM: 88 Nguyễn Huệ, Quận 1, TP.HCM; Shenzhen Office: 8F Tower A, High-Tech Park, Nanshan, Shenzhen, China';

    $branches = $matcher->parseBranches($addr, $city, $country, $branchInfo, '02439876543', 'info@suntrans.vn');

    if (count($branches) !== 4) {
        throw new Exception("Expected 4 branches (1 HQ + 3 parsed), got " . count($branches));
    }

    if ($branches[0]['is_headquarters'] !== 1 || !str_contains($branches[0]['branch_name'], 'Trụ Sở Chính')) {
        throw new Exception("First branch must be Headquarters");
    }

    if ($branches[1]['is_headquarters'] !== 0 || !str_contains($branches[1]['branch_name'], 'Hải Phòng')) {
        throw new Exception("Second branch should be Hải Phòng");
    }

    if ($branches[3]['country'] !== 'China' || !str_contains($branches[3]['branch_name'], 'Shenzhen')) {
        throw new Exception("Fourth branch should be Shenzhen, China");
    }
});

runTest("2.6 In-Batch Card Grouping & Deduplication", function() use ($matcher) {
    $cards = [
        // Group 1: Suntransco Card A (CEO)
        [
            'file_path'   => 'uploads/cards/suntrans_card1.jpg',
            'side'        => 'single',
            'card_index'  => 0,
            'ocr_parsed'  => [
                'company_name'    => 'Công Ty TNHH Vận Tải Quốc Tế Suntransco',
                'company_name_en' => 'Suntransco International Transport Co., Ltd',
                'tax_code'        => '0101234567',
                'website'         => 'https://suntrans.vn',
                'address'         => '18 Phan Chu Trinh, Hà Nội',
                'city'            => 'Hà Nội',
                'contact_name'    => 'Trần Hoàng Long',
                'position'        => 'Tổng Giám Đốc',
                'phone'           => '02439876543',
                'email'           => 'long.tran@suntrans.vn',
            ]
        ],
        // Group 1: Suntransco Card B (Sales Director, matching tax code & domain)
        [
            'file_path'   => 'uploads/cards/suntrans_card2.jpg',
            'side'        => 'front',
            'card_index'  => 1,
            'ocr_parsed'  => [
                'company_name'    => 'Suntransco Co., Ltd',
                'tax_code'        => '0101234567',
                'website'         => 'https://suntrans.vn/sales',
                'contact_name'    => 'Lê Văn Bình',
                'position'        => 'Phó Giám Đốc Kinh Doanh',
                'phone'           => '0908889999',
                'email'           => 'binh.le@suntrans.vn',
                'branch_info'     => 'Chi nhánh Hải Phòng: 15 Lê Hồng Phong, Hải Phòng',
            ]
        ],
        // Group 2: Ánh Dương Logistics (Different company)
        [
            'file_path'   => 'uploads/cards/anhduong.jpg',
            'side'        => 'single',
            'card_index'  => 2,
            'ocr_parsed'  => [
                'company_name'    => 'Công Ty Cổ Phần Logistics Ánh Dương',
                'tax_code'        => '0400123456',
                'website'         => 'https://anhduonglogistics.vn',
                'address'         => '45 Lê Duẩn, Đà Nẵng',
                'city'            => 'Đà Nẵng',
                'contact_name'    => 'Nguyễn Thị Ánh Dương',
                'position'        => 'Giám Đốc',
                'phone'           => '0908111222',
                'email'           => 'contact@anhduong.vn',
            ]
        ],
        // Group 3: Sino-Viet Shipping (Different company)
        [
            'file_path'   => 'uploads/cards/sinoviet.jpg',
            'side'        => 'single',
            'card_index'  => 3,
            'ocr_parsed'  => [
                'company_name'    => '中越国际货运代理有限公司',
                'company_name_en' => 'Sino-Viet International Freight Forwarding Co., Ltd',
                'tax_code'        => '0107766554',
                'website'         => 'https://sinoviet-freight.com',
                'contact_name'    => 'Chen Wei',
                'position'        => 'Country Director',
                'phone'           => '0988665544',
                'email'           => 'chenwei@sinoviet.com',
            ]
        ],
    ];

    $groups = $matcher->groupCards($cards);

    if (count($groups) !== 3) {
        throw new Exception("Expected 4 cards to group into 3 companies, got " . count($groups));
    }

    // Check Group 1 (Suntransco)
    $suntransGroup = $groups[0];
    if (count($suntransGroup['contacts']) !== 2) {
        throw new Exception("Suntransco group must contain 2 contacts, got " . count($suntransGroup['contacts']));
    }
    if (count($suntransGroup['source_cards']) !== 2) {
        throw new Exception("Suntransco group must contain 2 source cards, got " . count($suntransGroup['source_cards']));
    }
    if ($suntransGroup['contacts'][0]['is_primary'] !== 1) {
        throw new Exception("First contact must be marked as primary");
    }
    if ($suntransGroup['contacts'][1]['is_primary'] !== 0) {
        throw new Exception("Second contact must not be primary");
    }
    if (count($suntransGroup['branches']) < 2) {
        throw new Exception("Suntransco branches should include HQ and Hải Phòng branch");
    }
});

runTest("2.7 Cross-matching with Existing DB Members", function() use ($matcher) {
    $existingDbMembers = [
        (object)[
            'id'                 => 101,
            'company_name'       => 'Công Ty TNHH Logistics Ánh Dương',
            'company_name_en'    => 'Anh Duong Logistics Ltd',
            'tax_code'           => '0400123456',
            'website'            => 'https://anhduonglogistics.vn',
            'industry_type_id'   => 2,
            'member_type'        => 'member',
        ],
    ];

    $cards = [
        [
            'ocr_parsed' => [
                'company_name' => 'Công Ty CP Logistics Ánh Dương',
                'tax_code'     => '0400123456',
                'contact_name' => 'Trần Văn C',
                'position'     => 'Trưởng Phòng Logistics',
                'phone'        => '0905556677',
            ]
        ]
    ];

    $groups = $matcher->groupCards($cards, $existingDbMembers);

    if (count($groups) !== 1) throw new Exception("Expected 1 group");
    $g = $groups[0];
    if ($g['existing_member_id'] !== 101) {
        throw new Exception("Existing member ID not matched: expected 101, got " . var_export($g['existing_member_id'], true));
    }
    if ($g['match_type'] !== 'existing_db') {
        throw new Exception("Match type should be 'existing_db', got {$g['match_type']}");
    }
    if ($g['company_info']['industry_type_id'] !== 2) {
        throw new Exception("DB industry_type_id should be inherited");
    }
});

echo "\n======================================================================\n";
echo "  TEST SUMMARY: {$passed} / {$total} TESTS PASSED (" . round(($passed / $total) * 100) . "%)\n";
echo "======================================================================\n\n";

if ($passed === $total) {
    exit(0);
} else {
    exit(1);
}
