<?php
$isEdit = !empty($member);
$actionUrl = $isEdit ? adminUrl('members/edit-post/' . $member->id) : adminUrl('members/add-post');
$existingContacts = $isEdit ? ($member->contacts ?? []) : [];
$existingBranches = $isEdit ? ($member->branches ?? []) : [];
?>

<style>
    .repeater-card { background: #fafbfc; border: 1px solid #e1e8ed; border-radius: 6px; padding: 12px 15px; margin-bottom: 12px; position: relative; }
    .repeater-card:hover { border-color: #cbd6e2; background: #fdfdfd; }
    .btn-remove-row { position: absolute; top: 10px; right: 10px; color: #d9534f; cursor: pointer; font-size: 16px; }
    .btn-remove-row:hover { color: #c9302c; }
    .section-box-header { font-size: 14px; font-weight: 700; border-bottom: 2px solid #3c8dbc; padding-bottom: 5px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; }
</style>

<div class="row">
    <div class="col-lg-11 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title">
                        <i class="<?= $isEdit ? 'fa fa-edit' : 'fa fa-plus-circle'; ?> text-primary"></i>
                        <?= esc($title); ?>
                    </h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('members'); ?>" class="btn btn-default btn-sm">
                        <i class="fa fa-bars"></i> Danh sách đối tác
                    </a>
                </div>
            </div>

            <form action="<?= $actionUrl; ?>" method="post" enctype="multipart/form-data" id="memberForm">
                <?= csrf_field(); ?>

                <div class="box-body">
                    <?= view('admin/includes/_messages'); ?>

                    <!-- SECTION 1: Company Profile (3 Languages) -->
                    <div class="section-box-header" style="color: #3c8dbc; border-color: #3c8dbc;">
                        <span><i class="fa fa-building-o"></i> 1. Thông Tin Doanh Nghiệp (Đa Ngôn Ngữ)</span>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Tên Công Ty (Tiếng Việt) <span class="text-danger">*</span></label>
                                <input type="text" name="company_name" class="form-control" placeholder="Tên công ty tiếng Việt" value="<?= old('company_name', $isEdit ? ($member->company_name ?? '') : ''); ?>" maxlength="255" required style="font-weight: 600;">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Tên Tiếng Anh (English Name)</label>
                                <input type="text" name="company_name_en" class="form-control" placeholder="English company name" value="<?= old('company_name_en', $isEdit ? ($member->company_name_en ?? '') : ''); ?>" maxlength="255">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Tên Ngôn Ngữ Gốc (Local / CN / JP / KR)</label>
                                <input type="text" name="company_name_local" class="form-control" placeholder="Tên tiếng Trung, Nhật, Hàn..." value="<?= old('company_name_local', $isEdit ? ($member->company_name_local ?? '') : ''); ?>" maxlength="255">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Mã Số Thuế (MST)</label>
                                <input type="text" name="tax_code" class="form-control" placeholder="0101234567" value="<?= old('tax_code', $isEdit ? ($member->tax_code ?? '') : ''); ?>" maxlength="50">
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Ngôn Ngữ Nhận Diện</label>
                                <?php $detLang = old('detected_language', $isEdit ? ($member->detected_language ?? 'vi') : 'vi'); ?>
                                <select name="detected_language" class="form-control">
                                    <option value="vi" <?= $detLang === 'vi' ? 'selected' : ''; ?>>Tiếng Việt (vi)</option>
                                    <option value="en" <?= $detLang === 'en' ? 'selected' : ''; ?>>English (en)</option>
                                    <option value="zh" <?= $detLang === 'zh' ? 'selected' : ''; ?>>Chinese (zh)</option>
                                    <option value="ja" <?= $detLang === 'ja' ? 'selected' : ''; ?>>Japanese (ja)</option>
                                    <option value="ko" <?= $detLang === 'ko' ? 'selected' : ''; ?>>Korean (ko)</option>
                                    <option value="other" <?= $detLang === 'other' ? 'selected' : ''; ?>>Ngôn ngữ khác</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Ngành Nghề Kinh Doanh</label>
                                <select name="industry_type_id" class="form-control select2" style="width: 100%;">
                                    <option value="">-- Chọn ngành nghề --</option>
                                    <?php if (!empty($industries)): ?>
                                        <?php foreach ($industries as $ind): ?>
                                            <?php $selected = old('industry_type_id', $isEdit ? ($member->industry_type_id ?? '') : '') == $ind->id ? 'selected' : ''; ?>
                                            <option value="<?= $ind->id; ?>" <?= $selected; ?>><?= esc($ind->name); ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Phân Loại Đối Tác</label>
                                <?php $mType = old('member_type', $isEdit ? ($member->member_type ?? 'member') : 'member'); ?>
                                <select name="member_type" class="form-control">
                                    <option value="member" <?= $mType === 'member' ? 'selected' : ''; ?>>Chính thức</option>
                                    <option value="prospect" <?= $mType === 'prospect' ? 'selected' : ''; ?>>Tiềm năng</option>
                                    <option value="partner" <?= $mType === 'partner' ? 'selected' : ''; ?>>Đối tác chiến lược</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="control-label">Địa Chỉ Trụ Sở</label>
                                <input type="text" name="address" class="form-control" placeholder="Số nhà, đường, phường/xã..." value="<?= old('address', $isEdit ? ($member->address ?? '') : ''); ?>" maxlength="255">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">Tỉnh / Thành Phố</label>
                                <input type="text" name="city" class="form-control" placeholder="Hà Nội, TP.HCM..." value="<?= old('city', $isEdit ? ($member->city ?? '') : ''); ?>" maxlength="100">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">Website</label>
                                <input type="text" name="website" class="form-control" placeholder="https://company.vn" value="<?= old('website', $isEdit ? ($member->website ?? '') : ''); ?>" maxlength="255">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">Fanpage</label>
                                <input type="text" name="fanpage" class="form-control" placeholder="https://fb.com/page" value="<?= old('fanpage', $isEdit ? ($member->fanpage ?? '') : ''); ?>" maxlength="255">
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: Dynamic Contacts Repeater -->
                    <div class="section-box-header" style="color: #00a65a; border-color: #00a65a; margin-top: 20px;">
                        <span><i class="fa fa-users"></i> 2. Danh Bạ Người Liên Hệ (Đa Nhân Sự)</span>
                        <button type="button" class="btn btn-xs btn-success" id="btnAddContact"><i class="fa fa-plus"></i> Thêm Người Liên Hệ</button>
                    </div>

                    <div id="contactsContainer">
                        <?php if (!empty($existingContacts)): ?>
                            <?php foreach ($existingContacts as $cIdx => $cnt): ?>
                                <div class="repeater-card contact-row" data-index="<?= $cIdx; ?>">
                                    <span class="btn-remove-row remove-contact" title="Xoá người liên hệ này"><i class="fa fa-times-circle"></i></span>
                                    <input type="hidden" name="contacts[<?= $cIdx; ?>][id]" value="<?= esc($cnt->id ?? ''); ?>">
                                    <div class="row">
                                        <div class="col-md-3 col-sm-6">
                                            <div class="form-group" style="margin-bottom: 8px;">
                                                <label style="font-size: 11px;">Họ và Tên <span class="text-danger">*</span></label>
                                                <input type="text" name="contacts[<?= $cIdx; ?>][full_name]" class="form-control input-sm" value="<?= esc($cnt->full_name ?? ''); ?>" placeholder="Họ và tên" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="form-group" style="margin-bottom: 8px;">
                                                <label style="font-size: 11px;">Chức Vụ</label>
                                                <input type="text" name="contacts[<?= $cIdx; ?>][position]" class="form-control input-sm" value="<?= esc($cnt->position ?? ''); ?>" placeholder="Giám đốc, Sale...">
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-6">
                                            <div class="form-group" style="margin-bottom: 8px;">
                                                <label style="font-size: 11px;">Số Điện Thoại</label>
                                                <input type="text" name="contacts[<?= $cIdx; ?>][phone]" class="form-control input-sm" value="<?= esc($cnt->phone ?? ''); ?>" placeholder="0901234567">
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-6">
                                            <div class="form-group" style="margin-bottom: 8px;">
                                                <label style="font-size: 11px;">Email Liên Hệ</label>
                                                <input type="email" name="contacts[<?= $cIdx; ?>][email]" class="form-control input-sm" value="<?= esc($cnt->email ?? ''); ?>" placeholder="contact@domain.com">
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-12" style="padding-top: 22px;">
                                            <label style="font-size: 12px; font-weight: normal; cursor: pointer;">
                                                <input type="radio" name="primary_contact_radio" value="<?= $cIdx; ?>" <?= !empty($cnt->is_primary) ? 'checked' : ''; ?> class="radio-is-primary">
                                                <input type="hidden" name="contacts[<?= $cIdx; ?>][is_primary]" value="<?= !empty($cnt->is_primary) ? 1 : 0; ?>" class="val-is-primary">
                                                <strong>Đại diện chính</strong>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="repeater-card contact-row" data-index="0">
                                <span class="btn-remove-row remove-contact" title="Xoá người liên hệ này"><i class="fa fa-times-circle"></i></span>
                                <div class="row">
                                    <div class="col-md-3 col-sm-6">
                                        <div class="form-group" style="margin-bottom: 8px;">
                                            <label style="font-size: 11px;">Họ và Tên <span class="text-danger">*</span></label>
                                            <input type="text" name="contacts[0][full_name]" class="form-control input-sm" value="<?= old('representative_name', $isEdit ? ($member->representative_name ?? '') : ''); ?>" placeholder="Họ và tên" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6">
                                        <div class="form-group" style="margin-bottom: 8px;">
                                            <label style="font-size: 11px;">Chức Vụ</label>
                                            <input type="text" name="contacts[0][position]" class="form-control input-sm" value="<?= old('position', $isEdit ? ($member->position ?? '') : ''); ?>" placeholder="Giám đốc, Sale...">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-6">
                                        <div class="form-group" style="margin-bottom: 8px;">
                                            <label style="font-size: 11px;">Số Điện Thoại</label>
                                            <input type="text" name="contacts[0][phone]" class="form-control input-sm" value="<?= old('phone', $isEdit ? ($member->phone ?? '') : ''); ?>" placeholder="0901234567">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-6">
                                        <div class="form-group" style="margin-bottom: 8px;">
                                            <label style="font-size: 11px;">Email Liên Hệ</label>
                                            <input type="email" name="contacts[0][email]" class="form-control input-sm" value="<?= old('email', $isEdit ? ($member->email ?? '') : ''); ?>" placeholder="contact@domain.com">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-12" style="padding-top: 22px;">
                                        <label style="font-size: 12px; font-weight: normal; cursor: pointer;">
                                            <input type="radio" name="primary_contact_radio" value="0" checked class="radio-is-primary">
                                            <input type="hidden" name="contacts[0][is_primary]" value="1" class="val-is-primary">
                                            <strong>Đại diện chính</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- SECTION 3: Dynamic Branches Repeater -->
                    <div class="section-box-header" style="color: #00c0ef; border-color: #00c0ef; margin-top: 20px;">
                        <span><i class="fa fa-map-marker"></i> 3. Mạng Lưới Chi Nhánh & Văn Phòng</span>
                        <button type="button" class="btn btn-xs btn-info" id="btnAddBranch"><i class="fa fa-plus"></i> Thêm Chi Nhánh</button>
                    </div>

                    <div id="branchesContainer">
                        <?php if (!empty($existingBranches)): ?>
                            <?php foreach ($existingBranches as $bIdx => $br): ?>
                                <div class="repeater-card branch-row" data-index="<?= $bIdx; ?>">
                                    <span class="btn-remove-row remove-branch" title="Xoá chi nhánh này"><i class="fa fa-times-circle"></i></span>
                                    <input type="hidden" name="branches[<?= $bIdx; ?>][id]" value="<?= esc($br->id ?? ''); ?>">
                                    <div class="row">
                                        <div class="col-md-3 col-sm-6">
                                            <div class="form-group" style="margin-bottom: 8px;">
                                                <label style="font-size: 11px;">Tên Chi Nhánh <span class="text-danger">*</span></label>
                                                <input type="text" name="branches[<?= $bIdx; ?>][branch_name]" class="form-control input-sm" value="<?= esc($br->branch_name ?? ''); ?>" placeholder="Văn phòng Hà Nội..." required>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-6">
                                            <div class="form-group" style="margin-bottom: 8px;">
                                                <label style="font-size: 11px;">Thành Phố / Tỉnh</label>
                                                <input type="text" name="branches[<?= $bIdx; ?>][city]" class="form-control input-sm" value="<?= esc($br->city ?? ''); ?>" placeholder="Hà Nội, Hải Phòng...">
                                                <input type="hidden" name="branches[<?= $bIdx; ?>][country]" value="<?= esc($br->country ?? 'Việt Nam'); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-12">
                                            <div class="form-group" style="margin-bottom: 8px;">
                                                <label style="font-size: 11px;">Địa Chỉ Chi Tiết</label>
                                                <input type="text" name="branches[<?= $bIdx; ?>][address]" class="form-control input-sm" value="<?= esc($br->address ?? ''); ?>" placeholder="Số nhà, đường...">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-12" style="padding-top: 22px;">
                                            <label style="font-size: 12px; font-weight: normal; cursor: pointer;">
                                                <input type="checkbox" name="branches[<?= $bIdx; ?>][is_headquarters]" value="1" <?= !empty($br->is_headquarters) ? 'checked' : ''; ?>>
                                                <strong>Trụ sở chính (HQ)</strong>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- SECTION 4: Cards & Notes -->
                    <div class="section-box-header" style="color: #605ca8; border-color: #605ca8; margin-top: 20px;">
                        <span><i class="fa fa-id-card-o"></i> 4. Đính Kèm Danh Thiếp & Ghi Chú</span>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label"><i class="fa fa-id-card text-muted"></i> Upload Thêm Danh Thiếp (Tùy chọn)</label>
                                <input type="file" name="card_image" class="form-control" accept="image/jpeg, image/png, image/webp">
                                <small class="text-muted">Hỗ trợ JPG, PNG, WEBP (tối đa 10MB).</small>
                            </div>
                            <?php if ($isEdit && !empty($member->cards)): ?>
                                <div class="form-group">
                                    <label style="font-size: 12px;">Ảnh danh thiếp hiện có:</label>
                                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                        <?php foreach ($member->cards as $card): ?>
                                            <div style="border: 1px solid #ddd; padding: 3px; border-radius: 4px; background: #fafafa;">
                                                <img src="<?= base_url($card->file_path); ?>" style="height: 50px; width: 80px; object-fit: cover; border-radius: 2px;">
                                                <div style="font-size: 10px; text-align: center; color: #666; margin-top: 2px;"><?= esc($card->side); ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Ghi Chú Nội Bộ</label>
                                <textarea name="note" class="form-control" rows="3" placeholder="Ghi chú về nhu cầu, sự kiện kết nối..."><?= old('note', $isEdit ? ($member->note ?? '') : ''); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box-footer text-right" style="background: #f9fafc; border-top: 1px solid #e1e8ed; padding: 15px;">
                    <a href="<?= adminUrl('members'); ?>" class="btn btn-default btn-lg" style="margin-right: 5px;">
                        <i class="fa fa-ban"></i> Huỷ bỏ
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg" style="font-weight: 600; min-width: 160px;">
                        <i class="fa fa-check"></i> <?= $isEdit ? 'Cập Nhật Đối Tác' : 'Lưu Đối Tác'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    var contactIndex = <?= max(1, count($existingContacts)); ?>;
    var branchIndex = <?= max(0, count($existingBranches)); ?>;

    $('#btnAddContact').on('click', function () {
        var html = '<div class="repeater-card contact-row" data-index="' + contactIndex + '">' +
            '<span class="btn-remove-row remove-contact" title="Xoá người liên hệ này"><i class="fa fa-times-circle"></i></span>' +
            '<div class="row">' +
            '<div class="col-md-3 col-sm-6">' +
            '<div class="form-group" style="margin-bottom: 8px;">' +
            '<label style="font-size: 11px;">Họ và Tên <span class="text-danger">*</span></label>' +
            '<input type="text" name="contacts[' + contactIndex + '][full_name]" class="form-control input-sm" placeholder="Họ và tên" required>' +
            '</div></div>' +
            '<div class="col-md-3 col-sm-6">' +
            '<div class="form-group" style="margin-bottom: 8px;">' +
            '<label style="font-size: 11px;">Chức Vụ</label>' +
            '<input type="text" name="contacts[' + contactIndex + '][position]" class="form-control input-sm" placeholder="Giám đốc, Sale...">' +
            '</div></div>' +
            '<div class="col-md-2 col-sm-6">' +
            '<div class="form-group" style="margin-bottom: 8px;">' +
            '<label style="font-size: 11px;">Số Điện Thoại</label>' +
            '<input type="text" name="contacts[' + contactIndex + '][phone]" class="form-control input-sm" placeholder="0901234567">' +
            '</div></div>' +
            '<div class="col-md-2 col-sm-6">' +
            '<div class="form-group" style="margin-bottom: 8px;">' +
            '<label style="font-size: 11px;">Email Liên Hệ</label>' +
            '<input type="email" name="contacts[' + contactIndex + '][email]" class="form-control input-sm" placeholder="contact@domain.com">' +
            '</div></div>' +
            '<div class="col-md-2 col-sm-12" style="padding-top: 22px;">' +
            '<label style="font-size: 12px; font-weight: normal; cursor: pointer;">' +
            '<input type="radio" name="primary_contact_radio" value="' + contactIndex + '" class="radio-is-primary">' +
            '<input type="hidden" name="contacts[' + contactIndex + '][is_primary]" value="0" class="val-is-primary"> ' +
            '<strong>Đại diện chính</strong>' +
            '</label></div></div></div>';
        $('#contactsContainer').append(html);
        contactIndex++;
    });

    $('#btnAddBranch').on('click', function () {
        var html = '<div class="repeater-card branch-row" data-index="' + branchIndex + '">' +
            '<span class="btn-remove-row remove-branch" title="Xoá chi nhánh này"><i class="fa fa-times-circle"></i></span>' +
            '<div class="row">' +
            '<div class="col-md-3 col-sm-6">' +
            '<div class="form-group" style="margin-bottom: 8px;">' +
            '<label style="font-size: 11px;">Tên Chi Nhánh <span class="text-danger">*</span></label>' +
            '<input type="text" name="branches[' + branchIndex + '][branch_name]" class="form-control input-sm" placeholder="Văn phòng Hà Nội..." required>' +
            '</div></div>' +
            '<div class="col-md-2 col-sm-6">' +
            '<div class="form-group" style="margin-bottom: 8px;">' +
            '<label style="font-size: 11px;">Thành Phố / Tỉnh</label>' +
            '<input type="text" name="branches[' + branchIndex + '][city]" class="form-control input-sm" placeholder="Hà Nội, Hải Phòng...">' +
            '<input type="hidden" name="branches[' + branchIndex + '][country]" value="Việt Nam">' +
            '</div></div>' +
            '<div class="col-md-4 col-sm-12">' +
            '<div class="form-group" style="margin-bottom: 8px;">' +
            '<label style="font-size: 11px;">Địa Chỉ Chi Tiết</label>' +
            '<input type="text" name="branches[' + branchIndex + '][address]" class="form-control input-sm" placeholder="Số nhà, đường...">' +
            '</div></div>' +
            '<div class="col-md-3 col-sm-12" style="padding-top: 22px;">' +
            '<label style="font-size: 12px; font-weight: normal; cursor: pointer;">' +
            '<input type="checkbox" name="branches[' + branchIndex + '][is_headquarters]" value="1"> ' +
            '<strong>Trụ sở chính (HQ)</strong>' +
            '</label></div></div></div>';
        $('#branchesContainer').append(html);
        branchIndex++;
    });

    $(document).on('click', '.remove-contact', function () {
        if ($('.contact-row').length > 1) {
            $(this).closest('.contact-row').remove();
        } else {
            alert('Doanh nghiệp cần có ít nhất một người liên hệ.');
        }
    });

    $(document).on('click', '.remove-branch', function () {
        $(this).closest('.branch-row').remove();
    });

    $(document).on('change', '.radio-is-primary', function () {
        $('.val-is-primary').val(0);
        $(this).closest('.contact-row').find('.val-is-primary').val(1);
    });
});
</script>
