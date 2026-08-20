<?php

namespace App\Models;

/**
 * NominationCandidateModel: Manages candidate dossiers, atomic vote increments, and composite scores
 */
class NominationCandidateModel extends BaseModel
{
    protected $table = 'tb_nomination_candidates';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'season_id', 'category_id', 'candidate_code', 'name', 'organization_name', 'slug',
        'candidate_type', 'avatar', 'cover_image', 'bio_summary', 'dossier_content', 'tax_code',
        'contact_person', 'contact_email', 'contact_phone', 'website', 'stage', 'public_votes_count',
        'jury_score_avg', 'composite_score', 'final_rank', 'award_title', 'certificate_serial',
        'digital_badge_url', 'is_featured', 'status', 'created_at', 'updated_at',
    ];
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table($this->table);
    }

    public function getCandidate($id)
    {
        return $this->db->table('tb_nomination_candidates c')
            ->select('c.*, cat.name AS category_name, cat.slug AS category_slug, cat.industry_sector, cat.jury_weight, cat.public_weight, s.title AS season_title, s.theme_year')
            ->join('tb_award_categories cat', 'cat.id = c.category_id', 'left')
            ->join('tb_award_seasons s', 's.id = c.season_id', 'left')
            ->where('c.id', clrNum($id))
            ->get()
            ->getRow();
    }

    public function getCandidateByCode(string $code)
    {
        return $this->db->table('tb_nomination_candidates c')
            ->select('c.*, cat.name AS category_name, cat.slug AS category_slug, s.title AS season_title')
            ->join('tb_award_categories cat', 'cat.id = c.category_id', 'left')
            ->join('tb_award_seasons s', 's.id = c.season_id', 'left')
            ->where('c.candidate_code', cleanStr($code))
            ->get()
            ->getRow();
    }

    public function getCandidateBySlug(string $slug)
    {
        return $this->db->table('tb_nomination_candidates c')
            ->select('c.*, cat.name AS category_name, cat.slug AS category_slug, s.title AS season_title')
            ->join('tb_award_categories cat', 'cat.id = c.category_id', 'left')
            ->join('tb_award_seasons s', 's.id = c.season_id', 'left')
            ->where('c.slug', cleanSlug($slug))
            ->get()
            ->getRow();
    }

    public function getCandidateByCertificateSerial(string $serial)
    {
        return $this->db->table('tb_nomination_candidates c')
            ->select('c.*, cat.name AS category_name, cat.slug AS category_slug, cat.industry_sector, s.title AS season_title, s.theme_year')
            ->join('tb_award_categories cat', 'cat.id = c.category_id', 'left')
            ->join('tb_award_seasons s', 's.id = c.season_id', 'left')
            ->where('c.certificate_serial', cleanStr($serial))
            ->get()
            ->getRow();
    }

    public function getCandidatesForVoting(int $categoryId, ?int $seasonId = null): array
    {
        $builder = $this->db->table('tb_nomination_candidates c')
            ->select('c.*, cat.name AS category_name')
            ->join('tb_award_categories cat', 'cat.id = c.category_id', 'left')
            ->where('c.category_id', clrNum($categoryId))
            ->where('c.status', 'approved')
            ->whereIn('c.stage', ['voting', 'evaluation', 'final']);

        if ($seasonId !== null) {
            $builder->where('c.season_id', clrNum($seasonId));
        }

        return $builder
            ->orderBy('c.composite_score', 'DESC')
            ->orderBy('c.public_votes_count', 'DESC')
            ->orderBy('c.id', 'ASC')
            ->get()
            ->getResult();
    }

    public function getFeaturedCandidates(int $limit = 6): array
    {
        return $this->db->table('tb_nomination_candidates c')
            ->select('c.*, cat.name AS category_name, cat.slug AS category_slug, cat.industry_sector')
            ->join('tb_award_categories cat', 'cat.id = c.category_id', 'left')
            ->where('c.is_featured', 1)
            ->where('c.status', 'approved')
            ->orderBy('c.composite_score', 'DESC')
            ->orderBy('c.public_votes_count', 'DESC')
            ->limit($limit)
            ->get()
            ->getResult();
    }

    public function getHallOfFameHonorees(?int $seasonId = null, int $limit = 50): array
    {
        $builder = $this->db->table('tb_nomination_candidates c')
            ->select('c.*, cat.name AS category_name, cat.slug AS category_slug, cat.industry_sector, s.theme_year')
            ->join('tb_award_categories cat', 'cat.id = c.category_id', 'left')
            ->join('tb_award_seasons s', 's.id = c.season_id', 'left')
            ->where('c.stage', 'awarded')
            ->where('c.status', 'approved');

        if ($seasonId !== null) {
            $builder->where('c.season_id', clrNum($seasonId));
        }

        return $builder
            ->orderBy('c.final_rank', 'ASC')
            ->orderBy('c.composite_score', 'DESC')
            ->limit($limit)
            ->get()
            ->getResult();
    }

    public function incrementVotesAtomic(int $candidateId): bool
    {
        $id = clrNum($candidateId);
        $sql = "UPDATE tb_nomination_candidates SET public_votes_count = public_votes_count + 1, updated_at = NOW() WHERE id = ?";
        return (bool)$this->db->query($sql, [$id]);
    }

    public function updateScoresAndRank(int $candidateId, float $juryScore, float $compositeScore, int $rank = 0): bool
    {
        return $this->builder
            ->where('id', clrNum($candidateId))
            ->update([
                'jury_score_avg'  => $juryScore,
                'composite_score' => $compositeScore,
                'final_rank'      => $rank,
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);
    }

    public function addCandidate(array $data)
    {
        $name = strTrim($data['name'] ?? '');
        if (empty($name)) {
            return false;
        }

        $code = !empty($data['candidate_code']) ? cleanStr($data['candidate_code']) : 'TBG-' . date('Y') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
        $slug = !empty($data['slug']) ? cleanSlug($data['slug']) : strSlug($name);

        $insertData = [
            'season_id'          => !empty($data['season_id']) ? clrNum($data['season_id']) : 1,
            'category_id'        => !empty($data['category_id']) ? clrNum($data['category_id']) : 1,
            'candidate_code'     => $code,
            'name'               => $name,
            'organization_name'  => !empty($data['organization_name']) ? strTrim($data['organization_name']) : $name,
            'slug'               => $slug,
            'candidate_type'     => !empty($data['candidate_type']) ? strTrim($data['candidate_type']) : 'enterprise',
            'avatar'             => !empty($data['avatar']) ? strTrim($data['avatar']) : null,
            'cover_image'        => !empty($data['cover_image']) ? strTrim($data['cover_image']) : null,
            'bio_summary'        => !empty($data['bio_summary']) ? strTrim($data['bio_summary']) : null,
            'dossier_content'    => !empty($data['dossier_content']) ? $data['dossier_content'] : null,
            'tax_code'           => !empty($data['tax_code']) ? strTrim($data['tax_code']) : null,
            'contact_person'     => !empty($data['contact_person']) ? strTrim($data['contact_person']) : null,
            'contact_email'      => !empty($data['contact_email']) ? strTrim($data['contact_email']) : null,
            'contact_phone'      => !empty($data['contact_phone']) ? strTrim($data['contact_phone']) : null,
            'website'            => !empty($data['website']) ? strTrim($data['website']) : null,
            'stage'              => !empty($data['stage']) ? strTrim($data['stage']) : 'preliminary',
            'public_votes_count' => 0,
            'jury_score_avg'     => 0.00,
            'composite_score'    => 0.00,
            'final_rank'         => 0,
            'award_title'        => !empty($data['award_title']) ? strTrim($data['award_title']) : null,
            'certificate_serial' => !empty($data['certificate_serial']) ? strTrim($data['certificate_serial']) : null,
            'digital_badge_url'  => !empty($data['digital_badge_url']) ? strTrim($data['digital_badge_url']) : null,
            'is_featured'        => !empty($data['is_featured']) ? 1 : 0,
            'status'             => !empty($data['status']) ? strTrim($data['status']) : 'approved',
            'created_at'         => date('Y-m-d H:i:s'),
            'updated_at'         => date('Y-m-d H:i:s'),
        ];

        if ($this->builder->insert($insertData)) {
            return $this->db->insertID();
        }
        return false;
    }

    public function updateCandidate($id, array $data): bool
    {
        $id = clrNum($id);
        $candidate = $this->getCandidate($id);
        if (!$candidate) {
            return false;
        }

        $fields = [
            'season_id', 'category_id', 'candidate_code', 'name', 'organization_name', 'slug',
            'candidate_type', 'avatar', 'cover_image', 'bio_summary', 'dossier_content', 'tax_code',
            'contact_person', 'contact_email', 'contact_phone', 'website', 'stage', 'award_title',
            'certificate_serial', 'digital_badge_url', 'is_featured', 'status', 'public_votes_count',
            'jury_score_avg', 'composite_score', 'final_rank'
        ];

        $updateData = [];
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }
        $updateData['updated_at'] = date('Y-m-d H:i:s');

        return $this->builder->where('id', $id)->update($updateData);
    }

    public function updateAverageJuryScore(int $candidateId): bool
    {
        $id = clrNum($candidateId);
        $juryEvalModel = new \App\Models\JuryEvaluationModel();
        $avgData = $juryEvalModel->getCandidateJuryAverage($id);
        $avgScore = (float)($avgData['avg_score'] ?? 0.00);

        $candidate = $this->getCandidate($id);
        if (!$candidate) {
            return false;
        }

        $juryWeight = isset($candidate->jury_weight) ? (float)$candidate->jury_weight : 70.00;
        $compositeScore = round(($avgScore * ($juryWeight / 100)), 2);

        return $this->builder
            ->where('id', $id)
            ->update([
                'jury_score_avg'  => $avgScore,
                'composite_score' => $compositeScore,
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);
    }

    public function countCandidatesFiltered(array $filters = []): int
    {
        $builder = $this->db->table('tb_nomination_candidates c');
        $this->applyFilters($builder, $filters);
        return $builder->countAllResults();
    }

    public function getCandidatesFiltered(array $filters = [], int $perPage = 20, int $offset = 0): array
    {
        $builder = $this->db->table('tb_nomination_candidates c')
            ->select('c.*, cat.name AS category_name, cat.slug AS category_slug, cat.industry_sector, s.title AS season_title, s.theme_year')
            ->join('tb_award_categories cat', 'cat.id = c.category_id', 'left')
            ->join('tb_award_seasons s', 's.id = c.season_id', 'left');

        $this->applyFilters($builder, $filters);

        return $builder
            ->orderBy('c.id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResult();
    }

    private function applyFilters(&$builder, array $filters): void
    {
        if (!empty($filters['season_id'])) {
            $builder->where('c.season_id', clrNum($filters['season_id']));
        }
        if (!empty($filters['category_id'])) {
            $builder->where('c.category_id', clrNum($filters['category_id']));
        }
        if (!empty($filters['stage'])) {
            $builder->where('c.stage', cleanStr($filters['stage']));
        }
        if (!empty($filters['status'])) {
            $builder->where('c.status', cleanStr($filters['status']));
        }
        if (!empty($filters['q'])) {
            $q = cleanStr($filters['q']);
            $builder->groupStart()
                ->like('c.name', $q)
                ->orLike('c.organization_name', $q)
                ->orLike('c.candidate_code', $q)
                ->orLike('c.tax_code', $q)
                ->orLike('c.contact_email', $q)
                ->groupEnd();
        }
    }

    public function deleteCandidate($id): bool
    {
        $id = clrNum($id);
        return $this->builder->where('id', $id)->delete();
    }
}
