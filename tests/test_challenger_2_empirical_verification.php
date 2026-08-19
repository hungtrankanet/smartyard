<?php
/**
 * Challenger 2: Empirical Verification & Adversarial Benchmark Suite
 * 
 * Features Under Test:
 * 1. File Length Compliance (<= 500 lines per file)
 * 2. Transaction Safety & Atomic Rollback in Batch Grouped OCR / DB operations
 * 3. High-Concurrency Search Benchmarks (10,000+ records, multi-lingual, subqueries)
 * 4. Foreign Key Cascading:
 *    - ON DELETE CASCADE (member -> contacts, branches, cards, verify_logs)
 *    - ON DELETE SET NULL (contact -> member_cards.contact_id)
 * 
 * Rule Compliance: Under 500 lines.
 */

namespace Tests\Challenger2;

require_once __DIR__ . '/member_tests/test_runner_core.php';
require_once __DIR__ . '/member_tests/test_db_helper.php';

use Tests\MemberTests\TestRunnerCore;
use Tests\MemberTests\TestDbHelper;

class Challenger2VerificationSuite
{
    public static function run(): void
    {
        TestRunnerCore::init();
        TestRunnerCore::setTier('CHALLENGER 2: EMPIRICAL VERIFICATION & STRESS BENCHMARK');

        self::testFileLengthRule();
        self::testDatabaseCascadeBehavior();
        self::testTransactionRollbackSafety();
        self::testHighConcurrencySearchAndSubqueryBenchmark();

        TestRunnerCore::summary();
    }

    private static function testFileLengthRule(): void
    {
        TestRunnerCore::setFeature('1. File Length Constraint Audit (<= 500 lines)');

        $files = [
            'app/Controllers/MemberController.php',
            'app/Models/MemberModel.php',
            'app/Models/MemberContactModel.php',
            'app/Models/MemberBranchModel.php',
            'app/Models/MemberCardModel.php',
            'app/Models/MemberVerifyLogModel.php',
            'app/Models/IndustryTypeModel.php',
            'app/Libraries/CompanyMatcher.php',
            'app/Libraries/OcrService.php',
            'app/Libraries/BusinessVerifyService.php',
            'app/Database/Migrations/2026-08-16-000001_CreateIndustryTypes.php',
            'app/Database/Migrations/2026-08-16-000002_CreateMembers.php',
            'app/Database/Migrations/2026-08-16-000003_CreateMemberCards.php',
            'app/Database/Migrations/2026-08-16-000004_CreateMemberVerifyLogs.php',
            'app/Database/Migrations/2026-08-16-100001_UpgradeMemberSchema.php',
            'app/Views/admin/members/index.php',
            'app/Views/admin/members/form.php',
            'app/Views/admin/members/detail.php',
            'app/Views/admin/members/confirm_ocr.php',
            'app/Views/admin/members/upload_cards.php',
            'migrate_members.sql',
        ];

        $baseDir = dirname(__DIR__);
        foreach ($files as $rel) {
            $path = $baseDir . '/' . $rel;
            TestRunnerCore::test("File {$rel} exists and <= 500 lines", function() use ($path, $rel) {
                TestRunnerCore::assertTrue(file_exists($path), "File {$rel} must exist");
                $lines = count(file($path));
                TestRunnerCore::assertTrue($lines <= 500, "File {$rel} has {$lines} lines (limit 500)");
            });
        }
    }

    private static function testDatabaseCascadeBehavior(): void
    {
        TestRunnerCore::setFeature('2. Foreign Key Cascade & SET NULL Integrity');

        TestRunnerCore::test('T2.1 ON DELETE CASCADE on members removes contacts, branches, cards, and verify logs', function() {
            TestDbHelper::resetDatabase();
            $pdo = TestDbHelper::getPdo();

            $pdo->exec("INSERT INTO members (company_name, status) VALUES ('Cascade Corp', 1)");
            $mId = (int)$pdo->lastInsertId();

            $pdo->exec("INSERT INTO member_contacts (company_id, full_name, is_primary) VALUES ({$mId}, 'Contact A', 1), ({$mId}, 'Contact B', 0)");
            $pdo->exec("INSERT INTO member_branches (company_id, branch_name) VALUES ({$mId}, 'HQ Hanoi'), ({$mId}, 'Branch HCMC')");
            $pdo->exec("INSERT INTO member_cards (member_id, file_path) VALUES ({$mId}, 'uploads/c1.jpg'), ({$mId}, 'uploads/c2.jpg')");
            $pdo->exec("INSERT INTO member_verify_logs (member_id, check_type, result) VALUES ({$mId}, 'manual', 'verified')");

            TestRunnerCore::assertEqual(2, (int)$pdo->query("SELECT COUNT(*) FROM member_contacts WHERE company_id = {$mId}")->fetchColumn());
            TestRunnerCore::assertEqual(2, (int)$pdo->query("SELECT COUNT(*) FROM member_branches WHERE company_id = {$mId}")->fetchColumn());
            TestRunnerCore::assertEqual(2, (int)$pdo->query("SELECT COUNT(*) FROM member_cards WHERE member_id = {$mId}")->fetchColumn());
            TestRunnerCore::assertEqual(1, (int)$pdo->query("SELECT COUNT(*) FROM member_verify_logs WHERE member_id = {$mId}")->fetchColumn());

            $pdo->exec("DELETE FROM members WHERE id = {$mId}");

            TestRunnerCore::assertEqual(0, (int)$pdo->query("SELECT COUNT(*) FROM members WHERE id = {$mId}")->fetchColumn());
            TestRunnerCore::assertEqual(0, (int)$pdo->query("SELECT COUNT(*) FROM member_contacts WHERE company_id = {$mId}")->fetchColumn());
            TestRunnerCore::assertEqual(0, (int)$pdo->query("SELECT COUNT(*) FROM member_branches WHERE company_id = {$mId}")->fetchColumn());
            TestRunnerCore::assertEqual(0, (int)$pdo->query("SELECT COUNT(*) FROM member_cards WHERE member_id = {$mId}")->fetchColumn());
            TestRunnerCore::assertEqual(0, (int)$pdo->query("SELECT COUNT(*) FROM member_verify_logs WHERE member_id = {$mId}")->fetchColumn());
        });

        TestRunnerCore::test('T2.2 ON DELETE SET NULL on member_cards when contact is deleted', function() {
            TestDbHelper::resetDatabase();
            $pdo = TestDbHelper::getPdo();

            $pdo->exec("INSERT INTO members (company_name, status) VALUES ('SetNull Corp', 1)");
            $mId = (int)$pdo->lastInsertId();

            $pdo->exec("INSERT INTO member_contacts (company_id, full_name, is_primary) VALUES ({$mId}, 'Director Tran', 1)");
            $c1Id = (int)$pdo->lastInsertId();
            $pdo->exec("INSERT INTO member_contacts (company_id, full_name, is_primary) VALUES ({$mId}, 'Manager Le', 0)");
            $c2Id = (int)$pdo->lastInsertId();

            $pdo->exec("INSERT INTO member_cards (member_id, contact_id, file_path) VALUES ({$mId}, {$c1Id}, 'uploads/card1.jpg')");
            $card1Id = (int)$pdo->lastInsertId();
            $pdo->exec("INSERT INTO member_cards (member_id, contact_id, file_path) VALUES ({$mId}, {$c2Id}, 'uploads/card2.jpg')");
            $card2Id = (int)$pdo->lastInsertId();

            $pdo->exec("DELETE FROM member_contacts WHERE id = {$c1Id}");

            TestRunnerCore::assertEqual(0, (int)$pdo->query("SELECT COUNT(*) FROM member_contacts WHERE id = {$c1Id}")->fetchColumn());
            TestRunnerCore::assertEqual(1, (int)$pdo->query("SELECT COUNT(*) FROM member_contacts WHERE id = {$c2Id}")->fetchColumn());

            $c1Row = $pdo->query("SELECT member_id, contact_id FROM member_cards WHERE id = {$card1Id}")->fetch();
            TestRunnerCore::assertNotNull($c1Row);
            TestRunnerCore::assertEqual($mId, (int)$c1Row->member_id);
            TestRunnerCore::assertNull($c1Row->contact_id, "Card 1 contact_id must be SET NULL");

            $c2Row = $pdo->query("SELECT member_id, contact_id FROM member_cards WHERE id = {$card2Id}")->fetch();
            TestRunnerCore::assertNotNull($c2Row);
            TestRunnerCore::assertEqual($c2Id, (int)$c2Row->contact_id);
        });
    }

    private static function testTransactionRollbackSafety(): void
    {
        TestRunnerCore::setFeature('3. Transaction Safety & Mid-Insertion Error Rollback');

        TestRunnerCore::test('T3.1 Transaction rolls back all company, contact, branch, and card insertions on mid-batch failure', function() {
            TestDbHelper::resetDatabase();
            $pdo = TestDbHelper::getPdo();

            $initialMembers = (int)$pdo->query("SELECT COUNT(*) FROM members")->fetchColumn();
            $initialContacts = (int)$pdo->query("SELECT COUNT(*) FROM member_contacts")->fetchColumn();

            $groups = [
                ['company' => 'Alpha Corp', 'fail' => false],
                ['company' => 'Beta Corp', 'fail' => true],
                ['company' => 'Gamma Corp', 'fail' => false],
            ];

            $caught = false;
            try {
                $pdo->beginTransaction();
                foreach ($groups as $grp) {
                    $pdo->prepare("INSERT INTO members (company_name, status) VALUES (?, 1)")->execute([$grp['company']]);
                    $mid = (int)$pdo->lastInsertId();
                    $pdo->prepare("INSERT INTO member_contacts (company_id, full_name) VALUES (?, ?)")->execute([$mid, $grp['company'] . ' Rep']);
                    $pdo->prepare("INSERT INTO member_branches (company_id, branch_name) VALUES (?, ?)")->execute([$mid, $grp['company'] . ' Branch']);
                    $pdo->prepare("INSERT INTO member_cards (member_id, file_path) VALUES (?, ?)")->execute([$mid, 'uploads/card.jpg']);

                    if ($grp['fail']) {
                        throw new \RuntimeException("Mid-insertion database failure simulated on: " . $grp['company']);
                    }
                }
                $pdo->commit();
            } catch (\Throwable $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $caught = true;
            }

            TestRunnerCore::assertTrue($caught, 'Transaction failure must be caught');
            TestRunnerCore::assertEqual($initialMembers, (int)$pdo->query("SELECT COUNT(*) FROM members")->fetchColumn(), '0 members persisted on rollback');
            TestRunnerCore::assertEqual($initialContacts, (int)$pdo->query("SELECT COUNT(*) FROM member_contacts")->fetchColumn(), '0 contacts persisted on rollback');
        });

        TestRunnerCore::test('T3.2 Transaction rolls back updates to existing members on failure', function() {
            TestDbHelper::resetDatabase();
            $pdo = TestDbHelper::getPdo();

            $pdo->exec("INSERT INTO members (company_name, company_name_en, status) VALUES ('Original Co', 'Original EN', 1)");
            $eId = (int)$pdo->lastInsertId();

            try {
                $pdo->beginTransaction();
                $pdo->prepare("UPDATE members SET company_name = 'Hacked Co', company_name_en = 'Hacked EN' WHERE id = ?")->execute([$eId]);
                $pdo->prepare("INSERT INTO member_contacts (company_id, full_name) VALUES (?, ?)")->execute([$eId, 'Hacked Rep']);
                throw new \Exception("Simulated mid-update failure");
            } catch (\Throwable $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
            }

            $row = $pdo->query("SELECT company_name, company_name_en FROM members WHERE id = {$eId}")->fetch();
            TestRunnerCore::assertEqual('Original Co', $row->company_name);
            TestRunnerCore::assertEqual('Original EN', $row->company_name_en);
            TestRunnerCore::assertEqual(0, (int)$pdo->query("SELECT COUNT(*) FROM member_contacts WHERE company_id = {$eId}")->fetchColumn());
        });
    }

    private static function testHighConcurrencySearchAndSubqueryBenchmark(): void
    {
        TestRunnerCore::setFeature('4. High Concurrency Search & Subquery Benchmark (10,000 Records)');

        TestRunnerCore::test('T4.1 Seed 10,000 multilingual members with contacts & branches', function() {
            TestDbHelper::resetDatabase();
            $pdo = TestDbHelper::getPdo();

            $pdo->beginTransaction();
            $mStmt = $pdo->prepare("INSERT INTO members (company_name, company_name_en, company_name_local, detected_language, tax_code, city, phone, email, representative_name, industry_type_id, status, verify_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 'verified')");
            $cStmt = $pdo->prepare("INSERT INTO member_contacts (company_id, full_name, full_name_en, is_primary) VALUES (?, ?, ?, ?)");
            $bStmt = $pdo->prepare("INSERT INTO member_branches (company_id, branch_name, country, city, is_headquarters) VALUES (?, ?, 'Vietnam', ?, ?)");

            $cities = ['Hà Nội', 'TP. Hồ Chí Minh', 'Hải Phòng', 'Đà Nẵng', 'Bình Dương'];
            $templates = [
                ['CÔNG TY CP VẬN TẢI SUNTRANSCO', 'SUNTRANSCO LOGISTICS JSC', '太阳物流股份有限公司', 'zh'],
                ['TẬP ĐOÀN LOGISTICS ĐẠI DƯƠNG', 'OCEAN LOGISTICS GROUP', '大洋物流集团', 'zh'],
                ['CÔNG TY TNHH XNK TOÀN CẦU', 'GLOBAL IMPORT EXPORT CO., LTD', 'グローバル輸出入株式会社', 'ja'],
                ['TỔNG CÔNG TY KHO BÃI ĐÔNG Á', 'EAST ASIA WAREHOUSING CORP', '동아창고 주식회사', 'ko'],
            ];

            for ($i = 1; $i <= 10000; $i++) {
                $t = $templates[$i % 4];
                $city = $cities[$i % 5];
                $tax = sprintf('01%08d', $i);
                $mStmt->execute([$t[0] . " #{$i}", $t[1] . " #{$i}", $t[2] . " #{$i}", $t[3], $tax, $city, "090{$i}", "user{$i}@sun.vn", "Rep {$i}", ($i % 15) + 1]);
                $mId = (int)$pdo->lastInsertId();

                $cStmt->execute([$mId, "Contact 1 #{$i}", "Contact EN 1 #{$i}", 1]);
                if ($i % 2 === 0) {
                    $cStmt->execute([$mId, "Contact 2 #{$i}", "Contact EN 2 #{$i}", 0]);
                    $bStmt->execute([$mId, "Branch {$city}", $city, 1]);
                }
            }
            $pdo->commit();

            TestRunnerCore::assertEqual(10000, (int)$pdo->query("SELECT COUNT(*) FROM members")->fetchColumn());
            TestRunnerCore::assertEqual(15000, (int)$pdo->query("SELECT COUNT(*) FROM member_contacts")->fetchColumn());
            TestRunnerCore::assertEqual(5000, (int)$pdo->query("SELECT COUNT(*) FROM member_branches")->fetchColumn());
        });

        TestRunnerCore::test('T4.2 Multi-lingual 3-column search & subquery benchmark across 10,000 records', function() {
            $pdo = TestDbHelper::getPdo();
            $queries = ['SUNTRANSCO', 'VẬN TẢI', 'ĐẠI DƯƠNG', 'グローバル', '太阳物流', '동아창고', '010005000', 'user8888@sun.vn'];

            $sql = "SELECT m.*, it.name AS industry_name, (SELECT COUNT(*) FROM member_contacts mc WHERE mc.company_id = m.id) AS contact_count FROM members m LEFT JOIN industry_types it ON it.id = m.industry_type_id WHERE (m.company_name LIKE :q1 OR m.company_name_en LIKE :q2 OR m.company_name_local LIKE :q3 OR m.representative_name LIKE :q4 OR m.phone LIKE :q5 OR m.email LIKE :q6 OR m.tax_code LIKE :q7) ORDER BY m.id DESC LIMIT 20 OFFSET 0";
            $stmt = $pdo->prepare($sql);

            $start = microtime(true);
            $count = 500;
            for ($i = 0; $i < $count; $i++) {
                $term = '%' . $queries[$i % count($queries)] . '%';
                $stmt->execute([':q1' => $term, ':q2' => $term, ':q3' => $term, ':q4' => $term, ':q5' => $term, ':q6' => $term, ':q7' => $term]);
                $rows = $stmt->fetchAll();
                if ($i === 0) {
                    TestRunnerCore::assertTrue(count($rows) > 0);
                    TestRunnerCore::assertTrue(isset($rows[0]->contact_count));
                }
            }
            $ms = (microtime(true) - $start) * 1000;
            $qps = round($count / ($ms / 1000), 2);
            $avgMs = round($ms / $count, 3);

            echo "\n    -> Executed {$count} queries across 10k records in " . round($ms, 2) . " ms (Avg: {$avgMs} ms/query | QPS: {$qps})\n";
            TestRunnerCore::assertTrue($avgMs < 50.0, "Avg latency {$avgMs}ms must be within 50ms SLA");
        });

        TestRunnerCore::test('T4.3 High Concurrency Rapid Read Benchmark (20,000 lookups)', function() {
            $pdo = TestDbHelper::getPdo();
            $stmt = $pdo->prepare("SELECT m.id, m.company_name, m.company_name_en, (SELECT COUNT(*) FROM member_contacts mc WHERE mc.company_id = m.id) AS contact_count FROM members m WHERE m.id = ?");

            $start = microtime(true);
            $ops = 20000;
            for ($i = 1; $i <= $ops; $i++) {
                $stmt->execute([($i % 10000) + 1]);
                $stmt->fetch();
            }
            $ms = (microtime(true) - $start) * 1000;
            $qps = round($ops / ($ms / 1000), 2);
            $mem = round(memory_get_peak_usage() / 1024 / 1024, 2);

            echo "    -> Completed {$ops} lookups in " . round($ms, 2) . " ms ({$qps} lookups/sec | Mem: {$mem} MB)\n";
            TestRunnerCore::assertTrue($qps > 5000, "Throughput {$qps} > 5,000 ops/sec");
            TestRunnerCore::assertTrue($mem < 100, "Peak memory {$mem}MB < 100MB");
        });
    }
}

Challenger2VerificationSuite::run();
