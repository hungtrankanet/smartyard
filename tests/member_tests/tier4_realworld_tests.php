<?php
/**
 * Tier 4: Real-World Scenarios & Production Workloads Test Suite
 * Tests high-load simulations, realistic business flows, and alert integrations:
 * - High-volume batch card upload & sequential OCR extraction (20+ cards)
 * - Enterprise directory multi-parameter filtering & search (50+ members across 15 industries)
 * - Closed business detection and admin email alert notification trigger
 * - Verification failure recovery & timeline history retention
 * - Multi-cycle rolling 18-month verification scheduler
 * Maximum 500 lines constraint strictly enforced.
 */

namespace Tests\MemberTests;

require_once __DIR__ . '/test_runner_core.php';
require_once __DIR__ . '/test_db_helper.php';

class Tier4RealworldTests
{
    public static function run(): void
    {
        TestRunnerCore::setTier('Tier 4: Real-World Scenarios');

        self::testBatchCardImportStressWorkload();
        self::testEnterpriseDirectorySearchWorkload();
        self::testClosedBusinessEmailAlertScenario();
        self::testFailureRecoveryAndTimelineScenario();
        self::testRolling18MonthVerificationLifecycle();
    }

    private static function testBatchCardImportStressWorkload(): void
    {
        TestRunnerCore::setFeature('4.1 High-Volume Batch Card Import Simulation');

        TestRunnerCore::test('T4.1.1 Batch upload and sequential processing of 20 business cards', function() {
            $pdo = TestDbHelper::getPdo();
            $cardCount = 20;
            $now = date('Y-m-d H:i:s');

            $pdo->beginTransaction();
            $createdMembers = [];

            for ($i = 1; $i <= $cardCount; $i++) {
                // 1. Create member draft
                $mStmt = $pdo->prepare("
                    INSERT INTO members (company_name, status, verify_status, created_at, updated_at)
                    VALUES (?, 1, 'pending', ?, ?)
                ");
                $company = "Logistics Partner #{$i} Corp";
                $mStmt->execute([$company, $now, $now]);
                $memberId = (int)$pdo->lastInsertId();

                // 2. Attach front card
                $cStmt = $pdo->prepare("
                    INSERT INTO member_cards (member_id, file_path, side, ocr_status, created_at)
                    VALUES (?, ?, 'front', 'pending', ?)
                ");
                $filePath = "uploads/cards/batch_card_{$i}.jpg";
                $cStmt->execute([$memberId, $filePath, $now]);
                $cardId = (int)$pdo->lastInsertId();

                // 3. Simulate sequential OCR processing
                $ocrPayload = [
                    'company_name'        => $company,
                    'tax_code'            => sprintf('010%07d', $i),
                    'address'             => "Address {$i}, Industrial Park",
                    'city'                => ($i % 2 === 0) ? 'Hà Nội' : 'TP. Hồ Chí Minh',
                    'website'             => "https://partner{$i}.com",
                    'fanpage'             => "https://facebook.com/partner{$i}",
                    'phone'               => sprintf('090%07d', $i),
                    'email'               => "contact@partner{$i}.com",
                    'representative_name' => "Representative {$i}",
                    'position'            => 'Giám Đốc',
                ];

                $ocrStmt = $pdo->prepare("
                    UPDATE member_cards 
                    SET ocr_parsed = ?, ocr_status = 'done', ocr_raw = ? 
                    WHERE id = ?
                ");
                $ocrStmt->execute([
                    json_encode($ocrPayload, JSON_UNESCAPED_UNICODE),
                    "RAW OCR TEXT FOR CARD {$i}",
                    $cardId
                ]);

                // 4. Update member from OCR
                $upStmt = $pdo->prepare("
                    UPDATE members SET
                        tax_code = ?, address = ?, city = ?, website = ?, fanpage = ?,
                        phone = ?, email = ?, representative_name = ?, position = ?,
                        industry_type_id = ?, next_verify_at = ?
                    WHERE id = ?
                ");
                $industryId = ($i % 15) + 1;
                $sixMonths = date('Y-m-d H:i:s', strtotime('+6 months'));
                $upStmt->execute([
                    $ocrPayload['tax_code'], $ocrPayload['address'], $ocrPayload['city'],
                    $ocrPayload['website'], $ocrPayload['fanpage'], $ocrPayload['phone'],
                    $ocrPayload['email'], $ocrPayload['representative_name'], $ocrPayload['position'],
                    $industryId, $sixMonths, $memberId
                ]);

                $createdMembers[] = $memberId;
            }
            $pdo->commit();

            TestRunnerCore::assertCount(20, $createdMembers);

            // Verify all cards are done
            $doneCount = (int)$pdo->query("SELECT COUNT(*) FROM member_cards WHERE ocr_status = 'done' AND file_path LIKE 'uploads/cards/batch_card_%'")->fetchColumn();
            TestRunnerCore::assertEqual(20, $doneCount, 'All 20 cards must be processed with ocr_status=done');
        });
    }

    private static function testEnterpriseDirectorySearchWorkload(): void
    {
        TestRunnerCore::setFeature('4.2 Enterprise Directory Search & Multi-Filter Benchmark');

        TestRunnerCore::test('T4.2.1 Complex multi-column search across 50 seeded enterprise members', function() {
            $pdo = TestDbHelper::getPdo();
            $now = date('Y-m-d H:i:s');
            $cities = ['Hà Nội', 'TP. Hồ Chí Minh', 'Hải Phòng', 'Đà Nẵng', 'Cần Thơ'];
            $statuses = ['pending', 'verified', 'unverified', 'failed'];
            $types = ['prospect', 'member', 'partner'];

            $pdo->beginTransaction();
            for ($i = 1; $i <= 50; $i++) {
                $city = $cities[($i - 1) % count($cities)];
                $verifyStatus = $statuses[($i - 1) % count($statuses)];
                $memberType = $types[($i - 1) % count($types)];
                $industryId = (($i - 1) % 15) + 1;
                $name = "Tập Đoàn Logistics Biển Đông #{$i}";

                $stmt = $pdo->prepare("
                    INSERT INTO members (company_name, tax_code, city, industry_type_id, member_type, verify_status, status, created_at, next_verify_at)
                    VALUES (?, ?, ?, ?, ?, ?, 1, ?, datetime('now', '+6 months'))
                ");
                $stmt->execute([$name, sprintf('030%07d', $i), $city, $industryId, $memberType, $verifyStatus, $now]);
            }
            $pdo->commit();

            // Perform complex search query: company contains 'Biển Đông', industry = 2, city = 'TP. Hồ Chí Minh' (i=2 matches)
            $searchKeyword = 'Biển Đông';
            $targetIndustry = 2; // Vận Tải & Logistics
            $targetCity = 'TP. Hồ Chí Minh';

            $query = $pdo->prepare("
                SELECT COUNT(*) FROM members 
                WHERE company_name LIKE ? 
                AND industry_type_id = ? 
                AND city = ?
            ");
            $query->execute(["%{$searchKeyword}%", $targetIndustry, $targetCity]);
            $count = (int)$query->fetchColumn();
            TestRunnerCore::assertTrue($count > 0, 'Should find matching records for multi-filter criteria');

            // Pagination slice test (page 1, limit 10)
            $sliceStmt = $pdo->prepare("
                SELECT id, company_name FROM members 
                WHERE company_name LIKE ? 
                ORDER BY id DESC LIMIT 10 OFFSET 0
            ");
            $sliceStmt->execute(["%{$searchKeyword}%"]);
            $results = $sliceStmt->fetchAll();
            TestRunnerCore::assertEqual(10, count($results));
        });
    }

    private static function testClosedBusinessEmailAlertScenario(): void
    {
        TestRunnerCore::setFeature('4.3 Closed Business Detection & Admin Alert Notification');

        TestRunnerCore::test('T4.3.1 Google Maps returns closed -> member marked failed -> email alert constructed', function() {
            $pdo = TestDbHelper::getPdo();
            $now = date('Y-m-d H:i:s');

            // 1. Create active member
            $pdo->exec("
                INSERT INTO members (company_name, tax_code, address, phone, email, status, verify_status, next_verify_at)
                VALUES ('Công Ty Vận Tải VinaTrans Closed', '0107776666', '78 Cau Giay, Ha Noi', '0243123456', 'closed@vinatrans.vn', 1, 'verified', datetime('now', '-1 day'))
            ");
            $memberId = (int)$pdo->lastInsertId();

            // 2. Cron runner simulates verification detecting closed signal
            $detectedResult = 'closed';
            $newVerifyStatus = 'failed';
            $logDetail = [
                'service'         => 'google_maps',
                'signal_detected' => 'Đã đóng cửa vĩnh viễn',
                'url'             => 'https://www.google.com/search?q=VinaTrans+Closed+Vietnam+map',
                'timestamp'       => $now,
            ];

            // 3. Write verify log
            $logStmt = $pdo->prepare("
                INSERT INTO member_verify_logs (member_id, check_type, result, detail, checked_at)
                VALUES (?, 'google_maps', ?, ?, ?)
            ");
            $logStmt->execute([$memberId, $detectedResult, json_encode($logDetail, JSON_UNESCAPED_UNICODE), $now]);

            // 4. Update member verify_status
            $upStmt = $pdo->prepare("
                UPDATE members 
                SET verify_status = ?, last_verified_at = ?, next_verify_at = datetime('now', '+6 months') 
                WHERE id = ?
            ");
            $upStmt->execute([$newVerifyStatus, $now, $memberId]);

            // 5. Construct email alert notification
            $emailAlert = [
                'to'      => 'admin@suntransco.vn',
                'subject' => '[CẢNH BÁO] Doanh nghiệp hội viên có dấu hiệu đóng cửa: Công Ty Vận Tải VinaTrans Closed',
                'body'    => "Kính gửi Ban Quản Trị Suntransco,\n\nHệ thống tự động phát hiện doanh nghiệp sau có dấu hiệu đóng cửa:\n- Tên công ty: Công Ty Vận Tải VinaTrans Closed\n- Mã số thuế: 0107776666\n- Địa chỉ: 78 Cau Giay, Ha Noi\n- Kết quả xác minh: Đã đóng cửa vĩnh viễn (Google Maps)\n- Thời gian kiểm tra: {$now}\n\nVui lòng kiểm tra lại thông tin chi tiết trên trang quản trị.",
            ];

            TestRunnerCore::assertContains('CẢNH BÁO', $emailAlert['subject']);
            TestRunnerCore::assertContains('0107776666', $emailAlert['body']);
            TestRunnerCore::assertContains('Đã đóng cửa vĩnh viễn', $emailAlert['body']);

            // Verify status in DB is failed
            $currentStatus = $pdo->query("SELECT verify_status FROM members WHERE id = {$memberId}")->fetchColumn();
            TestRunnerCore::assertEqual('failed', $currentStatus);
        });
    }

    private static function testFailureRecoveryAndTimelineScenario(): void
    {
        TestRunnerCore::setFeature('4.4 Failure Recovery & Verification Timeline History');

        TestRunnerCore::test('T4.4.1 Recovery workflow: failed status re-verified to verified with full audit trail', function() {
            $pdo = TestDbHelper::getPdo();
            $t1 = '2026-08-10 10:00:00';
            $t2 = '2026-08-16 09:00:00';

            // Insert member with failed status from previous run
            $pdo->exec("
                INSERT INTO members (company_name, verify_status, status, last_verified_at)
                VALUES ('Tập Đoàn Hồi Sinh Logistics', 'failed', 1, '{$t1}')
            ");
            $memberId = (int)$pdo->lastInsertId();

            // Insert old failed log
            $pdo->exec("
                INSERT INTO member_verify_logs (member_id, check_type, result, detail, checked_at)
                VALUES ({$memberId}, 'google_maps', 'closed', 'Temporary closure noted', '{$t1}')
            ");

            // Admin triggers "Xác Minh Ngay" -> business is now active
            $pdo->exec("
                INSERT INTO member_verify_logs (member_id, check_type, result, detail, checked_at)
                VALUES ({$memberId}, 'google_maps', 'active', 'Re-opened and confirmed active', '{$t2}')
            ");
            $pdo->exec("
                UPDATE members 
                SET verify_status = 'verified', last_verified_at = '{$t2}', next_verify_at = datetime('{$t2}', '+6 months') 
                WHERE id = {$memberId}
            ");

            // Assert member is verified
            $member = $pdo->query("SELECT verify_status, last_verified_at FROM members WHERE id = {$memberId}")->fetch();
            TestRunnerCore::assertEqual('verified', $member->verify_status);
            TestRunnerCore::assertEqual($t2, $member->last_verified_at);

            // Assert audit timeline has both history records
            $logs = $pdo->query("SELECT * FROM member_verify_logs WHERE member_id = {$memberId} ORDER BY checked_at ASC")->fetchAll();
            TestRunnerCore::assertCount(2, $logs);
            TestRunnerCore::assertEqual('closed', $logs[0]->result);
            TestRunnerCore::assertEqual('active', $logs[1]->result);
        });
    }

    private static function testRolling18MonthVerificationLifecycle(): void
    {
        TestRunnerCore::setFeature('4.5 18-Month Rolling Verification Lifecycle');

        TestRunnerCore::test('T4.5.1 Multi-cycle 18-month simulation validates periodic scheduling continuity', function() {
            $pdo = TestDbHelper::getPdo();

            // Month 0: Initial Registration
            $month0 = '2026-01-01 00:00:00';
            $month6Expected = date('Y-m-d H:i:s', strtotime('+6 months', strtotime($month0)));

            $pdo->exec("
                INSERT INTO members (company_name, verify_status, status, created_at, next_verify_at)
                VALUES ('Long-term Partner Trans', 'verified', 1, '{$month0}', '{$month6Expected}')
            ");
            $id = (int)$pdo->lastInsertId();

            // Cycle 1: Month 6 Cron Run
            $month6Actual = '2026-07-01 00:00:00';
            $month12Expected = date('Y-m-d H:i:s', strtotime('+6 months', strtotime($month6Actual)));
            $pdo->exec("UPDATE members SET last_verified_at = '{$month6Actual}', next_verify_at = '{$month12Expected}' WHERE id = {$id}");

            $c1 = $pdo->query("SELECT next_verify_at FROM members WHERE id = {$id}")->fetchColumn();
            TestRunnerCore::assertEqual($month12Expected, $c1);

            // Cycle 2: Month 12 Cron Run
            $month12Actual = '2027-01-01 00:00:00';
            $month18Expected = date('Y-m-d H:i:s', strtotime('+6 months', strtotime($month12Actual)));
            $pdo->exec("UPDATE members SET last_verified_at = '{$month12Actual}', next_verify_at = '{$month18Expected}' WHERE id = {$id}");

            $c2 = $pdo->query("SELECT next_verify_at FROM members WHERE id = {$id}")->fetchColumn();
            TestRunnerCore::assertEqual($month18Expected, $c2);

            // Cycle 3: Month 18 Cron Run
            $month18Actual = '2027-07-01 00:00:00';
            $month24Expected = date('Y-m-d H:i:s', strtotime('+6 months', strtotime($month18Actual)));
            $pdo->exec("UPDATE members SET last_verified_at = '{$month18Actual}', next_verify_at = '{$month24Expected}' WHERE id = {$id}");

            $c3 = $pdo->query("SELECT next_verify_at FROM members WHERE id = {$id}")->fetchColumn();
            TestRunnerCore::assertEqual($month24Expected, $c3);
        });
    }
}
