<div class="row">
    <div class="col-md-12">
        <div class="box-header with-border" style="margin-bottom: 15px; padding-left: 0;">
            <div class="left">
                <h3 class="box-title" style="font-size: 20px; font-weight: 700;">
                    <i class="fa fa-briefcase text-primary"></i> <?= esc($title); ?>
                </h3>
            </div>
            <div class="right">
                <a href="<?= adminUrl('members'); ?>" class="btn btn-default btn-sm">
                    <i class="fa fa-bars"></i> Danh Sách Hội Viên
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column: Add New Industry Form -->
    <div class="col-md-4">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title" style="font-size: 15px; font-weight: 600;">
                    <i class="fa fa-plus-circle text-success"></i> Thêm Ngành Nghề Mới
                </h3>
            </div>
            <form action="<?= adminUrl('industry-types/add-post'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label">Tên Ngành Nghề <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control input-sm" placeholder="VD: Vận Tải & Logistics" value="<?= old('name'); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Đường Dẫn (Slug)</label>
                        <input type="text" name="name_slug" class="form-control input-sm" placeholder="Tự động tạo nếu để trống" value="<?= old('name_slug'); ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label">Icon FontAwesome (Icon Class)</label>
                        <div class="input-group">
                            <input type="text" name="icon" id="industryIconInput" class="form-control input-sm" placeholder="fa fa-truck" value="<?= old('icon') ?: 'fa fa-briefcase'; ?>">
                            <span class="input-group-addon" id="iconPreview"><i class="<?= old('icon') ?: 'fa fa-briefcase'; ?>"></i></span>
                        </div>
                        <small class="text-muted">VD: <code>fa fa-truck</code>, <code>fa fa-globe</code>, <code>fa fa-industry</code>, <code>fa fa-laptop</code>, <code>fa fa-ship</code></small>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Thứ Tự Sắp Xếp</label>
                        <input type="number" name="sort_order" class="form-control input-sm" value="<?= old('sort_order') ?: 0 ?>" min="0">
                    </div>

                    <div class="form-group">
                        <label class="control-label">Mô Tả</label>
                        <textarea name="description" class="form-control input-sm" rows="3" placeholder="Mô tả tóm tắt về loại hình doanh nghiệp..."><?= old('description'); ?></textarea>
                    </div>
                </div>

                <div class="box-footer text-right">
                    <button type="submit" class="btn btn-primary btn-sm" style="font-weight: 600;">
                        <i class="fa fa-plus"></i> Thêm Ngành Nghề
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Right Column: Industry Types Table -->
    <div class="col-md-8">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title" style="font-size: 15px; font-weight: 600;">
                    <i class="fa fa-list"></i> Danh Sách Ngành Nghề Hiện Có (<?= count($industries ?? []); ?>)
                </h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-bordered table-striped dataTable" style="font-size: 13px;">
                    <thead>
                        <tr style="background: #f4f6f9;">
                            <th style="width: 50px;" class="text-center">#</th>
                            <th>Tên Ngành Nghề</th>
                            <th>Slug</th>
                            <th style="width: 70px;" class="text-center">Thứ Tự</th>
                            <th style="width: 110px;" class="text-center">Hội Viên</th>
                            <th style="width: 110px;" class="text-center">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($industries)): ?>
                            <?php foreach ($industries as $ind): ?>
                                <tr>
                                    <td class="text-center" style="vertical-align: middle; color: #888;"><?= $ind->id; ?></td>
                                    <td style="vertical-align: middle;">
                                        <i class="<?= esc($ind->icon ?: 'fa fa-briefcase'); ?> text-primary" style="margin-right: 8px; font-size: 15px; width: 18px; text-align: center;"></i>
                                        <strong><?= esc($ind->name); ?></strong>
                                        <?php if (!empty($ind->description)): ?>
                                            <div style="font-size: 11px; color: #888; margin-top: 2px;"><?= esc($ind->description); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td style="vertical-align: middle; color: #666;">
                                        <code><?= esc($ind->name_slug); ?></code>
                                    </td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        <span class="badge bg-gray text-black"><?= $ind->sort_order; ?></span>
                                    </td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        <a href="<?= adminUrl('members?industry_type_id=' . $ind->id); ?>" class="label <?= ($ind->member_count > 0) ? 'label-primary' : 'label-default'; ?>" style="font-size: 12px; padding: 4px 8px;">
                                            <i class="fa fa-users"></i> <?= number_format($ind->member_count ?? 0); ?>
                                        </a>
                                    </td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        <a href="<?= adminUrl('industry-types/edit/' . $ind->id); ?>" class="btn btn-xs btn-default" title="Chỉnh sửa">
                                            <i class="fa fa-edit text-primary"></i> Sửa
                                        </a>
                                        <a href="javascript:void(0)" onclick="deleteItem('IndustryType/deleteIndustryPost', '<?= $ind->id; ?>', '<?= clrQuotes('Bạn có chắc chắn muốn xoá ngành nghề này? Các hội viên thuộc ngành nghề này sẽ được chuyển về Chưa Phân Loại.'); ?>');" class="btn btn-xs btn-default text-danger" title="Xoá">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted" style="padding: 30px;">
                                    Chưa có ngành nghề nào trong cơ sở dữ liệu.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#industryIconInput').on('input', function() {
        var val = $(this).val().trim();
        $('#iconPreview i').attr('class', val || 'fa fa-briefcase');
    });
});
</script>
