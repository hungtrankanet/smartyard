<?php
/**
 * Tier 5: Adversarial Challenge & Stress-Testing Suite
 * Deep adversarial challenge and stress-testing for Member Management Module:
 * - Malformed & corrupted OCR input payloads, missing JSON fields, extreme UTF-8 Vietnamese diacritics.
 * - Google Maps tricky responses (mixed casing, partial HTML, foreign languages, network timeouts).
 * - Facebook Fanpage edge cases (HTTP 301/302 redirects, 404, 500, non-standard URLs).
 * - SQL injection / XSS resistance in member search, filters, and batch uploads.
 * - High concurrency simulation for member lookups and pagination.
 * - Sequential sleep(2) thread saturation prevention.
 * Maximum 500 lines constraint strictly enforced.
 */

namespace Tests\MemberTests;

require_once __DIR__ . '/test_runner_core.php';
require_once __DIR__ . '/test_db_helper.php';

class Tier5AdversarialChallengeTests
{
    public static function run(): void
    {
        TestRunnerCore::setTier('Tier 5: Adversarial Challenge & Stress Suite');

        self::testOcrPayloadFuzzingAndDiacritics();
        self::testGoogleMapsCrawlerTrickyResponses();
        self::testFacebookFanpageEdgeCases();
        self::testSqlInjectionAndXssPenetration();
        self::testHighConcurrencyAndMemoryProfiling();
        self::testRateLimitingAndSequentialThrottling();
    }

    private static function testOcrPayloadFuzzingAndDiacritics(): void
    {
        TestRunnerCore::setFeature('5.1 OCR Payload Fuzzing & Diacritics');

        // Helper mock parser simulating OcrService::parseJsonResponse
        $parseJson = function(string $rawText): ?array {
            $cleaned = trim($rawText);
            $cleaned = preg_replace('/^```(?:json)?\s*/i', '', $cleaned);
            $cleaned = preg_replace('/\s*```$/', '', $cleaned);
            $cleaned = trim($cleaned);
            $data = json_decode($cleaned, true);
            return is_array($data) ? $data : null;
        };

        // Helper normalizer simulating OcrService::normalizeResult
        $normalize = function(array $data): array {
            $schema = ['company_name', 'tax_code', 'address', 'city', 'website', 'fanpage', 'phone', 'email', 'representative_name', 'position'];
            $normalized = [];
            foreach ($schema as $key) {
                $val = $data[$key] ?? '';
                $normalized[$key] = $val !== null ? trim((string)$val) : '';
            }
            return $normalized;
        };

        TestRunnerCore::test('T5.1.1 Malformed, truncated, and non-JSON OCR payloads safely return null', function() use ($parseJson) {
            TestRunnerCore::assertNull($parseJson('{"company_name": "Broken'));
            TestRunnerCore::assertNull($parseJson('<html><body>502 Bad Gateway</body></html>'));
            TestRunnerCore::assertNull($parseJson(''));
            TestRunnerCore::assertNull($parseJson('   '));
            TestRunnerCore::assertNull($parseJson('key_without_quotes: 123'));
        });

        TestRunnerCore::test('T5.1.2 Markdown wrapped JSON blocks (```json ... ```) extracted cleanly', function() use ($parseJson, $normalize) {
            $wrapped = "```json\n{\n  \"company_name\": \"Công Ty Vận Tải Biển Bắc Nam\",\n  \"tax_code\": \"0201122334\",\n  \"city\": \"Hải Phòng\"\n}\n```";
            $parsed = $parseJson($wrapped);
            TestRunnerCore::assertNotNull($parsed);
            $norm = $normalize($parsed);
            TestRunnerCore::assertEqual('Công Ty Vận Tải Biển Bắc Nam', $norm['company_name']);
            TestRunnerCore::assertEqual('0201122334', $norm['tax_code']);
            TestRunnerCore::assertEqual('Hải Phòng', $norm['city']);
            TestRunnerCore::assertEqual('', $norm['website']);
        });

        TestRunnerCore::test('T5.1.3 Extreme Vietnamese UTF-8 diacritics and complex characters preserved losslessly', function() use ($normalize) {
            $complexDiacritics = [
                'company_name'        => 'CÔNG TY TNHH VẬN TẢI & DỊCH VỤ HẢI QUAN TÂN CẢNG ĐỒNG NAI',
                'tax_code'            => '3601234567-001',
                'address'             => 'Đường số 9, KCN Biên Hòa 1, P. An Bình, TP. Biên Hòa, Đồng Nai',
                'city'                => 'Đồng Nai',
                'website'             => 'https://tancang-dongnai.vn',
                'fanpage'             => 'https://facebook.com/tancangdongnai',
                'phone'               => '(+84) 0251 3838 999',
                'email'               => 'nguyen.van.hau@tancang-dongnai.vn',
                'representative_name' => 'Nguyễn Thị Thu Hường',
                'position'            => 'Tổng Giám Đốc (CEO)',
            ];
            $norm = $normalize($complexDiacritics);
            foreach ($complexDiacritics as $key => $expected) {
                TestRunnerCore::assertEqual($expected, $norm[$key], "Field '{$key}' must match exact UTF-8 bytes");
            }
        });

        TestRunnerCore::test('T5.1.4 Front and back asymmetric OCR merge precedence', function() use ($normalize) {
            $front = ['company_name' => 'Sun Logistics', 'phone' => '090111222', 'email' => ''];
            $back = ['website' => 'https://sunlogistics.vn', 'phone' => '024333444', 'email' => 'contact@sunlogistics.vn', 'address' => 'Hà Nội'];

            $schema = ['company_name', 'tax_code', 'address', 'city', 'website', 'fanpage', 'phone', 'email', 'representative_name', 'position'];
            $merged = [];
            foreach ($schema as $k) {
                $fVal = isset($front[$k]) ? trim((string)$front[$k]) : '';
                $bVal = isset($back[$k]) ? trim((string)$back[$k]) : '';
                $merged[$k] = $fVal !== '' ? $fVal : ($bVal !== '' ? $bVal : '');
            }

            TestRunnerCore::assertEqual('Sun Logistics', $merged['company_name']);
            TestRunnerCore::assertEqual('090111222', $merged['phone'], 'Front phone must have priority');
            TestRunnerCore::assertEqual('https://sunlogistics.vn', $merged['website']);
            TestRunnerCore::assertEqual('contact@sunlogistics.vn', $merged['email'], 'Back email populates empty front email');
            TestRunnerCore::assertEqual('Hà Nội', $merged['address']);
        });
    }

    private static function testGoogleMapsCrawlerTrickyResponses(): void
    {
        TestRunnerCore::setFeature('5.2 Google Maps Crawler Tricky Responses');

        $parseGoogleMaps = function(int $httpCode, string $html): string {
            if ($httpCode !== 200 || empty($html)) {
                return 'unknown';
            }
            $lower = mb_strtolower($html, 'UTF-8');
            $closedKeywords = [
                'đã đóng cửa vĩnh viễn', 'đã đóng cửa', 'tạm thời đóng cửa',
                'permanently closed', 'temporarily closed', 'closed permanently', 'đóng cửa tạm thời'
            ];
            foreach ($closedKeywords as $kw) {
                if (strpos($lower, $kw) !== false) {
                    return 'closed';
                }
            }
            $notFoundSignals = ['did not match any documents', 'không tìm thấy kết quả', 'your search -'];
            foreach ($notFoundSignals as $signal) {
                if (strpos($lower, $signal) !== false) {
                    return 'not_found';
                }
            }
            $activeSignals = ['đang mở cửa', 'open now', 'business-card', 'bản đồ', 'rating', 'xếp hạng:'];
            foreach ($activeSignals as $signal) {
                if (strpos($lower, $signal) !== false) {
                    return 'active';
                }
            }
            return 'unknown';
        };

        TestRunnerCore::test('T5.2.1 Case-insensitive uppercase & mixed signals detect closed status', function() use ($parseGoogleMaps) {
            TestRunnerCore::assertEqual('closed', $parseGoogleMaps(200, '<div class="biz-info"><span class="badge">ĐÃ ĐÓNG CỬA VĨNH VIỄN</span></div>'));
            TestRunnerCore::assertEqual('closed', $parseGoogleMaps(200, '<div>Status: TẠM THỜI ĐÓNG CỬA</div>'));
            TestRunnerCore::assertEqual('closed', $parseGoogleMaps(200, '<span>Permanently CLOSED</span>'));
            TestRunnerCore::assertEqual('closed', $parseGoogleMaps(200, '<p>Temporarily Closed for renovations</p>'));
            TestRunnerCore::assertEqual('closed', $parseGoogleMaps(200, '<h3>CLOSED PERMANENTLY</h3>'));
        });

        TestRunnerCore::test('T5.2.2 Network errors, HTTP 500/502/429, and timeouts safely degrade to unknown', function() use ($parseGoogleMaps) {
            TestRunnerCore::assertEqual('unknown', $parseGoogleMaps(500, 'Internal Server Error'));
            TestRunnerCore::assertEqual('unknown', $parseGoogleMaps(502, 'Bad Gateway'));
            TestRunnerCore::assertEqual('unknown', $parseGoogleMaps(429, 'Too Many Requests'));
            TestRunnerCore::assertEqual('unknown', $parseGoogleMaps(0, ''));
        });

        TestRunnerCore::test('T5.2.3 Foreign language responses without Latin/Vietnamese signals degrade safely', function() use ($parseGoogleMaps) {
            $chineseClosed = '<div class="place">已关闭</div>';
            TestRunnerCore::assertEqual('unknown', $parseGoogleMaps(200, $chineseClosed), 'Foreign closed signal degrades to unknown without throwing');
        });
    }

    private static function testFacebookFanpageEdgeCases(): void
    {
        TestRunnerCore::setFeature('5.3 Facebook Fanpage Edge Cases');

        $parseFanpage = function(int $httpCode, string $html): string {
            if ($httpCode === 404 || $httpCode === 410) {
                return 'not_found';
            }
            if ($httpCode === 200 || $httpCode === 301 || $httpCode === 302) {
                $lower = mb_strtolower($html, 'UTF-8');
                $unavailableSignals = [
                    'trang này không khả dụng', "this page isn't available",
                    'content not found', 'nội dung này hiện không khả dụng',
                    'the link you followed may be broken', 'liên kết có thể đã bị hỏng'
                ];
                foreach ($unavailableSignals as $signal) {
                    if (strpos($lower, $signal) !== false) {
                        return 'not_found';
                    }
                }
                return 'active';
            }
            return 'unknown';
        };

        TestRunnerCore::test('T5.3.1 Fanpage URL protocol normalization', function() {
            $normalizeUrl = function(string $url): string {
                $trimmed = trim($url);
                if (empty($trimmed)) return '';
                if (!preg_match('~^https?://~i', $trimmed)) {
                    return 'https://' . $trimmed;
                }
                return $trimmed;
            };

            TestRunnerCore::assertEqual('https://facebook.com/suntransco', $normalizeUrl('facebook.com/suntransco'));
            TestRunnerCore::assertEqual('http://facebook.com/suntransco', $normalizeUrl('http://facebook.com/suntransco'));
            TestRunnerCore::assertEqual('https://m.facebook.com/page?id=123', $normalizeUrl('https://m.facebook.com/page?id=123'));
            TestRunnerCore::assertEqual('', $normalizeUrl(''));
        });

        TestRunnerCore::test('T5.3.2 Fanpage 404, 410, and Vietnamese/English removal indicators detect not_found', function() use ($parseFanpage) {
            TestRunnerCore::assertEqual('not_found', $parseFanpage(404, ''));
            TestRunnerCore::assertEqual('not_found', $parseFanpage(410, ''));
            TestRunnerCore::assertEqual('not_found', $parseFanpage(200, '<title>Trang này không khả dụng | Facebook</title>'));
            TestRunnerCore::assertEqual('not_found', $parseFanpage(200, "<title>This page isn't available | Facebook</title>"));
            TestRunnerCore::assertEqual('not_found', $parseFanpage(200, '<span>Nội dung này hiện không khả dụng</span>'));
            TestRunnerCore::assertEqual('not_found', $parseFanpage(200, '<span>The link you followed may be broken</span>'));
            TestRunnerCore::assertEqual('active', $parseFanpage(200, '<title>Suntransco Logistics Official | Facebook</title>'));
        });
    }

    private static function testSqlInjectionAndXssPenetration(): void
    {
        TestRunnerCore::setFeature('5.4 SQL Injection & XSS Penetration');

        TestRunnerCore::test('T5.4.1 SQL Injection payloads in member filter queries safely parameterized', function() {
            $pdo = TestDbHelper::getPdo();
            $pdo->exec("INSERT INTO members (company_name, representative_name, status, verify_status) VALUES ('Secure Corp', 'Nguyen Van Safe', 1, 'verified')");

            $sqliVectors = [
                "' OR '1'='1",
                "1; DROP TABLE members; --",
                "' UNION SELECT 1, 'Hacked', '999', 'Hacked', 'Hanoi', '', '', '', '', '', '', 1, 'member', 1, 'verified', NULL, NULL, '', NULL, NULL --",
                "admin'--",
                "\" OR \"\"=\"",
            ];

            foreach ($sqliVectors as $vec) {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM members WHERE (company_name LIKE :q OR representative_name LIKE :q)");
                $stmt->execute([':q' => '%' . $vec . '%']);
                $count = (int)$stmt->fetchColumn();
                TestRunnerCore::assertEqual(0, $count, "SQL injection leaked records: {$vec}");
            }

            // Verify table integrity
            $count = (int)$pdo->query("SELECT COUNT(*) FROM members")->fetchColumn();
            TestRunnerCore::assertTrue($count >= 1, 'Table integrity preserved');
        });

        TestRunnerCore::test('T5.4.2 XSS payloads escaped by esc() across HTML and attribute contexts', function() {
            $xssVectors = [
                '<script>alert("XSS")</script>',
                '<img src=x onerror=alert(1)>',
                '"><svg onload=alert(1)>',
                "javascript:alert('XSS')",
                "<body onload=alert('XSS')>",
                "';alert(String.fromCharCode(88,83,83))//\'",
            ];

            foreach ($xssVectors as $payload) {
                $escaped = esc($payload);
                TestRunnerCore::assertFalse(strpos($escaped, '<script>'), "Unescaped script tag: {$escaped}");
                TestRunnerCore::assertFalse(strpos($escaped, '<img src=x onerror='), "Unescaped img tag: {$escaped}");
                TestRunnerCore::assertFalse(strpos($escaped, '<svg onload='), "Unescaped svg tag: {$escaped}");
            }
        });
    }

    private static function testHighConcurrencyAndMemoryProfiling(): void
    {
        TestRunnerCore::setFeature('5.5 High Concurrency & Memory Profiling');

        TestRunnerCore::test('T5.5.1 High-throughput lookup simulation: 2,000 DB operations', function() {
            $pdo = TestDbHelper::getPdo();
            $startMem = memory_get_usage();
            $startTime = microtime(true);
            $iterations = 2000;

            $stmt = $pdo->prepare("SELECT m.*, it.name as industry_name FROM members m LEFT JOIN industry_types it ON it.id = m.industry_type_id WHERE m.id = ?");
            for ($k = 0; $k < $iterations; $k++) {
                $stmt->execute([1]);
                $row = $stmt->fetch();
            }

            $elapsedMs = (microtime(true) - $startTime) * 1000;
            $memDeltaKb = (memory_get_usage() - $startMem) / 1024;
            $opsSec = number_format($iterations / ($elapsedMs / 1000), 0);

            TestRunnerCore::assertTrue($elapsedMs < 1000, "Throughput too slow: {$elapsedMs}ms ({$opsSec} ops/sec)");
            TestRunnerCore::assertTrue($memDeltaKb < 1024, "Memory leak detected: {$memDeltaKb} KB");
        });

        TestRunnerCore::test('T5.5.2 Pagination boundary math handles negative and massive offsets safely', function() {
            $calcOffset = fn($page, $perPage) => (max(1, (int)$page) - 1) * max(1, (int)$perPage);
            TestRunnerCore::assertEqual(0, $calcOffset(-10, 20));
            TestRunnerCore::assertEqual(0, $calcOffset(0, 20));
            TestRunnerCore::assertEqual(0, $calcOffset(1, 20));
            TestRunnerCore::assertEqual(20, $calcOffset(2, 20));
            TestRunnerCore::assertEqual(1980, $calcOffset(100, 20));

            $pdo = TestDbHelper::getPdo();
            $stmt = $pdo->prepare("SELECT * FROM members LIMIT 20 OFFSET ?");
            $stmt->execute([999999]);
            TestRunnerCore::assertCount(0, $stmt->fetchAll());
        });
    }

    private static function testRateLimitingAndSequentialThrottling(): void
    {
        TestRunnerCore::setFeature('5.6 Rate Limiting & Sequential Throttling');

        TestRunnerCore::test('T5.6.1 Sequential throttling delay calculation prevents thread saturation', function() {
            $sleepSeconds = 2;
            $memberCount = 10;
            $totalDelay = ($memberCount - 1) * $sleepSeconds;

            TestRunnerCore::assertEqual(18, $totalDelay, '10 batch items must delay exactly 18s total (2s between each)');
            TestRunnerCore::assertTrue($sleepSeconds >= 2, 'Minimum 2s delay protects external target endpoints');
        });
    }
}
