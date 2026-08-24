<?php ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-white mb-1"><i class="fa-solid fa-user-shield text-info me-2"></i> Phân Quyền Phạm Vi Kho (Warehouse Scopes)</h3>
        <p class="text-muted small mb-0">Cấp quyền Trưởng kho và Nhân viên nhập liệu theo từng kho cụ thể (Rule 01)</p>
    </div>
    <div>
        <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modalAddScope">
            <i class="fa-solid fa-plus me-1"></i> Gán Phạm Vi Kho Cho User
        </button>
    </div>
</div>

<!-- Scopes List Table -->
<div class="sy-card p-4">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr class="text-muted small">
                    <th>Tài Khoản</th>
                    <th>Email</th>
                    <th>Kho Được Phân Công</th>
                    <th>Quyền Xem</th>
                    <th>Quyền Nhập</th>
                    <th>Quyền Xuất</th>
                    <th>Ngày Gán</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($scopes)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">Chưa có phạm vi kho nào được phân công riêng biệt. Super Admin và Manager có quyền toàn cục mặc định.</td></tr>
                <?php else: ?>
                    <?php foreach ($scopes as $sc): ?>
                        <tr>
                            <td><strong class="text-info"><?= esc($sc->username) ?></strong></td>
                            <td><?= esc($sc->email) ?></td>
                            <td><span class="badge bg-secondary"><?= esc($sc->warehouse_name) ?> (<?= esc($sc->warehouse_code) ?>)</span></td>
                            <td><?= $sc->can_view ? '<span class="badge bg-success">Có</span>' : '<span class="badge bg-danger">Không</span>' ?></td>
                            <td><?= $sc->can_import ? '<span class="badge bg-success">Có</span>' : '<span class="badge bg-danger">Không</span>' ?></td>
                            <td><?= $sc->can_export ? '<span class="badge bg-success">Có</span>' : '<span class="badge bg-danger">Không</span>' ?></td>
                            <td><small class="text-muted"><?= $sc->created_at ?></small></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Add Scope -->
<div class="modal fade" id="modalAddScope" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content sy-card border-info">
            <div class="modal-header border-secondary bg-dark">
                <h5 class="modal-title text-info fw-bold"><i class="fa-solid fa-user-plus me-1"></i> Gán Quyền Kho Cho Người Dùng</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('smartyard/admin/scopes') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4 bg-dark">
                    <div class="mb-3">
                        <label class="form-label text-light fw-bold">Chọn Người Dùng</label>
                        <select name="user_id" class="form-select bg-dark text-light border-secondary" required>
                            <?php foreach ($users as $u): ?>
                                <option value="<?= $u->id ?>"><?= esc($u->username) ?> (<?= esc($u->email) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light fw-bold">Chọn Kho Phụ Trách</label>
                        <select name="warehouse_id" class="form-select bg-dark text-light border-secondary" required>
                            <?php foreach ($warehouses as $wh): ?>
                                <option value="<?= $wh->id ?>"><?= esc($wh->name) ?> (<?= esc($wh->code) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light fw-bold">Các Quyền Thao Tác</label>
                        <div class="d-flex flex-column gap-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="can_view" value="1" id="chkView" checked>
                                <label class="form-check-label text-light" for="chkView">Quyền xem dữ liệu kho</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="can_import" value="1" id="chkImport" checked>
                                <label class="form-check-label text-light" for="chkImport">Quyền nhập lô hàng vào kho</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="can_export" value="1" id="chkExport" checked>
                                <label class="form-check-label text-light" for="chkExport">Quyền xuất lô hàng khỏi kho</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary bg-dark">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-info"><i class="fa-solid fa-check me-1"></i> Lưu Phân Quyền</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?= view('smartyard/layout', ['content' => $content, 'title' => $title, 'currentUser' => $currentUser ?? null]) ?>
