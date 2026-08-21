<!-- TOP BEST GLOBAL - Seasons Directory View -->
<section class="py-5" style="background: linear-gradient(135deg, #0A192F 0%, #0d2342 60%, #1e293b 100%); border-bottom: 3px solid #D4AF37;">
    <div class="container py-3 text-center">
        <span class="badge px-3 py-1 mb-3" style="background: rgba(212,175,55,0.15); border: 1px solid #D4AF37; color: #F3E5AB; font-weight: 700; font-size: 11px; letter-spacing: 1.2px; text-transform: uppercase;">
            LỊCH SỬ CÁC MÙA GIẢI
        </span>
        <h1 class="display-5 font-weight-bold text-white mb-2">
            Các Mùa Giải Vinh Danh Quốc Gia
        </h1>
        <p class="lead text-light mb-0" style="font-size: 15px; opacity: 0.9; max-width: 700px; margin: 0 auto;">
            Hành trình tôn vinh các giá trị xuất sắc, thương hiệu dẫn đầu và doanh nhân truyền cảm hứng qua các năm.
        </p>
    </div>
</section>

<section class="py-5" style="background: #f8fafc; min-height: 500px;">
    <div class="container">
        <div class="row">
            <?php if (!empty($seasons)): foreach ($seasons as $s): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden;">
                        <div class="p-4 text-white position-relative" style="background: linear-gradient(135deg, #0A192F 0%, #1e3a8a 100%);">
                            <span class="badge position-absolute" style="top: 15px; right: 15px; background: #D4AF37; color: #0A192F; font-weight: 800;">
                                NĂM <?= esc($s->theme_year); ?>
                            </span>
                            <div style="font-size: 32px; color: #D4AF37; margin-bottom: 8px;">
                                <i class="fa fa-trophy"></i>
                            </div>
                            <h4 class="font-weight-bold mb-1" style="color: #ffffff;">
                                <?= esc($s->title); ?>
                            </h4>
                        </div>
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <p class="small text-muted mb-4" style="line-height: 22px;">
                                <?= esc($s->description ?? 'Mùa giải vinh danh thương hiệu quốc gia uy tín và xuất sắc.'); ?>
                            </p>
                            <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                                <span class="badge <?= ($s->is_active == 1) ? 'badge-success' : 'badge-secondary'; ?> px-3 py-1">
                                    <?= ($s->is_active == 1) ? 'Đang diễn ra' : 'Đã trao giải'; ?>
                                </span>
                                <a href="<?= langBaseUrl('honors/seasons/' . $s->id); ?>" class="btn btn-sm btn-outline-primary font-weight-bold" style="border-radius: 6px;">
                                    Xem Mùa Giải <i class="fa fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; else: ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted">Chưa có thông tin mùa giải.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
