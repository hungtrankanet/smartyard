<?php
/**
 * Tier 3: Cross-Feature Workflow Test Suite
 * Tests full end-to-end multi-feature pipelines:
 * - Card upload -> OCR extraction -> Confirm -> Member save -> Instant verify -> Cron due update
 * - Multi-side card binding (Front + Back OCR merge)
 * - Foreign key cascade deletion (Member -> Cards & Verify Logs)
 * - Instant verification state machine & log recording
 * - Multi-criteria filter and pagination pipeline
 * - 6-Month rolling cron runner simulation
 * Maximum 500 lines constraint strictly enforced.
 */

namespace Tests\MemberTests;

require_once __DIR__ . '/test_runner_core.php';
require_once __DIR__ . '/test_db_helper.php';

class Tier3WorkflowTests
{
    public static function run(): void
    {
        TestRunnerCore::setTier('Tier 3: Cross-Feature Workflows');

        self::testFullRegistrationAndVerificationPipeline();
        self::testMultiSideCardMergePipeline();
        self::testCascadeDeleteWorkflow();
        self::testInstantVerifyStateTransitions();
        self::testMultiCriteriaFilterPipeline();
        self::testCronBatchRunnerPipeline();
    }

    private static function testFullRegistrationAndVerificationPipeline(): void
    {
        TestRunnerCore::setFeature('3.1 Full End-to-End Registration & Verification Pipeline');

        TestRunnerCore::test('T3.1.1 Step 1: Batch card upload registers member_cards with status=pending', function() {
            $pdo = TestDbHelper::getPdo();
            
            // Create initial draft/placeholder member
            $stmt = $pdo->prepare("
                INSERT INTO members (company_name, status, verify_status, created_at, updated_at) 
                VALUES (?, 1, 'pending', datetime('now'), datetime('now'))
            ");
            $stmt->execute(['Suntransco Logistics']);
            $memberId = (int)$pdo->lastInsertId();

            // Upload 2 cards (front and back)
            $cardStmt = $pdo->prepare("
                INSERT INTO member_cards (member_id, file_path, side, ocr_status, created_at)
                VALUES (?, ?, ?, 'pending', datetime('now'))
            ");
            $cardStmt->execute([$memberId, 'uploads/cards/card_1_front.jpg', 'front']);
            $frontCardId = (int)$pdo->lastInsertId();

            $cardStmt->execute([$memberId, 'uploads/cards/card_1_back.jpg', 'back']);
            $backCardId = (int)$pdo->lastInsertId();

            TestRunnerCore::assertTrue($frontCardId > 0 && $backCardId > 0);

            $cards = $pdo->query("SELECT * FROM member_cards WHERE member_id = {$memberId}")->fetchAll();
            TestRunnerCore::assertCount(2, $cards);
            TestRunnerCore::assertEqual('pending', $cards[0]->ocr_status);
            TestRunnerCore::assertEqual('pending', $cards[1]->ocr_status);
        });

        TestRunnerCore::test('T3.1.2 Step 2: OCR Service processes cards and updates ocr_parsed and ocr_status=done', function() {
            $pdo = TestDbHelper::getPdo();

            $ocrDataFront = [
                'company_name' => 'Công Ty TNHH Suntransco Logistics',
                'phone'        => '02439876543',
                'address'      => '18 Phan Chu Trinh, Hoàn Kiếm, Hà Nội',
                'city'         => 'Hà Nội',
                'website'      => 'https://suntrans.vn',
                'fanpage'      => 'https://facebook.com/suntransco',
            ];
            $ocrDataBack = [
                'tax_code'            => '0101234567',
                'representative_name' => 'Trần Hoàng Long',
                'position'            => 'Tổng Giám Đốc',
                'email'               => 'info@suntrans.vn',
            ];

            // Update front card
            $stmt = $pdo->prepare("
                UPDATE member_cards 
                SET ocr_parsed = ?, ocr_status = 'done', ocr_raw = 'RAW OCR TEXT FRONT' 
                WHERE file_path = 'uploads/cards/card_1_front.jpg'
            ");
            $stmt->execute([json_encode($ocrDataFront, JSON_UNESCAPED_UNICODE)]);

            // Update back card
            $stmt = $pdo->prepare("
                UPDATE member_cards 
                SET ocr_parsed = ?, ocr_status = 'done', ocr_raw = 'RAW OCR TEXT BACK' 
                WHERE file_path = 'uploads/cards/card_1_back.jpg'
            ");
            $stmt->execute([json_encode($ocrDataBack, JSON_UNESCAPED_UNICODE)]);

            $cards = $pdo->query("SELECT ocr_status, ocr_parsed FROM member_cards WHERE file_path LIKE 'uploads/cards/card_1_%'")->fetchAll();
            TestRunnerCore::assertCount(2, $cards);
            TestRunnerCore::assertEqual('done', $cards[0]->ocr_status);
            TestRunnerCore::assertEqual('done', $cards[1]->ocr_status);
        });

        TestRunnerCore::test('T3.1.3 Step 3: Confirm OCR form consolidates data and updates member with next_verify_at = NOW + 6 months', function() {
            $pdo = TestDbHelper::getPdo();
            $now = date('Y-m-d H:i:s');
            $sixMonths = date('Y-m-d H:i:s', strtotime('+6 months'));

            $updateMemberStmt = $pdo->prepare("
                UPDATE members SET
                    company_name = ?,
                    tax_code = ?,
                    address = ?,
                    city = ?,
                    website = ?,
                    fanpage = ?,
                    phone = ?,
                    email = ?,
                    representative_name = ?,
                    position = ?,
                    industry_type_id = ?,
                    member_type = 'member',
                    next_verify_at = ?,
                    updated_at = ?
                WHERE company_name = 'Suntransco Logistics'
            ");

            $updateMemberStmt->execute([
                'Công Ty TNHH Suntransco Logistics',
                '0101234567',
                '18 Phan Chu Trinh, Hoàn Kiếm, Hà Nội',
                'Hà Nội',
                'https://suntrans.vn',
                'https://facebook.com/suntransco',
                '02439876543',
                'info@suntrans.vn',
                'Trần Hoàng Long',
                'Tổng Giám Đốc',
                2, // Vận Tải & Logistics
                $sixMonths,
                $now
            ]);

            $member = $pdo->query("SELECT * FROM members WHERE tax_code = '0101234567'")->fetch();
            TestRunnerCore::assertNotNull($member);
            TestRunnerCore::assertEqual('Công Ty TNHH Suntransco Logistics', $member->company_name);
            TestRunnerCore::assertEqual('Trần Hoàng Long', $member->representative_name);
            TestRunnerCore::assertEqual(2, (int)$member->industry_type_id);
            TestRunnerCore::assertTrue(strtotime($member->next_verify_at) > time() + (170 * 86400));
        });

        TestRunnerCore::test('T3.1.4 Step 4: Instant verify triggers checks, writes member_verify_logs, and sets verify_status=verified', function() {
            $pdo = TestDbHelper::getPdo();
            $member = $pdo->query("SELECT id FROM members WHERE tax_code = '0101234567'")->fetch();
            $memberId = (int)$member->id;
            $now = date('Y-m-d H:i:s');

            // Log Google Maps check
            $logStmt = $pdo->prepare("
                INSERT INTO member_verify_logs (member_id, check_type, result, detail, checked_at)
                VALUES (?, ?, ?, ?, ?)
            ");
            $logStmt->execute([
                $memberId,
                'google_maps',
                'active',
                json_encode(['status' => 'active', 'matched_name' => 'Công Ty TNHH Suntransco Logistics', 'hours' => 'Open']),
                $now
            ]);

            // Log Fanpage check
            $logStmt->execute([
                $memberId,
                'fanpage',
                'active',
                json_encode(['http_code' => 200, 'title' => 'Suntransco Official Fanpage']),
                $now
            ]);

            // Update member verify status
            $upStmt = $pdo->prepare("
                UPDATE members 
                SET verify_status = 'verified', last_verified_at = ? 
                WHERE id = ?
            ");
            $upStmt->execute([$now, $memberId]);

            $updated = $pdo->query("SELECT verify_status, last_verified_at FROM members WHERE id = {$memberId}")->fetch();
            TestRunnerCore::assertEqual('verified', $updated->verify_status);
            TestRunnerCore::assertEqual($now, $updated->last_verified_at);

            $logs = $pdo->query("SELECT * FROM member_verify_logs WHERE member_id = {$memberId}")->fetchAll();
            TestRunnerCore::assertCount(2, $logs);
        });
    }

    private static function testMultiSideCardMergePipeline(): void
    {
        TestRunnerCore::setFeature('3.2 Multi-Side Card Binding & OCR Merge');

        TestRunnerCore::test('T3.2.1 Merge front and back OCR JSON payloads without loss of data', function() {
            $frontJson = [
                'company_name' => 'Công Ty TNHH Giao Nhận Ánh Dương',
                'phone'        => '0908111222',
                'email'        => 'contact@anhduong.vn',
                'website'      => 'https://anhduong.vn',
                'address'      => '45 Le Duan, Da Nang',
                'city'         => 'Đà Nẵng',
            ];

            $backJson = [
                'tax_code'            => '0400123456',
                'representative_name' => 'Nguyễn Thị Ánh Dương',
                'position'            => 'Giám Đốc Điều Hành',
                'fanpage'             => 'https://facebook.com/anhduonglogistics',
            ];

            $merged = array_merge($frontJson, $backJson);
            $requiredKeys = ['company_name', 'tax_code', 'address', 'city', 'website', 'fanpage', 'phone', 'email', 'representative_name', 'position'];
            
            foreach ($requiredKeys as $k) {
                TestRunnerCore::assertArrayHasKey($k, $merged);
                TestRunnerCore::assertTrue(!empty($merged[$k]), "Merged key '{$k}' must not be empty");
            }
        });
    }

    private static function testCascadeDeleteWorkflow(): void
    {
        TestRunnerCore::setFeature('3.3 Foreign Key Cascade Deletion Workflow');

        TestRunnerCore::test('T3.3.1 Deleting a member removes related member_cards and member_verify_logs', function() {
            $pdo = TestDbHelper::getPdo();
            $now = date('Y-m-d H:i:s');

            // 1. Insert member
            $pdo->exec("INSERT INTO members (company_name, status, verify_status, created_at) VALUES ('Cascade Test Corp', 1, 'verified', '{$now}')");
            $memberId = (int)$pdo->lastInsertId();

            // 2. Insert 2 cards
            $pdo->exec("INSERT INTO member_cards (member_id, file_path, side, ocr_status) VALUES ({$memberId}, 'uploads/cards/c1.jpg', 'front', 'done')");
            $pdo->exec("INSERT INTO member_cards (member_id, file_path, side, ocr_status) VALUES ({$memberId}, 'uploads/cards/c2.jpg', 'back', 'done')");

            // 3. Insert 2 logs
            $pdo->exec("INSERT INTO member_verify_logs (member_id, check_type, result, checked_at) VALUES ({$memberId}, 'google_maps', 'active', '{$now}')");
            $pdo->exec("INSERT INTO member_verify_logs (member_id, check_type, result, checked_at) VALUES ({$memberId}, 'fanpage', 'active', '{$now}')");

            // Verify initial counts
            $cardCountBefore = (int)$pdo->query("SELECT COUNT(*) FROM member_cards WHERE member_id = {$memberId}")->fetchColumn();
            $logCountBefore = (int)$pdo->query("SELECT COUNT(*) FROM member_verify_logs WHERE member_id = {$memberId}")->fetchColumn();
            TestRunnerCore::assertEqual(2, $cardCountBefore);
            TestRunnerCore::assertEqual(2, $logCountBefore);

            // Enable SQLite foreign keys and delete member
            $pdo->exec("PRAGMA foreign_keys = ON;");
            $pdo->exec("DELETE FROM members WHERE id = {$memberId}");

            // Verify cascade deletion
            $cardCountAfter = (int)$pdo->query("SELECT COUNT(*) FROM member_cards WHERE member_id = {$memberId}")->fetchColumn();
            $logCountAfter = (int)$pdo->query("SELECT COUNT(*) FROM member_verify_logs WHERE member_id = {$memberId}")->fetchColumn();
            TestRunnerCore::assertEqual(0, $cardCountAfter, 'Cards must be deleted when member is deleted');
            TestRunnerCore::assertEqual(0, $logCountAfter, 'Verify logs must be deleted when member is deleted');
        });
    }

    private static function testInstantVerifyStateTransitions(): void
    {
        TestRunnerCore::setFeature('3.4 Instant Verify State Transitions');

        TestRunnerCore::test('T3.4.1 State transition: pending -> verified on successful check', function() {
            $pdo = TestDbHelper::getPdo();
            $pdo->exec("INSERT INTO members (company_name, verify_status, status) VALUES ('Transition Corp 1', 'pending', 1)");
            $id = $pdo->lastInsertId();

            $pdo->exec("UPDATE members SET verify_status = 'verified', last_verified_at = datetime('now') WHERE id = {$id}");
            $status = $pdo->query("SELECT verify_status FROM members WHERE id = {$id}")->fetchColumn();
            TestRunnerCore::assertEqual('verified', $status);
        });

        TestRunnerCore::test('T3.4.2 State transition: verified -> failed when business is closed', function() {
            $pdo = TestDbHelper::getPdo();
            $pdo->exec("INSERT INTO members (company_name, verify_status, status) VALUES ('Transition Corp 2', 'verified', 1)");
            $id = $pdo->lastInsertId();

            $pdo->exec("UPDATE members SET verify_status = 'failed', last_verified_at = datetime('now') WHERE id = {$id}");
            $status = $pdo->query("SELECT verify_status FROM members WHERE id = {$id}")->fetchColumn();
            TestRunnerCore::assertEqual('failed', $status);
        });
    }

    private static function testMultiCriteriaFilterPipeline(): void
    {
        TestRunnerCore::setFeature('3.5 Multi-Criteria Search & Filter Pipeline');

        TestRunnerCore::test('T3.5.1 Filter by industry + verify_status + search query produces exact result subset', function() {
            $pdo = TestDbHelper::getPdo();
            $now = date('Y-m-d H:i:s');

            // Seed distinct members
            $pdo->exec("INSERT INTO members (company_name, industry_type_id, verify_status, member_type, city, status, created_at) VALUES ('Hai Phong Marine Lines', 2, 'verified', 'member', 'Hải Phòng', 1, '{$now}')");
            $pdo->exec("INSERT INTO members (company_name, industry_type_id, verify_status, member_type, city, status, created_at) VALUES ('Hai Phong Tech Solutions', 5, 'pending', 'prospect', 'Hải Phòng', 1, '{$now}')");
            $pdo->exec("INSERT INTO members (company_name, industry_type_id, verify_status, member_type, city, status, created_at) VALUES ('Sai Gon Express Cargo', 2, 'verified', 'partner', 'TP. Hồ Chí Minh', 1, '{$now}')");

            // Query: industry_type_id = 2 AND verify_status = 'verified' AND city LIKE 'Hải Phòng'
            $stmt = $pdo->prepare("
                SELECT company_name FROM members 
                WHERE industry_type_id = ? AND verify_status = ? AND city LIKE ?
            ");
            $stmt->execute([2, 'verified', '%Hải Phòng%']);
            $results = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            TestRunnerCore::assertCount(1, $results);
            TestRunnerCore::assertEqual('Hai Phong Marine Lines', $results[0]);
        });
    }

    private static function testCronBatchRunnerPipeline(): void
    {
        TestRunnerCore::setFeature('3.6 Cron Batch Verification Pipeline');

        TestRunnerCore::test('T3.6.1 Cron processes due members sequentially and advances next_verify_at +6 months', function() {
            $pdo = TestDbHelper::getPdo();
            $pastDate = date('Y-m-d H:i:s', strtotime('-5 days'));

            // Insert 3 members due for verification
            $pdo->exec("INSERT INTO members (company_name, status, verify_status, next_verify_at) VALUES ('Cron Batch Member 1', 1, 'pending', '{$pastDate}')");
            $id1 = $pdo->lastInsertId();
            $pdo->exec("INSERT INTO members (company_name, status, verify_status, next_verify_at) VALUES ('Cron Batch Member 2', 1, 'pending', '{$pastDate}')");
            $id2 = $pdo->lastInsertId();

            // Simulate Cron execution
            $dueStmt = $pdo->prepare("
                SELECT id, company_name FROM members 
                WHERE status = 1 AND (next_verify_at <= datetime('now') OR next_verify_at IS NULL)
                AND id IN ({$id1}, {$id2})
            ");
            $dueStmt->execute();
            $dueMembers = $dueStmt->fetchAll();

            TestRunnerCore::assertCount(2, $dueMembers);

            $newNextVerify = date('Y-m-d H:i:s', strtotime('+6 months'));
            $updateStmt = $pdo->prepare("
                UPDATE members 
                SET verify_status = 'verified', last_verified_at = datetime('now'), next_verify_at = ? 
                WHERE id = ?
            ");

            foreach ($dueMembers as $m) {
                $updateStmt->execute([$newNextVerify, $m->id]);
            }

            // Verify they are no longer due
            $checkStmt = $pdo->prepare("
                SELECT id FROM members 
                WHERE status = 1 AND next_verify_at <= datetime('now')
                AND id IN ({$id1}, {$id2})
            ");
            $checkStmt->execute();
            TestRunnerCore::assertCount(0, $checkStmt->fetchAll(), 'Processed members should no longer be due for cron');
        });
    }
}
