<!-- Badge Verification & Anti-Counterfeiting Portal -->
<div class="py-5" style="background-color: #F8FAFC; min-height: 80vh;">
    <div class="container" style="max-width: 750px;">
        
        <!-- Header -->
        <div class="text-center mb-4">
            <div style="width: 60px; height: 60px; border-radius: 50%; background: #EFF6FF; border: 2px solid #1A4C96; color: #1A4C96; margin: 0 auto 12px; display: flex; align-items: center; justify-content: center; font-size: 26px;">
                <i class="fa-solid fa-shield-halved text-warning"></i>
            </div>
            <h1 class="font-serif text-primary mb-2" style="font-size: 1.85rem;">Cổng Xác Minh Huy Hiệu Chính Hãng</h1>
            <p class="text-muted small">
                Hệ thống xác thực nguồn gốc bảo chứng bởi <strong>Hội Kỷ lục Việt Nam (VietKings)</strong> & <strong>WORLDKINGS</strong>.
            </p>
        </div>

        <!-- Search / Verification Box -->
        <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 16px; background: #ffffff;">
            <form action="<?= langBaseUrl('verify'); ?>" method="get">
                <div class="form-group mb-3">
                    <label class="font-weight-bold small text-primary">Nhập Mã Định Danh Hồ Sơ (Profile Code):</label>
                    <div class="input-group">
                        <input type="text" name="code" class="form-control form-control-lg" placeholder="Ví dụ: TBG-VN-2026-001" value="<?= esc($searchCode ?? ''); ?>" style="border-radius: 8px 0 0 8px; font-size: 0.95rem;" required>
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-warning px-4 font-weight-bold" style="background: #D9A441; border: none; color: #0A192F;">
                                <i class="fa-solid fa-check-double mr-1"></i> Xác Minh
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="text-center pt-3 border-top">
                <button type="button" class="btn btn-outline-primary btn-sm font-weight-bold" onclick="alert('Đang mở máy ảnh quét mã QR từ bao bì/POSM...');" style="border-radius: 20px;">
                    <i class="fa-solid fa-camera mr-1"></i> Bật Camera Quét Mã QR Trực Tiếp
                </button>
            </div>
        </div>

        <!-- Verification Result -->
        <?php if ($searched): ?>
            <?php if (!empty($profile)): ?>
                <!-- Certified Genuine Badge Card -->
                <div class="card card-best border-0 shadow p-4 text-center mb-4" style="border-radius: 16px; background: #ffffff; border: 2.5px solid #16A34A !important;">
                    <div class="mb-3">
                        <span class="badge badge-success px-3 py-2 font-weight-bold" style="font-size: 0.85rem; background: #16A34A;">
                            <i class="fa-solid fa-circle-check mr-1"></i> CHỨNG NHẬN CHÍNH HÃNG HỢP LỆ
                        </span>
                    </div>

                    <h3 class="font-serif text-primary mb-2" style="font-size: 1.35rem;">
                        <?= esc($profile['title_formula']); ?>
                    </h3>
                    <p class="text-muted small mb-3">Mã hồ sơ: <strong><?= esc($profile['code']); ?></strong> • Tỉnh thành: <strong><?= esc($profile['province']); ?></strong></p>

                    <div class="p-3 bg-light rounded text-left mb-3 small" style="line-height: 1.8;">
                        <div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                            <span>Phân Hạng Danh Hiệu:</span>
                            <span class="font-weight-bold text-primary"><?= $profile['rank_tier']; ?> (Hạng #<?= $profile['rank_number']; ?>)</span>
                        </div>
                        <div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                            <span>Loại Huy Hiệu:</span>
                            <span class="font-weight-bold text-warning">HUY HIỆU <?= strtoupper($profile['badge_type']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                            <span>Chu Kỳ Thẩm Định:</span>
                            <span class="font-weight-bold">6 Tháng / Lần</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Thời Hạn Hiệu Lực:</span>
                            <span class="font-weight-bold text-success">Đến ngày <?= esc($profile['valid_until']); ?></span>
                        </div>
                    </div>

                    <div>
                        <a href="<?= langBaseUrl('ho-so/' . $profile['code']); ?>" class="btn btn-primary btn-block font-weight-bold" style="background: #1A4C96; border-radius: 8px;">
                            Xem Hồ Sơ Chi Tiết & Video Thẩm Định <i class="fa-solid fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Not Found Alert -->
                <div class="card border-0 shadow-sm p-4 text-center mb-4" style="border-radius: 16px; background: #FFF1F2; border: 2px solid #E11D48 !important;">
                    <div class="mb-2 text-danger">
                        <i class="fa-solid fa-triangle-exclamation fa-3x"></i>
                    </div>
                    <h5 class="font-serif text-danger mb-2">Không Tìm Thấy Hồ Sơ Hoặc Mã Không Hợp Lệ!</h5>
                    <p class="small text-muted mb-0">
                        Mã định danh <code><?= esc($searchCode); ?></code> không khớp với bất kỳ chứng nhận nào trên hệ thống Bảng vàng TOP BEST GLOBAL. Vui lòng kiểm tra lại ký tự hoặc liên hệ Ban Thư ký để được hỗ trợ.
                    </p>
                </div>
            <?php endif; ?>
        <?php endif; ?>

    </div>
</div>
