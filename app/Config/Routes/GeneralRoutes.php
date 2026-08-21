<?php

/**
 * GeneralRoutes.php: Base site, home, cron, auth and static action routes
 * @var \CodeIgniter\Router\RouteCollection $routes
 */

$routes->get('/', 'TopBestPortalController::index');
$routes->get('index.php', 'TopBestPortalController::index');
$routes->get('index.html', 'TopBestPortalController::index');
$routes->get('index', 'TopBestPortalController::index');

$routes->get('top-best-la-gi', 'TopBestPortalController::aboutTopBest');
$routes->get('bang-xep-hang', 'TopBestDirectoryController::index');
$routes->get('directory', 'TopBestDirectoryController::index');
$routes->get('ho-so/(:any)', 'TopBestDirectoryController::detail/$1');
$routes->get('directory/(:any)', 'TopBestDirectoryController::detail/$1');
$routes->get('badge/embed/(:any)', 'TopBestDirectoryController::embedBadge/$1');

$routes->get('doanh-nghiep/dang-ky', 'TopBestRegistrationController::businessRegister');
$routes->post('doanh-nghiep/gui-dang-ky', 'TopBestRegistrationController::submitBusiness');
$routes->get('nomination/apply', 'TopBestRegistrationController::businessRegister');

$routes->get('dai-ly', 'TopBestRegistrationController::agency');
$routes->get('agency', 'TopBestRegistrationController::agency');
$routes->post('dai-ly/dang-ky', 'TopBestRegistrationController::submitAgency');

$routes->get('xac-minh', 'TopBestVerificationController::index');
$routes->get('verify', 'TopBestVerificationController::index');
$routes->get('verify/(:any)', 'TopBestVerificationController::verifyCode/$1');

$routes->get('su-kien', 'TopBestPortalController::events');
$routes->get('events', 'TopBestPortalController::events');
$routes->get('ve-chung-toi', 'TopBestPortalController::aboutUs');
$routes->get('about-us', 'TopBestPortalController::aboutUs');
$routes->get('about', 'TopBestPortalController::aboutUs');
$routes->get('lien-he', 'TopBestPortalController::contact');
$routes->get('contact', 'TopBestPortalController::contact');
$routes->get('news', 'TopBestPortalController::index');
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
