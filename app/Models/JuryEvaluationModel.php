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
}
