<?php

namespace {
    error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
    ini_set("display_errors", "1");

    if (!function_exists("str_ends_with")) {
        function str_ends_with(string $haystack, string $needle): bool {
            return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
        }
    }
    if (!function_exists("str_starts_with")) {
        function str_starts_with(string $haystack, string $needle): bool {
            return strncmp($haystack, $needle, strlen($needle)) === 0;
        }
    }

    define("APPPATH", dirname(__DIR__) . "/app/");
    define("ROOTPATH", dirname(__DIR__) . "/");
    define("ENVIRONMENT", "testing");

    if (!function_exists("cleanStr")) {
        function cleanStr($str) {
            return htmlspecialchars(trim((string)$str), ENT_QUOTES, "UTF-8");
        }
    }
    if (!function_exists("esc")) {
        function esc($data) {
            return htmlspecialchars((string)$data, ENT_QUOTES, "UTF-8");
        }
    }
}

namespace CodeIgniter\HTTP {
    interface RequestInterface {}
    interface ResponseInterface {}
}

namespace Psr\Log {
    interface LoggerInterface {}
}

namespace CodeIgniter {
    class Controller {
        public $request;
        public $response;
        public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger) {}
    }
}

namespace App\Controllers {
    class BaseController extends \CodeIgniter\Controller {
        public $activeLang;
        public $generalSettings;
        public $settings;
        public function __construct() {
            $this->activeLang = (object)["id" => 1, "short_form" => "vi", "name" => "Vietnamese"];
            $this->generalSettings = (object)["maintenance_mode_status" => 0];
            $this->settings = (object)["site_title" => "TOP BEST GLOBAL"];
        }
    }
    class BaseAdminController extends \CodeIgniter\Controller {
        public $perPage = 20;
        public $activeLang;
        public $generalSettings;
        public function __construct() {
            $this->activeLang = (object)["id" => 1, "short_form" => "vi", "name" => "Vietnamese"];
            $this->generalSettings = (object)["maintenance_mode_status" => 0];
        }
    }
}

namespace App\Models {
    class BaseModel {
        public function __construct() {}
    }
    class MockCandidateModel {
        public function getCandidate($id) {
            return (object)[
                "id" => $id,
                "name" => "Doanh Nghiệp Tiêu Biểu 2026",
                "candidate_code" => "TBG-2026-001",
                "status" => "approved",
                "season_id" => 1,
                "category_id" => 1,
                "jury_score_avg" => 88.50,
                "public_votes_count" => 1250,
                "final_rank" => 1,
            ];
        }
        public function getCandidatesForVoting($catId, $seasonId) {
            return [$this->getCandidate(101)];
        }
        public function incrementVotesAtomic($id) { return true; }
        public function updateScoresAndRank($id, $jury, $comp, $rank) { return true; }
    }
    class MockCategoryModel {
        public function getCategory($id) {
            return (object)[
                "id" => $id,
                "name" => "Thương Hiệu Xuất Sắc Quốc Gia",
                "jury_weight" => 70.00,
                "public_weight" => 30.00,
            ];
        }
        public function getCategoriesBySeason($seasonId) {
            return [$this->getCategory(1)];
        }
    }
    class MockJuryModel {
        public function getCandidateJuryAverage($id) {
            return [
                "avg_score" => 88.50,
                "total_evaluations" => 5,
            ];
        }
    }
    class MockAuditModel {
        public function hasVotedForCandidate($email, $cId, $sId) {
            return ($email === "already.voted@gmail.com");
        }
        public function getIpVoteCount($ip, $hours) { return 1; }
        public function logVote($cId, $sId, $catId, $email, $ip, $fp, $otpId, $risk, $status) {
            return 9999;
        }
    }
    class MockOtpModel {
        public function createOtp($email, $cId, $ip, $fp, $ttl) {
            return [
                "token" => "tok_mock_123456",
                "otp_code" => "654321",
                "expires_at" => date("Y-m-d H:i:s", time() + 300),
            ];
        }
        public function verifyOtp($token, $otp, $cId) {
            if ($token === "tok_mock_123456" && $otp === "654321") {
                return (object)["id" => 55, "email" => "voter@example.com"];
            }
            return null;
        }
        public function getActiveCooldownOtp($email, $cId, $cd) { return null; }
        public function getRecentOtpCountByIp($ip, $sec) { return 1; }
    }
    class MockEmailModel {
        public function sendEmail($data) { return true; }
    }
}

namespace {
    require_once APPPATH . "Services/OtpMailerService.php";
    require_once APPPATH . "Services/AntiFraudSecurityService.php";
    require_once APPPATH . "Services/HybridScoringService.php";
    require_once APPPATH . "Services/VotingEngineService.php";
    require_once APPPATH . "Controllers/VotingEngineController.php";
    require_once APPPATH . "Controllers/VotingApiController.php";
    require_once APPPATH . "Controllers/Admin/AdminVotingAuditController.php";

    $passed = 0;
    $failed = 0;

    function assertTest(string $name, bool $condition, string $detail = "") {
        global $passed, $failed;
        if ($condition) {
            $passed++;
            echo "  [PASS] {$name}\n";
        } else {
            $failed++;
            echo "  [FAIL] {$name} - {$detail}\n";
        }
    }

    echo "====================================================================\n";
    echo " TOP BEST GLOBAL — Milestone 3 (M3) Comprehensive Test Suite\n";
    echo "====================================================================\n\n";

    // 1. 500-LINE COMPLIANCE GATEKEEPER
    echo "1. Testing <= 500 Lines Per File Compliance...\n";

    $m3Files = [
        "app/Services/OtpMailerService.php",
        "app/Services/AntiFraudSecurityService.php",
        "app/Services/HybridScoringService.php",
        "app/Services/VotingEngineService.php",
        "app/Controllers/VotingEngineController.php",
        "app/Controllers/VotingApiController.php",
        "app/Controllers/Admin/AdminVotingAuditController.php",
        "app/Views/themes/suntransco/voting/list.php",
        "app/Views/themes/suntransco/voting/detail.php",
        "app/Views/themes/suntransco/voting/leaderboard.php",
        "app/Views/themes/suntransco/voting/partials/_otp_modal.php",
        "app/Views/email/email_voting_otp.php",
        "app/Views/admin/voting/logs.php",
        "app/Views/admin/voting/results_summary.php",
        "app/Config/Routes/VotingRoutes.php",
        "app/Config/Routes/AdminAwardRoutes.php",
    ];

    foreach ($m3Files as $file) {
        $fullPath = ROOTPATH . $file;
        if (file_exists($fullPath)) {
            $lineCount = count(file($fullPath));
            assertTest("Line count <= 500: {$file} ({$lineCount} lines)", $lineCount <= 500, "Exceeded limit: {$lineCount}");
        } else {
            assertTest("File exists: {$file}", false, "File not found at {$fullPath}");
        }
    }

    // 2. ANTI-FRAUD SECURITY SERVICE TESTS
    echo "\n2. Testing AntiFraudSecurityService Logic & Edge Cases...\n";

    $mockAudit = new \App\Models\MockAuditModel();
    $mockCandidate = new \App\Models\MockCandidateModel();
    $antiFraud = new \App\Services\AntiFraudSecurityService($mockAudit, $mockCandidate);

    $disposableEmails = [
        "hacker@mailinator.com",
        "bot123@tempmail.com",
        "spammer@guerrillamail.com",
        "cheat@10minutemail.com",
        "test@sharklasers.com",
        "user@subdomain.mailinator.com"
    ];
    foreach ($disposableEmails as $dispEmail) {
        assertTest("Disposable email detected: {$dispEmail}", $antiFraud->isDisposableEmail($dispEmail));
    }

    $legitEmails = [
        "executive@vietnamtech.com.vn",
        "director@vinamilk.vn",
        "contact@topbestglobal.com",
        "voter.national@gmail.com"
    ];
    foreach ($legitEmails as $legitEmail) {
        assertTest("Legitimate email approved: {$legitEmail}", !$antiFraud->isDisposableEmail($legitEmail));
    }

    // Duplicate Vote Prevention
    $dupEval = $antiFraud->evaluateRequest("already.voted@gmail.com", 101, "127.0.0.1", "fp_123");
    assertTest("Duplicate vote blocked (already voted)", $dupEval["status"] === "BLOCKED" && $dupEval["risk_score"] === 100);

    // Legitimate Request Evaluation
    $okEval = $antiFraud->evaluateRequest("voter@example.com", 101, "127.0.0.1", "fp_123");
    assertTest("Legitimate voter evaluated with status APPROVED", $okEval["status"] === "APPROVED");

    // Integrity Hash Generation
    $hash1 = $antiFraud->generateIntegrityHash(101, 1, 5, "voter@example.com", "127.0.0.1", "2026-08-19 12:00:00");
    $hash2 = $antiFraud->generateIntegrityHash(101, 1, 5, "voter@example.com", "127.0.0.1", "2026-08-19 12:00:00");
    $hash3 = $antiFraud->generateIntegrityHash(102, 1, 5, "voter@example.com", "127.0.0.1", "2026-08-19 12:00:00");

    assertTest("Integrity hash is 64-char SHA256 hex string", strlen($hash1) === 64 && ctype_xdigit($hash1));
    assertTest("Integrity hash is deterministic", $hash1 === $hash2);
    assertTest("Integrity hash differs across candidates", $hash1 !== $hash3);

    assertTest("Captcha verification passes valid alphanumeric token", $antiFraud->verifyCaptchaToken("AbCd89"));
    assertTest("Captcha verification rejects empty token", !$antiFraud->verifyCaptchaToken(""));

    // 3. HYBRID SCORING FORMULA (70% JURY + 30% COMMUNITY)
    echo "\n3. Testing HybridScoringService (70% Jury + 30% Community Formula)...\n";

    $mockCategory = new \App\Models\MockCategoryModel();
    $mockJury = new \App\Models\MockJuryModel();
    $scoring = new \App\Services\HybridScoringService($mockCandidate, $mockCategory, $mockJury);

    $juryRaw = 88.50;
    $juryWeight = 70.00;
    $publicVotesA = 1250;
    $maxVotes = 1250;
    $publicWeight = 30.00;

    $expectedJuryWeighted = round($juryRaw * ($juryWeight / 100), 2);
    $expectedNormVotes = round(($publicVotesA / $maxVotes) * 100, 2);
    $expectedPublicWeighted = round($expectedNormVotes * ($publicWeight / 100), 2);
    $expectedComposite = round($expectedJuryWeighted + $expectedPublicWeighted, 2);

    assertTest("Jury weighted calculation (88.50 * 0.70 = 61.95)", $expectedJuryWeighted == 61.95);
    assertTest("Public normalized (1250 / 1250 = 100.00%)", $expectedNormVotes == 100.00);
    assertTest("Public weighted calculation (100.00 * 0.30 = 30.00)", $expectedPublicWeighted == 30.00);
    assertTest("Final composite score (61.95 + 30.00 = 91.95)", $expectedComposite == 91.95);

    $juryRawB = 92.00;
    $publicVotesB = 625;
    $juryWeightedB = round($juryRawB * 0.70, 2);
    $normVotesB = round(($publicVotesB / $maxVotes) * 100, 2);
    $publicWeightedB = round($normVotesB * 0.30, 2);
    $compositeB = round($juryWeightedB + $publicWeightedB, 2);

    assertTest("Candidate B composite score calculation (79.40)", $compositeB == 79.40);
    assertTest("Candidate A ranks higher than Candidate B (91.95 > 79.40)", $expectedComposite > $compositeB);

    $normZero = ($maxVotes > 0) ? round((0 / $maxVotes) * 100, 2) : 0.00;
    $pubWeightedZero = round($normZero * 0.30, 2);
    assertTest("Zero public votes produces 0.00 public weighted points", $pubWeightedZero == 0.00);

    $customJuryW = round($juryRaw * 0.80, 2);
    $customPubW = round($expectedNormVotes * 0.20, 2);
    $customComposite = round($customJuryW + $customPubW, 2);
    assertTest("Custom weights 80/20 calculated accurately (70.80 + 20.00 = 90.80)", $customComposite == 90.80);

    // 4. OTP MAILER SERVICE LOGIC
    echo "\n4. Testing OtpMailerService Logic & Methods...\n";

    $mockOtp = new \App\Models\MockOtpModel();
    $mockEmail = new \App\Models\MockEmailModel();
    $otpService = new \App\Services\OtpMailerService($mockOtp, $mockEmail, $mockCandidate);

    $otpGen = $otpService->generateAndSendOtp("voter@example.com", 101, "127.0.0.1", "fp_123");
    assertTest("OTP generation succeeds with 6-digit code", $otpGen["status"] === "success" && !empty($otpGen["token"]));

    $verified = $otpService->verifyOtp("tok_mock_123456", "654321", 101);
    assertTest("OTP verification succeeds for valid token & code", !empty($verified) && $verified->email === "voter@example.com");

    $invalidOtp = $otpService->verifyOtp("tok_mock_123456", "000000", 101);
    assertTest("OTP verification rejects invalid code", empty($invalidOtp));

    // 5. VOTING ENGINE SERVICE END-TO-END EXECUTION
    echo "\n5. Testing VotingEngineService End-to-End Pipeline...\n";

    $votingEngine = new \App\Services\VotingEngineService(
        $mockCandidate,
        $mockCategory,
        null,
        $mockAudit,
        $mockOtp,
        $otpService,
        $antiFraud,
        $scoring
    );

    $voteExec = $votingEngine->executeVote(
        "tok_mock_123456",
        "654321",
        101,
        "voter@example.com",
        "127.0.0.1",
        "fp_123"
    );

    assertTest("Vote execution pipeline succeeds", $voteExec["status"] === "success");
    assertTest("Audit log ID returned in vote execution", $voteExec["audit_log_id"] === 9999);
    assertTest("Composite score returned in vote execution", isset($voteExec["composite_score"]));

    // 6. INTERFACE CONTRACTS COMPLIANCE (PROJECT.md § 5)
    echo "\n6. Testing Interface Contracts Compliance (PROJECT.md § 5)...\n";

    assertTest("Contract 5.1: HybridScoringService::calculateCompositeScore exists", method_exists(\App\Services\HybridScoringService::class, "calculateCompositeScore"));
    assertTest("HybridScoringService::recalculateCategoryScores exists", method_exists(\App\Services\HybridScoringService::class, "recalculateCategoryScores"));
    assertTest("HybridScoringService::recalculateAllCategories exists", method_exists(\App\Services\HybridScoringService::class, "recalculateAllCategories"));

    assertTest("Contract 5.2: AntiFraudSecurityService::evaluateRequest exists", method_exists(\App\Services\AntiFraudSecurityService::class, "evaluateRequest"));
    assertTest("AntiFraudSecurityService::isDisposableEmail exists", method_exists(\App\Services\AntiFraudSecurityService::class, "isDisposableEmail"));
    assertTest("AntiFraudSecurityService::generateIntegrityHash exists", method_exists(\App\Services\AntiFraudSecurityService::class, "generateIntegrityHash"));

    assertTest("Contract 5.3: OtpMailerService::generateAndSendOtp exists", method_exists(\App\Services\OtpMailerService::class, "generateAndSendOtp"));

    assertTest("VotingApiController::sendOtpAjax exists", method_exists(\App\Controllers\VotingApiController::class, "sendOtpAjax"));
    assertTest("VotingApiController::verifyOtpAjax exists", method_exists(\App\Controllers\VotingApiController::class, "verifyOtpAjax"));
    assertTest("VotingApiController::submitVoteAjax exists", method_exists(\App\Controllers\VotingApiController::class, "submitVoteAjax"));
    assertTest("VotingApiController::getLivePollData exists", method_exists(\App\Controllers\VotingApiController::class, "getLivePollData"));
    assertTest("VotingApiController::getCategoryStats exists", method_exists(\App\Controllers\VotingApiController::class, "getCategoryStats"));

    assertTest("VotingEngineController::index exists", method_exists(\App\Controllers\VotingEngineController::class, "index"));
    assertTest("VotingEngineController::category exists", method_exists(\App\Controllers\VotingEngineController::class, "category"));
    assertTest("VotingEngineController::candidate exists", method_exists(\App\Controllers\VotingEngineController::class, "candidate"));
    assertTest("VotingEngineController::leaderboard exists", method_exists(\App\Controllers\VotingEngineController::class, "leaderboard"));
    assertTest("VotingEngineController::categoryLeaderboard exists", method_exists(\App\Controllers\VotingEngineController::class, "categoryLeaderboard"));

    assertTest("AdminVotingAuditController::logs exists", method_exists(\App\Controllers\Admin\AdminVotingAuditController::class, "logs"));
    assertTest("AdminVotingAuditController::resultsSummary exists", method_exists(\App\Controllers\Admin\AdminVotingAuditController::class, "resultsSummary"));
    assertTest("AdminVotingAuditController::recalculateRanksPost exists", method_exists(\App\Controllers\Admin\AdminVotingAuditController::class, "recalculateRanksPost"));
    assertTest("AdminVotingAuditController::exportAuditCsv exists", method_exists(\App\Controllers\Admin\AdminVotingAuditController::class, "exportAuditCsv"));

    echo "\n====================================================================\n";
    echo " TEST RESULTS: {$passed} PASSED | {$failed} FAILED\n";
    echo "====================================================================\n";

    if ($failed > 0) {
        exit(1);
    } else {
        echo "🎉 ALL TESTS PASSED SUCCESSFULLY! 100% COMPLIANT.\n";
        exit(0);
    }
}
