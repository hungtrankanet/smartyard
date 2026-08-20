<?php

namespace App\Controllers;

use App\Models\AwardCategoryModel;
use App\Models\AwardSeasonModel;
use App\Models\NominationCandidateModel;
use App\Services\NominationWorkflowService;

/**
 * NominationController
 * Handles public nomination submission wizard, file uploads, and tracking status lookup.
 *
 * Strict Compliance: <= 500 lines
 */
class NominationController extends BaseController
{
    protected $workflowService;
    protected $categoryModel;
    protected $seasonModel;
    protected $candidateModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->workflowService = new NominationWorkflowService();
        $this->categoryModel   = new AwardCategoryModel();
        $this->seasonModel     = new AwardSeasonModel();
        $this->candidateModel  = new NominationCandidateModel();
    }

    /**
     * Nomination Landing & Wizard Page
     */
    public function index()
    {
        $categories = $this->categoryModel->orderBy('order_num', 'ASC')->findAll();
        $seasons    = $this->seasonModel->where('status', 'active')->orWhere('is_active', 1)->findAll();
        $activeSeason = $this->seasonModel->getActiveSeason();

        $data = [
            'title'        => 'Cổng Đề Cử Giải Thưởng TOP BEST GLOBAL 2026',
            'description'  => 'Nộp hồ sơ đề cử doanh nghiệp, thương hiệu và nhà lãnh đạo xuất sắc tham gia bình chọn và vinh danh quốc gia.',
            'categories'   => $categories,
            'seasons'      => $seasons,
            'activeSeason' => $activeSeason,
        ];

        return loadThemeView('nomination/index', $data);
    }

    /**
     * Apply Route Alias
     */
    public function apply()
    {
        return $this->index();
    }

    /**
     * Submit Nomination Dossier (Form POST or AJAX)
     */
    public function applyPost()
    {
        if ($this->request->getMethod() !== 'post') {
            return $this->response->setJSON(['success' => false, 'message' => 'Yêu cầu không hợp lệ.'])->setStatusCode(405);
        }

        $postData = $this->request->getPost();

        // Handle File Uploads (Dossier documents)
        $uploadedFiles = [];
        $files = $this->request->getFiles();
        if (!empty($files['dossier_files'])) {
            $fileList = is_array($files['dossier_files']) ? $files['dossier_files'] : [$files['dossier_files']];
            $allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
            $maxSizeBytes = 15 * 1024 * 1024; // 15MB

            foreach ($fileList as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $ext = strtolower($file->getClientExtension());
                    $size = $file->getSize();

                    // Security check: reject executable or unauthorized files
                    if (!in_array($ext, $allowedExtensions, true) || $size > $maxSizeBytes) {
                        if ($this->request->isAJAX()) {
                            return $this->response->setJSON([
                                'success' => false,
                                'message' => "Tập tin '{$file->getClientName()}' không hợp lệ. Chỉ chấp nhận file PDF, Word, Ảnh dung lượng <= 15MB."
                            ])->setStatusCode(400);
                        }
                        return redirect()->back()->withInput()->with('error', "Tập tin không đúng định dạng hoặc vượt quá 15MB.");
                    }

                    $newName = 'dossier_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                    $uploadPath = FCPATH . 'uploads/nominations';
                    if (!is_dir($uploadPath)) {
                        @mkdir($uploadPath, 0755, true);
                    }
                    $file->move($uploadPath, $newName);
                    $uploadedFiles[] = [
                        'file_name' => $file->getClientName(),
                        'file_path' => 'uploads/nominations/' . $newName,
                        'file_size' => $size,
                    ];
                }
            }
        }

        if (!empty($uploadedFiles)) {
            $postData['dossier_files'] = $uploadedFiles;
        }

        $result = $this->workflowService->submitNomination($postData);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($result);
        }

        if (!empty($result['success'])) {
            $code = $result['tracking_code'] ?? '';
            return redirect()->to(base_url("nomination/tracker?code={$code}"))
                             ->with('success', $result['message']);
        }

        return redirect()->back()->withInput()->with('error', $result['message'] ?? 'Lỗi khi nộp hồ sơ.');
    }

    /**
     * Submit Alias
     */
    public function submit()
    {
        return $this->applyPost();
    }

    /**
     * Tracker Page
     */
    public function tracker()
    {
        return $this->track();
    }

    /**
     * Track Application Status Page & Search
     */
    public function track()
    {
        $code = trim($this->request->getGet('code') ?? ($this->request->getPost('code') ?? ''));
        $trackingResult = null;

        if (!empty($code)) {
            $trackingResult = $this->workflowService->getStatusByTrackingCode($code);
        }

        if ($this->request->isAJAX()) {
            if (empty($trackingResult)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Không tìm thấy mã hồ sơ này.'])->setStatusCode(404);
            }
            return $this->response->setJSON(['success' => true, 'data' => $trackingResult]);
        }

        $data = [
            'title'          => 'Tra Cứu Tiến Độ Hồ Sơ Đề Cử — TOP BEST GLOBAL',
            'description'    => 'Theo dõi thời gian thực tiến độ thẩm định hồ sơ đề cử giải thưởng quốc gia.',
            'trackingCode'   => $code,
            'trackingResult' => $trackingResult,
        ];

        return loadThemeView('nomination/track', $data);
    }

    /**
     * Dedicated AJAX Tracking Lookup Endpoint
     */
    public function trackAjax()
    {
        $code = trim($this->request->getPost('code') ?? ($this->request->getGet('code') ?? ''));
        if (empty($code)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Vui lòng nhập mã hồ sơ tra cứu.'])->setStatusCode(400);
        }

        $status = $this->workflowService->getStatusByTrackingCode($code);
        if (empty($status)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Không tìm thấy thông tin cho mã hồ sơ: ' . esc($code)])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'success' => true,
            'data'    => $status,
        ]);
    }

    /**
     * Public Dossier Overview
     */
    public function dossier($code)
    {
        $clean = trim($code);
        $candidate = $this->candidateModel->getCandidateByCode($clean);
        if (!$candidate) {
            $candidate = $this->candidateModel->getCandidateBySlug($clean);
        }

        if (!$candidate) {
            return redirect()->to(base_url('nomination/tracker'))->with('error', 'Không tìm thấy hồ sơ đề cử.');
        }

        $trackingResult = $this->workflowService->getStatusByTrackingCode($candidate->candidate_code);

        $data = [
            'title'          => 'Hồ Sơ Đề Cử: ' . esc($candidate->organization_name ?: $candidate->name),
            'description'    => 'Thông tin chi tiết hồ sơ đề cử tham gia giải thưởng TOP BEST GLOBAL.',
            'candidate'      => $candidate,
            'trackingResult' => $trackingResult,
            'trackingCode'   => $candidate->candidate_code,
        ];

        return loadThemeView('nomination/track', $data);
    }
}

