<?php

/**
 * HonorsRoutes.php: TOP BEST GLOBAL Portal & National Honors Branding Routes
 * @var \CodeIgniter\Router\RouteCollection $routes
 */

$routes->get('honors', 'HonorsPortalController::index');
$routes->get('honors.html', 'HonorsPortalController::index');
$routes->get('honors/categories', 'HonorsPortalController::categories');
$routes->get('honors/category/(:any)', 'HonorsPortalController::categoryDetail/$1');
$routes->get('honors/seasons', 'HonorsPortalController::seasons');
$routes->get('honors/seasons/(:num)', 'HonorsPortalController::seasonDetail/$1');
$routes->get('honors/about', 'HonorsPortalController::about');
$routes->get('honors/timeline', 'HonorsPortalController::timeline');
$routes->get('honors/press', 'HonorsPortalController::press');
$routes->get('honors/rules', 'HonorsPortalController::rules');
$routes->get('gala', 'HonorsPortalController::gala');
$routes->get('gala.html', 'HonorsPortalController::gala');

$routes->get('honors/news', 'HonorsPortalController::news');
$routes->get('honors/news/(:any)', 'HonorsPortalController::newsDetail/$1');
$routes->get('honors/events', 'HonorsPortalController::events');
$routes->get('honors/events/(:any)', 'HonorsPortalController::eventDetail/$1');
