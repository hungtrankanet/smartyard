<?php

namespace App\Controllers\SmartYard;

use App\Controllers\BaseController;
use App\Services\SmartYard\SmartYardInventoryService;
use App\Services\SmartYard\SmartYardRbacService;
use App\Models\SmartYard\SmartYardWarehouseModel;
use App\Models\SmartYard\SmartYardProjectModel;
use App\Models\SmartYard\SmartYardLotModel;
use App\Models\SmartYard\SmartYardTransactionModel;

/**
 * SmartYardInventoryController
 * Manages Lot Imports, Exports, and Audit Transaction Histories
 */
class SmartYardInventoryController extends BaseController
{
    protected $inventoryService;
    protected $rbacService;
    protected $warehouseModel;
    protected $projectModel;
    protected $lotModel;
    protected $transactionModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->inventoryService = new SmartYardInventoryService();
        $this->rbacService = new SmartYardRbacService();
        $this->warehouseModel = new SmartYardWarehouseModel();
        $this->projectModel = new SmartYardProjectModel();
        $this->lotModel = new SmartYardLotModel();
        $this->transactionModel = new SmartYardTransactionModel();
    }

    /**
     * Import Lot Page
     */
    public function import()
    {
        $currentUser = user() ?? (object)['id' => 1, 'role_id' => 1, 'role' => 'admin', 'username' => 'Admin'];
        $isSuperAdmin = $this->rbacService->isSuperAdmin($currentUser);

        $warehouses = $this->warehouseModel->getScopedWarehouses((int)$currentUser->id, $isSuperAdmin);
        $projects = $this->projectModel->where('status', 'active')->findAll();

        $selectedWarehouseId = $this->request->getGet('warehouse_id');

        $data = [
            'title' => 'Nhập Lô Hàng Vào Kho | Smart Yard Petro',
            'warehouses' => $warehouses,
            'projects' => $projects,
            'selectedWarehouseId' => $selectedWarehouseId,
            'currentUser' => $currentUser
        ];

        return view('smartyard/inventory/import', $data);
    }

    /**
     * Submit Import Lot
     */
    public function submitImport()
    {
        $currentUser = user() ?? (object)['id' => 1, 'role_id' => 1, 'role' => 'admin', 'username' => 'Admin'];

        $postData = [
            'warehouse_id' => $this->request->getPost('warehouse_id'),
            'project_id' => $this->request->getPost('project_id'),
            'lot_code' => $this->request->getPost('lot_code'),
            'item_name' => $this->request->getPost('item_name'),
            'area' => $this->request->getPost('area'),
            'notes' => $this->request->getPost('notes')
        ];

        $result = $this->inventoryService->importLot($currentUser, $postData);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($result);
        }

        if ($result['status']) {
            $this->session->setFlashdata('success', $result['message']);
            return redirect()->to(base_url('smartyard/inventory/lots'));
        } else {
            $this->session->setFlashdata('error', $result['message']);
            return redirect()->back()->withInput();
        }
    }

    /**
     * Export Lot Page
     */
    public function export()
    {
        $currentUser = user() ?? (object)['id' => 1, 'role_id' => 1, 'role' => 'admin', 'username' => 'Admin'];
        $isSuperAdmin = $this->rbacService->isSuperAdmin($currentUser);

        $warehouses = $this->warehouseModel->getScopedWarehouses((int)$currentUser->id, $isSuperAdmin);
        $selectedLotId = $this->request->getGet('lot_id');

        $data = [
            'title' => 'Xuất Lô Hàng Khỏi Kho | Smart Yard Petro',
            'warehouses' => $warehouses,
            'selectedLotId' => $selectedLotId,
            'currentUser' => $currentUser
        ];

        return view('smartyard/inventory/export', $data);
    }

    /**
     * Submit Export Lot
     */
    public function submitExport()
    {
        $currentUser = user() ?? (object)['id' => 1, 'role_id' => 1, 'role' => 'admin', 'username' => 'Admin'];

        $postData = [
            'lot_id' => $this->request->getPost('lot_id'),
            'export_area' => $this->request->getPost('export_area'),
            'notes' => $this->request->getPost('notes')
        ];

        $result = $this->inventoryService->exportLot($currentUser, $postData);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($result);
        }

        if ($result['status']) {
            $this->session->setFlashdata('success', $result['message']);
            return redirect()->to(base_url('smartyard/inventory/lots'));
        } else {
            $this->session->setFlashdata('error', $result['message']);
            return redirect()->back()->withInput();
        }
    }

    /**
     * Active Lots List
     */
    public function lots()
    {
        $currentUser = user() ?? (object)['id' => 1, 'role_id' => 1, 'role' => 'admin', 'username' => 'Admin'];
        $isSuperAdmin = $this->rbacService->isSuperAdmin($currentUser);

        $warehouseId = $this->request->getGet('warehouse_id');
        $projectId = $this->request->getGet('project_id');

        $builder = $this->lotModel->db->table('smartyard_lots l')
            ->select('l.*, w.name as warehouse_name, w.code as warehouse_code, p.project_name, p.project_code')
            ->join('smartyard_warehouses w', 'w.id = l.warehouse_id', 'left')
            ->join('smartyard_projects p', 'p.id = l.project_id', 'left');

        if (!$isSuperAdmin) {
            $builder->join('smartyard_user_scopes s', 's.warehouse_id = w.id')
                ->where('s.user_id', (int)$currentUser->id)
                ->where('s.can_view', 1);
        }

        if (!empty($warehouseId)) {
            $builder->where('l.warehouse_id', (int)$warehouseId);
        }
        if (!empty($projectId)) {
            $builder->where('l.project_id', (int)$projectId);
        }

        $lots = $builder->orderBy('l.id', 'DESC')->get()->getResult();

        $warehouses = $this->warehouseModel->getScopedWarehouses((int)$currentUser->id, $isSuperAdmin);
        $projects = $this->projectModel->findAll();

        $data = [
            'title' => 'Quản Lý Lô Hàng Theo Dự Án | Smart Yard Petro',
            'lots' => $lots,
            'warehouses' => $warehouses,
            'projects' => $projects,
            'selectedWarehouseId' => $warehouseId,
            'selectedProjectId' => $projectId
        ];

        return view('smartyard/inventory/lots', $data);
    }

    /**
     * Transaction History (Immutable audit)
     */
    public function transactions()
    {
        $currentUser = user() ?? (object)['id' => 1, 'role_id' => 1, 'role' => 'admin', 'username' => 'Admin'];
        $filters = [
            'warehouse_id' => $this->request->getGet('warehouse_id'),
            'project_id' => $this->request->getGet('project_id'),
            'transaction_type' => $this->request->getGet('transaction_type')
        ];

        $transactions = $this->transactionModel->getTransactionsList($filters, 100, 0);

        $data = [
            'title' => 'Lịch Sử Giao Dịch Nhập / Xuất Kho | Smart Yard Petro',
            'transactions' => $transactions,
            'filters' => $filters
        ];

        return view('smartyard/inventory/transactions', $data);
    }
}
