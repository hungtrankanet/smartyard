<!-- FilePond Stylesheets CDN -->
<link rel="stylesheet" href="https://unpkg.com/filepond@^4/dist/filepond.css">
<link rel="stylesheet" href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css">
<style>
    .filepond--root { font-family: inherit; margin-bottom: 15px; }
    .filepond--panel-root { background-color: #f8fafc; border: 2px dashed #3c8dbc; border-radius: 8px; }
    .pair-container { display: grid; grid-template-columns: repeat(auto-fill, minmax(360px, 1fr)); gap: 15px; margin-top: 15px; }
    .pair-card { background: #fff; border: 1.5px solid #d2d6de; border-radius: 8px; padding: 12px; transition: all .2s; position: relative; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    .pair-card:hover { border-color: #3c8dbc; box-shadow: 0 4px 12px rgba(60,141,188,0.15); }
    .pair-card.processing { border-color: #f39c12; background: #fffdf9; }
    .pair-card.done { border-color: #00a65a; background: #f9fffb; }
    .pair-card.error { border-color: #dd4b39; background: #fffafa; }
    .pair-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding-bottom: 8px; border-bottom: 1px solid #f0f0f0; }
    .pair-title { font-weight: 700; font-size: 13px; color: #333; }
    .pair-slots { display: flex; gap: 8px; align-items: center; justify-content: space-between; }
    .card-slot { flex: 1; border: 1px solid #e1e8ed; border-radius: 6px; padding: 6px; text-align: center; background: #fafbfc; position: relative; min-height: 140px; display: flex; flex-direction: column; justify-content: space-between; }
    .card-slot.empty-slot { border: 2px dashed #ccd5dc; justify-content: center; align-items: center; cursor: pointer; color: #888; }
    .card-slot.empty-slot:hover { border-color: #3c8dbc; color: #3c8dbc; background: #f0f7fb; }
    .slot-thumb { width: 100%; height: 95px; object-fit: cover; border-radius: 4px; cursor: pointer; border: 1px solid #eee; }
    .slot-badge { font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 3px; display: inline-block; margin-top: 4px; }
    .slot-name { font-size: 11px; color: #666; margin-top: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .swap-btn-col { display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 0 4px; }
    .btn-swap { width: 32px; height: 32px; border-radius: 50%; padding: 0; display: flex; align-items: center; justify-content: center; font-size: 13px; background: #fff; border: 1px solid #3c8dbc; color: #3c8dbc; box-shadow: 0 2px 4px rgba(0,0,0,0.1); cursor: pointer; transition: all .2s; }
    .btn-swap:hover { background: #3c8dbc; color: #fff; transform: rotate(180deg); }
    .pair-actions { display: flex; justify-content: space-between; align-items: center; margin-top: 10px; padding-top: 8px; border-top: 1px dashed #eee; font-size: 11px; }
    .ocr-extracted-preview { margin-top: 8px; padding: 6px 8px; background: #edf7ed; border-radius: 4px; font-size: 11px; color: #1e4620; display: none; }
    .progress-wrapper { display: none; margin-top: 15px; background: #fff; padding: 15px; border-radius: 6px; border: 1px solid #d2d6de; }
</style>

<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><i class="fa fa-id-card-o text-primary"></i> <?= trans('upload_visit_cards') ?? 'Upload & Ghép Cặp Danh Thiếp Hàng Loạt'; ?></h3>
                    <p class="text-muted" style="margin-top: 5px; margin-bottom: 0;">
                        Tải lên hàng loạt ảnh danh thiếp, tự động chia cặp 2 mặt trước/sau để kiểm tra trực quan trước khi quét AI OCR.
                    </p>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('members'); ?>" class="btn btn-default btn-sm"><i class="fa fa-bars"></i> Danh sách đối tác</a>
                </div>
            </div>

            <div class="box-body">
                <!-- Mode & Instructions Bar -->
                <div class="well well-sm" style="background:#f9fbfd; border:1px solid #e2e8f0; border-radius:6px; margin-bottom:15px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                    <div style="display:flex; align-items:center; gap:15px;">
                        <span style="font-weight:600; color:#333;"><i class="fa fa-sliders text-primary"></i> Chế độ nạp ảnh:</span>
                        <label class="radio-inline" style="font-weight:600; cursor:pointer;">
                            <input type="radio" name="card_mode" value="pair" checked> <strong>2 Mặt (Ghép cặp Trước & Sau)</strong>
                        </label>
                        <label class="radio-inline" style="cursor:pointer;">
                            <input type="radio" name="card_mode" value="single"> 1 Mặt (Danh thiếp đơn lẻ)
                        </label>
                    </div>
                    <div>
                        <span class="badge bg-aqua" id="totalUploadedBadge" style="font-size:12px; padding:6px 12px;">Đã nạp: 0 ảnh (0 cặp)</span>
                        <button type="button" id="btnClearAll" class="btn btn-default btn-xs" style="margin-left:8px;"><i class="fa fa-trash-o"></i> Xóa tất cả</button>
                    </div>
                </div>

                <!-- Dropzone Area -->
                <div class="row">
                    <div class="col-md-12">
                        <input type="file" class="filepond" id="cardFilePond" name="card_files[]" multiple data-allow-reorder="true" data-max-file-size="10MB" data-max-files="60" accept="image/png, image/jpeg, image/webp" capture="camera">
                    </div>
                </div>

                <!-- Live Sequential Progress Bar -->
                <div id="progressWrapper" class="progress-wrapper">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                        <span id="progressStatusText" style="font-weight: 600; font-size: 13px; color: #333;"><i class="fa fa-spinner fa-spin"></i> Đang chuẩn bị quét OCR...</span>
                        <span id="progressPercentage" style="font-weight: bold; color: #3c8dbc;">0%</span>
                    </div>
                    <div class="progress progress-striped active" style="margin-bottom: 6px; height: 20px; border-radius: 4px;">
                        <div id="progressBar" class="progress-bar progress-bar-primary" role="progressbar" style="width: 0%;"></div>
                    </div>
                    <small id="progressSubText" class="text-muted">Đang phân tích tuần tự từng cặp danh thiếp để tối ưu độ chính xác...</small>
                </div>

                <!-- Pre-OCR Pairing Grid Container -->
                <div id="pairingArea" style="margin-top: 10px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                        <h4 style="margin:0; font-weight:700; color:#2c3e50;"><i class="fa fa-object-group text-primary"></i> Kiểm Tra & Ghép Cặp Trước Khi Quét OCR (<span id="pairCount">0</span>)</h4>
                        <button type="button" id="btnStartOcrPairs" class="btn btn-success btn-lg" style="font-weight:700; padding:10px 25px;" disabled>
                            <i class="fa fa-magic"></i> Bắt Đầu Quét AI OCR (<span id="btnPairCount">0</span> Cặp)
                        </button>
                    </div>

                    <div id="emptyNotice" class="text-center text-muted" style="padding: 50px 20px; background:#fafbfc; border:1px dashed #d2d6de; border-radius:8px;">
                        <i class="fa fa-id-card-o" style="font-size: 48px; color: #ccd5dc;"></i>
                        <p style="margin-top: 12px; font-size: 14px; font-weight: 500;">Chưa có ảnh danh thiếp nào.<br><span style="font-size:12px; color:#888;">Kéo thả nhiều ảnh hoặc chụp từ camera ở khung trên. Hệ thống sẽ tự động ghép cặp Mặt trước - Mặt sau để bạn kiểm tra trước khi quét.</span></p>
                    </div>

                    <div id="pairGrid" class="pair-container"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FilePond Plugins CDN -->
<script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
<script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>

<script>
$(document).ready(function () {
    FilePond.registerPlugin(FilePondPluginFileValidateType, FilePondPluginFileValidateSize, FilePondPluginImagePreview);

    var rawFiles = []; // Array of uploaded files: [{id, tempId, filePath, filename, thumbUrl}]
    var pairs = [];    // Array of pairs: [{id, mode: 'pair'|'single', front: fileObj, back: fileObj, status: 'ready'|'processing'|'done'|'error', ocrResult: null}]
    var isOcrRunning = false;

    var pond = FilePond.create(document.getElementById('cardFilePond'), {
        allowMultiple: true,
        maxFiles: 60,
        acceptedFileTypes: ['image/jpeg', 'image/png', 'image/webp'],
        labelIdle: '<i class="fa fa-camera" style="font-size:24px;color:#3c8dbc;display:block;margin-bottom:6px;"></i> Kéo & Thả danh thiếp hàng loạt hoặc <span class="filepond--label-action">Chọn Tệp / Chụp Ảnh</span>',
        imagePreviewHeight: 100,
        server: {
            process: function (fieldName, file, metadata, load, error, progress, abort) {
                var formData = new FormData();
                formData.append('card_file', file);
                formData.append(VrConfig.csrfTokenName, $('meta[name="X-CSRF-TOKEN"]').attr('content'));

                var xhr = new XMLHttpRequest();
                xhr.open('POST', '<?= adminUrl("members/upload-file-ajax"); ?>');
                xhr.upload.onprogress = function (e) { progress(e.lengthComputable, e.loaded, e.total); };
                xhr.onload = function () {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        try {
                            var res = JSON.parse(xhr.responseText);
                            if (res.status === 'success') {
                                var reader = new FileReader();
                                reader.onload = function (evt) {
                                    var fObj = {
                                        tempId: 'f_' + Date.now() + '_' + Math.random().toString(36).substr(2, 5),
                                        filePath: res.file_path,
                                        filename: res.filename,
                                        thumbUrl: evt.target.result
                                    };
                                    rawFiles.push(fObj);
                                    rebuildPairs();
                                };
                                reader.readAsDataURL(file);
                                load(res.file_path);
                            } else { error('Lỗi nạp file'); }
                        } catch (err) { error('Phản hồi server không hợp lệ'); }
                    } else { error('Lỗi kết nối upload'); }
                };
                xhr.send(formData);
                return { abort: function () { xhr.abort(); abort(); } };
            }
        }
    });

    $('input[name="card_mode"]').on('change', function () {
        rebuildPairs();
    });

    function rebuildPairs() {
        var mode = $('input[name="card_mode"]:checked').val();
        pairs = [];

        if (mode === 'pair') {
            for (var i = 0; i < rawFiles.length; i += 2) {
                var front = rawFiles[i];
                var back = (i + 1 < rawFiles.length) ? rawFiles[i + 1] : null;
                pairs.push({
                    id: 'pair_' + (pairs.length + 1),
                    mode: back ? 'pair' : 'single',
                    front: front,
                    back: back,
                    status: 'ready',
                    ocrResult: null
                });
            }
        } else {
            for (var j = 0; j < rawFiles.length; j++) {
                pairs.push({
                    id: 'single_' + (j + 1),
                    mode: 'single',
                    front: rawFiles[j],
                    back: null,
                    status: 'ready',
                    ocrResult: null
                });
            }
        }

        renderPairGrid();
        updateStats();
    }

    function renderPairGrid() {
        if (pairs.length === 0) {
            $('#emptyNotice').show();
            $('#pairGrid').html('');
            $('#btnStartOcrPairs').prop('disabled', true);
            return;
        }

        $('#emptyNotice').hide();
        $('#btnStartOcrPairs').prop('disabled', isOcrRunning);

        var html = '';
        pairs.forEach(function (pair, idx) {
            var cardNum = idx + 1;
            var statusBadge = '<span class="label label-default">Chờ quét OCR</span>';
            if (pair.status === 'processing') statusBadge = '<span class="label label-warning"><i class="fa fa-spinner fa-spin"></i> Đang OCR</span>';
            else if (pair.status === 'done') statusBadge = '<span class="label label-success"><i class="fa fa-check"></i> Đã xong</span>';
            else if (pair.status === 'error') statusBadge = '<span class="label label-danger"><i class="fa fa-times"></i> Lỗi</span>';

            var frontThumb = pair.front ? '<img src="' + pair.front.thumbUrl + '" class="slot-thumb" title="' + pair.front.filename + '">' : '<div style="color:#aaa;padding:30px 0;"><i class="fa fa-image"></i> Chưa có</div>';
            var frontName = pair.front ? pair.front.filename : 'N/A';

            var backSlot = '';
            if (pair.back) {
                backSlot = '<div class="card-slot">' +
                           '  <img src="' + pair.back.thumbUrl + '" class="slot-thumb" title="' + pair.back.filename + '">' +
                           '  <div class="slot-name">' + pair.back.filename + '</div>' +
                           '  <div><span class="slot-badge label-warning">MẶT SAU</span></div>' +
                           '</div>';
            } else {
                backSlot = '<div class="card-slot empty-slot" onclick="alert(\'Để thêm mặt sau, bạn hãy nạp thêm ảnh vào khung upload phía trên.\')">' +
                           '  <i class="fa fa-plus-circle" style="font-size:24px; margin-bottom:5px;"></i>' +
                           '  <div style="font-size:11px; font-weight:600;">(Thẻ 1 Mặt)</div>' +
                           '  <small style="font-size:10px; color:#999;">Khuyết mặt sau</small>' +
                           '</div>';
            }

            var swapBtn = pair.back ? '<button type="button" class="btn-swap btn-swap-pair" data-index="' + idx + '" title="Hoán đổi Mặt trước & Mặt sau"><i class="fa fa-exchange"></i></button>' : '<div style="width:32px;"></div>';

            html += '<div class="pair-card ' + pair.status + '" id="pair-box-' + idx + '">' +
                    '  <div class="pair-header">' +
                    '    <span class="pair-title"><i class="fa fa-id-card text-primary"></i> Cặp Thẻ #' + cardNum + ' ' + (pair.back ? '<span class="label label-info" style="font-size:10px;">2 Mặt</span>' : '<span class="label label-default" style="font-size:10px;">1 Mặt</span>') + '</span>' +
                    '    <div>' + statusBadge + '</div>' +
                    '  </div>' +
                    '  <div class="pair-slots">' +
                    '    <div class="card-slot">' +
                           frontThumb +
                    '      <div class="slot-name">' + frontName + '</div>' +
                    '      <div><span class="slot-badge label-primary">MẶT TRƯỚC</span></div>' +
                    '    </div>' +
                    '    <div class="swap-btn-col">' + swapBtn + '</div>' +
                         backSlot +
                    '  </div>' +
                    '  <div id="ocr-preview-' + idx + '" class="ocr-extracted-preview"></div>' +
                    '  <div class="pair-actions">' +
                    '    <span><a href="javascript:void(0)" class="btn-split-pair text-muted" data-index="' + idx + '"><i class="fa fa-scissors"></i> Tách riêng</a></span>' +
                    '    <span><a href="javascript:void(0)" class="btn-remove-pair text-danger" data-index="' + idx + '"><i class="fa fa-trash-o"></i> Xóa cặp này</a></span>' +
                    '  </div>' +
                    '</div>';
        });

        $('#pairGrid').html(html);
    }

    function updateStats() {
        var totalImages = rawFiles.length;
        var totalPairs = pairs.length;
        $('#totalUploadedBadge').text('Đã nạp: ' + totalImages + ' ảnh (' + totalPairs + ' cặp)');
        $('#pairCount').text(totalPairs + ' Cặp');
        $('#btnPairCount').text(totalPairs);
    }

    // Event: Swap Front and Back in Pair
    $(document).on('click', '.btn-swap-pair', function () {
        if (isOcrRunning) return;
        var idx = parseInt($(this).data('index'), 10);
        if (pairs[idx] && pairs[idx].front && pairs[idx].back) {
            var temp = pairs[idx].front;
            pairs[idx].front = pairs[idx].back;
            pairs[idx].back = temp;
            renderPairGrid();
        }
    });

    // Event: Split pair into 2 single cards
    $(document).on('click', '.btn-split-pair', function () {
        if (isOcrRunning) return;
        var idx = parseInt($(this).data('index'), 10);
        var targetPair = pairs[idx];
        if (targetPair && targetPair.back) {
            var frontItem = targetPair.front;
            var backItem = targetPair.back;
            pairs.splice(idx, 1, 
                { id: 'single_' + Date.now() + '_1', mode: 'single', front: frontItem, back: null, status: 'ready', ocrResult: null },
                { id: 'single_' + Date.now() + '_2', mode: 'single', front: backItem, back: null, status: 'ready', ocrResult: null }
            );
            renderPairGrid();
            updateStats();
        }
    });

    // Event: Remove pair
    $(document).on('click', '.btn-remove-pair', function () {
        if (isOcrRunning) return;
        var idx = parseInt($(this).data('index'), 10);
        var targetPair = pairs[idx];
        if (targetPair) {
            if (targetPair.front) rawFiles = rawFiles.filter(function (f) { return f.tempId !== targetPair.front.tempId; });
            if (targetPair.back) rawFiles = rawFiles.filter(function (f) { return f.tempId !== targetPair.back.tempId; });
            pairs.splice(idx, 1);
            renderPairGrid();
            updateStats();
        }
    });

    // Clear All
    $('#btnClearAll').on('click', function () {
        if (isOcrRunning) return;
        pond.removeFiles();
        rawFiles = [];
        pairs = [];
        renderPairGrid();
        updateStats();
        $('#progressWrapper').hide();
    });

    // Sequential OCR Execution
    $('#btnStartOcrPairs').on('click', function () {
        if (pairs.length === 0 || isOcrRunning) return;

        isOcrRunning = true;
        $('#btnStartOcrPairs').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Đang Xử Lý OCR...');
        $('#btnClearAll').prop('disabled', true);
        $('#progressWrapper').slideDown(200);

        var total = pairs.length;
        var processedResults = [];

        function runPairOcr(index) {
            if (index >= total) {
                // Done all
                $('#progressBar').css('width', '100%').removeClass('progress-bar-primary').addClass('progress-bar-success');
                $('#progressPercentage').text('100%');
                $('#progressStatusText').html('<i class="fa fa-check-circle text-success"></i> Hoàn thành quét tất cả ' + total + ' cặp danh thiếp!');
                $('#progressSubText').text('Đang chuyển hướng sang bảng xác nhận thông tin doanh nghiệp...');

                setTimeout(function () {
                    if (processedResults.length > 0) {
                        var $form = $('<form action="<?= adminUrl("members/confirm-ocr"); ?>" method="POST" style="display:none;"></form>');
                        $form.append('<input type="hidden" name="' + VrConfig.csrfTokenName + '" value="' + $('meta[name="X-CSRF-TOKEN"]').attr('content') + '">');
                        var $input = $('<input type="hidden" name="cards_json">');
                        $input.val(JSON.stringify(processedResults));
                        $form.append($input);
                        $('body').append($form);
                        $form.submit();
                    } else {
                        window.location.href = '<?= adminUrl("members/confirm-ocr"); ?>';
                    }
                }, 1000);
                return;
            }

            var pair = pairs[index];
            pair.status = 'processing';
            renderPairGrid();

            var currentPercent = Math.round((index / total) * 100);
            $('#progressBar').css('width', currentPercent + '%');
            $('#progressPercentage').text(currentPercent + '%');
            $('#progressStatusText').html('<i class="fa fa-spinner fa-spin"></i> Đang OCR Cặp #' + (index + 1) + '/' + total + '...');

            $.ajax({
                url: '<?= adminUrl("members/ocr-pair-ajax"); ?>',
                type: 'POST',
                data: {
                    front_path: pair.front ? pair.front.filePath : '',
                    back_path: pair.back ? pair.back.filePath : '',
                    side: pair.back ? 'pair' : 'single',
                    pair_index: index,
                    '<?= csrf_token(); ?>': '<?= csrf_hash(); ?>'
                },
                dataType: 'json',
                success: function (res) {
                    if (res && res.status === 'success' && res.data) {
                        pair.status = 'done';
                        pair.ocrResult = res.data;
                        processedResults.push(res.data);
                        showExtractedPreview(index, res.data);
                    } else {
                        var fb = makeFallback(pair);
                        pair.status = 'done';
                        pair.ocrResult = fb;
                        processedResults.push(fb);
                    }
                },
                error: function () {
                    var fb = makeFallback(pair);
                    pair.status = 'done';
                    pair.ocrResult = fb;
                    processedResults.push(fb);
                },
                complete: function () {
                    renderPairGrid();
                    setTimeout(function () { runPairOcr(index + 1); }, 150);
                }
            });
        }

        function makeFallback(pair) {
            var name = pair.front ? pair.front.filename.replace(/\.[^/.]+$/, '').replace(/[_-]/g, ' ') : 'Danh thiếp';
            return {
                detected_language: 'vi',
                company_name: name,
                company_name_vi: name,
                file_path: pair.front ? pair.front.filePath : (pair.back ? pair.back.filePath : ''),
                side: pair.back ? 'pair' : 'single'
            };
        }

        function showExtractedPreview(idx, data) {
            var $box = $('#ocr-preview-' + idx);
            var title = data.company_name || data.company_name_vi || 'Không rõ tên công ty';
            var contact = data.contact_name ? ' | LH: ' + data.contact_name : '';
            var phone = data.phone ? ' | SĐT: ' + data.phone : '';
            $box.html('<strong>✔ ' + title + '</strong>' + contact + phone).slideDown(150);
        }

        // Start sequential OCR from 1st pair
        runPairOcr(0);
    });
});
</script>
