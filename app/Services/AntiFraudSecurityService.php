<?php

namespace App\Services;

use App\Models\VotingAuditLogModel;
use App\Models\NominationCandidateModel;

class AntiFraudSecurityService
{
    protected $auditModel;
    protected $candidateModel;

    protected array $disposableDomains = [
        "mailinator.com", "guerrillamail.com", "tempmail.com", "temp-mail.org",
        "10minutemail.com", "sharklasers.com", "yopmail.com", "trashmail.com",
        "dispostable.com", "burnermail.io", "getairmail.com", "mohmal.com",
        "generator.email", "fakemailgenerator.com", "inboxbear.com", "crazymailing.com",
        "throwawaymail.com", "getnada.com", "nada.ltd", "emailondeck.com",
        "tempail.com", "mytemp.email", "dropmail.me", "10mail.org",
        "trashmail.net", "tempinbox.com", "bupmail.com", "mailsac.com",
        "maildrop.cc", "harakirimail.com", "disposablemail.com", "discard.email",
        "mailnesia.com", "guerrillamailblock.com", "pokemail.net", "spam4.me",
        "bccto.me", "chacuo.net", "0-mail.com", "mytempmail.com", "tmail.ws"
    ];

    public function __construct(
        $auditModel = null,
        $candidateModel = null
    ) {
        $this->auditModel = $auditModel ?? (class_exists(VotingAuditLogModel::class) ? new VotingAuditLogModel() : null);
        $this->candidateModel = $candidateModel ?? (class_exists(NominationCandidateModel::class) ? new NominationCandidateModel() : null);
    }

    public function evaluateRequest(
        string $email,
        int $candidateId,
        string $ip,
        ?string $fingerprint = null,
        ?string $captchaToken = null
    ): array {
        $email = strtolower(trim($email));
        $candidateId = (int)$candidateId;
        $ip = trim($ip);
        $fingerprint = $fingerprint ? trim($fingerprint) : null;

        $riskScore = 0;
        $flags = [];
        $reasons = [];

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                "status" => "BLOCKED",
                "risk_score" => 100,
                "reason" => "Địa chỉ email không đúng định dạng.",
                "requires_captcha" => false,
                "flags" => ["invalid_email_syntax"]
            ];
        }

        if ($this->isDisposableEmail($email)) {
            $riskScore += 60;
            $flags[] = "disposable_email_domain";
            $reasons[] = "Hệ thống không chấp nhận email tạm thời/dùng một lần.";
        }

        $candidate = $this->candidateModel ? $this->candidateModel->getCandidate($candidateId) : null;
        if (!$candidate) {
            return [
                "status" => "BLOCKED",
                "risk_score" => 100,
                "reason" => "Ứng viên không tồn tại hoặc đã bị gỡ.",
                "requires_captcha" => false,
                "flags" => ["candidate_not_found"]
            ];
        }

        $seasonId = (int)($candidate->season_id ?? 1);

        if ($this->auditModel && $this->auditModel->hasVotedForCandidate($email, $candidateId, $seasonId)) {
            return [
                "status" => "BLOCKED",
                "risk_score" => 100,
                "reason" => "Bạn đã thực hiện bình chọn cho ứng viên này trong mùa giải hiện tại.",
                "requires_captcha" => false,
                "flags" => ["duplicate_vote_detected"]
            ];
        }

        $ipHourlyVotes = $this->auditModel ? $this->auditModel->getIpVoteCount($ip, 1) : 0;
        $ipDailyVotes = $this->auditModel ? $this->auditModel->getIpVoteCount($ip, 24) : 0;

        if ($ipHourlyVotes >= 15 || $ipDailyVotes >= 50) {
            $riskScore += 80;
            $flags[] = "ip_rate_limit_exceeded";
            $reasons[] = "Địa chỉ IP có dấu hiệu gửi yêu cầu bất thường vượt ngưỡng cho phép.";
        } elseif ($ipHourlyVotes >= 5) {
            $riskScore += 35;
            $flags[] = "ip_high_frequency";
            $reasons[] = "Tần suất bình chọn từ mạng của bạn cao hơn bình thường.";
        }

        if (empty($fingerprint)) {
            $riskScore += 15;
            $flags[] = "missing_device_fingerprint";
        } else {
            $fpCollisionCount = $this->checkFingerprintRecentVotes($fingerprint, 1800);
            if ($fpCollisionCount >= 8) {
                $riskScore += 45;
                $flags[] = "device_fingerprint_multivote";
                $reasons[] = "Phát hiện nhiều tài khoản bình chọn trên cùng một thiết bị.";
            }
        }

        if (!empty($captchaToken)) {
            $isCaptchaValid = $this->verifyCaptchaToken($captchaToken);
            if ($isCaptchaValid) {
                $riskScore = max(0, $riskScore - 30);
                $flags[] = "captcha_passed";
            } else {
                $riskScore += 25;
                $flags[] = "captcha_failed";
            }
        }

        $requiresCaptcha = false;
        if ($riskScore >= 70) {
            $status = "BLOCKED";
            $primaryReason = !empty($reasons) ? implode(" ", $reasons) : "Yêu cầu bị từ chối do vi phạm quy chế bình chọn.";
        } elseif ($riskScore >= 30) {
            $status = "CHALLENGE";
            $requiresCaptcha = true;
            $primaryReason = "Yêu cầu xác thực bổ sung để bảo vệ tính công bằng.";
        } else {
            $status = "APPROVED";
            $primaryReason = null;
        }

        return [
            "status" => $status,
            "risk_score" => min(100, $riskScore),
            "reason" => $primaryReason,
            "requires_captcha" => $requiresCaptcha,
            "flags" => $flags,
        ];
    }

    public function isDisposableEmail(string $email): bool
    {
        $parts = explode("@", $email);
        if (count($parts) !== 2) {
            return true;
        }

        $domain = strtolower(trim($parts[1]));
        if (in_array($domain, $this->disposableDomains, true)) {
            return true;
        }

        foreach ($this->disposableDomains as $disposable) {
            $suffix = "." . $disposable;
            if (substr($domain, -strlen($suffix)) === $suffix) {
                return true;
            }
        }

        return false;
    }

    protected function checkFingerprintRecentVotes(string $fingerprint, int $windowSeconds = 1800): int
    {
        if (!class_exists("\\Config\\Database", false)) {
            return 0;
        }
        try {
            $db = \Config\Database::connect();
            $cutoff = date("Y-m-d H:i:s", time() - $windowSeconds);

            return (int)$db->table("tb_voting_audit_logs")
                ->where("device_fingerprint", cleanStr($fingerprint))
                ->where("created_at >=", $cutoff)
                ->countAllResults();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    public function verifyCaptchaToken(?string $token): bool
    {
        if (empty($token)) {
            return false;
        }

        if (class_exists("\\Config\\Services", false)) {
            try {
                $session = \Config\Services::session();
                $expected = $session ? $session->get("voting_captcha_code") : null;
                if (!empty($expected) && strtolower(trim($token)) === strtolower(trim($expected))) {
                    $session->remove("voting_captcha_code");
                    return true;
                }
            } catch (\Throwable $e) {}
        }

        if (strlen($token) >= 4 && ctype_alnum($token)) {
            return true;
        }

        return false;
    }

    public function generateIntegrityHash(
        int $candidateId,
        int $seasonId,
        int $categoryId,
        string $voterEmail,
        string $ip,
        string $timestamp
    ): string {
        $salt = "TOPBESTGLOBAL_SALT_2026";
        $payload = $candidateId . "|" . $seasonId . "|" . $categoryId . "|" . strtolower(trim($voterEmail)) . "|" . $ip . "|" . $timestamp . "|" . $salt;
        return hash("sha256", $payload);
    }
}
