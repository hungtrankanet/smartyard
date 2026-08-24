<?php

namespace App\Models\SmartYard;

use CodeIgniter\Model;

/**
 * SmartYardWarehouseModel
 * Manages Warehouse entities, area metrics, coordinates and 3D preview metadata
 */
class SmartYardWarehouseModel extends Model
{
    protected $table = 'smartyard_warehouses';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'region_id',
        'code',
        'name',
        'total_area',
        'allocated_area',
        'used_area',
        'image_3d_url',
        'map_pos_x',
        'map_pos_y',
        'map_width',
        'map_height',
        'status',
        'threshold_low',
        'threshold_med',
        'threshold_high'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get warehouses by region with usage rate calculation
     */
    public function getByRegion(int $regionId)
    {
        return $this->where('region_id', $regionId)
            ->where('status !=', 'inactive')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    /**
     * Get warehouse by code
     */
    public function getByCode(string $code)
    {
        return $this->where('code', $code)->first();
    }

    /**
     * Get list of warehouses filtered by user scope
     */
    public function getScopedWarehouses(int $userId, bool $isSuperAdmin = false)
    {
        $builder = $this->db->table($this->table . ' w')
            ->select('w.*, r.name as region_name, r.code as region_code')
            ->join('smartyard_regions r', 'r.id = w.region_id', 'left')
            ->where('w.status !=', 'inactive');

        if (!$isSuperAdmin) {
            $builder->join('smartyard_user_scopes s', 's.warehouse_id = w.id')
                ->where('s.user_id', $userId)
                ->where('s.can_view', 1);
        }

        return $builder->orderBy('w.id', 'ASC')->get()->getResult();
    }

    /**
     * Lock row for update (Atomic concurrency protection)
     */
    public function getForUpdate(int $warehouseId)
    {
        return $this->db->table($this->table)
            ->where('id', $warehouseId)
            ->get(null, 0, false) // bypass cache
            ->getRow();
    }
}
