<?php

namespace App\Models\SmartYard;

use CodeIgniter\Model;

/**
 * SmartYardAuditModel
 * Tracks all security events, logins, modifications, imports and exports
 */
class SmartYardAuditModel extends Model
{
    protected $table = 'smartyard_audit_logs';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'user_id',
        'action',
        'object_type',
        'object_id',
        'before_data',
        'after_data',
        'ip_address',
        'user_agent'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';

    /**
     * Fetch audit logs with user info
     */
    public function getLogs(array $filters = [], int $limit = 100, int $offset = 0)
    {
        $builder = $this->db->table($this->table . ' a')
            ->select('a.*, u.username, u.email')
            ->join('users u', 'u.id = a.user_id', 'left');

        if (!empty($filters['user_id'])) {
            $builder->where('a.user_id', $filters['user_id']);
        }
        if (!empty($filters['action'])) {
            $builder->where('a.action', $filters['action']);
        }
        if (!empty($filters['object_type'])) {
            $builder->where('a.object_type', $filters['object_type']);
        }

        return $builder->orderBy('a.id', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResult();
    }
}
