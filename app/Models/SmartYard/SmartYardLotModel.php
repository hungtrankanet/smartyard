<?php

namespace App\Models\SmartYard;

use CodeIgniter\Model;

/**
 * SmartYardLotModel
 * Manages inventory lots, area allocation, and status tracking
 */
class SmartYardLotModel extends Model
{
    protected $table = 'smartyard_lots';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'warehouse_id',
        'project_id',
        'lot_code',
        'item_name',
        'initial_area',
        'remaining_area',
        'status',
        'imported_at',
        'notes',
        'file_attachment'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get lots by warehouse with project info
     */
    public function getByWarehouse(int $warehouseId)
    {
        return $this->db->table($this->table . ' l')
            ->select('l.*, p.project_code, p.project_name, w.name as warehouse_name, w.code as warehouse_code')
            ->join('smartyard_projects p', 'p.id = l.project_id', 'left')
            ->join('smartyard_warehouses w', 'w.id = l.warehouse_id', 'left')
            ->where('l.warehouse_id', $warehouseId)
            ->where('l.status !=', 'EXPORTED')
            ->orderBy('l.id', 'DESC')
            ->get()
            ->getResult();
    }

    /**
     * Find lot by code
     */
    public function getByCode(string $lotCode)
    {
        return $this->db->table($this->table . ' l')
            ->select('l.*, p.project_code, p.project_name, w.name as warehouse_name, w.code as warehouse_code')
            ->join('smartyard_projects p', 'p.id = l.project_id', 'left')
            ->join('smartyard_warehouses w', 'w.id = l.warehouse_id', 'left')
            ->where('l.lot_code', $lotCode)
            ->get()
            ->getRow();
    }
}
