<!-- TOP BEST GLOBAL - Expert Jury Evaluation Dashboard -->
<section class="py-5" style="background: linear-gradient(135deg, #0A192F 0%, #0d2342 60%, #1e293b 100%); border-bottom: 3px solid #D4AF37;">
    <div class="container py-3">
        <div class="row align-items-center">
            <div class="col-lg-8 text-white">
                <div class="d-inline-flex align-items-center mb-3 px-3 py-1 rounded-pill" style="background: rgba(212,175,55,0.15); border: 1px solid #D4AF37;">
                    <i class="fa fa-gavel text-warning mr-2"></i>
                    <span style="color: #F3E5AB; font-weight: 700; font-size: 12px; letter-spacing: 1px; text-transform: uppercase;">
                        CỔNG HỘI ĐỒNG GIÁM KHẢO CHUYÊN MÔN
                    </span>
                </div>
                <h1 class="display-5 font-weight-bold mb-2" style="color: #ffffff;">
                    Thẩm Định & Chấm Điểm Hồ Sơ Đề Cử
                </h1>
                <p class="lead text-light mb-0" style="font-size: 15px; opacity: 0.9; max-width: 680px; line-height: 24px;">
                    Hệ thống đánh giá độc lập theo bộ tiêu chí chuẩn hóa 100 điểm: Đổi mới sáng tạo (25%), Hiệu quả kinh doanh (30%), ESG & Trách nhiệm xã hội (25%), Uy tín thương hiệu (20%).
                </p>
            </div>
            <div class="col-lg-4 text-center mt-4 mt-lg-0 d-none d-lg-block">
                <div class="p-3 rounded-lg" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(212,175,55,0.3); border-radius: 16px;">
                    <div style="font-size: 32px; color: #D4AF37; margin-bottom: 6px;"><i class="fa fa-balance-scale"></i></div>
                    <h6 class="text-white font-weight-bold mb-1">Tỷ Trọng Giám Khảo: 70%</h6>
                    <small class="text-light" style="opacity: 0.8;">Kết quả thẩm định của Giám khảo đóng góp 70% vào Điểm Tổng Hợp cuối cùng.</small>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5" style="background: #f8fafc; min-height: 650px;">
    <div class="container">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 10px;">
                <i class="fa fa-check-circle mr-1"></i> <?= session()->getFlashdata('success'); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 10px;">
                <i class="fa fa-exclamation-triangle mr-1"></i> <?= session()->getFlashdata('error'); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        <?php endif; ?>

        <!-- Category Filters & Search -->
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-3 border-bottom">
            <div class="d-flex flex-wrap gap-2 py-2" style="gap: 8px;">
                <a href="<?= langBaseUrl('jury'); ?>" class="btn btn-sm <?= empty($selectedCategoryId) ? 'btn-primary font-weight-bold' : 'btn-outline-secondary'; ?>" style="border-radius: 20px; padding: 6px 16px;">
                    Tất Cả Hạng Mục
                </a>
                <?php if (!empty($categories)): foreach ($categories as $cat): ?>
                    <a href="<?= langBaseUrl('jury?category=' . $cat->id); ?>" class="btn btn-sm <?= ($selectedCategoryId == $cat->id) ? 'btn-primary font-weight-bold' : 'btn-outline-secondary'; ?>" style="border-radius: 20px; padding: 6px 16px;">
                        <i class="<?= esc($cat->icon ?? 'fa fa-award'); ?> mr-1"></i> <?= esc($cat->name); ?>
                    </a>
                <?php endforeach; endif; ?>
            </div>
            <div>
                <input type="text" id="juryCandidateSearch" class="form-control form-control-sm" placeholder="Tìm theo tên doanh nghiệp/mã..." onkeyup="filterJuryList()" style="border-radius: 20px; width: 250px; padding-left: 14px;">
            </div>
        </div>

        <!-- Candidate Dossiers Grid for Evaluation -->
        <div class="row" id="juryCandidateGrid">
            <?php if (!empty($candidates)): foreach ($candidates as $candidate): 
                $avatar = !empty($candidate->avatar) ? base_url($candidate->avatar) : '';
                $isEvaluated = !empty($candidate->jury_evaluated);
                $myScore = $candidate->my_score !== null ? number_format((float)$candidate->my_score, 2) : null;
            ?>
                <div class="col-md-6 col-lg-4 mb-4 jury-card-item" data-name="<?= strtolower(esc($candidate->name)); ?>" data-code="<?= strtolower(esc($candidate->candidate_code)); ?>">
                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 14px; border: 1px solid #e2e8f0; overflow: hidden;">
                        <div class="p-3 d-flex align-items-center justify-content-between" style="background: <?= $isEvaluated ? '#f0fdf4' : '#f8fafc'; ?>; border-bottom: 1px solid #e2e8f0;">
                            <span class="badge" style="background: #0A192F; color: #D4AF37; font-weight: 800; font-size: 11px; padding: 4px 10px; border-radius: 12px;">
                                <?= esc($candidate->candidate_code); ?>
                            </span>
                            <?php if ($isEvaluated): ?>
                                <span class="badge badge-success font-weight-bold" style="font-size: 11px; padding: 4px 10px; border-radius: 12px;">
                                    <i class="fa fa-check-circle"></i> ĐÃ CHẤM: <?= $myScore; ?>/100
                                </span>
                            <?php else: ?>
                                <span class="badge badge-warning text-dark font-weight-bold" style="font-size: 11px; padding: 4px 10px; border-radius: 12px;">
                                    <i class="fa fa-clock-o"></i> CHỜ THẨM ĐỊNH
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center mb-3">
                                    <div style="width: 55px; height: 55px; border-radius: 12px; background: #0A192F; color: #D4AF37; display: flex; align-items: center; justify-content: center; font-size: 22px; font-weight: bold; overflow: hidden; margin-right: 14px; flex-shrink: 0; border: 1px solid #e2e8f0;">
                                        <?php if (!empty($avatar)): ?>
                                            <img src="<?= $avatar; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <i class="fa fa-building"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="overflow-hidden">
                                        <h5 class="font-weight-bold mb-1 text-truncate" style="color: #0A192F; font-size: 16px;">
                                            <?= esc($candidate->name); ?>
                                        </h5>
                                        <small class="text-muted d-block text-truncate">
                                            <i class="fa fa-briefcase text-warning mr-1"></i> <?= esc($candidate->organization_name ?? $candidate->name); ?>
                                        </small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <span class="badge badge-light border text-muted" style="font-size: 11px;">
                                        <i class="fa fa-tag text-primary mr-1"></i> <?= esc($candidate->category_name ?? 'Hạng mục'); ?>
                                    </span>
                                </div>

                                <p class="small text-muted mb-3" style="line-height: 19px; height: 38px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                    <?= esc($candidate->bio_summary ?? 'Hồ sơ đề cử chính thức đã qua vòng sơ tuyển.'); ?>
                                </p>
                            </div>

                            <div class="pt-3 border-top">
                                <a href="<?= langBaseUrl('jury/evaluate/' . $candidate->id); ?>" class="btn btn-block font-weight-bold <?= $isEvaluated ? 'btn-outline-primary' : 'btn-warning text-dark'; ?>" style="border-radius: 8px; font-size: 14px; padding: 9px; <?= !$isEvaluated ? 'background: linear-gradient(135deg, #D4AF37 0%, #AA771C 100%); border: none;' : ''; ?>">
                                    <i class="fa fa-pencil-square-o mr-1"></i> <?= $isEvaluated ? 'Xem Lại & Sửa Điểm' : 'Chấm Điểm Thẩm Định'; ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; else: ?>
                <div class="col-12 text-center py-5">
                    <div class="p-5 bg-white rounded shadow-sm">
                        <i class="fa fa-folder-open-o fa-3x text-muted mb-3"></i>
                        <h5 class="font-weight-bold text-muted">Không có hồ sơ trong danh mục này</h5>
                        <p class="small text-muted mb-3">Hồ sơ ứng viên đang được cập nhật hoặc điều phối.</p>
                        <a href="<?= langBaseUrl('jury'); ?>" class="btn btn-primary btn-sm">Xem tất cả hồ sơ</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
function filterJuryList(){
    const val = document.getElementById("juryCandidateSearch").value.toLowerCase().trim();
    const cards = document.querySelectorAll(".jury-card-item");
    cards.forEach(card => {
        const name = card.getAttribute("data-name") || "", code = card.getAttribute("data-code") || "";
        card.style.display = (name.includes(val) || code.includes(val)) ? "" : "none";
    });
}
</script>
