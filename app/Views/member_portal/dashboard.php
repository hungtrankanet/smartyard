<style>
    .member-portal-wrapper { background: #f8fafc; min-height: 85vh; padding: 105px 0 60px 0; }
    .portal-container { max-width: 1280px; margin: 0 auto; padding: 0 20px; }
    .portal-header-card { background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 20px 26px; box-shadow: 0 4px 16px rgba(0,0,0,0.03); margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
    .portal-company-title { font-size: 21px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0; display: flex; align-items: center; gap: 10px; }
    .portal-meta-item { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: #64748b; margin-right: 16px; }
    .portal-grid-layout { display: grid; grid-template-columns: 25% calc(75% - 24px); gap: 24px; align-items: start; }
    @media (max-width: 991px) { .portal-grid-layout { grid-template-columns: 1fr; gap: 20px; } }
    .portal-sidebar { background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 16px; box-shadow: 0 4px 16px rgba(0,0,0,0.03); position: sticky; top: 96px; }
    .portal-user-badge { padding: 12px 14px; background: #f1f5f9; border-radius: 8px; margin-bottom: 14px; font-size: 12px; color: #334155; }
    .portal-user-badge strong { display: block; font-size: 13px; color: #0f172a; margin-bottom: 2px; }
    .portal-nav-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 4px; }
    .portal-nav-item { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 8px; font-weight: 600; font-size: 13.5px; color: #475569; transition: all 0.2s ease; cursor: pointer; text-decoration: none; border-left: 3px solid transparent; }
    .portal-nav-item:hover { background: #f8fafc; color: #1d4ed8; text-decoration: none; }
    .portal-nav-item.active { background: #eff6ff; color: #1d4ed8; font-weight: 700; border-left-color: #1d4ed8; text-decoration: none; }
    .portal-nav-item i { width: 18px; text-align: center; font-size: 15px; }
    .portal-nav-item .nav-badge { margin-left: auto; font-size: 11px; padding: 3px 8px; border-radius: 12px; font-weight: 700; }
    .portal-support-box { margin-top: 16px; padding: 12px; background: #faf5ff; border: 1px dashed #d8b4fe; border-radius: 8px; font-size: 12px; color: #6b21a8; }
    .portal-content-card { background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 26px; box-shadow: 0 4px 16px rgba(0,0,0,0.03); margin-bottom: 24px; }
    .portal-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; flex-wrap: wrap; gap: 10px; }
    .portal-card-title { font-size: 18px; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px; }
    .kpi-grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
    @media (max-width: 640px) { .kpi-grid-3 { grid-template-columns: 1fr; } }
    .kpi-card { background: #fff; border-radius: 10px; border: 1px solid #e2e8f0; padding: 18px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }
    .kpi-num { font-size: 26px; font-weight: 900; margin-top: 4px; }
    .kpi-label { font-size: 11.5px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    .visitor-row { display: flex; align-items: center; justify-content: space-between; padding: 14px 0; border-bottom: 1px solid #f1f5f9; gap: 12px; }
    .msg-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px 18px; margin-bottom: 14px; }
    .msg-card.unread { background: #eff6ff; border-color: #bfdbfe; }
    .portal-input { width: 100%; border: 1.5px solid #cbd5e1; border-radius: 6px; padding: 10px 14px; font-size: 13.5px; color: #1e293b; background: #fff; box-sizing: border-box; }
    .portal-input:focus { border-color: #2563eb; outline: none; box-shadow: 0 0 0 3px rgba(37,99,235,0.12); }
    .b2b-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px); z-index: 99999; display: none; align-items: center; justify-content: center; padding: 20px; box-sizing: border-box; }
    .b2b-modal-window { background: #fff; border-radius: 12px; max-width: 540px; width: 100%; box-shadow: 0 20px 40px rgba(0,0,0,0.2); overflow: hidden; }
</style>

<div class="member-portal-wrapper">
    <div class="portal-container">
        <!-- Top Enterprise Header -->
        <div class="portal-header-card">
            <div>
                <h2 class="portal-company-title"><i class="fa-solid fa-building text-primary" style="color:#1d4ed8;"></i> <?= esc($member->company_name); ?></h2>
                <div style="display:flex; flex-wrap:wrap; align-items:center; gap:8px;">
                    <span class="portal-meta-item"><i class="fa-solid fa-user-tie"></i> Đại diện: <strong><?= esc($member->representative_name ?: $user->username); ?></strong></span>
                    <span class="portal-meta-item"><i class="fa-regular fa-envelope"></i> <?= esc($member->email ?: $user->email); ?></span>
                    <span class="portal-meta-item"><i class="fa-solid fa-phone"></i> <?= esc($member->phone ?: 'Chưa cập nhật'); ?></span>
                    <span>
                        <?php if ($member->verify_status === 'verified'): ?>
                            <span class="label label-success" style="background:#16a34a; color:#fff; padding:4px 10px; border-radius:12px; font-size:11.5px;"><i class="fa-solid fa-circle-check"></i> Đã Xác Minh</span>
                        <?php else: ?>
                            <span class="label label-warning" style="background:#f59e0b; color:#fff; padding:4px 10px; border-radius:12px; font-size:11.5px;"><i class="fa-regular fa-clock"></i> Chờ Xác Minh</span>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
            <div style="display:flex; gap:10px; align-items:center;">
                <button type="button" class="btn-open-msg-modal" data-target-id="" data-target-name="" style="background:#2563eb; color:#fff; font-weight:700; padding:8px 16px; border-radius:6px; border:none; cursor:pointer; font-size:13px;">
                    <i class="fa-solid fa-paper-plane"></i> Soạn Tin B2B
                </button>
                <a href="<?= langBaseUrl('member/logout'); ?>" style="font-weight: 700; color: #dc2626; border: 1px solid #fecaca; background: #fff5f5; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size:13px;">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Đăng xuất
                </a>
            </div>
        </div>

        <!-- 2-Column Responsive Layout: 1/4 Menu + 3/4 Content -->
        <div class="portal-grid-layout">
            <!-- Column 1 (1/4): Menu -->
            <div class="portal-sidebar">
                <div class="portal-user-badge">
                    <strong><?= esc($member->company_name); ?></strong>
                    <span>Tài khoản: <?= esc($user->email); ?></span>
                </div>
                <div class="portal-nav-list">
                    <div class="portal-nav-item active" data-tab="tab-overview"><i class="fa-solid fa-chart-pie" style="color:#2563eb;"></i><span>Tổng Quan & Hồ Sơ</span></div>
                    <div class="portal-nav-item" data-tab="tab-post"><i class="fa-solid fa-newspaper" style="color:#059669;"></i><span>Bài Viết Giới Thiệu</span>
                        <?php if ($post && $post->status === 'approved'): ?><span class="nav-badge" style="background:#dcfce7; color:#15803d;">Đã duyệt</span>
                        <?php elseif ($post && $post->status === 'pending'): ?><span class="nav-badge" style="background:#fef3c7; color:#b45309;">Chờ duyệt</span><?php endif; ?>
                    </div>
                    <div class="portal-nav-item" data-tab="tab-visitors"><i class="fa-solid fa-eye" style="color:#0284c7;"></i><span>Ai Đã Xem Bạn?</span><span class="nav-badge" style="background:#e0f2fe; color:#0369a1;"><?= (int)($stats['week_views'] ?? 0); ?></span></div>
                    <div class="portal-nav-item" data-tab="tab-messages"><i class="fa-solid fa-envelope-open-text" style="color:#7c3aed;"></i><span>Hộp Thư Đối Tác</span><?php if (!empty($unreadCount)): ?><span class="nav-badge" style="background:#fee2e2; color:#b91c1c;"><?= $unreadCount; ?> mới</span><?php endif; ?></div>
                    <div class="portal-nav-item" data-tab="tab-offers"><i class="fa-solid fa-gift" style="color:#db2777;"></i><span>Thưởng Cước & Sự Kiện</span><span class="nav-badge" style="background:#fce7f3; color:#be185d;">Đặc quyền</span></div>
                </div>
                <div class="portal-support-box">
                    <strong><i class="fa-solid fa-headset"></i> Hỗ Trợ Đối Tác B2B</strong>
                    <span>Hotline: <?= !empty($baseSettings->contact_phone) ? esc($baseSettings->contact_phone) : '028 3997 1199'; ?></span><br>
                    <span>Email: <?= !empty($baseSettings->contact_email) ? esc($baseSettings->contact_email) : 'support@topbestglobal.com'; ?></span>
                </div>
            </div>

            <!-- Column 2 (3/4): Content -->
            <div class="portal-main-content">
                <!-- TAB 1: OVERVIEW -->
                <div id="tab-overview" class="portal-tab-content">
                    <div class="kpi-grid-3">
                        <div class="kpi-card" style="border-top: 4px solid #2563eb;"><div class="kpi-label">Tổng Lượt Xem Hồ Sơ</div><div class="kpi-num" style="color: #2563eb;"><?= (int)($stats['total_views'] ?? 0); ?></div></div>
                        <div class="kpi-card" style="border-top: 4px solid #059669;"><div class="kpi-label">Đối Tác Xem (7 ngày qua)</div><div class="kpi-num" style="color: #059669;"><?= (int)($stats['week_views'] ?? 0); ?></div></div>
                        <div class="kpi-card" style="border-top: 4px solid #7c3aed;"><div class="kpi-label">Tin Nhắn B2B Nhận Được</div><div class="kpi-num" style="color: #7c3aed;"><?= count($messages); ?></div></div>
                    </div>
                    <div class="portal-content-card">
                        <div class="portal-card-header">
                            <h3 class="portal-card-title"><i class="fa-solid fa-id-card text-primary" style="color:#2563eb;"></i> Thông Tin Hồ Sơ Doanh Nghiệp</h3>
                            <button type="button" class="btn-open-msg-modal" data-target-id="" data-target-name="" style="background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe; font-weight:700; padding:6px 14px; border-radius:6px; font-size:12.5px; cursor:pointer;">
                                <i class="fa-solid fa-paper-plane"></i> Gửi Tin Nhắn B2B Tới Đối Tác Khác
                            </button>
                        </div>
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px; font-size: 13.5px; line-height: 2;">
                            <div>
                                <span style="color:#64748b;">Mã số thuế:</span> <strong><?= esc($member->tax_code ?: 'Chưa cập nhật'); ?></strong><br>
                                <span style="color:#64748b;">Người đại diện:</span> <strong><?= esc($member->representative_name ?: 'Chưa cập nhật'); ?></strong><br>
                                <span style="color:#64748b;">Số điện thoại:</span> <strong><?= esc($member->phone ?: 'Chưa cập nhật'); ?></strong><br>
                                <span style="color:#64748b;">Email:</span> <strong><?= esc($member->email ?: 'Chưa cập nhật'); ?></strong>
                            </div>
                            <div>
                                <span style="color:#64748b;">Địa chỉ:</span> <strong><?= esc($member->address ?: 'Chưa cập nhật'); ?></strong><br>
                                <span style="color:#64748b;">Tỉnh / TP:</span> <strong><?= esc($member->city ?: 'Chưa cập nhật'); ?></strong><br>
                                <span style="color:#64748b;">Website:</span> <?= !empty($member->website) ? '<a href="' . esc($member->website) . '" target="_blank" style="color:#2563eb; font-weight:700;">' . esc($member->website) . '</a>' : '<em>Chưa cập nhật</em>'; ?><br>
                                <span style="color:#64748b;">Fanpage:</span> <?= !empty($member->fanpage) ? '<a href="' . esc($member->fanpage) . '" target="_blank" style="color:#2563eb; font-weight:700;">' . esc($member->fanpage) . '</a>' : '<em>Chưa cập nhật</em>'; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: INTRODUCTION POST -->
                <div id="tab-post" class="portal-tab-content" style="display: none;">
                    <div class="portal-content-card">
                        <div class="portal-card-header">
                            <h3 class="portal-card-title"><i class="fa-solid fa-newspaper text-primary" style="color:#059669;"></i> Bài Tự Giới Thiệu Doanh Nghiệp (1 Bài Độc Quyền)</h3>
                            <div>
                                <?php if (!$post): ?><span class="label" style="background:#e2e8f0; color:#475569; padding:5px 12px; border-radius:12px; font-size:12px;">Chưa tạo bài viết</span>
                                <?php elseif ($post->status === 'approved'): ?><span class="label" style="background:#dcfce7; color:#15803d; padding:5px 12px; border-radius:12px; font-size:12px;"><i class="fa-solid fa-circle-check"></i> Đã Duyệt & Công Khai</span>
                                <?php elseif ($post->status === 'rejected'): ?><span class="label" style="background:#fee2e2; color:#b91c1c; padding:5px 12px; border-radius:12px; font-size:12px;"><i class="fa-solid fa-circle-xmark"></i> Bị Từ Chối</span>
                                <?php else: ?><span class="label" style="background:#fef3c7; color:#b45309; padding:5px 12px; border-radius:12px; font-size:12px;"><i class="fa-regular fa-clock"></i> Đang Chờ Admin Duyệt</span><?php endif; ?>
                            </div>
                        </div>
                        <form id="formMemberPost">
                            <div style="margin-bottom:14px;">
                                <label style="display:block; font-weight:700; font-size:13px; color:#334155; margin-bottom:6px;">Tiêu Đề Bài Giới Thiệu <span style="color:#ef4444;">*</span></label>
                                <input type="text" name="title" class="portal-input" value="<?= esc($post->title ?? ('Giới thiệu về ' . $member->company_name)); ?>" required placeholder="Ví dụ: Dịch vụ Vận tải Container Quốc Tế & Thủ tục Hải quan">
                            </div>
                            <div style="margin-bottom:14px;">
                                <label style="display:block; font-weight:700; font-size:13px; color:#334155; margin-bottom:6px;">Tóm Tắt Ngắn Gọn</label>
                                <textarea name="summary" class="portal-input" rows="2" placeholder="Tóm tắt năng lực cốt lõi, dịch vụ thế mạnh..."><?= esc($post->summary ?? ''); ?></textarea>
                            </div>
                            <div style="margin-bottom:14px;">
                                <label style="display:block; font-weight:700; font-size:13px; color:#334155; margin-bottom:6px;">Nội Dung Chi Tiết Doanh Nghiệp & Dịch Vụ <span style="color:#ef4444;">*</span></label>
                                <textarea name="content" class="portal-input" rows="8" required placeholder="Giới thiệu chi tiết năng lực đội xe, kho bãi, tuyến vận chuyển..."><?= esc($post->content ?? ''); ?></textarea>
                            </div>
                            <div style="margin-bottom:18px;">
                                <label style="display:block; font-weight:700; font-size:13px; color:#334155; margin-bottom:6px;">Ảnh Banner Bài Viết</label>
                                <input type="file" name="banner_image" class="portal-input" accept="image/*">
                                <?php if (!empty($post->image_default)): ?><div style="margin-top: 8px;"><img src="<?= base_url($post->image_default); ?>" style="max-height: 80px; border-radius: 6px; border: 1px solid #e2e8f0;"></div><?php endif; ?>
                            </div>
                            <div id="postAlert" style="display:none; padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:13px;"></div>
                            <button type="submit" id="btnSavePost" style="background:#2563eb; color:#fff; font-weight:700; padding:10px 26px; border-radius:8px; border:none; cursor:pointer;"><i class="fa-solid fa-paper-plane"></i> Lưu & Gửi Ban Quản Trị Duyệt</button>
                        </form>
                    </div>
                </div>

                <!-- TAB 3: PROFILE VISITORS -->
                <div id="tab-visitors" class="portal-tab-content" style="display: none;">
                    <div class="portal-content-card">
                        <div class="portal-card-header">
                            <h3 class="portal-card-title"><i class="fa-solid fa-users text-primary" style="color:#0284c7;"></i> Đối Tác Đã Ghé Thăm Hồ Sơ</h3>
                        </div>
                        <?php if (empty($visitors)): ?>
                            <div style="text-align:center; padding: 40px 0; color: #94a3b8;"><i class="fa-regular fa-eye-slash" style="font-size: 36px; margin-bottom: 8px; display:block;"></i><p>Chưa có lượt ghé thăm nào được ghi nhận gần đây.</p></div>
                        <?php else: ?>
                            <div class="visitor-list">
                                <?php foreach ($visitors as $v): ?>
                                    <div class="visitor-row">
                                        <div>
                                            <strong style="color: #0f172a; font-size: 14.5px;"><?= esc($v->viewer_company_name ?: 'Doanh nghiệp đối tác TOP BEST GLOBAL'); ?></strong>
                                            <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                                                <span><i class="fa-solid fa-location-dot"></i> <?= esc($v->viewer_city ?: 'Toàn quốc'); ?></span>
                                                <?php if (!empty($v->industry_name)): ?> &bull; <span><?= esc($v->industry_name); ?></span><?php endif; ?>
                                            </div>
                                        </div>
                                        <div style="text-align: right;">
                                            <small style="color:#94a3b8; display:block; margin-bottom:4px;"><?= timeAgo($v->created_at); ?></small>
                                            <?php if (!empty($v->viewer_member_id)): ?>
                                                <button type="button" class="btn-open-msg-modal" data-target-id="<?= $v->viewer_member_id; ?>" data-target-name="<?= esc($v->viewer_company_name); ?>" style="background:#2563eb; color:#fff; border:none; padding:6px 14px; border-radius:6px; font-size:12px; cursor:pointer; font-weight:600;"><i class="fa-solid fa-paper-plane"></i> Gửi Tin Nhắn B2B</button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- TAB 4: MESSAGES -->
                <div id="tab-messages" class="portal-tab-content" style="display: none;">
                    <div class="portal-content-card">
                        <div class="portal-card-header">
                            <h3 class="portal-card-title"><i class="fa-solid fa-inbox text-primary" style="color:#7c3aed;"></i> Hộp Thư B2B & Yêu Cầu Kết Nối</h3>
                            <button type="button" class="btn-open-msg-modal" data-target-id="" data-target-name="" style="background:#7c3aed; color:#fff; border:none; padding:8px 18px; border-radius:6px; font-size:13px; font-weight:700; cursor:pointer;">
                                <i class="fa-solid fa-paper-plane"></i> + Soạn Tin Nhắn B2B Mới
                            </button>
                        </div>
                        <?php if (empty($messages)): ?>
                            <div style="text-align:center; padding: 40px 0; color: #94a3b8;">
                                <i class="fa-regular fa-comments" style="font-size: 36px; margin-bottom: 8px; display:block;"></i>
                                <p style="font-size: 14px; margin-bottom:14px;">Hộp thư trống. Bạn chưa có tin nhắn nào từ đối tác.</p>
                                <button type="button" class="btn-open-msg-modal" data-target-id="" data-target-name="" style="background:#2563eb; color:#fff; border:none; padding:8px 20px; border-radius:6px; font-size:13px; font-weight:700; cursor:pointer;">
                                    <i class="fa-solid fa-paper-plane"></i> Gửi Tin Nhắn / Yêu Cầu Báo Giá Đầu Tiên
                                </button>
                            </div>
                        <?php else: ?>
                            <?php foreach ($messages as $msg): ?>
                                <div class="msg-card <?= empty($msg->is_read) ? 'unread' : ''; ?>" id="msg-card-<?= $msg->id; ?>">
                                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 6px;">
                                        <strong style="color: #0f172a; font-size: 14.5px;"><?= esc($msg->sender_company ?: ($msg->sender_name ?: 'Đối tác')); ?></strong>
                                        <small style="color:#94a3b8;"><?= timeAgo($msg->created_at); ?></small>
                                    </div>
                                    <div style="font-weight: 700; font-size: 13.5px; color: #1e293b; margin-bottom: 6px;"><?= esc($msg->subject); ?></div>
                                    <p style="font-size: 13px; color: #475569; margin-bottom: 10px; line-height: 1.5; white-space: pre-line;"><?= esc($msg->message); ?></p>
                                    <div style="font-size: 12px; color: #64748b; display:flex; gap:16px; align-items:center; flex-wrap:wrap;">
                                        <?php if (!empty($msg->sender_phone)): ?><span><i class="fa-solid fa-phone"></i> SĐT: <a href="tel:<?= esc($msg->sender_phone); ?>" style="color:#2563eb; font-weight:700;"><?= esc($msg->sender_phone); ?></a></span><?php endif; ?>
                                        <?php if (!empty($msg->sender_email)): ?><span><i class="fa-regular fa-envelope"></i> Email: <a href="mailto:<?= esc($msg->sender_email); ?>" style="color:#2563eb; font-weight:700;"><?= esc($msg->sender_email); ?></a></span><?php endif; ?>
                                        <?php if (!empty($msg->sender_member_id)): ?>
                                            <button type="button" class="btn-open-msg-modal" data-target-id="<?= $msg->sender_member_id; ?>" data-target-name="<?= esc($msg->sender_company ?: 'Đối tác'); ?>" style="background:none; border:none; color:#2563eb; font-weight:700; cursor:pointer; padding:0;"><i class="fa-solid fa-reply"></i> Trả lời</button>
                                        <?php endif; ?>
                                        <?php if (empty($msg->is_read)): ?>
                                            <a href="javascript:void(0)" class="btn-mark-read" data-id="<?= $msg->id; ?>" style="margin-left:auto; color:#2563eb; font-weight:700; text-decoration:none;"><i class="fa-solid fa-check"></i> Đánh dấu đã đọc</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- TAB 5: OFFERS & EVENTS -->
                <div id="tab-offers" class="portal-tab-content" style="display: none;">
                    <div class="portal-content-card">
                        <div class="portal-card-header"><h3 class="portal-card-title"><i class="fa-solid fa-gift text-primary" style="color:#db2777;"></i> Đặc Quyền & Chương Trình Thưởng Cước / Sự Kiện</h3></div>
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 20px;">
                                <h4 style="color: #166534; font-weight: 800; margin: 0 0 10px 0; font-size:16px;"><i class="fa-solid fa-trophy"></i> Thưởng Cước Vận Tải Quốc Tế</h4>
                                <p style="font-size: 13px; color: #14532d; line-height: 1.6; margin-bottom:12px;">Nhận hoàn cước và voucher chiết khấu dịch vụ FCL/LCL tuyến Trung - Việt, Đông Nam Á và Bắc Mỹ định kỳ hàng tháng.</p>
                                <span class="label" style="background:#16a34a; color:#fff; padding:4px 12px; border-radius:12px; font-size:11px; font-weight:700;">Đã kích hoạt cho đối tác</span>
                            </div>
                            <div style="background: #faf5ff; border: 1px solid #e9d5ff; border-radius: 10px; padding: 20px;">
                                <h4 style="color: #6b21a8; font-weight: 800; margin: 0 0 10px 0; font-size:16px;"><i class="fa-solid fa-ticket"></i> Vé Tham Gia Sự Kiện B2B Miễn Phí</h4>
                                <p style="font-size: 13px; color: #581c87; line-height: 1.6; margin-bottom:12px;">Đặc quyền tham gia các buổi hội thảo xúc tiến thương mại, kết nối chủ hàng - hãng tàu - logistics không thu phí.</p>
                                <span class="label" style="background:#7c3aed; color:#fff; padding:4px 12px; border-radius:12px; font-size:11px; font-weight:700;">Gửi vé mời tự động qua Email</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Standalone B2B Message Modal (Pure CSS + JS) -->
<div class="b2b-modal-overlay" id="b2bMsgModal">
    <div class="b2b-modal-window">
        <div style="background:#f8fafc; border-bottom:1px solid #e2e8f0; padding:16px 22px; display:flex; justify-content:space-between; align-items:center;">
            <h4 style="font-weight: 800; font-size:16px; margin:0; color:#0f172a;"><i class="fa-solid fa-paper-plane" style="color:#2563eb;"></i> Soạn Tin Nhắn B2B / Báo Giá Logistics</h4>
            <button type="button" class="b2b-modal-close" style="background:none; border:none; font-size:22px; cursor:pointer; color:#64748b; line-height:1;">&times;</button>
        </div>
        <form id="formSendMsg" style="padding:22px;">
            <div style="margin-bottom:14px;" id="boxSelectPartner">
                <label style="display:block; font-weight:700; font-size:13px; margin-bottom:6px; color:#334155;">Doanh nghiệp đối tác nhận tin: <span style="color:#ef4444;">*</span></label>
                <select name="receiver_member_id" id="modalReceiverSelect" class="portal-input" required>
                    <option value="">-- Chọn doanh nghiệp đối tác trong liên minh --</option>
                    <?php if (!empty($otherMembers)): foreach ($otherMembers as $om): ?>
                        <option value="<?= $om->id; ?>"><?= esc($om->company_name); ?> (<?= esc($om->city ?: 'Việt Nam'); ?>)</option>
                    <?php endforeach; endif; ?>
                </select>
                <input type="text" id="modalReceiverNameFixed" class="portal-input" readonly style="display:none; background:#f8fafc; font-weight:700; color:#1d4ed8;">
            </div>
            <div style="margin-bottom:14px;">
                <label style="display:block; font-weight:700; font-size:13px; margin-bottom:6px; color:#334155;">Tiêu đề tin nhắn: <span style="color:#ef4444;">*</span></label>
                <input type="text" name="subject" class="portal-input" value="Yêu cầu kết nối & Báo giá dịch vụ Logistics" required>
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block; font-weight:700; font-size:13px; margin-bottom:6px; color:#334155;">Nội dung tin nhắn / Yêu cầu báo giá chi tiết: <span style="color:#ef4444;">*</span></label>
                <textarea name="message" id="modalMessage" class="portal-input" rows="5" placeholder="Chi tiết tuyến vận chuyển (Cảng đi - Cảng đến), khối lượng hàng (Tấn/CBM/Container), yêu cầu thời gian, thủ tục..." required></textarea>
            </div>
            <div id="msgModalAlert" style="display:none; padding:12px; border-radius:6px; font-size:13px; margin-bottom:14px;"></div>
            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="b2b-modal-close" style="background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; padding:9px 18px; border-radius:6px; font-size:13px; font-weight:600; cursor:pointer;">Đóng</button>
                <button type="submit" id="btnSubmitSendMsg" style="background:#2563eb; color:#fff; font-weight:700; border:none; border-radius:6px; padding:9px 24px; font-size:13px; cursor:pointer;"><i class="fa-solid fa-paper-plane"></i> Gửi Tin Nhắn Ngay</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    var csrfName = '<?= csrf_token(); ?>';
    var csrfHash = '<?= csrf_hash(); ?>';

    $('.portal-nav-item').on('click', function () {
        var targetTab = $(this).data('tab');
        $('.portal-nav-item').removeClass('active');
        $(this).addClass('active');
        $('.portal-tab-content').hide();
        $('#' + targetTab).fadeIn(150);
    });

    $('#formMemberPost').on('submit', function (e) {
        e.preventDefault();
        var $btn = $('#btnSavePost');
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Đang gửi bài viết...');
        var formData = new FormData(this);
        formData.append(csrfName, csrfHash);
        $.ajax({
            url: '<?= langBaseUrl("member/save-post-ajax"); ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (res) {
                if (res.csrf_token) csrfHash = res.csrf_token;
                if (res.status === 'success') {
                    $('#postAlert').css({'background':'#f0fdf4','color':'#166534','border':'1px solid #bbf7d0','display':'block'}).html('<i class="fa-solid fa-circle-check"></i> ' + res.message);
                } else {
                    $('#postAlert').css({'background':'#fef2f2','color':'#991b1b','border':'1px solid #fecaca','display':'block'}).html('<i class="fa-solid fa-circle-exclamation"></i> ' + (res.message || 'Lỗi lưu bài viết.'));
                }
            },
            error: function () {
                $('#postAlert').css({'background':'#fef2f2','color':'#991b1b','border':'1px solid #fecaca','display':'block'}).html('Lỗi kết nối máy chủ khi lưu bài viết.');
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="fa-solid fa-paper-plane"></i> Lưu & Gửi Ban Quản Trị Duyệt');
            }
        });
    });

    $(document).on('click', '.btn-open-msg-modal', function (e) {
        e.preventDefault();
        var id = $(this).data('target-id');
        var name = $(this).data('target-name');
        $('#msgModalAlert').hide();
        $('#modalMessage').val('');

        if (id && name) {
            $('#modalReceiverSelect').hide().prop('required', false);
            $('#modalReceiverNameFixed').show().val(name);
            if ($('#modalReceiverSelect option[value="' + id + '"]').length === 0) {
                $('#modalReceiverSelect').append('<option value="' + id + '">' + name + '</option>');
            }
            $('#modalReceiverSelect').val(id);
        } else {
            $('#modalReceiverSelect').show().prop('required', true).val('');
            $('#modalReceiverNameFixed').hide();
        }
        $('#b2bMsgModal').css('display', 'flex').hide().fadeIn(200);
    });

    $(document).on('click', '.b2b-modal-close', function () {
        $('#b2bMsgModal').fadeOut(150);
    });

    $('#b2bMsgModal').on('click', function (e) {
        if ($(e.target).is('#b2bMsgModal')) {
            $('#b2bMsgModal').fadeOut(150);
        }
    });

    $('#formSendMsg').on('submit', function (e) {
        e.preventDefault();
        var $btn = $('#btnSubmitSendMsg');
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Đang gửi...');
        var data = $(this).serialize() + '&' + csrfName + '=' + csrfHash;
        $.ajax({
            url: '<?= langBaseUrl("member/send-message-ajax"); ?>',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function (res) {
                if (res.csrf_token) csrfHash = res.csrf_token;
                if (res.status === 'success') {
                    $('#msgModalAlert').css({'background':'#f0fdf4','color':'#166534','border':'1px solid #bbf7d0','display':'block'}).html('<i class="fa-solid fa-circle-check"></i> ' + res.message);
                    setTimeout(function () { $('#b2bMsgModal').fadeOut(150); }, 1200);
                } else {
                    $('#msgModalAlert').css({'background':'#fef2f2','color':'#991b1b','border':'1px solid #fecaca','display':'block'}).html('<i class="fa-solid fa-circle-exclamation"></i> ' + (res.message || 'Không thể gửi tin nhắn.'));
                }
            },
            error: function () {
                $('#msgModalAlert').css({'background':'#fef2f2','color':'#991b1b','border':'1px solid #fecaca','display':'block'}).html('Lỗi kết nối máy chủ khi gửi tin nhắn.');
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="fa-solid fa-paper-plane"></i> Gửi Tin Nhắn Ngay');
            }
        });
    });

    $(document).on('click', '.btn-mark-read', function () {
        var id = $(this).data('id');
        var $card = $('#msg-card-' + id);
        var $this = $(this);
        $.post('<?= langBaseUrl("member/mark-message-read-ajax"); ?>/' + id, { [csrfName]: csrfHash }, function (res) {
            $card.removeClass('unread');
            $this.remove();
        }, 'json');
    });
});
</script>
