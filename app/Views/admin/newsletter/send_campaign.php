<div class="row">
    <div class="col-sm-12 title-section" style="margin-bottom: 20px;">
        <h3 style="margin: 0; font-size: 22px; font-weight: 800; color: #0f172a;">
            <i class="fa fa-paper-plane text-primary"></i> Gửi Chiến Dịch: <?= esc($campaign->title); ?>
        </h3>
        <p style="color: #64748b; margin: 4px 0 0; font-size: 13px;">Thuật toán gửi email marketing chống spam: Giãn cách ngẫu nhiên (Jitter Delay 1.5s - 2.5s) kèm giải nhiệt kết nối sau mỗi 10 email.</p>
    </div>
</div>

<div class="row">
    <!-- Campaign Stats Card -->
    <div class="col-md-4 col-sm-12">
        <div class="box box-primary" style="border-radius: 10px; box-shadow: 0 4px 16px rgba(0,0,0,0.04);">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-info-circle text-primary"></i> Thông Tin Chiến Dịch</h3>
            </div>
            <div class="box-body" style="font-size: 13px;">
                <p><strong>Tiêu đề gửi:</strong> <?= esc($campaign->subject); ?></p>
                <p><strong>Ngôn ngữ:</strong> <span class="label label-primary"><?= ($campaign->lang_id == 2 ? 'English' : 'Tiếng Việt'); ?></span></p>
                <p><strong>Bài viết đã chọn:</strong> <span class="label label-info"><?= count($posts); ?> tin tức</span></p>
                <p><strong>Tổng người nhận:</strong> <strong id="totalRecipientsDisplay" style="color: #2563eb; font-size: 16px;"><?= count($logs); ?></strong></p>
                <p><strong>Đã gửi thành công:</strong> <span id="sentCountDisplay" style="color: #16a34a; font-weight: 800; font-size: 16px;"><?= $campaign->sent_count; ?></span></p>

                <div style="margin: 15px 0;">
                    <div style="display:flex; justify-content:space-between; font-size:12px; font-weight:700; margin-bottom:4px;">
                        <span>Tiến độ gửi:</span>
                        <span id="progressPercent">0%</span>
                    </div>
                    <div class="progress progress-sm active" style="border-radius: 6px;">
                        <div id="progressBar" class="progress-bar progress-bar-success progress-bar-striped" style="width: 0%"></div>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 20px;">
                    <button type="button" id="btnPreviewModal" class="btn btn-info btn-block" style="font-weight: 700;">
                        <i class="fa fa-eye"></i> Xem Trước Email Cá Nhân Hóa
                    </button>

                    <button type="button" id="btnStartSending" class="btn btn-primary btn-block btn-lg" style="font-weight: 800; padding: 12px; box-shadow: 0 4px 14px rgba(37,99,235,0.3);">
                        <i class="fa fa-send"></i> Bắt Đầu Gửi Email (Chống Spam)
                    </button>

                    <button type="button" id="btnPauseSending" class="btn btn-warning btn-block" style="font-weight: 700; display: none;">
                        <i class="fa fa-pause"></i> Tạm Dừng Gửi
                    </button>
                    
                    <a href="<?= adminUrl('newsletter'); ?>" class="btn btn-default btn-block">
                        <i class="fa fa-arrow-left"></i> Quay lại danh sách chiến dịch
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recipients & Live Sending Log -->
    <div class="col-md-8 col-sm-12">
        <div class="box box-success" style="border-radius: 10px; box-shadow: 0 4px 16px rgba(0,0,0,0.04);">
            <div class="box-header with-border" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 class="box-title"><i class="fa fa-users text-green"></i> Danh Sách Người Nhận</h3>
                <button type="button" class="btn btn-success btn-sm" id="btnOpenAddRecipientModal" style="font-weight: 700;">
                    <i class="fa fa-plus"></i> + Thêm Người Nhận Mới
                </button>
            </div>
            <div class="box-body table-responsive" style="max-height: 480px; overflow-y: auto;">
                <table class="table table-bordered table-striped" id="tableRecipients" style="font-size: 13px;">
                    <thead>
                        <tr style="background: #f8fafc;">
                            <th width="30">#</th>
                            <th>Email</th>
                            <th>Tên Đại Diện</th>
                            <th>Doanh Nghiệp / Thương Hiệu</th>
                            <th width="100" class="text-center">Trạng Thái</th>
                            <th width="90" class="text-center">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $idx = 1;
                        foreach ($logs as $log): 
                        ?>
                            <tr id="row_log_<?= $log->id; ?>" data-log-id="<?= $log->id; ?>" data-sent="<?= $log->is_sent; ?>" data-email="<?= esc($log->recipient_email); ?>" data-name="<?= esc($log->recipient_name); ?>" data-company="<?= esc($log->company_name); ?>">
                                <td><?= $idx++; ?></td>
                                <td class="col-email"><strong><?= esc($log->recipient_email); ?></strong></td>
                                <td class="col-name"><?= esc($log->recipient_name); ?></td>
                                <td class="col-company"><?= esc($log->company_name); ?></td>
                                <td class="text-center status-col">
                                    <?php if ($log->is_sent): ?>
                                        <span class="label label-success"><i class="fa fa-check"></i> Đã gửi</span>
                                    <?php else: ?>
                                        <span class="label label-default">Chờ gửi</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-xs btn-primary btn-edit-recipient" data-id="<?= $log->id; ?>" title="Sửa"><i class="fa fa-pencil"></i></button>
                                    <button type="button" class="btn btn-xs btn-danger btn-delete-recipient" data-id="<?= $log->id; ?>" title="Xóa"><i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add Recipient -->
<div class="modal fade" id="modalAddRecipient" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 8px;">
            <div class="modal-header" style="background: #16a34a; color: #fff;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;">&times;</button>
                <h4 class="modal-title"><i class="fa fa-user-plus"></i> Thêm Người Nhận Vào Chiến Dịch</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Địa Chỉ Email: <span style="color:#ef4444;">*</span></label>
                    <input type="email" id="addEmailInput" class="form-control" placeholder="partner@example.com" required>
                </div>
                <div class="form-group">
                    <label>Tên Người Đại Diện:</label>
                    <input type="text" id="addNameInput" class="form-control" placeholder="VD: Nguyễn Văn A">
                </div>
                <div class="form-group">
                    <label>Tên Doanh Nghiệp / Thương Hiệu:</label>
                    <input type="text" id="addCompanyInput" class="form-control" placeholder="VD: Công ty TNHH Logistics ABC">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-success" id="btnSaveAddRecipient">Lưu Người Nhận</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Recipient -->
<div class="modal fade" id="modalEditRecipient" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 8px;">
            <div class="modal-header" style="background: #2563eb; color: #fff;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;">&times;</button>
                <h4 class="modal-title"><i class="fa fa-pencil"></i> Chỉnh Sửa Thông Tin Người Nhận</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editLogId">
                <div class="form-group">
                    <label>Địa Chỉ Email: <span style="color:#ef4444;">*</span></label>
                    <input type="email" id="editEmailInput" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Tên Người Đại Diện:</label>
                    <input type="text" id="editNameInput" class="form-control">
                </div>
                <div class="form-group">
                    <label>Tên Doanh Nghiệp / Thương Hiệu:</label>
                    <input type="text" id="editCompanyInput" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="btnSaveEditRecipient">Cập Nhật</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Preview -->
<div class="modal fade" id="modalCampaignPreview" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 10px; overflow: hidden;">
            <div class="modal-header" style="background: #0f172a; color: #fff;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;">&times;</button>
                <h4 class="modal-title"><i class="fa fa-eye text-primary"></i> Xem Trước Email Thực Tế Gửi Cho Khách Hàng</h4>
            </div>
            <div class="modal-body" style="background: #f1f5f9; padding: 20px;">
                <div style="background: #ffffff; padding: 12px 18px; border-radius: 8px; margin-bottom: 15px; border: 1px solid #cbd5e1; font-size: 13px;">
                    <div><strong>Tiêu đề gửi:</strong> <span style="color: #2563eb; font-weight: 700;"><?= esc($campaign->subject); ?></span></div>
                    <div><strong>Mẫu người nhận:</strong> <span class="label label-info">Nguyễn Văn A - Công ty CP Xuất Nhập Khẩu Toàn Cầu</span></div>
                </div>

                <div style="max-height: 500px; overflow-y: auto;">
                    <?php 
                    $sampleLog = (object)[
                        'tracking_token' => 'sample-token-12345',
                        'recipient_name' => ($campaign->lang_id == 2 ? 'David Johnson' : 'Nguyễn Văn A'),
                        'company_name' => ($campaign->lang_id == 2 ? 'Global Freight Express LLC' : 'Công ty CP Xuất Nhập Khẩu Toàn Cầu')
                    ];
                    $sampleHtml = (new \App\Models\EmailCampaignModel())->buildPersonalizedEmailHtml($campaign, $sampleLog, $posts);
                    echo $sampleHtml;
                    ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng Xem Trước</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var campaignId = <?= $campaign->id; ?>;
    var isSending = false;
    var isPaused = false;
    var sentBatchCounter = 0;

    $('#btnPreviewModal').on('click', function() { $('#modalCampaignPreview').modal('show'); });

    // Open Add Modal
    $('#btnOpenAddRecipientModal').on('click', function() {
        $('#addEmailInput').val('');
        $('#addNameInput').val('');
        $('#addCompanyInput').val('');
        $('#modalAddRecipient').modal('show');
    });

    // Save Add Recipient
    $('#btnSaveAddRecipient').on('click', function() {
        var email = $('#addEmailInput').val().trim();
        var name = $('#addNameInput').val().trim();
        var company = $('#addCompanyInput').val().trim();
        if (!email) { alert('Vui lòng nhập địa chỉ email.'); return; }

        $.ajax({
            type: "POST",
            url: VrConfig.baseURL + '/admin/newsletter-add-recipient-ajax',
            data: setAjaxData({ campaign_id: campaignId, email: email, name: name, company: company }),
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    $('#modalAddRecipient').modal('hide');
                    window.location.reload();
                } else {
                    alert(res.message || 'Lỗi thêm người nhận.');
                }
            }
        });
    });

    // Open Edit Modal
    $(document).on('click', '.btn-edit-recipient', function() {
        var $row = $(this).closest('tr');
        var id = $(this).attr('data-id');
        $('#editLogId').val(id);
        $('#editEmailInput').val($row.attr('data-email'));
        $('#editNameInput').val($row.attr('data-name'));
        $('#editCompanyInput').val($row.attr('data-company'));
        $('#modalEditRecipient').modal('show');
    });

    // Save Edit Recipient
    $('#btnSaveEditRecipient').on('click', function() {
        var logId = $('#editLogId').val();
        var email = $('#editEmailInput').val().trim();
        var name = $('#editNameInput').val().trim();
        var company = $('#editCompanyInput').val().trim();
        if (!email) { alert('Vui lòng nhập địa chỉ email.'); return; }

        $.ajax({
            type: "POST",
            url: VrConfig.baseURL + '/admin/newsletter-edit-recipient-ajax',
            data: setAjaxData({ log_id: logId, email: email, name: name, company: company }),
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    $('#modalEditRecipient').modal('hide');
                    var $row = $('#row_log_' + logId);
                    $row.attr('data-email', email).find('.col-email').html('<strong>' + email + '</strong>');
                    $row.attr('data-name', name).find('.col-name').text(name);
                    $row.attr('data-company', company).find('.col-company').text(company);
                } else {
                    alert(res.message || 'Lỗi cập nhật người nhận.');
                }
            }
        });
    });

    // Delete Recipient
    $(document).on('click', '.btn-delete-recipient', function() {
        if (!confirm('Bạn có chắc chắn muốn xóa người nhận này khỏi chiến dịch?')) return;
        var logId = $(this).attr('data-id');
        var $row = $('#row_log_' + logId);

        $.ajax({
            type: "POST",
            url: VrConfig.baseURL + '/admin/newsletter-delete-recipient-ajax',
            data: setAjaxData({ log_id: logId }),
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    $row.fadeOut(200, function() { $(this).remove(); });
                }
            }
        });
    });

    // Build sending queue
    var logsToSend = [];
    $('#tableRecipients tbody tr').each(function() {
        if ($(this).attr('data-sent') == '0') {
            logsToSend.push($(this).attr('data-log-id'));
        }
    });

    var totalLogs = <?= count($logs); ?>;
    var currentSent = <?= (int)$campaign->sent_count; ?>;

    updateProgress();

    function updateProgress() {
        if (totalLogs > 0) {
            var percent = Math.min(100, Math.round((currentSent / totalLogs) * 100));
            $('#progressBar').css('width', percent + '%');
            $('#progressPercent').text(percent + '% (' + currentSent + '/' + totalLogs + ')');
            $('#sentCountDisplay').text(currentSent);
        }
    }

    $('#btnStartSending').on('click', function() {
        if (isSending) return;
        if (logsToSend.length === 0) {
            alert('Tất cả người nhận trong danh sách này đều đã được gửi.');
            return;
        }

        isSending = true;
        isPaused = false;
        $(this).hide();
        $('#btnPauseSending').show();
        sendNextLog();
    });

    $('#btnPauseSending').on('click', function() {
        isPaused = true;
        isSending = false;
        $(this).hide();
        $('#btnStartSending').show().html('<i class="fa fa-play"></i> Tiếp Tục Gửi Email');
    });

    function sendNextLog() {
        if (isPaused) return;
        if (logsToSend.length === 0) {
            $('#btnPauseSending').hide();
            $('#btnStartSending').show().prop('disabled', true).html('<i class="fa fa-check-circle"></i> Đã Gửi Hoàn Tất Toàn Bộ!').removeClass('btn-primary').addClass('btn-success');
            isSending = false;
            return;
        }

        var logId = logsToSend.shift();
        var $row = $('#row_log_' + logId);
        $row.find('.status-col').html('<span class="label label-warning"><i class="fa fa-spinner fa-spin"></i> Đang gửi...</span>');

        $.ajax({
            type: "POST",
            url: VrConfig.baseURL + '/admin/newsletter-send-single-log',
            data: setAjaxData({ 'log_id': logId }),
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    $row.find('.status-col').html('<span class="label label-success"><i class="fa fa-check"></i> Đã gửi</span>');
                    currentSent++;
                    sentBatchCounter++;
                    updateProgress();
                } else {
                    $row.find('.status-col').html('<span class="label label-danger"><i class="fa fa-times"></i> Lỗi</span>');
                }
            },
            error: function() {
                $row.find('.status-col').html('<span class="label label-danger"><i class="fa fa-times"></i> Lỗi</span>');
            },
            complete: function() {
                // Anti-spam algorithmic jitter: 1500ms - 2500ms random delay
                var delay = Math.floor(Math.random() * 1000) + 1500;
                
                // Adaptive batch cooling: after every 10 emails, pause 3.5 seconds
                if (sentBatchCounter % 10 === 0) {
                    delay += 2500;
                }
                
                if (!isPaused) {
                    setTimeout(sendNextLog, delay);
                }
            }
        });
    }
});
</script>
