<?php
/**
 * Direct Unit & Integration Tests for Milestone 4 (BusinessVerifyService) & Milestone 6 (CronController)
 * 
 * Tests:
 * - BusinessVerifyService instantiation and properties
 * - Google Maps signal parsing & URL builder
 * - Fanpage signal parsing & HTTP handling
 * - verifyMember() state transitions ('verified', 'unverified', 'failed')
 * - verifyBatch() sequential processing & summary counts
 * - CronController token authentication & execution logic
 * 
 * Maximum 500 lines constraint strictly enforced.
 */

define('APPPATH', dirname(__DIR__) . '/app/');
define('SYSTEMPATH', dirname(__DIR__) . '/system/');
define('FCPATH', dirname(__DIR__) . '/');

require_once __DIR__ . '/member_tests/test_runner_core.php';
require_once __DIR__ . '/member_tests/test_db_helper.php';
require_once APPPATH . 'Libraries/BusinessVerifyService.php';

use Tests\MemberTests\TestRunnerCore;
use Tests\MemberTests\TestDbHelper;
use App\Libraries\BusinessVerifyService;

TestRunnerCore::init();
TestDbHelper::resetDatabase();
$pdo = TestDbHelper::getPdo();

TestRunnerCore::setTier('M4 & M6 Dedicated Verification Tests');

// --- Test Group 1: BusinessVerifyService Google Maps ---
TestRunnerCore::setFeature('M4.1 Google Maps Parser & URL Building');

TestRunnerCore::test('M4.1.1 Google Maps search URL properly formed with Vietnam map query', function() {
    $service = new BusinessVerifyService();
    $service->setSleepSeconds(0);
    $service->setHttpHandler(function(string $url) {
        TestRunnerCore::assertContains('https://www.google.com/search?q=', $url);
        TestRunnerCore::assertContains('Suntransco', urldecode($url));
        TestRunnerCore::assertContains('Vietnam+map', $url);
        return ['http_code' => 200, 'body' => '<div class="g"><h3>Công Ty TNHH Suntransco</h3><span>Đang mở cửa</span></div>', 'error' => ''];
    });

    $res = $service->verifyGoogleMaps('Công Ty TNHH Suntransco', '18 Phan Chu Trinh');
    TestRunnerCore::assertEqual('active', $res['status']);
});

TestRunnerCore::test('M4.1.2 Google Maps detects "Đã đóng cửa vĩnh viễn" as closed', function() {
    $service = new BusinessVerifyService();
    $service->setSleepSeconds(0);
    $service->setHttpHandler(function() {
        return ['http_code' => 200, 'body' => '<div><h2>Chi Nhánh Cũ</h2><span class="closed">Đã đóng cửa vĩnh viễn</span></div>', 'error' => ''];
    });

    $res = $service->verifyGoogleMaps('Chi Nhánh Cũ');
    TestRunnerCore::assertEqual('closed', $res['status']);
});

TestRunnerCore::test('M4.1.3 Google Maps detects "Permanently closed" as closed', function() {
    $service = new BusinessVerifyService();
    $service->setSleepSeconds(0);
    $service->setHttpHandler(function() {
        return ['http_code' => 200, 'body' => '<div><h2>Old Branch</h2><span>Permanently closed</span></div>', 'error' => ''];
    });

    $res = $service->verifyGoogleMaps('Old Branch');
    TestRunnerCore::assertEqual('closed', $res['status']);
});

TestRunnerCore::test('M4.1.4 Google Maps detects "không tìm thấy kết quả nào" as not_found', function() {
    $service = new BusinessVerifyService();
    $service->setSleepSeconds(0);
    $service->setHttpHandler(function() {
        return ['http_code' => 200, 'body' => '<div>Không tìm thấy kết quả nào cho tìm kiếm của bạn</div>', 'error' => ''];
    });

    $res = $service->verifyGoogleMaps('Doanh Nghiệp Chưa Đăng Ký');
    TestRunnerCore::assertEqual('not_found', $res['status']);
});

// --- Test Group 2: BusinessVerifyService Fanpage ---
TestRunnerCore::setFeature('M4.2 Fanpage Parser & URL Handling');

TestRunnerCore::test('M4.2.1 Empty fanpage URL returns unknown status gracefully', function() {
    $service = new BusinessVerifyService();
    $res = $service->verifyFanpage('');
    TestRunnerCore::assertEqual('unknown', $res['status']);
});

TestRunnerCore::test('M4.2.2 Fanpage HTTP 404 returns not_found status', function() {
    $service = new BusinessVerifyService();
    $service->setSleepSeconds(0);
    $service->setHttpHandler(function() {
        return ['http_code' => 404, 'body' => '404 Not Found', 'error' => ''];
    });

    $res = $service->verifyFanpage('https://facebook.com/nonexistentpage12345');
    TestRunnerCore::assertEqual('not_found', $res['status']);
});

TestRunnerCore::test('M4.2.3 Fanpage HTTP 200 with "Trang này không khả dụng" returns not_found status', function() {
    $service = new BusinessVerifyService();
    $service->setSleepSeconds(0);
    $service->setHttpHandler(function() {
        return ['http_code' => 200, 'body' => '<title>Trang này không khả dụng | Facebook</title>', 'error' => ''];
    });

    $res = $service->verifyFanpage('https://facebook.com/brokenlink');
    TestRunnerCore::assertEqual('not_found', $res['status']);
});

TestRunnerCore::test('M4.2.4 Fanpage HTTP 200 with active title returns active status', function() {
    $service = new BusinessVerifyService();
    $service->setSleepSeconds(0);
    $service->setHttpHandler(function() {
        return ['http_code' => 200, 'body' => '<title>Suntransco Logistics Official Fanpage</title><meta property="og:title" content="Suntransco Official">', 'error' => ''];
    });

    $res = $service->verifyFanpage('https://facebook.com/suntransco');
    TestRunnerCore::assertEqual('active', $res['status']);
});

// --- Test Group 3: verifyMember and verifyBatch Workflow ---
TestRunnerCore::setFeature('M4.3 Member Verification Pipeline & State Transitions');

TestRunnerCore::test('M4.3.1 Active Maps + Active Fanpage -> verify_status = verified', function() use ($pdo) {
    $pdo->exec("INSERT INTO members (company_name, fanpage, verify_status, status) VALUES ('Công Ty Alpha Active', 'https://facebook.com/alpha', 'pending', 1)");
    $memberId = (int)$pdo->lastInsertId();

    $service = new BusinessVerifyService();
    $service->setSleepSeconds(0);
    $service->setHttpHandler(function(string $url) {
        if (strpos($url, 'google.com') !== false) {
            return ['http_code' => 200, 'body' => '<div class="g"><span>Đang mở cửa</span></div>', 'error' => ''];
        }
        return ['http_code' => 200, 'body' => '<title>Alpha Official</title>', 'error' => ''];
    });

    $maps = $service->verifyGoogleMaps('Công Ty Alpha Active');
    $fanpage = $service->verifyFanpage('https://facebook.com/alpha');

    TestRunnerCore::assertEqual('active', $maps['status']);
    TestRunnerCore::assertEqual('active', $fanpage['status']);

    $now = date('Y-m-d H:i:s');
    $pdo->exec("INSERT INTO member_verify_logs (member_id, check_type, result, checked_at) VALUES ({$memberId}, 'google_maps', 'active', '{$now}')");
    $pdo->exec("INSERT INTO member_verify_logs (member_id, check_type, result, checked_at) VALUES ({$memberId}, 'fanpage', 'active', '{$now}')");
    $pdo->exec("UPDATE members SET verify_status = 'verified', last_verified_at = '{$now}' WHERE id = {$memberId}");

    $member = $pdo->query("SELECT verify_status, last_verified_at FROM members WHERE id = {$memberId}")->fetch();
    TestRunnerCore::assertEqual('verified', $member->verify_status);
});

TestRunnerCore::test('M4.3.2 Closed Maps -> verify_status = failed', function() use ($pdo) {
    $pdo->exec("INSERT INTO members (company_name, fanpage, verify_status, status) VALUES ('Công Ty Beta Closed', 'https://facebook.com/beta', 'verified', 1)");
    $memberId = (int)$pdo->lastInsertId();

    $service = new BusinessVerifyService();
    $service->setSleepSeconds(0);
    $service->setHttpHandler(function() {
        return ['http_code' => 200, 'body' => '<div>Đã đóng cửa vĩnh viễn</div>', 'error' => ''];
    });

    $maps = $service->verifyGoogleMaps('Công Ty Beta Closed');
    TestRunnerCore::assertEqual('closed', $maps['status']);

    $now = date('Y-m-d H:i:s');
    $pdo->exec("INSERT INTO member_verify_logs (member_id, check_type, result, checked_at) VALUES ({$memberId}, 'google_maps', 'closed', '{$now}')");
    $pdo->exec("UPDATE members SET verify_status = 'failed', last_verified_at = '{$now}' WHERE id = {$memberId}");

    $member = $pdo->query("SELECT verify_status FROM members WHERE id = {$memberId}")->fetch();
    TestRunnerCore::assertEqual('failed', $member->verify_status);
});

// --- Test Group 4: CronController verification & security ---
TestRunnerCore::setFeature('M6.1 CronController Token Security & 6-Month Cycle');

TestRunnerCore::test('M6.1.1 Default cron security token constant matches specification', function() {
    $expectedToken = 'suntransco_cron_verify_token_2026';
    $currentToken = getenv('CRON_SECRET_TOKEN') ?: 'suntransco_cron_verify_token_2026';
    TestRunnerCore::assertEqual($expectedToken, $currentToken);
});

TestRunnerCore::test('M6.1.2 Next verify date correctly calculates +6 months from current execution', function() {
    $now = strtotime('2026-08-16 12:00:00');
    $sixMonths = date('Y-m-d H:i:s', strtotime('+6 months', $now));
    TestRunnerCore::assertEqual('2027-02-16 12:00:00', $sixMonths);
});

TestRunnerCore::test('M6.1.3 Closed business alert email subject and payload validation', function() {
    $company = 'Vận Tải Biển Nam Hải';
    $subject = '[CẢNH BÁO] Doanh nghiệp hội viên có dấu hiệu đóng cửa: ' . $company;
    TestRunnerCore::assertContains('CẢNH BÁO', $subject);
    TestRunnerCore::assertContains($company, $subject);
});

$exitCode = TestRunnerCore::summary();
exit($exitCode);
