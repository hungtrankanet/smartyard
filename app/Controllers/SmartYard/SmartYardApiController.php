<?php

namespace App\Controllers\SmartYard;

use App\Controllers\BaseController;
use App\Services\SmartYard\SmartYardWarehouseService;
use App\Services\SmartYard\SmartYardInventoryService;
use App\Services\SmartYard\SmartYardAiService;
use App\Services\SmartYard\SmartYardRbacService;
use App\Models\SmartYard\SmartYardLotModel;
use App\Models\SmartYard\SmartYardWarehouseModel;

/**
 * SmartYardApiController
 * REST API for asynchronous UI operations, AI assistant, and warehouse state queries
 */
class SmartYardApiController extends BaseController
{
    protected $warehouseService;
    protected $inventoryService;
    protected $aiService;
    protected $rbacService;
    protected $lotModel;
    protected $warehouseModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->warehouseService = new SmartYardWarehouseService();
        $this->inventoryService = new SmartYardInventoryService();
        $this->aiService = new SmartYardAiService();
        $this->rbacService = new SmartYardRbacService();
        $this->lotModel = new SmartYardLotModel();
        $this->warehouseModel = new SmartYardWarehouseModel();
    }

    /**
     * Map Regions and Scoped Warehouses
     */
    public function getMapData()
    {
        $currentUser = user() ?? (object)['id' => 1, 'role_id' => 1, 'role' => 'admin', 'username' => 'Admin'];
        $isSuperAdmin = $this->rbacService->isSuperAdmin($currentUser);

        $data = $this->warehouseService->getRegionsMapData((int)$currentUser->id, $isSuperAdmin);
        return $this->response->setJSON(['status' => true, 'data' => $data]);
    }

    /**
     * Get Stored Lots for a specific Warehouse (for export selector)
     */
    public function getLotsByWarehouse(int $warehouseId)
    {
        $currentUser = user() ?? (object)['id' => 1, 'role_id' => 1, 'role' => 'admin', 'username' => 'Admin'];
        if (!$this->rbacService->canAccessWarehouse($currentUser, $warehouseId, 'view')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Không có quyền truy cập kho này.']);
        }

        $lots = $this->lotModel->getByWarehouse($warehouseId);
        return $this->response->setJSON(['status' => true, 'lots' => $lots]);
    }

    /**
     * AI Query endpoint with RBAC context protection
     */
    public function aiQuery()
    {
        $currentUser = user() ?? (object)['id' => 1, 'role_id' => 1, 'role' => 'admin', 'username' => 'Admin'];
        $queryText = $this->request->getPost('query') ?? $this->request->getVar('query') ?? '';

        if (empty(trim($queryText))) {
            return $this->response->setJSON(['status' => false, 'response' => 'Vui lòng nhập câu hỏi.']);
        }

        $result = $this->aiService->query($currentUser, $queryText);
        return $this->response->setJSON($result);
    }
}
