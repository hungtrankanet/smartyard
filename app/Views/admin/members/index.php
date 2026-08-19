<div class="row">
    <div class="col-md-12">
        <!-- Quick Statistics Widget Row -->
        <?php if (!empty($stats)): ?>
            <div class="row" style="margin-bottom: 15px;">
                <div class="col-md-2 col-sm-4 col-xs-6">
                    <div class="info-box bg-aqua" style="min-height: 70px; margin-bottom: 10px; border-radius: 6px;">
                        <span class="info-box-icon" style="height: 70px; line-height: 70px; font-size: 28px; background: rgba(0,0,0,0.1);"><i class="fa fa-users"></i></span>
                        <div class="info-box-content" style="margin-left: 70px; padding-top: 8px;">
                            <span class="info-box-text" style="font-size: 11px; text-transform: uppercase;">Tổng Đối Tác</span>
                            <span class="info-box-number" style="font-size: 20px; font-weight: bold;"><?= number_format($stats['total'] ?? 0); ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 col-xs-6">
                    <div class="info-box bg-green" style="min-height: 70px; margin-bottom: 10px; border-radius: 6px;">
                        <span class="info-box-icon" style="height: 70px; line-height: 70px; font-size: 28px; background: rgba(0,0,0,0.1);"><i class="fa fa-check-circle"></i></span>
                        <div class="info-box-content" style="margin-left: 70px; padding-top: 8px;">
                            <span class="info-box-text" style="font-size: 11px; text-transform: uppercase;">Đã Xác Minh</span>
                            <span class="info-box-number" style="font-size: 20px; font-weight: bold;"><?= number_format($stats['verified'] ?? 0); ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 col-xs-6">
                    <div class="info-box bg-yellow" style="min-height: 70px; margin-bottom: 10px; border-radius: 6px;">
                        <span class="info-box-icon" style="height: 70px; line-height: 70px; font-size: 28px; background: rgba(0,0,0,0.1);"><i class="fa fa-clock-o"></i></span>
                        <div class="info-box-content" style="margin-left: 70px; padding-top: 8px;">
                            <span class="info-box-text" style="font-size: 11px; text-transform: uppercase;">Chờ Xác Minh</span>
                            <span class="info-box-number" style="font-size: 20px; font-weight: bold;"><?= number_format($stats['pending'] ?? 0); ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-6">
                    <div class="info-box bg-gray" style="min-height: 70px; margin-bottom: 10px; border-radius: 6px; color: #333 !important;">
                        <span class="info-box-icon" style="height: 70px; line-height: 70px; font-size: 28px; background: rgba(0,0,0,0.06); color: #555;"><i class="fa fa-question-circle"></i></span>
                        <div class="info-box-content" style="margin-left: 70px; padding-top: 8px;">
                            <span class="info-box-text" style="font-size: 11px; text-transform: uppercase; color: #666;">Chưa Xác Minh</span>
                            <span class="info-box-number" style="font-size: 20px; font-weight: bold; color: #333;"><?= number_format($stats['unverified'] ?? 0); ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-6">
                    <div class="info-box bg-red" style="min-height: 70px; margin-bottom: 10px; border-radius: 6px;">
                        <span class="info-box-icon" style="height: 70px; line-height: 70px; font-size: 28px; background: rgba(0,0,0,0.1);"><i class="fa fa-exclamation-triangle"></i></span>
                        <div class="info-box-content" style="margin-left: 70px; padding-top: 8px;">
                            <span class="info-box-text" style="font-size: 11px; text-transform: uppercase;">Đóng Cửa / Lỗi</span>
                            <span class="info-box-number" style="font-size: 20px; font-weight: bold;"><?= number_format($stats['failed'] ?? 0); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Main Box -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><i class="fa fa-address-card-o text-primary"></i> <?= esc($title); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('members/verify-logs'); ?>" class="btn btn-warning btn-add-new" style="margin-right: 5px;">
                        <i class="fa fa-history"></i> Nhật Ký & Tiến Độ Xác Minh
                    </a>
                    <a href="<?= adminUrl('members/add'); ?>" class="btn btn-success btn-add-new" style="margin-right: 5px;">
                        <i class="fa fa-plus"></i> Thêm Thủ Công
                    </a>
                    <a href="<?= adminUrl('members/upload-cards'); ?>" class="btn btn-primary btn-add-new">
                        <i class="fa fa-cloud-upload"></i> Upload Visit Card (AI OCR)
                    </a>
                </div>
            </div>

            <div class="box-body">
                <!-- Filter Bar -->
                <div class="row table-filter-container" style="margin-bottom: 15px;">
                    <div class="col-sm-12">
                        <form action="<?= adminUrl('members'); ?>" method="get" class="form-inline">
                            <div class="item-table-filter" style="margin-right: 8px; margin-bottom: 8px;">
                                <label style="display: block; font-size: 11px; font-weight: 600; color: #555;">Ngành Nghề</label>
                                <select name="industry_type_id" class="form-control select2" style="min-width: 180px;">
                                    <option value="">-- Tất cả ngành nghề --</option>
                                    <?php if (!empty($industries)): ?>
                                        <?php foreach ($industries as $ind): ?>
                                            <option value="<?= $ind->id; ?>" <?= (inputGet('industry_type_id') == $ind->id) ? 'selected' : ''; ?>>
                                                <?= esc($ind->name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="item-table-filter" style="margin-right: 8px; margin-bottom: 8px;">
                                <label style="display: block; font-size: 11px; font-weight: 600; color: #555;">Xác Minh</label>
                                <select name="verify_status" class="form-control" style="min-width: 140px;">
                                    <option value="">-- Tất cả --</option>
                                    <option value="verified" <?= (inputGet('verify_status') === 'verified') ? 'selected' : ''; ?>>Đã xác minh (Verified)</option>
                                    <option value="pending" <?= (inputGet('verify_status') === 'pending') ? 'selected' : ''; ?>>Chờ xác minh (Pending)</option>
                                    <option value="unverified" <?= (inputGet('verify_status') === 'unverified') ? 'selected' : ''; ?>>Chưa xác minh (Unverified)</option>
                                    <option value="failed" <?= (inputGet('verify_status') === 'failed') ? 'selected' : ''; ?>>Đã đóng cửa (Failed)</option>
                                </select>
                            </div>

                            <div class="item-table-filter" style="margin-right: 8px; margin-bottom: 8px;">
                                <label style="display: block; font-size: 11px; font-weight: 600; color: #555;">Phân Loại Đối Tác</label>
                                <select name="member_type" class="form-control" style="min-width: 130px;">
                                    <option value="">-- Tất cả --</option>
                                    <option value="member" <?= (inputGet('member_type') === 'member') ? 'selected' : ''; ?>>Chính thức</option>
                                    <option value="prospect" <?= (inputGet('member_type') === 'prospect') ? 'selected' : ''; ?>>Tiềm năng</option>
                                    <option value="partner" <?= (inputGet('member_type') === 'partner') ? 'selected' : ''; ?>>Đối tác</option>
                                </select>
                            </div>

                            <div class="item-table-filter" style="margin-right: 8px; margin-bottom: 8px; flex: 1;">
                                <label style="display: block; font-size: 11px; font-weight: 600; color: #555;">Tìm Kiếm (VI / EN / Local)</label>
                                <input name="q" class="form-control" placeholder="Tên công ty (VI/EN/Local), MST, SĐT, Email..." type="search" value="<?= esc(inputGet('q')); ?>" style="min-width: 260px;">
                            </div>

                            <div class="item-table-filter" style="margin-bottom: 8px;">
                                <label style="display: block; font-size: 11px;">&nbsp;</label>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Lọc</button>
                                <a href="<?= adminUrl('members'); ?>" class="btn btn-default" title="Reset bộ lọc"><i class="fa fa-refresh"></i></a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Table Members -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" style="font-size: 13px;">
                        <thead>
                            <tr style="background: #f4f6f9;">
                                <th style="width: 40px; text-align: center;">#</th>
                                <th>Doanh Nghiệp / Công Ty</th>
                                <th>Ngành Nghề</th>
                                <th style="width: 95px; text-align: center;">Số Liên Hệ</th>
                                <th>Đại Diện & Liên Hệ</th>
                                <th>Loại</th>
                                <th>Xác Minh Doanh Nghiệp</th>
                                <th>Lịch Xác Minh</th>
                                <th style="width: 110px; text-align: center;">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($members)): ?>
                                <?php foreach ($members as $index => $item): ?>
                                    <tr>
                                        <td style="text-align: center; vertical-align: middle; color: #888;">
                                            <?= $item->id; ?>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <a href="<?= adminUrl('members/detail/' . $item->id); ?>" style="font-weight: 600; color: #1e395b; font-size: 14px;">
                                                <?= esc($item->company_name); ?>
                                            </a>
                                            <?php if (!empty($item->company_name_en) || !empty($item->company_name_local)): ?>
                                                <div style="font-size: 11px; color: #777; margin-top: 1px;">
                                                    <?= esc($item->company_name_en ?? ''); ?>
                                                    <?= !empty($item->company_name_local) ? '(' . esc($item->company_name_local) . ')' : ''; ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($item->tax_code)): ?>
                                                <div style="font-size: 11px; color: #666; margin-top: 2px;">
                                                    <i class="fa fa-barcode"></i> MST: <strong><?= esc($item->tax_code); ?></strong>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($item->city)): ?>
                                                <div style="font-size: 11px; color: #888;">
                                                    <i class="fa fa-map-marker"></i> <?= esc($item->city); ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <?php if (!empty($item->industry_name)): ?>
                                                <span class="label label-info" style="font-size: 11px; font-weight: normal; background-color: #3c8dbc;">
                                                    <i class="<?= !empty($item->industry_icon) ? esc($item->industry_icon) : 'fa fa-folder'; ?>"></i>
                                                    <?= esc($item->industry_name); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted" style="font-size: 11px;">Chưa phân loại</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="vertical-align: middle; text-align: center;">
                                            <span class="badge bg-purple" style="font-size: 12px; padding: 4px 9px;" title="Số lượng người liên hệ đã lưu">
                                                <i class="fa fa-users"></i> <?= $item->contact_count ?? 0; ?>
                                            </span>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <?php if (!empty($item->representative_name)): ?>
                                                <div style="font-weight: 500;"><i class="fa fa-user text-muted"></i> <?= esc($item->representative_name); ?></div>
                                            <?php endif; ?>
                                            <?php if (!empty($item->phone)): ?>
                                                <div style="font-size: 12px; color: #333;"><i class="fa fa-phone text-muted"></i> <?= esc($item->phone); ?></div>
                                            <?php endif; ?>
                                            <?php if (!empty($item->email)): ?>
                                                <div style="font-size: 11px; color: #777;"><i class="fa fa-envelope-o text-muted"></i> <?= esc($item->email); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <?php if ($item->member_type === 'partner'): ?>
                                                <span class="label label-primary" style="font-size: 11px;">Đối tác</span>
                                            <?php elseif ($item->member_type === 'prospect'): ?>
                                                <span class="label label-warning" style="font-size: 11px;">Tiềm năng</span>
                                            <?php else: ?>
                                                <span class="label label-success" style="font-size: 11px;">Chính thức</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="vertical-align: middle;" class="td-verify-status" data-id="<?= $item->id; ?>" data-status="<?= $item->verify_status; ?>">
                                            <?php if ($item->verify_status === 'verified'): ?>
                                                <span class="label label-success" style="font-size: 11px; padding: 4px 8px; display: inline-block;">
                                                    <i class="fa fa-check-circle"></i> Đã xác minh
                                                </span>
                                            <?php elseif ($item->verify_status === 'failed'): ?>
                                                <span class="label label-danger" style="font-size: 11px; padding: 4px 8px; display: inline-block;">
                                                    <i class="fa fa-times-circle"></i> Đã đóng cửa
                                                </span>
                                            <?php elseif ($item->verify_status === 'pending'): ?>
                                                <span class="label label-warning badge-pulse" style="font-size: 11px; padding: 4px 8px; display: inline-block;">
                                                    <i class="fa fa-spinner fa-spin"></i> Đang xác minh...
                                                </span>
                                            <?php else: ?>
                                                <span class="label label-default" style="font-size: 11px; padding: 4px 8px; display: inline-block; background: #e0e0e0; color: #555;">
                                                    <i class="fa fa-question-circle"></i> Chưa xác minh
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="vertical-align: middle; font-size: 11px;">
                                            <div><strong>Lần cuối:</strong> <?= !empty($item->last_verified_at) ? date('d/m/Y H:i', strtotime($item->last_verified_at)) : '<span class="text-muted">Chưa có</span>'; ?></div>
                                            <div style="margin-top: 2px;"><strong>Kế tiếp:</strong> <?= !empty($item->next_verify_at) ? date('d/m/Y', strtotime($item->next_verify_at)) : '<span class="text-muted">Chưa hẹn</span>'; ?></div>
                                        </td>
                                        <td style="vertical-align: middle; text-align: center;" class="td-select-option">
                                            <div class="dropdown">
                                                <button class="btn bg-purple dropdown-toggle btn-select-option" type="button" data-toggle="dropdown" style="font-size: 12px; padding: 4px 10px;">
                                                    Tùy chọn <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu options-dropdown dropdown-menu-right">
                                                    <li>
                                                        <a href="<?= adminUrl('members/detail/' . $item->id); ?>"><i class="fa fa-eye option-icon"></i> Hồ sơ 360°</a>
                                                    </li>
                                                    <li>
                                                        <a href="<?= adminUrl('members/edit/' . $item->id); ?>"><i class="fa fa-edit option-icon"></i> Chỉnh sửa</a>
                                                    </li>
                                                    <li>
                                                        <a href="<?= adminUrl('members/verify/' . $item->id); ?>"><i class="fa fa-refresh option-icon text-success"></i> Xác minh ngay</a>
                                                    </li>
                                                    <li class="divider"></li>
                                                    <li>
                                                        <a href="javascript:void(0)" onclick="deleteItem('Member/deleteMemberPost','<?= $item->id; ?>','Bạn có chắc chắn muốn xoá đối tác này? Dữ liệu danh bạ và chi nhánh cũng sẽ bị xoá.');">
                                                            <i class="fa fa-trash option-icon text-danger"></i> Xoá đối tác
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center" style="padding: 40px 10px; color: #888;">
                                        <i class="fa fa-address-card-o" style="font-size: 36px; color: #ccc; margin-bottom: 10px; display: block;"></i>
                                        Không tìm thấy đối tác nào phù hợp với điều kiện tìm kiếm.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="row" style="margin-top: 15px;">
                    <div class="col-sm-12 text-right">
                        <?= $pager->links; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var pendingItems = $('.td-verify-status[data-status="pending"]');
    if (pendingItems.length > 0) {
        var queue = [];
        pendingItems.each(function() {
            queue.push($(this).data('id'));
        });

        function processNext() {
            if (queue.length === 0) return;
            var memberId = queue.shift();
            var cell = $('.td-verify-status[data-id="' + memberId + '"]');

            var postData = {};
            if (typeof setAjaxData === 'function') postData = setAjaxData(postData);

            $.ajax({
                type: 'POST',
                url: '<?= adminUrl("members/verify-ajax"); ?>/' + memberId,
                data: postData,
                dataType: 'json',
                success: function(res) {
                    if (res && res.status) {
                        cell.attr('data-status', res.status);
                        if (res.status === 'verified') {
                            cell.html('<span class="label label-success" style="font-size: 11px; padding: 4px 8px; display: inline-block;"><i class="fa fa-check-circle"></i> Đã xác minh</span>');
                        } else if (res.status === 'failed') {
                            cell.html('<span class="label label-danger" style="font-size: 11px; padding: 4px 8px; display: inline-block;"><i class="fa fa-times-circle"></i> Đã đóng cửa</span>');
                        } else {
                            cell.html('<span class="label label-default" style="font-size: 11px; padding: 4px 8px; display: inline-block; background: #e0e0e0; color: #555;"><i class="fa fa-question-circle"></i> Chưa xác minh</span>');
                        }
                    }
                },
                complete: function() {
                    setTimeout(processNext, 1200);
                }
            });
        }

        setTimeout(processNext, 1000);
    }
});
</script>
