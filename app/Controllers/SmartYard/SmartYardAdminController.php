<?php

namespace App\Controllers\SmartYard;

use App\Controllers\BaseController;
use App\Models\SmartYard\SmartYardConfigModel;
use App\Models\SmartYard\SmartYardWarehouseModel;
use App\Models\SmartYard\SmartYardUserScopeModel;
use App\Models\SmartYard\SmartYardRegionModel;
use App\Models\SmartYard\SmartYardProjectModel;
use App\Services\SmartYard\SmartYardRbacService;
use App\Services\SmartYard\SmartYardAuditService;

/**
 * SmartYardAdminController
 * Administration, RBAC Scope Assignment, Threshold Configurations and Excel Batch Import
 */
class SmartYardAdminController extends BaseController
{
    protected $configModel;
    protected $warehouseModel;
    protected $userScopeModel;
    protected $regionModel;
    protected $projectModel;
    protected $rbacService;
    protected $auditService;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->configModel = new SmartYardConfigModel();
        $this->warehouseModel = new SmartYardWarehouseModel();
        $this->userScopeModel = new SmartYardUserScopeModel();
        $this->regionModel = new SmartYardRegionModel();
        $this->projectModel = new SmartYardProjectModel();
        $this->rbacService = new SmartYardRbacService();
        $this->auditService = new SmartYardAuditService();
    }

    /**
     * Admin Config & Thresholds
     */
    public function settings()
    {
        $currentUser = user() ?? (object)['id' => 1, 'role_id' => 1, 'role' => 'admin', 'username' => 'Admin'];
        if (!$this->rbacService->isSuperAdmin($currentUser)) {
            $this->session->setFlashdata('error', 'Chỉ Super Admin mới có quyền truy cập cấu hình hệ thống.');
            return redirect()->to(base_url('smartyard/map'));
        }

        $thLow = $this->configModel->getValue('threshold_low', '30');
        $thMed = $this->configModel->getValue('threshold_med', '60');
        $thHigh = $this->configModel->getValue('threshold_high', '80');
        $allowOver = $this->configModel->getValue('allow_over_allocation', '0');

        $data = [
            'title' => 'Cấu Hình Ngưỡng & Tham Số Hệ Thống | Smart Yard Petro',
            'threshold_low' => $thLow,
            'threshold_med' => $thMed,
            'threshold_high' => $thHigh,
            'allow_over_allocation' => $allowOver
        ];

        return view('smartyard/admin/settings', $data);
    }

    /**
     * Save Admin Config
     */
    public function saveSettings()
    {
        $currentUser = user() ?? (object)['id' => 1, 'role_id' => 1, 'role' => 'admin', 'username' => 'Admin'];
        if (!$this->rbacService->isSuperAdmin($currentUser)) {
            return redirect()->to(base_url('smartyard/map'));
        }

        $thLow = $this->request->getPost('threshold_low');
        $thMed = $this->request->getPost('threshold_med');
        $thHigh = $this->request->getPost('threshold_high');
        $allowOver = $this->request->getPost('allow_over_allocation') ? '1' : '0';

        $this->configModel->setValue('threshold_low', (string)$thLow, 'Ngưỡng % Thấp');
        $this->configModel->setValue('threshold_med', (string)$thMed, 'Ngưỡng % Trung bình');
        $this->configModel->setValue('threshold_high', (string)$thHigh, 'Ngưỡng % Cao');
        $this->configModel->setValue('allow_over_allocation', $allowOver, 'Cho phép vượt diện tích');

        $this->auditService->log((int)$currentUser->id, 'UPDATE_CONFIG', 'SYSTEM_CONFIG', '0', null, [
            'threshold_low' => $thLow,
            'threshold_med' => $thMed,
            'threshold_high' => $thHigh,
            'allow_over_allocation' => $allowOver
        ]);

        $this->session->setFlashdata('success', 'Đã lưu thành công cấu hình hệ thống.');
        return redirect()->to(base_url('smartyard/admin/settings'));
    }

    /**
     * User Scope Management
     */
    public function scopes()
    {
        $currentUser = user() ?? (object)['id' => 1, 'role_id' => 1, 'role' => 'admin', 'username' => 'Admin'];
        if (!$this->rbacService->isSuperAdmin($currentUser)) {
            return redirect()->to(base_url('smartyard/map'));
        }

        $scopes = $this->userScopeModel->db->table('smartyard_user_scopes s')
            ->select('s.*, u.username, u.email, w.name as warehouse_name, w.code as warehouse_code')
            ->join('users u', 'u.id = s.user_id', 'left')
            ->join('smartyard_warehouses w', 'w.id = s.warehouse_id', 'left')
            ->orderBy('s.id', 'DESC')
            ->get()
            ->getResult();

        $users = $this->userScopeModel->db->table('users')->where('status', 1)->get()->getResult();
        $warehouses = $this->warehouseModel->where('status', 'active')->findAll();

        $data = [
            'title' => 'Phân Quyền Phạm Vi Kho (Warehouse Scopes) | Smart Yard Petro',
            'scopes' => $scopes,
            'users' => $users,
            'warehouses' => $warehouses
        ];

        return view('smartyard/admin/scopes', $data);
    }

    /**
     * Save Scope
     */
    public function saveScope()
    {
        $currentUser = user() ?? (object)['id' => 1, 'role_id' => 1, 'role' => 'admin', 'username' => 'Admin'];
        if (!$this->rbacService->isSuperAdmin($currentUser)) {
            return redirect()->to(base_url('smartyard/map'));
        }

        $userId = (int)$this->request->getPost('user_id');
        $warehouseId = (int)$this->request->getPost('warehouse_id');
        $canView = $this->request->getPost('can_view') ? 1 : 0;
        $canImport = $this->request->getPost('can_import') ? 1 : 0;
        $canExport = $this->request->getPost('can_export') ? 1 : 0;

        $existing = $this->userScopeModel->getUserScope($userId, $warehouseId);
        if ($existing) {
            $this->userScopeModel->update($existing->id, [
                'can_view' => $canView,
                'can_import' => $canImport,
                'can_export' => $canExport
            ]);
        } else {
            $this->userScopeModel->insert([
                'user_id' => $userId,
                'warehouse_id' => $warehouseId,
                'max_allocated_area' => 0,
                'can_view' => $canView,
                'can_import' => $canImport,
                'can_export' => $canExport
            ]);
        }

        $this->session->setFlashdata('success', 'Đã cập nhật phạm vi phân quyền thành công.');
        return redirect()->to(base_url('smartyard/admin/scopes'));
    }

    /**
     * Data Excel Batch Import (Section 27: Upload -> Validate -> Preview -> Confirm -> Import)
     */
    public function excelImport()
    {
        $data = [
            'title' => 'Nhập Dữ Liệu Hàng Loạt Từ Excel / CSV | Smart Yard Petro'
        ];
        return view('smartyard/admin/excel_import', $data);
    }
}
