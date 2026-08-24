<?php

namespace App\Controllers;

use Config\TopBestData;
use Config\Globals;
use App\Models\CategoryModel;
use App\Models\PostModel;
use App\Models\PageModel;
use App\Services\TopBestNewsSyncService;

class TopBestPortalController extends BaseController
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

    public function index()
    {
        try {
            $this->ensureInitialized();
            TopBestNewsSyncService::syncDefaultContent();

            $isEn = ($this->activeLang->short_form ?? 'vi') == 'en';
            $langId = $this->activeLang->id ?? 1;

            list($featuredNews, $secondaryNews) = $this->fetchDynamicHeroNews($langId);
            $categoryClusters = $this->fetchDynamicCategoryClusters($langId);

            $data = [
                'title'             => $isEn ? 'TOP BEST GLOBAL — Official National Honors & Verification Portal' : 'TOP BEST GLOBAL — Cổng Thông Tin & Xác Minh Huy Hiệu Quốc Gia',
                'description'       => !empty($this->settings->site_description) ? esc($this->settings->site_description) : 'Cổng thông tin chính thức chương trình TOP BEST thuộc Hội Kỷ lục Việt Nam (VietKings) & GAA uỷ thác cho TOLUCK triển khai.',
                'keywords'          => !empty($this->settings->keywords) ? esc($this->settings->keywords) : 'top best global, vietkings, worldkings, gaa, toluck, bang vang vinh danh, xac minh huy hieu',
                'featuredNews'      => $featuredNews,
                'secondaryNews'     => $secondaryNews,
                'allNews'           => TopBestData::getNewsArticles(),
                'categoryClusters'  => $categoryClusters,
                'announcements'     => TopBestData::getOfficialAnnouncements(),
                'interactivePoll'   => TopBestData::getInteractivePoll(),
                'adSpaces'          => TopBestData::getAdSpaces(),
                'directoryPreviews' => array_slice(TopBestData::getDirectoryProfiles(), 0, 5),
                'industries'        => TopBestData::getIndustries(),
                'provinces'         => TopBestData::getProvinces(),
                'generalSettings'   => $this->generalSettings,
                'baseSettings'      => $this->settings,
                'userSession'       => getUserSession()
            ];

            return loadView('partials/_header', $data)
                . loadView('index', $data)
                . loadView('partials/_footer', $data);
        } catch (\Throwable $e) {
            log_message('error', 'TopBestPortalController::index error: ' . $e->getMessage());
            return $this->renderFallbackPage('Trang Chủ');
        }
    }

    private function fetchDynamicHeroNews($langId): array
    {
        try {
            $postModel = new PostModel();
            $dbPosts = $postModel->getLatestPosts($langId, 4);
            if (!empty($dbPosts) && count($dbPosts) >= 1) {
                $featured = null;
                $secondary = [];
                foreach ($dbPosts as $idx => $p) {
                    $item = [
                        'id'       => $p->id,
                        'title'    => $p->title,
                        'summary'  => $p->summary,
                        'badge'    => !empty($p->category_name) ? $p->category_name : 'TIN NỔI BẬT',
                        'image'    => getPostImage($p, 'big'),
                        'date'     => formattedDate($p->created_at),
                        'author'   => !empty($p->author_username) ? $p->author_username : 'Ban Thư Ký TOP BEST',
                        'url'      => generatePostURL($p)
                    ];
                    if ($idx === 0) {
                        $featured = $item;
                    } else {
                        $secondary[] = $item;
                    }
                }
                return [$featured, $secondary];
            }
        } catch (\Throwable $e) {
            log_message('error', 'fetchDynamicHeroNews error: ' . $e->getMessage());
        }

        $allNews = TopBestData::getNewsArticles();
        $featured = $allNews[0] ?? [];
        $secondary = array_slice($allNews, 1, 3);
        return [$featured, $secondary];
    }

    private function fetchDynamicCategoryClusters($langId): array
    {
        try {
            $catModel = new CategoryModel();
            $postModel = new PostModel();
            $categories = $catModel->getParentCategoriesByLang($langId);

            if (!empty($categories)) {
                $clusters = [];
                foreach ($categories as $cat) {
                    $categoryTree = getCategoryTree($cat->id, $categories);
                    $posts = $postModel->getPostsByCategoryPaginated($cat->id, $categoryTree, 4, 0);

                    if (!empty($posts)) {
                        $featured = null;
                        $subPosts = [];
                        foreach ($posts as $idx => $p) {
                            $item = [
                                'id'      => $p->id,
                                'title'   => $p->title,
                                'summary' => $p->summary,
                                'badge'   => !empty($p->category_name) ? $p->category_name : $cat->name,
                                'image'   => getPostImage($p, 'mid'),
                                'date'    => formattedDate($p->created_at),
                                'author'  => !empty($p->author_username) ? $p->author_username : 'Ban Thư Ký',
                                'url'     => generatePostURL($p)
                            ];
                            if ($idx === 0) {
                                $featured = $item;
                            } else {
                                $subPosts[] = $item;
                            }
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
            log_message('error', 'fetchDynamicCategoryClusters error: ' . $e->getMessage());
        }

        return TopBestData::getCategoryClusters();
    }

    public function aboutTopBest()
    {
        try {
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
        } catch (\Throwable $e) {
            log_message('error', 'TopBestPortalController::aboutTopBest error: ' . $e->getMessage());
            return $this->renderFallbackPage('TOP BEST Là Gì');
        }
    }

    public function events()
    {
        try {
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
        } catch (\Throwable $e) {
            log_message('error', 'TopBestPortalController::events error: ' . $e->getMessage());
            return $this->renderFallbackPage('Sự Kiện');
        }
    }

    public function aboutUs()
    {
        try {
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
        } catch (\Throwable $e) {
            log_message('error', 'TopBestPortalController::aboutUs error: ' . $e->getMessage());
            return $this->renderFallbackPage('Về Chúng Tôi');
        }
    }

    public function contact()
    {
        try {
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
        } catch (\Throwable $e) {
            log_message('error', 'TopBestPortalController::contact error: ' . $e->getMessage());
            return $this->renderFallbackPage('Liên Hệ');
        }
    }

    private function renderFallbackPage(string $title): string
    {
        $data = [
            'title'           => $title . ' | TOP BEST GLOBAL',
            'generalSettings' => $this->generalSettings ?? Globals::$generalSettings,
            'baseSettings'    => $this->settings ?? Globals::$settings,
            'userSession'     => getUserSession()
        ];
        return loadView('partials/_header', $data)
            . '<div class="container py-5 text-center"><h2 class="font-serif text-primary mb-3">' . esc($title) . '</h2><p class="text-muted">Đang cập nhật dữ liệu...</p><a href="' . langBaseUrl() . '" class="btn btn-tbg-cta">Về Trang Chủ</a></div>'
            . loadView('partials/_footer', $data);
    }
}
