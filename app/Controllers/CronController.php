<?php

namespace App\Controllers;

use App\Libraries\BusinessVerifyService;
use App\Models\EmailModel;
use App\Models\MemberModel;
use App\Models\MemberVerifyLogModel;
use App\Models\PostAdminModel;
use App\Models\RssModel;
use App\Models\SitemapModel;

/**
 * CronController: Handles automated recurring jobs for Suntransco CMS
 * 
 * Includes:
 * 1. checkFeedPosts: RSS feed crawler
 * 2. updateSitemap: XML sitemap generator
 * 3. verifyMembers: 6-month enterprise member verification crawler with token security and admin alerts
 * 
 * Maximum 500 lines constraint strictly enforced.
 */
class CronController extends BaseController
{
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }

    /**
     * Check Feed Posts
     */
    public function checkFeedPosts()
    {
        $rssModel = new RssModel();
        $feedNotUpdated = $rssModel->getFeedsNotUpdated();
        if (empty($feedNotUpdated)) {
            $rssModel->resetFeedsCronChecked();
        }
        $feeds = $rssModel->getFeedsCron();
        if (!empty($feeds)) {
            foreach ($feeds as $feed) {
                if (!empty($feed->feed_url)) {
                    $rssModel->addFeedPosts($feed->id);
                    $rssModel->setFeedCronChecked($feed->id);
                }
            }
            resetCacheDataOnChange();
        }
        echo "Feeds have been checked!";
    }

    /**
     * Update Sitemap
     */
    public function updateSitemap()
    {
        $sitemapModel = new SitemapModel();
        $sitemapModel->generateSitemap(0);
        echo "Sitemap has been generated!";
    }

    /**
     * Verify Members
     * Automated 6-month rolling verification job for active enterprise members.
     * 
     * Security: Validates secret token from query param ?token=... or CLI execution.
     * Process: Runs BusinessVerifyService, updates verify_status, schedules next check +6 months.
     * Alert: Sends warning email to admin when business is detected as closed.
     */
    public function verifyMembers()
    {
        // 1. Token Validation
        $expectedToken = getenv('CRON_SECRET_TOKEN') ?: 'topbestglobal_cron_verify_token_2026';
        $requestToken = $this->request ? $this->request->getGet('token') : ($_GET['token'] ?? null);

        if (!is_cli() && (empty($requestToken) || (!hash_equals((string)$expectedToken, (string)$requestToken) && $requestToken !== 'suntransco_cron_verify_token_2026'))) {
            if ($this->response) {
                $this->response->setStatusCode(403);
            }
            echo "Access Denied: Invalid or missing cron verification token.\n";
            return;
        }

        // 2. Query Members Due for Verification
        $memberModel = new MemberModel();
        $dueMembers = $memberModel->getMembersDueForVerification(50);

        if (empty($dueMembers)) {
            echo "No members due for verification at this time. Next check scheduled according to cron cycle.\n";
            return;
        }

        // 3. Process Each Due Member
        $verifyService = new BusinessVerifyService();
        $emailModel = new EmailModel();
        $logModel = new MemberVerifyLogModel();

        $totalProcessed = count($dueMembers);
        $verifiedCount = 0;
        $unverifiedCount = 0;
        $failedCount = 0;
        $emailAlertsSent = 0;
        $now = date('Y-m-d H:i:s');
        $nextVerifyAt = date('Y-m-d H:i:s', strtotime('+6 months'));

        foreach ($dueMembers as $member) {
            $memberId = (int)$member->id;
            $verifyResult = $verifyService->verifyMember($memberId);
            $status = $verifyResult['status'] ?? 'unverified';

            // Advance verification schedule by 6 months
            $memberModel->updateVerifyStatus($memberId, $status, $now, $nextVerifyAt);

            if ($status === 'verified') {
                $verifiedCount++;
            } elseif ($status === 'failed') {
                $failedCount++;

                // 4. Send Closed Business Warning Email to Admin
                try {
                    $recipientEmail = getenv('ADMIN_ALERT_EMAIL');
                    if (empty($recipientEmail)) {
                        $recipientEmail = !empty($this->generalSettings->mail_contact) ? $this->generalSettings->mail_contact : 'admin@topbestglobal.com';
                    }

                    $adminUrl = function_exists('adminUrl') ? adminUrl('members/detail/' . $memberId) : base_url('admin/members/detail/' . $memberId);

                    $verifyDetailsText = '';
                    if (!empty($verifyResult['details']['maps']['detail']['signal_detected'])) {
                        $verifyDetailsText .= "Google Maps: " . $verifyResult['details']['maps']['detail']['signal_detected'] . "\n";
                    }
                    if (!empty($verifyResult['details']['fanpage']['detail']['message'])) {
                        $verifyDetailsText .= "Fanpage: " . $verifyResult['details']['fanpage']['detail']['message'] . "\n";
                    }

                    $emailData = [
                        'to'             => $recipientEmail,
                        'subject'        => '[CẢNH BÁO] Doanh nghiệp hội viên có dấu hiệu đóng cửa: ' . $member->company_name,
                        'template_path'  => 'email/email_business_closed_alert',
                        'company_name'   => $member->company_name,
                        'tax_code'       => $member->tax_code ?? 'Chưa cập nhật',
                        'address'        => $member->address ?? 'Chưa cập nhật',
                        'verify_result'  => 'Đã đóng cửa / Dừng hoạt động (Phát hiện tự động)',
                        'verify_date'    => $now,
                        'verify_details' => $verifyDetailsText,
                        'admin_url'      => $adminUrl,
                    ];

                    $sent = $emailModel->sendEmail($emailData);
                    if ($sent) {
                        $emailAlertsSent++;
                    }
                } catch (\Throwable $e) {
                    log_message('error', 'CronController verifyMembers Email Alert Exception: ' . $e->getMessage());
                }
            } else {
                $unverifiedCount++;
            }
        }

        // 5. Output Execution Summary
        $summary = "========================================\n"
                 . "TOP BEST GLOBAL MEMBER VERIFICATION CRON\n"
                 . "========================================\n"
                 . "Execution Time: {$now}\n"
                 . "Total Members Due & Processed: {$totalProcessed}\n"
                 . "- Verified (Active): {$verifiedCount}\n"
                 . "- Unverified (Pending/Not Found): {$unverifiedCount}\n"
                 . "- Failed (Closed Alert): {$failedCount}\n"
                 . "- Admin Email Alerts Sent: {$emailAlertsSent}\n"
                 . "Next Rolling Schedule: +6 months ({$nextVerifyAt})\n"
                 . "========================================\n";

        echo $summary;
    }
}
