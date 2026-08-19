<?= view('email/_header', ['subject' => $subject]); ?>
<table role="presentation" class="main" style="background: #ffffff; border-radius: 8px; overflow: hidden;">
    <tr>
        <td class="wrapper" style="padding: 30px 25px;">
            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td style="text-align: center;">
                        <span style="display: inline-block; padding: 4px 12px; background: #e0f2fe; color: #0284c7; font-size: 11px; font-weight: bold; border-radius: 20px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px;">
                            B2B Logistics Member Verification
                        </span>
                        <h1 style="text-decoration: none; font-size: 22px; line-height: 28px; font-weight: 800; color: #0f172a; margin: 0 0 10px 0;">
                            Mã Xác Thực Đăng Ký Tài Khoản
                        </h1>
                        <p style="font-size: 14px; color: #475569; line-height: 22px; margin: 0 0 25px 0;">
                            Xin chào <strong><?= esc($company_name ?? 'Quý Doanh nghiệp'); ?></strong>,<br>
                            Dưới đây là mã xác thực OTP 6 chữ số để kích hoạt tài khoản thành viên trên mạng lưới TOP BEST GLOBAL:
                        </p>

                        <!-- Big OTP Code Box -->
                        <div style="background: #f8fafc; border: 2px dashed #3c8dbc; border-radius: 10px; padding: 20px; max-width: 320px; margin: 0 auto 25px auto; text-align: center;">
                            <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px;">
                                Mã Xác Thực OTP
                            </div>
                            <div style="font-size: 32px; font-weight: 900; letter-spacing: 8px; color: #1e40af; font-family: Consolas, Monaco, monospace;">
                                <?= esc($otp_code); ?>
                            </div>
                            <div style="font-size: 11px; color: #94a3b8; margin-top: 6px;">
                                (Mã có hiệu lực trong vòng <strong>5 phút</strong>)
                            </div>
                        </div>

                        <p style="font-size: 13px; color: #64748b; line-height: 20px; margin: 0 0 15px 0;">
                            Nếu bạn không thực hiện yêu cầu này, vui lòng bỏ qua email hoặc thông báo cho bộ phận hỗ trợ của chúng tôi.
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<?= view('email/_footer'); ?>
