<?php

/**
 * test_m2_admin_cms.php: Unit and Integration verification for Milestone M2
 * Verifies Admin Controllers, View Templates, Navigation, and Route mappings
 * Strict Compliance: <= 500 lines
 */

require_once __DIR__ . '/e2e_test_harness.php';

$totalChecks = 0;
$passedChecks = 0;

function check(string $name, bool $condition, string $details = '') {
    global $totalChecks, $passedChecks;
    $totalChecks++;
    if ($condition) {
        $passedChecks++;
        echo "  [PASS] {$name}\n";
    } else {
        echo "  [FAIL] {$name} - {$details}\n";
    }
}

echo "==========================================================\n";
echo " M2: Admin CMS Modules & Navigation Verification Suite    \n";
echo "==========================================================\n";

$projectDir = realpath(__DIR__ . '/..');

// 1. Controller Existence & Class Structure
echo "\n--- 1. Admin Controller Class Architecture ---\n";
$controllers = [
    'AdminAwardSeasonController' => [
        'path' => $projectDir . '/app/Controllers/AdminAwardSeasonController.php',
        'methods' => ['seasons', 'addSeason', 'addSeasonPost', 'editSeason', 'editSeasonPost', 'deleteSeasonPost', 'categories', 'addCategory', 'addCategoryPost', 'editCategory', 'editCategoryPost', 'deleteCategoryPost'],
    ],
    'AdminNominationController' => [
        'path' => $projectDir . '/app/Controllers/AdminNominationController.php',
        'methods' => ['index', 'dossier', 'updateStagePost', 'decisionPost', 'deletePost'],
    ],
    'AdminJuryController' => [
        'path' => $projectDir . '/app/Controllers/AdminJuryController.php',
        'methods' => ['evaluations', 'index', 'scoring', 'submitScorePost', 'juryMembers', 'assignCandidatePost'],
    ],
];

foreach ($controllers as $name => $info) {
    check("File {$name}.php exists", file_exists($info['path']));
    $content = file_get_contents($info['path']);
    check("{$name} extends BaseAdminController", strpos($content, 'extends BaseAdminController') !== false);
    check("{$name} line count <= 500", count(file($info['path'])) <= 500, count(file($info['path'])) . ' lines');
    foreach ($info['methods'] as $m) {
        check("{$name}::{$m}() method defined", strpos($content, "function {$m}") !== false);
    }
}

// 2. View Templates Existence & Line Counts
echo "\n--- 2. Admin View Templates in app/Views/admin/awards/ ---\n";
$views = [
    'app/Views/admin/awards/seasons/index.php',
    'app/Views/admin/awards/seasons/categories.php',
    'app/Views/admin/awards/seasons/form.php',
    'app/Views/admin/awards/nominations/index.php',
    'app/Views/admin/awards/nominations/detail.php',
    'app/Views/admin/awards/jury/index.php',
    'app/Views/admin/awards/jury/form.php',
    'app/Views/admin/awards/jury/members.php',
];

foreach ($views as $v) {
    $fullPath = $projectDir . '/' . $v;
    check("View {$v} exists", file_exists($fullPath));
    if (file_exists($fullPath)) {
        $lines = count(file($fullPath));
        check("View {$v} lines <= 500", $lines <= 500, "{$lines} lines");
        check("View {$v} non-empty", $lines > 10);
    }
}

// 3. Admin Sidebar Navigation Check
echo "\n--- 3. Admin Sidebar Navigation in _header.php ---\n";
$headerPath = $projectDir . '/app/Views/admin/includes/_header.php';
$headerContent = file_get_contents($headerPath);
check("_header.php exists and <= 500 lines", count(file($headerPath)) <= 500, count(file($headerPath)) . ' lines');
check("_header.php contains 'Vinh Danh & Bình Chọn (TOP BEST GLOBAL)'", strpos($headerContent, 'Vinh Danh &amp; Bình Chọn (TOP BEST GLOBAL)') !== false);
check("_header.php contains award-seasons link", strpos($headerContent, "adminUrl('award-seasons')") !== false);
check("_header.php contains nominations link", strpos($headerContent, "adminUrl('nominations')") !== false);
check("_header.php contains jury-evaluations link", strpos($headerContent, "adminUrl('jury-evaluations')") !== false);
check("_header.php contains voting-audit-logs link", strpos($headerContent, "adminUrl('voting-audit-logs')") !== false);

// 4. Admin Routes Check
echo "\n--- 4. Admin Routes in AdminAwardRoutes.php ---\n";
$routesPath = $projectDir . '/app/Config/Routes/AdminAwardRoutes.php';
$routesContent = file_get_contents($routesPath);
check("AdminAwardRoutes.php exists and <= 500 lines", count(file($routesPath)) <= 500, count(file($routesPath)) . ' lines');
check("Route award-seasons mapped", strpos($routesContent, "'award-seasons'") !== false);
check("Route nominations mapped", strpos($routesContent, "'nominations'") !== false);
check("Route jury mapped", strpos($routesContent, "'jury'") !== false);
check("Route jury-evaluations mapped", strpos($routesContent, "'jury-evaluations'") !== false);
check("Route voting-audit mapped", strpos($routesContent, "'voting-audit'") !== false);
check("Route voting-audit-logs mapped", strpos($routesContent, "'voting-audit-logs'") !== false);
check("Route voting-results-summary mapped", strpos($routesContent, "'voting-results-summary'") !== false);

echo "==========================================================\n";
echo " M2 Verification Result: {$passedChecks} / {$totalChecks} checks PASSED\n";
echo "==========================================================\n";

if ($passedChecks !== $totalChecks) {
    exit(1);
}
exit(0);
