<?php

namespace App\Models;

/**
 * IndustryTypeModel: Manages industry categories for business members
 */
class IndustryTypeModel extends BaseModel
{
    protected $table = 'industry_types';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'name',
        'name_slug',
        'icon',
        'description',
        'sort_order',
        'created_at',
    ];
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('industry_types');
    }

    /**
     * Get all active industries sorted by sort_order and name
     */
    public function getIndustries(): array
    {
        return $this->builder
            ->orderBy('sort_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResult();
    }

    /**
     * Get single industry by ID
     */
    public function getIndustry($id)
    {
        return $this->builder
            ->where('id', clrNum($id))
            ->get()
            ->getRow();
    }

    /**
     * Get single industry by Slug
     */
    public function getIndustryBySlug(string $slug)
    {
        return $this->builder
            ->where('name_slug', cleanSlug($slug))
            ->get()
            ->getRow();
    }

    /**
     * Add new industry
     */
    public function addIndustry(array $data)
    {
        $name = strTrim($data['name'] ?? '');
        if (empty($name)) {
            return false;
        }

        $slug = !empty($data['name_slug']) ? cleanSlug($data['name_slug']) : strSlug($name);
        if (empty($slug)) {
            $slug = 'industry-' . uniqid();
        }

        $insertData = [
            'name'        => $name,
            'name_slug'   => $slug,
            'icon'        => !empty($data['icon']) ? strTrim($data['icon']) : 'fa fa-folder',
            'description' => !empty($data['description']) ? strTrim($data['description']) : null,
            'sort_order'  => isset($data['sort_order']) ? clrNum($data['sort_order']) : 0,
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        if ($this->builder->insert($insertData)) {
            return $this->db->insertID();
        }
        return false;
    }

    /**
     * Update existing industry
     */
    public function updateIndustry($id, array $data): bool
    {
        $id = clrNum($id);
        $industry = $this->getIndustry($id);
        if (empty($industry)) {
            return false;
        }

        $updateData = [];
        if (isset($data['name'])) {
            $updateData['name'] = strTrim($data['name']);
            if (empty($data['name_slug'])) {
                $updateData['name_slug'] = strSlug($updateData['name']);
            }
        }
        if (isset($data['name_slug'])) {
            $updateData['name_slug'] = cleanSlug($data['name_slug']);
        }
        if (isset($data['icon'])) {
            $updateData['icon'] = strTrim($data['icon']);
        }
        if (isset($data['description'])) {
            $updateData['description'] = strTrim($data['description']);
        }
        if (isset($data['sort_order'])) {
            $updateData['sort_order'] = clrNum($data['sort_order']);
        }

        if (!empty($updateData)) {
            return $this->builder->where('id', $id)->update($updateData);
        }
        return false;
    }

    /**
     * Delete industry by ID
     */
    public function deleteIndustry($id): bool
    {
        $id = clrNum($id);
        return $this->builder->where('id', $id)->delete();
    }

    /**
     * Get industries with member count
     */
    public function getIndustriesWithMemberCount(): array
    {
        return $this->db->table('industry_types')
            ->select('industry_types.*, COUNT(members.id) AS member_count')
            ->join('members', 'members.industry_type_id = industry_types.id', 'left')
            ->groupBy('industry_types.id')
            ->orderBy('industry_types.sort_order', 'ASC')
            ->orderBy('industry_types.name', 'ASC')
            ->get()
            ->getResult();
    }

    /**
     * Smart Auto-detection of Industry ID based on business text (name, keywords, position, note)
     */
    public function detectIndustryId(string $text, array $industries = []): ?int
    {
        if (empty($industries)) {
            $industries = $this->getIndustries();
        }
        if (empty($industries) || empty(trim($text))) {
            return null;
        }

        $normalized = mb_strtolower(trim($text), 'UTF-8');
        $unaccented = strSlug($normalized);

        $keywordRules = [
            'xuat-nhap-khau-thuong-mai-quoc-te' => ['xuat nhap khau', 'xnk', 'import', 'export', 'trading', 'thuong mai', 'mau dich', 'international trade', 'global trade', 'commerce', 'mậu dịch', 'xuất nhập khẩu', 'thương mại'],
            'van-tai-logistics' => ['logistics', 'van tai', 'giao nhan', 'forwarding', 'freight', 'shipping', 'kho van', 'warehousing', 'cfs', 'container', 'seaway', 'airway', 'express', 'chuyen phat', 'tau bien', 'trucking', 'hai quan', 'customs', 'vận tải', 'giao nhận', 'hải quan'],
            'san-xuat-che-bien' => ['san xuat', 'che bien', 'manufacturing', 'factory', 'nha may', 'production', 'gia cong', 'processing', 'cong nghiep', 'industrial', 'che tao', 'sản xuất', 'chế biến', 'gia công', 'nhà máy'],
            'phan-phoi-ban-le' => ['phan phoi', 'ban le', 'retail', 'distribution', 'dai ly', 'sieu thi', 'mart', 'store', 'shop', 'wholesale', 'ban si', 'chuoi cua hang', 'distributor', 'phân phối', 'bán lẻ', 'đại lý'],
            'cong-nghe-phan-mem' => ['cong nghe', 'phan mem', 'software', 'technology', 'digital', 'chuyen doi so', 'tech', 'telecom', 'vien thong', 'solution', 'cloud', 'công nghệ', 'phần mềm', 'viễn thông'],
            'tai-chinh-ngan-hang' => ['tai chinh', 'ngan hang', 'bank', 'finance', 'financial', 'bao hiem', 'insurance', 'chung khoan', 'securities', 'investment', 'dau tu', 'fintech', 'thanh toan', 'tài chính', 'ngân hàng'],
            'bat-dong-san-xay-dung' => ['bat dong san', 'xay dung', 'real estate', 'property', 'construction', 'building', 'kien truc', 'dia oc', 'kcn', 'industrial park', 'ha tang', 'infrastructure', 'kho bai', 'bất động sản', 'xây dựng'],
            'thuc-pham-do-uong' => ['thuc pham', 'do uong', 'food', 'beverage', 'f&b', 'nong san', 'seafood', 'coffee', 'tea', 'ca phe', 'tra', 'banh keo', 'dairy', 'sua', 'thit', 'thực phẩm', 'đồ uống', 'cà phê'],
            'y-te-duoc-pham' => ['y te', 'duoc pham', 'medical', 'pharma', 'pharmaceutical', 'hospital', 'benh vien', 'phong kham', 'clinic', 'thiet bi y te', 'healthcare', 'thuoc', 'medicine', 'y tế', 'dược phẩm'],
            'giao-duc-dao-tao' => ['giao duc', 'dao tao', 'education', 'training', 'school', 'truong hoc', 'academy', 'hoc vien', 'university', 'dai hoc', 'du hoc', 'giáo dục', 'đào tạo', 'trường học'],
            'dich-vu-chuyen-nghiep-luat-ke-toan-tu-van' => ['luat', 'law', 'legal', 'ke toan', 'accounting', 'audit', 'kiem toan', 'tu van', 'consulting', 'advisory', 'tax', 'thue', 'luat su', 'luật', 'kế toán', 'kiểm toán', 'tư vấn'],
            'nong-nghiep-thuy-san' => ['nong nghiep', 'thuy san', 'agriculture', 'fishery', 'aquaculture', 'hai san', 'tom', 'ca', 'lua', 'gao', 'rice', 'farming', 'agri', 'trai cay', 'nông nghiệp', 'thủy sản'],
            'nang-luong-moi-truong' => ['nang luong', 'moi truong', 'energy', 'solar', 'wind', 'power', 'dien', 'tai tao', 'renewable', 'environment', 'waste', 'chat thai', 'green', 'xanh', 'năng lượng', 'môi trường'],
            'du-lich-khach-san' => ['du lich', 'khach san', 'hotel', 'resort', 'tourism', 'travel', 'tour', 'hospitality', 'nghi duong', 'lu hanh', 've may bay', 'du lịch', 'khách sạn', 'lữ hành'],
        ];

        $bestMatchId = null;
        $maxMatches = 0;

        foreach ($industries as $ind) {
            $slug = $ind->name_slug ?? strSlug($ind->name);
            $keywords = $keywordRules[$slug] ?? [];
            $indWords = explode(' ', mb_strtolower($ind->name, 'UTF-8'));
            foreach ($indWords as $w) {
                if (mb_strlen($w, 'UTF-8') > 3 && !in_array($w, $keywords)) $keywords[] = $w;
            }

            $matches = 0;
            foreach ($keywords as $kw) {
                if (mb_stripos($normalized, $kw) !== false || stripos($unaccented, strSlug($kw)) !== false) {
                    $matches += (mb_strlen($kw, 'UTF-8') > 6 ? 2 : 1);
                }
            }

            if ($matches > $maxMatches) {
                $maxMatches = $matches;
                $bestMatchId = $ind->id;
            }
        }

        if ($bestMatchId !== null && $maxMatches > 0) {
            return $bestMatchId;
        }

        foreach ($industries as $ind) {
            if (($ind->name_slug ?? '') === 'khac' || mb_stripos($ind->name, 'khác') !== false) {
                return $ind->id;
            }
        }

        return $industries[0]->id ?? null;
    }

    /**
     * Get total industry count
     */
    public function getIndustryCount(): int
    {
        return $this->builder->countAllResults();
    }
}
