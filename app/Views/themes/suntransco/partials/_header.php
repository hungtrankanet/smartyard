<!DOCTYPE html>
<html lang="<?= $activeLang->short_form ?? 'vi'; ?>">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title><?= isset($title) ? esc($title) . ' | ' : ''; ?>TOP BEST GLOBAL — VietKings × GAA × TOLUCK</title>
    <meta name="description" content="<?= !empty($description) ? esc(strip_tags($description)) : 'Cổng thông tin chính thức chương trình TOP BEST thuộc Hội Kỷ lục Việt Nam (VietKings) & GAA uỷ thác cho TOLUCK triển khai.'; ?>">
    <meta name="keywords" content="<?= !empty($keywords) ? esc($keywords) : 'top best global, vietkings, worldkings, gaa, toluck, xac minh huy hieu'; ?>">
    <meta name="robots" content="index, follow">

    <!-- Fonts: Serif for Titles, Sans-serif for Body -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,400;0,700;0,900;1,400;1,700&family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        :root {
            --tbg-navy-dark: #0A192F;
            --tbg-navy-primary: #1A4C96;
            --tbg-navy-light: #2A69AC;
            --tbg-gold-primary: #D9A441;
            --tbg-gold-dark: #B8860B;
            --tbg-gold-light: #F3E5AB;
            --tbg-ruby: #9B111E;
            --tbg-diamond: #D4AF37;
            --tbg-bg-gray: #F8FAFC;
        }
        body { font-family: 'Montserrat', sans-serif; background-color: #ffffff; color: #1E293B; line-height: 1.6; }
        h1, h2, h3, h4, .font-serif { font-family: 'Merriweather', serif; }
        
        /* Sticky Header */
        .tbg-header {
            background-color: var(--tbg-navy-dark);
            border-bottom: 2px solid var(--tbg-gold-primary);
            position: sticky;
            top: 0;
            z-index: 1050;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        .tbg-navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: nowrap;
        }
        .tbg-navbar .nav-link {
            color: #E2E8F0 !important;
            font-size: 0.84rem;
            font-weight: 600;
            padding: 8px 10px !important;
            white-space: nowrap;
            letter-spacing: 0.2px;
            transition: all 0.2s ease;
        }
        .tbg-navbar .nav-link:hover, .tbg-navbar .nav-link.active {
            color: var(--tbg-gold-light) !important;
        }
        .btn-tbg-cta {
            background: linear-gradient(135deg, #F3E5AB 0%, #D9A441 50%, #B8860B 100%);
            color: var(--tbg-navy-dark) !important;
            font-weight: 800;
            border-radius: 8px;
            padding: 7px 16px;
            font-size: 0.82rem;
            border: none;
            white-space: nowrap;
            box-shadow: 0 2px 8px rgba(217,164,65,0.3);
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .btn-tbg-verify {
            background: rgba(255,255,255,0.08);
            color: #ffffff !important;
            border: 1px solid var(--tbg-gold-primary);
            border-radius: 8px;
            padding: 6px 14px;
            font-size: 0.82rem;
            font-weight: 700;
            white-space: nowrap;
            margin-right: 8px;
        }
        .btn-tbg-verify:hover { background: rgba(217,164,65,0.2); }
        
        /* Badges & Card Styles */
        .badge-ruby { background-color: var(--tbg-ruby); color: #fff; font-weight: 700; }
        .badge-diamond { background-color: var(--tbg-diamond); color: #0A192F; font-weight: 800; }
        .card-best { border: 2px solid var(--tbg-gold-primary); box-shadow: 0 4px 15px rgba(217,164,65,0.15); }
        .card-top { border: 1.5px solid #CBD5E1; }
        
        /* Mobile Sticky Bottom Bar */
        .tbg-mobile-bottom-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1040;
            background: var(--tbg-navy-dark);
            border-top: 2px solid var(--tbg-gold-primary);
            padding: 10px 15px;
            display: none;
        }
        @media (max-width: 991px) {
            .tbg-mobile-bottom-bar { display: flex; gap: 10px; }
            body { padding-bottom: 70px; }
        }
    </style>
</head>
<body>

<!-- Header Sticky -->
<header class="tbg-header">
    <div class="container-fluid px-lg-4">
        <nav class="navbar navbar-expand-lg navbar-dark tbg-navbar py-2 px-0">
            <!-- Dynamic Logo -->
            <a class="navbar-brand d-flex align-items-center mr-lg-3 mr-auto" href="<?= langBaseUrl(); ?>" style="white-space: nowrap;">
                <?php if (!empty($generalSettings->logo) && file_exists(FCPATH . $generalSettings->logo)): ?>
                    <img src="<?= getLogo(); ?>" alt="<?= esc($baseSettings->application_name ?? 'TOP BEST GLOBAL'); ?>" style="max-height: 38px; width: auto;" class="mr-2">
                <?php else: ?>
                    <div class="mr-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; border-radius: 8px; background: linear-gradient(135deg, #D9A441, #B8860B); color: #0A192F; font-weight: 900; font-size: 16px;">
                        <i class="fa-solid fa-trophy"></i>
                    </div>
                <?php endif; ?>
                <div class="d-inline-block">
                    <div style="color: #ffffff; font-weight: 900; font-size: 1.05rem; line-height: 1.1; letter-spacing: 0.5px;">
                        <?= esc($baseSettings->application_name ?? 'TOP BEST GLOBAL'); ?>
                    </div>
                    <div style="color: var(--tbg-gold-light); font-size: 0.65rem; font-weight: 700; letter-spacing: 0.7px;">VIETKINGS × GAA × TOLUCK</div>
                </div>
            </a>

            <button class="navbar-toggler border-0 ml-2" type="button" data-toggle="collapse" data-target="#tbgNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu On Single Straight Row -->
            <div class="collapse navbar-collapse" id="tbgNav">
                <ul class="navbar-nav mx-auto d-flex align-items-center flex-row flex-wrap flex-lg-nowrap justify-content-center" style="gap: 2px;">
                    <li class="nav-item"><a class="nav-link" href="<?= langBaseUrl(); ?>"><i class="fa-regular fa-newspaper mr-1 text-warning"></i> Tin Tức</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= langBaseUrl('top-best-la-gi'); ?>"><i class="fa-solid fa-circle-info mr-1 text-warning"></i> TOP BEST Là Gì</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= langBaseUrl('bang-xep-hang'); ?>"><i class="fa-solid fa-ranking-star mr-1 text-warning"></i> Bảng Xếp Hạng</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= langBaseUrl('doanh-nghiep/dang-ky'); ?>"><i class="fa-solid fa-building mr-1 text-warning"></i> Doanh Nghiệp</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= langBaseUrl('dai-ly'); ?>"><i class="fa-solid fa-handshake mr-1 text-warning"></i> Đại Lý</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= langBaseUrl('su-kien'); ?>"><i class="fa-solid fa-calendar-check mr-1 text-warning"></i> Sự Kiện</a></li>
                </ul>

                <!-- Right Actions -->
                <div class="d-flex align-items-center flex-nowrap ml-lg-2">
                    <a href="<?= langBaseUrl('verify'); ?>" class="btn btn-tbg-verify d-none d-xl-inline-flex align-items-center">
                        <i class="fa-solid fa-shield-halved mr-1 text-warning"></i> Tra Cứu Huy Hiệu
                    </a>
                    <a href="<?= langBaseUrl('doanh-nghiep/dang-ky'); ?>" class="btn btn-tbg-cta">
                        <i class="fa-solid fa-file-signature mr-1"></i> Đăng Ký Ngay
                    </a>
                </div>
            </div>
        </nav>
</header>

<!-- Mobile Sticky Bottom Bar -->
<div class="tbg-mobile-bottom-bar">
    <a href="<?= langBaseUrl('verify'); ?>" class="btn btn-tbg-verify btn-sm flex-fill text-center m-0">
        <i class="fa-solid fa-shield-halved text-warning"></i> Tra Cứu Huy Hiệu
    </a>
    <a href="<?= langBaseUrl('doanh-nghiep/dang-ky'); ?>" class="btn btn-tbg-cta btn-sm flex-fill text-center">
        <i class="fa-solid fa-file-signature"></i> Đăng Ký Ngay
    </a>
</div>
