<?php ob_start(); ?>

<div class="row g-3 mb-4 align-items-center">
    <div class="col-md-6">
        <h3 class="fw-bold text-white mb-1"><i class="fa-solid fa-map-location-dot text-info me-2"></i> Bản Đồ 2D & 3D Quản Lý Hệ Thống Kho</h3>
        <p class="text-muted small mb-0">Giám sát trực quan diện tích, trạng thái sử dụng đa khu vực và hình ảnh đại diện 3D</p>
    </div>
    <div class="col-md-6 text-md-end">
        <div class="d-inline-flex gap-2">
            <span class="badge sy-badge-low px-3 py-2"><i class="fa-solid fa-circle me-1"></i> Mức Thấp (0-30%)</span>
            <span class="badge sy-badge-med px-3 py-2"><i class="fa-solid fa-circle me-1"></i> Trung Bình (30-60%)</span>
            <span class="badge sy-badge-high px-3 py-2"><i class="fa-solid fa-circle me-1"></i> Mức Cao (60-80%)</span>
            <span class="badge sy-badge-full px-3 py-2"><i class="fa-solid fa-circle me-1"></i> Gần Đầy / Đầy (>80%)</span>
        </div>
    </div>
</div>

<!-- Region Selector Tabs -->
<ul class="nav nav-pills mb-3 gap-2" id="regionTabs" role="tablist">
    <?php foreach ($regions as $index => $reg): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $index === 0 ? 'active' : '' ?> btn-outline-info text-white px-4 py-2" id="tab-reg-<?= $reg['id'] ?>" data-bs-toggle="tab" data-bs-target="#content-reg-<?= $reg['id'] ?>" type="button">
                <i class="fa-solid fa-industry me-2 text-info"></i> <?= esc($reg['name']) ?>
                <span class="badge bg-secondary ms-2"><?= count($reg['warehouses']) ?> Kho</span>
            </button>
        </li>
    <?php endforeach; ?>
</ul>

<!-- Region Map Views -->
<div class="tab-content" id="regionTabsContent">
    <?php foreach ($regions as $index => $reg): ?>
        <div class="tab-pane fade <?= $index === 0 ? 'show active' : '' ?>" id="content-reg-<?= $reg['id'] ?>">
            <div class="sy-card p-4 position-relative" style="min-height: 520px; overflow: hidden; background: radial-gradient(circle at 50% 50%, #152238 0%, #0b1329 100%);">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="text-info fw-bold mb-0"><?= esc($reg['name']) ?> (<?= esc($reg['code']) ?>)</h5>
                        <small class="text-muted"><?= esc($reg['description']) ?></small>
                    </div>
                    <div class="text-muted small">
                        <i class="fa-solid fa-hand-pointer me-1"></i> Nhấp vào kho bất kỳ để xem ảnh 3D và danh sách lô hàng
                    </div>
                </div>

                <!-- 2D Interactive SVG / Canvas Map -->
                <div class="position-relative border border-secondary rounded p-3" style="min-height: 440px; background: rgba(0,0,0,0.25);">
                    <div class="d-flex flex-wrap gap-4 p-3">
                        <?php if (empty($reg['warehouses'])): ?>
                            <div class="text-center w-100 py-5 text-muted">
                                <i class="fa-solid fa-warehouse fa-3x mb-3 text-secondary"></i>
                                <p>Chưa có kho nào được cấu hình trong khu vực này hoặc bạn chưa được phân quyền scope.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($reg['warehouses'] as $wh): ?>
                                <div class="warehouse-box sy-card p-3 position-relative cursor-pointer" 
                                     style="width: 220px; border-left: 5px solid <?= $wh->status_color ?>; transition: transform 0.2s, box-shadow 0.2s; cursor: pointer;"
                                     onclick="openWarehouseDetail(<?= $wh->id ?>)"
                                     onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.5)'"
                                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                    
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="fw-bold text-white mb-0 text-truncate" title="<?= esc($wh->name) ?>"><?= esc($wh->name) ?></h6>
                                        <span class="badge" style="background: <?= $wh->status_color ?>; color: #fff; font-size: 11px;">
                                            <?= $wh->usage_rate ?>%
                                        </span>
                                    </div>
                                    <div class="text-muted small mb-2">Mã: <span class="text-info"><?= esc($wh->code) ?></span></div>

                                    <!-- Progress Bar -->
                                    <div class="progress mb-2" style="height: 8px; background: rgba(255,255,255,0.1);">
                                        <div class="progress-bar" role="progressbar" style="width: <?= min(100, $wh->usage_rate) ?>%; background-color: <?= $wh->status_color ?>;"></div>
                                    </div>

                                    <div class="d-flex justify-content-between small text-muted">
                                        <span>Đã dùng: <strong class="text-light"><?= number_format($wh->used_area, 0) ?>m²</strong></span>
                                        <span>Trống: <strong class="text-success"><?= number_format($wh->available_area, 0) ?>m²</strong></span>
                                    </div>

                                    <div class="mt-2 pt-2 border-top border-secondary text-end">
                                        <small class="text-info"><i class="fa-solid fa-cube me-1"></i> Xem 3D & Lô <i class="fa-solid fa-chevron-right ms-1"></i></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Modal Warehouse Detail with 3D Representative Image -->
<div class="modal fade" id="warehouseDetailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content sy-card border-info">
            <div class="modal-header border-secondary bg-dark">
                <h5 class="modal-title text-info fw-bold d-flex align-items-center gap-2" id="whModalTitle">
                    <i class="fa-solid fa-warehouse"></i> Chi Tiết Kho Hàng
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-dark" id="whModalBody">
                <div class="text-center py-4"><i class="fa-solid fa-spinner fa-spin fa-2x text-info"></i> Đang tải dữ liệu...</div>
            </div>
            <div class="modal-footer border-secondary bg-dark d-flex justify-content-between">
                <span class="text-muted small"><i class="fa-solid fa-shield-halved me-1"></i> Phân quyền Scope RBAC được áp dụng</span>
                <div id="whModalActions"></div>
            </div>
        </div>
    </div>
</div>

<script>
function openWarehouseDetail(warehouseId) {
    const modal = new bootstrap.Modal(document.getElementById('warehouseDetailModal'));
    const modalBody = document.getElementById('whModalBody');
    const modalTitle = document.getElementById('whModalTitle');
    const modalActions = document.getElementById('whModalActions');

    modalTitle.innerHTML = `<i class="fa-solid fa-warehouse"></i> Đang tải thông tin kho...`;
    modalBody.innerHTML = `<div class="text-center py-5"><i class="fa-solid fa-spinner fa-spin fa-2x text-info"></i></div>`;
    modal.show();

    fetch('<?= base_url('smartyard/warehouse') ?>/' + warehouseId)
        .then(res => res.json())
        .then(data => {
            if (!data.status) {
                modalBody.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                return;
            }
            const wh = data.warehouse;
            modalTitle.innerHTML = `<i class="fa-solid fa-warehouse text-info"></i> ${wh.name} (${wh.code})`;

            let lotsHtml = '';
            if (data.lots && data.lots.length > 0) {
                lotsHtml = `
                    <div class="table-responsive mt-3">
                        <table class="table table-dark table-sm table-hover align-middle mb-0">
                            <thead>
                                <tr class="text-muted">
                                    <th>Mã Lô</th>
                                    <th>Tên Hàng Hóa</th>
                                    <th>Dự Án</th>
                                    <th>Diện Tích (m²)</th>
                                    <th>Trạng Thái</th>
                                    <th>Ngày Nhập</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.lots.map(l => `
                                    <tr>
                                        <td><strong class="text-info">${l.lot_code}</strong></td>
                                        <td>${l.item_name}</td>
                                        <td><span class="badge bg-secondary">${l.project_code || 'N/A'}</span></td>
                                        <td><span class="text-warning">${Number(l.remaining_area).toLocaleString()} m²</span></td>
                                        <td><span class="badge bg-success">${l.status}</span></td>
                                        <td><small class="text-muted">${l.imported_at || ''}</small></td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
            } else {
                lotsHtml = `<div class="text-muted py-3 text-center"><i class="fa-solid fa-box-open me-2"></i>Hiện không có lô hàng nào lưu trong kho này.</div>`;
            }

            modalBody.innerHTML = `
                <div class="row g-4">
                    <!-- Left: 3D Representative Image -->
                    <div class="col-lg-5">
                        <div class="p-2 border border-secondary rounded bg-black position-relative text-center">
                            <div class="badge bg-primary position-absolute top-0 start-0 m-3"><i class="fa-solid fa-cube me-1"></i> 3D Representative View</div>
                            <div style="height: 240px; background: #111; display: flex; align-items: center; justify-content: center; border-radius: 8px; overflow: hidden;">
                                <img src="<?= base_url('assets/images/warehouse_3d_mockup.jpg') ?>" 
                                     onerror="this.src='https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=600&q=80'" 
                                     alt="3D Warehouse" 
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <small class="text-muted d-block mt-2"><i class="fa-solid fa-info-circle me-1"></i> Ảnh đại diện cố định (Phase 1: Không tracking realtime vật tư)</small>
                        </div>
                    </div>

                    <!-- Right: Area Stats & Info -->
                    <div class="col-lg-7">
                        <div class="row g-2 mb-3">
                            <div class="col-sm-6">
                                <div class="p-3 rounded bg-secondary bg-opacity-10 border border-secondary">
                                    <small class="text-muted">Tổng Diện Tích</small>
                                    <h5 class="text-white fw-bold mb-0">${Number(wh.total_area).toLocaleString()} m²</h5>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 rounded bg-secondary bg-opacity-10 border border-secondary">
                                    <small class="text-muted">Diện Tích Được Cấp</small>
                                    <h5 class="text-info fw-bold mb-0">${Number(wh.allocated_area).toLocaleString()} m²</h5>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 rounded bg-secondary bg-opacity-10 border border-secondary">
                                    <small class="text-muted">Đã Sử Dụng</small>
                                    <h5 class="text-warning fw-bold mb-0">${Number(wh.used_area).toLocaleString()} m² (${wh.usage_rate}%)</h5>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 rounded bg-secondary bg-opacity-10 border border-secondary">
                                    <small class="text-muted">Còn Khả Dụng</small>
                                    <h5 class="text-success fw-bold mb-0">${Number(wh.available_area).toLocaleString()} m²</h5>
                                </div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="small text-muted d-flex justify-content-between">
                                <span>Tỷ Lệ Lấp Đầy</span>
                                <span class="fw-bold" style="color: ${wh.status_color};">${wh.status_label}</span>
                            </label>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar" style="width: ${Math.min(100, wh.usage_rate)}%; background-color: ${wh.status_color};"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <h6 class="text-info fw-bold"><i class="fa-solid fa-boxes-stacked me-2"></i> Danh Sách Lô Hàng Đang Lưu Trữ</h6>
                    ${lotsHtml}
                </div>
            `;

            let actionBtns = '';
            if (data.permissions.can_import) {
                actionBtns += `<a href="<?= base_url('smartyard/inventory/import') ?>?warehouse_id=${wh.id}" class="btn btn-success"><i class="fa-solid fa-arrow-right-to-bracket me-1"></i> Nhập Lô Mới</a> `;
            }
            if (data.permissions.can_export && data.lots && data.lots.length > 0) {
                actionBtns += `<a href="<?= base_url('smartyard/inventory/export') ?>?warehouse_id=${wh.id}" class="btn btn-warning text-dark"><i class="fa-solid fa-arrow-right-from-bracket me-1"></i> Xuất Lô</a> `;
            }
            actionBtns += `<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>`;
            modalActions.innerHTML = actionBtns;
        })
        .catch(err => {
            modalBody.innerHTML = `<div class="alert alert-danger">Lỗi tải dữ liệu: ${err.message}</div>`;
        });
}
</script>

<?php $content = ob_get_clean(); ?>
<?= view('smartyard/layout', ['content' => $content, 'title' => $title, 'currentUser' => $currentUser]) ?>
