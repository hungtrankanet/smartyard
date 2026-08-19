<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberPostModel extends Model
{
    protected $table            = 'member_posts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $allowedFields    = ['member_id', 'user_id', 'title', 'title_slug', 'summary', 'content', 'image_default', 'status', 'reject_reason', 'views_count', 'approved_at', 'approved_by', 'created_at', 'updated_at'];
    protected $useTimestamps    = false;

    public function ensureTableExists()
    {
        $db = \Config\Database::connect();
        $forge = \Config\Database::forge();
        if (!$db->tableExists($this->table)) {
            $forge->addField([
                'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'member_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'user_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'title'         => ['type' => 'VARCHAR', 'constraint' => 255],
                'title_slug'    => ['type' => 'VARCHAR', 'constraint' => 255],
                'summary'       => ['type' => 'TEXT', 'null' => true],
                'content'       => ['type' => 'LONGTEXT', 'null' => true],
                'image_default' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
                'status'        => ['type' => 'ENUM', 'constraint' => ['pending', 'approved', 'rejected'], 'default' => 'pending'],
                'reject_reason' => ['type' => 'TEXT', 'null' => true],
                'views_count'   => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'approved_at'   => ['type' => 'DATETIME', 'null' => true],
                'approved_by'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'created_at'    => ['type' => 'DATETIME', 'null' => true],
                'updated_at'    => ['type' => 'DATETIME', 'null' => true],
            ]);
            $forge->addKey('id', true);
            $forge->addUniqueKey('member_id');
            $forge->addKey('user_id');
            $forge->addKey('status');
            $forge->addKey('title_slug');
            $forge->createTable($this->table, true);
        }
    }

    public function getPostByMemberId(int $memberId)
    {
        $this->ensureTableExists();
        return $this->where('member_id', $memberId)->first();
    }

    public function saveMemberPost(int $memberId, int $userId, array $data): int
    {
        $this->ensureTableExists();
        $existing = $this->getPostByMemberId($memberId);
        $slug = strSlug($data['title'] ?? 'gioi-thieu-doanh-nghiep') . '-' . $memberId;

        $saveData = [
            'member_id'     => $memberId,
            'user_id'       => $userId,
            'title'         => trim((string)($data['title'] ?? '')),
            'title_slug'    => $slug,
            'summary'       => trim((string)($data['summary'] ?? '')),
            'content'       => trim((string)($data['content'] ?? '')),
            'image_default' => !empty($data['image_default']) ? trim($data['image_default']) : ($existing->image_default ?? null),
            'status'        => 'pending', // Luôn chuyển về chờ duyệt khi tạo mới hoặc cập nhật
            'reject_reason' => null,
            'updated_at'    => date('Y-m-d H:i:s'),
        ];

        if ($existing) {
            $this->update($existing->id, $saveData);
            return (int)$existing->id;
        } else {
            $saveData['created_at'] = date('Y-m-d H:i:s');
            $saveData['views_count'] = 0;
            return (int)$this->insert($saveData, true);
        }
    }

    public function approvePost(int $id, int $adminUserId): bool
    {
        $this->ensureTableExists();
        return $this->update($id, [
            'status'        => 'approved',
            'approved_at'   => date('Y-m-d H:i:s'),
            'approved_by'   => $adminUserId,
            'reject_reason' => null,
        ]);
    }

    public function rejectPost(int $id, string $reason = ''): bool
    {
        $this->ensureTableExists();
        return $this->update($id, [
            'status'        => 'rejected',
            'reject_reason' => $reason,
        ]);
    }

    public function getPendingPostsPaginated(int $perPage, int $offset, array $filters = []): array
    {
        $this->ensureTableExists();
        $builder = $this->builder()->select('member_posts.*, members.company_name, members.email as company_email, members.phone as company_phone, members.city')
                                   ->join('members', 'members.id = member_posts.member_id', 'left');

        if (!empty($filters['status'])) $builder->where('member_posts.status', $filters['status']);
        if (!empty($filters['q'])) $builder->groupStart()->like('member_posts.title', $filters['q'])->orLike('members.company_name', $filters['q'])->groupEnd();

        return $builder->orderBy('member_posts.id', 'DESC')->limit($perPage, $offset)->get()->getResult();
    }

    public function getPostsCount(array $filters = []): int
    {
        $this->ensureTableExists();
        $builder = $this->builder()->join('members', 'members.id = member_posts.member_id', 'left');
        if (!empty($filters['status'])) $builder->where('member_posts.status', $filters['status']);
        if (!empty($filters['q'])) $builder->groupStart()->like('member_posts.title', $filters['q'])->orLike('members.company_name', $filters['q'])->groupEnd();
        return $builder->countAllResults();
    }
}
