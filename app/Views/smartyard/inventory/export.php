<?php ob_start(); ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="sy-card p-4">
            <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom border-secondary">
                <div class="p-3 bg-danger bg-opacity-20 text-danger rounded-circle">
                    <i class="fa-solid fa-arrow-right-from-bracket fa-2x"></i>
                </div>
                <div>
                    <h4 class="fw-bold text-white mb-0">Xuất Lô Hàng Khỏi Kho</h4>
                    <p class="text-muted small mb-0">Hoàn trả diện tích sử dụng của kho và cập nhật trạng thái lô hàng</p>
                </div>
            </div>

            <form action="<?= base_url('smartyard/inventory/export') ?>" method="POST" id="formExportLot">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label text-light fw-bold">Chọn Kho Lưu Trữ <span class="text-danger">*</span></label>
                    <select id="selectWarehouseExport" class="form-select bg-dark text-light border-secondary" required onchange="loadWarehouseLots()">
                        <option value="">-- Chọn kho để tải danh sách lô --</option>
                        <?php foreach ($warehouses as $wh): ?>
                            <option value="<?= $wh->id ?>"><?= esc($wh->name) ?> (<?= esc($wh->code) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label text-light fw-bold">Chọn Lô Hàng Cần Xuất <span class="text-danger">*</span></label>
                    <select name="lot_id" id="selectLot" class="form-select bg-dark text-light border-secondary" required onchange="updateLotDetails()" disabled>
                        <option value="">-- Vui lòng chọn kho trước --</option>
                    </select>
                </div>

                <!-- Lot Details Box -->
                <div class="p-3 mb-4 rounded bg-dark border border-secondary" id="lotDetailsBox" style="display: none;">
                    <div class="row text-center mb-2">
                        <div class="col-6 border-end border-secondary">
                            <small class="text-muted">Diện Tích Ban Đầu</small>
                            <div class="fw-bold text-info fs-5" id="valLotInitial">0 m²</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Diện Tích Còn Lại Trong Kho</small>
                            <div class="fw-bold text-warning fs-5" id="valLotRemaining">0 m²</div>
                        </div>
                    </div>
                    <div class="small text-muted border-top border-secondary pt-2">
                        <span>Hàng hóa: <strong class="text-light" id="valItemName">--</strong></span> | 
                        <span>Dự án: <strong class="text-info" id="valProjectCode">--</strong></span>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-light fw-bold">Diện Tích Xuất (m²) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" step="0.01" min="0.01" name="export_area" id="inputExportArea" class="form-control bg-dark text-light border-secondary" placeholder="Nhập số m² cần xuất" required oninput="validateExportLimit()">
                        <button class="btn btn-outline-secondary" type="button" onclick="exportFullLot()">Xuất toàn bộ</button>
                    </div>
                    <small class="text-danger mt-1 d-block" id="exportErrorText" style="display: none;"><i class="fa-solid fa-triangle-exclamation me-1"></i> Diện tích xuất không được vượt quá diện tích còn lại của lô!</small>
                </div>

                <div class="mb-4">
                    <label class="form-label text-light fw-bold">Ghi Chú Xuất Kho / Phiếu Yêu Cầu</label>
                    <textarea name="notes" rows="3" class="form-control bg-dark text-light border-secondary" placeholder="Số phiếu điều động, xe vận chuyển, dự án tiếp nhận..."></textarea>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-3 border-top border-secondary">
                    <a href="<?= base_url('smartyard/map') ?>" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Quay lại Bản đồ</a>
                    <button type="submit" id="btnSubmitExport" class="btn btn-danger px-4 py-2" disabled><i class="fa-solid fa-check me-1"></i> Xác Nhận Xuất Kho</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentLotRemaining = 0;
let loadedLots = [];

function loadWarehouseLots() {
    const whId = document.getElementById('selectWarehouseExport').value;
    const lotSelect = document.getElementById('selectLot');
    const detailsBox = document.getElementById('lotDetailsBox');

    detailsBox.style.display = 'none';
    currentLotRemaining = 0;

    if (!whId) {
        lotSelect.innerHTML = '<option value="">-- Vui lòng chọn kho trước --</option>';
        lotSelect.disabled = true;
        return;
    }

    lotSelect.innerHTML = '<option value="">Đang tải danh sách lô...</option>';
    lotSelect.disabled = true;

    fetch('<?= base_url('api/smartyard/lots-by-warehouse') ?>/' + whId)
        .then(res => res.json())
        .then(data => {
            if (data.status && data.lots.length > 0) {
                loadedLots = data.lots;
                let options = '<option value="">-- Chọn lô hàng cần xuất --</option>';
                data.lots.forEach(lot => {
                    options += `<option value="${lot.id}">${lot.lot_code} — ${lot.item_name} (Còn ${Number(lot.remaining_area).toLocaleString()} m²)</option>`;
                });
                lotSelect.innerHTML = options;
                lotSelect.disabled = false;
            } else {
                lotSelect.innerHTML = '<option value="">(Kho này hiện không có lô hàng nào)</option>';
                lotSelect.disabled = true;
            }
        })
        .catch(err => {
            lotSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
        });
}

function updateLotDetails() {
    const lotId = document.getElementById('selectLot').value;
    const detailsBox = document.getElementById('lotDetailsBox');

    if (!lotId) {
        detailsBox.style.display = 'none';
        currentLotRemaining = 0;
        document.getElementById('btnSubmitExport').disabled = true;
        return;
    }

    const lot = loadedLots.find(l => l.id == lotId);
    if (lot) {
        currentLotRemaining = Number(lot.remaining_area || 0);
        document.getElementById('valLotInitial').innerText = Number(lot.initial_area || 0).toLocaleString() + ' m²';
        document.getElementById('valLotRemaining').innerText = currentLotRemaining.toLocaleString() + ' m²';
        document.getElementById('valItemName').innerText = lot.item_name || '';
        document.getElementById('valProjectCode').innerText = lot.project_code || 'N/A';
        detailsBox.style.display = 'block';
        validateExportLimit();
    }
}

function exportFullLot() {
    if (currentLotRemaining > 0) {
        document.getElementById('inputExportArea').value = currentLotRemaining;
        validateExportLimit();
    }
}

function validateExportLimit() {
    const inputArea = parseFloat(document.getElementById('inputExportArea').value || 0);
    const errorText = document.getElementById('exportErrorText');
    const submitBtn = document.getElementById('btnSubmitExport');

    if (currentLotRemaining > 0 && inputArea > 0 && inputArea <= currentLotRemaining) {
        errorText.style.display = 'none';
        submitBtn.disabled = false;
    } else {
        errorText.style.display = (inputArea > currentLotRemaining) ? 'block' : 'none';
        submitBtn.disabled = true;
    }
}
</script>

<?php $content = ob_get_clean(); ?>
<?= view('smartyard/layout', ['content' => $content, 'title' => $title, 'currentUser' => $currentUser]) ?>
