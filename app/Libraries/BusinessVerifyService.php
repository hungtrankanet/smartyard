<?php

namespace App\Libraries;

use App\Models\MemberModel;
use App\Models\MemberVerifyLogModel;

/**
 * BusinessVerifyService: Automated Isolated Background Verification & Information Enrichment Engine
 * 
 * Verifies business activity in isolated sequential sessions:
 * 1. Opens isolated headless/crawler session (clears cookies, headers, memory)
 * 2. Checks Google Maps presence & operating signals
 * 3. Crawls Corporate Website & enriches missing contacts
 * 4. Checks Facebook Fanpage
 * 5. Aggregates status & records audit logs
 * 6. Explicitly closes isolated session & releases all resources
 */
class BusinessVerifyService
{
    protected string $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36';
    protected int $timeout = 8;
    protected int $sleepSeconds = 0;
    protected ?string $sessionCookieFile = null;
    protected ?MemberModel $memberModel = null;
    protected ?MemberVerifyLogModel $logModel = null;
    protected $httpHandler = null;

    public function __construct(?MemberModel $memberModel = null, ?MemberVerifyLogModel $logModel = null)
    {
        $this->memberModel = $memberModel;
        $this->logModel = $logModel;
    }

    public function setSleepSeconds(int $seconds): self { $this->sleepSeconds = max(0, $seconds); return $this; }
    public function setHttpHandler(?callable $handler): self { $this->httpHandler = $handler; return $this; }
    protected function getMemberModel(): MemberModel { if ($this->memberModel === null) $this->memberModel = new MemberModel(); return $this->memberModel; }
    protected function getLogModel(): MemberVerifyLogModel { if ($this->logModel === null) $this->logModel = new MemberVerifyLogModel(); return $this->logModel; }

    /**
     * Open a fresh, isolated background session (clean cookie jar & memory)
     */
    public function openIsolatedSession(): void
    {
        $this->closeIsolatedSession();
        $this->sessionCookieFile = sys_get_temp_dir() . '/topbestglobal_verify_' . uniqid('sess_', true) . '.tmp';
        @touch($this->sessionCookieFile);
    }

    /**
     * Explicitly close and destroy the background session, cookies, and memory
     */
    public function closeIsolatedSession(): void
    {
        if (!empty($this->sessionCookieFile) && file_exists($this->sessionCookieFile)) @unlink($this->sessionCookieFile);
        $this->sessionCookieFile = null;
        if (function_exists('gc_collect_cycles')) @gc_collect_cycles();
    }

    public function sendHttpRequest(string $url, array $customHeaders = []): array
    {
        if (is_callable($this->httpHandler)) return ($this->httpHandler)($url, $customHeaders);

        $ch = curl_init();
        $headers = array_merge([
            'User-Agent: ' . $this->userAgent,
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7',
            'Upgrade-Insecure-Requests: 1',
        ], $customHeaders);

        $opts = [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 4,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => 4,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_ENCODING       => '',
        ];

        if (!empty($this->sessionCookieFile) && file_exists($this->sessionCookieFile)) {
            $opts[CURLOPT_COOKIEJAR] = $this->sessionCookieFile;
            $opts[CURLOPT_COOKIEFILE] = $this->sessionCookieFile;
        }

        curl_setopt_array($ch, $opts);

        $body = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $effectiveUrl = (string)curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $error = curl_error($ch);
        curl_close($ch);

        return [
            'http_code'     => $httpCode,
            'body'          => (string)($body !== false ? $body : ''),
            'error'         => $error,
            'effective_url' => $effectiveUrl,
        ];
    }

    /**
     * 1. Google Maps & Local Business Verification
     */
    public function verifyGoogleMaps(string $companyName, ?string $address = null): array
    {
        $companyName = trim($companyName);
        if (empty($companyName)) return ['status' => 'unknown', 'detail' => ['message' => 'Tên doanh nghiệp trống']];

        $query = trim($companyName . ($address ? ' ' . trim($address) : '') . ' Vietnam');
        $searchUrl = 'https://www.google.com/search?q=' . urlencode($query . ' google maps');
        $response = $this->sendHttpRequest($searchUrl);
        $html = $response['body'];
        $httpCode = $response['http_code'];

        $res = [
            'status'           => 'unknown',
            'operating_status' => 'Chưa rõ trạng thái',
            'matched_name'     => $companyName,
            'matched_address'  => $address ?: '',
            'rating'           => null,
            'reviews_count'    => null,
            'signals'          => [],
            'url'              => $searchUrl,
            'http_code'        => $httpCode,
        ];

        if ($httpCode !== 200 || empty($html)) {
            $res['error'] = $response['error'] ?: 'Không nhận được phản hồi từ máy chủ Google';
            return ['status' => 'unknown', 'detail' => $res];
        }

        $lower = mb_strtolower($html, 'UTF-8');

        // Check Rating & Reviews
        if (preg_match('/(?:rating|xếp hạng|điểm)[:\s]*([0-5](?:\.[0-9])?)\s*(?:\/5|sao|stars)?/i', $html, $m)) $res['rating'] = $m[1];
        if (preg_match('/([0-9,.]+)\s*(?:bài đánh giá|đánh giá|reviews|review)/i', $html, $m)) $res['reviews_count'] = (int)str_replace([',', '.'], '', $m[1]);

        // Closed Signals
        $closedSignals = ['đã đóng cửa vĩnh viễn', 'đã đóng cửa', 'permanently closed', 'closed permanently', 'tạm thời đóng cửa', 'temporarily closed', 'đóng cửa tạm thời'];
        foreach ($closedSignals as $s) {
            if (strpos($lower, $s) !== false) {
                $res['status'] = 'closed';
                $res['operating_status'] = 'Đã đóng cửa / Ngừng hoạt động';
                $res['signals'][] = $s;
                return ['status' => 'closed', 'detail' => $res];
            }
        }

        // Active Signals
        $activeSignals = ['đang mở cửa', 'mở cửa', 'open now', 'bản đồ', 'địa chỉ:', 'xếp hạng:', 'rating', 'class="g"', 'data-hveid', 'chỉ đường', 'directions', 'website', 'trang web'];
        foreach ($activeSignals as $s) {
            if (strpos($lower, $s) !== false) $res['signals'][] = $s;
        }

        if (count($res['signals']) >= 2 || !empty($res['rating']) || strpos($lower, 'bản đồ') !== false) {
            $res['status'] = 'active';
            $res['operating_status'] = 'Đang hoạt động';
        } elseif (strpos($lower, 'không tìm thấy kết quả nào') !== false || strpos($lower, 'did not match any documents') !== false) {
            $res['status'] = 'not_found';
            $res['operating_status'] = 'Không tìm thấy địa điểm trên bản đồ';
        } else {
            $res['status'] = 'unknown';
            $res['operating_status'] = 'Chưa xác định rõ tín hiệu';
        }

        return ['status' => $res['status'], 'detail' => $res];
    }

    /**
     * 2. Corporate Website Crawling & Extraction (Services, Intro, Partners, Contacts)
     */
    public function verifyWebsite(?string $websiteUrl, ?int $memberId = null): array
    {
        $websiteUrl = trim((string)$websiteUrl);
        if (empty($websiteUrl)) return ['status' => 'unknown', 'detail' => ['message' => 'Chưa có thông tin Website']];
        if (!preg_match('~^https?://~i', $websiteUrl)) $websiteUrl = 'https://' . $websiteUrl;

        $response = $this->sendHttpRequest($websiteUrl);
        $httpCode = $response['http_code'];
        $html = $response['body'];

        if ($httpCode >= 200 && $httpCode < 400 && !empty($html)) {
            $title = '';
            if (preg_match('/<title[^>]*>(.*?)<\/title>/si', $html, $m)) $title = trim(html_entity_decode(strip_tags($m[1]), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));

            $metaDesc = '';
            if (preg_match('/<meta[^>]*name=["\'](?:description|og:description)["\'][^>]*content=["\']([^"\']*)["\']/si', $html, $m)) $metaDesc = trim(html_entity_decode($m[1], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));

            // 1. Phone numbers
            $phones = [];
            if (preg_match_all('/(?:\+84|0)(?:2\d{1,2}|3[2-9]|5[689]|7[06-9]|8[1-9]|9\d)[\s.\-]?\d{3,4}[\s.\-]?\d{3,4}/', $html, $m)) {
                $phones = array_values(array_unique(array_map(function($p) { return preg_replace('/[^\+0-9]/', '', $p); }, $m[0])));
            }

            // 2. Emails
            $emails = [];
            if (preg_match_all('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $html, $m)) {
                $excluded = ['example.com', 'domain.com', 'wixpress.com', 'sentry.io', 'email.com', 'google.com'];
                foreach (array_unique($m[0]) as $em) {
                    $domain = substr(strrchr($em, "@"), 1);
                    if (!in_array($domain, $excluded) && !preg_match('/\.(png|jpg|jpeg|gif|webp|svg|css|js)$/i', $em)) $emails[] = $em;
                }
            }

            // 3. Service types & Products
            $services = [];
            $serviceKws = ['vận tải đường biển', 'vận chuyển đường biển', 'vận tải hàng không', 'vận tải đường bộ', 'logistics', 'kho bãi', 'khai báo hải quan', 'fcl', 'lcl', 'forwarding', 'cước tàu', 'xuất nhập khẩu', 'chuyển phát nhanh', 'đóng gói hàng hóa', 'cho thuê container', 'thủ tục hải quan'];
            $lower = mb_strtolower($html, 'UTF-8');
            foreach ($serviceKws as $kw) {
                if (strpos($lower, $kw) !== false) $services[] = mb_convert_case($kw, MB_CASE_TITLE, 'UTF-8');
            }
            $services = array_values(array_unique($services));

            // 4. Company summary / intro
            $intro = $metaDesc;
            if (empty($intro) && preg_match('/<(?:p|div|section)[^>]*class=["\'][^"\']*(?:about|intro|gioi-thieu|summary)[^"\']*["\'][^>]*>(.*?)<\/(?:p|div|section)>/si', $html, $m)) {
                $intro = trim(preg_replace('/\s+/', ' ', strip_tags($m[1])));
            }

            // 5. Partners & Clients
            $partners = [];
            if (preg_match_all('/<(?:div|span|h\d|a|p)[^>]*class=["\'][^"\']*(?:partner|client|doi-tac|khach-hang)[^"\']*["\'][^>]*>(.*?)<\/(?:div|span|h\d|a|p)>/si', $html, $m)) {
                foreach (array_slice($m[1], 0, 8) as $rawP) {
                    $cleanedP = trim(strip_tags($rawP));
                    if (mb_strlen($cleanedP) >= 3 && mb_strlen($cleanedP) <= 60 && !preg_match('/[<>{}@]/', $cleanedP)) $partners[] = $cleanedP;
                }
            }
            $partners = array_values(array_unique($partners));

            // 6. Social Links
            $discoveredFb = '';
            if (preg_match('/href=["\'](https?:\/\/(?:www\.)?(?:facebook\.com|fb\.com)\/(?!sharer|share|plugins|policies|help)[a-zA-Z0-9._-]+)\/?["\']/i', $html, $m)) $discoveredFb = $m[1];

            $discoveredZalo = '';
            if (preg_match('/(?:https?:\/\/)?(?:zalo\.me|oa\.zalo\.me)\/([0-9a-zA-Z._-]+)/i', $html, $m)) $discoveredZalo = 'https://zalo.me/' . $m[1];

            $extractedData = [
                'company_name'       => $title,
                'page_title'         => $title,
                'company_intro'      => $intro ?: $metaDesc,
                'extracted_phones'   => $phones,
                'extracted_emails'   => $emails,
                'service_types'      => $services,
                'products_services'  => $services,
                'partners_clients'   => $partners,
                'discovered_fb'      => $discoveredFb,
                'discovered_zalo'    => $discoveredZalo,
            ];

            if ($memberId) {
                $this->enrichMemberData($memberId, [
                    'emails'        => $emails,
                    'phones'        => $phones,
                    'fanpage'       => $discoveredFb,
                    'meta_desc'     => $intro,
                    'website_title' => $title,
                ]);
            }

            return [
                'status' => 'active',
                'detail' => [
                    'url'            => $websiteUrl,
                    'http_code'      => $httpCode,
                    'extracted_data' => $extractedData,
                ]
            ];
        }

        return ['status' => 'unknown', 'detail' => ['url' => $websiteUrl, 'http_code' => $httpCode, 'error' => $response['error'] ?: 'Không kết nối được website']];
    }

    /**
     * 3. Facebook Fanpage Search & Information Extraction
     */
    public function verifyFanpage(?string $fanpageUrl, string $companyName, ?int $memberId = null): array
    {
        $fanpageUrl = trim((string)$fanpageUrl);
        if (empty($fanpageUrl)) {
            $discoveredUrl = $this->searchFanpageUrl($companyName);
            if ($discoveredUrl) {
                $fanpageUrl = $discoveredUrl;
                if ($memberId) $this->getMemberModel()->updateMember($memberId, ['fanpage' => $discoveredUrl]);
            } else {
                return ['status' => 'unknown', 'detail' => ['message' => 'Không tìm thấy URL Fanpage Facebook']];
            }
        }

        if (!preg_match('~^https?://~i', $fanpageUrl)) $fanpageUrl = 'https://' . $fanpageUrl;

        $response = $this->sendHttpRequest($fanpageUrl);
        $httpCode = $response['http_code'];
        $html = $response['body'];

        if ($httpCode >= 200 && $httpCode < 400 && !empty($html)) {
            $lower = mb_strtolower($html, 'UTF-8');
            if (strpos($lower, 'trang này không hiển thị') !== false || strpos($lower, 'this page isn\'t available') !== false) {
                return ['status' => 'not_found', 'detail' => ['url' => $fanpageUrl, 'signal' => 'Trang không tồn tại hoặc đã bị xóa']];
            }

            $title = '';
            if (preg_match('/<title[^>]*>(.*?)<\/title>/si', $html, $m)) $title = trim(html_entity_decode(strip_tags($m[1]), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));

            $likes = null;
            if (preg_match('/([0-9,.]+[KkMm]?)\s*(?:lượt thích|likes|người theo dõi|followers)/i', $html, $m)) $likes = $m[1];

            $phone = '';
            if (preg_match('/(?:\+84|0)(?:2\d{1,2}|3[2-9]|5[689]|7[06-9]|8[1-9]|9\d)[\s.\-]?\d{3,4}[\s.\-]?\d{3,4}/', $html, $m)) $phone = preg_replace('/[^\+0-9]/', '', $m[0]);

            return [
                'status' => 'active',
                'detail' => [
                    'url'             => $fanpageUrl,
                    'http_code'       => $httpCode,
                    'page_title'      => $title,
                    'likes_followers' => $likes,
                    'extracted_phone' => $phone,
                    'activity_status' => 'Trang hoạt động bình thường',
                ]
            ];
        }

        return ['status' => 'unknown', 'detail' => ['url' => $fanpageUrl, 'http_code' => $httpCode, 'error' => $response['error'] ?: 'Lỗi kết nối Facebook']];
    }

    protected function searchFanpageUrl(string $companyName): ?string
    {
        $companyName = trim($companyName);
        if (empty($companyName)) return null;

        $searchUrl = 'https://www.google.com/search?q=' . urlencode($companyName . ' site:facebook.com');
        $response = $this->sendHttpRequest($searchUrl);

        if ($response['http_code'] === 200 && !empty($response['body'])) {
            if (preg_match('/href=["\'](https?:\/\/(?:www\.)?(?:facebook\.com|fb\.com)\/(?!sharer|share|plugins|help|policies)[a-zA-Z0-9._-]+)\/?["\']/i', $response['body'], $m)) {
                return $m[1];
            }
        }
        return null;
    }

    /**
     * Auto-enrich member fields in Database if empty
     */
    protected function enrichMemberData(int $memberId, array $data): void
    {
        $member = $this->getMemberModel()->getMember($memberId);
        if (empty($member)) return;

        $updates = [];
        if (empty($member->fanpage) && !empty($data['fanpage'])) $updates['fanpage'] = $data['fanpage'];
        if (empty($member->email) && !empty($data['emails'][0])) $updates['email'] = $data['emails'][0];
        if (empty($member->phone) && !empty($data['phones'][0])) $updates['phone'] = $data['phones'][0];
        if (empty($member->note) && !empty($data['meta_desc'])) $updates['note'] = $data['meta_desc'];

        if (!empty($updates)) {
            $this->getMemberModel()->updateMember($memberId, $updates);
        }
    }

    /**
     * Execute full isolated verification lifecycle for a member
     */
    public function verifyMember(int $memberId): array
    {
        $memberModel = $this->getMemberModel();
        $logModel = $this->getLogModel();
        $member = $memberModel->getMember($memberId);

        if (empty($member)) return ['status' => 'error', 'message' => "Không tìm thấy hội viên ID $memberId"];

        $now = date('Y-m-d H:i:s');

        // STEP A: Open Clean Isolated Background Session
        $this->openIsolatedSession();

        try {
            // 1. Google Maps Check
            $mapsRes = $this->verifyGoogleMaps($member->company_name, $member->address);
            $logModel->addLog(['member_id' => $memberId, 'check_type' => 'google_maps', 'result' => $mapsRes['status'], 'detail' => $mapsRes['detail'], 'checked_at' => $now]);

            if ($this->sleepSeconds > 0) sleep($this->sleepSeconds);

            // 2. Website Crawling & Extra Data Extraction
            $webRes = ['status' => 'unknown', 'detail' => ['message' => 'Chưa có website']];
            if (!empty($member->website)) {
                $webRes = $this->verifyWebsite($member->website, $memberId);
                $logModel->addLog(['member_id' => $memberId, 'check_type' => 'website', 'result' => $webRes['status'], 'detail' => $webRes['detail'], 'checked_at' => $now]);
            }

            if ($this->sleepSeconds > 0) sleep($this->sleepSeconds);

            // 3. Fanpage Search & Verification
            $fanpageUrl = $member->fanpage ?: ($webRes['detail']['extracted_data']['discovered_fb'] ?? null);
            $fanRes = $this->verifyFanpage($fanpageUrl, $member->company_name, $memberId);
            if (!empty($fanpageUrl) || $fanRes['status'] !== 'unknown') {
                $logModel->addLog(['member_id' => $memberId, 'check_type' => 'fanpage', 'result' => $fanRes['status'], 'detail' => $fanRes['detail'], 'checked_at' => $now]);
            }

            // Aggregate Final Status & Reason
            $finalStatus = 'unverified';
            $reason = 'Chưa đủ dữ liệu để xác minh';
            if ($mapsRes['status'] === 'closed') {
                $finalStatus = 'failed';
                $reason = 'Phát hiện tín hiệu ngừng hoạt động trên Google Maps';
            } elseif ($mapsRes['status'] === 'active' || $webRes['status'] === 'active' || $fanRes['status'] === 'active') {
                $finalStatus = 'verified';
                $channelsActive = [];
                if ($mapsRes['status'] === 'active') $channelsActive[] = 'Google Maps (' . ($mapsRes['detail']['operating_status'] ?? 'Hoạt động') . ')';
                if ($webRes['status'] === 'active') $channelsActive[] = 'Website (Khả dụng)';
                if ($fanRes['status'] === 'active') $channelsActive[] = 'Facebook Fanpage (Hoạt động)';
                $reason = 'Xác minh thành công qua ' . implode(', ', $channelsActive);
            } elseif (!empty($member->tax_code) && strlen(trim($member->tax_code)) >= 8) {
                $finalStatus = 'verified';
                $reason = 'Doanh nghiệp có Mã Số Thuế hợp lệ: ' . $member->tax_code;
            }

            $memberModel->updateVerifyStatus($memberId, $finalStatus, $now);

            return [
                'status'         => $finalStatus,
                'reason'         => $reason,
                'maps_result'    => $mapsRes['status'],
                'web_result'     => $webRes['status'],
                'fanpage_result' => $fanRes['status'],
                'channels'       => [
                    'google_maps' => $mapsRes,
                    'website'     => $webRes,
                    'fanpage'     => $fanRes,
                ],
                'details'        => ['maps' => $mapsRes, 'website' => $webRes, 'fanpage' => $fanRes],
            ];
        } finally {
            // STEP B: Explicitly Close & Destroy Session & Memory
            $this->closeIsolatedSession();
        }
    }

    /**
     * Batch verification with strict sequential session isolation
     */
    public function verifyBatch(array $memberIds): array
    {
        $results = [];
        foreach ($memberIds as $idx => $id) {
            $id = (int)$id;
            if ($id <= 0) continue;
            $results[$id] = $this->verifyMember($id);
            if ($idx < count($memberIds) - 1 && $this->sleepSeconds > 0) sleep($this->sleepSeconds);
        }
        return $results;
    }

    /**
     * Fast non-blocking background trigger for newly saved/approved members
     */
    public static function triggerAsyncVerification(array $memberIds): void
    {
        if (empty($memberIds)) return;
        $idsStr = implode(',', array_map('intval', $memberIds));
        
        $url = base_url('api/verify-pending-members?ids=' . urlencode($idsStr) . '&token=topbestglobal_verify_' . date('Ymd'));
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_TIMEOUT_MS     => 250,
            CURLOPT_NOSIGNAL       => 1,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        @curl_exec($ch);
        @curl_close($ch);
    }
}
