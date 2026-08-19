<div class="tracker-page-wrapper py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Search Box -->
                <div class="card shadow-sm border-0 mb-4 rounded-lg">
                    <div class="card-body p-4 text-center">
                        <span class="badge bg-primary text-white px-3 py-1 rounded-pill mb-2 font-weight-bold">
                            <i class="fa fa-search mr-1"></i> TRA CỨU HỒ SƠ ĐỀ CỬ
                        </span>
                        <h2 class="font-weight-bold text-dark mb-3">Kiểm Tra Tiến Độ Xét Duyệt Giải Thưởng</h2>
                        <p class="text-muted small mb-4">Nhập mã hồ sơ đề cử (Ví dụ: <code>TBG-2026-ABC123</code>) để xem kết quả thẩm định và các vòng tiếp theo.</p>

                        <form action="<?= base_url('nomination/tracker'); ?>" method="GET" class="form-inline justify-content-center">
                            <div class="input-group input-group-lg w-100 max-w-500">
                                <input type="text" name="code" value="<?= esc($trackingCode ?? ''); ?>" class="form-control font-weight-bold text-uppercase" placeholder="Nhập mã hồ sơ (TBG-...)" required>
                                <div class="input-group-append">
                                    <button class="btn btn-warning font-weight-bold px-4 text-dark" type="submit">
                                        <i class="fa fa-search mr-1"></i> Tra Cứu
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Result Card -->
                <?php if (!empty($trackingResult)): ?>
                    <div class="card shadow border-0 rounded-lg border-top-4 border-warning">
                        <div class="card-body p-4 p-md-5">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                                <div>
                                    <span class="badge badge-warning text-dark font-weight-bold">MÃ HỒ SƠ: <?= esc($trackingResult['tracking_code']); ?></span>
                                    <h3 class="font-weight-bold text-dark mt-2 mb-0"><?= esc($trackingResult['organization_name']); ?></h3>
                                    <?php if (!empty($trackingResult['brand_name'])): ?>
                                        <small class="text-muted">Thương hiệu: <?= esc($trackingResult['brand_name']); ?></small>
                                    <?php endif; ?>
                                </div>
                                <div class="text-right">
                                    <span class="badge badge-success px-3 py-2 font-weight-bold"><?= esc($trackingResult['stage_label']); ?></span>
                                </div>
                            </div>

                            <!-- Progress Stepper -->
                            <h5 class="font-weight-bold text-primary mb-3">Tiến Trình Thẩm Định Qua 4 Vòng</h5>
                            <div class="progress mb-4" style="height: 12px; border-radius: 6px;">
                                <?php 
                                    $step = $trackingResult['stage_order'] ?? 1;
                                    $percent = min(100, $step * 25);
                                ?>
                                <div class="progress-bar bg-warning progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $percent; ?>%;"></div>
                            </div>

                            <div class="row text-center small text-muted">
                                <div class="col-3 font-weight-bold <?= $step >= 1 ? 'text-primary' : ''; ?>">1. Sơ Khảo</div>
                                <div class="col-3 font-weight-bold <?= $step >= 2 ? 'text-primary' : ''; ?>">2. Thẩm Định</div>
                                <div class="col-3 font-weight-bold <?= $step >= 3 ? 'text-primary' : ''; ?>">3. Chung Khảo</div>
                                <div class="col-3 font-weight-bold <?= $step >= 4 ? 'text-success' : ''; ?>">4. Trao Giải</div>
                            </div>

                            <div class="bg-light p-3 rounded mt-4 border">
                                <div class="row small">
                                    <div class="col-md-6"><strong>Ngày nộp:</strong> <?= esc($trackingResult['created_at'] ?? 'Chưa xác định'); ?></div>
                                    <div class="col-md-6 text-md-right"><strong>Cập nhật gần nhất:</strong> <?= esc($trackingResult['updated_at'] ?? 'Đang cập nhật'); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php elseif (!empty($trackingCode)): ?>
                    <div class="alert alert-warning text-center shadow-sm p-4">
                        <i class="fa fa-exclamation-triangle fa-2x mb-2 text-warning"></i>
                        <h5 class="font-weight-bold">Không tìm thấy mã hồ sơ: <?= esc($trackingCode); ?></h5>
                        <p class="mb-0 text-muted">Vui lòng kiểm tra lại chính xác mã số đã được cấp qua email xác nhận khi đăng ký.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
