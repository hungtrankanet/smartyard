<!-- Coming Soon Page -->
<section class="coming-soon-section" style="
    min-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #07090f 0%, #0d1b3e 50%, #1a2a5e 100%);
    position: relative;
    overflow: hidden;
    padding: 80px 20px;
">
    <!-- Decorative animated orbs -->
    <div style="position:absolute;top:10%;left:8%;width:300px;height:300px;background:radial-gradient(circle,rgba(29,78,216,0.18) 0%,transparent 70%);border-radius:50%;animation:pulse 4s ease-in-out infinite;"></div>
    <div style="position:absolute;bottom:10%;right:8%;width:250px;height:250px;background:radial-gradient(circle,rgba(59,130,246,0.14) 0%,transparent 70%);border-radius:50%;animation:pulse 5s ease-in-out infinite 1s;"></div>

    <div class="container" style="text-align:center;position:relative;z-index:2;">
        <!-- Icon -->
        <div style="margin-bottom:32px;">
            <div style="
                display:inline-flex;
                align-items:center;
                justify-content:center;
                width:100px;
                height:100px;
                border-radius:50%;
                background:linear-gradient(135deg,#1d4ed8,#3b82f6);
                box-shadow:0 0 40px rgba(59,130,246,0.4);
                margin:0 auto;
                animation:float 3s ease-in-out infinite;
            ">
                <i class="fa-solid fa-hammer" style="font-size:2.5rem;color:#fff;"></i>
            </div>
        </div>

        <!-- Badge -->
        <div style="margin-bottom:24px;">
            <span style="
                background:rgba(29,78,216,0.2);
                border:1px solid rgba(59,130,246,0.4);
                color:#60a5fa;
                padding:6px 20px;
                border-radius:30px;
                font-size:0.82rem;
                font-weight:700;
                letter-spacing:0.12em;
                text-transform:uppercase;
            ">
                <span class="lang-vi">🚀 Sắp ra mắt</span>
                <span class="lang-en">🚀 Coming Soon</span>
            </span>
        </div>

        <!-- Heading -->
        <h1 style="color:#fff;font-size:2.8rem;font-weight:800;margin-bottom:16px;line-height:1.2;">
            <span class="lang-vi">Trang đang được phát triển</span>
            <span class="lang-en">Page Under Development</span>
        </h1>

        <!-- Description -->
        <p style="color:rgba(255,255,255,0.65);font-size:1.1rem;max-width:560px;margin:0 auto 40px;line-height:1.7;">
            <span class="lang-vi">
                Chúng tôi đang tích cực xây dựng tính năng này. Vui lòng quay lại sớm để trải nghiệm những dịch vụ mới nhất từ <strong style="color:#60a5fa;">TOP BEST GLOBAL</strong>.
            </span>
            <span class="lang-en">
                We are actively building this feature. Please check back soon to experience the latest services from <strong style="color:#60a5fa;">TOP BEST GLOBAL</strong>.
            </span>
        </p>

        <!-- Action buttons -->
        <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap;">
            <a href="<?= langBaseUrl(); ?>" class="btn btn-primary" style="padding:14px 36px;font-size:1rem;">
                <i class="fa-solid fa-house" style="margin-right:8px;"></i>
                <span class="lang-vi">Về trang chủ</span>
                <span class="lang-en">Back to Home</span>
            </a>
            <a href="<?= langBaseUrl('contact'); ?>" class="btn btn-outline" style="padding:14px 36px;font-size:1rem;border-color:rgba(255,255,255,0.3);color:#fff;">
                <i class="fa-solid fa-envelope" style="margin-right:8px;"></i>
                <span class="lang-vi">Liên hệ chúng tôi</span>
                <span class="lang-en">Contact Us</span>
            </a>
        </div>
    </div>
</section>

<style>
@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-12px); }
}
@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 0.8; }
    50% { transform: scale(1.15); opacity: 1; }
}
</style>
