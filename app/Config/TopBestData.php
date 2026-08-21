<?php

namespace Config;

/**
 * TOP BEST GLOBAL Master Data & Configuration
 * Official program under VietKings (Hội Kỷ lục Việt Nam, member of WORLDKINGS) & GAA entrusted to TOLUCK
 */
class TopBestData
{
    public static function getIndustries(): array
    {
        return [
            ['id' => 'am-thuc-nong-san', 'name' => 'Ẩm thực & Nông sản', 'name_en' => 'Food & Agriculture', 'icon' => 'fa-wheat-awn'],
            ['id' => 'thu-cong-my-nghe', 'name' => 'Thủ công mỹ nghệ & Làng nghề', 'name_en' => 'Handicrafts & Traditional Villages', 'icon' => 'fa-hands-holding'],
            ['id' => 'my-thuat-son-mai', 'name' => 'Mỹ thuật & Sơn mài', 'name_en' => 'Fine Arts & Lacquerware', 'icon' => 'fa-palette'],
            ['id' => 'thoi-trang-to-lua', 'name' => 'Thời trang & Tơ lụa', 'name_en' => 'Fashion & Silk', 'icon' => 'fa-shirt'],
            ['id' => 'du-lich-luu-tru', 'name' => 'Du lịch & Lưu trú', 'name_en' => 'Tourism & Hospitality', 'icon' => 'fa-hotel'],
            ['id' => 'ban-le-thuong-mai', 'name' => 'Bán lẻ & Thương mại', 'name_en' => 'Retail & Commerce', 'icon' => 'fa-shop'],
            ['id' => 'san-xuat-che-bien', 'name' => 'Sản xuất & Chế biến', 'name_en' => 'Manufacturing & Processing', 'icon' => 'fa-industry'],
            ['id' => 'dich-vu-giao-duc', 'name' => 'Dịch vụ & Giáo dục', 'name_en' => 'Services & Education', 'icon' => 'fa-graduation-cap']
        ];
    }

    public static function getProvinces(): array
    {
        return [
            'Hà Nội', 'TP. Hồ Chí Minh', 'Đà Nẵng', 'Hải Phòng', 'Cần Thơ',
            'Thừa Thiên Huế', 'Quảng Nam', 'Lâm Đồng', 'Khánh Hòa', 'An Giang',
            'Bà Rịa - Vũng Tàu', 'Bắc Ninh', 'Bình Dương', 'Đồng Nai', 'Bến Tre',
            'Bình Định', 'Bình Thuận', 'Cà Mau', 'Đắk Lắk', 'Đồng Tháp',
            'Gia Lai', 'Hà Giang', 'Hà Tĩnh', 'Hải Dương', 'Kiên Giang',
            'Lào Cai', 'Long An', 'Nam Định', 'Nghệ An', 'Ninh Bình',
            'Phú Thọ', 'Quảng Ninh', 'Tây Ninh', 'Thái Nguyên'
        ];
    }

    public static function getDirectoryProfiles(): array
    {
        return [
            [
                'code' => 'TBG-VN-2026-001',
                'name' => 'Trầm Hương Khánh Hòa',
                'title_formula' => 'TOP Trầm Hương Tự Nhiên trong Mỹ thuật & Thủ công tại Khánh Hòa',
                'rank_tier' => 'BEST',
                'rank_number' => 1,
                'badge_type' => 'Diamond',
                'category_id' => 'thu-cong-my-nghe',
                'category_name' => 'Thủ công mỹ nghệ & Làng nghề',
                'province' => 'Khánh Hòa',
                'logo' => 'assets/themes/suntransco/logo.png',
                'banner' => 'assets/themes/suntransco/hero_bg.jpg',
                'valid_until' => '2026-12-31',
                'cycle_months' => 6,
                'last_updated' => '2026-08-15',
                'followers' => '128,500',
                'reviews_count' => 1420,
                'rating' => 4.95,
                'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'ecommerce_url' => 'https://tramhuongkhanhhoa.com.vn',
                'address' => 'Số 15 đường Trần Phú, TP. Nha Trang, Tỉnh Khánh Hòa',
                'summary' => 'Doanh nghiệp tiêu biểu dẫn đầu ngành Trầm hương Việt Nam với các sản phẩm nghệ thuật điêu khắc trầm tự nhiên được xuất khẩu sang 20+ quốc gia.'
            ],
            [
                'code' => 'TBG-VN-2026-002',
                'name' => 'Gốm Sứ Bát Tràng Di Sản',
                'title_formula' => 'TOP Gốm Sứ Men Lam Thủ Công trong Mỹ thuật & Làng nghề tại Hà Nội',
                'rank_tier' => 'BEST',
                'rank_number' => 2,
                'badge_type' => 'Diamond',
                'category_id' => 'thu-cong-my-nghe',
                'category_name' => 'Thủ công mỹ nghệ & Làng nghề',
                'province' => 'Hà Nội',
                'logo' => 'assets/themes/suntransco/logo.png',
                'banner' => 'assets/themes/suntransco/hero_bg.jpg',
                'valid_until' => '2026-12-31',
                'cycle_months' => 6,
                'last_updated' => '2026-08-10',
                'followers' => '94,200',
                'reviews_count' => 890,
                'rating' => 4.92,
                'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'ecommerce_url' => 'https://gombattrang.vn',
                'address' => 'Làng cổ Bát Tràng, Huyện Gia Lâm, TP. Hà Nội',
                'summary' => 'Nghệ nhân ưu tú giữ gìn và phát huy dòng men lam cổ truyền Thăng Long, phục vụ nghi lễ quốc gia và xuất khẩu mỹ nghệ thế giới.'
            ],
            [
                'code' => 'TBG-VN-2026-003',
                'name' => 'Cà Phê Đặc Sản Robusta Honey Đắk Lắk',
                'title_formula' => 'TOP Cà Phê Chế Biến Honey trong Ẩm thực & Nông sản tại Đắk Lắk',
                'rank_tier' => 'BEST',
                'rank_number' => 3,
                'badge_type' => 'Ruby',
                'category_id' => 'am-thuc-nong-san',
                'category_name' => 'Ẩm thực & Nông sản',
                'province' => 'Đắk Lắk',
                'logo' => 'assets/themes/suntransco/logo.png',
                'banner' => 'assets/themes/suntransco/hero_bg.jpg',
                'valid_until' => '2026-12-31',
                'cycle_months' => 6,
                'last_updated' => '2026-08-12',
                'followers' => '67,800',
                'reviews_count' => 610,
                'rating' => 4.88,
                'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'ecommerce_url' => 'https://daklakcoffee.vn',
                'address' => 'Xã Ea Kao, TP. Buôn Ma Thuột, Tỉnh Đắk Lắk',
                'summary' => 'Dòng sản phẩm nông sản hữu cơ đạt chuẩn Specialty Coffee quốc tế, nâng tầm giá trị hạt cà phê Robusta Việt Nam.'
            ],
            [
                'code' => 'TBG-VN-2026-011',
                'name' => 'Tơ Lụa Bảo Lộc Heritage',
                'title_formula' => 'TOP Tơ Lụa Tự Nhiên Dệt Tay trong Thời trang & Tơ lụa tại Lâm Đồng',
                'rank_tier' => 'TOP',
                'rank_number' => 11,
                'badge_type' => 'Ruby',
                'category_id' => 'thoi-trang-to-lua',
                'category_name' => 'Thời trang & Tơ lụa',
                'province' => 'Lâm Đồng',
                'logo' => 'assets/themes/suntransco/logo.png',
                'banner' => 'assets/themes/suntransco/hero_bg.jpg',
                'valid_until' => '2026-12-31',
                'cycle_months' => 6,
                'last_updated' => '2026-08-14',
                'followers' => '45,300',
                'reviews_count' => 380,
                'rating' => 4.85,
                'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'ecommerce_url' => 'https://luabaoloc.vn',
                'address' => 'Phường B’Lao, TP. Bảo Lộc, Tỉnh Lâm Đồng',
                'summary' => 'Thương hiệu lụa tơ tằm 100% tự nhiên dệt theo phương pháp truyền thống kết hợp công nghệ hiện đại.'
            ],
            [
                'code' => 'TBG-VN-2026-012',
                'name' => 'Sơn Mài Tương Bình Hiệp',
                'title_formula' => 'TOP Sơn Mài Cẩn Trứng Mỹ Nghệ trong Mỹ thuật & Sơn mài tại Bình Dương',
                'rank_tier' => 'TOP',
                'rank_number' => 12,
                'badge_type' => 'Ruby',
                'category_id' => 'my-thuat-son-mai',
                'category_name' => 'Mỹ thuật & Sơn mài',
                'province' => 'Bình Dương',
                'logo' => 'assets/themes/suntransco/logo.png',
                'banner' => 'assets/themes/suntransco/hero_bg.jpg',
                'valid_until' => '2026-12-31',
                'cycle_months' => 6,
                'last_updated' => '2026-08-08',
                'followers' => '38,900',
                'reviews_count' => 290,
                'rating' => 4.82,
                'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'ecommerce_url' => 'https://sonmaituongbinhhiep.com',
                'address' => 'Làng nghề Tương Bình Hiệp, TP. Thủ Dầu Một, Tỉnh Bình Dương',
                'summary' => 'Di sản sơn mài truyền thống nổi tiếng Đông Nam Bộ, kết hợp chất liệu vỏ trứng, dát vàng và sơn ta bản địa.'
            ],
            [
                'code' => 'TBG-VN-2026-015',
                'name' => 'Khu Nghỉ Dưỡng Sinh Thái Cồn Sơn',
                'title_formula' => 'TOP Nghỉ Dưỡng Sinh Thái Miệt Vườn trong Du lịch & Lưu trú tại Cần Thơ',
                'rank_tier' => 'TOP',
                'rank_number' => 15,
                'badge_type' => 'Ruby',
                'category_id' => 'du-lich-luu-tru',
                'category_name' => 'Du lịch & Lưu trú',
                'province' => 'Cần Thơ',
                'logo' => 'assets/themes/suntransco/logo.png',
                'banner' => 'assets/themes/suntransco/hero_bg.jpg',
                'valid_until' => '2026-12-31',
                'cycle_months' => 6,
                'last_updated' => '2026-08-05',
                'followers' => '52,100',
                'reviews_count' => 410,
                'rating' => 4.86,
                'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'ecommerce_url' => 'https://consonresort.vn',
                'address' => 'Cồn Sơn, Phường Bùi Hữu Nghĩa, Quận Bình Thủy, TP. Cần Thơ',
                'summary' => 'Điểm đến du lịch cộng đồng kiểu mẫu sông nước miền Tây, mang lại trải nghiệm bản địa độc đáo cho du khách quốc tế.'
            ]
        ];
    }

    public static function getNewsArticles(): array
    {
        return [
            [
                'id' => 1,
                'title' => 'Trầm Hương Khánh Hòa Được Vinh Danh Hạng BEST Mùa Giải 2026',
                'slug' => 'tram-huong-khanh-hoa-dat-hang-best-2026',
                'category' => 'Doanh nghiệp vinh danh',
                'category_slug' => 'doanh-nghiep-vinh-danh',
                'created_at' => '2026-08-20',
                'image' => 'assets/themes/suntransco/hero_bg.jpg',
                'summary' => 'Hội đồng Giám khảo VietKings và GAA chính thức thẩm định và trao chứng nhận BEST cho Trầm Hương Khánh Hòa với chuỗi giá trị xuất khẩu toàn cầu.',
                'is_featured' => 1
            ],
            [
                'id' => 2,
                'title' => 'TOLUCK Ký Kết Hợp Tác Chiến Lược Cùng Hội Kỷ Lục Việt Nam (VietKings)',
                'slug' => 'toluck-ky-ket-hop-tac-chien-luoc-cung-vietkings',
                'category' => 'Tin chương trình',
                'category_slug' => 'tin-chuong-trinh',
                'created_at' => '2026-08-18',
                'image' => 'assets/themes/suntransco/hero_bg.jpg',
                'summary' => 'VietKings và GAA chính thức uỷ thác toàn diện cho TOLUCK triển khai cổng số hoá và chuỗi vinh danh TOP BEST GLOBAL trên 34 tỉnh thành.',
                'is_featured' => 0
            ],
            [
                'id' => 3,
                'title' => 'Lộ Trình Đưa Nông Sản & Thủ Công Mỹ Nghệ Việt Ra Thế Giới',
                'slug' => 'lo-trinh-dua-nong-san-thu-cong-viet-ra-the-gioi',
                'category' => 'Xu hướng ngành',
                'category_slug' => 'xu-huong-nganh',
                'created_at' => '2026-08-15',
                'image' => 'assets/themes/suntransco/hero_bg.jpg',
                'summary' => 'Cách các doanh nghiệp đạt TOP BEST tận dụng mạng lưới truyền thông WorldKings để chinh phục khách hàng quốc tế và đối tác xuất khẩu.',
                'is_featured' => 0
            ],
            [
                'id' => 4,
                'title' => 'Công Bố Quy Chế Thẩm Định 4 Vòng & Cơ Chế Điểm 70/30 Minh Bạch',
                'slug' => 'cong-bo-quy-che-tham-dinh-4-vong-70-30',
                'category' => 'Tin chương trình',
                'category_slug' => 'tin-chuong-trinh',
                'created_at' => '2026-08-12',
                'image' => 'assets/themes/suntransco/hero_bg.jpg',
                'summary' => 'Đảm bảo sự kết hợp giữa 70% Điểm Hội đồng Chuyên môn và 30% Điểm bình chọn xác thực độc giả qua OTP chống gian lận.',
                'is_featured' => 0
            ]
        ];
    }

    public static function getFaqs(): array
    {
        return [
            [
                'q' => 'TOP BEST mới lập ra có đáng tin cậy không?',
                'a' => 'TOP BEST là hạng mục giải thưởng chính thức trực thuộc Hội Kỷ lục Việt Nam (VietKings) — tổ chức được Bộ Nội vụ cấp phép theo Quyết định 959/QĐ-BNV từ năm 2013 và là thành viên chính thức của Liên minh Kỷ lục Thế giới (WORLDKINGS). Mọi quy trình xét duyệt đều qua Hội đồng Giám khảo chuyên gia và Viện Kỷ lục Việt Nam thẩm định độc lập.'
            ],
            [
                'q' => 'Ai là người quyết định danh hiệu TOP hay BEST?',
                'a' => 'Danh hiệu BEST (Hạng 1–10) do Viện Kỷ lục Việt Nam và Hội đồng Thẩm định trực tiếp xét duyệt dựa trên hồ sơ chứng minh di sản, năng lực xuất khẩu và uy tín thương hiệu. Danh hiệu TOP (Hạng 11–100) được xét duyệt kết hợp giữa thẩm định hồ sơ kỹ thuật và điểm bình chọn minh bạch từ cộng đồng.'
            ],
            [
                'q' => 'Nếu hồ sơ không đạt chuẩn thẩm định thì sao?',
                'a' => 'TOLUCK và Ban tổ chức cam kết chính sách hoàn phí 100% nếu hồ sơ của doanh nghiệp không vượt qua được vòng thẩm định kỹ thuật của Hội đồng Giám khảo. Doanh nghiệp cũng nhận được biên bản đánh giá chi tiết để hoàn thiện năng lực.'
            ],
            [
                'q' => 'Đơn vị có cả sản phẩm và thương hiệu thì đăng ký như thế nào?',
                'a' => 'Doanh nghiệp có thể đăng ký song song ở 3 cấp độ: Cấp độ Thương hiệu (Brand), Cấp độ Dịch vụ (Service) và Cấp độ Sản phẩm (Product). Mỗi cấp độ sẽ có tiêu chí đánh giá và bảng vàng vinh danh riêng biệt.'
            ],
            [
                'q' => 'TOP BEST có được quốc tế công nhận trực tiếp không?',
                'a' => 'TOP BEST là chương trình quốc gia uy tín thuộc mạng lưới Liên minh Kỷ lục Thế giới (WORLDKINGS). Danh hiệu TOP BEST là bảo chứng tin cậy và bước đệm truyền thông vững chắc trên toàn cầu, giúp đối tác quốc tế dễ dàng xác minh nguồn gốc xuất xứ và uy tín của đơn vị.'
            ]
        ];
    }
}
