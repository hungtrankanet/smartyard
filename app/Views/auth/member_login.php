<div id="wrapper">
    <div class="container" style="max-width: 480px; margin: 50px auto; padding: 0 15px;">
        <div class="page-content" style="background: #ffffff; border-radius: 12px; box-shadow: 0 8px 30px rgba(0,0,0,0.08); padding: 35px 30px; border: 1px solid #eef2f6;">
            <div class="text-center" style="margin-bottom: 25px;">
                <span class="label label-primary" style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px; padding: 4px 10px; border-radius: 20px;">B2B Logistics Portal</span>
                <h2 style="font-size: 24px; font-weight: 700; color: #1e293b; margin-top: 10px; margin-bottom: 6px;">Đăng Nhập Thành Viên</h2>
                <p style="color: #64748b; font-size: 13px; margin: 0;">Truy cập Cổng Thành Viên TOP BEST GLOBAL & kết nối đối tác</p>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger" style="border-radius: 6px; font-size: 13px; padding: 10px 15px;">
                    <i class="fa fa-exclamation-circle"></i> <?= session()->getFlashdata('error'); ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success" style="border-radius: 6px; font-size: 13px; padding: 10px 15px;">
                    <i class="fa fa-check-circle"></i> <?= session()->getFlashdata('success'); ?>
                </div>
            <?php endif; ?>

            <form action="<?= langBaseUrl('member/login-post'); ?>" method="POST">
                <?= csrf_field(); ?>
                <div class="form-group">
                    <label style="font-weight: 600; font-size: 13px; color: #334155;">Email Đăng Nhập</label>
                    <div class="input-group">
                        <span class="input-group-addon" style="background: #f8fafc; border-color: #cbd5e1;"><i class="fa fa-envelope-o text-muted"></i></span>
                        <input type="email" name="email" class="form-control input-lg" placeholder="email@domain.com" value="<?= old('email'); ?>" required style="font-size: 14px; border-color: #cbd5e1;">
                    </div>
                </div>

                <div class="form-group">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <label style="font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 5px;">Mật Khẩu</label>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon" style="background: #f8fafc; border-color: #cbd5e1;"><i class="fa fa-lock text-muted"></i></span>
                        <input type="password" name="password" class="form-control input-lg" placeholder="••••••••" required style="font-size: 14px; border-color: #cbd5e1;">
                    </div>
                </div>

                <div style="margin-top: 20px;">
                    <button type="submit" class="btn btn-primary btn-block btn-lg" style="font-weight: 700; font-size: 15px; border-radius: 6px; padding: 12px;">
                        <i class="fa fa-sign-in"></i> Đăng Nhập Vào Member Portal
                    </button>
                </div>
            </form>

            <hr style="margin: 25px 0; border-color: #f1f5f9;">
            <div class="text-center" style="font-size: 13px; color: #64748b;">
                Chưa có tài khoản doanh nghiệp? <a href="<?= langBaseUrl('member/register'); ?>" style="font-weight: 700; color: #3c8dbc;">Đăng ký thành viên nhận OTP</a>
            </div>
        </div>
    </div>
</div>
