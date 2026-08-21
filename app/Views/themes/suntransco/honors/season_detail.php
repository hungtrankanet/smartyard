<!-- TOP BEST GLOBAL - Season Detail View -->
<section class="py-5" style="background: linear-gradient(135deg, #0A192F 0%, #0d2342 60%, #1e293b 100%); border-bottom: 3px solid #D4AF37;">
    <div class="container py-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-3" style="font-size: 13px;">
                <li class="breadcrumb-item"><a href="<?= langBaseUrl(); ?>" class="text-light">Trang Chủ</a></li>
                <li class="breadcrumb-item"><a href="<?= langBaseUrl('honors/seasons'); ?>" class="text-light">Các Mùa Giải</a></li>
                <li class="breadcrumb-item active text-warning" aria-current="page"><?= esc($season->title); ?></li>
            </ol>
        </nav>
        <div class="row align-items-center">
            <div class="col-lg-8 text-white">
                <span class="badge px-3 py-1 mb-2" style="background: #D4AF37; color: #0A192F; font-weight: 800; font-size: 12px; border-radius: 20px;">
                    MÙA GIẢI <?= esc($season->theme_year); ?>
                </span>
                <h1 class="font-weight-bold display-5 mb-2" style="color: #ffffff;">
                    <?= esc($season->title); ?>
                </h1>
                <p class="lead text-light mb-4" style="font-size: 15px; opacity: 0.9; line-height: 24px;">
                    <?= esc($season->description ?? 'Chương trình vinh danh các doanh nghiệp và lãnh đạo có đóng góp xuất sắc.'); ?>
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="<?= langBaseUrl('voting'); ?>" class="btn btn-warning font-weight-bold px-4 py-2 mr-3" style="background: linear-gradient(135deg, #D4AF37 0%, #AA771C 100%); color: #0A192F; border: none; border-radius: 8px;">
                        <i class="fa fa-check-circle mr-1"></i> Tham Gia Bình Chọn
                    </a>
                    <a href="<?= langBaseUrl('hall-of-fame/season/' . $season->id); ?>" class="btn btn-outline-light px-4 py-2 font-weight-bold" style="border-radius: 8px;">
                        <i class="fa fa-trophy mr-1"></i> Bảng Vàng Mùa Này
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5" style="background: #f8fafc;">
    <div class="container">
        <h4 class="font-weight-bold mb-4" style="color: #0A192F;">
            <i class="fa fa-list text-warning mr-2"></i> Các Hạng Mục Trong Mùa Giải <?= esc($season->theme_year); ?>
        </h4>
        <div class="row">
            <?php if (!empty($categories)): foreach ($categories as $cat): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm p-4" style="border-radius: 14px; border: 1px solid #e2e8f0;">
                        <h5 class="font-weight-bold mb-2" style="color: #0A192F;">
                            <a href="<?= langBaseUrl('honors/category/' . $cat->slug); ?>" class="text-dark text-decoration-none">
                                <?= esc($cat->name); ?>
                            </a>
                        </h5>
                        <p class="small text-muted mb-3"><?= esc($cat->description ?? ''); ?></p>
                        <a href="<?= langBaseUrl('voting/category/' . $cat->slug); ?>" class="btn btn-sm btn-outline-primary font-weight-bold mt-auto" style="border-radius: 6px;">
                            Bình chọn ứng viên <i class="fa fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; else: ?>
                <div class="col-12 text-center py-4">
                    <p class="text-muted">Đang cập nhật danh mục cho mùa giải này.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
