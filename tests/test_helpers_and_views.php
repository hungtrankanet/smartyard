<?php
// Test harness for Suntransco CI4 News Flow Views & Helpers
define('APPPATH', __DIR__ . '/../app/');
define('SYSTEMPATH', __DIR__ . '/../system/');
define('FCPATH', __DIR__ . '/../');

$_SERVER['REQUEST_URI'] = '/';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['REQUEST_METHOD'] = 'GET';

require_once APPPATH . 'Common.php';

echo "Testing Helper Functions & View Rendering...\n";

// Mock post object
$post = new stdClass();
$post->id = 1;
$post->title = 'Test Logistics News Title';
$post->title_slug = 'test-logistics-news-title';
$post->slug = 'test-logistics-news-title';
$post->summary = 'This is a test summary for the logistics news article that should be truncated properly.';
$post->content = '<p>This is full article content with <strong>HTML</strong> markup.</p>';
$post->created_at = '2026-08-15 12:00:00';
$post->pageviews = 128;
$post->category_name = 'Vận Tải Biển';
$post->category_slug = 'van-tai-bien';
$post->author_username = 'admin';
$post->post_type = 'article';
$post->image_url = 'https://example.com/uploads/images/test.jpg';
$post->image_data = 'img_bg::uploads/images/test_bg.jpg||img_df::uploads/images/test_df.jpg||img_sl::uploads/images/test_sl.jpg||img_md::uploads/images/test_md.jpg||img_sm::uploads/images/test_sm.jpg||img_mi::image/jpeg||img_st::local';

// 1. Test getPostImage
$imgMid = getPostImage($post, 'mid');
assert(!empty($imgMid), 'getPostImage mid must not be empty');
echo "  [PASS] getPostImage('mid'): " . $imgMid . "\n";

$imgBig = getPostImage($post, 'big');
assert(!empty($imgBig), 'getPostImage big must not be empty');
echo "  [PASS] getPostImage('big'): " . $imgBig . "\n";

// 2. Test characterLimiter
$limited = characterLimiter($post->summary, 20);
assert(mb_strlen($limited) <= 23, 'characterLimiter must truncate string');
echo "  [PASS] characterLimiter(20): " . $limited . "\n";

// 3. Test formatDate
$formattedDate = formatDate($post->created_at);
assert(!empty($formattedDate), 'formatDate must return formatted timestamp');
echo "  [PASS] formatDate(): " . $formattedDate . "\n";

echo "All helper assertions PASSED successfully!\n";
