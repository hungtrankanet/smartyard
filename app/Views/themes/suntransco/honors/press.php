<!-- TOP BEST GLOBAL - Press & Media View -->
<section class="py-5" style="background: linear-gradient(135deg, #0A192F 0%, #0d2342 60%, #1e293b 100%); border-bottom: 3px solid #D4AF37;">
    <div class="container py-3 text-center">
        <span class="badge px-3 py-1 mb-3" style="background: rgba(212,175,55,0.15); border: 1px solid #D4AF37; color: #F3E5AB; font-weight: 700; font-size: 11px; letter-spacing: 1.2px; text-transform: uppercase;">
            TRUYỀN THÔNG & BÁO CHÍ
        </span>
        <h1 class="display-5 font-weight-bold text-white mb-2">
            Thông Cáo Báo Chí & Tin Tức Vinh Danh
        </h1>
        <p class="lead text-light mb-0" style="font-size: 15px; opacity: 0.9; max-width: 700px; margin: 0 auto;">
            Cập nhật các thông tin chính thức từ Ban Tổ Chức và các cơ quan truyền thông đồng hành.
        </p>
    </div>
</section>

<section class="py-5" style="background: #f8fafc;">
    <div class="container">
        <div class="row">
            <?php if (!empty($posts)): foreach ($posts as $post): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 14px; border: 1px solid #e2e8f0; overflow: hidden;">
                        <a href="<?= generatePostUrl($post); ?>">
                            <img src="<?= getPostImage($post, 'mid'); ?>" alt="<?= esc($post->title); ?>" style="width: 100%; height: 200px; object-fit: cover;">
                        </a>
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <small class="text-muted mb-2 d-block"><i class="fa fa-calendar"></i> <?= formatDate($post->created_at); ?></small>
                                <h5 class="font-weight-bold mb-2" style="color: #0A192F; font-size: 16px;">
                                    <a href="<?= generatePostUrl($post); ?>" class="text-dark text-decoration-none">
                                        <?= esc($post->title); ?>
                                    </a>
                                </h5>
                                <p class="small text-muted mb-3" style="line-height: 20px;">
                                    <?= esc(characterLimiter($post->summary ?? '', 100)); ?>
                                </p>
                            </div>
                            <a href="<?= generatePostUrl($post); ?>" class="btn btn-sm btn-outline-primary font-weight-bold align-self-start" style="border-radius: 6px;">
                                Đọc tiếp <i class="fa fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; else: ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted">Đang cập nhật các bài viết thông cáo báo chí.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
