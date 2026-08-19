<?php

namespace App\Controllers;

use App\Models\NominationCandidateModel;
use App\Services\AntiFraudSecurityService;
use App\Services\OtpMailerService;
use App\Services\VotingEngineService;
use CodeIgniter\HTTP\ResponseInterface;

class VotingApiController extends BaseController
{
    protected $votingService;
    protected $otpService;
    protected $antiFraudService;
    protected $candidateModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        $this->candidateModel = new NominationCandidateModel();
        $this->otpService = new OtpMailerService();
        $this->antiFraudService = new AntiFraudSecurityService();
        $this->votingService = new VotingEngineService($this->candidateModel);
    }

    public function sendOtpAjax()
    {
        $email = strtolower(trim((string)inputPost("email")));
        $candidateId = (int)inputPost("candidate_id");
        $fingerprint = cleanStr((string)inputPost("fingerprint"));
        $captchaToken = cleanStr((string)inputPost("captcha_token"));
        $ip = $this->request->getIPAddress();

        $fraudEval = $this->antiFraudService->evaluateRequest(
            $email,
            $candidateId,
            $ip,
            $fingerprint,
            $captchaToken
        );

        if ($fraudEval["status"] === "BLOCKED") {
            return $this->response->setJSON([
                "status"     => "error",
                "message"    => $fraudEval["reason"] ?? "Bình chọn bị từ chối do vi phạm quy tắc bảo mật.",
                "risk_score" => $fraudEval["risk_score"],
                "flags"      => $fraudEval["flags"],
            ]);
        }

        if ($fraudEval["status"] === "CHALLENGE" && empty($captchaToken)) {
            return $this->response->setJSON([
                "status"           => "challenge",
                "message"          => "Hệ thống cần xác minh mã kiểm tra bảo vệ.",
                "requires_captcha" => true,
                "risk_score"       => $fraudEval["risk_score"],
            ]);
        }

        $result = $this->otpService->generateAndSendOtp($email, $candidateId, $ip, $fingerprint);

        return $this->response->setJSON($result);
    }

    public function verifyOtpAjax()
    {
        $token = cleanStr((string)inputPost("token"));
        $otpCode = cleanStr((string)inputPost("otp_code"));
        $candidateId = (int)inputPost("candidate_id");

        if (empty($token) || empty($otpCode) || $candidateId <= 0) {
            return $this->response->setJSON([
                "status"  => "error",
                "message" => "Dữ liệu xác thực không đầy đủ.",
            ]);
        }

        $verified = $this->otpService->verifyOtp($token, $otpCode, $candidateId);
        if (!$verified) {
            return $this->response->setJSON([
                "status"  => "error",
                "message" => "Mã OTP không chính xác hoặc đã hết hạn.",
            ]);
        }

        return $this->response->setJSON([
            "status"      => "success",
            "is_verified" => true,
            "message"     => "Xác thực OTP thành công!",
            "token"       => $token,
        ]);
    }

    public function submitVoteAjax()
    {
        $token = cleanStr((string)inputPost("token"));
        $otpCode = cleanStr((string)inputPost("otp_code"));
        $candidateId = (int)inputPost("candidate_id");
        $email = strtolower(trim((string)inputPost("email")));
        $fingerprint = cleanStr((string)inputPost("fingerprint"));
        $captchaToken = cleanStr((string)inputPost("captcha_token"));
        $ip = $this->request->getIPAddress();

        if (empty($token) || empty($otpCode) || empty($email) || $candidateId <= 0) {
            return $this->response->setJSON([
                "status"  => "error",
                "message" => "Thiếu thông tin gửi bình chọn.",
            ]);
        }

        $result = $this->votingService->executeVote(
            $token,
            $otpCode,
            $candidateId,
            $email,
            $ip,
            $fingerprint,
            $captchaToken
        );

        return $this->response->setJSON($result);
    }

    public function getLivePollData(int $candidateId)
    {
        $stats = $this->votingService->getCandidateVotingStats($candidateId);

        if (isset($stats["error"])) {
            return $this->response->setStatusCode(404)->setJSON([
                "status"  => "error",
                "message" => "Không tìm thấy ứng viên.",
            ]);
        }

        return $this->response->setJSON([
            "status" => "success",
            "data"   => $stats,
        ]);
    }

    public function getCategoryStats(int $categoryId)
    {
        $seasonId = (int)inputGet("season_id") ?: 1;
        $leaderboard = $this->votingService->getCategoryLeaderboard($categoryId, $seasonId);

        return $this->response->setJSON([
            "status" => "success",
            "data"   => $leaderboard,
        ]);
    }
}
