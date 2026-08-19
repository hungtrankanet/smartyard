<?php
/**
 * TOP BEST GLOBAL - E2E Testing Framework & Harness
 * Target: /varient-v2.4/tests/e2e_test_harness.php
 * Compatibility: PHP 7.4+ and PHP 8.0+
 * Constraint: Strictly <= 500 lines
 */

namespace TopBestGlobal\Tests;

// Polyfill for str_contains on PHP 7.4
if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool {
        return '' === $needle || false !== strpos($haystack, $needle);
    }
}

class Assert {
    public static int $assertionsCount = 0;

    public static function assertTrue(bool $condition, string $msg = ''): void {
        self::$assertionsCount++;
        if (!$condition) {
            throw new \AssertionError($msg ?: "Failed asserting that condition is true.");
        }
    }

    public static function assertFalse(bool $condition, string $msg = ''): void {
        self::$assertionsCount++;
        if ($condition) {
            throw new \AssertionError($msg ?: "Failed asserting that condition is false.");
        }
    }

    public static function assertEquals($expected, $actual, string $msg = ''): void {
        self::$assertionsCount++;
        if ($expected !== $actual) {
            $eStr = is_scalar($expected) ? (string)$expected : json_encode($expected);
            $aStr = is_scalar($actual) ? (string)$actual : json_encode($actual);
            throw new \AssertionError($msg ?: "Failed asserting that [{$aStr}] equals expected [{$eStr}].");
        }
    }

    public static function assertEqualsDelta(float $expected, float $actual, float $delta = 0.01, string $msg = ''): void {
        self::$assertionsCount++;
        if (abs($expected - $actual) > $delta) {
            throw new \AssertionError($msg ?: "Failed asserting that {$actual} is within {$delta} of {$expected}.");
        }
    }

    public static function assertNotEquals($expected, $actual, string $msg = ''): void {
        self::$assertionsCount++;
        if ($expected === $actual) {
            throw new \AssertionError($msg ?: "Failed asserting that actual value does not equal [{$expected}].");
        }
    }

    public static function assertGreaterThan($expected, $actual, string $msg = ''): void {
        self::$assertionsCount++;
        if ($actual <= $expected) {
            throw new \AssertionError($msg ?: "Failed asserting that {$actual} is greater than {$expected}.");
        }
    }

    public static function assertGreaterThanOrEqual($expected, $actual, string $msg = ''): void {
        self::$assertionsCount++;
        if ($actual < $expected) {
            throw new \AssertionError($msg ?: "Failed asserting that {$actual} is >= {$expected}.");
        }
    }

    public static function assertLessThanOrEqual($expected, $actual, string $msg = ''): void {
        self::$assertionsCount++;
        if ($actual > $expected) {
            throw new \AssertionError($msg ?: "Failed asserting that {$actual} is <= {$expected}.");
        }
    }

    public static function assertContains($needle, $haystack, string $msg = ''): void {
        self::$assertionsCount++;
        if (is_string($haystack)) {
            if (!str_contains($haystack, (string)$needle)) {
                throw new \AssertionError($msg ?: "Failed asserting that string contains '{$needle}'.");
            }
        } elseif (is_array($haystack)) {
            if (!in_array($needle, $haystack, true)) {
                throw new \AssertionError($msg ?: "Failed asserting that array contains specified element.");
            }
        }
    }

    public static function assertArrayHasKey($key, array $array, string $msg = ''): void {
        self::$assertionsCount++;
        if (!array_key_exists($key, $array)) {
            throw new \AssertionError($msg ?: "Failed asserting that array contains key '{$key}'.");
        }
    }

    public static function assertMatchesRegex(string $pattern, string $string, string $msg = ''): void {
        self::$assertionsCount++;
        if (!preg_match($pattern, $string)) {
            throw new \AssertionError($msg ?: "Failed asserting that '{$string}' matches pattern '{$pattern}'.");
        }
    }

    public static function assertNotNull($actual, string $msg = ''): void {
        self::$assertionsCount++;
        if ($actual === null) {
            throw new \AssertionError($msg ?: "Failed asserting that value is not null.");
        }
    }
}

class TestSuiteResult {
    public string $suiteName;
    public int $passed = 0;
    public int $failed = 0;
    public int $assertions = 0;
    public float $durationMs = 0.0;
    public array $errors = [];

    public function __construct(string $name) {
        $this->suiteName = $name;
    }
}

abstract class E2ETestCase {
    protected string $suiteName;
    protected array $tests = [];
    protected TestSuiteResult $result;

    public function __construct(string $name) {
        $this->suiteName = $name;
        $this->result = new TestSuiteResult($name);
        $this->registerTests();
    }

    abstract protected function registerTests(): void;

    protected function addTest(string $name, callable $fn): void {
        $this->tests[$name] = $fn;
    }

    public function run(bool $verbose = true): TestSuiteResult {
        $startTime = microtime(true);
        $startAssertions = Assert::$assertionsCount;

        if ($verbose) {
            echo "\033[1;36m▶ Running Suite:\033[0m \033[1;37m{$this->suiteName}\033[0m\n";
        }

        foreach ($this->tests as $name => $fn) {
            try {
                $fn();
                $this->result->passed++;
                if ($verbose) {
                    echo "  \033[32m✔ PASS:\033[0m {$name}\n";
                }
            } catch (\Throwable $e) {
                $this->result->failed++;
                $err = "[{$name}] " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine();
                $this->result->errors[] = $err;
                if ($verbose) {
                    echo "  \033[31m✖ FAIL:\033[0m {$name} -> \033[33m" . $e->getMessage() . "\033[0m\n";
                }
            }
        }

        $this->result->durationMs = round((microtime(true) - $startTime) * 1000, 2);
        $this->result->assertions = Assert::$assertionsCount - $startAssertions;

        if ($verbose) {
            $color = $this->result->failed === 0 ? "\033[32m" : "\033[31m";
            echo "  {$color}Summary: {$this->result->passed} passed, {$this->result->failed} failed ({$this->result->durationMs}ms, {$this->result->assertions} assertions)\033[0m\n\n";
        }

        return $this->result;
    }
}

/**
 * Domain Logic Simulation Engine for Standalone & Hermetic E2E Execution
 */
class TopBestGlobalEngine {
    public static array $industries = [
        ['id' => 1, 'name' => 'Công Nghệ & Chuyển Đổi Số', 'slug' => 'cong-nghe-so', 'icon' => 'fa-laptop-code'],
        ['id' => 2, 'name' => 'Y Tế & Chăm Sóc Sức Khỏe', 'slug' => 'y-te-suc-khoe', 'icon' => 'fa-heartbeat'],
        ['id' => 3, 'name' => 'Giáo Dục & Đào Tạo Tiên Tiến', 'slug' => 'giao-duc-dao-tao', 'icon' => 'fa-graduation-cap'],
        ['id' => 4, 'name' => 'Tài Chính, Ngân Hàng & Fintech', 'slug' => 'tai-chinh-ngan-hang', 'icon' => 'fa-coins'],
        ['id' => 5, 'name' => 'Phát Triển Bền Vững & ESG', 'slug' => 'esg-ben-vung', 'icon' => 'fa-leaf'],
        ['id' => 6, 'name' => 'Sản Xuất & Công Nghiệp Trọng Điểm', 'slug' => 'san-xuat-cong-nghiep', 'icon' => 'fa-industry'],
        ['id' => 7, 'name' => 'Logistics & Chuỗi Cung Ứng', 'slug' => 'logistics-supply-chain', 'icon' => 'fa-shipping-fast'],
        ['id' => 8, 'name' => 'Bất Động Sản & Đô Thị Thông Minh', 'slug' => 'bat-dong-san', 'icon' => 'fa-city'],
        ['id' => 9, 'name' => 'Nông Nghiệp Công Nghệ Cao', 'slug' => 'nong-nghiep-cnc', 'icon' => 'fa-seedling'],
        ['id' => 10, 'name' => 'Năng Lượng & Môi Trường Xanh', 'slug' => 'nang-luong-xanh', 'icon' => 'fa-bolt'],
        ['id' => 11, 'name' => 'Bán Lẻ, Thương Mại Điện Tử & FMCG', 'slug' => 'ban-le-ecommerce', 'icon' => 'fa-shopping-cart'],
        ['id' => 12, 'name' => 'Du Lịch, Khách Sạn & Ẩm Thực', 'slug' => 'du-lich-khach-san', 'icon' => 'fa-utensils'],
        ['id' => 13, 'name' => 'Truyền Thông, Quảng Cáo & Sáng Tạo', 'slug' => 'truyen-thong-sang-tao', 'icon' => 'fa-bullhorn'],
        ['id' => 14, 'name' => 'Khởi Nghiệp Đổi Mới Sáng Tạo (Startup)', 'slug' => 'khoi-nghiep-startup', 'icon' => 'fa-rocket'],
        ['id' => 15, 'name' => 'Lãnh Đạo & Doanh Nhân Tiêu Biểu', 'slug' => 'lanh-dao-tieu-bieu', 'icon' => 'fa-user-tie']
    ];

    public static array $disposableDomains = [
        'mailinator.com', '10minutemail.com', 'tempmail.org', 'guerrillamail.com', 'trashmail.com', 'sharklasers.com'
    ];

    public static function calculate7030Score(float $juryScoreRaw, int $publicVotes, int $maxPublicVotes = 1000): array {
        $juryScoreNorm = max(0.0, min(100.0, $juryScoreRaw));
        $juryWeighted = $juryScoreNorm * 0.70;
        $publicScoreNorm = $maxPublicVotes > 0 ? min(100.0, ($publicVotes / $maxPublicVotes) * 100.0) : 0.0;
        $publicWeighted = $publicScoreNorm * 0.30;
        $compositeScore = round($juryWeighted + $publicWeighted, 2);

        return [
            'jury_score_raw' => $juryScoreRaw,
            'jury_score_norm' => round($juryScoreNorm, 2),
            'jury_weighted_70' => round($juryWeighted, 2),
            'public_votes_raw' => $publicVotes,
            'public_score_norm' => round($publicScoreNorm, 2),
            'public_weighted_30' => round($publicWeighted, 2),
            'composite_score' => $compositeScore
        ];
    }

    public static function evaluateAntiFraud(string $email, string $ip, string $fingerprint, int $ipRecentVotesCount, bool $isDisposable = false): array {
        $riskScore = 0;
        $reasons = [];

        $emailDomain = substr(strrchr($email, "@"), 1);
        if ($isDisposable || in_array(strtolower($emailDomain), self::$disposableDomains, true)) {
            $riskScore += 90;
            $reasons[] = 'disposable_email_detected';
        }

        if ($ipRecentVotesCount >= 5) {
            $riskScore += 80;
            $reasons[] = 'ip_rate_limit_exceeded';
        } elseif ($ipRecentVotesCount >= 3) {
            $riskScore += 30;
            $reasons[] = 'frequent_ip_activity';
        }

        if (empty(trim($fingerprint)) || strlen($fingerprint) < 16) {
            $riskScore += 40;
            $reasons[] = 'invalid_device_fingerprint';
        }

        $status = 'APPROVED';
        if ($riskScore >= 75) {
            $status = 'BLOCKED';
        } elseif ($riskScore >= 30) {
            $status = 'CHALLENGE';
        }

        return [
            'status' => $status,
            'risk_score' => $riskScore,
            'reasons' => $reasons,
            'requires_captcha' => ($status === 'CHALLENGE')
        ];
    }

    public static function generateOtp(string $email, int $candidateId): array {
        $otpCode = str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        $token = hash('sha256', $email . '|' . $candidateId . '|' . $otpCode . '|' . time());
        return [
            'otp_code' => $otpCode,
            'token' => $token,
            'created_at' => time(),
            'expires_at' => time() + 300, // 5 min TTL
            'cooldown_seconds' => 60
        ];
    }

    public static function generateCertificateSerial(int $year, string $tier, int $id): string {
        $tierCode = strtoupper(substr($tier, 0, 4));
        return sprintf("TBG-%d-%s-%04d", $year, $tierCode, $id);
    }

    public static function renderSvgBadge(string $serial, string $nominee, string $award, int $year): string {
        $escapedNominee = htmlspecialchars($nominee, ENT_QUOTES, 'UTF-8');
        $escapedAward = htmlspecialchars($award, ENT_QUOTES, 'UTF-8');
        return '<svg xmlns="http://www.w3.org/2000/svg" width="320" height="420" viewBox="0 0 320 420">' .
               '<defs><linearGradient id="gold" x1="0%" y1="0%" x2="100%" y2="100%">' .
               '<stop offset="0%" stop-color="#FFD700"/><stop offset="50%" stop-color="#D4AF37"/>' .
               '<stop offset="100%" stop-color="#AA771C"/></linearGradient></defs>' .
               '<rect width="320" height="420" rx="16" fill="#0A192F" stroke="url(#gold)" stroke-width="4"/>' .
               '<circle cx="160" cy="70" r="36" fill="url(#gold)"/>' .
               '<text x="160" y="78" font-size="28" text-anchor="middle" fill="#0A192F" font-weight="bold">★</text>' .
               '<text x="160" y="140" font-size="18" fill="#FFD700" font-weight="bold" text-anchor="middle">TOP BEST GLOBAL ' . $year . '</text>' .
               '<text x="160" y="180" font-size="14" fill="#FFFFFF" text-anchor="middle">' . $escapedAward . '</text>' .
               '<text x="160" y="220" font-size="16" fill="#FFD700" font-weight="bold" text-anchor="middle">' . $escapedNominee . '</text>' .
               '<text x="160" y="380" font-size="11" fill="#8892B0" text-anchor="middle">Verified: ' . $serial . '</text>' .
               '</svg>';
    }
}
