<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title" style="font-size: 16px; font-weight: 700;">
                        <i class="fa fa-edit text-primary"></i> <?= esc($title); ?>
                    </h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('industry-types'); ?>" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>

            <form action="<?= adminUrl('industry-types/edit-post/' . $industry->id); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label">Tên Ngành Nghề <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="VD: Vận Tải & Logistics" value="<?= old('name') ?: esc($industry->name); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Đường Dẫn (Slug)</label>
                        <input type="text" name="name_slug" class="form-control" placeholder="Tự động tạo nếu để trống" value="<?= old('name_slug') ?: esc($industry->name_slug); ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label">Icon FontAwesome (Icon Class)</label>
                        <div class="input-group">
                            <input type="text" name="icon" id="industryIconInput" class="form-control" placeholder="fa fa-truck" value="<?= old('icon') ?: esc($industry->icon ?: 'fa fa-briefcase'); ?>">
                            <span class="input-group-addon" id="iconPreview"><i class="<?= old('icon') ?: esc($industry->icon ?: 'fa fa-briefcase'); ?>"></i></span>
                        </div>
                        <small class="text-muted">VD: <code>fa fa-truck</code>, <code>fa fa-globe</code>, <code>fa fa-industry</code>, <code>fa fa-laptop</code>, <code>fa fa-ship</code></small>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Thứ Tự Sắp Xếp</label>
                        <input type="number" name="sort_order" class="form-control" value="<?= old('sort_order') !== null ? old('sort_order') : $industry->sort_order; ?>" min="0">
                    </div>

                    <div class="form-group">
                        <label class="control-label">Mô Tả</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Mô tả tóm tắt về loại hình doanh nghiệp..."><?= old('description') ?: esc($industry->description); ?></textarea>
                    </div>
                </div>

                <div class="box-footer text-right">
                    <a href="<?= adminUrl('industry-types'); ?>" class="btn btn-default">Hủy</a>
                    <button type="submit" class="btn btn-primary" style="font-weight: 600; min-width: 140px;">
                        <i class="fa fa-check"></i> Lưu Thay Đổi
                    </button>
                </div>
            </form>
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
