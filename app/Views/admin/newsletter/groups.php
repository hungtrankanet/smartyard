<div class="row">
    <div class="col-sm-12 title-section" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <h3 style="margin: 0; font-size: 22px; font-weight: 800; color: #0f172a;">
                <i class="fa fa-users text-primary"></i> Quản Lý Nhóm Email & Phân Khúc Đối Tượng
            </h3>
            <p style="color: #64748b; margin: 4px 0 0; font-size: 13px;">Tạo và phân loại các nhóm khách hàng dựa theo bộ lọc đa chiều (Ngôn ngữ, Ngành nghề, Trạng thái xác thực, Danh sách tự nhập).</p>
        </div>
        <div>
            <button type="button" class="btn btn-success btn-lg" id="btnOpenAddGroupModal" style="font-weight: 700; box-shadow: 0 4px 12px rgba(22,163,74,0.3);">
                <i class="fa fa-plus-circle"></i> + Tạo Nhóm Email Mới
            </button>
        </div>
    </div>
</div>

<!-- Nav Tabs: 1. Campaigns | 2. Groups -->
<div class="row" style="margin-bottom: 20px;">
    <div class="col-sm-12">
        <ul class="nav nav-pills" style="background: #fff; padding: 10px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); font-size: 14px; font-weight: 700;">
            <li>
                <a href="<?= adminUrl('newsletter'); ?>" style="color: #475569;"><i class="fa fa-paper-plane text-primary"></i> 1. Chiến Dịch Email Marketing</a>
            </li>
            <li class="active">
                <a href="<?= adminUrl('newsletter-groups'); ?>" style="background: #2563eb; color: #fff;"><i class="fa fa-users"></i> 2. Quản Lý Nhóm Email</a>
            </li>
        </ul>
    </div>
</div>

<!-- Groups Table -->
<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary" style="border-radius: 10px; box-shadow: 0 4px 16px rgba(0,0,0,0.04);">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-list text-primary"></i> Danh Sách Nhóm Email Đã Tạo (<?= count($groups); ?>)</h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-bordered table-striped" style="font-size: 13px;">
                    <thead>
                        <tr style="background: #f8fafc;">
                            <th width="40">#</th>
                            <th>Tên Nhóm Email</th>
                            <th>Nguồn Đối Tượng</th>
                            <th>Bộ Lọc Áp Dụng</th>
                            <th class="text-center" width="130">Số Lượng Email</th>
                            <th class="text-center" width="130">Ngày Tạo</th>
                            <th class="text-center" width="160">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($groups)): 
                            $idx = 1;
                            foreach ($groups as $g): 
                                $srcLabel = 'Tất cả';
                                if ($g->filter_source === 'members') $srcLabel = 'Đối tác TOP BEST GLOBAL';
                                elseif ($g->filter_source === 'subscribers') $srcLabel = 'Khách Subscribers';
                                elseif ($g->filter_source === 'users') $srcLabel = 'Người dùng (Users)';
                                elseif ($g->filter_source === 'custom') $srcLabel = 'Danh sách nhập tay';

                                $langLabel = 'Mọi ngôn ngữ';
                                if ($g->filter_lang === 'vi') $langLabel = '🇻🇳 Tiếng Việt';
                                elseif ($g->filter_lang === 'en') $langLabel = '🇬🇧 English';
                        ?>
                            <tr>
                                <td><?= $idx++; ?></td>
                                <td>
                                    <strong style="color: #0f172a; font-size: 14px;"><?= esc($g->name); ?></strong>
                                    <?php if (!empty($g->description)): ?>
                                        <p style="color: #64748b; font-size: 12px; margin: 2px 0 0;"><?= esc($g->description); ?></p>
                                    <?php endif; ?>
                                </td>
                                <td><span class="label label-primary"><?= $srcLabel; ?></span></td>
                                <td>
                                    <span class="label label-info"><?= $langLabel; ?></span>
                                    <?php if ($g->filter_industry_id > 0): ?>
                                        <span class="label label-warning">Ngành nghề ID: <?= $g->filter_industry_id; ?></span>
                                    <?php endif; ?>
                                    <?php if ($g->filter_verify_status === 'verified'): ?>
                                        <span class="label label-success">Đã xác thực</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-green" style="font-size: 13px; padding: 5px 10px;"><?= (int)$g->total_contacts; ?> email</span>
                                </td>
                                <td class="text-center" style="color: #64748b;"><?= date('d/m/Y', strtotime($g->created_at)); ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-info btn-preview-group" data-source="<?= $g->filter_source; ?>" data-lang="<?= $g->filter_lang; ?>" data-industry="<?= $g->filter_industry_id; ?>" data-verify="<?= $g->filter_verify_status; ?>" data-custom="<?= esc($g->custom_emails); ?>" title="Xem trước danh sách"><i class="fa fa-eye"></i></button>
                                    <button type="button" class="btn btn-sm btn-primary btn-edit-group" data-id="<?= $g->id; ?>" data-name="<?= esc($g->name); ?>" data-desc="<?= esc($g->description); ?>" data-source="<?= $g->filter_source; ?>" data-lang="<?= $g->filter_lang; ?>" data-industry="<?= $g->filter_industry_id; ?>" data-verify="<?= $g->filter_verify_status; ?>" data-custom="<?= esc($g->custom_emails); ?>" title="Sửa nhóm"><i class="fa fa-pencil"></i></button>
                                    <form action="<?= adminUrl('newsletter-delete-group-post'); ?>" method="post" style="display:inline-block;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhóm email này?');">
                                        <?= csrf_field(); ?>
                                        <input type="hidden" name="id" value="<?= $g->id; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Xóa nhóm"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted" style="padding: 40px;">
                                    <i class="fa fa-users fa-3x" style="color: #cbd5e1; margin-bottom: 12px; display: block;"></i>
                                    Chưa có nhóm email nào được tạo. Hãy bấm <strong>+ Tạo Nhóm Email Mới</strong> để bắt đầu phân loại đối tượng nhận tin.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add/Edit Group -->
<div class="modal fade" id="modalGroupForm" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 10px; overflow: hidden;">
            <form action="<?= adminUrl('newsletter-save-group-post'); ?>" method="post" id="formSaveGroup">
                <?= csrf_field(); ?>
                <input type="hidden" name="id" id="groupIdInput">

                <div class="modal-header" style="background: #2563eb; color: #fff;">
                    <button type="button" class="close" data-dismiss="modal" style="color:#fff;">&times;</button>
                    <h4 class="modal-title" id="modalGroupTitle"><i class="fa fa-users"></i> Tạo Nhóm Email & Phân Khúc Khách Hàng</h4>
                </div>

                <div class="modal-body" style="padding: 22px;">
                    <div class="form-group">
                        <label>Tên Nhóm Email: <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="name" id="groupNameInput" required class="form-control input-lg" placeholder="VD: Doanh Nghiệp Logistics Tiếng Việt - Đã Xác Thực">
                    </div>

                    <div class="form-group">
                        <label>Mô Tả / Ghi Chú Nhóm:</label>
                        <textarea name="description" id="groupDescInput" class="form-control" rows="2" placeholder="VD: Dành cho các chiến dịch quảng bá cước biển FCL và ưu đãi tháng 8..."></textarea>
                    </div>

                    <h4 style="font-weight: 800; color: #0f172a; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px; margin: 20px 0 15px 0;">
                        <i class="fa fa-filter text-primary"></i> Thiết Lập Bộ Lọc Đối Tượng (Dynamic Filters)
                    </h4>

                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label>1. Nguồn Đối Tượng (Audience Source):</label>
                                <select name="filter_source" id="groupFilterSource" class="form-control">
                                    <option value="all" selected>Tất cả nguồn (Đối tác + Subscribers + Users)</option>
                                    <option value="members">Chỉ Đối tác Doanh nghiệp TOP BEST GLOBAL</option>
                                    <option value="subscribers">Chỉ Khách đăng ký nhận bản tin (Subscribers)</option>
                                    <option value="users">Chỉ Người dùng tài khoản hệ thống (Users)</option>
                                    <option value="custom">Chỉ Danh sách email tự nhập thủ công</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label>2. Ngôn Ngữ Khách Hàng (Language Preference):</label>
                                <select name="filter_lang" id="groupFilterLang" class="form-control">
                                    <option value="all" selected>Mọi ngôn ngữ (Không phân biệt)</option>
                                    <option value="vi">🇻🇳 Ưu tiên nhận Tiếng Việt (Vietnamese)</option>
                                    <option value="en">🇬🇧 Ưu tiên nhận Tiếng Anh (English - International)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label>3. Ngành Nghề Hoạt Động (Dành cho Đối tác):</label>
                                <select name="filter_industry_id" id="groupFilterIndustry" class="form-control">
                                    <option value="0" selected>Tất cả các ngành nghề</option>
                                    <?php if (!empty($industries)): foreach ($industries as $ind): ?>
                                        <option value="<?= $ind->id; ?>"><?= esc($ind->name); ?></option>
                                    <?php endforeach; endif; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label>4. Trạng Thái Xác Thực (Dành cho Đối tác):</label>
                                <select name="filter_verify_status" id="groupFilterVerify" class="form-control">
                                    <option value="all" selected>Tất cả (Đã xác thực & Chờ duyệt)</option>
                                    <option value="verified">Chỉ Đối tác đã xác thực (Verified)</option>
                                    <option value="pending">Chỉ Đối tác đang chờ duyệt (Pending)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>5. Danh Sách Email Bổ Sung Thủ Công (Tùy chọn):</label>
                        <textarea name="custom_emails" id="groupCustomEmails" class="form-control" rows="3" placeholder="Nhập mỗi dòng một khách hàng theo định dạng: email, Tên đại diện, Tên công ty&#10;VD: partner1@global.com, John Doe, Global Logistics LLC&#10;partner2@xnk.vn, Nguyễn Văn B, Công ty TNHH Vận Tải X"></textarea>
                        <small style="color: #64748b; font-size: 11px;">Hệ thống sẽ tự động lọc trùng email và gộp chung vào nhóm này.</small>
                    </div>

                    <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 14px; display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                        <div>
                            <span style="font-weight: 700; color: #0f172a;">Ước tính số lượng thỏa mãn:</span>
                            <span id="liveGroupCount" class="badge bg-green" style="font-size: 14px; margin-left: 6px;">Đang tính...</span>
                        </div>
                        <button type="button" id="btnTestFilter" class="btn btn-default btn-sm" style="font-weight: 700;">
                            <i class="fa fa-refresh"></i> Kiểm Tra Số Lượng Thỏa Mãn
                        </button>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary" style="font-weight: 700; padding: 8px 24px;">Lưu Nhóm Email</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Preview Contacts -->
<div class="modal fade" id="modalPreviewContacts" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 10px; overflow: hidden;">
            <div class="modal-header" style="background: #0f172a; color: #fff;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;">&times;</button>
                <h4 class="modal-title"><i class="fa fa-users text-primary"></i> Xem Trước Danh Sách Khách Hàng Trong Nhóm</h4>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <p><strong>Tổng số email thỏa mãn bộ lọc:</strong> <span id="previewTotalBadge" class="badge bg-green" style="font-size: 14px;">0</span></p>
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-bordered table-striped" id="tablePreviewSample" style="font-size: 13px;">
                        <thead>
                            <tr style="background: #f8fafc;">
                                <th>#</th>
                                <th>Email</th>
                                <th>Tên Đại Diện</th>
                                <th>Doanh Nghiệp / Thương Hiệu</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#btnOpenAddGroupModal').on('click', function() {
        $('#groupIdInput').val('');
        $('#groupNameInput').val('');
        $('#groupDescInput').val('');
        $('#groupFilterSource').val('all');
        $('#groupFilterLang').val('all');
        $('#groupFilterIndustry').val('0');
        $('#groupFilterVerify').val('all');
        $('#groupCustomEmails').val('');
        $('#modalGroupTitle').html('<i class="fa fa-users"></i> Tạo Nhóm Email & Phân Khúc Khách Hàng');
        $('#modalGroupForm').modal('show');
        checkLiveCount();
    });

    $(document).on('click', '.btn-edit-group', function() {
        $('#groupIdInput').val($(this).attr('data-id'));
        $('#groupNameInput').val($(this).attr('data-name'));
        $('#groupDescInput').val($(this).attr('data-desc'));
        $('#groupFilterSource').val($(this).attr('data-source'));
        $('#groupFilterLang').val($(this).attr('data-lang'));
        $('#groupFilterIndustry').val($(this).attr('data-industry'));
        $('#groupFilterVerify').val($(this).attr('data-verify'));
        $('#groupCustomEmails').val($(this).attr('data-custom'));
        $('#modalGroupTitle').html('<i class="fa fa-pencil"></i> Chỉnh Sửa Nhóm Email');
        $('#modalGroupForm').modal('show');
        checkLiveCount();
    });

    $('#groupFilterSource, #groupFilterLang, #groupFilterIndustry, #groupFilterVerify').on('change', function() {
        checkLiveCount();
    });
    $('#btnTestFilter').on('click', function() { checkLiveCount(); });

    function checkLiveCount() {
        $('#liveGroupCount').text('Đang tính...');
        $.ajax({
            type: "POST",
            url: VrConfig.baseURL + '/admin/newsletter-preview-group-ajax',
            data: setAjaxData({
                filter_source: $('#groupFilterSource').val(),
                filter_lang: $('#groupFilterLang').val(),
                filter_industry_id: $('#groupFilterIndustry').val(),
                filter_verify_status: $('#groupFilterVerify').val(),
                custom_emails: $('#groupCustomEmails').val()
            }),
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    $('#liveGroupCount').text(res.total + ' liên hệ');
                }
            }
        });
    }

    $(document).on('click', '.btn-preview-group', function() {
        var source = $(this).attr('data-source');
        var lang = $(this).attr('data-lang');
        var industry = $(this).attr('data-industry');
        var verify = $(this).attr('data-verify');
        var custom = $(this).attr('data-custom');

        $.ajax({
            type: "POST",
            url: VrConfig.baseURL + '/admin/newsletter-preview-group-ajax',
            data: setAjaxData({
                filter_source: source,
                filter_lang: lang,
                filter_industry_id: industry,
                filter_verify_status: verify,
                custom_emails: custom
            }),
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    $('#previewTotalBadge').text(res.total + ' liên hệ');
                    var html = '';
                    if (res.sample && res.sample.length > 0) {
                        for (var i = 0; i < res.sample.length; i++) {
                            var item = res.sample[i];
                            html += '<tr><td>' + (i + 1) + '</td><td><strong>' + item.email + '</strong></td><td>' + item.name + '</td><td>' + item.company + '</td></tr>';
                        }
                    } else {
                        html = '<tr><td colspan="4" class="text-center text-muted">Không có liên hệ nào thỏa mãn bộ lọc.</td></tr>';
                    }
                    $('#tablePreviewSample tbody').html(html);
                    $('#modalPreviewContacts').modal('show');
                }
            }
        });
    });
});
</script>
