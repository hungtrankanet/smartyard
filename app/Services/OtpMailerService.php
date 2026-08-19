<?php

namespace App\Services;

use App\Models\EmailModel;
use App\Models\NominationCandidateModel;
use App\Models\VotingOtpModel;

class OtpMailerService
{
    protected $otpModel;
    protected $emailModel;
    protected $candidateModel;
    protected $otpTtlSeconds = 300;
    protected $cooldownSeconds = 60;

    public function __construct(
        $otpModel = null,
        $emailModel = null,
        $candidateModel = null
    ) {
        $this->otpModel = $otpModel ?? (class_exists(VotingOtpModel::class) ? new VotingOtpModel() : null);
        $this->emailModel = $emailModel ?? (class_exists(EmailModel::class) ? new EmailModel() : null);
        $this->candidateModel = $candidateModel ?? (class_exists(NominationCandidateModel::class) ? new NominationCandidateModel() : null);
    }

    public function generateAndSendOtp(
        string $email,
        int $candidateId,
        string $ip,
        ?string $fingerprint = null
    ): array {
        $email = strtolower(trim($email));
        $candidateId = (int)$candidateId;

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                "status" => "error",
                "message" => "Địa chỉ email không hợp lệ.",
                "reason" => "invalid_email",
            ];
        }

        $candidate = $this->candidateModel ? $this->candidateModel->getCandidate($candidateId) : null;
        if (!$candidate) {
            return [
                "status" => "error",
                "message" => "Không tìm thấy thông tin ứng viên bình chọn.",
                "reason" => "candidate_not_found",
            ];
        }

        if ($this->isCooldownActive($email, $candidateId, $this->cooldownSeconds)) {
            return [
                "status" => "error",
                "message" => "Vui lòng đợi " . $this->cooldownSeconds . " giây trước khi yêu cầu mã OTP mới.",
                "reason" => "cooldown_active",
                "cooldown_seconds" => $this->cooldownSeconds,
            ];
        }

        $ipOtpCount = $this->otpModel ? $this->otpModel->getRecentOtpCountByIp($ip, 3600) : 0;
        if ($ipOtpCount >= 15) {
            return [
                "status" => "error",
                "message" => "Địa chỉ IP của bạn đã vượt quá giới hạn yêu cầu OTP trong giờ qua.",
                "reason" => "ip_rate_limited",
            ];
        }

        $otpRecord = $this->otpModel ? $this->otpModel->createOtp(
            $email,
            $candidateId,
            $ip,
            $fingerprint,
            $this->otpTtlSeconds
        ) : null;

        if (!$otpRecord) {
            return [
                "status" => "error",
                "message" => "Không thể khởi tạo mã OTP. Vui lòng thử lại.",
                "reason" => "database_insert_failed",
            ];
        }

        $candidateName = $candidate->name ?? "Ứng viên tiêu biểu";
        $emailSent = $this->dispatchOtpEmail($email, $otpRecord["otp_code"], $candidateName);

        return [
            "status" => "success",
            "token" => $otpRecord["token"],
            "expires_at" => $otpRecord["expires_at"],
            "cooldown_seconds" => $this->cooldownSeconds,
            "message" => "Mã OTP đã được gửi đến email của bạn. Vui lòng kiểm tra hộp thư (kể cả thư rác / Spam).",
            "email_sent" => $emailSent,
            "debug_otp" => (defined("ENVIRONMENT") && ENVIRONMENT !== "production") ? $otpRecord["otp_code"] : null,
        ];
    }

    public function verifyOtp(string $token, string $otpCode, int $candidateId): ?object
    {
        $token = trim($token);
        $otpCode = trim($otpCode);
        $candidateId = (int)$candidateId;

        if (empty($token) || empty($otpCode) || $candidateId <= 0 || !$this->otpModel) {
            return null;
        }

        return $this->otpModel->verifyOtp($token, $otpCode, $candidateId);
    }

    public function isCooldownActive(string $email, int $candidateId, int $cooldownSeconds = 60): bool
    {
        if (!$this->otpModel) {
            return false;
        }
        $recent = $this->otpModel->getActiveCooldownOtp($email, $candidateId, $cooldownSeconds);
        return !empty($recent);
    }

    protected function dispatchOtpEmail(string $email, string $otpCode, string $candidateName): bool
    {
        try {
            $data = [
                "subject" => "[TOP BEST GLOBAL] Mã OTP xác thực bình chọn: " . $otpCode,
                "to" => $email,
                "template_path" => "email/email_voting_otp",
                "otp_code" => $otpCode,
                "candidate_name" => $candidateName,
            ];

            if ($this->emailModel && method_exists($this->emailModel, "sendEmail")) {
                return (bool)$this->emailModel->sendEmail($data);
            }
            return true;
        } catch (\Throwable $e) {
            if (function_exists("log_message")) {
                log_message("error", "OtpMailerService::dispatchOtpEmail failed: " . $e->getMessage());
            }
            return false;
        }
    }
}
