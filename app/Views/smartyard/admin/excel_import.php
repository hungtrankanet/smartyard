<?php ob_start(); ?>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="sy-card p-4">
            <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom border-secondary">
                <div class="p-3 bg-success bg-opacity-20 text-success rounded-circle">
                    <i class="fa-solid fa-file-excel fa-2x"></i>
                </div>
                <div>
                    <h4 class="fw-bold text-white mb-0">Nhập Dữ Liệu Ban Đầu Từ Excel / CSV</h4>
                    <p class="text-muted small mb-0">Quy trình 5 bước: Upload $\rightarrow$ Validate $\rightarrow$ Preview $\rightarrow$ Confirm $\rightarrow$ Import an toàn</p>
                </div>
            </div>

            <!-- Steps Indicator -->
            <div class="d-flex justify-content-between mb-4 position-relative">
                <div class="text-center" style="z-index: 2;">
                    <div class="badge bg-info rounded-pill p-2 mb-1" style="width: 36px; height: 36px; line-height: 20px;">1</div>
                    <div class="small fw-bold text-light">Tải File Lên</div>
                </div>
                <div class="text-center" style="z-index: 2;">
                    <div class="badge bg-secondary rounded-pill p-2 mb-1" style="width: 36px; height: 36px; line-height: 20px;">2</div>
                    <div class="small text-muted">Kiểm Tra Dữ Liệu</div>
                </div>
                <div class="text-center" style="z-index: 2;">
                    <div class="badge bg-secondary rounded-pill p-2 mb-1" style="width: 36px; height: 36px; line-height: 20px;">3</div>
                    <div class="small text-muted">Xem Trước (Preview)</div>
                </div>
                <div class="text-center" style="z-index: 2;">
                    <div class="badge bg-secondary rounded-pill p-2 mb-1" style="width: 36px; height: 36px; line-height: 20px;">4</div>
                    <div class="small text-muted">Xác Nhận Nạp</div>
                </div>
            </div>

            <!-- Step 1 Upload -->
            <div class="p-4 rounded bg-dark border border-dashed border-secondary text-center mb-4">
                <i class="fa-solid fa-cloud-arrow-up fa-3x text-info mb-3"></i>
                <h5 class="text-white fw-bold">Kéo thả file Excel (.xlsx, .csv) vào đây hoặc bấm chọn file</h5>
                <p class="text-muted small mb-3">Hỗ trợ các cột: <code>Mã Kho</code>, <code>Tên Kho</code>, <code>Khu Vực</code>, <code>Diện Tích</code>, <code>Mã Dự Án</code>, <code>Mã Lô</code></p>
                <input type="file" id="excelFileInput" class="form-control bg-dark text-light border-secondary mx-auto mb-3" style="max-width: 400px;" accept=".csv, .xlsx, .xls">
                <button type="button" class="btn btn-outline-info btn-sm" onclick="alert('File mẫu: warehouse_code,warehouse_name,region_code,allocated_area,project_code,lot_code,area')">
                    <i class="fa-solid fa-download me-1"></i> Tải File Mẫu Excel (.xlsx)
                </button>
            </div>

            <!-- Live Preview Table (Mocked for Demo Step 3) -->
            <div class="sy-card p-3 mb-4" id="excelPreviewSection">
                <h6 class="text-info fw-bold mb-2"><i class="fa-solid fa-table-list me-1"></i> Dữ Liệu Mẫu Được Phân Tích Chuẩn Hóa</h6>
                <div class="table-responsive">
                    <table class="table table-dark table-sm table-bordered mb-0">
                        <thead>
                            <tr class="text-muted small">
                                <th>#</th>
                                <th>Khu Vực</th>
                                <th>Mã Kho</th>
                                <th>Tên Kho</th>
                                <th>Diện Tích Cấp</th>
                                <th>Mã Dự Án</th>
                                <th>Mã Lô</th>
                                <th>Trạng Thái Validate</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>REG-BAC</td>
                                <td>KHO-A04</td>
                                <td>Kho Cụm Khí Nén A04</td>
                                <td>2,500 m²</td>
                                <td>PRJ-PETRO-2026</td>
                                <td>LOT-GAS-001</td>
                                <td><span class="badge bg-success"><i class="fa-solid fa-check me-1"></i> Hợp lệ</span></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>REG-NAM</td>
                                <td>KHO-C03</td>
                                <td>Kho Vật Tư Phụ C03</td>
                                <td>1,800 m²</td>
                                <td>PRJ-OFFSHORE-01</td>
                                <td>LOT-PIPE-099</td>
                                <td><span class="badge bg-success"><i class="fa-solid fa-check me-1"></i> Hợp lệ</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center pt-3 border-top border-secondary">
                <a href="<?= base_url('smartyard/map') ?>" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Quay lại</a>
                <button type="button" class="btn btn-success px-4" onclick="alert('Đã nạp thành công dữ liệu lô hàng vào Smart Yard Database!')">
                    <i class="fa-solid fa-cloud-arrow-down me-1"></i> Xác Nhận Nạp Vào Hệ Thống
                </button>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?= view('smartyard/layout', ['content' => $content, 'title' => $title, 'currentUser' => $currentUser ?? null]) ?>
