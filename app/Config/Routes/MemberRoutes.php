<?php

/**
 * MemberRoutes.php: Member & Partner Portal Routes, Events, and OCR Verification
 * @var \CodeIgniter\Router\RouteCollection $routes
 */

$routes->get('member', 'MemberPortalController::index');
$routes->get('(:segment)/member', 'MemberPortalController::index');
$routes->get('partner', 'MemberPortalController::index');
$routes->get('(:segment)/partner', 'MemberPortalController::index');
$routes->get('member/register', 'MemberPortalController::register');
$routes->get('(:segment)/member/register', 'MemberPortalController::register');
$routes->get('partner/register', 'MemberPortalController::register');
$routes->get('(:segment)/partner/register', 'MemberPortalController::register');
$routes->post('member/send-otp-ajax', 'MemberPortalController::sendRegisterOtpAjax');
$routes->post('(:segment)/member/send-otp-ajax', 'MemberPortalController::sendRegisterOtpAjax');
$routes->post('member/verify-otp-ajax', 'MemberPortalController::verifyRegisterOtpAjax');
$routes->post('(:segment)/member/verify-otp-ajax', 'MemberPortalController::verifyRegisterOtpAjax');
$routes->get('member/login', 'MemberPortalController::login');
$routes->get('(:segment)/member/login', 'MemberPortalController::login');
$routes->get('partner/login', 'MemberPortalController::login');
$routes->get('(:segment)/partner/login', 'MemberPortalController::login');
$routes->post('member/login-post', 'MemberPortalController::loginPost');
$routes->post('(:segment)/member/login-post', 'MemberPortalController::loginPost');
$routes->get('member/logout', 'MemberPortalController::logout');
$routes->get('(:segment)/member/logout', 'MemberPortalController::logout');
$routes->get('partner/logout', 'MemberPortalController::logout');
$routes->get('(:segment)/partner/logout', 'MemberPortalController::logout');
$routes->get('member/dashboard', 'MemberPortalController::dashboard');
$routes->get('(:segment)/member/dashboard', 'MemberPortalController::dashboard');
$routes->get('partner/dashboard', 'MemberPortalController::dashboard');
$routes->get('(:segment)/partner/dashboard', 'MemberPortalController::dashboard');
$routes->post('member/save-post-ajax', 'MemberPortalController::saveIntroductionPostAjax');
$routes->post('member/send-message-ajax', 'MemberPortalController::sendMessageAjax');
$routes->post('member/mark-message-read-ajax/(:num)', 'MemberPortalController::markMessageReadAjax/$1');

$routes->get('members', 'HomeController::members');
$routes->get('members.html', 'HomeController::members');
$routes->get('partners', 'HomeController::members');
$routes->get('partners.html', 'HomeController::members');
$routes->get('events', 'EventController::events');
$routes->get('events.html', 'EventController::events');
$routes->get('events/(:any)', 'EventController::eventDetail/$1');
$routes->post('api/register-event', 'EventController::registerEventAjax');
$routes->post('events/register-ajax', 'EventController::registerEventAjax');
