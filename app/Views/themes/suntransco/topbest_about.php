<!-- TOP BEST LÀ GÌ - Comprehensive Single Page Guide with Sticky Table of Contents -->
<div class="py-5" style="background-color: #F8FAFC;">
    <div class="container">
        
        <!-- Hero Header -->
        <div class="text-center mb-5 p-5 rounded-lg text-white" style="background: linear-gradient(135deg, #0A192F 0%, #1A4C96 100%); border-bottom: 3px solid #D9A441; border-radius: 16px;">
            <span class="badge badge-warning px-3 py-1 font-weight-bold mb-3" style="background: #D9A441; color: #0A192F;">
                HỘI KỶ LỤC VIỆT NAM (VIETKINGS) × GAA × TOLUCK
            </span>
            <h1 class="font-serif text-white mb-3" style="font-size: 2.2rem;">TOP BEST GLOBAL Là Gì?</h1>
            <p class="mx-auto" style="max-width: 750px; font-size: 1rem; color: #E2E8F0; line-height: 1.6;">
                Cổng thông tin và Bảng vàng vinh danh chuẩn mực quốc gia tôn vinh các thương hiệu, sản phẩm và dịch vụ di sản tiêu biểu của Việt Nam vươn tầm thế giới.
            </p>
        </div>

        <div class="row">
            <!-- Sticky Table of Contents (Mục Lục Dính Bên Trái) -->
            <div class="col-lg-3 d-none d-lg-block">
                <div class="sticky-top p-3 bg-white rounded shadow-sm border" style="top: 90px; font-size: 0.82rem; line-height: 1.8;">
                    <h6 class="font-serif font-weight-bold mb-3 text-primary"><i class="fa-solid fa-list-ol mr-2"></i> Mục Lục Nội Dung</h6>
                    <ul class="list-unstyled mb-0">
                        <li><a href="#sec-1" class="text-dark"><i class="fa-solid fa-angle-right text-warning mr-1"></i> 1. Vấn đề đang giải quyết</a></li>
                        <li><a href="#sec-2" class="text-dark"><i class="fa-solid fa-angle-right text-warning mr-1"></i> 2. Định nghĩa & câu chuyện</a></li>
                        <li><a href="#sec-3" class="text-dark"><i class="fa-solid fa-angle-right text-warning mr-1"></i> 3. Cơ chế TOP / BEST</a></li>
                        <li><a href="#sec-4" class="text-dark"><i class="fa-solid fa-angle-right text-warning mr-1"></i> 4. Ba cấp độ đăng ký</a></li>
                        <li><a href="#sec-5" class="text-dark"><i class="fa-solid fa-angle-right text-warning mr-1"></i> 5. Hệ sinh thái pháp lý</a></li>
                        <li><a href="#sec-6" class="text-dark"><i class="fa-solid fa-angle-right text-warning mr-1"></i> 6. Bảng xếp hạng nổi bật</a></li>
                        <li><a href="#sec-7" class="text-dark"><i class="fa-solid fa-angle-right text-warning mr-1"></i> 7. Lộ trình 12 tháng</a></li>
                        <li><a href="#sec-8" class="text-dark"><i class="fa-solid fa-angle-right text-warning mr-1"></i> 8. Sự kiện vinh danh</a></li>
                        <li><a href="#sec-9" class="text-dark"><i class="fa-solid fa-angle-right text-warning mr-1"></i> 9. Dành cho Đại lý</a></li>
                        <li><a href="#sec-10" class="text-dark"><i class="fa-solid fa-angle-right text-warning mr-1"></i> 10. Câu hỏi thường gặp FAQ</a></li>
                    </ul>
                </div>
            </div>

            <!-- Content Column (10 Sections) -->
            <div class="col-lg-9">
                
                <!-- 1. Vấn đề đang giải quyết -->
                <section id="sec-1" class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 14px;">
                    <h3 class="font-serif text-primary mb-3" style="font-size: 1.35rem;">1. Vấn Đề Đang Giải Quyết</h3>
                    <div class="p-3 mb-3 rounded" style="background: #FEF3C7; border-left: 4px solid #D9A441; font-weight: 600; color: #92400E;">
                        "Khách hàng chưa gặp bạn bao giờ, họ dựa vào đâu để tin?"
                    </div>
                    <p style="font-size: 0.9rem; color: #475569;">
                        Trên trường quốc tế, niềm tin thương hiệu luôn gắn liền với các bảo chứng tiêu chuẩn đã được định danh lịch sử:
                    </p>
                    <div class="row mb-3">
                        <div class="col-md-6 mb-2"><strong>• Phố Wall:</strong> Tiêu chuẩn tài chính chứng khoán toàn cầu.</div>
                        <div class="col-md-6 mb-2"><strong>• McDonald's:</strong> Chuẩn mực nhượng quyền thức ăn nhanh.</div>
                        <div class="col-md-6 mb-2"><strong>• Nước hoa Pháp:</strong> Biểu tượng nghệ thuật hương thơm di sản.</div>
                        <div class="col-md-6 mb-2"><strong>• Đồng hồ Thụy Sỹ:</strong> Đỉnh cao cơ khí chính xác thế giới.</div>
                    </div>
                    <h6 class="font-weight-bold mb-2">8 Nhóm Ngành Trọng Điểm TOP BEST Hướng Tới:</h6>
                    <div class="row">
                        <?php foreach ($industries as $ind): ?>
                            <div class="col-md-6 col-lg-3 mb-2">
                                <div class="p-2 border rounded text-center" style="background: #F8FAFC; font-size: 0.78rem; font-weight: 700;">
                                    <i class="fa-solid <?= esc($ind['icon']); ?> text-warning mr-1"></i> <?= esc($ind['name']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- 2. Định nghĩa & Câu chuyện -->
                <section id="sec-2" class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 14px;">
                    <h3 class="font-serif text-primary mb-3" style="font-size: 1.35rem;">2. Định Nghĩa & Câu Chuyện TOP BEST</h3>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="p-3 border rounded h-100" style="background: #F8FAFC;">
                                <div class="badge badge-primary px-2 py-1 mb-2">Điểm 1</div>
                                <h6 class="font-weight-bold">Giải Thưởng Thuộc VietKings & WORLDKINGS</h6>
                                <p class="small text-muted mb-0">Hạng mục giải thưởng chính thức thuộc Hội Kỷ lục Việt Nam, thành viên Liên minh Kỷ lục Thế giới.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="p-3 border rounded h-100" style="background: #F8FAFC;">
                                <div class="badge badge-primary px-2 py-1 mb-2">Điểm 2</div>
                                <h6 class="font-weight-bold">Tập Hợp Chứng Chỉ & Thẩm Định Độc Lập</h6>
                                <p class="small text-muted mb-0">Hệ thống hoá hồ sơ năng lực, bằng khen và chứng nhận chất lượng để Hội đồng Giám khảo thẩm định.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="p-3 border rounded h-100" style="background: #F8FAFC;">
                                <div class="badge badge-primary px-2 py-1 mb-2">Điểm 3</div>
                                <h6 class="font-weight-bold">Công Nhận = Truyền Thông Toàn Cầu</h6>
                                <p class="small text-muted mb-0">Được truyền thông rộng rãi trên mạng lưới WorldKings, tạo lợi thế đàm phán xuất khẩu.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="p-3 border rounded h-100" style="background: #F8FAFC;">
                                <div class="badge badge-primary px-2 py-1 mb-2">Điểm 4</div>
                                <h6 class="font-weight-bold">Ưu Tiên Tiêu Dùng Hàng Xuất Khẩu</h6>
                                <p class="small text-muted mb-0">Đáp ứng xu hướng người tiêu dùng trong nước ưu tiên lựa chọn các sản phẩm đạt tiêu chuẩn xuất khẩu.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- 3. Cơ chế TOP / BEST -->
                <section id="sec-3" class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 14px;">
                    <h3 class="font-serif text-primary mb-3" style="font-size: 1.35rem;">3. Cơ Chế Phân Hạng TOP / BEST</h3>
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <div class="card-best p-3 rounded h-100 text-white" style="background: #0A192F;">
                                <span class="badge badge-warning px-2 py-1 mb-2" style="background: #D9A441; color: #0A192F; font-weight: 800;">HẠNG 1 – 10 (BEST)</span>
                                <h5 class="font-serif text-warning">Hạng BEST Quốc Gia</h5>
                                <p class="small" style="color: #E2E8F0;">Do Viện Kỷ lục Việt Nam và Hội đồng Thẩm định trực tiếp xét chọn theo hồ sơ di sản và quy chuẩn quốc tế.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card-top p-3 rounded h-100 bg-white">
                                <span class="badge badge-primary px-2 py-1 mb-2" style="background: #1A4C96;">HẠNG 11 – 100 (TOP)</span>
                                <h5 class="font-serif text-primary">Hạng TOP Khu Vực</h5>
                                <p class="small text-muted">Dành cho các đơn vị tiêu biểu đạt chuẩn kỹ thuật, kết hợp bình chọn minh bạch của cộng đồng.</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-3 rounded text-center" style="background: #EFF6FF; border: 2px dashed #1A4C96;">
                        <span class="text-muted small font-weight-bold d-block mb-1">CÔNG THỨC ĐẶT TÊN HỒ SƠ CHUẨN HOÁ:</span>
                        <code style="font-size: 1rem; color: #1A4C96; font-weight: 800;">
                            TOP + [Tên sản phẩm, dịch vụ] trong [Ngành/Lĩnh vực] tại [Địa phương]
                        </code>
                    </div>
                </section>

                <!-- 4. Ba cấp độ đăng ký -->
                <section id="sec-4" class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 14px;">
                    <h3 class="font-serif text-primary mb-3" style="font-size: 1.35rem;">4. Ba Cấp Độ Đăng Ký Đề Cử</h3>
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <div class="p-3 border rounded text-center h-100 bg-white">
                                <i class="fa-solid fa-crown text-warning fa-2x mb-2"></i>
                                <h6 class="font-weight-bold">Cấp Độ Thương Hiệu</h6>
                                <p class="small text-muted mb-0">Tôn vinh tên tuổi, uy tín doanh nghiệp và giá trị pháp nhân.</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="p-3 border rounded text-center h-100 bg-white">
                                <i class="fa-solid fa-bell-concierge text-primary fa-2x mb-2"></i>
                                <h6 class="font-weight-bold">Cấp Độ Dịch Vụ</h6>
                                <p class="small text-muted mb-0">Tôn vinh quy trình trải nghiệm, sự hài lòng và chất lượng phục vụ.</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="p-3 border rounded text-center h-100 bg-white">
                                <i class="fa-solid fa-box-open text-success fa-2x mb-2"></i>
                                <h6 class="font-weight-bold">Cấp Độ Sản Phẩm</h6>
                                <p class="small text-muted mb-0">Tôn vinh chất lượng hiện vật, chỉ số an toàn và đóng gói xuất khẩu.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- 5. Hệ sinh thái pháp lý -->
                <section id="sec-5" class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 14px;">
                    <h3 class="font-serif text-primary mb-3" style="font-size: 1.35rem;">5. Hệ Sinh Thái Pháp Lý (VietKings × GAA × TOLUCK)</h3>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="p-3 border rounded h-100 bg-light">
                                <h6 class="font-weight-bold text-primary">VietKings & WORLDKINGS</h6>
                                <p class="small text-muted mb-0">Giấy phép 959/QĐ-BNV (2013). Thành viên WORLDKINGS cùng US Kings, IBR; sáng lập IWRHA; bảo trợ bởi WRI.</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="p-3 border rounded h-100 bg-light">
                                <h6 class="font-weight-bold text-primary">Global American Academy (GAA)</h6>
                                <p class="small text-muted mb-0">Học viện uy tín do TS. Trần Thị Hồng Duyên làm Chủ tịch, bảo trợ chuyên môn và hội đồng cố vấn quốc tế.</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="p-3 border rounded h-100 bg-light">
                                <h6 class="font-weight-bold text-primary">TOLUCK</h6>
                                <p class="small text-muted mb-0">Đơn vị uỷ thác triển khai toàn diện nền tảng số hoá, sáng lập bởi ông Trần Kim Hưng.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- 6. Bảng xếp hạng preview -->
                <section id="sec-6" class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 14px;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="font-serif text-primary mb-0" style="font-size: 1.35rem;">6. Bảng Xếp Hạng Tiêu Biểu</h3>
                        <a href="<?= langBaseUrl('bang-xep-hang'); ?>" class="btn btn-sm btn-outline-primary font-weight-bold">Xem Toàn Bộ <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                    <div class="row">
                        <?php if (!empty($directoryPreviews)): ?>
                            <?php foreach ($directoryPreviews as $p): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="p-3 border rounded d-flex align-items-center justify-content-between bg-white <?= (!empty($p['rank_tier']) && $p['rank_tier'] === 'BEST') ? 'card-best' : ''; ?>">
                                        <div>
                                            <span class="badge <?= (!empty($p['rank_tier']) && $p['rank_tier'] === 'BEST') ? 'badge-warning' : 'badge-primary'; ?> mb-1">
                                                <?= esc($p['rank_tier'] ?? 'TOP'); ?> #<?= esc($p['rank_number'] ?? '1'); ?>
                                            </span>
                                            <h6 class="font-weight-bold mb-0" style="font-size: 0.88rem;"><?= esc($p['name'] ?? ''); ?></h6>
                                            <small class="text-muted"><?= esc($p['province'] ?? 'Toàn quốc'); ?> • <?= esc($p['category_name'] ?? 'Đa ngành'); ?></small>
                                        </div>
                                        <a href="<?= langBaseUrl('ho-so/' . ($p['code'] ?? '')); ?>" class="btn btn-sm btn-light">Hồ sơ</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- 7. Lộ trình 12 tháng -->
                <section id="sec-7" class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 14px;">
                    <h3 class="font-serif text-primary mb-3" style="font-size: 1.35rem;">7. Lộ Trình Triển Khai 12 Tháng</h3>
                    <div class="row text-center">
                        <div class="col-md-4 mb-2">
                            <div class="p-3 rounded h-100" style="background: #F1F5F9;">
                                <span class="badge badge-primary px-2 py-1 mb-2">Giai Đoạn 1</span>
                                <h6 class="font-weight-bold mb-1">T8 – T10/2026</h6>
                                <p class="small text-muted mb-0">Thiết lập 10 Đại lý tiên phong, tổ chức chuỗi Workshop đào tạo chuẩn hoá toàn quốc.</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="p-3 rounded h-100" style="background: #F1F5F9;">
                                <span class="badge badge-primary px-2 py-1 mb-2">Giai Đoạn 2</span>
                                <h6 class="font-weight-bold mb-1">T10/2026 – T5/2027</h6>
                                <p class="small text-muted mb-0">Mở rộng mạng lưới 5 tỉnh trọng điểm, tiếp nhận và thẩm định 500+ hồ sơ.</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="p-3 rounded h-100" style="background: #FEF3C7;">
                                <span class="badge badge-warning px-2 py-1 mb-2" style="background: #D9A441; color: #0A192F;">Giai Đoạn 3</span>
                                <h6 class="font-weight-bold mb-1">T6 – T11/2027</h6>
                                <p class="small text-muted mb-0">Ngày hội Gia đình VN (28/6) & Đại lễ Gala Vinh danh toàn quốc (20/11).</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- 8. Sự kiện vinh danh -->
                <section id="sec-8" class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 14px;">
                    <h3 class="font-serif text-primary mb-3" style="font-size: 1.35rem;">8. Chuỗi Sự Kiện Vinh Danh</h3>
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <div class="p-3 border rounded text-center h-100 bg-white">
                                <i class="fa-solid fa-list-check text-primary fa-2x mb-2"></i>
                                <h6 class="font-weight-bold">Trụ Cột 1: Đánh Giá</h6>
                                <p class="small text-muted mb-0">Hội đồng thẩm định độc lập theo bộ tiêu chuẩn 4 vòng nghiêm ngặt.</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="p-3 border rounded text-center h-100 bg-white">
                                <i class="fa-solid fa-trophy text-warning fa-2x mb-2"></i>
                                <h6 class="font-weight-bold">Trụ Cột 2: Sự Kiện</h6>
                                <p class="small text-muted mb-0">4 Sự kiện Quý/năm kết hợp Đại lễ Gala Vinh danh thường niên.</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="p-3 border rounded text-center h-100 bg-white">
                                <i class="fa-solid fa-handshake text-success fa-2x mb-2"></i>
                                <h6 class="font-weight-bold">Trụ Cột 3: Đồng Hành</h6>
                                <p class="small text-muted mb-0">Hỗ trợ truyền thông quốc tế, kết nối giao thương và xúc tiến xuất khẩu.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- 9. Dành cho Đại lý (Banner Dẫn Sang Trang Riêng) -->
                <section id="sec-9" class="card border-0 shadow-sm p-4 mb-4 text-white text-center" style="border-radius: 14px; background: linear-gradient(135deg, #1A4C96 0%, #0A192F 100%);">
                    <h3 class="font-serif text-white mb-2" style="font-size: 1.35rem;">9. Dành Cho Đại Lý & Đối Tác Phát Triển Thị Trường</h3>
                    <p style="font-size: 0.9rem; color: #CBD5E1; max-width: 650px; margin: 0 auto 20px;">
                        Gia nhập mạng lưới phát triển thương hiệu di sản quốc gia với cơ chế chính sách độc quyền và hợp đồng hợp tác minh bạch.
                    </p>
                    <div>
                        <a href="<?= langBaseUrl('dai-ly'); ?>" class="btn btn-tbg-cta px-4 py-2">
                            Xem Chính Sách & Đăng Ký Đại Lý <i class="fa-solid fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </section>

                <!-- 10. FAQ Accordion -->
                <section id="sec-10" class="card border-0 shadow-sm p-4" style="border-radius: 14px;">
                    <h3 class="font-serif text-primary mb-3" style="font-size: 1.35rem;">10. Câu Hỏi Thường Gặp (FAQ)</h3>
                    <div class="accordion" id="faqAcc">
                        <?php foreach ($faqs as $i => $faq): ?>
                            <div class="card border mb-2">
                                <div class="card-header bg-white py-3" id="heading<?= $i; ?>">
                                    <h6 class="mb-0">
                                        <button class="btn btn-link text-dark font-weight-bold text-left p-0 btn-block" type="button" data-toggle="collapse" data-target="#collapse<?= $i; ?>">
                                            <i class="fa-solid fa-circle-question text-warning mr-2"></i> <?= esc($faq['q']); ?>
                                        </button>
                                    </h6>
                                </div>
                                <div id="collapse<?= $i; ?>" class="collapse <?= $i === 0 ? 'show' : ''; ?>" data-parent="#faqAcc">
                                    <div class="card-body py-3 text-muted small" style="line-height: 1.6;">
                                        <?= esc($faq['a']); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>

            </div>
        </div>
    </div>
</div>
