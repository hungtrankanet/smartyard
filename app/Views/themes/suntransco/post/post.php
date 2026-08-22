<!-- Post Detail View: TOP BEST GLOBAL -->
<div class="py-5" style="background-color: #F8FAFC; min-height: 85vh;">
    <div class="container">
        
        <?php if (!empty($post)): ?>
            <!-- Breadcrumb Navigation -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb bg-transparent p-0 small">
                    <li class="breadcrumb-item"><a href="<?= langBaseUrl(); ?>" class="text-primary"><i class="fa-solid fa-house mr-1"></i> Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="<?= langBaseUrl('news'); ?>" class="text-primary">Tin Tức</a></li>
                    <?php if (!empty($post->category_name)): ?>
                        <li class="breadcrumb-item active text-muted"><?= esc($post->category_name); ?></li>
                    <?php endif; ?>
                </ol>
            </nav>

            <div class="row">
                <!-- Main Post Content -->
                <div class="col-lg-8 mb-5">
                    <article class="card border-0 shadow-sm p-4 p-md-5" style="border-radius: 16px; background: #ffffff;">
                        
                        <!-- Category Badge -->
                        <?php if (!empty($post->category_name)): ?>
                            <div class="mb-2">
                                <span class="badge badge-warning px-3 py-1 font-weight-bold" style="background: #D9A441; color: #0A192F; font-size: 0.75rem;">
                                    <?= esc($post->category_name); ?>
                                </span>
                            </div>
                        <?php endif; ?>

                        <!-- Title -->
                        <h1 class="font-serif text-primary mb-3" style="font-size: 1.85rem; line-height: 1.35; font-weight: 900;">
                            <?= esc($post->title); ?>
                        </h1>

                        <!-- Meta Bar -->
                        <div class="d-flex align-items-center flex-wrap text-muted small pb-3 mb-4 border-bottom" style="gap: 15px;">
                            <span><i class="fa-regular fa-calendar text-warning mr-1"></i> <?= formattedDate($post->created_at); ?></span>
                            <span><i class="fa-regular fa-eye text-warning mr-1"></i> <?= number_format($post->pageviews ?? 0); ?> lượt xem</span>
                            <?php if (!empty($post->author_username)): ?>
                                <span><i class="fa-regular fa-user text-warning mr-1"></i> <?= esc($post->author_username); ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Featured Image -->
                        <?php 
                        $heroImg = getPostImage($post, 'big');
                        if (!empty($heroImg)): ?>
                            <div class="mb-4 overflow-hidden rounded shadow-sm" style="max-height: 420px;">
                                <img src="<?= $heroImg; ?>" alt="<?= esc($post->title); ?>" class="img-fluid w-100" style="object-fit: cover;">
                            </div>
                        <?php endif; ?>

                        <!-- Summary Lead -->
                        <?php if (!empty($post->summary)): ?>
                            <div class="p-3 mb-4 rounded" style="background: #EFF6FF; border-left: 4px solid #1A4C96; font-size: 0.95rem; font-weight: 600; color: #1E293B; line-height: 1.6;">
                                <?= esc($post->summary); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Content -->
                        <div class="post-content" style="font-size: 1rem; line-height: 1.8; color: #334155;">
                            <?= $post->content; ?>
                        </div>

                        <!-- Footer Ecosystem Assurance -->
                        <div class="mt-5 pt-4 border-top">
                            <div class="p-3 rounded alert-success small d-flex align-items-center justify-content-between">
                                <div>
                                    <i class="fa-solid fa-shield-check text-success mr-2"></i>
                                    <strong>Chương trình TOP BEST GLOBAL</strong> — Thuộc Hội Kỷ lục Việt Nam (VietKings) & WORLDKINGS.
                                </div>
                                <a href="<?= langBaseUrl('top-best-la-gi'); ?>" class="btn btn-sm btn-primary font-weight-bold" style="background: #1A4C96; border-radius: 6px;">
                                    Tìm Hiểu Thêm
                                </a>
                            </div>
                        </div>

                    </article>
                </div>

                <!-- Sidebar (4 Widgets) -->
                <div class="col-lg-4">
                    <?= loadView('partials/_sidebar_news'); ?>
                </div>
            </div>

        <?php else: ?>
            <div class="card border-0 shadow-sm p-5 text-center" style="border-radius: 16px; background: #ffffff;">
                <i class="fa-solid fa-triangle-exclamation text-warning fa-3x mb-3"></i>
                <h3 class="font-serif text-primary">Bài Viết Không Tồn Tại Hoặc Đã Bị Gỡ Bỏ</h3>
                <p class="text-muted small mb-4">Vui lòng kiểm tra lại đường dẫn hoặc quay về trang chủ tin tức.</p>
                <a href="<?= langBaseUrl(); ?>" class="btn btn-tbg-cta px-4 py-2 font-weight-bold">
                    Quay Về Trang Chủ
                </a>
            </div>
        <?php endif; ?>

    </div>
</div>
