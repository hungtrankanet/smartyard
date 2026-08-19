<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><i class="fa fa-pencil-square-o text-primary"></i> <?= esc($title); ?></h3>
                    <p class="text-muted" style="margin-top: 5px; margin-bottom: 0;">
                        Kiểm duyệt các bài tự giới thiệu của thành viên doanh nghiệp trước khi cho phép hiển thị công khai trên cổng thông tin TOP BEST GLOBAL.
                    </p>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('members'); ?>" class="btn btn-default btn-sm"><i class="fa fa-handshake-o"></i> Danh Sách Đối Tác</a>
                </div>
            </div>

            <div class="box-body">
                <!-- Filters -->
                <form method="GET" action="<?= adminUrl('members/posts'); ?>" style="margin-bottom: 15px;">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="q" class="form-control input-sm" placeholder="Tìm kiếm theo tiêu đề bài viết, tên công ty..." value="<?= esc($filters['q'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-control input-sm">
                                <option value="">-- Tất cả trạng thái --</option>
                                <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : ''; ?>>⏳ Chờ Duyệt (Pending)</option>
                                <option value="approved" <?= ($filters['status'] ?? '') === 'approved' ? 'selected' : ''; ?>>✔ Đã Duyệt (Approved)</option>
                                <option value="rejected" <?= ($filters['status'] ?? '') === 'rejected' ? 'selected' : ''; ?>>✖ Từ Chối (Rejected)</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Lọc dữ liệu</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped dataTable" role="grid">
                        <thead>
                            <tr role="row">
                                <th width="40">ID</th>
                                <th>Tiêu Đề Bài Giới Thiệu</th>
                                <th>Doanh Nghiệp</th>
                                <th>Thông Tin Liên Hệ</th>
                                <th>Trạng Thái</th>
                                <th>Thời Gian</th>
                                <th width="140" class="text-center">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($posts)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted" style="padding: 30px;">
                                        <i class="fa fa-folder-open-o" style="font-size: 28px;"></i>
                                        <p style="margin-top: 5px;">Chưa có bài giới thiệu nào của thành viên.</p>
                                    </td>
                                </tr>
                            <?php else: foreach ($posts as $p): ?>
                                <tr>
                                    <td><?= $p->id; ?></td>
                                    <td>
                                        <strong><?= esc($p->title); ?></strong>
                                        <?php if (!empty($p->image_default)): ?>
                                            <span class="label label-default"><i class="fa fa-image"></i> Có ảnh</span>
                                        <?php endif; ?>
                                        <div style="font-size: 11px; color: #888; margin-top: 2px;">
                                            <?= esc(characterLimiter($p->summary ?: strip_tags($p->content), 80)); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <strong><?= esc($p->company_name ?: 'Doanh nghiệp #' . $p->member_id); ?></strong>
                                        <div style="font-size: 11px; color: #888;"><?= esc($p->city ?: 'Toàn quốc'); ?></div>
                                    </td>
                                    <td style="font-size: 12px;">
                                        <div><i class="fa fa-envelope-o"></i> <?= esc($p->company_email ?: 'N/A'); ?></div>
                                        <div><i class="fa fa-phone"></i> <?= esc($p->company_phone ?: 'N/A'); ?></div>
                                    </td>
                                    <td>
                                        <?php if ($p->status === 'approved'): ?>
                                            <span class="label label-success"><i class="fa fa-check"></i> Đã Duyệt</span>
                                        <?php elseif ($p->status === 'rejected'): ?>
                                            <span class="label label-danger" title="<?= esc($p->reject_reason); ?>"><i class="fa fa-times"></i> Từ Chối</span>
                                        <?php else: ?>
                                            <span class="label label-warning"><i class="fa fa-clock-o"></i> Chờ Duyệt</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="font-size: 11px; color: #888;">
                                        <?= formatDate($p->created_at); ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default btn-xs btn-view-post" 
                                                    data-title="<?= esc($p->title); ?>" 
                                                    data-company="<?= esc($p->company_name); ?>" 
                                                    data-content="<?= esc($p->content); ?>" 
                                                    data-image="<?= !empty($p->image_default) ? base_url($p->image_default) : ''; ?>" 
                                                    title="Xem trước nội dung"><i class="fa fa-eye"></i></button>

                                            <?php if ($p->status !== 'approved'): ?>
                                                <a href="<?= adminUrl('members/approve-post/' . $p->id); ?>" class="btn btn-success btn-xs" onclick="return confirm('Duyệt bài viết này để công khai trên website?')" title="Duyệt bài"><i class="fa fa-check"></i> Duyệt</a>
                                            <?php endif; ?>

                                            <?php if ($p->status !== 'rejected'): ?>
                                                <button type="button" class="btn btn-danger btn-xs btn-reject-modal" data-id="<?= $p->id; ?>" title="Từ chối"><i class="fa fa-times"></i></button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="text-right">
                    <?= $pager->links('default', 'custom_pager'); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal View Post -->
<div class="modal fade" id="modalPostPreview" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 6px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="viewPostTitle" style="font-weight: 700;"></h4>
                <small class="text-muted" id="viewPostCompany"></small>
            </div>
            <div class="modal-body" style="max-height: 500px; overflow-y: auto;">
                <div id="viewPostBanner" style="margin-bottom: 15px; text-align: center; display: none;">
                    <img src="" style="max-height: 250px; border-radius: 6px;" id="viewPostImg">
                </div>
                <div id="viewPostContent" style="font-size: 14px; line-height: 1.7; white-space: pre-line;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reject Post -->
<div class="modal fade" id="modalRejectPost" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 6px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" style="font-weight: 700; color: #dd4b39;"><i class="fa fa-times-circle"></i> Từ Chối Bài Viết Giới Thiệu</h4>
            </div>
            <form id="formRejectPost" method="POST" action="">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Lý do từ chối (thành viên sẽ nhận được thông báo để sửa lại):</label>
                        <textarea name="reject_reason" class="form-control" rows="4" placeholder="Ví dụ: Thiếu thông tin liên hệ chính thức, hình ảnh chưa rõ nét..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger" style="font-weight: 700;">Xác Nhận Từ Chối</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $('.btn-view-post').on('click', function () {
        $('#viewPostTitle').text($(this).data('title'));
        $('#viewPostCompany').text('Doanh nghiệp: ' + $(this).data('company'));
        $('#viewPostContent').text($(this).data('content'));
        var img = $(this).data('image');
        if (img) {
            $('#viewPostImg').attr('src', img);
            $('#viewPostBanner').show();
        } else {
            $('#viewPostBanner').hide();
        }
        $('#modalPostPreview').modal('show');
    });

    $('.btn-reject-modal').on('click', function () {
        var id = $(this).data('id');
        $('#formRejectPost').attr('action', '<?= adminUrl("members/reject-post/"); ?>' + id);
        $('#modalRejectPost').modal('show');
    });
});
</script>
