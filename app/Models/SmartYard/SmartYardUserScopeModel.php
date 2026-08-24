<?php

namespace App\Models\SmartYard;

use CodeIgniter\Model;

/**
 * SmartYardUserScopeModel
 * Manages RBAC Scope assignments for Users -> Warehouses
 */
class SmartYardUserScopeModel extends Model
{
    protected $table = 'smartyard_user_scopes';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'user_id',
        'warehouse_id',
        'max_allocated_area',
        'can_view',
        'can_import',
        'can_export'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get user scope permissions for a specific warehouse
     */
    public function getUserScope(int $userId, int $warehouseId)
    {
        return $this->where('user_id', $userId)
            ->where('warehouse_id', $warehouseId)
            ->first();
    }

    /**
     * Get all warehouse scopes assigned to a user
     */
    public function getUserWarehouses(int $userId)
    {
        return $this->db->table($this->table . ' s')
            ->select('s.*, w.code as warehouse_code, w.name as warehouse_name, w.total_area, w.allocated_area, w.used_area, w.available_area, r.name as region_name')
            ->join('smartyard_warehouses w', 'w.id = s.warehouse_id')
            ->join('smartyard_regions r', 'r.id = w.region_id', 'left')
            ->where('s.user_id', $userId)
            ->get()
            ->getResult();
    }
}
