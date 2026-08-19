<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Create industry_types table and seed initial industries
 */
class CreateIndustryTypes extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'name_slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'icon' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'sort_order' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('name_slug');
        $this->forge->addKey('sort_order');
        $this->forge->createTable('industry_types', true);

        // Seed 15 popular industries for logistics & commercial businesses in Vietnam
        $now = date('Y-m-d H:i:s');
        $industries = [
            [
                'name'        => 'Xuất Nhập Khẩu & Thương Mại Quốc Tế',
                'name_slug'   => 'xuat-nhap-khau-thuong-mai-quoc-te',
                'icon'        => 'fa fa-globe',
                'description' => 'Hoạt động xuất nhập khẩu hàng hóa, thương mại và mậu dịch quốc tế',
                'sort_order'  => 1,
                'created_at'  => $now,
            ],
            [
                'name'        => 'Vận Tải & Logistics',
                'name_slug'   => 'van-tai-logistics',
                'icon'        => 'fa fa-truck',
                'description' => 'Vận tải đường bộ, đường biển, hàng không, kho bãi và giao nhận',
                'sort_order'  => 2,
                'created_at'  => $now,
            ],
            [
                'name'        => 'Sản Xuất & Chế Biến',
                'name_slug'   => 'san-xuat-che-bien',
                'icon'        => 'fa fa-industry',
                'description' => 'Nhà máy, xưởng chế biến, gia công và sản xuất hàng công nghiệp',
                'sort_order'  => 3,
                'created_at'  => $now,
            ],
            [
                'name'        => 'Phân Phối & Bán Lẻ',
                'name_slug'   => 'phan-phoi-ban-le',
                'icon'        => 'fa fa-shopping-cart',
                'description' => 'Hệ thống đại lý phân phối, siêu thị, chuỗi cửa hàng bán lẻ',
                'sort_order'  => 4,
                'created_at'  => $now,
            ],
            [
                'name'        => 'Công Nghệ & Phần Mềm',
                'name_slug'   => 'cong-nghe-phan-mem',
                'icon'        => 'fa fa-laptop',
                'description' => 'Giải pháp chuyển đổi số, phần mềm quản trị, IT và viễn thông',
                'sort_order'  => 5,
                'created_at'  => $now,
            ],
            [
                'name'        => 'Tài Chính & Ngân Hàng',
                'name_slug'   => 'tai-chinh-ngan-hang',
                'icon'        => 'fa fa-university',
                'description' => 'Dịch vụ ngân hàng, bảo hiểm, tài chính và thanh toán quốc tế',
                'sort_order'  => 6,
                'created_at'  => $now,
            ],
            [
                'name'        => 'Bất Động Sản & Xây Dựng',
                'name_slug'   => 'bat-dong-san-xay-dung',
                'icon'        => 'fa fa-building',
                'description' => 'Bất động sản công nghiệp, kho bãi xây dựng, dự án hạ tầng',
                'sort_order'  => 7,
                'created_at'  => $now,
            ],
            [
                'name'        => 'Thực Phẩm & Đồ Uống',
                'name_slug'   => 'thuc-pham-do-uong',
                'icon'        => 'fa fa-cutlery',
                'description' => 'F&B, chế biến thực phẩm, nông sản đóng gói và đồ uống',
                'sort_order'  => 8,
                'created_at'  => $now,
            ],
            [
                'name'        => 'Y Tế & Dược Phẩm',
                'name_slug'   => 'y-te-duoc-pham',
                'icon'        => 'fa fa-medkit',
                'description' => 'Thiết bị y tế, dược phẩm, dịch vụ chăm sóc sức khỏe',
                'sort_order'  => 9,
                'created_at'  => $now,
            ],
            [
                'name'        => 'Giáo Dục & Đào Tạo',
                'name_slug'   => 'giao-duc-dao-tao',
                'icon'        => 'fa fa-graduation-cap',
                'description' => 'Trường học, trung tâm đào tạo nghiệp vụ logistics & quản lý',
                'sort_order'  => 10,
                'created_at'  => $now,
            ],
            [
                'name'        => 'Dịch Vụ Chuyên Nghiệp (Luật, Kế Toán, Tư Vấn)',
                'name_slug'   => 'dich-vu-chuyen-nghiep-luat-ke-toan-tu-van',
                'icon'        => 'fa fa-briefcase',
                'description' => 'Tư vấn pháp lý hải quan, kiểm toán thuế, tư vấn doanh nghiệp',
                'sort_order'  => 11,
                'created_at'  => $now,
            ],
            [
                'name'        => 'Nông Nghiệp & Thủy Sản',
                'name_slug'   => 'nong-nghiep-thuy-san',
                'icon'        => 'fa fa-leaf',
                'description' => 'Nuôi trồng, đánh bắt thủy hải sản, chế biến nông sản xuất khẩu',
                'sort_order'  => 12,
                'created_at'  => $now,
            ],
            [
                'name'        => 'Năng Lượng & Môi Trường',
                'name_slug'   => 'nang-luong-moi-truong',
                'icon'        => 'fa fa-bolt',
                'description' => 'Năng lượng tái tạo, xử lý chất thải, giải pháp xanh',
                'sort_order'  => 13,
                'created_at'  => $now,
            ],
            [
                'name'        => 'Du Lịch & Khách Sạn',
                'name_slug'   => 'du-lich-khach-san',
                'icon'        => 'fa fa-plane',
                'description' => 'Lữ hành quốc tế, chuỗi khách sạn, nghỉ dưỡng và sự kiện',
                'sort_order'  => 14,
                'created_at'  => $now,
            ],
            [
                'name'        => 'Khác',
                'name_slug'   => 'khac',
                'icon'        => 'fa fa-ellipsis-h',
                'description' => 'Các ngành nghề kinh doanh và dịch vụ thương mại khác',
                'sort_order'  => 15,
                'created_at'  => $now,
            ],
        ];

        $this->db->table('industry_types')->insertBatch($industries);
    }

    public function down()
    {
        $this->forge->dropTable('industry_types', true);
    }
}
