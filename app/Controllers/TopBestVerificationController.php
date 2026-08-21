<?php

namespace App\Controllers;

use Config\TopBestData;

class TopBestVerificationController extends BaseController
{
    public function index()
    {
        $isEn = ($this->activeLang->short_form ?? 'vi') == 'en';
        $code = trim($this->request->getGet('code') ?? '');
        $profile = null;
        $searched = false;

        if (!empty($code)) {
            $searched = true;
            $allProfiles = TopBestData::getDirectoryProfiles();
            foreach ($allProfiles as $p) {
                if (strcasecmp($p['code'], $code) === 0 || stripos($p['name'], $code) !== false) {
                    $profile = $p;
                    break;
                }
            }
        }

        $data = [
            'title'       => $isEn ? 'Official Badge Verification & Anti-Counterfeiting Portal — TOP BEST GLOBAL' : 'Cổng Tra Cứu & Xác Minh Huy Hiệu Chính Hãng — TOP BEST GLOBAL',
            'description' => 'Xác minh tính xác thực của Huy hiệu và Chứng nhận số TOP BEST GLOBAL bằng mã định danh hoặc quét mã QR từ bao bì/POSM.',
            'keywords'    => 'xac minh huy hieu, verify badge, tra cuu qr code, top best global, chong gia mao',
            'searched'    => $searched,
            'searchCode'  => $code,
            'profile'     => $profile,
            'userSession' => getUserSession()
        ];

        return loadView('partials/_header', $data)
            . loadView('verify_badge', $data)
            . loadView('partials/_footer', $data);
    }

    public function verifyCode($code = null)
    {
        $code = trim($code ?? '');
        return redirect()->to(langBaseUrl('verify?code=' . urlencode($code)));
    }
}
