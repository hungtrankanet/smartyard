<?php

/**
 * NominationRoutes.php: Nomination Dossiers, Hall of Fame, Jury Portal & Digital Certificates
 * @var \CodeIgniter\Router\RouteCollection $routes
 */

// 1. Nomination Dossiers (Canonical English & Vietnamese SEO Aliases)
$routes->get('nomination', 'NominationController::index');
$routes->get('nomination.html', 'NominationController::index');
$routes->get('nomination/apply', 'NominationController::apply');
$routes->post('nomination/apply-post', 'NominationController::applyPost');
$routes->get('nomination/tracker', 'NominationController::tracker');
$routes->post('nomination/track-ajax', 'NominationController::trackAjax');
$routes->get('nomination/dossier/(:any)', 'NominationController::dossier/$1');

// Vietnamese SEO Route Aliases
$routes->get('de-cu', 'NominationController::index');
$routes->get('de-cu.html', 'NominationController::index');
$routes->get('nop-ho-so-de-cu', 'NominationController::apply');
$routes->get('nop-ho-so-de-cu.html', 'NominationController::apply');
$routes->post('nop-ho-so-de-cu-post', 'NominationController::applyPost');
$routes->get('tra-cuu-de-cu', 'NominationController::tracker');
$routes->get('tra-cuu-de-cu.html', 'NominationController::tracker');
$routes->post('tra-cuu-de-cu-ajax', 'NominationController::trackAjax');
$routes->get('ho-so-de-cu/(:any)', 'NominationController::dossier/$1');

// 2. Hall of Fame (Bảng Vàng Vinh Danh)
$routes->get('hall-of-fame', 'HallOfFameController::index');
$routes->get('hall-of-fame.html', 'HallOfFameController::index');
$routes->get('hall-of-fame/season/(:num)', 'HallOfFameController::season/$1');
$routes->get('hall-of-fame/honoree/(:any)', 'HallOfFameController::honoree/$1');
$routes->get('bang-vang', 'HallOfFameController::index');
$routes->get('bang-vang.html', 'HallOfFameController::index');

// 3. Expert Jury Portal (Cổng Hội Đồng Giám Khảo)
$routes->get('jury', 'JuryEvaluationController::index');
$routes->get('jury/evaluate/(:num)', 'JuryEvaluationController::evaluate/$1');
$routes->post('jury/submit-score', 'JuryEvaluationController::submitScore');
$routes->get('hoi-dong-giam-khao', 'JuryEvaluationController::index');
$routes->get('hoi-dong-giam-khao.html', 'JuryEvaluationController::index');
$routes->get('hoi-dong-giam-khao/cham-diem/(:num)', 'JuryEvaluationController::evaluate/$1');
$routes->post('hoi-dong-giam-khao/nop-diem', 'JuryEvaluationController::submitScore');

// 4. Digital Certificates & Badges
$routes->get('verify-certificate/(:any)', 'CertificateController::verify/$1');
$routes->get('verify-certificate', 'CertificateController::verify');
$routes->get('certificate/download/(:any)', 'CertificateController::download/$1');
$routes->get('api/certificate/verify/(:any)', 'CertificateController::verifyApi/$1');
$routes->get('badge/(:any)', 'CertificateController::badgeSvg/$1');
$routes->get('api/badge/svg/(:any)', 'CertificateController::badgeSvg/$1');

