<?php

namespace App\Controllers;

use App\Models\AwardCategoryModel;
use App\Models\AwardSeasonModel;
use App\Models\NominationCandidateModel;
use App\Services\HybridScoringService;
use App\Services\VotingEngineService;

class VotingEngineController extends BaseController
{
    protected $seasonModel;
    protected $categoryModel;
    protected $candidateModel;
    protected $votingService;
    protected $scoringService;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        $this->seasonModel = new AwardSeasonModel();
        $this->categoryModel = new AwardCategoryModel();
        $this->candidateModel = new NominationCandidateModel();
        $this->votingService = new VotingEngineService($this->candidateModel, $this->categoryModel, $this->seasonModel);
        $this->scoringService = new HybridScoringService($this->candidateModel, $this->categoryModel);
    }

    public function index()
    {
        $activeSeason = $this->seasonModel->getActiveSeason();
        $seasonId = (int)($activeSeason->id ?? 1);

        $categories = $this->categoryModel->getCategoriesBySeason($seasonId);
        $categoriesGrouped = $this->categoryModel->getCategoriesGroupedBySector($seasonId);
        $featuredCandidates = $this->candidateModel->getFeaturedCandidates(8);
        $selectedCategoryId = (int)inputGet("category");

        $candidates = [];
        $activeCategory = null;

        if ($selectedCategoryId > 0) {
            $activeCategory = $this->categoryModel->getCategory($selectedCategoryId);
            $candidates = $this->candidateModel->getCandidatesForVoting($selectedCategoryId, $seasonId);
        } else {
            $firstCategory = !empty($categories) ? $categories[0] : null;
            if ($firstCategory) {
                $activeCategory = $firstCategory;
                $candidates = $this->candidateModel->getCandidatesForVoting((int)$firstCategory->id, $seasonId);
            }
        }

        $isEn = ($this->activeLang->short_form ?? "vi") === "en";
        $data = [
            "title"              => $isEn ? "National Honors Public Voting Portal 2026 | TOP BEST GLOBAL" : "Cổng Bình Chọn Vinh Danh Quốc Gia 2026 | TOP BEST GLOBAL",
            "description"        => $isEn ? "Cast your verified vote for Vietnam outstanding enterprises, brands, and visionary leaders. 70/30 Hybrid Scoring Engine." : "Bình chọn minh bạch cho các thương hiệu và doanh nghiệp tiêu biểu Việt Nam. Cơ chế chấm điểm 70% Hội đồng Giám khảo + 30% Độc giả.",
            "keywords"           => "bình chọn trực tuyến, top best global, vinh danh thương hiệu, cúp vàng 2026, 70 30 scoring",
            "activeSeason"       => $activeSeason,
            "categories"         => $categories,
            "categoriesGrouped"  => $categoriesGrouped,
            "activeCategory"     => $activeCategory,
            "candidates"         => $candidates,
            "featuredCandidates" => $featuredCandidates,
            "selectedCategoryId" => $selectedCategoryId,
        ];

        return loadView("partials/_header", $data)
            . loadView("voting/list", $data)
            . loadView("partials/_footer", $data);
    }

    public function category(string $slug)
    {
        $activeSeason = $this->seasonModel->getActiveSeason();
        $seasonId = (int)($activeSeason->id ?? 1);

        $category = $this->categoryModel->getCategoryBySlug($slug, $seasonId);
        if (!$category) {
            $category = $this->categoryModel->getCategoryBySlug($slug);
        }

        if (!$category) {
            return redirect()->to(langBaseUrl("voting"));
        }

        $catId = (int)$category->id;
        $categories = $this->categoryModel->getCategoriesBySeason($seasonId);
        $candidates = $this->candidateModel->getCandidatesForVoting($catId, $seasonId);

        $isEn = ($this->activeLang->short_form ?? "vi") === "en";
        $data = [
            "title"              => ($isEn ? "Vote: " : "Bình Chọn: ") . esc($category->name) . " | TOP BEST GLOBAL",
            "description"        => esc($category->description ?? $category->name),
            "keywords"           => "bình chọn " . esc($category->name) . ", giải thưởng quốc gia, top best global",
            "activeSeason"       => $activeSeason,
            "categories"         => $categories,
            "activeCategory"     => $category,
            "candidates"         => $candidates,
            "selectedCategoryId" => $catId,
        ];

        return loadView("partials/_header", $data)
            . loadView("voting/list", $data)
            . loadView("partials/_footer", $data);
    }

    public function candidate(string $slug)
    {
        $candidate = $this->candidateModel->getCandidateBySlug($slug);
        if (!$candidate) {
            $candidate = $this->candidateModel->getCandidateByCode($slug);
        }

        if (!$candidate) {
            return redirect()->to(langBaseUrl("voting"));
        }

        $cId = (int)$candidate->id;
        $seasonId = (int)($candidate->season_id ?? 1);
        $categoryId = (int)($candidate->category_id ?? 1);

        $category = $this->categoryModel->getCategory($categoryId);
        $scoreData = $this->scoringService->calculateCompositeScore($cId, $seasonId, $categoryId);
        $rivals = $this->candidateModel->getCandidatesForVoting($categoryId, $seasonId);

        $isEn = ($this->activeLang->short_form ?? "vi") === "en";
        $data = [
            "title"       => esc($candidate->name) . " - " . ($isEn ? "Candidate Profile & Voting" : "Hồ Sơ Ứng Viên & Bình Chọn") . " | TOP BEST GLOBAL",
            "description" => esc($candidate->bio_summary ?? $candidate->name),
            "keywords"    => esc($candidate->name) . ", bình chọn ứng viên, top best global, " . esc($category->name ?? ""),
            "candidate"   => $candidate,
            "category"    => $category,
            "scoreData"   => $scoreData,
            "rivals"      => array_slice($rivals, 0, 4),
        ];

        return loadView("partials/_header", $data)
            . loadView("voting/detail", $data)
            . loadView("partials/_footer", $data);
    }

    public function leaderboard()
    {
        $activeSeason = $this->seasonModel->getActiveSeason();
        $seasonId = (int)($activeSeason->id ?? 1);

        $categories = $this->categoryModel->getCategoriesBySeason($seasonId);
        $globalTop = $this->votingService->getGlobalLeaderboard($seasonId, 12);

        $isEn = ($this->activeLang->short_form ?? "vi") === "en";
        $data = [
            "title"          => $isEn ? "Real-Time National Honors Leaderboard 2026 | TOP BEST GLOBAL" : "Bảng Xếp Hạng Bình Chọn Thời Gian Thực 2026 | TOP BEST GLOBAL",
            "description"    => $isEn ? "Live rankings, composite 70/30 scores, and real-time public voting tallies." : "Bảng xếp hạng trực tiếp, điểm tổng hợp 70/30 và số lượt bình chọn thời gian thực.",
            "keywords"       => "bảng xếp hạng bình chọn, real-time leaderboard, top best global, kết quả bình chọn",
            "activeSeason"   => $activeSeason,
            "categories"     => $categories,
            "globalTop"      => $globalTop,
            "activeCategory" => null,
        ];

        return loadView("partials/_header", $data)
            . loadView("voting/leaderboard", $data)
            . loadView("partials/_footer", $data);
    }

    public function categoryLeaderboard(string $slug)
    {
        $activeSeason = $this->seasonModel->getActiveSeason();
        $seasonId = (int)($activeSeason->id ?? 1);

        $category = $this->categoryModel->getCategoryBySlug($slug, $seasonId);
        if (!$category) {
            return redirect()->to(langBaseUrl("voting/leaderboard"));
        }

        $catId = (int)$category->id;
        $categories = $this->categoryModel->getCategoriesBySeason($seasonId);
        $leaderboardData = $this->votingService->getCategoryLeaderboard($catId, $seasonId);

        $isEn = ($this->activeLang->short_form ?? "vi") === "en";
        $data = [
            "title"           => ($isEn ? "Leaderboard: " : "Bảng Xếp Hạng: ") . esc($category->name) . " | TOP BEST GLOBAL",
            "description"     => esc($category->description ?? $category->name),
            "keywords"        => "bảng xếp hạng " . esc($category->name) . ", kết quả bình chọn",
            "activeSeason"    => $activeSeason,
            "categories"      => $categories,
            "activeCategory"  => $category,
            "categoryBoard"   => $leaderboardData,
            "globalTop"       => [],
        ];

        return loadView("partials/_header", $data)
            . loadView("voting/leaderboard", $data)
            . loadView("partials/_footer", $data);
    }
}
