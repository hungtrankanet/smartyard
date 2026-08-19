<!DOCTYPE html>
<html lang="<?= $activeLang->short_form ?? 'vi'; ?>">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Primary Meta SEO Tags -->
    <title><?= isset($title) ? esc($title) . ' | ' : ''; ?><?= !empty($baseSettings->site_title) ? esc($baseSettings->site_title) : 'TOP BEST GLOBAL Corporation'; ?></title>
    <meta name="description" content="<?= !empty($description) ? esc(strip_tags($description)) : (!empty($baseSettings->site_description) ? esc($baseSettings->site_description) : 'Nền tảng logistics số hóa & vận tải đa phương thức hàng đầu - Kết nối 500+ doanh nghiệp sản xuất, xuất nhập khẩu toàn cầu.'); ?>">
    <meta name="keywords" content="<?= !empty($keywords) ? esc($keywords) : (!empty($baseSettings->keywords) ? esc($baseSettings->keywords) : 'vận tải quốc tế, logistics toàn cầu, cước vận tải biển, vận tải hàng không, khai báo hải quan, fcl, lcl, xuất nhập khẩu, top best global, topbestglobal, b2b logistics, freight forwarding'); ?>">
    <meta name="author" content="TOP BEST GLOBAL Corporation">
    <meta name="copyright" content="TOP BEST GLOBAL Corporation">
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">

    <!-- Canonical & Multilingual Alternate Hreflang -->
    <link rel="canonical" href="<?= esc(currentFullURL()); ?>">
    <link rel="alternate" hreflang="vi" href="<?= esc(base_url(uri_string())); ?>">
    <link rel="alternate" hreflang="en" href="<?= esc(base_url('en/' . ltrim(uri_string(), 'en/'))); ?>">
    <link rel="alternate" hreflang="x-default" href="<?= esc(base_url()); ?>">

    <!-- OpenGraph / Facebook / Zalo / LinkedIn -->
    <meta property="og:locale" content="<?= ($activeLang->short_form ?? 'vi') == 'en' ? 'en_US' : 'vi_VN'; ?>">
    <meta property="og:site_name" content="<?= !empty($baseSettings->application_name) ? esc($baseSettings->application_name) : 'TOP BEST GLOBAL Corporation'; ?>">
    <meta property="og:type" content="<?= !empty($ogType) ? esc($ogType) : 'website'; ?>">
    <meta property="og:title" content="<?= isset($title) ? esc($title) : 'TOP BEST GLOBAL - Nền Tảng Vận Tải & Kết Nối Doanh Nghiệp Toàn Cầu'; ?>">
    <meta property="og:description" content="<?= !empty($description) ? esc(strip_tags($description)) : 'Nền tảng logistics số hóa & vận tải đa phương thức hàng đầu - Kết nối 500+ doanh nghiệp sản xuất, xuất nhập khẩu toàn cầu.'; ?>">
    <meta property="og:url" content="<?= esc(currentFullURL()); ?>">
    <meta property="og:image" content="<?= !empty($ogImage) ? esc($ogImage) : base_url('assets/themes/suntransco/logo.png'); ?>">
    <meta property="og:image:width" content="<?= !empty($ogWidth) ? $ogWidth : '1200'; ?>">
    <meta property="og:image:height" content="<?= !empty($ogHeight) ? $ogHeight : '630'; ?>">
    <?php if (isset($postType)): ?>
        <meta property="article:published_time" content="<?= !empty($ogPublishedTime) ? esc($ogPublishedTime) : date('c'); ?>">
        <meta property="article:modified_time" content="<?= !empty($ogModifiedTime) ? esc($ogModifiedTime) : date('c'); ?>">
        <meta property="article:author" content="<?= !empty($ogAuthor) ? esc($ogAuthor) : 'TOP BEST GLOBAL Logistics'; ?>">
        <?php if (!empty($ogTags) && is_array($ogTags)): foreach ($ogTags as $tag): ?>
            <meta property="article:tag" content="<?= esc($tag->tag ?? ''); ?>">
        <?php endforeach; endif; ?>
    <?php endif; ?>

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= isset($title) ? esc($title) : 'TOP BEST GLOBAL Corporation'; ?>">
    <meta name="twitter:description" content="<?= !empty($description) ? esc(strip_tags($description)) : 'Nền tảng logistics số hóa & kết nối doanh nghiệp toàn cầu'; ?>">
    <meta name="twitter:image" content="<?= !empty($ogImage) ? esc($ogImage) : base_url('assets/themes/suntransco/logo.png'); ?>">

    <!-- Favicon & Icons -->
    <link rel="shortcut icon" type="image/png" href="<?= base_url('assets/themes/suntransco/logo.png'); ?>">
    <link rel="apple-touch-icon" href="<?= base_url('assets/themes/suntransco/logo.png'); ?>">

    <!-- Google Structured Data (JSON-LD) -->
    <?= view('common/_json_ld'); ?>

    <!-- Google Fonts: Montserrat -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/themes/suntransco/style.css?v=2.9'); ?>">
</head>
<body class="corporate-light lang-<?= $activeLang->short_form ?? 'vi'; ?>">
    <!-- Header -->
    <header class="main-header">
        <div class="container header-container">
            <a href="<?= langBaseUrl(); ?>" class="logo" style="display:flex;align-items:center;gap:10px;text-decoration:none;">
                <img src="<?= base_url('assets/themes/suntransco/logo.png'); ?>" alt="TOP BEST GLOBAL Logo" style="height:40px;width:auto;">
                <span class="logo-sun" style="font-weight:900;letter-spacing:0.5px;color:#1e3a8a;">TOP BEST</span> <span class="logo-trans" style="font-weight:900;letter-spacing:0.5px;color: var(--primary,#2563eb);">GLOBAL</span>
            </a>
            <nav class="nav-menu" id="navMenu">
                <a href="<?= langBaseUrl(); ?>" class="<?= uri_string() == '' ? 'active' : ''; ?>">
                    <span class="lang-vi">Trang chủ</span>
                    <span class="lang-en">Home</span>
                </a>
                <a href="<?= langBaseUrl('about'); ?>" class="<?= uri_string() == 'about' ? 'active' : ''; ?>">
                    <span class="lang-vi">Giới thiệu</span>
                    <span class="lang-en">About Us</span>
                </a>
                <div class="nav-dropdown-item">
                    <a href="<?= langBaseUrl('services'); ?>" class="<?= uri_string() == 'services' ? 'active' : ''; ?>">
                        <span class="lang-vi">Dịch vụ <i class="fa-solid fa-chevron-down" style="font-size:0.7rem;margin-left:4px;"></i></span>
                        <span class="lang-en">Services <i class="fa-solid fa-chevron-down" style="font-size:0.7rem;margin-left:4px;"></i></span>
                    </a>
                    <div class="dropdown-content">
                        <a href="<?= langBaseUrl('services'); ?>?tab=air-freight">
                            <span class="lang-vi">Vận chuyển đường không</span>
                            <span class="lang-en">Air Freight</span>
                        </a>
                        <a href="<?= langBaseUrl('services'); ?>?tab=sea-freight">
                            <span class="lang-vi">Vận chuyển đường biển</span>
                            <span class="lang-en">Sea Freight</span>
                        </a>
                        <a href="<?= langBaseUrl('services'); ?>?tab=inland-freight">
                            <span class="lang-vi">Vận chuyển nội địa</span>
                            <span class="lang-en">Inland Freight</span>
                        </a>
                        <a href="<?= langBaseUrl('services'); ?>?tab=warehousing">
                            <span class="lang-vi">Dịch vụ kho bãi</span>
                            <span class="lang-en">Warehousing</span>
                        </a>
                        <a href="<?= langBaseUrl('services'); ?>?tab=customs-clearance">
                            <span class="lang-vi">Khai báo hải quan</span>
                            <span class="lang-en">Customs Clearance</span>
                        </a>
                    </div>
                </div>
                <!-- Đối tác Doanh nghiệp Dropdown (Simplified to 2 items) -->
                <div class="nav-dropdown-item">
                    <a href="<?= langBaseUrl('partners'); ?>" class="<?= (strpos(uri_string(), 'partner') !== false || strpos(uri_string(), 'member') !== false) ? 'active' : ''; ?>">
                        <span class="lang-vi">Đối tác <i class="fa-solid fa-chevron-down" style="font-size:0.7rem;margin-left:4px;"></i></span>
                        <span class="lang-en">Partners <i class="fa-solid fa-chevron-down" style="font-size:0.7rem;margin-left:4px;"></i></span>
                    </a>
                    <div class="dropdown-content">
                        <a href="<?= langBaseUrl('partners'); ?>">
                            <span class="lang-vi">Danh bạ Đối tác</span>
                            <span class="lang-en">Partner Directory</span>
                        </a>
                        <a href="<?= langBaseUrl('partner/register'); ?>">
                            <span class="lang-vi">Đăng ký Trở thành Đối tác</span>
                            <span class="lang-en">Become a Partner</span>
                        </a>
                    </div>
                </div>
                <div class="nav-dropdown-item">
                    <a href="<?= langBaseUrl('posts'); ?>" class="<?= (uri_string() == 'posts' || uri_string() == 'events') ? 'active' : ''; ?>">
                        <span class="lang-vi">Tin tức <i class="fa-solid fa-chevron-down" style="font-size:0.7rem;margin-left:4px;"></i></span>
                        <span class="lang-en">News <i class="fa-solid fa-chevron-down" style="font-size:0.7rem;margin-left:4px;"></i></span>
                    </a>
                    <div class="dropdown-content">
                        <a href="<?= langBaseUrl('posts'); ?>">
                            <span class="lang-vi">Tin tức</span>
                            <span class="lang-en">News</span>
                        </a>
                        <a href="<?= langBaseUrl('events'); ?>">
                            <span class="lang-vi">Sự kiện</span>
                            <span class="lang-en">Events</span>
                        </a>
                    </div>
                </div>
                <a href="<?= langBaseUrl('contact'); ?>" class="<?= uri_string() == 'contact' ? 'active' : ''; ?>">
                    <span class="lang-vi">Liên hệ</span>
                    <span class="lang-en">Contact</span>
                </a>
                <!-- Dynamic CMS Back-End Menu Links -->
                <?php if (!empty($menuLinks)): ?>
                    <?php foreach ($menuLinks as $menuItem): ?>
                        <?php 
                        if ($menuItem->item_parent_id != 0 || (isset($menuItem->item_visibility) && $menuItem->item_visibility != 1)) continue;
                        $coreSlugs = ['home', 'about', 'services', 'members', 'partners', 'partner', 'posts', 'events', 'contact', 'news', 'trang-chu', 'gioi-thieu', 'dich-vu', 'doi-tac', 'hoi-vien', 'tin-tuc', 'su-kien', 'lien-he'];
                        if (in_array(strtolower($menuItem->item_slug), $coreSlugs)) continue;
                        $subLinks = getSubMenuLinks($menuLinks, $menuItem->item_id, $menuItem->item_type);
                        $itemUrl = generateMenuItemURL($menuItem, $categories ?? []);
                        ?>
                        <?php if (!empty($subLinks)): ?>
                            <div class="nav-dropdown-item">
                                <a href="<?= $itemUrl; ?>">
                                    <?= esc($menuItem->item_name); ?> <i class="fa-solid fa-chevron-down" style="font-size:0.7rem;margin-left:4px;"></i>
                                </a>
                                <div class="dropdown-content">
                                    <?php foreach ($subLinks as $subItem): ?>
                                        <?php if (!isset($subItem->item_visibility) || $subItem->item_visibility == 1): ?>
                                            <a href="<?= generateMenuItemURL($subItem, $categories ?? []); ?>">
                                                <?= esc($subItem->item_name); ?>
                                            </a>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <a href="<?= $itemUrl; ?>">
                                <?= esc($menuItem->item_name); ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                <div class="lang-switcher" style="display:flex; align-items:center; background: rgba(0,0,0,0.05); padding: 4px 6px; border-radius: 30px; border: 1px solid rgba(0,0,0,0.1); gap: 4px;">
                    <a href="<?= base_url('switch-lang/vi'); ?>" class="lang-btn <?= ($activeLang->short_form ?? 'vi') == 'vi' ? 'active' : ''; ?>" id="lang-vi" style="display:flex; align-items:center; gap:6px; padding: 6px 12px; border-radius: 20px; font-weight: 800; font-size: 0.82rem; text-decoration:none; transition: all 0.2s;">
                        <span style="font-size: 1.15rem;">🇻🇳</span> VI
                    </a>
                    <a href="<?= base_url('switch-lang/en'); ?>" class="lang-btn <?= ($activeLang->short_form ?? 'vi') == 'en' ? 'active' : ''; ?>" id="lang-en" style="display:flex; align-items:center; gap:6px; padding: 6px 12px; border-radius: 20px; font-weight: 800; font-size: 0.82rem; text-decoration:none; transition: all 0.2s;">
                        <span style="font-size: 1.15rem;">🇬🇧</span> EN
                    </a>
                </div>
                <div class="auth-buttons" style="display:flex; gap:8px;">
                    <?php if (authCheck()): ?>
                        <?php if (!empty(user()->member_id) || !isAdmin()): ?>
                            <a href="<?= langBaseUrl('partner/dashboard'); ?>" class="btn btn-primary btn-sm">
                                <i class="fa-solid fa-building-user"></i>
                                <span class="lang-vi">Cổng Đối Tác</span>
                                <span class="lang-en">Partner Portal</span>
                            </a>
                        <?php else: ?>
                            <a href="<?= adminUrl(); ?>" class="btn btn-primary btn-sm"><i class="fa-solid fa-gauge"></i> Admin</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <button class="btn btn-primary btn-sm" onclick="openModal('authModal')">
                            <i class="fa-solid fa-building-shield" style="margin-right:4px;"></i>
                            <span class="lang-vi">Cổng Đối Tác</span>
                            <span class="lang-en">Partner Portal</span>
                        </button>
                    <?php endif; ?>
                </div>
            </nav>
            <div class="menu-toggle" id="menuToggle">
                <i class="fa-solid fa-bars"></i>
            </div>
        </div>
    </header>
