<!-- Enterprise Registration Multi-Step Wizard -->
<div class="py-5" style="background-color: #F8FAFC;">
    <div class="container" style="max-width: 850px;">
        
        <!-- Header -->
        <div class="text-center mb-4">
            <span class="badge badge-warning px-3 py-1 font-weight-bold mb-2" style="background: #D9A441; color: #0A192F;">
                QUY TRÌNH THẨM ĐỊNH 5 BƯỚC MINH BẠCH
            </span>
            <h1 class="font-serif text-primary mb-2" style="font-size: 2rem;">Đăng Ký Đề Cử TOP BEST GLOBAL</h1>
            <p class="text-muted" style="font-size: 0.9rem;">
                Dành cho Doanh nghiệp, Hợp tác xã và Nghệ nhân tiêu biểu. <strong>Không thu tiền ở bước gửi hồ sơ này.</strong>
            </p>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success shadow-sm p-4 text-center rounded mb-4">
                <i class="fa-solid fa-circle-check fa-3x text-success mb-3"></i>
                <h4 class="font-serif">Gửi Yêu Cầu Tư Vấn Thành Công!</h4>
                <p class="mb-0"><?= session()->getFlashdata('success'); ?></p>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger rounded mb-4">
                <?= session()->getFlashdata('error'); ?>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm p-4 p-md-5" style="border-radius: 16px; background: #ffffff;">
            <form action="<?= langBaseUrl('doanh-nghiep/gui-dang-ky'); ?>" method="post" id="regForm">
                
                <!-- BƯỚC 1: Chọn Cấp Độ -->
                <div class="mb-4 pb-4 border-bottom">
                    <h5 class="font-serif text-primary mb-3">
                        <span class="badge badge-primary rounded-circle mr-1" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.75rem;">1</span>
                        Chọn Cấp Độ Đăng Ký Đề Cử
                    </h5>
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="card p-3 border text-center cursor-pointer level-card active h-100" style="border-radius: 10px; cursor: pointer; border-color: #1A4C96 !important; background: #EFF6FF;">
                                <input type="radio" name="registration_level" value="brand" checked class="d-none">
                                <i class="fa-solid fa-crown text-warning fa-2x mb-2"></i>
                                <h6 class="font-weight-bold mb-1">Thương Hiệu</h6>
                                <small class="text-muted">Tôn vinh tên tuổi và uy tín pháp nhân</small>
                            </label>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="card p-3 border text-center cursor-pointer level-card h-100" style="border-radius: 10px; cursor: pointer;">
                                <input type="radio" name="registration_level" value="service" class="d-none">
                                <i class="fa-solid fa-bell-concierge text-primary fa-2x mb-2"></i>
                                <h6 class="font-weight-bold mb-1">Dịch Vụ</h6>
                                <small class="text-muted">Tôn vinh chất lượng phục vụ & trải nghiệm</small>
                            </label>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="card p-3 border text-center cursor-pointer level-card h-100" style="border-radius: 10px; cursor: pointer;">
                                <input type="radio" name="registration_level" value="product" class="d-none">
                                <i class="fa-solid fa-box-open text-success fa-2x mb-2"></i>
                                <h6 class="font-weight-bold mb-1">Sản Phẩm</h6>
                                <small class="text-muted">Tôn vinh chất lượng hiện vật & xuất khẩu</small>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- BƯỚC 2: Thông Tin Cơ Bản -->
                <div class="mb-4 pb-4 border-bottom">
                    <h5 class="font-serif text-primary mb-3">
                        <span class="badge badge-primary rounded-circle mr-1" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.75rem;">2</span>
                        Thông Tin Đơn Vị & Người Đại Diện
                    </h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold small">Tên Doanh Nghiệp / Hợp Tác Xã / Chủ Thể *</label>
                            <input type="text" name="company_name" id="inputCompanyName" class="form-control" placeholder="VD: Trầm Hương Khánh Hòa" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold small">Mã Số Thuế / Giấy Phép ĐKKD</label>
                            <input type="text" name="tax_code" class="form-control" placeholder="VD: 0102030405">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="font-weight-bold small">Họ & Tên Người Đại Diện *</label>
                            <input type="text" name="rep_name" class="form-control" placeholder="Họ và tên" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="font-weight-bold small">Số Điện Thoại Liên Hệ *</label>
                            <input type="tel" name="phone" class="form-control" placeholder="0909 xxx xxx" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="font-weight-bold small">Email Nhận Thông Báo *</label>
                            <input type="email" name="email" class="form-control" placeholder="email@domain.com" required>
                        </div>
                    </div>
                </div>

                <!-- BƯỚC 3: Chọn Ngành & Địa Phương + Auto Suggest Công Thức -->
                <div class="mb-4 pb-4 border-bottom">
                    <h5 class="font-serif text-primary mb-3">
                        <span class="badge badge-primary rounded-circle mr-1" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.75rem;">3</span>
                        Lĩnh Vực & Địa Phương Đăng Ký
                    </h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold small">Nhóm Ngành *</label>
                            <select name="industry" id="selectIndustry" class="form-control" required>
                                <option value="">-- Chọn ngành --</option>
                                <?php foreach ($industries as $ind): ?>
                                    <option value="<?= esc($ind['name']); ?>"><?= esc($ind['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold small">Tỉnh / Thành Phố *</label>
                            <select name="province" id="selectProvince" class="form-control" required>
                                <option value="">-- Chọn tỉnh thành --</option>
                                <?php foreach ($provinces as $prov): ?>
                                    <option value="<?= esc($prov); ?>"><?= esc($prov); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Auto-Suggested Title Formula -->
                    <div class="p-3 rounded bg-light border">
                        <label class="font-weight-bold small text-primary d-block mb-1">
                            <i class="fa-solid fa-wand-magic-sparkles text-warning mr-1"></i> Tên Hồ Sơ Gợi Ý Theo Công Thức Chuẩn:
                        </label>
                        <input type="text" name="formula_title" id="inputFormulaTitle" class="form-control font-weight-bold text-primary" readonly value="TOP [Tên sản phẩm/thương hiệu] trong [Ngành] tại [Địa phương]">
                        <small class="text-muted mt-1 d-block">Hệ thống tự động đồng bộ theo công thức: <code>TOP + [Tên] trong [Ngành] tại [Địa phương]</code>.</small>
                    </div>
                </div>

                <!-- BƯỚC 4: Mức Phí Minh Bạch & Cam Kết Hoàn Phí -->
                <div class="mb-4 pb-4 border-bottom">
                    <h5 class="font-serif text-primary mb-3">
                        <span class="badge badge-primary rounded-circle mr-1" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.75rem;">4</span>
                        Biểu Phí Thẩm Định & Chính Sách Bảo Lãnh
                    </h5>
                    <div class="row mb-3">
                        <div class="col-md-6 mb-2">
                            <div class="p-3 border rounded text-center">
                                <span class="badge badge-secondary mb-1">Cá Nhân / Hộ Kinh Doanh</span>
                                <h4 class="font-weight-bold text-primary mb-0">36.000.000 VNĐ</h4>
                                <small class="text-muted">Gói thẩm định & cấp huy hiệu</small>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="p-3 border rounded text-center" style="background: #FEF3C7; border-color: #D9A441 !important;">
                                <span class="badge badge-warning mb-1" style="background: #D9A441; color: #0A192F;">Doanh Nghiệp / HTX</span>
                                <h4 class="font-weight-bold text-primary mb-0">60.000.000 VNĐ</h4>
                                <small class="text-muted">Gói toàn diện + video song ngữ</small>
                            </div>
                        </div>
                    </div>
                    <div class="p-3 rounded alert-success small mb-0">
                        <i class="fa-solid fa-shield-check text-success mr-1"></i> <strong>Cam kết hoàn phí 100%:</strong> Nếu hồ sơ không vượt qua vòng thẩm định của Hội đồng Giám khảo VietKings, đơn vị sẽ được hoàn trả toàn bộ chi phí theo hợp đồng.
                    </div>
                </div>

                <!-- BƯỚC 5: Gửi Yêu Cầu Tư Vấn (Chưa Thu Tiền) -->
                <div>
                    <h5 class="font-serif text-primary mb-2">
                        <span class="badge badge-primary rounded-circle mr-1" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.75rem;">5</span>
                        Gửi Yêu Cầu Tư Vấn & Xác Định Phạm Vi
                    </h5>
                    <p class="small text-muted mb-4">
                        * Nhấn nút bên dưới để gửi yêu cầu. Bạn <strong>chưa phải thanh toán bất kỳ chi phí nào</strong> ở bước này. Chuyên viên Ban Thư ký sẽ liên hệ hướng dẫn hoàn thiện hồ sơ kỹ thuật.
                    </p>
                    <button type="submit" class="btn btn-tbg-cta btn-lg btn-block py-3 font-weight-bold" style="font-size: 1rem;">
                        <i class="fa-solid fa-paper-plane mr-2"></i> Gửi Yêu Cầu Tư Vấn Hồ Sơ Ngay
                    </button>
                </div>

            </form>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputName = document.getElementById('inputCompanyName');
    const selectInd = document.getElementById('selectIndustry');
    const selectProv = document.getElementById('selectProvince');
    const inputFormula = document.getElementById('inputFormulaTitle');
    const levelCards = document.querySelectorAll('.level-card');

    function updateFormula() {
        const name = inputName.value.trim() || '[Tên đơn vị]';
        const ind = selectInd.value || '[Ngành]';
        const prov = selectProv.value || '[Địa phương]';
        inputFormula.value = `TOP ${name} trong ${ind} tại ${prov}`;
    }

    inputName.addEventListener('input', updateFormula);
    selectInd.addEventListener('change', updateFormula);
    selectProv.addEventListener('change', updateFormula);

    levelCards.forEach(card => {
        card.addEventListener('click', function() {
            levelCards.forEach(c => {
                c.classList.remove('active');
                c.style.borderColor = '#CBD5E1';
                c.style.background = '#ffffff';
            });
            this.classList.add('active');
            this.style.borderColor = '#1A4C96';
            this.style.background = '#EFF6FF';
        });
    });
});
</script>
