<div class="row">
    <div class="col-md-12">
        <div class="box-header with-border" style="margin-bottom: 15px; padding-left: 0;">
            <div class="left">
                <h3 class="box-title" style="font-size: 20px; font-weight: 700;">
                    <i class="fa fa-history text-primary"></i> <?= esc($title); ?>
                </h3>
            </div>
            <div class="right">
                <button type="button" class="btn btn-success btn-sm" id="btnStartBatchVerify" style="font-weight: 600;">
                    <i class="fa fa-play-circle"></i> Chạy Tiến Trình Xác Minh (Live Monitor)
                </button>
                <a href="<?= adminUrl('members'); ?>" class="btn btn-default btn-sm">
                    <i class="fa fa-handshake-o"></i> Danh Sách Đối Tác
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Stats Counter Row -->
<div class="row" style="margin-bottom: 15px;">
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-aqua" style="border-radius: 8px;">
            <div class="inner">
                <h3><?= number_format($totalLogs ?? 0); ?></h3>
                <p>Tổng Số Lượt Kiểm Tra</p>
            </div>
            <div class="icon"><i class="fa fa-list-alt"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green" style="border-radius: 8px;">
            <div class="inner">
                <h3><?= number_format($stats->verified ?? 0); ?></h3>
                <p>Doanh Nghiệp Đã Xác Minh</p>
            </div>
            <div class="icon"><i class="fa fa-check-circle"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow" style="border-radius: 8px;">
            <div class="inner">
                <h3><?= number_format($stats->pending ?? 0); ?></h3>
                <p>Doanh Nghiệp Chờ Xác Minh</p>
            </div>
            <div class="icon"><i class="fa fa-clock-o"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red" style="border-radius: 8px;">
            <div class="inner">
                <h3><?= number_format($stats->failed ?? 0); ?></h3>
                <p>Phát Hiện Đóng Cửa / Cảnh Báo</p>
            </div>
            <div class="icon"><i class="fa fa-exclamation-triangle"></i></div>
        </div>
    </div>
</div>

<!-- Filter Bar & Table Box -->
<div class="box box-default">
    <div class="box-header with-border">
        <form action="<?= adminUrl('members/verify-logs'); ?>" method="get">
            <div class="row">
                <div class="col-md-3 col-sm-6 m-b-5">
                    <input type="text" name="q" class="form-control input-sm" placeholder="Tìm theo tên công ty, MST, kết quả..." value="<?= esc($filters['q'] ?? ''); ?>">
                </div>
                <div class="col-md-3 col-sm-6 m-b-5">
                    <select name="check_type" class="form-control input-sm">
                        <option value="">-- Tất cả loại kiểm tra --</option>
                        <option value="google_maps" <?= ($filters['check_type'] ?? '') === 'google_maps' ? 'selected' : ''; ?>>Google Maps Signals</option>
                        <option value="website" <?= ($filters['check_type'] ?? '') === 'website' ? 'selected' : ''; ?>>Bóc Tách Website Doanh Nghiệp</option>
                        <option value="fanpage" <?= ($filters['check_type'] ?? '') === 'fanpage' ? 'selected' : ''; ?>>Facebook Fanpage</option>
                        <option value="manual" <?= ($filters['check_type'] ?? '') === 'manual' ? 'selected' : ''; ?>>Thủ công (Manual)</option>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6 m-b-5">
                    <select name="result" class="form-control input-sm">
                        <option value="">-- Tất cả kết quả --</option>
                        <option value="active" <?= ($filters['result'] ?? '') === 'active' ? 'selected' : ''; ?>>Hoạt động (Active / Verified)</option>
                        <option value="closed" <?= ($filters['result'] ?? '') === 'closed' ? 'selected' : ''; ?>>Đã đóng cửa (Closed)</option>
                        <option value="not_found" <?= ($filters['result'] ?? '') === 'not_found' ? 'selected' : ''; ?>>Không tìm thấy (Not Found)</option>
                        <option value="unknown" <?= ($filters['result'] ?? '') === 'unknown' ? 'selected' : ''; ?>>Chưa rõ (Unknown)</option>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6 m-b-5 text-right">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Lọc Nhật Ký</button>
                    <a href="<?= adminUrl('members/verify-logs'); ?>" class="btn btn-default btn-sm">Đặt lại</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="box-body table-responsive no-padding">
        <table class="table table-bordered table-striped" style="font-size: 13px;">
            <thead>
                <tr style="background: #f4f6f9;">
                    <th style="width: 50px;" class="text-center">#</th>
                    <th style="width: 150px;">Thời Gian</th>
                    <th>Doanh Nghiệp</th>
                    <th style="width: 160px;" class="text-center">Loại Kiểm Tra</th>
                    <th style="width: 130px;" class="text-center">Kết Quả</th>
                    <th>Chi Tiết Tín Hiệu & Dữ Liệu Bóc Tách</th>
                    <th style="width: 110px;" class="text-center">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($logs)): ?>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td class="text-center" style="vertical-align: middle; color: #888;"><?= $log->id; ?></td>
                            <td style="vertical-align: middle; font-size: 12px; color: #555;">
                                <i class="fa fa-clock-o text-muted"></i> <?= date('d/m/Y H:i:s', strtotime($log->checked_at)); ?>
                            </td>
                            <td style="vertical-align: middle;">
                                <strong><a href="<?= adminUrl('members/detail/' . $log->member_id); ?>" target="_blank"><?= esc($log->company_name ?: 'Đối tác #' . $log->member_id); ?></a></strong>
                                <?php if (!empty($log->tax_code)): ?>
                                    <div style="font-size: 11px; color: #666;">MST: <code><?= esc($log->tax_code); ?></code></div>
                                <?php endif; ?>
                            </td>
                            <td class="text-center" style="vertical-align: middle;">
                                <?php if ($log->check_type === 'google_maps'): ?>
                                    <span class="label bg-blue"><i class="fa fa-map-marker"></i> Google Maps</span>
                                <?php elseif ($log->check_type === 'website'): ?>
                                    <span class="label bg-purple"><i class="fa fa-globe"></i> Website Crawl</span>
                                <?php elseif ($log->check_type === 'fanpage'): ?>
                                    <span class="label bg-navy"><i class="fa fa-facebook"></i> Fanpage</span>
                                <?php else: ?>
                                    <span class="label label-default"><i class="fa fa-user"></i> Thủ công</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center" style="vertical-align: middle;">
                                <?php if ($log->result === 'active' || $log->result === 'verified'): ?>
                                    <span class="label label-success" style="font-size: 11px;"><i class="fa fa-check-circle"></i> Hoạt động</span>
                                <?php elseif ($log->result === 'closed' || $log->result === 'failed'): ?>
                                    <span class="label label-danger" style="font-size: 11px;"><i class="fa fa-times-circle"></i> Đã đóng cửa</span>
                                <?php elseif ($log->result === 'not_found'): ?>
                                    <span class="label label-warning" style="font-size: 11px;"><i class="fa fa-exclamation-circle"></i> Không tìm thấy</span>
                                <?php else: ?>
                                    <span class="label label-default" style="font-size: 11px;"><i class="fa fa-question-circle"></i> Chưa rõ</span>
                                <?php endif; ?>
                            </td>
                            <td style="vertical-align: middle; font-size: 12px; max-width: 380px;">
                                <?php 
                                    $detailObj = @json_decode($log->detail, true);
                                    if (is_array($detailObj)):
                                ?>
                                    <?php if (!empty($detailObj['page_title'])): ?>
                                        <div><i class="fa fa-tag text-muted"></i> <strong>Tiêu đề:</strong> <?= esc($detailObj['page_title']); ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($detailObj['signal_detected']) || !empty($detailObj['signal'])): ?>
                                        <div><i class="fa fa-bolt text-warning"></i> <strong>Tín hiệu:</strong> <code><?= esc($detailObj['signal_detected'] ?? $detailObj['signal']); ?></code></div>
                                    <?php endif; ?>
                                    <?php if (!empty($detailObj['extracted_emails'])): ?>
                                        <div><i class="fa fa-envelope text-info"></i> Email: <?= esc(implode(', ', (array)$detailObj['extracted_emails'])); ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($detailObj['extracted_phones'])): ?>
                                        <div><i class="fa fa-phone text-success"></i> SĐT: <?= esc(implode(', ', (array)$detailObj['extracted_phones'])); ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($detailObj['message'])): ?>
                                        <div class="text-muted"><?= esc($detailObj['message']); ?></div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted"><?= esc($log->detail ?: 'Không có ghi chú'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center" style="vertical-align: middle;">
                                <button type="button" class="btn btn-xs btn-default btn-view-log" data-id="<?= $log->id; ?>" title="Xem JSON chi tiết">
                                    <i class="fa fa-code text-primary"></i> Chi tiết
                                </button>
                                <button type="button" class="btn btn-xs btn-default btn-reverify-one" data-id="<?= $log->member_id; ?>" title="Xác minh lại ngay">
                                    <i class="fa fa-refresh text-success"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted" style="padding: 40px;">
                            <i class="fa fa-history" style="font-size: 32px; color: #ccc; margin-bottom: 10px; display: block;"></i>
                            Chưa có dữ liệu nhật ký xác minh nào.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="box-footer clearfix">
        <div class="col-sm-12 text-right">
            <?= $pager->links; ?>
        </div>
    </div>
</div>

<!-- MODAL 1: Live Verification Progress Monitor Modal -->
<div class="modal fade" id="modalBatchVerify" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 10px;">
            <div class="modal-header bg-navy" style="border-radius: 9px 9px 0 0;">
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title font-600"><i class="fa fa-bolt text-yellow"></i> Tiến Trình Xác Minh Doanh Nghiệp (Live Monitor)</h4>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <!-- Progress bar -->
                <div style="margin-bottom: 18px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-weight: 600;">
                        <span id="verifyStatusText">Đang chuẩn bị hàng đợi xác minh...</span>
                        <span id="verifyPercentText">0%</span>
                    </div>
                    <div class="progress progress-sm active" style="height: 18px; border-radius: 9px;">
                        <div id="verifyProgressBar" class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" style="width: 0%; font-size: 11px; line-height: 18px;">0%</div>
                    </div>
                </div>

                <!-- Counters Badge -->
                <div class="row text-center" style="margin-bottom: 20px;">
                    <div class="col-xs-3">
                        <div style="background: #f4f6f9; padding: 10px; border-radius: 8px;">
                            <div style="font-size: 18px; font-weight: 700; color: #0073b7;" id="statTotal">0</div>
                            <div style="font-size: 11px; color: #666;">Tổng hàng đợi</div>
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div style="background: #f4f6f9; padding: 10px; border-radius: 8px;">
                            <div style="font-size: 18px; font-weight: 700; color: #00a65a;" id="statVerified">0</div>
                            <div style="font-size: 11px; color: #666;">Đã xác minh</div>
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div style="background: #f4f6f9; padding: 10px; border-radius: 8px;">
                            <div style="font-size: 18px; font-weight: 700; color: #dd4b39;" id="statClosed">0</div>
                            <div style="font-size: 11px; color: #666;">Đã đóng cửa</div>
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div style="background: #f4f6f9; padding: 10px; border-radius: 8px;">
                            <div style="font-size: 18px; font-weight: 700; color: #f39c12;" id="statRemaining">0</div>
                            <div style="font-size: 11px; color: #666;">Còn lại</div>
                        </div>
                    </div>
                </div>

                <!-- Live Terminal Log Box -->
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <label style="font-size: 12px; font-weight: 600; color: #444; margin: 0;"><i class="fa fa-terminal"></i> Nhật Ký Sự Kiện Real-time (Đa Kênh):</label>
                        <div>
                            <button type="button" class="btn btn-xs btn-default" id="btnClearTerminal" title="Xóa màn hình log"><i class="fa fa-trash"></i> Xóa log</button>
                            <button type="button" class="btn btn-xs btn-default" id="btnCopyTerminal" title="Sao chép toàn bộ log"><i class="fa fa-copy"></i> Sao chép</button>
                        </div>
                    </div>
                    <div id="liveTerminalBox" style="background: #0f172a; color: #38bdf8; font-family: 'Consolas', 'Monaco', monospace; font-size: 11.5px; padding: 14px; border-radius: 8px; height: 260px; overflow-y: auto; line-height: 1.7; border: 1px solid #334155; box-shadow: inset 0 2px 4px rgba(0,0,0,0.4);">
                        <div style="color: #94a3b8;">[Hệ thống] Nhấn "Bắt đầu" để khởi chạy tiến trình quét ngầm đa kênh...</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="btnRunQueueNow"><i class="fa fa-play"></i> Bắt Đầu Xác Minh</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL 2: Log Raw Detail JSON Modal -->
<div class="modal fade" id="modalLogDetail" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 10px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title font-600"><i class="fa fa-info-circle text-primary"></i> Chi Tiết Nhật Ký Xác Minh</h4>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <div id="logDetailContent">
                    <pre id="logDetailPre" style="background: #f8fafc; font-size: 12px; max-height: 350px; overflow: auto; border: 1px solid #e2e8f0; padding: 12px; border-radius: 6px;"></pre>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var verifyQueue = [];
    var totalQueue = 0;
    var countVerified = 0;
    var countClosed = 0;
    var countProcessed = 0;
    var isRunning = false;

    function addLogToTerminal(msg, type) {
        var color = '#38bdf8';
        if (type === 'success') color = '#4ade80';
        if (type === 'danger') color = '#f87171';
        if (type === 'warning') color = '#fbbf24';
        if (type === 'muted') color = '#94a3b8';
        if (type === 'purple') color = '#c084fc';
        var time = new Date().toLocaleTimeString();
        var line = '<div style="color: ' + color + '; margin-bottom: 2px;">[' + time + '] ' + msg + '</div>';
        $('#liveTerminalBox').append(line);
        $('#liveTerminalBox').scrollTop($('#liveTerminalBox')[0].scrollHeight);
    }

    $('#btnClearTerminal').on('click', function() {
        $('#liveTerminalBox').html('<div style="color: #94a3b8;">[Hệ thống] Đã dọn dẹp màn hình terminal.</div>');
    });

    $('#btnCopyTerminal').on('click', function() {
        var text = $('#liveTerminalBox').text();
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Đã sao chép toàn bộ nhật ký vào Clipboard!');
            });
        } else {
            alert('Trình duyệt không hỗ trợ sao chép tự động.');
        }
    });

    $('#btnStartBatchVerify').on('click', function() {
        $('#modalBatchVerify').modal('show');
        $.getJSON('<?= adminUrl("members/verify-queue-ajax"); ?>', function(res) {
            if (res && res.queue) {
                verifyQueue = res.queue;
                totalQueue = verifyQueue.length;
                countVerified = 0;
                countClosed = 0;
                countProcessed = 0;
                $('#statTotal').text(totalQueue);
                $('#statRemaining').text(totalQueue);
                $('#statVerified').text(0);
                $('#statClosed').text(0);
                $('#verifyPercentText').text('0%');
                $('#verifyProgressBar').css('width', '0%').text('0%');
                $('#verifyStatusText').text('Sẵn sàng xác minh ' + totalQueue + ' doanh nghiệp đang chờ.');
                addLogToTerminal('Đã nạp ' + totalQueue + ' doanh nghiệp đang ở trạng thái "Chờ xác minh".', 'warning');
            }
        });
    });

    $('#btnRunQueueNow').on('click', function() {
        if (isRunning || verifyQueue.length === 0) return;
        isRunning = true;
        $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Đang Chạy...');
        processNextInQueue();
    });

    function processNextInQueue() {
        if (verifyQueue.length === 0) {
            isRunning = false;
            $('#verifyStatusText').text('Đã hoàn tất toàn bộ tiến trình xác minh tuần tự!');
            $('#btnRunQueueNow').prop('disabled', false).html('<i class="fa fa-check"></i> Hoàn Tất');
            addLogToTerminal('══════════════════════════════════════════════════════════════', 'muted');
            addLogToTerminal('🎉 HOÀN TẤT TOÀN BỘ TIẾN TRÌNH XÁC MINH TUẦN TỰ!', 'success');
            addLogToTerminal('📊 Thống kê: ' + countVerified + ' hợp lệ, ' + countClosed + ' đóng cửa/cảnh báo, tổng ' + countProcessed + ' doanh nghiệp.', 'purple');
            return;
        }

        var item = verifyQueue.shift();
        countProcessed++;
        var percent = Math.round((countProcessed / totalQueue) * 100);
        $('#verifyPercentText').text(percent + '%');
        $('#verifyProgressBar').css('width', percent + '%').text(percent + '%');
        $('#statRemaining').text(verifyQueue.length);
        $('#verifyStatusText').text('Đang xử lý tuần tự (' + countProcessed + '/' + totalQueue + '): ' + item.company_name);

        addLogToTerminal('──────────────────────────────────────────────────────────────', 'muted');
        addLogToTerminal('🚀 [MỞ PHIÊN NGẦM #' + item.id + '] Doanh nghiệp: ' + item.company_name + ' | MST: ' + (item.tax_code || 'Chưa có'), 'info');
        if (item.website) addLogToTerminal('   ├─ Website đối chiếu: ' + item.website, 'muted');
        if (item.address) addLogToTerminal('   ├─ Địa chỉ: ' + item.address, 'muted');

        var targetUrl = '<?= adminUrl("members/verify-ajax"); ?>/' + item.id;

        $.ajax({
            type: 'GET',
            url: targetUrl,
            dataType: 'json',
            timeout: 30000,
            success: function(res) {
                if (res && res.csrf_token) {
                    $('meta[name="X-CSRF-TOKEN"]').attr('content', res.csrf_token);
                }
                if (res && res.status === 'success' && res.result) {
                    var r = res.result;
                    var st = r.status;
                    
                    // Chi tiết từng kênh
                    if (r.channels) {
                        if (r.channels.google_maps) {
                            var mapSt = r.channels.google_maps.status;
                            var mapIcon = (mapSt === 'active') ? '✔ Maps: Hoạt động' : ((mapSt === 'closed') ? '✖ Maps: Đã đóng cửa' : '○ Maps: ' + mapSt);
                            addLogToTerminal('   ├─ 📍 ' + mapIcon + (r.channels.google_maps.detail && r.channels.google_maps.detail.signal ? ' (' + r.channels.google_maps.detail.signal + ')' : ''), (mapSt === 'active' ? 'success' : (mapSt === 'closed' ? 'danger' : 'warning')));
                        }
                        if (r.channels.website) {
                            var webSt = r.channels.website.status;
                            var webCode = r.channels.website.http_code ? ' (HTTP ' + r.channels.website.http_code + ')' : '';
                            addLogToTerminal('   ├─ 🌐 Website: ' + (webSt === 'active' ? 'Khả dụng' + webCode : 'Không phản hồi' + webCode), (webSt === 'active' ? 'success' : 'warning'));
                        }
                        if (r.channels.fanpage) {
                            var fbSt = r.channels.fanpage.status;
                            addLogToTerminal('   ├─ 📱 Fanpage: ' + (fbSt === 'active' ? 'Hoạt động' : fbSt), (fbSt === 'active' ? 'success' : 'muted'));
                        }
                    }

                    // Quyết định tổng hợp
                    if (st === 'verified') {
                        countVerified++;
                        $('#statVerified').text(countVerified);
                        addLogToTerminal('   └─ ✔ [KẾT QUẢ: HỢP LỆ] ' + (r.reason || 'Doanh nghiệp có tín hiệu hoạt động tốt'), 'success');
                    } else if (st === 'failed') {
                        countClosed++;
                        $('#statClosed').text(countClosed);
                        addLogToTerminal('   └─ ✖ [KẾT QUẢ: ĐÃ ĐÓNG CỬA] ' + (r.reason || 'Phát hiện tín hiệu ngừng hoạt động'), 'danger');
                    } else {
                        addLogToTerminal('   └─ ❓ [KẾT QUẢ: CHƯA RÕ TÍN HIỆU] Cần đối chiếu thủ công', 'warning');
                    }
                } else if (res && res.status === 'error') {
                    addLogToTerminal('   └─ ⚠ [LỖI NGHIỆP VỤ #' + item.id + '] ' + (res.message || 'Lỗi không xác định'), 'danger');
                } else {
                    addLogToTerminal('   └─ ⚠ Phản hồi không đúng định dạng JSON từ máy chủ', 'danger');
                }
            },
            error: function(xhr, status, error) {
                var errDetail = '';
                try {
                    var parsed = JSON.parse(xhr.responseText);
                    if (parsed && parsed.message) errDetail = ' | ' + parsed.message;
                } catch(e) {
                    if (xhr.statusText) errDetail = ' (' + xhr.statusText + ')';
                }
                addLogToTerminal('   └─ 🔴 [LỖI KẾT NỐI HTTP ' + xhr.status + errDetail + ']', 'danger');
                addLogToTerminal('      • Endpoint: ' + targetUrl, 'danger');
                if (xhr.status === 404) {
                    addLogToTerminal('      • Nguyên nhân: Đường dẫn route chưa khớp hoặc ID doanh nghiệp không tồn tại.', 'warning');
                } else if (xhr.status === 403) {
                    addLogToTerminal('      • Nguyên nhân: Phiên đăng nhập hết hạn hoặc tài khoản không có quyền xác minh.', 'warning');
                } else if (xhr.status === 500) {
                    addLogToTerminal('      • Nguyên nhân: Lỗi máy chủ nội bộ. Vui lòng kiểm tra log lỗi PHP.', 'warning');
                }
            },
            complete: function() {
                addLogToTerminal('🔒 [ĐÓNG PHIÊN NGẦM #' + item.id + '] Đã giải phóng RAM & dọn dẹp kết nối.', 'muted');
                setTimeout(processNextInQueue, 800);
            }
        });
    }

    // View log detail modal
    $('.btn-view-log').on('click', function() {
        var id = $(this).data('id');
        $.getJSON('<?= adminUrl("members/verify-log-detail-ajax"); ?>/' + id, function(res) {
            if (res && res.log) {
                var jsonStr = JSON.stringify(JSON.parse(res.log.detail || '{}'), null, 4);
                $('#logDetailPre').text(jsonStr);
                $('#modalLogDetail').modal('show');
            }
        });
    });

    // Reverify single member button
    $('.btn-reverify-one').on('click', function() {
        var btn = $(this);
        var memberId = btn.data('id');
        btn.html('<i class="fa fa-spinner fa-spin"></i>');
        var postData = {};
        if (typeof setAjaxData === 'function') postData = setAjaxData(postData);
        $.post('<?= adminUrl("members/verify-ajax"); ?>/' + memberId, postData, function(res) {
            location.reload();
        });
    });
});
</script>
