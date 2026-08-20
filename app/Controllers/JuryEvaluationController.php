<?php

namespace App\Controllers;

use App\Models\NominationCandidateModel;
use App\Models\JuryEvaluationModel;
use App\Models\AwardCategoryModel;

/**
 * JuryEvaluationController
 * Expert jury scoring portal with 100-point standardized rubric evaluation.
 *
 * Strict Compliance: <= 500 lines
 */
class JuryEvaluationController extends BaseController
{
    protected $candidateModel;
    protected $juryModel;
    protected $categoryModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->candidateModel = new NominationCandidateModel();
        $this->juryModel      = new JuryEvaluationModel();
        $this->categoryModel  = new AwardCategoryModel();
    }

    /**
     * Jury Evaluation Portal Dashboard
     */
    public function index()
    {
        $categoryId = (int) ($this->request->getGet('category') ?? 0);
        $categories = $this->categoryModel->orderBy('order_num', 'ASC')->findAll();

        $builder = $this->candidateModel->whereIn('stage', ['tham_dinh', 'chung_khao', 'evaluation', 'preliminary']);
        if ($categoryId > 0) {
            $builder->where('category_id', $categoryId);
        }

        $candidates = $builder->orderBy('id', 'DESC')->findAll();

        // Attach category names and evaluation states
        foreach ($candidates as $c) {
            if (empty($c->category_name) && !empty($c->category_id)) {
                $cat = $this->categoryModel->getCategory($c->category_id);
                $c->category_name = $cat ? $cat->name : 'N/A';
            }
            $eval = $this->juryModel->getEvaluationByJuryAndCandidate(1, (int)$c->id);
            $c->jury_evaluated = !empty($eval && $eval->is_submitted);
            $c->my_score = $eval ? $eval->total_score : null;
        }

        $data = [
            'title'              => 'Cổng Hội Đồng Giám Khảo & Thẩm Định — TOP BEST GLOBAL',
            'description'        => 'Hệ thống chấm điểm chuyên môn theo bộ tiêu chí chuẩn hóa 100 điểm cho các hồ sơ đề cử.',
            'categories'         => $categories,
            'candidates'         => $candidates,
            'selectedCategoryId' => $categoryId,
        ];

        return loadThemeView('jury/index', $data);
    }

    /**
     * Candidate Evaluation View with Rubric
     */
    public function evaluate($candidateId)
    {
        $candidate = $this->candidateModel->getCandidate((int) $candidateId);
        if (empty($candidate)) {
            $candidate = $this->candidateModel->find((int) $candidateId);
        }

        if (empty($candidate)) {
            return redirect()->to(base_url('jury'))->with('error', 'Không tìm thấy hồ sơ ứng viên.');
        }

        $existingEval = $this->juryModel->getEvaluationByJuryAndCandidate(1, (int)$candidate->id);

        $data = [
            'title'            => 'Thẩm Định Hồ Sơ: ' . esc($candidate->organization_name ?: $candidate->name),
            'candidate'        => $candidate,
            'existingEval'     => $existingEval,
            'rubricDimensions' => [
                'innovation'       => ['weight' => 25, 'title' => '1. Đổi Mới Sáng Tạo & Ứng Dụng Công Nghệ', 'desc' => 'Đổi mới công nghệ, bằng độc quyền, sở hữu trí tuệ, mức độ số hóa.'],
                'business_growth'  => ['weight' => 30, 'title' => '2. Hiệu Quả Kinh Doanh & Năng Lực Cạnh Tranh', 'desc' => 'Tăng trưởng doanh thu, thị phần, hiệu quả tài chính, quy mô xuất khẩu.'],
                'social_impact'    => ['weight' => 25, 'title' => '3. Trách Nhiệm Xã Hội & Phát Triển Bền Vững (ESG)', 'desc' => 'Bảo vệ môi trường, phát thải ròng, phúc lợi người lao động và đóng góp cộng đồng.'],
                'brand_reputation' => ['weight' => 20, 'title' => '4. Uy Tín Thương Hiệu & Quản Trị Doanh Nghiệp', 'desc' => 'Minh bạch thông tin, tuân thủ pháp luật, văn hóa doanh nghiệp và chỉ số uy tín.'],
            ]
        ];

        return loadThemeView('jury/evaluate', $data);
    }

    /**
     * Submit Jury Rubric Score
     */
    public function submitScore()
    {
        if ($this->request->getMethod() !== 'post') {
            return $this->response->setJSON(['success' => false, 'message' => 'Yêu cầu không hợp lệ.'])->setStatusCode(405);
        }

        $candidateId = (int) $this->request->getPost('candidate_id');
        $judgeId     = (int) ($this->request->getPost('judge_id') ?? 1);
        $judgeName   = trim($this->request->getPost('judge_name') ?? 'Hội Đồng Chuyên Gia');

        $candidate = $this->candidateModel->getCandidate($candidateId);
        $seasonId  = (int)($candidate->season_id ?? 1);
        $categoryId = (int)($candidate->category_id ?? 1);

        $scoreInnovation = max(0, min(100, (float) $this->request->getPost('score_innovation')));
        $scoreBusiness   = max(0, min(100, (float) $this->request->getPost('score_business')));
        $scoreSocial     = max(0, min(100, (float) $this->request->getPost('score_social')));
        $scoreBrand      = max(0, min(100, (float) $this->request->getPost('score_brand')));

        // Standardized Rubric Weighted Formula: (25% + 30% + 25% + 20%)
        $totalScore = round(($scoreInnovation * 0.25) + ($scoreBusiness * 0.30) + ($scoreSocial * 0.25) + ($scoreBrand * 0.20), 2);
        $comments   = trim($this->request->getPost('comments') ?? '');

        try {
            $this->juryModel->submitEvaluation($candidateId, $seasonId, $categoryId, $judgeId, [
                'c1' => $scoreInnovation,
                'c2' => $scoreBusiness,
                'c3' => $scoreSocial,
                'c4' => $scoreBrand,
            ], $comments);

            // Update candidate consensus jury score average and composite score
            $this->candidateModel->updateAverageJuryScore($candidateId);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success'     => true,
                    'total_score' => $totalScore,
                    'message'     => "Đã lưu và khóa kết quả thẩm định thành công: {$totalScore}/100 điểm."
                ]);
            }

            return redirect()->to(base_url('jury'))->with('success', "Đã chấm điểm thành công: {$totalScore}/100 điểm.");
        } catch (\Exception $e) {
            log_message('error', 'JuryEvaluationController::submitScore - ' . $e->getMessage());
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Lỗi lưu điểm thẩm định.'])->setStatusCode(500);
            }
            return redirect()->back()->withInput()->with('error', 'Lỗi lưu điểm thẩm định.');
        }
    }
}

