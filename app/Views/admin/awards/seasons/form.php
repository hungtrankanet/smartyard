<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><i class="fa fa-pencil-square-o text-primary"></i> <?= esc($title); ?></h3>
                </div>
                <div class="right">
                    <?php if ($formType === 'season'): ?>
                        <a href="<?= adminUrl('award-seasons'); ?>" class="btn btn-sm btn-default">
                            <i class="fa fa-arrow-left"></i> Danh Sách Mùa Giải
                        </a>
                    <?php else: ?>
                        <a href="<?= adminUrl('award-categories'); ?>" class="btn btn-sm btn-default">
                            <i class="fa fa-arrow-left"></i> Danh Sách Hạng Mục
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($formType === 'season'): ?>
                <!-- AWARD SEASON FORM -->
                <?php
                $formAction = ($action === 'edit' && !empty($season))
                    ? adminUrl('edit-award-season-post/' . $season->id)
                    : adminUrl('add-award-season-post');
                ?>
                <form action="<?= $formAction; ?>" method="post">
                    <?= csrf_field(); ?>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="control-label">Tên Mùa Giải Vinh Danh <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" placeholder="Ví dụ: TOP BEST GLOBAL AWARDS 2026" value="<?= old('title', $season->title ?? ''); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Năm Chủ Đề (Theme Year) <span class="text-danger">*</span></label>
                                    <input type="number" name="theme_year" class="form-control" placeholder="2026" value="<?= old('theme_year', $season->theme_year ?? date('Y')); ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Đường Dẫn Tùy Chỉnh (Slug)</label>
                                    <input type="text" name="slug" class="form-control" placeholder="top-best-global-2026 (để trống tự tạo theo tiêu đề)" value="<?= old('slug', $season->slug ?? ''); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label">Mô Tả Chủ Đề &amp; Thông Điệp Mùa Giải</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Mô tả tôn chỉ vinh danh, tiêu chí đánh giá và thông điệp mùa giải..."><?= old('description', $season->description ?? ''); ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Thời Gian Mở Nhận Hồ Sơ Đề Cử</label>
                                    <input type="datetime-local" name="nomination_start_at" class="form-control" value="<?= old('nomination_start_at', !empty($season->nomination_start_at) ? date('Y-m-d\TH:i', strtotime($season->nomination_start_at)) : ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Hạn Chót Nộp Hồ Sơ Đề Cử</label>
                                    <input type="datetime-local" name="nomination_end_at" class="form-control" value="<?= old('nomination_end_at', !empty($season->nomination_end_at) ? date('Y-m-d\TH:i', strtotime($season->nomination_end_at)) : ''); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Bắt Đầu Cổng Bình Chọn Trực Tuyến</label>
                                    <input type="datetime-local" name="voting_start_at" class="form-control" value="<?= old('voting_start_at', !empty($season->voting_start_at) ? date('Y-m-d\TH:i', strtotime($season->voting_start_at)) : ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Đóng Cổng Bình Chọn Trực Tuyến</label>
                                    <input type="datetime-local" name="voting_end_at" class="form-control" value="<?= old('voting_end_at', !empty($season->voting_end_at) ? date('Y-m-d\TH:i', strtotime($season->voting_end_at)) : ''); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Ngày Tổ Chức Đêm Gala Vinh Danh &amp; Trao Cúp</label>
                                    <input type="date" name="gala_date" class="form-control" value="<?= old('gala_date', !empty($season->gala_date) ? date('Y-m-d', strtotime($season->gala_date)) : ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Trạng Thái Mùa Giải</label>
                                    <select name="status" class="form-control">
                                        <option value="active" <?= (old('status', $season->status ?? '') === 'active') ? 'selected' : ''; ?>>Đang hoạt động (Active)</option>
                                        <option value="draft" <?= (old('status', $season->status ?? '') === 'draft') ? 'selected' : ''; ?>>Bản nháp (Draft)</option>
                                        <option value="completed" <?= (old('status', $season->status ?? '') === 'completed') ? 'selected' : ''; ?>>Đã kết thúc / Đã trao giải (Completed)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label">Link Ảnh Banner Mùa Giải (Cloud URL / Storage)</label>
                            <input type="text" name="banner_image" class="form-control" placeholder="https://..." value="<?= old('banner_image', $season->banner_image ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <div class="checkbox">
                                <label style="font-weight: bold; color: #D4AF37;">
                                    <input type="checkbox" name="is_active" value="1" <?= (!empty($season->is_active) || old('is_active')) ? 'checked' : ''; ?>>
                                    Đặt làm Mùa Giải Đang Diễn Ra Mặc Định (Active Season Portal)
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary pull-right">
                            <i class="fa fa-save"></i> <?= ($action === 'edit') ? 'Lưu Thay Đổi Mùa Giải' : 'Khởi Tạo Mùa Giải Mới'; ?>
                        </button>
                        <a href="<?= adminUrl('award-seasons'); ?>" class="btn btn-default">Hủy</a>
                    </div>
                </form>

            <?php else: ?>
                <!-- AWARD CATEGORY FORM -->
                <?php
                $formAction = ($action === 'edit' && !empty($category))
                    ? adminUrl('edit-award-category-post/' . $category->id)
                    : adminUrl('add-award-category-post');
                ?>
                <form action="<?= $formAction; ?>" method="post">
                    <?= csrf_field(); ?>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Mùa Giải Áp Dụng <span class="text-danger">*</span></label>
                                    <select name="season_id" class="form-control" required>
                                        <?php if (!empty($seasons)): foreach ($seasons as $s): ?>
                                            <option value="<?= $s->id; ?>" <?= (old('season_id', $category->season_id ?? ($activeSeason->id ?? 1)) == $s->id) ? 'selected' : ''; ?>>
                                                <?= esc($s->title); ?> (<?= $s->theme_year; ?>)
                                            </option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Lĩnh Vực Ngành Nghề <span class="text-danger">*</span></label>
                                    <input type="text" name="industry_sector" class="form-control" placeholder="Ví dụ: Công Nghệ & Phần Mềm, Tài Chính, Y Tế..." value="<?= old('industry_sector', $category->industry_sector ?? 'Toàn Diện'); ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="control-label">Tên Hạng Mục Giải Thưởng <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" placeholder="Ví dụ: Doanh Nghiệp Công Nghệ Số Xuất Sắc Nhất" value="<?= old('name', $category->name ?? ''); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Icon FontAwesome</label>
                                    <input type="text" name="icon" class="form-control" placeholder="fa fa-award" value="<?= old('icon', $category->icon ?? 'fa fa-award'); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Đường Dẫn Tùy Chỉnh (Slug)</label>
                                    <input type="text" name="slug" class="form-control" placeholder="doanh-nghiep-cong-nghe-so-xuat-sac (để trống tự tạo)" value="<?= old('slug', $category->slug ?? ''); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label">Mô Tả &amp; Tiêu Chuẩn Xét Duyệt Hạng Mục</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Mô tả tiêu chí xét duyệt cho các ứng viên thuộc hạng mục này..."><?= old('description', $category->description ?? ''); ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Trọng Số Điểm Giám Khảo (%)</label>
                                    <input type="number" step="0.01" name="jury_weight" class="form-control" value="<?= old('jury_weight', $category->jury_weight ?? '70.00'); ?>" required>
                                    <small class="text-muted">Mặc định: 70% từ Hội đồng Chuyên môn</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Trọng Số Điểm Bình Chọn (%)</label>
                                    <input type="number" step="0.01" name="public_weight" class="form-control" value="<?= old('public_weight', $category->public_weight ?? '30.00'); ?>" required>
                                    <small class="text-muted">Mặc định: 30% từ Cộng đồng bình chọn</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Thứ Tự Sắp Xếp</label>
                                    <input type="number" name="order_num" class="form-control" value="<?= old('order_num', $category->order_num ?? 0); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Trạng Thái Hạng Mục</label>
                                    <select name="status" class="form-control">
                                        <option value="active" <?= (old('status', $category->status ?? '') === 'active') ? 'selected' : ''; ?>>Hoạt động (Active)</option>
                                        <option value="inactive" <?= (old('status', $category->status ?? '') === 'inactive') ? 'selected' : ''; ?>>Tạm ẩn (Inactive)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary pull-right">
                            <i class="fa fa-save"></i> <?= ($action === 'edit') ? 'Lưu Thay Đổi Hạng Mục' : 'Tạo Hạng Mục Mới'; ?>
                        </button>
                        <a href="<?= adminUrl('award-categories'); ?>" class="btn btn-default">Hủy</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
