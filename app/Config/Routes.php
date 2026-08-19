<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 * 
 * TOP BEST GLOBAL - Modular Route Dispatcher
 * Master router loading specialized, decoupled sub-route modules (< 500 lines constraint).
 */

$routeModules = [
    'GeneralRoutes.php',     // Base portal, cron, and authentication
    'MemberRoutes.php',      // Member & partner portal, events
    'HonorsRoutes.php',      // TOP BEST GLOBAL national honors & branding
    'VotingRoutes.php',      // Voting catalog, OTP authentication & live leaderboard
    'NominationRoutes.php',  // Nomination dossiers, Hall of Fame & Digital Certificates
    'AdminAwardRoutes.php',  // Admin award seasons, categories, jury & voting audit
    'AdminRoutes.php',       // Admin CMS (posts, categories, widgets, settings, users)
    'ApiRoutes.php',         // REST API & tracking endpoints
    'PostRoutes.php',        // Static action POST mappings
    'DynamicRoutes.php',     // Multi-language dynamic routing & fallback catch-alls
];

foreach ($routeModules as $module) {
    $modulePath = APPPATH . 'Config/Routes/' . $module;
    if (is_file($modulePath)) {
        require $modulePath;
    }
}
