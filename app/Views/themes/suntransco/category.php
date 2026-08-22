<!-- Category Posts View: TOP BEST GLOBAL -->
<div class="py-5" style="background-color: #F8FAFC; min-height: 85vh;">
    <div class="container">
        
        <!-- Breadcrumb Navigation -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-transparent p-0 small">
                <li class="breadcrumb-item"><a href="<?= langBaseUrl(); ?>" class="text-primary"><i class="fa-solid fa-house mr-1"></i> Trang Chủ</a></li>
                <li class="breadcrumb-item"><a href="<?= langBaseUrl('news'); ?>" class="text-primary">Tin Tức</a></li>
                <li class="breadcrumb-item active text-muted"><?= esc($category->name ?? 'Chuyên Mục'); ?></li>
            </ol>
        </nav>

        <!-- Category Header Banner -->
        <div class="p-4 p-md-5 rounded-lg text-white mb-5" style="background: linear-gradient(135deg, #0A192F 0%, #1A4C96 100%); border-bottom: 3px solid #D9A441; border-radius: 16px;">
            <span class="badge badge-warning px-3 py-1 font-weight-bold mb-2" style="background: #D9A441; color: #0A192F; font-size: 0.75rem;">
                CHUYÊN MỤC TIN TỨC CHÍNH THỨC
            </span>
            <h1 class="font-serif text-white mb-2" style="font-size: 2rem; font-weight: 900;">
                <?= esc($category->name ?? 'Chuyên Mục Tin Tức'); ?>
            </h1>
            <?php if (!empty($category->description)): ?>
                <p class="mb-0" style="color: #CBD5E1; font-size: 0.95rem; max-width: 750px; line-height: 1.6;">
                    <?= esc($category->description); ?>
                </p>
            <?php endif; ?>
        </div>

        <div class="row">
            <!-- News Grid (8 Cols) -->
            <div class="col-lg-8 mb-5">
                <div class="row">
                    <?php if (!empty($posts)): ?>
                        <?php foreach ($posts as $post): 
                            $postImg = getPostImage($post, 'mid');
                            $postUrl = generatePostURL($post);
                        ?>
                            <div class="col-md-6 mb-4">
                                <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-radius: 12px; background: #ffffff;">
                                    <a href="<?= $postUrl; ?>" style="display: block; position: relative; height: 180px; background: #0A192F;">
                                        <?php if (!empty($postImg)): ?>
                                            <img src="<?= $postImg; ?>" alt="<?= esc($post->title); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="d-flex align-items-center justify-content-center h-100 text-warning" style="font-size: 32px;">
                                                <i class="fa-solid fa-trophy"></i>
                                            </div>
                                        <?php endif; ?>
                                        <span class="badge badge-warning" style="position: absolute; top: 10px; left: 10px; background: #D9A441; color: #0A192F; font-size: 0.7rem; font-weight: 800;">
                                            <?= esc($category->name ?? 'TOP BEST'); ?>
                                        </span>
                                    </a>
                                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                                        <div>
                                            <small class="text-muted d-block mb-1" style="font-size: 0.72rem;">
                                                <i class="fa-regular fa-calendar text-warning mr-1"></i> <?= formattedDate($post->created_at); ?>
                                            </small>
                                            <h6 class="font-serif mb-2" style="font-size: 0.95rem; line-height: 1.4;">
                                                <a href="<?= $postUrl; ?>" style="color: #0F172A; text-decoration: none;">
                                                    <?= esc($post->title); ?>
                                                </a>
                                            </h6>
                                            <p class="text-muted small mb-0" style="line-height: 1.45; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                <?= esc($post->summary); ?>
                                            </p>
                                        </div>
                                        <div class="pt-2 mt-2 border-top">
                                            <a href="<?= $postUrl; ?>" class="text-primary font-weight-bold small">
                                                Đọc tiếp <i class="fa-solid fa-arrow-right ml-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="card border-0 shadow-sm p-5 text-center" style="border-radius: 12px; background: #ffffff;">
                                <i class="fa-solid fa-folder-open text-warning fa-3x mb-3"></i>
                                <h5 class="font-serif text-primary">Chưa Có Bài Viết Nào Trong Chuyên Mục Này</h5>
                                <p class="text-muted small mb-3">Các bài viết mới sẽ được cập nhật sớm nhất sau khi hoàn tất xét duyệt.</p>
                                <div>
                                    <a href="<?= langBaseUrl(); ?>" class="btn btn-tbg-cta btn-sm px-4 py-2 font-weight-bold">
                                        Về Trang Chủ
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if (!empty($pager) && !empty($pager->links)): ?>
                    <div class="mt-4 d-flex justify-content-center">
                        <?= $pager->links; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar (4 Cols) -->
            <div class="col-lg-4">
                <?= loadView('partials/_sidebar_news'); ?>
            </div>
        </div>

    </div>
</div>
