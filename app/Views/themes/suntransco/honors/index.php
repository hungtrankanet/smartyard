<!-- TOP BEST GLOBAL - National Honors & Awards Portal Landing -->
<section class="tbg-honors-hero py-5" style="background: linear-gradient(135deg, #0A192F 0%, #0d2342 50%, #1e293b 100%); position: relative; overflow: hidden; border-bottom: 3px solid #D4AF37;">
    <div class="container py-4 position-relative" style="z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-8 text-white">
                <div class="d-inline-flex align-items-center mb-3 px-3 py-1 rounded-pill" style="background: rgba(212,175,55,0.15); border: 1px solid #D4AF37;">
                    <i class="fa fa-trophy text-warning mr-2"></i>
                    <span style="color: #F3E5AB; font-weight: 800; font-size: 12px; letter-spacing: 1.5px; text-transform: uppercase;">
                        CỔNG BẢNG VÀNG VINH DANH QUỐC GIA <?= esc($activeSeason->theme_year ?? 2026); ?>
                    </span>
                </div>
                <h1 class="font-weight-bold display-4 mb-3" style="color: #ffffff; text-shadow: 0 2px 12px rgba(0,0,0,0.6); line-height: 1.2;">
                    Tôn Vinh Thương Hiệu & <br><span style="background: linear-gradient(135deg, #F3E5AB 0%, #D4AF37 50%, #AA771C 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Lãnh Đạo Xuất Sắc Việt Nam</span>
                </h1>
                <p class="lead mb-4 text-light" style="font-size: 16px; max-width: 680px; opacity: 0.92; line-height: 26px;">
                    Nền tảng vinh danh quốc gia quy tụ 15+ lĩnh vực kinh tế - xã hội, ứng dụng cơ chế thẩm định chuẩn hóa: 
                    <strong style="color: #F3E5AB;">70% Điểm Hội Đồng Giám Khảo</strong> + <strong style="color: #F3E5AB;">30% Điểm Bình Chọn Độc Giả</strong>.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="<?= langBaseUrl('voting'); ?>" class="btn btn-warning font-weight-bold px-4 py-3 mr-3" style="background: linear-gradient(135deg, #D4AF37 0%, #AA771C 100%); color: #0A192F; border: none; border-radius: 10px; box-shadow: 0 4px 15px rgba(212,175,55,0.4);">
                        <i class="fa fa-check-circle mr-1"></i> THAM GIA BÌNH CHỌN
                    </a>
                    <a href="<?= langBaseUrl('nomination/apply'); ?>" class="btn btn-outline-light px-4 py-3 font-weight-bold mr-3" style="border-radius: 10px; border-color: rgba(255,255,255,0.5);">
                        <i class="fa fa-file-text mr-1"></i> Nộp Hồ Sơ Đề Cử
                    </a>
                    <a href="<?= langBaseUrl('hall-of-fame'); ?>" class="btn btn-outline-warning px-4 py-3 font-weight-bold" style="border-radius: 10px; border-color: #D4AF37; color: #F3E5AB;">
                        <i class="fa fa-trophy mr-1"></i> Bảng Vàng Vinh Danh
                    </a>
                </div>
            </div>
            <div class="col-lg-4 d-none d-lg-block text-center">
                <div class="p-4 rounded-lg" style="background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); border: 2px solid rgba(212,175,55,0.4); border-radius: 20px;">
                    <div style="width: 90px; height: 90px; margin: 0 auto 15px; border-radius: 50%; background: linear-gradient(135deg, #D4AF37 0%, #AA771C 100%); display: flex; align-items: center; justify-content: center; color: #0A192F; font-size: 40px; box-shadow: 0 0 25px rgba(212,175,55,0.5);">
                        <i class="fa fa-shield"></i>
                    </div>
                    <h5 class="text-white font-weight-bold mb-2">Quy Chuẩn Quốc Gia</h5>
                    <p class="small text-light mb-0" style="opacity: 0.85; line-height: 20px;">
                        Quy trình thẩm định 4 giai đoạn độc lập, chứng nhận số tích hợp mã băm mật mã và con dấu số điện tử.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 15+ Industry Sectors & Categories Grid -->
<section class="py-5" style="background: #f8fafc;">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge px-3 py-1 mb-2" style="background: rgba(212,175,55,0.15); border: 1px solid #D4AF37; color: #b45309; font-weight: 800; font-size: 11px; text-transform: uppercase;">
                LĨNH VỰC XÉT THƯỞNG 2026
            </span>
            <h2 class="font-weight-bold" style="color: #0A192F; font-size: 2rem;">
                Hệ Thống Danh Mục Giải Thưởng Toàn Quốc
            </h2>
            <p class="text-muted mx-auto" style="max-width: 650px; font-size: 15px;">
                Được bảo trợ bởi Hội đồng Chuyên gia Thẩm định độc lập và các hiệp hội ngành nghề hàng đầu.
            </p>
        </div>

        <div class="row">
            <?php if (!empty($categories)): foreach ($categories as $cat): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 14px; border: 1px solid #e2e8f0; transition: transform 0.2s, box-shadow 0.2s;">
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #0A192F 0%, #1e3a8a 100%); color: #D4AF37; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                        <i class="<?= esc($cat->icon ?? 'fa fa-award'); ?>"></i>
                                    </div>
                                    <span class="badge" style="background: #f1f5f9; color: #475569; font-weight: 700; font-size: 11px; border: 1px solid #cbd5e1; border-radius: 20px; padding: 4px 10px;">
                                        <?= esc($cat->industry_sector ?? 'Đa Ngành'); ?>
                                    </span>
                                </div>
                                <h5 class="card-title font-weight-bold mb-2" style="color: #0A192F; font-size: 17px;">
                                    <a href="<?= langBaseUrl('honors/category/' . $cat->slug); ?>" class="text-dark text-decoration-none">
                                        <?= esc($cat->name); ?>
                                    </a>
                                </h5>
                                <p class="small text-muted mb-3" style="line-height: 20px;">
                                    <?= esc($cat->description ?? 'Giải thưởng tôn vinh các thành tựu vượt bậc trong chuyển đổi số và phát triển bền vững.'); ?>
                                </p>
                            </div>
                            <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                                <span class="small text-muted"><strong><?= $cat->jury_weight ?? 70; ?>%</strong> Giám khảo • <strong><?= $cat->public_weight ?? 30; ?>%</strong> Độc giả</span>
                                <a href="<?= langBaseUrl('voting/category/' . $cat->slug); ?>" class="btn btn-sm btn-outline-primary font-weight-bold" style="border-radius: 6px;">
                                    Bình chọn <i class="fa fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </div>

        <div class="text-center mt-4">
            <a href="<?= langBaseUrl('honors/categories'); ?>" class="btn btn-primary px-4 py-2 font-weight-bold" style="border-radius: 8px;">
                Xem Toàn Bộ Danh Mục Giải Thưởng <i class="fa fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
</section>

<!-- 70/30 Hybrid Scoring & 4-Stage Workflow -->
<section class="py-5" style="background: linear-gradient(135deg, #0A192F 0%, #0d2342 100%); color: #ffffff;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <span class="badge px-3 py-1 mb-2" style="background: rgba(212,175,55,0.2); border: 1px solid #D4AF37; color: #F3E5AB; font-weight: 800; font-size: 11px;">
                    CƠ CHẾ CHẤM ĐIỂM MINH BẠCH
                </span>
                <h2 class="font-weight-bold mb-3" style="color: #ffffff; font-size: 2.2rem;">
                    Công Thức Điểm Tổng Hợp 70/30 Độc Quyền
                </h2>
                <p class="text-light lead mb-4" style="font-size: 15px; opacity: 0.9; line-height: 24px;">
                    Hệ thống loại bỏ hoàn toàn tình trạng thao túng hay gian lận bằng cách kết hợp thẩm định chuyên môn độc lập của Hội đồng Chuyên gia với lượt bình chọn xác thực bằng mã OTP Email của cộng đồng.
                </p>
                <div class="p-3 mb-3 rounded" style="background: rgba(255,255,255,0.05); border-left: 4px solid #D4AF37;">
                    <h6 class="font-weight-bold text-warning mb-1">70% Điểm Hội Đồng Giám Khảo (Standardized Rubric)</h6>
                    <p class="small text-light mb-0" style="opacity: 0.85;">Đổi mới sáng tạo (25%) + Năng lực kinh doanh (30%) + ESG & Xã hội (25%) + Uy tín quản trị (20%).</p>
                </div>
                <div class="p-3 rounded" style="background: rgba(255,255,255,0.05); border-left: 4px solid #38bdf8;">
                    <h6 class="font-weight-bold mb-1" style="color: #38bdf8;">30% Điểm Bình Chọn Độc Giả (Verified Public Votes)</h6>
                    <p class="small text-light mb-0" style="opacity: 0.85;">Xác thực mã OTP Email, chống spam IP, ghi nhận Audit Trail theo thời gian thực.</p>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="p-4 rounded-lg" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(212,175,55,0.3); border-radius: 20px;">
                    <h5 class="font-weight-bold mb-4" style="color: #F3E5AB;">Quy Trình 4 Giai Đoạn Xét Duyệt</h5>
                    <div class="d-flex flex-column gap-3 text-left">
                        <div class="d-flex align-items-center p-3 rounded" style="background: rgba(255,255,255,0.06);">
                            <div class="mr-3 text-warning font-weight-bold h4 mb-0">01</div>
                            <div>
                                <strong class="text-white d-block">Vòng Sơ Khảo (Preliminary Review)</strong>
                                <small class="text-light" style="opacity:0.75;">Tiếp nhận và kiểm tra tính hợp lệ của hồ sơ đề cử doanh nghiệp.</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center p-3 rounded" style="background: rgba(255,255,255,0.06);">
                            <div class="mr-3 text-warning font-weight-bold h4 mb-0">02</div>
                            <div>
                                <strong class="text-white d-block">Vòng Thẩm Định Chuyên Môn (Expert Appraisal)</strong>
                                <small class="text-light" style="opacity:0.75;">Hội đồng Giám khảo chấm điểm độc lập theo thang chuẩn 100 điểm.</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center p-3 rounded" style="background: rgba(255,255,255,0.06);">
                            <div class="mr-3 text-warning font-weight-bold h4 mb-0">03</div>
                            <div>
                                <strong class="text-white d-block">Vòng Bình Chọn & Chung Khảo (Finalist Voting)</strong>
                                <small class="text-light" style="opacity:0.75;">Mở cổng bình chọn cộng đồng và tính toán điểm tổng hợp 70/30.</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center p-3 rounded" style="background: rgba(255,255,255,0.06);">
                            <div class="mr-3 text-warning font-weight-bold h4 mb-0">04</div>
                            <div>
                                <strong class="text-white d-block">Lễ Trao Giải & Bảng Vàng (Gala & Awarding)</strong>
                                <small class="text-light" style="opacity:0.75;">Công bố quán quân, trao cúp vàng và cấp con dấu số hóa vĩnh viễn.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
