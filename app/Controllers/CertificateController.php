<?php

namespace App\Controllers;

use App\Services\DigitalCertificateService;
use App\Models\NominationCandidateModel;

class CertificateController extends BaseController
{
    protected $certificateService;
    protected $candidateModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->certificateService = new DigitalCertificateService();
        $this->candidateModel     = new NominationCandidateModel();
    }

    public function verify($serialCode = null)
    {
        $code = trim($serialCode ?? ($this->request->getGet("code") ?? ""));
        $verification = $this->certificateService->verifyCertificate($code);

        $data = [
            "title"        => "Xác Thực Chứng Nhận Vinh Danh Quốc Gia — TOP BEST GLOBAL",
            "description"  => "Tra cứu và xác thực tính chính danh của chứng nhận vinh danh kỹ thuật số với con dấu số.",
            "serialCode"   => $code,
            "verification" => $verification,
            "embedSnippet" => $this->certificateService->generateEmbedSnippet($code ?: "TBG-2026-C01-HONOR"),
        ];

        return loadThemeView("hall_of_fame/verify", $data);
    }

    public function verifyApi($serialCode = null)
    {
        $code = trim($serialCode ?? ($this->request->getGet("code") ?? ($this->request->getPost("code") ?? "")));
        if (empty($code)) {
            return $this->response->setJSON([
                "is_valid"    => false,
                "valid"       => false,
                "message"     => "Mã chứng nhận không được để trống.",
                "serial_code" => "",
            ])->setStatusCode(400);
        }

        $verification = $this->certificateService->verifyCertificate($code);
        $statusCode = !empty($verification["is_valid"]) ? 200 : 404;
        return $this->response->setJSON($verification)->setStatusCode($statusCode);
    }

    public function badgeSvg($serialCode)
    {
        $clean = trim($serialCode);
        $clean = str_replace(".svg", "", $clean);

        $verification = $this->certificateService->verifyCertificate($clean);
        $svgContent   = $this->certificateService->renderSvgBadge($verification);

        return $this->response
                    ->setHeader("Content-Type", "image/svg+xml; charset=utf-8")
                    ->setHeader("Cache-Control", "public, max-age=86400")
                    ->setBody($svgContent);
    }

    public function download($serialCode)
    {
        $clean = trim($serialCode);
        $clean = str_replace(".svg", "", $clean);

        $verification = $this->certificateService->verifyCertificate($clean);
        if (empty($verification["is_valid"])) {
            return redirect()->to(base_url("verify-certificate"))->with("error", "Mã chứng nhận không hợp lệ.");
        }

        $svgContent = $this->certificateService->renderSvgBadge($verification);
        $filename   = "TOPBESTGLOBAL_Certificate_{$clean}.svg";

        return $this->response
                    ->setHeader("Content-Type", "image/svg+xml; charset=utf-8")
                    ->setHeader("Content-Disposition", "attachment; filename=\"" . $filename . "\"")
                    ->setBody($svgContent);
    }
}
