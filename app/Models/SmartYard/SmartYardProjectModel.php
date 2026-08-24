<?php

namespace App\Models\SmartYard;

use CodeIgniter\Model;

/**
 * SmartYardProjectModel
 * Manages Project metadata and associated storage metrics
 */
class SmartYardProjectModel extends Model
{
    protected $table = 'smartyard_projects';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'project_code',
        'project_name',
        'description',
        'status'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get active projects with stored lot and area statistics
     */
    public function getProjectsWithStats()
    {
        return $this->db->table($this->table . ' p')
            ->select('p.*, COUNT(l.id) as total_lots, COALESCE(SUM(l.remaining_area), 0) as total_occupied_area')
            ->join('smartyard_lots l', 'l.project_id = p.id AND l.status != "EXPORTED"', 'left')
            ->groupBy('p.id')
            ->orderBy('p.id', 'ASC')
            ->get()
            ->getResult();
    }
}
