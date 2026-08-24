<?php ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-white mb-1"><i class="fa-solid fa-boxes-stacked text-info me-2"></i> Danh Sách Lô Hàng Lưu Kho</h3>
        <p class="text-muted small mb-0">Quản lý lô hàng theo dự án, theo dõi diện tích chiếm dụng và trạng thái xuất/nhập</p>
    </div>
    <div>
        <a href="<?= base_url('smartyard/inventory/import') ?>" class="btn btn-success">
            <i class="fa-solid fa-plus me-1"></i> Nhập Lô Mới
        </a>
    </div>
</div>

<!-- Filters -->
<div class="sy-card p-3 mb-4">
    <form method="GET" action="<?= base_url('smartyard/inventory/lots') ?>" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label text-light small fw-bold">Lọc Theo Kho</label>
            <select name="warehouse_id" class="form-select form-select-sm bg-dark text-light border-secondary">
                <option value="">-- Tất cả kho --</option>
                <?php foreach ($warehouses as $wh): ?>
                    <option value="<?= $wh->id ?>" <?= ($selectedWarehouseId == $wh->id) ? 'selected' : '' ?>><?= esc($wh->name) ?> (<?= esc($wh->code) ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label text-light small fw-bold">Lọc Theo Dự Án</label>
            <select name="project_id" class="form-select form-select-sm bg-dark text-light border-secondary">
                <option value="">-- Tất cả dự án --</option>
                <?php foreach ($projects as $prj): ?>
                    <option value="<?= $prj->id ?>" <?= ($selectedProjectId == $prj->id) ? 'selected' : '' ?>><?= esc($prj->project_name) ?> (<?= esc($prj->project_code) ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-info text-dark fw-bold w-50"><i class="fa-solid fa-filter me-1"></i> Lọc dữ liệu</button>
            <a href="<?= base_url('smartyard/inventory/lots') ?>" class="btn btn-sm btn-outline-secondary w-50"><i class="fa-solid fa-rotate-left me-1"></i> Xóa lọc</a>
        </div>
    </form>
</div>

<!-- Lots Table -->
<div class="sy-card p-4">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr class="text-muted small">
                    <th>Mã Lô Hàng</th>
                    <th>Tên Hàng Hóa / Vật Tư</th>
                    <th>Kho Lưu Trữ</th>
                    <th>Dự Án</th>
                    <th>Diện Tích Ban Đầu</th>
                    <th>Diện Tích Còn Lại</th>
                    <th>Trạng Thái</th>
                    <th>Ngày Nhập</th>
                    <th class="text-end">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($lots)): ?>
                    <tr><td colspan="9" class="text-center text-muted py-4">Không tìm thấy lô hàng nào phù hợp với điều kiện tìm kiếm.</td></tr>
                <?php else: ?>
                    <?php foreach ($lots as $lot): ?>
                        <tr>
                            <td><strong class="text-info"><?= esc($lot->lot_code) ?></strong></td>
                            <td><?= esc($lot->item_name) ?></td>
                            <td><?= esc($lot->warehouse_name ?? 'N/A') ?></td>
                            <td><span class="badge bg-secondary"><?= esc($lot->project_code ?? 'N/A') ?></span></td>
                            <td><?= number_format($lot->initial_area, 0) ?> m²</td>
                            <td><strong class="text-warning"><?= number_format($lot->remaining_area, 0) ?> m²</strong></td>
                            <td>
                                <?php if ($lot->status === 'STORED'): ?>
                                    <span class="badge bg-success">Đang lưu trữ</span>
                                <?php elseif ($lot->status === 'PARTIAL'): ?>
                                    <span class="badge bg-warning text-dark">Đã xuất 1 phần</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Đã xuất hết</span>
                                <?php endif; ?>
                            </td>
                            <td><small class="text-muted"><?= $lot->imported_at ?></small></td>
                            <td class="text-end">
                                <?php if ($lot->status !== 'EXPORTED'): ?>
                                    <a href="<?= base_url('smartyard/inventory/export') ?>?lot_id=<?= $lot->id ?>" class="btn btn-sm btn-outline-warning">
                                        <i class="fa-solid fa-arrow-right-from-bracket me-1"></i> Xuất
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">Đã tất toán</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?= view('smartyard/layout', ['content' => $content, 'title' => $title, 'currentUser' => $currentUser ?? null]) ?>
