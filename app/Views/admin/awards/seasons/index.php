<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><i class="fa fa-trophy" style="color: #D4AF37;"></i> <?= esc($title); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('add-award-season'); ?>" class="btn btn-sm btn-success btn-add-new">
                        <i class="fa fa-plus"></i> Thêm Mùa Giải Mới
                    </a>
                    <a href="<?= adminUrl('award-categories'); ?>" class="btn btn-sm btn-info">
                        <i class="fa fa-th-list"></i> Danh Sách Hạng Mục
                    </a>
                </div>
            </div>
            <div class="box-body">
                <?php if (!empty($activeSeason)): ?>
                    <div class="callout callout-info" style="background-color: #0A192F !important; border-left-color: #D4AF37; color: #fff;">
                        <h4 style="color: #D4AF37; margin-bottom: 5px;"><i class="fa fa-star"></i> Mùa Giải Đang Kích Hoạt (Active Season): <?= esc($activeSeason->title); ?> (<?= $activeSeason->theme_year; ?>)</h4>
                        <p style="margin-bottom: 0; font-size: 13px; opacity: 0.9;">
                            Thời gian nộp đề cử: <strong><?= $activeSeason->nomination_start_at ? date('d/m/Y', strtotime($activeSeason->nomination_start_at)) : 'N/A'; ?> - <?= $activeSeason->nomination_end_at ? date('d/m/Y', strtotime($activeSeason->nomination_end_at)) : 'N/A'; ?></strong> | 
                            Bình chọn: <strong><?= $activeSeason->voting_start_at ? date('d/m/Y', strtotime($activeSeason->voting_start_at)) : 'N/A'; ?> - <?= $activeSeason->voting_end_at ? date('d/m/Y', strtotime($activeSeason->voting_end_at)) : 'N/A'; ?></strong> | 
                            Đêm vinh danh Gala: <strong style="color: #D4AF37;"><?= $activeSeason->gala_date ? date('d/m/Y', strtotime($activeSeason->gala_date)) : 'N/A'; ?></strong>
                        </p>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped dataTable">
                        <thead>
                            <tr role="row" style="background: #f8fafc;">
                                <th width="50">ID</th>
                                <th>Năm / Mùa Giải</th>
                                <th>Tiêu Đề &amp; Chủ Đề</th>
                                <th>Thời Gian Nộp Đề Cử</th>
                                <th>Thời Gian Bình Chọn</th>
                                <th>Đêm Gala Trao Giải</th>
                                <th width="90" class="text-center">Hạng Mục</th>
                                <th width="90" class="text-center">Hồ Sơ</th>
                                <th width="110" class="text-center">Trạng Thái</th>
                                <th width="120" class="text-center">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($seasons)): foreach ($seasons as $season): ?>
                                <tr>
                                    <td><strong>#<?= $season->id; ?></strong></td>
                                    <td>
                                        <span class="label label-primary" style="font-size: 12px;"><?= $season->theme_year; ?></span>
                                    </td>
                                    <td>
                                        <strong style="font-size: 14px; color: #0A192F;"><?= esc($season->title); ?></strong>
                                        <?php if (!empty($season->is_active)): ?>
                                            <span class="label label-warning" style="background: #D4AF37 !important; color: #0A192F; margin-left: 5px;">
                                                <i class="fa fa-bolt"></i> Đang diễn ra
                                            </span>
                                        <?php endif; ?>
                                        <?php if (!empty($season->slug)): ?>
                                            <br><small class="text-muted">Slug: <?= esc($season->slug); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small><?= $season->nomination_start_at ? date('d/m/Y', strtotime($season->nomination_start_at)) : '-'; ?></small> &rarr; 
                                        <small><?= $season->nomination_end_at ? date('d/m/Y', strtotime($season->nomination_end_at)) : '-'; ?></small>
                                    </td>
                                    <td>
                                        <small><?= $season->voting_start_at ? date('d/m/Y', strtotime($season->voting_start_at)) : '-'; ?></small> &rarr; 
                                        <small><?= $season->voting_end_at ? date('d/m/Y', strtotime($season->voting_end_at)) : '-'; ?></small>
                                    </td>
                                    <td>
                                        <?= $season->gala_date ? '<strong class="text-danger"><i class="fa fa-calendar-star-o"></i> ' . date('d/m/Y', strtotime($season->gala_date)) . '</strong>' : '<span class="text-muted">-</span>'; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= adminUrl('award-categories?season_id=' . $season->id); ?>" class="badge bg-aqua" style="font-size: 12px;">
                                            <?= $season->category_count ?? 0; ?> hạng mục
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= adminUrl('nominations?season_id=' . $season->id); ?>" class="badge bg-green" style="font-size: 12px;">
                                            <?= $season->candidate_count ?? 0; ?> hồ sơ
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($season->status === 'active'): ?>
                                            <span class="label label-success">Hoạt động</span>
                                        <?php elseif ($season->status === 'completed'): ?>
                                            <span class="label label-primary">Đã trao giải</span>
                                        <?php else: ?>
                                            <span class="label label-default"><?= esc($season->status); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                                                <i class="fa fa-cog"></i> <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li>
                                                    <a href="<?= adminUrl('edit-award-season/' . $season->id); ?>">
                                                        <i class="fa fa-edit text-primary"></i> Chỉnh sửa
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="<?= adminUrl('award-categories?season_id=' . $season->id); ?>">
                                                        <i class="fa fa-list text-info"></i> Xem hạng mục
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="<?= adminUrl('nominations?season_id=' . $season->id); ?>">
                                                        <i class="fa fa-users text-green"></i> Xem hồ sơ đề cử
                                                    </a>
                                                </li>
                                                <li class="divider"></li>
                                                <li>
                                                    <form action="<?= adminUrl('delete-award-season-post'); ?>" method="post" onsubmit="return confirm('Bạn có chắc chắn muốn xóa mùa giải này?');">
                                                        <?= csrf_field(); ?>
                                                        <input type="hidden" name="id" value="<?= $season->id; ?>">
                                                        <button type="submit" style="background:none; border:none; padding: 3px 20px; color: #c9302c; width: 100%; text-align: left;">
                                                            <i class="fa fa-trash text-danger"></i> Xóa mùa giải
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">Chưa có mùa giải nào được tạo.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
