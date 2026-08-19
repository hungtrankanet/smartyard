<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\AwardCategoryModel;
use App\Models\AwardSeasonModel;
use App\Models\NominationCandidateModel;
use App\Models\VotingAuditLogModel;
use App\Services\HybridScoringService;

class AdminVotingAuditController extends BaseAdminController
{
    protected $auditModel;
    protected $seasonModel;
    protected $categoryModel;
    protected $candidateModel;
    protected $scoringService;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        $this->auditModel = new VotingAuditLogModel();
        $this->seasonModel = new AwardSeasonModel();
        $this->categoryModel = new AwardCategoryModel();
        $this->candidateModel = new NominationCandidateModel();
        $this->scoringService = new HybridScoringService($this->candidateModel, $this->categoryModel);
    }

    public function logs()
    {
        $data["title"] = "Nhật Ký Kiểm Toán Bình Chọn Trực Tuyến & Chống Gian Lận";
        $activeSeason = $this->seasonModel->getActiveSeason();
        $seasonId = (int)($activeSeason->id ?? 1);

        $filters = [
            "candidate_id" => inputGet("candidate_id") ? (int)inputGet("candidate_id") : null,
            "category_id"  => inputGet("category_id") ? (int)inputGet("category_id") : null,
            "season_id"    => inputGet("season_id") ? (int)inputGet("season_id") : $seasonId,
            "voter_email"  => inputGet("voter_email") ? trim(inputGet("voter_email")) : null,
            "ip_address"   => inputGet("ip_address") ? trim(inputGet("ip_address")) : null,
        ];

        $page = (int)inputGet("page") ?: 1;
        $perPage = $this->perPage ?: 20;
        $offset = ($page - 1) * $perPage;

        $totalRows = $this->auditModel->countAuditLogs($filters);
        $data["logs"] = $this->auditModel->getAuditLogs($filters, $perPage, $offset);
        $data["seasons"] = $this->seasonModel->getSeasons(20);
        $data["categories"] = $this->categoryModel->getCategoriesBySeason($seasonId);
        $data["filters"] = $filters;
        $data["totalRows"] = $totalRows;

        $pager = \Config\Services::pager();
        $data["pager"] = $pager ? $pager->makeLinks($page, $perPage, $totalRows, "default_full") : "";

        echo view("admin/includes/_header", $data);
        echo view("admin/voting/logs", $data);
        echo view("admin/includes/_footer");
    }

    public function resultsSummary()
    {
        $data["title"] = "Tổng Hợp Kết Quả Điểm Số & Xếp Hạng Giải Thưởng 2026";
        $activeSeason = $this->seasonModel->getActiveSeason();
        $seasonId = (int)($activeSeason->id ?? 1);

        $categories = $this->categoryModel->getCategoriesBySeason($seasonId);
        $summary = [];

        foreach ($categories as $category) {
            $catId = (int)$category->id;
            $candidates = $this->candidateModel->getCandidatesForVoting($catId, $seasonId);
            $scoredList = [];

            foreach ($candidates as $c) {
                $score = $this->scoringService->calculateCompositeScore((int)$c->id, $seasonId, $catId);
                $scoredList[] = [
                    "candidate"       => $c,
                    "score_breakdown" => $score,
                ];
            }

            $summary[] = [
                "category"   => $category,
                "candidates" => $scoredList,
                "total"      => count($scoredList),
            ];
        }

        $data["activeSeason"] = $activeSeason;
        $data["categories"] = $categories;
        $data["summary"] = $summary;

        echo view("admin/includes/_header", $data);
        echo view("admin/voting/results_summary", $data);
        echo view("admin/includes/_footer");
    }

    public function recalculateRanksPost()
    {
        $seasonId = (int)inputPost("season_id") ?: 1;
        $this->scoringService->recalculateAllCategories($seasonId);

        $this->session->setFlashdata("success", "Đã tính toán lại toàn bộ điểm tổng hợp 70/30 và cập nhật thứ hạng bảng vàng thành công!");
        return redirect()->to(adminUrl("voting-results-summary"));
    }

    public function exportAuditCsv()
    {
        $activeSeason = $this->seasonModel->getActiveSeason();
        $seasonId = (int)($activeSeason->id ?? 1);

        $filters = [
            "season_id" => (int)inputPost("season_id") ?: $seasonId,
        ];

        $logs = $this->auditModel->getAuditLogs($filters, 5000, 0);
        $filename = "TBG_Voting_Audit_" . date("Ymd_His") . ".csv";

        header("Content-Type: text/csv; charset=utf-8");
        header("Content-Disposition: attachment; filename=" . $filename);

        $output = fopen("php://output", "w");
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($output, [
            "ID", "Thời Gian", "Mã Ứng Viên", "Tên Ứng Viên", "Hạng Mục",
            "Email Bình Chọn", "Địa Chỉ IP", "Điểm Rủi Ro (Risk)",
            "Trạng Thái Xác Thực", "Chuỗi Băm SHA256 (Integrity Hash)"
        ]);

        foreach ($logs as $log) {
            fputcsv($output, [
                $log->id,
                $log->created_at,
                $log->candidate_code ?? ("ID-" . $log->candidate_id),
                $log->candidate_name ?? "N/A",
                $log->category_name ?? "N/A",
                $log->voter_email,
                $log->ip_address,
                $log->risk_score,
                $log->verification_status,
                $log->integrity_hash,
            ]);
        }

        fclose($output);
        exit();
    }
}
