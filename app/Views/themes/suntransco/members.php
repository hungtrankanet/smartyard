    <!-- Page Hero Banner -->
    <section class="page-banner" style="background: linear-gradient(135deg, #070e1f 0%, #0b1329 100%); padding: 65px 0 45px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.08); position: relative;">
        <div class="container">
            <span class="badge hero-badge-futuristic" style="margin-bottom: 12px; display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.4); color: #10b981; font-weight: 700;">
                <i class="fa-solid fa-shield-halved"></i> 
                <span class="lang-vi">MẠNG LƯỚI LIÊN MINH DOANH NGHIỆP</span>
                <span class="lang-en">VERIFIED CORPORATE ALLIANCE</span>
            </span>
            <h1 class="hero-title-white" style="font-size: 2.2rem; font-weight: 900; margin-bottom: 10px; color: #fff;">
                <span class="lang-vi">Danh Bạ Đối Tác Doanh Nghiệp</span>
                <span class="lang-en">Enterprise Partner Directory</span>
            </h1>
            <p style="color: rgba(255,255,255,0.75); max-width: 680px; margin: 0 auto; font-size: 0.92rem; line-height: 1.6;">
                <span class="lang-vi">Mạng lưới kết nối giao thương giữa hơn <?= number_format($totalVerified ?? 0); ?>+ doanh nghiệp xuất nhập khẩu, vận tải, logistics và sản xuất uy tín.</span>
                <span class="lang-en">Alliance network connecting <?= number_format($totalVerified ?? 0); ?>+ verified partner enterprises in global trade, logistics, and manufacturing.</span>
            </p>
        </div>
    </section>

    <!-- SECTION 1: 1:1 Square Industry Categories Grid -->
    <section style="background: #ffffff; padding: 40px 0 35px; border-bottom: 1px solid #e2e8f0;">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div>
                    <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0;">
                        <i class="fa-solid fa-shapes text-primary" style="margin-right: 6px;"></i>
                        <span class="lang-vi">Danh Mục Ngành Nghề</span>
                        <span class="lang-en">Industry Categories</span>
                    </h3>
                    <p style="font-size: 0.8rem; color: #64748b; margin: 2px 0 0;">
                        <span class="lang-vi">Chọn danh mục để lọc nhanh các doanh nghiệp liên kết</span>
                        <span class="lang-en">Select an industry to quickly filter member enterprises</span>
                    </p>
                </div>
                <?php if (!empty($selectedIndustryId)): ?>
                    <a href="<?= base_url('members'); ?>" style="font-size: 0.82rem; font-weight: 700; color: #ef4444; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                        <i class="fa-solid fa-rotate-left"></i> <span class="lang-vi">Xem tất cả</span><span class="lang-en">Reset</span>
                    </a>
                <?php endif; ?>
            </div>

            <!-- 1:1 Aspect Ratio Categories Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 14px;">
                <?php $isAllActive = empty($selectedIndustryId); ?>
                <a href="<?= base_url('members' . (!empty($keyword) ? '?q=' . urlencode($keyword) : '')); ?>" 
                   style="aspect-ratio: 1 / 1; border-radius: 14px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 12px 8px; text-decoration: none; transition: all 0.25s ease; <?= $isAllActive ? 'background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%); color: #ffffff; box-shadow: 0 8px 20px rgba(29, 78, 216, 0.35); transform: translateY(-3px);' : 'background: #f8fafc; color: #334155; border: 1px solid #e2e8f0;'; ?>">
                    <div style="width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; margin-bottom: 8px; <?= $isAllActive ? 'background: rgba(255,255,255,0.2); color: #fff;' : 'background: #eff6ff; color: #1d4ed8;'; ?>">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <span style="font-size: 0.8rem; font-weight: 700; line-height: 1.3;">
                        <span class="lang-vi">Tất cả ngành</span>
                        <span class="lang-en">All Fields</span>
                    </span>
                    <span style="font-size: 0.7rem; font-weight: 700; margin-top: 5px; padding: 2px 7px; border-radius: 10px; <?= $isAllActive ? 'background: rgba(255,255,255,0.25); color: #fff;' : 'background: #e2e8f0; color: #64748b;'; ?>">
                        <?= $totalVerified ?? 0; ?>
                    </span>
                </a>

                <?php if (!empty($industries)): ?>
                    <?php foreach ($industries as $ind): ?>
                        <?php 
                            $isActive = ($selectedIndustryId == $ind->id);
                            $cardUrl = base_url('members?industry_id=' . $ind->id . (!empty($keyword) ? '&q=' . urlencode($keyword) : ''));
                            $count = $ind->member_count ?? 0;
                        ?>
                        <a href="<?= $cardUrl; ?>" 
                           style="aspect-ratio: 1 / 1; border-radius: 14px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 12px 8px; text-decoration: none; transition: all 0.25s ease; <?= $isActive ? 'background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%); color: #ffffff; box-shadow: 0 8px 20px rgba(29, 78, 216, 0.35); transform: translateY(-3px);' : 'background: #f8fafc; color: #334155; border: 1px solid #e2e8f0;'; ?>">
                            <div style="width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; margin-bottom: 8px; <?= $isActive ? 'background: rgba(255,255,255,0.2); color: #fff;' : 'background: #eff6ff; color: #1d4ed8;'; ?>">
                                <i class="<?= esc($ind->icon ?: 'fa-solid fa-briefcase'); ?>"></i>
                            </div>
                            <span style="font-size: 0.78rem; font-weight: 700; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; padding: 0 4px;">
                                <?= esc($ind->name); ?>
                            </span>
                            <span style="font-size: 0.7rem; font-weight: 700; margin-top: 5px; padding: 2px 7px; border-radius: 10px; <?= $isActive ? 'background: rgba(255,255,255,0.25); color: #fff;' : 'background: #e2e8f0; color: #64748b;'; ?>">
                                <?= $count; ?>
                            </span>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- SECTION 2: 4-Column Layout (1 Column Filter Sidebar + 3 Columns Member Cards) -->
    <section class="section" style="padding: 45px 0 80px; background: #f1f5f9;">
        <div class="container">
            <div style="display: flex; gap: 30px; align-items: flex-start; flex-wrap: wrap;">
                
                <!-- COL 1: Filter Sidebar (280px) -->
                <aside style="flex: 0 0 280px; width: 280px; background: #ffffff; border-radius: 14px; padding: 22px; box-shadow: 0 4px 16px rgba(0,0,0,0.04); border: 1px solid #e2e8f0; position: sticky; top: 95px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9;">
                        <h4 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin: 0;">
                            <i class="fa-solid fa-filter text-primary" style="margin-right: 6px;"></i>
                            <span class="lang-vi">Bộ Lọc Tìm Kiếm</span>
                            <span class="lang-en">Search Filters</span>
                        </h4>
                        <a href="<?= base_url('members'); ?>" style="font-size: 0.78rem; color: #64748b; text-decoration: underline;">
                            <span class="lang-vi">Đặt lại</span><span class="lang-en">Reset</span>
                        </a>
                    </div>

                    <form action="<?= base_url('members'); ?>" method="get">
                        <!-- 1. Search Keyword -->
                        <div class="form-group" style="margin-bottom: 16px;">
                            <label style="font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px; display: block;">
                                <span class="lang-vi">Từ khóa / Tên công ty / MST</span>
                                <span class="lang-en">Keyword / Tax Code</span>
                            </label>
                            <input type="text" name="q" value="<?= esc($keyword ?? ''); ?>" placeholder="Nhập tên doanh nghiệp, MST..." class="form-control" style="font-size: 0.85rem; padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1; width: 100%; box-sizing: border-box;">
                        </div>

                        <!-- 2. Industry Type Dropdown -->
                        <div class="form-group" style="margin-bottom: 16px;">
                            <label style="font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px; display: block;">
                                <span class="lang-vi">Ngành nghề kinh doanh</span>
                                <span class="lang-en">Industry Field</span>
                            </label>
                            <select name="industry_id" class="form-control" style="font-size: 0.85rem; padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1; width: 100%;">
                                <option value="">-- Tất cả ngành nghề --</option>
                                <?php if (!empty($industries)): foreach ($industries as $ind): ?>
                                    <option value="<?= $ind->id; ?>" <?= (!empty($selectedIndustryId) && $selectedIndustryId == $ind->id) ? 'selected' : ''; ?>>
                                        <?= esc($ind->name); ?> (<?= $ind->member_count ?? 0; ?>)
                                    </option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>

                        <!-- 3. City Dropdown -->
                        <div class="form-group" style="margin-bottom: 16px;">
                            <label style="font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px; display: block;">
                                <span class="lang-vi">Tỉnh / Thành phố</span>
                                <span class="lang-en">Location / City</span>
                            </label>
                            <select name="city" class="form-control" style="font-size: 0.85rem; padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1; width: 100%;">
                                <option value="">-- Tất cả khu vực --</option>
                                <?php 
                                $cities = ['TP. Hồ Chí Minh', 'Hà Nội', 'Hải Phòng', 'Đà Nẵng', 'Bình Dương', 'Đồng Nai', 'Bà Rịa - Vũng Tàu', 'Bắc Ninh', 'Quảng Ninh', 'Cần Thơ'];
                                foreach ($cities as $c): ?>
                                    <option value="<?= esc($c); ?>" <?= (!empty($selectedCity) && $selectedCity === $c) ? 'selected' : ''; ?>><?= esc($c); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%; border-radius: 8px; padding: 10px; font-weight: 700; font-size: 0.88rem; cursor: pointer;">
                            <i class="fa-solid fa-sliders"></i> <span class="lang-vi">Áp Dụng Bộ Lọc</span><span class="lang-en">Apply Filters</span>
                        </button>
                    </form>
                </aside>

                <!-- COLS 2, 3, 4: Results Area with 3-Column Enterprise Cards Grid -->
                <main style="flex: 1; min-width: 0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
                        <div style="font-size: 0.95rem; color: #475569;">
                            <span class="lang-vi">Tìm thấy <strong style="color: #0f172a;"><?= number_format($totalCount ?? count($members ?? [])); ?></strong> doanh nghiệp xác minh</span>
                        </div>
                    </div>

                    <?php if (!empty($members)): ?>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(290px, 1fr)); gap: 20px;">
                            <?php foreach ($members as $m): ?>
                                <div class="card-corporate member-card" style="background: #ffffff; border-radius: 14px; padding: 20px; box-shadow: 0 4px 16px rgba(0,0,0,0.04); border: 1px solid #e2e8f0; display: flex; flex-direction: column; justify-content: space-between;">
                                    <div>
                                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; gap: 6px;">
                                            <span style="display: inline-flex; align-items: center; gap: 4px; background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; font-size: 0.72rem; font-weight: 800; padding: 3px 8px; border-radius: 15px;">
                                                <i class="fa-solid fa-circle-check"></i> ĐÃ XÁC MINH
                                            </span>
                                            <?php if (!empty($m->industry_name)): ?>
                                                <span style="font-size: 0.72rem; color: #1d4ed8; background: #eff6ff; padding: 3px 8px; border-radius: 6px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 140px;" title="<?= esc($m->industry_name); ?>">
                                                    <i class="<?= esc($m->industry_icon ?: 'fa-solid fa-folder'); ?>"></i> <?= esc($m->industry_name); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <h3 style="font-size: 1.02rem; font-weight: 800; color: #0f172a; margin: 0 0 8px; line-height: 1.35;">
                                            <?= esc($m->company_name); ?>
                                        </h3>

                                        <div style="font-size: 0.8rem; color: #334155; display: flex; flex-direction: column; gap: 6px; margin-bottom: 14px; background: #f8fafc; padding: 10px 12px; border-radius: 8px;">
                                            <?php if (!empty($m->tax_code)): ?>
                                                <div><strong style="color: #64748b;">MST:</strong> <span style="font-family: monospace; font-weight: 700; color: #0f172a;"><?= esc($m->tax_code); ?></span></div>
                                            <?php endif; ?>
                                            <?php if (!empty($m->city) || !empty($m->address)): ?>
                                                <div style="display: flex; gap: 5px;">
                                                    <i class="fa-solid fa-location-dot" style="color: #ef4444; margin-top: 3px; font-size: 0.75rem;"></i>
                                                    <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= esc(!empty($m->city) ? $m->city : $m->address); ?></span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($m->representative_name)): ?>
                                                <div style="display: flex; gap: 5px;">
                                                    <i class="fa-solid fa-user-tie" style="color: #3b82f6; margin-top: 3px; font-size: 0.75rem;"></i>
                                                    <span><strong><?= esc($m->representative_name); ?></strong></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Bottom Action Links & Direct B2B Message Button -->
                                    <div style="border-top: 1px solid #f1f5f9; padding-top: 12px; display: flex; flex-direction: column; gap: 8px;">
                                        <button type="button" class="btn-open-frontend-b2b" 
                                                data-member-id="<?= $m->id; ?>" 
                                                data-company-name="<?= esc($m->company_name); ?>" 
                                                data-industry="<?= esc($m->industry_name ?? ''); ?>"
                                                data-city="<?= esc($m->city ?? ''); ?>"
                                                data-rep="<?= esc($m->representative_name ?? ''); ?>"
                                                style="width: 100%; background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%); color: #fff; border: none; border-radius: 8px; padding: 9px 12px; font-size: 0.82rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 6px; box-shadow: 0 2px 8px rgba(37,99,235,0.25);">
                                            <i class="fa-solid fa-paper-plane"></i> <span class="lang-vi">Nhắn Tin B2B / Báo Giá</span><span class="lang-en">Send B2B Message</span>
                                        </button>
                                        
                                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem;">
                                            <div style="display: flex; gap: 6px;">
                                                <?php if (!empty($m->website)): ?>
                                                    <a href="<?= esc(!preg_match('~^https?://~i', $m->website) ? 'https://' . $m->website : $m->website); ?>" target="_blank" rel="nofollow noopener" style="color: #1d4ed8; text-decoration: none; font-weight: 600;">
                                                        <i class="fa-solid fa-globe"></i> Website
                                                    </a>
                                                <?php endif; ?>
                                                <?php if (!empty($m->phone)): ?>
                                                    <a href="tel:<?= esc($m->phone); ?>" style="color: #059669; text-decoration: none; font-weight: 600; margin-left: 6px;">
                                                        <i class="fa-solid fa-phone"></i> Hotline
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                            <span style="color: #94a3b8;"><i class="fa-solid fa-shield"></i> Verified</span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if (!empty($pager) && !empty($pager->links)): ?>
                            <div style="margin-top: 35px; text-align: center;">
                                <?= $pager->links; ?>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div style="text-align: center; padding: 60px 20px; background: #ffffff; border-radius: 14px; border: 1px dashed #cbd5e1; margin-top: 10px;">
                            <div style="font-size: 3rem; color: #94a3b8; margin-bottom: 12px;"><i class="fa-solid fa-building-circle-check"></i></div>
                            <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin-bottom: 6px;">Không tìm thấy đối tác phù hợp</h3>
                            <a href="<?= base_url('partners'); ?>" class="btn btn-primary" style="border-radius: 25px; padding: 8px 22px; font-weight: 700; font-size: 0.85rem; text-decoration: none; margin-top: 10px; display: inline-block;">
                                <i class="fa-solid fa-rotate-left"></i> Xem tất cả đối tác
                            </a>
                        </div>
                    <?php endif; ?>
                </main>
            </div>
        </div>
    </section>

    <!-- Standalone Frontend B2B Message & RFQ Modal -->
    <div id="frontendB2BModal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px); z-index: 99999; display: none; align-items: center; justify-content: center; padding: 20px; box-sizing: border-box;">
        <div style="background: #ffffff; border-radius: 14px; max-width: 560px; width: 100%; box-shadow: 0 25px 50px rgba(0,0,0,0.25); overflow: hidden; animation: popIn 0.2s ease-out;">
            <div style="background: #0f172a; color: #ffffff; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="font-size: 1.1rem; font-weight: 800; margin: 0; color: #fff; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-paper-plane" style="color: #38bdf8;"></i> Gửi Tin Nhắn B2B / Yêu Cầu Báo Giá
                    </h3>
                    <div style="font-size: 0.78rem; color: #94a3b8; margin-top: 2px;">
                        Tới: <strong id="modalTargetCompany" style="color: #38bdf8;">...</strong>
                    </div>
                </div>
                <button type="button" class="btn-close-frontend-modal" style="background: none; border: none; color: #94a3b8; font-size: 24px; cursor: pointer; line-height: 1;">&times;</button>
            </div>

            <form id="formFrontendB2B" style="padding: 24px;">
                <input type="hidden" name="receiver_member_id" id="modalReceiverMemberId">
                
                <?php if (!authCheck()): ?>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 4px;">Công ty / Đơn vị của bạn: <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="sender_company" required placeholder="Ví dụ: Công ty XNK ABC" style="width: 100%; border: 1.5px solid #cbd5e1; border-radius: 6px; padding: 8px 12px; font-size: 0.85rem; box-sizing: border-box;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 4px;">Người liên hệ: <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="sender_name" required placeholder="Họ và tên" style="width: 100%; border: 1.5px solid #cbd5e1; border-radius: 6px; padding: 8px 12px; font-size: 0.85rem; box-sizing: border-box;">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 4px;">Số điện thoại / Zalo: <span style="color:#ef4444;">*</span></label>
                            <input type="tel" name="sender_phone" required placeholder="Số điện thoại nhận báo giá" style="width: 100%; border: 1.5px solid #cbd5e1; border-radius: 6px; padding: 8px 12px; font-size: 0.85rem; box-sizing: border-box;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 4px;">Email nhận phản hồi:</label>
                            <input type="email" name="sender_email" placeholder="email@company.com" style="width: 100%; border: 1.5px solid #cbd5e1; border-radius: 6px; padding: 8px 12px; font-size: 0.85rem; box-sizing: border-box;">
                        </div>
                    </div>
                <?php else: ?>
                    <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 10px 14px; margin-bottom: 14px; font-size: 0.82rem; color: #1e40af;">
                        <i class="fa-solid fa-circle-user"></i> Gửi với tư cách đối tác: <strong><?= esc(user()->username); ?></strong> (<?= esc(user()->email); ?>)
                    </div>
                <?php endif; ?>

                <div style="margin-bottom: 12px;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 4px;">Nhu cầu / Tiêu đề tin nhắn: <span style="color:#ef4444;">*</span></label>
                    <select name="subject" style="width: 100%; border: 1.5px solid #cbd5e1; border-radius: 6px; padding: 8px 12px; font-size: 0.85rem; box-sizing: border-box;">
                        <option value="Yêu cầu báo giá cước vận tải biển FCL/LCL">Yêu cầu báo giá cước vận tải biển FCL/LCL</option>
                        <option value="Yêu cầu báo giá vận chuyển hàng không Air Freight">Yêu cầu báo giá vận chuyển hàng không Air Freight</option>
                        <option value="Yêu cầu hợp tác đại lý giao nhận Forwarder">Yêu cầu hợp tác đại lý giao nhận Forwarder</option>
                        <option value="Yêu cầu tư vấn thủ tục khai báo hải quan">Yêu cầu tư vấn thủ tục khai báo hải quan</option>
                        <option value="Yêu cầu dịch vụ vận tải nội địa & Kho bãi">Yêu cầu dịch vụ vận tải nội địa & Kho bãi</option>
                        <option value="Đề xuất hợp tác liên minh kinh doanh">Đề xuất hợp tác liên minh kinh doanh</option>
                    </select>
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 4px;">Nội dung tin nhắn chi tiết: <span style="color:#ef4444;">*</span></label>
                    <textarea name="message" required rows="4" placeholder="Ví dụ: Chúng tôi cần báo giá cước 2x40HC tuyến Cát Lái đi Shanghai, dự kiến xuất hàng ngày 25/08. Vui lòng liên hệ lại..." style="width: 100%; border: 1.5px solid #cbd5e1; border-radius: 6px; padding: 10px 12px; font-size: 0.85rem; box-sizing: border-box;"></textarea>
                </div>

                <div id="frontendB2BAlert" style="display: none; padding: 10px 14px; border-radius: 6px; font-size: 0.85rem; margin-bottom: 14px;"></div>

                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn-close-frontend-modal" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 9px 18px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; cursor: pointer;">Đóng</button>
                    <button type="submit" id="btnSubmitFrontendB2B" style="background: linear-gradient(135deg, #1d4ed8, #2563eb); color: #fff; font-weight: 700; border: none; border-radius: 6px; padding: 9px 24px; font-size: 0.85rem; cursor: pointer;">
                        <i class="fa-solid fa-paper-plane"></i> Gửi Tin Nhắn B2B Ngay
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function () {
        var csrfName = '<?= csrf_token(); ?>';
        var csrfHash = '<?= csrf_hash(); ?>';

        $(document).on('click', '.btn-open-frontend-b2b', function (e) {
            e.preventDefault();
            var id = $(this).data('member-id');
            var name = $(this).data('company-name');
            
            $('#modalReceiverMemberId').val(id);
            $('#modalTargetCompany').text(name);
            $('#frontendB2BAlert').hide();
            $('#formFrontendB2B textarea[name="message"]').val('');
            
            $('#frontendB2BModal').css('display', 'flex').hide().fadeIn(200);
        });

        $(document).on('click', '.btn-close-frontend-modal', function () {
            $('#frontendB2BModal').fadeOut(150);
        });

        $('#frontendB2BModal').on('click', function (e) {
            if ($(e.target).is('#frontendB2BModal')) {
                $('#frontendB2BModal').fadeOut(150);
            }
        });

        $('#formFrontendB2B').on('submit', function (e) {
            e.preventDefault();
            var $btn = $('#btnSubmitFrontendB2B');
            $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Đang gửi...');

            var data = $(this).serialize() + '&' + csrfName + '=' + csrfHash;
            $.ajax({
                url: '<?= base_url("member/send-message-ajax"); ?>',
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function (res) {
                    if (res.csrf_token) csrfHash = res.csrf_token;
                    if (res.status === 'success') {
                        $('#frontendB2BAlert').css({'background':'#f0fdf4','color':'#166534','border':'1px solid #bbf7d0','display':'block'}).html('<i class="fa-solid fa-circle-check"></i> ' + res.message);
                        setTimeout(function () {
                            $('#frontendB2BModal').fadeOut(150);
                        }, 1800);
                    } else {
                        $('#frontendB2BAlert').css({'background':'#fef2f2','color':'#991b1b','border':'1px solid #fecaca','display':'block'}).html('<i class="fa-solid fa-circle-exclamation"></i> ' + (res.message || 'Không thể gửi tin nhắn.'));
                    }
                },
                error: function () {
                    $('#frontendB2BAlert').css({'background':'#fef2f2','color':'#991b1b','border':'1px solid #fecaca','display':'block'}).html('Lỗi kết nối máy chủ khi gửi tin nhắn.');
                },
                complete: function () {
                    $btn.prop('disabled', false).html('<i class="fa-solid fa-paper-plane"></i> Gửi Tin Nhắn B2B Ngay');
                }
            });
        });
    });
    </script>
