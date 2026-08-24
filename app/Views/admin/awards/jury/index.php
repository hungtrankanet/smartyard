<div class="row">
    <div class="col-md-12">
        <!-- Jury Stats Info Boxes -->
        <div class="row" style="margin-bottom: 15px;">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box bg-purple" style="min-height: 75px; margin-bottom: 10px; border-radius: 6px;">
                    <span class="info-box-icon" style="height: 75px; line-height: 75px; font-size: 28px; background: rgba(0,0,0,0.15);"><i class="fa fa-gavel"></i></span>
                    <div class="info-box-content" style="margin-left: 75px; padding-top: 8px;">
                        <span class="info-box-text" style="font-size: 11px;">Tổng Phiếu Chấm Điểm</span>
                        <span class="info-box-number" style="font-size: 20px; font-weight: bold;"><?= number_format($stats['total_evaluations'] ?? 0); ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box bg-green" style="min-height: 75px; margin-bottom: 10px; border-radius: 6px;">
                    <span class="info-box-icon" style="height: 75px; line-height: 75px; font-size: 28px; background: rgba(0,0,0,0.15);"><i class="fa fa-check-circle"></i></span>
                    <div class="info-box-content" style="margin-left: 75px; padding-top: 8px;">
                        <span class="info-box-text" style="font-size: 11px;">Ứng Viên Đã Thẩm Định</span>
                        <span class="info-box-number" style="font-size: 20px; font-weight: bold;"><?= number_format($stats['total_candidates'] ?? 0); ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box bg-yellow" style="min-height: 75px; margin-bottom: 10px; border-radius: 6px;">
                    <span class="info-box-icon" style="height: 75px; line-height: 75px; font-size: 28px; background: rgba(0,0,0,0.15);"><i class="fa fa-star"></i></span>
                    <div class="info-box-content" style="margin-left: 75px; padding-top: 8px;">
                        <span class="info-box-text" style="font-size: 11px;">Điểm Trung Bình HĐGK</span>
                        <span class="info-box-number" style="font-size: 20px; font-weight: bold;"><?= number_format((float)($stats['avg_score'] ?? 0), 2); ?> / 100</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box bg-navy" style="min-height: 75px; margin-bottom: 10px; border-radius: 6px;">
                    <span class="info-box-icon" style="height: 75px; line-height: 75px; font-size: 28px; background: rgba(0,0,0,0.15);"><i class="fa fa-users"></i></span>
                    <div class="info-box-content" style="margin-left: 75px; padding-top: 8px;">
                        <span class="info-box-text" style="font-size: 11px;">Giám Khảo Hoạt Động</span>
                        <span class="info-box-number" style="font-size: 20px; font-weight: bold;"><?= number_format($stats['total_judges'] ?? 0); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Jury Evaluations Box -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><i class="fa fa-gavel text-primary"></i> <?= esc($title); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('jury-members'); ?>" class="btn btn-sm btn-info">
                        <i class="fa fa-users"></i> Danh Sách Hội Đồng Giám Khảo
                    </a>
                </div>
            </div>
            <div class="box-body">
                <!-- Filter Bar -->
                <div class="row m-b-15">
                    <form action="<?= adminUrl('jury-evaluations'); ?>" method="get">
                        <div class="col-md-3">
                            <label>Mùa Giải</label>
                            <select name="season_id" class="form-control" onchange="this.form.submit()">
                                <?php foreach ($seasons as $s): ?>
                                    <option value="<?= $s->id; ?>" <?= (($filters['season_id'] ?? 1) == $s->id) ? 'selected' : ''; ?>>
                                        <?= esc($s->title); ?> (<?= $s->theme_year; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Hạng Mục</label>
                            <select name="category_id" class="form-control" onchange="this.form.submit()">
                                <option value="">-- Tất cả hạng mục --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat->id; ?>" <?= (($filters['category_id'] ?? '') == $cat->id) ? 'selected' : ''; ?>>
                                        <?= esc($cat->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Giám Khảo Chấm Điểm</label>
                            <select name="jury_user_id" class="form-control" onchange="this.form.submit()">
                                <option value="">-- Tất cả giám khảo --</option>
                                <?php foreach ($judges as $j): ?>
                                    <option value="<?= $j->id; ?>" <?= (($filters['jury_user_id'] ?? '') == $j->id) ? 'selected' : ''; ?>>
                                        <?= esc($j->username); ?> (<?= esc($j->email); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label><br>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Lọc dữ liệu</button>
                            <a href="<?= adminUrl('jury-evaluations'); ?>" class="btn btn-default">Đặt lại</a>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped dataTable">
                        <thead>
                            <tr role="row" style="background: #f8fafc;">
                                <th width="50">ID</th>
                                <th>Ứng Viên / Đề Cử</th>
                                <th>Hạng Mục</th>
                                <th>Giám Khảo Thẩm Định</th>
                                <th width="80" class="text-center">Đổi Mới</th>
                                <th width="80" class="text-center">Tăng Trưởng</th>
                                <th width="80" class="text-center">Xã Hội</th>
                                <th width="80" class="text-center">Uy Tín</th>
                                <th width="100" class="text-center">Tổng Điểm</th>
                                <th width="110" class="text-center">Điểm 70%</th>
                                <th width="100" class="text-center">Thời Gian</th>
                                <th width="80" class="text-center">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($evaluations)): foreach ($evaluations as $e): ?>
                                <tr>
                                    <td><strong>#<?= $e->id; ?></strong></td>
                                    <td>
                                        <strong style="color: #0A192F;"><?= esc($e->candidate_name ?? ('ID: ' . $e->candidate_id)); ?></strong>
                                        <?php if (!empty($e->candidate_code)): ?>
                                            <br><small class="text-muted"><?= esc($e->candidate_code); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small class="text-primary font-weight-bold"><?= esc($e->category_name ?? 'N/A'); ?></small>
                                    </td>
                                    <td>
                                        <strong><i class="fa fa-user-secret text-purple"></i> <?= esc($e->jury_name ?? ('Giám khảo #' . $e->jury_user_id)); ?></strong>
                                        <br><small class="text-muted"><?= esc($e->jury_email ?? ''); ?></small>
                                    </td>
                                    <td class="text-center"><span class="badge bg-gray"><?= $e->criteria_1_score; ?></span></td>
                                    <td class="text-center"><span class="badge bg-gray"><?= $e->criteria_2_score; ?></span></td>
                                    <td class="text-center"><span class="badge bg-gray"><?= $e->criteria_3_score; ?></span></td>
                                    <td class="text-center"><span class="badge bg-gray"><?= $e->criteria_4_score; ?></span></td>
                                    <td class="text-center">
                                        <strong style="color: #605ca8; font-size: 14px;"><?= number_format((float)$e->total_score, 2); ?></strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="label label-primary" style="font-size: 12px; background-color: #0A192F !important; color: #D4AF37;">
                                            <?= number_format((float)($e->weighted_70 ?? 0), 2); ?> đ
                                        </span>
                                    </td>
                                    <td class="text-center"><small><?= $e->submitted_at ? date('d/m/Y', strtotime($e->submitted_at)) : '-'; ?></small></td>
                                    <td class="text-center">
                                        <a href="<?= adminUrl('jury-scoring/' . $e->candidate_id); ?>" class="btn btn-xs btn-primary" title="Xem/Sửa Điểm Chấm">
                                            <i class="fa fa-pencil"></i> Chấm lại
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="12" class="text-center text-muted py-4">Chưa có kết quả chấm điểm thẩm định nào.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if (!empty($pager)): ?>
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <?= $pager; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
