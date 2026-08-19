<?php

namespace App\Controllers;

use App\Models\NominationCandidateModel;
use App\Models\AwardCategoryModel;
use App\Models\AwardSeasonModel;
use App\Services\DigitalCertificateService;

/**
 * HallOfFameController
 * Bảng Vàng Vinh Danh (Hall of Fame) honorees showcase & public digital certificate verification.
 *
 * Strict Compliance: <= 500 lines
 */
class HallOfFameController extends BaseController
{
    protected $candidateModel;
    protected $categoryModel;
    protected $seasonModel;
    protected $certificateService;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->candidateModel     = new NominationCandidateModel();
        $this->categoryModel      = new AwardCategoryModel();
        $this->seasonModel        = new AwardSeasonModel();
        $this->certificateService = new DigitalCertificateService();
    }

    /**
     * Hall of Fame Main Showcase
     */
    public function index()
    {
        $year       = (int) ($this->request->getGet('year') ?? date('Y'));
        $categoryId = (int) ($this->request->getGet('category') ?? 0);

        $categories = $this->categoryModel->findAll();
        $seasons    = $this->seasonModel->findAll();

        $builder = $this->candidateModel->where('stage', 'trao_giai');
        if ($categoryId > 0) {
            $builder->where('category_id', $categoryId);
        }

        $honorees = $builder->orderBy('final_composite_score', 'DESC')->findAll();

        $data = [
            'title'              => "Bảng Vàng Vinh Danh TOP BEST GLOBAL {$year}",
            'description'        => "Tôn vinh các doanh nghiệp, thương hiệu và nhà lãnh đạo xuất sắc nhất Việt Nam mùa giải {$year}.",
            'selectedYear'       => $year,
            'selectedCategoryId' => $categoryId,
            'categories'         => $categories,
            'seasons'            => $seasons,
            'honorees'           => $honorees,
        ];

        return loadThemeView('hall_of_fame/index', $data);
    }

    /**
     * Public Digital Certificate Verification Endpoint
     */
    public function verify($serialCode = null)
    {
        $code = trim($serialCode ?? $this->request->getGet('code') ?? '');
        $verification = $this->certificateService->verifyCertificate($code);

        $data = [
            'title'        => 'Xác Thực Bảng Vàng Vinh Danh Số Hóa — TOP BEST GLOBAL',
            'description'  => 'Tra cứu và xác thực tính chính danh của chứng nhận vinh danh quốc gia.',
            'serialCode'   => $code,
            'verification' => $verification,
            'embedSnippet' => $this->certificateService->generateEmbedSnippet($code ?: 'TBG-2026-C01-HONOR'),
        ];

        return loadThemeView('hall_of_fame/verify', $data);
    }

    /**
     * Dynamic SVG Digital Badge API Endpoint
     */
    public function badgeSvg($serialCode)
    {
        $verification = $this->certificateService->verifyCertificate($serialCode);
        $svgContent   = $this->certificateService->renderSvgBadge($verification);

        return $this->response
                    ->setHeader('Content-Type', 'image/svg+xml')
                    ->setHeader('Cache-Control', 'public, max-age=86400')
                    ->setBody($svgContent);
    }
}
