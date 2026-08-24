<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><i class="fa fa-th-list text-primary"></i> <?= esc($title); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('add-award-category'); ?>" class="btn btn-sm btn-success btn-add-new">
                        <i class="fa fa-plus"></i> Thêm Hạng Mục Mới
                    </a>
                    <a href="<?= adminUrl('award-seasons'); ?>" class="btn btn-sm btn-default">
                        <i class="fa fa-arrow-left"></i> Quay lại Mùa Giải
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="row m-b-15">
                    <form action="<?= adminUrl('award-categories'); ?>" method="get">
                        <div class="col-md-4">
                            <label>Chọn Mùa Giải Vinh Danh</label>
                            <select name="season_id" class="form-control" onchange="this.form.submit()">
                                <?php foreach ($seasons as $s): ?>
                                    <option value="<?= $s->id; ?>" <?= ($selectedSeasonId == $s->id) ? 'selected' : ''; ?>>
                                        <?= esc($s->title); ?> (<?= $s->theme_year; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped dataTable">
                        <thead>
                            <tr role="row" style="background: #f8fafc;">
                                <th width="50">ID</th>
                                <th width="50" class="text-center">Thứ Tự</th>
                                <th>Tên Hạng Mục Giải Thưởng</th>
                                <th>Lĩnh Vực Ngành Nghề</th>
                                <th width="140" class="text-center">Trọng Số Chấm Điểm</th>
                                <th width="90" class="text-center">Hồ Sơ</th>
                                <th width="100" class="text-center">Trạng Thái</th>
                                <th width="120" class="text-center">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($categories)): foreach ($categories as $cat): ?>
                                <tr>
                                    <td><strong>#<?= $cat->id; ?></strong></td>
                                    <td class="text-center">
                                        <span class="badge bg-gray"><?= $cat->order_num ?? 0; ?></span>
                                    </td>
                                    <td>
                                        <i class="<?= esc($cat->icon ?: 'fa fa-award'); ?> text-yellow m-r-5"></i>
                                        <strong style="font-size: 14px; color: #0A192F;"><?= esc($cat->name); ?></strong>
                                        <br><small class="text-muted">Slug: <?= esc($cat->slug); ?></small>
                                    </td>
                                    <td>
                                        <span class="label label-info" style="font-size: 11px;">
                                            <i class="fa fa-briefcase"></i> <?= esc($cat->industry_sector ?: 'Toàn Diện'); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="label label-primary" title="Trọng số Giám khảo"><?= (float)($cat->jury_weight ?? 70); ?>% Giám Khảo</span><br>
                                        <span class="label label-success" title="Trọng số Bình chọn"><?= (float)($cat->public_weight ?? 30); ?>% Bình Chọn</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= adminUrl('nominations?category_id=' . $cat->id . '&season_id=' . $cat->season_id); ?>" class="badge bg-green">
                                            <?= $cat->candidate_count ?? 0; ?> ứng viên
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($cat->status === 'active'): ?>
                                            <span class="label label-success">Hoạt động</span>
                                        <?php else: ?>
                                            <span class="label label-default"><?= esc($cat->status); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= adminUrl('edit-award-category/' . $cat->id); ?>" class="btn btn-xs btn-primary" title="Chỉnh sửa">
                                            <i class="fa fa-edit"></i> Sửa
                                        </a>
                                        <form action="<?= adminUrl('delete-award-category-post'); ?>" method="post" style="display:inline-block;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa hạng mục này?');">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="id" value="<?= $cat->id; ?>">
                                            <button type="submit" class="btn btn-xs btn-danger" title="Xóa">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">Chưa có hạng mục nào cho mùa giải này.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
