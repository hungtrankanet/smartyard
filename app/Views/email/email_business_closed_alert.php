<?= view('email/_header', ['subject' => $subject]); ?>
    <table role="presentation" class="main">
        <tr>
            <td class="wrapper">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>
                            <h1 style="text-decoration: none; font-size: 22px; line-height: 28px; font-weight: bold; color: #d9534f; text-align: left;">
                                [CẢNH BÁO] Doanh Nghiệp Có Dấu Hiệu Đóng Cửa
                            </h1>
                            <div class="mailcontent" style="line-height: 24px; font-size: 14px; color: #333333;">
                                <p>Kính gửi <strong>Ban Quản Trị TOP BEST GLOBAL</strong>,</p>
                                <p>Hệ thống quét định kỳ tự động phát hiện doanh nghiệp hội viên sau đây có dấu hiệu <strong>Đóng cửa / Ngừng hoạt động</strong>:</p>
                                
                                <table style="width: 100%; border-collapse: collapse; margin: 15px 0; background: #fafafa; border: 1px solid #eeeeee; border-radius: 4px;">
                                    <tr>
                                        <td style="padding: 10px; border-bottom: 1px solid #eeeeee; width: 35%; font-weight: bold;">Tên Doanh Nghiệp:</td>
                                        <td style="padding: 10px; border-bottom: 1px solid #eeeeee; color: #d9534f; font-weight: bold;"><?= esc($company_name ?? ''); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px; border-bottom: 1px solid #eeeeee; font-weight: bold;">Mã Số Thuế:</td>
                                        <td style="padding: 10px; border-bottom: 1px solid #eeeeee;"><?= esc($tax_code ?? 'Chưa cập nhật'); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px; border-bottom: 1px solid #eeeeee; font-weight: bold;">Địa Chỉ:</td>
                                        <td style="padding: 10px; border-bottom: 1px solid #eeeeee;"><?= esc($address ?? 'Chưa cập nhật'); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px; border-bottom: 1px solid #eeeeee; font-weight: bold;">Kết Quả Xác Minh:</td>
                                        <td style="padding: 10px; border-bottom: 1px solid #eeeeee; color: #d9534f; font-weight: bold;"><?= esc($verify_result ?? 'Đã đóng cửa'); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px; font-weight: bold;">Thời Gian Kiểm Tra:</td>
                                        <td style="padding: 10px;"><?= esc($verify_date ?? date('Y-m-d H:i:s')); ?></td>
                                    </tr>
                                </table>

                                <?php if (!empty($verify_details)): ?>
                                <p style="margin-top: 15px; font-size: 13px; color: #666666;">
                                    <strong>Chi tiết tín hiệu phát hiện:</strong><br>
                                    <?= nl2br(esc($verify_details)); ?>
                                </p>
                                <?php endif; ?>

                                <p style="margin-top: 20px;">
                                    Vui lòng đăng nhập vào trang Quản trị TOP BEST GLOBAL để kiểm tra thông tin và xử lý trạng thái hội viên theo quy chế.
                                </p>

                                <?php if (!empty($admin_url)): ?>
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary" style="margin-top: 20px;">
                                    <tbody>
                                        <tr>
                                            <td align="center">
                                                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <a href="<?= esc($admin_url); ?>" target="_blank" style="background-color: #d9534f; border-color: #d9534f;">Xem Chi Tiết Hội Viên</a>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
<?= view('email/_footer'); ?>
