    <!-- Page Banner -->
    <section class="page-banner" style="background: linear-gradient(135deg, #070e1f 0%, #0b1329 100%); padding: 65px 0 45px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.08); position: relative;">
        <div class="container">
            <span class="badge hero-badge-futuristic" style="margin-bottom: 12px; display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.4); color: #10b981; font-weight: 700;">
                <i class="fa-solid fa-calendar-days"></i> 
                <span class="lang-vi">LỊCH SỰ KIỆN & HỘI THẢO B2B</span>
                <span class="lang-en">B2B LOGISTICS CONFERENCES</span>
            </span>
            <h1 class="hero-title-white" style="font-size: 2.2rem; font-weight: 900; margin-bottom: 10px; color: #fff;">
                <span class="lang-vi">Sự Kiện & Xúc Tiến Thương Mại</span>
                <span class="lang-en">Events & Trade Promotion</span>
            </h1>
            <p style="color: rgba(255,255,255,0.75); max-width: 680px; margin: 0 auto; font-size: 0.92rem; line-height: 1.6;">
                <span class="lang-vi">Cập nhật lịch hội thảo chuyên đề, diễn đàn xuất nhập khẩu và các chương trình kết nối giao thương B2B độc quyền do TOP BEST GLOBAL tổ chức.</span>
                <span class="lang-en">Discover upcoming supply chain seminars, international trade forums, and exclusive B2B networking events by TOP BEST GLOBAL.</span>
            </p>
        </div>
    </section>

    <!-- Events List Section -->
    <section class="section" style="padding: 50px 0 80px; background: #f8fafc;">
        <div class="container">
            
            <!-- Filter Bar -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 14px;">
                <div style="font-size: 1rem; color: #475569; font-weight: 600;">
                    <span class="lang-vi">Hiển thị <strong id="eventsCountDisplay" style="color: #0f172a;"><?= count($events ?? []); ?></strong> sự kiện xúc tiến thương mại</span>
                </div>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <button type="button" class="btn btn-sm btn-primary event-filter-btn active" data-filter="all" onclick="filterEventsList(this, 'all')" style="border-radius: 20px; padding: 7px 18px; font-weight: 700; font-size: 0.82rem;">Tất Cả Sự Kiện</button>
                    <button type="button" class="btn btn-sm btn-outline event-filter-btn" data-filter="upcoming" onclick="filterEventsList(this, 'upcoming')" style="border-radius: 20px; padding: 7px 18px; font-weight: 700; font-size: 0.82rem;">Sắp Diễn Ra</button>
                    <button type="button" class="btn btn-sm btn-outline event-filter-btn" data-filter="international" onclick="filterEventsList(this, 'international')" style="border-radius: 20px; padding: 7px 18px; font-weight: 700; font-size: 0.82rem;">Hội Thảo Quốc Tế</button>
                </div>
            </div>

            <?php if (!empty($events)): ?>
                <div id="eventsListContainer" style="display: flex; flex-direction: column; gap: 24px;">
                    <?php foreach ($events as $ev): 
                        $timestamp = strtotime($ev->event_date);
                        $day = date('d', $timestamp);
                        $month = 'THÁNG ' . date('m', $timestamp);
                        $year = date('Y', $timestamp);
                        $speakers = !empty($ev->speakers_json) ? json_decode($ev->speakers_json, true) : [];
                        $percent = min(100, round(($ev->registered_count / max(1, $ev->max_seats)) * 100));
                        $isInternational = (stripos($ev->title, 'apac') !== false || stripos($ev->title, 'quốc tế') !== false || stripos($ev->location, 'quốc tế') !== false);
                    ?>
                        <article class="card-corporate event-item-card" data-status="<?= esc($ev->status ?? 'upcoming'); ?>" data-type="<?= $isInternational ? 'international' : 'domestic'; ?>" style="background: #ffffff; border-radius: 16px; border: 1.5px solid #e2e8f0; overflow: hidden; display: grid; grid-template-columns: 340px 1fr; box-shadow: 0 4px 18px rgba(0,0,0,0.04); transition: all 0.25s ease;">
                            
                            <!-- Left: Event Image + Date Badge -->
                            <div style="position: relative; overflow: hidden; min-height: 220px;">
                                <img src="<?= esc($ev->image); ?>" alt="<?= esc($ev->title); ?>" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                                
                                <div style="position: absolute; top: 14px; left: 14px; background: #0f172a; color: #fff; border-radius: 10px; padding: 8px 14px; text-align: center; box-shadow: 0 4px 14px rgba(0,0,0,0.35);">
                                    <span style="font-size: 1.6rem; font-weight: 900; line-height: 1; display: block;"><?= $day; ?></span>
                                    <span style="font-size: 0.7rem; font-weight: 700; color: #38bdf8; text-transform: uppercase;"><?= $month; ?></span>
                                    <span style="font-size: 0.65rem; color: #94a3b8; display: block;"><?= $year; ?></span>
                                </div>

                                <span style="position: absolute; bottom: 14px; left: 14px; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(4px); color: #fff; font-size: 0.75rem; font-weight: 700; padding: 4px 10px; border-radius: 6px;">
                                    <i class="fa-solid fa-clock text-primary"></i> <?= esc($ev->event_time); ?>
                                </span>
                            </div>

                            <!-- Right: Detailed Event Information -->
                            <div style="padding: 24px; display: flex; flex-direction: column; justify-content: space-between;">
                                <div>
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px; gap: 8px;">
                                        <span style="display: inline-flex; align-items: center; gap: 4px; background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; font-size: 0.72rem; font-weight: 800; padding: 3px 10px; border-radius: 15px;">
                                            <i class="fa-solid fa-circle-check"></i> ĐANG NHẬN ĐĂNG KÝ
                                        </span>
                                        <span style="font-size: 0.75rem; color: #1d4ed8; background: #eff6ff; padding: 3px 10px; border-radius: 6px; font-weight: 700;">
                                            <i class="fa-solid fa-ticket"></i> <?= esc($ev->fee); ?>
                                        </span>
                                    </div>

                                    <h2 style="font-size: 1.22rem; font-weight: 900; color: #0f172a; margin: 0 0 10px; line-height: 1.35;">
                                        <a href="<?= base_url('events/' . $ev->slug); ?>" style="color: inherit; text-decoration: none;">
                                            <?= esc($ev->title); ?>
                                        </a>
                                    </h2>

                                    <div style="font-size: 0.82rem; color: #334155; display: flex; flex-direction: column; gap: 6px; margin-bottom: 12px; background: #f8fafc; padding: 10px 14px; border-radius: 8px;">
                                        <div style="display: flex; gap: 6px;">
                                            <i class="fa-solid fa-location-dot" style="color: #ef4444; margin-top: 3px;"></i>
                                            <span><strong>Địa điểm:</strong> <?= esc($ev->location); ?></span>
                                        </div>
                                        <div style="display: flex; gap: 6px;">
                                            <i class="fa-solid fa-building" style="color: #3b82f6; margin-top: 3px;"></i>
                                            <span><strong>Đơn vị tổ chức:</strong> <?= esc($ev->organizer); ?></span>
                                        </div>
                                    </div>

                                    <p style="font-size: 0.85rem; color: #475569; line-height: 1.55; margin: 0 0 14px;">
                                        <?= esc($ev->summary); ?>
                                    </p>
                                </div>

                                <!-- Footer: Capacity & Actions -->
                                <div style="border-top: 1px solid #f1f5f9; padding-top: 14px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                                    <div style="flex: 1; min-width: 200px; max-width: 320px;">
                                        <div style="display: flex; justify-content: space-between; font-size: 0.75rem; font-weight: 700; color: #475569; margin-bottom: 4px;">
                                            <span>Số chỗ đã đăng ký</span>
                                            <span style="color: #1d4ed8;"><?= $ev->registered_count; ?> / <?= $ev->max_seats; ?> (<?= $percent; ?>%)</span>
                                        </div>
                                        <div style="height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden;">
                                            <div style="width: <?= $percent; ?>%; height: 100%; background: linear-gradient(90deg, #2563eb, #10b981); border-radius: 3px;"></div>
                                        </div>
                                    </div>

                                    <div style="display: flex; gap: 8px;">
                                        <a href="<?= base_url('events/' . $ev->slug); ?>" class="btn btn-primary" style="border-radius: 8px; font-weight: 700; font-size: 0.85rem; padding: 9px 20px;">
                                            <span class="lang-vi">Xem Lịch Trình & Đăng Ký Vé</span>
                                            <span class="lang-en">View Agenda & Register</span>
                                            <i class="fa-solid fa-arrow-right" style="margin-left: 4px;"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 60px 20px; background: #fff; border-radius: 14px; border: 1px dashed #cbd5e1;">
                    <div style="font-size: 3rem; color: #94a3b8; margin-bottom: 12px;"><i class="fa-solid fa-calendar-xmark"></i></div>
                    <h3 style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin-bottom: 6px;">Chưa có sự kiện mới</h3>
                    <p style="font-size: 0.85rem; color: #64748b;">Vui lòng quay lại sau để cập nhật các sự kiện xúc tiến thương mại mới nhất.</p>
                </div>
            <?php endif; ?>

        </div>
    </section>

    <style>
    @media (max-width: 768px) {
        .card-corporate { grid-template-columns: 1fr !important; }
    }
    </style>

    <script>
    function filterEventsList(btn, filterType) {
        document.querySelectorAll('.event-filter-btn').forEach(function(b) {
            b.classList.remove('btn-primary');
            b.classList.add('btn-outline');
        });
        btn.classList.remove('btn-outline');
        btn.classList.add('btn-primary');

        var count = 0;
        document.querySelectorAll('.event-item-card').forEach(function(card) {
            var status = card.getAttribute('data-status') || 'upcoming';
            var type = card.getAttribute('data-type') || 'domestic';

            if (filterType === 'all') {
                card.style.display = 'grid';
                count++;
            } else if (filterType === 'upcoming' && status === 'upcoming') {
                card.style.display = 'grid';
                count++;
            } else if (filterType === 'international' && type === 'international') {
                card.style.display = 'grid';
                count++;
            } else {
                card.style.display = 'none';
            }
        });

        var countElem = document.getElementById('eventsCountDisplay');
        if (countElem) {
            countElem.innerText = count;
        }
    }
    </script>
