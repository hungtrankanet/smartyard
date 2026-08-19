<?php namespace App\Models;

use CodeIgniter\Model;

class EmailCampaignModel extends BaseModel
{
    protected $table = 'email_campaigns';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'title', 'subject', 'template_type', 'selected_post_ids', 'recipient_type', 'lang_id',
        'total_recipients', 'sent_count', 'opened_count', 'clicked_count', 'status',
        'created_at', 'updated_at'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->ensureTablesExist();
    }

    public function ensureTablesExist()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `email_campaigns` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(255) NOT NULL,
            `subject` VARCHAR(255) NOT NULL,
            `template_type` VARCHAR(50) DEFAULT 'news_digest',
            `selected_post_ids` TEXT NULL,
            `recipient_type` VARCHAR(50) DEFAULT 'all',
            `lang_id` INT DEFAULT 1,
            `total_recipients` INT DEFAULT 0,
            `sent_count` INT DEFAULT 0,
            `opened_count` INT DEFAULT 0,
            `clicked_count` INT DEFAULT 0,
            `status` VARCHAR(30) DEFAULT 'draft',
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `email_campaign_logs` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `campaign_id` INT NOT NULL,
            `recipient_email` VARCHAR(255) NOT NULL,
            `recipient_name` VARCHAR(255) DEFAULT '',
            `company_name` VARCHAR(255) DEFAULT '',
            `tracking_token` VARCHAR(64) NOT NULL UNIQUE,
            `is_sent` TINYINT DEFAULT 0,
            `is_opened` TINYINT DEFAULT 0,
            `opened_at` DATETIME NULL,
            `is_clicked` TINYINT DEFAULT 0,
            `clicked_at` DATETIME NULL,
            `sent_at` DATETIME NULL,
            INDEX `idx_camp` (`campaign_id`),
            INDEX `idx_token` (`tracking_token`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `email_groups` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(255) NOT NULL,
            `description` TEXT NULL,
            `filter_source` VARCHAR(50) DEFAULT 'all',
            `filter_lang` VARCHAR(10) DEFAULT 'all',
            `filter_industry_id` INT DEFAULT 0,
            `filter_verify_status` VARCHAR(20) DEFAULT 'all',
            `custom_emails` TEXT NULL,
            `total_contacts` INT DEFAULT 0,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        if (!$this->db->fieldExists('preferred_lang', 'members')) {
            $this->db->query("ALTER TABLE `members` ADD COLUMN `preferred_lang` VARCHAR(10) DEFAULT 'vi';");
        }
        if (!$this->db->fieldExists('lang_id', 'email_campaigns')) {
            $this->db->query("ALTER TABLE `email_campaigns` ADD COLUMN `lang_id` INT DEFAULT 1;");
        }
    }

    public function getCampaigns($limit = 50)
    {
        return $this->db->table('email_campaigns')->orderBy('id', 'DESC')->limit($limit)->get()->getResult();
    }

    public function getCampaignById($id)
    {
        return $this->db->table('email_campaigns')->where('id', (int)$id)->get()->getRow();
    }

    public function getCampaignLogs($campaignId)
    {
        return $this->db->table('email_campaign_logs')->where('campaign_id', (int)$campaignId)->get()->getResult();
    }

    public function getPostsByIds($postIds)
    {
        if (empty($postIds)) return [];
        $builder = $this->db->table('posts');
        $builder->select('posts.id, posts.title, posts.slug, posts.summary, posts.image_id, posts.image_url, posts.created_at, categories.name AS category_name');
        $builder->select("(SELECT CONCAT('img_bg::', i.image_big, '||','img_df::', i.image_default, '||','img_sl::', i.image_slider, '||','img_md::', i.image_mid, '||','img_sm::', i.image_small, '||','img_mi::', i.image_mime, '||','img_st::', i.storage) FROM images i WHERE i.id = posts.image_id LIMIT 1) AS image_data");
        $builder->join('categories', 'categories.id = posts.category_id', 'left');
        $builder->whereIn('posts.id', $postIds, false);
        return $builder->get()->getResult();
    }

    public function getLatestPostsForSelection($langId = 1, $limit = 40)
    {
        $builder = $this->db->table('posts');
        $builder->select('posts.id, posts.title, posts.slug, posts.summary, posts.image_id, posts.image_url, posts.created_at, categories.name AS category_name');
        $builder->select("(SELECT CONCAT('img_bg::', i.image_big, '||','img_df::', i.image_default, '||','img_sl::', i.image_slider, '||','img_md::', i.image_mid, '||','img_sm::', i.image_small, '||','img_mi::', i.image_mime, '||','img_st::', i.storage) FROM images i WHERE i.id = posts.image_id LIMIT 1) AS image_data");
        $builder->join('categories', 'categories.id = posts.category_id', 'left');
        $builder->where('posts.lang_id', (int)$langId);
        $builder->where('posts.status', 1);
        $builder->where('posts.visibility', 1);
        $builder->orderBy('posts.created_at', 'DESC');
        return $builder->limit($limit)->get()->getResult();
    }

    // --- EMAIL GROUPS MANAGEMENT ---
    public function getGroups()
    {
        return $this->db->table('email_groups')->orderBy('id', 'DESC')->get()->getResult();
    }

    public function getGroupById($id)
    {
        return $this->db->table('email_groups')->where('id', (int)$id)->get()->getRow();
    }

    public function saveGroup($data, $id = null)
    {
        $recipients = $this->getRecipientsByFilter(
            $data['filter_source'] ?? 'all',
            $data['filter_lang'] ?? 'all',
            $data['filter_industry_id'] ?? 0,
            $data['filter_verify_status'] ?? 'all',
            $data['custom_emails'] ?? ''
        );
        $data['total_contacts'] = count($recipients);

        if ($id) {
            $data['updated_at'] = date('Y-m-d H:i:s');
            $this->db->table('email_groups')->where('id', (int)$id)->update($data);
            return $id;
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
            $this->db->table('email_groups')->insert($data);
            return $this->db->insertID();
        }
    }

    public function deleteGroup($id)
    {
        return $this->db->table('email_groups')->where('id', (int)$id)->delete();
    }

    public function getRecipientsByFilter($source = 'all', $lang = 'all', $industryId = 0, $verifyStatus = 'all', $customEmails = '')
    {
        $recipients = [];
        $emailSet = [];

        // 1. Members
        if ($source === 'all' || $source === 'members') {
            $builder = $this->db->table('members')->select('company_name, representative_name, email, preferred_lang')->where('status', 1);
            if ($lang === 'vi') {
                $builder->where("(preferred_lang = 'vi' OR preferred_lang IS NULL OR preferred_lang = '')");
            } elseif ($lang === 'en') {
                $builder->where('preferred_lang', 'en');
            }
            if ((int)$industryId > 0) {
                $builder->where('industry_type_id', (int)$industryId);
            }
            if ($verifyStatus === 'verified') {
                $builder->where('verify_status', 'verified');
            } elseif ($verifyStatus === 'pending') {
                $builder->where('verify_status', 'pending');
            }

            $members = $builder->get()->getResult();
            foreach ($members as $m) {
                $em = trim(strtolower($m->email ?? ''));
                if (!empty($em) && !isset($emailSet[$em])) {
                    $emailSet[$em] = true;
                    $recipients[] = [
                        'email' => $em,
                        'name' => $m->representative_name ?: 'Đại diện Doanh nghiệp',
                        'company' => $m->company_name ?: 'Đối tác TOP BEST GLOBAL'
                    ];
                }
            }
        }

        // 2. Subscribers
        if ($source === 'all' || $source === 'subscribers') {
            $subs = $this->db->table('subscribers')->select('email')->get()->getResult();
            foreach ($subs as $s) {
                $em = trim(strtolower($s->email));
                if (!empty($em) && !isset($emailSet[$em])) {
                    $emailSet[$em] = true;
                    $recipients[] = ['email' => $em, 'name' => 'Quý Đối tác', 'company' => 'TOP BEST GLOBAL Partner'];
                }
            }
        }

        // 3. Regular Users
        if ($source === 'all' || $source === 'users') {
            $users = $this->db->table('users')->select('username, email')->where('status', 1)->get()->getResult();
            foreach ($users as $u) {
                $em = trim(strtolower($u->email ?? ''));
                if (!empty($em) && !isset($emailSet[$em])) {
                    $emailSet[$em] = true;
                    $recipients[] = ['email' => $em, 'name' => $u->username ?: 'Khách hàng', 'company' => 'Doanh nghiệp XNK'];
                }
            }
        }

        // 4. Custom Raw Emails
        if (!empty($customEmails)) {
            $lines = explode("\n", str_replace("\r", "", $customEmails));
            foreach ($lines as $line) {
                $parts = array_map('trim', explode(',', $line));
                $em = trim(strtolower($parts[0] ?? ''));
                if (filter_var($em, FILTER_VALIDATE_EMAIL) && !isset($emailSet[$em])) {
                    $emailSet[$em] = true;
                    $recipients[] = [
                        'email' => $em,
                        'name' => $parts[1] ?? 'Quý Đối tác',
                        'company' => $parts[2] ?? 'Doanh nghiệp Đối tác'
                    ];
                }
            }
        }

        return $recipients;
    }

    public function getRecipientsByGroup($type = 'all')
    {
        if (strpos($type, 'group_') === 0 || is_numeric($type)) {
            $groupId = is_numeric($type) ? (int)$type : (int)str_replace('group_', '', $type);
            $group = $this->getGroupById($groupId);
            if ($group) {
                return $this->getRecipientsByFilter(
                    $group->filter_source,
                    $group->filter_lang,
                    $group->filter_industry_id,
                    $group->filter_verify_status,
                    $group->custom_emails
                );
            }
        }

        if ($type === 'members_vi') {
            return $this->getRecipientsByFilter('members', 'vi');
        } elseif ($type === 'members_en') {
            return $this->getRecipientsByFilter('members', 'en');
        } elseif ($type === 'members') {
            return $this->getRecipientsByFilter('members', 'all');
        } elseif ($type === 'subscribers') {
            return $this->getRecipientsByFilter('subscribers', 'all');
        } elseif ($type === 'users') {
            return $this->getRecipientsByFilter('users', 'all');
        }

        return $this->getRecipientsByFilter('all', 'all');
    }

    public function createCampaignWithRecipients($title, $subject, $templateType, $postIds, $recipientType, $langId = 1)
    {
        $recipients = $this->getRecipientsByGroup($recipientType);
        $totalRecipients = count($recipients);

        $campaignData = [
            'title' => $title,
            'subject' => $subject,
            'template_type' => $templateType,
            'selected_post_ids' => is_array($postIds) ? implode(',', $postIds) : $postIds,
            'recipient_type' => $recipientType,
            'lang_id' => (int)$langId,
            'total_recipients' => $totalRecipients,
            'sent_count' => 0,
            'opened_count' => 0,
            'clicked_count' => 0,
            'status' => 'draft',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->table('email_campaigns')->insert($campaignData);
        $campaignId = $this->db->insertID();

        foreach ($recipients as $rec) {
            $this->db->table('email_campaign_logs')->insert([
                'campaign_id' => $campaignId,
                'recipient_email' => $rec['email'],
                'recipient_name' => $rec['name'],
                'company_name' => $rec['company'],
                'tracking_token' => bin2hex(random_bytes(24)),
                'is_sent' => 0,
                'is_opened' => 0,
                'is_clicked' => 0
            ]);
        }

        return $campaignId;
    }

    public function buildPersonalizedEmailHtml($campaign, $log, $posts = [])
    {
        $token = $log->tracking_token;
        $name = !empty($log->recipient_name) ? esc($log->recipient_name) : 'Quý Đối tác';
        $company = !empty($log->company_name) ? esc($log->company_name) : 'Quý Doanh nghiệp';
        $isEn = (isset($campaign->lang_id) && $campaign->lang_id == 2);

        $html = '<div style="font-family:\'Segoe UI\',Arial,sans-serif;max-width:620px;margin:0 auto;color:#1e293b;background:#ffffff;line-height:1.6;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">';
        
        // Header Banner
        $html .= '<div style="background:linear-gradient(135deg,#070e1f 0%,#1e3a8a 100%);padding:28px 24px;text-align:center;">';
        $tagText = $isEn ? 'TOP BEST GLOBAL BUSINESS ALLIANCE' : 'LIÊN MINH DOANH NGHIỆP TOP BEST GLOBAL';
        $html .= '<span style="display:inline-block;background:rgba(59,130,246,0.25);color:#93c5fd;font-size:11px;font-weight:800;padding:4px 12px;border-radius:20px;letter-spacing:1px;margin-bottom:8px;text-transform:uppercase;">' . $tagText . '</span>';
        $html .= '<h1 style="color:#ffffff;font-size:20px;font-weight:800;margin:6px 0 0;line-height:1.35;">' . esc($campaign->subject) . '</h1>';
        $html .= '</div>';

        // Personalized Greeting
        $html .= '<div style="padding:22px 24px 12px;font-size:14px;color:#334155;">';
        if ($isEn) {
            $html .= '<p style="margin:0 0 10px;font-size:15px;"><strong>Dear Mr./Ms. ' . $name . ' (' . $company . '),</strong></p>';
            $html .= '<p style="margin:0;color:#64748b;">TOP BEST GLOBAL is pleased to present the latest market insights, freight rate movements, and international logistics updates:</p>';
        } else {
            $html .= '<p style="margin:0 0 10px;font-size:15px;"><strong>Kính gửi: ' . $name . ' (' . $company . '),</strong></p>';
            $html .= '<p style="margin:0;color:#64748b;">TOP BEST GLOBAL trân trọng gửi tới Quý đối tác bản tin tổng hợp các phân tích thị trường, biến động giá cước và xu hướng xuất nhập khẩu logistics mới nhất:</p>';
        }
        $html .= '</div>';

        // Posts List Cards
        $html .= '<div style="padding:10px 24px 20px;">';
        foreach ($posts as $p) {
            $rawUrl = generatePostUrl($p);
            $trackUrl = base_url('email-track/click/' . $token . '?url=' . urlencode($rawUrl));
            $postImg = getPostImage($p, 'mid');
            $summary = characterLimiter($p->summary ?? '', 120);
            $catName = !empty($p->category_name) ? esc($p->category_name) : 'Logistics';
            $btnText = $isEn ? 'Read article &rarr;' : 'Đọc bài viết &rarr;';

            $html .= '<div style="background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:10px;overflow:hidden;margin-bottom:18px;padding:16px;">';
            if (!empty($postImg)) {
                $html .= '<a href="' . $trackUrl . '" target="_blank" style="display:block;margin-bottom:12px;"><img src="' . $postImg . '" alt="' . esc($p->title) . '" style="width:100%;max-height:220px;object-fit:cover;border-radius:6px;display:block;border:0;" /></a>';
            }
            $html .= '<span style="display:inline-block;background:#eff6ff;color:#1d4ed8;font-size:11px;font-weight:800;padding:2px 8px;border-radius:4px;margin-bottom:6px;">' . $catName . '</span>';
            $html .= '<h3 style="font-size:16px;font-weight:800;margin:4px 0 8px 0;line-height:1.4;"><a href="' . $trackUrl . '" target="_blank" style="color:#0f172a;text-decoration:none;">' . esc($p->title) . '</a></h3>';
            if (!empty($summary)) {
                $html .= '<p style="font-size:13px;color:#64748b;line-height:1.55;margin:0 0 12px 0;">' . esc($summary) . '</p>';
            }
            $html .= '<a href="' . $trackUrl . '" target="_blank" style="display:inline-block;background:#2563eb;color:#ffffff;font-size:12px;font-weight:700;padding:6px 14px;border-radius:6px;text-decoration:none;">' . $btnText . '</a>';
            $html .= '</div>';
        }
        $html .= '</div>';

        // TOP BEST GLOBAL Contact Footer
        $rfqTrackUrl = base_url('email-track/click/' . $token . '?url=' . urlencode(base_url('services')));
        $ctaTitle = $isEn ? 'NEED INSTANT FREIGHT RATE QUOTES & CUSTOMS BROKERAGE?' : 'CẦN BÁO GIÁ CƯỚC VẬN TẢI QUỐC TẾ & DỊCH VỤ HẢI QUAN?';
        $ctaSubtitle = $isEn ? 'Our logistics specialists are ready to respond 24/7 within 15-30 minutes.' : 'Đội ngũ chuyên viên TOP BEST GLOBAL hỗ trợ giải đáp và phản hồi báo giá 24/7 trong vòng 15-30 phút.';
        $ctaBtn = $isEn ? 'Get Freight Quote &rarr;' : 'Nhận Báo Giá Cước Ngay &rarr;';

        $html .= '<div style="background:#0f172a;color:#ffffff;padding:26px 24px;text-align:center;">';
        $html .= '<h4 style="margin:0 0 6px 0;font-size:15px;font-weight:800;color:#ffffff;">' . $ctaTitle . '</h4>';
        $html .= '<p style="margin:0 0 14px 0;font-size:12px;color:#94a3b8;">' . $ctaSubtitle . '</p>';
        $html .= '<a href="' . $rfqTrackUrl . '" target="_blank" style="display:inline-block;background:#10b981;color:#ffffff;font-size:13px;font-weight:800;padding:9px 22px;border-radius:6px;text-decoration:none;margin-bottom:18px;">' . $ctaBtn . '</a>';
        
        $html .= '<div style="border-top:1px solid rgba(255,255,255,0.12);padding-top:16px;font-size:11px;color:#94a3b8;line-height:1.7;text-align:left;">';
        $html .= '<strong>CÔNG TY CỔ PHẦN TOP BEST GLOBAL</strong><br>';
        $html .= '📍 Trụ sở: Tòa nhà TOP BEST GLOBAL, TP. Hồ Chí Minh & Chi nhánh Hải Phòng, Đà Nẵng<br>';
        $html .= '📞 Hotline / Zalo: <strong>0901 234 567</strong> | ✉️ Email: <strong>contact@topbestglobal.com</strong><br>';
        $html .= '🌐 Website: <a href="' . base_url() . '" target="_blank" style="color:#38bdf8;text-decoration:none;">' . base_url() . '</a><br>';
        $html .= '</div>';
        $html .= '</div>';

        // 1x1 Open Tracking Pixel
        $pixelUrl = base_url('email-track/open/' . $token);
        $html .= '<img src="' . $pixelUrl . '" width="1" height="1" alt="" style="display:none !important;width:1px;height:1px;opacity:0;" />';
        $html .= '</div>';

        return $html;
    }

    public function recordOpen($token)
    {
        $log = $this->db->table('email_campaign_logs')->where('tracking_token', cleanStr($token))->get()->getRow();
        if ($log && $log->is_opened == 0) {
            $this->db->table('email_campaign_logs')->where('id', $log->id)->update([
                'is_opened' => 1,
                'opened_at' => date('Y-m-d H:i:s')
            ]);
            $this->db->query("UPDATE email_campaigns SET opened_count = opened_count + 1 WHERE id = " . (int)$log->campaign_id);
        }
    }

    public function recordClick($token)
    {
        $log = $this->db->table('email_campaign_logs')->where('tracking_token', cleanStr($token))->get()->getRow();
        if ($log) {
            if ($log->is_clicked == 0) {
                $this->db->table('email_campaign_logs')->where('id', $log->id)->update([
                    'is_clicked' => 1,
                    'clicked_at' => date('Y-m-d H:i:s')
                ]);
                $this->db->query("UPDATE email_campaigns SET clicked_count = clicked_count + 1 WHERE id = " . (int)$log->campaign_id);
            }
            if ($log->is_opened == 0) {
                $this->recordOpen($token);
            }
        }
    }
}
