<!-- Agency Partner Portal -->
<div class="py-5" style="background-color: #F8FAFC;">
    <div class="container">
        
        <!-- Header Banner -->
        <div class="p-5 rounded-lg text-white text-center mb-5" style="background: linear-gradient(135deg, #0A192F 0%, #1A4C96 100%); border-bottom: 3px solid #D9A441; border-radius: 16px;">
            <span class="badge badge-warning px-3 py-1 font-weight-bold mb-3" style="background: #D9A441; color: #0A192F;">
                MẠNG LƯỚI ĐỐI TÁC CHIẾN LƯỢC TOÀN QUỐC
            </span>
            <h1 class="font-serif text-white mb-3" style="font-size: 2.2rem;">Chương Trình Đại Lý Uỷ Quyền TOP BEST</h1>
            <p class="mx-auto" style="max-width: 750px; font-size: 0.95rem; color: #E2E8F0; line-height: 1.6;">
                Đồng hành cùng TOLUCK và Hội Kỷ lục Việt Nam (VietKings) phát triển thị trường, tìm kiếm và hỗ trợ các thương hiệu di sản địa phương vươn tầm thế giới.
            </p>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success shadow-sm p-4 text-center rounded mb-4">
                <i class="fa-solid fa-circle-check fa-3x text-success mb-3"></i>
                <h4 class="font-serif">Đăng Ký Đại Lý Thành Công!</h4>
                <p class="mb-0"><?= session()->getFlashdata('success'); ?></p>
            </div>
        <?php endif; ?>

        <!-- Quyền Lợi & Cơ Chế Hoa Hồng Độc Quyền (Chỉ Tại Trang Này) -->
        <div class="row mb-5">
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm p-4 h-100 text-center" style="border-radius: 14px; background: #ffffff;">
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: #EFF6FF; color: #1A4C96; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center; font-size: 26px;">
                        <i class="fa-solid fa-percent"></i>
                    </div>
                    <h5 class="font-serif text-primary mb-2">Cơ Chế Hoa Hồng Hấp Dẫn</h5>
                    <p class="text-muted small mb-0" style="line-height: 1.6;">
                        Hưởng mức chiết khấu và hoa hồng phát triển hồ sơ trực tiếp từ Ban Tổ Chức theo từng hợp đồng thẩm định được ký kết thành công.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm p-4 h-100 text-center" style="border-radius: 14px; background: #ffffff;">
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: #FEF3C7; color: #B8860B; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center; font-size: 26px;">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>
                    <h5 class="font-serif text-primary mb-2">Đào Tạo & Workshop Toàn Diện</h5>
                    <p class="text-muted small mb-0" style="line-height: 1.6;">
                        Được cung cấp bộ tài liệu thẩm định chuẩn hoá, tham gia các buổi đào tạo chuyên sâu và đồng tổ chức sự kiện tại địa phương.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm p-4 h-100 text-center" style="border-radius: 14px; background: #ffffff;">
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: #DCFCE7; color: #166534; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center; font-size: 26px;">
                        <i class="fa-solid fa-chart-pie"></i>
                    </div>
                    <h5 class="font-serif text-primary mb-2">Portal Quản Lý Minh Bạch</h5>
                    <p class="text-muted small mb-0" style="line-height: 1.6;">
                        Hệ thống Portal dành riêng cho Đại lý theo dõi trạng thái thẩm định hồ sơ, tiến độ ký hợp đồng và báo cáo hoa hồng thời gian thực.
                    </p>
                </div>
            </div>
        </div>

        <!-- Form Đăng Ký Đại Lý -->
        <div class="card border-0 shadow-sm p-4 p-md-5" style="border-radius: 16px; background: #ffffff;" id="dang-ky">
            <h3 class="font-serif text-primary mb-2 text-center" style="font-size: 1.5rem;">Đăng Ký Trở Thành Đại Lý Uỷ Quyền</h3>
            <p class="text-muted small text-center mb-4">Điền thông tin bên dưới, Phòng Phát triển Đối tác TOLUCK sẽ liên hệ gửi hợp đồng đại lý chi tiết.</p>

            <form action="<?= langBaseUrl('dai-ly/dang-ky'); ?>" method="post">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold small">Họ & Tên Cá Nhân / Tên Tổ Chức *</label>
                        <input type="text" name="agency_name" class="form-control" placeholder="Họ và tên" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold small">Số Điện Thoại Liên Hệ *</label>
                        <input type="tel" name="phone" class="form-control" placeholder="0909 xxx xxx" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold small">Email Nhận Hợp Đồng *</label>
                        <input type="email" name="email" class="form-control" placeholder="email@domain.com" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold small">Tỉnh / Thành Phố Phụ Trách Thị Trường *</label>
                        <select name="province" class="form-control" required>
                            <option value="">-- Chọn tỉnh thành --</option>
                            <?php foreach ($provinces as $prov): ?>
                                <option value="<?= esc($prov); ?>"><?= esc($prov); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="font-weight-bold small">Kinh Nghiệm / Năng Lực Phát Triển Thị Trường</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Giới thiệu sơ lược về mạng lưới khách hàng doanh nghiệp hoặc hiệp hội ngành nghề của bạn..."></textarea>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-tbg-cta btn-lg px-5 py-3 font-weight-bold">
                        <i class="fa-solid fa-file-contract mr-2"></i> Gửi Đăng Ký Đại Lý Uỷ Quyền
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
