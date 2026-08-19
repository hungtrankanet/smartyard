<style>
    .event-detail-wrapper { background: #f8fafc; padding: 100px 0 80px 0; }
    .event-container { max-width: 1240px; margin: 0 auto; padding: 0 20px; }
    .event-grid-2col { display: grid; grid-template-columns: 63% calc(37% - 24px); gap: 24px; align-items: start; }
    @media (max-width: 991px) { .event-grid-2col { grid-template-columns: 1fr; gap: 24px; } }
    
    .event-main-card { background: #ffffff; border-radius: 16px; border: 1.5px solid #e2e8f0; overflow: hidden; padding: 32px; box-shadow: 0 4px 18px rgba(0,0,0,0.03); margin-bottom: 24px; }
    .event-hero-banner { width: 100%; aspect-ratio: 16 / 9; border-radius: 12px; object-fit: cover; margin-bottom: 24px; display: block; }
    
    .speaker-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; display: flex; align-items: center; gap: 14px; }
    .speaker-avatar { width: 56px; height: 56px; border-radius: 50%; background: linear-gradient(135deg, #1d4ed8, #2563eb); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; font-weight: 800; }
    
    .agenda-timeline { position: relative; padding-left: 24px; border-left: 2px solid #e2e8f0; margin: 20px 0 10px 10px; }
    .agenda-item { position: relative; margin-bottom: 24px; }
    .agenda-item::before { content: ''; position: absolute; left: -31px; top: 4px; width: 12px; height: 12px; border-radius: 50%; background: #2563eb; border: 3px solid #ffffff; box-shadow: 0 0 0 2px #2563eb; }
    .agenda-time { font-size: 0.78rem; font-weight: 800; color: #2563eb; background: #eff6ff; display: inline-block; padding: 2px 8px; border-radius: 4px; margin-bottom: 4px; }
    
    .event-reg-card { background: #ffffff; border-radius: 16px; border: 2px solid #2563eb; padding: 26px; box-shadow: 0 10px 30px rgba(37,99,235,0.08); position: sticky; top: 95px; }
    .form-group-event { margin-bottom: 12px; }
    .form-label-event { display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 4px; }
    .form-control-event { width: 100%; border: 1.5px solid #cbd5e1; border-radius: 6px; padding: 9px 12px; font-size: 0.84rem; color: #0f172a; box-sizing: border-box; background: #fff; }
    .form-control-event:focus { border-color: #2563eb; outline: none; box-shadow: 0 0 0 3px rgba(37,99,235,0.12); }
</style>

<div class="event-detail-wrapper">
    <div class="event-container">
        
        <!-- Breadcrumb & Back Link -->
        <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
            <a href="<?= base_url('events'); ?>" style="font-size: 0.85rem; font-weight: 700; color: #2563eb; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-arrow-left"></i> <span class="lang-vi">Quay lại danh sách sự kiện</span>
            </a>
            <span style="font-size: 0.78rem; color: #64748b;">
                Mã sự kiện: <strong>EV-<?= $event->id; ?></strong>
            </span>
        </div>

        <div class="event-grid-2col">
            
            <!-- LEFT COLUMN: Event Content, Speakers & Agenda -->
            <div class="event-left-details">
                <div class="event-main-card">
                    
                    <div style="display: flex; gap: 8px; margin-bottom: 12px; flex-wrap: wrap;">
                        <span style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; font-size: 0.72rem; font-weight: 800; padding: 4px 10px; border-radius: 20px;">
                            <i class="fa-solid fa-circle-check"></i> ĐANG MỞ ĐĂNG KÝ
                        </span>
                        <span style="background: #eff6ff; color: #1d4ed8; font-size: 0.72rem; font-weight: 800; padding: 4px 10px; border-radius: 20px;">
                            <i class="fa-solid fa-ticket"></i> <?= esc($event->fee); ?>
                        </span>
                    </div>

                    <h1 style="font-size: 1.8rem; font-weight: 900; color: #0f172a; margin: 0 0 16px; line-height: 1.35;">
                        <?= esc($event->title); ?>
                    </h1>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 16px; margin-bottom: 24px;">
                        <div>
                            <div style="font-size: 0.72rem; color: #64748b; font-weight: 700;">THỜI GIAN</div>
                            <div style="font-size: 0.85rem; font-weight: 800; color: #0f172a; margin-top: 2px;">
                                <i class="fa-solid fa-calendar-day text-primary"></i> <?= date('d/m/Y', strtotime($event->event_date)); ?> (<?= esc($event->event_time); ?>)
                            </div>
                        </div>
                        <div>
                            <div style="font-size: 0.72rem; color: #64748b; font-weight: 700;">ĐỊA ĐIỂM TỔ CHỨC</div>
                            <div style="font-size: 0.85rem; font-weight: 800; color: #0f172a; margin-top: 2px;">
                                <i class="fa-solid fa-location-dot" style="color:#ef4444;"></i> <?= esc($event->location); ?>
                            </div>
                        </div>
                    </div>

                    <img src="<?= esc($event->image); ?>" alt="<?= esc($event->title); ?>" class="event-hero-banner">

                    <div style="font-size: 0.92rem; color: #334155; line-height: 1.7; margin-bottom: 30px;">
                        <h3 style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin-bottom: 12px;">
                            <i class="fa-solid fa-circle-info text-primary"></i> Tổng Quan Về Sự Kiện
                        </h3>
                        <?= $event->content ?: '<p>' . esc($event->summary) . '</p>'; ?>
                    </div>

                    <!-- Key Speakers Section -->
                    <?php if (!empty($speakers)): ?>
                        <div style="margin-bottom: 30px;">
                            <h3 style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin-bottom: 16px;">
                                <i class="fa-solid fa-user-tie text-primary"></i> Diễn Giả & Chuyên Gia Danh Dự
                            </h3>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 14px;">
                                <?php foreach ($speakers as $sp): ?>
                                    <div class="speaker-card">
                                        <div class="speaker-avatar">
                                            <?= mb_substr($sp['name'] ?? 'S', 0, 1); ?>
                                        </div>
                                        <div>
                                            <strong style="font-size: 0.9rem; color: #0f172a; display: block;"><?= esc($sp['name'] ?? ''); ?></strong>
                                            <div style="font-size: 0.78rem; color: #2563eb; font-weight: 600;"><?= esc($sp['title'] ?? ''); ?></div>
                                            <div style="font-size: 0.74rem; color: #64748b;"><?= esc($sp['company'] ?? ''); ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Event Agenda Timeline -->
                    <?php if (!empty($agenda)): ?>
                        <div>
                            <h3 style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin-bottom: 16px;">
                                <i class="fa-solid fa-list-check text-primary"></i> Lịch Trình Chi Tiết (Agenda)
                            </h3>
                            <div class="agenda-timeline">
                                <?php foreach ($agenda as $ag): ?>
                                    <div class="agenda-item">
                                        <span class="agenda-time"><?= esc($ag['time'] ?? ''); ?></span>
                                        <h4 style="font-size: 0.95rem; font-weight: 800; color: #0f172a; margin: 4px 0 2px;">
                                            <?= esc($ag['title'] ?? ''); ?>
                                        </h4>
                                        <?php if (!empty($ag['speaker'])): ?>
                                            <div style="font-size: 0.78rem; color: #64748b;">
                                                <i class="fa-solid fa-microphone" style="font-size: 0.7rem;"></i> Trình bày: <strong><?= esc($ag['speaker']); ?></strong>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

            <!-- RIGHT COLUMN: Sticky Registration Card -->
            <div class="event-right-sidebar">
                <div class="event-reg-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9;">
                        <div>
                            <h3 style="font-size: 1.15rem; font-weight: 900; color: #0f172a; margin: 0;">
                                <i class="fa-solid fa-ticket text-primary"></i> Đăng Ký Tham Dự
                            </h3>
                            <span style="font-size: 0.75rem; color: #16a34a; font-weight: 700;">
                                <?= $event->registered_count; ?> / <?= $event->max_seats; ?> chỗ đã đăng ký
                            </span>
                        </div>
                        <span style="font-size: 0.75rem; background: #eff6ff; color: #1d4ed8; padding: 4px 10px; border-radius: 6px; font-weight: 800;">
                            <?= esc($event->fee); ?>
                        </span>
                    </div>

                    <form id="formEventRegister">
                        <input type="hidden" name="event_id" value="<?= $event->id; ?>">
                        <input type="hidden" name="event_slug" value="<?= esc($event->slug); ?>">

                        <div class="form-group-event">
                            <label class="form-label-event">Họ và tên đại biểu: <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="name" required placeholder="Nguyễn Văn A" class="form-control-event">
                        </div>

                        <div class="form-group-event">
                            <label class="form-label-event">Tên Doanh Nghiệp / Đơn Vị: <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="company" required placeholder="Công ty CP Xuất Nhập Khẩu..." class="form-control-event">
                        </div>

                        <div class="form-group-event">
                            <label class="form-label-event">Chức danh / Vị trí:</label>
                            <input type="text" name="position" placeholder="Giám Đốc / Trưởng phòng XNK..." class="form-control-event">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 12px;">
                            <div>
                                <label class="form-label-event">SĐT / Zalo: <span style="color:#ef4444;">*</span></label>
                                <input type="tel" name="phone" required placeholder="0901234567" class="form-control-event">
                            </div>
                            <div>
                                <label class="form-label-event">Số người tham dự:</label>
                                <select name="attendees" class="form-control-event">
                                    <option value="1">1 Đại biểu</option>
                                    <option value="2">2 Đại biểu</option>
                                    <option value="3">3 Đại biểu</option>
                                    <option value="4">4 Đại biểu</option>
                                    <option value="5">5+ Đại biểu</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group-event">
                            <label class="form-label-event">Email nhận vé mời điện tử: <span style="color:#ef4444;">*</span></label>
                            <input type="email" name="email" required placeholder="email@company.com" class="form-control-event">
                        </div>

                        <div class="form-group-event">
                            <label class="form-label-event">Câu hỏi hoặc nhu cầu kết nối B2B:</label>
                            <textarea name="notes" rows="2" placeholder="Ghi chú câu hỏi muốn gửi tới diễn giả..." class="form-control-event" style="resize:vertical;"></textarea>
                        </div>

                        <div id="eventRegisterAlert" style="display: none; padding: 10px 12px; border-radius: 6px; font-size: 0.8rem; margin-bottom: 12px;"></div>

                        <button type="submit" id="btnSubmitEventReg" style="width: 100%; background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%); color: #fff; font-weight: 800; font-size: 0.9rem; padding: 12px; border-radius: 8px; border: none; cursor: pointer; box-shadow: 0 4px 14px rgba(37,99,235,0.25);">
                            <i class="fa-solid fa-ticket"></i> Xác Nhận Đăng Ký Tham Gia
                        </button>
                    </form>

                    <div style="margin-top: 14px; font-size: 0.74rem; color: #64748b; line-height: 1.4; text-align: center;">
                        <i class="fa-solid fa-shield-halved text-primary"></i> Vé mời & tài liệu hội thảo sẽ được gửi tự động qua Email và Zalo của Quý khách.
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    var csrfName = '<?= csrf_token(); ?>';
    var csrfHash = '<?= csrf_hash(); ?>';

    $('#formEventRegister').on('submit', function (e) {
        e.preventDefault();
        var $btn = $('#btnSubmitEventReg');
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Đang đăng ký vé...');

        var data = $(this).serialize() + '&' + csrfName + '=' + csrfHash;
        $.ajax({
            url: '<?= base_url("api/register-event"); ?>',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function (res) {
                if (res.csrf_token) csrfHash = res.csrf_token;
                if (res.status === 'success') {
                    $('#eventRegisterAlert').css({'background':'#f0fdf4','color':'#166534','border':'1px solid #bbf7d0','display':'block'}).html('<i class="fa-solid fa-circle-check"></i> ' + res.message);
                    $('#formEventRegister')[0].reset();
                } else {
                    $('#eventRegisterAlert').css({'background':'#fef2f2','color':'#991b1b','border':'1px solid #fecaca','display':'block'}).html('<i class="fa-solid fa-circle-exclamation"></i> ' + (res.message || 'Không thể đăng ký.'));
                }
            },
            error: function () {
                $('#eventRegisterAlert').css({'background':'#fef2f2','color':'#991b1b','border':'1px solid #fecaca','display':'block'}).html('Lỗi kết nối máy chủ khi đăng ký sự kiện.');
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="fa-solid fa-ticket"></i> Xác Nhận Đăng Ký Tham Gia');
            }
        });
    });
});
</script>
