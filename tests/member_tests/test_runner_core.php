<?php
/**
 * TestRunnerCore: Test execution engine and assertion framework for Member Module
 * Maximum 500 lines constraint strictly enforced.
 */

namespace Tests\MemberTests;

class TestRunnerCore
{
    private static int $total = 0;
    private static int $passed = 0;
    private static int $failed = 0;
    private static int $skipped = 0;
    private static array $failures = [];
    private static float $startTime = 0.0;
    private static string $currentTier = '';
    private static string $currentFeature = '';

    public static function init(): void
    {
        self::$startTime = microtime(true);
        self::$total = 0;
        self::$passed = 0;
        self::$failed = 0;
        self::$skipped = 0;
        self::$failures = [];
    }

    public static function setTier(string $tier): void
    {
        self::$currentTier = $tier;
        echo "\n\033[1;36m======================================================================\033[0m\n";
        echo "\033[1;36m  " . strtoupper($tier) . "\033[0m\n";
        echo "\033[1;36m======================================================================\033[0m\n\n";
    }

    public static function setFeature(string $feature): void
    {
        self::$currentFeature = $feature;
        echo "\033[1;33m--- [Feature] " . $feature . " ---\033[0m\n";
    }

    public static function test(string $name, callable $closure): void
    {
        self::$total++;
        $testStart = microtime(true);
        try {
            $closure();
            self::$passed++;
            $duration = round((microtime(true) - $testStart) * 1000, 2);
            echo "  \033[32m✔ PASS\033[0m {$name} \033[90m({$duration}ms)\033[0m\n";
        } catch (\Throwable $e) {
            self::$failed++;
            $duration = round((microtime(true) - $testStart) * 1000, 2);
            $msg = $e->getMessage() . " in " . basename($e->getFile()) . ":" . $e->getLine();
            self::$failures[] = [
                'tier'    => self::$currentTier,
                'feature' => self::$currentFeature,
                'name'    => $name,
                'error'   => $msg,
            ];
            echo "  \033[31m✖ FAIL\033[0m {$name} \033[90m({$duration}ms)\033[0m\n";
            echo "         \033[31mError: {$msg}\033[0m\n";
        }
    }

    public static function skip(string $name, string $reason = ''): void
    {
        self::$total++;
        self::$skipped++;
        echo "  \033[33m⚠ SKIP\033[0m {$name} \033[90m({$reason})\033[0m\n";
    }

    public static function assertTrue(bool $condition, string $message = 'Expected true, got false'): void
    {
        if ($condition !== true) {
            throw new \AssertionError($message);
        }
    }

    public static function assertFalse(bool $condition, string $message = 'Expected false, got true'): void
    {
        if ($condition !== false) {
            throw new \AssertionError($message);
        }
    }

    public static function assertEqual($expected, $actual, string $message = ''): void
    {
        if ($expected !== $actual) {
            $expStr = is_scalar($expected) ? var_export($expected, true) : json_encode($expected);
            $actStr = is_scalar($actual) ? var_export($actual, true) : json_encode($actual);
            $detail = $message !== '' ? $message : "Expected: {$expStr}, got: {$actStr}";
            throw new \AssertionError($detail);
        }
    }

    public static function assertContains(string $needle, string $haystack, string $message = ''): void
    {
        if (strpos($haystack, $needle) === false) {
            $detail = $message !== '' ? $message : "Expected '{$haystack}' to contain '{$needle}'";
            throw new \AssertionError($detail);
        }
    }

    public static function assertMatchesRegex(string $pattern, string $string, string $message = ''): void
    {
        if (!preg_match($pattern, $string)) {
            $detail = $message !== '' ? $message : "Expected '{$string}' to match regex '{$pattern}'";
            throw new \AssertionError($detail);
        }
    }

    public static function assertArrayHasKey(string $key, array $array, string $message = ''): void
    {
        if (!array_key_exists($key, $array)) {
            $detail = $message !== '' ? $message : "Expected array to have key '{$key}'";
            throw new \AssertionError($detail);
        }
    }

    public static function assertCount(int $expectedCount, $countable, string $message = ''): void
    {
        $actualCount = count($countable);
        if ($actualCount !== $expectedCount) {
            $detail = $message !== '' ? $message : "Expected count {$expectedCount}, got {$actualCount}";
            throw new \AssertionError($detail);
        }
    }

    public static function assertNotNull($actual, string $message = 'Expected non-null value'): void
    {
        if ($actual === null) {
            throw new \AssertionError($message);
        }
    }

    public static function assertNull($actual, string $message = 'Expected null value'): void
    {
        if ($actual !== null) {
            $actStr = is_scalar($actual) ? var_export($actual, true) : json_encode($actual);
            throw new \AssertionError("{$message} (got {$actStr})");
        }
    }

    public static function summary(): int
    {
        $totalDuration = round((microtime(true) - self::$startTime), 3);
        echo "\n\033[1;37m======================================================================\033[0m\n";
        echo "\033[1;37m  TEST HARNESS EXECUTION SUMMARY\033[0m\n";
        echo "\033[1;37m======================================================================\033[0m\n";
        echo "  Total Tests:    \033[1;37m" . self::$total . "\033[0m\n";
        echo "  Passed:         \033[1;32m" . self::$passed . "\033[0m\n";
        echo "  Failed:         \033[1;31m" . self::$failed . "\033[0m\n";
        echo "  Skipped:        \033[1;33m" . self::$skipped . "\033[0m\n";
        echo "  Execution Time: \033[1;37m{$totalDuration}s\033[0m\n";

        if (self::$failed > 0) {
            echo "\n\033[1;31m  FAILURE DETAILS (" . count(self::$failures) . "):\033[0m\n";
            foreach (self::$failures as $idx => $f) {
                $num = $idx + 1;
                echo "    {$num}. [{$f['tier']}] [{$f['feature']}] {$f['name']}\n";
                echo "       \033[31m{$f['error']}\033[0m\n";
            }
            echo "\n\033[1;31m  RESULT: FAILED (Exit Code 1)\033[0m\n\n";
            return 1;
        }

        echo "\n\033[1;32m  RESULT: ALL ACCEPTANCE TESTS PASSED (Exit Code 0)\033[0m\n\n";
        return 0;
    }
}
