<!-- Member Login View -->
<div class="py-5" style="background: linear-gradient(135deg, #0A192F 0%, #1A4C96 100%); min-height: 80vh; display: flex; align-items: center;">
    <div class="container" style="max-width: 480px;">
        
        <div class="card border-0 shadow-lg p-4 p-md-5" style="border-radius: 16px; background: #ffffff; border-top: 4px solid #D9A441 !important;">
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center mb-2" style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #D9A441, #B8860B); color: #0A192F; font-size: 22px;">
                    <i class="fa-solid fa-building-user"></i>
                </div>
                <h2 class="font-serif text-primary mb-1" style="font-size: 1.5rem; font-weight: 800;">Đăng Nhập Thành Viên</h2>
                <p class="text-muted small mb-0">Cổng Quản Trị Hồ Sơ Doanh Nghiệp TOP BEST GLOBAL</p>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger rounded py-2 px-3 small mb-3">
                    <i class="fa-solid fa-circle-exclamation mr-1"></i> <?= session()->getFlashdata('error'); ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success rounded py-2 px-3 small mb-3">
                    <i class="fa-solid fa-circle-check mr-1"></i> <?= session()->getFlashdata('success'); ?>
                </div>
            <?php endif; ?>

            <form action="<?= langBaseUrl('member/login-post'); ?>" method="POST">
                <?= csrf_field(); ?>
                
                <div class="form-group mb-3">
                    <label class="font-weight-bold small text-muted mb-1">Email Đăng Ký Doanh Nghiệp *</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light border-right-0 text-muted"><i class="fa-solid fa-envelope"></i></span>
                        </div>
                        <input type="email" name="email" class="form-control border-left-0" placeholder="email@domain.com" value="<?= old('email'); ?>" required style="height: 46px; font-size: 0.9rem;">
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label class="font-weight-bold small text-muted mb-1">Mật Khẩu *</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light border-right-0 text-muted"><i class="fa-solid fa-lock"></i></span>
                        </div>
                        <input type="password" name="password" class="form-control border-left-0" placeholder="••••••••" required style="height: 46px; font-size: 0.9rem;">
                    </div>
                </div>

                <button type="submit" class="btn btn-tbg-cta btn-block py-2 font-weight-bold" style="height: 46px; font-size: 0.95rem;">
                    <i class="fa-solid fa-right-to-bracket mr-2"></i> Đăng Nhập Hệ Thống
                </button>
            </form>

            <hr class="my-4">

            <div class="text-center small text-muted">
                Chưa có hồ sơ doanh nghiệp? 
                <a href="<?= langBaseUrl('doanh-nghiep/dang-ky'); ?>" class="font-weight-bold text-primary">
                    Đăng ký đề cử TOP BEST
                </a>
            </div>
        </div>

    </div>
</div>
