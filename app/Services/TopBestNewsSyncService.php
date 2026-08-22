<?php

namespace App\Services;

use Config\Database;
use Config\TopBestData;

/**
 * TopBestNewsSyncService: Synchronizes official TOP BEST GLOBAL categories and articles
 * between Backend database (Admin CMS) and Frontend portal.
 */
class TopBestNewsSyncService
{
    /**
     * Synchronize all official categories and articles into the DB if not yet present.
     */
    public static function syncDefaultContent(): bool
    {
        try {
            $db = Database::connect();
            if (!$db->tableExists('categories') || !$db->tableExists('posts')) {
                return false;
            }

            // 1. Sync Categories
            $categories = [
                [
                    'id'               => 1,
                    'lang_id'          => 1,
                    'name'             => 'Doanh Nghiệp & Thương Hiệu Vinh Danh',
                    'slug'             => 'doanh-nghiep-vinh-danh',
                    'parent_id'        => 0,
                    'description'      => 'Tôn vinh các doanh nghiệp, thương hiệu và sản phẩm đạt chuẩn kỷ lục TOP / BEST trên 34 tỉnh thành.',
                    'keywords'         => 'doanh nghiep vinh danh, thuong hieu ky luc, top best',
                    'color'            => '#D9A441',
                    'block_type'       => 'fa-award',
                    'category_order'   => 1,
                    'show_on_homepage' => 1,
                    'show_on_menu'     => 1,
                    'category_status'  => 1
                ],
                [
                    'id'               => 2,
                    'lang_id'          => 1,
                    'name'             => 'Tin Tức Hoạt Động & Sự Kiện',
                    'slug'             => 'tin-chuong-trinh',
                    'parent_id'        => 0,
                    'description'      => 'Thông báo chính thức từ Hội Kỷ lục Việt Nam (VietKings), GAA và ban điều hành TOLUCK.',
                    'keywords'         => 'tin chuong trinh, su kien vietkings, gaa, toluck',
                    'color'            => '#1A4C96',
                    'block_type'       => 'fa-bullhorn',
                    'category_order'   => 2,
                    'show_on_homepage' => 1,
                    'show_on_menu'     => 1,
                    'category_status'  => 1
                ],
                [
                    'id'               => 3,
                    'lang_id'          => 1,
                    'name'             => 'Xu Hướng Ngành & Hội Nhập Xuất Khẩu',
                    'slug'             => 'xu-huong-nganh',
                    'parent_id'        => 0,
                    'description'      => 'Định hướng xuất khẩu, tiêu chuẩn quốc tế và câu chuyện di sản bản địa vươn tầm thế giới.',
                    'keywords'         => 'xu huong nganh, hoi nhap quoc te, xuat khau nong san',
                    'color'            => '#16A34A',
                    'block_type'       => 'fa-arrow-trend-up',
                    'category_order'   => 3,
                    'show_on_homepage' => 1,
                    'show_on_menu'     => 1,
                    'category_status'  => 1
                ],
                [
                    'id'               => 4,
                    'lang_id'          => 1,
                    'name'             => 'Câu Chuyện Nghệ Nhân & Di Sản',
                    'slug'             => 'cau-chuyen-nghe-nhan',
                    'parent_id'        => 0,
                    'description'      => 'Hành trình gìn giữ tinh hoa làng nghề và bí quyết di sản truyền thống Việt Nam.',
                    'keywords'         => 'nghe nhan, lang nghe truyen thong, di san',
                    'color'            => '#9B111E',
                    'block_type'       => 'fa-hands-holding',
                    'category_order'   => 4,
                    'show_on_homepage' => 1,
                    'show_on_menu'     => 1,
                    'category_status'  => 1
                ]
            ];

            foreach ($categories as $cat) {
                $exists = $db->table('categories')->where('slug', $cat['slug'])->countAllResults();
                if ($exists == 0) {
                    $db->table('categories')->insert($cat);
                }
            }

            // 2. Sync Official Articles
            $articles = self::getOfficialSeedArticles();
            foreach ($articles as $art) {
                $exists = $db->table('posts')->where('slug', $art['slug'])->countAllResults();
                if ($exists == 0) {
                    $db->table('posts')->insert($art);
                }
            }

            return true;
        } catch (\Throwable $e) {
            log_message('error', 'TopBestNewsSyncService::syncDefaultContent error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get rich official seed articles list.
     */
    public static function getOfficialSeedArticles(): array
    {
        return [
            [
                'id'             => 1,
                'lang_id'        => 1,
                'title'          => 'Trầm Hương Khánh Hòa Đạt Danh Hiệu BEST Mùa Giải 2026: Hành Trình Chinh Phục 20+ Thị Trường Quốc Tế',
                'slug'           => 'tram-huong-khanh-hoa-dat-hang-best-2026',
                'summary'        => 'Hội đồng Giám khảo VietKings và GAA chính thức thẩm định và trao chứng nhận BEST cho Trầm Hương Khánh Hòa với chuỗi giá trị xuất khẩu toàn cầu sang 20+ quốc gia.',
                'content'        => '<p>Chương trình <strong>TOP BEST GLOBAL</strong> — được Hội Kỷ lục Việt Nam (VietKings) và Global American Academy (GAA) uỷ thác cho <strong>TOLUCK</strong> triển khai — vừa chính thức công bố kết quả thẩm định hồ sơ của <em>Trầm Hương Khánh Hòa</em>, đơn vị xuất sắc được vinh danh ở phân hạng <strong>BEST (Hạng 1)</strong>.</p>
<h3>1. Bảo Chứng Di Sản Và Uy Tín Kỷ Lục</h3>
<p>Trầm Hương Khánh Hòa từ lâu đã khẳng định vị thế dẫn đầu trong ngành trầm hương tự nhiên và điêu khắc nghệ thuật. Trải qua quy trình thẩm định 4 vòng khắt khe của Hội đồng Giám khảo chuyên gia Viện Kỷ lục Việt Nam, đơn vị đã chứng minh đầy đủ nguồn gốc trầm tự nhiên bền vững, quy trình tinh chế đạt chuẩn quốc tế và giá trị di sản văn hóa sâu sắc.</p>
<blockquote>"Danh hiệu BEST từ Hội Kỷ lục Việt Nam và WORLDKINGS là bảo chứng pháp lý và uy tín quan trọng, giúp thương hiệu tự tin đàm phán các hợp đồng xuất khẩu sang các thị trường khó tính như Nhật Bản, Đài Loan, Trung Đông và Châu Âu."</blockquote>
<h3>2. Chu Kỳ Thẩm Định 6 Tháng & Mã QR Chống Làm Giả</h3>
<p>Mỗi chứng nhận và huy hiệu số của Trầm Hương Khánh Hòa được tích hợp mã QR xác thực riêng biệt với chu kỳ thẩm định định kỳ 6 tháng/lần, đảm bảo tính trung thực tuyệt đối và phòng chống triệt để mọi hành vi giả mạo thương hiệu trên thị trường quốc tế.</p>',
                'category_id'    => 1,
                'image_url'      => 'assets/themes/suntransco/hero_bg.jpg',
                'user_id'        => 1,
                'status'         => 1,
                'visibility'     => 1,
                'slider_order'   => 1,
                'featured_order' => 1,
                'pageviews'      => 3420,
                'post_type'      => 'post',
                'created_at'     => '2026-08-20 09:00:00'
            ],
            [
                'id'             => 2,
                'lang_id'        => 1,
                'title'          => 'TOLUCK Ký Kết Hợp Tác Chiến Lược Cùng Hội Kỷ Lục Việt Nam (VietKings) Và GAA',
                'slug'           => 'toluck-ky-ket-hop-tac-chien-luoc-cung-vietkings',
                'summary'        => 'VietKings và GAA chính thức uỷ thác toàn diện cho TOLUCK triển khai cổng số hoá và mạng lưới Bảng vàng TOP BEST GLOBAL trên 34 tỉnh thành toàn quốc.',
                'content'        => '<p>Tại TP. Hồ Chí Minh, lễ ký kết hợp tác chiến lược giữa <strong>TOLUCK</strong>, <strong>Hội Kỷ lục Việt Nam (VietKings)</strong> và <strong>Global American Academy (GAA)</strong> đã diễn ra thành công tốt đẹp.</p>
<h3>Nội Dung Trọng Tâm Hợp Tác</h3>
<ul>
<li><strong>Số hóa toàn diện:</strong> Xây dựng Cổng thông tin và xác minh TOP BEST GLOBAL với khả năng phục vụ hơn 10.000 truy cập đồng thời.</li>
<li><strong>Quy chế thẩm định minh bạch:</strong> Áp dụng cơ chế chấm điểm kết hợp 70% Điểm Hội đồng Chuyên môn và 30% Điểm bình chọn xác thực độc giả qua OTP chống gian lận.</li>
<li><strong>Hỗ trợ xuất khẩu:</strong> Đưa các thương hiệu đạt giải tham gia mạng lưới truyền thông của Liên minh Kỷ lục Thế giới (WORLDKINGS).</li>
</ul>',
                'category_id'    => 2,
                'image_url'      => 'assets/themes/suntransco/hero_bg.jpg',
                'user_id'        => 1,
                'status'         => 1,
                'visibility'     => 1,
                'slider_order'   => 2,
                'featured_order' => 2,
                'pageviews'      => 2190,
                'post_type'      => 'post',
                'created_at'     => '2026-08-18 14:30:00'
            ],
            [
                'id'             => 3,
                'lang_id'        => 1,
                'title'          => 'Gốm Sứ Bát Tràng Di Sản: Giữ Vững Tinh Hoa Men Lam Thăng Long Vươn Tầm Toàn Cầu',
                'slug'           => 'gom-su-bat-trang-di-san',
                'summary'        => 'Dòng men lam cổ truyền Bát Tràng vượt qua các tiêu chuẩn khắt khe, đạt thứ hạng BEST #2 trong bảng vinh danh mỹ thuật & làng nghề truyền thống.',
                'content'        => '<p>Làng gốm Bát Tràng với lịch sử ngàn năm văn hiến đã ghi dấu ấn mạnh mẽ tại mùa giải TOP BEST GLOBAL 2026 với danh hiệu <strong>BEST #2</strong>.</p>
<p>Các nghệ nhân ưu tú đã ứng dụng công nghệ nung khí sạch kết hợp bí quyết pha chế men lam gia truyền, tạo ra những tuyệt tác mỹ nghệ phục vụ các nghi lễ ngoại giao quốc tế và xuất khẩu sang thị trường Bắc Mỹ, Nhật Bản.</p>',
                'category_id'    => 1,
                'image_url'      => 'assets/themes/suntransco/hero_bg.jpg',
                'user_id'        => 1,
                'status'         => 1,
                'visibility'     => 1,
                'slider_order'   => 3,
                'featured_order' => 3,
                'pageviews'      => 1850,
                'post_type'      => 'post',
                'created_at'     => '2026-08-19 10:15:00'
            ],
            [
                'id'             => 4,
                'lang_id'        => 1,
                'title'          => 'Cà Phê Robusta Honey Đắk Lắk: Nâng Cao Giá Trị Nông Sản Di Sản Bằng Chế Biến Sâu',
                'slug'           => 'ca-phe-robusta-honey-dak-lak',
                'summary'        => 'Quy trình sơ chế Honey lên men tự nhiên đạt chuẩn xuất khẩu quốc tế giúp cà phê Đắk Lắk đạt chứng nhận BEST #3 ngành Nông sản.',
                'content'        => '<p>Hợp tác xã Nông nghiệp Đắk Lắk đã xuất sắc được Hội đồng Chuyên môn VietKings vinh danh với sản phẩm <strong>Cà phê Robusta Honey</strong>.</p>
<p>Bằng việc liên kết cùng 300+ hộ nông dân bản địa áp dụng tiêu chuẩn thu hái quả chín 100% và lên men yếm khí tự nhiên, hạt cà phê Việt Nam đã đạt điểm cupping trên 85 điểm SCAA và được các đối tác châu Âu đón nhận nồng nhiệt.</p>',
                'category_id'    => 1,
                'image_url'      => 'assets/themes/suntransco/hero_bg.jpg',
                'user_id'        => 1,
                'status'         => 1,
                'visibility'     => 1,
                'slider_order'   => 4,
                'featured_order' => 4,
                'pageviews'      => 1420,
                'post_type'      => 'post',
                'created_at'     => '2026-08-17 16:45:00'
            ],
            [
                'id'             => 5,
                'lang_id'        => 1,
                'title'          => 'Công Bố Quy Chế Thẩm Định 4 Vòng & Cơ Chế Điểm 70/30 Minh Bạch',
                'slug'           => 'cong-bo-quy-che-tham-dinh-4-vong-70-30',
                'summary'        => 'Quy chế xét chọn TOP BEST GLOBAL kết hợp chặt chẽ giữa 70% Điểm Hội đồng Chuyên gia và 30% Điểm độc giả bình chọn có xác thực OTP.',
                'content'        => '<p>Nhằm đảm bảo sự công bằng và uy tín cao nhất cho giải thưởng, Ban Tổ Chức đã ban hành Quy chế thẩm định mùa giải 2026 – 2027 gồm 4 vòng xét duyệt: Sơ khảo hồ sơ pháp lý, Thẩm định năng lực thực tế, Chung khảo chấm điểm 70/30 và Trao chứng nhận Kỷ lục.</p>',
                'category_id'    => 2,
                'image_url'      => 'assets/themes/suntransco/hero_bg.jpg',
                'user_id'        => 1,
                'status'         => 1,
                'visibility'     => 1,
                'slider_order'   => 5,
                'featured_order' => 5,
                'pageviews'      => 1680,
                'post_type'      => 'post',
                'created_at'     => '2026-08-14 08:30:00'
            ],
            [
                'id'             => 6,
                'lang_id'        => 1,
                'title'          => 'Lộ Trình Đưa Nông Sản & Thủ Công Mỹ Nghệ Việt Ra Thị Trường Toàn Cầu',
                'slug'           => 'lo-trinh-dua-nong-san-thu-cong-viet-ra-the-gioi',
                'summary'        => 'Cách các doanh nghiệp đạt TOP BEST tận dụng mạng lưới truyền thông WorldKings để bảo chứng nguồn gốc và ký kết hợp đồng xuất khẩu.',
                'content'        => '<p>Hội thảo chuyên đề về xúc tiến thương mại quốc tế đã vạch rõ lộ trình 3 bước cho các đơn vị đạt giải thưởng TOP BEST tiếp cận sàn thương mại điện tử xuyên biên giới và các chuỗi bán lẻ quốc tế.</p>',
                'category_id'    => 3,
                'image_url'      => 'assets/themes/suntransco/hero_bg.jpg',
                'user_id'        => 1,
                'status'         => 1,
                'visibility'     => 1,
                'slider_order'   => 6,
                'featured_order' => 6,
                'pageviews'      => 1290,
                'post_type'      => 'post',
                'created_at'     => '2026-08-15 11:00:00'
            ],
            [
                'id'             => 7,
                'lang_id'        => 1,
                'title'          => 'Ứng Dụng Mã QR Xác Minh Chống Giả Mạo Trên Bao Bì Hàng Xuất Khẩu',
                'slug'           => 'ung-dung-ma-qr-xac-minh-bao-bi',
                'summary'        => 'Công nghệ mã QR động và tem chứng nhận số giúp khách hàng quốc tế kiểm tra tính xác thực của sản phẩm trong vòng 3 giây.',
                'content'        => '<p>Giải pháp xác minh huy hiệu bảo chứng Kỷ lục bằng mã định danh kỹ thuật số độc bản đã giúp các đơn vị đạt TOP BEST bảo vệ uy tín thương hiệu trên các thị trường xuất khẩu.</p>',
                'category_id'    => 3,
                'image_url'      => 'assets/themes/suntransco/hero_bg.jpg',
                'user_id'        => 1,
                'status'         => 1,
                'visibility'     => 1,
                'slider_order'   => 7,
                'featured_order' => 7,
                'pageviews'      => 980,
                'post_type'      => 'post',
                'created_at'     => '2026-08-10 15:20:00'
            ],
            [
                'id'             => 8,
                'lang_id'        => 1,
                'title'          => 'Tơ Lụa Bảo Lộc Heritage: Đưa Tơ Tằm Tự Nhiên Đến Kinh Đô Thời Trang Paris',
                'slug'           => 'to-lua-bao-loc-heritage',
                'summary'        => 'Làng nghề ươm tơ dệt lụa Bảo Lộc Lâm Đồng xuất sắc đạt phân hạng TOP #11 và tiến hành đàm phán cung ứng cho các nhà mốt châu Âu.',
                'content'        => '<p>Tơ lụa Bảo Lộc với sợi tơ tự nhiên 100% óng ả và mềm mịn đã khẳng định chất lượng vượt trội tại hội đồng đánh giá chất lượng TOP BEST GLOBAL.</p>',
                'category_id'    => 4,
                'image_url'      => 'assets/themes/suntransco/hero_bg.jpg',
                'user_id'        => 1,
                'status'         => 1,
                'visibility'     => 1,
                'slider_order'   => 8,
                'featured_order' => 8,
                'pageviews'      => 1150,
                'post_type'      => 'post',
                'created_at'     => '2026-08-16 13:40:00'
            ]
        ];
    }
}
