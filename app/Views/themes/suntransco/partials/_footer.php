    <!-- Footer -->
    <footer class="footer" style="background: #050a18; padding-top: 60px; padding-bottom: 30px;">
        <div class="container grid grid-4">
            <div class="footer-col">
                <a href="<?= langBaseUrl(); ?>" class="logo footer-logo" style="display:flex;align-items:center;gap:10px;margin-bottom:14px;text-decoration:none;">
                    <img src="<?= base_url('assets/themes/suntransco/logo.png'); ?>" alt="TOP BEST GLOBAL Logo" style="height:35px;width:auto;filter:brightness(0) invert(1);">
                    <span class="logo-sun" style="color:var(--white);font-weight:900;letter-spacing:0.5px;">TOP BEST</span> <span class="logo-trans" style="color:var(--white);font-weight:900;letter-spacing:0.5px;">GLOBAL</span>
                </a>
                <p style="color:rgba(255,255,255,0.65);font-size:0.83rem;line-height:1.6;">
                    <span class="lang-vi">Nền tảng logistics & kết nối thương mại toàn cầu tin cậy của cộng đồng doanh nghiệp.</span>
                    <span class="lang-en">Trusted global logistics & enterprise networking platform for businesses.</span>
                </p>
            </div>
            <div class="footer-col">
                <h4 style="color:var(--white);font-size:0.95rem;margin-bottom:16px;">
                    <span class="lang-vi">Dịch Vụ</span>
                    <span class="lang-en">Services</span>
                </h4>
                <ul style="list-style:none;">
                    <li style="margin-bottom:8px;"><a href="<?= langBaseUrl('services'); ?>?tab=air-freight" style="color:rgba(255,255,255,0.65);font-size:0.83rem;"><i class="fa-solid fa-plane fa-xs" style="margin-right:6px;color:var(--primary);"></i>Air Freight</a></li>
                    <li style="margin-bottom:8px;"><a href="<?= langBaseUrl('services'); ?>?tab=sea-freight" style="color:rgba(255,255,255,0.65);font-size:0.83rem;"><i class="fa-solid fa-ship fa-xs" style="margin-right:6px;color:var(--primary);"></i>Sea Freight</a></li>
                    <li style="margin-bottom:8px;"><a href="<?= langBaseUrl('services'); ?>?tab=inland-freight" style="color:rgba(255,255,255,0.65);font-size:0.83rem;"><i class="fa-solid fa-truck fa-xs" style="margin-right:6px;color:var(--primary);"></i>Inland Freight</a></li>
                    <li style="margin-bottom:8px;"><a href="<?= langBaseUrl('services'); ?>?tab=warehousing" style="color:rgba(255,255,255,0.65);font-size:0.83rem;"><i class="fa-solid fa-warehouse fa-xs" style="margin-right:6px;color:var(--primary);"></i>Warehousing</a></li>
                    <li style="margin-bottom:8px;"><a href="<?= langBaseUrl('services'); ?>?tab=customs-clearance" style="color:rgba(255,255,255,0.65);font-size:0.83rem;"><i class="fa-solid fa-file-invoice fa-xs" style="margin-right:6px;color:var(--primary);"></i>Customs Clearance</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4 style="color:var(--white);font-size:0.95rem;margin-bottom:16px;">
                    <span class="lang-vi">Đối Tác</span>
                    <span class="lang-en">Partners</span>
                </h4>
                <ul style="list-style:none;">
                    <li style="margin-bottom:8px;"><a href="<?= langBaseUrl('partners'); ?>" style="color:rgba(255,255,255,0.65);font-size:0.83rem;"><span class="lang-vi">Mạng lưới đối tác</span><span class="lang-en">Partner Network</span></a></li>
                    <li style="margin-bottom:8px;"><a href="<?= langBaseUrl('events'); ?>" style="color:rgba(255,255,255,0.65);font-size:0.83rem;"><span class="lang-vi">Sự kiện & Hội thảo</span><span class="lang-en">Events & Seminars</span></a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4 style="color:var(--white);font-size:0.95rem;margin-bottom:16px;">
                    <span class="lang-vi">Văn Phòng Liên Hệ</span>
                    <span class="lang-en">Contact Office</span>
                </h4>
                <p style="font-size: 0.82rem; margin-bottom: 8px; line-height: 1.4; color:rgba(255,255,255,0.65);"><i class="fa-solid fa-location-dot text-primary"></i> <?= !empty($baseSettings->contact_address) ? esc($baseSettings->contact_address) : '20 Đường Hoàng Minh Giám, Phường Đúc Nhuận, TP.HCM'; ?></p>
                <p style="font-size: 0.82rem; margin-bottom: 12px; color:rgba(255,255,255,0.65);"><i class="fa-solid fa-phone text-primary"></i> <?= !empty($baseSettings->contact_phone) ? esc($baseSettings->contact_phone) : '+84.28.39971199'; ?></p>
                <form action="<?= base_url('api/newsletter'); ?>" method="post" onsubmit="event.preventDefault(); alert('Cảm ơn bạn! Chúng tôi đã ghi nhận thông tin liên hệ.'); this.reset();" style="display:flex; gap:6px;">
                    <?= csrf_field(); ?>
                    <input type="text" name="email" placeholder="Phone or Email..." required style="flex:1; padding: 8px 12px; border: 1px solid rgba(255,255,255,0.15); background: rgba(255,255,255,0.05); color: var(--white); border-radius: var(--radius-xs); font-size: 0.78rem;">
                    <button type="submit" class="btn btn-primary btn-sm" style="padding:8px 12px; font-size:0.75rem;">
                        <span class="lang-vi">Gửi</span>
                        <span class="lang-en">Send</span>
                    </button>
                </form>
            </div>
        </div>

        <div class="footer-bottom text-center" style="margin-top: 40px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.06); font-size: 0.78rem; color: rgba(255,255,255,0.45);">
            <p>
                <?= !empty($baseSettings->copyright) ? esc($baseSettings->copyright) : '&copy; ' . date('Y') . ' TOP BEST GLOBAL Corporation. All rights reserved.'; ?>
            </p>
        </div>
    </footer>

    <!-- Floating Contact Bubbles: Zalo OA & Fanpage -->
    <div class="floating-contact-bubbles" id="floatingContactBubbles">
        <!-- Zalo OA Bubble -->
        <a href="https://zalo.me/<?= preg_replace('/[^0-9]/', '', !empty($baseSettings->contact_phone) ? $baseSettings->contact_phone : '02839971199'); ?>" target="_blank" rel="noopener noreferrer" class="bubble-btn bubble-zalo" title="Zalo Official Account">
            <div class="bubble-icon">
                <span style="font-size: 0.82rem; font-weight: 900; letter-spacing: -0.5px;">Zalo</span>
            </div>
            <span class="bubble-label">Zalo OA</span>
        </a>

        <!-- Fanpage Bubble -->
        <a href="<?= !empty($baseSettings->facebook_url) ? esc($baseSettings->facebook_url) : 'https://facebook.com/topbestglobal'; ?>" target="_blank" rel="noopener noreferrer" class="bubble-btn bubble-facebook" title="Facebook Fanpage">
            <div class="bubble-icon">
                <i class="fa-brands fa-facebook-f"></i>
            </div>
            <span class="bubble-label">Fanpage</span>
        </a>
    </div>

    <?= loadView('partials/_modals'); ?>
    <script src="<?= base_url('assets/themes/suntransco/app.js?v=2.2'); ?>"></script>
</body>
</html>
