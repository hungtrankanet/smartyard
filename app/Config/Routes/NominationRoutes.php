<?php

/**
 * NominationRoutes.php: Nomination Dossiers, Hall of Fame & Digital Certificates
 * @var \CodeIgniter\Router\RouteCollection $routes
 */

$routes->get('nomination', 'NominationController::index');
$routes->get('nomination.html', 'NominationController::index');
$routes->get('nomination/apply', 'NominationController::apply');
$routes->post('nomination/apply-post', 'NominationController::applyPost');
$routes->get('nomination/tracker', 'NominationController::tracker');
$routes->post('nomination/track-ajax', 'NominationController::trackAjax');
$routes->get('nomination/dossier/(:any)', 'NominationController::dossier/$1');

$routes->get('hall-of-fame', 'HallOfFameController::index');
$routes->get('hall-of-fame.html', 'HallOfFameController::index');
$routes->get('hall-of-fame/season/(:num)', 'HallOfFameController::season/$1');
$routes->get('hall-of-fame/honoree/(:any)', 'HallOfFameController::honoree/$1');

$routes->get('verify-certificate/(:any)', 'CertificateController::verify/$1');
$routes->get('certificate/download/(:any)', 'CertificateController::download/$1');
$routes->get('api/certificate/verify/(:any)', 'CertificateController::verifyApi/$1');
$routes->get('badge/(:any)', 'CertificateController::badgeSvg/$1');
