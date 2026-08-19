<?php

namespace App\Controllers;

use App\Models\CommonModel;
use App\Models\PostModel;
use App\Models\SettingsModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Globals;
use Psr\Log\LoggerInterface;

/**
 * ApiController — REST JSON API for Suntransco Frontend
 *
 * Endpoints:
 *   GET  /api/news           — paginated list of news posts
 *   GET  /api/news/{slug}    — single news post
 *   GET  /api/events         — list of events (category: events)
 *   GET  /api/members        — list of members (category: members)
 *   GET  /api/settings       — site settings (name, logo, contact, social)
 *   POST /api/contact        — submit contact form
 *   POST /api/newsletter     — subscribe to newsletter
 */
class ApiController extends Controller
{
    protected $db;
    protected $postModel;
    protected $settingsModel;
    protected $commonModel;
    protected $generalSettings;
    protected $settings;
    protected $activeLangId = 1; // default lang: Vietnamese

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->db            = \Config\Database::connect();
        $this->postModel     = new PostModel();
        $this->settingsModel = new SettingsModel();
        $this->commonModel   = new CommonModel();
        $this->generalSettings = Globals::$generalSettings;
        $this->settings        = Globals::$settings;

        // Detect language from query param: ?lang=en
        $lang = $this->request->getGet('lang');
        if (!empty($lang)) {
            $langRow = $this->db->table('languages')
                ->where('short_form', $lang)
                ->get()->getRow();
            if (!empty($langRow)) {
                $this->activeLangId = $langRow->id;
            }
        } elseif (!empty(Globals::$activeLang)) {
            $this->activeLangId = Globals::$activeLang->id;
        }
    }

    // ─────────────────────────────────────────
    // CORS + JSON response helpers
    // ─────────────────────────────────────────
    private function jsonResponse($data, int $status = 200)
    {
        return $this->response
            ->setHeader('Access-Control-Allow-Origin', '*')
            ->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
            ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
            ->setStatusCode($status)
            ->setContentType('application/json')
            ->setBody(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function handleOptions()
    {
        return $this->response
            ->setHeader('Access-Control-Allow-Origin', '*')
            ->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
            ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->setStatusCode(204)
            ->setBody('');
    }

    // ─────────────────────────────────────────
    // GET /api/news
    // ─────────────────────────────────────────
    public function news()
    {
        if ($this->request->getMethod() === 'options') return $this->handleOptions();

        $limit  = (int)($this->request->getGet('limit') ?? 10);
        $offset = (int)($this->request->getGet('offset') ?? 0);
        $limit  = min($limit, 50); // max 50 per request

        $rows = $this->db->table('posts')
            ->select('posts.id, posts.title, posts.slug, posts.summary, posts.created_at, posts.pageviews, posts.image_url,
                      categories.name AS category_name, categories.slug AS category_slug, categories.color AS category_color,
                      users.username AS author,
                      (SELECT image_default FROM images WHERE images.id = posts.image_id LIMIT 1) AS thumbnail')
            ->join('categories', 'categories.id = posts.category_id', 'left')
            ->join('users', 'users.id = posts.user_id', 'left')
            ->where('posts.lang_id', $this->activeLangId)
            ->where('posts.status', 1)
            ->where('posts.visibility', 1)
            ->where('posts.is_scheduled', 0)
            ->orderBy('posts.created_at', 'DESC')
            ->limit($limit, $offset)
            ->get()->getResult();

        $total = $this->db->table('posts')
            ->where('lang_id', $this->activeLangId)
            ->where('status', 1)->where('visibility', 1)->where('is_scheduled', 0)
            ->countAllResults();

        $data = array_map([$this, 'formatPost'], $rows);

        return $this->jsonResponse([
            'success' => true,
            'total'   => $total,
            'limit'   => $limit,
            'offset'  => $offset,
            'data'    => $data,
        ]);
    }

    // ─────────────────────────────────────────
    // GET /api/news/{slug}
    // ─────────────────────────────────────────
    public function newsDetail($slug = '')
    {
        if ($this->request->getMethod() === 'options') return $this->handleOptions();

        $slug = preg_replace('/[^a-z0-9\-_]/', '', strtolower($slug));
        if (empty($slug)) {
            return $this->jsonResponse(['success' => false, 'message' => 'Invalid slug'], 400);
        }

        $row = $this->db->table('posts')
            ->select('posts.*, categories.name AS category_name, categories.slug AS category_slug,
                      users.username AS author,
                      (SELECT image_default FROM images WHERE images.id = posts.image_id LIMIT 1) AS thumbnail')
            ->join('categories', 'categories.id = posts.category_id', 'left')
            ->join('users', 'users.id = posts.user_id', 'left')
            ->where('posts.slug', $slug)
            ->where('posts.lang_id', $this->activeLangId)
            ->where('posts.status', 1)->where('posts.visibility', 1)
            ->get()->getRow();

        if (empty($row)) {
            return $this->jsonResponse(['success' => false, 'message' => 'Post not found'], 404);
        }

        return $this->jsonResponse(['success' => true, 'data' => $this->formatPost($row, true)]);
    }

    // ─────────────────────────────────────────
    // GET /api/events
    // ─────────────────────────────────────────
    public function events()
    {
        if ($this->request->getMethod() === 'options') return $this->handleOptions();

        $limit  = (int)($this->request->getGet('limit') ?? 10);
        $offset = (int)($this->request->getGet('offset') ?? 0);
        $limit  = min($limit, 50);

        // Events stored in posts table under category slug 'events' OR dedicated events table
        $rows = $this->db->table('posts')
            ->select('posts.id, posts.title, posts.slug, posts.summary, posts.created_at, posts.image_url,
                      (SELECT image_default FROM images WHERE images.id = posts.image_id LIMIT 1) AS thumbnail')
            ->join('categories', 'categories.id = posts.category_id', 'left')
            ->where('posts.lang_id', $this->activeLangId)
            ->where('posts.status', 1)
            ->where('posts.visibility', 1)
            ->where('posts.is_scheduled', 0)
            ->groupStart()
                ->where('categories.slug', 'events')
                ->orWhere('categories.slug', 'su-kien')
            ->groupEnd()
            ->orderBy('posts.created_at', 'DESC')
            ->limit($limit, $offset)
            ->get()->getResult();

        $data = array_map(function ($row) {
            return [
                'id'        => (int)$row->id,
                'title'     => $row->title,
                'slug'      => $row->slug,
                'summary'   => $row->summary,
                'date'      => $row->created_at,
                'thumbnail' => $this->buildImageUrl($row->thumbnail ?? $row->image_url),
                'url'       => base_url('news/' . $row->slug),
            ];
        }, $rows);

        return $this->jsonResponse(['success' => true, 'total' => count($data), 'data' => $data]);
    }

    // ─────────────────────────────────────────
    // GET /api/members
    // ─────────────────────────────────────────
    public function members()
    {
        if ($this->request->getMethod() === 'options') return $this->handleOptions();

        $limit  = (int)($this->request->getGet('limit') ?? 20);
        $offset = (int)($this->request->getGet('offset') ?? 0);
        $limit  = min($limit, 100);

        // Members stored as users with role 'member', or as posts in category 'members'
        $rows = $this->db->table('users')
            ->select('users.id, users.username, users.fullname, users.about, users.image, users.created_at')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->where('users.status', 1)
            ->orderBy('users.created_at', 'DESC')
            ->limit($limit, $offset)
            ->get()->getResult();

        $total = $this->db->table('users')->where('status', 1)->countAllResults();

        $data = array_map(function ($row) {
            return [
                'id'       => (int)$row->id,
                'name'     => $row->fullname ?: $row->username,
                'username' => $row->username,
                'about'    => $row->about,
                'avatar'   => $this->buildImageUrl($row->image),
                'joined'   => $row->created_at,
            ];
        }, $rows);

        return $this->jsonResponse([
            'success' => true,
            'total'   => $total,
            'limit'   => $limit,
            'offset'  => $offset,
            'data'    => $data,
        ]);
    }

    // ─────────────────────────────────────────
    // GET /api/settings
    // ─────────────────────────────────────────
    public function settings()
    {
        if ($this->request->getMethod() === 'options') return $this->handleOptions();

        $gs = $this->generalSettings;
        $s  = $this->settings;

        $data = [
            'site_name'    => $s->site_name ?? ($gs->site_name ?? 'TOP BEST GLOBAL'),
            'site_logo'    => base_url($gs->logo ?? ''),
            'site_email'   => $s->email ?? '',
            'site_phone'   => $s->phone ?? '',
            'site_address' => $s->address ?? '',
            'facebook'     => $s->facebook ?? '',
            'linkedin'     => $s->linkedin ?? '',
            'youtube'      => $s->youtube ?? '',
            'description'  => $s->site_description ?? '',
        ];

        return $this->jsonResponse(['success' => true, 'data' => $data]);
    }

    // ─────────────────────────────────────────
    // POST /api/contact
    // ─────────────────────────────────────────
    public function contact()
    {
        if ($this->request->getMethod() === 'options') return $this->handleOptions();

        $name        = trim($this->request->getPost('name') ?? '');
        $company     = trim($this->request->getPost('company') ?? '');
        $email       = trim($this->request->getPost('email') ?? '');
        $phone       = trim($this->request->getPost('phone') ?? '');
        $serviceName = trim($this->request->getPost('service_name') ?? 'Vận Tải & Logistics');
        $pol         = trim($this->request->getPost('pol') ?? '');
        $pod         = trim($this->request->getPost('pod') ?? '');
        $type        = trim($this->request->getPost('type') ?? '');
        $message     = trim($this->request->getPost('message') ?? '');

        if (empty($name) || (empty($email) && empty($phone))) {
            return $this->jsonResponse(['success' => false, 'message' => 'Vui lòng nhập họ tên và số điện thoại hoặc email liên hệ.', 'csrf_token' => csrf_hash()], 400);
        }

        // Save to native contacts table
        try {
            $formattedMsg = "Dịch vụ: " . $serviceName 
                . (!empty($company) ? " | Công ty: " . $company : "")
                . (!empty($phone) ? " | SĐT: " . $phone : "")
                . (!empty($pol) && !empty($pod) ? " | Tuyến: " . $pol . " -> " . $pod : "")
                . (!empty($type) ? " | Loại: " . $type : "")
                . "\nNội dung: " . ($message ?: 'Yêu cầu tư vấn báo giá dịch vụ');

            $this->db->table('contacts')->insert([
                'name'       => $name,
                'email'      => $email ?: 'customer@topbestglobal.com',
                'message'    => $formattedMsg,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            log_message('error', 'ApiController::contact contacts table - ' . $e->getMessage());
        }

        // Dispatch notification emails to Sales Team and Customer
        try {
            $emailModel = new \App\Models\EmailModel();
            $emailModel->sendEmailServiceRfq([
                'name'         => $name,
                'company'      => $company,
                'email'        => $email,
                'phone'        => $phone,
                'service_name' => $serviceName,
                'pol'          => $pol,
                'pod'          => $pod,
                'type'         => $type,
                'message'      => $message,
            ]);
        } catch (\Exception $e) {
            log_message('error', 'ApiController::contact email dispatch - ' . $e->getMessage());
        }

        return $this->jsonResponse([
            'success'    => true, 
            'message'    => 'Cảm ơn Quý khách! Yêu cầu báo giá đã được tiếp nhận. Đội ngũ TOP BEST GLOBAL đã gửi email xác nhận và sẽ phản hồi trong vòng 15-30 phút.',
            'csrf_token' => csrf_hash()
        ]);
    }

    // ─────────────────────────────────────────
    // POST /api/newsletter
    // ─────────────────────────────────────────
    public function newsletter()
    {
        if ($this->request->getMethod() === 'options') return $this->handleOptions();

        $email = trim($this->request->getPost('email') ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->jsonResponse(['success' => false, 'message' => 'Invalid email address'], 400);
        }

        // Check duplicate
        $exists = $this->db->table('newsletters')->where('email', $email)->countAllResults();
        if ($exists > 0) {
            return $this->jsonResponse(['success' => false, 'message' => 'Email already subscribed']);
        }

        $this->db->table('newsletters')->insert([
            'email'      => $email,
            'status'     => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->jsonResponse(['success' => true, 'message' => 'Successfully subscribed to newsletter.']);
    }

    // ─────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────
    private function formatPost($row, bool $includeContent = false): array
    {
        $item = [
            'id'             => (int)$row->id,
            'title'          => $row->title,
            'slug'           => $row->slug,
            'summary'        => $row->summary ?? '',
            'category'       => $row->category_name ?? '',
            'category_slug'  => $row->category_slug ?? '',
            'category_color' => $row->category_color ?? '',
            'author'         => $row->author ?? '',
            'date'           => $row->created_at,
            'pageviews'      => (int)($row->pageviews ?? 0),
            'thumbnail'      => $this->buildImageUrl($row->thumbnail ?? $row->image_url ?? ''),
            'url'            => base_url('news/' . $row->slug),
        ];

        if ($includeContent && isset($row->content)) {
            $item['content'] = $row->content;
        }

        return $item;
    }

    private function buildImageUrl(?string $path): string
    {
        if (empty($path)) return '';
        if (str_starts_with($path, 'http')) return $path;
        return base_url('uploads/' . ltrim($path, '/'));
    }
}
