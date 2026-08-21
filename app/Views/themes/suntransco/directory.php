<!-- Public Directory & Rankings -->
<div class="py-5" style="background-color: #F8FAFC; min-height: 80vh;">
    <div class="container">
        
        <!-- Header Title -->
        <div class="text-center mb-4">
            <span class="badge badge-warning px-3 py-1 font-weight-bold mb-2" style="background: #D9A441; color: #0A192F;">
                DANH BẠ CHÍNH THỨC WORLDKINGS & VIETKINGS
            </span>
            <h1 class="font-serif text-primary mb-2" style="font-size: 2.1rem;">Bảng Xếp Hạng Thương Hiệu Quốc Gia</h1>
            <p class="text-muted mx-auto" style="max-width: 700px; font-size: 0.95rem;">
                Tra cứu các đơn vị, thương hiệu và sản phẩm tiêu biểu được thẩm định và công nhận danh hiệu TOP & BEST trên 34 tỉnh thành.
            </p>
        </div>

        <!-- Filter Bar -->
        <div class="card border-0 shadow-sm p-3 mb-5" style="border-radius: 12px; background: #ffffff;">
            <form action="<?= langBaseUrl('bang-xep-hang'); ?>" method="get">
                <div class="row align-items-center">
                    <div class="col-md-4 mb-2 mb-md-0">
                        <select name="industry" class="form-control form-control-sm" style="font-size: 0.85rem; height: 40px; border-radius: 8px;">
                            <option value="">-- Tất cả 8 Nhóm Ngành --</option>
                            <?php foreach ($industries as $ind): ?>
                                <option value="<?= esc($ind['id']); ?>" <?= ($currentIndustry === $ind['id']) ? 'selected' : ''; ?>>
                                    <?= esc($ind['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2 mb-md-0">
                        <select name="province" class="form-control form-control-sm" style="font-size: 0.85rem; height: 40px; border-radius: 8px;">
                            <option value="">-- Tất cả 34 Tỉnh/Thành --</option>
                            <?php foreach ($provinces as $prov): ?>
                                <option value="<?= esc($prov); ?>" <?= ($currentProvince === $prov) ? 'selected' : ''; ?>>
                                    <?= esc($prov); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2 mb-md-0">
                        <input type="text" name="q" class="form-control form-control-sm" placeholder="Tìm tên đơn vị, mã số..." value="<?= esc($currentQuery); ?>" style="font-size: 0.85rem; height: 40px; border-radius: 8px;">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-block font-weight-bold" style="background: #1A4C96; height: 40px; border-radius: 8px; font-size: 0.85rem; border: none;">
                            <i class="fa-solid fa-filter mr-1"></i> Lọc Kết Quả
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Group 1: BEST — Hạng 1 đến 10 (Viền Vàng Kim Sang Trọng) -->
        <?php if (!empty($bestProfiles)): ?>
            <div class="mb-5">
                <div class="d-flex align-items-center mb-3 pb-2 border-bottom" style="border-color: #D9A441 !important;">
                    <div class="mr-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-radius: 6px; background: #D9A441; color: #0A192F; font-weight: 900;">
                        <i class="fa-solid fa-crown"></i>
                    </div>
                    <h3 class="font-serif mb-0 text-primary" style="font-size: 1.35rem;">
                        BEST — Hạng 1 Đến 10 <span class="badge badge-warning ml-2" style="background: #D9A441; color: #0A192F; font-size: 0.75rem;">Hội Đồng Thẩm Định Xét Chọn</span>
                    </h3>
                </div>
                <div class="row">
                    <?php foreach ($bestProfiles as $p): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card card-best h-100 border-0 shadow-sm overflow-hidden" style="border-radius: 14px; background: #ffffff; border: 2px solid #D9A441 !important;">
                                <div style="height: 140px; background: url('<?= base_url($p['banner']); ?>') center center / cover no-repeat; position: relative;">
                                    <div style="position: absolute; top: 12px; left: 12px;">
                                        <span class="badge badge-warning px-3 py-1 font-weight-bold" style="background: #D9A441; color: #0A192F;">
                                            <i class="fa-solid fa-award mr-1"></i> BEST #<?= $p['rank_number']; ?>
                                        </span>
                                    </div>
                                    <div style="position: absolute; top: 12px; right: 12px;">
                                        <span class="badge badge-diamond px-2 py-1 font-weight-bold">
                                            <i class="fa-solid fa-gem mr-1"></i> <?= $p['badge_type']; ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body p-3 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge badge-light px-2 py-1 mr-2" style="font-size: 0.7rem; font-weight: 700; color: #1A4C96; background: #EFF6FF;">
                                                <?= esc($p['province']); ?>
                                            </span>
                                            <small class="text-muted" style="font-size: 0.72rem;">Mã: <?= esc($p['code']); ?></small>
                                        </div>
                                        <h5 class="font-serif mb-2" style="font-size: 1rem; line-height: 1.4; color: #0F172A;">
                                            <?= esc($p['title_formula']); ?>
                                        </h5>
                                        <p class="text-muted small mb-3" style="line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            <?= esc($p['summary']); ?>
                                        </p>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                                        <small class="text-muted"><i class="fa-regular fa-clock mr-1"></i> Hiệu lực: 6 tháng</small>
                                        <a href="<?= langBaseUrl('ho-so/' . $p['code']); ?>" class="btn btn-sm btn-primary font-weight-bold" style="background: #1A4C96; border-radius: 6px; font-size: 0.78rem;">
                                            Xem Hồ Sơ <i class="fa-solid fa-arrow-right ml-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Group 2: TOP — Hạng 11 đến 100 (Viền Navy Nhạt Chuẩn Mực) -->
        <?php if (!empty($topProfiles)): ?>
            <div>
                <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                    <div class="mr-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-radius: 6px; background: #1A4C96; color: #ffffff; font-weight: 900;">
                        <i class="fa-solid fa-ranking-star"></i>
                    </div>
                    <h3 class="font-serif mb-0 text-primary" style="font-size: 1.35rem;">
                        TOP — Hạng 11 Đến 100 <span class="badge badge-secondary ml-2" style="font-size: 0.75rem; background: #64748B;">Thẩm Định Kỹ Thuật & Cộng Đồng</span>
                    </h3>
                </div>
                <div class="row">
                    <?php foreach ($topProfiles as $p): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card card-top h-100 border-0 shadow-sm overflow-hidden" style="border-radius: 14px; background: #ffffff; border: 1.5px solid #CBD5E1 !important;">
                                <div style="height: 140px; background: url('<?= base_url($p['banner']); ?>') center center / cover no-repeat; position: relative;">
                                    <div style="position: absolute; top: 12px; left: 12px;">
                                        <span class="badge badge-primary px-3 py-1 font-weight-bold" style="background: #1A4C96;">
                                            TOP #<?= $p['rank_number']; ?>
                                        </span>
                                    </div>
                                    <div style="position: absolute; top: 12px; right: 12px;">
                                        <span class="badge badge-ruby px-2 py-1 font-weight-bold">
                                            <i class="fa-solid fa-gem mr-1"></i> <?= $p['badge_type']; ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body p-3 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge badge-light px-2 py-1 mr-2" style="font-size: 0.7rem; font-weight: 700; color: #1A4C96; background: #EFF6FF;">
                                                <?= esc($p['province']); ?>
                                            </span>
                                            <small class="text-muted" style="font-size: 0.72rem;">Mã: <?= esc($p['code']); ?></small>
                                        </div>
                                        <h5 class="font-serif mb-2" style="font-size: 0.95rem; line-height: 1.4; color: #0F172A;">
                                            <?= esc($p['title_formula']); ?>
                                        </h5>
                                        <p class="text-muted small mb-3" style="line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            <?= esc($p['summary']); ?>
                                        </p>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                                        <small class="text-muted"><i class="fa-regular fa-clock mr-1"></i> Hiệu lực: 6 tháng</small>
                                        <a href="<?= langBaseUrl('ho-so/' . $p['code']); ?>" class="btn btn-sm btn-outline-primary font-weight-bold" style="border-radius: 6px; font-size: 0.78rem;">
                                            Xem Hồ Sơ <i class="fa-solid fa-arrow-right ml-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (empty($bestProfiles) && empty($topProfiles)): ?>
            <div class="text-center py-5 bg-white rounded shadow-sm">
                <i class="fa-solid fa-search fa-3x text-muted mb-3"></i>
                <h5 class="font-serif">Không tìm thấy hồ sơ phù hợp</h5>
                <p class="text-muted small">Hãy thử tìm kiếm với từ khóa khác hoặc chọn lại nhóm ngành/tỉnh thành.</p>
                <a href="<?= langBaseUrl('bang-xep-hang'); ?>" class="btn btn-sm btn-primary">Xóa bộ lọc</a>
            </div>
        <?php endif; ?>

    </div>
</div>
