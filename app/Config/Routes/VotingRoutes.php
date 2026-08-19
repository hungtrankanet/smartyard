<?php

/**
 * VotingRoutes.php: TOP BEST GLOBAL Voting Engine & Real-time Leaderboard Routes
 * @var \CodeIgniter\Router\RouteCollection $routes
 */

$routes->get('voting', 'VotingEngineController::index');
$routes->get('voting.html', 'VotingEngineController::index');
$routes->get('voting/category/(:any)', 'VotingEngineController::category/$1');
$routes->get('voting/candidate/(:any)', 'VotingEngineController::candidate/$1');
$routes->get('voting/leaderboard', 'VotingEngineController::leaderboard');
$routes->get('voting/leaderboard/(:any)', 'VotingEngineController::categoryLeaderboard/$1');

$routes->post('voting/send-otp-ajax', 'VotingApiController::sendOtpAjax');
$routes->post('voting/verify-otp-ajax', 'VotingApiController::verifyOtpAjax');
$routes->post('voting/submit-vote-ajax', 'VotingApiController::submitVoteAjax');
$routes->get('api/voting/live-poll/(:num)', 'VotingApiController::getLivePollData/$1');
$routes->get('api/voting/category-stats/(:num)', 'VotingApiController::getCategoryStats/$1');
