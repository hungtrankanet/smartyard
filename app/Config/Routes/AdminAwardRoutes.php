<?php

/**
 * AdminAwardRoutes.php: Admin Award Seasons, Categories, Nominations, Jury, & Voting Audit
 * @var \CodeIgniter\Router\RouteCollection $routes
 */

use Config\Globals;

$customRoutes = Globals::$customRoutes;
$adminPrefix = !empty($customRoutes->admin) ? $customRoutes->admin : 'admin';

$routes->group($adminPrefix, ['filter' => 'auth'], function ($routes) {
    // Seasons
    $routes->get('award-seasons', 'AdminAwardSeasonController::seasons');
    $routes->get('add-award-season', 'AdminAwardSeasonController::addSeason');
    $routes->post('add-award-season-post', 'AdminAwardSeasonController::addSeasonPost');
    $routes->get('edit-award-season/(:num)', 'AdminAwardSeasonController::editSeason/$1');
    $routes->post('edit-award-season-post/(:num)', 'AdminAwardSeasonController::editSeasonPost/$1');
    $routes->post('delete-award-season-post', 'AdminAwardSeasonController::deleteSeasonPost');

    // Categories
    $routes->get('award-categories', 'AdminAwardSeasonController::categories');
    $routes->get('add-award-category', 'AdminAwardSeasonController::addCategory');
    $routes->post('add-award-category-post', 'AdminAwardSeasonController::addCategoryPost');
    $routes->get('edit-award-category/(:num)', 'AdminAwardSeasonController::editCategory/$1');
    $routes->post('edit-award-category-post/(:num)', 'AdminAwardSeasonController::editCategoryPost/$1');
    $routes->post('delete-award-category-post', 'AdminAwardSeasonController::deleteCategoryPost');

    // Nominations
    $routes->get('nominations', 'AdminNominationController::index');
    $routes->get('nomination-dossier/(:num)', 'AdminNominationController::dossier/$1');
    $routes->post('nomination-update-stage-post', 'AdminNominationController::updateStagePost');
    $routes->post('nomination-decision-post', 'AdminNominationController::decisionPost');
    $routes->post('nomination-delete-post', 'AdminNominationController::deletePost');

    // Jury
    $routes->get('jury-evaluations', 'AdminJuryController::evaluations');
    $routes->get('jury-scoring/(:num)', 'AdminJuryController::scoring/$1');
    $routes->post('jury-submit-score-post', 'AdminJuryController::submitScorePost');
    $routes->get('jury-members', 'AdminJuryController::juryMembers');
    $routes->post('jury-assign-candidate-post', 'AdminJuryController::assignCandidatePost');

    // Voting Audit
    $routes->get('voting-audit-logs', 'Admin\AdminVotingAuditController::logs');
    $routes->get('voting-results-summary', 'Admin\AdminVotingAuditController::resultsSummary');
    $routes->post('voting-recalculate-ranks-post', 'Admin\AdminVotingAuditController::recalculateRanksPost');
    $routes->post('voting-export-audit-csv', 'Admin\AdminVotingAuditController::exportAuditCsv');
});
