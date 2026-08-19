<?= view("email/_header", ["subject" => $subject ?? "Mã Xác Thực Bình Chọn"]); ?>
<table role="presentation" class="main" style="background: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0;">
    <tr>
        <td class="wrapper" style="padding: 30px 25px;">
            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td style="text-align: center;">
                        <span style="display: inline-block; padding: 5px 14px; background: linear-gradient(135deg, #0A192F 0%, #1e293b 100%); color: #D4AF37; font-size: 11px; font-weight: 700; border-radius: 20px; text-transform: uppercase; letter-spacing: 1.2px; margin-bottom: 14px; border: 1px solid #D4AF37;">
                            TOP BEST GLOBAL NATIONAL AWARDS
                        </span>
                        <h1 style="font-size: 22px; line-height: 30px; font-weight: 800; color: #0A192F; margin: 0 0 10px 0;">
                            Mã Xác Thực Bình Chọn Trực Tuyến
                        </h1>
                        <p style="font-size: 14px; color: #475569; line-height: 22px; margin: 0 0 20px 0;">
                            Bạn đang thực hiện bình chọn cho ứng viên <strong><?= esc($candidate_name ?? "Ứng Viên Tiêu Biểu"); ?></strong> tại Cổng thông tin vinh danh quốc gia TOP BEST GLOBAL.
                        </p>

                        <div style="background: linear-gradient(180deg, #faf7ee 0%, #fdfbf7 100%); border: 2px dashed #D4AF37; border-radius: 12px; padding: 22px; max-width: 320px; margin: 0 auto 25px auto; text-align: center;">
                            <div style="font-size: 11px; font-weight: 700; color: #856404; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 8px;">
                                MÃ XÁC THỰC OTP (6 CHỮ SỐ)
                            </div>
                            <div style="font-size: 36px; font-weight: 900; letter-spacing: 10px; color: #0A192F; font-family: Consolas, Monaco, monospace; text-indent: 10px;">
                                <?= esc($otp_code ?? "000000"); ?>
                            </div>
                            <div style="font-size: 12px; color: #b45309; margin-top: 8px; font-weight: 600;">
                                Hiệu lực trong <strong>5 phút</strong> • Chỉ sử dụng 1 lần
                            </div>
                        </div>

                        <p style="font-size: 12px; color: #64748b; line-height: 19px; margin: 0 0 15px 0;">
                            Hệ thống bảo vệ bình chọn minh bạch bằng công nghệ mã hóa SHA256 & Rate Limiting. Nếu bạn không thực hiện yêu cầu này, vui lòng bỏ qua email.
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<?= view("email/_footer"); ?>
