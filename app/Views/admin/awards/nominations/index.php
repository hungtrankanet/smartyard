<div class="row">
    <div class="col-md-12">
        <!-- Stage Filter Counters -->
        <div class="row" style="margin-bottom: 15px;">
            <div class="col-md-2 col-sm-4 col-xs-6">
                <a href="<?= adminUrl('nominations?season_id=' . ($filters['season_id'] ?? 1)); ?>" style="color: inherit; text-decoration: none;">
                    <div class="info-box bg-navy" style="min-height: 75px; margin-bottom: 10px; border-radius: 6px;">
                        <span class="info-box-icon" style="height: 75px; line-height: 75px; font-size: 28px; background: rgba(0,0,0,0.15);"><i class="fa fa-folder-open"></i></span>
                        <div class="info-box-content" style="margin-left: 75px; padding-top: 8px;">
                            <span class="info-box-text" style="font-size: 11px;">Tất Cả Hồ Sơ</span>
                            <span class="info-box-number" style="font-size: 20px; font-weight: bold;"><?= number_format($stageStats['total'] ?? 0); ?></span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6">
                <a href="<?= adminUrl('nominations?stage=so_khao&season_id=' . ($filters['season_id'] ?? 1)); ?>" style="color: inherit; text-decoration: none;">
                    <div class="info-box bg-aqua" style="min-height: 75px; margin-bottom: 10px; border-radius: 6px;">
                        <span class="info-box-icon" style="height: 75px; line-height: 75px; font-size: 28px; background: rgba(0,0,0,0.15);"><i class="fa fa-file-text"></i></span>
                        <div class="info-box-content" style="margin-left: 75px; padding-top: 8px;">
                            <span class="info-box-text" style="font-size: 11px;">Vòng 1: Sơ Khảo</span>
                            <span class="info-box-number" style="font-size: 20px; font-weight: bold;"><?= number_format($stageStats['so_khao'] ?? 0); ?></span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6">
                <a href="<?= adminUrl('nominations?stage=tham_dinh&season_id=' . ($filters['season_id'] ?? 1)); ?>" style="color: inherit; text-decoration: none;">
                    <div class="info-box bg-purple" style="min-height: 75px; margin-bottom: 10px; border-radius: 6px;">
                        <span class="info-box-icon" style="height: 75px; line-height: 75px; font-size: 28px; background: rgba(0,0,0,0.15);"><i class="fa fa-gavel"></i></span>
                        <div class="info-box-content" style="margin-left: 75px; padding-top: 8px;">
                            <span class="info-box-text" style="font-size: 11px;">Vòng 2: Thẩm Định</span>
                            <span class="info-box-number" style="font-size: 20px; font-weight: bold;"><?= number_format($stageStats['tham_dinh'] ?? 0); ?></span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6">
                <a href="<?= adminUrl('nominations?stage=chung_khao&season_id=' . ($filters['season_id'] ?? 1)); ?>" style="color: inherit; text-decoration: none;">
                    <div class="info-box bg-yellow" style="min-height: 75px; margin-bottom: 10px; border-radius: 6px;">
                        <span class="info-box-icon" style="height: 75px; line-height: 75px; font-size: 28px; background: rgba(0,0,0,0.15);"><i class="fa fa-users"></i></span>
                        <div class="info-box-content" style="margin-left: 75px; padding-top: 8px;">
                            <span class="info-box-text" style="font-size: 11px;">Vòng 3: Chung Khảo</span>
                            <span class="info-box-number" style="font-size: 20px; font-weight: bold;"><?= number_format($stageStats['chung_khao'] ?? 0); ?></span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6">
                <a href="<?= adminUrl('nominations?stage=trao_giai&season_id=' . ($filters['season_id'] ?? 1)); ?>" style="color: inherit; text-decoration: none;">
                    <div class="info-box bg-green" style="min-height: 75px; margin-bottom: 10px; border-radius: 6px;">
                        <span class="info-box-icon" style="height: 75px; line-height: 75px; font-size: 28px; background: rgba(0,0,0,0.15);"><i class="fa fa-trophy"></i></span>
                        <div class="info-box-content" style="margin-left: 75px; padding-top: 8px;">
                            <span class="info-box-text" style="font-size: 11px;">Vòng 4: Trao Giải</span>
                            <span class="info-box-number" style="font-size: 20px; font-weight: bold;"><?= number_format($stageStats['trao_giai'] ?? 0); ?></span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6">
                <a href="<?= adminUrl('nominations?stage=rejected&season_id=' . ($filters['season_id'] ?? 1)); ?>" style="color: inherit; text-decoration: none;">
                    <div class="info-box bg-red" style="min-height: 75px; margin-bottom: 10px; border-radius: 6px;">
                        <span class="info-box-icon" style="height: 75px; line-height: 75px; font-size: 28px; background: rgba(0,0,0,0.15);"><i class="fa fa-times-circle"></i></span>
                        <div class="info-box-content" style="margin-left: 75px; padding-top: 8px;">
                            <span class="info-box-text" style="font-size: 11px;">Không Đạt</span>
                            <span class="info-box-number" style="font-size: 20px; font-weight: bold;"><?= number_format($stageStats['rejected'] ?? 0); ?></span>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Main Nominations Table Box -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><i class="fa fa-files-o text-primary"></i> <?= esc($title); ?></h3>
                </div>
                <div class="right">
                    <span class="label label-default" style="font-size: 13px;">Tổng cộng: <?= number_format($totalRows); ?> hồ sơ</span>
                </div>
            </div>
            <div class="box-body">
                <!-- Filter Bar -->
                <div class="row m-b-15">
                    <form action="<?= adminUrl('nominations'); ?>" method="get">
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
                            <label>Hạng Mục Giải Thưởng</label>
                            <select name="category_id" class="form-control" onchange="this.form.submit()">
                                <option value="">-- Tất cả hạng mục --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat->id; ?>" <?= (($filters['category_id'] ?? '') == $cat->id) ? 'selected' : ''; ?>>
                                        <?= esc($cat->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Vòng Xét Duyệt</label>
                            <select name="stage" class="form-control" onchange="this.form.submit()">
                                <option value="">-- Tất cả vòng --</option>
                                <option value="so_khao" <?= (($filters['stage'] ?? '') === 'so_khao') ? 'selected' : ''; ?>>Vòng 1: Sơ Khảo</option>
                                <option value="tham_dinh" <?= (($filters['stage'] ?? '') === 'tham_dinh') ? 'selected' : ''; ?>>Vòng 2: Thẩm Định</option>
                                <option value="chung_khao" <?= (($filters['stage'] ?? '') === 'chung_khao') ? 'selected' : ''; ?>>Vòng 3: Chung Khảo</option>
                                <option value="trao_giai" <?= (($filters['stage'] ?? '') === 'trao_giai') ? 'selected' : ''; ?>>Vòng 4: Trao Giải</option>
                                <option value="rejected" <?= (($filters['stage'] ?? '') === 'rejected') ? 'selected' : ''; ?>>Không Đạt</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Tìm kiếm Tên/MST/Mã hồ sơ</label>
                            <input type="text" name="q" class="form-control" placeholder="Từ khóa tìm kiếm..." value="<?= esc($filters['q'] ?? ''); ?>">
                        </div>
                        <div class="col-md-1">
                            <label>&nbsp;</label><br>
                            <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-search"></i></button>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped dataTable">
                        <thead>
                            <tr role="row" style="background: #f8fafc;">
                                <th width="50">ID</th>
                                <th width="120">Mã Đề Cử</th>
                                <th>Ứng Viên / Doanh Nghiệp</th>
                                <th>Hạng Mục Giải Thưởng</th>
                                <th width="150" class="text-center">Vòng Xét Duyệt</th>
                                <th width="90" class="text-center">Điểm GK (70%)</th>
                                <th width="90" class="text-center">Bình Chọn</th>
                                <th width="100" class="text-center">Tổng Điểm</th>
                                <th width="90" class="text-center">Trạng Thái</th>
                                <th width="120" class="text-center">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($candidates)): foreach ($candidates as $c): ?>
                                <tr>
                                    <td><strong>#<?= $c->id; ?></strong></td>
                                    <td>
                                        <code style="font-size: 11px; font-weight: bold; color: #0284c7;"><?= esc($c->candidate_code); ?></code>
                                    </td>
                                    <td>
                                        <strong style="font-size: 14px; color: #0A192F;"><?= esc($c->name); ?></strong>
                                        <?php if (!empty($c->organization_name) && $c->organization_name !== $c->name): ?>
                                            <br><small class="text-muted"><i class="fa fa-building-o"></i> <?= esc($c->organization_name); ?></small>
                                        <?php endif; ?>
                                        <?php if (!empty($c->tax_code)): ?>
                                            <br><small class="text-muted">MST: <code><?= esc($c->tax_code); ?></code></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small class="text-primary font-weight-bold"><?= esc($c->category_name ?? 'N/A'); ?></small><br>
                                        <small class="text-muted"><?= esc($c->industry_sector ?? ''); ?></small>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $stage = $c->stage ?? 'so_khao';
                                        if ($stage === 'so_khao'): ?>
                                            <span class="label label-info"><i class="fa fa-file-text-o"></i> V1: Sơ Khảo</span>
                                        <?php elseif ($stage === 'tham_dinh'): ?>
                                            <span class="label label-primary" style="background-color: #605ca8 !important;"><i class="fa fa-gavel"></i> V2: Thẩm Định</span>
                                        <?php elseif ($stage === 'chung_khao'): ?>
                                            <span class="label label-warning" style="background-color: #f39c12 !important;"><i class="fa fa-users"></i> V3: Chung Khảo</span>
                                        <?php elseif ($stage === 'trao_giai' || $stage === 'awarded'): ?>
                                            <span class="label label-success" style="background-color: #00a65a !important;"><i class="fa fa-trophy"></i> V4: Trao Giải</span>
                                        <?php elseif ($stage === 'rejected'): ?>
                                            <span class="label label-danger"><i class="fa fa-times"></i> Không đạt</span>
                                        <?php else: ?>
                                            <span class="label label-default"><?= esc($stage); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <strong style="color: #605ca8; font-size: 13px;"><?= number_format((float)($c->jury_score_avg ?? 0), 2); ?></strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-green"><?= number_format((int)($c->public_votes_count ?? 0)); ?></span>
                                    </td>
                                    <td class="text-center">
                                        <strong style="color: #D4AF37; font-size: 14px;"><?= number_format((float)($c->composite_score ?? 0), 2); ?></strong>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($c->status === 'approved'): ?>
                                            <span class="label label-success">Đã duyệt</span>
                                        <?php elseif ($c->status === 'rejected'): ?>
                                            <span class="label label-danger">Từ chối</span>
                                        <?php else: ?>
                                            <span class="label label-warning">Chờ duyệt</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= adminUrl('nomination-dossier/' . $c->id); ?>" class="btn btn-xs btn-info" title="Xem chi tiết hồ sơ & Chuyển vòng">
                                            <i class="fa fa-eye"></i> Hồ sơ
                                        </a>
                                        <form action="<?= adminUrl('nomination-delete-post'); ?>" method="post" style="display:inline-block;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa hồ sơ đề cử này?');">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="id" value="<?= $c->id; ?>">
                                            <button type="submit" class="btn btn-xs btn-danger" title="Xóa">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">Không tìm thấy hồ sơ đề cử nào phù hợp.</td>
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
