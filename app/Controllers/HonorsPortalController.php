<?php

namespace App\Controllers;

use App\Models\AwardCategoryModel;
use App\Models\AwardSeasonModel;
use App\Models\NominationCandidateModel;
use App\Models\PostModel;
use App\Models\EventModel;

/**
 * HonorsPortalController
 * Master Controller for TOP BEST GLOBAL National Honors & Voting Portal.
 *
 * Strict Compliance: <= 500 lines
 */
class HonorsPortalController extends BaseController
{
    protected $seasonModel;
    protected $categoryModel;
    protected $candidateModel;
    protected $postModel;
    protected $eventModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        $this->seasonModel    = new AwardSeasonModel();
        $this->categoryModel  = new AwardCategoryModel();
        $this->candidateModel = new NominationCandidateModel();
        $this->postModel      = new PostModel();
        $this->eventModel     = new EventModel();
    }

    /**
     * National Honors Portal Homepage
     */
    public function index()
    {
        $activeSeason       = $this->seasonModel->getActiveSeason();
        $seasonId           = (int)($activeSeason->id ?? 1);
        $categories         = $this->categoryModel->getCategoriesBySeason($seasonId);
        $categoriesGrouped  = $this->categoryModel->getCategoriesGroupedBySector($seasonId);
        $featuredCandidates = $this->candidateModel->getFeaturedCandidates(6);
        $recentNews         = $this->postModel->getPosts(4);
        $upcomingEvents     = $this->eventModel->getUpcomingEvents(3);

        $isEn = ($this->activeLang->short_form ?? 'vi') === 'en';
        $data = [
            'title'              => $isEn ? 'TOP BEST GLOBAL — National Honors & Awards Portal 2026' : 'TOP BEST GLOBAL — Cổng Thông Tin & Bảng Vàng Vinh Danh Thương Hiệu Quốc Gia',
            'description'        => $isEn ? 'Vietnam premier digital platform for national honors, verified public & expert voting, and awarding prestigious brand excellence.' : 'Cổng thông tin, tin tức sự kiện, bảng vàng vinh danh và trao giải thưởng danh giá hàng đầu Việt Nam cho tất cả các ngành nghề.',
            'keywords'           => 'top best global, vinh danh thương hiệu, cúp vàng 2026, giải thưởng quốc gia, bảng vàng danh dự, bình chọn uy tín',
            'activeSeason'       => $activeSeason,
            'categories'         => $categories,
            'categoriesGrouped'  => $categoriesGrouped,
            'featuredCandidates' => $featuredCandidates,
            'recentNews'         => $recentNews,
            'upcomingEvents'     => $upcomingEvents,
        ];

        return loadThemeView('honors/index', $data);
    }

    /**
     * Award Categories Catalog
     */
    public function categories()
    {
        $activeSeason      = $this->seasonModel->getActiveSeason();
        $seasonId          = (int)($activeSeason->id ?? 1);
        $categories        = $this->categoryModel->getCategoriesBySeason($seasonId);
        $categoriesGrouped = $this->categoryModel->getCategoriesGroupedBySector($seasonId);

        $isEn = ($this->activeLang->short_form ?? 'vi') === 'en';
        $data = [
            'title'             => $isEn ? 'Award Categories Catalog | TOP BEST GLOBAL' : 'Danh Mục Hạng Mục Vinh Danh Quốc Gia | TOP BEST GLOBAL',
            'description'       => $isEn ? 'Explore standardized national award categories across technology, industry, healthcare, commerce, and education.' : 'Khám phá hệ thống danh mục giải thưởng quốc gia chuẩn hóa trên 15+ lĩnh vực kinh tế - xã hội, công nghệ và phát triển bền vững.',
            'keywords'          => 'danh mục giải thưởng, hạng mục vinh danh, top best global categories, giải thưởng thương hiệu',
            'activeSeason'      => $activeSeason,
            'categories'        => $categories,
            'categoriesGrouped' => $categoriesGrouped,
        ];

        return loadThemeView('honors/categories', $data);
    }

    /**
     * Category Detail View
     */
    public function categoryDetail(string $slug)
    {
        $activeSeason = $this->seasonModel->getActiveSeason();
        $seasonId     = (int)($activeSeason->id ?? 1);
        $category     = $this->categoryModel->getCategoryBySlug($slug, $seasonId);

        if (!$category) {
            $category = $this->categoryModel->getCategoryBySlug($slug);
        }

        if (!$category) {
            return redirect()->to(langBaseUrl('honors/categories'));
        }

        $candidates = $this->candidateModel->getCandidatesForVoting((int)$category->id, $seasonId);
        $categories = $this->categoryModel->getCategoriesBySeason($seasonId);

        $isEn = ($this->activeLang->short_form ?? 'vi') === 'en';
        $data = [
            'title'        => ($isEn ? 'Award Category: ' : 'Hạng Mục Vinh Danh: ') . esc($category->name) . ' | TOP BEST GLOBAL',
            'description'  => esc($category->description ?? $category->name),
            'keywords'     => 'hạng mục ' . esc($category->name) . ', giải thưởng quốc gia, top best global',
            'activeSeason' => $activeSeason,
            'category'     => $category,
            'candidates'   => $candidates,
            'categories'   => $categories,
        ];

        return loadThemeView('honors/category_detail', $data);
    }

    /**
     * Award Seasons Directory
     */
    public function seasons()
    {
        $seasons      = $this->seasonModel->getSeasons(20);
        $activeSeason = $this->seasonModel->getActiveSeason();

        $isEn = ($this->activeLang->short_form ?? 'vi') === 'en';
        $data = [
            'title'        => $isEn ? 'National Honors Seasons & Editions | TOP BEST GLOBAL' : 'Các Mùa Giải Vinh Danh Quốc Gia | TOP BEST GLOBAL',
            'description'  => $isEn ? 'History and archives of national honors seasons and gala ceremonies.' : 'Lịch sử và hành trình các mùa giải vinh danh thương hiệu uy tín, cúp vàng và lễ trao giải quốc gia.',
            'keywords'     => 'mùa giải vinh danh, top best global seasons, gala vinh danh, cúp vàng thương hiệu',
            'seasons'      => $seasons,
            'activeSeason' => $activeSeason,
        ];

        return loadThemeView('honors/seasons', $data);
    }

    /**
     * Season Detail
     */
    public function seasonDetail($seasonId)
    {
        $season = $this->seasonModel->getSeason((int)$seasonId);
        if (!$season) {
            return redirect()->to(langBaseUrl('honors/seasons'));
        }

        $categories = $this->categoryModel->getCategoriesBySeason((int)$season->id);

        $data = [
            'title'        => 'Mùa Giải ' . esc($season->title) . ' (' . esc($season->theme_year) . ') | TOP BEST GLOBAL',
            'description'  => esc($season->description ?? $season->title),
            'season'       => $season,
            'categories'   => $categories,
            'activeSeason' => $season,
        ];

        return loadThemeView('honors/season_detail', $data);
    }

    /**
     * About National Honors Portal
     */
    public function about()
    {
        $activeSeason = $this->seasonModel->getActiveSeason();

        $isEn = ($this->activeLang->short_form ?? 'vi') === 'en';
        $data = [
            'title'        => $isEn ? 'About TOP BEST GLOBAL National Honors' : 'Giới Thiệu Cổng Vinh Danh Quốc Gia TOP BEST GLOBAL',
            'description'  => $isEn ? 'Vision, Mission, Council of Experts, and Evaluation Criteria of TOP BEST GLOBAL National Honors.' : 'Tôn chỉ, sứ mệnh, hội đồng chuyên gia thẩm định và quy chế xét thưởng danh giá TOP BEST GLOBAL.',
            'keywords'     => 'giới thiệu top best global, hội đồng chuyên môn, quy chế vinh danh, 70 30 scoring methodology',
            'activeSeason' => $activeSeason,
        ];

        return loadThemeView('honors/about', $data);
    }

    /**
     * Timeline & Milestones
     */
    public function timeline()
    {
        $activeSeason = $this->seasonModel->getActiveSeason();

        $data = [
            'title'        => 'Lộ Trình & Tiến Độ Mùa Giải 2026 | TOP BEST GLOBAL',
            'description'  => 'Các mốc thời gian xét duyệt hồ sơ, bình chọn trực tuyến và đêm Gala trao giải quốc gia.',
            'activeSeason' => $activeSeason,
        ];

        return loadThemeView('honors/timeline', $data);
    }

    /**
     * Rules & Hybrid 70/30 Scoring Philosophy
     */
    public function rules()
    {
        $activeSeason = $this->seasonModel->getActiveSeason();

        $data = [
            'title'        => 'Quy Chế & Cơ Chế Chấm Điểm 70/30 | TOP BEST GLOBAL',
            'description'  => 'Quy định chi tiết về 70% Điểm Hội Đồng Giám Khảo Chuyên Môn + 30% Điểm Bình Chọn Độc Giả Minh Bạch.',
            'activeSeason' => $activeSeason,
        ];

        return loadThemeView('honors/rules', $data);
    }

    /**
     * Gala Ceremony & Honors Night
     */
    public function gala()
    {
        $activeSeason = $this->seasonModel->getActiveSeason();

        $data = [
            'title'        => 'Đại Nhạc Hội & Lễ Trao Giải Gala Quốc Gia | TOP BEST GLOBAL',
            'description'  => 'Sự kiện vinh danh trang trọng bậc nhất hội tụ 1000+ lãnh đạo doanh nghiệp, quan khách và cơ quan thông tấn báo chí.',
            'activeSeason' => $activeSeason,
        ];

        return loadThemeView('honors/gala', $data);
    }

    /**
     * Press & Media Releases
     */
    public function press()
    {
        $posts = $this->postModel->getPosts(12);

        $data = [
            'title'       => 'Thông Tấn & Báo Chí Đồng Hành | TOP BEST GLOBAL',
            'description' => 'Tin tức báo chí, phóng sự truyền hình và thông cáo báo chí chính thức.',
            'posts'       => $posts,
        ];

        return loadThemeView('honors/press', $data);
    }

    /**
     * News List
     */
    public function news()
    {
        $posts = $this->postModel->getPosts(15);
        $data  = [
            'title'       => 'Tin Tức & Sự Kiện Vinh Danh | TOP BEST GLOBAL',
            'description' => 'Cập nhật tin tức mới nhất về các hoạt động thẩm định và vinh danh thương hiệu.',
            'posts'       => $posts,
        ];
        return loadThemeView('post/posts', $data);
    }

    /**
     * News Detail
     */
    public function newsDetail(string $slug)
    {
        $post = $this->postModel->getPostBySlug($slug);
        if (!$post) {
            return redirect()->to(langBaseUrl('honors/news'));
        }
        $data = [
            'title' => esc($post->title) . ' | TOP BEST GLOBAL',
            'post'  => $post,
        ];
        return loadThemeView('post/post', $data);
    }

    /**
     * Events List
     */
    public function events()
    {
        $events = $this->eventModel->getUpcomingEvents(12);
        $data   = [
            'title'  => 'Lịch Sự Kiện & Hội Thảo Vinh Danh | TOP BEST GLOBAL',
            'events' => $events,
        ];
        return loadThemeView('events', $data);
    }

    /**
     * Event Detail
     */
    public function eventDetail(string $slug)
    {
        $event = $this->eventModel->getEventBySlug($slug);
        if (!$event) {
            return redirect()->to(langBaseUrl('honors/events'));
        }
        $data = [
            'title' => esc($event->title) . ' | TOP BEST GLOBAL',
            'event' => $event,
        ];
        return loadThemeView('event_detail', $data);
    }
}
