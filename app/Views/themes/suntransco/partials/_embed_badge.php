<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: transparent; }
        .badge-box {
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #0A192F 0%, #1A4C96 100%);
            border: 2px solid <?= ($profile['rank_tier'] === 'BEST') ? '#D9A441' : '#CBD5E1'; ?>;
            border-radius: 10px;
            padding: 10px 14px;
            color: #ffffff;
            text-decoration: none;
            box-sizing: border-box;
            height: 110px;
        }
        .badge-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #D9A441;
            color: #0A192F;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: bold;
            margin-right: 12px;
            flex-shrink: 0;
        }
        .badge-info { flex: 1; overflow: hidden; }
        .badge-tag { font-size: 10px; font-weight: 800; color: #F3E5AB; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-title { font-size: 12px; font-weight: 700; color: #ffffff; margin: 2px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .badge-sub { font-size: 9px; color: #94A3B8; }
    </style>
</head>
<body>
    <a href="<?= langBaseUrl('ho-so/' . $profile['code']); ?>" target="_blank" class="badge-box">
        <div class="badge-icon">
            <i class="fa-solid fa-trophy"></i>
        </div>
        <div class="badge-info">
            <div class="badge-tag">TOP BEST GLOBAL • <?= $profile['rank_tier']; ?> #<?= $profile['rank_number']; ?></div>
            <div class="badge-title"><?= esc($profile['name']); ?></div>
            <div class="badge-sub">Xác thực bởi VietKings & WORLDKINGS</div>
        </div>
    </a>
</body>
</html>
