<!-- TOP BEST GLOBAL - National Awards Voting Catalog -->
<section class="tbg-voting-hero py-5" style="background: linear-gradient(135deg, #0A192F 0%, #0d2342 60%, #1e293b 100%); position: relative; overflow: hidden; border-bottom: 3px solid #D4AF37;">
    <div class="container py-4 position-relative" style="z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-8 text-white">
                <div class="d-inline-flex align-items-center mb-3 px-3 py-1 rounded-pill" style="background: rgba(212,175,55,0.15); border: 1px solid #D4AF37;">
                    <i class="fa fa-trophy text-warning mr-2"></i>
                    <span style="color: #F3E5AB; font-weight: 700; font-size: 12px; letter-spacing: 1px; text-transform: uppercase;">
                        MÙA GIẢI VINH DANH QUỐC GIA <?= esc($activeSeason->theme_year ?? 2026); ?>
                    </span>
                </div>
                <h1 class="font-weight-bold display-5 mb-3" style="color: #ffffff; text-shadow: 0 2px 10px rgba(0,0,0,0.5);">
                    Cổng Bình Chọn Thương Hiệu & Lãnh Đạo Tiêu Biểu
                </h1>
                <p class="lead mb-4 text-light" style="font-size: 16px; max-width: 680px; opacity: 0.9;">
                    Bình chọn trực tuyến minh bạch với hệ thống bảo vệ đa tầng & cơ chế chấm điểm kết hợp: 
                    <strong style="color: #F3E5AB;">70% Điểm Hội Đồng Giám Khảo</strong> + <strong style="color: #F3E5AB;">30% Điểm Bình Chọn Độc Giả</strong>.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="<?= langBaseUrl("voting/leaderboard"); ?>" class="btn btn-warning font-weight-bold px-4 py-2 mr-3" style="background: linear-gradient(135deg, #D4AF37 0%, #AA771C 100%); color: #0A192F; border: none; border-radius: 8px; box-shadow: 0 4px 15px rgba(212,175,55,0.4);">
                        <i class="fa fa-bar-chart mr-1"></i> XEM BẢNG XẾP HẠNG REAL-TIME
                    </a>
                    <a href="<?= langBaseUrl("nomination"); ?>" class="btn btn-outline-light px-4 py-2 font-weight-bold" style="border-radius: 8px; border-color: rgba(255,255,255,0.4);">
                        <i class="fa fa-file-text-o mr-1"></i> Nộp Hồ Sơ Đề Cử
                    </a>
                </div>
            </div>
            <div class="col-lg-4 d-none d-lg-block text-center">
                <div class="p-4 rounded-lg" style="background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); border: 1px solid rgba(212,175,55,0.3); border-radius: 16px;">
                    <i class="fa fa-shield fa-3x mb-2" style="color: #D4AF37;"></i>
                    <h5 class="text-white font-weight-bold mb-2">Bảo Mật & Công Bằng</h5>
                    <p class="small text-light mb-0" style="opacity: 0.85;">
                        Mỗi lượt bình chọn được xác thực bằng mã OTP Email, chống spam IP & kiểm soát vân tay thiết bị Canvas/Audio.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5" style="background: #f8fafc; min-height: 600px;">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between pb-3 border-bottom">
                    <div class="mb-2">
                        <h4 class="font-weight-bold text-dark mb-1" style="color: #0A192F;">
                            <i class="fa fa-th-large text-warning mr-2"></i> Danh Mục Hạng Mục Vinh Danh
                        </h4>
                        <small class="text-muted">Chọn hạng mục để xem danh sách ứng viên và thực hiện bình chọn</small>
                    </div>
                    <div class="mb-2">
                        <input type="text" id="tbgCandidateSearchInput" class="form-control form-control-sm" placeholder="Tìm theo tên doanh nghiệp/ứng viên..." onkeyup="filterCandidateCards()" style="border-radius: 20px; width: 260px; padding-left: 15px;">
                    </div>
                </div>

                <div class="py-3 d-flex flex-wrap" style="gap: 8px;">
                    <a href="<?= langBaseUrl("voting"); ?>" class="btn btn-sm <?= empty($selectedCategoryId) ? "btn-primary" : "btn-outline-secondary"; ?>" style="border-radius: 20px; font-weight: 600; padding: 6px 16px;">
                        Tất Cả Hạng Mục
                    </a>
                    <?php if (!empty($categories)): foreach ($categories as $cat): ?>
                        <a href="<?= langBaseUrl("voting/category/" . $cat->slug); ?>" class="btn btn-sm <?= ($selectedCategoryId == $cat->id || (!empty($activeCategory) && $activeCategory->id == $cat->id)) ? "btn-primary font-weight-bold" : "btn-outline-secondary"; ?>" style="border-radius: 20px; font-weight: 600; padding: 6px 16px;">
                            <i class="<?= esc($cat->icon ?? "fa fa-award"); ?> mr-1"></i> <?= esc($cat->name); ?>
                        </a>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>

        <?php if (!empty($activeCategory)): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="p-3 rounded" style="background: #ffffff; border-left: 4px solid #D4AF37; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                        <h5 class="font-weight-bold mb-1" style="color: #0A192F;">
                            <?= esc($activeCategory->name); ?>
                        </h5>
                        <div class="small text-muted">
                            <?= esc($activeCategory->description ?? "Danh sách ứng viên chính thức đã vượt qua vòng thẩm định hồ sơ"); ?>
                            • Tỷ trọng: <strong><?= $activeCategory->jury_weight ?? 70; ?>% Giám Khảo</strong> + <strong><?= $activeCategory->public_weight ?? 30; ?>% Độc Giả</strong>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row" id="tbgCandidateGrid">
            <?php if (!empty($candidates)): foreach ($candidates as $candidate): 
                $avatar = !empty($candidate->avatar) ? base_url($candidate->avatar) : "";
                $composite = number_format((float)($candidate->composite_score ?? 0), 2);
                $votes = number_format((int)($candidate->public_votes_count ?? 0));
                $rank = (int)($candidate->final_rank ?? 0);
            ?>
                <div class="col-md-6 col-lg-4 mb-4 candidate-card-item" data-name="<?= strtolower(esc($candidate->name)); ?>" data-code="<?= strtolower(esc($candidate->candidate_code)); ?>">
                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 14px; overflow: hidden; border: 1px solid #e2e8f0;">
                        <div class="position-relative" style="background: linear-gradient(135deg, #0A192F 0%, #1e3a8a 100%); height: 100px;">
                            <span class="badge position-absolute" style="top: 12px; right: 12px; background: rgba(212,175,55,0.9); color: #0A192F; font-weight: 800; font-size: 11px; padding: 4px 10px; border-radius: 20px;">
                                <?= esc($candidate->candidate_code); ?>
                            </span>
                            <?php if ($rank > 0 && $rank <= 3): ?>
                                <span class="badge position-absolute" style="top: 12px; left: 12px; background: #D4AF37; color: #0A192F; font-weight: 800; font-size: 11px;">
                                    🥇 TOP <?= $rank; ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="card-body pt-0 position-relative" style="margin-top: -45px;">
                            <div class="d-flex align-items-end justify-content-between mb-3">
                                <div style="width: 80px; height: 80px; border-radius: 14px; background: #ffffff; border: 3px solid #ffffff; box-shadow: 0 4px 10px rgba(0,0,0,0.12); overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                    <?php if (!empty($avatar)): ?>
                                        <img src="<?= $avatar; ?>" alt="<?= esc($candidate->name); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                    <?php else: ?>
                                        <i class="fa fa-building fa-2x text-warning"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="text-right">
                                    <span class="small text-muted d-block" style="font-size: 11px; font-weight: 700; text-transform: uppercase;">Điểm 70/30</span>
                                    <span class="badge" style="background: #0A192F; color: #D4AF37; font-size: 15px; font-weight: 800; padding: 4px 10px; border-radius: 8px;">
                                        <?= $composite; ?>
                                    </span>
                                </div>
                            </div>

                            <h5 class="card-title font-weight-bold mb-1" style="color: #0A192F; font-size: 16px; line-height: 22px;">
                                <a href="<?= langBaseUrl("voting/candidate/" . $candidate->slug); ?>" class="text-dark text-decoration-none hover-gold">
                                    <?= esc($candidate->name); ?>
                                </a>
                            </h5>
                            <div class="small text-muted mb-3 text-truncate">
                                <i class="fa fa-briefcase mr-1 text-warning"></i> <?= esc($candidate->organization_name ?? $candidate->name); ?>
                            </div>

                            <p class="small text-muted mb-3" style="line-height: 18px; height: 36px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                <?= esc($candidate->bio_summary ?? "Hồ sơ đề cử ứng viên xuất sắc tham gia giải thưởng danh giá quốc gia TOP BEST GLOBAL 2026."); ?>
                            </p>

                            <div class="p-2 mb-3 rounded d-flex align-items-center justify-content-between" style="background: #f1f5f9; border: 1px solid #e2e8f0;">
                                <div class="small text-muted font-weight-bold">
                                    <i class="fa fa-heart text-danger mr-1"></i> Lượt bình chọn:
                                </div>
                                <div class="font-weight-bold text-primary" style="font-size: 15px;" id="tbgVoteCounter_<?= $candidate->id; ?>">
                                    <?= $votes; ?>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-warning flex-grow-1 font-weight-bold text-dark" onclick="openVotingModal(<?= $candidate->id; ?>, \"<?= addslashes($candidate->name); ?>\", \"<?= addslashes($candidate->category_name ?? \"\"); ?>\", \"<?= $avatar; ?>\")" style="background: linear-gradient(135deg, #D4AF37 0%, #F3E5AB 50%, #AA771C 100%); border: none; border-radius: 8px; font-size: 13px; padding: 8px;">
                                    <i class="fa fa-check-circle mr-1"></i> BÌNH CHỌN
                                </button>
                                <a href="<?= langBaseUrl("voting/candidate/" . $candidate->slug); ?>" class="btn btn-outline-secondary font-weight-bold px-3" style="border-radius: 8px; font-size: 13px; padding: 8px;">
                                    Chi Tiết
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; else: ?>
                <div class="col-12 text-center py-5">
                    <div class="p-5 bg-white rounded shadow-sm">
                        <i class="fa fa-folder-open-o fa-3x text-muted mb-3"></i>
                        <h5 class="font-weight-bold text-muted">Chưa có ứng viên trong hạng mục này</h5>
                        <p class="small text-muted mb-3">Hồ sơ ứng viên đang trong giai đoạn thẩm định sơ khảo.</p>
                        <a href="<?= langBaseUrl("voting"); ?>" class="btn btn-primary btn-sm">Xem tất cả hạng mục</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?= loadView("voting/partials/_otp_modal"); ?>

<script>
function filterCandidateCards(){
    const val = document.getElementById("tbgCandidateSearchInput").value.toLowerCase().trim();
    const cards = document.querySelectorAll(".candidate-card-item");
    cards.forEach(card => {
        const name = card.getAttribute("data-name") || "", code = card.getAttribute("data-code") || "";
        card.style.display = (name.includes(val) || code.includes(val)) ? "" : "none";
    });
}
</script>
