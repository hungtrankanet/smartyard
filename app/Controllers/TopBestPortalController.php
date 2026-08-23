<?php

namespace App\Controllers;

use Config\TopBestData;
use App\Models\CategoryModel;
use App\Models\PostModel;
use App\Models\PageModel;
use App\Services\TopBestNewsSyncService;

class TopBestPortalController extends BaseController
{
    protected function ensureInitialized()
    {
        if (empty($this->generalSettings)) {
            $this->generalSettings = \Config\Globals::$generalSettings;
        }
        if (empty($this->settings)) {
            $this->settings = \Config\Globals::$settings;
        }
        if (empty($this->activeLang)) {
            $this->activeLang = \Config\Globals::$activeLang;
        }
    }

    public function index()
    {
        $this->ensureInitialized();
        // Auto-sync official categories and articles into DB if not present
        TopBestNewsSyncService::syncDefaultContent();

        $isEn = ($this->activeLang->short_form ?? 'vi') == 'en';
        $langId = $this->activeLang->id ?? 1;

        // Fetch dynamic Hero news & Category clusters from Backend DB with fallback
        list($featuredNews, $secondaryNews) = $this->fetchDynamicHeroNews($langId);
        $categoryClusters = $this->fetchDynamicCategoryClusters($langId);

        $directoryPreviews = array_slice(TopBestData::getDirectoryProfiles(), 0, 5);
        $announcements = TopBestData::getOfficialAnnouncements();
        $interactivePoll = TopBestData::getInteractivePoll();
        $adSpaces = TopBestData::getAdSpaces();

        $data = [
            'title'             => $isEn ? 'TOP BEST GLOBAL — Official National Honors & Verification Portal' : 'TOP BEST GLOBAL — Cổng Thông Tin & Xác Minh Huy Hiệu Quốc Gia',
            'description'       => !empty($this->settings->site_description) ? esc($this->settings->site_description) : 'Cổng thông tin chính thức chương trình TOP BEST thuộc Hội Kỷ lục Việt Nam (VietKings) & GAA uỷ thác cho TOLUCK triển khai.',
            'keywords'          => !empty($this->settings->keywords) ? esc($this->settings->keywords) : 'top best global, vietkings, worldkings, gaa, toluck, bang vang vinh danh, xac minh huy hieu',
            'featuredNews'      => $featuredNews,
            'secondaryNews'     => $secondaryNews,
            'allNews'           => TopBestData::getNewsArticles(),
            'categoryClusters'  => $categoryClusters,
            'announcements'     => $announcements,
            'interactivePoll'   => $interactivePoll,
            'adSpaces'          => $adSpaces,
            'directoryPreviews' => $directoryPreviews,
            'industries'        => TopBestData::getIndustries(),
            'provinces'         => TopBestData::getProvinces(),
            'generalSettings'   => $this->generalSettings,
            'baseSettings'      => $this->settings,
            'userSession'       => getUserSession()
        ];

        return loadView('partials/_header', $data)
            . loadView('index', $data)
            . loadView('partials/_footer', $data);
    }

    private function fetchDynamicHeroNews($langId): array
    {
        try {
            $posts = $this->postModel->getLatestPosts($langId, 4, 0);
            if (!empty($posts) && count($posts) > 0) {
                $p0 = $posts[0];
                $featured = [
                    'id'         => $p0->id,
                    'title'      => $p0->title,
                    'slug'       => $p0->slug,
                    'image'      => getPostImage($p0, 'big'),
                    'category'   => $p0->category_name ?? 'Tin vinh danh',
                    'created_at' => formattedDate($p0->created_at),
                    'summary'    => $p0->summary,
                    'url'        => generatePostURL($p0)
                ];
                $secondary = [];
                for ($i = 1; $i < min(4, count($posts)); $i++) {
                    $p = $posts[$i];
                    $secondary[] = [
                        'id'         => $p->id,
                        'title'      => $p->title,
                        'slug'       => $p->slug,
                        'image'      => getPostImage($p, 'mid'),
                        'category'   => $p->category_name ?? 'Tin tức',
                        'created_at' => formattedDate($p->created_at),
                        'summary'    => $p->summary,
                        'url'        => generatePostURL($p)
                    ];
                }
                return [$featured, $secondary];
            }
        } catch (\Throwable $e) {
            // DB fallback
        }

        $newsList = TopBestData::getNewsArticles();
        return [$newsList[0] ?? null, array_slice($newsList, 1, 3)];
    }

    private function fetchDynamicCategoryClusters($langId): array
    {
        try {
            $categoryModel = new CategoryModel();
            $dbCategories = $categoryModel->where('parent_id', 0)
                ->where('category_status', 1)
                ->where('lang_id', $langId)
                ->orderBy('category_order', 'ASC')
                ->get()->getResult();

            if (!empty($dbCategories)) {
                $clusters = [];
                foreach ($dbCategories as $cat) {
                    $catPosts = $this->postModel->where('category_id', $cat->id)
                        ->where('status', 1)
                        ->where('visibility', 1)
                        ->where('is_scheduled', 0)
                        ->orderBy('created_at', 'DESC')
                        ->limit(4)
                        ->get()->getResult();

                    if (!empty($catPosts)) {
                        $fPost = $catPosts[0];
                        $featured = [
                            'title'   => $fPost->title,
                            'slug'    => $fPost->slug,
                            'image'   => getPostImage($fPost, 'big'),
                            'date'    => formattedDate($fPost->created_at),
                            'badge'   => $cat->name,
                            'summary' => $fPost->summary,
                            'url'     => generatePostURL($fPost)
                        ];

                        $subPosts = [];
                        for ($k = 1; $k < count($catPosts); $k++) {
                            $sp = $catPosts[$k];
                            $subPosts[] = [
                                'title' => $sp->title,
                                'slug'  => $sp->slug,
                                'date'  => formattedDate($sp->created_at),
                                'badge' => $cat->name,
                                'url'   => generatePostURL($sp)
                            ];
                        }

                        $clusters[] = [
                            'id'        => $cat->id,
                            'name'      => $cat->name,
                            'slug'      => $cat->slug,
                            'url'       => generateCategoryURL($cat),
                            'icon'      => !empty($cat->block_type) ? $cat->block_type : 'fa-folder-open',
                            'priority'  => $cat->category_order,
                            'featured'  => $featured,
                            'sub_posts' => $subPosts
                        ];
                    }
                }
                if (!empty($clusters)) {
                    return $clusters;
                }
            }
        } catch (\Throwable $e) {
            // DB fallback
        }

        return TopBestData::getCategoryClusters();
    }

    public function aboutTopBest()
    {
        $this->ensureInitialized();
        $isEn = ($this->activeLang->short_form ?? 'vi') == 'en';
        
        $directoryPreviews = [];
        try {
            $db = \Config\Database::connect();
            if ($db->tableExists('members')) {
                $dbMembers = $db->table('members')
                    ->where('status', 1)
                    ->orderBy('id', 'DESC')
                    ->limit(6)
                    ->get()->getResultArray();
                if (!empty($dbMembers)) {
                    foreach ($dbMembers as $m) {
                        $directoryPreviews[] = [
                            'code'          => $m['member_code'] ?? ('TBG-VN-2026-' . str_pad($m['id'], 3, '0', STR_PAD_LEFT)),
                            'name'          => $m['company_name'] ?? ($m['name'] ?? 'Doanh Nghiệp'),
                            'rank_tier'     => ($m['membership_tier'] ?? 'Diamond') == 'Diamond' ? 'BEST' : 'TOP',
                            'rank_number'   => $m['id'] ?? 1,
                            'category_name' => $m['business_sector'] ?? 'Đa ngành',
                            'province'      => $m['province'] ?? 'Toàn quốc'
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {
            // DB fallback
        }

        if (empty($directoryPreviews)) {
            $directoryPreviews = array_slice(TopBestData::getDirectoryProfiles(), 0, 6);
        }

        $siteDesc = !empty($this->settings->site_description) ? esc($this->settings->site_description) : 'Tìm hiểu chi tiết về chương trình TOP BEST: 4 ví dụ quốc tế, 8 nhóm ngành, cơ chế TOP/BEST, hệ sinh thái VietKings x GAA x TOLUCK và lộ trình 12 tháng.';
        $keywords = !empty($this->settings->keywords) ? esc($this->settings->keywords) : 'top best la gi, quy che top best, vietkings, gaa, toluck, tran kim hung, tieu chuan ky luc';

        $data = [
            'title'             => $isEn ? 'What is TOP BEST? — Complete Program Guide & Regulations' : 'TOP BEST Là Gì? — Toàn Bộ Định Nghĩa, Cơ Chế & Quy Chuẩn Pháp Lý',
            'description'       => $siteDesc,
            'keywords'          => $keywords,
            'industries'        => TopBestData::getIndustries(),
            'directoryPreviews' => $directoryPreviews,
            'faqs'              => TopBestData::getFaqs(),
            'generalSettings'   => $this->generalSettings,
            'baseSettings'      => $this->settings,
            'userSession'       => getUserSession()
        ];

        return loadView('partials/_header', $data)
            . loadView('topbest_about', $data)
            . loadView('partials/_footer', $data);
    }

    public function events()
    {
        $this->ensureInitialized();
        $isEn = ($this->activeLang->short_form ?? 'vi') == 'en';
        $data = [
            'title'           => $isEn ? 'Events & National Award Ceremonies | TOP BEST GLOBAL' : 'Sự Kiện & Lễ Vinh Danh Quốc Gia | TOP BEST GLOBAL',
            'description'     => 'Chuỗi sự kiện quý 4 lần/năm và Đại lễ Gala vinh danh TOP BEST thường niên tôn vinh các thương hiệu di sản xuất sắc.',
            'keywords'        => 'su kien top best, gala vinh danh, hoi ky luc viet nam, le trao giai',
            'generalSettings' => $this->generalSettings,
            'baseSettings'    => $this->settings,
            'userSession'     => getUserSession()
        ];

        return loadView('partials/_header', $data)
            . loadView('events', $data)
            . loadView('partials/_footer', $data);
    }

    public function aboutUs()
    {
        $this->ensureInitialized();
        $isEn = ($this->activeLang->short_form ?? 'vi') == 'en';
        $data = [
            'title'           => $isEn ? 'About Us — VietKings × GAA × TOLUCK Ecosystem' : 'Về Chúng Tôi — Hệ Sinh Thái VietKings × GAA × TOLUCK',
            'description'     => 'Giới thiệu các đơn vị đồng hành sáng lập và triển khai chương trình TOP BEST: Hội Kỷ lục Việt Nam (VietKings), GAA và TOLUCK.',
            'keywords'        => 'vietkings, gaa, toluck, tran kim hung, tran thi hong duyen, ban to chuc top best',
            'generalSettings' => $this->generalSettings,
            'baseSettings'    => $this->settings,
            'userSession'     => getUserSession()
        ];

        return loadView('partials/_header', $data)
            . loadView('about', $data)
            . loadView('partials/_footer', $data);
    }

    public function contact()
    {
        $this->ensureInitialized();
        $isEn = ($this->activeLang->short_form ?? 'vi') == 'en';
        $data = [
            'title'           => $isEn ? 'Contact Program Secretariat — TOP BEST GLOBAL' : 'Liên Hệ Ban Thư Ký Chương Trình — TOP BEST GLOBAL',
            'description'     => 'Thông tin liên hệ, đường dây nóng tiếp nhận hồ sơ và hỗ trợ doanh nghiệp của Ban Thư ký TOP BEST GLOBAL.',
            'keywords'        => 'lien he top best, hotline vietkings, ban thu ky toluck',
            'generalSettings' => $this->generalSettings,
            'baseSettings'    => $this->settings,
            'userSession'     => getUserSession()
        ];

        return loadView('partials/_header', $data)
            . loadView('contact', $data)
            . loadView('partials/_footer', $data);
    }
}
