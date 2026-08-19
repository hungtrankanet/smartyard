<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberInteractionModel extends Model
{
    protected $tableViews    = 'member_profile_views';
    protected $tableMessages = 'member_messages';

    public function ensureTablesExist()
    {
        $db = \Config\Database::connect();
        $forge = \Config\Database::forge();

        if (!$db->tableExists($this->tableViews)) {
            $forge->addField([
                'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'viewed_member_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'viewer_user_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'viewer_member_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'ip_address'       => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'user_agent'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'created_at'       => ['type' => 'DATETIME', 'null' => true],
            ]);
            $forge->addKey('id', true);
            $forge->addKey('viewed_member_id');
            $forge->addKey('viewer_member_id');
            $forge->addKey('created_at');
            $forge->createTable($this->tableViews, true);
        }

        if (!$db->tableExists($this->tableMessages)) {
            $forge->addField([
                'id'                 => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'sender_user_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'sender_member_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'receiver_member_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'sender_name'        => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
                'sender_company'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'sender_phone'       => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'sender_email'       => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
                'subject'            => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'message'            => ['type' => 'TEXT'],
                'is_read'            => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'created_at'         => ['type' => 'DATETIME', 'null' => true],
            ]);
            $forge->addKey('id', true);
            $forge->addKey('receiver_member_id');
            $forge->addKey('sender_member_id');
            $forge->addKey('is_read');
            $forge->createTable($this->tableMessages, true);
        }
    }

    public function recordProfileView(int $viewedMemberId, ?int $viewerUserId = null, ?int $viewerMemberId = null, ?string $ip = null, ?string $ua = null)
    {
        $this->ensureTablesExist();
        $db = \Config\Database::connect();
        // Không tính tự xem chính mình
        if ($viewerMemberId && $viewerMemberId === $viewedMemberId) return;

        // Tránh spam refresh: 1 viewer/IP chỉ tính 1 lần mỗi 1 giờ
        $oneHourAgo = date('Y-m-d H:i:s', strtotime('-1 hour'));
        $builder = $db->table($this->tableViews)->where('viewed_member_id', $viewedMemberId)->where('created_at >=', $oneHourAgo);
        if ($viewerMemberId) $builder->where('viewer_member_id', $viewerMemberId);
        elseif ($ip) $builder->where('ip_address', $ip);
        if ($builder->countAllResults() > 0) return;

        $db->table($this->tableViews)->insert([
            'viewed_member_id' => $viewedMemberId,
            'viewer_user_id'   => $viewerUserId,
            'viewer_member_id' => $viewerMemberId,
            'ip_address'       => $ip ?: (service('request')->getIPAddress() ?? null),
            'user_agent'       => substr((string)($ua ?: (service('request')->getUserAgent() ?? '')), 0, 250),
            'created_at'       => date('Y-m-d H:i:s'),
        ]);
    }

    public function getProfileVisitors(int $memberId, int $limit = 20): array
    {
        $this->ensureTablesExist();
        $db = \Config\Database::connect();
        return $db->table($this->tableViews . ' pv')
                  ->select('pv.*, m.company_name as viewer_company_name, m.city as viewer_city, m.phone as viewer_phone, m.email as viewer_email, it.name as industry_name')
                  ->join('members m', 'm.id = pv.viewer_member_id', 'left')
                  ->join('industry_types it', 'it.id = m.industry_type_id', 'left')
                  ->where('pv.viewed_member_id', $memberId)
                  ->orderBy('pv.id', 'DESC')
                  ->limit($limit)
                  ->get()->getResult();
    }

    public function getVisitorStats(int $memberId): array
    {
        $this->ensureTablesExist();
        $db = \Config\Database::connect();
        $totalViews = $db->table($this->tableViews)->where('viewed_member_id', $memberId)->countAllResults();
        $weekAgo = date('Y-m-d H:i:s', strtotime('-7 days'));
        $weekViews = $db->table($this->tableViews)->where('viewed_member_id', $memberId)->where('created_at >=', $weekAgo)->countAllResults();
        $partnerViews = $db->table($this->tableViews)->where('viewed_member_id', $memberId)->where('viewer_member_id IS NOT NULL')->countAllResults();
        return ['total_views' => $totalViews, 'week_views' => $weekViews, 'partner_views' => $partnerViews];
    }

    public function sendMessage(array $data): int
    {
        $this->ensureTablesExist();
        $db = \Config\Database::connect();
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['is_read'] = 0;
        $db->table($this->tableMessages)->insert($data);
        return (int)$db->insertID();
    }

    public function getInboxMessages(int $memberId, int $limit = 25): array
    {
        $this->ensureTablesExist();
        $db = \Config\Database::connect();
        return $db->table($this->tableMessages . ' msg')
                  ->select('msg.*, m.company_name as sender_company_name, m.phone as sender_member_phone, m.email as sender_member_email')
                  ->join('members m', 'm.id = msg.sender_member_id', 'left')
                  ->where('msg.receiver_member_id', $memberId)
                  ->orderBy('msg.id', 'DESC')
                  ->limit($limit)
                  ->get()->getResult();
    }

    public function getUnreadCount(int $memberId): int
    {
        $this->ensureTablesExist();
        $db = \Config\Database::connect();
        return $db->table($this->tableMessages)->where('receiver_member_id', $memberId)->where('is_read', 0)->countAllResults();
    }

    public function markMessageAsRead(int $messageId, int $memberId): bool
    {
        $this->ensureTablesExist();
        $db = \Config\Database::connect();
        return $db->table($this->tableMessages)->where('id', $messageId)->where('receiver_member_id', $memberId)->update(['is_read' => 1]);
    }
}
