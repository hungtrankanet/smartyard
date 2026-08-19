<?php

/**
 * test_m2_infra.php
 * Automated Verification Suite for Milestone 2: Database Schema, Modular Routing & Decoupled Infrastructure
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

define('APPPATH', realpath(__DIR__ . '/../app') . DIRECTORY_SEPARATOR);
define('FCPATH', realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR);

if (!function_exists('env')) {
    function env($key, $default = null) {
        $val = getenv($key);
        return $val !== false ? $val : $default;
    }
}
if (!function_exists('base_url')) {
    function base_url($path = '') {
        return 'http://localhost:3240/' . ltrim($path, '/');
    }
}

echo "==========================================================\n";
echo " TOP BEST GLOBAL - M2 Infrastructure & Schema Test Suite   \n";
echo "==========================================================\n";

$passedCount = 0;
$failedCount = 0;

function assertCondition(bool $condition, string $testName, string $failureDetails = '') {
    global $passedCount, $failedCount;
    if ($condition) {
        $passedCount++;
        echo " [PASS] {$testName}\n";
    } else {
        $failedCount++;
        echo " [FAIL] {$testName}\n";
        if (!empty($failureDetails)) {
            echo "        Details: {$failureDetails}\n";
        }
    }
}

echo "\n--- Group 1: PHP Syntax & Structure Verification ---\n";

$targetFiles = [
    APPPATH . 'Database/Migrations/2026-08-19-000001_CreateTopBestGlobalAwardsSchema.php',
    APPPATH . 'Models/AwardSeasonModel.php',
    APPPATH . 'Models/AwardCategoryModel.php',
    APPPATH . 'Models/NominationCandidateModel.php',
    APPPATH . 'Models/VotingOtpModel.php',
    APPPATH . 'Models/VotingAuditLogModel.php',
    APPPATH . 'Models/JuryEvaluationModel.php',
    APPPATH . 'Services/MediaStorageService.php',
    APPPATH . 'Config/Routes.php',
    APPPATH . 'Config/Routes/HonorsRoutes.php',
    APPPATH . 'Config/Routes/VotingRoutes.php',
    APPPATH . 'Config/Routes/NominationRoutes.php',
    APPPATH . 'Config/Routes/AdminAwardRoutes.php',
    APPPATH . 'Config/Routes/GeneralRoutes.php',
    APPPATH . 'Config/Routes/MemberRoutes.php',
    APPPATH . 'Config/Routes/ApiRoutes.php',
    APPPATH . 'Config/Routes/AdminRoutes.php',
    APPPATH . 'Config/Routes/PostRoutes.php',
    APPPATH . 'Config/Routes/DynamicRoutes.php',
];

foreach ($targetFiles as $file) {
    $exists = file_exists($file);
    assertCondition($exists, "File exists: " . basename($file));
    if ($exists) {
        $output = [];
        $returnVar = 0;
        exec("php -l " . escapeshellarg($file), $output, $returnVar);
        assertCondition($returnVar === 0, "Valid PHP syntax: " . basename($file), implode(" ", $output));
    }
}

echo "\n--- Group 2: Sub-500 Line Rule Compliance ---\n";

foreach ($targetFiles as $file) {
    if (file_exists($file)) {
        $lines = count(file($file));
        assertCondition($lines <= 500, "Line count <= 500 lines: " . basename($file) . " ({$lines} lines)", "Exceeded 500 lines limit!");
    }
}

$masterRoutesLines = count(file(APPPATH . 'Config/Routes.php'));
assertCondition($masterRoutesLines < 80, "Master Routes.php is < 80 lines ({$masterRoutesLines} lines)");

$subRoutes = glob(APPPATH . 'Config/Routes/*.php');
foreach ($subRoutes as $sr) {
    $srLines = count(file($sr));
    $limit = (basename($sr) === 'AdminAwardRoutes.php') ? 180 : 150;
    assertCondition($srLines <= $limit, "Sub-route " . basename($sr) . " <= {$limit} lines ({$srLines} lines)");
}

echo "\n--- Group 3: Database Migration & Schema SQL Check ---\n";

$sqlFile = FCPATH . 'topbestglobal_awards_schema.sql';
assertCondition(file_exists($sqlFile), "SQL Schema dump exists at topbestglobal_awards_schema.sql");

if (file_exists($sqlFile)) {
    $sqlContent = file_get_contents($sqlFile);
    $requiredTables = [
        'tb_award_seasons',
        'tb_award_categories',
        'tb_nomination_candidates',
        'tb_voting_otps',
        'tb_voting_audit_logs',
        'tb_jury_evaluations',
    ];
    foreach ($requiredTables as $table) {
        assertCondition(strpos($sqlContent, "CREATE TABLE IF NOT EXISTS `{$table}`") !== false, "Table definition present in SQL: {$table}");
    }

    assertCondition(strpos($sqlContent, "idx_season_cat_stage_status") !== false, "Composite index idx_season_cat_stage_status present");
    assertCondition(strpos($sqlContent, "idx_cat_composite") !== false, "Composite index idx_cat_composite present");
    assertCondition(strpos($sqlContent, "idx_email_candidate_verified") !== false, "Composite index idx_email_candidate_verified present");
    assertCondition(strpos($sqlContent, "idx_integrity_hash") !== false, "Composite index idx_integrity_hash present");
}

echo "\n--- Group 4: MediaStorageService Storage & CDN Resolution ---\n";

if (!class_exists('Config\Globals')) {
    eval('namespace Config; class Globals { public static $generalSettings = null; }');
}

require_once APPPATH . 'Services/MediaStorageService.php';

$mediaService = new \App\Services\MediaStorageService('local');
assertCondition($mediaService->getStorageDriver() === 'local', "MediaStorageService initializes with local driver");
assertCondition(!$mediaService->isCloudStorage(), "Local driver correctly identifies as non-cloud");

$dirtyName = "tap doan cong nghe 2026.png";
$cleanName = $mediaService->sanitizeFileName($dirtyName);
assertCondition(!preg_match('/[^a-zA-Z0-9_\-\.]/', $cleanName), "File name sanitized: {$cleanName}");

$testPath = "uploads/badges/tbg_gold_2026.png";
$url = $mediaService->getUrl($testPath);
assertCondition(strpos($url, 'uploads/badges/tbg_gold_2026.png') !== false, "URL correctly resolved for asset: {$url}");

$base64Sample = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==";
$b64Result = $mediaService->uploadFromBase64($base64Sample, 'test_media', 'png', 'unit_test_pixel.png');
assertCondition($b64Result !== null && file_exists(FCPATH . $b64Result['relative_path']), "Base64 asset saved successfully to persistent storage");

if ($b64Result) {
    $deleted = $mediaService->deleteFile($b64Result['relative_path']);
    assertCondition($deleted && !file_exists(FCPATH . $b64Result['relative_path']), "Asset successfully cleaned up via deleteFile()");
}

echo "\n--- Group 5: Docker Compose & Infrastructure Checks ---\n";

$composeFile = FCPATH . 'docker-compose.yml';
assertCondition(file_exists($composeFile), "docker-compose.yml exists");

if (file_exists($composeFile)) {
    $composeContent = file_get_contents($composeFile);
    assertCondition(strpos($composeContent, '3240:80') !== false, "Port 3240:80 proxy mapping configured in docker-compose.yml");
    assertCondition(strpos($composeContent, 'mysql:8.0') !== false, "MySQL 8.0 image configured");
    assertCondition(strpos($composeContent, 'media_uploads:') !== false, "Decoupled named volume media_uploads configured");
    assertCondition(strpos($composeContent, 'topbestglobal_awards_schema.sql') !== false, "Awards schema SQL mounted in docker-entrypoint-initdb.d");
    assertCondition(strpos($composeContent, 'healthcheck:') !== false, "Service healthchecks configured");
}

echo "\n--- Group 6: CI/CD Workflow & Gatekeeper Config ---\n";

$deployWorkflow = FCPATH . '.github/workflows/deploy.yml';
assertCondition(file_exists($deployWorkflow), ".github/workflows/deploy.yml exists");

if (file_exists($deployWorkflow)) {
    $workflowContent = file_get_contents($deployWorkflow);
    assertCondition(strpos($workflowContent, 'gatekeeper_500_lines.php') !== false, "500-line gatekeeper script integrated into CI/CD");
    assertCondition(strpos($workflowContent, 'php -l') !== false, "Automated PHP syntax linting included in CI/CD");
}

echo "\n==========================================================\n";
echo " Test Results: {$passedCount} Passed, {$failedCount} Failed\n";
echo "==========================================================\n";

exit($failedCount > 0 ? 1 : 0);
