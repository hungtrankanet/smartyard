<?php

namespace App\Models;

/**
 * VotingAuditLogModel: Manages immutable voting audit logs with SHA256 integrity verification
 */
class VotingAuditLogModel extends BaseModel
{
    protected $table = 'tb_voting_audit_logs';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'candidate_id', 'season_id', 'category_id', 'voter_email', 'otp_id',
        'ip_address', 'device_fingerprint', 'user_agent', 'risk_score',
        'verification_status', 'integrity_hash', 'created_at',
    ];
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table($this->table);
    }

    public function logVote(
        int $candidateId,
        int $seasonId,
        int $categoryId,
        string $voterEmail,
        string $ip,
        ?string $fingerprint = null,
        ?int $otpId = null,
        int $riskScore = 0,
        string $status = 'verified'
    ): ?int {
        $now = date('Y-m-d H:i:s');
        $voterEmail = strtolower(trim($voterEmail));
        $userAgent = $this->request->getUserAgent() ? substr($this->request->getUserAgent()->getAgentString(), 0, 500) : 'CLI/API';

        $salt = 'TOPBESTGLOBAL_SALT_2026';
        $payload = "{$candidateId}|{$seasonId}|{$categoryId}|{$voterEmail}|{$ip}|{$now}|{$salt}";
        $integrityHash = hash('sha256', $payload);

        $insertData = [
            'candidate_id'        => clrNum($candidateId),
            'season_id'           => clrNum($seasonId),
            'category_id'         => clrNum($categoryId),
            'voter_email'         => $voterEmail,
            'otp_id'              => $otpId ? clrNum($otpId) : null,
            'ip_address'          => cleanStr($ip),
            'device_fingerprint'  => $fingerprint ? cleanStr($fingerprint) : null,
            'user_agent'          => $userAgent,
            'risk_score'          => clrNum($riskScore),
            'verification_status' => cleanStr($status),
            'integrity_hash'      => $integrityHash,
            'created_at'          => $now,
        ];

        if ($this->builder->insert($insertData)) {
            return $this->db->insertID();
        }
        return null;
    }

    public function hasVotedForCandidate(string $email, int $candidateId, ?int $seasonId = null): bool
    {
        $builder = $this->builder
            ->where('voter_email', strtolower(trim($email)))
            ->where('candidate_id', clrNum($candidateId))
            ->where('verification_status', 'verified');

        if ($seasonId !== null) {
            $builder->where('season_id', clrNum($seasonId));
        }

        return $builder->countAllResults() > 0;
    }

    public function getIpVoteCount(string $ip, int $hours = 24): int
    {
        $cutoff = date('Y-m-d H:i:s', time() - ($hours * 3600));
        return $this->builder
            ->where('ip_address', cleanStr($ip))
            ->where('created_at >=', $cutoff)
            ->countAllResults();
    }

    public function getAuditLogs(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        $builder = $this->db->table('tb_voting_audit_logs l')
            ->select('l.*, c.name AS candidate_name, c.candidate_code, cat.name AS category_name, s.title AS season_title')
            ->join('tb_nomination_candidates c', 'c.id = l.candidate_id', 'left')
            ->join('tb_award_categories cat', 'cat.id = l.category_id', 'left')
            ->join('tb_award_seasons s', 's.id = l.season_id', 'left');

        if (!empty($filters['candidate_id'])) {
            $builder->where('l.candidate_id', clrNum($filters['candidate_id']));
        }
        if (!empty($filters['season_id'])) {
            $builder->where('l.season_id', clrNum($filters['season_id']));
        }
        if (!empty($filters['category_id'])) {
            $builder->where('l.category_id', clrNum($filters['category_id']));
        }
        if (!empty($filters['voter_email'])) {
            $builder->like('l.voter_email', trim($filters['voter_email']));
        }
        if (!empty($filters['ip_address'])) {
            $builder->where('l.ip_address', cleanStr($filters['ip_address']));
        }

        return $builder
            ->orderBy('l.id', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResult();
    }

    public function countAuditLogs(array $filters = []): int
    {
        $builder = $this->builder;
        if (!empty($filters['candidate_id'])) {
            $builder->where('candidate_id', clrNum($filters['candidate_id']));
        }
        if (!empty($filters['season_id'])) {
            $builder->where('season_id', clrNum($filters['season_id']));
        }
        return $builder->countAllResults();
    }
}
