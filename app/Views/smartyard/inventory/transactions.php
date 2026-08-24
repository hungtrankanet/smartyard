<?php ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-white mb-1"><i class="fa-solid fa-clock-rotate-left text-info me-2"></i> Lịch Sử Giao Dịch Kho (Immutable Audit Log)</h3>
        <p class="text-muted small mb-0">Toàn bộ giao dịch Nhập / Xuất kho được lưu trữ bất biến phục vụ truy vết và kiểm toán</p>
    </div>
</div>

<div class="sy-card p-4">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr class="text-muted small">
                    <th>Mã GD</th>
                    <th>Loại Giao Dịch</th>
                    <th>Mã Lô Hàng</th>
                    <th>Tên Hàng Hóa</th>
                    <th>Kho</th>
                    <th>Dự Án</th>
                    <th>Diện Tích (m²)</th>
                    <th>Kho Trước $\rightarrow$ Sau</th>
                    <th>Lô Trước $\rightarrow$ Sau</th>
                    <th>Người Thực Hiện</th>
                    <th>Thời Gian</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transactions)): ?>
                    <tr><td colspan="11" class="text-center text-muted py-4">Chưa có giao dịch nhập / xuất nào được ghi nhận.</td></tr>
                <?php else: ?>
                    <?php foreach ($transactions as $tx): ?>
                        <tr>
                            <td><span class="badge bg-secondary">#<?= $tx->id ?></span></td>
                            <td>
                                <?php if ($tx->transaction_type === 'IMPORT'): ?>
                                    <span class="badge bg-success"><i class="fa-solid fa-arrow-down me-1"></i> NHẬP KHO</span>
                                <?php elseif ($tx->transaction_type === 'EXPORT'): ?>
                                    <span class="badge bg-danger"><i class="fa-solid fa-arrow-up me-1"></i> XUẤT KHO</span>
                                <?php else: ?>
                                    <span class="badge bg-warning"><?= $tx->transaction_type ?></span>
                                <?php endif; ?>
                            </td>
                            <td><strong class="text-info"><?= esc($tx->lot_code ?? 'N/A') ?></strong></td>
                            <td><?= esc($tx->item_name ?? 'N/A') ?></td>
                            <td><?= esc($tx->warehouse_name ?? 'N/A') ?></td>
                            <td><span class="badge bg-secondary"><?= esc($tx->project_code ?? 'N/A') ?></span></td>
                            <td><strong class="text-warning"><?= number_format($tx->area, 0) ?> m²</strong></td>
                            <td><small class="text-muted"><?= number_format($tx->warehouse_used_before, 0) ?> $\rightarrow$ <?= number_format($tx->warehouse_used_after, 0) ?></small></td>
                            <td><small class="text-muted"><?= number_format($tx->lot_remaining_before, 0) ?> $\rightarrow$ <?= number_format($tx->lot_remaining_after, 0) ?></small></td>
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
<?= view('smartyard/layout', ['content' => $content, 'title' => $title, 'currentUser' => $currentUser ?? null]) ?>
