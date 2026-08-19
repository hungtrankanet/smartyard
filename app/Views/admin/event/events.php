<div class="row">
    <div class="col-sm-12 title-section" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <div>
            <h3 style="margin: 0; font-size: 22px; font-weight: 800; color: #0f172a;">
                <i class="fa fa-calendar text-primary"></i> Quản Lý Sự Kiện & Hội Thảo B2B
            </h3>
            <p style="color: #64748b; margin: 4px 0 0; font-size: 13px;">Thêm mới, chỉnh sửa, xóa và theo dõi danh sách đại biểu đăng ký tham dự các sự kiện của TOP BEST GLOBAL.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="<?= adminUrl('event-registrations'); ?>" class="btn btn-info btn-lg" style="font-weight: 700;">
                <i class="fa fa-users"></i> Danh Sách Đăng Ký
            </a>
            <a href="<?= adminUrl('add-event'); ?>" class="btn btn-success btn-lg" style="font-weight: 800; border-radius: 8px; box-shadow: 0 4px 12px rgba(22,163,74,0.3);">
                <i class="fa fa-plus-circle"></i> + Thêm Sự Kiện Mới
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary" style="border-radius: 10px; box-shadow: 0 4px 16px rgba(0,0,0,0.04);">
            <div class="box-header with-border" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <h3 class="box-title"><i class="fa fa-list text-primary"></i> Danh Sách Sự Kiện (<?= count($events ?? []); ?>)</h3>
                
                <div style="display: flex; gap: 8px;">
                    <a href="<?= adminUrl('events'); ?>" class="btn btn-sm <?= ($status === 'all' ? 'btn-primary' : 'btn-default'); ?>">Tất cả</a>
                    <a href="<?= adminUrl('events?status=upcoming'); ?>" class="btn btn-sm <?= ($status === 'upcoming' ? 'btn-primary' : 'btn-default'); ?>">Sắp diễn ra</a>
                    <a href="<?= adminUrl('events?status=ongoing'); ?>" class="btn btn-sm <?= ($status === 'ongoing' ? 'btn-primary' : 'btn-default'); ?>">Đang diễn ra</a>
                    <a href="<?= adminUrl('events?status=completed'); ?>" class="btn btn-sm <?= ($status === 'completed' ? 'btn-primary' : 'btn-default'); ?>">Đã kết thúc</a>
                </div>
            </div>
            
            <div class="box-body table-responsive">
                <table class="table table-bordered table-striped" style="font-size: 13px;">
                    <thead>
                        <tr style="background: #f8fafc;">
                            <th width="40">#</th>
                            <th width="100">Hình Ảnh</th>
                            <th>Thông Tin Sự Kiện</th>
                            <th>Thời Gian & Địa Điểm</th>
                            <th width="120" class="text-center">Đăng Ký / Chỗ</th>
                            <th width="110" class="text-center">Trạng Thái</th>
                            <th width="160" class="text-center">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($events)): 
                            $idx = 1;
                            foreach ($events as $ev): 
                        ?>
                            <tr>
                                <td><?= $idx++; ?></td>
                                <td>
                                    <div style="width: 90px; height: 60px; border-radius: 6px; overflow: hidden; background: #e2e8f0;">
                                        <?php if (!empty($ev->image)): ?>
                                            <img src="<?= esc($ev->image); ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <div style="display:flex; align-items:center; justify-content:center; height:100%; color:#94a3b8;"><i class="fa fa-calendar fa-2x"></i></div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <strong style="color: #0f172a; font-size: 14px;"><?= esc($ev->title); ?></strong>
                                    <div style="font-size: 12px; color: #64748b; margin-top: 4px;">
                                        <span><i class="fa fa-building-o"></i> <?= esc($ev->organizer); ?></span> | 
                                        <span><i class="fa fa-tag"></i> <?= esc($ev->fee); ?></span>
                                    </div>
                                    <a href="<?= base_url('events/' . $ev->slug); ?>" target="_blank" style="font-size: 11px; color: #2563eb; text-decoration: underline;">Xem ngoài web &rarr;</a>
                                </td>
                                <td>
                                    <div><i class="fa fa-calendar-check-o text-primary"></i> <strong><?= date('d/m/Y', strtotime($ev->event_date)); ?></strong> (<?= esc($ev->event_time); ?>)</div>
                                    <div style="font-size: 12px; color: #64748b; margin-top: 2px;"><i class="fa fa-map-marker text-danger"></i> <?= esc($ev->location); ?></div>
                                </td>
                                <td class="text-center">
                                    <strong style="color: #16a34a; font-size: 14px;"><?= (int)$ev->registered_count; ?></strong> / <?= (int)$ev->max_seats; ?>
                                    <div style="margin-top: 4px;">
                                        <a href="<?= adminUrl('event-registrations/' . $ev->id); ?>" class="btn btn-xs btn-default"><i class="fa fa-eye"></i> Xem DS</a>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php if ($ev->status === 'upcoming'): ?>
                                        <span class="label label-success">Sắp diễn ra</span>
                                    <?php elseif ($ev->status === 'ongoing'): ?>
                                        <span class="label label-warning">Đang diễn ra</span>
                                    <?php else: ?>
                                        <span class="label label-default">Đã kết thúc</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= adminUrl('edit-event/' . $ev->id); ?>" class="btn btn-sm btn-primary" title="Sửa sự kiện"><i class="fa fa-pencil"></i> Sửa</a>
                                    <form action="<?= adminUrl('delete-event-post'); ?>" method="post" style="display:inline-block;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sự kiện này không?');">
                                        <?= csrf_field(); ?>
                                        <input type="hidden" name="id" value="<?= $ev->id; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Xóa"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted" style="padding: 40px;">
                                    <i class="fa fa-calendar-o fa-3x" style="color: #cbd5e1; margin-bottom: 10px; display: block;"></i>
                                    Chưa có sự kiện nào trong danh sách. Bấm <strong>+ Thêm Sự Kiện Mới</strong> để bắt đầu.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
