<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><i class="fa fa-gavel text-primary"></i> <?= esc($title); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('jury-evaluations'); ?>" class="btn btn-sm btn-default">
                        <i class="fa fa-arrow-left"></i> Quay lại Danh Sách Thẩm Định
                    </a>
                </div>
            </div>

            <!-- Candidate Summary Card -->
            <div class="box-body" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                <div class="row">
                    <div class="col-md-8">
                        <h4 style="margin-top: 0; color: #0A192F; font-weight: bold;">
                            <?= esc($candidate->name); ?> 
                            <code style="font-size: 13px; color: #0284c7;">(<?= esc($candidate->candidate_code); ?>)</code>
                        </h4>
                        <p class="text-muted" style="margin-bottom: 5px;">
                            <strong>Đơn vị / Doanh nghiệp:</strong> <?= esc($candidate->organization_name ?: $candidate->name); ?> | 
                            <strong>MST:</strong> <?= esc($candidate->tax_code ?: 'N/A'); ?>
                        </p>
                        <p class="text-muted" style="margin-bottom: 0;">
                            <strong>Mùa giải:</strong> <?= esc($season->title ?? 'Mùa giải 2026'); ?> | 
                            <strong>Hạng mục:</strong> <span class="label label-primary"><?= esc($category->name ?? 'Hạng mục'); ?></span>
                        </p>
                    </div>
                    <div class="col-md-4 text-right">
                        <div style="background: #fff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px; display: inline-block; text-align: right;">
                            <span style="font-size: 11px; text-transform: uppercase; color: #64748b;">Trọng số Giám Khảo:</span><br>
                            <strong style="font-size: 20px; color: #D4AF37;"><?= (float)($category->jury_weight ?? 70); ?>%</strong>
                        </div>
                    </div>
                </div>
            </div>

            <form action="<?= adminUrl('jury-submit-score-post'); ?>" method="post" id="juryScoreForm">
                <?= csrf_field(); ?>
                <input type="hidden" name="candidate_id" value="<?= $candidate->id; ?>">
                <input type="hidden" name="season_id" value="<?= $candidate->season_id; ?>">
                <input type="hidden" name="category_id" value="<?= $candidate->category_id; ?>">

                <div class="box-body">
                    <!-- Judge Selector (Admin can select judge account) -->
                    <div class="row m-b-15">
                        <div class="col-md-6">
                            <label>Tài Khoản Giám Khảo Thẩm Định <span class="text-danger">*</span></label>
                            <select name="jury_user_id" class="form-control" required>
                                <?php if (!empty($judges)): foreach ($judges as $j): ?>
                                    <option value="<?= $j->id; ?>" <?= (($evaluation->jury_user_id ?? $juryUserId) == $j->id) ? 'selected' : ''; ?>>
                                        <?= esc($j->username); ?> (<?= esc($j->email); ?> - <?= esc($j->role); ?>)
                                    </option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                    </div>

                    <!-- 4 Rubric Evaluation Criteria -->
                    <div class="box box-solid box-default" style="border: 1px solid #cbd5e1; border-radius: 6px;">
                        <div class="box-header with-border" style="background: #f1f5f9;">
                            <h4 class="box-title" style="font-weight: bold; color: #0A192F;">
                                <i class="fa fa-sliders text-primary"></i> 4 Tiêu Chí Chấm Điểm Thẩm Định (Thang điểm 0 - 100)
                            </h4>
                        </div>
                        <div class="box-body">
                            <!-- Criterion 1 -->
                            <div class="form-group" style="padding-bottom: 12px; border-bottom: 1px dashed #e2e8f0;">
                                <div class="row">
                                    <div class="col-md-8">
                                        <label style="font-size: 14px; color: #0A192F;">
                                            1. Đổi Mới Sáng Tạo &amp; Tiên Phong Công Nghệ (Trọng số 25%)
                                        </label>
                                        <p class="text-muted" style="font-size: 12px; margin-bottom: 0;">
                                            Tính độc đáo, ứng dụng công nghệ, năng lực sáng tạo và sự đột phá so với thị trường.
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <input type="number" step="0.5" min="0" max="100" name="criteria_1_score" id="c1" class="form-control rubric-input" style="font-size: 16px; font-weight: bold; text-align: right;" value="<?= old('criteria_1_score', $evaluation->criteria_1_score ?? 85); ?>" required>
                                            <span class="input-group-addon">/ 100</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Criterion 2 -->
                            <div class="form-group" style="padding-bottom: 12px; border-bottom: 1px dashed #e2e8f0;">
                                <div class="row">
                                    <div class="col-md-8">
                                        <label style="font-size: 14px; color: #0A192F;">
                                            2. Hiệu Quả Kinh Doanh &amp; Tăng Trưởng Bền Vững (Trọng số 30%)
                                        </label>
                                        <p class="text-muted" style="font-size: 12px; margin-bottom: 0;">
                                            Quy mô doanh thu, biên lợi nhuận, tốc độ tăng trưởng, năng lực tài chính minh bạch.
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <input type="number" step="0.5" min="0" max="100" name="criteria_2_score" id="c2" class="form-control rubric-input" style="font-size: 16px; font-weight: bold; text-align: right;" value="<?= old('criteria_2_score', $evaluation->criteria_2_score ?? 88); ?>" required>
                                            <span class="input-group-addon">/ 100</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Criterion 3 -->
                            <div class="form-group" style="padding-bottom: 12px; border-bottom: 1px dashed #e2e8f0;">
                                <div class="row">
                                    <div class="col-md-8">
                                        <label style="font-size: 14px; color: #0A192F;">
                                            3. Đóng Góp Cộng Đồng &amp; Trách Nhiệm Xã Hội (Trọng số 25%)
                                        </label>
                                        <p class="text-muted" style="font-size: 12px; margin-bottom: 0;">
                                            Trách nhiệm môi trường (ESG), tạo việc làm, an sinh xã hội và đóng góp ngân sách quốc gia.
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <input type="number" step="0.5" min="0" max="100" name="criteria_3_score" id="c3" class="form-control rubric-input" style="font-size: 16px; font-weight: bold; text-align: right;" value="<?= old('criteria_3_score', $evaluation->criteria_3_score ?? 90); ?>" required>
                                            <span class="input-group-addon">/ 100</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Criterion 4 -->
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-8">
                                        <label style="font-size: 14px; color: #0A192F;">
                                            4. Uy Tín Thương Hiệu &amp; Năng Lực Quản Trị (Trọng số 20%)
                                        </label>
                                        <p class="text-muted" style="font-size: 12px; margin-bottom: 0;">
                                            Sự tin cậy của khách hàng, giải thưởng uy tín, văn hóa doanh nghiệp và chuẩn mực lãnh đạo.
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <input type="number" step="0.5" min="0" max="100" name="criteria_4_score" id="c4" class="form-control rubric-input" style="font-size: 16px; font-weight: bold; text-align: right;" value="<?= old('criteria_4_score', $evaluation->criteria_4_score ?? 86); ?>" required>
                                            <span class="input-group-addon">/ 100</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Live Calculation Preview Card -->
                    <div class="well" style="background: #0A192F; color: #fff; border-radius: 8px; padding: 15px;">
                        <div class="row text-center">
                            <div class="col-md-6 col-xs-12" style="border-right: 1px solid #334155;">
                                <span style="font-size: 12px; text-transform: uppercase; color: #94a3b8;">Điểm Thẩm Định Trung Bình (Thang 100):</span><br>
                                <strong id="previewAvgScore" style="font-size: 28px; color: #fff;">0.00</strong>
                            </div>
                            <div class="col-md-6 col-xs-12">
                                <span style="font-size: 12px; text-transform: uppercase; color: #D4AF37;">Quy Đổi Trọng Số 70% Điểm Tổng Hợp:</span><br>
                                <strong id="previewWeightedScore" style="font-size: 28px; color: #D4AF37;">0.00</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Reviewer Notes -->
                    <div class="form-group">
                        <label>Nhận Xét &amp; Đánh Giá Chi Tiết Của Giám Khảo</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Nhập nhận xét chuyên môn, đánh giá điểm mạnh / tiềm năng của ứng viên..."><?= old('notes', $evaluation->notes ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right" style="font-weight: bold; padding: 8px 25px;">
                        <i class="fa fa-check-circle"></i> Lưu Kết Quả Chấm Điểm Thẩm Định
                    </button>
                    <a href="<?= adminUrl('jury-evaluations'); ?>" class="btn btn-default">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function calculateJuryPreview() {
        var c1 = parseFloat($('#c1').val()) || 0;
        var c2 = parseFloat($('#c2').val()) || 0;
        var c3 = parseFloat($('#c3').val()) || 0;
        var c4 = parseFloat($('#c4').val()) || 0;
        
        var avg = (c1 + c2 + c3 + c4) / 4;
        var juryWeight = <?= (float)($category->jury_weight ?? 70); ?>;
        var weighted = avg * (juryWeight / 100);

        $('#previewAvgScore').text(avg.toFixed(2));
        $('#previewWeightedScore').text(weighted.toFixed(2) + ' đ');
    }

    $(document).ready(function() {
        calculateJuryPreview();
        $('.rubric-input').on('input change', function() {
            calculateJuryPreview();
        });
    });
</script>
