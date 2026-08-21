<?php
use Config\TopBestData;
$sidebarDirectory = array_slice(TopBestData::getDirectoryProfiles(), 0, 5);
?>
<!-- Sidebar Component -->
<div class="tbg-sidebar">
    <!-- Widget 1: Tra cứu nhanh -->
    <div class="card mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #0A192F 0%, #1A4C96 100%); color: #ffffff; border-radius: 12px;">
        <div class="card-body p-4">
            <h5 class="font-serif text-white mb-2" style="font-size: 1.05rem;">
                <i class="fa-solid fa-shield-halved text-warning mr-2"></i> Tra Cứu Huy Hiệu
            </h5>
            <p style="font-size: 0.8rem; color: #CBD5E1; margin-bottom: 15px;">
                Nhập mã định danh hồ sơ hoặc quét mã QR từ bao bì/POSM để kiểm tra chứng nhận thật.
            </p>
            <form action="<?= langBaseUrl('verify'); ?>" method="get">
                <div class="input-group mb-2">
                    <input type="text" name="code" class="form-control form-control-sm" placeholder="VD: TBG-VN-2026-001" style="border-radius: 6px 0 0 6px; font-size: 0.82rem;" required>
                    <div class="input-group-append">
                        <button class="btn btn-warning btn-sm font-weight-bold" type="submit" style="background: #D9A441; border: none; color: #0A192F;">
                            <i class="fa-solid fa-magnifying-glass"></i> Tra Cứu
                        </button>
                    </div>
                </div>
            </form>
            <div class="text-right">
                <a href="<?= langBaseUrl('verify'); ?>" class="text-warning" style="font-size: 0.75rem; font-weight: 600;">
                    <i class="fa-solid fa-qrcode mr-1"></i> Bật camera quét mã QR
                </a>
            </div>
        </div>
    </div>

    <!-- Widget 2: Mới cập nhật Bảng xếp hạng -->
    <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px; background: #ffffff;">
        <div class="card-header bg-white border-bottom py-3" style="border-radius: 12px 12px 0 0;">
            <h5 class="font-serif mb-0" style="font-size: 0.95rem; color: #0A192F;">
                <i class="fa-solid fa-clock-rotate-left text-primary mr-2"></i> Mới Cập Nhật Bảng Xếp Hạng
            </h5>
        </div>
        <div class="card-body p-0">
            <ul class="list-group list-group-flush">
                <?php foreach ($sidebarDirectory as $item): ?>
                    <li class="list-group-item px-3 py-2 d-flex align-items-center justify-content-between">
                        <div>
                            <a href="<?= langBaseUrl('ho-so/' . $item['code']); ?>" style="color: #0F172A; font-weight: 700; font-size: 0.82rem; line-height: 1.3; display: block;">
                                <?= esc($item['name']); ?>
                            </a>
                            <small class="text-muted" style="font-size: 0.72rem;">
                                <?= esc($item['province']); ?> • Cập nhật: <?= esc($item['last_updated']); ?>
                            </small>
                        </div>
                        <div>
                            <?php if ($item['rank_tier'] === 'BEST'): ?>
                                <span class="badge badge-warning" style="background: #D9A441; color: #0A192F; font-size: 0.7rem; font-weight: 800;">
                                    BEST #<?= $item['rank_number']; ?>
                                </span>
                            <?php else: ?>
                                <span class="badge badge-secondary" style="background: #1A4C96; color: #fff; font-size: 0.7rem;">
                                    TOP #<?= $item['rank_number']; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="p-3 text-center bg-light" style="border-radius: 0 0 12px 12px;">
                <a href="<?= langBaseUrl('bang-xep-hang'); ?>" class="btn btn-outline-primary btn-sm font-weight-bold" style="font-size: 0.78rem; border-radius: 20px;">
                    Xem Toàn Bộ Bảng Xếp Hạng <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Widget 3: CTA Doanh nghiệp -->
    <div class="card mb-4 border-0 shadow-sm p-4 text-center" style="border-radius: 12px; background: linear-gradient(135deg, #1A4C96 0%, #0A192F 100%); color: #ffffff;">
        <div style="width: 50px; height: 50px; border-radius: 50%; background: rgba(217,164,65,0.2); border: 1px solid #D9A441; margin: 0 auto 12px; display: flex; align-items: center; justify-content: center; color: #D9A441; font-size: 22px;">
            <i class="fa-solid fa-award"></i>
        </div>
        <h5 class="font-serif text-white mb-2" style="font-size: 1rem;">Doanh Nghiệp Của Bạn Đã Có TOP BEST?</h5>
        <p style="font-size: 0.8rem; color: #CBD5E1; margin-bottom: 18px;">
            Gia nhập Bảng vàng Kỷ lục quốc gia để gia tăng uy tín xuất khẩu và được truyền thông toàn cầu qua WORLDKINGS.
        </p>
        <a href="<?= langBaseUrl('doanh-nghiep/dang-ky'); ?>" class="btn btn-tbg-cta btn-block py-2">
            Đăng Ký Thẩm Định Ngay
        </a>
    </div>

    <!-- Widget 4: Sự kiện sắp diễn ra -->
    <div class="card border-0 shadow-sm" style="border-radius: 12px; background: #ffffff;">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="font-serif mb-0" style="font-size: 0.95rem; color: #0A192F;">
                <i class="fa-solid fa-calendar-star text-danger mr-2"></i> Sự Kiện Sắp Diễn Ra
            </h5>
        </div>
        <div class="card-body p-3">
            <div class="media mb-3 pb-3 border-bottom">
                <div class="mr-3 text-center p-2 rounded" style="background: #F1F5F9; min-width: 50px;">
                    <div style="font-size: 1.1rem; font-weight: 800; color: #1A4C96;">28</div>
                    <div style="font-size: 0.7rem; text-transform: uppercase; font-weight: 700;">Tháng 6</div>
                </div>
                <div class="media-body">
                    <h6 style="font-size: 0.82rem; font-weight: 700; margin-bottom: 3px;">Ngày Hội Gia Đình & Tôn Vinh Doanh Nghiệp</h6>
                    <small class="text-muted"><i class="fa-regular fa-clock mr-1"></i> Sự kiện Quý II</small>
                </div>
            </div>
            <div class="media">
                <div class="mr-3 text-center p-2 rounded" style="background: #FEF3C7; min-width: 50px;">
                    <div style="font-size: 1.1rem; font-weight: 800; color: #B8860B;">20</div>
                    <div style="font-size: 0.7rem; text-transform: uppercase; font-weight: 700; color: #B8860B;">Tháng 11</div>
                </div>
                <div class="media-body">
                    <h6 style="font-size: 0.82rem; font-weight: 700; margin-bottom: 3px;">Đại Lễ Gala Vinh Danh Toàn Quốc TOP BEST</h6>
                    <small class="text-muted"><i class="fa-solid fa-trophy mr-1 text-warning"></i> Trao Cúp Kỷ Lục</small>
                </div>
            </div>
        </div>
    </div>
</div>
