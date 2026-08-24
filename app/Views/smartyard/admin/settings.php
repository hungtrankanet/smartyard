<?php ob_start(); ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="sy-card p-4">
            <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom border-secondary">
                <div class="p-3 bg-info bg-opacity-20 text-info rounded-circle">
                    <i class="fa-solid fa-sliders fa-2x"></i>
                </div>
                <div>
                    <h4 class="fw-bold text-white mb-0">Cấu Hình Tham Số & Ngưỡng Màu Kho</h4>
                    <p class="text-muted small mb-0">Quản trị các tỷ lệ ngưỡng cảnh báo lấp đầy và quy tắc nhập diện tích</p>
                </div>
            </div>

            <form action="<?= base_url('smartyard/admin/settings') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="p-3 rounded bg-dark border border-secondary mb-4">
                    <h6 class="text-info fw-bold mb-3"><i class="fa-solid fa-palette me-2"></i> Ngưỡng Tỷ Lệ Sử Dụng Diện Tích (Usage Rate %)</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label text-light small fw-bold">Ngưỡng Thấp (Xanh)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-secondary text-light border-secondary">0 -</span>
                                <input type="number" step="1" min="1" max="100" name="threshold_low" class="form-control bg-dark text-light border-secondary" value="<?= esc($threshold_low) ?>" required>
                                <span class="input-group-text bg-secondary text-light border-secondary">%</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-light small fw-bold">Ngưỡng Trung Bình (Vàng)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-secondary text-light border-secondary">> Thấp -</span>
                                <input type="number" step="1" min="1" max="100" name="threshold_med" class="form-control bg-dark text-light border-secondary" value="<?= esc($threshold_med) ?>" required>
                                <span class="input-group-text bg-secondary text-light border-secondary">%</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-light small fw-bold">Ngưỡng Mức Cao (Cam)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-secondary text-light border-secondary">> TB -</span>
                                <input type="number" step="1" min="1" max="100" name="threshold_high" class="form-control bg-dark text-light border-secondary" value="<?= esc($threshold_high) ?>" required>
                                <span class="input-group-text bg-secondary text-light border-secondary">%</span>
                            </div>
                        </div>
                    </div>
                    <small class="text-muted mt-2 d-block">Trên mức ngưỡng cao (> <?= esc($threshold_high) ?>%) hệ thống sẽ tự động chuyển sang trạng thái <strong>Đỏ (Gần đầy / Đầy)</strong>.</small>
                </div>

                <div class="p-3 rounded bg-dark border border-secondary mb-4">
                    <h6 class="text-info fw-bold mb-3"><i class="fa-solid fa-shield-halved me-2"></i> Quy Tắc Kiểm Soát Diện Tích</h6>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="allow_over_allocation" value="1" id="allowOverSwitch" <?= ($allow_over_allocation == '1') ? 'checked' : '' ?>>
                        <label class="form-check-label text-light" for="allowOverSwitch">
                            <strong>Cho phép nhập vượt diện tích được cấp (Allocated Area)</strong>
                            <div class="text-muted small">Mặc định tắt: Hệ thống sẽ chặn cứng mọi giao dịch nhập kho nếu <code>used_area + new_area > allocated_area</code>.</div>
                        </label>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 pt-3 border-top border-secondary">
                    <button type="submit" class="btn btn-info px-4"><i class="fa-solid fa-floppy-disk me-1"></i> Lưu Cấu Hình</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?= view('smartyard/layout', ['content' => $content, 'title' => $title, 'currentUser' => $currentUser ?? null]) ?>
