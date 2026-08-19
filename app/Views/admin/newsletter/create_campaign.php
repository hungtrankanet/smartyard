<div class="row">
    <div class="col-sm-12 title-section" style="margin-bottom: 20px;">
        <h3 style="margin: 0; font-size: 22px; font-weight: 800; color: #0f172a;">
            <i class="fa fa-plus-circle text-green"></i> Tạo Chiến Dịch Email Marketing Mới
        </h3>
        <p style="color: #64748b; margin: 4px 0 0; font-size: 13px;">Thiết lập thông tin chiến dịch và lựa chọn các bài viết nổi bật để tự động tổng hợp vào bản tin gửi khách hàng.</p>
    </div>
</div>

<form action="<?= adminUrl('newsletter-create-campaign-post'); ?>" method="post" id="formCreateCampaign">
    <?= csrf_field(); ?>

    <!-- Step 1: Campaign Basic Info -->
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-primary" style="border-radius: 10px; box-shadow: 0 4px 16px rgba(0,0,0,0.04);">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-info-circle text-primary"></i> 1. Thông Tin Chiến Dịch & Đối Tượng Nhận</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label>Tên Chiến Dịch Nội Bộ: <span style="color:#ef4444;">*</span></label>
                                <input type="text" id="campaignTitleInput" name="title" required class="form-control input-lg" placeholder="VD: Bản Tin Doanh Nghiệp Tuần 34 - Biến Động Cước Biển" value="<?= esc($defaultTitle ?? ('Bản Tin Doanh Nghiệp TOP BEST GLOBAL - ' . date('d/m/Y'))); ?>">
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label>Tiêu Đề Email Gửi Khách Hàng (Subject): <span style="color:#ef4444;">*</span></label>
                                <input type="text" id="campaignSubjectInput" name="subject" required class="form-control input-lg" placeholder="VD: [TOP BEST GLOBAL] Cập Nhật Tin Tức & Xu Hướng Thị Trường Mới Nhất" value="<?= esc($defaultSubject ?? '[TOP BEST GLOBAL] Cập Nhật Tin Tức & Xu Hướng Thị Trường Mới Nhất'); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label>Nhóm Đối Tượng Nhận Email (Phân Loại):</label>
                                <select name="recipient_type" class="form-control input-lg">
                                    <?php if (!empty($emailGroups)): ?>
                                        <optgroup label="📁 Nhóm Email Đã Tạo">
                                            <?php foreach ($emailGroups as $eg): ?>
                                                <option value="group_<?= $eg->id; ?>">📁 <?= esc($eg->name); ?> (<?= (int)$eg->total_contacts; ?> liên hệ)</option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endif; ?>
                                    <optgroup label="🌐 Nhóm Hệ Thống Mặc Định">
                                        <option value="all" selected>1. Tất cả đối tác & khách hàng (Toàn bộ hệ thống)</option>
                                        <option value="members_vi">2. Đối tác TOP BEST GLOBAL nhận Tiếng Việt (VN Language)</option>
                                        <option value="members_en">3. Đối tác TOP BEST GLOBAL nhận Tiếng Anh (English)</option>
                                        <option value="members">4. Tất cả Đối tác Doanh nghiệp TOP BEST GLOBAL</option>
                                        <option value="subscribers">5. Khách đăng ký nhận bản tin (Subscribers)</option>
                                        <option value="users">6. Người dùng đăng ký tài khoản (Users)</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label>Ngôn Ngữ Bản Tin & Tin Tức:</label>
                                <input type="hidden" name="lang_id" value="<?= $selectedLangId; ?>">
                                <select id="filterLangId" class="form-control" onchange="switchCampaignLang(this.value)">
                                    <?php if (!empty($languages)): ?>
                                        <?php foreach ($languages as $l): ?>
                                            <option value="<?= $l->id; ?>" <?= ($selectedLangId == $l->id) ? 'selected' : ''; ?> data-short="<?= $l->short_form; ?>"><?= esc($l->name); ?> (<?= strtoupper($l->short_form); ?>)</option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="1" <?= $selectedLangId == 1 ? 'selected' : ''; ?> data-short="vi">Tiếng Việt (VI)</option>
                                        <option value="2" <?= $selectedLangId == 2 ? 'selected' : ''; ?> data-short="en">English (EN)</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 2: Choose Articles from Dynamic List -->
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-success" style="border-radius: 10px; box-shadow: 0 4px 16px rgba(0,0,0,0.04);">
                <div class="box-header with-border" style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 class="box-title"><i class="fa fa-newspaper-o text-green"></i> 2. Chọn Danh Sách Tin Tức Đưa Vào Email (Tối đa 10 tin)</h3>
                    <span id="selectedCountBadge" class="label label-primary" style="font-size: 13px; font-weight: 700; padding: 6px 12px; border-radius: 20px;">
                        Đã chọn: <span id="selectedCountNum">0</span> / 10 tin
                    </span>
                </div>
                <div class="box-body">
                    <?php if (!empty($posts)): ?>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 16px; max-height: 550px; overflow-y: auto; padding: 8px;">
                            <?php 
                            $defaultChecked = 0;
                            foreach ($posts as $p): 
                                $postImg = getPostImage($p, 'mid');
                                $isChecked = ($defaultChecked < 4);
                                if ($isChecked) $defaultChecked++;
                            ?>
                                <label class="post-select-card <?= $isChecked ? 'selected' : ''; ?>" style="border: 2px solid <?= $isChecked ? '#2563eb' : '#e2e8f0'; ?>; border-radius: 10px; padding: 14px; background: #fff; cursor: pointer; display: flex; gap: 12px; transition: all 0.2s; position: relative;">
                                    <input type="checkbox" name="post_ids[]" value="<?= $p->id; ?>" class="post-checkbox" <?= $isChecked ? 'checked' : ''; ?> style="position: absolute; top: 12px; right: 12px; width: 18px; height: 18px;">
                                    
                                    <div style="width: 90px; height: 75px; flex-shrink: 0; border-radius: 6px; overflow: hidden; background: #eff6ff;">
                                        <?php if (!empty($postImg)): ?>
                                            <img src="<?= $postImg; ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <div style="display:flex; align-items:center; justify-content:center; height:100%; color:#2563eb;"><i class="fa fa-newspaper-o fa-2x"></i></div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div style="flex: 1; padding-right: 20px;">
                                        <span class="label label-info" style="font-size: 10px;"><?= esc($p->category_name ?? 'Tin tức'); ?></span>
                                        <span style="font-size: 11px; color: #64748b; margin-left: 4px;"><?= date('d/m/Y', strtotime($p->created_at)); ?></span>
                                        <h4 style="font-size: 13px; font-weight: 800; margin: 4px 0 2px; color: #0f172a; line-height: 1.35; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            <?= esc($p->title); ?>
                                        </h4>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-center text-muted" style="padding: 40px; font-size: 14px;">
                            <i class="fa fa-info-circle fa-2x" style="display:block; margin-bottom: 8px; color: #94a3b8;"></i>
                            Chưa có bài viết nào thuộc ngôn ngữ này. Vui lòng chọn ngôn ngữ khác hoặc thêm bài viết mới.
                        </p>
                    <?php endif; ?>
                </div>

                <div class="box-footer" style="padding: 18px; display: flex; justify-content: space-between; align-items: center;">
                    <a href="<?= adminUrl('newsletter'); ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Quay lại</a>
                    <button type="submit" class="btn btn-success btn-lg" style="font-weight: 800; padding: 10px 30px; border-radius: 8px;">
                        <i class="fa fa-arrow-right"></i> Lưu & Tiếp Tục Sang Màn Hình Gửi Email
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function switchCampaignLang(langId) {
    window.location.href = '<?= adminUrl('newsletter-create-campaign'); ?>?lang=' + langId;
}

$(document).ready(function() {
    updateCount();

    $('.post-checkbox').on('change', function() {
        var count = $('.post-checkbox:checked').length;
        if (count > 10) {
            alert('Bạn chỉ có thể chọn tối đa 10 bài viết cho một chiến dịch email.');
            $(this).prop('checked', false);
            return;
        }
        if ($(this).is(':checked')) {
            $(this).closest('.post-select-card').addClass('selected').css('border-color', '#2563eb');
        } else {
            $(this).closest('.post-select-card').removeClass('selected').css('border-color', '#e2e8f0');
        }
        updateCount();
    });

    function updateCount() {
        var count = $('.post-checkbox:checked').length;
        $('#selectedCountNum').text(count);
    }
});
</script>
