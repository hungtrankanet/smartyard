<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Màn Hình Sảnh Điều Hành Thông Minh (Kiosk) | Smart Yard Petro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #060b19;
            color: #f8fafc;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            overflow-x: hidden;
        }
        .kiosk-header {
            background: linear-gradient(90deg, #0f172a 0%, #1e293b 100%);
            border-bottom: 2px solid #0284c7;
        }
        .sy-card {
            background-color: #0f172a;
            border: 1px solid #1e293b;
            border-radius: 12px;
        }
        .sy-badge-low { background-color: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid #10b981; }
        .sy-badge-med { background-color: rgba(245, 158, 11, 0.2); color: #f59e0b; border: 1px solid #f59e0b; }
        .sy-badge-high { background-color: rgba(249, 115, 22, 0.2); color: #f97316; border: 1px solid #f97316; }
        .sy-badge-full { background-color: rgba(239, 68, 68, 0.2); color: #ef4444; border: 1px solid #ef4444; }
    </style>
</head>
<body class="p-3">

<div class="kiosk-header p-3 rounded-3 mb-3 d-flex justify-content-between align-items-center shadow">
    <div class="d-flex align-items-center gap-3">
        <i class="fa-solid fa-layer-group text-info fa-2x"></i>
        <div>
            <h4 class="fw-bold mb-0 text-info">SMART YARD PETRO — MÀN HÌNH SẢNH ĐIỀU HÀNH KHO</h4>
            <small class="text-muted">Trung tâm giám sát trực quan & Trạng thái tải diện tích thời gian thực</small>
        </div>
    </div>
    <div class="d-flex align-items-center gap-4">
        <div class="text-end">
            <span class="badge bg-danger pulse me-2"><i class="fa-solid fa-circle text-white me-1"></i> LIVE MONITOR</span>
            <span id="kioskClock" class="fw-bold text-white fs-5">--:--:--</span>
        </div>
        <button onclick="document.documentElement.requestFullscreen()" class="btn btn-sm btn-outline-info"><i class="fa-solid fa-expand"></i></button>
    </div>
</div>

<!-- KPI Cards Summary -->
<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="sy-card p-3 border-start border-4 border-info">
            <small class="text-muted text-uppercase fw-bold">Tổng Diện Tích Cấp</small>
            <h3 class="fw-bold text-white mb-0 mt-1"><?= number_format($metrics['summary']['allocated_area'], 0) ?> m²</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="sy-card p-3 border-start border-4 border-warning">
            <small class="text-muted text-uppercase fw-bold">Đã Sử Dụng (Lấp Đầy)</small>
            <h3 class="fw-bold text-warning mb-0 mt-1"><?= number_format($metrics['summary']['used_area'], 0) ?> m² (<?= $metrics['summary']['overall_usage_rate'] ?>%)</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="sy-card p-3 border-start border-4 border-success">
            <small class="text-muted text-uppercase fw-bold">Diện Tích Khả Dụng Còn Lại</small>
            <h3 class="fw-bold text-success mb-0 mt-1"><?= number_format($metrics['summary']['available_area'], 0) ?> m²</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="sy-card p-3 border-start border-4 border-primary">
            <small class="text-muted text-uppercase fw-bold">Tổng Số Kho Hoạt Động</small>
            <h3 class="fw-bold text-info mb-0 mt-1"><?= $metrics['summary']['total_warehouses'] ?> Kho / <?= $metrics['summary']['total_regions'] ?> Khu Vực</h3>
        </div>
    </div>
</div>

<!-- Multi Region 2D Maps Grid -->
<div class="row g-3">
    <?php foreach ($regions as $reg): ?>
        <div class="col-lg-4">
            <div class="sy-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary">
                    <h6 class="fw-bold text-info mb-0"><i class="fa-solid fa-industry me-1"></i> <?= esc($reg['name']) ?></h6>
                    <span class="badge bg-secondary"><?= count($reg['warehouses']) ?> Kho</span>
                </div>
                <div class="d-flex flex-column gap-2 mt-2">
                    <?php foreach ($reg['warehouses'] as $wh): ?>
                        <div class="p-2 rounded bg-dark border" style="border-left: 5px solid <?= $wh->status_color ?> !important;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold text-white small"><?= esc($wh->name) ?> (<?= esc($wh->code) ?>)</span>
                                <span class="badge" style="background: <?= $wh->status_color ?>;"><?= $wh->usage_rate ?>%</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar" style="width: <?= min(100, $wh->usage_rate) ?>%; background: <?= $wh->status_color ?>;"></div>
                            </div>
                            <div class="d-flex justify-content-between small text-muted mt-1" style="font-size: 11px;">
                                <span>Dùng: <?= number_format($wh->used_area, 0) ?>m²</span>
                                <span>Trống: <strong class="text-success"><?= number_format($wh->available_area, 0) ?>m²</strong></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
function updateClock() {
    const now = new Date();
    document.getElementById('kioskClock').innerText = now.toLocaleTimeString('vi-VN') + ' - ' + now.toLocaleDateString('vi-VN');
}
setInterval(updateClock, 1000);
updateClock();

// Auto refresh kiosk page every 30 seconds
setTimeout(function() {
    location.reload();
}, 30000);
</script>
</body>
</html>
