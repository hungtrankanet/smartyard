<!-- TOP BEST GLOBAL - Category Detail View -->
<section class="py-5" style="background: linear-gradient(135deg, #0A192F 0%, #0d2342 60%, #1e293b 100%); border-bottom: 3px solid #D4AF37;">
    <div class="container py-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-3" style="font-size: 13px;">
                <li class="breadcrumb-item"><a href="<?= langBaseUrl(); ?>" class="text-light">Trang Chủ</a></li>
                <li class="breadcrumb-item"><a href="<?= langBaseUrl('honors/categories'); ?>" class="text-light">Danh Mục Giải Thưởng</a></li>
                <li class="breadcrumb-item active text-warning" aria-current="page"><?= esc($category->name); ?></li>
            </ol>
        </nav>
        <div class="row align-items-center">
            <div class="col-lg-8 text-white">
                <span class="badge px-3 py-1 mb-2" style="background: #D4AF37; color: #0A192F; font-weight: 800; font-size: 12px; border-radius: 20px;">
                    LĨNH VỰC: <?= esc($category->industry_sector ?? 'Quốc Gia'); ?>
                </span>
                <h1 class="font-weight-bold display-5 mb-2" style="color: #ffffff;">
                    <?= esc($category->name); ?>
                </h1>
                <p class="lead text-light mb-4" style="font-size: 15px; opacity: 0.9; line-height: 24px;">
                    <?= esc($category->description ?? 'Giải thưởng tôn vinh các thành tựu vượt bậc trong đổi mới sáng tạo và chuyển đổi số.'); ?>
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="<?= langBaseUrl('voting/category/' . $category->slug); ?>" class="btn btn-warning font-weight-bold px-4 py-2 mr-3" style="background: linear-gradient(135deg, #D4AF37 0%, #AA771C 100%); color: #0A192F; border: none; border-radius: 8px;">
                        <i class="fa fa-check-circle mr-1"></i> BÌNH CHỌN TRONG HẠNG MỤC NÀY
                    </a>
                    <a href="<?= langBaseUrl('voting/leaderboard/' . $category->slug); ?>" class="btn btn-outline-light px-4 py-2 font-weight-bold" style="border-radius: 8px;">
                        <i class="fa fa-bar-chart mr-1"></i> Xem Bảng Xếp Hạng
                    </a>
                </div>
            </div>
            <div class="col-lg-4 text-center mt-4 mt-lg-0">
                <div class="p-4 bg-white rounded-lg shadow" style="border-radius: 16px; border: 2px solid #D4AF37;">
                    <div class="small text-muted font-weight-bold text-uppercase mb-2">Tỷ Trọng Điểm Chuẩn Hóa</div>
                    <div class="h3 font-weight-bold text-primary mb-1"><?= $category->jury_weight ?? 70; ?>% Giám Khảo</div>
                    <div class="h4 font-weight-bold text-success mb-2"><?= $category->public_weight ?? 30; ?>% Độc Giả</div>
                    <small class="text-muted">Bộ tiêu chí 4 chiều 100 điểm</small>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5" style="background: #f8fafc;">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
            <h4 class="font-weight-bold mb-0" style="color: #0A192F;">
                <i class="fa fa-users text-warning mr-2"></i> Danh Sách Ứng Viên Trong Hạng Mục
            </h4>
            <span class="badge badge-primary px-3 py-1 font-weight-bold"><?= count($candidates ?? []); ?> Đề cử</span>
        </div>

        <div class="row">
            <?php if (!empty($candidates)): foreach ($candidates as $cand): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 14px; border: 1px solid #e2e8f0;">
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center mb-3">
                                    <div style="width: 50px; height: 50px; border-radius: 10px; background: #0A192F; color: #D4AF37; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: bold; margin-right: 12px; flex-shrink: 0; overflow: hidden;">
                                        <?php if (!empty($cand->avatar)): ?>
                                            <img src="<?= base_url($cand->avatar); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <i class="fa fa-building"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="overflow-hidden">
                                        <h6 class="font-weight-bold mb-0 text-truncate">
                                            <a href="<?= langBaseUrl('voting/candidate/' . $cand->slug); ?>" class="text-dark">
                                                <?= esc($cand->name); ?>
                                            </a>
                                        </h6>
                                        <small class="text-muted text-truncate d-block"><?= esc($cand->organization_name ?? $cand->name); ?></small>
                                    </div>
                                </div>
                                <p class="small text-muted mb-3" style="line-height: 18px; height: 36px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                    <?= esc($cand->bio_summary ?? 'Hồ sơ ứng viên xuất sắc tham gia xét duyệt cúp vàng 2026.'); ?>
                                </p>
                            </div>
                            <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                                <span class="small text-muted"><i class="fa fa-heart text-danger"></i> <?= number_format((int)($cand->public_votes_count ?? 0)); ?> bình chọn</span>
                                <a href="<?= langBaseUrl('voting/candidate/' . $cand->slug); ?>" class="btn btn-sm btn-primary font-weight-bold" style="border-radius: 6px;">
                                    Chi tiết & Bình chọn
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; else: ?>
                <div class="col-12 text-center py-5">
                    <div class="p-5 bg-white rounded shadow-sm">
                        <i class="fa fa-folder-open-o fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted font-weight-bold">Đang cập nhật danh sách ứng viên</h5>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
