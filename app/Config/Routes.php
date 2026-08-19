<?php

use CodeIgniter\Router\RouteCollection;
use Config\Globals;

/**
 * @var RouteCollection $routes
 */

$languages = Globals::$languages;
$generalSettings = Globals::$generalSettings;
$customRoutes = Globals::$customRoutes;

$routes->get('/', 'HomeController::index');
$routes->get('cron/update-feeds', 'CronController::checkFeedPosts');
$routes->get('cron/update-sitemap', 'CronController::updateSitemap');
$routes->get('cron/verify-members', 'CronController::verifyMembers');
$routes->get('unsubscribe', 'AuthController::unsubscribe');
$routes->get('connect-with-facebook', 'AuthController::connectWithFacebook');
$routes->get('facebook-callback', 'AuthController::facebookCallback'); $routes->get('connect-with-google', 'AuthController::connectWithGoogle'); $routes->get('connect-with-vk', 'AuthController::connectWithVK');
$routes->get('switch-lang/(:any)', 'HomeController::switchLang/$1');
$routes->get('index.php', 'HomeController::index'); $routes->get('index.html', 'HomeController::index'); $routes->get('index', 'HomeController::index');
$routes->get('about', 'HomeController::about'); $routes->get('about.html', 'HomeController::about');
$routes->get('services', 'HomeController::services'); $routes->get('services.html', 'HomeController::services');
$routes->get('members', 'HomeController::members'); $routes->get('members.html', 'HomeController::members');
$routes->get('partners', 'HomeController::members'); $routes->get('partners.html', 'HomeController::members');
$routes->get('events', 'EventController::events'); $routes->get('events.html', 'EventController::events'); $routes->get('events/(:any)', 'EventController::eventDetail/$1');
$routes->post('api/register-event', 'EventController::registerEventAjax'); $routes->post('events/register-ajax', 'EventController::registerEventAjax');
$routes->get('contact', 'HomeController::contactPage'); $routes->get('contact.html', 'HomeController::contactPage');
$routes->get('news', 'HomeController::posts'); $routes->get('news.html', 'HomeController::posts');

// Member & Partner Portal Routes
$routes->get('member', 'MemberPortalController::index'); $routes->get('(:segment)/member', 'MemberPortalController::index');
$routes->get('partner', 'MemberPortalController::index'); $routes->get('(:segment)/partner', 'MemberPortalController::index');
$routes->get('member/register', 'MemberPortalController::register'); $routes->get('(:segment)/member/register', 'MemberPortalController::register');
$routes->get('partner/register', 'MemberPortalController::register'); $routes->get('(:segment)/partner/register', 'MemberPortalController::register');
$routes->post('member/send-otp-ajax', 'MemberPortalController::sendRegisterOtpAjax'); $routes->post('(:segment)/member/send-otp-ajax', 'MemberPortalController::sendRegisterOtpAjax');
$routes->post('member/verify-otp-ajax', 'MemberPortalController::verifyRegisterOtpAjax'); $routes->post('(:segment)/member/verify-otp-ajax', 'MemberPortalController::verifyRegisterOtpAjax');
$routes->get('member/login', 'MemberPortalController::login'); $routes->get('(:segment)/member/login', 'MemberPortalController::login');
$routes->get('partner/login', 'MemberPortalController::login'); $routes->get('(:segment)/partner/login', 'MemberPortalController::login');
$routes->post('member/login-post', 'MemberPortalController::loginPost'); $routes->post('(:segment)/member/login-post', 'MemberPortalController::loginPost');
$routes->get('member/logout', 'MemberPortalController::logout'); $routes->get('(:segment)/member/logout', 'MemberPortalController::logout');
$routes->get('partner/logout', 'MemberPortalController::logout'); $routes->get('(:segment)/partner/logout', 'MemberPortalController::logout');
$routes->get('member/dashboard', 'MemberPortalController::dashboard'); $routes->get('(:segment)/member/dashboard', 'MemberPortalController::dashboard');
$routes->get('partner/dashboard', 'MemberPortalController::dashboard'); $routes->get('(:segment)/partner/dashboard', 'MemberPortalController::dashboard');
$routes->post('member/save-post-ajax', 'MemberPortalController::saveIntroductionPostAjax');
$routes->post('member/send-message-ajax', 'MemberPortalController::sendMessageAjax');
$routes->post('member/mark-message-read-ajax/(:num)', 'MemberPortalController::markMessageReadAjax/$1');

// Email Marketing Tracking Routes
$routes->get('email-track/open/(:any)', 'EmailCampaignController::trackOpen/$1');
$routes->get('email-track/click/(:any)', 'EmailCampaignController::trackClick/$1');

/*
 * --------------------------------------------------------------------
 * Suntransco REST API Routes (JSON endpoints for frontend)
 * --------------------------------------------------------------------
 */
$routes->options('api/(:any)', function() { return ''; }); // CORS preflight
$routes->get('api/news', 'ApiController::news'); $routes->get('api/news/(:segment)', 'ApiController::newsDetail/$1');
$routes->get('api/events', 'ApiController::events'); $routes->get('api/members', 'ApiController::members');
$routes->get('api/settings', 'ApiController::settings'); $routes->post('api/contact', 'ApiController::contact'); $routes->post('api/newsletter', 'ApiController::newsletter');

// Post Routes
$routes->post('register-post', 'AuthController::registerPost'); $routes->post('forgot-password-post', 'AuthController::forgotPasswordPost');
$routes->post('reset-password-post', 'AuthController::resetPasswordPost'); $routes->post('contact-post', 'HomeController::contactPost');
$routes->post('switch-dark-mode', 'CommonController::switchDarkMode'); $routes->post('follow-user-post', 'ProfileController::followUnfollowUserPost');
$routes->post('edit-profile-post', 'ProfileController::editProfilePost'); $routes->post('social-accounts-post', 'ProfileController::socialAccountsPost');
$routes->post('preferences-post', 'ProfileController::preferencesPost'); $routes->post('change-password-post', 'ProfileController::changePasswordPost');
$routes->post('delete-account-post', 'ProfileController::deleteAccountPost'); $routes->post('download-file', 'CommonController::downloadFile');
$routes->post('add-newsletter-post', 'AjaxController::addNewsletterPost'); $routes->post('close-cookies-warning-post', 'AjaxController::closeCookiesWarningPost');

/*f
 * --------------------------------------------------------------------
 * Admin Routes
 * --------------------------------------------------------------------
 */

$routes->get($customRoutes->admin . '/login', 'CommonController::adminLogin');
$routes->post($customRoutes->admin . '/login-post', 'CommonController::adminLoginPost');

$routes->group($customRoutes->admin, ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'AdminController::index');
    $routes->get('themes', 'AdminController::themes');
    //pages
    $routes->get('pages', 'AdminController::pages');
    $routes->get('add-page', 'AdminController::addPage');
    $routes->get('edit-page/(:num)', 'AdminController::editPage/$1');
    $routes->get('navigation', 'AdminController::navigation');
    $routes->get('edit-menu-link/(:num)', 'AdminController::editMenuLink/$1');
    //posts
    $routes->get('post-format', 'PostController::postFormat');
    $routes->get('add-post', 'PostController::addPost');
    $routes->get('posts', 'PostController::posts');
    $routes->get('slider-posts', 'PostController::sliderPosts');
    $routes->get('featured-posts', 'PostController::featuredPosts');
    $routes->get('breaking-news', 'PostController::breakingNews');
    $routes->get('recommended-posts', 'PostController::recommendedPosts');
    $routes->get('pending-posts', 'PostController::pendingPosts');
    $routes->get('scheduled-posts', 'PostController::scheduledPosts');
    $routes->get('drafts', 'PostController::drafts');
    $routes->get('bulk-post-upload', 'PostController::bulkPostUpload');
    $routes->get('edit-post/(:num)', 'PostController::editPost/$1');
    //rss feeds
    $routes->get('feeds', 'RssController::feeds');
    $routes->get('import-feed', 'RssController::importFeed');
    $routes->get('edit-feed/(:num)', 'RssController::editFeed/$1');
    //categories
    $routes->get('add-category', 'CategoryController::addCategory');
    $routes->get('categories', 'CategoryController::categories');
    $routes->get('edit-category/(:num)', 'CategoryController::editCategory/$1');
    //widgets
    $routes->get('widgets', 'AdminController::widgets');
    $routes->get('add-widget', 'AdminController::addWidget');
    $routes->get('edit-widget/(:num)', 'AdminController::editWidget/$1');
    //polls
    $routes->get('polls', 'AdminController::polls');
    $routes->get('add-poll', 'AdminController::addPoll');
    $routes->get('edit-poll/(:num)', 'AdminController::editPoll/$1');
    //gallery
    $routes->get('gallery-images', 'GalleryController::images');
    $routes->get('gallery-add-image', 'GalleryController::addImage');
    $routes->get('edit-gallery-image/(:num)', 'GalleryController::editImage/$1');
    $routes->get('gallery-albums', 'GalleryController::albums');
    $routes->get('edit-gallery-album/(:num)', 'GalleryController::editAlbum/$1');
    $routes->get('gallery-categories', 'GalleryController::categories');
    $routes->get('edit-gallery-category/(:num)', 'GalleryController::editCategory/$1');
    //contact
    $routes->get('contact-messages', 'AdminController::contactMessages');
    //comments
    $routes->get('comments', 'AdminController::comments');
    $routes->get('pending-comments', 'AdminController::pendingComments');
    //newsletter & email marketing campaigns & groups
    $routes->get('newsletter', 'EmailCampaignController::campaigns');
    $routes->get('newsletter-groups', 'EmailCampaignController::groups');
    $routes->post('newsletter-save-group-post', 'EmailCampaignController::saveGroupPost');
    $routes->post('newsletter-delete-group-post', 'EmailCampaignController::deleteGroupPost');
    $routes->post('newsletter-preview-group-ajax', 'EmailCampaignController::getGroupPreviewAjax');
    $routes->get('newsletter-create-campaign', 'EmailCampaignController::createCampaign');
    $routes->post('newsletter-create-campaign-post', 'EmailCampaignController::createCampaignPost');
    $routes->get('newsletter-send-campaign/(:num)', 'EmailCampaignController::sendCampaign/$1');
    $routes->post('newsletter-send-single-log', 'EmailCampaignController::sendSingleLogAjax');
    $routes->post('newsletter-delete-campaign', 'EmailCampaignController::deleteCampaignPost');
    $routes->post('newsletter-add-recipient-ajax', 'EmailCampaignController::addRecipientAjax');
    $routes->post('newsletter-edit-recipient-ajax', 'EmailCampaignController::editRecipientAjax');
    $routes->post('newsletter-delete-recipient-ajax', 'EmailCampaignController::deleteRecipientAjax');
    $routes->post('newsletter-send-email', 'AdminController::newsletterSendEmail');
    $routes->post('newsletter-generate-template', 'AdminController::newsletterGenerateTemplate');

    //events management
    $routes->get('events', 'AdminEventController::events');
    $routes->get('add-event', 'AdminEventController::addEvent');
    $routes->post('add-event-post', 'AdminEventController::addEventPost');
    $routes->get('edit-event/(:num)', 'AdminEventController::editEvent/$1');
    $routes->post('edit-event-post', 'AdminEventController::editEventPost');
    $routes->post('delete-event-post', 'AdminEventController::deleteEventPost');
    $routes->get('event-registrations', 'AdminEventController::registrations');
    $routes->get('event-registrations/(:num)', 'AdminEventController::registrations/$1');
    $routes->post('delete-event-registration-post', 'AdminEventController::deleteRegistrationPost');

    //reward-system
    $routes->get('reward-system', 'RewardController::rewardSystem'); $routes->get('reward-system/earnings', 'RewardController::earnings');
    $routes->get('reward-system/payouts', 'RewardController::payouts'); $routes->get('reward-system/add-payout', 'RewardController::addPayout');
    $routes->get('reward-system/pageviews', 'RewardController::pageviews'); $routes->get('author-earnings', 'EarningsController::authorEarnings'); $routes->get('set-payout-account', 'EarningsController::setPayoutAccount');

    //ad spaces & users
    $routes->get('ad-spaces', 'AdminController::adSpaces'); $routes->get('users', 'AdminController::users');
    $routes->get('edit-user/(:num)', 'AdminController::editUser/$1'); $routes->get('add-user', 'AdminController::addUser');
    //roles permissions & settings
    $routes->get('roles-permissions', 'AdminController::rolesPermissions'); $routes->get('add-role', 'AdminController::addRole'); $routes->get('edit-role/(:num)', 'AdminController::editRole/$1');
    $routes->get('seo-tools', 'AdminController::seoTools'); $routes->get('storage', 'AdminController::storage');
    $routes->get('cache-system', 'AdminController::cacheSystem'); $routes->get('google-news', 'AdminController::googleNews');
    //settings
    $routes->get('preferences', 'AdminController::preferences');
    $routes->get('route-settings', 'AdminController::routeSettings');
    $routes->get('email-settings', 'AdminController::emailSettings');
    $routes->get('font-settings', 'AdminController::fontSettings');
    $routes->get('edit-font/(:num)', 'AdminController::editFont/$1');
    $routes->get('social-login-settings', 'AdminController::socialLoginSettings');
    $routes->get('general-settings', 'AdminController::generalSettings');
    //language
    $routes->get('language-settings', 'LanguageController::languages');
    $routes->get('edit-language/(:num)', 'LanguageController::editLanguage/$1');
    $routes->get('edit-translations/(:num)', 'LanguageController::editTranslations/$1');
    //members
    $routes->get('members', 'MemberController::index');
    $routes->get('members/add', 'MemberController::addMember');
    $routes->post('members/add-post', 'MemberController::addMemberPost');
    $routes->get('members/edit/(:num)', 'MemberController::editMember/$1');
    $routes->post('members/edit-post/(:num)', 'MemberController::editMemberPost/$1');
    $routes->post('members/delete-post', 'MemberController::deleteMemberPost');
    $routes->get('members/upload-cards', 'MemberController::uploadCards');
    $routes->post('members/upload-card-post', 'MemberController::uploadCardAjax');
    $routes->post('members/upload-file-ajax', 'MemberController::uploadFileOnlyAjax');
    $routes->post('members/ocr-pair-ajax', 'MemberController::ocrPairAjax');
    $routes->get('members/confirm-ocr', 'MemberController::confirmOcr');
    $routes->post('members/confirm-ocr', 'MemberController::confirmOcr');
    $routes->post('members/save-ocr-post', 'MemberController::confirmOcrPost');
    $routes->get('members/skip-ocr', 'MemberController::skipOcr');
    $routes->get('members/detail/(:num)', 'MemberController::detail/$1');
    $routes->get('members/verify/(:num)', 'MemberController::verifyMember/$1');
    $routes->match(['get', 'post'], 'members/verify-ajax/(:segment)', 'MemberController::verifyMemberAjax/$1');
    $routes->match(['get', 'post'], 'members/verify-ajax', 'MemberController::verifyMemberAjax');
    $routes->get('members/verify-logs', 'MemberController::verifyLogs');
    $routes->get('members/verify-queue-ajax', 'MemberController::getPendingQueueAjax');
    $routes->get('members/posts', 'MemberController::posts');
    $routes->post('members/approve-post/(:num)', 'MemberController::approvePost/$1');
    $routes->post('members/reject-post/(:num)', 'MemberController::rejectPost/$1');
    $routes->get('industry-types', 'IndustryTypeController::index');
    $routes->post('industry-types/add-post', 'IndustryTypeController::addIndustryPost');
    $routes->get('industry-types/edit/(:num)', 'IndustryTypeController::editIndustry/$1');
    $routes->post('industry-types/edit-post/(:num)', 'IndustryTypeController::editIndustryPost/$1');
    $routes->post('industry-types/delete-post', 'IndustryTypeController::deleteIndustryPost');
    $routes->get('tags', 'CategoryController::tags');
});

/*
 * --------------------------------------------------------------------
 * Static POST Routes
 * --------------------------------------------------------------------
 */

$postRoutesArray = [
    //Admin
    'Admin/deleteNavigationPost',
    'Admin/deletePagePost',
    'Admin/adSpacesPost',
    'Admin/googleAdsenseCodePost',
    'Admin/cacheSystemPost',
    'Admin/approveCommentPost',
    'Admin/deleteCommentPost',
    'Admin/deleteContactMessagePost',
    'Admin/googleNewsPost',
    'Admin/seoToolsPost',
    'Admin/googleIndexingApiPost',
    'Admin/sitemapSettingsPost',
    'Admin/sitemapPost',
    'Admin/socialLoginSettingsPost',
    'Admin/storagePost',
    'Admin/awsS3Post',
    'Admin/setThemePost',
    'Admin/setThemeSettingsPost',
    'Admin/editFontPost',
    'Admin/setSiteFontPost',
    'Admin/addFontPost',
    'Admin/deleteFontPost',
    'Admin/setActiveLanguagePost',
    'Admin/downloadDatabaseBackup',
    'Admin/editMenuLinkPost',
    'Admin/addMenuLinkPost',
    'Admin/menuLimitPost',
    'Admin/sortMenuItems',
    'Admin/hideShowHomeLink',
    'Admin/deleteSubscriberPost',
    'Admin/newsletterSettingsPost',
    'Admin/newsletterSendEmailPost',
    'Admin/addPagePost',
    'Admin/editPagePost',
    'Admin/deletePagePost',
    'Admin/addPollPost',
    'Admin/editPollPost',
    'Admin/deletePollPost',
    'Admin/emailSettingsPost',
    'Admin/emailVerificationSettingsPost',
    'Admin/contactEmailSettingsPost',
    'Admin/sendTestEmailPost',
    'Admin/generalSettingsPost',
    'Admin/recaptchaSettingsPost',
    'Admin/maintenanceModePost',
    'Admin/preferencesPost',
    'Admin/aiWriterPost',
    'Admin/fileUploadSettingsPost',
    'Admin/routeSettingsPost',
    'Admin/addUserPost',
    'Admin/userOptionsPost',
    'Admin/deleteUserPost',
    'Admin/addRolePost',
    'Admin/editRolePost',
    'Admin/editUserPost',
    'Admin/deleteRolePost',
    'Admin/loadUsersDropdown',
    'Admin/changeUserRolePost',
    'Admin/addWidgetPost',
    'Admin/editWidgetPost',
    'Admin/deleteWidgetPost',
    'Admin/getMenuLinksByLang',
    'Admin/approveSelectedComments',
    'Admin/deleteSelectedComments',
    'Admin/deleteSelectedContactMessages',
    //Ajax
    'Ajax/setThemeModePost',
    'Ajax/incrementPostViews',
    'Ajax/runOnPageLoad',
    'Ajax/addPollVote',
    'Ajax/loadMorePosts',
    'Ajax/loadMoreUsers',
    'Ajax/loadMoreSubscribers',
    'Ajax/addRemoveReadingListItem',
    'Ajax/addReaction',
    'Ajax/addCommentPost',
    'Ajax/addCommentPost',
    'Ajax/addCommentPost',
    'Ajax/addCommentPost',
    'Ajax/loadSubcommentBox',
    'Ajax/likeCommentPost',
    'Ajax/loadMoreComments',
    'Ajax/deleteCommentPost',
    'Ajax/addRemoveReadingListItem',
    'Ajax/getQuizAnswers',
    'Ajax/getQuizResults',
    'Ajax/addPostPollVote',
    'Ajax/generateTextAI',
    'Ajax/getTagSuggestions',
    //Auth
    'Auth/loginPost',
    //Category
    'Category/deleteCategoryPost',
    'Category/addCategoryPost',
    'Category/deleteCategoryPost',
    'Category/editCategoryPost',
    'Category/getParentCategoriesByLang',
    'Category/getSubCategories',
    'Category/addTagPost',
    'Category/editTagPost',
    'Category/deleteTagPost',
    //Earnings
    'Earnings/setPayoutAccountPost',
    'Earnings/newPayoutRequestPost',
    //File
    'File/uploadFile',
    'File/uploadAudio',
    'File/uploadImage',
    'File/uploadQuizImageFile',
    'File/uploadVideo',
    'File/getImages',
    'File/deleteImage',
    'File/loadMoreImages',
    'File/searchImage',
    'File/getQuizImages',
    'File/deleteQuizImage',
    'File/loadMoreQuizImages',
    'File/searchQuizImage',
    'File/uploadRecipeImage',
    'File/getRecipeImages',
    'File/deleteRecipeImage',
    'File/loadMoreRecipeImages',
    'File/searchRecipeImage',
    'File/deleteFile',
    'File/getFiles',
    'File/loadMoreFiles',
    'File/searchFiles',
    'File/deleteVideo',
    'File/getVideos',
    'File/loadMoreVideos',
    'File/searchVideos',
    'File/deleteAudio',
    'File/getAudios',
    'File/loadMoreAudios',
    'File/searchAudios',
    //Gallery
    'Gallery/addImagePost',
    'Gallery/addAlbumPost',
    'Gallery/deleteAlbumPost',
    'Gallery/addCategoryPost',
    'Gallery/deleteCategoryPost',
    'Gallery/editAlbumPost',
    'Gallery/editCategoryPost',
    'Gallery/editImagePost',
    'Gallery/deleteImagePost',
    'Gallery/setAsAlbumCover',
    'Gallery/getAlbumsByLang',
    'Gallery/getCategoriesByAlbum',
    //Language
    'Language/addLanguagePost',
    'Language/editLanguagePost',
    'Language/setDefaultLanguagePost',
    'Language/exportLanguagePost',
    'Language/deleteLanguagePost',
    'Language/importLanguagePost',
    'Language/editTranslationsPost',
    //Post
    'Post/addPostPost',
    'Post/downloadCSVFilePost',
    'Post/generateCSVObjectPost',
    'Post/importCSVItemPost',
    'Post/postOptionsPost',
    'Post/deletePost',
    'Post/editPostPost',
    'Post/deletePostMainImage',
    'Post/deletePostAdditionalImage',
    'Post/setHomeSliderPostOrderPost',
    'Post/setFeaturedPostOrderPost',
    'Post/deleteSelectedPosts',
    'Post/postBulkOptionsPost',
    'Post/getVideoFromURL',
    'Post/deletePostVideo',
    'Post/deletePostAudio',
    'Post/deletePostFile',
    'Post/getListItemHTML',
    'Post/addListItem',
    'Post/deletePostListItemPost',
    'Post/getQuizQuestionHTML',
    'Post/addQuizQuestion',
    'Post/getQuizAnswerHTML',
    'Post/addQuizQuestionAnswer',
    'Post/deleteQuizQuestion',
    'Post/deleteQuizQuestionAnswer',
    'Post/getQuizResultHTML',
    'Post/addQuizResult',
    'Post/deleteQuizResult',
    'Post/testGoogleIndexingApiPost',
    //Reward
    'Reward/addPayoutPost',
    'Reward/deletePayoutPost',
    'Reward/updateSettingsPost',
    'Reward/updatePayoutPost',
    'Reward/updateCurrencyPost',
    'Reward/approvePayoutPost',
    //Rss
    'Rss/editFeedPost',
    'Rss/checkFeedPosts',
    'Rss/deleteFeedPost',
    'Rss/importFeedPost',
    //Member
    'Member/addMemberPost',
    'Member/editMemberPost',
    'Member/deleteMemberPost',
    'Member/uploadCardAjax',
    'Member/confirmOcrPost',
    'Member/verifyMemberAjax',
    //IndustryType
    'IndustryType/addIndustryPost',
    'IndustryType/editIndustryPost',
    'IndustryType/deleteIndustryPost',
];

foreach ($postRoutesArray as $item) {
    $array = explode('/', $item);
    $routes->post($item, $array[0] . 'Controller::' . $array[1]);
}

/*
 * --------------------------------------------------------------------
 * Dynamic Routes
 * --------------------------------------------------------------------
 */

if (!empty($languages)) {
    foreach ($languages as $language) {
        $key = '';
        if ($generalSettings->site_lang != $language->id) {
            $key = $language->short_form . '/';
            $routes->get($language->short_form, 'HomeController::index');
        }
        $routes->get($key . 'about', 'HomeController::about');
        $routes->get($key . 'about.html', 'HomeController::about');
        $routes->get($key . 'services', 'HomeController::services');
        $routes->get($key . 'services.html', 'HomeController::services');
        $routes->get($key . 'members', 'HomeController::members');
        $routes->get($key . 'members.html', 'HomeController::members');
        $routes->get($key . 'events', 'EventController::events');
        $routes->get($key . 'events.html', 'EventController::events');
        $routes->get($key . 'events/(:any)', 'EventController::eventDetail/$1');
        $routes->get($key . 'contact', 'HomeController::contactPage');
        $routes->get($key . 'contact.html', 'HomeController::contactPage');
        $routes->get($key . 'news', 'HomeController::posts');
        $routes->get($key . 'news.html', 'HomeController::posts');
        $routes->get($key . $customRoutes->register, 'AuthController::register');
        $routes->get($key . $customRoutes->forgot_password, 'AuthController::forgotPassword');
        $routes->get($key . $customRoutes->logout, 'CommonController::logout');
        $routes->get($key . $customRoutes->posts, 'HomeController::posts');
        $routes->get($key . $customRoutes->tag . '/(:any)', 'HomeController::tag/$1');
        $routes->get($key . $customRoutes->gallery_album . '/(:num)', 'HomeController::galleryAlbum/$1');
        $routes->get($key . $customRoutes->search, 'HomeController::search');
        $routes->get($key . $customRoutes->profile . '/(:any)', 'ProfileController::profile/$1');
        $routes->get($key . $customRoutes->settings, 'ProfileController::editProfile', ['filter' => 'auth']);
        $routes->get($key . $customRoutes->settings . '/' . $customRoutes->social_accounts, 'ProfileController::socialAccounts', ['filter' => 'auth']);
        $routes->get($key . $customRoutes->settings . '/' . $customRoutes->preferences, 'ProfileController::preferences', ['filter' => 'auth']);
        $routes->get($key . $customRoutes->settings . '/' . $customRoutes->change_password, 'ProfileController::changePassword', ['filter' => 'auth']);
        $routes->get($key . $customRoutes->settings . '/' . $customRoutes->delete_account, 'ProfileController::deleteAccount', ['filter' => 'auth']);
        $routes->get($key . $customRoutes->reading_list, 'HomeController::readingList', ['filter' => 'auth']);
        $routes->get($key . $customRoutes->rss_feeds, 'HomeController::rssFeeds');
        $routes->get($key . 'rss/latest-posts', 'HomeController::rssLatestPosts');
        $routes->get($key . 'rss/category/(:any)', 'HomeController::rssByCategory/$1');
        $routes->get($key . 'rss/author/(:any)', 'HomeController::rssByUser/$1');
        $routes->get($key . 'preview/(:any)', 'HomeController::preview/$1');
        $routes->get($key . 'reset-password', 'AuthController::resetPassword');
        $routes->get($key . 'confirm-email', 'AuthController::confirmEmail');
        if ($generalSettings->site_lang != $language->id) {
            $routes->get($key . '(:any)/(:any)/(:any)', 'HomeController::error404');
            $routes->get($key . '(:any)/(:any)', 'HomeController::subCategory/$1/$2');
            $routes->get($key . '(:any)', 'HomeController::any/$1');
        }
    }
}

$routes->get('(:any)/(:any)/(:any)', 'HomeController::error404');
$routes->get('(:any)/(:any)', 'HomeController::subCategory/$1/$2');
$routes->get('(:any)', 'HomeController::any/$1');