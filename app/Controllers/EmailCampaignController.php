<?php namespace App\Controllers;

use App\Models\EmailCampaignModel;
use App\Models\EmailModel;
use App\Models\NewsletterModel;
use App\Models\AuthModel;

class EmailCampaignController extends BaseAdminController
{
    protected $campaignModel;
    protected $newsletterModel;
    protected $authModel;
    protected $db;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->db = \Config\Database::connect();
        $this->campaignModel = new EmailCampaignModel();
        $this->newsletterModel = new NewsletterModel();
        $this->authModel = new AuthModel();
    }

    public function campaigns()
    {
        checkPermission('newsletter');
        $data['title'] = "Email Marketing - Quản Lý Chiến Dịch";
        $data['campaigns'] = $this->campaignModel->getCampaigns(50);
        $data['groupsCount'] = count($this->campaignModel->getGroups());
        $data['subscribersCount'] = $this->newsletterModel->getSubscribersCount();
        $data['usersCount'] = $this->authModel->getUsersCount();

        echo view('admin/includes/_header', $data);
        echo view('admin/newsletter/newsletter', $data);
        echo view('admin/includes/_footer');
    }

    public function groups()
    {
        checkPermission('newsletter');
        $data['title'] = "Email Marketing - Quản Lý Nhóm Email";
        $data['groups'] = $this->campaignModel->getGroups();
        $data['industries'] = $this->db->table('industry_types')->orderBy('name', 'ASC')->get()->getResult();

        echo view('admin/includes/_header', $data);
        echo view('admin/newsletter/groups', $data);
        echo view('admin/includes/_footer');
    }

    public function saveGroupPost()
    {
        checkPermission('newsletter');
        $id = inputPost('id');
        $name = trim(inputPost('name'));
        if (empty($name)) {
            setErrorMessage("Vui lòng nhập tên nhóm email.");
            return redirect()->back();
        }

        $groupData = [
            'name' => $name,
            'description' => trim(inputPost('description')),
            'filter_source' => inputPost('filter_source') ?: 'all',
            'filter_lang' => inputPost('filter_lang') ?: 'all',
            'filter_industry_id' => (int)inputPost('filter_industry_id'),
            'filter_verify_status' => inputPost('filter_verify_status') ?: 'all',
            'custom_emails' => trim(inputPost('custom_emails'))
        ];

        $this->campaignModel->saveGroup($groupData, $id ?: null);
        setSuccessMessage($id ? "Cập nhật nhóm email thành công." : "Tạo nhóm email mới thành công.");
        return redirect()->to(adminUrl('newsletter-groups'));
    }

    public function deleteGroupPost()
    {
        checkPermission('newsletter');
        $id = inputPost('id');
        $this->campaignModel->deleteGroup($id);
        setSuccessMessage("Xóa nhóm email thành công.");
        return redirect()->to(adminUrl('newsletter-groups'));
    }

    public function getGroupPreviewAjax()
    {
        checkPermission('newsletter');
        $source = inputPost('filter_source') ?: 'all';
        $lang = inputPost('filter_lang') ?: 'all';
        $industryId = (int)inputPost('filter_industry_id');
        $verifyStatus = inputPost('filter_verify_status') ?: 'all';
        $customEmails = trim(inputPost('custom_emails'));

        $recipients = $this->campaignModel->getRecipientsByFilter($source, $lang, $industryId, $verifyStatus, $customEmails);
        echo json_encode([
            'status' => 'success',
            'total' => count($recipients),
            'sample' => array_slice($recipients, 0, 15)
        ]);
        exit();
    }

    public function createCampaign()
    {
        checkPermission('newsletter');
        $data['title'] = "Tạo Chiến Dịch Email Marketing Mới";
        $data['languages'] = $this->activeLanguages;
        $data['emailGroups'] = $this->campaignModel->getGroups();

        $langId = inputGet('lang');
        if (empty($langId)) {
            $langId = $this->activeLang->id ?? 1;
        }

        $selectedLang = null;
        if (!empty($this->activeLanguages)) {
            foreach ($this->activeLanguages as $l) {
                if ($l->id == $langId) {
                    $selectedLang = $l;
                    break;
                }
            }
        }

        $isEnglish = ($selectedLang && ($selectedLang->short_form == 'en' || strpos(strtolower($selectedLang->name), 'eng') !== false || $selectedLang->id == 2));

        $data['selectedLangId'] = $langId;
        $data['isEnglish'] = $isEnglish;
        $data['defaultTitle'] = $isEnglish ? ("TOP BEST GLOBAL Business Newsletter - " . date('d/m/Y')) : ("Bản Tin Doanh Nghiệp TOP BEST GLOBAL - " . date('d/m/Y'));
        $data['defaultSubject'] = $isEnglish ? "[TOP BEST GLOBAL] Latest Market Trends & Global Business Insights" : "[TOP BEST GLOBAL] Cập Nhật Tin Tức & Xu Hướng Thị Trường Mới Nhất";
        $data['posts'] = $this->campaignModel->getLatestPostsForSelection($langId, 40);

        echo view('admin/includes/_header', $data);
        echo view('admin/newsletter/create_campaign', $data);
        echo view('admin/includes/_footer');
    }

    public function createCampaignPost()
    {
        checkPermission('newsletter');
        $title = inputPost('title');
        $subject = inputPost('subject');
        $templateType = inputPost('template_type') ?: 'news_digest';
        $postIds = inputPost('post_ids') ?: [];
        $recipientType = inputPost('recipient_type') ?: 'all';
        $langId = inputPost('lang_id') ?: ($this->activeLang->id ?? 1);

        if (empty($title) || empty($subject)) {
            setErrorMessage("Vui lòng nhập đầy đủ tên chiến dịch và tiêu đề email.");
            return redirect()->back();
        }

        $campaignId = $this->campaignModel->createCampaignWithRecipients($title, $subject, $templateType, $postIds, $recipientType, $langId);
        setSuccessMessage("Tạo chiến dịch email thành công. Bạn có thể xem lại và tùy chỉnh danh sách người nhận.");
        return redirect()->to(adminUrl('newsletter-send-campaign/' . $campaignId));
    }

    public function sendCampaign($campaignId)
    {
        checkPermission('newsletter');
        $campaign = $this->campaignModel->getCampaignById($campaignId);
        if (!$campaign) {
            setErrorMessage("Không tìm thấy chiến dịch email.");
            return redirect()->to(adminUrl('newsletter'));
        }

        $data['title'] = "Gửi Chiến Dịch: " . esc($campaign->title);
        $data['campaign'] = $campaign;
        $data['logs'] = $this->campaignModel->getCampaignLogs($campaignId);

        $postIds = !empty($campaign->selected_post_ids) ? explode(',', $campaign->selected_post_ids) : [];
        $data['posts'] = $this->campaignModel->getPostsByIds($postIds);

        echo view('admin/includes/_header', $data);
        echo view('admin/newsletter/send_campaign', $data);
        echo view('admin/includes/_footer');
    }

    public function sendSingleLogAjax()
    {
        checkPermission('newsletter');
        $logId = inputPost('log_id');
        $log = $this->db->table('email_campaign_logs')->where('id', (int)$logId)->get()->getRow();
        if (!$log) {
            echo json_encode(['status' => 'error', 'message' => 'Log not found']);
            exit();
        }

        $campaign = $this->campaignModel->getCampaignById($log->campaign_id);
        if (!$campaign) {
            echo json_encode(['status' => 'error', 'message' => 'Campaign not found']);
            exit();
        }

        $postIds = !empty($campaign->selected_post_ids) ? explode(',', $campaign->selected_post_ids) : [];
        $posts = $this->campaignModel->getPostsByIds($postIds);

        $emailHtml = $this->campaignModel->buildPersonalizedEmailHtml($campaign, $log, $posts);
        $emailModel = new EmailModel();
        
        $data = [
            'subject' => $campaign->subject,
            'message' => $emailHtml,
            'to' => $log->recipient_email,
            'template_path' => "email/email_newsletter",
            'subscriber' => null
        ];

        $sent = $emailModel->sendEmail($data);
        if ($sent || ENVIRONMENT !== 'production') {
            $this->db->table('email_campaign_logs')->where('id', $log->id)->update([
                'is_sent' => 1,
                'sent_at' => date('Y-m-d H:i:s')
            ]);
            $this->db->query("UPDATE email_campaigns SET sent_count = sent_count + 1 WHERE id = " . (int)$campaign->id);
            
            echo json_encode([
                'status' => 'success',
                'email' => $log->recipient_email,
                'name' => $log->recipient_name
            ]);
            exit();
        }

        echo json_encode(['status' => 'error', 'message' => 'Failed to send']);
        exit();
    }

    public function addRecipientAjax()
    {
        checkPermission('newsletter');
        $campaignId = (int)inputPost('campaign_id');
        $email = trim(inputPost('email'));
        $name = trim(inputPost('name'));
        $company = trim(inputPost('company'));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'message' => 'Email không hợp lệ.']);
            exit();
        }

        $token = bin2hex(random_bytes(24));
        $this->db->table('email_campaign_logs')->insert([
            'campaign_id' => $campaignId,
            'recipient_email' => $email,
            'recipient_name' => $name,
            'company_name' => $company,
            'tracking_token' => $token,
            'is_sent' => 0,
            'is_opened' => 0,
            'is_clicked' => 0
        ]);
        $logId = $this->db->insertID();
        $this->db->query("UPDATE email_campaigns SET total_recipients = total_recipients + 1 WHERE id = " . $campaignId);

        echo json_encode([
            'status' => 'success',
            'log' => [
                'id' => $logId,
                'email' => $email,
                'name' => $name,
                'company' => $company
            ]
        ]);
        exit();
    }

    public function editRecipientAjax()
    {
        checkPermission('newsletter');
        $logId = (int)inputPost('log_id');
        $email = trim(inputPost('email'));
        $name = trim(inputPost('name'));
        $company = trim(inputPost('company'));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'message' => 'Email không hợp lệ.']);
            exit();
        }

        $this->db->table('email_campaign_logs')->where('id', $logId)->update([
            'recipient_email' => $email,
            'recipient_name' => $name,
            'company_name' => $company
        ]);

        echo json_encode(['status' => 'success']);
        exit();
    }

    public function deleteRecipientAjax()
    {
        checkPermission('newsletter');
        $logId = (int)inputPost('log_id');
        $log = $this->db->table('email_campaign_logs')->where('id', $logId)->get()->getRow();
        if ($log) {
            $campaignId = (int)$log->campaign_id;
            $isSent = (int)$log->is_sent;
            $this->db->table('email_campaign_logs')->where('id', $logId)->delete();
            $this->db->query("UPDATE email_campaigns SET total_recipients = GREATEST(0, total_recipients - 1)" . ($isSent ? ", sent_count = GREATEST(0, sent_count - 1)" : "") . " WHERE id = " . $campaignId);
        }

        echo json_encode(['status' => 'success']);
        exit();
    }

    public function deleteCampaignPost()
    {
        checkPermission('newsletter');
        $id = inputPost('id');
        $this->db->table('email_campaign_logs')->where('campaign_id', (int)$id)->delete();
        $this->db->table('email_campaigns')->where('id', (int)$id)->delete();
        setSuccessMessage("Xóa chiến dịch thành công.");
        return redirect()->to(adminUrl('newsletter'));
    }

    public function trackOpen($token)
    {
        $this->campaignModel->recordOpen($token);
        header('Content-Type: image/png');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        echo base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=');
        exit();
    }

    public function trackClick($token)
    {
        $this->campaignModel->recordClick($token);
        $targetUrl = inputGet('url') ?: base_url();
        return redirect()->to($targetUrl);
    }
}
