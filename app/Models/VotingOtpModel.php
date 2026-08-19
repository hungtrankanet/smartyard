<?php

namespace App\Models;

/**
 * VotingOtpModel: Manages 6-digit OTP verification, rate limiting, and session tokens
 */
class VotingOtpModel extends BaseModel
{
    protected $table = 'tb_voting_otps';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'email', 'otp_code', 'candidate_id', 'token', 'ip_address', 'user_agent',
        'device_fingerprint', 'is_verified', 'verified_at', 'expires_at', 'created_at',
    ];
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table($this->table);
    }

    public function createOtp(string $email, int $candidateId, string $ip, ?string $fingerprint = null, int $ttlSeconds = 300): ?array
    {
        $email = strtolower(trim($email));
        $candidateId = clrNum($candidateId);
        $otpCode = (string)random_int(100000, 999999);
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + $ttlSeconds);
        $userAgent = $this->request->getUserAgent() ? substr($this->request->getUserAgent()->getAgentString(), 0, 500) : null;

        $insertData = [
            'email'              => $email,
            'otp_code'           => $otpCode,
            'candidate_id'       => $candidateId,
            'token'              => $token,
            'ip_address'         => cleanStr($ip),
            'user_agent'         => $userAgent,
            'device_fingerprint' => $fingerprint ? cleanStr($fingerprint) : null,
            'is_verified'        => 0,
            'verified_at'        => null,
            'expires_at'         => $expiresAt,
            'created_at'         => date('Y-m-d H:i:s'),
        ];

        if ($this->builder->insert($insertData)) {
            return [
                'id'         => $this->db->insertID(),
                'token'      => $token,
                'otp_code'   => $otpCode,
                'expires_at' => $expiresAt,
                'email'      => $email,
            ];
        }
        return null;
    }

    public function verifyOtp(string $token, string $otpCode, int $candidateId): ?object
    {
        $token = cleanStr($token);
        $otpCode = cleanStr($otpCode);
        $candidateId = clrNum($candidateId);
        $now = date('Y-m-d H:i:s');

        $record = $this->builder
            ->where('token', $token)
            ->where('candidate_id', $candidateId)
            ->where('is_verified', 0)
            ->where('expires_at >=', $now)
            ->get()
            ->getRow();

        if (!$record) {
            return null;
        }

        if (trim($record->otp_code) !== trim($otpCode)) {
            return null;
        }

        $this->builder
            ->where('id', $record->id)
            ->update([
                'is_verified' => 1,
                'verified_at' => $now,
            ]);

        $record->is_verified = 1;
        $record->verified_at = $now;
        return $record;
    }

    public function getActiveCooldownOtp(string $email, int $candidateId, int $cooldownSeconds = 60)
    {
        $cutoff = date('Y-m-d H:i:s', time() - $cooldownSeconds);
        return $this->builder
            ->where('email', strtolower(trim($email)))
            ->where('candidate_id', clrNum($candidateId))
            ->where('created_at >=', $cutoff)
            ->orderBy('id', 'DESC')
            ->get()
            ->getRow();
    }

    public function getRecentOtpCountByIp(string $ip, int $windowSeconds = 3600): int
    {
        $cutoff = date('Y-m-d H:i:s', time() - $windowSeconds);
        return $this->builder
            ->where('ip_address', cleanStr($ip))
            ->where('created_at >=', $cutoff)
            ->countAllResults();
    }
}
