<?php
/**
 * Master Automated Test Harness for Suntransco Smart Member Management Module
 * 
 * Executes comprehensive 4-tier acceptance test suites:
 * - Tier 1: Feature Coverage
 * - Tier 2: Boundary & Corner Cases
 * - Tier 3: Cross-Feature Workflows
 * - Tier 4: Real-World Scenarios & Workloads
 * 
 * Usage:
 *   php tests/test_member_module.php
 * 
 * Returns:
 *   Exit code 0 on all tests passing
 *   Exit code 1 on any assertion failure
 * 
 * Maximum 500 lines constraint strictly enforced.
 */

// Set up CLI environment definitions
define('APPPATH', dirname(__DIR__) . '/app/');
define('SYSTEMPATH', dirname(__DIR__) . '/system/');
define('FCPATH', dirname(__DIR__) . '/');

$_SERVER['REQUEST_URI'] = '/admin/members';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['REQUEST_METHOD'] = 'GET';

if (!function_exists('base_url')) {
    function base_url($path = '') {
        return 'http://localhost/' . ltrim($path, '/');
    }
}

if (!function_exists('esc')) {
    function esc($data, string $context = 'html') {
        if ($context === 'raw') return $data;
        if (is_array($data)) {
            return array_map('esc', $data);
        }
        return htmlspecialchars((string)$data, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('cleanSlug')) {
    function cleanSlug($slug) {
        $slug = trim((string)$slug);
        $slug = urldecode($slug);
        $slug = strip_tags($slug);
        $forbiddenChars = [';', '"', '$', '%', '*', '/', '\'', '<', '>', '=', '?', '[', ']', '\\', '^', '`', '{', '}', '|', '~', '+', '#', '!', '(', ')'];
        return str_replace($forbiddenChars, '', $slug);
    }
}

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

// Load test runner framework and suites
require_once __DIR__ . '/member_tests/test_runner_core.php';
require_once __DIR__ . '/member_tests/test_db_helper.php';
require_once __DIR__ . '/member_tests/tier1_feature_tests.php';
require_once __DIR__ . '/member_tests/tier2_boundary_tests.php';
require_once __DIR__ . '/member_tests/tier3_workflow_tests.php';
require_once __DIR__ . '/member_tests/tier4_realworld_tests.php';
require_once __DIR__ . '/member_tests/tier5_adversarial_tests.php';

use Tests\MemberTests\TestRunnerCore;
use Tests\MemberTests\TestDbHelper;
use Tests\MemberTests\Tier1FeatureTests;
use Tests\MemberTests\Tier2BoundaryTests;
use Tests\MemberTests\Tier3WorkflowTests;
use Tests\MemberTests\Tier4RealworldTests;
use Tests\MemberTests\Tier5AdversarialChallengeTests;

echo "\n\033[1;35m======================================================================\033[0m\n";
echo "\033[1;35m  SUNTRANSCO SMART MEMBER MANAGEMENT MODULE - 5-TIER TEST HARNESS     \033[0m\n";
echo "\033[1;35m======================================================================\033[0m\n";
echo "  Target: Suntransco CodeIgniter 4 CMS\n";
echo "  Date:   " . date('Y-m-d H:i:s T') . "\n";
echo "  PHP:    " . PHP_VERSION . " (" . PHP_SAPI . ")\n";

// Initialize test runner
TestRunnerCore::init();

// Initialize in-memory SQLite schema and seed data
TestDbHelper::resetDatabase();

// Execute Tier 1: Feature Coverage
Tier1FeatureTests::run();

// Execute Tier 2: Boundary & Corner Cases
Tier2BoundaryTests::run();

// Execute Tier 3: Cross-Feature Workflows
Tier3WorkflowTests::run();

// Execute Tier 4: Real-World Scenarios
Tier4RealworldTests::run();

// Execute Tier 5: Adversarial Challenge & Stress Tests
Tier5AdversarialChallengeTests::run();

// Output summary and return exit code
$exitCode = TestRunnerCore::summary();
exit($exitCode);

