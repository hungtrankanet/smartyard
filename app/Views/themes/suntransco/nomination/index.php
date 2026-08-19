<div class="nomination-page-wrapper py-5 bg-light">
    <div class="container">
        <!-- Hero Header -->
        <div class="text-center mb-5">
            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill font-weight-bold mb-3">
                <i class="fa fa-trophy mr-1"></i> TOP BEST GLOBAL AWARDS 2026
            </span>
            <h1 class="font-weight-bold text-dark display-4">Cổng Đề Cử Giải Thưởng Quốc Gia</h1>
            <p class="lead text-muted max-w-700 mx-auto">
                Tôn vinh các doanh nghiệp tiên phong, sản phẩm dịch vụ xuất sắc và những nhà lãnh đạo có đóng góp nổi bật trong tiến trình phát triển kinh tế & xã hội Việt Nam.
            </p>
        </div>

        <!-- 4-Stage Timeline Indicator -->
        <div class="card shadow-sm border-0 mb-5 rounded-lg">
            <div class="card-body p-4">
                <h5 class="font-weight-bold text-center mb-4 text-primary">Quy Trình Xét Duyệt & Vinh Danh 4 Vòng Chuẩn Hóa</h5>
                <div class="row text-center">
                    <div class="col-md-3 col-6 mb-3">
                        <div class="p-3 bg-light rounded border-primary border-left-4">
                            <div class="badge badge-primary badge-pill mb-2">Vòng 1</div>
                            <h6 class="font-weight-bold mb-1">Sơ Khảo Hồ Sơ</h6>
                            <small class="text-muted">Kiểm tra tính hợp lệ & pháp lý</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="p-3 bg-light rounded border-info border-left-4">
                            <div class="badge badge-info badge-pill mb-2">Vòng 2</div>
                            <h6 class="font-weight-bold mb-1">Thẩm Định Chuyên Gia</h6>
                            <small class="text-muted">Hội đồng chấm Rubric 100 điểm</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="p-3 bg-light rounded border-warning border-left-4">
                            <div class="badge badge-warning badge-pill mb-2">Vòng 3</div>
                            <h6 class="font-weight-bold mb-1">Chung Khảo & Bình Chọn</h6>
                            <small class="text-muted">70% Giám khảo + 30% Độc giả</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="p-3 bg-light rounded border-success border-left-4">
                            <div class="badge badge-success badge-pill mb-2">Vòng 4</div>
                            <h6 class="font-weight-bold mb-1">Gala Vinh Danh</h6>
                            <small class="text-muted">Trao Cúp & Bảng Vàng Điện Tử</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nomination Form Wizard -->
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow border-0 rounded-lg">
                    <div class="card-header bg-dark text-white p-4">
                        <h4 class="mb-0 font-weight-bold text-warning">
                            <i class="fa fa-pencil-square-o mr-2"></i> Phiếu Đăng Ký Hồ Sơ Đề Cử Trực Tuyến
                        </h4>
                        <small class="text-light">Vui lòng nhập thông tin chính xác. Mã tra cứu hồ sơ sẽ được cấp ngay sau khi gửi.</small>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <form id="formNomination" action="<?= base_url('nomination/apply-post'); ?>" method="POST">
                            <?= csrf_field(); ?>

                            <h5 class="text-primary font-weight-bold border-bottom pb-2 mb-3">1. Thông Tin Đơn Vị / Cá Nhân Đề Cử</h5>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label class="font-weight-bold">Tên Doanh Nghiệp / Tổ Chức <span class="text-danger">*</span></label>
                                    <input type="text" name="organization_name" class="form-control" placeholder="VD: TẬP ĐOÀN CÔNG NGHỆ ABC" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="font-weight-bold">Tên Thương Hiệu / Nhãn Hiệu</label>
                                    <input type="text" name="brand_name" class="form-control" placeholder="VD: ABC GLOBAL TECH">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label class="font-weight-bold">Mã Số Thuế (Doanh Nghiệp) <span class="text-danger">*</span></label>
                                    <input type="text" name="tax_code" class="form-control" placeholder="10 hoặc 13 chữ số" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="font-weight-bold">Lĩnh Vực / Ngành Nghề <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-control" required>
                                        <option value="">-- Chọn Hạng Mục Giải Thưởng --</option>
                                        <?php if (!empty($categories)): foreach ($categories as $cat): ?>
                                            <option value="<?= $cat->id; ?>"><?= esc($cat->name); ?></option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                </div>
                            </div>

                            <h5 class="text-primary font-weight-bold border-bottom pb-2 mb-3 mt-4">2. Thông Tin Người Đại Diện & Liên Hệ</h5>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label class="font-weight-bold">Họ & Tên Người Đại Diện</label>
                                    <input type="text" name="representative" class="form-control" placeholder="Họ tên đại diện pháp luật / liên hệ">
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="font-weight-bold">Email Nhận Thông Báo <span class="text-danger">*</span></label>
                                    <input type="email" name="contact_email" class="form-control" placeholder="email@domain.com" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="font-weight-bold">Số Điện Thoại Liên Hệ <span class="text-danger">*</span></label>
                                    <input type="tel" name="contact_phone" class="form-control" placeholder="0901234567" required>
                                </div>
                            </div>

                            <h5 class="text-primary font-weight-bold border-bottom pb-2 mb-3 mt-4">3. Tóm Tắt Thành Tựu & Hồ Sơ Năng Lực</h5>
                            <div class="form-group">
                                <label class="font-weight-bold">Tóm tắt các thành tựu nổi bật, đổi mới sáng tạo hoặc đóng góp xã hội:</label>
                                <textarea name="achievements_summary" class="form-control" rows="4" placeholder="Mô tả ngắn gọn về sản phẩm, dịch vụ, doanh thu tăng trưởng hoặc các giải thưởng, chứng nhận đã đạt được..."></textarea>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-warning btn-lg font-weight-bold px-5 text-dark shadow">
                                    <i class="fa fa-paper-plane mr-2"></i> Gửi Hồ Sơ Đề Cử
                                </button>
                                <div class="mt-2 text-muted small">
                                    Đã có mã hồ sơ? <a href="<?= base_url('nomination/tracker'); ?>" class="font-weight-bold text-primary">Tra cứu tiến độ xét duyệt tại đây</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
