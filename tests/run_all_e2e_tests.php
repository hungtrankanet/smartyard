<?php
/**
 * TOP BEST GLOBAL - Master E2E Test Suite Runner
 * Target: /varient-v2.4/tests/run_all_e2e_tests.php
 * Constraint: Strictly <= 500 lines
 */

namespace TopBestGlobal\Tests;

require_once __DIR__ . '/e2e_test_harness.php';
require_once __DIR__ . '/test_e2e_portal.php';
require_once __DIR__ . '/test_e2e_voting.php';
require_once __DIR__ . '/test_e2e_security_leaderboard.php';
require_once __DIR__ . '/test_e2e_nomination.php';
require_once __DIR__ . '/test_e2e_hall_of_fame.php';
require_once __DIR__ . '/test_e2e_concurrency_infra.php';
require_once __DIR__ . '/test_e2e_cross_feature.php';
require_once __DIR__ . '/test_e2e_real_world.php';

class MasterE2ERunner {
    public static function runAll(): int {
        echo "\n";
        echo "\033[1;33m╔═══════════════════════════════════════════════════════════════════════════════╗\033[0m\n";
        echo "\033[1;33m║     🏆 TOP BEST GLOBAL — COMPREHENSIVE 4-TIER E2E TEST SUITE RUNNER 🏆         ║\033[0m\n";
        echo "\033[1;33m╚═══════════════════════════════════════════════════════════════════════════════╝\033[0m\n\n";

        $suites = [
            new TestE2EPortal(),
            new TestE2EVoting(),
            new TestE2ESecurityLeaderboard(),
            new TestE2ENomination(),
            new TestE2EHallOfFame(),
            new TestE2EConcurrencyInfra(),
            new TestE2ECrossFeature(),
            new TestE2ERealWorld()
        ];

        $totalPassed = 0;
        $totalFailed = 0;
        $totalAssertions = 0;
        $totalDuration = 0.0;
        $allErrors = [];
        $results = [];

        $startTime = microtime(true);

        foreach ($suites as $suite) {
            $res = $suite->run(true);
            $results[] = $res;
            $totalPassed += $res->passed;
            $totalFailed += $res->failed;
            $totalAssertions += $res->assertions;
            $totalDuration += $res->durationMs;
            if (!empty($res->errors)) {
                $allErrors = array_merge($allErrors, $res->errors);
            }
        }

        $totalWallDuration = round((microtime(true) - $startTime) * 1000, 2);

        // Line Count Verification Step (<= 500 lines per file)
        echo "\033[1;36m▶ Running Sub-500 Line Governance Gatekeeper Check:\033[0m\n";
        $testDir = __DIR__;
        $phpFiles = glob($testDir . '/*.php');
        $lineCheckPassed = true;
        $oversizedFiles = [];

        foreach ($phpFiles as $file) {
            $lines = count(file($file));
            $relPath = basename($file);
            if ($lines > 500) {
                $lineCheckPassed = false;
                $oversizedFiles[] = "{$relPath} ({$lines} lines)";
                echo "  \033[31m✖ VIOLATION:\033[0m {$relPath} has {$lines} lines (> 500 lines limit)\n";
            } else {
                echo "  \033[32m✔ COMPLIANT:\033[0m {$relPath} -> {$lines}/500 lines\n";
            }
        }

        // Summary Table
        echo "\n\033[1;37m" . str_repeat("═", 79) . "\033[0m\n";
        echo sprintf("\033[1;37m %-48s | %-6s | %-6s | %-8s\033[0m\n", "Test Suite Name", "Pass", "Fail", "Time");
        echo "\033[1;37m" . str_repeat("─", 79) . "\033[0m\n";

        foreach ($results as $res) {
            $statusColor = $res->failed === 0 ? "\033[32m" : "\033[31m";
            echo sprintf(" %-48s | %s%-6d\033[0m | %s%-6d\033[0m | %-6.1fms\n",
                substr($res->suiteName, 0, 48),
                $statusColor, $res->passed,
                $statusColor, $res->failed,
                $res->durationMs
            );
        }

        echo "\033[1;37m" . str_repeat("═", 79) . "\033[0m\n";
        $totalColor = $totalFailed === 0 && $lineCheckPassed ? "\033[1;32m" : "\033[1;31m";
        echo sprintf("{$totalColor} TOTAL: %d Tests (%d Passed, %d Failed, %d Assertions) in %.2fms\033[0m\n",
            ($totalPassed + $totalFailed),
            $totalPassed,
            $totalFailed,
            $totalAssertions,
            $totalWallDuration
        );

        if (!$lineCheckPassed) {
            echo "\n\033[1;31m✖ LINE LIMIT GATEKEEPER FAILED: Oversized files detected: " . implode(', ', $oversizedFiles) . "\033[0m\n";
            return 1;
        }

        if ($totalFailed > 0) {
            echo "\n\033[1;31m✖ TEST FAILURES ENCOUNTERED:\033[0m\n";
            foreach ($allErrors as $err) {
                echo "  \033[33m• " . $err . "\033[0m\n";
            }
            return 1;
        }

        echo "\n\033[1;32m✔ 100% E2E TEST SUITES PASSED — READY FOR PRODUCTION VERIFICATION! 🚀\033[0m\n\n";
        return 0;
    }
}

// CLI entrypoint
if (php_sapi_name() === 'cli') {
    $exitCode = MasterE2ERunner::runAll();
    exit($exitCode);
}
