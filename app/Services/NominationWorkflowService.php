<?php

namespace App\Services;

use App\Models\NominationCandidateModel;
use App\Models\JuryEvaluationModel;

/**
 * NominationWorkflowService
 * Manages the 4-stage nomination & award review lifecycle:
 * Stage 1: Sơ khảo (Initial Screening)
 * Stage 2: Thẩm định (Expert Jury Rubric Evaluation)
 * Stage 3: Chung khảo (Final Composite Assessment)
 * Stage 4: Trao giải (Awarded / Hall of Fame Induction)
 *
 * Strict Compliance: <= 500 lines
 */
class NominationWorkflowService
{
    public const STAGE_SO_KHAO    = 'so_khao';
    public const STAGE_THAM_DINH  = 'tham_dinh';
    public const STAGE_CHUNG_KHAO = 'chung_khao';
    public const STAGE_TRAO_GIAI  = 'trao_giai';
    public const STAGE_REJECTED   = 'rejected';

    protected array $stageOrder = [
        self::STAGE_SO_KHAO    => 1,
        self::STAGE_THAM_DINH  => 2,
        self::STAGE_CHUNG_KHAO => 3,
        self::STAGE_TRAO_GIAI  => 4,
    ];

    protected $candidateModel;
    protected $juryModel;
    protected $db;

    public function __construct()
    {
        $this->candidateModel = new NominationCandidateModel();
        $this->juryModel      = new JuryEvaluationModel();
        $this->db             = \Config\Database::connect();
    }

    /**
     * Generate unique application tracking code: TBG-YYYY-XXXXXX
     */
    public function generateTrackingCode(int $year = 2026): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $rand  = '';
        for ($i = 0; $i < 6; $i++) {
            $rand .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return sprintf('TBG-%d-%s', $year, $rand);
    }

    /**
     * Validate tax code (10 or 13 digits for Vietnam businesses)
     */
    public function validateTaxCode(string $taxCode): bool
    {
        $clean = trim($taxCode);
        return (bool) preg_match('/^[0-9]{10}(-[0-9]{3}|[0-9]{3})?$/', $clean);
    }

    /**
     * Submit public nomination dossier
     */
    public function submitNomination(array $data): array
    {
        if (empty($data['organization_name']) || empty($data['category_id']) || empty($data['contact_email'])) {
            return ['success' => false, 'message' => 'Vui lòng điền đầy đủ các thông tin bắt buộc.'];
        }

        if (!empty($data['tax_code']) && !$this->validateTaxCode($data['tax_code'])) {
            return ['success' => false, 'message' => 'Mã số thuế không đúng định dạng (10 hoặc 13 chữ số).'];
        }

        $seasonId = $data['season_id'] ?? 1;
        if (!empty($data['tax_code'])) {
            $existing = $this->candidateModel->where('season_id', $seasonId)
                                             ->where('tax_code', trim($data['tax_code']))
                                             ->first();
            if (!empty($existing)) {
                return ['success' => false, 'message' => 'Doanh nghiệp này đã nộp hồ sơ đề cử trong mùa giải hiện tại.'];
            }
        }

        $trackingCode = $this->generateTrackingCode((int) date('Y'));

        $insertData = [
            'candidate_code'       => $trackingCode,
            'tracking_code'        => $trackingCode,
            'season_id'            => $seasonId,
            'category_id'          => (int) $data['category_id'],
            'name'                 => trim($data['brand_name'] ?? $data['organization_name']),
            'organization_name'    => trim($data['organization_name']),
            'brand_name'           => trim($data['brand_name'] ?? $data['organization_name']),
            'contact_person'       => trim($data['representative'] ?? ''),
            'representative'       => trim($data['representative'] ?? ''),
            'tax_code'             => trim($data['tax_code'] ?? ''),
            'industry_sector'      => trim($data['industry_sector'] ?? ''),
            'contact_email'        => trim($data['contact_email']),
            'contact_phone'        => trim($data['contact_phone'] ?? ''),
            'address'              => trim($data['address'] ?? ''),
            'bio_summary'          => $data['achievements_summary'] ?? '',
            'achievements_summary' => $data['achievements_summary'] ?? '',
            'dossier_content'      => json_encode($data['dossier_files'] ?? []),
            'dossier_files_json'   => json_encode($data['dossier_files'] ?? []),
            'stage'                => self::STAGE_SO_KHAO,
            'status'               => 'approved',
            'created_at'           => date('Y-m-d H:i:s'),
        ];

        try {
            $id = $this->candidateModel->insert($insertData);
            return [
                'success'       => true,
                'candidate_id'  => $id,
                'tracking_code' => $trackingCode,
                'message'       => 'Hồ sơ đề cử đã được nộp thành công vào vòng Sơ khảo.'
            ];
        } catch (\Exception $e) {
            log_message('error', 'NominationWorkflowService::submitNomination - ' . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi hệ thống khi lưu hồ sơ. Vui lòng thử lại.'];
        }
    }

    /**
     * Advance candidate to next stage with stage-gate validation
     */
    public function advanceStage(int $candidateId, string $targetStage, string $adminNotes = ''): array
    {
        $candidate = $this->candidateModel->find($candidateId);
        if (empty($candidate)) {
            return ['success' => false, 'message' => 'Không tìm thấy hồ sơ ứng viên.'];
        }

        $currentStage = $candidate->stage ?? self::STAGE_SO_KHAO;
        if ($currentStage === self::STAGE_TRAO_GIAI && $targetStage !== self::STAGE_TRAO_GIAI) {
            return ['success' => false, 'message' => 'Ứng viên đã được trao giải vinh danh, không thể hạ cấp.'];
        }

        if ($targetStage === self::STAGE_REJECTED) {
            $this->candidateModel->update($candidateId, [
                'stage'       => self::STAGE_REJECTED,
                'status'      => 'rejected',
                'admin_notes' => $adminNotes,
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);
            return ['success' => true, 'message' => 'Hồ sơ đã được đánh dấu không đạt yêu cầu.'];
        }

        $currentOrder = $this->stageOrder[$currentStage] ?? 1;
        $targetOrder  = $this->stageOrder[$targetStage] ?? 1;

        if ($targetOrder > $currentOrder + 1) {
            return ['success' => false, 'message' => 'Không thể nhảy cóc vòng xét duyệt. Vui lòng thực hiện tuần tự.'];
        }

        if ($targetStage === self::STAGE_CHUNG_KHAO) {
            $juryCount = $this->juryModel->where('candidate_id', $candidateId)->countAllResults();
            if ($juryCount === 0) {
                return ['success' => false, 'message' => 'Ứng viên cần có ít nhất 1 đánh giá thẩm định từ Hội đồng Giám khảo trước khi vào vòng Chung khảo.'];
            }
        }

        $this->candidateModel->update($candidateId, [
            'stage'       => $targetStage,
            'admin_notes' => $adminNotes,
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);

        return [
            'success'      => true,
            'new_stage'    => $targetStage,
            'message'      => 'Hồ sơ đã được chuyển tiếp sang vòng: ' . $this->getStageLabel($targetStage)
        ];
    }

    /**
     * Look up nomination status by tracking code
     */
    public function getStatusByTrackingCode(string $code): ?array
    {
        $clean = trim($code);
        $candidate = $this->candidateModel->where('candidate_code', $clean)
                                          ->orWhere('tracking_code', $clean)
                                          ->first();
        if (empty($candidate)) {
            return null;
        }

        $orgName = $candidate->organization_name ?? ($candidate->name ?? 'N/A');
        $brandName = $candidate->brand_name ?? ($candidate->name ?? $orgName);

        return [
            'tracking_code'     => $candidate->candidate_code ?? ($candidate->tracking_code ?? $clean),
            'organization_name' => $orgName,
            'brand_name'        => $brandName,
            'stage'             => $candidate->stage ?? self::STAGE_SO_KHAO,
            'stage_label'       => $this->getStageLabel($candidate->stage ?? self::STAGE_SO_KHAO),
            'stage_order'       => $this->stageOrder[$candidate->stage ?? self::STAGE_SO_KHAO] ?? 1,
            'status'            => $candidate->status ?? 'active',
            'created_at'        => $candidate->created_at ?? date('Y-m-d H:i:s'),
            'updated_at'        => $candidate->updated_at ?? null,
        ];
    }

    public function getStageLabel(string $stage): string
    {
        $labels = [
            self::STAGE_SO_KHAO    => 'Vòng 1: Sơ Khảo Hồ Sơ',
            self::STAGE_THAM_DINH  => 'Vòng 2: Thẩm Định Chuyên Gia',
            self::STAGE_CHUNG_KHAO => 'Vòng 3: Chung Khảo & Bình Chọn',
            self::STAGE_TRAO_GIAI  => 'Vòng 4: Vinh Danh & Trao Giải',
            self::STAGE_REJECTED   => 'Chưa Đạt Yêu Cầu',
        ];
        return $labels[$stage] ?? 'Đang Xử Lý';
    }
}
