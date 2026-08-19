<?php namespace App\Models;

use CodeIgniter\Model;

class EventModel extends BaseModel
{
    protected $table = 'events';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'title', 'slug', 'summary', 'content', 'event_date', 'event_time',
        'location', 'location_type', 'organizer', 'fee', 'max_seats',
        'registered_count', 'speakers_json', 'agenda_json', 'image', 'status', 'created_at'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->ensureEventsTable();
    }

    private function ensureEventsTable()
    {
        if (!$this->db->tableExists('events')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `events` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `title` VARCHAR(255) NOT NULL,
                `slug` VARCHAR(255) NOT NULL UNIQUE,
                `summary` TEXT NULL,
                `content` LONGTEXT NULL,
                `event_date` VARCHAR(50) NOT NULL,
                `event_time` VARCHAR(50) NOT NULL,
                `location` VARCHAR(255) NOT NULL,
                `location_type` VARCHAR(50) DEFAULT 'offline',
                `organizer` VARCHAR(255) DEFAULT 'TOP BEST GLOBAL Business Alliance',
                `fee` VARCHAR(100) DEFAULT 'Miễn phí cho Hội viên TOP BEST GLOBAL',
                `max_seats` INT DEFAULT 200,
                `registered_count` INT DEFAULT 0,
                `speakers_json` TEXT NULL,
                `agenda_json` TEXT NULL,
                `image` VARCHAR(255) NULL,
                `status` VARCHAR(50) DEFAULT 'upcoming',
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

            $this->seedInitialEvents();
        }

        if (!$this->db->tableExists('event_registrations')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `event_registrations` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `event_id` INT NOT NULL,
                `full_name` VARCHAR(255) NOT NULL,
                `email` VARCHAR(255) NOT NULL,
                `phone` VARCHAR(50) NOT NULL,
                `company_name` VARCHAR(255) NULL,
                `position` VARCHAR(100) NULL,
                `note` TEXT NULL,
                `status` VARCHAR(50) DEFAULT 'confirmed',
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX `idx_event` (`event_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
        }
    }

    public function seedInitialEvents()
    {
        $events = [
            [
                'title' => 'Hội Thảo B2B: Tự Động Hóa Tờ Khai Hải Quan Bằng AI & Tối Ưu Chi Phí Logistics 2026',
                'slug' => 'hoi-thao-ai-logistics-2026',
                'summary' => 'Chia sẻ giải pháp áp dụng trí tuệ nhân tạo (AI) trong phân loại mã HS Code, tự động lập tờ khai xuất nhập khẩu và giảm 30% chi phí vận hành chuỗi cung ứng.',
                'content' => '<p>Trong bối cảnh chuyển đổi số mạnh mẽ của ngành Logistics toàn cầu, việc áp dụng công nghệ AI vào quy trình thông quan và quản lý chuỗi cung ứng đang trở thành lợi thế cạnh tranh sống còn của doanh nghiệp xuất nhập khẩu.</p><h3>Nội dung chính hội thảo:</h3><ul><li>Tổng quan các quy định mới nhất về thủ tục hải quan điện tử và kiểm tra chuyên ngành năm 2026.</li><li>Ứng dụng mô hình AI tự động quét chứng từ, tra cứu biểu thuế và đề xuất mã HS Code với độ chính xác trên 98%.</li><li>Kinh nghiệm thực chiến từ các doanh nghiệp đầu ngành trong việc tối ưu hóa chi phí lưu kho bãi (Demurrage/Detention).</li><li>Giao lưu và kết nối giao thương B2B trực tiếp giữa hơn 200+ chủ hàng và đơn vị giao nhận forwarder.</li></ul>',
                'event_date' => '2026-08-28',
                'event_time' => '08:30 - 12:00',
                'location' => 'GEM Center, 08 Nguyễn Bỉnh Khiêm, Phường Đa Kao, Quận 1, TP. Hồ Chí Minh',
                'location_type' => 'offline',
                'organizer' => 'Liên Minh Doanh Nghiệp TOP BEST GLOBAL',
                'fee' => 'Miễn phí cho Hội viên (Khách vãng lai: 500.000 VNĐ)',
                'max_seats' => 250,
                'registered_count' => 184,
                'speakers_json' => json_encode([
                    ['name' => 'Ông Trần Quốc Hùng', 'title' => 'Chuyên Gia Cố Vấn Hải Quan', 'company' => 'TOP BEST GLOBAL Advisory Board'],
                    ['name' => 'TS. Nguyễn Minh Tuấn', 'title' => 'Giám Đốc Giải Pháp AI', 'company' => 'Logistics Tech Lab'],
                    ['name' => 'Bà Lê Thu Hương', 'title' => 'Trưởng Phòng XNK', 'company' => 'Tập Đoàn Thủy Sản Miền Nam']
                ]),
                'agenda_json' => json_encode([
                    ['time' => '08:00 - 08:30', 'title' => 'Đón tiếp đại biểu & Check-in B2B Networking', 'speaker' => 'Ban Tổ Chức'],
                    ['time' => '08:30 - 09:30', 'title' => 'Cập nhật chính sách & Biểu thuế Hải Quan 2026', 'speaker' => 'Ông Trần Quốc Hùng'],
                    ['time' => '09:30 - 10:30', 'title' => 'Trình diễn giải pháp AI tự động hóa tờ khai hải quan', 'speaker' => 'TS. Nguyễn Minh Tuấn'],
                    ['time' => '10:30 - 10:45', 'title' => 'Teabreak & Giao lưu kết nối doanh nghiệp', 'speaker' => 'Toàn thể đại biểu'],
                    ['time' => '10:45 - 11:45', 'title' => 'Tọa đàm: Tối ưu chi phí logistics đa phương thức', 'speaker' => 'Khách mời & Chuyên gia'],
                    ['time' => '11:45 - 12:00', 'title' => 'Tổng kết & Trao quà lưu niệm hội viên', 'speaker' => 'Ban Tổ Chức']
                ]),
                'image' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=1200&auto=format&fit=crop&q=80',
                'status' => 'upcoming'
            ]
        ];

        foreach ($events as $ev) {
            $this->db->table('events')->insert($ev);
        }
    }

    public function getEvents($status = 'all', $limit = 50, $offset = 0)
    {
        $builder = $this->db->table('events');
        if ($status !== 'all') {
            $builder->where('status', $status);
        }
        return $builder->orderBy('event_date', 'DESC')->limit($limit, $offset)->get()->getResult();
    }

    public function getEventsCount($status = 'all')
    {
        $builder = $this->db->table('events');
        if ($status !== 'all') {
            $builder->where('status', $status);
        }
        return $builder->countAllResults();
    }

    public function getFeaturedUpcomingEvents($limit = 3)
    {
        return $this->db->table('events')
            ->where('status', 'upcoming')
            ->orderBy('event_date', 'ASC')
            ->limit($limit)
            ->get()->getResult();
    }

    public function getEventById($id)
    {
        return $this->db->table('events')->where('id', (int)$id)->get()->getRow();
    }

    public function getEventBySlug($slug)
    {
        return $this->db->table('events')->where('slug', cleanSlug($slug))->get()->getRow();
    }

    public function addEvent($data)
    {
        $data['slug'] = !empty($data['slug']) ? cleanSlug($data['slug']) : strSlug($data['title']);
        $this->db->table('events')->insert($data);
        return $this->db->insertID();
    }

    public function editEvent($id, $data)
    {
        if (!empty($data['slug'])) {
            $data['slug'] = cleanSlug($data['slug']);
        }
        return $this->db->table('events')->where('id', (int)$id)->update($data);
    }

    public function deleteEvent($id)
    {
        $this->db->table('event_registrations')->where('event_id', (int)$id)->delete();
        return $this->db->table('events')->where('id', (int)$id)->delete();
    }

    public function incrementRegisteredCount($eventId)
    {
        $this->db->query("UPDATE `events` SET `registered_count` = `registered_count` + 1 WHERE `id` = ?", [clrNum($eventId)]);
    }

    public function getRegistrations($eventId = null, $limit = 100)
    {
        $builder = $this->db->table('event_registrations')
            ->select('event_registrations.*, events.title AS event_title, events.event_date')
            ->join('events', 'events.id = event_registrations.event_id', 'left');
        
        if ($eventId) {
            $builder->where('event_registrations.event_id', (int)$eventId);
        }
        return $builder->orderBy('event_registrations.id', 'DESC')->limit($limit)->get()->getResult();
    }

    public function deleteRegistration($id)
    {
        $reg = $this->db->table('event_registrations')->where('id', (int)$id)->get()->getRow();
        if ($reg) {
            $this->db->table('event_registrations')->where('id', (int)$id)->delete();
            $this->db->query("UPDATE `events` SET `registered_count` = GREATEST(0, `registered_count` - 1) WHERE `id` = ?", [(int)$reg->event_id]);
            return true;
        }
        return false;
    }
}
