<?php

namespace App\Models\SmartYard;

use CodeIgniter\Model;

/**
 * SmartYardTransactionModel
 * Manages immutable transaction logs (IMPORT, EXPORT, ADJUSTMENT)
 */
class SmartYardTransactionModel extends Model
{
    protected $table = 'smartyard_transactions';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useSoftDeletes = false; // Rule 05: Immutable history
    protected $allowedFields = [
        'warehouse_id',
        'lot_id',
        'project_id',
        'user_id',
        'transaction_type',
        'area',
        'warehouse_used_before',
        'warehouse_used_after',
        'lot_remaining_before',
        'lot_remaining_after',
        'note',
        'ip_address'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = ''; // Transactions are immutable

    /**
     * Get transaction logs with full relations and filters
     */
    public function getTransactionsList(array $filters = [], int $limit = 50, int $offset = 0)
    {
        $builder = $this->db->table($this->table . ' t')
            ->select('t.*, w.name as warehouse_name, w.code as warehouse_code, l.lot_code, l.item_name, p.project_code, p.project_name, u.username as user_name')
            ->join('smartyard_warehouses w', 'w.id = t.warehouse_id', 'left')
            ->join('smartyard_lots l', 'l.id = t.lot_id', 'left')
            ->join('smartyard_projects p', 'p.id = t.project_id', 'left')
            ->join('users u', 'u.id = t.user_id', 'left');

        if (!empty($filters['warehouse_id'])) {
            $builder->where('t.warehouse_id', $filters['warehouse_id']);
        }
        if (!empty($filters['project_id'])) {
            $builder->where('t.project_id', $filters['project_id']);
        }
        if (!empty($filters['transaction_type'])) {
            $builder->where('t.transaction_type', $filters['transaction_type']);
        }
        if (!empty($filters['user_id'])) {
            $builder->where('t.user_id', $filters['user_id']);
        }

        return $builder->orderBy('t.id', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResult();
    }
}
