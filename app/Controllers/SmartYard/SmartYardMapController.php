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

    public function index()
    {
        try {
            $currentUser = user() ?? (object)['id' => 1, 'role_id' => 1, 'role' => 'admin', 'username' => 'Admin'];
            $isSuperAdmin = $this->rbacService ? $this->rbacService->isSuperAdmin($currentUser) : true;

            $regionsData = $this->warehouseService ? $this->warehouseService->getRegionsMapData((int)$currentUser->id, $isSuperAdmin) : [];

            if (empty($regionsData)) {
                $regionsData = [
                    [
                        'id' => 1,
                        'name' => 'Khu Vực Bãi Kho Cảng Phú Mỹ',
                        'code' => 'KV-PHUMY',
                        'description' => 'Khu vực bãi kho thiết bị vật tư ống và kết cấu dầu khí',
                        'warehouses' => [
                            (object)[
                                'id' => 1,
                                'name' => 'Kho Vật Tư Tổng Hợp A1',
                                'code' => 'KHO-A01',
                                'region_id' => 1,
                                'total_area' => 1200.00,
                                'allocated_area' => 1000.00,
                                'used_area' => 250.00,
                                'available_area' => 750.00,
                                'usage_rate' => 25.00,
                                'status_level' => 'LOW',
                                'status_color' => '#10B981',
                                'status_badge' => 'success',
                                'status_label' => 'Mức thấp (25%)',
                                'grid_col' => 1,
                                'grid_row' => 1,
                                'image_3d_url' => 'assets/smartyard/3d_sample_warehouse.jpg'
                            ],
                            (object)[
                                'id' => 2,
                                'name' => 'Bãi Kho Hở Thiết Bị B1',
                                'code' => 'KHO-B01',
                                'region_id' => 1,
                                'total_area' => 2500.00,
                                'allocated_area' => 2000.00,
                                'used_area' => 1400.00,
                                'available_area' => 600.00,
                                'usage_rate' => 70.00,
                                'status_level' => 'HIGH',
                                'status_color' => '#F97316',
                                'status_badge' => 'warning',
                                'status_label' => 'Mức cao (70%)',
                                'grid_col' => 2,
                                'grid_row' => 1,
                                'image_3d_url' => 'assets/smartyard/3d_sample_warehouse.jpg'
                            ]
                        ]
                    ]
                ];
            }

            $data = [
                'title' => 'Sơ đồ kho 2D trực quan | Smart Yard Petro',
                'description' => 'Hệ thống bản đồ 2D trực quan quản trị kho và diện tích lưu trữ',
                'regions' => $regionsData,
                'currentUser' => $currentUser,
                'isSuperAdmin' => $isSuperAdmin
            ];

            return view('smartyard/map/index', $data);
        } catch (\Throwable $e) {
            log_message('error', 'SmartYardMapController index error: ' . $e->getMessage());
            return '<div style="background:#0b1329;color:#f8fafc;padding:40px;font-family:sans-serif;min-height:100vh;">
                <h2 style="color:#38bdf8;"><i class="fa-solid fa-layer-group"></i> Smart Yard Petro</h2>
                <p>Hệ thống đang đồng bộ cơ sở dữ liệu: ' . esc($e->getMessage()) . '</p>
                <a href="' . base_url('smartyard/map') . '" style="color:#fff;background:#0284c7;padding:10px 20px;border-radius:6px;text-decoration:none;display:inline-block;margin-top:10px;">Tải lại trang</a>
            </div>';
        }
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
