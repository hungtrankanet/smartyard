<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Smart Yard Petro — Hệ Thống Quản Trị Kho') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sy-bg-primary: #0b1329;
            --sy-bg-card: #152238;
            --sy-bg-hover: #1c2e4a;
            --sy-text-main: #f8fafc;
            --sy-text-muted: #94a3b8;
            --sy-accent: #0284c7;
            --sy-accent-glow: rgba(2, 132, 199, 0.4);
            --sy-border: #243b55;
            --sy-green: #10b981;
            --sy-yellow: #f59e0b;
            --sy-orange: #f97316;
            --sy-red: #ef4444;
        }
        body {
            background-color: var(--sy-bg-primary);
            color: var(--sy-text-main);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            min-height: 100vh;
        }
        .navbar-smartyard {
            background: linear-gradient(135deg, #0d1b2a 0%, #1b263b 100%);
            border-bottom: 1px solid var(--sy-border);
            box-shadow: 0 4px 20px rgba(0,0,0,0.4);
        }
        .navbar-brand-petro {
            font-weight: 700;
            font-size: 1.25rem;
            color: #38bdf8 !important;
            letter-spacing: 0.5px;
        }
        .nav-link-sy {
            color: #cbd5e1 !important;
            font-weight: 500;
            padding: 0.5rem 0.9rem !important;
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        .nav-link-sy:hover, .nav-link-sy.active {
            color: #38bdf8 !important;
            background-color: rgba(56, 189, 248, 0.12);
        }
        .sy-card {
            background-color: var(--sy-bg-card);
            border: 1px solid var(--sy-border);
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.25);
        }
        .sy-badge-low { background-color: rgba(16, 185, 129, 0.2); color: var(--sy-green); border: 1px solid var(--sy-green); }
        .sy-badge-med { background-color: rgba(245, 158, 11, 0.2); color: var(--sy-yellow); border: 1px solid var(--sy-yellow); }
        .sy-badge-high { background-color: rgba(249, 115, 22, 0.2); color: var(--sy-orange); border: 1px solid var(--sy-orange); }
        .sy-badge-full { background-color: rgba(239, 68, 68, 0.2); color: var(--sy-red); border: 1px solid var(--sy-red); }
        .ai-fab {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0284c7 0%, #2563eb 100%);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 8px 25px var(--sy-accent-glow);
            cursor: pointer;
            z-index: 1050;
            transition: transform 0.2s ease;
        }
        .ai-fab:hover { transform: scale(1.08); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-smartyard sticky-top py-2">
    <div class="container-fluid px-4">
        <a class="navbar-brand navbar-brand-petro d-flex align-items-center gap-2" href="<?= base_url('smartyard/map') ?>">
            <i class="fa-solid fa-layer-group text-info"></i>
            <span>SMART YARD <span class="badge bg-primary text-uppercase fs-xs">PETRO</span></span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSmartYard">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSmartYard">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-1">
                <li class="nav-item">
                    <a class="nav-link nav-link-sy <?= (uri_string() == 'smartyard/map' || uri_string() == 'smartyard') ? 'active' : '' ?>" href="<?= base_url('smartyard/map') ?>">
                        <i class="fa-solid fa-map-location-dot me-1"></i> Sơ đồ 2D & 3D
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-sy <?= (strpos(uri_string(), 'smartyard/dashboard') !== false) ? 'active' : '' ?>" href="<?= base_url('smartyard/dashboard') ?>">
                        <i class="fa-solid fa-chart-pie me-1"></i> Dashboard Điều Hành
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link nav-link-sy dropdown-toggle <?= (strpos(uri_string(), 'smartyard/inventory') !== false) ? 'active' : '' ?>" href="#" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-boxes-stacked me-1"></i> Quản Lý Lô Hàng
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark bg-dark border-secondary">
                        <li><a class="dropdown-item" href="<?= base_url('smartyard/inventory/import') ?>"><i class="fa-solid fa-arrow-right-to-bracket text-success me-2"></i> Nhập lô hàng mới</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('smartyard/inventory/export') ?>"><i class="fa-solid fa-arrow-right-from-bracket text-danger me-2"></i> Xuất lô hàng</a></li>
                        <li><hr class="dropdown-divider border-secondary"></li>
                        <li><a class="dropdown-item" href="<?= base_url('smartyard/inventory/lots') ?>"><i class="fa-solid fa-list-check me-2"></i> Danh sách lô hiện hữu</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('smartyard/inventory/transactions') ?>"><i class="fa-solid fa-clock-rotate-left me-2"></i> Lịch sử giao dịch</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link nav-link-sy dropdown-toggle <?= (strpos(uri_string(), 'smartyard/admin') !== false) ? 'active' : '' ?>" href="#" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-sliders me-1"></i> Quản Trị Hệ Thống
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark bg-dark border-secondary">
                        <li><a class="dropdown-item" href="<?= base_url('smartyard/admin/settings') ?>"><i class="fa-solid fa-gear me-2"></i> Cấu hình & Ngưỡng màu</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('smartyard/admin/scopes') ?>"><i class="fa-solid fa-user-shield me-2"></i> Phân quyền Scope Kho</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('smartyard/admin/excel-import') ?>"><i class="fa-solid fa-file-excel me-2"></i> Nhập Excel hàng loạt</a></li>
                    </ul>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-3">
                <a href="<?= base_url('smartyard/kiosk') ?>" target="_blank" class="btn btn-sm btn-outline-info">
                    <i class="fa-solid fa-tv me-1"></i> Chế độ Sảnh (Kiosk)
                </a>
                <div class="text-end border-start border-secondary ps-3">
                    <div class="small fw-bold text-light"><?= esc($currentUser->username ?? 'Admin') ?></div>
                    <div class="badge bg-secondary" style="font-size: 10px;"><?= esc($currentUser->role ?? 'Quản trị viên') ?></div>
                </div>
            </div>
        </div>
    </div>
</nav>

<div class="container-fluid px-4 py-4">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show bg-success text-white border-0" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show bg-danger text-white border-0" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?= $content ?? '' ?>
</div>

<!-- Floating AI Assistant Widget -->
<div class="ai-fab" id="btnAiAssistant" title="AI Trợ Lý Smart Yard (Tuân thủ RBAC)">
    <i class="fa-solid fa-robot"></i>
</div>

<!-- AI Modal -->
<div class="modal fade" id="aiModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content sy-card border-info">
            <div class="modal-header border-secondary bg-dark">
                <h5 class="modal-title text-info d-flex align-items-center gap-2">
                    <i class="fa-solid fa-robot"></i> Smart Yard AI Assistant
                    <span class="badge bg-primary fs-xs">RBAC Scoped</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" style="background: #0f172a; max-height: 60vh; overflow-y: auto;" id="aiChatBox">
                <div class="p-3 mb-3 rounded bg-secondary bg-opacity-25 border border-secondary text-light">
                    <strong>Xin chào <?= esc($currentUser->username ?? 'User') ?>!</strong> Tôi là trợ lý AI Smart Yard. Bạn có thể hỏi về tình trạng diện tích kho, tìm kho phù hợp cho lô hàng, hoặc phát hiện kho sắp đầy. Dữ liệu trả lời hoàn toàn tuân thủ phạm vi phân quyền của bạn.
                </div>
            </div>
            <div class="modal-footer border-secondary bg-dark">
                <div class="input-group">
                    <input type="text" class="form-control bg-dark text-light border-secondary" id="aiQueryInput" placeholder="Ví dụ: Kho nào còn nhiều diện tích nhất? hoặc Lô hàng 200m2 để ở đâu?">
                    <button class="btn btn-info" id="btnSendAiQuery" type="button"><i class="fa-solid fa-paper-plane me-1"></i> Gửi</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('btnAiAssistant').addEventListener('click', function() {
    new bootstrap.Modal(document.getElementById('aiModal')).show();
});

document.getElementById('btnSendAiQuery').addEventListener('click', sendAiPrompt);
document.getElementById('aiQueryInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') sendAiPrompt();
});

function sendAiPrompt() {
    const input = document.getElementById('aiQueryInput');
    const query = input.value.trim();
    if (!query) return;

    const chatBox = document.getElementById('aiChatBox');
    chatBox.innerHTML += `<div class="p-2 mb-2 text-end"><span class="badge bg-info text-dark p-2 text-wrap text-start fs-6">${query}</span></div>`;
    input.value = '';

    fetch('<?= base_url('api/smartyard/ai-query') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'query=' + encodeURIComponent(query)
    })
    .then(res => res.json())
    .then(data => {
        let formatted = (data.response || '').replace(/\n/g, '<br>').replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        let bgClass = data.violation ? 'bg-danger bg-opacity-25 border-danger' : 'bg-primary bg-opacity-25 border-primary';
        chatBox.innerHTML += `<div class="p-3 mb-2 rounded ${bgClass} border text-light">${formatted}</div>`;
        chatBox.scrollTop = chatBox.scrollHeight;
    })
    .catch(err => {
        chatBox.innerHTML += `<div class="p-3 mb-2 rounded bg-danger bg-opacity-25 border border-danger text-light">Lỗi kết nối AI: ${err.message}</div>`;
    });
}
</script>
</body>
</html>
