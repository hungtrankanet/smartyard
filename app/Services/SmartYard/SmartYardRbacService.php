<?php

namespace App\Services\SmartYard;

use App\Models\SmartYard\SmartYardUserScopeModel;
use App\Models\AuthModel;

/**
 * SmartYardRbacService
 * Enforces multi-tier RBAC & Scope authorization (Role -> Warehouse Scope -> Area Allocation)
 */
class SmartYardRbacService
{
    protected $scopeModel;
    protected $authModel;

    public function __construct()
    {
        $this->scopeModel = new SmartYardUserScopeModel();
        $this->authModel = new AuthModel();
    }

    /**
     * Check if a user is Super Admin or Manager with global view
     */
    public function isSuperAdmin(object $user): bool
    {
        if (empty($user)) return false;
        // Role ID 1 is Super Admin
        return (isset($user->role_id) && (int)$user->role_id === 1) || 
               (isset($user->role) && in_array(strtolower($user->role), ['admin', 'super_admin']));
    }

    /**
     * Check if user is Manager (Xem toàn bộ kho, dashboard, báo cáo)
     */
    public function isManager(object $user): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        return (isset($user->role) && strtolower($user->role) === 'manager') ||
               (isset($user->role_id) && (int)$user->role_id === 2);
    }

    /**
     * Validate whether user has permission to perform action on a specific warehouse
     * Actions: 'view', 'import', 'export', 'admin'
     */
    public function canAccessWarehouse(object $user, int $warehouseId, string $action = 'view'): bool
    {
        if (empty($user)) return false;

        // Super Admin has unrestricted access
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        // Manager can view all warehouses
        if ($action === 'view' && $this->isManager($user)) {
            return true;
        }

        // Check explicit warehouse scope for Trưởng kho / Nhân viên nhập liệu
        $scope = $this->scopeModel->getUserScope((int)$user->id, $warehouseId);
        if (!$scope) {
            return false;
        }

        switch ($action) {
            case 'view':
                return (bool)$scope->can_view;
            case 'import':
                return (bool)$scope->can_import;
            case 'export':
                return (bool)$scope->can_export;
            case 'admin':
                return false; // Only Super Admin can manage configuration
            default:
                return false;
        }
    }

    /**
     * Get array of warehouse IDs the user is allowed to access
     */
    public function getAllowedWarehouseIds(object $user, string $action = 'view'): array
    {
        if (empty($user)) return [];

        if ($this->isSuperAdmin($user) || ($action === 'view' && $this->isManager($user))) {
            return ['*']; // Global access
        }

        $scopes = $this->scopeModel->getUserWarehouses((int)$user->id);
        $allowed = [];
        foreach ($scopes as $s) {
            if ($action === 'view' && $s->can_view) $allowed[] = (int)$s->warehouse_id;
            if ($action === 'import' && $s->can_import) $allowed[] = (int)$s->warehouse_id;
            if ($action === 'export' && $s->can_export) $allowed[] = (int)$s->warehouse_id;
        }
        return $allowed;
    }
}
