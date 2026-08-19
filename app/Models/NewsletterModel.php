<?php namespace App\Models;

use CodeIgniter\Model;

class NewsletterModel extends BaseModel
{
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('subscribers');
    }

    //add to subscriber
    public function addSubscriber($email)
    {
        $data = [
            'email' => $email,
            'token' => generateToken(),
            'created_at' => date('Y-m-d H:i:s')
        ];
        return $this->builder->insert($data);
    }

    //update subscriber token
    public function updateSubscriberToken($email)
    {
        $subscriber = $this->getSubscriber($email);
        if (!empty($subscriber)) {
            if (empty($subscriber->token)) {
                $this->builder->where('email', cleanStr($email))->update(['token' => generateToken()]);
            }
        }
    }

    //get subscribers count
    public function getSubscribersCount()
    {
        return $this->builder->countAllResults();
    }

    //load more subscribers
    public function loadMoreSubscribers($q, $perPage, $offset)
    {
        $q = cleanStr($q);
        if (!empty($q)) {
            $this->builder->like('email', $q);
        }
        return $this->builder->orderBy('id')->limit($perPage, $offset)->get()->getResult();
    }

    //get subscriber emails by ids
    public function getSubscriberEmailsByIds($ids)
    {
        $emails = array();
        $rows = $this->builder->select('email')->whereIn('id', $ids, false)->get()->getResult();
        if (!empty($rows)) {
            $emails = array_map(function ($item) {
                return $item->email;
            }, $rows);
        }
        return $emails;
    }

    //get subscriber
    public function getSubscriber($email)
    {
        return $this->builder->where('email', cleanStr($email))->get()->getRow();
    }

    //delete from subscribers
    public function deleteSubscriber($id)
    {
        return $this->builder->where('id', clrNum($id))->delete();
    }

    //get subscriber by token
    public function getSubscriberByToken($token)
    {
        return $this->builder->where('token', cleanStr($token))->get()->getRow();
    }

    //unsubscribe email
    public function unsubscribeEmail($email)
    {
        return $this->builder->where('email', cleanStr($email))->delete();
    }

    //update settings
    public function updateSettings()
    {
        $data = [
            'newsletter_status' => inputPost('newsletter_status'),
            'newsletter_popup' => inputPost('newsletter_popup')
        ];

        $uploadModel = new UploadModel();
        $file = $uploadModel->uploadTempFile('file');
        if (!empty($file) && !empty($file['path'])) {
            @unlink(FCPATH . $this->generalSettings->newsletter_image);
            $data['newsletter_image'] = $uploadModel->uploadNewsletterImage($file['path']);
        }
        return $this->db->table('general_settings')->where('id', 1)->update($data);
    }

    //send email
    public function sendEmail()
    {
        $emailModel = new EmailModel();
        $email = inputPost('email');
        $subject = inputPost('subject');
        $body = inputPost('body');
        $submit = inputPost('submit');
        if ($submit == "subscribers") {
            $subscriber = $this->getSubscriber($email);
            if (!empty($subscriber)) {
                if ($emailModel->sendEmailNewsletter($subscriber, $subject, $body)) {
                    return true;
                }
            }
        } else {
            $data = [
                'subject' => $subject,
                'message' => $body,
                'to' => $email,
                'template_path' => "email/email_newsletter",
                'subscriber' => $subscriber,
            ];
            return $emailModel->sendEmail($data);
        }
        return false;
    }

    /**
     * Generate News Digest Email Template
     */
    public function generateNewsDigestTemplate($postsCount = 4, $langId = null)
    {
        $postsCount = max(1, min(10, (int)$postsCount));
        $langId = !empty($langId) ? (int)$langId : ($this->activeLang->id ?? 1);
        
        $postModel = new PostModel();
        $posts = $postModel->getPosts($langId, $postsCount);
        
        $brandName = "TOP BEST GLOBAL";
        $subject = "[Bản Tin TOP BEST GLOBAL] Cập Nhật Tin Tức & Xu Hướng Thị Trường Mới Nhất";
        
        $html = '<div style="font-family: \'Segoe UI\', Arial, Helvetica, sans-serif; max-width: 620px; margin: 0 auto; color: #1e293b; background-color: #ffffff; line-height: 1.6;">';
        
        // Header Banner
        $html .= '<div style="background: linear-gradient(135deg, #070e1f 0%, #1e3a8a 100%); padding: 28px 24px; text-align: center; border-radius: 10px 10px 0 0;">';
        $html .= '<span style="display: inline-block; background: rgba(59,130,246,0.25); color: #93c5fd; font-size: 11px; font-weight: 800; padding: 4px 12px; border-radius: 20px; letter-spacing: 1px; margin-bottom: 8px; text-transform: uppercase;">BẢN TIN DOANH NGHIỆP & LOGISTICS</span>';
        $html .= '<h1 style="color: #ffffff; font-size: 20px; font-weight: 800; margin: 6px 0 0; line-height: 1.35;">Cập Nhật Chuyển Động Thị Trường & Xu Hướng Mới Nhất</h1>';
        $html .= '</div>';
        
        // Intro Greeting
        $html .= '<div style="padding: 20px 24px 10px 24px; font-size: 14px; color: #475569; border-left: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0;">';
        $html .= '<p style="margin: 0 0 12px;"><strong>Kính gửi Quý Khách hàng & Đối tác,</strong></p>';
        $html .= '<p style="margin: 0;">TOP BEST GLOBAL xin gửi tới Quý doanh nghiệp bản tin tổng hợp các tin tức, biến động giá cước và chính sách xuất nhập khẩu đáng chú ý nhất trong thời gian qua:</p>';
        $html .= '</div>';
        
        // Posts List Cards
        $html .= '<div style="padding: 10px 24px 20px 24px; border-left: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0;">';
        if (!empty($posts)) {
            foreach ($posts as $idx => $post) {
                $postUrl = generatePostUrl($post);
                $postImg = getPostImage($post, 'mid');
                $summary = characterLimiter($post->summary ?? '', 120);
                $catName = !empty($post->category_name) ? esc($post->category_name) : 'Logistics';
                
                $html .= '<div style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 10px; overflow: hidden; margin-bottom: 18px; padding: 16px;">';
                
                if (!empty($postImg)) {
                    $html .= '<div style="margin-bottom: 12px;">';
                    $html .= '<a href="' . $postUrl . '" target="_blank" style="display: block; text-decoration: none;">';
                    $html .= '<img src="' . $postImg . '" alt="' . esc($post->title) . '" style="width: 100%; max-height: 220px; object-fit: cover; border-radius: 6px; display: block; border: 0;" />';
                    $html .= '</a>';
                    $html .= '</div>';
                }
                
                $html .= '<div style="display: inline-block; background: #eff6ff; color: #1d4ed8; font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 4px; margin-bottom: 6px;">' . $catName . '</div>';
                
                $html .= '<h3 style="font-size: 16px; font-weight: 800; margin: 4px 0 8px 0; line-height: 1.4;">';
                $html .= '<a href="' . $postUrl . '" target="_blank" style="color: #0f172a; text-decoration: none;">' . esc($post->title) . '</a>';
                $html .= '</h3>';
                
                if (!empty($summary)) {
                    $html .= '<p style="font-size: 13px; color: #64748b; line-height: 1.55; margin: 0 0 12px 0;">' . esc($summary) . '</p>';
                }
                
                $html .= '<a href="' . $postUrl . '" target="_blank" style="display: inline-block; background: #2563eb; color: #ffffff; font-size: 12px; font-weight: 700; padding: 6px 14px; border-radius: 6px; text-decoration: none;">Đọc bài viết &rarr;</a>';
                $html .= '</div>';
            }
        } else {
            $html .= '<p style="text-align: center; color: #94a3b8; padding: 20px;">Không có bài viết nào để hiển thị.</p>';
        }
        $html .= '</div>';
        
        // Bottom CTA Banner
        $html .= '<div style="background: #0f172a; color: #ffffff; padding: 24px; text-align: center; border-radius: 0 0 10px 10px;">';
        $html .= '<h4 style="margin: 0 0 6px 0; font-size: 15px; font-weight: 800; color: #ffffff;">Quý Khách Cần Báo Giá Cước Vận Tải Quốc Tế Nhanh?</h4>';
        $html .= '<p style="margin: 0 0 14px 0; font-size: 12px; color: #94a3b8;">Đội ngũ chuyên viên TOP BEST GLOBAL hỗ trợ phản hồi báo giá trong vòng 15 - 30 phút.</p>';
        $html .= '<a href="' . base_url('services') . '" target="_blank" style="display: inline-block; background: #10b981; color: #ffffff; font-size: 13px; font-weight: 800; padding: 8px 20px; border-radius: 6px; text-decoration: none;">Yêu Cầu Báo Giá Ngay &rarr;</a>';
        $html .= '</div>';
        
        $html .= '</div>';
        
        return [
            'subject' => $subject,
            'html' => $html,
            'posts_count' => count($posts)
        ];
    }
}