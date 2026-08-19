<?php
/**
 * Automated Verification Suite for Worker 4 (MemberController & Admin Views Specialist)
 * Suntransco CodeIgniter 4 Member Management Module v2.0
 */

$baseDir = dirname(__DIR__);

echo "======================================================================\n";
echo "  WORKER 4: CONTROLLER & ADMIN VIEWS VERIFICATION SUITE\n";
echo "======================================================================\n\n";

$passCount = 0;
$totalTests = 0;

function assertWorker4($desc, $condition, $detail = '') {
    global $passCount, $totalTests;
    $totalTests++;
    if ($condition) {
        $passCount++;
        echo "  ✔ PASS: {$desc}\n";
    } else {
        echo "  ❌ FAIL: {$desc} - {$detail}\n";
    }
}

// 1. Line Count Checks (Rule 1: STRICTLY <= 500 lines per file)
$ownedFiles = [
    'app/Controllers/MemberController.php'   => 500,
    'app/Views/admin/members/confirm_ocr.php' => 500,
    'app/Views/admin/members/detail.php'      => 500,
    'app/Views/admin/members/index.php'       => 500,
    'app/Views/admin/members/form.php'        => 500,
];

echo "--- 1. Testing Line Counts & PHP Syntax ---\n";
foreach ($ownedFiles as $relPath => $maxLines) {
    $fullPath = $baseDir . '/' . $relPath;
    assertWorker4("File exists: {$relPath}", file_exists($fullPath));

    $lines = count(file($fullPath));
    assertWorker4("Line count <= {$maxLines} for {$relPath} (Actual: {$lines} lines)", $lines <= $maxLines);

    // Check PHP syntax
    $output = [];
    $returnVar = 0;
    exec("php -l " . escapeshellarg($fullPath) . " 2>&1", $output, $returnVar);
    assertWorker4("PHP syntax check passed for {$relPath}", $returnVar === 0, implode(' ', $output));
}

// 2. Inspect MemberController Methods and Structure
echo "\n--- 2. Testing MemberController Methods & Architecture ---\n";
$controllerContent = file_get_contents($baseDir . '/app/Controllers/MemberController.php');

$requiredMethods = [
    'index',
    'addMember',
    'addMemberPost',
    'editMember',
    'editMemberPost',
    'deleteMemberPost',
    'uploadCards',
    'uploadCardAjax',
    'confirmOcr',
    'confirmOcrPost',
    'confirmGroupedOcr',
    'skipOcr',
    'detail',
    'verifyMember',
    'verifyMemberAjax',
    'handleCardUpload',
];

foreach ($requiredMethods as $method) {
    assertWorker4("MemberController defines method {$method}()", strpos($controllerContent, "function {$method}") !== false);
}

assertWorker4("MemberController uses DB transactions (transStart / transComplete)", 
    strpos($controllerContent, 'transStart()') !== false && strpos($controllerContent, 'transComplete()') !== false);
assertWorker4("MemberController instantiates and manages CompanyMatcher", strpos($controllerContent, 'CompanyMatcher') !== false);
assertWorker4("MemberController instantiates and manages MemberContactModel", strpos($controllerContent, 'MemberContactModel') !== false);
assertWorker4("MemberController instantiates and manages MemberBranchModel", strpos($controllerContent, 'MemberBranchModel') !== false);

// 3. Inspect Admin Views for Required UI Components
echo "\n--- 3. Testing Admin Views Requirements ---\n";

// 3.1 confirm_ocr.php
$confirmOcrContent = file_get_contents($baseDir . '/app/Views/admin/members/confirm_ocr.php');
assertWorker4("confirm_ocr.php contains 3-language company name inputs", 
    strpos($confirmOcrContent, 'company_name') !== false && 
    strpos($confirmOcrContent, 'company_name_en') !== false && 
    strpos($confirmOcrContent, 'company_name_local') !== false);
assertWorker4("confirm_ocr.php contains match badge display logic", 
    strpos($confirmOcrContent, 'Khớp MST') !== false && strpos($confirmOcrContent, 'Khớp Domain') !== false);
assertWorker4("confirm_ocr.php contains nested contacts table and primary radio input", 
    strpos($confirmOcrContent, 'primary_contact_index') !== false || strpos($confirmOcrContent, 'is_primary') !== false);
assertWorker4("confirm_ocr.php contains nested branches table", strpos($confirmOcrContent, 'branches') !== false);
assertWorker4("confirm_ocr.php contains card thumbnails and zoom modal", 
    strpos($confirmOcrContent, 'modalOcrZoom') !== false || strpos($confirmOcrContent, 'showZoomModal') !== false);
assertWorker4("confirm_ocr.php contains batch submit button", strpos($confirmOcrContent, 'save-ocr-post') !== false);

// 3.2 detail.php
$detailContent = file_get_contents($baseDir . '/app/Views/admin/members/detail.php');
assertWorker4("detail.php contains 5-tab navigation structure", 
    strpos($detailContent, '#tab_company') !== false && 
    strpos($detailContent, '#tab_contacts') !== false && 
    strpos($detailContent, '#tab_branches') !== false && 
    strpos($detailContent, '#tab_cards') !== false && 
    strpos($detailContent, '#tab_logs') !== false);
assertWorker4("detail.php contains 3-language company display", 
    strpos($detailContent, 'company_name_en') !== false && strpos($detailContent, 'company_name_local') !== false);
assertWorker4("detail.php contains instant verification AJAX action", strpos($detailContent, 'verify-ajax') !== false);
assertWorker4("detail.php contains visit cards gallery modal zoom", strpos($detailContent, 'modalZoomCard') !== false);

// 3.3 index.php
$indexContent = file_get_contents($baseDir . '/app/Views/admin/members/index.php');
assertWorker4("index.php contains Contact Count badge column with fa-users icon", 
    strpos($indexContent, 'contact_count') !== false && strpos($indexContent, 'fa-users') !== false);
assertWorker4("index.php contains 3-language search filter", 
    strpos($indexContent, 'Tìm Kiếm (VI / EN / Local)') !== false || strpos($indexContent, 'name="q"') !== false);
assertWorker4("index.php contains 360 profile link in options dropdown", strpos($indexContent, 'Hồ sơ 360°') !== false);

// 3.4 form.php
$formContent = file_get_contents($baseDir . '/app/Views/admin/members/form.php');
assertWorker4("form.php contains 3-language company name inputs", 
    strpos($formContent, 'name="company_name"') !== false && 
    strpos($formContent, 'name="company_name_en"') !== false && 
    strpos($formContent, 'name="company_name_local"') !== false);
assertWorker4("form.php contains detected_language dropdown", strpos($formContent, 'name="detected_language"') !== false);
assertWorker4("form.php contains dynamic Contacts Repeater JS and container", 
    strpos($formContent, 'contactsContainer') !== false && strpos($formContent, 'btnAddContact') !== false);
assertWorker4("form.php contains dynamic Branches Repeater JS and container", 
    strpos($formContent, 'branchesContainer') !== false && strpos($formContent, 'btnAddBranch') !== false);

echo "\n======================================================================\n";
echo "  SUMMARY: {$passCount} / {$totalTests} TESTS PASSED (" . round(($passCount / $totalTests) * 100, 1) . "%)\n";
echo "======================================================================\n";

if ($passCount === $totalTests) {
    exit(0);
} else {
    exit(1);
}
