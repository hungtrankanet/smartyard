<?php

namespace App\Models;

/**
 * MemberModel: Manages enterprise members, status, relations, and verification schedules
 */
class MemberModel extends BaseModel
{
    protected $table = 'members';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'company_name',
        'company_name_en',
        'company_name_local',
        'detected_language',
        'tax_code',
        'address',
        'city',
        'website',
        'fanpage',
        'phone',
        'email',
        'representative_name',
        'position',
        'industry_type_id',
        'member_type',
        'status',
        'verify_status',
        'last_verified_at',
        'next_verify_at',
        'note',
        'metadata',
        'preferred_lang',
        'created_at',
        'updated_at',
    ];
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('members');
    }

    /**
     * Build base query with join to industry_types and subquery for contact_count
     */
    private function buildBaseQuery()
    {
        return $this->db->table('members')
            ->select('members.*, industry_types.name AS industry_name, industry_types.icon AS industry_icon,
                     (SELECT COUNT(*) FROM member_contacts WHERE member_contacts.company_id = members.id) AS contact_count')
            ->join('industry_types', 'industry_types.id = members.industry_type_id', 'left');
    }

    /**
     * Apply filter conditions to a query builder instance (including 3-language search)
     */
    private function applyFilters($query, array $filters = [])
    {
        if (!empty($filters['q'])) {
            $q = strTrim($filters['q']);
            $query->groupStart()
                ->like('members.company_name', $q)
                ->orLike('members.company_name_en', $q)
                ->orLike('members.company_name_local', $q)
                ->orLike('members.representative_name', $q)
                ->orLike('members.phone', $q)
                ->orLike('members.email', $q)
                ->orLike('members.tax_code', $q)
                ->groupEnd();
        }

        if (!empty($filters['industry_type_id'])) {
            $query->where('members.industry_type_id', clrNum($filters['industry_type_id']));
        }

        if (!empty($filters['verify_status'])) {
            $query->where('members.verify_status', strTrim($filters['verify_status']));
        }

        if (!empty($filters['member_type'])) {
            $query->where('members.member_type', strTrim($filters['member_type']));
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('members.status', clrNum($filters['status']));
        }

        if (!empty($filters['city'])) {
            $query->like('members.city', strTrim($filters['city']));
        }

        return $query;
    }

    /**
     * Get paginated members list with filters
     */
    public function getMembersPaginated(int $perPage = 20, int $offset = 0, array $filters = []): array
    {
        $query = $this->buildBaseQuery();
        $query = $this->applyFilters($query, $filters);

        return $query
            ->orderBy('members.id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResult();
    }

    /**
     * Get total count of filtered members
     */
    public function getMembersCount(array $filters = []): int
    {
        $query = $this->db->table('members');
        $query = $this->applyFilters($query, $filters);
        return $query->countAllResults();
    }

    /**
     * Get member by ID with industry information and contact count
     */
    public function getMember($id)
    {
        return $this->buildBaseQuery()
            ->where('members.id', clrNum($id))
            ->get()
            ->getRow();
    }

    /**
     * Get member by ID with all relations (contacts, branches, cards, verify logs)
     */
    public function getMemberWithRelations($id)
    {
        $member = $this->getMember($id);
        if (empty($member)) {
            return null;
        }

        // Attach contacts
        $contactModel = new MemberContactModel();
        $member->contacts = $contactModel->getContactsByCompanyId($member->id);
        $member->primary_contact = $contactModel->getPrimaryContact($member->id);

        // Attach branches
        $branchModel = new MemberBranchModel();
        $member->branches = $branchModel->getBranchesByCompanyId($member->id);
        $member->headquarters = $branchModel->getHeadquarters($member->id);

        // Attach cards
        $cardModel = new MemberCardModel();
        $member->cards = $cardModel->getCardsByMemberId($member->id);

        // Attach verify logs
        $logModel = new MemberVerifyLogModel();
        $member->verify_logs = $logModel->getLogsByMemberId($member->id);

        return $member;
    }

    /**
     * Find member by exact tax code match
     */
    public function findByTaxCode(string $taxCode): ?object
    {
        $taxCode = strTrim($taxCode);
        if (empty($taxCode)) {
            return null;
        }

        $row = $this->db->table('members')
            ->where('tax_code', $taxCode)
            ->get()
            ->getRow();

        return $row ?: null;
    }

    /**
     * Find members matching a website domain or email domain
     */
    public function findByDomain(string $domain): array
    {
        $domain = strtolower(strTrim($domain));
        if (empty($domain)) {
            return [];
        }

        $cleanDomain = preg_replace('#^https?://#i', '', $domain);
        $cleanDomain = preg_replace('#^www\.#i', '', $cleanDomain);
        $cleanDomain = rtrim($cleanDomain, '/');

        if (empty($cleanDomain)) {
            return [];
        }

        return $this->db->table('members')
            ->groupStart()
                ->like('website', $cleanDomain)
                ->orLike('email', '@' . $cleanDomain)
            ->groupEnd()
            ->get()
            ->getResult();
    }

    /**
     * Find existing companies with fuzzy name similarity above threshold
     */
    public function findSimilarName(string $companyName, float $threshold = 80.0): array
    {
        $cleanInput = $this->normalizeCompanyNameForMatching($companyName);
        if (empty($cleanInput)) {
            return [];
        }

        $all = $this->getAllCompaniesForMatching();
        $matches = [];

        foreach ($all as $item) {
            $candidates = array_filter([
                $item->company_name ?? '',
                $item->company_name_en ?? '',
                $item->company_name_local ?? '',
            ]);

            $bestScore = 0.0;
            $matchedField = '';

            foreach ($candidates as $cand) {
                $cleanCand = $this->normalizeCompanyNameForMatching($cand);
                if (empty($cleanCand)) {
                    continue;
                }

                similar_text($cleanInput, $cleanCand, $percent);
                if ($percent > $bestScore) {
                    $bestScore = (float)$percent;
                    $matchedField = $cand;
                }
            }

            if ($bestScore >= $threshold) {
                $item->similarity = round($bestScore, 2);
                $item->matched_name = $matchedField;
                $matches[] = $item;
            }
        }

        usort($matches, function ($a, $b) {
            return ($b->similarity <=> $a->similarity);
        });

        return $matches;
    }

    /**
     * Get lightweight array of all active companies for CompanyMatcher auto-grouping
     */
    public function getAllCompaniesForMatching(): array
    {
        return $this->db->table('members')
            ->select('id, company_name, company_name_en, company_name_local, tax_code, website, phone, email')
            ->where('status', 1)
            ->get()
            ->getResult();
    }

    /**
     * Normalize company name for fuzzy matching by removing common suffixes and diacritics
     */
    private function normalizeCompanyNameForMatching(string $name): string
    {
        $str = mb_strtolower(strTrim($name), 'UTF-8');

        $accents = [
            'à','á','ạ','ả','ã','â','ầ','ấ','ậ','ẩ','ẫ','ă','ằ','ắ','ặ','ẳ','ẵ',
            'è','é','ẹ','ẻ','ẽ','ê','ề','ế','ệ','ể','ễ',
            'ì','í','ị','ỉ','ĩ',
            'ò','ó','ọ','ỏ','õ','ô','ồ','ố','ộ','ổ','ỗ','ơ','ờ','ớ','ợ','ở','ỡ',
            'ù','ú','ụ','ủ','ũ','ư','ừ','ứ','ự','ử','ữ',
            'ỳ','ý','ỵ','ỷ','ỹ',
            'đ',
        ];
        $replacements = [
            'a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a',
            'e','e','e','e','e','e','e','e','e','e','e',
            'i','i','i','i','i',
            'o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o',
            'u','u','u','u','u','u','u','u','u','u','u',
            'y','y','y','y','y',
            'd',
        ];
        $str = str_replace($accents, $replacements, $str);

        $removeKeywords = [
            'cong ty tnhh mtv', 'cong ty tnhh 1 tv', 'cong ty tnhh', 'cong ty co phan',
            'cong ty cp', 'cong ty', 'doanh nghiep tu nhan', 'dntn', 'tap doan',
            'co., ltd', 'co. ltd', 'co ltd', 'ltd.', 'ltd', 'corp.', 'corp',
            'corporation', 'inc.', 'inc', 'jsc', 'group', 'company',
        ];
        foreach ($removeKeywords as $kw) {
            $str = preg_replace('/\b' . preg_quote($kw, '/') . '\b/u', '', $str);
        }

        $str = preg_replace('/[^a-z0-9\s]/u', ' ', $str);
        $str = preg_replace('/\s+/', ' ', $str);
        return trim($str);
    }

    /**
     * Get members due for verification (next_verify_at <= NOW or next_verify_at IS NULL, and status = 1)
     */
    public function getMembersDueForVerification(int $limit = 50): array
    {
        $now = date('Y-m-d H:i:s');
        return $this->db->table('members')
            ->where('status', 1)
            ->groupStart()
                ->where('next_verify_at <=', $now)
                ->orWhere('next_verify_at IS NULL')
            ->groupEnd()
            ->orderBy('next_verify_at', 'ASC')
            ->limit($limit)
            ->get()
            ->getResult();
    }

    /**
     * Add a new member
     */
    public function addMember(array $data)
    {
        $companyName = strTrim($data['company_name'] ?? '');
        if (empty($companyName)) {
            return false;
        }

        $now = date('Y-m-d H:i:s');
        $nextVerifyAt = !empty($data['next_verify_at']) 
            ? $data['next_verify_at'] 
            : date('Y-m-d H:i:s', strtotime('+6 months'));

        $metadata = null;
        if (!empty($data['metadata'])) {
            $metadata = is_array($data['metadata']) ? json_encode($data['metadata'], JSON_UNESCAPED_UNICODE) : $data['metadata'];
        }

        $insertData = [
            'company_name'        => $companyName,
            'company_name_en'     => !empty($data['company_name_en']) ? strTrim($data['company_name_en']) : null,
            'company_name_local'  => !empty($data['company_name_local']) ? strTrim($data['company_name_local']) : null,
            'detected_language'   => in_array($data['detected_language'] ?? '', ['vi', 'en', 'zh', 'ja', 'ko', 'mixed', 'other']) ? $data['detected_language'] : 'vi',
            'tax_code'            => !empty($data['tax_code']) ? strTrim($data['tax_code']) : null,
            'address'             => !empty($data['address']) ? strTrim($data['address']) : null,
            'city'                => !empty($data['city']) ? strTrim($data['city']) : null,
            'website'             => !empty($data['website']) ? strTrim($data['website']) : null,
            'fanpage'             => !empty($data['fanpage']) ? strTrim($data['fanpage']) : null,
            'phone'               => !empty($data['phone']) ? strTrim($data['phone']) : null,
            'email'               => !empty($data['email']) ? strTrim($data['email']) : null,
            'representative_name' => !empty($data['representative_name']) ? strTrim($data['representative_name']) : null,
            'position'            => !empty($data['position']) ? strTrim($data['position']) : null,
            'industry_type_id'    => !empty($data['industry_type_id']) ? clrNum($data['industry_type_id']) : null,
            'member_type'         => in_array($data['member_type'] ?? '', ['prospect', 'member', 'partner']) ? $data['member_type'] : 'member',
            'status'              => isset($data['status']) ? clrNum($data['status']) : 1,
            'verify_status'       => in_array($data['verify_status'] ?? '', ['pending', 'verified', 'unverified', 'failed']) ? $data['verify_status'] : 'pending',
            'last_verified_at'    => !empty($data['last_verified_at']) ? $data['last_verified_at'] : null,
            'next_verify_at'      => $nextVerifyAt,
            'note'                => !empty($data['note']) ? strTrim($data['note']) : null,
            'metadata'            => $metadata,
            'created_at'          => $now,
            'updated_at'          => $now,
        ];

        if ($this->db->table($this->table)->insert($insertData)) {
            return $this->db->insertID();
        }
        return false;
    }

    /**
     * Update an existing member
     */
    public function updateMember($id, array $data): bool
    {
        $id = clrNum($id);
        $member = $this->getMember($id);
        if (empty($member)) {
            return false;
        }

        $fields = [
            'company_name', 'company_name_en', 'company_name_local', 'detected_language',
            'tax_code', 'address', 'city', 'website',
            'fanpage', 'phone', 'email', 'representative_name', 'position',
            'industry_type_id', 'member_type', 'status', 'verify_status',
            'last_verified_at', 'next_verify_at', 'note', 'metadata'
        ];

        $updateData = ['updated_at' => date('Y-m-d H:i:s')];
        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                if (in_array($field, ['industry_type_id', 'status'])) {
                    $updateData[$field] = $data[$field] !== null && $data[$field] !== '' ? clrNum($data[$field]) : null;
                } elseif ($field === 'metadata') {
                    $updateData[$field] = !empty($data[$field]) 
                        ? (is_array($data[$field]) ? json_encode($data[$field], JSON_UNESCAPED_UNICODE) : $data[$field]) 
                        : null;
                } else {
                    $updateData[$field] = ($data[$field] !== null && $data[$field] !== '') ? strTrim($data[$field]) : null;
                }
            }
        }

        return $this->db->table($this->table)->where('id', $id)->update($updateData);
    }

    /**
     * Update verification status & schedule
     */
    public function updateVerifyStatus($id, string $verifyStatus, ?string $lastVerifiedAt = null, ?string $nextVerifyAt = null): bool
    {
        $id = clrNum($id);
        $updateData = [
            'verify_status'    => in_array($verifyStatus, ['pending', 'verified', 'unverified', 'failed']) ? $verifyStatus : 'pending',
            'last_verified_at' => $lastVerifiedAt ?? date('Y-m-d H:i:s'),
            'next_verify_at'   => $nextVerifyAt ?? date('Y-m-d H:i:s', strtotime('+6 months')),
            'updated_at'       => date('Y-m-d H:i:s'),
        ];

        return $this->builder->where('id', $id)->update($updateData);
    }

    /**
     * Delete a member
     */
    public function deleteMember($id): bool
    {
        $id = clrNum($id);
        return $this->builder->where('id', $id)->delete();
    }

    /**
     * Get aggregate statistics for dashboard
     */
    public function getStats(): array
    {
        $total = $this->db->table('members')->countAllResults();
        $verified = $this->db->table('members')->where('verify_status', 'verified')->countAllResults();
        $pending = $this->db->table('members')->where('verify_status', 'pending')->countAllResults();
        $unverified = $this->db->table('members')->where('verify_status', 'unverified')->countAllResults();
        $failed = $this->db->table('members')->where('verify_status', 'failed')->countAllResults();

        return [
            'total'      => $total,
            'verified'   => $verified,
            'pending'    => $pending,
            'unverified' => $unverified,
            'failed'     => $failed,
        ];
    }

    public function getIndustryTypes(): array
    {
        return (new \App\Models\IndustryTypeModel())->getIndustries();
    }
}
