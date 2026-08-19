    <!-- Page Banner -->
    <section class="page-banner" style="background: linear-gradient(135deg, #070e1f 0%, #0b1329 100%); padding: 80px 0 60px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.08);">
        <div class="container">
            <span class="badge hero-badge-futuristic" style="margin-bottom:12px;"><i class="fa-solid fa-headset"></i> 24/7 Support Desk</span>
            <h1 class="hero-title-white" style="font-size: 2.2rem; font-weight: 900; margin-bottom: 12px;">
                <span class="lang-vi">Liên Hệ Văn Phòng</span>
                <span class="lang-en">Contact Our Offices</span>
            </h1>
            <p style="color: rgba(255,255,255,0.7); max-width: 600px; margin: 0 auto; font-size: 0.9rem;">
                <span class="lang-vi">Chúng tôi sẵn sàng tư vấn và giải đáp mọi yêu cầu logistics của doanh nghiệp 24/7.</span>
                <span class="lang-en">We are ready to advise and solve all corporate logistics requests 24/7.</span>
            </p>
        </div>
    </section>

    <!-- Contact Form & Info -->
    <section class="section" style="padding: 70px 0;">
        <div class="container grid grid-2 gap-lg">
            <div>
                <span class="section-tag" data-i18n="nav_contact">
                    <span class="lang-vi">Gửi Yêu Cầu Tư Vấn</span>
                    <span class="lang-en">Submit Consultation Request</span>
                </span>
                <h2 class="section-title">
                    <span class="lang-vi">Kết Nối Với Chuyên Gia TOP BEST GLOBAL</span>
                    <span class="lang-en">Connect With TOP BEST GLOBAL Experts</span>
                </h2>
                <p style="color: var(--text-muted); line-height: 1.7; margin-bottom: 24px;">
                    <span class="lang-vi">Điền thông tin vào biểu mẫu bên dưới, chuyên viên tư vấn chuỗi cung ứng của chúng tôi sẽ liên hệ lại trong vòng 15 phút.</span>
                    <span class="lang-en">Fill out the form below and our supply chain advisors will contact you within 15 minutes.</span>
                </p>
                <form action="<?= base_url('api/contact'); ?>" method="post" onsubmit="event.preventDefault(); alert('Cảm ơn bạn! Thông tin liên hệ đã được gửi thành công.'); this.reset();" class="card-corporate" style="padding: 30px;">
                    <?= csrf_field(); ?>
                    <div class="form-group" style="margin-bottom: 16px;">
                        <label style="display: block; font-weight: 700; font-size: 0.8rem; margin-bottom: 6px;">
                            <span class="lang-vi">Họ và tên *</span>
                            <span class="lang-en">Full Name *</span>
                        </label>
                        <input type="text" name="name" placeholder="Nguyễn Văn A" required style="width: 100%; height: 44px; padding: 0 14px; border: 1px solid var(--border); border-radius: 8px;">
                    </div>
                    <div class="grid grid-2 gap-md" style="margin-bottom: 16px;">
                        <div class="form-group">
                            <label style="display: block; font-weight: 700; font-size: 0.8rem; margin-bottom: 6px;">
                                <span class="lang-vi">Email *</span>
                                <span class="lang-en">Email Address *</span>
                            </label>
                            <input type="email" name="email" placeholder="email@congty.com" required style="width: 100%; height: 44px; padding: 0 14px; border: 1px solid var(--border); border-radius: 8px;">
                        </div>
                        <div class="form-group">
                            <label style="display: block; font-weight: 700; font-size: 0.8rem; margin-bottom: 6px;">
                                <span class="lang-vi">Số điện thoại *</span>
                                <span class="lang-en">Phone Number *</span>
                            </label>
                            <input type="text" name="phone" placeholder="0901234567" required style="width: 100%; height: 44px; padding: 0 14px; border: 1px solid var(--border); border-radius: 8px;">
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 700; font-size: 0.8rem; margin-bottom: 6px;">
                            <span class="lang-vi">Nội dung yêu cầu *</span>
                            <span class="lang-en">Inquiry Message *</span>
                        </label>
                        <textarea name="message" rows="4" placeholder="Nhập nội dung cần tư vấn báo giá hoặc hợp tác..." required style="width: 100%; padding: 12px 14px; border: 1px solid var(--border); border-radius: 8px; font-family: inherit; font-size: 0.85rem;"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                        <span class="lang-vi">Gửi Liên Hệ Tư Vấn</span>
                        <span class="lang-en">Submit Consultation Request</span>
                    </button>
                </form>
            </div>
            <div>
                <span class="section-tag">
                    <span class="lang-vi">Trụ Sở Chính</span>
                    <span class="lang-en">Head Office</span>
                </span>
                <h2 class="section-title">
                    <span class="lang-vi">Thông Tin Liên Lạc</span>
                    <span class="lang-en">Contact Details</span>
                </h2>
                <div class="card-corporate" style="padding: 30px; margin-top: 24px; display: flex; flex-direction: column; gap: 20px;">
                    <div style="display: flex; gap: 16px; align-items: flex-start;">
                        <div style="background: rgba(29,78,216,0.1); width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 1.2rem; flex-shrink: 0;">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div>
                            <strong style="display: block; font-size: 0.95rem; margin-bottom: 4px;">
                                <span class="lang-vi">Địa Chỉ Trụ Sở</span>
                                <span class="lang-en">Headquarters Address</span>
                            </strong>
                            <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">
                                <?= !empty($baseSettings->contact_address) ? esc($baseSettings->contact_address) : '20 Đường Hoàng Minh Giám, Phường Đúc Nhuận, TP. Hồ Chí Minh, Việt Nam'; ?>
                            </p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 16px; align-items: flex-start;">
                        <div style="background: rgba(225,29,72,0.1); width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--accent); font-size: 1.2rem; flex-shrink: 0;">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div>
                            <strong style="display: block; font-size: 0.95rem; margin-bottom: 4px;">
                                <span class="lang-vi">Điện Thoại Hotline</span>
                                <span class="lang-en">Hotline Phone</span>
                            </strong>
                            <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;"><?= !empty($baseSettings->contact_phone) ? esc($baseSettings->contact_phone) : '+84.28.39971199'; ?> (24/7 Support)</p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 16px; align-items: flex-start;">
                        <div style="background: rgba(16,185,129,0.1); width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--success); font-size: 1.2rem; flex-shrink: 0;">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div>
                            <strong style="display: block; font-size: 0.95rem; margin-bottom: 4px;">
                                <span class="lang-vi">Email Hỗ Trợ</span>
                                <span class="lang-en">Support Email</span>
                            </strong>
                            <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;"><?= !empty($baseSettings->contact_email) ? esc($baseSettings->contact_email) : 'contact@topbestglobal.com'; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
