<?php

namespace App\Controllers;

use Config\TopBestData;
use Config\Globals;

class TopBestRegistrationController extends BaseController
{
    protected function ensureInitialized()
    {
        if (empty($this->generalSettings)) {
            $this->generalSettings = Globals::$generalSettings;
        }
        if (empty($this->settings)) {
            $this->settings = Globals::$settings;
        }
        if (empty($this->activeLang)) {
            $this->activeLang = Globals::$activeLang;
        }
    }

    public function businessRegister()
    {
        try {
            $this->ensureInitialized();
            $isEn = ($this->activeLang->short_form ?? 'vi') == 'en';
            $data = [
                'title'          => $isEn ? 'Nomination & Registration for Enterprises/Cooperatives — TOP BEST GLOBAL' : 'Đăng Ký Đề Cử Dành Cho Doanh Nghiệp & Hợp Tác Xã — TOP BEST GLOBAL',
                'description'    => 'Quy trình đăng ký thẩm định 5 bước minh bạch, tự động gợi ý tên hồ sơ theo quy chuẩn TOP BEST, hoàn phí 100% nếu không đạt chuẩn.',
                'keywords'       => 'dang ky top best, de cu doanh nghiep, ho so vietkings, hop tac xa, tham dinh',
                'industries'     => TopBestData::getIndustries(),
                'provinces'      => TopBestData::getProvinces(),
                'generalSettings'=> $this->generalSettings,
                'baseSettings'   => $this->settings,
                'userSession'    => getUserSession()
            ];

            return loadView('partials/_header', $data)
                . loadView('business_register', $data)
                . loadView('partials/_footer', $data);
        } catch (\Throwable $e) {
            log_message('error', 'businessRegister error: ' . $e->getMessage());
            return redirect()->to(langBaseUrl());
        }
    }

    public function submitBusiness()
    {
        $session = session();
        $post = $this->request->getPost();
        
        $companyName = trim($post['company_name'] ?? '');
        $phone = trim($post['phone'] ?? '');
        $email = trim($post['email'] ?? '');

        if (empty($companyName) || empty($phone) || empty($email)) {
            $session->setFlashdata('error', 'Vui lòng điền đầy đủ các trường thông tin bắt buộc.');
            return redirect()->to(langBaseUrl('doanh-nghiep/dang-ky'));
        }

        // Save consultation request (Step 1 of 12-step process - NO payment online)
        $session->setFlashdata('success', 'Đã tiếp nhận hồ sơ đề cử của ' . esc($companyName) . '. Ban Thư ký TOP BEST GLOBAL sẽ liên hệ tư vấn và xác định phạm vi trong vòng 24h làm việc.');
        return redirect()->to(langBaseUrl('doanh-nghiep/dang-ky?status=success'));
    }

    public function agency()
    {
        try {
            $this->ensureInitialized();
            $isEn = ($this->activeLang->short_form ?? 'vi') == 'en';
            $data = [
                'title'          => $isEn ? 'Authorized Agency Partner Program — TOP BEST GLOBAL' : 'Chương Trình Đối Tác & Đại Lý Uỷ Quyền — TOP BEST GLOBAL',
                'description'    => 'Cơ chế chính sách quyền lợi, hoa hồng và phát triển thị trường dành cho Đại lý uỷ quyền TOP BEST GLOBAL trên toàn quốc.',
                'keywords'       => 'dai ly top best, doi tac uy quyen, hoa hong dai ly, phat trien thi truong',
                'provinces'      => TopBestData::getProvinces(),
                'generalSettings'=> $this->generalSettings,
                'baseSettings'   => $this->settings,
                'userSession'    => getUserSession()
            ];

            return loadView('partials/_header', $data)
                . loadView('agency', $data)
                . loadView('partials/_footer', $data);
        } catch (\Throwable $e) {
            log_message('error', 'agency error: ' . $e->getMessage());
            return redirect()->to(langBaseUrl());
        }
    }

    public function submitAgency()
    {
        $session = session();
        $post = $this->request->getPost();
        
        $agencyName = trim($post['agency_name'] ?? '');
        $phone = trim($post['phone'] ?? '');
        $email = trim($post['email'] ?? '');

        if (empty($agencyName) || empty($phone) || empty($email)) {
            $session->setFlashdata('error', 'Vui lòng điền đầy đủ các thông tin bắt buộc.');
            return redirect()->to(langBaseUrl('dai-ly'));
        }

        $session->setFlashdata('success', 'Đã tiếp nhận đăng ký Đại lý của ' . esc($agencyName) . '. Ban Phát triển Đối tác sẽ liên hệ xác minh và gửi thoả thuận trong vòng 24h.');
        return redirect()->to(langBaseUrl('dai-ly?status=success'));
    }
}
