<?php

namespace App\Models;

/**
 * MemberVerifyLogModel: Tracks automated and manual business verification audit logs
 */
class MemberVerifyLogModel extends BaseModel
{
    protected $table = 'member_verify_logs';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'member_id',
        'check_type',
        'result',
        'detail',
        'checked_at',
    ];
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('member_verify_logs');
    }

    /**
     * Get verification logs for a member
     */
    public function getLogsByMemberId($memberId, int $limit = 50): array
    {
        return $this->builder
            ->where('member_id', clrNum($memberId))
            ->orderBy('checked_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->limit($limit)
            ->get()
            ->getResult();
    }

    /**
     * Get single log with member info
     */
    public function getLog($id)
    {
        return $this->db->table('member_verify_logs')
            ->select('member_verify_logs.*, members.company_name, members.tax_code, members.phone, members.email, members.city')
            ->join('members', 'members.id = member_verify_logs.member_id', 'left')
            ->where('member_verify_logs.id', clrNum($id))
            ->get()
            ->getRow();
    }

    /**
     * Add a verification log record
     */
    public function addLog(array $data)
    {
        $memberId = clrNum($data['member_id'] ?? 0);
        $checkType = strTrim($data['check_type'] ?? '');
        $result = strTrim($data['result'] ?? '');

        if (empty($memberId) || empty($checkType) || empty($result)) {
            return false;
        }

        $detail = null;
        if (!empty($data['detail'])) {
            $detail = is_array($data['detail']) ? json_encode($data['detail'], JSON_UNESCAPED_UNICODE) : $data['detail'];
        }

        $insertData = [
            'member_id'  => $memberId,
            'check_type' => $checkType,
            'result'     => $result,
            'detail'     => $detail,
            'checked_at' => !empty($data['checked_at']) ? $data['checked_at'] : date('Y-m-d H:i:s'),
        ];

        if ($this->builder->insert($insertData)) {
            return $this->db->insertID();
        }
        return false;
    }

    /**
     * Apply filter conditions for logs
     */
    private function applyLogFilters($query, array $filters = [])
    {
        if (!empty($filters['q'])) {
            $q = strTrim($filters['q']);
            $query->groupStart()
                ->like('members.company_name', $q)
                ->orLike('members.tax_code', $q)
                ->orLike('member_verify_logs.result', $q)
                ->orLike('member_verify_logs.detail', $q)
                ->groupEnd();
        }

        if (!empty($filters['check_type'])) {
            $query->where('member_verify_logs.check_type', strTrim($filters['check_type']));
        }

        if (!empty($filters['result'])) {
            $query->where('member_verify_logs.result', strTrim($filters['result']));
        }

        if (!empty($filters['member_id'])) {
            $query->where('member_verify_logs.member_id', clrNum($filters['member_id']));
        }

        return $query;
    }

    /**
     * Get paginated logs list with member information
     */
    public function getLogsPaginated(int $perPage = 25, int $offset = 0, array $filters = []): array
    {
        $query = $this->db->table('member_verify_logs')
            ->select('member_verify_logs.*, members.company_name, members.tax_code, members.city, members.verify_status')
            ->join('members', 'members.id = member_verify_logs.member_id', 'left');

        $query = $this->applyLogFilters($query, $filters);

        return $query
            ->orderBy('member_verify_logs.id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResult();
    }

    /**
     * Get total count of filtered logs
     */
    public function getLogsCount(array $filters = []): int
    {
        $query = $this->db->table('member_verify_logs')
            ->join('members', 'members.id = member_verify_logs.member_id', 'left');

        $query = $this->applyLogFilters($query, $filters);
        return $query->countAllResults();
    }

    /**
     * Get latest verification log for a member
     */
    public function getLatestLog($memberId, ?string $checkType = null)
    {
        $query = $this->builder->where('member_id', clrNum($memberId));
        if (!empty($checkType)) {
            $query->where('check_type', $checkType);
        }

        return $query
            ->orderBy('checked_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->get()
            ->getRow();
    }

    /**
     * Delete logs for a specific member
     */
    public function deleteLogsByMemberId($memberId): bool
    {
        return $this->builder
            ->where('member_id', clrNum($memberId))
            ->delete();
    }
}
