<section class="section" style="padding: 120px 0 80px; min-height: 80vh; background: #ffffff;">
    <div class="container" style="max-width: 920px;">
        <?php if (!empty($post)): ?>
            <div class="post-navigation mb-md" style="margin-bottom: 24px;">
                <a href="<?= langBaseUrl('posts'); ?>" style="display: inline-flex; align-items: center; gap: 8px; color: #2563eb; text-decoration: none; font-size: 0.88rem; font-weight: 700; transition: color 0.2s ease;">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span class="lang-vi">Quay lại danh sách tin tức</span>
                    <span class="lang-en">Back to News</span>
                </a>
            </div>

            <article class="card-corporate post-detail-container" style="background: #ffffff; border: 2px solid #2563eb; border-radius: 18px; padding: 45px 50px; box-shadow: 0 8px 30px rgba(37,99,235,0.1);">
                <?php if (!empty($post->category_name)): ?>
                    <span class="category-badge" style="background: rgba(37, 99, 235, 0.1); color: #1d4ed8; border: 1.5px solid #2563eb; padding: 5px 16px; border-radius: 6px; font-size: 0.78rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block; margin-bottom: 16px;">
                        <?= esc($post->category_name); ?>
                    </span>
                <?php endif; ?>

                <h1 class="post-title" style="font-size: 2.2rem; font-weight: 900; color: #0f172a; line-height: 1.35; margin: 0 0 20px;">
                    <?= esc($post->title); ?>
                </h1>

                <div class="post-meta" style="font-size: 0.84rem; color: #64748b; margin-bottom: 28px; display: flex; flex-wrap: wrap; gap: 20px; align-items: center; border-bottom: 1px solid rgba(37,99,235,0.15); padding-bottom: 18px; font-weight: 600;">
                    <span style="display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fa-regular fa-calendar text-primary"></i>
                        <?= formatDate($post->created_at); ?>
                    </span>
                    <span style="display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fa-regular fa-eye text-primary"></i>
                        <?= esc($post->pageviews ?? 0); ?>
                        <span class="lang-vi">lượt xem</span>
                        <span class="lang-en">views</span>
                    </span>
                    <?php if (!empty($post->author_username)): ?>
                        <span style="display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fa-regular fa-user"></i>
                            <?= esc($post->author_username); ?>
                        </span>
                    <?php endif; ?>
                </div>

                <?php 
                $heroImg = getPostImage($post, 'big');
                if (!empty($heroImg)): ?>
                    <div class="post-hero-image" style="margin: 0 0 32px; border-radius: 12px; overflow: hidden; border: 1.5px solid #2563eb; box-shadow: 0 4px 18px rgba(37,99,235,0.1);">
                        <img src="<?= $heroImg; ?>" alt="<?= esc($post->title); ?>" style="width: 100%; aspect-ratio: 16 / 9; object-fit: cover; display: block;">
                    </div>
                <?php endif; ?>

                <?php if (!empty($post->summary)): ?>
                    <div class="post-summary-lead" style="font-size: 1.05rem; line-height: 1.7; font-weight: 600; color: #1e293b; padding: 18px 24px; background: #eff6ff; border-left: 4px solid #2563eb; border-radius: 0 10px 10px 0; margin-bottom: 30px;">
                        <?= esc($post->summary); ?>
                    </div>
                <?php endif; ?>

                <div class="post-body-content" style="line-height: 1.9; color: #334155; font-size: 1.05rem;">
                    <?= $post->content; ?>
                </div>

                <div class="post-footer" style="margin-top: 48px; padding-top: 24px; border-top: 1px solid rgba(37,99,235,0.15); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                    <a href="<?= langBaseUrl('posts'); ?>" class="btn btn-outline btn-sm" style="display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span class="lang-vi">Tất cả tin tức</span>
                        <span class="lang-en">All News</span>
                    </a>
                    <a href="<?= langBaseUrl(); ?>" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-house"></i>
                        <span class="lang-vi">Trang chủ</span>
                        <span class="lang-en">Home</span>
                    </a>
                </div>
            </article>
        <?php else: ?>
            <div class="card-corporate text-center" style="padding: 60px 20px; background: #ffffff; border: 1.5px solid #2563eb; border-radius: 14px;">
                <i class="fa-solid fa-circle-exclamation" style="font-size: 3rem; color: #ef4444; margin-bottom: 16px;"></i>
                <h2 style="color: #0f172a; font-size: 1.5rem; margin-bottom: 10px;">
                    <span class="lang-vi">Bài viết không tồn tại</span>
                    <span class="lang-en">Post Not Found</span>
                </h2>
                <p style="color: #475569; margin-bottom: 24px;">
                    <span class="lang-vi">Nội dung bài viết bạn tìm kiếm không có hoặc đã bị gỡ bỏ.</span>
                    <span class="lang-en">The post you are looking for does not exist or has been removed.</span>
                </p>
                <a href="<?= langBaseUrl('posts'); ?>" class="btn btn-primary">
                    <span class="lang-vi">Xem tin tức khác</span>
                    <span class="lang-en">Browse Other News</span>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>
