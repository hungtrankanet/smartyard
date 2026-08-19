<div class="row" style="margin-bottom: 15px;">
    <div class="col-sm-12">
        <div class="alert alert-info alert-large m-t-10" style="border-radius: 8px; border-left: 5px solid #2563eb;">
            <strong><i class="fa fa-envelope-open"></i> Quản Trị Email Marketing & Bản Tin Doanh Nghiệp</strong><br>
            Hệ thống hỗ trợ gửi email marketing hàng loạt theo các mẫu giao diện chuẩn hóa (Bản tin thị trường, thông báo xúc tiến thương mại) hoặc tự do biên tập nội dung.
        </div>
    </div>
</div>

<!-- Template Selection Box -->
<div class="row">
    <div class="col-sm-12">
        <div class="box box-success" style="border-radius: 8px; border-top: 3px solid #00a65a;">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-magic text-green"></i> Chọn Mẫu Email Marketing (Email Templates)</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-4 col-sm-6">
                        <div class="form-group">
                            <label>Chọn Mẫu Giao Diện Email:</label>
                            <select id="select_email_template" class="form-control">
                                <option value="template_news" selected>Mẫu 1: Bản Tin Tin Tức Mới Nhất (Latest News Digest)</option>
                                <option value="template_custom">Soạn Email Tự Do (Blank Email)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6" id="box_template_news_count">
                        <div class="form-group">
                            <label>Số Lượng Tin Tức (Tối đa 10 tin):</label>
                            <select id="template_posts_count" class="form-control">
                                <option value="2">2 Tin mới nhất</option>
                                <option value="3">3 Tin mới nhất</option>
                                <option value="4" selected>4 Tin mới nhất (Khuyên dùng)</option>
                                <option value="5">5 Tin mới nhất</option>
                                <option value="6">6 Tin mới nhất</option>
                                <option value="8">8 Tin mới nhất</option>
                                <option value="10">10 Tin mới nhất</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-6" id="box_template_news_lang">
                        <div class="form-group">
                            <label>Ngôn Ngữ Bài Viết:</label>
                            <select id="template_lang_id" class="form-control">
                                <?php if (!empty($languages)): ?>
                                    <?php foreach ($languages as $l): ?>
                                        <option value="<?= $l->id; ?>" <?= ($activeLang->id ?? 1) == $l->id ? 'selected' : ''; ?>><?= esc($l->name); ?></option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="1">Tiếng Việt</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6" style="padding-top: 25px;">
                        <button type="button" id="btn_apply_template" class="btn btn-success btn-block" style="font-weight: 700;">
                            <i class="fa fa-bolt"></i> Áp Dụng Mẫu Email
                        </button>
                    </div>
                </div>

                <div id="template_alert" style="display: none; margin-top: 10px;" class="alert alert-success"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary" style="border-radius: 8px;">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-paper-plane text-primary"></i> <?= trans('send_email_subscriber'); ?></h3>
            </div>
            <form id="form_send_email">
                <div class="box-body">
                    <div class="form-group" style="margin-bottom: 10px;">
                        <label><?= trans('to'); ?> (<?= count($emails ?? []); ?> người nhận):</label>
                        <?php if (!empty($emails)): ?>
                            <p style="max-height: 120px; overflow-y: auto; background: #f8fafc; padding: 10px; border-radius: 6px; border: 1px solid #e2e8f0;">
                                <?php foreach ($emails as $email): ?>
                                    <label class="label-newsletter-email"><?= $email; ?></label>
                                <?php endforeach; ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label><?= trans('subject'); ?>: <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="subject" id="newsletter_subject" class="form-control" placeholder="<?= trans('subject'); ?>" required style="font-weight: 700; font-size: 15px;">
                    </div>

                    <div class="form-group">
                        <label><?= trans('content'); ?>:</label>
                        <div class="row" style="margin-bottom: 6px;">
                            <div class="col-sm-12 editor-buttons">
                                <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#file_manager_image" data-image-type="editor"><i class="fa fa-image"></i>&nbsp;&nbsp;&nbsp;<?= trans("add_image"); ?></button>
                                <button type="button" id="btn_preview_email" class="btn btn-sm btn-info pull-right"><i class="fa fa-eye"></i> Xem Trước Email (Preview)</button>
                            </div>
                        </div>
                        <textarea class="tinyMCE form-control" id="newsletter_body" name="body" style="height: 400px;"></textarea>
                    </div>
                </div>
                
                <div class="box-footer">
                    <a href="<?= adminUrl('newsletter'); ?>" id="btn_newsletter_back" class="btn btn-default"><i class="fa fa-arrow-left"></i> <?= trans("back"); ?></a>
                    <button type="submit" id="btn_send_newsletter" class="btn btn-primary pull-right" style="font-weight: 700; font-size: 15px; padding: 8px 24px;">
                        <i class="fa fa-send"></i> Bắt Đầu Gửi Email Marketing
                    </button>
                    
                    <div class="col-sm-12 m-t-30">
                        <div class="row">
                            <div id="newsletter_spinner" class="newsletter-spinner">
                                <strong class="newsletter-sending text-primary" style="font-size: 16px;"><i class="fa fa-spinner fa-spin"></i> <?= trans("mail_is_being_sent"); ?></strong>
                                <strong class="text-newsletter-completed text-green" style="font-size: 18px;"><i class="fa fa-check-circle"></i> Đã hoàn thành gửi toàn bộ danh sách!</strong>
                                <div class="spinner" style="margin-top: 15px;">
                                    <div class="bounce1"></div>
                                    <div class="bounce2"></div>
                                    <div class="bounce3"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="newsletter-email-container">
                                <ul id="newsletter_sent_emails" class="list-group csv-uploaded-files"></ul>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Preview Email -->
<div class="modal fade" id="modalPreviewEmail" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 10px; overflow: hidden;">
            <div class="modal-header" style="background: #0f172a; color: #fff;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;">&times;</button>
                <h4 class="modal-title"><i class="fa fa-eye text-primary"></i> Xem Trước Bản Tin Email Marketing</h4>
            </div>
            <div class="modal-body" style="background: #f1f5f9; padding: 20px;">
                <div style="background: #fff; padding: 15px; border-radius: 8px; margin-bottom: 15px; border: 1px solid #cbd5e1;">
                    <strong>Tiêu đề gửi:</strong> <span id="preview_email_subject" style="color: #2563eb; font-weight: 700;"></span>
                </div>
                <div id="preview_email_content" style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #cbd5e1; max-height: 520px; overflow-y: auto;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng Xem Trước</button>
            </div>
        </div>
    </div>
</div>

<?= view('admin/file-manager/_load_file_manager', ['loadImages' => true, 'loadFiles' => false, 'loadVideos' => false, 'loadAudios' => false]); ?>

<script>
    var arrayEmails = "";
    var arraySent = [];
    <?php if(!empty($emails)): ?>var arrayEmails = <?= json_encode($emails); ?>;<?php endif; ?>

    var subject = "";
    var body = "";
    var sentCount = 0;

    // Handle Template Change
    $('#select_email_template').on('change', function() {
        if ($(this).val() === 'template_news') {
            $('#box_template_news_count, #box_template_news_lang').show();
        } else {
            $('#box_template_news_count, #box_template_news_lang').hide();
        }
    });

    // Apply Template Button
    $('#btn_apply_template').on('click', function() {
        var tpl = $('#select_email_template').val();
        if (tpl === 'template_news') {
            var $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Đang tải dữ liệu tin...');
            
            var data = {
                'posts_count': $('#template_posts_count').val(),
                'lang_id': $('#template_lang_id').val()
            };
            
            $.ajax({
                type: "POST",
                url: VrConfig.baseURL + '/admin/newsletter-generate-template',
                data: setAjaxData(data),
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') {
                        $('#newsletter_subject').val(res.subject);
                        if (typeof tinyMCE !== 'undefined' && tinyMCE.get('newsletter_body')) {
                            tinyMCE.get('newsletter_body').setContent(res.html);
                        } else {
                            $('#newsletter_body').val(res.html);
                        }
                        $('#template_alert').html('<i class="fa fa-check-circle"></i> Đã áp dụng mẫu bản tin với ' + res.posts_count + ' bài viết gần nhất vào trình soạn thảo.').show().delay(4000).fadeOut();
                    }
                },
                error: function() {
                    alert('Không thể tạo mẫu email tự động. Vui lòng thử lại.');
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<i class="fa fa-bolt"></i> Áp Dụng Mẫu Email');
                }
            });
        } else {
            if (typeof tinyMCE !== 'undefined' && tinyMCE.get('newsletter_body')) {
                tinyMCE.get('newsletter_body').setContent('');
            }
            $('#newsletter_subject').val('');
            $('#template_alert').html('<i class="fa fa-info-circle"></i> Đã chuyển sang chế độ soạn thảo tự do.').show().delay(3000).fadeOut();
        }
    });

    // Preview Email Button
    $('#btn_preview_email').on('click', function() {
        var subj = $('#newsletter_subject').val() || '(Chưa có tiêu đề)';
        var content = '';
        if (typeof tinyMCE !== 'undefined' && tinyMCE.get('newsletter_body')) {
            content = tinyMCE.get('newsletter_body').getContent();
        } else {
            content = $('#newsletter_body').val();
        }
        $('#preview_email_subject').text(subj);
        $('#preview_email_content').html(content || '<p style="color:#94a3b8; text-align:center;">(Nội dung email đang trống)</p>');
        $('#modalPreviewEmail').modal('show');
    });

    // Form Submit Send
    $("#form_send_email").submit(function (event) {
        event.preventDefault();
        if (typeof tinyMCE !== 'undefined' && tinyMCE.get('newsletter_body')) {
            body = tinyMCE.get('newsletter_body').getContent();
        } else {
            body = $("#newsletter_body").val();
        }
        subject = $("#newsletter_subject").val();

        if (!body || !subject) {
            alert('Vui lòng nhập đầy đủ tiêu đề và nội dung email.');
            return;
        }

        $("#newsletter_spinner").show();
        document.getElementById("btn_newsletter_back").disabled = true;
        document.getElementById("btn_send_newsletter").disabled = true;
        sendNewsletterEmail();
    });

    function sendNewsletterEmail() {
        var email = getNextEmail();
        if (email != "") {
            var data = {
                'subject': subject,
                'body': body,
                'email': email,
                'submit': "<?= $submit ?>"
            };
            $.ajax({
                type: "POST",
                url: VrConfig.baseURL + '/Admin/newsletterSendEmailPost',
                cache: false,
                data: setAjaxData(data),
                success: function (response) {
                    var obj = JSON.parse(response);
                    if (obj.result == 1) {
                        removeItemFromArray(arrayEmails, email);
                        arraySent.push(email);
                        sentCount = sentCount + 1;
                        $("#newsletter_sent_emails").prepend('<li class="list-group-item list-group-item-success"><i class="fa fa-check"></i>&nbsp;' + sentCount + '. ' + email + '</li>');
                        sendNewsletterEmail();
                    }
                }
            });
        } else {
            $("#newsletter_spinner .newsletter-sending").hide();
            $("#newsletter_spinner .spinner").hide();
            $("#newsletter_spinner .text-newsletter-completed").css('display', 'block');
        }
    }

    function getNextEmail() {
        var next_email = "";
        var i;
        for (i = 0; i < arrayEmails.length; i++) {
            if (arraySent.indexOf(arrayEmails[i]) < 0) {
                next_email = arrayEmails[i];
                break;
            }
        }
        return next_email;
    }

    function removeItemFromArray(array, item) {
        var index = array.indexOf(item);
        while (index > -1) {
            array.splice(index, 1);
            index = array.indexOf(item);
        }
    }
</script>

<style>
    .label-newsletter-email {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600 !important;
        color: #1e293b !important;
        background-color: #e2e8f0 !important;
        margin: 2px;
    }
    .newsletter-email-container {
        max-height: 250px;
        overflow-y: auto;
        margin-top: 15px;
    }
    .newsletter-spinner {
        display: none;
        text-align: center;
        font-size: 16px;
    }
    .text-newsletter-completed {
        display: none;
    }
</style>
