<!-- TOP BEST GLOBAL - Categories Directory View -->
<section class="py-5" style="background: linear-gradient(135deg, #0A192F 0%, #0d2342 60%, #1e293b 100%); border-bottom: 3px solid #D4AF37;">
    <div class="container py-3 text-center">
        <span class="badge px-3 py-1 mb-3" style="background: rgba(212,175,55,0.15); border: 1px solid #D4AF37; color: #F3E5AB; font-weight: 700; font-size: 11px; letter-spacing: 1.2px; text-transform: uppercase;">
            MÙA GIẢI VINH DANH QUỐC GIA <?= esc($activeSeason->theme_year ?? 2026); ?>
        </span>
        <h1 class="display-5 font-weight-bold text-white mb-2">
            Danh Mục Hạng Mục Vinh Danh Quốc Gia
        </h1>
        <p class="lead text-light mb-0" style="font-size: 15px; opacity: 0.9; max-width: 720px; margin: 0 auto;">
            Hệ thống phân ngành chuyên sâu bao gồm Công nghệ & Chuyển đổi số, Tài chính, Y tế, Giáo dục, Bất động sản, Sản xuất và Lãnh đạo tiêu biểu.
        </p>
    </div>
</section>

<section class="py-5" style="background: #f8fafc; min-height: 600px;">
    <div class="container">
        <?php if (!empty($categoriesGrouped)): foreach ($categoriesGrouped as $sector => $cats): ?>
            <div class="mb-5">
                <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
                    <div style="width: 38px; height: 38px; border-radius: 10px; background: #0A192F; color: #D4AF37; display: flex; align-items: center; justify-content: center; font-size: 18px; margin-right: 12px;">
                        <i class="fa fa-th-large"></i>
                    </div>
                    <h3 class="font-weight-bold mb-0" style="color: #0A192F; font-size: 1.4rem;">
                        Lĩnh vực: <?= esc($sector); ?>
                    </h3>
                </div>

                <div class="row">
                    <?php foreach ($cats as $cat): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 border-0 shadow-sm" style="border-radius: 14px; border: 1px solid #e2e8f0;">
                                <div class="card-body p-4 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div style="width: 44px; height: 44px; border-radius: 10px; background: #eff6ff; color: #1d4ed8; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                                <i class="<?= esc($cat->icon ?? 'fa fa-award'); ?>"></i>
                                            </div>
                                            <span class="badge badge-pill badge-light border" style="font-size: 11px; padding: 4px 10px;">
                                                70% Giám Khảo / 30% Độc Giả
                                            </span>
                                        </div>
                                        <h5 class="font-weight-bold mb-2" style="color: #0A192F; font-size: 16px;">
                                            <a href="<?= langBaseUrl('honors/category/' . $cat->slug); ?>" class="text-dark text-decoration-none">
                                                <?= esc($cat->name); ?>
                                            </a>
                                        </h5>
                                        <p class="small text-muted mb-3" style="line-height: 20px;">
                                            <?= esc($cat->description ?? 'Hạng mục tôn vinh doanh nghiệp và lãnh đạo tiêu biểu xuất sắc trong ngành.'); ?>
                                        </p>
                                    </div>

                                    <div class="pt-3 border-top d-flex gap-2">
                                        <a href="<?= langBaseUrl('voting/category/' . $cat->slug); ?>" class="btn btn-warning btn-sm font-weight-bold text-dark flex-grow-1" style="background: linear-gradient(135deg, #D4AF37 0%, #AA771C 100%); border: none; border-radius: 6px;">
                                            <i class="fa fa-check-circle mr-1"></i> Bình Chọn
                                        </a>
                                        <a href="<?= langBaseUrl('honors/category/' . $cat->slug); ?>" class="btn btn-outline-secondary btn-sm font-weight-bold px-3" style="border-radius: 6px;">
                                            Chi Tiết
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>
</section>
