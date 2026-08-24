<?php

namespace App\Models\SmartYard;

use CodeIgniter\Model;

/**
 * SmartYardRegionModel
 * Manages Smart Yard Regions (Khu vực kho)
 */
class SmartYardRegionModel extends Model
{
    protected $table = 'smartyard_regions';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'code',
        'name',
        'description',
        'map_layout_json',
        'status'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get all active regions with warehouse counts
     */
    public function getActiveRegionsWithStats()
    {
        return $this->db->table($this->table)
            ->select('smartyard_regions.*, COUNT(w.id) as total_warehouses, COALESCE(SUM(w.total_area), 0) as region_total_area, COALESCE(SUM(w.allocated_area), 0) as region_allocated_area, COALESCE(SUM(w.used_area), 0) as region_used_area')
            ->join('smartyard_warehouses w', 'w.region_id = smartyard_regions.id AND w.status = "active"', 'left')
            ->where('smartyard_regions.status', 'active')
            ->groupBy('smartyard_regions.id')
            ->orderBy('smartyard_regions.id', 'ASC')
            ->get()
            ->getResult();
    }

    /**
     * Find region by code
     */
    public function getByCode(string $code)
    {
        return $this->where('code', $code)->first();
    }
}
