<?php

namespace App\Services;

use App\Models\AwardCategoryModel;
use App\Models\JuryEvaluationModel;
use App\Models\NominationCandidateModel;

class HybridScoringService
{
    protected $candidateModel;
    protected $categoryModel;
    protected $juryModel;

    protected static array $scoreMemoryCache = [];

    public function __construct(
        $candidateModel = null,
        $categoryModel = null,
        $juryModel = null
    ) {
        $this->candidateModel = $candidateModel ?? (class_exists(NominationCandidateModel::class) ? new NominationCandidateModel() : null);
        $this->categoryModel = $categoryModel ?? (class_exists(AwardCategoryModel::class) ? new AwardCategoryModel() : null);
        $this->juryModel = $juryModel ?? (class_exists(JuryEvaluationModel::class) ? new JuryEvaluationModel() : null);
    }

    public function calculateCompositeScore(int $candidateId, int $seasonId = 1, int $categoryId = 1): array
    {
        $candidate = $this->candidateModel ? $this->candidateModel->getCandidate($candidateId) : null;
        if (!$candidate) {
            return [
                "candidate_id"            => $candidateId,
                "jury_score_raw"          => 0.00,
                "jury_score_weighted"     => 0.00,
                "public_votes_raw"        => 0,
                "public_score_normalized" => 0.00,
                "public_score_weighted"   => 0.00,
                "final_composite_score"   => 0.00,
                "category_rank"           => 0,
                "is_jury_pending"         => true,
            ];
        }

        $categoryId = (int)($candidate->category_id ?? $categoryId);
        $seasonId = (int)($candidate->season_id ?? $seasonId);

        $category = $this->categoryModel ? $this->categoryModel->getCategory($categoryId) : null;
        $juryWeight = (float)($category->jury_weight ?? 70.00);
        $publicWeight = (float)($category->public_weight ?? 30.00);

        $juryData = $this->juryModel ? $this->juryModel->getCandidateJuryAverage($candidateId) : ["avg_score" => 0, "total_evaluations" => 0];
        $juryRaw = ($juryData["total_evaluations"] > 0)
            ? (float)$juryData["avg_score"]
            : (float)($candidate->jury_score_avg ?? 0.00);
        $isJuryPending = ($juryData["total_evaluations"] === 0 && (float)($candidate->jury_score_avg ?? 0) <= 0);

        $publicVotesRaw = (int)($candidate->public_votes_count ?? 0);

        $maxCategoryVotes = $this->getMaxVotesInCategory($categoryId, $seasonId);
        if ($maxCategoryVotes <= 0) {
            $maxCategoryVotes = $publicVotesRaw;
        }

        $publicScoreNormalized = ($maxCategoryVotes > 0)
            ? round(($publicVotesRaw / $maxCategoryVotes) * 100, 2)
            : 0.00;

        $juryScoreWeighted = round($juryRaw * ($juryWeight / 100), 2);
        $publicScoreWeighted = round($publicScoreNormalized * ($publicWeight / 100), 2);
        $finalCompositeScore = round($juryScoreWeighted + $publicScoreWeighted, 2);

        return [
            "candidate_id"            => $candidateId,
            "jury_score_raw"          => $juryRaw,
            "jury_score_weighted"     => $juryScoreWeighted,
            "public_votes_raw"        => $publicVotesRaw,
            "public_score_normalized" => $publicScoreNormalized,
            "public_score_weighted"   => $publicScoreWeighted,
            "final_composite_score"   => $finalCompositeScore,
            "category_rank"           => (int)($candidate->final_rank ?? 0),
            "is_jury_pending"         => $isJuryPending,
        ];
    }

    public function recalculateCategoryScores(int $categoryId, int $seasonId = 1): array
    {
        if (!$this->candidateModel) {
            return [];
        }
        $candidates = $this->candidateModel->getCandidatesForVoting($categoryId, $seasonId);
        if (empty($candidates)) {
            return [];
        }

        $category = $this->categoryModel ? $this->categoryModel->getCategory($categoryId) : null;
        $juryWeight = (float)($category->jury_weight ?? 70.00);
        $publicWeight = (float)($category->public_weight ?? 30.00);

        $maxVotes = 0;
        foreach ($candidates as $c) {
            $votes = (int)($c->public_votes_count ?? 0);
            if ($votes > $maxVotes) {
                $maxVotes = $votes;
            }
        }

        $scored = [];
        foreach ($candidates as $c) {
            $cId = (int)$c->id;
            $juryData = $this->juryModel ? $this->juryModel->getCandidateJuryAverage($cId) : ["avg_score" => 0, "total_evaluations" => 0];
            $juryRaw = ($juryData["total_evaluations"] > 0)
                ? (float)$juryData["avg_score"]
                : (float)($c->jury_score_avg ?? 0.00);

            $votesRaw = (int)($c->public_votes_count ?? 0);
            $normVotes = ($maxVotes > 0) ? round(($votesRaw / $maxVotes) * 100, 2) : 0.00;

            $juryWeighted = round($juryRaw * ($juryWeight / 100), 2);
            $publicWeighted = round($normVotes * ($publicWeight / 100), 2);
            $composite = round($juryWeighted + $publicWeighted, 2);

            $scored[] = [
                "candidate"          => $c,
                "candidate_id"       => $cId,
                "jury_score_avg"     => $juryRaw,
                "public_votes_count" => $votesRaw,
                "composite_score"    => $composite,
            ];
        }

        usort($scored, function ($a, $b) {
            if ($b["composite_score"] != $a["composite_score"]) {
                return $b["composite_score"] <=> $a["composite_score"];
            }
            if ($b["public_votes_count"] != $a["public_votes_count"]) {
                return $b["public_votes_count"] <=> $a["public_votes_count"];
            }
            return $a["candidate_id"] <=> $b["candidate_id"];
        });

        $rank = 1;
        $results = [];
        foreach ($scored as &$item) {
            $item["final_rank"] = $rank;
            $this->candidateModel->updateScoresAndRank(
                $item["candidate_id"],
                $item["jury_score_avg"],
                $item["composite_score"],
                $rank
            );
            $results[] = $item;
            $rank++;
        }

        self::$scoreMemoryCache[$categoryId] = $results;

        return $results;
    }

    public function recalculateAllCategories(int $seasonId = 1): array
    {
        if (!$this->categoryModel) {
            return [];
        }
        $categories = $this->categoryModel->getCategoriesBySeason($seasonId);
        $summary = [];
        foreach ($categories as $category) {
            $catId = (int)$category->id;
            $summary[$catId] = $this->recalculateCategoryScores($catId, $seasonId);
        }
        return $summary;
    }

    protected function getMaxVotesInCategory(int $categoryId, int $seasonId = 1): int
    {
        if (!class_exists("\\Config\\Database", false)) {
            return 0;
        }
        try {
            $db = \Config\Database::connect();
            $row = $db->table("tb_nomination_candidates")
                ->selectMax("public_votes_count", "max_votes")
                ->where("category_id", $categoryId)
                ->where("season_id", $seasonId)
                ->where("status", "approved")
                ->get()
                ->getRow();

            return (int)($row->max_votes ?? 0);
        } catch (\Throwable $e) {
            return 0;
        }
    }
}
