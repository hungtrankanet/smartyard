<section class="section" style="padding: 120px 0 60px;">
    <div class="container">
        <?php if (!empty($page)): ?>
            <div class="section-header text-center mb-lg">
                <h1 class="section-title"><?= esc($page->title); ?></h1>
            </div>
            <div class="card-corporate" style="padding: 40px; line-height: 1.8; color: rgba(255,255,255,0.85);">
                <?= $page->page_content; ?>
            </div>
        <?php else: ?>
            <div class="section-header text-center mb-lg">
                <h1 class="section-title">Trang không tồn tại</h1>
                <p>Nội dung bạn tìm kiếm hiện chưa có hoặc đã bị di chuyển.</p>
            </div>
        <?php endif; ?>
    </div>
</section>
