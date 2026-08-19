<div class="row">
    <div class="col-sm-12 title-section" style="margin-bottom: 20px;">
        <h3 style="margin: 0; font-size: 22px; font-weight: 800; color: #0f172a;">
            <i class="fa fa-pencil text-primary"></i> Chỉnh Sửa Sự Kiện: <?= esc($event->title); ?>
        </h3>
        <p style="color: #64748b; margin: 4px 0 0; font-size: 13px;">Cập nhật thông tin chi tiết sự kiện, thời gian, địa điểm, ban diễn giả và lịch trình.</p>
    </div>
</div>

<form action="<?= adminUrl('edit-event-post'); ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field(); ?>
    <input type="hidden" name="id" value="<?= $event->id; ?>">

    <div class="row">
        <!-- Main Column -->
        <div class="col-md-8 col-sm-12">
            <div class="box box-primary" style="border-radius: 10px; box-shadow: 0 4px 16px rgba(0,0,0,0.04);">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-info-circle text-primary"></i> 1. Nội Dung Sự Kiện</h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label>Tiêu Đề Sự Kiện: <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="title" required class="form-control input-lg" value="<?= esc($event->title); ?>">
                    </div>

                    <div class="form-group">
                        <label>Đường Dẫn Thân Thiện (Slug):</label>
                        <input type="text" name="slug" class="form-control" value="<?= esc($event->slug); ?>">
                    </div>

                    <div class="form-group">
                        <label>Tóm Tắt Ngắn Sự Kiện (Hiển thị ở card danh sách):</label>
                        <textarea name="summary" class="form-control" rows="3"><?= esc($event->summary); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Nội Dung Chi Tiết Sự Kiện:</label>
                        <textarea name="content" class="form-control" rows="10"><?= esc($event->content); ?></textarea>
                    </div>
                </div>
            </div>

            <!-- 2. Ban Diễn Giả (Speakers Visual Builder) -->
            <div class="box box-success" style="border-radius: 10px; box-shadow: 0 4px 16px rgba(0,0,0,0.04);">
                <div class="box-header with-border" style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 class="box-title"><i class="fa fa-user-circle text-green"></i> 2. Ban Diễn Giả Khách Mời</h3>
                    <button type="button" class="btn btn-sm btn-success" id="btnAddSpeakerRow" style="font-weight: 700;">
                        <i class="fa fa-plus"></i> + Thêm Diễn Giả
                    </button>
                </div>
                <div class="box-body">
                    <div id="speakersContainer">
                        <?php if (!empty($speakers) && is_array($speakers)): 
                            foreach ($speakers as $spk): 
                        ?>
                            <div class="speaker-row" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; margin-bottom: 10px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                <div style="flex: 1; min-width: 180px;">
                                    <label style="font-size: 11px; margin-bottom: 2px; color: #64748b;">Họ và Tên:</label>
                                    <input type="text" name="speaker_names[]" class="form-control" placeholder="VD: Ông Trần Quốc Hùng" value="<?= esc($spk->name ?? ''); ?>">
                                </div>
                                <div style="flex: 1; min-width: 160px;">
                                    <label style="font-size: 11px; margin-bottom: 2px; color: #64748b;">Chức Vụ / Chuyên Gia:</label>
                                    <input type="text" name="speaker_titles[]" class="form-control" placeholder="VD: Cố Vấn Hải Quan" value="<?= esc($spk->title ?? ''); ?>">
                                </div>
                                <div style="flex: 1; min-width: 160px;">
                                    <label style="font-size: 11px; margin-bottom: 2px; color: #64748b;">Doanh Nghiệp / Tổ Chức:</label>
                                    <input type="text" name="speaker_companies[]" class="form-control" placeholder="VD: TOP BEST GLOBAL Board" value="<?= esc($spk->company ?? ''); ?>">
                                </div>
                                <div style="padding-top: 18px;">
                                    <button type="button" class="btn btn-default btn-remove-row" style="color: #ef4444;" title="Xóa dòng"><i class="fa fa-trash"></i></button>
                                </div>
                            </div>
                        <?php endforeach; else: ?>
                            <div class="speaker-row" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; margin-bottom: 10px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                <div style="flex: 1; min-width: 180px;">
                                    <label style="font-size: 11px; margin-bottom: 2px; color: #64748b;">Họ và Tên:</label>
                                    <input type="text" name="speaker_names[]" class="form-control" placeholder="Họ và tên diễn giả">
                                </div>
                                <div style="flex: 1; min-width: 160px;">
                                    <label style="font-size: 11px; margin-bottom: 2px; color: #64748b;">Chức Vụ / Chuyên Gia:</label>
                                    <input type="text" name="speaker_titles[]" class="form-control" placeholder="Chức vụ / Chuyên môn">
                                </div>
                                <div style="flex: 1; min-width: 160px;">
                                    <label style="font-size: 11px; margin-bottom: 2px; color: #64748b;">Doanh Nghiệp / Tổ Chức:</label>
                                    <input type="text" name="speaker_companies[]" class="form-control" placeholder="Tên công ty / Tổ chức">
                                </div>
                                <div style="padding-top: 18px;">
                                    <button type="button" class="btn btn-default btn-remove-row" style="color: #ef4444;" title="Xóa dòng"><i class="fa fa-trash"></i></button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- 3. Lịch Trình Chương Trình (Agenda Visual Builder) -->
            <div class="box box-info" style="border-radius: 10px; box-shadow: 0 4px 16px rgba(0,0,0,0.04);">
                <div class="box-header with-border" style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 class="box-title"><i class="fa fa-clock-o text-info"></i> 3. Lịch Trình Chương Trình (Agenda)</h3>
                    <button type="button" class="btn btn-sm btn-info" id="btnAddAgendaRow" style="font-weight: 700;">
                        <i class="fa fa-plus"></i> + Thêm Mục Lịch Trình
                    </button>
                </div>
                <div class="box-body">
                    <div id="agendaContainer">
                        <?php if (!empty($agenda) && is_array($agenda)): 
                            foreach ($agenda as $ag): 
                        ?>
                            <div class="agenda-row" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; margin-bottom: 10px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                <div style="width: 140px;">
                                    <label style="font-size: 11px; margin-bottom: 2px; color: #64748b;">Khung Giờ:</label>
                                    <input type="text" name="agenda_times[]" class="form-control" placeholder="08:00 - 08:30" value="<?= esc($ag->time ?? ''); ?>">
                                </div>
                                <div style="flex: 1; min-width: 220px;">
                                    <label style="font-size: 11px; margin-bottom: 2px; color: #64748b;">Nội Dung / Tiết Mục:</label>
                                    <input type="text" name="agenda_titles[]" class="form-control" placeholder="Nội dung thảo luận" value="<?= esc($ag->title ?? ''); ?>">
                                </div>
                                <div style="width: 180px;">
                                    <label style="font-size: 11px; margin-bottom: 2px; color: #64748b;">Người Trình Bày:</label>
                                    <input type="text" name="agenda_speakers[]" class="form-control" placeholder="Người trình bày" value="<?= esc($ag->speaker ?? ''); ?>">
                                </div>
                                <div style="padding-top: 18px;">
                                    <button type="button" class="btn btn-default btn-remove-row" style="color: #ef4444;" title="Xóa dòng"><i class="fa fa-trash"></i></button>
                                </div>
                            </div>
                        <?php endforeach; else: ?>
                            <div class="agenda-row" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; margin-bottom: 10px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                <div style="width: 140px;">
                                    <label style="font-size: 11px; margin-bottom: 2px; color: #64748b;">Khung Giờ:</label>
                                    <input type="text" name="agenda_times[]" class="form-control" placeholder="08:00 - 08:30">
                                </div>
                                <div style="flex: 1; min-width: 220px;">
                                    <label style="font-size: 11px; margin-bottom: 2px; color: #64748b;">Nội Dung / Tiết Mục:</label>
                                    <input type="text" name="agenda_titles[]" class="form-control" placeholder="Nội dung thảo luận">
                                </div>
                                <div style="width: 180px;">
                                    <label style="font-size: 11px; margin-bottom: 2px; color: #64748b;">Người Trình Bày:</label>
                                    <input type="text" name="agenda_speakers[]" class="form-control" placeholder="Người trình bày">
                                </div>
                                <div style="padding-top: 18px;">
                                    <button type="button" class="btn btn-default btn-remove-row" style="color: #ef4444;" title="Xóa dòng"><i class="fa fa-trash"></i></button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Options -->
        <div class="col-md-4 col-sm-12">
            <div class="box box-primary" style="border-radius: 10px; box-shadow: 0 4px 16px rgba(0,0,0,0.04);">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-sliders text-primary"></i> Thời Gian & Thiết Lập</h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label>Ngày Diễn Ra: <span style="color:#ef4444;">*</span></label>
                        <input type="date" name="event_date" required class="form-control" value="<?= esc($event->event_date); ?>">
                    </div>

                    <div class="form-group">
                        <label>Khung Giờ: <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="event_time" required class="form-control" value="<?= esc($event->event_time); ?>">
                    </div>

                    <div class="form-group">
                        <label>Hình Thức Tổ Chức:</label>
                        <select name="location_type" class="form-control">
                            <option value="offline" <?= ($event->location_type === 'offline' ? 'selected' : ''); ?>>Trực tiếp tại hội trường (Offline)</option>
                            <option value="online" <?= ($event->location_type === 'online' ? 'selected' : ''); ?>>Trực tuyến qua Zoom / Meet (Online)</option>
                            <option value="hybrid" <?= ($event->location_type === 'hybrid' ? 'selected' : ''); ?>>Kết hợp Trực tiếp & Trực tuyến (Hybrid)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Địa Điểm Tổ Chức: <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="location" required class="form-control" value="<?= esc($event->location); ?>">
                    </div>

                    <div class="form-group">
                        <label>Đơn Vị Tổ Chức:</label>
                        <input type="text" name="organizer" class="form-control" value="<?= esc($event->organizer); ?>">
                    </div>

                    <div class="form-group">
                        <label>Chi Phí / Vé Tham Dự:</label>
                        <input type="text" name="fee" class="form-control" value="<?= esc($event->fee); ?>">
                    </div>

                    <div class="form-group">
                        <label>Số Lượng Chỗ Ngồi Tối Đa:</label>
                        <input type="number" name="max_seats" class="form-control" value="<?= (int)$event->max_seats; ?>" min="1">
                    </div>

                    <div class="form-group">
                        <label>Trạng Thái Sự Kiện:</label>
                        <select name="status" class="form-control">
                            <option value="upcoming" <?= ($event->status === 'upcoming' ? 'selected' : ''); ?>>Sắp diễn ra (Upcoming)</option>
                            <option value="ongoing" <?= ($event->status === 'ongoing' ? 'selected' : ''); ?>>Đang diễn ra (Ongoing)</option>
                            <option value="completed" <?= ($event->status === 'completed' ? 'selected' : ''); ?>>Đã kết thúc (Completed)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Ảnh Đại Diện (URL hoặc Tải lên):</label>
                        <?php if (!empty($event->image)): ?>
                            <div style="margin-bottom: 8px; border-radius: 6px; overflow: hidden; max-height: 140px;">
                                <img src="<?= esc($event->image); ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        <?php endif; ?>
                        <input type="text" name="image_url" class="form-control" value="<?= esc($event->image); ?>" style="margin-bottom: 6px;">
                        <input type="file" name="image_file" accept="image/*" class="form-control">
                    </div>
                </div>

                <div class="box-footer" style="padding: 15px;">
                    <button type="submit" class="btn btn-primary btn-block btn-lg" style="font-weight: 800; padding: 12px; border-radius: 8px;">
                        <i class="fa fa-save"></i> Cập Nhật Sự Kiện
                    </button>
                    <a href="<?= adminUrl('events'); ?>" class="btn btn-default btn-block" style="margin-top: 8px;">
                        <i class="fa fa-arrow-left"></i> Quay lại danh sách
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {
    $('#btnAddSpeakerRow').on('click', function() {
        var html = '<div class="speaker-row" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; margin-bottom: 10px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">' +
            '<div style="flex: 1; min-width: 180px;"><label style="font-size: 11px; margin-bottom: 2px; color: #64748b;">Họ và Tên:</label><input type="text" name="speaker_names[]" class="form-control" placeholder="Họ và tên diễn giả"></div>' +
            '<div style="flex: 1; min-width: 160px;"><label style="font-size: 11px; margin-bottom: 2px; color: #64748b;">Chức Vụ:</label><input type="text" name="speaker_titles[]" class="form-control" placeholder="Chức vụ / Chuyên môn"></div>' +
            '<div style="flex: 1; min-width: 160px;"><label style="font-size: 11px; margin-bottom: 2px; color: #64748b;">Doanh Nghiệp / Tổ Chức:</label><input type="text" name="speaker_companies[]" class="form-control" placeholder="Tên công ty / Tổ chức"></div>' +
            '<div style="padding-top: 18px;"><button type="button" class="btn btn-default btn-remove-row" style="color: #ef4444;" title="Xóa dòng"><i class="fa fa-trash"></i></button></div>' +
            '</div>';
        $('#speakersContainer').append(html);
    });

    $('#btnAddAgendaRow').on('click', function() {
        var html = '<div class="agenda-row" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; margin-bottom: 10px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">' +
            '<div style="width: 140px;"><label style="font-size: 11px; margin-bottom: 2px; color: #64748b;">Khung Giờ:</label><input type="text" name="agenda_times[]" class="form-control" placeholder="10:00 - 11:30"></div>' +
            '<div style="flex: 1; min-width: 220px;"><label style="font-size: 11px; margin-bottom: 2px; color: #64748b;">Nội Dung / Tiết Mục:</label><input type="text" name="agenda_titles[]" class="form-control" placeholder="Nội dung thảo luận"></div>' +
            '<div style="width: 180px;"><label style="font-size: 11px; margin-bottom: 2px; color: #64748b;">Người Trình Bày:</label><input type="text" name="agenda_speakers[]" class="form-control" placeholder="Người trình bày"></div>' +
            '<div style="padding-top: 18px;"><button type="button" class="btn btn-default btn-remove-row" style="color: #ef4444;" title="Xóa dòng"><i class="fa fa-trash"></i></button></div>' +
            '</div>';
        $('#agendaContainer').append(html);
    });

    $(document).on('click', '.btn-remove-row', function() {
        $(this).closest('.speaker-row, .agenda-row').slideUp(150, function() {
            $(this).remove();
        });
    });
});
</script>
