<?php

namespace App\Models;

/**
 * AwardSeasonModel: Manages award seasons, timeline, and status for TOP BEST GLOBAL
 */
class AwardSeasonModel extends BaseModel
{
    protected $table = 'tb_award_seasons';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'title', 'slug', 'theme_year', 'description', 'banner_image',
        'nomination_start_at', 'nomination_end_at', 'voting_start_at', 'voting_end_at',
        'gala_date', 'status', 'is_active', 'created_at', 'updated_at',
    ];
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table($this->table);
    }

    public function getActiveSeason()
    {
        $active = $this->builder->where('is_active', 1)->orderBy('id', 'DESC')->get()->getRow();
        if ($active) {
            return $active;
        }
        return $this->builder->orderBy('id', 'DESC')->get()->getRow();
    }

    public function getSeasons(int $limit = 50): array
    {
        return $this->builder->orderBy('theme_year', 'DESC')->orderBy('id', 'DESC')->limit($limit)->get()->getResult();
    }

    public function getSeason($id)
    {
        return $this->builder->where('id', clrNum($id))->get()->getRow();
    }

    public function getSeasonBySlug(string $slug)
    {
        return $this->builder->where('slug', cleanSlug($slug))->get()->getRow();
    }

    public function addSeason(array $data)
    {
        $title = strTrim($data['title'] ?? '');
        if (empty($title)) {
            return false;
        }

        $slug = !empty($data['slug']) ? cleanSlug($data['slug']) : strSlug($title);
        $insertData = [
            'title'               => $title,
            'slug'                => $slug,
            'theme_year'          => !empty($data['theme_year']) ? clrNum($data['theme_year']) : (int)date('Y'),
            'description'         => !empty($data['description']) ? strTrim($data['description']) : null,
            'banner_image'        => !empty($data['banner_image']) ? strTrim($data['banner_image']) : null,
            'nomination_start_at' => !empty($data['nomination_start_at']) ? $data['nomination_start_at'] : null,
            'nomination_end_at'   => !empty($data['nomination_end_at']) ? $data['nomination_end_at'] : null,
            'voting_start_at'     => !empty($data['voting_start_at']) ? $data['voting_start_at'] : null,
            'voting_end_at'       => !empty($data['voting_end_at']) ? $data['voting_end_at'] : null,
            'gala_date'           => !empty($data['gala_date']) ? $data['gala_date'] : null,
            'status'              => !empty($data['status']) ? strTrim($data['status']) : 'draft',
            'is_active'           => !empty($data['is_active']) ? 1 : 0,
            'created_at'          => date('Y-m-d H:i:s'),
            'updated_at'          => date('Y-m-d H:i:s'),
        ];

        if ($insertData['is_active'] === 1) {
            $this->builder->set(['is_active' => 0])->update();
        }

        if ($this->builder->insert($insertData)) {
            return $this->db->insertID();
        }
        return false;
    }

    public function updateSeason($id, array $data): bool
    {
        $id = clrNum($id);
        $season = $this->getSeason($id);
        if (!$season) {
            return false;
        }

        $updateData = [];
        $fields = ['title', 'slug', 'theme_year', 'description', 'banner_image', 'nomination_start_at', 'nomination_end_at', 'voting_start_at', 'voting_end_at', 'gala_date', 'status'];
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }
        if (isset($data['is_active'])) {
            $updateData['is_active'] = !empty($data['is_active']) ? 1 : 0;
            if ($updateData['is_active'] === 1) {
                $this->builder->where('id !=', $id)->set(['is_active' => 0])->update();
            }
        }
        $updateData['updated_at'] = date('Y-m-d H:i:s');

        return $this->builder->where('id', $id)->update($updateData);
    }

    public function deleteSeason($id): bool
    {
        $id = clrNum($id);
        return $this->builder->where('id', $id)->delete();
    }
}
