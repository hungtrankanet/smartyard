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
        $categories = $this->categoryModel->findAll();
        $candidates = $this->candidateModel->whereIn('stage', ['tham_dinh', 'chung_khao'])->findAll();

        $data = [
            'title'       => 'Cổng Hội Đồng Giám Khảo & Thẩm Định — TOP BEST GLOBAL',
            'description' => 'Hệ thống chấm điểm chuyên môn theo bộ tiêu chí chuẩn hóa 100 điểm cho các hồ sơ đề cử.',
            'categories'  => $categories,
            'candidates'  => $candidates,
        ];

        return loadThemeView('jury/index', $data);
    }

    /**
     * Candidate Evaluation View with Rubric
     */
    public function evaluate($candidateId)
    {
        $candidate = $this->candidateModel->find((int) $candidateId);
        if (empty($candidate)) {
            return redirect()->to(base_url('jury'))->with('error', 'Không tìm thấy hồ sơ ứng viên.');
        }

        $existingEval = $this->juryModel->where('candidate_id', $candidate->id)->first();

        $data = [
            'title'        => 'Thẩm Định Hồ Sơ: ' . esc($candidate->organization_name),
            'candidate'    => $candidate,
            'existingEval' => $existingEval,
            'rubricDimensions' => [
                'innovation'       => ['weight' => 25, 'title' => 'Đổi Mới Sáng Tạo & Ứng Dụng Công Nghệ'],
                'business_growth'  => ['weight' => 30, 'title' => 'Hiệu Quả Kinh Doanh & Năng Lực Cạnh Tranh'],
                'social_impact'    => ['weight' => 25, 'title' => 'Trách Nhiệm Xã Hội & Phát Triển Bền Vững'],
                'brand_reputation' => ['weight' => 20, 'title' => 'Uy Tín Thương Hiệu & Quản Trị Doanh Nghiệp'],
            ]
        ];

        return loadThemeView('jury/evaluate', $data);
    }

    /**
     * Submit Jury Rubric Score via AJAX
     */
    public function submitScore()
    {
        if ($this->request->getMethod() !== 'post') {
            return $this->response->setJSON(['success' => false, 'message' => 'Yêu cầu không hợp lệ.'])->setStatusCode(405);
        }

        $candidateId = (int) $this->request->getPost('candidate_id');
        $judgeId     = (int) ($this->request->getPost('judge_id') ?? 1);
        $judgeName   = trim($this->request->getPost('judge_name') ?? 'Hội Đồng Chuyên Gia');

        $scoreInnovation = max(0, min(100, (float) $this->request->getPost('score_innovation')));
        $scoreBusiness   = max(0, min(100, (float) $this->request->getPost('score_business')));
        $scoreSocial     = max(0, min(100, (float) $this->request->getPost('score_social')));
        $scoreBrand      = max(0, min(100, (float) $this->request->getPost('score_brand')));

        // Weighted sum (25% + 30% + 25% + 20%)
        $totalScore = round(($scoreInnovation * 0.25) + ($scoreBusiness * 0.30) + ($scoreSocial * 0.25) + ($scoreBrand * 0.20), 2);
        $comments   = trim($this->request->getPost('comments') ?? '');

        $saveData = [
            'candidate_id'     => $candidateId,
            'judge_id'         => $judgeId,
            'judge_name'       => $judgeName,
            'rubric_scores'    => json_encode([
                'innovation'   => $scoreInnovation,
                'business'     => $scoreBusiness,
                'social'       => $scoreSocial,
                'brand'        => $scoreBrand,
            ]),
            'total_score'      => $totalScore,
            'evaluation_notes' => $comments,
            'is_locked'        => 1,
            'evaluated_at'     => date('Y-m-d H:i:s'),
        ];

        try {
            $this->juryModel->saveEvaluation($saveData);
            // Update candidate jury_score average
            $this->candidateModel->updateAverageJuryScore($candidateId);

            return $this->response->setJSON([
                'success'     => true,
                'total_score' => $totalScore,
                'message'     => "Đã lưu và khóa kết quả thẩm định: {$totalScore}/100 điểm."
            ]);
        } catch (\Exception $e) {
            log_message('error', 'JuryEvaluationController::submitScore - ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Lỗi lưu điểm thẩm định.'])->setStatusCode(500);
        }
    }
}
