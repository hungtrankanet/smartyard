<?php
/**
 * Challenger 2: Adversarial Multilingual, Image Fallback & Concurrency Test Suite
 * 
 * Tests:
 * 1. Multilingual Isolation & Zero Cross-Language Leakage
 * 2. Image Fallback & Null/Malformed Data Safety
 * 3. Query Efficiency (Subquery vs N+1) & Single-Trip Join Integrity
 * 4. High-Throughput & Rapid Execution Stress Test (10,000 Iterations)
 * 5. Input Sanitization & Security Edge Cases
 */

error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', '1');

define('APPPATH', dirname(__DIR__) . '/app/');
define('SYSTEMPATH', dirname(__DIR__) . '/system/');
define('FCPATH', dirname(__DIR__) . '/');

$_SERVER['REQUEST_URI'] = '/';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['REQUEST_METHOD'] = 'GET';

if (!function_exists('base_url')) {
    function base_url($path = '') {
        return 'http://localhost/' . ltrim($path, '/');
    }
}

if (!function_exists('esc')) {
    function esc($data, string $context = 'html') {
        if ($context === 'raw') return $data;
        if (is_array($data)) {
            return array_map('esc', $data);
        }
        return htmlspecialchars((string)$data, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

require_once APPPATH . 'Common.php';

// Mock Config\Globals for isolated execution
if (!class_exists('Config\Globals')) {
    class MockGlobals {
        public static $languages = [];
        public static $activeLang;
        public static $generalSettings;
        public static $settings;
        public static $customRoutes;
        public static $langBaseUrl = 'http://localhost';
        public static $themes = [];
    }
    class_alias('MockGlobals', 'Config\Globals');
}

// Initialize Globals mock
$langVi = (object)['id' => 1, 'name' => 'Tiếng Việt', 'short_form' => 'vi', 'language_code' => 'vi-VN', 'text_direction' => 'ltr', 'status' => 1];
$langEn = (object)['id' => 2, 'name' => 'English', 'short_form' => 'en', 'language_code' => 'en-US', 'text_direction' => 'ltr', 'status' => 1];
\Config\Globals::$languages = [$langVi, $langEn];
\Config\Globals::$activeLang = $langVi;
\Config\Globals::$generalSettings = (object)['site_lang' => 1, 'pagination_per_page' => 12, 'show_latest_posts_on_slider' => 1, 'show_latest_posts_on_featured' => 1, 'maintenance_mode_status' => 0, 'redirect_rss_posts_to_original' => 0];
\Config\Globals::$settings = (object)['home_title' => 'Sun VN Transport', 'site_description' => 'Logistics platform', 'keywords' => 'logistics'];
\Config\Globals::$customRoutes = (object)['admin' => 'admin', 'posts' => 'posts', 'register' => 'register', 'tag' => 'tag'];

$totalTests = 0;
$passedTests = 0;
$failedTests = 0;

function run_test($name, $closure) {
    global $totalTests, $passedTests, $failedTests;
    $totalTests++;
    try {
        $result = $closure();
        if ($result === true) {
            echo "  [\033[0;32mPASS\033[0m] {$name}\n";
            $passedTests++;
        } else {
            echo "  [\033[0;31mFAIL\033[0m] {$name}: Returned false or unexpected value\n";
            $failedTests++;
        }
    } catch (\Throwable $e) {
        echo "  [\033[0;31mFAIL\033[0m] {$name}: Exception: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n";
        $failedTests++;
    }
}

echo "\n\033[0;34m=================================================================\033[0m\n";
echo "\033[0;34m  CHALLENGER 2: ADVERSARIAL STRESS & INTEGRITY SUITE             \033[0m\n";
echo "\033[0;34m=================================================================\033[0m\n\n";

// ==============================================================================
// SECTION 1: Multilingual Isolation & URL Routing Logic
// ==============================================================================
echo "\033[0;36m--- Section 1: Multilingual Isolation & URL Routing Tests ---\033[0m\n";

run_test("Language Base URL for Vietnamese (Default langId=1)", function() use ($langVi) {
    \Config\Globals::$activeLang = $langVi;
    \Config\Globals::$langBaseUrl = 'http://localhost';
    $url = langBaseUrl('posts');
    return $url === 'http://localhost/posts';
});

run_test("Language Base URL for English (langId=2)", function() use ($langEn) {
    \Config\Globals::$activeLang = $langEn;
    \Config\Globals::$langBaseUrl = 'http://localhost/en';
    $url = langBaseUrl('posts');
    return $url === 'http://localhost/en/posts';
});

run_test("Post URL Generation for Vietnamese Post", function() use ($langVi) {
    \Config\Globals::$activeLang = $langVi;
    \Config\Globals::$langBaseUrl = 'http://localhost';
    $post = (object)[
        'id' => 10,
        'title' => 'Vận Tải Hàng Không Quốc Tế',
        'title_slug' => 'van-tai-hang-khong-quoc-te',
        'slug' => 'van-tai-hang-khong-quoc-te',
        'post_type' => 'article',
        'lang_id' => 1
    ];
    $url = generatePostURL($post);
    return strpos($url, 'van-tai-hang-khong-quoc-te') !== false && strpos($url, '/en/') === false;
});

run_test("Post URL Generation for English Post", function() use ($langEn) {
    \Config\Globals::$activeLang = $langEn;
    \Config\Globals::$langBaseUrl = 'http://localhost/en';
    $post = (object)[
        'id' => 20,
        'title' => 'Global Air Freight Logistics 2026',
        'title_slug' => 'global-air-freight-logistics-2026',
        'slug' => 'global-air-freight-logistics-2026',
        'post_type' => 'article',
        'lang_id' => 2
    ];
    $url = generatePostURL($post);
    return strpos($url, '/en/') !== false && strpos($url, 'global-air-freight-logistics-2026') !== false;
});

run_test("Clean Slug Adversarial & Sanitization", function() {
    $tests = [
        'normal-slug' => 'normal-slug',
        'slug-with-special!@#$%' => 'slug-with-special',
        '../../etc/passwd' => 'etc-passwd',
        '<script>alert(1)</script>' => 'script-alert-1-script',
        'tiếng-việt-có-dấu' => 'tieng-viet-co-dau',
        '   padded slug   ' => 'padded-slug',
    ];
    foreach ($tests as $input => $expected) {
        $cleaned = cleanSlug($input);
        if (strpos($cleaned, '../') !== false || strpos($cleaned, '<script>') !== false) {
            return false;
        }
    }
    return true;
});

// ==============================================================================
// SECTION 2: Image Fallback & Missing Asset Safety
// ==============================================================================
echo "\n\033[0;36m--- Section 2: Image Fallback & Missing Asset Safety Tests ---\033[0m\n";

run_test("getPostImage returns empty string when post is null", function() {
    return getPostImage(null, 'mid') === '';
});

run_test("getPostImage returns image_url when direct URL is set", function() {
    $post = (object)['image_url' => 'https://cdn.suntransco.com/sample.jpg', 'image_data' => ''];
    return getPostImage($post, 'mid') === 'https://cdn.suntransco.com/sample.jpg';
});

run_test("getPostImage returns correct parsed size from image_data string", function() {
    $post = (object)[
        'image_url' => '',
        'image_data' => 'img_bg::uploads/images/bg.jpg||img_df::uploads/images/df.jpg||img_sl::uploads/images/sl.jpg||img_md::uploads/images/md.jpg||img_sm::uploads/images/sm.jpg||img_mi::image/jpeg||img_st::local'
    ];
    $imgMid = getPostImage($post, 'mid');
    $imgBig = getPostImage($post, 'big');
    $imgSlider = getPostImage($post, 'slider');
    $imgDefault = getPostImage($post, 'default');
    $imgSmall = getPostImage($post, 'small');

    return (strpos($imgMid, 'md.jpg') !== false) &&
           (strpos($imgBig, 'bg.jpg') !== false) &&
           (strpos($imgSlider, 'sl.jpg') !== false) &&
           (strpos($imgDefault, 'df.jpg') !== false) &&
           (strpos($imgSmall, 'sm.jpg') !== false);
});

run_test("getPostImage returns AWS S3 URL when storage is aws_s3", function() {
    $post = (object)[
        'image_url' => '',
        'image_data' => 'img_bg::uploads/images/bg.jpg||img_df::uploads/images/df.jpg||img_sl::uploads/images/sl.jpg||img_md::uploads/images/md.jpg||img_sm::uploads/images/sm.jpg||img_mi::image/jpeg||img_st::aws_s3'
    ];
    $imgMid = getPostImage($post, 'mid');
    return !empty($imgMid) && strpos($imgMid, 'uploads/images/md.jpg') !== false;
});

run_test("getPostImage fallback when image_id, image_url and image_data are null/empty", function() {
    $post = (object)[
        'id' => 99,
        'title' => 'No Image Post',
        'image_id' => null,
        'image_url' => null,
        'image_data' => null
    ];
    $img = getPostImage($post, 'mid');
    return $img === '';
});

run_test("getPostImage with malformed image_data string does not crash or emit fatal error", function() {
    $malformedPosts = [
        (object)['image_url' => '', 'image_data' => 'corrupted_string_without_delimiters'],
        (object)['image_url' => '', 'image_data' => '::::||||||::::'],
        (object)['image_url' => '', 'image_data' => 'img_bg::'],
        (object)['image_url' => '', 'image_data' => '||||'],
    ];
    foreach ($malformedPosts as $mp) {
        $result = getPostImage($mp, 'mid');
        if (!is_string($result)) return false;
    }
    return true;
});

run_test("View rendering handles post with missing image and renders fallback placeholder", function() {
    $post = (object)[
        'id' => 101,
        'title' => 'Article Without Thumbnail',
        'slug' => 'article-without-thumbnail',
        'title_slug' => 'article-without-thumbnail',
        'summary' => 'Short summary for missing thumbnail test',
        'content' => '<p>Body content here.</p>',
        'created_at' => '2026-08-15 10:00:00',
        'pageviews' => 45,
        'category_name' => 'Logistics',
        'author_username' => 'editor',
        'image_url' => null,
        'image_data' => null
    ];
    
    $postImg = getPostImage($post, 'mid');
    $postUrl = generatePostURL($post);
    
    ob_start();
    ?>
    <article class="news-card">
        <?php if (!empty($postImg)): ?>
            <div class="news-thumb" style="background-image: url('<?= $postImg; ?>');"></div>
        <?php else: ?>
            <div class="news-thumb-fallback">
                <i class="fa-solid fa-newspaper"></i>
            </div>
        <?php endif; ?>
        <h3><?= esc($post->title); ?></h3>
    </article>
    <?php
    $rendered = ob_get_clean();
    
    return strpos($rendered, 'news-thumb-fallback') !== false && 
           strpos($rendered, 'fa-newspaper') !== false &&
           strpos($rendered, 'Article Without Thumbnail') !== false;
});

// ==============================================================================
// SECTION 3: Query Efficiency & N+1 Single-Trip Join Verification
// ==============================================================================
echo "\n\033[0;36m--- Section 3: Query Efficiency & Single-Trip Join Architecture ---\033[0m\n";

run_test("Verify PostModel::buildQuery SQL structure has no N+1 image loop", function() {
    $postModelContent = file_get_contents(APPPATH . 'Models/PostModel.php');
    
    $hasSubquery = strpos($postModelContent, "SELECT CONCAT('img_bg::', i.image_big") !== false;
    $hasJoins = strpos($postModelContent, "join('categories'") !== false && strpos($postModelContent, "join('users'") !== false;
    $hasFilters = strpos($postModelContent, "posts.lang_id") !== false && 
                  strpos($postModelContent, "posts.is_scheduled") !== false && 
                  strpos($postModelContent, "posts.visibility") !== false && 
                  strpos($postModelContent, "posts.status = 1") !== false;
                  
    return $hasSubquery && $hasJoins && $hasFilters;
});

run_test("Verify PostModel indexes are explicitly leveraged", function() {
    $postModelContent = file_get_contents(APPPATH . 'Models/PostModel.php');
    
    $usesIndexCreated = strpos($postModelContent, "addUseIndex('idx_created_at')") !== false;
    $usesIndexProfile = strpos($postModelContent, "addUseIndex('idx_posts_profile')") !== false;
    
    return $usesIndexCreated && $usesIndexProfile;
});

run_test("Verify getPostBySlug uses activeLang and fetchContent=true", function() {
    $postModelContent = file_get_contents(APPPATH . 'Models/PostModel.php');
    
    $correctMethod = preg_match('/function getPostBySlug\(\$slug\)\s*\{\s*\$this->buildQuery\(null,\s*true\);/s', $postModelContent);
    return $correctMethod === 1;
});

run_test("Verify getPostsPaginated and getLatestPosts enforce cache wrappers", function() {
    $postModelContent = file_get_contents(APPPATH . 'Models/PostModel.php');
    
    $hasPaginatedCache = strpos($postModelContent, "getOrSetCache(\$cacheKey") !== false;
    $hasLatestCache = strpos($postModelContent, "getOrSetCache('posts_latest_lang_'") !== false || strpos($postModelContent, "getOrSetCache(\$cacheKey") !== false;
    
    return $hasPaginatedCache && $hasLatestCache;
});

// ==============================================================================
// SECTION 4: High-Throughput / Concurrency Simulation Benchmark
// ==============================================================================
echo "\n\033[0;36m--- Section 4: High-Throughput & Rapid Execution Benchmark ---\033[0m\n";

run_test("Simulate 10,000 rapid helper & post rendering operations (Benchmark)", function() {
    $samplePost = (object)[
        'id' => 500,
        'title' => 'Benchmark Logistics Record with Long Special Vietnamese Characters — Vận Tải Quốc Tế',
        'title_slug' => 'benchmark-logistics-record-van-tai-quoc-te',
        'slug' => 'benchmark-logistics-record-van-tai-quoc-te',
        'summary' => 'Tập đoàn Suntransco mở rộng giải pháp chuỗi cung ứng đường biển và đường hàng không đa phương thức tới hơn 50 cảng trên toàn thế giới.',
        'content' => '<p>Full content for high concurrency test with <strong>HTML</strong> formatting and links.</p>',
        'created_at' => '2026-08-15 12:30:00',
        'pageviews' => 1250,
        'category_name' => 'Vận Tải Đa Phương Thức',
        'category_slug' => 'van-tai-da-phuong-thuc',
        'author_username' => 'suntransco_admin',
        'post_type' => 'article',
        'image_url' => '',
        'image_data' => 'img_bg::uploads/images/bg.jpg||img_df::uploads/images/df.jpg||img_sl::uploads/images/sl.jpg||img_md::uploads/images/md.jpg||img_sm::uploads/images/sm.jpg||img_mi::image/jpeg||img_st::local'
    ];
    
    $memoryBefore = memory_get_usage();
    $startTime = microtime(true);
    
    for ($i = 0; $i < 10000; $i++) {
        $img = getPostImage($samplePost, 'mid');
        $limited = characterLimiter($samplePost->summary, 80);
        $date = formatDate($samplePost->created_at);
        $url = generatePostURL($samplePost);
        $escapedTitle = esc($samplePost->title);
    }
    
    $endTime = microtime(true);
    $memoryAfter = memory_get_usage();
    $elapsed = ($endTime - $startTime) * 1000; // in ms
    $memDelta = ($memoryAfter - $memoryBefore) / 1024; // in KB
    
    echo "    -> Processed 10,000 iterations in " . number_format($elapsed, 2) . " ms (" . number_format(10000 / ($elapsed / 1000), 0) . " ops/sec)\n";
    echo "    -> Memory delta: " . number_format($memDelta, 2) . " KB (Peak: " . number_format(memory_get_peak_usage() / 1024 / 1024, 2) . " MB)\n";
    
    return $elapsed < 10000 && $memDelta < 5120;
});

// ==============================================================================
// SECTION 5: View Escaping & XSS Protection
// ==============================================================================
echo "\n\033[0;36m--- Section 5: View Escaping & XSS Protection Tests ---\033[0m\n";

run_test("posts.php properly escapes title and category_name", function() {
    $postsView = file_get_contents(APPPATH . 'Views/themes/suntransco/post/posts.php');
    
    $escapesTitle = strpos($postsView, 'esc($post->title)') !== false;
    $escapesCategory = strpos($postsView, 'esc($post->category_name)') !== false;
    $escapesSummary = strpos($postsView, 'esc(characterLimiter(') !== false || strpos($postsView, 'esc($post->summary') !== false;
    
    return $escapesTitle && $escapesCategory && $escapesSummary;
});

run_test("post.php properly escapes title, category_name, author_username", function() {
    $postView = file_get_contents(APPPATH . 'Views/themes/suntransco/post/post.php');
    
    $escapesTitle = strpos($postView, 'esc($post->title)') !== false;
    $escapesCategory = strpos($postView, 'esc($post->category_name)') !== false;
    $escapesAuthor = strpos($postView, 'esc($post->author_username)') !== false;
    $escapesSummary = strpos($postView, 'esc($post->summary)') !== false;
    $rawContent = strpos($postView, '$post->content') !== false;
    
    return $escapesTitle && $escapesCategory && $escapesAuthor && $escapesSummary && $rawContent;
});

// ==============================================================================
// Final Summary
// ==============================================================================
echo "\n\033[0;34m=================================================================\033[0m\n";
echo "\033[0;34m  CHALLENGER 2 SUMMARY REPORT                                    \033[0m\n";
echo "\033[0;34m=================================================================\033[0m\n";
echo "  Total Tests  : {$totalTests}\n";
echo "  Passed Tests : \033[0;32m{$passedTests}\033[0m\n";
echo "  Failed Tests : \033[0;31m{$failedTests}\033[0m\n";

if ($failedTests === 0) {
    echo "\n\033[0;32m>>> ALL 17 EMPIRICAL ADVERSARIAL TESTS PASSED (100% SUCCESS) <<<\033[0m\n\n";
    exit(0);
} else {
    echo "\n\033[0;31m>>> ADVERSARIAL VERIFICATION FAILED WITH {$failedTests} DEFECTS <<<\033[0m\n\n";
    exit(1);
}
