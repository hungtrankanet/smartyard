<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title">
                        <i class="fa fa-id-card-o text-primary"></i> 
                        Hồ Sơ Đề Cử: <span style="color: #0A192F; font-weight: bold;"><?= esc($candidate->name); ?></span> 
                        <code style="color: #0284c7; font-size: 13px;">(<?= esc($candidate->candidate_code); ?>)</code>
                    </h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('nominations?season_id=' . $candidate->season_id); ?>" class="btn btn-sm btn-default">
                        <i class="fa fa-arrow-left"></i> Danh Sách Đề Cử
                    </a>
                </div>
            </div>
            <div class="box-body">
                <!-- 4-Stage Workflow Stepper -->
                <?php
                $currStage = $candidate->stage ?? 'so_khao';
                $stageMap = [
                    'so_khao'    => 1,
                    'tham_dinh'  => 2,
                    'chung_khao' => 3,
                    'trao_giai'  => 4,
                    'awarded'    => 4,
                    'rejected'   => -1,
                ];
                $currentStep = $stageMap[$currStage] ?? 1;
                ?>
                <div class="well" style="background: #0A192F; color: #fff; border-radius: 8px; margin-bottom: 25px; padding: 20px;">
                    <h4 style="color: #D4AF37; margin-top: 0; margin-bottom: 15px; font-weight: bold;">
                        <i class="fa fa-sitemap"></i> Tiến Trình Xét Duyệt 4 Vòng Giải Thưởng TOP BEST GLOBAL
                    </h4>
                    <div class="row text-center">
                        <div class="col-md-3 col-xs-6" style="margin-bottom: 10px;">
                            <div style="padding: 10px; border-radius: 6px; border: 2px solid <?= ($currentStep >= 1 && $currentStep !== -1) ? '#D4AF37' : '#334155'; ?>; background: <?= ($currentStep == 1) ? 'rgba(212, 175, 55, 0.2)' : 'transparent'; ?>;">
                                <i class="fa fa-file-text-o" style="font-size: 24px; color: <?= ($currentStep >= 1 && $currentStep !== -1) ? '#D4AF37' : '#64748b'; ?>;"></i>
                                <h5 style="margin: 8px 0 3px 0; font-weight: bold; color: <?= ($currentStep >= 1 && $currentStep !== -1) ? '#fff' : '#64748b'; ?>;">Vòng 1: Sơ Khảo</h5>
                                <small style="color: #94a3b8;">Thẩm tra hồ sơ &amp; MST</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-6" style="margin-bottom: 10px;">
                            <div style="padding: 10px; border-radius: 6px; border: 2px solid <?= ($currentStep >= 2) ? '#D4AF37' : '#334155'; ?>; background: <?= ($currentStep == 2) ? 'rgba(212, 175, 55, 0.2)' : 'transparent'; ?>;">
                                <i class="fa fa-gavel" style="font-size: 24px; color: <?= ($currentStep >= 2) ? '#D4AF37' : '#64748b'; ?>;"></i>
                                <h5 style="margin: 8px 0 3px 0; font-weight: bold; color: <?= ($currentStep >= 2) ? '#fff' : '#64748b'; ?>;">Vòng 2: Thẩm Định</h5>
                                <small style="color: #94a3b8;">HĐGK Chấm điểm (70%)</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-6" style="margin-bottom: 10px;">
                            <div style="padding: 10px; border-radius: 6px; border: 2px solid <?= ($currentStep >= 3) ? '#D4AF37' : '#334155'; ?>; background: <?= ($currentStep == 3) ? 'rgba(212, 175, 55, 0.2)' : 'transparent'; ?>;">
                                <i class="fa fa-users" style="font-size: 24px; color: <?= ($currentStep >= 3) ? '#D4AF37' : '#64748b'; ?>;"></i>
                                <h5 style="margin: 8px 0 3px 0; font-weight: bold; color: <?= ($currentStep >= 3) ? '#fff' : '#64748b'; ?>;">Vòng 3: Chung Khảo</h5>
                                <small style="color: #94a3b8;">Bình chọn trực tuyến (30%)</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-6" style="margin-bottom: 10px;">
                            <div style="padding: 10px; border-radius: 6px; border: 2px solid <?= ($currentStep >= 4) ? '#D4AF37' : '#334155'; ?>; background: <?= ($currentStep == 4) ? 'rgba(212, 175, 55, 0.2)' : 'transparent'; ?>;">
                                <i class="fa fa-trophy" style="font-size: 24px; color: <?= ($currentStep >= 4) ? '#D4AF37' : '#64748b'; ?>;"></i>
                                <h5 style="margin: 8px 0 3px 0; font-weight: bold; color: <?= ($currentStep >= 4) ? '#fff' : '#64748b'; ?>;">Vòng 4: Trao Giải</h5>
                                <small style="color: #94a3b8;">Gala &amp; Bảng Vàng Danh Dự</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Left Column: Dossier Information -->
                    <div class="col-md-7">
                        <div class="box box-solid box-default" style="border: 1px solid #e2e8f0;">
                            <div class="box-header with-border" style="background: #f8fafc;">
                                <h4 class="box-title" style="font-weight: bold; color: #0A192F;">
                                    <i class="fa fa-building text-primary"></i> Thông Tin Doanh Nghiệp / Đơn Vị Đề Cử
                                </h4>
                            </div>
                            <div class="box-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="35%" style="background: #f8fafc;">Tên Đề Cử / Thương Hiệu:</th>
                                        <td><strong style="font-size: 15px; color: #0A192F;"><?= esc($candidate->name); ?></strong></td>
                                    </tr>
                                    <tr>
                                        <th style="background: #f8fafc;">Tên Doanh Nghiệp Đầy Đủ:</th>
                                        <td><?= esc($candidate->organization_name ?: $candidate->name); ?></td>
                                    </tr>
                                    <tr>
                                        <th style="background: #f8fafc;">Mã Số Thuế (MST):</th>
                                        <td><code><?= esc($candidate->tax_code ?: 'Chưa cập nhật'); ?></code></td>
                                    </tr>
                                    <tr>
                                        <th style="background: #f8fafc;">Mùa Giải &amp; Hạng Mục:</th>
                                        <td>
                                            <span class="label label-primary"><?= esc($season->title ?? 'Mùa giải 2026'); ?></span>
                                            <span class="label label-info"><?= esc($category->name ?? 'Hạng mục'); ?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="background: #f8fafc;">Người Đại Diện / Liên Hệ:</th>
                                        <td><?= esc($candidate->contact_person ?: 'N/A'); ?></td>
                                    </tr>
                                    <tr>
                                        <th style="background: #f8fafc;">Email Liên Hệ:</th>
                                        <td><a href="mailto:<?= esc($candidate->contact_email); ?>"><?= esc($candidate->contact_email); ?></a></td>
                                    </tr>
                                    <tr>
                                        <th style="background: #f8fafc;">Số Điện Thoại:</th>
                                        <td><?= esc($candidate->contact_phone ?: 'N/A'); ?></td>
                                    </tr>
                                    <tr>
                                        <th style="background: #f8fafc;">Website:</th>
                                        <td>
                                            <?php if (!empty($candidate->website)): ?>
                                                <a href="<?= esc($candidate->website); ?>" target="_blank"><?= esc($candidate->website); ?></a>
                                            <?php else: ?>
                                                <span class="text-muted">Chưa cập nhật</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="background: #f8fafc;">Tóm Tắt Thành Tích &amp; Hồ Sơ Năng Lực:</th>
                                        <td>
                                            <div style="max-height: 200px; overflow-y: auto; background: #f8fafc; padding: 10px; border-radius: 4px;">
                                                <?= nl2br(esc($candidate->bio_summary ?: 'Chưa có nội dung tóm tắt')); ?>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Dossier Files Attachment Box -->
                        <div class="box box-solid box-default" style="border: 1px solid #e2e8f0;">
                            <div class="box-header with-border" style="background: #f8fafc;">
                                <h4 class="box-title" style="font-weight: bold; color: #0A192F;">
                                    <i class="fa fa-paperclip text-primary"></i> Tài Liệu Minh Chứng &amp; Hồ Sơ Đính Kèm
                                </h4>
                            </div>
                            <div class="box-body">
                                <?php if (!empty($dossierFiles)): ?>
                                    <ul class="list-group" style="margin-bottom: 0;">
                                        <?php foreach ($dossierFiles as $index => $file): ?>
                                            <li class="list-group-item" style="display: flex; justify-content: space-between; align-items: center;">
                                                <div>
                                                    <i class="fa fa-file-pdf-o text-danger m-r-5"></i>
                                                    <strong><?= esc($file['name'] ?? ('Tài liệu minh chứng #' . ($index + 1))); ?></strong>
                                                    <?php if (!empty($file['size'])): ?>
                                                        <small class="text-muted">(<?= esc($file['size']); ?>)</small>
                                                    <?php endif; ?>
                                                </div>
                                                <a href="<?= esc($file['url'] ?? '#'); ?>" target="_blank" class="btn btn-xs btn-primary">
                                                    <i class="fa fa-download"></i> Xem / Tải về
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="text-muted text-center py-3" style="margin-bottom: 0;">Hồ sơ chưa đính kèm file tài liệu rời (được nộp trực tiếp theo biểu mẫu).</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Stage Advancement, Decision & Score Breakdown -->
                    <div class="col-md-5">
                        <!-- Stage Transition Box -->
                        <div class="box box-solid box-primary" style="border: 2px solid #3c8dbc;">
                            <div class="box-header with-border" style="background: #3c8dbc; color: #fff;">
                                <h4 class="box-title" style="font-weight: bold;"><i class="fa fa-exchange"></i> Chuyển Vòng Xét Duyệt (Stage Transition)</h4>
                            </div>
                            <form action="<?= adminUrl('nomination-update-stage-post'); ?>" method="post">
                                <?= csrf_field(); ?>
                                <input type="hidden" name="candidate_id" value="<?= $candidate->id; ?>">
                                <input type="hidden" name="redirect_to" value="dossier">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label>Chọn Vòng Chuyển Tiếp:</label>
                                        <select name="target_stage" class="form-control" style="font-weight: bold;">
                                            <option value="so_khao" <?= ($currStage === 'so_khao') ? 'selected' : ''; ?>>Vòng 1: Sơ Khảo Hồ Sơ</option>
                                            <option value="tham_dinh" <?= ($currStage === 'tham_dinh') ? 'selected' : ''; ?>>Vòng 2: Thẩm Định Giám Khảo (70%)</option>
                                            <option value="chung_khao" <?= ($currStage === 'chung_khao') ? 'selected' : ''; ?>>Vòng 3: Chung Khảo &amp; Bình Chọn (30%)</option>
                                            <option value="trao_giai" <?= ($currStage === 'trao_giai' || $currStage === 'awarded') ? 'selected' : ''; ?>>Vòng 4: Vinh Danh &amp; Trao Cúp</option>
                                            <option value="rejected" <?= ($currStage === 'rejected') ? 'selected' : ''; ?>>Không Đạt Yêu Cầu (Reject)</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Ghi Chú Xét Duyệt Của Ban Thư Ký:</label>
                                        <textarea name="admin_notes" class="form-control" rows="2" placeholder="Nhập nhận xét / lý do chuyển vòng..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-block" style="font-weight: bold;">
                                        <i class="fa fa-check-circle"></i> Cập Nhật Vòng Xét Duyệt
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Decision & Honors Details Box -->
                        <div class="box box-solid box-warning" style="border: 2px solid #D4AF37;">
                            <div class="box-header with-border" style="background: #D4AF37; color: #0A192F;">
                                <h4 class="box-title" style="font-weight: bold;"><i class="fa fa-trophy"></i> Quyết Định Phê Duyệt &amp; Danh Hiệu</h4>
                            </div>
                            <form action="<?= adminUrl('nomination-decision-post'); ?>" method="post">
                                <?= csrf_field(); ?>
                                <input type="hidden" name="candidate_id" value="<?= $candidate->id; ?>">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label>Trạng Thái Duyệt Hồ Sơ:</label>
                                        <select name="status" class="form-control">
                                            <option value="approved" <?= ($candidate->status === 'approved') ? 'selected' : ''; ?>>Đã Phê Duyệt (Approved)</option>
                                            <option value="pending" <?= ($candidate->status === 'pending') ? 'selected' : ''; ?>>Chờ Bổ Sung Hồ Sơ (Pending)</option>
                                            <option value="rejected" <?= ($candidate->status === 'rejected') ? 'selected' : ''; ?>>Từ Chối Hồ Sơ (Rejected)</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Danh Hiệu Vinh Danh (Award Title):</label>
                                        <input type="text" name="award_title" class="form-control" placeholder="Ví dụ: Cúp Vàng TOP BEST GLOBAL 2026" value="<?= esc($candidate->award_title ?? ''); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Số Hiệu Chứng Nhận Số (Certificate Serial):</label>
                                        <input type="text" name="certificate_serial" class="form-control" placeholder="TBG-CERT-2026-XXXX" value="<?= esc($candidate->certificate_serial ?? ''); ?>">
                                    </div>
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <label style="font-weight: bold; color: #D4AF37;">
                                                <input type="checkbox" name="is_featured" value="1" <?= !empty($candidate->is_featured) ? 'checked' : ''; ?>>
                                                Đề cử Nổi Bật Trang Chủ &amp; Bảng Vàng (Featured)
                                            </label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-warning btn-block" style="background: #D4AF37; color: #0A192F; font-weight: bold; border: none;">
                                        <i class="fa fa-save"></i> Lưu Quyết Định Xét Duyệt
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Scores Summary Box -->
                        <div class="box box-solid box-info">
                            <div class="box-header with-border">
                                <h4 class="box-title"><i class="fa fa-bar-chart"></i> Điểm Số Thẩm Định &amp; Bình Chọn</h4>
                                <div class="box-tools pull-right">
                                    <a href="<?= adminUrl('jury-scoring/' . $candidate->id); ?>" class="btn btn-xs btn-primary">
                                        <i class="fa fa-pencil"></i> Chấm điểm GK
                                    </a>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="row text-center m-b-15">
                                    <div class="col-xs-4">
                                        <div style="background: #f0fdf4; padding: 10px; border-radius: 6px; border: 1px solid #bbf7d0;">
                                            <span style="font-size: 11px; color: #166534; text-transform: uppercase;">Điểm GK (70%)</span><br>
                                            <strong style="font-size: 18px; color: #15803d;"><?= number_format((float)($candidate->jury_score_avg ?? 0), 2); ?></strong>
                                        </div>
                                    </div>
                                    <div class="col-xs-4">
                                        <div style="background: #eff6ff; padding: 10px; border-radius: 6px; border: 1px solid #bfdbfe;">
                                            <span style="font-size: 11px; color: #1e40af; text-transform: uppercase;">Bình Chọn (30%)</span><br>
                                            <strong style="font-size: 18px; color: #1d4ed8;"><?= number_format((int)($candidate->public_votes_count ?? 0)); ?></strong>
                                        </div>
                                    </div>
                                    <div class="col-xs-4">
                                        <div style="background: #fefce8; padding: 10px; border-radius: 6px; border: 1px solid #fef08a;">
                                            <span style="font-size: 11px; color: #854d0e; text-transform: uppercase;">Tổng Điểm 70/30</span><br>
                                            <strong style="font-size: 18px; color: #b45309;"><?= number_format((float)($candidate->composite_score ?? 0), 2); ?></strong>
                                        </div>
                                    </div>
                                </div>

                                <?php if (!empty($evaluations)): ?>
                                    <label><i class="fa fa-list-ol"></i> Chi tiết đánh giá từ Hội đồng Giám khảo:</label>
                                    <ul class="list-group" style="font-size: 12px; margin-bottom: 0;">
                                        <?php foreach ($evaluations as $e): ?>
                                            <li class="list-group-item">
                                                <strong><?= esc($e->jury_name ?? 'Giám khảo #' . $e->jury_user_id); ?>:</strong>
                                                <span class="pull-right badge bg-purple"><?= number_format((float)$e->total_score, 2); ?> / 100</span>
                                                <br>
                                                <small class="text-muted">
                                                    Đổi mới: <?= $e->criteria_1_score; ?> | Kinh doanh: <?= $e->criteria_2_score; ?> | Xã hội: <?= $e->criteria_3_score; ?> | Uy tín: <?= $e->criteria_4_score; ?>
                                                </small>
                                                <?php if (!empty($e->notes)): ?>
                                                    <br><small><em>"<?= esc($e->notes); ?>"</em></small>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="text-muted text-center" style="font-size: 12px; margin-bottom: 0;">Chưa có giám khảo nào chấm điểm cho hồ sơ này.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
