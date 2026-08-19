<!-- TOP BEST GLOBAL - Real-Time National Honors Leaderboard -->
<section class="py-5" style="background: linear-gradient(135deg, #0A192F 0%, #0d2342 60%, #1e293b 100%); border-bottom: 3px solid #D4AF37;">
    <div class="container py-3 text-center">
        <span class="badge px-3 py-1 mb-3" style="background: rgba(212,175,55,0.15); border: 1px solid #D4AF37; color: #F3E5AB; font-weight: 700; font-size: 11px; letter-spacing: 1.2px; text-transform: uppercase;">
            <i class="fa fa-dot-circle-o text-danger mr-1"></i> TRUYỀN HÌNH TRỰC TIẾP KẾT QUẢ BÌNH CHỌN
        </span>
        <h1 class="display-5 font-weight-bold text-white mb-2">
            Bảng Xếp Hạng Vinh Danh Quốc Gia <?= esc($activeSeason->theme_year ?? 2026); ?>
        </h1>
        <p class="lead text-light mb-0" style="font-size: 15px; opacity: 0.9; max-width: 700px; margin: 0 auto;">
            Hệ thống xếp hạng kết hợp: <strong>70% Điểm Hội Đồng Giám Khảo</strong> + <strong>30% Điểm Bình Chọn Độc Giả</strong>. Cập nhật theo thời gian thực.
        </p>
    </div>
</section>

<section class="py-5" style="background: #f8fafc; min-height: 700px;">
    <div class="container">
        <div class="mb-4 pb-3 border-bottom d-flex flex-wrap align-items-center justify-content-between">
            <div class="d-flex flex-wrap py-2" style="gap: 8px;">
                <a href="<?= langBaseUrl("voting/leaderboard"); ?>" class="btn btn-sm <?= empty($activeCategory) ? "btn-primary font-weight-bold" : "btn-outline-secondary"; ?>" style="border-radius: 20px; padding: 6px 18px;">
                    <i class="fa fa-globe mr-1"></i> Bảng Tổng Sắp Toàn Quốc
                </a>
                <?php if (!empty($categories)): foreach ($categories as $cat): ?>
                    <a href="<?= langBaseUrl("voting/leaderboard/" . $cat->slug); ?>" class="btn btn-sm <?= (!empty($activeCategory) && $activeCategory->id == $cat->id) ? "btn-primary font-weight-bold" : "btn-outline-secondary"; ?>" style="border-radius: 20px; padding: 6px 16px;">
                        <i class="<?= esc($cat->icon ?? "fa fa-award"); ?> mr-1"></i> <?= esc($cat->name); ?>
                    </a>
                <?php endforeach; endif; ?>
            </div>
            <div class="small text-muted">
                <i class="fa fa-refresh mr-1 text-primary"></i> Tự động cập nhật mỗi <strong>15 giây</strong>
            </div>
        </div>

        <?php 
        $candidateList = !empty($categoryBoard["candidates"]) ? $categoryBoard["candidates"] : (!empty($globalTop) ? $globalTop : []);
        $top1 = isset($candidateList[0]) ? (object)$candidateList[0] : null;
        $top2 = isset($candidateList[1]) ? (object)$candidateList[1] : null;
        $top3 = isset($candidateList[2]) ? (object)$candidateList[2] : null;
        ?>

        <?php if (!empty($top1)): ?>
            <div class="row justify-content-center align-items-end mb-5 pt-3">
                <?php if (!empty($top2)): ?>
                    <div class="col-4 col-md-3 text-center order-1">
                        <div class="p-3 bg-white shadow-sm mb-2" style="border-radius: 16px 16px 0 0; border-top: 4px solid #94a3b8; background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);">
                            <div style="width: 60px; height: 60px; border-radius: 50%; background: #94a3b8; margin: 0 auto 10px; border: 3px solid #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 22px; color: #fff; font-weight: bold; overflow: hidden;">
                                <?php if (!empty($top2->avatar)): ?>
                                    <img src="<?= base_url($top2->avatar); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    🥈
                                <?php endif; ?>
                            </div>
                            <span class="badge badge-secondary mb-1" style="font-size: 11px;">HẠNG #2</span>
                            <h6 class="font-weight-bold mb-1 text-truncate" style="font-size: 13px;">
                                <a href="<?= langBaseUrl("voting/candidate/" . $top2->slug); ?>" class="text-dark"><?= esc($top2->name); ?></a>
                            </h6>
                            <div class="font-weight-bold text-primary" style="font-size: 14px;"><?= number_format((float)($top2->composite_score ?? 0), 2); ?> đ</div>
                            <small class="text-muted d-block"><?= number_format((int)($top2->public_votes_count ?? 0)); ?> phiếu</small>
                        </div>
                        <div style="height: 60px; background: #cbd5e1; border-radius: 0 0 8px 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 20px; color: #475569;">
                            2
                        </div>
                    </div>
                <?php endif; ?>

                <div class="col-4 col-md-4 text-center order-2">
                    <div class="p-4 bg-white shadow-lg mb-2 position-relative" style="border-radius: 20px 20px 0 0; border: 2px solid #D4AF37; border-bottom: none; background: linear-gradient(180deg, #faf7ee 0%, #ffffff 100%);">
                        <div class="position-absolute" style="top: -16px; left: 50%; transform: translateX(-50%); background: #D4AF37; color: #0A192F; border-radius: 20px; padding: 2px 14px; font-size: 11px; font-weight: 900; letter-spacing: 1px;">
                            👑 QUÁN QUÂN
                        </div>
                        <div style="width: 80px; height: 80px; border-radius: 50%; background: #D4AF37; margin: 10px auto 10px; border: 4px solid #F3E5AB; box-shadow: 0 4px 15px rgba(212,175,55,0.4); display: flex; align-items: center; justify-content: center; font-size: 32px; color: #0A192F; font-weight: bold; overflow: hidden;">
                            <?php if (!empty($top1->avatar)): ?>
                                <img src="<?= base_url($top1->avatar); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                🏆
                            <?php endif; ?>
                        </div>
                        <span class="badge" style="background: #D4AF37; color: #0A192F; font-weight: 800; font-size: 12px; margin-bottom: 4px;">HẠNG #1</span>
                        <h5 class="font-weight-bold mb-1 text-truncate" style="font-size: 16px;">
                            <a href="<?= langBaseUrl("voting/candidate/" . $top1->slug); ?>" class="text-dark"><?= esc($top1->name); ?></a>
                        </h5>
                        <div class="h4 font-weight-bold mb-0" style="color: #b45309;"><?= number_format((float)($top1->composite_score ?? 0), 2); ?> <small style="font-size: 13px;">/ 100đ</small></div>
                        <small class="text-muted d-block"><?= number_format((int)($top1->public_votes_count ?? 0)); ?> phiếu bình chọn</small>
                    </div>
                    <div style="height: 90px; background: linear-gradient(180deg, #D4AF37 0%, #AA771C 100%); border-radius: 0 0 10px 10px; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 32px; color: #ffffff; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                        1
                    </div>
                </div>

                <?php if (!empty($top3)): ?>
                    <div class="col-4 col-md-3 text-center order-3">
                        <div class="p-3 bg-white shadow-sm mb-2" style="border-radius: 16px 16px 0 0; border-top: 4px solid #cd7f32; background: linear-gradient(180deg, #fdfbf9 0%, #ffffff 100%);">
                            <div style="width: 60px; height: 60px; border-radius: 50%; background: #cd7f32; margin: 0 auto 10px; border: 3px solid #fed7aa; display: flex; align-items: center; justify-content: center; font-size: 22px; color: #fff; font-weight: bold; overflow: hidden;">
                                <?php if (!empty($top3->avatar)): ?>
                                    <img src="<?= base_url($top3->avatar); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    🥉
                                <?php endif; ?>
                            </div>
                            <span class="badge mb-1" style="background: #cd7f32; color: #fff; font-size: 11px;">HẠNG #3</span>
                            <h6 class="font-weight-bold mb-1 text-truncate" style="font-size: 13px;">
                                <a href="<?= langBaseUrl("voting/candidate/" . $top3->slug); ?>" class="text-dark"><?= esc($top3->name); ?></a>
                            </h6>
                            <div class="font-weight-bold text-primary" style="font-size: 14px;"><?= number_format((float)($top3->composite_score ?? 0), 2); ?> đ</div>
                            <small class="text-muted d-block"><?= number_format((int)($top3->public_votes_count ?? 0)); ?> phiếu</small>
                        </div>
                        <div style="height: 45px; background: #d97706; border-radius: 0 0 8px 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 18px; color: #ffffff;">
                            3
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm" style="border-radius: 14px; overflow: hidden; border: 1px solid #e2e8f0;">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="font-weight-bold mb-0" style="color: #0A192F;">
                    <i class="fa fa-list-ol text-warning mr-2"></i> Bảng Điểm Chi Tiết & Tỷ Lệ Bình Chọn
                </h5>
                <span class="badge badge-light p-2 font-weight-bold" style="font-size: 12px; color: #475569;">
                    Tổng số: <?= count($candidateList); ?> ứng viên
                </span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background: #f1f5f9; font-size: 12px; text-transform: uppercase; color: #475569;">
                        <tr>
                            <th width="70" class="text-center">Thứ Hạng</th>
                            <th>Ứng Viên / Doanh Nghiệp</th>
                            <th width="150" class="text-center">Phiếu Bình Chọn</th>
                            <th width="140" class="text-center">GK (70%)</th>
                            <th width="140" class="text-center">Độc Giả (30%)</th>
                            <th width="160" class="text-center">Điểm 70/30</th>
                            <th width="120" class="text-center">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($candidateList)): 
                            $rowRank = 1;
                            foreach ($candidateList as $row): 
                                $cObj = (object)$row;
                                $displayRank = (int)($cObj->final_rank ?? $rowRank);
                                $compScore = number_format((float)($cObj->composite_score ?? 0), 2);
                                $vCount = number_format((int)($cObj->public_votes_count ?? 0));
                        ?>
                            <tr>
                                <td class="text-center font-weight-bold">
                                    <?php if ($displayRank === 1): ?>
                                        <span class="badge p-2" style="background: #D4AF37; color: #0A192F; font-size: 13px;">🥇 #1</span>
                                    <?php elseif ($displayRank === 2): ?>
                                        <span class="badge badge-secondary p-2" style="font-size: 13px;">🥈 #2</span>
                                    <?php elseif ($displayRank === 3): ?>
                                        <span class="badge p-2" style="background: #cd7f32; color: #fff; font-size: 13px;">🥉 #3</span>
                                    <?php else: ?>
                                        <span class="badge badge-light text-muted p-2" style="font-size: 12px;">#<?= $displayRank; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div style="width: 42px; height: 42px; border-radius: 8px; background: #0A192F; display: flex; align-items: center; justify-content: center; color: #D4AF37; font-size: 14px; font-weight: bold; overflow: hidden; margin-right: 12px; flex-shrink: 0;">
                                            <?php if (!empty($cObj->avatar)): ?>
                                                <img src="<?= base_url($cObj->avatar); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                            <?php else: ?>
                                                <i class="fa fa-award"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <a href="<?= langBaseUrl("voting/candidate/" . $cObj->slug); ?>" class="font-weight-bold text-dark d-block">
                                                <?= esc($cObj->name); ?>
                                            </a>
                                            <small class="text-muted"><?= esc($cObj->organization_name ?? $cObj->name); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center font-weight-bold text-primary" style="font-size: 14px;">
                                    <?= $vCount; ?>
                                </td>
                                <td class="text-center font-weight-bold text-dark">
                                    <?= number_format((float)($cObj->jury_score_avg ?? 0), 2); ?>
                                </td>
                                <td class="text-center font-weight-bold text-success">
                                    <?= isset($cObj->public_weighted) ? number_format((float)$cObj->public_weighted, 2) : "—"; ?>đ
                                </td>
                                <td class="text-center">
                                    <div class="font-weight-bold" style="color: #b45309; font-size: 15px;">
                                        <?= $compScore; ?>
                                    </div>
                                    <div class="progress" style="height: 4px; border-radius: 2px; background: #e2e8f0; width: 100px; margin: 2px auto 0;">
                                        <div class="progress-bar" style="background: linear-gradient(90deg, #D4AF37, #AA771C); width: <?= min(100, (float)($cObj->composite_score ?? 0)); ?>%;"></div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <a href="<?= langBaseUrl("voting/candidate/" . $cObj->slug); ?>" class="btn btn-sm btn-outline-primary font-weight-bold" style="border-radius: 6px; font-size: 12px; padding: 4px 10px;">
                                        Bình Chọn
                                    </a>
                                </td>
                            </tr>
                        <?php $rowRank++; endforeach; else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <em>Chưa có dữ liệu xếp hạng nào.</em>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
