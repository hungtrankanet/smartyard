<div class="row">
    <div class="col-sm-12 title-section" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <div>
            <h3 style="margin: 0; font-size: 22px; font-weight: 800; color: #0f172a;">
                <i class="fa fa-users text-primary"></i> Danh Sách Đại Biểu Đăng Ký Sự Kiện
            </h3>
            <p style="color: #64748b; margin: 4px 0 0; font-size: 13px;">Theo dõi danh sách khách mời, doanh nghiệp và đại biểu đăng ký tham dự các sự kiện của TOP BEST GLOBAL.</p>
        </div>
        <div>
            <a href="<?= adminUrl('events'); ?>" class="btn btn-default btn-lg" style="font-weight: 700;">
                <i class="fa fa-arrow-left"></i> Quay lại Sự kiện
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary" style="border-radius: 10px; box-shadow: 0 4px 16px rgba(0,0,0,0.04);">
            <div class="box-header with-border" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <h3 class="box-title"><i class="fa fa-list text-primary"></i> Tổng Số Đăng Ký (<?= count($registrations ?? []); ?>)</h3>
                
                <div style="min-width: 280px;">
                    <select class="form-control" onchange="window.location.href='<?= adminUrl('event-registrations'); ?>/' + (this.value ? this.value : '');">
                        <option value="">-- Lọc theo tất cả sự kiện --</option>
                        <?php if (!empty($events)): foreach ($events as $ev): ?>
                            <option value="<?= $ev->id; ?>" <?= ($selectedEventId == $ev->id ? 'selected' : ''); ?>>
                                <?= esc($ev->title); ?> (<?= date('d/m/Y', strtotime($ev->event_date)); ?>)
                            </option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
            </div>
            
            <div class="box-body table-responsive">
                <table class="table table-bordered table-striped" style="font-size: 13px;">
                    <thead>
                        <tr style="background: #f8fafc;">
                            <th width="40">#</th>
                            <th>Họ và Tên Đại Biểu</th>
                            <th>Số Điện Thoại</th>
                            <th>Email Liên Hệ</th>
                            <th>Doanh Nghiệp / Chức Vụ</th>
                            <th>Sự Kiện Đăng Ký</th>
                            <th width="120">Ngày Đăng Ký</th>
                            <th width="80" class="text-center">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($registrations)): 
                            $idx = 1;
                            foreach ($registrations as $reg): 
                        ?>
                            <tr>
                                <td><?= $idx++; ?></td>
                                <td>
                                    <strong style="color: #0f172a; font-size: 14px;"><?= esc($reg->full_name); ?></strong>
                                    <?php if (!empty($reg->note)): ?>
                                        <div style="font-size: 11px; color: #64748b; margin-top: 2px;"><em>Ghi chú: <?= esc($reg->note); ?></em></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="tel:<?= esc($reg->phone); ?>" style="color: #2563eb; font-weight: 700;"><?= esc($reg->phone); ?></a>
                                </td>
                                <td>
                                    <a href="mailto:<?= esc($reg->email); ?>" style="color: #475569;"><?= esc($reg->email); ?></a>
                                </td>
                                <td>
                                    <strong><?= esc($reg->company_name ?: 'Doanh nghiệp XNK'); ?></strong>
                                    <?php if (!empty($reg->position)): ?>
                                        <div style="font-size: 11px; color: #64748b;"><?= esc($reg->position); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong style="color: #1e3a8a;"><?= esc($reg->event_title ?? 'Sự kiện'); ?></strong>
                                    <?php if (!empty($reg->event_date)): ?>
                                        <div style="font-size: 11px; color: #64748b;">Ngày diễn ra: <?= date('d/m/Y', strtotime($reg->event_date)); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td style="color: #64748b; font-size: 12px;"><?= date('d/m/Y H:i', strtotime($reg->created_at)); ?></td>
                                <td class="text-center">
                                    <form action="<?= adminUrl('delete-event-registration-post'); ?>" method="post" onsubmit="return confirm('Bạn có chắc chắn muốn xóa lượt đăng ký này không?');">
                                        <?= csrf_field(); ?>
                                        <input type="hidden" name="id" value="<?= $reg->id; ?>">
                                        <button type="submit" class="btn btn-xs btn-danger" title="Xóa"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted" style="padding: 40px;">
                                    <i class="fa fa-users fa-3x" style="color: #cbd5e1; margin-bottom: 10px; display: block;"></i>
                                    Chưa có đại biểu nào đăng ký tham gia sự kiện này.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
