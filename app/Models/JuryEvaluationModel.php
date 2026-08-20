<?php

namespace App\Models;

/**
 * JuryEvaluationModel: Manages expert jury scoring rubrics, criteria scores, and evaluation submissions
 */
class JuryEvaluationModel extends BaseModel
{
    protected $table = 'tb_jury_evaluations';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'candidate_id', 'season_id', 'category_id', 'jury_user_id', 'criteria_1_score',
        'criteria_2_score', 'criteria_3_score', 'criteria_4_score', 'total_score', 'notes',
        'is_submitted', 'submitted_at', 'created_at', 'updated_at',
    ];
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table($this->table);
    }

    public function submitEvaluation(
        int $candidateId,
        int $seasonId,
        int $categoryId,
        int $juryUserId,
        array $criteriaScores,
        ?string $notes = null
    ): bool {
        $c1 = isset($criteriaScores['c1']) ? (float)$criteriaScores['c1'] : 0.00;
        $c2 = isset($criteriaScores['c2']) ? (float)$criteriaScores['c2'] : 0.00;
        $c3 = isset($criteriaScores['c3']) ? (float)$criteriaScores['c3'] : 0.00;
        $c4 = isset($criteriaScores['c4']) ? (float)$criteriaScores['c4'] : 0.00;

        $totalScore = round(($c1 + $c2 + $c3 + $c4) / 4, 2);
        $now = date('Y-m-d H:i:s');

        $existing = $this->builder
            ->where('candidate_id', clrNum($candidateId))
            ->where('jury_user_id', clrNum($juryUserId))
            ->get()
            ->getRow();

        $data = [
            'candidate_id'     => clrNum($candidateId),
            'season_id'        => clrNum($seasonId),
            'category_id'      => clrNum($categoryId),
            'jury_user_id'     => clrNum($juryUserId),
            'criteria_1_score' => $c1,
            'criteria_2_score' => $c2,
            'criteria_3_score' => $c3,
            'criteria_4_score' => $c4,
            'total_score'      => $totalScore,
            'notes'            => $notes ? strTrim($notes) : null,
            'is_submitted'     => 1,
            'submitted_at'     => $now,
            'updated_at'       => $now,
        ];

        if ($existing) {
            return $this->builder->where('id', $existing->id)->update($data);
        } else {
            $data['created_at'] = $now;
            return (bool)$this->builder->insert($data);
        }
    }

    public function getCandidateJuryAverage(int $candidateId): array
    {
        $res = $this->builder
            ->select('AVG(total_score) AS avg_score, COUNT(id) AS total_evaluations')
            ->where('candidate_id', clrNum($candidateId))
            ->where('is_submitted', 1)
            ->get()
            ->getRow();

        return [
            'avg_score'         => $res && $res->avg_score !== null ? round((float)$res->avg_score, 2) : 0.00,
            'total_evaluations' => $res ? (int)$res->total_evaluations : 0,
        ];
    }

    public function getEvaluationByJuryAndCandidate(int $juryUserId, int $candidateId)
    {
        return $this->builder
            ->where('jury_user_id', clrNum($juryUserId))
            ->where('candidate_id', clrNum($candidateId))
            ->get()
            ->getRow();
    }

    public function getEvaluationsByCandidate(int $candidateId): array
    {
        return $this->db->table('tb_jury_evaluations e')
            ->select('e.*, u.username AS jury_name, u.email AS jury_email')
            ->join('users u', 'u.id = e.jury_user_id', 'left')
            ->where('e.candidate_id', clrNum($candidateId))
            ->where('e.is_submitted', 1)
            ->orderBy('e.id', 'DESC')
            ->get()
            ->getResult();
    }

    public function saveEvaluation(array $data): bool
    {
        $candidateId = clrNum($data['candidate_id'] ?? 0);
        $juryUserId  = clrNum($data['jury_user_id'] ?? ($data['judge_id'] ?? 1));
        $seasonId    = clrNum($data['season_id'] ?? 1);
        $categoryId  = clrNum($data['category_id'] ?? 1);

        $rubric = $data['rubric_scores'] ?? null;
        if (is_string($rubric)) {
            $rubric = json_decode($rubric, true);
        }

        $c1 = isset($rubric['innovation']) ? (float)$rubric['innovation'] : (float)($data['criteria_1_score'] ?? 0.00);
        $c2 = isset($rubric['business']) ? (float)$rubric['business'] : (float)($data['criteria_2_score'] ?? 0.00);
        $c3 = isset($rubric['social']) ? (float)$rubric['social'] : (float)($data['criteria_3_score'] ?? 0.00);
        $c4 = isset($rubric['brand']) ? (float)$rubric['brand'] : (float)($data['criteria_4_score'] ?? 0.00);

        $totalScore = isset($data['total_score'])
            ? (float)$data['total_score']
            : round(($c1 * 0.25) + ($c2 * 0.30) + ($c3 * 0.25) + ($c4 * 0.20), 2);

        $notes = $data['notes'] ?? ($data['evaluation_notes'] ?? null);

        return $this->submitEvaluation($candidateId, $seasonId, $categoryId, $juryUserId, [
            'c1' => $c1,
            'c2' => $c2,
            'c3' => $c3,
            'c4' => $c4,
        ], $notes);
    }

    public function countEvaluationsFiltered(array $filters = []): int
    {
        $builder = $this->db->table('tb_jury_evaluations e');
        $this->applyJuryFilters($builder, $filters);
        return $builder->countAllResults();
    }

    public function getEvaluationsFiltered(array $filters = [], int $perPage = 20, int $offset = 0): array
    {
        $builder = $this->db->table('tb_jury_evaluations e')
            ->select('e.*, c.name AS candidate_name, c.organization_name, c.candidate_code, cat.name AS category_name, u.username AS jury_name, u.email AS jury_email')
            ->join('tb_nomination_candidates c', 'c.id = e.candidate_id', 'left')
            ->join('tb_award_categories cat', 'cat.id = e.category_id', 'left')
            ->join('users u', 'u.id = e.jury_user_id', 'left');

        $this->applyJuryFilters($builder, $filters);

        return $builder
            ->orderBy('e.id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResult();
    }

    private function applyJuryFilters(&$builder, array $filters): void
    {
        if (!empty($filters['candidate_id'])) {
            $builder->where('e.candidate_id', clrNum($filters['candidate_id']));
        }
        if (!empty($filters['season_id'])) {
            $builder->where('e.season_id', clrNum($filters['season_id']));
        }
        if (!empty($filters['category_id'])) {
            $builder->where('e.category_id', clrNum($filters['category_id']));
        }
        if (!empty($filters['jury_user_id'])) {
            $builder->where('e.jury_user_id', clrNum($filters['jury_user_id']));
        }
    }
}
