<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><i class="fa fa-users text-primary"></i> <?= esc($title); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('jury-evaluations'); ?>" class="btn btn-sm btn-default">
                        <i class="fa fa-arrow-left"></i> Quay lại Bảng Điểm Thẩm Định
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="callout callout-info" style="background-color: #0A192F !important; border-left-color: #D4AF37; color: #fff;">
                    <h4 style="color: #D4AF37; margin-bottom: 5px;"><i class="fa fa-balance-scale"></i> Hội Đồng Giám Khảo &amp; Ban Chuyên Gia Thẩm Định Quốc Gia</h4>
                    <p style="margin-bottom: 0; font-size: 13px; opacity: 0.9;">
                        Các thành viên giám khảo có quyền thẩm định hồ sơ theo 4 tiêu chí rubric, chấm điểm độc lập và điểm số chiếm <strong>70% trọng số xếp hạng</strong> cuối cùng của giải thưởng TOP BEST GLOBAL.
                    </p>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped dataTable">
                        <thead>
                            <tr role="row" style="background: #f8fafc;">
                                <th width="50">ID</th>
                                <th>Giám Khảo / Chuyên Gia</th>
                                <th>Email</th>
                                <th width="120" class="text-center">Vai Trò Hệ Thống</th>
                                <th width="140" class="text-center">Số Hồ Sơ Đã Chấm</th>
                                <th width="160" class="text-center">Lần Đánh Giá Cuối</th>
                                <th width="120" class="text-center">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($judges)): foreach ($judges as $judge): ?>
                                <tr>
                                    <td><strong>#<?= $judge->id; ?></strong></td>
                                    <td>
                                        <i class="fa fa-user-secret text-purple m-r-5"></i>
                                        <strong style="color: #0A192F; font-size: 14px;"><?= esc($judge->username); ?></strong>
                                    </td>
                                    <td><a href="mailto:<?= esc($judge->email); ?>"><?= esc($judge->email); ?></a></td>
                                    <td class="text-center">
                                        <span class="label label-primary"><?= esc($judge->role); ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-green" style="font-size: 13px;"><?= number_format($judge->total_evaluations ?? 0); ?> hồ sơ</span>
                                    </td>
                                    <td class="text-center">
                                        <small><?= !empty($judge->last_evaluation->submitted_at) ? date('d/m/Y H:i', strtotime($judge->last_evaluation->submitted_at)) : 'Chưa có'; ?></small>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= adminUrl('jury-evaluations?jury_user_id=' . $judge->id); ?>" class="btn btn-xs btn-primary">
                                            <i class="fa fa-list"></i> Xem bài chấm
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Chưa có thành viên hội đồng giám khảo nào.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
