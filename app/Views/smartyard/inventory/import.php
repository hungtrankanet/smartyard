<?php ob_start(); ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="sy-card p-4">
            <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom border-secondary">
                <div class="p-3 bg-success bg-opacity-20 text-success rounded-circle">
                    <i class="fa-solid fa-arrow-right-to-bracket fa-2x"></i>
                </div>
                <div>
                    <h4 class="fw-bold text-white mb-0">Nhập Lô Hàng Mới Vào Kho</h4>
                    <p class="text-muted small mb-0">Tự động kiểm tra hạn mức diện tích khả dụng và ghi log giao dịch bất biến</p>
                </div>
            </div>

            <form action="<?= base_url('smartyard/inventory/import') ?>" method="POST" id="formImportLot">
                <?= csrf_field() ?>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-light fw-bold">Chọn Kho Nhập Hàng <span class="text-danger">*</span></label>
                        <select name="warehouse_id" id="selectWarehouse" class="form-select bg-dark text-light border-secondary" required onchange="updateWarehouseInfo()">
                            <option value="">-- Chọn kho được phân quyền --</option>
                            <?php foreach ($warehouses as $wh): ?>
                                <option value="<?= $wh->id ?>" 
                                        data-allocated="<?= $wh->allocated_area ?>" 
                                        data-used="<?= $wh->used_area ?>" 
                                        data-available="<?= $wh->available_area ?>"
                                        <?= ($selectedWarehouseId == $wh->id) ? 'selected' : '' ?>>
                                    <?= esc($wh->name) ?> (<?= esc($wh->code) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-light fw-bold">Dự Án Trực Thuộc <span class="text-danger">*</span></label>
                        <select name="project_id" class="form-select bg-dark text-light border-secondary" required>
                            <option value="">-- Chọn dự án --</option>
                            <?php foreach ($projects as $prj): ?>
                                <option value="<?= $prj->id ?>"><?= esc($prj->project_name) ?> (<?= esc($prj->project_code) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Warehouse Live Area Info Box -->
                <div class="p-3 mb-4 rounded bg-dark border border-secondary" id="whLiveInfo" style="display: none;">
                    <div class="row text-center">
                        <div class="col-4 border-end border-secondary">
                            <small class="text-muted">Diện Tích Được Cấp</small>
                            <div class="fw-bold text-info fs-5" id="valAllocated">0 m²</div>
                        </div>
                        <div class="col-4 border-end border-secondary">
                            <small class="text-muted">Đã Dùng</small>
                            <div class="fw-bold text-warning fs-5" id="valUsed">0 m²</div>
                        </div>
                        <div class="col-4">
                            <small class="text-muted">Còn Khả Dụng</small>
                            <div class="fw-bold text-success fs-5" id="valAvailable">0 m²</div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-light fw-bold">Mã Lô Hàng <span class="text-danger">*</span></label>
                        <input type="text" name="lot_code" class="form-control bg-dark text-light border-secondary" placeholder="Ví dụ: LOT-PETRO-2026-001" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-light fw-bold">Diện Tích Chiếm Dụng (m²) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" name="area" id="inputArea" class="form-control bg-dark text-light border-secondary" placeholder="Ví dụ: 150.00" required oninput="validateAreaLimit()">
                        <small class="text-danger mt-1 d-block" id="areaErrorText" style="display: none;"><i class="fa-solid fa-triangle-exclamation me-1"></i> Diện tích nhập vượt quá diện tích còn lại của kho!</small>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-light fw-bold">Tên Hàng Hóa / Vật Tư <span class="text-danger">*</span></label>
                    <input type="text" name="item_name" class="form-control bg-dark text-light border-secondary" placeholder="Ví dụ: Cụm van điều áp khí cao áp 1000 PSI" required>
                </div>

                <div class="mb-4">
                    <label class="form-label text-light fw-bold">Ghi Chú / Yêu Cầu Bảo Quản</label>
                    <textarea name="notes" rows="3" class="form-control bg-dark text-light border-secondary" placeholder="Quy cách bốc xếp, tiêu chuẩn an toàn PCCC..."></textarea>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-3 border-top border-secondary">
                    <a href="<?= base_url('smartyard/map') ?>" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Quay lại Bản đồ</a>
                    <button type="submit" id="btnSubmitImport" class="btn btn-success px-4 py-2"><i class="fa-solid fa-check me-1"></i> Xác Nhận Nhập Kho</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentWhAvailable = 0;

function updateWarehouseInfo() {
    const select = document.getElementById('selectWarehouse');
    const selectedOption = select.options[select.selectedIndex];
    const infoBox = document.getElementById('whLiveInfo');

    if (!selectedOption || !selectedOption.value) {
        infoBox.style.display = 'none';
        currentWhAvailable = 0;
        return;
    }

    const allocated = Number(selectedOption.dataset.allocated || 0);
    const used = Number(selectedOption.dataset.used || 0);
    currentWhAvailable = Number(selectedOption.dataset.available || 0);

    document.getElementById('valAllocated').innerText = allocated.toLocaleString() + ' m²';
    document.getElementById('valUsed').innerText = used.toLocaleString() + ' m²';
    document.getElementById('valAvailable').innerText = currentWhAvailable.toLocaleString() + ' m²';

    infoBox.style.display = 'block';
    validateAreaLimit();
}

function validateAreaLimit() {
    const inputArea = parseFloat(document.getElementById('inputArea').value || 0);
    const errorText = document.getElementById('areaErrorText');
    const submitBtn = document.getElementById('btnSubmitImport');

    if (currentWhAvailable > 0 && inputArea > currentWhAvailable) {
        errorText.style.display = 'block';
        submitBtn.disabled = true;
    } else {
        errorText.style.display = 'none';
        submitBtn.disabled = false;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('selectWarehouse').value) {
        updateWarehouseInfo();
    }
});
</script>

<?php $content = ob_get_clean(); ?>
<?= view('smartyard/layout', ['content' => $content, 'title' => $title, 'currentUser' => $currentUser]) ?>
