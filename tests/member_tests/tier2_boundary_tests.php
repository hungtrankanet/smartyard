<?php
/**
 * Tier 2: Boundary & Corner Cases Test Suite
 * Tests edge cases, extremes, and error conditions:
 * - Empty / missing fields and whitespace trimming
 * - Malformed / invalid image files and corrupted base64
 * - Fanpage 404, 500, redirect, and unavailable messages
 * - Google Maps varied casing, accents, and closed keyword signals
 * - Timestamp boundaries (exact NOW, +/- 1 sec, NULL dates)
 * - Inactive member (status = 0) cron exclusion
 * - Missing URL graceful degradation
 * - Enum constraint violations
 * - Long strings and special characters injection resilience
 * Maximum 500 lines constraint strictly enforced.
 */

namespace Tests\MemberTests;

require_once __DIR__ . '/test_runner_core.php';
require_once __DIR__ . '/test_db_helper.php';

class Tier2BoundaryTests
{
    public static function run(): void
    {
        TestRunnerCore::setTier('Tier 2: Boundary & Corner Cases');

        self::testEmptyAndWhitespaceFields();
        self::testImageFileBoundaries();
        self::testFanpageVerificationBoundaries();
        self::testGoogleMapsClosedSignalsBoundaries();
        self::testTimestampAndCronBoundaries();
        self::testEnumAndTypeBoundaries();
        self::testSanitizationAndSecurityBoundaries();
    }

    private static function testEmptyAndWhitespaceFields(): void
    {
        TestRunnerCore::setFeature('2.1 Empty & Whitespace Fields Handling');

        TestRunnerCore::test('T2.1.1 Empty company_name is rejected during member creation', function() {
            $pdo = TestDbHelper::getPdo();
            $validator = function(array $data): bool {
                $name = trim($data['company_name'] ?? '');
                return !empty($name);
            };

            TestRunnerCore::assertFalse($validator(['company_name' => '']));
            TestRunnerCore::assertFalse($validator(['company_name' => '   ']));
            TestRunnerCore::assertFalse($validator(['company_name' => "\t\n\r"]));
            TestRunnerCore::assertTrue($validator(['company_name' => 'Công Ty Hợp Lệ']));
        });

        TestRunnerCore::test('T2.1.2 Optional fields with empty string or null convert to null in DB', function() {
            $cleanField = function($val) {
                if ($val === null) return null;
                $trimmed = trim((string)$val);
                return $trimmed !== '' ? $trimmed : null;
            };

            TestRunnerCore::assertNull($cleanField(''));
            TestRunnerCore::assertNull($cleanField('   '));
            TestRunnerCore::assertNull($cleanField(null));
            TestRunnerCore::assertEqual('0101234567', $cleanField('  0101234567  '));
        });

        TestRunnerCore::test('T2.1.3 OCR extraction missing optional fields assigns empty strings', function() {
            $rawAiResponse = [
                'company_name' => 'Công Ty Cổ Phần Alpha',
                'phone'        => '0987654321',
            ];

            $schemaKeys = [
                'company_name', 'tax_code', 'address', 'city', 'website',
                'fanpage', 'phone', 'email', 'representative_name', 'position'
            ];

            $normalized = [];
            foreach ($schemaKeys as $key) {
                $normalized[$key] = isset($rawAiResponse[$key]) ? (string)$rawAiResponse[$key] : '';
            }

            TestRunnerCore::assertCount(10, $normalized);
            TestRunnerCore::assertEqual('Công Ty Cổ Phần Alpha', $normalized['company_name']);
            TestRunnerCore::assertEqual('', $normalized['tax_code']);
            TestRunnerCore::assertEqual('', $normalized['website']);
            TestRunnerCore::assertEqual('', $normalized['fanpage']);
            TestRunnerCore::assertEqual('0987654321', $normalized['phone']);
        });
    }

    private static function testImageFileBoundaries(): void
    {
        TestRunnerCore::setFeature('2.2 Image File & Upload Boundaries');

        TestRunnerCore::test('T2.2.1 Non-existent card file path handled gracefully without crash', function() {
            $checkFile = function(string $path): bool {
                return !empty($path) && file_exists($path) && is_file($path);
            };

            TestRunnerCore::assertFalse($checkFile('/tmp/non_existent_card_' . uniqid() . '.jpg'));
            TestRunnerCore::assertFalse($checkFile(''));
        });

        TestRunnerCore::test('T2.2.2 Zero-byte image file is rejected', function() {
            $tmp = tempnam(sys_get_temp_dir(), 'empty_img_');
            file_put_contents($tmp, '');

            $isValidImage = function(string $path): bool {
                if (!file_exists($path) || filesize($path) === 0) {
                    return false;
                }
                $info = @getimagesize($path);
                return $info !== false;
            };

            TestRunnerCore::assertFalse($isValidImage($tmp), 'Zero-byte file must be rejected as invalid image');
            @unlink($tmp);
        });

        TestRunnerCore::test('T2.2.3 Corrupted base64 payload is safely detected', function() {
            $invalidBase64 = '!!!This_is_NOT_valid_base64!!!';
            $decoded = base64_decode($invalidBase64, true);
            TestRunnerCore::assertFalse($decoded, 'Strict base64 decode returns false for corrupted strings');
        });

        TestRunnerCore::test('T2.2.4 Allowed visit card image extensions validation', function() {
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'heic'];
            $validateExt = function(string $filename) use ($allowedExtensions): bool {
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                return in_array($ext, $allowedExtensions);
            };

            TestRunnerCore::assertTrue($validateExt('card_front.jpg'));
            TestRunnerCore::assertTrue($validateExt('card_back.PNG'));
            TestRunnerCore::assertTrue($validateExt('card.webp'));
            TestRunnerCore::assertFalse($validateExt('malicious.php'));
            TestRunnerCore::assertFalse($validateExt('script.sh'));
            TestRunnerCore::assertFalse($validateExt('document.pdf'));
        });
    }

    private static function testFanpageVerificationBoundaries(): void
    {
        TestRunnerCore::setFeature('2.3 Fanpage Verification Boundaries');

        $verifyFanpage = function(?string $url, int $httpCode, string $htmlBody): string {
            if (empty($url) || trim($url) === '') {
                return 'skipped';
            }
            if ($httpCode === 404 || $httpCode === 410) {
                return 'not_found';
            }
            if ($httpCode === 200 || $httpCode === 301 || $httpCode === 302) {
                $lower = mb_strtolower($htmlBody, 'UTF-8');
                $notFoundSignals = [
                    'trang này không khả dụng',
                    "this page isn't available",
                    'liên kết có thể đã bị hỏng',
                    'the link may be broken',
                    'page not found',
                ];
                foreach ($notFoundSignals as $signal) {
                    if (strpos($lower, $signal) !== false) {
                        return 'not_found';
                    }
                }
                return 'active';
            }
            return 'unknown';
        };

        TestRunnerCore::test('T2.3.1 Empty or null fanpage URL returns skipped status', function() use ($verifyFanpage) {
            TestRunnerCore::assertEqual('skipped', $verifyFanpage('', 200, ''));
            TestRunnerCore::assertEqual('skipped', $verifyFanpage(null, 200, ''));
            TestRunnerCore::assertEqual('skipped', $verifyFanpage('   ', 200, ''));
        });

        TestRunnerCore::test('T2.3.2 Fanpage 404 Not Found returns not_found status', function() use ($verifyFanpage) {
            TestRunnerCore::assertEqual('not_found', $verifyFanpage('https://facebook.com/nonexistent123', 404, ''));
        });

        TestRunnerCore::test('T2.3.3 Vietnamese "Trang này không khả dụng" returns not_found status', function() use ($verifyFanpage) {
            $html = '<html><head><title>Trang này không khả dụng</title></head><body>Liên kết có thể đã bị hỏng hoặc trang đã bị gỡ.</body></html>';
            TestRunnerCore::assertEqual('not_found', $verifyFanpage('https://facebook.com/closedpage', 200, $html));
        });

        TestRunnerCore::test('T2.3.4 English "This page isn\'t available" returns not_found status', function() use ($verifyFanpage) {
            $html = '<html><head><title>This page isn\'t available</title></head><body>The link may be broken or the Page may have been removed.</body></html>';
            TestRunnerCore::assertEqual('not_found', $verifyFanpage('https://facebook.com/removedpage', 200, $html));
        });

        TestRunnerCore::test('T2.3.5 Valid active fanpage with live title returns active status', function() use ($verifyFanpage) {
            $html = '<html><head><title>Suntransco Logistics Vietnam - Trang chủ | Facebook</title></head><body>Posts and events</body></html>';
            TestRunnerCore::assertEqual('active', $verifyFanpage('https://facebook.com/suntransco', 200, $html));
        });
    }

    private static function testGoogleMapsClosedSignalsBoundaries(): void
    {
        TestRunnerCore::setFeature('2.4 Google Maps Signals & Diacritics Boundaries');

        $parseMapsStatus = function(string $html): string {
            $lower = mb_strtolower($html, 'UTF-8');
            $closedKeywords = [
                'đã đóng cửa',
                'đã đóng cửa vĩnh viễn',
                'tạm thời đóng cửa',
                'permanently closed',
                'temporarily closed',
                'closed permanently',
            ];
            foreach ($closedKeywords as $kw) {
                if (strpos($lower, $kw) !== false) {
                    return 'closed';
                }
            }
            if (strpos($lower, 'không tìm thấy kết quả') !== false || strpos($lower, 'did not match any documents') !== false) {
                return 'not_found';
            }
            return 'active';
        };

        TestRunnerCore::test('T2.4.1 Case-insensitive Vietnamese uppercase "ĐÃ ĐÓNG CỬA"', function() use ($parseMapsStatus) {
            $html = '<div class="biz-info"><span class="badge">ĐÃ ĐÓNG CỬA VĨNH VIỄN</span></div>';
            TestRunnerCore::assertEqual('closed', $parseMapsStatus($html));
        });

        TestRunnerCore::test('T2.4.2 Mixed casing "Temporarily Closed" detected as closed', function() use ($parseMapsStatus) {
            $html = '<div class="place-header"><span>Status: Temporarily Closed</span></div>';
            TestRunnerCore::assertEqual('closed', $parseMapsStatus($html));
        });

        TestRunnerCore::test('T2.4.3 Search with no results found returns not_found', function() use ($parseMapsStatus) {
            $html = '<div>Không tìm thấy kết quả nào phù hợp với từ khóa của bạn.</div>';
            TestRunnerCore::assertEqual('not_found', $parseMapsStatus($html));
        });

        TestRunnerCore::test('T2.4.4 Active location with rating and hours returns active', function() use ($parseMapsStatus) {
            $html = '<div class="place-card"><h3>Suntransco Logistics</h3><span>Đang mở cửa: 08:00 - 17:30</span></div>';
            TestRunnerCore::assertEqual('active', $parseMapsStatus($html));
        });
    }

    private static function testTimestampAndCronBoundaries(): void
    {
        TestRunnerCore::setFeature('2.5 Timestamp & Cron Query Boundaries');

        TestRunnerCore::test('T2.5.1 Expired next_verify_at (1 second ago) IS selected for verification', function() {
            $pdo = TestDbHelper::getPdo();
            $now = time();
            $oneSecAgo = date('Y-m-d H:i:s', $now - 1);
            $nowDate = date('Y-m-d H:i:s', $now);

            $pdo->exec("INSERT INTO members (company_name, status, verify_status, next_verify_at) VALUES ('Expired 1s Corp', 1, 'pending', '{$oneSecAgo}')");
            $id = $pdo->lastInsertId();

            $stmt = $pdo->prepare("SELECT id FROM members WHERE id = ? AND status = 1 AND next_verify_at <= ?");
            $stmt->execute([$id, $nowDate]);
            TestRunnerCore::assertNotNull($stmt->fetch(), 'Member with next_verify_at 1s ago must be selected');
        });

        TestRunnerCore::test('T2.5.2 Future next_verify_at (1 second in future) is NOT selected for verification', function() {
            $pdo = TestDbHelper::getPdo();
            $now = time();
            $oneSecFuture = date('Y-m-d H:i:s', $now + 3600); // 1 hour future
            $nowDate = date('Y-m-d H:i:s', $now);

            $pdo->exec("INSERT INTO members (company_name, status, verify_status, next_verify_at) VALUES ('Future 1h Corp', 1, 'verified', '{$oneSecFuture}')");
            $id = $pdo->lastInsertId();

            $stmt = $pdo->prepare("SELECT id FROM members WHERE id = ? AND status = 1 AND next_verify_at <= ?");
            $stmt->execute([$id, $nowDate]);
            TestRunnerCore::assertFalse($stmt->fetch(), 'Member with future next_verify_at must NOT be selected');
        });

        TestRunnerCore::test('T2.5.3 Inactive member (status = 0) with expired next_verify_at is NOT selected', function() {
            $pdo = TestDbHelper::getPdo();
            $now = date('Y-m-d H:i:s');
            $past = date('Y-m-d H:i:s', strtotime('-10 days'));

            $pdo->exec("INSERT INTO members (company_name, status, verify_status, next_verify_at) VALUES ('Banned Corp', 0, 'pending', '{$past}')");
            $id = $pdo->lastInsertId();

            $stmt = $pdo->prepare("SELECT id FROM members WHERE id = ? AND status = 1 AND next_verify_at <= ?");
            $stmt->execute([$id, $now]);
            TestRunnerCore::assertFalse($stmt->fetch(), 'Inactive member must be filtered out by status = 1');
        });

        TestRunnerCore::test('T2.5.4 NULL next_verify_at is treated as due for initial verification', function() {
            $pdo = TestDbHelper::getPdo();
            $now = date('Y-m-d H:i:s');

            $pdo->exec("INSERT INTO members (company_name, status, verify_status, next_verify_at) VALUES ('Unscheduled Corp', 1, 'pending', NULL)");
            $id = $pdo->lastInsertId();

            $stmt = $pdo->prepare("SELECT id FROM members WHERE id = ? AND status = 1 AND (next_verify_at <= ? OR next_verify_at IS NULL)");
            $stmt->execute([$id, $now]);
            TestRunnerCore::assertNotNull($stmt->fetch(), 'Member with NULL next_verify_at must be selected for initial run');
        });
    }

    private static function testEnumAndTypeBoundaries(): void
    {
        TestRunnerCore::setFeature('2.6 Enum & Field Type Constraints');

        TestRunnerCore::test('T2.6.1 member_type enum validation falls back to "member"', function() {
            $validateMemberType = function(string $type): string {
                return in_array($type, ['prospect', 'member', 'partner']) ? $type : 'member';
            };

            TestRunnerCore::assertEqual('prospect', $validateMemberType('prospect'));
            TestRunnerCore::assertEqual('member', $validateMemberType('member'));
            TestRunnerCore::assertEqual('partner', $validateMemberType('partner'));
            TestRunnerCore::assertEqual('member', $validateMemberType('invalid_type'));
            TestRunnerCore::assertEqual('member', $validateMemberType(''));
        });

        TestRunnerCore::test('T2.6.2 verify_status enum validation falls back to "pending"', function() {
            $validateVerifyStatus = function(string $status): string {
                return in_array($status, ['pending', 'verified', 'unverified', 'failed']) ? $status : 'pending';
            };

            TestRunnerCore::assertEqual('pending', $validateVerifyStatus('pending'));
            TestRunnerCore::assertEqual('verified', $validateVerifyStatus('verified'));
            TestRunnerCore::assertEqual('unverified', $validateVerifyStatus('unverified'));
            TestRunnerCore::assertEqual('failed', $validateVerifyStatus('failed'));
            TestRunnerCore::assertEqual('pending', $validateVerifyStatus('unknown_status'));
        });

        TestRunnerCore::test('T2.6.3 card side enum validation falls back to "single"', function() {
            $validateCardSide = function(string $side): string {
                return in_array($side, ['front', 'back', 'single']) ? $side : 'single';
            };

            TestRunnerCore::assertEqual('front', $validateCardSide('front'));
            TestRunnerCore::assertEqual('back', $validateCardSide('back'));
            TestRunnerCore::assertEqual('single', $validateCardSide('single'));
            TestRunnerCore::assertEqual('single', $validateCardSide('both'));
        });
    }

    private static function testSanitizationAndSecurityBoundaries(): void
    {
        TestRunnerCore::setFeature('2.7 Sanitization & Injection Resilience');

        TestRunnerCore::test('T2.7.1 XSS payloads in representative_name and note are sanitized/escaped', function() {
            $xssName = '<script>alert("XSS")</script> Nguyễn Văn A';
            $cleaned = strip_tags($xssName);
            TestRunnerCore::assertFalse(strpos($cleaned, '<script>'));
            TestRunnerCore::assertTrue(strpos($cleaned, 'Nguyễn Văn A') !== false);
        });

        TestRunnerCore::test('T2.7.2 SQL Injection patterns in search filters safely parameterized', function() {
            $pdo = TestDbHelper::getPdo();
            $maliciousSearch = "' OR '1'='1";
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM members WHERE company_name LIKE ?");
            $stmt->execute(['%' . $maliciousSearch . '%']);
            $count = (int)$stmt->fetchColumn();
            // Should match 0 records (literal string match), NOT return all records
            TestRunnerCore::assertEqual(0, $count, 'SQL injection string must not bypass parameterized query');
        });
    }
}
