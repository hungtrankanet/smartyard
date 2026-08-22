<?php

/**
 * DynamicRoutes.php: Multi-Language Dynamic Route Resolution & Fallback Catch-all
 * @var \CodeIgniter\Router\RouteCollection $routes
 */

use Config\Globals;

$languages = Globals::$languages;
$generalSettings = Globals::$generalSettings;
$customRoutes = Globals::$customRoutes;

if (!empty($languages)) {
    foreach ($languages as $language) {
        $key = '';
        if ($generalSettings->site_lang != $language->id) {
            $key = $language->short_form . '/';
            $routes->get($language->short_form, 'HomeController::index');
        }
        $routes->get($key . 'top-best-la-gi', 'TopBestPortalController::aboutTopBest');
        $routes->get($key . 'bang-xep-hang', 'TopBestDirectoryController::index');
        $routes->get($key . 'directory', 'TopBestDirectoryController::index');
        $routes->get($key . 'ho-so/(:any)', 'TopBestDirectoryController::detail/$1');
        $routes->get($key . 'doanh-nghiep/dang-ky', 'TopBestRegistrationController::businessRegister');
        $routes->get($key . 'dai-ly', 'TopBestRegistrationController::agency');
        $routes->get($key . 'agency', 'TopBestRegistrationController::agency');
        $routes->get($key . 'su-kien', 'TopBestPortalController::events');
        $routes->get($key . 've-chung-toi', 'TopBestPortalController::aboutUs');
        $routes->get($key . 'lien-he', 'TopBestPortalController::contact');
        $routes->get($key . 'about', 'TopBestPortalController::aboutUs');
        $routes->get($key . 'about.html', 'TopBestPortalController::aboutUs');
        $routes->get($key . 'services', 'HomeController::services');
        $routes->get($key . 'services.html', 'HomeController::services');
        $routes->get($key . 'members', 'HomeController::members');
        $routes->get($key . 'members.html', 'HomeController::members');
        $routes->get($key . 'events', 'TopBestPortalController::events');
        $routes->get($key . 'events.html', 'TopBestPortalController::events');
        $routes->get($key . 'events/(:any)', 'EventController::eventDetail/$1');
        $routes->get($key . 'contact', 'TopBestPortalController::contact');
        $routes->get($key . 'contact.html', 'TopBestPortalController::contact');
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
