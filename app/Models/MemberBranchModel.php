<?php

namespace App\Models;

/**
 * MemberBranchModel: Manages regional branches and headquarters of enterprises
 */
class MemberBranchModel extends BaseModel
{
    protected $table = 'member_branches';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'company_id',
        'branch_name',
        'country',
        'city',
        'address',
        'phone',
        'email',
        'is_headquarters',
        'metadata',
        'created_at',
    ];
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('member_branches');
    }

    /**
     * Get all branches for a company ordered by headquarters first, then id
     */
    public function getBranchesByCompanyId(int $companyId): array
    {
        return $this->builder
            ->where('company_id', clrNum($companyId))
            ->orderBy('is_headquarters', 'DESC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResult();
    }

    /**
     * Get single branch by ID
     */
    public function getBranch(int $id)
    {
        return $this->builder->where('id', clrNum($id))->get()->getRow();
    }

    /**
     * Get headquarters branch for a company
     */
    public function getHeadquarters(int $companyId)
    {
        return $this->builder
            ->where('company_id', clrNum($companyId))
            ->where('is_headquarters', 1)
            ->get()
            ->getRow();
    }

    /**
     * Add a new branch for a company
     */
    public function addBranch(int $companyId, array $data)
    {
        $branchName = strTrim($data['branch_name'] ?? '');
        if (empty($branchName)) {
            return false;
        }

        $companyId = clrNum($companyId);
        $isHeadquarters = !empty($data['is_headquarters']) ? 1 : 0;

        if ($isHeadquarters) {
            $this->builder->where('company_id', $companyId)->update(['is_headquarters' => 0]);
        }

        $metadata = null;
        if (!empty($data['metadata'])) {
            $metadata = is_array($data['metadata']) ? json_encode($data['metadata'], JSON_UNESCAPED_UNICODE) : $data['metadata'];
        }

        $insertData = [
            'company_id'      => $companyId,
            'branch_name'     => $branchName,
            'country'         => !empty($data['country']) ? strTrim($data['country']) : null,
            'city'            => !empty($data['city']) ? strTrim($data['city']) : null,
            'address'         => !empty($data['address']) ? strTrim($data['address']) : null,
            'phone'           => !empty($data['phone']) ? strTrim($data['phone']) : null,
            'email'           => !empty($data['email']) ? strTrim($data['email']) : null,
            'is_headquarters' => $isHeadquarters,
            'metadata'        => $metadata,
            'created_at'      => date('Y-m-d H:i:s'),
        ];

        if ($this->builder->insert($insertData)) {
            return $this->db->insertID();
        }
        return false;
    }

    /**
     * Update an existing branch
     */
    public function updateBranch(int $id, array $data): bool
    {
        $id = clrNum($id);
        $branch = $this->getBranch($id);
        if (empty($branch)) {
            return false;
        }

        if (!empty($data['is_headquarters'])) {
            $this->builder->where('company_id', $branch->company_id)->update(['is_headquarters' => 0]);
        }

        $fields = ['branch_name', 'country', 'city', 'address', 'phone', 'email', 'is_headquarters', 'metadata'];
        $updateData = [];

        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                if ($field === 'is_headquarters') {
                    $updateData[$field] = !empty($data[$field]) ? 1 : 0;
                } elseif ($field === 'metadata') {
                    $updateData[$field] = !empty($data[$field]) 
                        ? (is_array($data[$field]) ? json_encode($data[$field], JSON_UNESCAPED_UNICODE) : $data[$field]) 
                        : null;
                } else {
                    $updateData[$field] = ($data[$field] !== null && $data[$field] !== '') ? strTrim($data[$field]) : null;
                }
            }
        }

        if (empty($updateData)) {
            return false;
        }

        return $this->builder->where('id', $id)->update($updateData);
    }

    /**
     * Set a specific branch as headquarters for the company
     */
    public function setHeadquarters(int $companyId, int $branchId): bool
    {
        $companyId = clrNum($companyId);
        $branchId = clrNum($branchId);

        $this->builder->where('company_id', $companyId)->update(['is_headquarters' => 0]);
        return $this->builder
            ->where('id', $branchId)
            ->where('company_id', $companyId)
            ->update([
                'is_headquarters' => 1,
            ]);
    }

    /**
     * Delete branch by ID
     */
    public function deleteBranch(int $id): bool
    {
        return $this->builder->where('id', clrNum($id))->delete();
    }

    /**
     * Delete all branches for a company
     */
    public function deleteBranchesByCompanyId(int $companyId): bool
    {
        return $this->builder->where('company_id', clrNum($companyId))->delete();
    }

    /**
     * Sync/reconcile branches list from form submission
     */
    public function syncBranches(int $companyId, array $branchesList): bool
    {
        $companyId = clrNum($companyId);
        $existing = $this->getBranchesByCompanyId($companyId);
        $existingIds = array_column($existing, 'id');
        $keptIds = [];

        foreach ($branchesList as $item) {
            if (empty($item['branch_name'])) {
                continue;
            }
            $branchId = !empty($item['id']) ? clrNum($item['id']) : 0;
            if ($branchId > 0 && in_array($branchId, $existingIds)) {
                $this->updateBranch($branchId, $item);
                $keptIds[] = $branchId;
            } else {
                $newId = $this->addBranch($companyId, $item);
                if ($newId) {
                    $keptIds[] = $newId;
                }
            }
        }

        foreach ($existingIds as $oldId) {
            if (!in_array($oldId, $keptIds)) {
                $this->deleteBranch($oldId);
            }
        }
        return true;
    }
}
