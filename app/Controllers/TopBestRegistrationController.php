<?php

namespace App\Controllers;

use Config\TopBestData;

class TopBestRegistrationController extends BaseController
{
    public function businessRegister()
    {
        $isEn = ($this->activeLang->short_form ?? 'vi') == 'en';
        $data = [
            'title'       => $isEn ? 'Nomination & Registration for Enterprises/Cooperatives — TOP BEST GLOBAL' : 'Đăng Ký Đề Cử Dành Cho Doanh Nghiệp & Hợp Tác Xã — TOP BEST GLOBAL',
            'description' => 'Quy trình đăng ký thẩm định 5 bước minh bạch, tự động gợi ý tên hồ sơ theo quy chuẩn TOP BEST, hoàn phí 100% nếu không đạt chuẩn.',
            'keywords'    => 'dang ky top best, de cu doanh nghiep, ho so vietkings, hop tac xa, tham dinh',
            'industries'  => TopBestData::getIndustries(),
            'provinces'   => TopBestData::getProvinces(),
            'userSession' => getUserSession()
        ];

        return loadView('partials/_header', $data)
            . loadView('business_register', $data)
            . loadView('partials/_footer', $data);
    }

    public function submitBusiness()
    {
        $session = session();
        $post = $this->request->getPost();
        
        $level = $post['registration_level'] ?? 'brand';
        $companyName = trim($post['company_name'] ?? '');
        $repName = trim($post['rep_name'] ?? '');
        $phone = trim($post['phone'] ?? '');
        $email = trim($post['email'] ?? '');
        $industry = $post['industry'] ?? '';
        $province = $post['province'] ?? '';
        $formulaTitle = trim($post['formula_title'] ?? '');

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
        $isEn = ($this->activeLang->short_form ?? 'vi') == 'en';
        $data = [
            'title'       => $isEn ? 'Authorized Agency Partner Program — TOP BEST GLOBAL' : 'Chương Trình Đối Tác & Đại Lý Uỷ Quyền — TOP BEST GLOBAL',
            'description' => 'Cơ chế chính sách quyền lợi, hoa hồng và phát triển thị trường dành cho Đại lý uỷ quyền TOP BEST GLOBAL trên toàn quốc.',
            'keywords'    => 'dai ly top best, doi tac uy quyen, hoa hong dai ly, phat trien thi truong',
            'provinces'   => TopBestData::getProvinces(),
            'userSession' => getUserSession()
        ];

        return loadView('partials/_header', $data)
            . loadView('agency', $data)
            . loadView('partials/_footer', $data);
    }

    public function submitAgency()
    {
        $session = session();
        $post = $this->request->getPost();
        $name = trim($post['agency_name'] ?? '');
        $phone = trim($post['phone'] ?? '');
        $email = trim($post['email'] ?? '');
        $province = $post['province'] ?? '';

        if (empty($name) || empty($phone)) {
            $session->setFlashdata('error', 'Vui lòng điền họ tên và số điện thoại liên hệ.');
            return redirect()->to(langBaseUrl('dai-ly'));
        }

        $session->setFlashdata('success', 'Cảm ơn bạn đã đăng ký làm Đại lý uỷ quyền. Phòng Phát triển Đối tác TOLUCK sẽ liên hệ gửi hợp đồng đại lý và tài liệu đào tạo.');
        return redirect()->to(langBaseUrl('dai-ly?status=success'));
    }
}
