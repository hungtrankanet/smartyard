<?php

/**
 * SMART YARD PETRO — Routing Module
 * High-performance routes for Map 2D, 3D Warehouse, Inventory, Dashboard, Kiosk & AI
 */

$routes->group('smartyard', function($routes) {
    // 2D Map & 3D Warehouse Detail
    $routes->get('/', 'SmartYard\SmartYardMapController::index');
    $routes->get('map', 'SmartYard\SmartYardMapController::index');
    $routes->get('warehouse/(:num)', 'SmartYard\SmartYardMapController::getWarehouseDetail/$1');

    // Dashboard & Kiosk Touchscreen
    $routes->get('dashboard', 'SmartYard\SmartYardDashboardController::index');
    $routes->get('kiosk', 'SmartYard\SmartYardDashboardController::kiosk');

    // Inventory Workflow (Import, Export, Lots, History)
    $routes->get('inventory/import', 'SmartYard\SmartYardInventoryController::import');
    $routes->post('inventory/import', 'SmartYard\SmartYardInventoryController::submitImport');
    $routes->get('inventory/export', 'SmartYard\SmartYardInventoryController::export');
    $routes->post('inventory/export', 'SmartYard\SmartYardInventoryController::submitExport');
    $routes->get('inventory/lots', 'SmartYard\SmartYardInventoryController::lots');
    $routes->get('inventory/transactions', 'SmartYard\SmartYardInventoryController::transactions');

    // Admin & Configurations
    $routes->get('admin/settings', 'SmartYard\SmartYardAdminController::settings');
    $routes->post('admin/settings', 'SmartYard\SmartYardAdminController::saveSettings');
    $routes->get('admin/scopes', 'SmartYard\SmartYardAdminController::scopes');
    $routes->post('admin/scopes', 'SmartYard\SmartYardAdminController::saveScope');
    $routes->get('admin/excel-import', 'SmartYard\SmartYardAdminController::excelImport');
});

// Smart Yard API endpoints
$routes->group('api/smartyard', function($routes) {
    $routes->get('map-data', 'SmartYard\SmartYardApiController::getMapData');
    $routes->get('lots-by-warehouse/(:num)', 'SmartYard\SmartYardApiController::getLotsByWarehouse/$1');
    $routes->post('ai-query', 'SmartYard\SmartYardApiController::aiQuery');
    $routes->get('ai-query', 'SmartYard\SmartYardApiController::aiQuery');
});
