<!-- Main Home: Hero Banner & 5-Column News Portal -->
<div class="py-4" style="background-color: #F8FAFC; min-height: 85vh;">
    <div class="container-fluid px-lg-4">
        
        <!-- KHỐI HERO BANNER: TIN NỔI BẬT (HERO DẠNG TIN SPLIT) -->
        <div class="row mb-5">
            <!-- Tin Lớn Bên Trái -->
            <div class="col-lg-8 mb-4 mb-lg-0">
                <?php if (!empty($featuredNews)): ?>
                    <div class="card border-0 shadow-sm overflow-hidden h-100" style="border-radius: 16px; background: #ffffff;">
                        <div style="position: relative; height: 360px; background: url('<?= base_url($featuredNews['image']); ?>') center center / cover no-repeat;">
                            <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(10,25,47,0.15) 0%, rgba(10,25,47,0.9) 100%);"></div>
                            <div style="position: absolute; top: 20px; left: 20px;">
                                <span class="badge badge-warning px-3 py-2 font-weight-bold" style="background: #D9A441; color: #0A192F; font-size: 0.8rem; box-shadow: 0 2px 8px rgba(0,0,0,0.3);">
                                    <i class="fa-solid fa-crown mr-1"></i> TIÊU ĐIỂM VINH DANH MÙA GIẢI 2026
                                </span>
                            </div>
                            <div style="position: absolute; bottom: 25px; left: 25px; right: 25px; color: #ffffff;">
                                <div class="mb-2" style="font-size: 0.82rem; color: #F3E5AB;">
                                    <i class="fa-regular fa-calendar mr-1"></i> <?= esc($featuredNews['created_at']); ?> • <span class="text-uppercase"><?= esc($featuredNews['category']); ?></span>
                                </div>
                                <h2 class="font-serif text-white mb-2" style="font-size: 1.65rem; line-height: 1.35; text-shadow: 0 2px 8px rgba(0,0,0,0.5);">
                                    <a href="<?= langBaseUrl('top-best-la-gi'); ?>" style="color: #ffffff; text-decoration: none;">
                                        <?= esc($featuredNews['title']); ?>
                                    </a>
                                </h2>
                                <p style="font-size: 0.9rem; color: #E2E8F0; line-height: 1.5; margin-bottom: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
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
                    <?php if (!empty($secondaryNews)): ?>
                        <?php foreach ($secondaryNews as $item): ?>
                            <div class="card border-0 shadow-sm p-3 flex-fill" style="border-radius: 12px; background: #ffffff; transition: transform 0.2s ease;">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="badge badge-light px-2 py-1 mr-2 font-weight-bold" style="font-size: 0.7rem; color: #1A4C96; background: #EFF6FF;">
                                        <?= esc($item['category']); ?>
                                    </span>
                                    <small class="text-muted" style="font-size: 0.72rem;">
                                        <i class="fa-regular fa-clock mr-1"></i> <?= esc($item['created_at']); ?>
                                    </small>
                                </div>
                                <h6 class="font-serif mb-1" style="font-size: 0.9rem; line-height: 1.4;">
                                    <a href="<?= langBaseUrl('top-best-la-gi'); ?>" style="color: #0F172A; text-decoration: none;">
                                        <?= esc($item['title']); ?>
                                    </a>
                                </h6>
                                <p class="text-muted mb-0 small" style="line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    <?= esc($item['summary']); ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- 5-COLUMN MASTER LAYOUT CONTAINER (3 CỘT CHUYÊN MỤC + 2 CỘT SIDEBAR) -->
        <div class="tbg-master-grid">
            
            <!-- LEFT 3 COLUMNS: Chuyên Mục Tin Tức (Mỗi Cụm 4 Bài: 2 Cột Bài Lớn + 1 Cột 3 Bài Nhỏ) -->
            <div class="tbg-col-content">
                <?php if (!empty($categoryClusters)): ?>
                    <?php foreach ($categoryClusters as $cluster): ?>
                        <div class="category-cluster-box mb-5">
                            
                            <!-- Category Header -->
                            <div class="d-flex align-items-center justify-content-between pb-2 mb-3 border-bottom" style="border-color: #1A4C96 !important; border-bottom-width: 2px !important;">
                                <div class="d-flex align-items-center">
                                    <div class="mr-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-radius: 6px; background: #1A4C96; color: #ffffff; font-size: 15px;">
                                        <i class="fa-solid <?= esc($cluster['icon']); ?>"></i>
                                    </div>
                                    <h4 class="font-serif mb-0 text-primary" style="font-size: 1.15rem; font-weight: 800;">
                                        <?= esc($cluster['name']); ?>
                                    </h4>
                                </div>
                                <a href="<?= langBaseUrl('top-best-la-gi'); ?>" class="btn btn-sm btn-outline-primary font-weight-bold" style="border-radius: 20px; font-size: 0.75rem; padding: 4px 12px;">
                                    Xem Thêm <i class="fa-solid fa-angle-right ml-1"></i>
                                </a>
                            </div>

                            <!-- Cluster 4 Posts: 2 Cols for 1 Big + 1 Col for 3 Small -->
                            <div class="cluster-grid">
                                
                                <!-- 1 Bài Viết Lớn (Chiếm 2 Cột) -->
                                <?php if (!empty($cluster['featured'])): $feat = $cluster['featured']; ?>
                                    <div class="cluster-big-col">
                                        <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-radius: 12px; background: #ffffff;">
                                            <div style="height: 240px; background: url('<?= base_url($feat['image']); ?>') center center / cover no-repeat; position: relative;">
                                                <span class="badge badge-warning" style="position: absolute; top: 12px; left: 12px; background: #D9A441; color: #0A192F; font-size: 0.72rem; font-weight: 800;">
                                                    <i class="fa-solid fa-star mr-1"></i> <?= esc($feat['badge']); ?>
                                                </span>
                                            </div>
                                            <div class="card-body p-3 d-flex flex-column justify-content-between">
                                                <div>
                                                    <small class="text-muted d-block mb-1" style="font-size: 0.72rem;">
                                                        <i class="fa-regular fa-calendar mr-1"></i> <?= esc($feat['date']); ?>
                                                    </small>
                                                    <h5 class="font-serif mb-2" style="font-size: 1.05rem; line-height: 1.4; color: #0F172A;">
                                                        <a href="<?= langBaseUrl('top-best-la-gi'); ?>" style="color: #0F172A; text-decoration: none;">
                                                            <?= esc($feat['title']); ?>
                                                        </a>
                                                    </h5>
                                                    <p class="text-muted small mb-0" style="line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                                        <?= esc($feat['summary']); ?>
                                                    </p>
                                                </div>
                                                <div class="pt-3 mt-2 border-top">
                                                    <a href="<?= langBaseUrl('top-best-la-gi'); ?>" class="text-primary font-weight-bold" style="font-size: 0.8rem;">
                                                        Đọc toàn bộ bài viết <i class="fa-solid fa-arrow-right ml-1"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- 3 Bài Viết Nhỏ (Chiếm 1 Cột, Xếp Dọc) -->
                                <div class="cluster-small-col">
                                    <div class="d-flex flex-column justify-content-between h-100" style="gap: 10px;">
                                        <?php if (!empty($cluster['sub_posts'])): ?>
                                            <?php foreach ($cluster['sub_posts'] as $sub): ?>
                                                <div class="card border-0 shadow-sm p-3 h-100" style="border-radius: 10px; background: #ffffff; transition: all 0.2s ease;">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <span class="badge badge-light px-2 py-1 mr-2 font-weight-bold" style="font-size: 0.68rem; color: #1A4C96; background: #EFF6FF;">
                                                            <?= esc($sub['badge']); ?>
                                                        </span>
                                                        <small class="text-muted" style="font-size: 0.7rem;">
                                                            <i class="fa-regular fa-clock mr-1"></i> <?= esc($sub['date']); ?>
                                                        </small>
                                                    </div>
                                                    <h6 class="font-serif mb-0" style="font-size: 0.84rem; line-height: 1.35;">
                                                        <a href="<?= langBaseUrl('top-best-la-gi'); ?>" style="color: #0F172A; text-decoration: none;">
                                                            <?= esc($sub['title']); ?>
                                                        </a>
                                                    </h6>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>

                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- RIGHT 2 COLUMNS: Thông Báo, Khảo Sát, Quảng Cáo & Tra Cứu -->
            <div class="tbg-col-sidebar">
                
                <!-- 1. THÔNG BÁO TỪ BAN TỔ CHỨC / ADMIN -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: #ffffff;">
                    <div class="card-header bg-white border-bottom py-3" style="border-radius: 12px 12px 0 0;">
                        <h5 class="font-serif mb-0" style="font-size: 0.95rem; color: #0A192F;">
                            <i class="fa-solid fa-bullhorn text-danger mr-2"></i> Thông Báo Từ Ban Thư Ký
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <?php if (!empty($announcements)): ?>
                            <ul class="list-unstyled mb-0">
                                <?php foreach ($announcements as $anc): ?>
                                    <li class="mb-3 pb-3 border-bottom last-no-border">
                                        <div class="d-flex align-items-center mb-1">
                                            <span class="badge badge-danger px-2 py-1 mr-2" style="font-size: 0.65rem; font-weight: 700;">
                                                <?= esc($anc['tag']); ?>
                                            </span>
                                            <small class="text-muted" style="font-size: 0.7rem;"><?= esc($anc['date']); ?></small>
                                        </div>
                                        <a href="<?= langBaseUrl('top-best-la-gi'); ?>" style="color: #0F172A; font-size: 0.82rem; font-weight: 600; line-height: 1.35; text-decoration: none; display: block;">
                                            <?= esc($anc['title']); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 2. KHẢO SÁT & BÌNH CHỌN TƯƠNG TÁC (INTERACTIVE POLL) -->
                <?php if (!empty($interactivePoll)): $poll = $interactivePoll; ?>
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: #ffffff;">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="font-serif mb-0" style="font-size: 0.95rem; color: #0A192F;">
                                <i class="fa-solid fa-square-poll-vertical text-primary mr-2"></i> Khảo Sát & Thăm Dò Ý Kiến
                            </h5>
                        </div>
                        <div class="card-body p-3">
                            <p class="font-weight-bold small mb-3" style="color: #1E293B; line-height: 1.4;">
                                <?= esc($poll['question']); ?>
                            </p>
                            <form action="#" method="post" onsubmit="event.preventDefault(); alert('Cảm ơn bạn đã tham gia khảo sát ý kiến độc giả!');">
                                <?php foreach ($poll['options'] as $opt): ?>
                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between small mb-1">
                                            <label class="mb-0 text-muted" style="font-size: 0.76rem;">
                                                <input type="radio" name="poll_opt" class="mr-1"> <?= esc($opt['text']); ?>
                                            </label>
                                            <span class="font-weight-bold text-primary" style="font-size: 0.75rem;"><?= $opt['percentage']; ?>%</span>
                                        </div>
                                        <div class="progress" style="height: 6px; border-radius: 3px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $opt['percentage']; ?>%;"></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <div class="mt-3 d-flex align-items-center justify-content-between">
                                    <small class="text-muted" style="font-size: 0.72rem;">Đã có <?= number_format($poll['total_votes']); ?> lượt</small>
                                    <button type="submit" class="btn btn-sm btn-primary font-weight-bold" style="background: #1A4C96; font-size: 0.75rem; border-radius: 6px;">
                                        Gửi Bình Chọn
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- 3. QUẢNG CÁO & KHÔNG GIAN TRUYỀN THÔNG (SPONSOR / ADS) -->
                <?php if (!empty($adSpaces)): ?>
                    <?php foreach ($adSpaces as $ad): ?>
                        <div class="card border-0 shadow-sm mb-4 overflow-hidden text-center" style="border-radius: 12px; background: linear-gradient(135deg, #0A192F 0%, #1A4C96 100%); color: #ffffff;">
                            <div class="card-body p-4">
                                <span class="badge badge-warning px-2 py-1 mb-2 font-weight-bold" style="background: #D9A441; color: #0A192F; font-size: 0.68rem;">
                                    <?= esc($ad['tag']); ?>
                                </span>
                                <h6 class="font-serif text-white mb-2" style="font-size: 0.95rem;"><?= esc($ad['title']); ?></h6>
                                <p style="font-size: 0.78rem; color: #CBD5E1; margin-bottom: 15px;">
                                    <?= esc($ad['desc']); ?>
                                </p>
                                <a href="<?= langBaseUrl($ad['url']); ?>" class="btn btn-tbg-cta btn-sm btn-block py-2 font-weight-bold">
                                    Đăng Ký Vị Trí Quảng Bá
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- 4. TRA CỨU NHANH & XÁC MINH HUY HIỆU QR -->
                <div class="card border-0 shadow-sm" style="border-radius: 12px; background: #ffffff;">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="font-serif mb-0" style="font-size: 0.95rem; color: #0A192F;">
                            <i class="fa-solid fa-shield-halved text-warning mr-2"></i> Tra Cứu Nhanh Huy Hiệu
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <form action="<?= langBaseUrl('verify'); ?>" method="get">
                            <div class="input-group mb-2">
                                <input type="text" name="code" class="form-control form-control-sm" placeholder="VD: TBG-VN-2026-001" required style="font-size: 0.8rem; border-radius: 6px 0 0 6px;">
                                <div class="input-group-append">
                                    <button class="btn btn-warning btn-sm font-weight-bold" type="submit" style="background: #D9A441; color: #0A192F;">
                                        Tra Cứu
                                    </button>
                                </div>
                            </div>
                        </form>
                        <a href="<?= langBaseUrl('verify'); ?>" class="text-primary small font-weight-bold d-block text-center mt-2">
                            <i class="fa-solid fa-qrcode mr-1"></i> Quét mã QR từ bao bì/POSM
                        </a>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

<style>
/* 5-Column Master Layout System */
.tbg-master-grid {
    display: grid;
    grid-template-columns: 3fr 2fr;
    gap: 25px;
}
.tbg-col-content {
    min-width: 0;
}
.tbg-col-sidebar {
    min-width: 0;
}

/* Cluster Layout (3 Content Columns: 2 Columns Big Post + 1 Column 3 Small Posts) */
.cluster-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 15px;
}
.cluster-big-col {
    min-width: 0;
}
.cluster-small-col {
    min-width: 0;
}

.last-no-border:last-child {
    border-bottom: none !important;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}

/* Responsive Grid on Tablet / Mobile */
@media (max-width: 991px) {
    .tbg-master-grid {
        grid-template-columns: 1fr;
    }
    .cluster-grid {
        grid-template-columns: 1fr;
    }
}
</style>
