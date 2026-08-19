<section class="section" style="padding: 120px 0 80px; min-height: 80vh; background: #ffffff;">
    <div class="container">
        <div class="section-header text-center mb-lg">
            <span class="badge" style="background: rgba(37,99,235,0.08); border: 1.5px solid #2563eb; color: #1d4ed8; font-weight: 800;">
                <i class="fa-solid fa-newspaper text-primary"></i>
                <span class="lang-vi">Tin Tức Chuỗi Cung Ứng</span>
                <span class="lang-en">Supply Chain News</span>
            </span>
            <h1 class="section-title" style="font-size: 2.3rem; font-weight: 900; color: #0f172a; margin-top: 10px;">
                <span class="lang-vi">Tin Tức & Thông Tin Xuất Nhập Khẩu</span>
                <span class="lang-en">Logistics & International Trade News</span>
            </h1>
            <p class="section-subtitle" style="color: #475569; max-width: 680px; margin: 12px auto 0; font-size: 0.95rem;">
                <span class="lang-vi">Cập nhật tin tức thị trường vận tải biển, hàng không, giá cước và biến động chuỗi cung ứng toàn cầu.</span>
                <span class="lang-en">Stay updated with maritime, air freight rates, customs regulations, and global supply chain insights.</span>
            </p>
        </div>

        <div class="grid grid-2 news-grid-2col" style="gap: 32px;">
            <?php if (!empty($posts)): ?>
                <?php foreach ($posts as $post): 
                    $postImg = getPostImage($post, 'mid');
                    $postUrl = generatePostUrl($post);
                ?>
                    <article class="news-card card-corporate" style="background: #ffffff; border: 1.5px solid #2563eb; border-radius: 16px; overflow: hidden; display: flex; flex-direction: column; transition: all 0.3s ease; box-shadow: 0 4px 20px rgba(37,99,235,0.08);">
                        <a href="<?= $postUrl; ?>" style="display: block; position: relative; overflow: hidden; text-decoration: none; width: 100%; aspect-ratio: 16 / 9; border-bottom: 1.5px solid #2563eb;">
                            <?php if (!empty($postImg)): ?>
                                <img src="<?= $postImg; ?>" alt="<?= esc($post->title); ?>" class="news-thumb" style="width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.4s ease;" loading="lazy">
                            <?php else: ?>
                                <div class="news-thumb-fallback" style="width: 100%; height: 100%; background: linear-gradient(135deg, #eff6ff, #dbeafe); display: flex; align-items: center; justify-content: center; color: #2563eb; font-size: 2.5rem;">
                                    <i class="fa-solid fa-newspaper"></i>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($post->category_name)): ?>
                                <span style="position: absolute; top: 12px; left: 12px; background: #2563eb; color: #ffffff; padding: 4px 12px; border-radius: 6px; font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 2px 8px rgba(37,99,235,0.35); z-index: 2;">
                                    <?= esc($post->category_name); ?>
                                </span>
                            <?php endif; ?>
                        </a>
                        <div style="padding: 24px 26px; display: flex; flex-direction: column; flex: 1;">
                            <div style="display: flex; align-items: center; gap: 8px; font-size: 0.75rem; color: #64748b; margin-bottom: 10px; font-weight: 600;">
                                <i class="fa-regular fa-calendar text-primary"></i>
                                <span><?= formatDate($post->created_at); ?></span>
                            </div>
                            <h3 style="font-size: 1.12rem; font-weight: 800; margin: 0 0 10px; line-height: 1.45; color: #0f172a; flex-grow: 0;">
                                <a href="<?= $postUrl; ?>" style="color: inherit; text-decoration: none; transition: color 0.2s ease;">
                                    <?= esc($post->title); ?>
                                </a>
                            </h3>
                            <p style="font-size: 0.86rem; color: #475569; line-height: 1.65; margin-bottom: 18px; flex-grow: 1;">
                                <?= esc(characterLimiter($post->summary ?? '', 120)); ?>
                            </p>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 14px; border-top: 1px solid rgba(37,99,235,0.15); margin-top: auto;">
                                <a href="<?= $postUrl; ?>" class="read-more-btn" style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.84rem; font-weight: 800; color: #1d4ed8; text-decoration: none; transition: gap 0.2s ease;">
                                    <span class="lang-vi">Đọc tiếp</span>
                                    <span class="lang-en">Read more</span>
                                    <i class="fa-solid fa-arrow-right" style="font-size: 0.75rem;"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="card-corporate text-center" style="grid-column: 1/-1; padding: 60px 20px; background: #ffffff; border: 1.5px solid #2563eb; border-radius: 14px;">
                    <i class="fa-solid fa-inbox text-primary" style="font-size: 3rem; margin-bottom: 16px;"></i>
                    <p style="color: #475569; font-size: 1rem; margin: 0;">
                        <span class="lang-vi">Chưa có bài viết mới trong danh mục này.</span>
                        <span class="lang-en">No posts available in this section yet.</span>
                    </p>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($pager) && !empty($pager->links)): ?>
            <div class="pagination-wrapper text-center mt-lg" style="margin-top: 50px;">
                <?= $pager->links; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
