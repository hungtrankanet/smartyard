<?php

namespace App\Models;

/**
 * MemberContactModel: Manages individual contacts attached to enterprises
 */
class MemberContactModel extends BaseModel
{
    protected $table = 'member_contacts';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'company_id',
        'full_name',
        'full_name_en',
        'full_name_local',
        'position',
        'position_en',
        'department',
        'phone',
        'phone_2',
        'email',
        'email_2',
        'is_primary',
        'metadata',
        'created_at',
        'updated_at',
    ];
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('member_contacts');
    }

    /**
     * Get all contacts for a company ordered by primary first, then id
     */
    public function getContactsByCompanyId(int $companyId): array
    {
        return $this->builder
            ->where('company_id', clrNum($companyId))
            ->orderBy('is_primary', 'DESC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResult();
    }

    /**
     * Get single contact by ID
     */
    public function getContact(int $id)
    {
        return $this->builder->where('id', clrNum($id))->get()->getRow();
    }

    /**
     * Get primary contact for a company
     */
    public function getPrimaryContact(int $companyId)
    {
        return $this->builder
            ->where('company_id', clrNum($companyId))
            ->where('is_primary', 1)
            ->get()
            ->getRow();
    }

    /**
     * Add a new contact for a company
     */
    public function addContact(int $companyId, array $data)
    {
        $fullName = strTrim($data['full_name'] ?? '');
        if (empty($fullName)) {
            return false;
        }

        $companyId = clrNum($companyId);
        $now = date('Y-m-d H:i:s');
        $isPrimary = !empty($data['is_primary']) ? 1 : 0;

        if ($isPrimary) {
            $this->db->table($this->table)->where('company_id', $companyId)->update(['is_primary' => 0]);
        }

        $metadata = null;
        if (!empty($data['metadata'])) {
            $metadata = is_array($data['metadata']) ? json_encode($data['metadata'], JSON_UNESCAPED_UNICODE) : $data['metadata'];
        }

        $insertData = [
            'company_id'      => $companyId,
            'full_name'       => $fullName,
            'full_name_en'    => !empty($data['full_name_en']) ? strTrim($data['full_name_en']) : null,
            'full_name_local' => !empty($data['full_name_local']) ? strTrim($data['full_name_local']) : null,
            'position'        => !empty($data['position']) ? strTrim($data['position']) : null,
            'position_en'     => !empty($data['position_en']) ? strTrim($data['position_en']) : null,
            'department'      => !empty($data['department']) ? strTrim($data['department']) : null,
            'phone'           => !empty($data['phone']) ? strTrim($data['phone']) : null,
            'phone_2'         => !empty($data['phone_2']) ? strTrim($data['phone_2']) : null,
            'email'           => !empty($data['email']) ? strTrim($data['email']) : null,
            'email_2'         => !empty($data['email_2']) ? strTrim($data['email_2']) : null,
            'is_primary'      => $isPrimary,
            'metadata'        => $metadata,
            'created_at'      => $now,
            'updated_at'      => $now,
        ];

        if ($this->db->table($this->table)->insert($insertData)) {
            return $this->db->insertID();
        }
        return false;
    }

    /**
     * Update an existing contact
     */
    public function updateContact(int $id, array $data): bool
    {
        $id = clrNum($id);
        $contact = $this->getContact($id);
        if (empty($contact)) {
            return false;
        }

        $fields = [
            'full_name', 'full_name_en', 'full_name_local', 'position', 'position_en',
            'department', 'phone', 'phone_2', 'email', 'email_2', 'is_primary', 'metadata'
        ];

        $updateData = ['updated_at' => date('Y-m-d H:i:s')];
        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                if ($field === 'is_primary') {
                    $updateData[$field] = !empty($data[$field]) ? 1 : 0;
                    if ($updateData[$field] === 1) {
                        $this->db->table($this->table)->where('company_id', $contact->company_id)->where('id !=', $id)->update(['is_primary' => 0]);
                    }
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
     * Set a specific contact as primary for the company
     */
    public function setPrimaryContact(int $companyId, int $contactId): bool
    {
        $companyId = clrNum($companyId);
        $contactId = clrNum($contactId);

        $this->builder->where('company_id', $companyId)->update(['is_primary' => 0]);
        return $this->builder
            ->where('id', $contactId)
            ->where('company_id', $companyId)
            ->update([
                'is_primary' => 1,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
    }

    /**
     * Delete contact by ID
     */
    public function deleteContact(int $id): bool
    {
        return $this->builder->where('id', clrNum($id))->delete();
    }

    /**
     * Delete all contacts for a company
     */
    public function deleteContactsByCompanyId(int $companyId): bool
    {
        return $this->builder->where('company_id', clrNum($companyId))->delete();
    }

    /**
     * Sync/reconcile contacts list from form submission
     */
    public function syncContacts(int $companyId, array $contactsList): bool
    {
        $companyId = clrNum($companyId);
        $existing = $this->getContactsByCompanyId($companyId);
        $existingIds = array_column($existing, 'id');
        $keptIds = [];

        foreach ($contactsList as $item) {
            if (empty($item['full_name'])) {
                continue;
            }
            $contactId = !empty($item['id']) ? clrNum($item['id']) : 0;
            if ($contactId > 0 && in_array($contactId, $existingIds)) {
                $this->updateContact($contactId, $item);
                $keptIds[] = $contactId;
            } else {
                $newId = $this->addContact($companyId, $item);
                if ($newId) {
                    $keptIds[] = $newId;
                }
            }
        }

        foreach ($existingIds as $oldId) {
            if (!in_array($oldId, $keptIds)) {
                $this->deleteContact($oldId);
            }
        }
        return true;
    }
}
