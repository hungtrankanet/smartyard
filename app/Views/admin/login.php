<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= esc($title ?? 'Đăng nhập Quản Trị'); ?> - <?= esc($baseSettings->site_title ?? 'TOP BEST GLOBAL'); ?></title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="shortcut icon" type="image/png" href="<?= getFavicon(); ?>"/>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@700;900&family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        :root {
            --tbg-navy-dark: #0A192F;
            --tbg-navy-primary: #1A4C96;
            --tbg-gold-primary: #D9A441;
            --tbg-gold-dark: #B8860B;
            --tbg-gold-light: #F3E5AB;
        }
        body {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(135deg, #0A192F 0%, #0F274A 50%, #1A4C96 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            margin: 0;
        }
        .font-serif {
            font-family: 'Merriweather', serif;
        }
        .login-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.35);
            width: 100%;
            max-width: 440px;
            overflow: hidden;
            border: 1px solid rgba(217, 164, 65, 0.3);
        }
        .login-header {
            background: #0A192F;
            padding: 30px 25px 25px;
            text-align: center;
            border-bottom: 3px solid var(--tbg-gold-primary);
        }
        .login-body {
            padding: 35px 30px;
        }
        .form-control {
            height: 48px;
            font-size: 0.95rem;
            border-radius: 8px;
            border: 1.5px solid #CBD5E1;
            padding-left: 45px;
            transition: all 0.2s ease;
        }
        .form-control:focus {
            border-color: var(--tbg-navy-primary);
            box-shadow: 0 0 0 3px rgba(26, 76, 150, 0.15);
        }
        .input-icon-wrap {
            position: relative;
        }
        .input-icon-wrap i {
            position: absolute;
            left: 16px;
            top: 16px;
            color: #94A3B8;
            font-size: 1.05rem;
            z-index: 5;
        }
        .btn-tbg-submit {
            background: linear-gradient(135deg, #F3E5AB 0%, #D9A441 50%, #B8860B 100%);
            color: #0A192F !important;
            font-weight: 800;
            border-radius: 8px;
            height: 48px;
            font-size: 0.95rem;
            border: none;
            box-shadow: 0 4px 12px rgba(217, 164, 65, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.2s ease;
        }
        .btn-tbg-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(217, 164, 65, 0.4);
        }
        .alert {
            font-size: 0.88rem;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="login-card">
    <!-- Header -->
    <div class="login-header">
        <div class="d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px; border-radius: 12px; background: linear-gradient(135deg, #D9A441, #B8860B); color: #0A192F; font-size: 24px; box-shadow: 0 4px 15px rgba(217,164,65,0.4);">
            <i class="fa-solid fa-trophy"></i>
        </div>
        <h4 class="text-white font-serif mb-1" style="font-size: 1.3rem; letter-spacing: 0.5px;">
            <?= esc($baseSettings->application_name ?? 'TOP BEST GLOBAL'); ?>
        </h4>
        <div style="color: var(--tbg-gold-light); font-size: 0.72rem; font-weight: 700; letter-spacing: 1px;">
            CỔNG QUẢN TRỊ VIÊN HỆ THỐNG
        </div>
    </div>

    <!-- Body -->
    <div class="login-body">
        <?= view('admin/includes/_messages'); ?>

        <form action="<?= adminUrl('login-post'); ?>" method="post">
            <?= csrf_field(); ?>

            <div class="form-group mb-3">
                <label class="font-weight-bold small text-muted mb-1">Email Quản Trị *</label>
                <div class="input-icon-wrap">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" name="email" class="form-control" placeholder="admin@topbestglobal.com" value="<?= old('email'); ?>" required autofocus>
                </div>
            </div>

            <div class="form-group mb-4">
                <label class="font-weight-bold small text-muted mb-1">Mật Khẩu *</label>
                <div class="input-icon-wrap">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" value="<?= old('password'); ?>" required>
                </div>
            </div>

            <button type="submit" class="btn btn-tbg-submit btn-block">
                <i class="fa-solid fa-right-to-bracket mr-2"></i> Đăng Nhập Quản Trị
            </button>
        </form>

        <div class="text-center mt-4 pt-3 border-top">
            <a href="<?= langBaseUrl(); ?>" class="text-muted small font-weight-bold" style="text-decoration: none;">
                <i class="fa-solid fa-arrow-left mr-1"></i> Quay về Trang Chủ TOP BEST GLOBAL
            </a>
        </div>
    </div>
</div>

</body>
</html>