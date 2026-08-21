<?php

namespace App\Controllers;

use Config\TopBestData;
use App\Models\PageModel;

class TopBestPortalController extends BaseController
{
    public function index()
    {
        $isEn = ($this->activeLang->short_form ?? 'vi') == 'en';
        $newsList = TopBestData::getNewsArticles();
        $featuredNews = $newsList[0] ?? null;
        $secondaryNews = array_slice($newsList, 1, 3);
        $directoryPreviews = array_slice(TopBestData::getDirectoryProfiles(), 0, 5);

        $data = [
            'title'             => $isEn ? 'TOP BEST GLOBAL — Official National Honors & Verification Portal' : 'TOP BEST GLOBAL — Cổng Thông Tin & Xác Minh Huy Hiệu Quốc Gia',
            'description'       => 'Cổng thông tin chính thức chương trình TOP BEST thuộc Hội Kỷ lục Việt Nam (VietKings) & GAA uỷ thác cho TOLUCK triển khai.',
            'keywords'          => 'top best global, vietkings, worldkings, gaa, toluck, bang vang vinh danh, xac minh huy hieu',
            'featuredNews'      => $featuredNews,
            'secondaryNews'     => $secondaryNews,
            'allNews'           => $newsList,
            'directoryPreviews' => $directoryPreviews,
            'industries'        => TopBestData::getIndustries(),
            'provinces'         => TopBestData::getProvinces(),
            'userSession'       => getUserSession()
        ];

        return loadView('partials/_header', $data)
            . loadView('index', $data)
            . loadView('partials/_footer', $data);
    }

    public function aboutTopBest()
    {
        $isEn = ($this->activeLang->short_form ?? 'vi') == 'en';
        $data = [
            'title'             => $isEn ? 'What is TOP BEST? — Complete Program Guide & Regulations' : 'TOP BEST Là Gì? — Toàn Bộ Định Nghĩa, Cơ Chế & Quy Chuẩn Pháp Lý',
            'description'       => 'Tìm hiểu chi tiết về chương trình TOP BEST: 4 ví dụ quốc tế, 8 nhóm ngành, cơ chế TOP/BEST, hệ sinh thái VietKings x GAA x TOLUCK và lộ trình 12 tháng.',
            'keywords'          => 'top best la gi, quy che top best, vietkings, gaa, toluck, tran kim hung, tieu chuan ky luc',
            'industries'        => TopBestData::getIndustries(),
            'directoryPreviews' => array_slice(TopBestData::getDirectoryProfiles(), 0, 6),
            'faqs'              => TopBestData::getFaqs(),
            'userSession'       => getUserSession()
        ];

        return loadView('partials/_header', $data)
            . loadView('topbest_about', $data)
            . loadView('partials/_footer', $data);
    }

    public function events()
    {
        $isEn = ($this->activeLang->short_form ?? 'vi') == 'en';
        $data = [
            'title'       => $isEn ? 'Events & National Award Ceremonies | TOP BEST GLOBAL' : 'Sự Kiện & Lễ Vinh Danh Quốc Gia | TOP BEST GLOBAL',
            'description' => 'Chuỗi sự kiện quý 4 lần/năm và Đại lễ Gala vinh danh TOP BEST thường niên tôn vinh các thương hiệu di sản xuất sắc.',
            'keywords'    => 'su kien top best, gala vinh danh, hoi ky luc viet nam, le trao giai',
            'userSession' => getUserSession()
        ];

        return loadView('partials/_header', $data)
            . loadView('events', $data)
            . loadView('partials/_footer', $data);
    }

    public function aboutUs()
    {
        $isEn = ($this->activeLang->short_form ?? 'vi') == 'en';
        $data = [
            'title'       => $isEn ? 'About Us — VietKings × GAA × TOLUCK Ecosystem' : 'Về Chúng Tôi — Hệ Sinh Thái VietKings × GAA × TOLUCK',
            'description' => 'Giới thiệu các đơn vị đồng hành sáng lập và triển khai chương trình TOP BEST: Hội Kỷ lục Việt Nam (VietKings), GAA và TOLUCK.',
            'keywords'    => 'vietkings, gaa, toluck, tran kim hung, tran thi hong duyen, ban to chuc top best',
            'userSession' => getUserSession()
        ];

        return loadView('partials/_header', $data)
            . loadView('topbest_about', $data)
            . loadView('partials/_footer', $data);
    }

    public function contact()
    {
        $isEn = ($this->activeLang->short_form ?? 'vi') == 'en';
        $data = [
            'title'       => $isEn ? 'Contact TOP BEST GLOBAL Organizing Committee' : 'Liên Hệ Ban Tổ Chức TOP BEST GLOBAL',
            'description' => 'Thông tin liên hệ Ban Thư ký Chương trình TOP BEST GLOBAL (TOLUCK × VietKings × GAA).',
            'keywords'    => 'lien he top best, hotline gaa, ban thu ky vietkings, van phong toluck',
            'userSession' => getUserSession()
        ];

        return loadView('partials/_header', $data)
            . loadView('contact', $data)
            . loadView('partials/_footer', $data);
    }
}
