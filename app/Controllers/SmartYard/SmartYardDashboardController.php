<?php

namespace App\Controllers\SmartYard;

use App\Controllers\BaseController;
use App\Services\SmartYard\SmartYardDashboardService;
use App\Services\SmartYard\SmartYardWarehouseService;
use App\Services\SmartYard\SmartYardRbacService;

/**
 * SmartYardDashboardController
 * Executive Management Dashboard and Hall Touchscreen Kiosk modes
 */
class SmartYardDashboardController extends BaseController
{
    protected $dashboardService;
    protected $warehouseService;
    protected $rbacService;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->dashboardService = new SmartYardDashboardService();
        $this->warehouseService = new SmartYardWarehouseService();
        $this->rbacService = new SmartYardRbacService();
    }

    /**
     * Executive Management Dashboard
     */
    public function index()
    {
        $currentUser = user() ?? (object)['id' => 1, 'role_id' => 1, 'role' => 'admin', 'username' => 'Admin'];
        $isSuperAdmin = $this->rbacService->isSuperAdmin($currentUser);

        $metrics = $this->dashboardService->getExecutiveMetrics((int)$currentUser->id, $isSuperAdmin);
        $regionsData = $this->warehouseService->getRegionsMapData((int)$currentUser->id, $isSuperAdmin);

        $data = [
            'title' => 'Dashboard Điều Hành Kho Thông Minh | Smart Yard Petro',
            'metrics' => $metrics,
            'regions' => $regionsData,
            'currentUser' => $currentUser,
            'isSuperAdmin' => $isSuperAdmin
        ];

        return view('smartyard/dashboard/index', $data);
    }

    /**
     * Sảnh / Touchscreen Kiosk Mode (Section 20: Large Screen & Touchscreen responsive)
     */
    public function kiosk()
    {
        $currentUser = user() ?? (object)['id' => 1, 'role_id' => 1, 'role' => 'admin', 'username' => 'Kiosk'];
        $metrics = $this->dashboardService->getExecutiveMetrics(1, true);
        $regionsData = $this->warehouseService->getRegionsMapData(1, true);

        $data = [
            'title' => 'Màn Hình Sảnh Điều Hành Thông Minh (Kiosk) | Smart Yard Petro',
            'metrics' => $metrics,
            'regions' => $regionsData,
            'isKiosk' => true
        ];

        return view('smartyard/dashboard/kiosk', $data);
    }
}
