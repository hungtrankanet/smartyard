<div class="row">
    <div class="col-sm-12">
        <?= view("admin/includes/_messages"); ?>
    </div>
</div>
<div class="box">
    <div class="box-header with-border">
        <div class="left">
            <h3 class="box-title"><i class="fa fa-shield"></i> <?= esc($title); ?></h3>
        </div>
        <div class="right">
            <form action="<?= adminUrl("voting-export-audit-csv"); ?>" method="post" style="display:inline-block;">
                <?= csrf_field(); ?>
                <input type="hidden" name="season_id" value="<?= esc($filters["season_id"] ?? 1); ?>">
                <button type="submit" class="btn btn-sm btn-success btn-add-new">
                    <i class="fa fa-file-excel-o"></i> Xuất CSV Kiểm Toán
                </button>
            </form>
        </div>
    </div>
    <div class="box-body">
        <div class="row m-b-15">
            <form action="<?= adminUrl("voting-audit-logs"); ?>" method="get">
                <div class="col-md-3">
                    <label>Mùa giải</label>
                    <select name="season_id" class="form-control" onchange="this.form.submit()">
                        <?php foreach ($seasons as $s): ?>
                            <option value="<?= $s->id; ?>" <?= ($filters["season_id"] == $s->id) ? "selected" : ""; ?>>
                                <?= esc($s->title); ?> (<?= $s->theme_year; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Hạng mục</label>
                    <select name="category_id" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Tất cả hạng mục --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat->id; ?>" <?= ($filters["category_id"] == $cat->id) ? "selected" : ""; ?>>
                                <?= esc($cat->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Tìm theo Email</label>
                    <input type="text" name="voter_email" class="form-control" placeholder="Email cử tri..." value="<?= esc($filters["voter_email"] ?? ""); ?>">
                </div>
                <div class="col-md-3">
                    <label>&nbsp;</label><br>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Lọc dữ liệu</button>
                    <a href="<?= adminUrl("voting-audit-logs"); ?>" class="btn btn-default">Đặt lại</a>
                </div>
            </form>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped dataTable">
                        <thead>
                            <tr role="row" style="background: #f8fafc;">
                                <th width="60">ID</th>
                                <th>Thời Gian</th>
                                <th>Ứng Viên</th>
                                <th>Hạng Mục</th>
                                <th>Email Cử Tri</th>
                                <th>IP Address</th>
                                <th width="90">Rủi Ro</th>
                                <th width="100">Trạng Thái</th>
                                <th>Mã Băm Toàn Vẹn (SHA256)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($logs)): foreach ($logs as $log): ?>
                                <tr>
                                    <td><strong>#<?= $log->id; ?></strong></td>
                                    <td><small><?= $log->created_at; ?></small></td>
                                    <td>
                                        <strong><?= esc($log->candidate_name ?? "ID: " . $log->candidate_id); ?></strong><br>
                                        <small class="text-muted"><?= esc($log->candidate_code ?? ""); ?></small>
                                    </td>
                                    <td><?= esc($log->category_name ?? "N/A"); ?></td>
                                    <td><span class="text-primary font-weight-bold"><?= esc($log->voter_email); ?></span></td>
                                    <td><code><?= esc($log->ip_address); ?></code></td>
                                    <td>
                                        <?php if ((int)$log->risk_score < 30): ?>
                                            <span class="label label-success">Thấp (<?= $log->risk_score; ?>)</span>
                                        <?php elseif ((int)$log->risk_score < 70): ?>
                                            <span class="label label-warning">TB (<?= $log->risk_score; ?>)</span>
                                        <?php else: ?>
                                            <span class="label label-danger">Cao (<?= $log->risk_score; ?>)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="label label-success"><i class="fa fa-check-circle"></i> Đã xác thực</span>
                                    </td>
                                    <td>
                                        <code style="font-size: 11px; word-break: break-all; color: #0284c7;">
                                            <?= esc($log->integrity_hash); ?>
                                        </code>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted p-4">
                                        <em>Chưa có nhật ký bình chọn nào được ghi nhận.</em>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="pull-right">
                    <?= $pager ?? ""; ?>
                </div>
            </div>
        </div>
    </div>
</div>
