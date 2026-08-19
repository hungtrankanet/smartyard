<div class="verify-certificate-wrapper py-5 bg-dark text-white" style="background: radial-gradient(circle at center, #172A45 0%, #0A192F 100%); min-height: 85vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Verification Card -->
                <div class="card shadow-lg border-0 rounded-lg overflow-hidden" style="background: #0f1d32; border: 2px solid #D4AF37 !important;">
                    <div class="card-header text-center py-4" style="background: linear-gradient(135deg, #172A45 0%, #0A192F 100%); border-bottom: 1px solid rgba(212,175,55,0.4);">
                        <span class="badge badge-warning text-dark font-weight-bold px-3 py-1 mb-2">
                            <i class="fa fa-shield mr-1"></i> XÁC THỰC CHỨNG NHẬN ĐIỆN TỬ
                        </span>
                        <h2 class="text-warning font-weight-bold mb-0">Hệ Thống Xác Thực Bảng Vàng Quốc Gia</h2>
                        <small class="text-light">TOP BEST GLOBAL National Digital Certificate Registry</small>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <?php if (!empty($verification['valid'])): ?>
                            <div class="alert alert-success bg-transparent border-success text-success text-center py-3 mb-4 font-weight-bold">
                                <i class="fa fa-check-circle fa-2x d-block mb-1"></i>
                                CHỨNG NHẬN VINH DANH HỢP LỆ & CHÍNH HÃNG
                            </div>

                            <div class="certificate-details bg-dark p-4 rounded mb-4" style="border: 1px solid rgba(212,175,55,0.2);">
                                <div class="row mb-3">
                                    <div class="col-sm-4 text-muted">Đơn vị vinh danh:</div>
                                    <div class="col-sm-8 font-weight-bold text-white h5"><?= esc($verification['organization_name']); ?></div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4 text-muted">Danh hiệu / Giải thưởng:</div>
                                    <div class="col-sm-8 font-weight-bold text-warning"><?= esc($verification['award_title']); ?></div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4 text-muted">Hạng mục / Lĩnh vực:</div>
                                    <div class="col-sm-8 text-light"><?= esc($verification['category_name'] ?? 'Kinh Tế & Doanh Nghiệp Tiêu Biểu'); ?></div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4 text-muted">Mã chứng nhận (Serial):</div>
                                    <div class="col-sm-8 font-weight-bold text-warning"><code><?= esc($verification['serial_code']); ?></code></div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4 text-muted">Ngày cấp chứng nhận:</div>
                                    <div class="col-sm-8 text-light"><?= esc($verification['issued_date'] ?? date('d/m/Y')); ?></div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4 text-muted">Hội đồng thẩm định:</div>
                                    <div class="col-sm-8 text-light"><?= esc($verification['council_president'] ?? 'Hội Đồng Thẩm Định TOP BEST GLOBAL'); ?></div>
                                </div>
                            </div>

                            <!-- Embed Code Section -->
                            <div class="mt-4">
                                <h5 class="text-warning font-weight-bold mb-2">
                                    <i class="fa fa-code mr-1"></i> Mã Nhúng Huy Hiệu Số Hóa (Digital Badge) Vào Website
                                </h5>
                                <p class="text-muted small">Doanh nghiệp có thể nhúng đoạn mã HTML này vào chân trang hoặc website để đối tác tra cứu xác thực trực tiếp:</p>
                                <div class="bg-dark p-3 rounded border border-secondary position-relative">
                                    <code class="text-light small text-break"><?= esc($embedSnippet ?? ''); ?></code>
                                </div>
                            </div>

                        <?php else: ?>
                            <div class="alert alert-danger bg-transparent border-danger text-danger text-center py-4 font-weight-bold">
                                <i class="fa fa-times-circle fa-3x d-block mb-2"></i>
                                <?= esc($verification['message'] ?? 'Mã chứng nhận không hợp lệ hoặc chưa được kích hoạt trên hệ thống.'); ?>
                            </div>
                        <?php endif; ?>

                        <div class="text-center mt-4 pt-3 border-top border-secondary">
                            <a href="<?= base_url('hall-of-fame'); ?>" class="btn btn-outline-warning font-weight-bold px-4">
                                <i class="fa fa-arrow-left mr-1"></i> Về Bảng Vàng Vinh Danh
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
