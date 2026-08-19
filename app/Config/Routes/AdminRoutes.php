<?php

/**
 * AdminRoutes.php: CMS Administration Routes for Pages, Posts, Media, Settings & Members
 * @var \CodeIgniter\Router\RouteCollection $routes
 */

use Config\Globals;

$customRoutes = Globals::$customRoutes;
$adminPrefix = !empty($customRoutes->admin) ? $customRoutes->admin : 'admin';

$routes->get($adminPrefix . '/login', 'CommonController::adminLogin');
$routes->post($adminPrefix . '/login-post', 'CommonController::adminLoginPost');

$routes->group($adminPrefix, ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'AdminController::index');
    $routes->get('themes', 'AdminController::themes');
    // Pages & Navigation
    $routes->get('pages', 'AdminController::pages');
    $routes->get('add-page', 'AdminController::addPage');
    $routes->get('edit-page/(:num)', 'AdminController::editPage/$1');
    $routes->get('navigation', 'AdminController::navigation');
    $routes->get('edit-menu-link/(:num)', 'AdminController::editMenuLink/$1');
    // Posts & Categories
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
    $routes->get('categories', 'CategoryController::categories');
    $routes->get('add-category', 'CategoryController::addCategory');
    $routes->get('edit-category/(:num)', 'CategoryController::editCategory/$1');
    $routes->get('tags', 'CategoryController::tags');
    // RSS Feeds
    $routes->get('feeds', 'RssController::feeds');
    $routes->get('import-feed', 'RssController::importFeed');
    $routes->get('edit-feed/(:num)', 'RssController::editFeed/$1');
    // Widgets & Polls & Gallery
    $routes->get('widgets', 'AdminController::widgets');
    $routes->get('add-widget', 'AdminController::addWidget');
    $routes->get('edit-widget/(:num)', 'AdminController::editWidget/$1');
    $routes->get('polls', 'AdminController::polls');
    $routes->get('add-poll', 'AdminController::addPoll');
    $routes->get('edit-poll/(:num)', 'AdminController::editPoll/$1');
    $routes->get('gallery-images', 'GalleryController::images');
    $routes->get('gallery-add-image', 'GalleryController::addImage');
    $routes->get('edit-gallery-image/(:num)', 'GalleryController::editImage/$1');
    $routes->get('gallery-albums', 'GalleryController::albums');
    $routes->get('edit-gallery-album/(:num)', 'GalleryController::editAlbum/$1');
    $routes->get('gallery-categories', 'GalleryController::categories');
    $routes->get('edit-gallery-category/(:num)', 'GalleryController::editCategory/$1');
    // Contact & Comments
    $routes->get('contact-messages', 'AdminController::contactMessages');
    $routes->get('comments', 'AdminController::comments');
    $routes->get('pending-comments', 'AdminController::pendingComments');
    // Newsletter
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
    // Events
    $routes->get('events', 'AdminEventController::events');
    $routes->get('add-event', 'AdminEventController::addEvent');
    $routes->post('add-event-post', 'AdminEventController::addEventPost');
    $routes->get('edit-event/(:num)', 'AdminEventController::editEvent/$1');
    $routes->post('edit-event-post', 'AdminEventController::editEventPost');
    $routes->post('delete-event-post', 'AdminEventController::deleteEventPost');
    $routes->get('event-registrations', 'AdminEventController::registrations');
    $routes->get('event-registrations/(:num)', 'AdminEventController::registrations/$1');
    $routes->post('delete-event-registration-post', 'AdminEventController::deleteRegistrationPost');
    // Reward System
    $routes->get('reward-system', 'RewardController::rewardSystem');
    $routes->get('reward-system/earnings', 'RewardController::earnings');
    $routes->get('reward-system/payouts', 'RewardController::payouts');
    $routes->get('reward-system/add-payout', 'RewardController::addPayout');
    $routes->get('reward-system/pageviews', 'RewardController::pageviews');
    $routes->get('author-earnings', 'EarningsController::authorEarnings');
    $routes->get('set-payout-account', 'EarningsController::setPayoutAccount');
    // Ad Spaces, Users & Roles
    $routes->get('ad-spaces', 'AdminController::adSpaces');
    $routes->get('users', 'AdminController::users');
    $routes->get('edit-user/(:num)', 'AdminController::editUser/$1');
    $routes->get('add-user', 'AdminController::addUser');
    $routes->get('roles-permissions', 'AdminController::rolesPermissions');
    $routes->get('add-role', 'AdminController::addRole');
    $routes->get('edit-role/(:num)', 'AdminController::editRole/$1');
    // Settings
    $routes->get('seo-tools', 'AdminController::seoTools');
    $routes->get('storage', 'AdminController::storage');
    $routes->get('cache-system', 'AdminController::cacheSystem');
    $routes->get('google-news', 'AdminController::googleNews');
    $routes->get('preferences', 'AdminController::preferences');
    $routes->get('route-settings', 'AdminController::routeSettings');
    $routes->get('email-settings', 'AdminController::emailSettings');
    $routes->get('font-settings', 'AdminController::fontSettings');
    $routes->get('edit-font/(:num)', 'AdminController::editFont/$1');
    $routes->get('social-login-settings', 'AdminController::socialLoginSettings');
    $routes->get('general-settings', 'AdminController::generalSettings');
    $routes->get('language-settings', 'LanguageController::languages');
    $routes->get('edit-language/(:num)', 'LanguageController::editLanguage/$1');
    $routes->get('edit-translations/(:num)', 'LanguageController::editTranslations/$1');
    // Members Management
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
});
