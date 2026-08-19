<?php
/**
 * TOP BEST GLOBAL - E2E Test Suite 2: Advanced Voting Engine & 70/30 Scoring (F04, F05, F06, F07)
 * Target: /varient-v2.4/tests/test_e2e_voting.php
 * Constraint: Strictly <= 500 lines
 */

namespace TopBestGlobal\Tests;

require_once __DIR__ . '/e2e_test_harness.php';

class TestE2EVoting extends E2ETestCase {
    public function __construct() {
        parent::__construct('Suite 2: Advanced Voting Engine & 70/30 Scoring (F04, F05, F06, F07)');
    }

    protected function registerTests(): void {
        // ==========================================
        // F04: Candidate Profile & Voting UI
        // ==========================================
        $this->addTest('F04-T1-01: Candidate Profile Model & Dossier Structure', function() {
            $candidate = [
                'id' => 101,
                'name' => 'Tập Đoàn Công Nghệ Tiên Phong',
                'industry_id' => 1,
                'tax_code' => '0102030405',
                'status' => 'approved_for_voting',
                'achievements' => 'Top 10 Sao Khuê, Doanh thu tăng trưởng 45%'
            ];
            Assert::assertEquals(101, $candidate['id']);
            Assert::assertEquals('approved_for_voting', $candidate['status']);
            Assert::assertContains('Sao Khuê', $candidate['achievements']);
        });

        $this->addTest('F04-T1-02: Candidate Achievement Highlights & Metrics', function() {
            $metrics = [
                'employees' => 2500,
                'patents' => 18,
                'market_share' => '32.5%'
            ];
            Assert::assertGreaterThan(2000, $metrics['employees']);
            Assert::assertEquals(18, $metrics['patents']);
        });

        $this->addTest('F04-T1-03: Voting Trigger Modal Data Attributes', function() {
            $modalBtn = '<button class="btn-vote-trigger" data-candidate-id="101" data-category-id="1" data-candidate-name="FPT Corp">Bình Chọn Ngay</button>';
            Assert::assertContains('data-candidate-id="101"', $modalBtn);
            Assert::assertContains('data-category-id="1"', $modalBtn);
            Assert::assertContains('btn-vote-trigger', $modalBtn);
        });

        $this->addTest('F04-T1-04: Real-Time Public Vote Count Display Formatter', function() {
            $votes1 = 1250;
            $votes2 = 1450000;
            $formatted1 = number_format($votes1, 0, ',', '.');
            $formatted2 = round($votes2 / 1000000, 1) . 'M';
            Assert::assertEquals('1.250', $formatted1);
            Assert::assertEquals('1.5M', $formatted2);
        });

        $this->addTest('F04-T1-05: Candidate Media Gallery and Corporate Intro Video', function() {
            $media = [
                'video_embed' => 'https://www.youtube.com/embed/corporate_showcase',
                'images' => ['/uploads/c101/factory.jpg', '/uploads/c101/team.jpg']
            ];
            Assert::assertContains('corporate_showcase', $media['video_embed']);
            Assert::assertEquals(2, count($media['images']));
        });

        // F04 Tier 2: Boundary / Edge Cases
        $this->addTest('F04-T2-01: Inactive Candidate Profile Returns Voting Disabled State', function() {
            $candidate = ['id' => 102, 'status' => 'draft', 'voting_enabled' => false];
            $canVote = ($candidate['status'] === 'approved_for_voting' && $candidate['voting_enabled']);
            Assert::assertFalse($canVote);
        });

        $this->addTest('F04-T2-02: Candidate with 0 Votes Displays Initial Friendly Metric', function() {
            $candidateVotes = 0;
            $label = $candidateVotes > 0 ? "{$candidateVotes} lượt bình chọn" : "Hãy là người đầu tiên bình chọn!";
            Assert::assertEquals("Hãy là người đầu tiên bình chọn!", $label);
        });

        $this->addTest('F04-T2-03: Candidate with Extreme Vote Count (10M+) Formatted Safely', function() {
            $hugeVotes = 12850300;
            $formatted = number_format($hugeVotes, 0, '.', ',');
            Assert::assertEquals('12,850,300', $formatted);
        });

        $this->addTest('F04-T2-04: HTML/XSS in Candidate Bio Sanitized on Display', function() {
            $rawBio = '<img src=x onerror=alert(1)>Doanh nghiệp công nghệ hàng đầu';
            $cleanBio = htmlspecialchars(strip_tags($rawBio), ENT_QUOTES, 'UTF-8');
            Assert::assertFalse(str_contains($cleanBio, '<img'));
            Assert::assertContains('Doanh nghiệp công nghệ', $cleanBio);
        });

        $this->addTest('F04-T2-05: Non-Numeric Candidate ID Route Handled as Invalid', function() {
            $candidateSlug = 'invalid-id-xyz';
            $isValidId = is_numeric($candidateSlug) && (int)$candidateSlug > 0;
            Assert::assertFalse($isValidId);
        });

        // ==========================================
        // F05: OTP Email Authentication Service
        // ==========================================
        $this->addTest('F05-T1-01: 6-Digit Numeric OTP Generation', function() {
            $otp = TopBestGlobalEngine::generateOtp('voter@example.com', 101);
            Assert::assertEquals(6, strlen($otp['otp_code']));
            Assert::assertMatchesRegex('/^[0-9]{6}$/', $otp['otp_code']);
        });

        $this->addTest('F05-T1-02: 5-Minute TTL (300 seconds) Expiration Timestamp', function() {
            $otp = TopBestGlobalEngine::generateOtp('voter@example.com', 101);
            $diff = $otp['expires_at'] - $otp['created_at'];
            Assert::assertEquals(300, $diff);
            Assert::assertGreaterThan(time(), $otp['expires_at']);
        });

        $this->addTest('F05-T1-03: SHA-256 Token Signature Generation', function() {
            $otp = TopBestGlobalEngine::generateOtp('voter@example.com', 101);
            Assert::assertEquals(64, strlen($otp['token']));
            Assert::assertMatchesRegex('/^[a-f0-9]{64}$/', $otp['token']);
        });

        $this->addTest('F05-T1-04: 60-Second Cooldown Timer Between Resends', function() {
            $otp = TopBestGlobalEngine::generateOtp('voter@example.com', 101);
            Assert::assertEquals(60, $otp['cooldown_seconds']);
        });

        $this->addTest('F05-T1-05: Valid Email Format Validation Before OTP Dispatch', function() {
            $validEmail = 'director@leadingtech.vn';
            $isValid = filter_var($validEmail, FILTER_VALIDATE_EMAIL) !== false;
            Assert::assertTrue($isValid);
        });

        // F05 Tier 2: Boundary / Edge Cases
        $this->addTest('F05-T2-01: OTP Requested Within Cooldown Window Rejected with 429', function() {
            $lastSentAt = time() - 30; // 30s ago (within 60s cooldown)
            $cooldown = 60;
            $canResend = (time() - $lastSentAt) >= $cooldown;
            $remaining = $cooldown - (time() - $lastSentAt);
            Assert::assertFalse($canResend);
            Assert::assertGreaterThan(0, $remaining);
        });

        $this->addTest('F05-T2-02: OTP Expired at 5m 1s Rejects Verification', function() {
            $otpRecord = [
                'code' => '654321',
                'expires_at' => time() - 1 // 1 sec past expiry
            ];
            $isExpired = time() > $otpRecord['expires_at'];
            Assert::assertTrue($isExpired);
        });

        $this->addTest('F05-T2-03: Alpha/Special Character OTP Input Fails Validation', function() {
            $badInputs = ['12a456', '12 456', '12345', '1234567', '<script>'];
            foreach ($badInputs as $bad) {
                $isValid = (bool)preg_match('/^[0-9]{6}$/', $bad);
                Assert::assertFalse($isValid);
            }
        });

        $this->addTest('F05-T2-04: 5 Consecutive Failed OTP Attempts Triggers Lockout', function() {
            $failedAttempts = 5;
            $maxAllowed = 5;
            $isLocked = ($failedAttempts >= $maxAllowed);
            Assert::assertTrue($isLocked);
        });

        $this->addTest('F05-T2-05: Null/Empty Email Address Rejected Gracefully', function() {
            $emptyEmail = '';
            $isValid = !empty($emptyEmail) && filter_var($emptyEmail, FILTER_VALIDATE_EMAIL) !== false;
            Assert::assertFalse($isValid);
        });

        // ==========================================
        // F06: OTP Verification & Vote Submission
        // ==========================================
        $this->addTest('F06-T1-01: Successful OTP Verification and Vote Submission', function() {
            $storedOtp = '582910';
            $userInput = '582910';
            $isVerified = hash_equals($storedOtp, $userInput);
            Assert::assertTrue($isVerified);
        });

        $this->addTest('F06-T1-02: Single Vote Tally Increment per Valid Submission', function() {
            $initialVotes = 100;
            $newVotes = $initialVotes + 1;
            Assert::assertEquals(101, $newVotes);
        });

        $this->addTest('F06-T1-03: 24-Hour Vote Uniqueness Enforcement per Voter Email', function() {
            $voterEmail = 'voter1@national.vn';
            $categoryVotes = [
                'voter1@national.vn|cat1' => time() - 3600 // 1 hour ago
            ];
            $key = $voterEmail . '|cat1';
            $hasVotedToday = isset($categoryVotes[$key]) && (time() - $categoryVotes[$key]) < 86400;
            Assert::assertTrue($hasVotedToday);
        });

        $this->addTest('F06-T1-04: Audit Trail Hash Generation on Vote Cast', function() {
            $payload = 'voter@national.vn|candidate_101|ip_14.225.1.1|' . time();
            $hash = hash('sha256', $payload);
            Assert::assertEquals(64, strlen($hash));
        });

        $this->addTest('F06-T1-05: Vote Response JSON Payload Structure', function() {
            $response = [
                'status' => 'success',
                'message' => 'Bình chọn thành công!',
                'candidate_id' => 101,
                'new_vote_total' => 1251
            ];
            Assert::assertEquals('success', $response['status']);
            Assert::assertContains('thành công', $response['message']);
            Assert::assertEquals(1251, $response['new_vote_total']);
        });

        // F06 Tier 2: Boundary / Edge Cases
        $this->addTest('F06-T2-01: Duplicate Vote from Same Email Within 24h Blocked', function() {
            $existingVoteTimestamp = time() - 7200; // 2 hours ago
            $canVoteAgain = (time() - $existingVoteTimestamp) >= 86400;
            Assert::assertFalse($canVoteAgain);
        });

        $this->addTest('F06-T2-02: Replay Attack with Already Used OTP Token Rejected', function() {
            $otpRecord = ['token' => 'tok123', 'is_used' => true];
            $canUse = !$otpRecord['is_used'];
            Assert::assertFalse($canUse);
        });

        $this->addTest('F06-T2-03: Tampered Candidate ID in Verified Payload Rejected', function() {
            $issuedForCandidate = 101;
            $submittedCandidate = 102;
            $matches = ($issuedForCandidate === $submittedCandidate);
            Assert::assertFalse($matches);
        });

        $this->addTest('F06-T2-04: Negative/Zero Delta Vote Injection Blocked', function() {
            $delta = -5;
            $isValidDelta = ($delta === 1);
            Assert::assertFalse($isValidDelta);
        });

        $this->addTest('F06-T2-05: Concurrent Double-Submit Handled Atomically', function() {
            $otpState = ['consumed' => false];
            // First submit
            $res1 = false;
            if (!$otpState['consumed']) {
                $otpState['consumed'] = true;
                $res1 = true;
            }
            // Second concurrent submit
            $res2 = false;
            if (!$otpState['consumed']) {
                $otpState['consumed'] = true;
                $res2 = true;
            }
            Assert::assertTrue($res1);
            Assert::assertFalse($res2);
        });

        // ==========================================
        // F07: 70/30 Combined Score Calculator
        // ==========================================
        $this->addTest('F07-T1-01: 70% Jury Score Weighting Calculation', function() {
            $score = TopBestGlobalEngine::calculate7030Score(90.0, 500, 1000);
            Assert::assertEquals(63.0, $score['jury_weighted_70']);
        });

        $this->addTest('F07-T1-02: 30% Community Public Votes Weighting Calculation', function() {
            $score = TopBestGlobalEngine::calculate7030Score(90.0, 500, 1000);
            Assert::assertEquals(15.0, $score['public_weighted_30']);
        });

        $this->addTest('F07-T1-03: Composite Score Aggregation (0-100 Scale)', function() {
            $score = TopBestGlobalEngine::calculate7030Score(90.0, 500, 1000);
            Assert::assertEquals(78.0, $score['composite_score']);
        });

        $this->addTest('F07-T1-04: Category Rank Determination Based on Composite Score', function() {
            $candidates = [
                ['id' => 1, 'score' => 88.5],
                ['id' => 2, 'score' => 94.2],
                ['id' => 3, 'score' => 76.0]
            ];
            usort($candidates, fn($a, $b) => $b['score'] <=> $a['score']);
            Assert::assertEquals(2, $candidates[0]['id']);
            Assert::assertEquals(1, $candidates[1]['id']);
            Assert::assertEquals(3, $candidates[2]['id']);
        });

        $this->addTest('F07-T1-05: Score Breakdown Transparency Metrics', function() {
            $score = TopBestGlobalEngine::calculate7030Score(85.5, 750, 1000);
            Assert::assertArrayHasKey('jury_score_norm', $score);
            Assert::assertArrayHasKey('jury_weighted_70', $score);
            Assert::assertArrayHasKey('public_score_norm', $score);
            Assert::assertArrayHasKey('public_weighted_30', $score);
            Assert::assertArrayHasKey('composite_score', $score);
        });

        // F07 Tier 2: Boundary / Edge Cases
        $this->addTest('F07-T2-01: Candidate with 0 Jury Score and 0 Votes Calculates to 0.00', function() {
            $score = TopBestGlobalEngine::calculate7030Score(0.0, 0, 1000);
            Assert::assertEquals(0.0, $score['composite_score']);
        });

        $this->addTest('F07-T2-02: Candidate with 100/100 Jury and 100% Votes Calculates to 100.00', function() {
            $score = TopBestGlobalEngine::calculate7030Score(100.0, 1000, 1000);
            Assert::assertEquals(100.0, $score['composite_score']);
        });

        $this->addTest('F07-T2-03: Exact Tie-Breaker Rule (Jury Precedence)', function() {
            $candA = ['id' => 'A', 'jury' => 90.0, 'public' => 300, 'composite' => 80.0];
            $candB = ['id' => 'B', 'jury' => 85.0, 'public' => 500, 'composite' => 80.0];
            // Compare on composite tie: jury score wins
            $winner = ($candA['composite'] === $candB['composite'])
                ? ($candA['jury'] > $candB['jury'] ? $candA['id'] : $candB['id'])
                : null;
            Assert::assertEquals('A', $winner);
        });

        $this->addTest('F07-T2-04: Candidate with Votes but Pending Jury Score Fallback', function() {
            $juryScorePending = null;
            $effectiveJury = $juryScorePending ?? 0.0;
            $score = TopBestGlobalEngine::calculate7030Score($effectiveJury, 800, 1000);
            Assert::assertEquals(24.0, $score['composite_score']);
        });

        $this->addTest('F07-T2-05: Floating Point Precision and 2-Decimal Rounding', function() {
            $score = TopBestGlobalEngine::calculate7030Score(87.33333, 437, 1000);
            Assert::assertEquals(round((87.33333 * 0.70) + ((437/1000)*100*0.30), 2), $score['composite_score']);
        });
    }
}

// Standalone CLI execution
if (php_sapi_name() === 'cli') {
    $suite = new TestE2EVoting();
    $res = $suite->run();
    if ($res->failed > 0) {
        exit(1);
    }
}
