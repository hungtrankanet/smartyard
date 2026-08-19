<?php

/**
 * GeneralRoutes.php: Base site, home, cron, auth and static action routes
 * @var \CodeIgniter\Router\RouteCollection $routes
 */

$routes->get('/', 'HomeController::index');
$routes->get('index.php', 'HomeController::index');
$routes->get('index.html', 'HomeController::index');
$routes->get('index', 'HomeController::index');
$routes->get('about', 'HomeController::about');
$routes->get('about.html', 'HomeController::about');
$routes->get('services', 'HomeController::services');
$routes->get('services.html', 'HomeController::services');
$routes->get('contact', 'HomeController::contactPage');
$routes->get('contact.html', 'HomeController::contactPage');
$routes->get('news', 'HomeController::posts');
$routes->get('news.html', 'HomeController::posts');
$routes->get('switch-lang/(:any)', 'HomeController::switchLang/$1');

$routes->get('cron/update-feeds', 'CronController::checkFeedPosts');
$routes->get('cron/update-sitemap', 'CronController::updateSitemap');
$routes->get('cron/verify-members', 'CronController::verifyMembers');

$routes->get('unsubscribe', 'AuthController::unsubscribe');
$routes->get('connect-with-facebook', 'AuthController::connectWithFacebook');
$routes->get('facebook-callback', 'AuthController::facebookCallback');
$routes->get('connect-with-google', 'AuthController::connectWithGoogle');
$routes->get('connect-with-vk', 'AuthController::connectWithVK');

$routes->post('register-post', 'AuthController::registerPost');
$routes->post('forgot-password-post', 'AuthController::forgotPasswordPost');
$routes->post('reset-password-post', 'AuthController::resetPasswordPost');
$routes->post('contact-post', 'HomeController::contactPost');
$routes->post('switch-dark-mode', 'CommonController::switchDarkMode');
$routes->post('follow-user-post', 'ProfileController::followUnfollowUserPost');
$routes->post('edit-profile-post', 'ProfileController::editProfilePost');
$routes->post('social-accounts-post', 'ProfileController::socialAccountsPost');
$routes->post('preferences-post', 'ProfileController::preferencesPost');
$routes->post('change-password-post', 'ProfileController::changePasswordPost');
$routes->post('delete-account-post', 'ProfileController::deleteAccountPost');
$routes->post('download-file', 'CommonController::downloadFile');
$routes->post('add-newsletter-post', 'AjaxController::addNewsletterPost');
$routes->post('close-cookies-warning-post', 'AjaxController::closeCookiesWarningPost');
