<?php

/**
 * ApiRoutes.php: REST API Endpoints & Email Marketing Tracking Routes
 * @var \CodeIgniter\Router\RouteCollection $routes
 */

$routes->get('email-track/open/(:any)', 'EmailCampaignController::trackOpen/$1');
$routes->get('email-track/click/(:any)', 'EmailCampaignController::trackClick/$1');

$routes->options('api/(:any)', function() { return ''; });
$routes->get('api/news', 'ApiController::news');
$routes->get('api/news/(:segment)', 'ApiController::newsDetail/$1');
$routes->get('api/events', 'ApiController::events');
$routes->get('api/members', 'ApiController::members');
$routes->get('api/settings', 'ApiController::settings');
$routes->post('api/contact', 'ApiController::contact');
$routes->post('api/newsletter', 'ApiController::newsletter');
