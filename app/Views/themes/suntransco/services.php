<style>
    .services-page-wrapper { background: #f8fafc; padding: 100px 0 60px 0; min-height: 85vh; }
    .services-container { max-width: 1240px; margin: 0 auto; padding: 0 20px; }
    
    /* Horizontal Service Navigation Tabs */
    .services-tabs-bar { display: flex; gap: 8px; background: #ffffff; padding: 8px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 16px rgba(0,0,0,0.03); margin-bottom: 24px; overflow-x: auto; scrollbar-width: none; }
    .services-tabs-bar::-webkit-scrollbar { display: none; }
    .service-tab-btn { flex: 1; min-width: 170px; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 14px; border-radius: 8px; border: none; background: transparent; color: #475569; font-weight: 700; font-size: 0.83rem; cursor: pointer; transition: all 0.2s ease; text-decoration: none; white-space: nowrap; }
    .service-tab-btn:hover { background: #f1f5f9; color: #1d4ed8; }
    .service-tab-btn.active { background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%); color: #ffffff; box-shadow: 0 4px 12px rgba(37,99,235,0.25); }
    
    /* 2-Column Responsive Frame Layout */
    .services-main-grid { display: grid; grid-template-columns: 58% calc(42% - 24px); gap: 24px; align-items: start; }
    @media (max-width: 991px) { .services-main-grid { grid-template-columns: 1fr; gap: 20px; } }
    
    /* Left: Service Detail Cards */
    .service-content-panel { display: none; }
    .service-content-panel.active { display: block; animation: fadeIn 0.25s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
    
    .service-card-white { background: #ffffff; border-radius: 14px; border: 1px solid #e2e8f0; padding: 28px; box-shadow: 0 4px 16px rgba(0,0,0,0.03); }
    .service-icon-wrap { width: 52px; height: 52px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.4rem; margin-bottom: 16px; }
    .service-detail-title { font-size: 1.35rem; font-weight: 900; color: #0f172a; margin: 0 0 10px; line-height: 1.3; }
    .service-detail-desc { font-size: 0.88rem; color: #475569; line-height: 1.65; margin-bottom: 20px; }
    
    .service-highlights-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px; }
    .service-feat-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 14px; font-size: 0.8rem; color: #334155; display: flex; align-items: flex-start; gap: 10px; }
    .service-feat-box i { color: #2563eb; font-size: 1rem; margin-top: 2px; }
    .service-feat-box strong { display: block; font-size: 0.83rem; color: #0f172a; margin-bottom: 2px; }

    /* Right: Compact RFQ & Consultation Form */
    .rfq-form-card { background: #ffffff; border-radius: 14px; border: 1.5px solid #bfdbfe; padding: 24px; box-shadow: 0 8px 24px rgba(37,99,235,0.06); position: sticky; top: 95px; }
    .rfq-form-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; }
    .rfq-title { font-size: 1.05rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px; }
    
    .form-compact-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px; }
    .form-compact-group { margin-bottom: 10px; }
    .form-compact-label { display: block; font-size: 0.76rem; font-weight: 700; color: #334155; margin-bottom: 4px; }
    .form-compact-input { width: 100%; border: 1.5px solid #cbd5e1; border-radius: 6px; padding: 8px 12px; font-size: 0.82rem; color: #0f172a; background: #fff; box-sizing: border-box; }
    .form-compact-input:focus { border-color: #2563eb; outline: none; box-shadow: 0 0 0 3px rgba(37,99,235,0.12); }
    .btn-submit-rfq { width: 100%; background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%); color: #fff; font-weight: 800; font-size: 0.86rem; padding: 11px 16px; border-radius: 8px; border: none; cursor: pointer; box-shadow: 0 4px 12px rgba(37,99,235,0.25); display: flex; align-items: center; justify-content: center; gap: 8px; transition: transform 0.15s; }
    .btn-submit-rfq:hover { transform: translateY(-1px); }
</style>

<div class="services-page-wrapper">
    <div class="services-container">
        
        <!-- Service Tabs Navigation -->
        <div class="services-tabs-bar" id="servicesTabBar">
            <button class="service-tab-btn" data-tab="sea-freight">
                <i class="fa-solid fa-ship"></i> <span>Vận Tải Đường Biển</span>
            </button>
            <button class="service-tab-btn" data-tab="air-freight">
                <i class="fa-solid fa-plane-departure"></i> <span>Vận Tải Hàng Không</span>
            </button>
            <button class="service-tab-btn" data-tab="inland-freight">
                <i class="fa-solid fa-truck-moving"></i> <span>Vận Tải Nội Địa</span>
            </button>
            <button class="service-tab-btn" data-tab="warehousing">
                <i class="fa-solid fa-warehouse"></i> <span>Kho Bãi & CFS</span>
            </button>
            <button class="service-tab-btn" data-tab="customs-clearance">
                <i class="fa-solid fa-file-invoice-dollar"></i> <span>Khai Báo Hải Quan</span>
            </button>
        </div>

        <!-- 2-Column Viewport Grid (Left Content + Right Compact Form) -->
        <div class="services-main-grid">
            
            <!-- LEFT COLUMN: Detailed Service Panels -->
            <div class="services-left-content">
                
                <!-- TAB 1: SEA FREIGHT -->
                <div class="service-content-panel" id="panel-sea-freight">
                    <div class="service-card-white">
                        <div class="service-icon-wrap" style="background: linear-gradient(135deg, #0284c7, #0369a1);">
                            <i class="fa-solid fa-ship"></i>
                        </div>
                        <span class="badge" style="background:#e0f2fe; color:#0369a1; font-size:0.72rem; font-weight:800; padding:4px 10px; border-radius:12px; margin-bottom:8px; display:inline-block;">OCEAN FREIGHT FORWARDING</span>
                        <h2 class="service-detail-title">Vận Tải Đường Biển Quốc Tế (FCL & LCL)</h2>
                        <p class="service-detail-desc">
                            TOP BEST GLOBAL cung cấp giải pháp vận tải đường biển toàn diện từ các cảng chính của Việt Nam (Cát Lái, Hải Phòng, Cái Mép, Đà Nẵng) kết nối trực tiếp đến hơn 200+ cảng biển quốc tế tại Châu Á, Bắc Mỹ và Châu Âu.
                        </p>
                        <div class="service-highlights-grid">
                            <div class="service-feat-box">
                                <i class="fa-solid fa-boxes-stacked"></i>
                                <div><strong>Hàng Nguyên Container (FCL)</strong><span>Hợp đồng dài hạn với các hãng tàu Maersk, MSC, COSCO, ONE, Evergreen...</span></div>
                            </div>
                            <div class="service-feat-box">
                                <i class="fa-solid fa-dolly"></i>
                                <div><strong>Gom Hàng Lẻ (LCL Consol)</strong><span>Lịch đóng hàng lẻ cố định 2-3 chuyến/tuần với giá cước cạnh tranh.</span></div>
                            </div>
                            <div class="service-feat-box">
                                <i class="fa-solid fa-temperature-low"></i>
                                <div><strong>Container Lạnh (Reefer)</strong><span>Kiểm soát nhiệt độ -25°C đến +25°C cho nông sản, thủy hải sản.</span></div>
                            </div>
                            <div class="service-feat-box">
                                <i class="fa-solid fa-shield-halved"></i>
                                <div><strong>Bảo Hiểm Hàng Hóa 100%</strong><span>Cam kết an toàn tuyệt đối và hỗ trợ thủ tục hải quan trọn gói tại cảng.</span></div>
                            </div>
                        </div>
                        <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:12px 16px; font-size:0.82rem; color:#166534; display:flex; align-items:center; gap:8px;">
                            <i class="fa-solid fa-circle-check"></i> Tuyến thế mạnh: <strong>Việt Nam - Trung Quốc, Singapore, Mỹ (LA/Long Beach), Châu Âu (Rotterdam/Hamburg)</strong>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: AIR FREIGHT -->
                <div class="service-content-panel" id="panel-air-freight">
                    <div class="service-card-white">
                        <div class="service-icon-wrap" style="background: linear-gradient(135deg, #2563eb, #1d4ed8);">
                            <i class="fa-solid fa-plane-departure"></i>
                        </div>
                        <span class="badge" style="background:#eff6ff; color:#1d4ed8; font-size:0.72rem; font-weight:800; padding:4px 10px; border-radius:12px; margin-bottom:8px; display:inline-block;">AIR CARGO EXPRESS</span>
                        <h2 class="service-detail-title">Vận Chuyển Hàng Không Quốc Tế (Air Freight)</h2>
                        <p class="service-detail-desc">
                            Giải pháp vận chuyển hàng không tốc độ cao qua các sân bay Tân Sơn Nhất (SGN), Nội Bài (HAN), Đà Nẵng (DAD). Đáp ứng các lô hàng cần giao gấp, giá trị cao, hàng mẫu và linh kiện điện tử.
                        </p>
                        <div class="service-highlights-grid">
                            <div class="service-feat-box">
                                <i class="fa-solid fa-bolt"></i>
                                <div><strong>Air Express (24h - 48h)</strong><span>Chuyển phát ưu tiên đi thẳng các trung tâm kinh tế US, EU, Nhật Bản, Hàn Quốc.</span></div>
                            </div>
                            <div class="service-feat-box">
                                <i class="fa-solid fa-microchip"></i>
                                <div><strong>Hàng Công Nghệ Cao</strong><span>Chuyên chở an toàn linh kiện vi mạch, thiết bị điện tử, dược phẩm.</span></div>
                            </div>
                            <div class="service-feat-box">
                                <i class="fa-solid fa-handshake"></i>
                                <div><strong>Đối Tác Hàng Không Lớn</strong><span>Hợp đồng booking với Vietnam Airlines, Singapore Airlines, Cathay, Emirates...</span></div>
                            </div>
                            <div class="service-feat-box">
                                <i class="fa-solid fa-door-open"></i>
                                <div><strong>Dịch Vụ Door-to-Door</strong><span>Giao nhận tận nơi, thông quan nhanh chóng tại cảng đến.</span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 3: INLAND FREIGHT -->
                <div class="service-content-panel" id="panel-inland-freight">
                    <div class="service-card-white">
                        <div class="service-icon-wrap" style="background: linear-gradient(135deg, #16a34a, #15803d);">
                            <i class="fa-solid fa-truck-moving"></i>
                        </div>
                        <span class="badge" style="background:#f0fdf4; color:#15803d; font-size:0.72rem; font-weight:800; padding:4px 10px; border-radius:12px; margin-bottom:8px; display:inline-block;">DOMESTIC TRUCKING & HAULAGE</span>
                        <h2 class="service-detail-title">Vận Tải Nội Địa & Kéo Container Bắc Nam</h2>
                        <p class="service-detail-desc">
                            Đội xe đầu kéo container và xe tải chuyên dụng 150+ phương tiện vận hành liên tục 24/7. Kết nối thông suốt giữa các khu công nghiệp, kho bãi và hệ thống cảng biển toàn quốc.
                        </p>
                        <div class="service-highlights-grid">
                            <div class="service-feat-box">
                                <i class="fa-solid fa-truck-front"></i>
                                <div><strong>Kéo Container Cảng</strong><span>Rút ruột, kéo container 20'/40'/45' từ cảng về nhà máy chuẩn giờ.</span></div>
                            </div>
                            <div class="service-feat-box">
                                <i class="fa-solid fa-road"></i>
                                <div><strong>Vận Chuyển Tuyến Bắc Nam</strong><span>Lịch xe xuất phát hằng ngày, thời gian hành trình 48h - 60h.</span></div>
                            </div>
                            <div class="service-feat-box">
                                <i class="fa-solid fa-location-crosshairs"></i>
                                <div><strong>Định Vị GPS Trực Tuyến</strong><span>Hệ thống giám sát hành trình và cảnh báo nhiệt độ thời gian thực.</span></div>
                            </div>
                            <div class="service-feat-box">
                                <i class="fa-solid fa-certificate"></i>
                                <div><strong>Đội Ngũ Lái Xe Chuẩn Chỉ</strong><span>100% tài xế chuyên nghiệp, đầy đủ chứng chỉ vận tải hàng nguy hiểm.</span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 4: WAREHOUSING -->
                <div class="service-content-panel" id="panel-warehousing">
                    <div class="service-card-white">
                        <div class="service-icon-wrap" style="background: linear-gradient(135deg, #d97706, #b45309);">
                            <i class="fa-solid fa-warehouse"></i>
                        </div>
                        <span class="badge" style="background:#fef3c7; color:#b45309; font-size:0.72rem; font-weight:800; padding:4px 10px; border-radius:12px; margin-bottom:8px; display:inline-block;">CFS & BONDED WAREHOUSING</span>
                        <h2 class="service-detail-title">Hệ Thống Kho Bãi & Trung Tâm Phân Phối CFS</h2>
                        <p class="service-detail-desc">
                            Hệ thống kho bãi rộng hơn 20.000m² tại TP.HCM, Hải Phòng và Bình Dương được trang bị phần mềm quản lý kho thông minh WMS, camera an ninh 24/7 và hệ thống PCCC tiêu chuẩn quốc tế.
                        </p>
                        <div class="service-highlights-grid">
                            <div class="service-feat-box">
                                <i class="fa-solid fa-barcode"></i>
                                <div><strong>Quản Lý Mã Vạch WMS</strong><span>Kiểm soát xuất nhập tồn chính xác theo lô, hạn sử dụng và FIFO.</span></div>
                            </div>
                            <div class="service-feat-box">
                                <i class="fa-solid fa-box-open"></i>
                                <div><strong>Dịch Vụ Giá Trị Gia Tăng</strong><span>Đóng gói, dán nhãn phụ, phân loại hàng hóa và đóng pallet tiêu chuẩn.</span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 5: CUSTOMS CLEARANCE -->
                <div class="service-content-panel" id="panel-customs-clearance">
                    <div class="service-card-white">
                        <div class="service-icon-wrap" style="background: linear-gradient(135deg, #7c3aed, #6d28d9);">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                        </div>
                        <span class="badge" style="background:#faf5ff; color:#6d28d9; font-size:0.72rem; font-weight:800; padding:4px 10px; border-radius:12px; margin-bottom:8px; display:inline-block;">CUSTOMS BROKERAGE & COMPLIANCE</span>
                        <h2 class="service-detail-title">Thủ Tục Hải Quan & Khai Báo Xuất Nhập Khẩu</h2>
                        <p class="service-detail-desc">
                            Đại lý khai hải quan uy tín với đội ngũ chuyên gia am hiểu sâu sắc biểu thuế, chính sách mặt hàng và quy định kiểm tra chuyên ngành, giúp hàng hóa thông quan nhanh chóng trong ngày.
                        </p>
                        <div class="service-highlights-grid">
                            <div class="service-feat-box">
                                <i class="fa-solid fa-stamp"></i>
                                <div><strong>Xin C/O Các Form (A, D, E, EUR1...)</strong><span>Tối ưu hóa thuế nhập khẩu theo các hiệp định thương mại tự do FTA.</span></div>
                            </div>
                            <div class="service-feat-box">
                                <i class="fa-solid fa-file-shield"></i>
                                <div><strong>Kiểm Tra Chuyên Ngành</strong><span>Hỗ trợ kiểm dịch thực vật, kiểm tra an toàn thực phẩm, hợp chuẩn hợp quy.</span></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: Compact RFQ Form -->
            <div class="services-right-form">
                <div class="rfq-form-card">
                    <div class="rfq-form-header">
                        <h3 class="rfq-title">
                            <i class="fa-solid fa-file-signature text-primary" style="color:#2563eb;"></i> Yêu Cầu Báo Giá RFQ
                        </h3>
                        <span style="font-size:0.72rem; color:#16a34a; font-weight:700; background:#f0fdf4; padding:3px 8px; border-radius:10px;">
                            <i class="fa-solid fa-clock"></i> Phản hồi 5-15p
                        </span>
                    </div>

                    <form id="serviceRfqForm">
                        <input type="hidden" name="service_name" id="rfqServiceName" value="Vận Tải Đường Biển">

                        <div class="form-compact-row">
                            <div>
                                <label class="form-compact-label">Họ và tên <span style="color:#ef4444;">*</span></label>
                                <input type="text" name="name" required placeholder="Nguyễn Văn A" class="form-compact-input">
                            </div>
                            <div>
                                <label class="form-compact-label">Tên Công Ty</label>
                                <input type="text" name="company" placeholder="Công ty XNK ABC" class="form-compact-input">
                            </div>
                        </div>

                        <div class="form-compact-row">
                            <div>
                                <label class="form-compact-label">SĐT / Zalo <span style="color:#ef4444;">*</span></label>
                                <input type="tel" name="phone" required placeholder="0901 234 567" class="form-compact-input">
                            </div>
                            <div>
                                <label class="form-compact-label">Email nhận báo giá <span style="color:#ef4444;">*</span></label>
                                <input type="email" name="email" required placeholder="email@company.com" class="form-compact-input">
                            </div>
                        </div>

                        <div class="form-compact-row">
                            <div>
                                <label class="form-compact-label">Điểm / Cảng đi (POL)</label>
                                <input type="text" name="pol" placeholder="Ví dụ: Cát Lái, HCM" class="form-compact-input">
                            </div>
                            <div>
                                <label class="form-compact-label">Điểm / Cảng đến (POD)</label>
                                <input type="text" name="pod" placeholder="Ví dụ: Shanghai / LA" class="form-compact-input">
                            </div>
                        </div>

                        <div class="form-compact-group">
                            <label class="form-compact-label">Loại hình / Phương thức đóng gói</label>
                            <select name="type" id="rfqSelectType" class="form-compact-input">
                                <option value="FCL (Nguyên container 20/40/45)">FCL (Nguyên container 20/40/45)</option>
                                <option value="LCL (Gom hàng lẻ CBM)">LCL (Gom hàng lẻ CBM)</option>
                                <option value="Air Freight (Hàng không Express)">Air Freight (Hàng không Express)</option>
                                <option value="Inland Trucking (Xe tải / Đầu kéo)">Inland Trucking (Xe tải / Đầu kéo)</option>
                                <option value="Thủ tục Hải quan & C/O">Thủ tục Hải quan & C/O</option>
                            </select>
                        </div>

                        <div class="form-compact-group">
                            <label class="form-compact-label">Chi tiết hàng hóa & Ghi chú</label>
                            <textarea name="message" rows="3" placeholder="Tên mặt hàng, khối lượng (Tấn/CBM/Pallet), thời gian dự kiến xuất..." class="form-compact-input" style="resize:vertical;"></textarea>
                        </div>

                        <div id="serviceRfqAlert" style="display:none; padding:10px 12px; border-radius:6px; font-size:0.78rem; margin-bottom:10px;"></div>

                        <button type="submit" id="btnSubmitServiceRfq" class="btn-submit-rfq">
                            <i class="fa-solid fa-paper-plane"></i> <span>Gửi Yêu Cầu Báo Giá Ngay</span>
                        </button>
                    </form>
                    
                    <div style="margin-top:12px; font-size:0.72rem; color:#64748b; text-align:center;">
                        <i class="fa-solid fa-shield-halved text-primary"></i> Thông tin được bảo mật 100% theo tiêu chuẩn TOP BEST GLOBAL
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    var serviceNamesMap = {
        'sea-freight': { name: 'Vận Tải Đường Biển (Sea Freight)', type: 'FCL (Nguyên container 20/40/45)' },
        'air-freight': { name: 'Vận Tải Hàng Không (Air Freight)', type: 'Air Freight (Hàng không Express)' },
        'inland-freight': { name: 'Vận Tải Nội Địa (Inland Freight)', type: 'Inland Trucking (Xe tải / Đầu kéo)' },
        'warehousing': { name: 'Dịch Vụ Kho Bãi & CFS', type: 'LCL (Gom hàng lẻ CBM)' },
        'customs-clearance': { name: 'Thủ Tục Khai Báo Hải Quan', type: 'Thủ tục Hải quan & C/O' }
    };

    function switchServiceTab(tabKey) {
        if (!serviceNamesMap[tabKey]) tabKey = 'sea-freight';
        $('.service-tab-btn').removeClass('active');
        $('.service-tab-btn[data-tab="' + tabKey + '"]').addClass('active');
        
        $('.service-content-panel').removeClass('active');
        $('#panel-' + tabKey).addClass('active');

        $('#rfqServiceName').val(serviceNamesMap[tabKey].name);
        $('#rfqSelectType').val(serviceNamesMap[tabKey].type);
    }

    // Check URL query param ?tab=...
    var urlParams = new URLSearchParams(window.location.search);
    var activeTab = urlParams.get('tab') || 'sea-freight';
    switchServiceTab(activeTab);

    // Tab button click
    $('.service-tab-btn').on('click', function () {
        var tab = $(this).data('tab');
        switchServiceTab(tab);
        if (history.pushState) {
            var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?tab=' + tab;
            window.history.pushState({path:newurl}, '', newurl);
        }
    });

    // Form Submit via AJAX
    $('#serviceRfqForm').on('submit', function (e) {
        e.preventDefault();
        var $btn = $('#btnSubmitServiceRfq');
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> <span>Đang xử trị & gửi email...</span>');

        var data = $(this).serialize();
        $.ajax({
            url: '<?= base_url("api/contact"); ?>',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    $('#serviceRfqAlert').css({'background':'#f0fdf4','color':'#166534','border':'1px solid #bbf7d0','display':'block'}).html('<i class="fa-solid fa-circle-check"></i> ' + res.message);
                    $('#serviceRfqForm')[0].reset();
                } else {
                    $('#serviceRfqAlert').css({'background':'#fef2f2','color':'#991b1b','border':'1px solid #fecaca','display':'block'}).html('<i class="fa-solid fa-circle-exclamation"></i> ' + (res.message || 'Lỗi gửi yêu cầu.'));
                }
            },
            error: function () {
                $('#serviceRfqAlert').css({'background':'#fef2f2','color':'#991b1b','border':'1px solid #fecaca','display':'block'}).html('Lỗi kết nối máy chủ khi gửi báo giá.');
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="fa-solid fa-paper-plane"></i> <span>Gửi Yêu Cầu Báo Giá Ngay</span>');
            }
        });
    });
});
</script>
