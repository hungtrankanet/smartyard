<?php
$avatar = !empty($candidate->avatar) ? base_url($candidate->avatar) : '';
$c1 = isset($existingEval->c1_score) ? (float)$existingEval->c1_score : 80;
$c2 = isset($existingEval->c2_score) ? (float)$existingEval->c2_score : 85;
$c3 = isset($existingEval->c3_score) ? (float)$existingEval->c3_score : 80;
$c4 = isset($existingEval->c4_score) ? (float)$existingEval->c4_score : 85;
$initialTotal = round(($c1 * 0.25) + ($c2 * 0.30) + ($c3 * 0.25) + ($c4 * 0.20), 2);
?>
<!-- TOP BEST GLOBAL - Expert Jury Candidate Evaluation & Rubric Form -->
<section class="py-5" style="background: linear-gradient(135deg, #0A192F 0%, #0d2342 60%, #1e293b 100%); border-bottom: 3px solid #D4AF37;">
    <div class="container py-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-3" style="font-size: 13px;">
                <li class="breadcrumb-item"><a href="<?= langBaseUrl(); ?>" class="text-light">Trang Chủ</a></li>
                <li class="breadcrumb-item"><a href="<?= langBaseUrl('jury'); ?>" class="text-light">Cổng Giám Khảo</a></li>
                <li class="breadcrumb-item active text-warning" aria-current="page"><?= esc($candidate->name); ?></li>
            </ol>
        </nav>
        <div class="row align-items-center">
            <div class="col-lg-8 text-white">
                <div class="d-flex align-items-center mb-2">
                    <span class="badge mr-2 px-3 py-1" style="background: #D4AF37; color: #0A192F; font-weight: 800; font-size: 12px; border-radius: 20px;">
                        MÃ: <?= esc($candidate->candidate_code); ?>
                    </span>
                    <span class="badge badge-outline-light px-3 py-1" style="border: 1px solid rgba(255,255,255,0.4); color: #fff; font-size: 12px; border-radius: 20px;">
                        Giai đoạn: Thẩm định chuyên môn
                    </span>
                </div>
                <h1 class="font-weight-bold display-5 mb-2" style="color: #ffffff;">
                    <?= esc($candidate->name); ?>
                </h1>
                <h5 class="text-warning font-weight-normal mb-0" style="opacity: 0.95;">
                    <i class="fa fa-building-o mr-1"></i> <?= esc($candidate->organization_name ?? $candidate->name); ?>
                </h5>
            </div>
            <div class="col-lg-4 text-right mt-3 mt-lg-0">
                <a href="<?= langBaseUrl('jury'); ?>" class="btn btn-outline-light font-weight-bold" style="border-radius: 8px;">
                    <i class="fa fa-arrow-left mr-1"></i> Quay Lại Danh Sách
                </a>
            </div>
        </div>
    </div>
</section>

<section class="py-5" style="background: #f8fafc;">
    <div class="container">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 10px;">
                <i class="fa fa-check-circle mr-1"></i> <?= session()->getFlashdata('success'); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Left Column: Dossier Information -->
            <div class="col-lg-5 mb-4 mb-lg-0">
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden;">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="font-weight-bold mb-0" style="color: #0A192F;">
                            <i class="fa fa-file-text-o text-warning mr-1"></i> Thông Tin Hồ Sơ Đề Cử
                        </h6>
                        <span class="badge badge-light border">Bảo mật giám khảo</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <div style="width: 90px; height: 90px; border-radius: 16px; background: #0A192F; color: #D4AF37; margin: 0 auto 12px; display: flex; align-items: center; justify-content: center; font-size: 36px; overflow: hidden; border: 2px solid #e2e8f0;">
                                <?php if (!empty($avatar)): ?>
                                    <img src="<?= $avatar; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <i class="fa fa-building"></i>
                                <?php endif; ?>
                            </div>
                            <h5 class="font-weight-bold mb-1" style="color: #0A192F;"><?= esc($candidate->name); ?></h5>
                            <small class="text-muted"><?= esc($candidate->organization_name ?? ''); ?></small>
                        </div>

                        <div class="p-3 mb-3 rounded" style="background: #f1f5f9; font-size: 13px; line-height: 22px;">
                            <?php if (!empty($candidate->tax_code)): ?>
                                <div><strong>Mã số thuế:</strong> <?= esc($candidate->tax_code); ?></div>
                            <?php endif; ?>
                            <?php if (!empty($candidate->website)): ?>
                                <div><strong>Website:</strong> <a href="<?= esc($candidate->website); ?>" target="_blank" rel="nofollow"><?= esc($candidate->website); ?></a></div>
                            <?php endif; ?>
                            <div><strong>Mã ứng viên:</strong> <?= esc($candidate->candidate_code); ?></div>
                        </div>

                        <h6 class="font-weight-bold mb-2 text-dark" style="font-size: 14px;">Tóm tắt năng lực & thành tích:</h6>
                        <p class="small text-muted mb-3" style="line-height: 20px;">
                            <?= nl2br(esc($candidate->bio_summary ?? 'Chưa cập nhật tóm tắt thành tích.')); ?>
                        </p>

                        <?php if (!empty($candidate->dossier_content)): ?>
                            <h6 class="font-weight-bold mb-2 text-dark" style="font-size: 14px;">Hồ sơ chi tiết đính kèm:</h6>
                            <div class="small text-muted p-3 bg-white border rounded" style="max-height: 250px; overflow-y: auto; line-height: 20px;">
                                <?= $candidate->dossier_content; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column: Interactive Rubric Scoring Form -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm" style="border-radius: 16px; border: 2px solid #D4AF37; overflow: hidden;">
                    <div class="p-4 text-white d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #0A192F 0%, #1e293b 100%); border-bottom: 2px solid #D4AF37;">
                        <div>
                            <span class="badge" style="background: #D4AF37; color: #0A192F; font-weight: 800; font-size: 11px;">RUBRIC CHUẨN HÓA</span>
                            <h5 class="font-weight-bold mb-0 mt-1" style="color: #ffffff;">Bảng Điểm Thẩm Định 100 Điểm</h5>
                        </div>
                        <div class="text-right">
                            <span class="small text-light d-block" style="font-size: 11px; text-transform: uppercase;">Điểm Tổng Hợp Rubric</span>
                            <span class="h3 font-weight-bold mb-0" id="rubricTotalDisplay" style="color: #F3E5AB;"><?= $initialTotal; ?> / 100</span>
                        </div>
                    </div>

                    <div class="card-body p-4 p-md-5 bg-white">
                        <form id="juryScoringForm" action="<?= langBaseUrl('jury/submit-score'); ?>" method="post">
                            <?= csrf_field(); ?>
                            <input type="hidden" name="candidate_id" value="<?= (int)$candidate->id; ?>">
                            <input type="hidden" name="judge_id" value="1">
                            <input type="hidden" name="judge_name" value="Hội Đồng Thẩm Định Chuyên Gia">

                            <!-- Criterion 1: Innovation (25%) -->
                            <div class="mb-4 p-3 rounded" style="background: #faf8f5; border: 1px solid #f0e6d2;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <strong style="color: #0A192F; font-size: 14px;">1. Đổi Mới Sáng Tạo & Ứng Dụng Công Nghệ</strong>
                                        <span class="badge badge-warning text-dark ml-2">Trọng số 25%</span>
                                    </div>
                                    <span class="h5 font-weight-bold text-primary mb-0" id="c1ValDisplay"><?= $c1; ?></span>
                                </div>
                                <p class="small text-muted mb-2">Đổi mới công nghệ, số hóa quy trình, sở hữu trí tuệ, bằng sáng chế hoặc giải pháp kỹ thuật vượt bậc.</p>
                                <div class="d-flex align-items-center gap-3">
                                    <input type="range" class="custom-range flex-grow-1" id="score_innovation" name="score_innovation" min="0" max="100" step="1" value="<?= $c1; ?>" oninput="updateRubricScore()">
                                    <input type="number" class="form-control form-control-sm text-center font-weight-bold" style="width: 70px; border-radius: 6px;" min="0" max="100" value="<?= $c1; ?>" onchange="syncFromInput('score_innovation', this.value)">
                                </div>
                            </div>

                            <!-- Criterion 2: Business Growth (30%) -->
                            <div class="mb-4 p-3 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <strong style="color: #0A192F; font-size: 14px;">2. Hiệu Quả Kinh Doanh & Năng Lực Cạnh Tranh</strong>
                                        <span class="badge badge-primary ml-2">Trọng số 30%</span>
                                    </div>
                                    <span class="h5 font-weight-bold text-primary mb-0" id="c2ValDisplay"><?= $c2; ?></span>
                                </div>
                                <p class="small text-muted mb-2">Tăng trưởng doanh thu, lợi nhuận, quy mô thị trường, năng lực tài chính và uy tín xuất khẩu.</p>
                                <div class="d-flex align-items-center gap-3">
                                    <input type="range" class="custom-range flex-grow-1" id="score_business" name="score_business" min="0" max="100" step="1" value="<?= $c2; ?>" oninput="updateRubricScore()">
                                    <input type="number" class="form-control form-control-sm text-center font-weight-bold" style="width: 70px; border-radius: 6px;" min="0" max="100" value="<?= $c2; ?>" onchange="syncFromInput('score_business', this.value)">
                                </div>
                            </div>

                            <!-- Criterion 3: ESG & Social Impact (25%) -->
                            <div class="mb-4 p-3 rounded" style="background: #f0fdf4; border: 1px solid #dcfce7;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <strong style="color: #0A192F; font-size: 14px;">3. Trách Nhiệm Xã Hội & Phát Triển Bền Vững (ESG)</strong>
                                        <span class="badge badge-success ml-2">Trọng số 25%</span>
                                    </div>
                                    <span class="h5 font-weight-bold text-success mb-0" id="c3ValDisplay"><?= $c3; ?></span>
                                </div>
                                <p class="small text-muted mb-2">Bảo vệ môi trường, tiết kiệm năng lượng, phúc lợi nhân viên và đóng góp an sinh xã hội.</p>
                                <div class="d-flex align-items-center gap-3">
                                    <input type="range" class="custom-range flex-grow-1" id="score_social" name="score_social" min="0" max="100" step="1" value="<?= $c3; ?>" oninput="updateRubricScore()">
                                    <input type="number" class="form-control form-control-sm text-center font-weight-bold" style="width: 70px; border-radius: 6px;" min="0" max="100" value="<?= $c3; ?>" onchange="syncFromInput('score_social', this.value)">
                                </div>
                            </div>

                            <!-- Criterion 4: Brand Reputation & Governance (20%) -->
                            <div class="mb-4 p-3 rounded" style="background: #faf5ff; border: 1px solid #f3e8ff;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <strong style="color: #0A192F; font-size: 14px;">4. Uy Tín Thương Hiệu & Quản Trị Doanh Nghiệp</strong>
                                        <span class="badge badge-info ml-2">Trọng số 20%</span>
                                    </div>
                                    <span class="h5 font-weight-bold text-info mb-0" id="c4ValDisplay"><?= $c4; ?></span>
                                </div>
                                <p class="small text-muted mb-2">Minh bạch thông tin, tuân thủ pháp lý, văn hóa tổ chức và đánh giá tín nhiệm thị trường.</p>
                                <div class="d-flex align-items-center gap-3">
                                    <input type="range" class="custom-range flex-grow-1" id="score_brand" name="score_brand" min="0" max="100" step="1" value="<?= $c4; ?>" oninput="updateRubricScore()">
                                    <input type="number" class="form-control form-control-sm text-center font-weight-bold" style="width: 70px; border-radius: 6px;" min="0" max="100" value="<?= $c4; ?>" onchange="syncFromInput('score_brand', this.value)">
                                </div>
                            </div>

                            <!-- Qualitative Comments -->
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark" style="font-size: 14px;">Nhận xét chuyên môn & Ý kiến hội đồng:</label>
                                <textarea name="comments" rows="3" class="form-control" placeholder="Ghi nhận đánh giá định tính của Giám khảo về hồ sơ đề cử này..." style="border-radius: 8px; font-size: 13px;"><?= esc($existingEval->comments ?? ''); ?></textarea>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-warning btn-block font-weight-bold text-dark py-3" style="background: linear-gradient(135deg, #D4AF37 0%, #F3E5AB 50%, #AA771C 100%); border: none; border-radius: 10px; font-size: 16px; letter-spacing: 0.5px; box-shadow: 0 4px 15px rgba(212,175,55,0.4);">
                                <i class="fa fa-lock mr-1"></i> XÁC NHẬN & KHÓA ĐIỂM THẨM ĐỊNH
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function updateRubricScore(){
    const s1 = parseFloat(document.getElementById("score_innovation").value) || 0;
    const s2 = parseFloat(document.getElementById("score_business").value) || 0;
    const s3 = parseFloat(document.getElementById("score_social").value) || 0;
    const s4 = parseFloat(document.getElementById("score_brand").value) || 0;

    document.getElementById("c1ValDisplay").innerText = s1;
    document.getElementById("c2ValDisplay").innerText = s2;
    document.getElementById("c3ValDisplay").innerText = s3;
    document.getElementById("c4ValDisplay").innerText = s4;

    const total = ((s1 * 0.25) + (s2 * 0.30) + (s3 * 0.25) + (s4 * 0.20)).toFixed(2);
    document.getElementById("rubricTotalDisplay").innerText = total + " / 100";
}

function syncFromInput(rangeId, val){
    const num = Math.max(0, Math.min(100, parseFloat(val) || 0));
    document.getElementById(rangeId).value = num;
    updateRubricScore();
}
</script>
