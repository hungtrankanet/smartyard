<?php
$contacts = $member->contacts ?? [];
$branches = $member->branches ?? [];
$cards = $member->cards ?? [];
$verifyLogs = $member->verify_logs ?? [];
?>

<style>
    .member-profile-header { background: #fff; border: 1px solid #e1e8ed; border-radius: 6px; padding: 20px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
    .member-avatar-box { width: 64px; height: 64px; border-radius: 8px; background: #eef2f7; display: flex; align-items: center; justify-content: center; font-size: 28px; color: #3c8dbc; border: 1px solid #d2d6de; }
    .card-thumb-item { position: relative; border-radius: 6px; overflow: hidden; border: 1px solid #ddd; background: #222; text-align: center; cursor: pointer; transition: transform .2s; margin-bottom: 15px; }
    .card-thumb-item:hover { transform: scale(1.02); box-shadow: 0 4px 10px rgba(0,0,0,0.15); }
    .card-thumb-item img { width: 100%; height: 160px; object-fit: cover; }
    .card-thumb-badge { position: absolute; top: 8px; left: 8px; font-size: 10px; text-transform: uppercase; padding: 3px 8px; }
    .detail-label { color: #888; font-size: 12px; text-transform: uppercase; margin-bottom: 3px; font-weight: 600; }
    .detail-value { font-size: 14px; font-weight: 600; color: #333; margin-bottom: 15px; word-break: break-word; }
    .nav-tabs-custom > .nav-tabs > li.active { border-top-color: #3c8dbc; }
    .nav-tabs-custom > .nav-tabs > li > a { font-weight: 600; color: #555; }
</style>

<div class="row">
    <div class="col-md-12">
        <?= view('admin/includes/_messages'); ?>

        <!-- Member 360 Header Bar -->
        <div class="member-profile-header">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div class="member-avatar-box">
                    <i class="fa fa-building"></i>
                </div>
                <div>
                    <h3 style="margin: 0 0 5px 0; font-weight: 700; color: #1e395b;">
                        <?= esc($member->company_name); ?>
                    </h3>
                    <?php if (!empty($member->company_name_en) || !empty($member->company_name_local)): ?>
                        <div style="font-size: 13px; color: #666; margin-bottom: 5px;">
                            <?php if (!empty($member->company_name_en)): ?>
                                <span style="margin-right: 10px;"><i class="fa fa-globe text-muted"></i> <?= esc($member->company_name_en); ?></span>
                            <?php endif; ?>
                            <?php if (!empty($member->company_name_local)): ?>
                                <span><i class="fa fa-language text-muted"></i> <?= esc($member->company_name_local); ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                        <?php if (!empty($member->tax_code)): ?>
                            <span class="text-muted" style="font-size: 12px;"><i class="fa fa-barcode"></i> MST: <strong><?= esc($member->tax_code); ?></strong></span>
                        <?php endif; ?>

                        <?php if ($member->member_type === 'partner'): ?>
                            <span class="label label-primary">Đối tác chiến lược</span>
                        <?php elseif ($member->member_type === 'prospect'): ?>
                            <span class="label label-warning">Đối tác tiềm năng</span>
                        <?php else: ?>
                            <span class="label label-success">Đối tác chính thức</span>
                        <?php endif; ?>

                        <span id="verifyStatusBadgeContainer">
                            <?php if ($member->verify_status === 'verified'): ?>
                                <span class="label label-success" style="font-size: 12px; padding: 4px 10px;"><i class="fa fa-check-circle"></i> Đã xác minh</span>
                            <?php elseif ($member->verify_status === 'failed'): ?>
                                <span class="label label-danger" style="font-size: 12px; padding: 4px 10px;"><i class="fa fa-times-circle"></i> Đã đóng cửa</span>
                            <?php elseif ($member->verify_status === 'pending'): ?>
                                <span class="label label-warning" style="font-size: 12px; padding: 4px 10px;"><i class="fa fa-clock-o"></i> Chờ xác minh</span>
                            <?php else: ?>
                                <span class="label label-default" style="font-size: 12px; padding: 4px 10px; background: #e0e0e0; color: #555;"><i class="fa fa-question-circle"></i> Chưa xác minh</span>
                            <?php endif; ?>
                        </span>

                        <?php if ($member->status == 1): ?>
                            <span class="label label-success"><i class="fa fa-circle"></i> Hoạt động</span>
                        <?php else: ?>
                            <span class="label label-danger"><i class="fa fa-ban"></i> Tạm khoá</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; gap: 8px; align-items: center;">
                <button type="button" id="btnVerifyNow" class="btn btn-success btn-lg" data-id="<?= $member->id; ?>" style="font-weight: 600;">
                    <i class="fa fa-refresh"></i> <span id="btnVerifyText">Xác Minh Ngay</span>
                </button>
                <a href="<?= adminUrl('members/edit/' . $member->id); ?>" class="btn btn-primary btn-lg">
                    <i class="fa fa-edit"></i> Chỉnh sửa
                </a>
                <a href="<?= adminUrl('members'); ?>" class="btn btn-default btn-lg">
                    <i class="fa fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>

        <!-- 5-Tab 360 Dashboard Container -->
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#tab_company" data-toggle="tab"><i class="fa fa-building-o text-primary"></i> Thông Tin Công Ty</a>
                </li>
                <li>
                    <a href="#tab_contacts" data-toggle="tab"><i class="fa fa-users text-green"></i> Danh Bạ Liên Hệ <span class="badge bg-green"><?= count($contacts); ?></span></a>
                </li>
                <li>
                    <a href="#tab_branches" data-toggle="tab"><i class="fa fa-map-marker text-aqua"></i> Chi Nhánh & Trụ Sở <span class="badge bg-aqua"><?= count($branches); ?></span></a>
                </li>
                <li>
                    <a href="#tab_cards" data-toggle="tab"><i class="fa fa-id-card-o text-purple"></i> Danh Thiếp Visit Cards <span class="badge bg-purple"><?= count($cards); ?></span></a>
                </li>
                <li>
                    <a href="#tab_logs" data-toggle="tab"><i class="fa fa-history text-orange"></i> Lịch Sử Xác Minh <span class="badge bg-orange"><?= count($verifyLogs); ?></span></a>
                </li>
            </ul>

            <div class="tab-content" style="padding: 20px;">
                <!-- TAB 1: Company Profile -->
                <div class="tab-pane active" id="tab_company">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-label">Tên Doanh Nghiệp (Tiếng Việt)</div>
                            <div class="detail-value"><?= esc($member->company_name); ?></div>

                            <div class="detail-label">Tên Tiếng Anh (English)</div>
                            <div class="detail-value"><?= !empty($member->company_name_en) ? esc($member->company_name_en) : '<span class="text-muted">Chưa có</span>'; ?></div>

                            <div class="detail-label">Tên Ngôn Ngữ Gốc (Local / CN / JP / KR)</div>
                            <div class="detail-value"><?= !empty($member->company_name_local) ? esc($member->company_name_local) : '<span class="text-muted">Chưa có</span>'; ?></div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="detail-label">Mã Số Thuế</div>
                                    <div class="detail-value"><?= !empty($member->tax_code) ? esc($member->tax_code) : '<span class="text-muted">Chưa có</span>'; ?></div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="detail-label">Ngôn Ngữ Nhận Diện</div>
                                    <div class="detail-value"><span class="label label-default"><?= strtoupper(esc($member->detected_language ?? 'vi')); ?></span></div>
                                </div>
                            </div>

                            <div class="detail-label">Ngành Nghề Kinh Doanh</div>
                            <div class="detail-value">
                                <?php if (!empty($member->industry_name)): ?>
                                    <span class="label label-info" style="font-size: 12px; font-weight: normal;">
                                        <i class="<?= !empty($member->industry_icon) ? esc($member->industry_icon) : 'fa fa-folder'; ?>"></i> <?= esc($member->industry_name); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">Chưa phân loại</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="detail-label">Địa Chỉ Trụ Sở</div>
                            <div class="detail-value"><?= !empty($member->address) ? esc($member->address) : '<span class="text-muted">Chưa cập nhật</span>'; ?></div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="detail-label">Tỉnh / Thành Phố</div>
                                    <div class="detail-value"><?= !empty($member->city) ? esc($member->city) : '<span class="text-muted">Chưa có</span>'; ?></div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="detail-label">Số Điện Thoại Tổng Đài</div>
                                    <div class="detail-value">
                                        <?= !empty($member->phone) ? '<a href="tel:' . esc($member->phone) . '"><i class="fa fa-phone"></i> ' . esc($member->phone) . '</a>' : '<span class="text-muted">Chưa có</span>'; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="detail-label">Website Doanh Nghiệp</div>
                                    <div class="detail-value">
                                        <?= !empty($member->website) ? '<a href="' . esc($member->website) . '" target="_blank"><i class="fa fa-external-link"></i> ' . esc($member->website) . '</a>' : '<span class="text-muted">Chưa có</span>'; ?>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="detail-label">Facebook Fanpage</div>
                                    <div class="detail-value">
                                        <?= !empty($member->fanpage) ? '<a href="' . esc($member->fanpage) . '" target="_blank"><i class="fa fa-facebook-square"></i> ' . esc($member->fanpage) . '</a>' : '<span class="text-muted">Chưa có</span>'; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="detail-label">Lần Xác Minh Cuối</div>
                                    <div class="detail-value" id="valLastVerified"><?= !empty($member->last_verified_at) ? date('d/m/Y H:i:s', strtotime($member->last_verified_at)) : '<span class="text-muted">Chưa kiểm tra</span>'; ?></div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="detail-label">Lịch Xác Minh Kế Tiếp (+6T)</div>
                                    <div class="detail-value" id="valNextVerify"><?= !empty($member->next_verify_at) ? date('d/m/Y', strtotime($member->next_verify_at)) : '<span class="text-muted">Chưa thiết lập</span>'; ?></div>
                                </div>
                            </div>

                            <?php if (!empty($member->note)): ?>
                                <div class="detail-label">Ghi Chú Nội Bộ</div>
                                <div style="background: #f9f9f9; padding: 10px; border-radius: 4px; border: 1px solid #eee; font-size: 13px; color: #555;">
                                    <?= nl2br(esc($member->note)); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: Contacts Directory -->
                <div class="tab-pane" id="tab_contacts">
                    <?php if (!empty($contacts)): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead style="background: #f4f6f9;">
                                    <tr>
                                        <th style="width: 50px; text-align: center;">#</th>
                                        <th>Họ và Tên Nhân Sự</th>
                                        <th>Chức Vụ & Phòng Ban</th>
                                        <th>Số Điện Thoại</th>
                                        <th>Email</th>
                                        <th>Vai Trò</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($contacts as $cIdx => $cnt): ?>
                                        <tr>
                                            <td style="text-align: center; color: #888;"><?= $cIdx + 1; ?></td>
                                            <td>
                                                <strong style="color: #1e395b; font-size: 14px;"><?= esc($cnt->full_name); ?></strong>
                                                <?php if (!empty($cnt->full_name_en) || !empty($cnt->full_name_local)): ?>
                                                    <div style="font-size: 11px; color: #777;">
                                                        <?= esc($cnt->full_name_en ?? ''); ?> <?= !empty($cnt->full_name_local) ? '(' . esc($cnt->full_name_local) . ')' : ''; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div><strong><?= esc($cnt->position ?? 'Chưa rõ'); ?></strong></div>
                                                <?php if (!empty($cnt->department)): ?>
                                                    <small class="text-muted"><?= esc($cnt->department); ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($cnt->phone)): ?>
                                                    <div><a href="tel:<?= esc($cnt->phone); ?>"><i class="fa fa-phone text-muted"></i> <?= esc($cnt->phone); ?></a></div>
                                                <?php endif; ?>
                                                <?php if (!empty($cnt->phone_2)): ?>
                                                    <div style="font-size: 11px;"><a href="tel:<?= esc($cnt->phone_2); ?>"><i class="fa fa-phone text-muted"></i> <?= esc($cnt->phone_2); ?></a></div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($cnt->email)): ?>
                                                    <div><a href="mailto:<?= esc($cnt->email); ?>"><i class="fa fa-envelope-o text-muted"></i> <?= esc($cnt->email); ?></a></div>
                                                <?php endif; ?>
                                                <?php if (!empty($cnt->email_2)): ?>
                                                    <div style="font-size: 11px;"><a href="mailto:<?= esc($cnt->email_2); ?>"><i class="fa fa-envelope-o text-muted"></i> <?= esc($cnt->email_2); ?></a></div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($cnt->is_primary)): ?>
                                                    <span class="label label-success"><i class="fa fa-star"></i> Đại diện chính</span>
                                                <?php else: ?>
                                                    <span class="label label-default">Liên hệ</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted" style="padding: 40px 10px;">
                            <i class="fa fa-users" style="font-size: 40px; color: #ddd; margin-bottom: 10px; display: block;"></i>
                            Chưa có danh bạ nhân sự liên hệ nào được lưu cho đối tác này.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- TAB 3: Branches -->
                <div class="tab-pane" id="tab_branches">
                    <?php if (!empty($branches)): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead style="background: #f4f6f9;">
                                    <tr>
                                        <th>Tên Chi Nhánh / Văn Phòng</th>
                                        <th>Phân Loại</th>
                                        <th>Quốc Gia & Thành Phố</th>
                                        <th>Địa Chỉ</th>
                                        <th>Điện Thoại & Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($branches as $br): ?>
                                        <tr>
                                            <td><strong><?= esc($br->branch_name); ?></strong></td>
                                            <td>
                                                <?php if (!empty($br->is_headquarters)): ?>
                                                    <span class="label label-success"><i class="fa fa-building"></i> Trụ sở chính</span>
                                                <?php else: ?>
                                                    <span class="label label-primary"><i class="fa fa-map-pin"></i> Chi nhánh</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= esc($br->city ?? ''); ?>, <?= esc($br->country ?? 'Việt Nam'); ?></td>
                                            <td><?= esc($br->address ?? '<Chưa có>'); ?></td>
                                            <td>
                                                <?php if (!empty($br->phone)): ?><div><i class="fa fa-phone text-muted"></i> <?= esc($br->phone); ?></div><?php endif; ?>
                                                <?php if (!empty($br->email)): ?><div><i class="fa fa-envelope-o text-muted"></i> <?= esc($br->email); ?></div><?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted" style="padding: 40px 10px;">
                            <i class="fa fa-map-marker" style="font-size: 40px; color: #ddd; margin-bottom: 10px; display: block;"></i>
                            Chưa có thông tin chi nhánh / văn phòng đại diện nào.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- TAB 4: Visit Cards -->
                <div class="tab-pane" id="tab_cards">
                    <?php if (!empty($cards)): ?>
                        <div class="row">
                            <?php foreach ($cards as $card): ?>
                                <div class="col-md-4 col-sm-6">
                                    <div class="card-thumb-item" onclick="showZoomModal('<?= base_url($card->file_path); ?>');">
                                        <img src="<?= base_url($card->file_path); ?>" alt="Visit Card">
                                        <span class="label label-primary card-thumb-badge">
                                            <?= ($card->side === 'front') ? 'Mặt Trước' : (($card->side === 'back') ? 'Mặt Sau' : 'Danh Thiếp'); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted" style="padding: 40px 10px;">
                            <i class="fa fa-id-card-o" style="font-size: 40px; color: #ddd; margin-bottom: 10px; display: block;"></i>
                            Chưa có ảnh danh thiếp scan nào được đính kèm.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- TAB 5: Verification Logs -->
                <div class="tab-pane" id="tab_logs">
                    <?php if (!empty($verifyLogs)): ?>
                        <ul class="timeline timeline-inverse">
                            <?php foreach ($verifyLogs as $log): ?>
                                <?php
                                    $bgClass = ($log->result === 'closed' || $log->result === 'failed') ? 'bg-red' : (($log->result === 'active' || $log->result === 'verified') ? 'bg-green' : 'bg-gray');
                                    $iconClass = ($log->result === 'closed' || $log->result === 'failed') ? 'fa-times' : (($log->result === 'active' || $log->result === 'verified') ? 'fa-check' : 'fa-question');
                                ?>
                                <li>
                                    <i class="fa <?= $iconClass; ?> <?= $bgClass; ?>"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="fa fa-clock-o"></i> <?= date('d/m/Y H:i', strtotime($log->checked_at)); ?></span>
                                        <h3 class="timeline-header" style="font-size: 13px; font-weight: 600;">
                                            <span class="label label-info"><?= esc($log->check_type); ?></span> Kết quả: <strong><?= strtoupper(esc($log->result)); ?></strong>
                                        </h3>
                                        <?php if (!empty($log->detail)): ?>
                                            <div class="timeline-body" style="font-size: 12px; color: #555;">
                                                <?= esc($log->detail); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                            <li><i class="fa fa-clock-o bg-gray"></i></li>
                        </ul>
                    <?php else: ?>
                        <div class="text-center text-muted" style="padding: 40px 10px;">
                            <i class="fa fa-history" style="font-size: 40px; color: #ddd; margin-bottom: 10px; display: block;"></i>
                            Chưa có nhật ký xác minh nào. Nhấn <strong>"Xác Minh Ngay"</strong> để kiểm tra doanh nghiệp trên Google Maps và Facebook.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Zoom Card -->
<div class="modal fade" id="modalZoomCard" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 85%; max-width: 1000px;">
        <div class="modal-content" style="background: #111; color: #fff; border-radius: 8px;">
            <div class="modal-header" style="border-bottom: 1px solid #333; padding: 10px 15px;">
                <button type="button" class="close" data-dismiss="modal" style="color: #fff; font-size: 24px;">&times;</button>
                <h4 class="modal-title"><i class="fa fa-search-plus"></i> Xem Danh Thiếp Phóng To</h4>
            </div>
            <div class="modal-body text-center" style="padding: 20px;">
                <img id="zoomCardImg" src="" style="max-width: 100%; border-radius: 4px; box-shadow: 0 0 20px rgba(255,255,255,0.2);">
            </div>
        </div>
    </div>
</div>

<script>
function showZoomModal(src) {
    $('#zoomCardImg').attr('src', src);
    $('#modalZoomCard').modal('show');
}

$(document).ready(function () {
    $('#btnVerifyNow').on('click', function () {
        var memberId = $(this).data('id');
        var $btn = $(this);
        $btn.prop('disabled', true);
        $('#btnVerifyText').text('Đang xác minh...');
        $btn.find('i').addClass('fa-spin');

        $.ajax({
            url: '<?= adminUrl("members/verify-ajax"); ?>/' + memberId,
            type: 'POST',
            data: setAjaxData({ 'id': memberId }),
            dataType: 'json',
            success: function (res) {
                if (res && res.status === 'success') {
                    Swal.fire({
                        title: 'Xác Minh Hoàn Tất!',
                        text: 'Kết quả: ' + (res.result.status === 'verified' ? 'Hợp lệ' : (res.result.status === 'failed' ? 'Đã đóng cửa' : 'Chưa xác định')),
                        icon: res.result.status === 'verified' ? 'success' : (res.result.status === 'failed' ? 'error' : 'warning'),
                    }).then(function () { location.reload(); });
                }
            },
            error: function () {
                window.location.href = '<?= adminUrl("members/verify/"); ?>' + memberId;
            },
            complete: function () {
                $btn.prop('disabled', false);
                $('#btnVerifyText').text('Xác Minh Ngay');
                $btn.find('i').removeClass('fa-spin');
            }
        });
    });
});
</script>
