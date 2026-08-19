<div class="row">
    <div class="col-sm-12 title-section" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
        <div>
            <h3 style="margin: 0; font-size: 24px; font-weight: 800; color: #0f172a;">
                <i class="fa fa-paper-plane text-primary"></i> Email Marketing - Quản Lý Chiến Dịch & Bản Tin
            </h3>
            <p style="color: #64748b; margin: 4px 0 0; font-size: 13px;">Theo dõi hiệu quả gửi, lượt mở (Open Rate %), lượt nhấp (CTR %) và cá nhân hóa chiến dịch tự động tới khách hàng.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="<?= adminUrl('newsletter-groups'); ?>" class="btn btn-default btn-lg" style="font-weight: 700;">
                <i class="fa fa-users text-primary"></i> Quản Lý Nhóm Email
            </a>
            <a href="<?= adminUrl('newsletter-create-campaign'); ?>" class="btn btn-success btn-lg" style="font-weight: 800; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,166,90,0.3);">
                <i class="fa fa-plus-circle"></i> + Tạo Chiến Dịch Mới
            </a>
        </div>
    </div>
</div>

<!-- Nav Tabs: 1. Campaigns | 2. Groups -->
<div class="row" style="margin-bottom: 20px;">
    <div class="col-sm-12">
        <ul class="nav nav-pills" style="background: #fff; padding: 10px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); font-size: 14px; font-weight: 700;">
            <li class="active">
                <a href="<?= adminUrl('newsletter'); ?>" style="background: #2563eb; color: #fff;"><i class="fa fa-paper-plane"></i> 1. Chiến Dịch Email Marketing (<?= count($campaigns ?? []); ?>)</a>
            </li>
            <li>
                <a href="<?= adminUrl('newsletter-groups'); ?>" style="color: #475569;"><i class="fa fa-users text-primary"></i> 2. Quản Lý Nhóm Email & Phân Khúc (<?= (int)($groupsCount ?? 0); ?>)</a>
            </li>
        </ul>
    </div>
</div>

<!-- 1. Email Marketing Campaigns Table -->
<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary" style="border-radius: 10px; border-top: 3px solid #3c8dbc; box-shadow: 0 4px 16px rgba(0,0,0,0.05);">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-bullhorn text-primary"></i> Danh Sách Chiến Dịch Email Marketing</h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-bordered table-striped" style="font-size: 13px;">
                    <thead>
                        <tr style="background: #f8fafc;">
                            <th width="40">ID</th>
                            <th>Tên Chiến Dịch</th>
                            <th>Tiêu Đề Email</th>
                            <th>Ngôn Ngữ</th>
                            <th>Đối Tượng Nhận</th>
                            <th width="120" class="text-center">Đã Gửi / Tổng</th>
                            <th width="110" class="text-center">Đã Mở</th>
                            <th width="110" class="text-center">Đã Nhấp (Click)</th>
                            <th width="110" class="text-center">Trạng Thái</th>
                            <th width="120">Ngày Tạo</th>
                            <th width="140" class="text-center">Tùy Chọn</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($campaigns)): ?>
                            <?php foreach ($campaigns as $camp): 
                                $openRate = $camp->sent_count > 0 ? round(($camp->opened_count / $camp->sent_count) * 100, 1) : 0;
                                $clickRate = $camp->sent_count > 0 ? round(($camp->clicked_count / $camp->sent_count) * 100, 1) : 0;
                            ?>
                                <tr>
                                    <td><strong>#<?= $camp->id; ?></strong></td>
                                    <td>
                                        <strong style="color: #0f172a; font-size: 14px;"><?= esc($camp->title); ?></strong>
                                    </td>
                                    <td><?= esc($camp->subject); ?></td>
                                    <td>
                                        <span class="label <?= $camp->lang_id == 2 ? 'label-info' : 'label-primary'; ?>">
                                            <?= $camp->lang_id == 2 ? 'English' : 'Tiếng Việt'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="label label-default" style="font-size: 11px;">
                                            <?= esc($camp->recipient_type); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <strong style="color: #2563eb;"><?= $camp->sent_count; ?></strong> / <?= $camp->total_recipients; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-green" style="font-size: 12px;"><?= $camp->opened_count; ?> (<?= $openRate; ?>%)</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-purple" style="font-size: 12px;"><?= $camp->clicked_count; ?> (<?= $clickRate; ?>%)</span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($camp->sent_count >= $camp->total_recipients && $camp->total_recipients > 0): ?>
                                            <span class="label label-success">Hoàn thành</span>
                                        <?php elseif ($camp->sent_count > 0): ?>
                                            <span class="label label-warning">Đang gửi</span>
                                        <?php else: ?>
                                            <span class="label label-default">Bản nháp</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="color: #64748b; font-size: 12px;"><?= date('d/m/Y H:i', strtotime($camp->created_at)); ?></td>
                                    <td class="text-center">
                                        <a href="<?= adminUrl('newsletter-send-campaign/' . $camp->id); ?>" class="btn btn-sm btn-primary" title="Xem & Gửi"><i class="fa fa-send"></i> Gửi</a>
                                        <form action="<?= adminUrl('newsletter-delete-campaign'); ?>" method="post" style="display:inline-block;" onsubmit="return confirm('Bạn có chắc muốn xóa chiến dịch này không?');">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="id" value="<?= $camp->id; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Xóa"><i class="fa fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="11" class="text-center text-muted" style="padding: 40px;">
                                    <i class="fa fa-paper-plane-o fa-3x" style="color: #cbd5e1; margin-bottom: 10px; display: block;"></i>
                                    Chưa có chiến dịch email marketing nào. Hãy tạo chiến dịch đầu tiên để kết nối với khách hàng.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>