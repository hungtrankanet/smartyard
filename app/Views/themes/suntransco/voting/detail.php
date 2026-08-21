<?php
$avatar = !empty($candidate->avatar) ? base_url($candidate->avatar) : "";
$juryRaw = number_format((float)($scoreData["jury_score_raw"] ?? 0), 2);
$juryWeighted = number_format((float)($scoreData["jury_score_weighted"] ?? 0), 2);
$publicVotes = number_format((int)($candidate->public_votes_count ?? 0));
$publicNormalized = number_format((float)($scoreData["public_score_normalized"] ?? 0), 2);
$publicWeighted = number_format((float)($scoreData["public_score_weighted"] ?? 0), 2);
$compositeScore = number_format((float)($scoreData["final_composite_score"] ?? 0), 2);
$rank = (int)($scoreData["category_rank"] ?? 0);
?>
<section class="py-5" style="background: linear-gradient(135deg, #0A192F 0%, #0d2342 60%, #1e293b 100%); border-bottom: 3px solid #D4AF37;">
    <div class="container py-3">
        <div class="row align-items-center">
            <div class="col-lg-8 text-white">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent p-0 mb-3" style="font-size: 13px;">
                        <li class="breadcrumb-item"><a href="<?= langBaseUrl(); ?>" class="text-light">Trang Chủ</a></li>
                        <li class="breadcrumb-item"><a href="<?= langBaseUrl("voting"); ?>" class="text-light">Cổng Bình Chọn</a></li>
                        <li class="breadcrumb-item active text-warning" aria-current="page"><?= esc($candidate->name); ?></li>
                    </ol>
                </nav>

                <div class="d-flex align-items-center mb-3">
                    <span class="badge mr-2 px-3 py-1" style="background: #D4AF37; color: #0A192F; font-weight: 800; font-size: 12px; border-radius: 20px;">
                        MÃ: <?= esc($candidate->candidate_code); ?>
                    </span>
                    <span class="badge badge-outline-light px-3 py-1" style="border: 1px solid rgba(255,255,255,0.4); color: #fff; font-size: 12px; border-radius: 20px;">
                        <i class="<?= esc($category->icon ?? "fa fa-award"); ?> text-warning mr-1"></i> <?= esc($category->name ?? "Hạng mục"); ?>
                    </span>
                </div>

                <h1 class="font-weight-bold display-5 mb-2" style="color: #ffffff;">
                    <?= esc($candidate->name); ?>
                </h1>
                <h5 class="mb-3 text-warning font-weight-normal" style="opacity: 0.95;">
                    <i class="fa fa-building-o mr-1"></i> <?= esc($candidate->organization_name ?? $candidate->name); ?>
                </h5>
                <p class="lead mb-4 text-light" style="font-size: 15px; max-width: 650px; opacity: 0.9; line-height: 24px;">
                    <?= esc($candidate->bio_summary ?? "Hồ sơ đề cử chính thức tại Giải Thưởng Quốc Gia TOP BEST GLOBAL 2026."); ?>
                </p>

                <div class="d-flex flex-wrap align-items-center" style="gap: 12px;">
                    <button type="button" class="btn btn-warning font-weight-bold text-dark px-4 py-3" onclick="openVotingModal(<?= (int)$candidate->id; ?>, '<?= esc(addslashes($candidate->name)); ?>', '<?= esc(addslashes($category->name ?? '')); ?>', '<?= esc($avatar); ?>')" style="background: linear-gradient(135deg, #D4AF37 0%, #F3E5AB 50%, #AA771C 100%); border: none; border-radius: 10px; font-size: 15px; letter-spacing: 0.5px; box-shadow: 0 4px 15px rgba(212,175,55,0.4);">
                        <i class="fa fa-check-circle mr-1"></i> BÌNH CHỌN CHO ỨNG VIÊN
                    </button>
                    <a href="<?= langBaseUrl("voting/leaderboard/" . ($category->slug ?? "")); ?>" class="btn btn-outline-light px-4 py-3 font-weight-bold" style="border-radius: 10px; font-size: 14px;">
                        <i class="fa fa-bar-chart mr-1"></i> Xem Bảng Xếp Hạng
                    </a>
                </div>
            </div>

            <div class="col-lg-4 text-center mt-4 mt-lg-0">
                <div class="p-3 bg-white rounded-lg shadow-lg d-inline-block position-relative" style="border-radius: 20px; border: 3px solid #D4AF37; max-width: 280px;">
                    <div style="width: 240px; height: 240px; border-radius: 14px; overflow: hidden; background: #f8fafc; display: flex; align-items: center; justify-content: center;">
                        <?php if (!empty($avatar)): ?>
                            <img src="<?= $avatar; ?>" alt="<?= esc($candidate->name); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <i class="fa fa-building fa-5x text-warning"></i>
                        <?php endif; ?>
                    </div>
                    <?php if ($rank > 0): ?>
                        <div class="position-absolute" style="bottom: -15px; left: 50%; transform: translateX(-50%); background: #0A192F; color: #D4AF37; border: 2px solid #D4AF37; border-radius: 20px; padding: 4px 16px; font-weight: 800; font-size: 13px; white-space: nowrap;">
                            🏆 HẠNG #<?= $rank; ?> TOÀN HẠNG MỤC
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-4" style="background: #f1f5f9; border-bottom: 1px solid #e2e8f0;">
    <div class="container">
        <div class="row">
            <div class="col-6 col-md-3 mb-3 mb-md-0">
                <div class="p-3 bg-white rounded-lg shadow-sm h-100 border-left border-warning" style="border-left-width: 4px !important; border-radius: 10px;">
                    <div class="small text-muted font-weight-bold text-uppercase">Bình Chọn Độc Giả</div>
                    <div class="h3 font-weight-bold mb-0 text-primary" id="tbgVoteCounter_<?= $candidate->id; ?>">
                        <?= $publicVotes; ?>
                    </div>
                    <small class="text-muted"><i class="fa fa-users"></i> Phiếu hợp lệ đã kiểm toán</small>
                </div>
            </div>

            <div class="col-6 col-md-3 mb-3 mb-md-0">
                <div class="p-3 bg-white rounded-lg shadow-sm h-100 border-left border-info" style="border-left-width: 4px !important; border-radius: 10px;">
                    <div class="small text-muted font-weight-bold text-uppercase">Điểm Giám Khảo (70%)</div>
                    <div class="h3 font-weight-bold mb-0 text-dark">
                        <?= $juryRaw; ?> <span style="font-size: 14px; font-weight: normal; color: #64748b;">/ 100</span>
                    </div>
                    <small class="text-muted"><i class="fa fa-gavel"></i> Đóng góp: +<?= $juryWeighted; ?>đ</small>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="p-3 bg-white rounded-lg shadow-sm h-100 border-left border-success" style="border-left-width: 4px !important; border-radius: 10px;">
                    <div class="small text-muted font-weight-bold text-uppercase">Điểm Độc Giả (30%)</div>
                    <div class="h3 font-weight-bold mb-0 text-success">
                        <?= $publicNormalized; ?>%
                    </div>
                    <small class="text-muted"><i class="fa fa-percent"></i> Đóng góp: +<?= $publicWeighted; ?>đ</small>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="p-3 rounded-lg shadow-sm h-100" style="background: linear-gradient(135deg, #0A192F 0%, #1e3a8a 100%); color: #fff; border-radius: 10px; border: 1px solid #D4AF37;">
                    <div class="small font-weight-bold text-uppercase" style="color: #D4AF37;">Điểm Tổng Hợp 70/30</div>
                    <div class="h3 font-weight-bold mb-0" style="color: #F3E5AB;">
                        <?= $compositeScore; ?> <span style="font-size: 14px; font-weight: normal; color: #fff;">/ 100</span>
                    </div>
                    <small style="color: #cbd5e1;"><i class="fa fa-trophy text-warning"></i> Xếp hạng: <strong>#<?= $rank; ?></strong></small>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5" style="background: #ffffff;">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="p-4 mb-4 rounded-lg" style="background: #faf8f5; border: 1px solid #f0e6d2; border-radius: 14px;">
                    <h5 class="font-weight-bold mb-3" style="color: #0A192F;">
                        <i class="fa fa-sliders text-warning mr-1"></i> Cơ Chế Tính Điểm Kết Hợp 70/30
                    </h5>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small font-weight-bold mb-1">
                            <span>Điểm Hội Đồng Giám Khảo Chuyên Môn (70%)</span>
                            <span class="text-primary"><?= $juryWeighted; ?> / 70.00</span>
                        </div>
                        <div class="progress" style="height: 10px; border-radius: 6px; background: #e2e8f0;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: <?= min(100, ((float)$scoreData["jury_score_raw"] ?? 0)); ?>%;"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small font-weight-bold mb-1">
                            <span>Điểm Bình Chọn Độc Giả Chuẩn Hóa (30%)</span>
                            <span class="text-success"><?= $publicWeighted; ?> / 30.00</span>
                        </div>
                        <div class="progress" style="height: 10px; border-radius: 6px; background: #e2e8f0;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?= min(100, ((float)$scoreData["public_score_normalized"] ?? 0)); ?>%;"></div>
                        </div>
                    </div>
                    <div class="small text-muted">
                        <em>* Dữ liệu điểm số và xếp hạng được tự động cập nhật và kiểm toán theo thời gian thực (Real-time Audit Trail).</em>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; border: 1px solid #e2e8f0;">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="font-weight-bold mb-0" style="color: #0A192F;">
                            <i class="fa fa-file-text-o text-warning mr-2"></i> Hồ Sơ Đề Cử & Năng Lực Doanh Nghiệp
                        </h5>
                    </div>
                    <div class="card-body p-4" style="line-height: 26px; color: #334155; font-size: 15px;">
                        <?php if (!empty($candidate->dossier_content)): ?>
                            <?= $candidate->dossier_content; ?>
                        <?php else: ?>
                            <p><strong>Doanh nghiệp / Đơn vị:</strong> <?= esc($candidate->organization_name ?? $candidate->name); ?></p>
                            <p><strong>Mã định danh hồ sơ:</strong> <?= esc($candidate->candidate_code); ?></p>
                            <?php if (!empty($candidate->tax_code)): ?>
                                <p><strong>Mã số thuế:</strong> <?= esc($candidate->tax_code); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($candidate->website)): ?>
                                <p><strong>Website chính thức:</strong> <a href="<?= esc($candidate->website); ?>" target="_blank" rel="nofollow"><?= esc($candidate->website); ?></a></p>
                            <?php endif; ?>
                            <hr>
                            <h6><strong>Tóm tắt thành tích tiêu biểu:</strong></h6>
                            <p><?= nl2br(esc($candidate->bio_summary ?? "Đơn vị tiêu biểu với nhiều đóng góp tích cực cho ngành và sự phát triển kinh tế xã hội quốc gia.")); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4 text-center p-4" style="border-radius: 14px; background: linear-gradient(135deg, #0A192F 0%, #1e293b 100%); border: 1px solid #D4AF37;">
                    <i class="fa fa-trophy fa-3x mb-3 text-warning"></i>
                    <h5 class="text-white font-weight-bold mb-2">Ủng Hộ Ứng Viên</h5>
                    <p class="small text-light mb-3" style="opacity: 0.85;">
                        Mỗi lá phiếu của bạn góp phần vinh danh những giá trị xuất sắc nhất Việt Nam.
                    </p>
                    <button type="button" class="btn btn-warning font-weight-bold text-dark btn-block py-3" onclick="openVotingModal(<?= (int)$candidate->id; ?>, '<?= esc(addslashes($candidate->name)); ?>', '<?= esc(addslashes($category->name ?? '')); ?>', '<?= esc($avatar); ?>')" style="background: linear-gradient(135deg, #D4AF37 0%, #AA771C 100%); border: none; border-radius: 8px;">
                        <i class="fa fa-check-circle mr-1"></i> BÌNH CHỌN NGAY
                    </button>
                </div>

                <?php if (!empty($rivals)): ?>
                    <div class="card border-0 shadow-sm" style="border-radius: 14px; border: 1px solid #e2e8f0;">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="font-weight-bold mb-0 text-dark">
                                <i class="fa fa-users text-warning mr-1"></i> Ứng Viên Cùng Hạng Mục
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                <?php foreach ($rivals as $rival): if ($rival->id == $candidate->id) continue; ?>
                                    <li class="list-group-item d-flex align-items-center justify-content-between p-3">
                                        <div class="d-flex align-items-center overflow-hidden mr-2">
                                            <div style="width: 36px; height: 36px; border-radius: 8px; background: #0A192F; display: flex; align-items: center; justify-content: center; color: #D4AF37; font-size: 14px; font-weight: bold; flex-shrink: 0; margin-right: 10px;">
                                                <i class="fa fa-award"></i>
                                            </div>
                                            <div class="text-truncate">
                                                <a href="<?= langBaseUrl("voting/candidate/" . $rival->slug); ?>" class="font-weight-bold text-dark small text-truncate d-block">
                                                    <?= esc($rival->name); ?>
                                                </a>
                                                <span class="small text-muted"><?= number_format((int)($rival->public_votes_count ?? 0)); ?> phiếu</span>
                                            </div>
                                        </div>
                                        <a href="<?= langBaseUrl("voting/candidate/" . $rival->slug); ?>" class="btn btn-sm btn-outline-primary" style="border-radius: 6px; font-size: 11px; padding: 3px 8px;">
                                            Xem
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?= loadView("voting/partials/_otp_modal"); ?>
