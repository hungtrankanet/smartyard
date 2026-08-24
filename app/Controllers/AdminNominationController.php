<?php

namespace App\Controllers;

use App\Models\AwardCategoryModel;
use App\Models\AwardSeasonModel;
use App\Models\JuryEvaluationModel;
use App\Models\NominationCandidateModel;
use App\Services\HybridScoringService;
use App\Services\NominationWorkflowService;

/**
 * AdminNominationController: Admin Review & 4-Stage Workflow for Award Nominations
 * Manages dossier screening, jury appraisal stage gates, final voting advancement, and award honors
 *
 * Strict Compliance: <= 500 lines
 */
class AdminNominationController extends BaseAdminController
{
    protected $candidateModel;
    protected $seasonModel;
    protected $categoryModel;
    protected $juryModel;
    protected $workflowService;
    protected $scoringService;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        $this->candidateModel = new NominationCandidateModel();
        $this->seasonModel = new AwardSeasonModel();
        $this->categoryModel = new AwardCategoryModel();
        $this->juryModel = new JuryEvaluationModel();
        $this->workflowService = new NominationWorkflowService();
        $this->scoringService = new HybridScoringService($this->candidateModel, $this->categoryModel, $this->juryModel);
    }

    protected function checkPermission(): void
    {
        if (!authCheck()) {
            redirectToUrl(adminUrl('login'));
            exit();
        }
        if (!isSuperAdmin() && !hasPermission('admin')) {
            redirectToUrl(adminUrl());
            exit();
        }
    }

    /**
     * List all nomination dossiers with stage filtering and search
     */
    public function index()
    {
        $this->checkPermission();
        $data['title'] = 'Hồ Sơ Đề Cử & Xét Duyệt Giải Thưởng (4 Vòng)';

        $activeSeason = $this->seasonModel->getActiveSeason();
        $seasonId = inputGet('season_id') ? clrNum(inputGet('season_id')) : (int)($activeSeason->id ?? 1);

        $filters = [
            'season_id'   => $seasonId,
            'category_id' => inputGet('category_id') ? clrNum(inputGet('category_id')) : null,
            'stage'       => inputGet('stage') ? cleanStr(inputGet('stage')) : null,
            'status'      => inputGet('status') ? cleanStr(inputGet('status')) : null,
            'q'           => inputGet('q') ? cleanStr(inputGet('q')) : null,
        ];

        $page = (int)inputGet('page') ?: 1;
        $perPage = $this->perPage ?: 20;
        $offset = ($page - 1) * $perPage;

        $totalRows = $this->candidateModel->countCandidatesFiltered($filters);
        $candidates = $this->candidateModel->getCandidatesFiltered($filters, $perPage, $offset);

        // Stage counters for stats tabs
        $db = \Config\Database::connect();
        $stageStats = [
            'total'      => $db->table('tb_nomination_candidates')->where('season_id', $seasonId)->countAllResults(),
            'so_khao'    => $db->table('tb_nomination_candidates')->where('season_id', $seasonId)->where('stage', 'so_khao')->countAllResults(),
            'tham_dinh'  => $db->table('tb_nomination_candidates')->where('season_id', $seasonId)->where('stage', 'tham_dinh')->countAllResults(),
            'chung_khao' => $db->table('tb_nomination_candidates')->where('season_id', $seasonId)->where('stage', 'chung_khao')->countAllResults(),
            'trao_giai'  => $db->table('tb_nomination_candidates')->where('season_id', $seasonId)->where('stage', 'trao_giai')->countAllResults(),
            'rejected'   => $db->table('tb_nomination_candidates')->where('season_id', $seasonId)->where('stage', 'rejected')->countAllResults(),
        ];

        $data['candidates'] = $candidates;
        $data['seasons'] = $this->seasonModel->getSeasons(50);
        $data['categories'] = $this->categoryModel->getCategoriesBySeason($seasonId);
        $data['filters'] = $filters;
        $data['stageStats'] = $stageStats;
        $data['totalRows'] = $totalRows;
        $data['workflowService'] = $this->workflowService;

        $pager = \Config\Services::pager();
        $data['pager'] = $pager ? $pager->makeLinks($page, $perPage, $totalRows, 'default_full') : '';

        echo view('admin/includes/_header', $data);
        echo view('admin/awards/nominations/index', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * View detailed nomination dossier, uploaded documents, and scoring breakdown
     */
    public function dossier($id)
    {
        $this->checkPermission();
        $candidate = $this->candidateModel->getCandidate($id);
        if (!$candidate) {
            $this->session->setFlashdata('error', 'Không tìm thấy hồ sơ ứng viên yêu cầu.');
            return redirect()->to(adminUrl('nominations'));
        }

        $data['title'] = 'Hồ Sơ Đề Cử: ' . esc($candidate->name) . ' (' . esc($candidate->candidate_code) . ')';
        $data['candidate'] = $candidate;
        $data['season'] = $this->seasonModel->getSeason($candidate->season_id);
        $data['category'] = $this->categoryModel->getCategory($candidate->category_id);
        $data['evaluations'] = $this->juryModel->getEvaluationsByCandidate((int)$candidate->id);
        $data['juryAverage'] = $this->juryModel->getCandidateJuryAverage((int)$candidate->id);
        $data['compositeScore'] = $this->scoringService->calculateCompositeScore((int)$candidate->id, (int)$candidate->season_id, (int)$candidate->category_id);
        $data['workflowService'] = $this->workflowService;

        // Parse uploaded dossier documents JSON
        $dossierFiles = [];
        if (!empty($candidate->dossier_content)) {
            $decoded = json_decode($candidate->dossier_content, true);
            if (is_array($decoded)) {
                $dossierFiles = $decoded;
            }
        }
        $data['dossierFiles'] = $dossierFiles;

        echo view('admin/includes/_header', $data);
        echo view('admin/awards/nominations/detail', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Handle POST to update review stage (Sơ khảo -> Thẩm định -> Chung khảo -> Trao giải)
     */
    public function updateStagePost()
    {
        $this->checkPermission();
        $candidateId = clrNum(inputPost('candidate_id'));
        $targetStage = cleanStr(inputPost('target_stage'));
        $adminNotes  = inputPost('admin_notes') ? strTrim(inputPost('admin_notes')) : '';

        if (empty($candidateId) || empty($targetStage)) {
            $this->session->setFlashdata('error', 'Dữ liệu chuyển vòng không hợp lệ.');
            return redirect()->to(adminUrl('nominations'));
        }

        $result = $this->workflowService->advanceStage($candidateId, $targetStage, $adminNotes);

        if ($result['success']) {
            $this->session->setFlashdata('success', $result['message']);
        } else {
            $this->session->setFlashdata('error', $result['message']);
        }

        $redirectUrl = inputPost('redirect_to') === 'dossier'
            ? adminUrl('nomination-dossier/' . $candidateId)
            : adminUrl('nominations');

        return redirect()->to($redirectUrl);
    }

    /**
     * Handle POST decision (Approval, Rejection, Set Award Title, Certificate Serial)
     */
    public function decisionPost()
    {
        $this->checkPermission();
        $candidateId = clrNum(inputPost('candidate_id'));
        $candidate = $this->candidateModel->getCandidate($candidateId);

        if (!$candidate) {
            $this->session->setFlashdata('error', 'Hồ sơ ứng viên không tồn tại.');
            return redirect()->to(adminUrl('nominations'));
        }

        $status = cleanStr(inputPost('status') ?: $candidate->status);
        $awardTitle = inputPost('award_title') ? strTrim(inputPost('award_title')) : $candidate->award_title;
        $isFeatured = inputPost('is_featured') !== null ? (inputPost('is_featured') ? 1 : 0) : $candidate->is_featured;
        $certSerial = inputPost('certificate_serial') ? cleanStr(inputPost('certificate_serial')) : $candidate->certificate_serial;

        $updateData = [
            'status'             => $status,
            'award_title'        => $awardTitle,
            'is_featured'        => $isFeatured,
            'certificate_serial' => $certSerial,
        ];

        if ($this->candidateModel->updateCandidate($candidateId, $updateData)) {
            $this->session->setFlashdata('success', 'Đã cập nhật quyết định xét duyệt hồ sơ thành công!');
        } else {
            $this->session->setFlashdata('error', 'Không thể cập nhật hồ sơ ứng viên.');
        }

        return redirect()->to(adminUrl('nomination-dossier/' . $candidateId));
    }

    /**
     * Handle POST to delete a nomination dossier
     */
    public function deletePost()
    {
        $this->checkPermission();
        $id = clrNum(inputPost('id'));
        if (!empty($id)) {
            $this->candidateModel->deleteCandidate($id);
            $this->session->setFlashdata('success', 'Đã xóa hồ sơ đề cử thành công!');
        } else {
            $this->session->setFlashdata('error', 'ID hồ sơ không hợp lệ.');
        }
        return redirect()->to(adminUrl('nominations'));
    }
}
