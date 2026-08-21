<!-- Main Home: News & Media Portal -->
<div class="py-4" style="background-color: #F8FAFC; min-height: 80vh;">
    <div class="container">
        
        <!-- Khối 1: Tin Nổi Bật (Hero Tin Split) -->
        <div class="row mb-4">
            <!-- Tin Lớn Bên Trái -->
            <div class="col-lg-8 mb-4 mb-lg-0">
                <?php if (!empty($featuredNews)): ?>
                    <div class="card border-0 shadow-sm overflow-hidden h-100" style="border-radius: 14px; background: #ffffff;">
                        <div style="position: relative; height: 340px; background: url('<?= base_url($featuredNews['image']); ?>') center center / cover no-repeat;">
                            <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(10,25,47,0.1) 0%, rgba(10,25,47,0.9) 100%);"></div>
                            <div style="position: absolute; top: 18px; left: 18px;">
                                <span class="badge badge-warning px-3 py-2 font-weight-bold" style="background: #D9A441; color: #0A192F; font-size: 0.78rem;">
                                    <i class="fa-solid fa-star mr-1"></i> TIÊU ĐIỂM VINH DANH
                                </span>
                            </div>
                            <div style="position: absolute; bottom: 20px; left: 20px; right: 20px; color: #ffffff;">
                                <div class="mb-2" style="font-size: 0.8rem; color: #F3E5AB;">
                                    <i class="fa-regular fa-calendar mr-1"></i> <?= esc($featuredNews['created_at']); ?> • <?= esc($featuredNews['category']); ?>
                                </div>
                                <h2 class="font-serif text-white mb-2" style="font-size: 1.55rem; line-height: 1.35;">
                                    <a href="<?= langBaseUrl('top-best-la-gi'); ?>" style="color: #ffffff; text-decoration: none;">
                                        <?= esc($featuredNews['title']); ?>
                                    </a>
                                </h2>
                                <p style="font-size: 0.88rem; color: #E2E8F0; line-height: 1.5; margin-bottom: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    <?= esc($featuredNews['summary']); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- 3 Tin Nhỏ Xếp Dọc Bên Phải -->
            <div class="col-lg-4">
                <div class="d-flex flex-column justify-content-between h-100" style="gap: 12px;">
                    <?php foreach ($secondaryNews as $item): ?>
                        <div class="card border-0 shadow-sm p-3" style="border-radius: 12px; background: #ffffff; transition: transform 0.2s ease;">
                            <div class="d-flex align-items-center mb-1">
                                <span class="badge badge-light text-primary px-2 py-1 mr-2" style="font-size: 0.7rem; font-weight: 700; background: #EFF6FF;">
                                    <?= esc($item['category']); ?>
                                </span>
                                <small class="text-muted" style="font-size: 0.72rem;">
                                    <i class="fa-regular fa-clock mr-1"></i> <?= esc($item['created_at']); ?>
                                </small>
                            </div>
                            <h6 class="font-serif mb-1" style="font-size: 0.92rem; line-height: 1.4;">
                                <a href="<?= langBaseUrl('top-best-la-gi'); ?>" style="color: #0F172A; text-decoration: none;">
                                    <?= esc($item['title']); ?>
                                </a>
                            </h6>
                            <p class="text-muted mb-0" style="font-size: 0.78rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                <?= esc($item['summary']); ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Khối 2: Thanh Chuyên Mục Lọc Tại Chỗ -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-body py-2 px-3 d-flex flex-wrap align-items-center justify-content-between">
                <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
                    <span class="text-muted mr-2 font-weight-bold" style="font-size: 0.8rem;"><i class="fa-solid fa-filter mr-1 text-warning"></i> Chuyên mục:</span>
                    <button class="btn btn-sm btn-primary active category-filter-btn" data-cat="all" style="border-radius: 20px; font-size: 0.78rem; font-weight: 700; background: #1A4C96; border: none;">
                        Tất Cả
                    </button>
                    <button class="btn btn-sm btn-light category-filter-btn" data-cat="tin-chuong-trinh" style="border-radius: 20px; font-size: 0.78rem; font-weight: 600;">
                        Tin Chương Trình
                    </button>
                    <button class="btn btn-sm btn-light category-filter-btn" data-cat="doanh-nghiep-vinh-danh" style="border-radius: 20px; font-size: 0.78rem; font-weight: 600;">
                        Doanh Nghiệp Vinh Danh
                    </button>
                    <button class="btn btn-sm btn-light category-filter-btn" data-cat="xu-huong-nganh" style="border-radius: 20px; font-size: 0.78rem; font-weight: 600;">
                        Xu Hướng Ngành
                    </button>
                    <button class="btn btn-sm btn-light category-filter-btn" data-cat="cau-chuyen-thuong-hieu" style="border-radius: 20px; font-size: 0.78rem; font-weight: 600;">
                        Câu Chuyện Thương Hiệu
                    </button>
                </div>
                <div class="d-none d-md-block">
                    <span style="font-size: 0.78rem; color: #64748B;">Cổng Truyền Thông Chính Thức WORLDKINGS</span>
                </div>
            </div>
        </div>

        <!-- Khối 3: Lưới Tin Tức + Sidebar -->
        <div class="row">
            <!-- Cột Trái: Lưới Tin Tức -->
            <div class="col-lg-8">
                <div class="row" id="newsGrid">
                    <?php foreach ($allNews as $news): ?>
                        <div class="col-md-6 mb-4 news-item" data-category="<?= esc($news['category_slug']); ?>">
                            <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-radius: 12px; background: #ffffff;">
                                <div style="height: 180px; background: url('<?= base_url($news['image']); ?>') center center / cover no-repeat; position: relative;">
                                    <span class="badge badge-dark" style="position: absolute; top: 12px; left: 12px; background: rgba(10,25,47,0.85); font-size: 0.7rem; font-weight: 700; border-left: 3px solid #D9A441;">
                                        <?= esc($news['category']); ?>
                                    </span>
                                </div>
                                <div class="card-body p-3 d-flex flex-column justify-content-between">
                                    <div>
                                        <small class="text-muted d-block mb-1" style="font-size: 0.72rem;">
                                            <i class="fa-regular fa-calendar mr-1"></i> <?= esc($news['created_at']); ?>
                                        </small>
                                        <h5 class="font-serif mb-2" style="font-size: 0.98rem; line-height: 1.4;">
                                            <a href="<?= langBaseUrl('top-best-la-gi'); ?>" style="color: #0F172A; text-decoration: none;">
                                                <?= esc($news['title']); ?>
                                            </a>
                                        </h5>
                                        <p class="text-muted" style="font-size: 0.8rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 12px;">
                                            <?= esc($news['summary']); ?>
                                        </p>
                                    </div>
                                    <div>
                                        <a href="<?= langBaseUrl('top-best-la-gi'); ?>" class="text-primary font-weight-bold" style="font-size: 0.78rem;">
                                            Đọc chi tiết <i class="fa-solid fa-arrow-right ml-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Phân Trang / Nút Xem Thêm -->
                <div class="text-center my-4">
                    <button class="btn btn-outline-primary px-4 py-2 font-weight-bold" style="border-radius: 25px; font-size: 0.85rem;" onclick="alert('Đã tải toàn bộ tin tức mới nhất từ Ban Thư ký TOP BEST.');">
                        <i class="fa-solid fa-rotate-right mr-1"></i> Xem Thêm Tin Tức
                    </button>
                </div>
            </div>

            <!-- Cột Phải: Sidebar 4 Widgets -->
            <div class="col-lg-4">
                <?= view('themes/suntransco/partials/_sidebar_news'); ?>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.category-filter-btn');
    const newsItems = document.querySelectorAll('.news-item');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            filterBtns.forEach(b => {
                b.classList.remove('btn-primary', 'active');
                b.classList.add('btn-light');
                b.style.background = '';
            });
            this.classList.remove('btn-light');
            this.classList.add('btn-primary', 'active');
            this.style.background = '#1A4C96';

            const cat = this.getAttribute('data-cat');
            newsItems.forEach(item => {
                if (cat === 'all' || item.getAttribute('data-category') === cat) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
});
</script>
