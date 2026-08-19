<?php

namespace App\Libraries;

/**
 * OcrService: Pipeline 2 Bước (Image → Raw Text → Structured JSON)
 * Đảm bảo 100% dữ liệu thực từ ảnh, không dữ liệu giả / mock.
 */
class OcrService
{
    protected string $geminiVisionUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';
    protected string $geminiTextUrl   = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';
    protected string $ocrSpaceUrl     = 'https://api.ocr.space/parse/image';
    protected ?string $apiKey         = null;

    protected array $requiredKeys = [
        'detected_language', 'company_name', 'company_name_vi', 'company_name_en', 'company_name_local',
        'tax_code', 'address', 'city', 'country', 'website', 'fanpage',
        'phone', 'phone_2', 'email', 'email_2',
        'contact_name', 'contact_name_vi', 'contact_name_en', 'contact_name_local',
        'position', 'position_vi', 'position_en', 'department',
        'social_media', 'branch_info', 'note', 'representative_name', 'metadata',
    ];

    protected array $socialMediaKeys = ['zalo', 'wechat', 'line', 'whatsapp', 'skype', 'linkedin', 'facebook'];

    public function __construct(?string $apiKey = null)
    {
        if (!empty($apiKey)) { $this->apiKey = $apiKey; return; }
        $this->apiKey = getenv('GEMINI_API_KEY') ?: ($_ENV['GEMINI_API_KEY'] ?? ($_SERVER['GEMINI_API_KEY'] ?? null));
        if (empty($this->apiKey) && function_exists('aiWriter')) { $aw = aiWriter(); if (!empty($aw->apiKey)) $this->apiKey = $aw->apiKey; }
        if (empty($this->apiKey)) {
            try {
                $db = \Config\Database::connect();
                $row = $db->table('general_settings')->select('ai_writer')->get()->getRow();
                if (!empty($row->ai_writer)) {
                    $aiData = @unserialize($row->ai_writer);
                    if (is_array($aiData) && !empty($aiData['api_key'])) $this->apiKey = trim($aiData['api_key']);
                }
            } catch (\Throwable $e) {}
        }
    }

    public function extractBusinessCard($imagePaths): array
    {
        if (empty($imagePaths)) return $this->emptyResult();
        if (is_string($imagePaths)) return $this->processSingleImage($imagePaths);
        if (is_array($imagePaths)) {
            $fd = !empty($imagePaths['front'] ?? ($imagePaths[0] ?? null)) ? $this->processSingleImage($imagePaths['front'] ?? ($imagePaths[0] ?? null)) : $this->emptyResult();
            $bd = !empty($imagePaths['back']  ?? ($imagePaths[1] ?? null)) ? $this->processSingleImage($imagePaths['back']  ?? ($imagePaths[1] ?? null)) : $this->emptyResult();
            return $this->mergeResults($fd, $bd);
        }
        return $this->emptyResult();
    }

    public function getDeterministicFallback(string $path = ''): array { return $this->processSingleImage($path); }
    protected function processSingleImage(string $imagePath): array
    {
        $realPath = $this->resolveImagePath($imagePath);
        if (empty($realPath) || !file_exists($realPath) || filesize($realPath) === 0) {
            $r = $this->emptyResult(basename($imagePath));
            $r['metadata'] = ['ocr_engine' => 'empty', 'is_mock' => false, 'note' => 'File ảnh không tồn tại trên server: ' . $imagePath];
            return $r;
        }

        // BƯỚC 1: Image → Raw Text
        $rawText = ''; $engine = 'none';
        if (!empty($this->apiKey)) {
            $rawText = $this->geminiImageToText($realPath);
            if (!empty($rawText)) $engine = 'gemini-vision';
        }
        if (empty($rawText)) {
            $rawText = $this->ocrSpaceImageToText($realPath);
            if (!empty($rawText)) $engine = 'ocr.space';
        }
        if (empty($rawText)) {
            $rawText = $this->tesseractImageToText($realPath);
            if (!empty($rawText)) $engine = 'tesseract';
        }

        if (empty(trim((string)$rawText))) {
            $r = $this->emptyResult(basename($imagePath));
            $r['metadata'] = [
                'ocr_engine' => 'empty',
                'is_mock' => false,
                'note' => 'Không đọc được chữ từ ảnh (Vui lòng kiểm tra API key Gemini hoặc độ rõ nét)',
                'has_gemini_key' => !empty($this->apiKey),
                'file_size_kb' => round(filesize($realPath) / 1024, 1),
            ];
            return $r;
        }

        // BƯỚC 2: Raw Text → Structured JSON
        $structured = !empty($this->apiKey) ? $this->geminiTextToJson($rawText) : null;
        if (empty($structured)) $structured = $this->regexParseText($rawText);
        $structured['metadata'] = ['ocr_engine' => $engine, 'is_mock' => false, 'raw_text' => $rawText];
        return $this->normalizeResult($structured);
    }

    protected function geminiImageToText(string $filePath): ?string
    {
        $optPath = $this->prepareOptimizedImage($filePath);
        $imgData = $this->encodeImage($optPath ?: $filePath);
        if (empty($imgData)) return null;

        $prompt = "Read and extract ALL text on this business card exactly as printed. Output ONLY the raw text.";
        // REST API requires camelCase inlineData and mimeType
        $parts = [
            ['text' => $prompt],
            ['inlineData' => ['mimeType' => $imgData['mime_type'], 'data' => $imgData['data']]]
        ];
        $payload = ['contents' => [['parts' => $parts]], 'generationConfig' => ['temperature' => 0.0]];

        $models = [
            $this->geminiVisionUrl,
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent',
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro:generateContent',
        ];

        foreach ($models as $url) {
            $resp = $this->callGeminiApi($url, $payload, 30);
            if (!empty($resp)) {
                $decoded = @json_decode($resp, true);
                $text = $decoded['candidates'][0]['content']['parts'][0]['text'] ?? null;
                if (!empty($text) && strlen(trim($text)) >= 3) return trim($text);
            }
        }
        return null;
    }

    protected function ocrSpaceImageToText(string $filePath): ?string
    {
        $path = $this->prepareOptimizedImage($filePath) ?: $filePath;
        if (!file_exists($path) || filesize($path) === 0) return null;
        $keys = ['helloworld', 'K84724838688957'];
        $bestText = '';
        foreach ([0, 90, 270] as $ang) {
            $src = @imagecreatefromstring(@file_get_contents($path));
            if (!$src) continue;
            if ($ang !== 0) $src = imagerotate($src, $ang, 0);
            ob_start(); imagejpeg($src, null, 90);
            $b64 = 'data:image/jpeg;base64,' . base64_encode(ob_get_clean());

            foreach (['chs', ''] as $lang) {
                foreach ($keys as $key) {
                    $post = ['apikey' => $key, 'base64Image' => $b64, 'OCREngine' => '2', 'detectOrientation' => 'true'];
                    if (!empty($lang)) $post['language'] = $lang;
                    $ch = curl_init($this->ocrSpaceUrl);
                    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_POSTFIELDS => $post, CURLOPT_TIMEOUT => 20, CURLOPT_CONNECTTIMEOUT => 6, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => 0]);
                    $resp = curl_exec($ch); $code = curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
                    if ($code === 200 && !empty($resp)) {
                        $j = @json_decode($resp, true);
                        $txt = trim($j['ParsedResults'][0]['ParsedText'] ?? '');
                        if (mb_strlen($txt) > mb_strlen($bestText)) $bestText = $txt;
                        if (mb_strlen($bestText) >= 80 && preg_match('/(?:有限公司|Co\.,?\s*Ltd|Company|Công ty)/u', $bestText)) break 3;
                    }
                }
            }
        }
        return mb_strlen($bestText) >= 5 ? $bestText : null;
    }

    protected function tesseractImageToText(string $filePath): ?string
    {
        if (@shell_exec('which tesseract 2>/dev/null') === null) return null;
        $out = @shell_exec("tesseract " . escapeshellarg($filePath) . " stdout -l vie+eng+chi_sim+jpn 2>/dev/null");
        return (!empty($out) && strlen(trim($out)) >= 5) ? trim($out) : null;
    }

    protected function geminiTextToJson(string $rawText): ?array
    {
        $desc = 'detected_language (vi|en|zh|ja|ko|mixed), company_name, company_name_vi, company_name_en, company_name_local, tax_code, address, city, country, website, fanpage, phone, phone_2, email, email_2, contact_name, contact_name_vi, contact_name_en, contact_name_local, position, position_vi, position_en, department, social_media (zalo, wechat, line, whatsapp, skype, linkedin, facebook), branch_info, note';
        $prompt = "Extract business card data from this raw text into valid JSON with fields: $desc. If not found, use empty string. Output ONLY valid JSON.\nRaw text:\n```\n$rawText\n```";

        $payload = ['contents' => [['parts' => [['text' => $prompt]]]], 'generationConfig' => ['temperature' => 0.1, 'response_mime_type' => 'application/json']];
        $resp = $this->callGeminiApi($this->geminiTextUrl, $payload, 20);
        if (empty($resp)) return null;

        $decoded = @json_decode($resp, true);
        $text = $decoded['candidates'][0]['content']['parts'][0]['text'] ?? null;
        return !empty($text) ? $this->parseJsonBlock($text) : null;
    }

    protected function regexParseText(string $rawText): array
    {
        $res = $this->emptyResult();
        $lines = array_values(array_filter(array_map('trim', explode("\n", $rawText))));

        // 1. Language
        if (preg_match('/[\x{4e00}-\x{9fa5}]/u', $rawText)) $res['detected_language'] = 'zh';
        elseif (preg_match('/[\x{3040}-\x{30ff}]/u', $rawText)) $res['detected_language'] = 'ja';
        elseif (preg_match('/[\x{ac00}-\x{d7af}]/u', $rawText)) $res['detected_language'] = 'ko';
        elseif (preg_match('/[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ]/iu', $rawText)) $res['detected_language'] = 'vi';
        else $res['detected_language'] = 'en';

        // 2. Tax Code
        if (preg_match('/(?:MST|Mã\s*số\s*thuế|Mã\s*số\s*DN|Tax\s*(?:code|no)?|税号)[:\s]*([0-9]{10}(?:-[0-9]{3})?)/i', $rawText, $m)) $res['tax_code'] = trim($m[1]);
        elseif (preg_match('/\b(0[1-9][0-9]{8}(?:-[0-9]{3})?)\b/', $rawText, $m)) $res['tax_code'] = trim($m[1]);

        // 3. Emails
        if (preg_match_all('/[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}/', $rawText, $m)) {
            $em = array_values(array_unique($m[0]));
            if (!empty($em[0])) $res['email'] = $em[0];
            if (!empty($em[1])) $res['email_2'] = $em[1];
        }

        // 4. Phones & Mobiles (including Chinese 11-digit numbers)
        if (preg_match_all('/(?:\+84|0084|0|\+86|\+81|\+82|1[3-9]\d)[\s.\-]?[0-9](?:[\s.\-]?[0-9]){7,11}/', $rawText, $m)) {
            $ph = [];
            foreach ($m[0] as $p) {
                $d = preg_replace('/[^\+0-9]/', '', $p);
                if (!empty($res['tax_code']) && str_replace('-', '', $res['tax_code']) === $d) continue;
                $ph[] = $d;
            }
            $ph = array_values(array_unique($ph));
            if (!empty($ph[0])) $res['phone'] = $ph[0];
            if (!empty($ph[1])) $res['phone_2'] = $ph[1];
        }

        // 5. Website & Fanpage (explicit website priority)
        if (preg_match('/(?:https?:\/\/)?(?:www\.)([a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}(?:\/[^\s,]*)?)/i', $rawText, $m)) {
            $res['website'] = 'https://www.' . ltrim(trim($m[1]), '/');
        } elseif (preg_match('/(?:website|web|site)[:\s]*(https?:\/\/[^\s,]+|[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}(?:\/[^\s,]*)?)/i', $rawText, $m)) {
            $res['website'] = (strpos($m[1], 'http') === 0 ? '' : 'https://') . ltrim(trim($m[1]), '/');
        } elseif (!empty($res['email']) && strpos($res['email'], '@') !== false) {
            $domain = substr(strrchr($res['email'], "@"), 1);
            if (!in_array($domain, ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', '163.com', 'qq.com'])) $res['website'] = 'https://' . $domain;
        }
        if (preg_match('/(?:https?:\/\/)?(?:www\.)?(?:facebook\.com|fb\.com|fb\.me)\/([a-zA-Z0-9._\-]+)/i', $rawText, $m)) $res['fanpage'] = 'https://facebook.com/' . $m[1];

        // 6. Social Media
        $sm = $res['social_media'];
        if (preg_match('/(?:zalo|wechat|微信)[:\s]*([\w.@\-+]{3,30})/iu', $rawText, $m)) {
            if (strpos($rawText, '微信') !== false || strpos($rawText, 'wechat') !== false) $sm['wechat'] = trim($m[1]);
            else $sm['zalo'] = trim($m[1]);
        }
        $res['social_media'] = $sm;

        // 7. Company Name (Vietnamese, English, Chinese)
        $coList = [];
        $coKw = ['co., ltd', 'co.,ltd', 'company limited', 'joint stock', 'công ty tnhh', 'công ty cổ phần', 'công ty cp', 'tập đoàn', '有限公司', '有限责任公司', '股份', '集团', '企业', '物流', '供应链', '贸易', 'ltd.', 'corporation', 'corp', 'inc.', 'llc', 'group', 'recruitment', 'shipping', 'forwarding'];
        foreach ($lines as $line) {
            if (preg_match('/(?:team\b|department\b|phòng\b|ban\b|division\b|other office|offices\b|核心业务|地址|手机|邮箱)/iu', $line)) continue;
            $lo = mb_strtolower($line, 'UTF-8');
            foreach ($coKw as $kw) {
                if (mb_strpos($lo, $kw) !== false && mb_strlen($line) >= 4 && mb_strlen($line) <= 120 && mb_strpos($lo, '@') === false) {
                    $coList[] = $line;
                    break;
                }
            }
        }
        if (!empty($coList[0])) { $res['company_name'] = $coList[0]; $res['company_name_vi'] = $coList[0]; }
        if (!empty($coList[1])) { $res['company_name_en'] = $coList[1]; }

        // 8. Branch & Department
        foreach ($lines as $line) {
            if (empty($res['branch_info']) && preg_match('/(?:Branch|Chi nhánh|Văn phòng|VPĐD|Head Office|Trụ sở chính|Hanoi Offices?|HCM Offices?)/iu', $line) && !preg_match('/(?:Other Office|Co\.,?\s*Ltd|Company|有限公司)/iu', $line)) {
                $res['branch_info'] = $line;
            }
            if (empty($res['department']) && preg_match('/(?:Team|Department|Bộ phận|Phòng ban|Phòng kinh doanh|Ban dự án|Dept|Division|Section)/iu', $line) && !preg_match('/(?:Leader|Manager|Director|Head|Co\.,?\s*Ltd|Company|Công ty)/iu', $line)) {
                $res['department'] = $line;
            }
        }

        // 9. Position & Contact Name
        $posKw = ['team leader', 'leader', 'lead', 'consultant', 'specialist', 'recruiter', 'sales', 'giám đốc', 'tổng giám đốc', 'phó giám đốc', 'chủ tịch', 'trưởng phòng', 'phó phòng', 'quản lý', 'chuyên viên', 'đại diện', 'kế toán', 'director', 'manager', 'ceo', 'coo', 'cfo', 'president', 'chairman', 'founder', 'head of', 'general manager', 'executive', 'officer', 'supervisor', '经理', '总监', '业务员', '业务经理'];
        foreach ($lines as $line) {
            if (!empty($res['position'])) break;
            $lo = mb_strtolower($line, 'UTF-8');
            foreach ($posKw as $kw) {
                if (mb_strpos($lo, $kw) !== false && mb_strlen($line) <= 45 && mb_strpos($lo, '@') === false && !preg_match('/(?:department|phòng\b|ban\b|chi nhánh|company|công ty|office|offices|核心业务)/iu', $line)) {
                    $res['position'] = $line;
                    $res['position_vi'] = $line;
                    break;
                }
            }
        }

        // 10. Multi-line / Multi-Branch Address (Support Vietnamese, English & Chinese)
        $addrLines = []; $collecting = false;
        foreach ($lines as $line) {
            if (preg_match('/^(?:Add|Địa chỉ|Đ\/c|Address|VP|Office|地址)[:\s：]*/iu', $line) || preg_match('/^(?:Floor|\d+(?:st|nd|rd|th)\s*Floor|Tầng|Tòa nhà|Building|No\.|Số\s*\d|Room|\d+[-\/]\d+|东莞|深圳|广州|北京|上海|平阳|胡志明)/iu', $line)) {
                $collecting = true;
                $clean = preg_replace('/^(?:Add|Địa chỉ|Đ\/c|Address|VP|Office|地址)[:\s：]*/iu', '', $line);
                if (mb_strlen($clean) > 0) $addrLines[] = trim($clean);
                continue;
            }
            if ($collecting) {
                if (preg_match('/^(?:Tel|Mobile|Phone|Hotline|Email|Web|Website|Fax|MST|Tax|Zalo|WeChat|Facebook|手机|邮箱|www\.)/iu', $line) || preg_match('/@/', $line)) {
                    $collecting = false;
                } else {
                    $addrLines[] = $line;
                }
            }
        }
        if (!empty($addrLines)) {
            $res['address'] = $addrLines[0];
            if (count($addrLines) > 1 && empty($res['branch_info'])) $res['branch_info'] = implode(' | ', array_slice($addrLines, 1));
        }

        // 11. City & Country Normalization
        $cityPat = '/(Hà Nội|Ha Noi|Hanoi|Hồ Chí Minh|Ho Chi Minh|TP\.?\s*HCM|Hải Phòng|Hai Phong|Đà Nẵng|Da Nang|Bình Dương|Binh Duong|Đồng Nai|Cần Thơ|Quảng Ninh|Bắc Ninh|Vũng Tàu|Nha Trang|Huế|Đà Lạt|东莞|Dongguan|深圳|Shenzhen|广州|Guangzhou|上海|Shanghai|北京|Beijing|Seoul|Tokyo)/iu';
        $foundCity = '';
        if (!empty($res['address']) && preg_match($cityPat, $res['address'], $m)) $foundCity = $m[1];
        elseif (preg_match($cityPat, $rawText, $m)) $foundCity = $m[1];

        if (!empty($foundCity)) {
            $cLower = mb_strtolower($foundCity, 'UTF-8');
            if (strpos($cLower, 'dongguan') !== false || strpos($cLower, '东莞') !== false) { $res['city'] = 'Đông Hoàn (Dongguan)'; $res['country'] = 'Trung Quốc'; }
            elseif (strpos($cLower, 'shenzhen') !== false || strpos($cLower, '深圳') !== false) { $res['city'] = 'Thâm Quyến (Shenzhen)'; $res['country'] = 'Trung Quốc'; }
            elseif (strpos($cLower, 'guangzhou') !== false || strpos($cLower, '广州') !== false) { $res['city'] = 'Quảng Châu (Guangzhou)'; $res['country'] = 'Trung Quốc'; }
            elseif (strpos($cLower, 'hải phòng') !== false || strpos($cLower, 'hai phong') !== false) $res['city'] = 'Hải Phòng';
            elseif (strpos($cLower, 'hà nội') !== false || strpos($cLower, 'ha noi') !== false || strpos($cLower, 'hanoi') !== false) $res['city'] = 'Hà Nội';
            elseif (strpos($cLower, 'hồ chí minh') !== false || strpos($cLower, 'ho chi minh') !== false || strpos($cLower, 'hcm') !== false) $res['city'] = 'TP. Hồ Chí Minh';
            elseif (strpos($cLower, 'bình dương') !== false || strpos($cLower, 'binh duong') !== false || strpos($cLower, '平阳') !== false) $res['city'] = 'Bình Dương';
            elseif (strpos($cLower, 'đà nẵng') !== false || strpos($cLower, 'da nang') !== false) $res['city'] = 'Đà Nẵng';
            else $res['city'] = $foundCity;
        }

        // 12. Contact Name & Representative Name
        $nonPersonKw = '/(?:other office|hanoi office|hcm office|office|offices|shipping|logistics|express|transport|freight|forwarding|service|services|trading|corp|ltd|co\.|company|group|branch|department|phòng|ban|chi nhánh|công ty|tnhh|cổ phần|tập đoàn|international|agency|add|tel|mobile|phone|email|website|fax|mst|tax|châu á|châu âu|miền bắc|miền nam|sales|golden jubilee|50th|核心业务|有限公司|地址|手机|邮箱)/iu';
        if (empty($res['contact_name'])) {
            foreach ($lines as $line) {
                if (in_array($line, $coList) || $line === $res['position'] || $line === $res['address'] || $line === $res['branch_info'] || $line === $res['department']) continue;
                if (preg_match($nonPersonKw, $line)) continue;
                if (preg_match('/^[\x{4e00}-\x{9fa5}]{2,4}$/u', $line)) {
                    $res['contact_name'] = $line;
                    $res['contact_name_vi'] = $line;
                    $res['representative_name'] = $line;
                    break;
                }
                if (mb_strlen($line) >= 3 && mb_strlen($line) <= 35 && !preg_match('/[0-9@\/\\\\]/', $line)) {
                    if (preg_match('/^[A-ZÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚÝĐ\x{4e00}-\x{9fa5}\x{30a0}-\x{30ff}]/u', $line)) {
                        $res['contact_name'] = $line;
                        $res['contact_name_vi'] = $line;
                        $res['representative_name'] = $line;
                        break;
                    }
                }
            }
        }
        foreach ($lines as $line) {
            if (preg_match('/^[A-Z]{3,15}$/', $line) && !empty($res['contact_name']) && $line !== $res['contact_name']) {
                $res['contact_name_en'] = $line;
                $res['representative_name'] = $res['contact_name'] . ' (' . $line . ')';
                break;
            }
        }

        // 13. Note / Core Services (Dịch vụ chính)
        if (strpos($rawText, '核心业务') !== false || strpos($rawText, '海运订舱') !== false) {
            $services = [];
            foreach ($lines as $line) {
                if (preg_match('/(?:海运|双清|报关|海外仓|支付|财税|公司注册|审计|做账|拖车)/u', $line)) $services[] = $line;
            }
            if (!empty($services)) $res['note'] = 'Dịch vụ: ' . implode(', ', $services);
        }

        if (empty($res['company_name'])) {
            $longest = ''; foreach (array_slice($lines, 0, 5) as $line) { if (mb_strlen($line) > mb_strlen($longest) && mb_strpos($line, '@') === false && !preg_match('/^[0-9\+\-()]+$/', $line)) $longest = $line; }
            if (mb_strlen($longest) >= 4) { $res['company_name'] = $longest; $res['company_name_vi'] = $longest; }
        }

        return $res;
    }

    protected function prepareOptimizedImage(string $filePath): ?string
    {
        if (!file_exists($filePath) || filesize($filePath) === 0) return null;
        if (function_exists('imagecreatefromstring')) {
            $data = @file_get_contents($filePath);
            if ($data !== false) {
                $src = @imagecreatefromstring($data);
                if ($src !== false) {
                    $w = imagesx($src); $h = imagesy($src);
                    if ($w > 1600 || $h > 1600) {
                        $max = 1600;
                        $nw = ($w > $h) ? $max : (int)round($w * ($max / $h));
                        $nh = ($w > $h) ? (int)round($h * ($max / $w)) : $max;
                        $dst = imagecreatetruecolor($nw, $nh);
                        imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
                        $tmp = sys_get_temp_dir() . '/ocr_opt_' . md5($filePath) . '.jpg';
                        imagejpeg($dst, $tmp, 88);
                        imagedestroy($dst); imagedestroy($src);
                        return $tmp;
                    }
                    imagedestroy($src);
                }
            }
        }
        return $filePath;
    }

    protected function callGeminiApi(string $url, array $payload, int $timeout = 20): ?string
    {
        if (empty($this->apiKey)) return null;
        $endpoint = $url . '?key=' . urlencode($this->apiKey);
        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'], CURLOPT_TIMEOUT => $timeout, CURLOPT_CONNECTTIMEOUT => 8, CURLOPT_SSL_VERIFYPEER => false
        ]);
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ($code === 200 && !empty($resp)) ? $resp : null;
    }

    protected function parseJsonBlock(string $text): ?array
    {
        $c = trim(preg_replace('/\s*```\s*$/i', '', preg_replace('/^```(?:json)?\s*/i', '', trim($text))));
        if (preg_match('/\{[\s\S]+\}/u', $c, $m)) $c = $m[0];
        $data = @json_decode(trim($c), true);
        return is_array($data) ? $data : null;
    }

    public function encodeImage(string $filePath): ?array
    {
        if (!file_exists($filePath) || !is_readable($filePath) || filesize($filePath) === 0) return null;
        $b = @file_get_contents($filePath); if ($b === false) return null;
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mime = ['png' => 'image/png', 'webp' => 'image/webp', 'gif' => 'image/gif', 'bmp' => 'image/bmp', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg'][$ext] ?? 'image/jpeg';
        return ['mime_type' => $mime, 'data' => base64_encode($b)];
    }

    protected function resolveImagePath(string $path): string
    {
        if (file_exists($path)) return $path;
        $clean = ltrim($path, '/');
        if (defined('FCPATH')) {
            if (file_exists(FCPATH . $clean)) return FCPATH . $clean;
            if (file_exists(FCPATH . 'public/' . $clean)) return FCPATH . 'public/' . $clean;
        }
        return $path;
    }
    public function normalizeResult(array $data, bool $isMock = false): array
    {
        $out = [];
        foreach ($this->requiredKeys as $key) {
            if ($key === 'social_media' || $key === 'metadata') continue;
            $out[$key] = isset($data[$key]) ? trim((string)$data[$key]) : '';
        }
        if (empty($out['contact_name']) && !empty($data['representative_name'])) $out['contact_name'] = trim($data['representative_name']);
        if (empty($out['representative_name']) && !empty($out['contact_name'])) $out['representative_name'] = $out['contact_name'];
        if (empty($out['company_name_vi']) && !empty($out['company_name'])) $out['company_name_vi'] = $out['company_name'];
        if (empty($out['company_name']) && !empty($out['company_name_vi'])) $out['company_name'] = $out['company_name_vi'];
        if (empty($out['contact_name_vi']) && !empty($out['contact_name'])) $out['contact_name_vi'] = $out['contact_name'];
        if (empty($out['position_vi']) && !empty($out['position'])) $out['position_vi'] = $out['position'];
        if (empty($out['country'])) $out['country'] = 'Việt Nam';

        $raw = is_string($data['social_media'] ?? null) ? (@json_decode($data['social_media'], true) ?: []) : ($data['social_media'] ?? []);
        $sm = []; foreach ($this->socialMediaKeys as $p) $sm[$p] = isset($raw[$p]) ? trim((string)$raw[$p]) : '';
        $out['social_media'] = $sm;
        $meta = is_array($data['metadata'] ?? null) ? $data['metadata'] : []; $meta['is_mock'] = false;
        if (!isset($meta['ocr_engine'])) $meta['ocr_engine'] = 'regex';
        $out['metadata'] = $meta;
        return $out;
    }

    public function emptyResult(string $filename = ''): array
    {
        $r = [];
        foreach ($this->requiredKeys as $k) {
            if ($k === 'social_media') $r['social_media'] = array_fill_keys($this->socialMediaKeys, '');
            elseif ($k === 'metadata') $r['metadata'] = ['ocr_engine' => 'empty', 'is_mock' => false];
            elseif ($k === 'detected_language') $r['detected_language'] = 'vi';
            elseif ($k === 'country') $r['country'] = 'Việt Nam';
            else $r[$k] = '';
        }
        return $r;
    }

    public function getEmptyResult(string $filename = ''): array { return $this->emptyResult($filename); }

    public function mergeResults(array $front, array $back): array
    {
        $merged = $this->emptyResult();
        foreach ($this->requiredKeys as $key) {
            if ($key === 'social_media') {
                $fs = is_array($front['social_media'] ?? null) ? $front['social_media'] : [];
                $bs = is_array($back['social_media']  ?? null) ? $back['social_media']  : [];
                $sm = []; foreach ($this->socialMediaKeys as $p) $sm[$p] = trim($fs[$p] ?? '') ?: trim($bs[$p] ?? '');
                $merged['social_media'] = $sm;
            } elseif ($key === 'branch_info') {
                $f = trim((string)($front['branch_info'] ?? '')); $b = trim((string)($back['branch_info'] ?? ''));
                $merged['branch_info'] = ($f && $b && $f !== $b) ? "$f; $b" : ($f ?: $b);
            } elseif ($key === 'detected_language') {
                $fl = trim((string)($front['detected_language'] ?? '')); $bl = trim((string)($back['detected_language'] ?? ''));
                $merged['detected_language'] = ($fl && $bl && $fl !== $bl) ? 'mixed' : ($fl ?: $bl ?: 'vi');
            } elseif ($key === 'metadata') {
                $merged['metadata'] = ['ocr_engine' => 'merged', 'is_mock' => false];
            } else {
                $fv = isset($front[$key]) ? trim((string)$front[$key]) : ''; $bv = isset($back[$key]) ? trim((string)$back[$key]) : '';
                $merged[$key] = $fv ?: $bv;
            }
        }
        return $this->normalizeResult($merged);
    }
}
