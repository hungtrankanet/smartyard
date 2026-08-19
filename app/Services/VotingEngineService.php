<?php

namespace App\Services;

use App\Models\AwardCategoryModel;
use App\Models\AwardSeasonModel;
use App\Models\NominationCandidateModel;
use App\Models\VotingAuditLogModel;
use App\Models\VotingOtpModel;

class VotingEngineService
{
    protected $candidateModel;
    protected $categoryModel;
    protected $seasonModel;
    protected $auditModel;
    protected $otpModel;
    protected $otpService;
    protected $antiFraudService;
    protected $scoringService;

    public function __construct(
        $candidateModel = null,
        $categoryModel = null,
        $seasonModel = null,
        $auditModel = null,
        $otpModel = null,
        $otpService = null,
        $antiFraudService = null,
        $scoringService = null
    ) {
        $this->candidateModel = $candidateModel ?? (class_exists(NominationCandidateModel::class) ? new NominationCandidateModel() : null);
        $this->categoryModel = $categoryModel ?? (class_exists(AwardCategoryModel::class) ? new AwardCategoryModel() : null);
        $this->seasonModel = $seasonModel ?? (class_exists(AwardSeasonModel::class) ? new AwardSeasonModel() : null);
        $this->auditModel = $auditModel ?? (class_exists(VotingAuditLogModel::class) ? new VotingAuditLogModel() : null);
        $this->otpModel = $otpModel ?? (class_exists(VotingOtpModel::class) ? new VotingOtpModel() : null);
        $this->otpService = $otpService ?? new OtpMailerService($this->otpModel);
        $this->antiFraudService = $antiFraudService ?? new AntiFraudSecurityService($this->auditModel, $this->candidateModel);
        $this->scoringService = $scoringService ?? new HybridScoringService($this->candidateModel, $this->categoryModel);
    }

    public function executeVote(
        string $token,
        string $otpCode,
        int $candidateId,
        string $voterEmail,
        string $ip,
        ?string $fingerprint = null,
        ?string $captchaToken = null
    ): array {
        $voterEmail = strtolower(trim($voterEmail));
        $candidateId = (int)$candidateId;
        $token = trim($token);
        $otpCode = trim($otpCode);

        $candidate = $this->candidateModel ? $this->candidateModel->getCandidate($candidateId) : null;
        if (!$candidate || $candidate->status !== "approved") {
            return [
                "status"  => "error",
                "message" => "Ứng viên không hợp lệ hoặc hồ sơ chưa được duyệt.",
                "reason"  => "invalid_candidate",
            ];
        }

        $seasonId = (int)($candidate->season_id ?? 1);
        $categoryId = (int)($candidate->category_id ?? 1);

        $fraudEval = $this->antiFraudService->evaluateRequest(
            $voterEmail,
            $candidateId,
            $ip,
            $fingerprint,
            $captchaToken
        );

        if ($fraudEval["status"] === "BLOCKED") {
            return [
                "status"     => "error",
                "message"    => $fraudEval["reason"] ?? "Yêu cầu bình chọn bị từ chối do vi phạm quy chế bảo mật.",
                "reason"     => "anti_fraud_blocked",
                "risk_score" => $fraudEval["risk_score"],
            ];
        }

        if ($fraudEval["status"] === "CHALLENGE" && empty($captchaToken)) {
            return [
                "status"           => "challenge",
                "message"          => "Vui lòng hoàn thành mã kiểm tra Captcha để tiếp tục.",
                "requires_captcha" => true,
                "risk_score"       => $fraudEval["risk_score"],
            ];
        }

        $verifiedOtp = $this->otpService->verifyOtp($token, $otpCode, $candidateId);
        if (!$verifiedOtp) {
            return [
                "status"  => "error",
                "message" => "Mã OTP không chính xác hoặc đã hết thời gian hiệu lực.",
                "reason"  => "otp_verification_failed",
            ];
        }

        if (strtolower(trim($verifiedOtp->email)) !== $voterEmail) {
            return [
                "status"  => "error",
                "message" => "Email xác thực không khớp với mã OTP đã cấp.",
                "reason"  => "email_mismatch",
            ];
        }

        $incrementSuccess = $this->candidateModel->incrementVotesAtomic($candidateId);
        if (!$incrementSuccess) {
            return [
                "status"  => "error",
                "message" => "Hệ thống đang quá tải, không thể ghi nhận lượt bình chọn. Vui lòng thử lại.",
                "reason"  => "atomic_increment_failed",
            ];
        }

        $auditId = $this->auditModel ? $this->auditModel->logVote(
            $candidateId,
            $seasonId,
            $categoryId,
            $voterEmail,
            $ip,
            $fingerprint,
            (int)$verifiedOtp->id,
            (int)$fraudEval["risk_score"],
            "verified"
        ) : 1;

        $this->scoringService->recalculateCategoryScores($categoryId, $seasonId);
        $updatedScore = $this->scoringService->calculateCompositeScore($candidateId, $seasonId, $categoryId);
        $updatedCandidate = $this->candidateModel->getCandidate($candidateId);

        return [
            "status"          => "success",
            "message"         => "Bình chọn thành công cho " . esc($candidate->name) . "!",
            "candidate_id"    => $candidateId,
            "candidate_name"  => $candidate->name,
            "public_votes"    => (int)($updatedCandidate->public_votes_count ?? 0),
            "composite_score" => $updatedScore["final_composite_score"],
            "category_rank"   => $updatedScore["category_rank"],
            "audit_log_id"    => $auditId,
            "risk_score"      => $fraudEval["risk_score"],
        ];
    }

    public function getCandidateVotingStats(int $candidateId): array
    {
        $candidate = $this->candidateModel ? $this->candidateModel->getCandidate($candidateId) : null;
        if (!$candidate) {
            return ["error" => "not_found"];
        }

        $seasonId = (int)($candidate->season_id ?? 1);
        $categoryId = (int)($candidate->category_id ?? 1);
        $scoreData = $this->scoringService->calculateCompositeScore($candidateId, $seasonId, $categoryId);

        return [
            "candidate_id"          => $candidateId,
            "name"                  => $candidate->name,
            "public_votes"          => (int)($candidate->public_votes_count ?? 0),
            "jury_score_avg"        => (float)($candidate->jury_score_avg ?? 0.00),
            "composite_score"       => (float)$scoreData["final_composite_score"],
            "category_rank"         => (int)$scoreData["category_rank"],
            "jury_score_weighted"   => (float)$scoreData["jury_score_weighted"],
            "public_score_weighted" => (float)$scoreData["public_score_weighted"],
            "category_name"         => $candidate->category_name ?? "",
        ];
    }

    public function getCategoryLeaderboard(int $categoryId, int $seasonId = 1): array
    {
        $candidates = $this->candidateModel ? $this->candidateModel->getCandidatesForVoting($categoryId, $seasonId) : [];
        $category = $this->categoryModel ? $this->categoryModel->getCategory($categoryId) : null;

        $list = [];
        foreach ($candidates as $c) {
            $scoreData = $this->scoringService->calculateCompositeScore((int)$c->id, $seasonId, $categoryId);
            $list[] = [
                "id"                 => (int)$c->id,
                "name"               => $c->name,
                "slug"               => $c->slug,
                "candidate_code"     => $c->candidate_code,
                "organization_name"  => $c->organization_name,
                "avatar"             => $c->avatar,
                "public_votes_count" => (int)($c->public_votes_count ?? 0),
                "jury_score_avg"     => (float)($c->jury_score_avg ?? 0.00),
                "composite_score"    => (float)$scoreData["final_composite_score"],
                "final_rank"         => (int)$scoreData["category_rank"],
                "jury_weighted"      => (float)$scoreData["jury_score_weighted"],
                "public_weighted"    => (float)$scoreData["public_score_weighted"],
            ];
        }

        return [
            "category"   => $category,
            "candidates" => $list,
            "total"      => count($list),
        ];
    }

    public function getGlobalLeaderboard(int $seasonId = 1, int $limit = 10): array
    {
        if (!class_exists("\\Config\\Database", false)) {
            return [];
        }
        try {
            $db = \Config\Database::connect();
            return $db->table("tb_nomination_candidates c")
                ->select("c.*, cat.name AS category_name, cat.slug AS category_slug, cat.industry_sector")
                ->join("tb_award_categories cat", "cat.id = c.category_id", "left")
                ->where("c.season_id", $seasonId)
                ->where("c.status", "approved")
                ->orderBy("c.composite_score", "DESC")
                ->orderBy("c.public_votes_count", "DESC")
                ->limit($limit)
                ->get()
                ->getResult();
        } catch (\Throwable $e) {
            return [];
        }
    }
}
