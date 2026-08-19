    <!-- Hero Banner -->
    <section class="hero-section" id="home">
        <div class="hero-overlay"></div>
        <div class="container hero-container-box">
            <div class="hero-content">
                <span class="badge hero-badge-futuristic"><i class="fa-solid fa-circle-nodes"></i> TOP BEST GLOBAL E-Carrier Platform</span>
                <h1 class="hero-title-white">
                    <span class="lang-vi">Giải Pháp Logistics<br><span class="text-gradient">Thông Minh & Toàn Diện</span></span>
                    <span class="lang-en">Smart & Comprehensive<br><span class="text-gradient">Logistics Solutions</span></span>
                </h1>
                <p class="hero-desc-white">
                    <span class="lang-vi">Nền tảng vận chuyển số hóa kết nối 500+ doanh nghiệp sản xuất, xuất nhập khẩu — tối ưu hóa quy trình vận tải, tiết kiệm 30% chi phí logistics.</span>
                    <span class="lang-en">Digital transport platform connecting 500+ manufacturing & trading enterprises — optimizing shipping workflows and saving 30% logistics costs.</span>
                </p>
                <div class="hero-actions">
                    <a href="<?= langBaseUrl('services'); ?>" class="btn btn-primary btn-lg">
                        <i class="fa-solid fa-rocket"></i> 
                        <span class="lang-vi">Khám Phá Dịch Vụ</span><span class="lang-en">Explore Services</span>
                    </a>
                    <a href="<?= langBaseUrl('services'); ?>?tab=sea-freight" class="btn btn-outline btn-lg">
                        <span class="lang-vi">Yêu Cầu Báo Giá</span><span class="lang-en">Request Quote</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Quote Bar -->
    <section class="quick-actions-section" style="padding: 30px 0; background: #ffffff; position: relative; z-index: 10;">
        <div class="container">
            <div class="quick-quote-container card-corporate" style="background:#ffffff; border:2px solid #2563eb; box-shadow: 0 10px 30px rgba(37,99,235,0.12); padding: 22px 28px;">
                <h4 class="quick-quote-title" style="color:#0f172a; font-size:1rem; margin-bottom:12px; font-weight:800;">
                    <i class="fa-solid fa-bolt text-primary"></i> <span class="lang-vi">Báo Giá Cước Nhanh</span><span class="lang-en">Quick Freight Quote</span>
                </h4>
                <form id="quickQuoteForm" action="<?= base_url('services'); ?>" method="get" class="quick-quote-form" style="display:grid; grid-template-columns: 1.2fr 1fr 1fr auto; gap: 14px; align-items: flex-end;">
                    <div class="quote-form-group">
                        <label style="color:#475569; font-weight:700; font-size:0.75rem; text-transform:uppercase; display:block; margin-bottom:4px;"><span class="lang-vi">Loại hình</span><span class="lang-en">Shipment Type</span></label>
                        <div class="quote-type-selector" style="display:flex; gap:6px; background:#f8fafc; padding:3px; border-radius:8px; border:1.5px solid #2563eb; height:42px; align-items:center;">
                            <label class="type-btn active" style="flex:1; display:flex; align-items:center; justify-content:center; gap:6px; height:34px; padding:0 12px; border-radius:6px; font-weight:800; font-size:0.82rem; cursor:pointer; background:#2563eb; color:#ffffff;">
                                <input type="radio" name="tab" value="sea-freight" checked style="display:none;"> FCL
                            </label>
                            <label class="type-btn" style="flex:1; display:flex; align-items:center; justify-content:center; gap:6px; height:34px; padding:0 12px; border-radius:6px; font-weight:800; font-size:0.82rem; cursor:pointer; color:#475569;">
                                <input type="radio" name="tab" value="air-freight" style="display:none;"> Air
                            </label>
                        </div>
                    </div>
                    <div class="quote-form-group">
                        <label style="color:#475569; font-weight:700; font-size:0.75rem; text-transform:uppercase; display:block; margin-bottom:4px;"><span class="lang-vi">Cảng Đi *</span><span class="lang-en">Origin Port *</span></label>
                        <select name="pol" required style="background:#ffffff; color:#0f172a; border:1.5px solid #2563eb; width:100%; height:42px; padding:0 12px; border-radius:8px; font-size:0.82rem; font-weight:600;">
                            <option value="Cát Lái (HCMC)">Cát Lái (HCMC)</option>
                            <option value="Hải Phòng">Hải Phòng</option>
                            <option value="Đà Nẵng">Đà Nẵng</option>
                            <option value="Singapore">Singapore</option>
                        </select>
                    </div>
                    <div class="quote-form-group">
                        <label style="color:#475569; font-weight:700; font-size:0.75rem; text-transform:uppercase; display:block; margin-bottom:4px;"><span class="lang-vi">Cảng Đến *</span><span class="lang-en">Destination Port *</span></label>
                        <select name="pod" required style="background:#ffffff; color:#0f172a; border:1.5px solid #2563eb; width:100%; height:42px; padding:0 12px; border-radius:8px; font-size:0.82rem; font-weight:600;">
                            <option value="Shanghai (China)">Shanghai (China)</option>
                            <option value="Los Angeles (US)">Los Angeles (US)</option>
                            <option value="Rotterdam (EU)">Rotterdam (EU)</option>
                            <option value="Tokyo (Japan)">Tokyo (Japan)</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-quote-submit" style="height:42px; padding:0 22px; font-weight:800; font-size:0.82rem;">
                        <span class="lang-vi">Nhận Báo Giá <i class="fa-solid fa-arrow-right"></i></span>
                        <span class="lang-en">Get Quote <i class="fa-solid fa-arrow-right"></i></span>
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Why Choose TOP BEST GLOBAL Module -->
    <section class="why-choose-section" style="padding: 75px 0; background: linear-gradient(135deg, #070e1f 0%, #0c1838 50%, #070e1f 100%); color: #ffffff; position: relative; border-top: 2px solid #2563eb; border-bottom: 2px solid #2563eb;">
        <div class="container">
            <div class="text-center" style="margin-bottom: 45px;">
                <span class="badge" style="background: rgba(37,99,235,0.2); border: 1.5px solid #3b82f6; color: #93c5fd; font-weight: 800; margin-bottom: 10px; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-crown text-warning"></i>
                    <span class="lang-vi">LỢI THẾ CẠNH TRANH ĐỘT PHÁ</span>
                    <span class="lang-en">COMPETITIVE ADVANTAGES</span>
                </span>
                <h2 class="section-title" style="color: #ffffff !important; font-size: 2.2rem; font-weight: 900; margin-bottom: 10px;">
                    <span class="lang-vi">Tại Sao Hơn 500+ Doanh Nghiệp Chọn TOP BEST GLOBAL?</span>
                    <span class="lang-en">Why 500+ Leading Enterprises Choose TOP BEST GLOBAL?</span>
                </h2>
                <p style="color: rgba(255,255,255,0.75); max-width: 720px; margin: 0 auto; font-size: 0.92rem; line-height: 1.6;">
                    <span class="lang-vi">Nền tảng logistics số hóa kết hợp mạng lưới liên minh vận tải thực chiến, đem lại hiệu quả vượt trội về chi phí, tốc độ và tính an toàn cho chuỗi cung ứng.</span>
                </p>
            </div>

            <!-- 6 Value Propositions Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px;">
                <div class="card-corporate" style="background: rgba(255,255,255,0.04); border: 1.5px solid rgba(255,255,255,0.12); backdrop-filter: blur(10px); border-radius: 16px; padding: 26px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
                        <div style="width: 50px; height: 50px; border-radius: 12px; background: linear-gradient(135deg, #10b981, #059669); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.35rem;">
                            <i class="fa-solid fa-piggy-bank"></i>
                        </div>
                        <span style="background: rgba(16,185,129,0.15); color: #34d399; font-size: 0.7rem; font-weight: 800; padding: 3px 8px; border-radius: 12px; border: 1px solid rgba(16,185,129,0.3);">TIẾT KIỆM 30% CHI PHÍ</span>
                    </div>
                    <h3 style="font-size: 1.12rem; font-weight: 800; color: #fff; margin-bottom: 6px;">Giá Cước Gốc Hãng Tàu</h3>
                    <p style="font-size: 0.83rem; color: rgba(255,255,255,0.7); line-height: 1.6; margin: 0;">Liên kết trực tiếp 20+ hãng tàu & hãng bay quốc tế (Maersk, MSC, COSCO, VN Airlines...). Báo giá gốc không qua trung gian, chính sách cước minh bạch.</p>
                </div>

                <div class="card-corporate" style="background: rgba(255,255,255,0.04); border: 1.5px solid rgba(255,255,255,0.12); backdrop-filter: blur(10px); border-radius: 16px; padding: 26px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
                        <div style="width: 50px; height: 50px; border-radius: 12px; background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.35rem;">
                            <i class="fa-solid fa-bolt-lightning"></i>
                        </div>
                        <span style="background: rgba(245,158,11,0.15); color: #fbbf24; font-size: 0.7rem; font-weight: 800; padding: 3px 8px; border-radius: 12px; border: 1px solid rgba(245,158,11,0.3);">THÔNG QUAN TRONG NGÀY</span>
                    </div>
                    <h3 style="font-size: 1.12rem; font-weight: 800; color: #fff; margin-bottom: 6px;">Tự Động Hóa Hải Quan Bằng AI</h3>
                    <p style="font-size: 0.83rem; color: rgba(255,255,255,0.7); line-height: 1.6; margin: 0;">Hệ thống AI tự động tra cứu mã HS Code và kiểm tra chứng từ xuất nhập khẩu. Rút ngắn thời gian thông quan luồng xanh, không lo phát sinh phí Demurrage.</p>
                </div>

                <div class="card-corporate" style="background: rgba(255,255,255,0.04); border: 1.5px solid rgba(255,255,255,0.12); backdrop-filter: blur(10px); border-radius: 16px; padding: 26px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
                        <div style="width: 50px; height: 50px; border-radius: 12px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.35rem;">
                            <i class="fa-solid fa-earth-americas"></i>
                        </div>
                        <span style="background: rgba(59,130,246,0.15); color: #93c5fd; font-size: 0.7rem; font-weight: 800; padding: 3px 8px; border-radius: 12px; border: 1px solid rgba(59,130,246,0.3);">200+ CẢNG QUỐC TẾ</span>
                    </div>
                    <h3 style="font-size: 1.12rem; font-weight: 800; color: #fff; margin-bottom: 6px;">Vận Tải Đa Phương Thức Toàn Cầu</h3>
                    <p style="font-size: 0.83rem; color: rgba(255,255,255,0.7); line-height: 1.6; margin: 0;">Vận tải đường biển FCL/LCL, hàng không Express và vận tải bộ Bắc Nam đến 120+ quốc gia: Trung Quốc, Singapore, Mỹ (LA), Châu Âu (Rotterdam).</p>
                </div>

                <div class="card-corporate" style="background: rgba(255,255,255,0.04); border: 1.5px solid rgba(255,255,255,0.12); backdrop-filter: blur(10px); border-radius: 16px; padding: 26px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
                        <div style="width: 50px; height: 50px; border-radius: 12px; background: linear-gradient(135deg, #8b5cf6, #6d28d9); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.35rem;">
                            <i class="fa-solid fa-warehouse"></i>
                        </div>
                        <span style="background: rgba(139,92,246,0.15); color: #c4b5fd; font-size: 0.7rem; font-weight: 800; padding: 3px 8px; border-radius: 12px; border: 1px solid rgba(139,92,246,0.3);">KHO CFS 20.000M²</span>
                    </div>
                    <h3 style="font-size: 1.12rem; font-weight: 800; color: #fff; margin-bottom: 6px;">Kho Bãi & Quản Trị WMS Thông Minh</h3>
                    <p style="font-size: 0.83rem; color: rgba(255,255,255,0.7); line-height: 1.6; margin: 0;">Hệ thống kho CFS hiện đại tại TP.HCM & Hải Phòng trang bị phần mềm WMS, đóng gói dán nhãn, bảo quản nhiệt độ và phân phối tự động 24/7.</p>
                </div>

                <div class="card-corporate" style="background: rgba(255,255,255,0.04); border: 1.5px solid rgba(255,255,255,0.12); backdrop-filter: blur(10px); border-radius: 16px; padding: 26px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
                        <div style="width: 50px; height: 50px; border-radius: 12px; background: linear-gradient(135deg, #ef4444, #b91c1c); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.35rem;">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <span style="background: rgba(239,68,68,0.15); color: #fca5a5; font-size: 0.7rem; font-weight: 800; padding: 3px 8px; border-radius: 12px; border: 1px solid rgba(239,68,68,0.3);">BẢO HIỂM 100%</span>
                    </div>
                    <h3 style="font-size: 1.12rem; font-weight: 800; color: #fff; margin-bottom: 6px;">Định Vị GPS & Cam Kết An Toàn</h3>
                    <p style="font-size: 0.83rem; color: rgba(255,255,255,0.7); line-height: 1.6; margin: 0;">Đội xe tải & đầu kéo 150+ phương tiện định vị GPS 24/7. Hàng hóa được bảo hiểm trọn gói với cam kết bồi thường 100% giá trị khi xảy ra rủi ro.</p>
                </div>

                <div class="card-corporate" style="background: rgba(255,255,255,0.04); border: 1.5px solid rgba(255,255,255,0.12); backdrop-filter: blur(10px); border-radius: 16px; padding: 26px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
                        <div style="width: 50px; height: 50px; border-radius: 12px; background: linear-gradient(135deg, #06b6d4, #0891b2); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.35rem;">
                            <i class="fa-solid fa-handshake-angle"></i>
                        </div>
                        <span style="background: rgba(6,182,212,0.15); color: #67e8f9; font-size: 0.7rem; font-weight: 800; padding: 3px 8px; border-radius: 12px; border: 1px solid rgba(6,182,212,0.3);">500+ HỘI VIÊN B2B</span>
                    </div>
                    <h3 style="font-size: 1.12rem; font-weight: 800; color: #fff; margin-bottom: 6px;">Liên Minh Giao Thương B2B Độc Quyền</h3>
                    <p style="font-size: 0.83rem; color: rgba(255,255,255,0.7); line-height: 1.6; margin: 0;">Gia nhập liên minh tiếp cận hơn 500+ doanh nghiệp xuất nhập khẩu uy tín. Gửi tin nhắn B2B, trao đổi cơ hội hợp tác và nhận đơn hàng hai chiều.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 5 Dịch vụ cốt lõi TOP BEST GLOBAL -->
    <section class="services-section" style="padding: 70px 0; background: var(--bg-light);">
        <div class="container">
            <div class="section-header text-center">
                <span class="section-tag"><span class="lang-vi">Dịch Vụ Cốt Lõi</span><span class="lang-en">Core Services</span></span>
                <h2 class="section-title"><span class="lang-vi">Giải Pháp Logistics Toàn Diện</span><span class="lang-en">Comprehensive Logistics</span></h2>
            </div>
            <div class="services-5grid mt-lg">
                <div class="card-corporate service-card-5 service-card-3col">
                    <div class="service-icon-wrap" style="background: linear-gradient(135deg, #1e40af, #2563eb);"><i class="fa-solid fa-plane-departure"></i></div>
                    <span class="badge" style="margin-bottom:8px; font-size:0.68rem; padding:3px 10px;">Air Cargo</span>
                    <h3 style="font-size:1.15rem; margin-bottom:8px;">Air Freight</h3>
                    <p style="font-size:0.83rem; color:var(--text-muted); line-height:1.6; margin-bottom:16px;">Vận chuyển hàng không hỏa tốc đến 150+ sân bay quốc tế.</p>
                    <a href="<?= langBaseUrl('services'); ?>?tab=air-freight" class="btn btn-outline btn-sm">Xem Chi Tiết <i class="fa-solid fa-arrow-right"></i></a>
                </div>

                <div class="card-corporate service-card-5 service-card-3col">
                    <div class="service-icon-wrap" style="background: linear-gradient(135deg, #0e7490, #0284c7);"><i class="fa-solid fa-ship"></i></div>
                    <span class="badge" style="margin-bottom:8px; font-size:0.68rem; padding:3px 10px;">FCL & LCL</span>
                    <h3 style="font-size:1.15rem; margin-bottom:8px;">Sea Freight</h3>
                    <p style="font-size:0.83rem; color:var(--text-muted); line-height:1.6; margin-bottom:16px;">Vận tải biển nguyên container và hàng lẻ giá gốc hãng tàu.</p>
                    <a href="<?= langBaseUrl('services'); ?>?tab=sea-freight" class="btn btn-outline btn-sm">Xem Chi Tiết <i class="fa-solid fa-arrow-right"></i></a>
                </div>

                <div class="card-corporate service-card-5 service-card-3col">
                    <div class="service-icon-wrap" style="background: linear-gradient(135deg, #15803d, #16a34a);"><i class="fa-solid fa-truck-moving"></i></div>
                    <span class="badge" style="margin-bottom:8px; font-size:0.68rem; padding:3px 10px;">Trucking</span>
                    <h3 style="font-size:1.15rem; margin-bottom:8px;">Inland Freight</h3>
                    <p style="font-size:0.83rem; color:var(--text-muted); line-height:1.6; margin-bottom:16px;">Kéo container và vận tải bộ Bắc Nam an toàn 24/7.</p>
                    <a href="<?= langBaseUrl('services'); ?>?tab=inland-freight" class="btn btn-outline btn-sm">Xem Chi Tiết <i class="fa-solid fa-arrow-right"></i></a>
                </div>

                <div class="card-corporate service-card-5 service-card-2col">
                    <div class="service-icon-wrap" style="background: linear-gradient(135deg, #b45309, #d97706);"><i class="fa-solid fa-warehouse"></i></div>
                    <span class="badge" style="margin-bottom:8px; font-size:0.68rem; padding:3px 10px;">CFS Warehouse</span>
                    <h3 style="font-size:1.15rem; margin-bottom:8px;">Kho Bãi & CFS</h3>
                    <p style="font-size:0.83rem; color:var(--text-muted); line-height:1.6; margin-bottom:16px;">Hệ thống kho CFS 20.000m² quản lý phần mềm WMS thông minh.</p>
                    <a href="<?= langBaseUrl('services'); ?>?tab=warehousing" class="btn btn-outline btn-sm">Xem Chi Tiết <i class="fa-solid fa-arrow-right"></i></a>
                </div>

                <div class="card-corporate service-card-5 service-card-2col">
                    <div class="service-icon-wrap" style="background: linear-gradient(135deg, #7c3aed, #9333ea);"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                    <span class="badge" style="margin-bottom:8px; font-size:0.68rem; padding:3px 10px;">Customs Clearance</span>
                    <h3 style="font-size:1.15rem; margin-bottom:8px;">Thủ Tục Hải Quan</h3>
                    <p style="font-size:0.83rem; color:var(--text-muted); line-height:1.6; margin-bottom:16px;">Đại lý khai hải quan, xin C/O và áp mã HS Code chính xác.</p>
                    <a href="<?= langBaseUrl('services'); ?>?tab=customs-clearance" class="btn btn-outline btn-sm">Xem Chi Tiết <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- Recent News Section (Tin Tức Chuyên Ngành) -->
    <?php 
    $homeNewsPosts = !empty($recentPosts) ? $recentPosts : (!empty($latestPosts) ? $latestPosts : []);
    $newsCategories = [];
    foreach ($homeNewsPosts as $p) {
        if (!empty($p->category_id) && !empty($p->category_name)) {
            $newsCategories[$p->category_id] = $p->category_name;
        }
    }
    if (!empty($homeNewsPosts)): ?>
    <section class="news-section" id="news" style="padding: 70px 0; background: #ffffff; border-top: 1px solid #e2e8f0;">
        <div class="container">
            <div class="section-header text-center mb-lg" style="margin-bottom: 28px;">
                <span class="badge" style="background: rgba(37,99,235,0.08); border: 1.5px solid #2563eb; color: #1d4ed8; font-weight: 800;">
                    <i class="fa-solid fa-newspaper text-primary"></i> <span class="lang-vi">Cổng Thông Tin</span>
                </span>
                <h2 class="section-title" style="font-size: 2rem; font-weight: 900; color: #0f172a; margin-top: 8px;">
                    <span class="lang-vi">Tin Tức & Phân Tích Chuyên Sâu</span>
                </h2>
            </div>

            <div class="news-category-filters">
                <button type="button" class="news-filter-btn active" onclick="filterNewsCategory(this, 'all')">Tất Cả</button>
                <?php foreach ($newsCategories as $catId => $catName): ?>
                    <button type="button" class="news-filter-btn" onclick="filterNewsCategory(this, '<?= $catId; ?>')"><?= esc($catName); ?></button>
                <?php endforeach; ?>
            </div>

            <div class="grid grid-2 news-grid-2col" id="homeNewsGrid" style="gap: 24px;">
                <?php 
                $count = 0;
                foreach ($homeNewsPosts as $post): 
                    if ($count >= 4) break;
                    $count++;
                    $postImg = getPostImage($post, 'mid');
                    $postUrl = generatePostUrl($post);
                ?>
                    <article class="news-card card-corporate" data-category="<?= $post->category_id ?? 0; ?>" style="background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 14px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 4px 16px rgba(0,0,0,0.03);">
                        <a href="<?= $postUrl; ?>" style="display: block; position: relative; overflow: hidden; width: 100%; aspect-ratio: 16 / 9;">
                            <?php if (!empty($postImg)): ?>
                                <img src="<?= $postImg; ?>" alt="<?= esc($post->title); ?>" style="width: 100%; height: 100%; object-fit: cover;" loading="lazy">
                            <?php else: ?>
                                <div style="width: 100%; height: 100%; background: #eff6ff; display: flex; align-items: center; justify-content: center; color: #2563eb; font-size: 2rem;"><i class="fa-solid fa-newspaper"></i></div>
                            <?php endif; ?>
                            <?php if (!empty($post->category_name)): ?>
                                <span style="position: absolute; top: 10px; left: 10px; background: #2563eb; color: #fff; padding: 3px 8px; border-radius: 4px; font-size: 0.68rem; font-weight: 800;"><?= esc($post->category_name); ?></span>
                            <?php endif; ?>
                        </a>
                        <div style="padding: 18px; display: flex; flex-direction: column; flex: 1;">
                            <div style="font-size: 0.72rem; color: #64748b; margin-bottom: 6px; font-weight: 600;"><i class="fa-regular fa-calendar text-primary"></i> <?= formatDate($post->created_at); ?></div>
                            <h3 style="font-size: 1rem; font-weight: 800; margin: 0 0 8px; line-height: 1.4; color: #0f172a;">
                                <a href="<?= $postUrl; ?>" style="color: inherit; text-decoration: none;"><?= esc($post->title); ?></a>
                            </h3>
                            <p style="font-size: 0.82rem; color: #475569; line-height: 1.55; margin-bottom: 12px; flex-grow: 1;"><?= esc(characterLimiter($post->summary ?? '', 90)); ?></p>
                            <a href="<?= $postUrl; ?>" style="font-size: 0.8rem; font-weight: 800; color: #1d4ed8; text-decoration: none;">Đọc tiếp <i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="text-center" style="margin-top: 24px;">
                <a href="<?= langBaseUrl('news'); ?>" class="btn btn-outline" style="padding: 8px 20px; font-weight: 700; font-size: 0.84rem;">Xem Tất Cả Tin Tức <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
    </section>
    <script>
    function filterNewsCategory(btn, catId) {
        document.querySelectorAll('.news-filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.querySelectorAll('#homeNewsGrid .news-card').forEach(card => {
            card.style.display = (catId === 'all' || card.getAttribute('data-category') === String(catId)) ? 'flex' : 'none';
        });
    }
    </script>
    <?php endif; ?>

    <!-- Featured B2B Events Mini Cards Section -->
    <?php if (!empty($featuredEvents)): ?>
    <section class="events-home-section" style="padding: 65px 0; background: #f8fafc; border-top: 1px solid #e2e8f0;">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 28px; flex-wrap: wrap; gap: 14px;">
                <div>
                    <span class="badge" style="background: rgba(16,185,129,0.12); border: 1.5px solid #10b981; color: #059669; font-weight: 800; margin-bottom: 6px; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-calendar-days"></i> <span class="lang-vi">LỊCH SỰ KIỆN B2B</span>
                    </span>
                    <h2 class="section-title" style="font-size: 1.9rem; font-weight: 900; color: #0f172a; margin: 0;">
                        <span class="lang-vi">Hội Thảo & Xúc Tiến Thương Mại</span>
                    </h2>
                </div>
                <a href="<?= base_url('events'); ?>" class="btn btn-outline" style="border-radius: 8px; font-weight: 700; font-size: 0.85rem;">Xem Tất Cả Sự Kiện <i class="fa-solid fa-arrow-right"></i></a>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(310px, 1fr)); gap: 22px;">
                <?php foreach ($featuredEvents as $ev): 
                    $timestamp = strtotime($ev->event_date);
                    $day = date('d', $timestamp);
                    $month = 'THÁNG ' . date('m', $timestamp);
                ?>
                    <div class="card-corporate" style="background: #ffffff; border-radius: 14px; border: 1.5px solid #e2e8f0; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 4px 16px rgba(0,0,0,0.03);">
                        <div style="position: relative; aspect-ratio: 16/9; overflow: hidden;">
                            <img src="<?= esc($ev->image); ?>" alt="<?= esc($ev->title); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <div style="position: absolute; top: 10px; left: 10px; background: #0f172a; color: #fff; border-radius: 8px; padding: 5px 10px; text-align: center;">
                                <span style="font-size: 1.2rem; font-weight: 900; line-height: 1; display: block;"><?= $day; ?></span>
                                <span style="font-size: 0.62rem; font-weight: 700; color: #38bdf8; text-transform: uppercase;"><?= $month; ?></span>
                            </div>
                            <span style="position: absolute; top: 10px; right: 10px; background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; font-size: 0.7rem; font-weight: 800; padding: 3px 8px; border-radius: 15px;"><i class="fa-solid fa-circle-check"></i> SẮP DIỄN RA</span>
                        </div>
                        <div style="padding: 18px; display: flex; flex-direction: column; flex: 1;">
                            <div style="font-size: 0.74rem; color: #64748b; font-weight: 600; margin-bottom: 6px;"><i class="fa-solid fa-clock text-primary"></i> <?= esc($ev->event_time); ?></div>
                            <h3 style="font-size: 0.98rem; font-weight: 800; color: #0f172a; margin: 0 0 8px; line-height: 1.4;">
                                <a href="<?= base_url('events/' . $ev->slug); ?>" style="color: inherit; text-decoration: none;"><?= esc($ev->title); ?></a>
                            </h3>
                            <div style="font-size: 0.76rem; color: #475569; display: flex; align-items: flex-start; gap: 6px; margin-bottom: 12px;">
                                <i class="fa-solid fa-location-dot" style="color: #ef4444; margin-top: 2px;"></i>
                                <span style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?= esc($ev->location); ?></span>
                            </div>
                            <div style="margin-top: auto; padding-top: 12px; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 0.72rem; color: #16a34a; font-weight: 700;"><i class="fa-solid fa-users"></i> <?= $ev->registered_count; ?>/<?= $ev->max_seats; ?> chỗ</span>
                                <a href="<?= base_url('events/' . $ev->slug); ?>" class="btn btn-primary btn-sm" style="border-radius: 6px; font-weight: 700; font-size: 0.76rem; padding: 6px 12px;">Đăng ký vé <i class="fa-solid fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Strategic Partners Marquee (Mạng Lưới Đối Tác Toàn Cầu) -->
    <section class="partners-marquee-section" style="padding: 55px 0 65px; background: linear-gradient(135deg, #070e1f 0%, #0c1c3f 50%, #070e1f 100%); overflow: hidden; border-top: 2px solid #2563eb; border-bottom: 2px solid #2563eb; position: relative;">
        <div class="container text-center" style="margin-bottom: 30px;">
            <span class="badge" style="background: rgba(59,130,246,0.15); border: 1.5px solid rgba(59,130,246,0.4); color: #93c5fd; margin-bottom: 10px; font-weight: 800;">
                <i class="fa-solid fa-handshake text-primary"></i> <span class="lang-vi">Mạng Lưới Đối Tác Toàn Cầu</span><span class="lang-en">Global Partner Network</span>
            </span>
            <h2 class="section-title" style="color: #ffffff !important; font-size: 1.8rem; font-weight: 900; margin-bottom: 6px;">
                <span class="lang-vi">Đồng Hành Cùng 20+ Hãng Tàu & Hãng Hàng Không Quốc Tế</span>
            </h2>
        </div>
        
        <?php 
        $t1 = [['icon'=>'fa-ship','color'=>'#2563eb','name'=>'MAERSK'],['icon'=>'fa-anchor','color'=>'#f59e0b','name'=>'MSC'],['icon'=>'fa-boxes-stacked','color'=>'#e11d48','name'=>'CMA CGM'],['icon'=>'fa-box-archive','color'=>'#10b981','name'=>'COSCO'],['icon'=>'fa-ship','color'=>'#059669','name'=>'EVERGREEN'],['icon'=>'fa-cubes','color'=>'#ec4899','name'=>'ONE LINE'],['icon'=>'fa-ship','color'=>'#ea580c','name'=>'HAPAG-LLOYD'],['icon'=>'fa-anchor','color'=>'#6366f1','name'=>'YANG MING']];
        $t2 = [['icon'=>'fa-plane','color'=>'#0284c7','name'=>'VN AIRLINES'],['icon'=>'fa-plane-departure','color'=>'#d97706','name'=>'DHL AIR'],['icon'=>'fa-plane','color'=>'#7c3aed','name'=>'FEDEX'],['icon'=>'fa-plane-arrival','color'=>'#059669','name'=>'SINGAPORE AIR'],['icon'=>'fa-plane','color'=>'#dc2626','name'=>'EMIRATES'],['icon'=>'fa-plane-departure','color'=>'#2563eb','name'=>'KOREAN AIR'],['icon'=>'fa-plane','color'=>'#0891b2','name'=>'CATHAY'],['icon'=>'fa-plane-arrival','color'=>'#9333ea','name'=>'QATAR AIRWAYS']];
        ?>
        <div class="marquee-wrapper" style="overflow: hidden; width: 100%; display: flex; margin-bottom: 18px;">
            <div class="marquee-track" style="display: flex; gap: 16px; width: max-content; animation: marqueeL 32s linear infinite;">
                <?php foreach (array_merge($t1, $t1) as $p): ?>
                    <div style="background: #ffffff; width: 130px; height: 130px; border-radius: 16px; border: 2px solid #2563eb; color: #0f172a; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; flex-shrink: 0; padding: 10px; text-align: center;">
                        <i class="fa-solid <?= $p['icon']; ?>" style="color: <?= $p['color']; ?>; font-size: 2rem;"></i>
                        <span style="font-weight: 800; font-size: 0.76rem; letter-spacing: 0.5px;"><?= $p['name']; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="marquee-wrapper" style="overflow: hidden; width: 100%; display: flex;">
            <div class="marquee-track" style="display: flex; gap: 16px; width: max-content; animation: marqueeR 32s linear infinite;">
                <?php foreach (array_merge($t2, $t2) as $p): ?>
                    <div style="background: #ffffff; width: 130px; height: 130px; border-radius: 16px; border: 2px solid #2563eb; color: #0f172a; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; flex-shrink: 0; padding: 10px; text-align: center;">
                        <i class="fa-solid <?= $p['icon']; ?>" style="color: <?= $p['color']; ?>; font-size: 2rem;"></i>
                        <span style="font-weight: 800; font-size: 0.76rem; letter-spacing: 0.5px;"><?= $p['name']; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
