<style>
    .reg-wrapper { background: #f8fafc; min-height: 80vh; padding: 40px 15px; }
    .reg-card { max-width: 680px; margin: 0 auto; background: #ffffff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); padding: 35px 30px; border: 1px solid #e2e8f0; }
    .reg-step-badge { display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; border-radius: 50%; font-size: 12px; font-weight: 700; margin-right: 6px; }
    .reg-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    @media (max-width: 640px) { .reg-grid-2 { grid-template-columns: 1fr; gap: 12px; } }
    .reg-form-group { margin-bottom: 16px; }
    .reg-form-group label { display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px; }
    .reg-input { width: 100%; height: 46px; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 14px; color: #1e293b; background: #ffffff; transition: border-color 0.2s, box-shadow 0.2s; box-sizing: border-box; }
    .reg-input:focus { border-color: #3b82f6; outline: none; box-shadow: 0 0 0 3px rgba(59,130,246,0.15); }
    .reg-btn-primary { width: 100%; height: 48px; background: #1d4ed8; color: #ffffff; border: none; border-radius: 8px; font-size: 15px; font-weight: 700; cursor: pointer; transition: background 0.2s, transform 0.1s; display: inline-flex; align-items: center; justify-content: center; gap: 8px; }
    .reg-btn-primary:hover { background: #1e40af; }
    .reg-btn-primary:active { transform: scale(0.99); }
    .reg-btn-success { width: 100%; height: 48px; background: #16a34a; color: #ffffff; border: none; border-radius: 8px; font-size: 15px; font-weight: 700; cursor: pointer; transition: background 0.2s; display: inline-flex; align-items: center; justify-content: center; gap: 8px; }
    .reg-btn-success:hover { background: #15803d; }
</style>

<div class="reg-wrapper">
    <div class="reg-card">
        <div class="text-center" style="margin-bottom: 25px; text-align: center;">
            <span style="display: inline-block; background: #eff6ff; color: #1d4ed8; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; padding: 5px 14px; border-radius: 20px; margin-bottom: 10px;">
                B2B Logistics Portal
            </span>
            <h2 style="font-size: 24px; font-weight: 800; color: #0f172a; margin: 0 0 6px 0;">Đăng Ký Thành Viên Doanh Nghiệp</h2>
            <p style="color: #64748b; font-size: 13px; margin: 0;">Gia nhập mạng lưới TOP BEST GLOBAL, quảng bá doanh nghiệp & kết nối đối tác toàn cầu.</p>
        </div>

        <!-- Steps Indicator -->
        <div style="display: flex; justify-content: center; align-items: center; margin-bottom: 25px; gap: 10px;">
            <div id="stepBadge1" style="display: flex; align-items: center; font-weight: 700; font-size: 12px; color: #1d4ed8;">
                <span class="reg-step-badge" style="background: #1d4ed8; color: #fff;">1</span> Thông Tin Doanh Nghiệp
            </div>
            <span style="color: #cbd5e1;">───</span>
            <div id="stepBadge2" style="display: flex; align-items: center; font-weight: 700; font-size: 12px; color: #94a3b8;">
                <span class="reg-step-badge" style="background: #e2e8f0; color: #64748b;">2</span> Xác Thực Email OTP
            </div>
        </div>

        <div id="regAlert" style="display: none; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; font-weight: 500;"></div>

        <!-- Form Step 1: Info Input -->
        <form id="formStep1" onsubmit="return false;">
            <?= csrf_field(); ?>
            <div class="reg-form-group">
                <label>Tên Doanh Nghiệp / Công Ty <span style="color: #ef4444;">*</span></label>
                <input type="text" name="company_name" id="regCompanyName" class="reg-input" placeholder="Ví dụ: Công ty Cổ phần TOP BEST GLOBAL" required>
            </div>

            <div class="reg-grid-2">
                <div class="reg-form-group">
                    <label>Người Đại Diện / Liên Hệ <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="full_name" id="regFullName" class="reg-input" placeholder="Họ và tên người đại diện" required>
                </div>
                <div class="reg-form-group">
                    <label>Số Điện Thoại / Hotline <span style="color: #ef4444;">*</span></label>
                    <input type="tel" name="phone" id="regPhone" class="reg-input" placeholder="0912 345 678" required>
                </div>
            </div>

            <div class="reg-form-group">
                <label>Email Doanh Nghiệp (Nhận mã OTP) <span style="color: #ef4444;">*</span></label>
                <input type="email" name="email" id="regEmail" class="reg-input" placeholder="contact@yourcompany.com" required>
                <small style="color: #64748b; font-size: 11px; display: block; margin-top: 4px;">Mã OTP xác thực sẽ được gửi trực tiếp đến địa chỉ email này.</small>
            </div>

            <div class="reg-grid-2">
                <div class="reg-form-group">
                    <label>Lĩnh Vực / Ngành Nghề</label>
                    <select name="industry_type_id" id="regIndustry" class="reg-input">
                        <option value="">-- Chọn lĩnh vực chính --</option>
                        <?php if (!empty($industries)): foreach ($industries as $ind): ?>
                            <option value="<?= $ind->id; ?>"><?= esc($ind->name); ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
                <div class="reg-form-group">
                    <label>Mật Khẩu Đăng Nhập <span style="color: #ef4444;">*</span></label>
                    <input type="password" name="password" id="regPassword" class="reg-input" placeholder="Tối thiểu 6 ký tự" minlength="6" required>
                </div>
            </div>

            <div class="reg-form-group">
                <label>Ngôn Ngữ Nhận Bản Tin & Email Marketing:</label>
                <select name="preferred_lang" id="regPreferredLang" class="reg-input">
                    <option value="vi" selected>🇻🇳 Tiếng Việt (Vietnamese - Mặc định)</option>
                    <option value="en">🇬🇧 Tiếng Anh (English - International)</option>
                </select>
                <small style="color: #64748b; font-size: 11px; display: block; margin-top: 4px;">Hệ thống sẽ gửi các bản tin logistics và báo cáo thị trường theo ngôn ngữ Quý khách lựa chọn.</small>
            </div>

            <div style="margin-top: 20px;">
                <button type="button" id="btnRequestOtp" class="reg-btn-primary">
                    <span>Tiếp Tục & Gửi Mã OTP Xác Thực</span> <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </form>

        <!-- Form Step 2: OTP Input -->
        <form id="formStep2" style="display: none;" onsubmit="return false;">
            <?= csrf_field(); ?>
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; text-align: center; margin-bottom: 20px;">
                <i class="fa-regular fa-envelope text-primary" style="font-size: 32px; color: #1d4ed8; margin-bottom: 8px; display: block;"></i>
                <h4 style="font-weight: 800; color: #0f172a; margin: 0 0 6px 0; font-size: 17px;">Nhập Mã OTP 6 Chữ Số</h4>
                <p style="color: #64748b; font-size: 13px; margin: 0;">
                    Mã xác nhận đã gửi đến: <strong id="displayTargetEmail" style="color: #0f172a;"></strong>
                </p>
            </div>

            <div class="reg-form-group" style="text-align: center;">
                <label style="display: block; margin-bottom: 10px;">Mã Xác Thực (OTP):</label>
                <input type="text" id="inputOtpCode" maxlength="6" class="reg-input" placeholder="123456" style="font-size: 26px; font-weight: 800; letter-spacing: 10px; text-align: center; max-width: 260px; margin: 0 auto; border: 2px solid #1d4ed8; color: #0f172a;" autocomplete="off">
                <small style="color: #64748b; font-size: 11px; display: block; margin-top: 8px;">Mã có hiệu lực trong 5 phút.</small>
            </div>

            <div style="margin-top: 20px;">
                <button type="button" id="btnVerifyOtp" class="reg-btn-success">
                    <i class="fa-solid fa-circle-check"></i> <span>Xác Nhận & Hoàn Tất Đăng Ký</span>
                </button>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 18px; font-size: 13px;">
                <a href="javascript:void(0)" id="btnBackToStep1" style="color: #64748b; text-decoration: none; font-weight: 600;"><i class="fa-solid fa-arrow-left"></i> Sửa lại thông tin</a>
                <div>
                    <span id="resendCountdown" style="color: #64748b;">Gửi lại mã sau: <strong>60s</strong></span>
                    <a href="javascript:void(0)" id="btnResendOtp" style="display: none; font-weight: 700; color: #1d4ed8; text-decoration: none;"><i class="fa-solid fa-rotate-right"></i> Gửi lại mã OTP</a>
                </div>
            </div>
        </form>

        <hr style="margin: 25px 0; border: none; border-top: 1px solid #f1f5f9;">
        <div class="text-center" style="font-size: 13px; color: #64748b; text-align: center;">
            Đã có tài khoản thành viên? <a href="<?= langBaseUrl('member/login'); ?>" style="font-weight: 700; color: #1d4ed8; text-decoration: none;">Đăng nhập ngay</a>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    var csrfName = '<?= csrf_token(); ?>';
    var csrfHash = '<?= csrf_hash(); ?>';
    var countdownTimer = null;

    function showAlert(msg, type) {
        var bg = (type === 'success') ? '#f0fdf4' : '#fef2f2';
        var col = (type === 'success') ? '#166534' : '#991b1b';
        var border = (type === 'success') ? '#bbf7d0' : '#fecaca';
        $('#regAlert').css({ 'background': bg, 'color': col, 'border': '1px solid ' + border }).html(msg).slideDown(150);
    }

    function startResendCountdown(seconds) {
        var remaining = seconds;
        $('#resendCountdown').show().find('strong').text(remaining + 's');
        $('#btnResendOtp').hide();
        if (countdownTimer) clearInterval(countdownTimer);
        countdownTimer = setInterval(function () {
            remaining--;
            if (remaining <= 0) {
                clearInterval(countdownTimer);
                $('#resendCountdown').hide();
                $('#btnResendOtp').fadeIn(150);
            } else {
                $('#resendCountdown').find('strong').text(remaining + 's');
            }
        }, 1000);
    }

    function handleRequestOtp() {
        var companyName = $('#regCompanyName').val().trim();
        var fullName = $('#regFullName').val().trim();
        var phone = $('#regPhone').val().trim();
        var email = $('#regEmail').val().trim();
        var password = $('#regPassword').val();

        if (!companyName || !fullName || !phone || !email || !password) {
            showAlert('Vui lòng điền đầy đủ tất cả các trường bắt buộc (*).', 'error');
            return;
        }
        if (password.length < 6) {
            showAlert('Mật khẩu phải có ít nhất 6 ký tự.', 'error');
            return;
        }

        var $btn = $('#btnRequestOtp');
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> <span>Đang gửi mã OTP...</span>');

        var postData = {
            company_name: companyName,
            email: email,
            [csrfName]: csrfHash
        };

        $.ajax({
            url: '<?= base_url("member/send-otp-ajax"); ?>',
            type: 'POST',
            data: postData,
            dataType: 'json',
            success: function (res) {
                if (res.csrf_token) {
                    csrfHash = res.csrf_token;
                    $('input[name="' + csrfName + '"]').val(csrfHash);
                }
                if (res.status === 'success') {
                    var msg = res.message;
                    if (res.debug_otp) {
                        msg += ' <br><span style="font-size:12px; color:#1e40af;">(Chế độ thử nghiệm - Mã OTP của bạn là: <strong style="font-size:15px; letter-spacing:2px;">' + res.debug_otp + '</strong>)</span>';
                    }
                    showAlert(msg, 'success');
                    $('#displayTargetEmail').text(email);
                    $('#formStep1').slideUp(150);
                    $('#formStep2').slideDown(150);
                    $('#stepBadge1').css('color', '#64748b').find('.reg-step-badge').css({ 'background': '#e2e8f0', 'color': '#64748b' });
                    $('#stepBadge2').css('color', '#1d4ed8').find('.reg-step-badge').css({ 'background': '#1d4ed8', 'color': '#ffffff' });
                    $('#inputOtpCode').val(res.debug_otp || '').focus();
                    startResendCountdown(60);
                } else {
                    showAlert(res.message || 'Không thể gửi mã OTP.', 'error');
                }
            },
            error: function (xhr) {
                var err = 'Lỗi kết nối máy chủ (' + xhr.status + '). Vui lòng thử lại.';
                showAlert(err, 'error');
            },
            complete: function () {
                $btn.prop('disabled', false).html('<span>Tiếp Tục & Gửi Mã OTP Xác Thực</span> <i class="fa-solid fa-arrow-right"></i>');
            }
        });
    }

    function handleVerifyOtp() {
        var otp = $('#inputOtpCode').val().trim();
        if (otp.length < 4) {
            showAlert('Vui lòng nhập đầy đủ mã OTP 6 chữ số.', 'error');
            return;
        }

        var $btn = $('#btnVerifyOtp');
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> <span>Đang kích hoạt tài khoản...</span>');

        var postData = {
            company_name: $('#regCompanyName').val().trim(),
            full_name: $('#regFullName').val().trim(),
            phone: $('#regPhone').val().trim(),
            email: $('#regEmail').val().trim(),
            password: $('#regPassword').val(),
            industry_type_id: $('#regIndustry').val(),
            preferred_lang: $('#regPreferredLang').val() || 'vi',
            otp: otp,
            [csrfName]: csrfHash
        };

        console.log('[MemberRegister] Sending verify OTP data:', postData);

        $.ajax({
            url: '<?= base_url("member/verify-otp-ajax"); ?>',
            type: 'POST',
            data: postData,
            dataType: 'json',
            success: function (res) {
                console.log('[MemberRegister] Verify OTP Response:', res);
                if (res.csrf_token) {
                    csrfHash = res.csrf_token;
                    $('input[name="' + csrfName + '"]').val(csrfHash);
                }
                if (res.status === 'success') {
                    showAlert(res.message, 'success');
                    setTimeout(function () {
                        window.location.href = res.redirect_url || '<?= langBaseUrl("member/dashboard"); ?>';
                    }, 1000);
                } else {
                    showAlert(res.message || 'Mã OTP không hợp lệ.', 'error');
                    $btn.prop('disabled', false).html('<i class="fa-solid fa-circle-check"></i> <span>Xác Nhận & Hoàn Tất Đăng Ký</span>');
                }
            },
            error: function (xhr) {
                console.error('[MemberRegister] Verify OTP Error Response:', xhr);
                showAlert('Lỗi kết nối máy chủ (' + xhr.status + ') khi kích hoạt tài khoản.', 'error');
                $btn.prop('disabled', false).html('<i class="fa-solid fa-circle-check"></i> <span>Xác Nhận & Hoàn Tất Đăng Ký</span>');
            }
        });
    }

    // Bind Button Click & Enter key
    $('#btnRequestOtp').on('click', handleRequestOtp);
    $('#formStep1 input').on('keypress', function (e) {
        if (e.which === 13) handleRequestOtp();
    });

    $('#btnVerifyOtp').on('click', handleVerifyOtp);
    $('#inputOtpCode').on('keypress', function (e) {
        if (e.which === 13) handleVerifyOtp();
    });

    // Back to Step 1
    $('#btnBackToStep1').on('click', function () {
        $('#formStep2').slideUp(150);
        $('#formStep1').slideDown(150);
        $('#stepBadge2').css('color', '#94a3b8').find('.reg-step-badge').css({ 'background': '#e2e8f0', 'color': '#64748b' });
        $('#stepBadge1').css('color', '#1d4ed8').find('.reg-step-badge').css({ 'background': '#1d4ed8', 'color': '#ffffff' });
    });

    // Resend OTP
    $('#btnResendOtp').on('click', function () {
        var email = $('#regEmail').val().trim();
        var companyName = $('#regCompanyName').val().trim();
        if (!email) return;
        $(this).hide();
        $('#resendCountdown').show().find('strong').text('Đang gửi...');

        $.ajax({
            url: '<?= base_url("member/send-otp-ajax"); ?>',
            type: 'POST',
            data: { email: email, company_name: companyName, [csrfName]: csrfHash },
            dataType: 'json',
            success: function (res) {
                if (res.csrf_token) {
                    csrfHash = res.csrf_token;
                    $('input[name="' + csrfName + '"]').val(csrfHash);
                }
                if (res.status === 'success') {
                    showAlert('Đã gửi lại mã OTP mới. Vui lòng kiểm tra email!', 'success');
                    startResendCountdown(60);
                } else {
                    showAlert(res.message || 'Không thể gửi lại mã.', 'error');
                    $('#resendCountdown').hide();
                    $('#btnResendOtp').show();
                }
            }
        });
    });
});
</script>
