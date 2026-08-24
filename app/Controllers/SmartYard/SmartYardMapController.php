<?php

namespace App\Controllers\SmartYard;

use App\Controllers\BaseController;
use App\Services\SmartYard\SmartYardWarehouseService;
use App\Services\SmartYard\SmartYardRbacService;
use App\Models\SmartYard\SmartYardWarehouseModel;
use App\Models\SmartYard\SmartYardLotModel;
use App\Models\SmartYard\SmartYardRegionModel;

/**
 * SmartYardMapController
 * Controller for interactive 2D Warehouse Maps, multi-region navigation and 3D preview
 */
class SmartYardMapController extends BaseController
{
    protected $warehouseService;
    protected $rbacService;
    protected $warehouseModel;
    protected $lotModel;
    protected $regionModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->warehouseService = new SmartYardWarehouseService();
        $this->rbacService = new SmartYardRbacService();
        $this->warehouseModel = new SmartYardWarehouseModel();
        $this->lotModel = new SmartYardLotModel();
        $this->regionModel = new SmartYardRegionModel();
    }

    /**
     * Main 2D Map View (Desktop & Touchscreen interactive)
     */
    public function index()
    {
        $currentUser = user() ?? (object)['id' => 1, 'role_id' => 1, 'role' => 'admin', 'username' => 'Admin'];
        $isSuperAdmin = $this->rbacService->isSuperAdmin($currentUser);

        $regionsData = $this->warehouseService->getRegionsMapData((int)$currentUser->id, $isSuperAdmin);

        $data = [
            'title' => 'Sơ đồ kho 2D trực quan | Smart Yard Petro',
            'description' => 'Hệ thống bản đồ 2D trực quan quản trị kho và diện tích lưu trữ',
            'regions' => $regionsData,
            'currentUser' => $currentUser,
            'isSuperAdmin' => $isSuperAdmin
        ];

        return view('smartyard/map/index', $data);
    }

    /**
     * AJAX endpoint to get warehouse detail drawer with fixed 3D representative image and stored lots
     */
    public function getWarehouseDetail(int $warehouseId)
    {
        $currentUser = user() ?? (object)['id' => 1, 'role_id' => 1, 'role' => 'admin', 'username' => 'Admin'];

        if (!$this->rbacService->canAccessWarehouse($currentUser, $warehouseId, 'view')) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Bạn không có quyền truy cập dữ liệu của kho này.'
            ]);
        }

        $warehouse = $this->warehouseModel->find($warehouseId);
        if (!$warehouse) {
            return $this->response->setJSON(['status' => false, 'message' => 'Không tìm thấy kho.']);
        }

        $computedWh = $this->warehouseService->computeWarehouseState($warehouse);
        $lots = $this->lotModel->getByWarehouse($warehouseId);
        $region = $this->regionModel->find($warehouse->region_id);

        $canImport = $this->rbacService->canAccessWarehouse($currentUser, $warehouseId, 'import');
        $canExport = $this->rbacService->canAccessWarehouse($currentUser, $warehouseId, 'export');

        return $this->response->setJSON([
            'status' => true,
            'warehouse' => $computedWh,
            'region' => $region,
            'lots' => $lots,
            'permissions' => [
                'can_import' => $canImport,
                'can_export' => $canExport
            ]
        ]);
    }
}
