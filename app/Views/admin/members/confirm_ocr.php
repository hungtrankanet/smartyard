<?php
$groupedData = $groupedData ?? [];
$industryTypes = $industryTypes ?? [];
$totalCards = $totalCards ?? 0;
$totalGroups = count($groupedData);
?>

<style>
    .group-card-box { background: #fff; border: 1px solid #d2d6de; border-top: 3px solid #3c8dbc; border-radius: 6px; margin-bottom: 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.05); }
    .group-card-header { padding: 12px 15px; background: #fbfbfc; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
    .group-title { font-size: 16px; font-weight: 700; color: #1e395b; margin: 0; }
    .group-badge { font-size: 11px; padding: 4px 8px; border-radius: 3px; font-weight: 600; }
    .card-thumb-preview { width: 110px; height: 70px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; cursor: pointer; transition: transform .2s; }
    .card-thumb-preview:hover { transform: scale(1.04); box-shadow: 0 2px 8px rgba(0,0,0,0.2); }
    .nested-table th { background: #f4f6f9; font-size: 12px; color: #555; }
    .nested-table td { font-size: 12px; vertical-align: middle !important; }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="callout callout-info" style="background-color: #3c8dbc !important; border-color: #367fa9; border-radius: 6px;">
            <h4 style="font-weight: 600; margin-bottom: 5px;"><i class="fa fa-magic"></i> AI OCR Gom Cụm Thông Minh Hoàn Tất!</h4>
            <p style="margin: 0; font-size: 13px;">
                Đã tự động phân tích <strong><?= $totalCards; ?></strong> ảnh danh thiếp và gom nhóm thành <strong><?= $totalGroups; ?></strong> doanh nghiệp dựa trên Mã Số Thuế, Domain và Tên Công Ty. Vui lòng rà soát trước khi lưu.
            </p>
        </div>

        <form action="<?= adminUrl('members/save-ocr-post'); ?>" method="post" id="formGroupedOcr">
            <?= csrf_field(); ?>

            <?php if (!empty($groupedData)): ?>
                <?php foreach ($groupedData as $gIdx => $group): ?>
                    <?php
                        $c = $group['company_info'] ?? [];
                        $contacts = $group['contacts'] ?? [];
                        $branches = $group['branches'] ?? [];
                        $cards = $group['source_cards'] ?? [];
                        $matchType = $group['match_type'] ?? 'new';
                        $matchScore = $group['match_score'] ?? 1.0;
                        $existingMemberId = $group['existing_member_id'] ?? null;
                    ?>
                    <div class="group-card-box" id="group_box_<?= $gIdx; ?>">
                        <input type="hidden" name="groups[<?= $gIdx; ?>][existing_member_id]" value="<?= esc($existingMemberId); ?>">

                        <!-- Header -->
                        <div class="group-card-header">
                            <div>
                                <span class="badge bg-purple" style="margin-right: 5px;">Đối Tác Chờ Duyệt #<?= $gIdx + 1; ?></span>
                                <span class="group-title"><?= esc($c['company_name'] ?? 'Doanh Nghiệp Chưa Đặt Tên'); ?></span>
                                <?php if (!empty($c['company_name_en'])): ?>
                                    <small class="text-muted" style="margin-left: 5px;">(<?= esc($c['company_name_en']); ?>)</small>
                                <?php endif; ?>
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <?php if ($matchType === 'tax_code'): ?>
                                    <span class="group-badge bg-green"><i class="fa fa-barcode"></i> Khớp MST (100%)</span>
                                <?php elseif ($matchType === 'domain'): ?>
                                    <span class="group-badge bg-aqua"><i class="fa fa-link"></i> Khớp Domain (95%)</span>
                                <?php elseif ($matchType === 'fuzzy_name'): ?>
                                    <span class="group-badge bg-yellow"><i class="fa fa-font"></i> Khớp Tên (<?= round($matchScore * 100); ?>%)</span>
                                <?php elseif (!empty($existingMemberId)): ?>
                                    <span class="group-badge bg-orange"><i class="fa fa-database"></i> Khớp CSDL (#<?= $existingMemberId; ?>)</span>
                                <?php else: ?>
                                    <span class="group-badge bg-gray text-black"><i class="fa fa-plus-circle"></i> Doanh Nghiệp Mới</span>
                                <?php endif; ?>
                                <span class="label label-default"><i class="fa fa-id-card"></i> <?= count($cards); ?> Danh thiếp</span>
                                <button type="button" class="btn btn-xs btn-info" onclick="showGroupJsonModal(<?= $gIdx; ?>)" title="Xem dữ liệu JSON & Text thô trích xuất">
                                    <i class="fa fa-code"></i> Xem JSON
                                </button>
                                <button type="button" class="btn btn-xs btn-danger" onclick="if(confirm('Bạn muốn loại bỏ đối tác này khỏi danh sách duyệt?')) { $('#group_box_<?= $gIdx; ?>').fadeOut(300, function(){ $(this).remove(); }); }" title="Loại bỏ đối tác này">
                                    <i class="fa fa-times"></i> Loại bỏ
                                </button>
                            </div>
                        </div>

                        <div class="box-body" style="padding: 15px;">
                            <!-- Row 1: Company Names in 3 Languages -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label" style="font-size: 12px;">Tên Công Ty (Tiếng Việt) <span class="text-danger">*</span></label>
                                        <input type="text" name="groups[<?= $gIdx; ?>][company_info][company_name]" class="form-control input-sm" value="<?= esc($c['company_name'] ?? ($c['company_name_vi'] ?? '')); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label" style="font-size: 12px;">Tên Tiếng Anh (English)</label>
                                        <input type="text" name="groups[<?= $gIdx; ?>][company_info][company_name_en]" class="form-control input-sm" value="<?= esc($c['company_name_en'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label" style="font-size: 12px;">Tên Ngôn Ngữ Gốc (Local / CN / JP / KR)</label>
                                        <input type="text" name="groups[<?= $gIdx; ?>][company_info][company_name_local]" class="form-control input-sm" value="<?= esc($c['company_name_local'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Row 2: Tax Code, Detected Lang, Industry, Member Type -->
                            <div class="row">
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" style="font-size: 12px;">Mã Số Thuế (MST)</label>
                                        <input type="text" name="groups[<?= $gIdx; ?>][company_info][tax_code]" class="form-control input-sm" value="<?= esc($c['tax_code'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" style="font-size: 12px;">Ngôn Ngữ Nhận Diện</label>
                                        <select name="groups[<?= $gIdx; ?>][company_info][detected_language]" class="form-control input-sm">
                                            <?php $dLang = $c['detected_language'] ?? 'vi'; ?>
                                            <option value="vi" <?= $dLang === 'vi' ? 'selected' : ''; ?>>Tiếng Việt (vi)</option>
                                            <option value="en" <?= $dLang === 'en' ? 'selected' : ''; ?>>English (en)</option>
                                            <option value="zh" <?= $dLang === 'zh' ? 'selected' : ''; ?>>Chinese (zh)</option>
                                            <option value="ja" <?= $dLang === 'ja' ? 'selected' : ''; ?>>Japanese (ja)</option>
                                            <option value="ko" <?= $dLang === 'ko' ? 'selected' : ''; ?>>Korean (ko)</option>
                                            <option value="mixed" <?= $dLang === 'mixed' ? 'selected' : ''; ?>>Đa ngôn ngữ (mixed)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" style="font-size: 12px;">Ngành Nghề <span class="text-danger">*</span></label>
                                        <select name="groups[<?= $gIdx; ?>][company_info][industry_type_id]" class="form-control input-sm select2" style="width: 100%;" required>
                                            <option value="">-- Chọn ngành nghề --</option>
                                            <?php foreach ($industryTypes as $ind): ?>
                                                <?php 
                                                    $indId = is_object($ind) ? $ind->id : $ind['id'];
                                                    $indName = is_object($ind) ? $ind->name : $ind['name'];
                                                    $selected = (!empty($c['industry_type_id']) && $c['industry_type_id'] == $indId) ? 'selected' : '';
                                                ?>
                                                <option value="<?= $indId; ?>" <?= $selected; ?>><?= esc($indName); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" style="font-size: 12px;">Phân Loại Đối Tác</label>
                                        <select name="groups[<?= $gIdx; ?>][company_info][member_type]" class="form-control input-sm">
                                            <option value="member" <?= (($c['member_type'] ?? '') === 'member') ? 'selected' : ''; ?>>Chính thức</option>
                                            <option value="prospect" <?= (($c['member_type'] ?? '') === 'prospect') ? 'selected' : ''; ?>>Tiềm năng</option>
                                            <option value="partner" <?= (($c['member_type'] ?? '') === 'partner') ? 'selected' : ''; ?>>Đối tác chiến lược</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 3: Address, City, Website, Fanpage -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label" style="font-size: 12px;">Địa Chỉ Trụ Sở</label>
                                        <input type="text" name="groups[<?= $gIdx; ?>][company_info][address]" class="form-control input-sm" value="<?= esc($c['address'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label" style="font-size: 12px;">Tỉnh / Thành Phố</label>
                                        <input type="text" name="groups[<?= $gIdx; ?>][company_info][city]" class="form-control input-sm" value="<?= esc($c['city'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label" style="font-size: 12px;">Website</label>
                                        <input type="text" name="groups[<?= $gIdx; ?>][company_info][website]" class="form-control input-sm" value="<?= esc($c['website'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label" style="font-size: 12px;">Fanpage Facebook</label>
                                        <input type="text" name="groups[<?= $gIdx; ?>][company_info][fanpage]" class="form-control input-sm" value="<?= esc($c['fanpage'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Contacts Section -->
                            <div style="margin-top: 10px; margin-bottom: 15px;">
                                <h5 style="font-weight: 700; color: #00a65a; margin-bottom: 8px; border-bottom: 1px solid #e8f5e9; padding-bottom: 4px;">
                                    <i class="fa fa-users"></i> Danh Bạ Người Liên Hệ (<?= count($contacts); ?> Nhân sự)
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-condensed nested-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px; text-align: center;">Chính</th>
                                                <th>Họ và Tên (VI / EN)</th>
                                                <th>Chức Vụ & Phòng Ban</th>
                                                <th>Điện Thoại</th>
                                                <th>Email</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($contacts)): ?>
                                                <?php foreach ($contacts as $cIdx => $cnt): ?>
                                                    <tr>
                                                        <td style="text-align: center;">
                                                            <input type="radio" name="groups[<?= $gIdx; ?>][primary_contact_index]" value="<?= $cIdx; ?>" <?= (!empty($cnt['is_primary']) || $cIdx === 0) ? 'checked' : ''; ?>>
                                                            <input type="hidden" name="groups[<?= $gIdx; ?>][contacts][<?= $cIdx; ?>][is_primary]" value="<?= !empty($cnt['is_primary']) ? 1 : 0; ?>" class="input-is-primary">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="groups[<?= $gIdx; ?>][contacts][<?= $cIdx; ?>][full_name]" class="form-control input-sm" value="<?= esc($cnt['full_name'] ?? ''); ?>" placeholder="Họ tên đầy đủ" required>
                                                            <input type="hidden" name="groups[<?= $gIdx; ?>][contacts][<?= $cIdx; ?>][full_name_en]" value="<?= esc($cnt['full_name_en'] ?? ''); ?>">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="groups[<?= $gIdx; ?>][contacts][<?= $cIdx; ?>][position]" class="form-control input-sm" value="<?= esc($cnt['position'] ?? ''); ?>" placeholder="Chức vụ">
                                                            <input type="hidden" name="groups[<?= $gIdx; ?>][contacts][<?= $cIdx; ?>][department]" value="<?= esc($cnt['department'] ?? ''); ?>">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="groups[<?= $gIdx; ?>][contacts][<?= $cIdx; ?>][phone]" class="form-control input-sm" value="<?= esc($cnt['phone'] ?? ''); ?>" placeholder="SĐT chính">
                                                        </td>
                                                        <td>
                                                            <input type="email" name="groups[<?= $gIdx; ?>][contacts][<?= $cIdx; ?>][email]" class="form-control input-sm" value="<?= esc($cnt['email'] ?? ''); ?>" placeholder="Email liên hệ">
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">Không tìm thấy thông tin cá nhân trên danh thiếp.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Branches Section (if any) -->
                            <?php if (!empty($branches)): ?>
                                <div style="margin-bottom: 15px;">
                                    <h5 style="font-weight: 700; color: #3c8dbc; margin-bottom: 8px; border-bottom: 1px solid #e8f4f8; padding-bottom: 4px;">
                                        <i class="fa fa-map-marker"></i> Chi Nhánh & Văn Phòng (<?= count($branches); ?>)
                                    </h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-condensed nested-table">
                                            <thead>
                                                <tr>
                                                    <th>Tên Chi Nhánh</th>
                                                    <th>Quốc Gia / Thành Phố</th>
                                                    <th>Địa Chỉ</th>
                                                    <th>Loại</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($branches as $bIdx => $br): ?>
                                                    <tr>
                                                        <td>
                                                            <input type="text" name="groups[<?= $gIdx; ?>][branches][<?= $bIdx; ?>][branch_name]" class="form-control input-sm" value="<?= esc($br['branch_name'] ?? ''); ?>">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="groups[<?= $gIdx; ?>][branches][<?= $bIdx; ?>][city]" class="form-control input-sm" value="<?= esc($br['city'] ?? ''); ?>" placeholder="Thành phố">
                                                            <input type="hidden" name="groups[<?= $gIdx; ?>][branches][<?= $bIdx; ?>][country]" value="<?= esc($br['country'] ?? 'Việt Nam'); ?>">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="groups[<?= $gIdx; ?>][branches][<?= $bIdx; ?>][address]" class="form-control input-sm" value="<?= esc($br['address'] ?? ''); ?>">
                                                        </td>
                                                        <td>
                                                            <span class="label <?= !empty($br['is_headquarters']) ? 'label-success' : 'label-default'; ?>">
                                                                <?= !empty($br['is_headquarters']) ? 'Trụ Sở Chính' : 'Chi Nhánh'; ?>
                                                            </span>
                                                            <input type="hidden" name="groups[<?= $gIdx; ?>][branches][<?= $bIdx; ?>][is_headquarters]" value="<?= !empty($br['is_headquarters']) ? 1 : 0; ?>">
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Source Card Images -->
                            <?php if (!empty($cards)): ?>
                                <div>
                                    <label style="font-size: 12px; color: #666;"><i class="fa fa-picture-o"></i> Danh Thiếp Thuộc Nhóm Này (Nhấp để phóng to HD):</label>
                                    <div style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
                                        <?php foreach ($cards as $kIdx => $card): ?>
                                            <?php $cardPath = $card['file_path'] ?? ''; ?>
                                            <?php if (!empty($cardPath)): ?>
                                                <div style="text-align: center;">
                                                    <img src="<?= base_url($cardPath); ?>" alt="Card" class="card-thumb-preview" onclick="showZoomModal('<?= base_url($cardPath); ?>');">
                                                    <div style="font-size: 10px; color: #777; margin-top: 2px;">
                                                        <?= ($card['side'] ?? 'single') === 'front' ? 'Mặt trước' : (($card['side'] ?? 'single') === 'back' ? 'Mặt sau' : 'Thẻ ' . ($kIdx + 1)); ?>
                                                    </div>
                                                    <input type="hidden" name="groups[<?= $gIdx; ?>][cards][<?= $kIdx; ?>][file_path]" value="<?= esc($cardPath); ?>">
                                                    <input type="hidden" name="groups[<?= $gIdx; ?>][cards][<?= $kIdx; ?>][side]" value="<?= esc($card['side'] ?? 'single'); ?>">
                                                    <input type="hidden" name="groups[<?= $gIdx; ?>][cards][<?= $kIdx; ?>][contact_index]" value="<?= $card['card_index'] ?? 0; ?>">
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Bottom Action Sticky Bar -->
                <div class="box-footer" style="background: #fff; border: 1px solid #d2d6de; border-radius: 6px; padding: 15px; margin-top: 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 -2px 10px rgba(0,0,0,0.04);">
                    <a href="<?= adminUrl('members/skip-ocr'); ?>" class="btn btn-default btn-lg text-danger" onclick="return confirm('Bạn có chắc chắn muốn hủy bỏ toàn bộ danh sách chờ duyệt này?');">
                        <i class="fa fa-ban"></i> Hủy Bỏ Danh Sách
                    </a>
                    <button type="submit" class="btn btn-success btn-lg" style="font-weight: 700; min-width: 280px; padding: 12px 24px; font-size: 16px;">
                        <i class="fa fa-check-circle"></i> Duyệt & Lưu Đối Tác (<?= $totalGroups; ?> Doanh Nghiệp)
                    </button>
                </div>
            <?php else: ?>
                <div class="box box-default">
                    <div class="box-body text-center" style="padding: 50px 20px;">
                        <i class="fa fa-inbox" style="font-size: 48px; color: #ccc; margin-bottom: 15px; display: block;"></i>
                        <h4>Không có dữ liệu danh thiếp chờ xác nhận</h4>
                        <p class="text-muted">Vui lòng tải lên danh thiếp mới để AI nhận diện và gom nhóm.</p>
                        <a href="<?= adminUrl('members/upload-cards'); ?>" class="btn btn-primary" style="margin-top: 10px;">
                            <i class="fa fa-cloud-upload"></i> Upload Visit Card
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- HD Zoom Modal -->
<div class="modal fade" id="modalOcrZoom" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 85%; max-width: 1100px;">
        <div class="modal-content" style="background: #111; color: #fff; border-radius: 8px;">
            <div class="modal-header" style="border-bottom: 1px solid #333; padding: 10px 15px;">
                <button type="button" class="close" data-dismiss="modal" style="color: #fff; font-size: 24px;">&times;</button>
                <h4 class="modal-title"><i class="fa fa-search-plus"></i> Xem Danh Thiếp Phóng To Full-HD</h4>
            </div>
            <div class="modal-body text-center" style="padding: 20px; max-height: 80vh; overflow-y: auto;">
                <img id="imgOcrZoomTarget" src="" style="max-width: 100%; border-radius: 4px; box-shadow: 0 0 25px rgba(255,255,255,0.25);">
            </div>
        </div>
    </div>
</div>

<!-- JSON & Raw Text Debug Modal -->
<div class="modal fade" id="modalJsonDebug" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 80%; max-width: 900px;">
        <div class="modal-content" style="border-radius: 8px;">
            <div class="modal-header bg-purple" style="border-radius: 8px 8px 0 0; padding: 12px 15px;">
                <button type="button" class="close" data-dismiss="modal" style="color: #fff; opacity: 0.9;">&times;</button>
                <h4 class="modal-title" style="color: #fff; font-weight: 600;"><i class="fa fa-code"></i> Dữ Liệu JSON & Raw Text Trích Xuất Từ AI OCR</h4>
            </div>
            <div class="modal-body" style="padding: 15px; background: #1e1e1e;">
                <pre id="jsonViewerContent" style="background: transparent; border: none; color: #4ec9b0; font-family: Consolas, Monaco, monospace; font-size: 12px; line-height: 1.5; max-height: 70vh; overflow-y: auto; margin: 0; white-space: pre-wrap; word-break: break-all;"></pre>
            </div>
            <div class="modal-footer" style="background: #f8fafc; padding: 10px 15px;">
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
var groupedOcrData = <?= json_encode($groupedData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?: '[]'; ?>;

function showGroupJsonModal(idx) {
    var data = groupedOcrData[idx] || {};
    $('#jsonViewerContent').text(JSON.stringify(data, null, 2));
    $('#modalJsonDebug').modal('show');
}

function showZoomModal(src) {
    $('#imgOcrZoomTarget').attr('src', src);
    $('#modalOcrZoom').modal('show');
}

$(document).ready(function () {
    $('input[type="radio"]').on('change', function () {
        var $group = $(this).closest('.group-card-box');
        $group.find('.input-is-primary').val(0);
        $(this).closest('tr').find('.input-is-primary').val(1);
    });
});
</script>
