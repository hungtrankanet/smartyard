<?php

namespace App\Controllers;

use App\Models\AwardCategoryModel;
use App\Models\AwardSeasonModel;
use App\Models\JuryEvaluationModel;
use App\Models\NominationCandidateModel;
use App\Services\HybridScoringService;

/**
 * AdminJuryController: Admin Management for Jury Judges & 70% Weighted Rubric Evaluations
 * Handles expert judge assignments, criteria score submissions, and composite score aggregations
 *
 * Strict Compliance: <= 500 lines
 */
class AdminJuryController extends BaseAdminController
{
    protected $juryModel;
    protected $candidateModel;
    protected $categoryModel;
    protected $seasonModel;
    protected $scoringService;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        $this->juryModel = new JuryEvaluationModel();
        $this->candidateModel = new NominationCandidateModel();
        $this->categoryModel = new AwardCategoryModel();
        $this->seasonModel = new AwardSeasonModel();
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
     * List all jury evaluations and 70% weighted breakdown
     */
    public function evaluations()
    {
        $this->checkPermission();
        $data['title'] = 'Hội Đồng Giám Khảo & Chấm Điểm Thẩm Định (70%)';

        $activeSeason = $this->seasonModel->getActiveSeason();
        $seasonId = inputGet('season_id') ? clrNum(inputGet('season_id')) : (int)($activeSeason->id ?? 1);

        $filters = [
            'season_id'    => $seasonId,
            'category_id'  => inputGet('category_id') ? clrNum(inputGet('category_id')) : null,
            'jury_user_id' => inputGet('jury_user_id') ? clrNum(inputGet('jury_user_id')) : null,
            'candidate_id' => inputGet('candidate_id') ? clrNum(inputGet('candidate_id')) : null,
        ];

        $page = (int)inputGet('page') ?: 1;
        $perPage = $this->perPage ?: 20;
        $offset = ($page - 1) * $perPage;

        $totalRows = $this->juryModel->countEvaluationsFiltered($filters);
        $evaluations = $this->juryModel->getEvaluationsFiltered($filters, $perPage, $offset);

        // Compute 70% weighted score for each evaluation entry
        foreach ($evaluations as &$eval) {
            $cat = $this->categoryModel->getCategory($eval->category_id);
            $juryWeight = isset($cat->jury_weight) ? (float)$cat->jury_weight : 70.00;
            $eval->weighted_70 = round((float)$eval->total_score * ($juryWeight / 100), 2);
        }

        // Summary Statistics
        $db = \Config\Database::connect();
        $stats = [
            'total_evaluations' => $db->table('tb_jury_evaluations')->where('season_id', $seasonId)->where('is_submitted', 1)->countAllResults(),
            'total_candidates'  => $db->table('tb_nomination_candidates')->where('season_id', $seasonId)->countAllResults(),
            'avg_score'         => $db->table('tb_jury_evaluations')->where('season_id', $seasonId)->where('is_submitted', 1)->selectAvg('total_score', 'avg')->get()->getRow()->avg ?? 0,
            'total_judges'      => $db->table('tb_jury_evaluations')->where('season_id', $seasonId)->select('COUNT(DISTINCT jury_user_id) AS cnt')->get()->getRow()->cnt ?? 0,
        ];

        $data['evaluations'] = $evaluations;
        $data['seasons'] = $this->seasonModel->getSeasons(50);
        $data['categories'] = $this->categoryModel->getCategoriesBySeason($seasonId);
        $data['judges'] = $this->getJuryUsersList();
        $data['filters'] = $filters;
        $data['stats'] = $stats;
        $data['totalRows'] = $totalRows;

        $pager = \Config\Services::pager();
        $data['pager'] = $pager ? $pager->makeLinks($page, $perPage, $totalRows, 'default_full') : '';

        echo view('admin/includes/_header', $data);
        echo view('admin/awards/jury/index', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Alias for evaluations list (matching admin/jury route)
     */
    public function index()
    {
        return $this->evaluations();
    }

    /**
     * Show jury scoring form for a specific candidate
     */
    public function scoring($candidateId)
    {
        $this->checkPermission();
        $candidate = $this->candidateModel->getCandidate($candidateId);
        if (!$candidate) {
            $this->session->setFlashdata('error', 'Ứng viên không tồn tại.');
            return redirect()->to(adminUrl('jury-evaluations'));
        }

        $juryUserId = user() ? (int)user()->id : 1;
        $evaluation = $this->juryModel->getEvaluationByJuryAndCandidate($juryUserId, (int)$candidate->id);

        $data['title'] = 'Chấm Điểm Thẩm Định: ' . esc($candidate->name);
        $data['candidate'] = $candidate;
        $data['category'] = $this->categoryModel->getCategory($candidate->category_id);
        $data['season'] = $this->seasonModel->getSeason($candidate->season_id);
        $data['evaluation'] = $evaluation;
        $data['juryUserId'] = $juryUserId;
        $data['judges'] = $this->getJuryUsersList();

        echo view('admin/includes/_header', $data);
        echo view('admin/awards/jury/form', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Handle POST submission of jury evaluation scores
     */
    public function submitScorePost()
    {
        $this->checkPermission();
        $candidateId = clrNum(inputPost('candidate_id'));
        $seasonId    = clrNum(inputPost('season_id') ?: 1);
        $categoryId  = clrNum(inputPost('category_id') ?: 1);
        $juryUserId  = clrNum(inputPost('jury_user_id') ?: (user() ? user()->id : 1));

        $c1 = (float)inputPost('criteria_1_score');
        $c2 = (float)inputPost('criteria_2_score');
        $c3 = (float)inputPost('criteria_3_score');
        $c4 = (float)inputPost('criteria_4_score');
        $notes = inputPost('notes') ? strTrim(inputPost('notes')) : null;

        // Save evaluation
        $success = $this->juryModel->submitEvaluation(
            $candidateId,
            $seasonId,
            $categoryId,
            $juryUserId,
            ['c1' => $c1, 'c2' => $c2, 'c3' => $c3, 'c4' => $c4],
            $notes
        );

        if ($success) {
            // Update candidate average jury score and composite score
            $this->candidateModel->updateAverageJuryScore($candidateId);

            // Recalculate category scores and rankings
            $this->scoringService->recalculateCategoryScores($categoryId, $seasonId);

            $this->session->setFlashdata('success', 'Đã lưu điểm đánh giá thẩm định và cập nhật điểm tổng hợp 70/30 thành công!');
            return redirect()->to(adminUrl('jury-evaluations?category_id=' . $categoryId . '&season_id=' . $seasonId));
        }

        $this->session->setFlashdata('error', 'Có lỗi xảy ra khi lưu kết quả chấm điểm.');
        return redirect()->to(adminUrl('jury-scoring/' . $candidateId))->withInput();
    }

    /**
     * List all jury judges and council members
     */
    public function juryMembers()
    {
        $this->checkPermission();
        $data['title'] = 'Danh Sách Hội Đồng Giám Khảo & Chuyên Gia';
        $judges = $this->getJuryUsersList();

        $db = \Config\Database::connect();
        foreach ($judges as &$j) {
            $j->total_evaluations = $db->table('tb_jury_evaluations')
                ->where('jury_user_id', $j->id)
                ->where('is_submitted', 1)
                ->countAllResults();
            $j->last_evaluation = $db->table('tb_jury_evaluations')
                ->where('jury_user_id', $j->id)
                ->orderBy('id', 'DESC')
                ->get()
                ->getRow();
        }

        $data['judges'] = $judges;
        $data['seasons'] = $this->seasonModel->getSeasons(50);
        $data['categories'] = $this->categoryModel->getCategoriesBySeason(1);

        echo view('admin/includes/_header', $data);
        echo view('admin/awards/jury/members', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Handle POST to assign candidate to judge for appraisal
     */
    public function assignCandidatePost()
    {
        $this->checkPermission();
        $candidateId = clrNum(inputPost('candidate_id'));
        $juryUserId  = clrNum(inputPost('jury_user_id'));

        if (empty($candidateId) || empty($juryUserId)) {
            $this->session->setFlashdata('error', 'Vui lòng chọn ứng viên và giám khảo.');
            return redirect()->to(adminUrl('jury-members'));
        }

        $candidate = $this->candidateModel->getCandidate($candidateId);
        if (!$candidate) {
            $this->session->setFlashdata('error', 'Ứng viên không tồn tại.');
            return redirect()->to(adminUrl('jury-members'));
        }

        // Initialize empty evaluation row if not exists
        $db = \Config\Database::connect();
        $existing = $db->table('tb_jury_evaluations')
            ->where('candidate_id', $candidateId)
            ->where('jury_user_id', $juryUserId)
            ->get()
            ->getRow();

        if (!$existing) {
            $db->table('tb_jury_evaluations')->insert([
                'candidate_id'     => $candidateId,
                'season_id'        => $candidate->season_id,
                'category_id'      => $candidate->category_id,
                'jury_user_id'     => $juryUserId,
                'criteria_1_score' => 0.00,
                'criteria_2_score' => 0.00,
                'criteria_3_score' => 0.00,
                'criteria_4_score' => 0.00,
                'total_score'      => 0.00,
                'is_submitted'     => 0,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ]);
        }

        $this->session->setFlashdata('success', 'Đã phân công ứng viên cho giám khảo thẩm định thành công!');
        return redirect()->to(adminUrl('jury-evaluations'));
    }

    /**
     * Fetch list of users eligible as jury judges
     */
    protected function getJuryUsersList(): array
    {
        $db = \Config\Database::connect();
        return $db->table('users')
            ->select('id, username, email, role, avatar, status')
            ->where('status', 1)
            ->orderBy('role', 'ASC')
            ->orderBy('username', 'ASC')
            ->get()
            ->getResult();
    }
}
