<style>
    .services-page-wrapper { background: #f8fafc; padding: 60px 0; min-height: 85vh; }
    .services-container { max-width: 1240px; margin: 0 auto; padding: 0 20px; }
    
    /* Horizontal Honors Tabs */
    .services-tabs-bar { display: flex; gap: 8px; background: #ffffff; padding: 8px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 16px rgba(0,0,0,0.03); margin-bottom: 24px; overflow-x: auto; scrollbar-width: none; }
    .services-tabs-bar::-webkit-scrollbar { display: none; }
    .service-tab-btn { flex: 1; min-width: 180px; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 14px; border-radius: 8px; border: none; background: transparent; color: #475569; font-weight: 700; font-size: 0.83rem; cursor: pointer; transition: all 0.2s ease; text-decoration: none; white-space: nowrap; }
    .service-tab-btn:hover { background: #faf8f5; color: #b45309; }
    .service-tab-btn.active { background: linear-gradient(135deg, #0A192F 0%, #1e3a8a 100%); color: #F3E5AB; border: 1px solid #D4AF37; box-shadow: 0 4px 12px rgba(10,25,47,0.25); }
    
    /* 2-Column Layout */
    .services-main-grid { display: grid; grid-template-columns: 60% calc(40% - 24px); gap: 24px; align-items: start; }
    @media (max-width: 991px) { .services-main-grid { grid-template-columns: 1fr; gap: 20px; } }
    
    .service-content-panel { display: none; }
    .service-content-panel.active { display: block; animation: fadeIn 0.25s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
    
    .service-card-white { background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 28px; box-shadow: 0 4px 16px rgba(0,0,0,0.03); }
    .service-icon-wrap { width: 52px; height: 52px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #0A192F; font-size: 1.4rem; margin-bottom: 16px; }
    .service-detail-title { font-size: 1.35rem; font-weight: 900; color: #0A192F; margin: 0 0 10px; line-height: 1.3; }
    .service-detail-desc { font-size: 0.88rem; color: #475569; line-height: 1.65; margin-bottom: 20px; }
    
    .service-highlights-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px; }
    .service-feat-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 14px; font-size: 0.8rem; color: #334155; display: flex; align-items: flex-start; gap: 10px; }
    .service-feat-box i { color: #D4AF37; font-size: 1rem; margin-top: 2px; }
    .service-feat-box strong { display: block; font-size: 0.83rem; color: #0A192F; margin-bottom: 2px; }

    .rfq-form-card { background: #ffffff; border-radius: 16px; border: 2px solid #D4AF37; padding: 24px; box-shadow: 0 8px 24px rgba(10,25,47,0.06); position: sticky; top: 95px; }
    .btn-submit-rfq { width: 100%; background: linear-gradient(135deg, #D4AF37 0%, #AA771C 100%); color: #0A192F; font-weight: 800; font-size: 0.88rem; padding: 12px 16px; border-radius: 8px; border: none; cursor: pointer; box-shadow: 0 4px 12px rgba(212,175,55,0.3); display: flex; align-items: center; justify-content: center; gap: 8px; }
</style>

<div class="services-page-wrapper">
    <div class="services-container">
        
        <!-- Header Banner -->
        <div class="text-center mb-4 pb-2">
            <span class="badge px-3 py-1 mb-2" style="background: rgba(212,175,55,0.15); border: 1px solid #D4AF37; color: #b45309; font-weight: 800; font-size: 11px;">
                DỊCH VỤ & PHÂN HỆ QUỐC GIA
            </span>
            <h1 style="color: #0A192F; font-weight: 900; font-size: 2.1rem; margin-bottom: 8px;">
                Chương Trình Vinh Danh & Bảo Chứng Thương Hiệu
            </h1>
            <p class="text-muted" style="max-width: 700px; margin: 0 auto; font-size: 0.92rem;">
                Hệ sinh thái giải pháp số hóa toàn diện từ nộp hồ sơ đề cử, bình chọn trực tuyến đến chứng nhận số và xúc tiến thương mại quốc tế.
            </p>
        </div>

        <!-- Service Tabs Navigation -->
        <div class="services-tabs-bar" id="servicesTabBar">
            <button class="service-tab-btn active" data-tab="nomination-appraisal">
                <i class="fa-solid fa-file-signature"></i> <span>1. Đề Cử & Thẩm Định</span>
            </button>
            <button class="service-tab-btn" data-tab="public-voting">
                <i class="fa-solid fa-check-to-slot"></i> <span>2. Bình Chọn Trực Tuyến</span>
            </button>
            <button class="service-tab-btn" data-tab="hall-of-fame-badge">
                <i class="fa-solid fa-trophy"></i> <span>3. Bảng Vàng & Chứng Nhận Số</span>
            </button>
            <button class="service-tab-btn" data-tab="b2b-trade">
                <i class="fa-solid fa-handshake"></i> <span>4. Giao Thương B2B</span>
            </button>
            <button class="service-tab-btn" data-tab="media-sponsor">
                <i class="fa-solid fa-bullhorn"></i> <span>5. Truyền Thông & Tài Trợ</span>
            </button>
        </div>

        <!-- 2-Column Grid -->
        <div class="services-main-grid">
            
            <!-- LEFT COLUMN: Detailed Panels -->
            <div class="services-left-content">
                
                <!-- TAB 1: NOMINATION & APPRAISAL -->
                <div class="service-content-panel active" id="panel-nomination-appraisal">
                    <div class="service-card-white">
                        <div class="service-icon-wrap" style="background: linear-gradient(135deg, #D4AF37, #AA771C);">
                            <i class="fa-solid fa-file-signature"></i>
                        </div>
                        <span class="badge" style="background:#faf8f5; color:#b45309; font-size:0.72rem; font-weight:800; padding:4px 10px; border-radius:12px; margin-bottom:8px; display:inline-block; border:1px solid #f0e6d2;">HỒ SƠ & THẨM ĐỊNH CHUYÊN GIA</span>
                        <h2 class="service-detail-title">Nộp Hồ Sơ Đề Cử & Thẩm Định Độc Lập 4 Vòng</h2>
                        <p class="service-detail-desc">
                            Hệ thống tiếp nhận hồ sơ đề cử doanh nghiệp trực tuyến theo quy trình chuẩn hóa. Hội đồng Giám khảo độc lập thẩm tra báo cáo tài chính, bằng sáng chế và đóng góp xã hội theo rubric 100 điểm.
                        </p>
                        <div class="service-highlights-grid">
                            <div class="service-feat-box">
                                <i class="fa-solid fa-check-double"></i>
                                <div><strong>Thẩm Tra Độc Lập</strong><span>Bảo mật dữ liệu, đánh giá minh bạch không chịu ảnh hưởng thương mại.</span></div>
                            </div>
                            <div class="service-feat-box">
                                <i class="fa-solid fa-sliders"></i>
                                <div><strong>Rubric 4 Chiều Chuẩn Hóa</strong><span>Đổi mới (25%), Kinh doanh (30%), ESG (25%), Uy tín (20%).</span></div>
                            </div>
                            <div class="service-feat-box">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                                <div><strong>Theo Dõi Tiến Độ Real-time</strong><span>Tra cứu trạng thái xét duyệt hồ sơ trực tuyến với mã định danh.</span></div>
                            </div>
                            <div class="service-feat-box">
                                <i class="fa-solid fa-award"></i>
                                <div><strong>Hội Đồng Uy Tín</strong><span>Quy tụ các chuyên gia kinh tế, nhà khoa học và viện trưởng đầu ngành.</span></div>
                            </div>
                        </div>
                        <a href="<?= langBaseUrl('nomination/apply'); ?>" class="btn btn-primary btn-sm font-weight-bold" style="border-radius:6px; padding:8px 18px;">
                            Nộp Hồ Sơ Đề Cử Ngay <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- TAB 2: PUBLIC VOTING -->
                <div class="service-content-panel" id="panel-public-voting">
                    <div class="service-card-white">
                        <div class="service-icon-wrap" style="background: linear-gradient(135deg, #16a34a, #15803d); color: #fff;">
                            <i class="fa-solid fa-check-to-slot"></i>
                        </div>
                        <span class="badge" style="background:#f0fdf4; color:#16a34a; font-size:0.72rem; font-weight:800; padding:4px 10px; border-radius:12px; margin-bottom:8px; display:inline-block; border:1px solid #dcfce7;">ĐỘNG CƠ BÌNH CHỌN 70/30</span>
                        <h2 class="service-detail-title">Cổng Bình Chọn Bảo Vệ Đa Tầng & Chống Gian Lận</h2>
                        <p class="service-detail-desc">
                            Hệ thống bình chọn độc giả với xác thực mã OTP Email, kiểm soát vân tay thiết bị Canvas/Audio, giới hạn tần suất IP và bảng xếp hạng truyền hình trực tiếp theo thời gian thực.
                        </p>
                        <div class="service-highlights-grid">
                            <div class="service-feat-box">
                                <i class="fa-solid fa-envelope-circle-check"></i>
                                <div><strong>Xác Thực Email OTP</strong><span>Chống spam và bot ảo, đảm bảo mỗi người dùng là một cử tri thực.</span></div>
                            </div>
                            <div class="service-feat-box">
                                <i class="fa-solid fa-chart-simple"></i>
                                <div><strong>Leaderboard Real-time</strong><span>Cập nhật bảng tổng sắp và tỷ lệ bình chọn trực tiếp mỗi 15 giây.</span></div>
                            </div>
                            <div class="service-feat-box">
                                <i class="fa-solid fa-shield-halved"></i>
                                <div><strong>Kiểm Toán Audit Trail</strong><span>Ghi nhật ký bảo mật chống chối bỏ, kiểm tra tính toàn vẹn 100%.</span></div>
                            </div>
                            <div class="service-feat-box">
                                <i class="fa-solid fa-bolt"></i>
                                <div><strong>Chịu Tải 10.000+ Kết Nối</strong><span>Kiến trúc phân tán bộ nhớ đệm chống nghẽn mạng trong giờ cao điểm.</span></div>
                            </div>
                        </div>
                        <a href="<?= langBaseUrl('voting'); ?>" class="btn btn-primary btn-sm font-weight-bold" style="border-radius:6px; padding:8px 18px;">
                            Xem Cổng Bình Chọn <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- TAB 3: HALL OF FAME & BADGES -->
                <div class="service-content-panel" id="panel-hall-of-fame-badge">
                    <div class="service-card-white">
                        <div class="service-icon-wrap" style="background: linear-gradient(135deg, #0A192F, #1e3a8a); color: #D4AF37;">
                            <i class="fa-solid fa-trophy"></i>
                        </div>
                        <span class="badge" style="background:#faf8f5; color:#b45309; font-size:0.72rem; font-weight:800; padding:4px 10px; border-radius:12px; margin-bottom:8px; display:inline-block; border:1px solid #f0e6d2;">BẢO CHỨNG SỐ HÓA</span>
                        <h2 class="service-detail-title">Bảng Vàng Vinh Danh & Chứng Nhận Số Hóa Vĩnh Viễn</h2>
                        <p class="service-detail-desc">
                            Tôn vinh thương hiệu trên Bảng Vàng Quốc Gia và cấp chứng nhận số hóa kèm mã QR, mã băm mật mã SHA-256 bảo chứng uy tín trước đối tác và khách hàng quốc tế.
                        </p>
                        <div class="service-highlights-grid">
                            <div class="service-feat-box">
                                <i class="fa-solid fa-qrcode"></i>
                                <div><strong>Huy Hiệu Vector SVG</strong><span>Chuẩn đồ họa sắc nét chất lượng cao dùng cho bao bì và website.</span></div>
                            </div>
                            <div class="service-feat-box">
                                <i class="fa-solid fa-certificate"></i>
                                <div><strong>Mã Băm Xác Thực</strong><span>Tra cứu trực tuyến tính hợp lệ của chứng nhận trên toàn cầu.</span></div>
                            </div>
                            <div class="service-feat-box">
                                <i class="fa-solid fa-trophy"></i>
                                <div><strong>Cúp Vàng Danh Dự</strong><span>Biểu trưng sang trọng trao tại đêm Gala truyền hình trực tiếp.</span></div>
                            </div>
                            <div class="service-feat-box">
                                <i class="fa-solid fa-globe"></i>
                                <div><strong>Hiện Diện Vĩnh Viễn</strong><span>Hồ sơ thành tích được lưu trữ trên Cổng Quốc Gia trọn đời.</span></div>
                            </div>
                        </div>
                        <a href="<?= langBaseUrl('hall-of-fame'); ?>" class="btn btn-primary btn-sm font-weight-bold" style="border-radius:6px; padding:8px 18px;">
                            Khám Phá Bảng Vàng <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- TAB 4: B2B TRADE -->
                <div class="service-content-panel" id="panel-b2b-trade">
                    <div class="service-card-white">
                        <div class="service-icon-wrap" style="background: linear-gradient(135deg, #2563eb, #1d4ed8); color:#fff;">
                            <i class="fa-solid fa-handshake"></i>
                        </div>
                        <span class="badge" style="background:#eff6ff; color:#1d4ed8; font-size:0.72rem; font-weight:800; padding:4px 10px; border-radius:12px; margin-bottom:8px; display:inline-block; border:1px solid #dbeafe;">MẠNG LƯỚI GIAO THƯƠNG</span>
                        <h2 class="service-detail-title">Xúc Tiến Thương Mại & Kết Nối Đối Tác B2B</h2>
                        <p class="service-detail-desc">
                            Mở rộng mạng lưới đối tác cung ứng, sản xuất và xuất nhập khẩu với hơn 500+ doanh nghiệp hàng đầu trong hệ sinh thái TOP BEST GLOBAL.
                        </p>
                        <a href="<?= langBaseUrl('partners'); ?>" class="btn btn-primary btn-sm font-weight-bold" style="border-radius:6px; padding:8px 18px;">
                            Xem Danh Bạ Đối Tác <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- TAB 5: MEDIA & SPONSOR -->
                <div class="service-content-panel" id="panel-media-sponsor">
                    <div class="service-card-white">
                        <div class="service-icon-wrap" style="background: linear-gradient(135deg, #9333ea, #7e22ce); color:#fff;">
                            <i class="fa-solid fa-bullhorn"></i>
                        </div>
                        <span class="badge" style="background:#faf5ff; color:#9333ea; font-size:0.72rem; font-weight:800; padding:4px 10px; border-radius:12px; margin-bottom:8px; display:inline-block; border:1px solid #f3e8ff;">TRUYỀN THÔNG ĐỒNG HÀNH</span>
                        <h2 class="service-detail-title">Bảo Trợ Truyền Thông & Quảng Bá Thương Hiệu</h2>
                        <p class="service-detail-desc">
                            Đồng hành cùng 20+ cơ quan thông tấn báo chí, truyền hình quốc gia và nền tảng số nhằm lan tỏa mạnh mẽ câu chuyện thương hiệu và định vị vị thế dẫn đầu.
                        </p>
                        <a href="<?= langBaseUrl('contact'); ?>" class="btn btn-primary btn-sm font-weight-bold" style="border-radius:6px; padding:8px 18px;">
                            Liên Hệ Ban Tổ Chức <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Compact Inquiry Form -->
            <div class="services-right-form">
                <div class="rfq-form-card">
                    <div class="d-flex align-items-center mb-3">
                        <div style="width: 38px; height: 38px; border-radius: 8px; background: #0A192F; color: #D4AF37; display: flex; align-items: center; justify-content: center; margin-right: 10px; font-size: 16px;">
                            <i class="fa-solid fa-paper-plane"></i>
                        </div>
                        <div>
                            <h5 style="font-size: 1.05rem; font-weight: 800; color: #0A192F; margin: 0;">Đăng Ký Tư Vấn Đề Cử</h5>
                            <small class="text-muted">Ban thư ký sẽ liên hệ hỗ trợ trong 24h</small>
                        </div>
                    </div>

                    <form action="<?= langBaseUrl('contact'); ?>" method="post">
                        <?= csrf_field(); ?>
                        <div style="margin-bottom: 12px;">
                            <label style="font-size: 0.76rem; font-weight: 700; color: #334155; margin-bottom: 4px; display: block;">Tên Doanh Nghiệp / Thương Hiệu *</label>
                            <input type="text" name="name" required class="form-control form-control-sm" placeholder="VD: Tập đoàn Công nghệ ABC" style="border-radius: 6px;">
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 12px;">
                            <div>
                                <label style="font-size: 0.76rem; font-weight: 700; color: #334155; margin-bottom: 4px; display: block;">Số Điện Thoại *</label>
                                <input type="tel" name="phone" required class="form-control form-control-sm" placeholder="090..." style="border-radius: 6px;">
                            </div>
                            <div>
                                <label style="font-size: 0.76rem; font-weight: 700; color: #334155; margin-bottom: 4px; display: block;">Email Liên Hệ *</label>
                                <input type="email" name="email" required class="form-control form-control-sm" placeholder="contact@..." style="border-radius: 6px;">
                            </div>
                        </div>
                        <div style="margin-bottom: 16px;">
                            <label style="font-size: 0.76rem; font-weight: 700; color: #334155; margin-bottom: 4px; display: block;">Lĩnh Vực Quan Tâm Đề Cử</label>
                            <select name="category" class="form-control form-control-sm" style="border-radius: 6px;">
                                <option value="tech">Công Nghệ & Chuyển Đổi Số</option>
                                <option value="logistics">Vận Tải & Chuỗi Cung Ứng</option>
                                <option value="manufacturing">Sản Xuất & Chế Biến</option>
                                <option value="finance">Tài Chính & Ngân Hàng</option>
                                <option value="leadership">Lãnh Đạo & Doanh Nhân Tiêu Biểu</option>
                                <option value="other">Lĩnh Vực Khác</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-submit-rfq font-weight-bold">
                            <i class="fa-solid fa-paper-plane mr-1"></i> GỬI YÊU CẦU TƯ VẤN
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.service-tab-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('.service-tab-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        const tab = this.getAttribute('data-tab');
        document.querySelectorAll('.service-content-panel').forEach(p => p.classList.remove('active'));
        const target = document.getElementById('panel-' + tab);
        if (target) target.classList.add('active');
    });
});
</script>
