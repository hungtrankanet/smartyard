<?php ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-white mb-1"><i class="fa-solid fa-chart-pie text-info me-2"></i> Dashboard Điều Hành Kho & Diện Tích</h3>
        <p class="text-muted small mb-0">Chỉ số tổng hợp theo thời gian thực dành cho Cấp Quản lý & Ban Lãnh đạo</p>
    </div>
    <div>
        <a href="<?= base_url('smartyard/kiosk') ?>" target="_blank" class="btn btn-outline-info">
            <i class="fa-solid fa-expand me-1"></i> Chế độ Toàn màn hình Sảnh
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="sy-card p-3 border-start border-4 border-info">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted text-uppercase fw-bold">Tổng Diện Tích Quản Lý</small>
                    <h3 class="fw-bold text-white mb-0 mt-1"><?= number_format($metrics['summary']['total_area'], 0) ?> <span class="fs-6 text-muted">m²</span></h3>
                </div>
                <div class="p-3 bg-info bg-opacity-10 text-info rounded-circle"><i class="fa-solid fa-layer-group fa-2x"></i></div>
            </div>
            <small class="text-muted mt-2 d-block"><?= $metrics['summary']['total_regions'] ?> Khu vực &bull; <?= $metrics['summary']['total_warehouses'] ?> Kho hoạt động</small>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="sy-card p-3 border-start border-4 border-warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted text-uppercase fw-bold">Diện Tích Đã Sử Dụng</small>
                    <h3 class="fw-bold text-warning mb-0 mt-1"><?= number_format($metrics['summary']['used_area'], 0) ?> <span class="fs-6 text-muted">m²</span></h3>
                </div>
                <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-circle"><i class="fa-solid fa-boxes-stacked fa-2x"></i></div>
            </div>
            <small class="text-warning mt-2 d-block"><i class="fa-solid fa-chart-line me-1"></i> Tỷ lệ lấp đầy: <strong><?= $metrics['summary']['overall_usage_rate'] ?>%</strong></small>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="sy-card p-3 border-start border-4 border-success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted text-uppercase fw-bold">Diện Tích Còn Khả Dụng</small>
                    <h3 class="fw-bold text-success mb-0 mt-1"><?= number_format($metrics['summary']['available_area'], 0) ?> <span class="fs-6 text-muted">m²</span></h3>
                </div>
                <div class="p-3 bg-success bg-opacity-10 text-success rounded-circle"><i class="fa-solid fa-circle-check fa-2x"></i></div>
            </div>
            <small class="text-success mt-2 d-block">Sẵn sàng tiếp nhận thêm lô hàng</small>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="sy-card p-3 border-start border-4 border-primary">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted text-uppercase fw-bold">Số Dự Án Đang Lưu Kho</small>
                    <h3 class="fw-bold text-info mb-0 mt-1"><?= count($metrics['projects']) ?> <span class="fs-6 text-muted">Dự án</span></h3>
                </div>
                <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-circle"><i class="fa-solid fa-diagram-project fa-2x"></i></div>
            </div>
            <small class="text-muted mt-2 d-block">Phân bổ đa khu vực</small>
        </div>
    </div>
</div>

<!-- Status Distribution & Region Breakdown -->
<div class="row g-4 mb-4">
    <!-- Status Distribution -->
    <div class="col-lg-4">
        <div class="sy-card p-4 h-100">
            <h5 class="text-white fw-bold mb-3"><i class="fa-solid fa-gauge-high text-info me-2"></i> Phân Bổ Trạng Thái Kho</h5>
            <div class="d-flex flex-column gap-3">
                <div class="p-3 rounded bg-dark border border-secondary d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge sy-badge-low"><i class="fa-solid fa-circle"></i> Mức thấp (0-30%)</span>
                    </div>
                    <span class="fw-bold fs-5 text-success"><?= $metrics['status_distribution']['LOW'] ?? 0 ?> kho</span>
                </div>
                <div class="p-3 rounded bg-dark border border-secondary d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge sy-badge-med"><i class="fa-solid fa-circle"></i> Trung bình (30-60%)</span>
                    </div>
                    <span class="fw-bold fs-5 text-warning"><?= $metrics['status_distribution']['MEDIUM'] ?? 0 ?> kho</span>
                </div>
                <div class="p-3 rounded bg-dark border border-secondary d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge sy-badge-high"><i class="fa-solid fa-circle"></i> Mức cao (60-80%)</span>
                    </div>
                    <span class="fw-bold fs-5 text-warning"><?= $metrics['status_distribution']['HIGH'] ?? 0 ?> kho</span>
                </div>
                <div class="p-3 rounded bg-dark border border-secondary d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge sy-badge-full"><i class="fa-solid fa-circle"></i> Gần đầy / Đầy (>80%)</span>
                    </div>
                    <span class="fw-bold fs-5 text-danger"><?= $metrics['status_distribution']['FULL'] ?? 0 ?> kho</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Project Allocations -->
    <div class="col-lg-8">
        <div class="sy-card p-4 h-100">
            <h5 class="text-white fw-bold mb-3"><i class="fa-solid fa-folder-tree text-info me-2"></i> Diện Tích Chiếm Dụng Theo Dự Án</h5>
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr class="text-muted small">
                            <th>Mã Dự Án</th>
                            <th>Tên Dự Án</th>
                            <th>Số Lô Hàng</th>
                            <th>Diện Tích Chiếm Dụng</th>
                            <th>Trạng Thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($metrics['projects'])): ?>
                            <tr><td colspan="5" class="text-center text-muted py-3">Chưa có dự án nào có hàng lưu kho.</td></tr>
                        <?php else: ?>
                            <?php foreach ($metrics['projects'] as $p): ?>
                                <tr>
                                    <td><strong class="text-info"><?= esc($p->project_code) ?></strong></td>
                                    <td><?= esc($p->project_name) ?></td>
                                    <td><span class="badge bg-secondary"><?= $p->total_lots ?> lô</span></td>
                                    <td><strong class="text-warning"><?= number_format($p->total_occupied_area, 0) ?> m²</strong></td>
                                    <td><span class="badge bg-success"><?= esc($p->status) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="sy-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="text-white fw-bold mb-0"><i class="fa-solid fa-clock-rotate-left text-info me-2"></i> Giao Dịch Nhập / Xuất Kho Gần Nhất</h5>
        <a href="<?= base_url('smartyard/inventory/transactions') ?>" class="btn btn-sm btn-outline-info">Xem Tất Cả Giao Dịch <i class="fa-solid fa-arrow-right ms-1"></i></a>
    </div>
    <div class="table-responsive">
        <table class="table table-dark table-sm table-hover align-middle mb-0">
            <thead>
                <tr class="text-muted small">
                    <th>Loại</th>
                    <th>Mã Lô</th>
                    <th>Kho</th>
                    <th>Dự Án</th>
                    <th>Diện Tích Thay Đổi</th>
                    <th>Người Thực Hiện</th>
                    <th>Thời Gian</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($metrics['recent_transactions'])): ?>
                    <tr><td colspan="7" class="text-center text-muted py-3">Chưa có giao dịch nhập/xuất nào được ghi nhận.</td></tr>
                <?php else: ?>
                    <?php foreach ($metrics['recent_transactions'] as $tx): ?>
                        <tr>
                            <td>
                                <?php if ($tx->transaction_type === 'IMPORT'): ?>
                                    <span class="badge bg-success"><i class="fa-solid fa-arrow-down me-1"></i> Nhập kho</span>
                                <?php elseif ($tx->transaction_type === 'EXPORT'): ?>
                                    <span class="badge bg-danger"><i class="fa-solid fa-arrow-up me-1"></i> Xuất kho</span>
                                <?php else: ?>
                                    <span class="badge bg-warning"><?= $tx->transaction_type ?></span>
                                <?php endif; ?>
                            </td>
                            <td><strong class="text-info"><?= esc($tx->lot_code ?? 'N/A') ?></strong></td>
                            <td><?= esc($tx->warehouse_name ?? 'N/A') ?></td>
                            <td><span class="badge bg-secondary"><?= esc($tx->project_code ?? 'N/A') ?></span></td>
                            <td><span class="text-warning fw-bold"><?= number_format($tx->area, 0) ?> m²</span></td>
                            <td><?= esc($tx->user_name ?? 'Hệ thống') ?></td>
                            <td><small class="text-muted"><?= $tx->created_at ?></small></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?= view('smartyard/layout', ['content' => $content, 'title' => $title, 'currentUser' => $currentUser]) ?>
