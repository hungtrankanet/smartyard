<?php

namespace App\Models;

/**
 * AwardCategoryModel: Manages award categories across 15+ socio-economic sectors
 */
class AwardCategoryModel extends BaseModel
{
    protected $table = 'tb_award_categories';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'season_id', 'parent_id', 'name', 'slug', 'industry_sector', 'description',
        'icon', 'order_num', 'jury_weight', 'public_weight', 'status', 'created_at', 'updated_at',
    ];
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table($this->table);
    }

    public function getCategoriesBySeason(int $seasonId = 1, string $status = 'active'): array
    {
        return $this->builder
            ->where('season_id', clrNum($seasonId))
            ->where('status', $status)
            ->orderBy('order_num', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResult();
    }

    public function getCategory($id)
    {
        return $this->builder->where('id', clrNum($id))->get()->getRow();
    }

    public function getCategoryBySlug(string $slug, ?int $seasonId = null)
    {
        $query = $this->builder->where('slug', cleanSlug($slug));
        if ($seasonId !== null) {
            $query->where('season_id', clrNum($seasonId));
        }
        return $query->get()->getRow();
    }

    public function getCategoriesGroupedBySector(int $seasonId = 1): array
    {
        $categories = $this->getCategoriesBySeason($seasonId);
        $grouped = [];
        foreach ($categories as $cat) {
            $sector = !empty($cat->industry_sector) ? $cat->industry_sector : 'General';
            if (!isset($grouped[$sector])) {
                $grouped[$sector] = [];
            }
            $grouped[$sector][] = $cat;
        }
        return $grouped;
    }

    public function addCategory(array $data)
    {
        $name = strTrim($data['name'] ?? '');
        if (empty($name)) {
            return false;
        }

        $slug = !empty($data['slug']) ? cleanSlug($data['slug']) : strSlug($name);
        $insertData = [
            'season_id'       => !empty($data['season_id']) ? clrNum($data['season_id']) : 1,
            'parent_id'       => !empty($data['parent_id']) ? clrNum($data['parent_id']) : 0,
            'name'            => $name,
            'slug'            => $slug,
            'industry_sector' => !empty($data['industry_sector']) ? strTrim($data['industry_sector']) : 'General',
            'description'     => !empty($data['description']) ? strTrim($data['description']) : null,
            'icon'            => !empty($data['icon']) ? strTrim($data['icon']) : 'fa fa-award',
            'order_num'       => isset($data['order_num']) ? clrNum($data['order_num']) : 0,
            'jury_weight'     => isset($data['jury_weight']) ? (float)$data['jury_weight'] : 70.00,
            'public_weight'   => isset($data['public_weight']) ? (float)$data['public_weight'] : 30.00,
            'status'          => !empty($data['status']) ? strTrim($data['status']) : 'active',
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ];

        if ($this->builder->insert($insertData)) {
            return $this->db->insertID();
        }
        return false;
    }

    public function updateCategory($id, array $data): bool
    {
        $id = clrNum($id);
        $category = $this->getCategory($id);
        if (!$category) {
            return false;
        }

        $updateData = [];
        $fields = ['season_id', 'parent_id', 'name', 'slug', 'industry_sector', 'description', 'icon', 'order_num', 'jury_weight', 'public_weight', 'status'];
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }
        $updateData['updated_at'] = date('Y-m-d H:i:s');

        return $this->builder->where('id', $id)->update($updateData);
    }

    public function deleteCategory($id): bool
    {
        $id = clrNum($id);
        return $this->builder->where('id', $id)->delete();
    }
}
