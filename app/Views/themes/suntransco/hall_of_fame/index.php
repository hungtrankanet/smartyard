<div class="hall-of-fame-wrapper py-5 bg-dark text-white" style="background: radial-gradient(circle at center, #172A45 0%, #0A192F 100%); min-height: 80vh;">
    <div class="container">
        <!-- Hero Header -->
        <div class="text-center mb-5">
            <span class="badge badge-warning text-dark px-3 py-2 rounded-pill font-weight-bold mb-3">
                <i class="fa fa-trophy mr-1"></i> TOP BEST GLOBAL HALL OF FAME
            </span>
            <h1 class="font-weight-bold display-4 text-warning">Bảng Vàng Vinh Danh Quốc Gia</h1>
            <p class="lead text-light max-w-700 mx-auto" style="opacity: 0.85;">
                Tôn vinh và lưu danh những thương hiệu, tổ chức và nhà lãnh đạo xuất sắc nhất Việt Nam — Biểu tượng của sự bứt phá và đóng góp vượt bậc.
            </p>
        </div>

        <!-- Filter Bar -->
        <div class="card bg-secondary text-white border-0 shadow-lg mb-5 rounded-lg" style="background: rgba(255,255,255,0.05) !important; backdrop-filter: blur(10px); border: 1px solid rgba(212,175,55,0.3) !important;">
            <div class="card-body p-3">
                <form action="<?= base_url('hall-of-fame'); ?>" method="GET" class="form-row align-items-center justify-content-center">
                    <div class="col-md-4 col-sm-6 mb-2 mb-md-0">
                        <select name="year" class="form-control bg-dark text-warning border-warning font-weight-bold">
                            <?php for ($y = (int)date('Y'); $y >= 2024; $y--): ?>
                                <option value="<?= $y; ?>" <?= ($selectedYear == $y) ? 'selected' : ''; ?>>Mùa Giải Năm <?= $y; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-5 col-sm-6 mb-2 mb-md-0">
                        <select name="category" class="form-control bg-dark text-light border-secondary">
                            <option value="0">-- Tất Cả Lĩnh Vực / Hạng Mục --</option>
                            <?php if (!empty($categories)): foreach ($categories as $cat): ?>
                                <option value="<?= $cat->id; ?>" <?= ($selectedCategoryId == $cat->id) ? 'selected' : ''; ?>><?= esc($cat->name); ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-12">
                        <button type="submit" class="btn btn-warning btn-block font-weight-bold text-dark">
                            <i class="fa fa-filter mr-1"></i> Lọc Bảng Vàng
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Honorees Grid -->
        <div class="row">
            <?php if (!empty($honorees)): foreach ($honorees as $honoree): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card bg-dark text-white border-0 shadow-lg h-100 rounded-lg overflow-hidden position-relative" style="border: 1px solid rgba(212,175,55,0.4) !important; background: #0f1d32 !important;">
                        <!-- Gold Ribbon Badge -->
                        <div class="position-absolute" style="top: 15px; right: 15px;">
                            <span class="badge badge-warning text-dark font-weight-bold px-2 py-1 shadow">
                                <i class="fa fa-star"></i> VINH DANH
                            </span>
                        </div>

                        <div class="card-body p-4 text-center">
                            <div class="mb-3">
                                <img src="<?= !empty($honoree->avatar) ? base_url($honoree->avatar) : base_url('assets/themes/suntransco/img/trophy_gold.png'); ?>" alt="<?= esc($honoree->organization_name); ?>" class="img-fluid rounded-circle p-2 border border-warning" style="width: 90px; height: 90px; object-fit: cover; background: #172a45;">
                            </div>
                            <h4 class="font-weight-bold text-warning mb-1"><?= esc($honoree->organization_name); ?></h4>
                            <p class="text-light small mb-3" style="opacity: 0.8;"><?= esc($honoree->brand_name ?? $honoree->industry_sector ?? 'Doanh Nghiệp Tiêu Biểu'); ?></p>

                            <div class="p-2 rounded mb-3" style="background: rgba(212,175,55,0.1); border: 1px dashed #D4AF37;">
                                <div class="text-warning font-weight-bold small">ĐIỂM CHUNG CUỘC</div>
                                <div class="h3 font-weight-bold text-white mb-0"><?= number_format($honoree->final_composite_score ?? 95.5, 2); ?> <small class="small">/100</small></div>
                            </div>

                            <p class="text-muted small text-truncate-3 mb-3">
                                <?= esc($honoree->achievements_summary ?? 'Đạt thành tích xuất sắc trong đổi mới sáng tạo, phát triển kinh tế và đóng góp tích cực cho cộng đồng.'); ?>
                            </p>

                            <div class="d-flex justify-content-between mt-auto">
                                <a href="<?= base_url('verify-certificate/' . ($honoree->tracking_code ?? 'TBG-2026-HONOR')); ?>" class="btn btn-outline-warning btn-sm btn-block font-weight-bold">
                                    <i class="fa fa-certificate mr-1"></i> Xem Chứng Nhận Số
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; else: ?>
                <div class="col-12 text-center py-5">
                    <div class="p-5 rounded-lg border border-secondary" style="background: rgba(255,255,255,0.02);">
                        <i class="fa fa-trophy fa-3x text-warning mb-3" style="opacity: 0.5;"></i>
                        <h4 class="text-warning font-weight-bold">Chưa Có Dữ Liệu Bảng Vàng Cho Hạng Mục Này</h4>
                        <p class="text-muted small">Các hồ sơ đạt cúp vàng và chứng nhận vinh danh sẽ được công bố chính thức sau Đêm Gala Trao Giải.</p>
                        <a href="<?= base_url('nomination'); ?>" class="btn btn-warning font-weight-bold text-dark px-4 mt-2">
                            <i class="fa fa-pencil mr-1"></i> Nộp Hồ Sơ Đề Cử Ngay
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
