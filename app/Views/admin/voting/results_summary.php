<div class="row">
    <div class="col-sm-12">
        <?= view("admin/includes/_messages"); ?>
    </div>
</div>
<div class="box">
    <div class="box-header with-border">
        <div class="left">
            <h3 class="box-title"><i class="fa fa-trophy text-yellow"></i> <?= esc($title); ?></h3>
        </div>
        <div class="right">
            <form action="<?= adminUrl("voting-recalculate-ranks-post"); ?>" method="post" style="display:inline-block;" onsubmit="return confirm(\"Bạn có chắc chắn muốn tính toán lại toàn bộ điểm tổng hợp 70/30 và cập nhật bảng xếp hạng?\");">
                <?= csrf_field(); ?>
                <input type="hidden" name="season_id" value="<?= esc($activeSeason->id ?? 1); ?>">
                <button type="submit" class="btn btn-sm btn-primary btn-add-new">
                    <i class="fa fa-refresh"></i> Tính Lại Điểm & Cập Nhật Thứ Hạng 70/30
                </button>
            </form>
        </div>
    </div>
    <div class="box-body">
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> <strong>Quy chế chấm điểm quốc gia:</strong> Điểm tổng hợp = (70% × Điểm Giám Khảo Chuyên Môn) + (30% × Điểm Bình Chọn Độc Giả Chuẩn Hóa Theo Hạng Mục).
        </div>

        <?php if (!empty($summary)): foreach ($summary as $item): $cat = $item["category"]; ?>
            <div class="box box-solid box-default m-b-20" style="border: 1px solid #e2e8f0; border-radius: 6px;">
                <div class="box-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                    <h4 class="box-title" style="font-weight: 700; color: #0A192F;">
                        <i class="<?= esc($cat->icon ?? "fa fa-award"); ?> text-yellow"></i> <?= esc($cat->name); ?>
                        <span class="badge bg-blue" style="margin-left: 8px;"><?= $item["total"]; ?> ứng viên</span>
                    </h4>
                    <span class="pull-right text-muted" style="font-size: 12px; margin-top: 4px;">
                        Tỷ trọng: <strong><?= $cat->jury_weight; ?>% Giám Khảo</strong> + <strong><?= $cat->public_weight; ?>% Độc Giả</strong>
                    </span>
                </div>
                <div class="box-body no-padding">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr style="background: #f1f5f9; font-size: 12px; text-transform: uppercase;">
                                <th width="60" class="text-center">Hạng</th>
                                <th>Mã Ứng Viên</th>
                                <th>Tên Doanh Nghiệp / Thương Hiệu</th>
                                <th width="140" class="text-center">Phiếu Bình Chọn</th>
                                <th width="140" class="text-center">Điểm GK (70%)</th>
                                <th width="140" class="text-center">Điểm ĐG (30%)</th>
                                <th width="150" class="text-center">Điểm Tổng Hợp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($item["candidates"])): foreach ($item["candidates"] as $cItem): 
                                $c = $cItem["candidate"]; 
                                $s = $cItem["score_breakdown"];
                                $rank = $s["category_rank"];
                            ?>
                                <tr>
                                    <td class="text-center">
                                        <?php if ($rank === 1): ?>
                                            <span class="badge bg-yellow" style="font-size: 13px; font-weight: bold;">🥇 #1</span>
                                        <?php elseif ($rank === 2): ?>
                                            <span class="badge bg-gray" style="font-size: 13px; font-weight: bold;">🥈 #2</span>
                                        <?php elseif ($rank === 3): ?>
                                            <span class="badge" style="background:#cd7f32; color:#fff; font-size: 13px; font-weight: bold;">🥉 #3</span>
                                        <?php else: ?>
                                            <span class="badge bg-default text-muted">#<?= $rank; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><code><?= esc($c->candidate_code); ?></code></td>
                                    <td>
                                        <strong><?= esc($c->name); ?></strong>
                                        <div class="text-muted small"><?= esc($c->organization_name ?? ""); ?></div>
                                    </td>
                                    <td class="text-center">
                                        <span class="label label-primary font-weight-bold" style="font-size: 13px;">
                                            <?= number_format($s["public_votes_raw"]); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <strong><?= number_format($s["jury_score_raw"], 2); ?></strong>
                                        <small class="text-muted">(+<?= number_format($s["jury_score_weighted"], 2); ?>)</small>
                                    </td>
                                    <td class="text-center">
                                        <strong><?= number_format($s["public_score_normalized"], 2); ?>%</strong>
                                        <small class="text-muted">(+<?= number_format($s["public_score_weighted"], 2); ?>)</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="label label-success" style="font-size: 14px; font-weight: 800; padding: 4px 10px;">
                                            <?= number_format($s["final_composite_score"], 2); ?> / 100
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted p-3">
                                        <em>Chưa có ứng viên được duyệt trong hạng mục này.</em>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>
</div>
