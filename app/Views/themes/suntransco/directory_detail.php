<!-- Public Entity Profile Detail -->
<div class="py-5" style="background-color: #F8FAFC;">
    <div class="container">
        
        <!-- Profile Header Card -->
        <div class="card border-0 shadow-sm overflow-hidden mb-4" style="border-radius: 16px; background: #ffffff;">
            <div style="height: 220px; background: url('<?= base_url($profile['banner']); ?>') center center / cover no-repeat; position: relative;">
                <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(10,25,47,0.2) 0%, rgba(10,25,47,0.85) 100%);"></div>
                <div style="position: absolute; top: 20px; right: 20px;">
                    <span class="badge <?= $profile['rank_tier'] === 'BEST' ? 'badge-diamond' : 'badge-ruby'; ?> px-3 py-2 font-weight-bold" style="font-size: 0.85rem;">
                        <i class="fa-solid fa-award mr-1"></i> HUY HIỆU <?= strtoupper($profile['badge_type']); ?>
                    </span>
                </div>
            </div>

            <div class="card-body p-4" style="position: relative; margin-top: -60px;">
                <div class="row align-items-end">
                    <div class="col-md-2 text-center text-md-left mb-3 mb-md-0">
                        <div class="d-inline-block p-2 bg-white rounded shadow" style="width: 110px; height: 110px;">
                            <img src="<?= base_url($profile['logo']); ?>" alt="<?= esc($profile['name']); ?>" class="img-fluid rounded" style="width: 100%; height: 100%; object-fit: contain;">
                        </div>
                    </div>
                    <div class="col-md-7 mb-3 mb-md-0">
                        <div class="d-flex flex-wrap align-items-center mb-1" style="gap: 8px;">
                            <span class="badge <?= $profile['rank_tier'] === 'BEST' ? 'badge-warning' : 'badge-primary'; ?> font-weight-bold px-2 py-1">
                                <?= $profile['rank_tier']; ?> #<?= $profile['rank_number']; ?>
                            </span>
                            <span class="badge badge-light px-2 py-1 border text-muted">Mã: <?= esc($profile['code']); ?></span>
                            <span class="badge badge-light px-2 py-1 border text-muted"><i class="fa-solid fa-location-dot text-danger mr-1"></i> <?= esc($profile['province']); ?></span>
                        </div>
                        <h2 class="font-serif mb-1" style="font-size: 1.45rem; color: #0A192F; font-weight: 800;">
                            <?= esc($profile['title_formula']); ?>
                        </h2>
                        <small class="text-muted">
                            <i class="fa-regular fa-calendar-check mr-1 text-success"></i> Chu kỳ thẩm định: <strong>6 tháng</strong> • Cập nhật gần nhất: <?= esc($profile['last_updated']); ?> (Hiệu lực đến: <?= esc($profile['valid_until']); ?>)
                        </small>
                    </div>
                    <div class="col-md-3 text-md-right">
                        <?php if (!empty($profile['ecommerce_url'])): ?>
                            <a href="<?= esc($profile['ecommerce_url']); ?>" target="_blank" class="btn btn-warning btn-block font-weight-bold mb-2" style="background: #D9A441; color: #0A192F; border-radius: 8px;">
                                <i class="fa-solid fa-bag-shopping mr-1"></i> Xem Trên Sàn TMĐT
                            </a>
                        <?php endif; ?>
                        <a href="<?= langBaseUrl('verify?code=' . $profile['code']); ?>" class="btn btn-outline-primary btn-block font-weight-bold" style="border-radius: 8px; font-size: 0.85rem;">
                            <i class="fa-solid fa-shield-halved mr-1"></i> Xác Minh Huy Hiệu
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Tabs Section -->
        <div class="card border-0 shadow-sm" style="border-radius: 16px; background: #ffffff;">
            <div class="card-header bg-white border-bottom p-0">
                <ul class="nav nav-tabs border-0 px-4 pt-3" id="profileTabs">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold" data-toggle="tab" href="#tab-intro">
                            <i class="fa-solid fa-circle-info mr-1 text-primary"></i> Giới Thiệu
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" data-toggle="tab" href="#tab-video">
                            <i class="fa-solid fa-video mr-1 text-danger"></i> Video Quy Trình Song Ngữ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" data-toggle="tab" href="#tab-metrics">
                            <i class="fa-solid fa-chart-line mr-1 text-success"></i> Chỉ Số Minh Bạch
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" data-toggle="tab" href="#tab-embed">
                            <i class="fa-solid fa-code mr-1 text-warning"></i> Mã Nhúng Badge
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="tab-content">
                    <!-- Tab 1: Giới thiệu -->
                    <div class="tab-pane fade show active" id="tab-intro">
                        <h5 class="font-serif mb-3 text-primary">Hồ Sơ Năng Lực & Di Sản Thương Hiệu</h5>
                        <p style="font-size: 0.92rem; line-height: 1.7; color: #334155;">
                            <?= esc($profile['summary']); ?>
                        </p>
                        <div class="p-3 bg-light rounded mt-4">
                            <h6 class="font-weight-bold mb-2"><i class="fa-solid fa-location-dot text-danger mr-2"></i> Địa Chỉ & Cơ Sở Sản Xuất:</h6>
                            <p class="mb-0 small text-muted"><?= esc($profile['address']); ?></p>
                        </div>
                    </div>

                    <!-- Tab 2: Video song ngữ -->
                    <div class="tab-pane fade" id="tab-video">
                        <h5 class="font-serif mb-3 text-primary">Phim Phóng Sự Quy Trình Chuẩn Song Ngữ</h5>
                        <div class="embed-responsive embed-responsive-16by9 rounded shadow-sm">
                            <iframe class="embed-responsive-item" src="<?= esc($profile['video_url']); ?>" allowfullscreen></iframe>
                        </div>
                        <small class="text-muted mt-2 d-block text-center">Video được kiểm duyệt và lưu trữ trên hệ sinh thái truyền thông WORLDKINGS.</small>
                    </div>

                    <!-- Tab 3: Chỉ số minh bạch -->
                    <div class="tab-pane fade" id="tab-metrics">
                        <h5 class="font-serif mb-3 text-primary">Chỉ Số Dữ Liệu Thật Được Đồng Bộ Tự Động</h5>
                        <div class="row text-center">
                            <div class="col-md-4 mb-3">
                                <div class="p-4 rounded border bg-light">
                                    <i class="fa-solid fa-users fa-2x text-primary mb-2"></i>
                                    <h3 class="font-weight-bold mb-1"><?= esc($profile['followers']); ?></h3>
                                    <span class="text-muted small">Người Theo Dõi Xác Thực</span>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="p-4 rounded border bg-light">
                                    <i class="fa-solid fa-comments fa-2x text-info mb-2"></i>
                                    <h3 class="font-weight-bold mb-1"><?= number_format($profile['reviews_count']); ?>+</h3>
                                    <span class="text-muted small">Đánh Giá Từ Khách Hàng Thật</span>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="p-4 rounded border bg-light">
                                    <i class="fa-solid fa-star fa-2x text-warning mb-2"></i>
                                    <h3 class="font-weight-bold mb-1"><?= number_format($profile['rating'], 2); ?> / 5.0</h3>
                                    <span class="text-muted small">Điểm Chất Lượng Tín Nhiệm</span>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-info small mb-0">
                            <i class="fa-solid fa-circle-check mr-1"></i> Dữ liệu được xác thực định kỳ qua hệ thống quét độc lập của Ban Thư ký TOP BEST GLOBAL.
                        </div>
                    </div>

                    <!-- Tab 4: Mã nhúng badge -->
                    <div class="tab-pane fade" id="tab-embed">
                        <h5 class="font-serif mb-3 text-primary">Mã Nhúng Huy Hiệu (Embed Badge) Cho Website Đơn Vị</h5>
                        <p class="small text-muted mb-3">Sao chép đoạn mã HTML bên dưới và dán vào website của bạn để hiển thị huy hiệu TOP BEST tự động cập nhật:</p>
                        <div class="p-3 bg-dark text-white rounded mb-3" style="font-family: monospace; font-size: 0.82rem;">
                            &lt;iframe src="<?= langBaseUrl('badge/embed/' . $profile['code']); ?>" width="280" height="120" frameborder="0" scrolling="no"&gt;&lt;/iframe&gt;
                        </div>
                        <div class="border p-3 rounded text-center bg-light">
                            <small class="text-muted d-block mb-2">Xem trước hiển thị (Preview):</small>
                            <iframe src="<?= langBaseUrl('badge/embed/' . $profile['code']); ?>" width="280" height="120" frameborder="0" scrolling="no" style="border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
