<?php

namespace App\Controllers;

use App\Models\AwardCategoryModel;
use App\Models\AwardSeasonModel;
use App\Services\NominationWorkflowService;

/**
 * NominationController
 * Handles public nomination submission wizard and tracking status lookup.
 *
 * Strict Compliance: <= 500 lines
 */
class NominationController extends BaseController
{
    protected $workflowService;
    protected $categoryModel;
    protected $seasonModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->workflowService = new NominationWorkflowService();
        $this->categoryModel   = new AwardCategoryModel();
        $this->seasonModel     = new AwardSeasonModel();
    }

    /**
     * Nomination Landing & Wizard Page
     */
    public function index()
    {
        $categories = $this->categoryModel->orderBy('display_order', 'ASC')->findAll();
        $seasons    = $this->seasonModel->where('status', 'active')->findAll();

        $data = [
            'title'       => 'Cổng Đề Cử Giải Thưởng TOP BEST GLOBAL 2026',
            'description' => 'Nộp hồ sơ đề cử doanh nghiệp, thương hiệu và nhà lãnh đạo xuất sắc tham gia bình chọn và vinh danh quốc gia.',
            'categories'  => $categories,
            'seasons'     => $seasons,
        ];

        return loadThemeView('nomination/index', $data);
    }

    /**
     * Submit Nomination Dossier via AJAX
     */
    public function submit()
    {
        if ($this->request->getMethod() !== 'post') {
            return $this->response->setJSON(['success' => false, 'message' => 'Yêu cầu không hợp lệ.'])->setStatusCode(405);
        }

        $postData = $this->request->getPost();
        $result   = $this->workflowService->submitNomination($postData);

        return $this->response->setJSON($result);
    }

    /**
     * Track Application Status Page & API
     */
    public function track()
    {
        $code = trim($this->request->getGet('code') ?? '');
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
}
