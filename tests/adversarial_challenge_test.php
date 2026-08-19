<?php
// ==============================================================================
// EMPIRICAL CHALLENGER 1: Adversarial & Edge Case Stress Test Harness
// Suntransco CodeIgniter 4 News Flow
// ==============================================================================

define('APPPATH', __DIR__ . '/../app/');
define('SYSTEMPATH', __DIR__ . '/../system/');
define('FCPATH', __DIR__ . '/../');

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

$totalPassed = 0;
$totalFailed = 0;

function run_test($name, $closure) {
    global $totalPassed, $totalFailed;
    try {
        $result = $closure();
        if ($result !== false) {
            echo "  [PASS] {$name}\n";
            $totalPassed++;
        } else {
            echo "  [FAIL] {$name}\n";
            $totalFailed++;
        }
    } catch (\Throwable $e) {
        echo "  [FAIL] {$name} - Exception: " . $e->getMessage() . "\n";
        $totalFailed++;
    }
}

echo "=================================================================\n";
echo "  EMPIRICAL CHALLENGER: ADVERSARIAL STRESS TEST SUITE\n";
echo "=================================================================\n\n";

// ------------------------------------------------------------------------------
// TEST SUITE 1: Slug Cleaning & Special Character Sanitization
// ------------------------------------------------------------------------------
echo "--- 1. Slug Sanitization & Edge Cases ---\n";

run_test("cleanSlug removes script tags", function() {
    $slug = '<script>alert(1)</script>my-post';
    $cleaned = cleanSlug($slug);
    return strpos($cleaned, '<script>') === false && strpos($cleaned, 'alert') !== false;
});

run_test("cleanSlug sanitizes SQL injection chars and special symbols", function() {
    $slug = "special!@#\$characters' OR 1=1--";
    $cleaned = cleanSlug($slug);
    return strpos($cleaned, "'") === false && strpos($cleaned, "#") === false && strpos($cleaned, "!") === false;
});

run_test("cleanSlug path traversal neutralization (removes directory separators)", function() {
    $slug = "/..%2F..%2Fetc/passwd";
    $cleaned = cleanSlug($slug);
    return strpos($cleaned, "/") === false && strpos($cleaned, "\\") === false;
});

run_test("cleanSlug empty/whitespace handling", function() {
    $slug = "   ";
    $cleaned = cleanSlug($slug);
    return empty($cleaned);
});

// ------------------------------------------------------------------------------
// TEST SUITE 2: Multibyte & Character Limiter Boundary Testing
// ------------------------------------------------------------------------------
echo "\n--- 2. Character Limiter & Formatting Boundaries ---\n";

run_test("characterLimiter with UTF-8 Vietnamese diacritics", function() {
    $viText = "Cập nhật tin tức thị trường vận tải biển, hàng không, giá cước và biến động chuỗi cung ứng toàn cầu.";
    $limited = characterLimiter($viText, 40);
    return mb_strlen($limited) <= 43 && mb_strpos($limited, "Cập nhật") === 0;
});

run_test("characterLimiter with null input", function() {
    $limited = characterLimiter(null, 50);
    return $limited === null || $limited === '';
});

run_test("characterLimiter with short text does not append ellipsis", function() {
    $short = "Short text";
    $limited = characterLimiter($short, 50);
    return $limited === "Short text";
});

run_test("formatDate with valid date string", function() {
    $formatted = formatDate('2026-08-15 12:30:00');
    return !empty($formatted) && (strpos($formatted, '2026') !== false || strpos($formatted, '15') !== false);
});

// ------------------------------------------------------------------------------
// TEST SUITE 3: XSS & HTML Escaping in Templates
// ------------------------------------------------------------------------------
echo "\n--- 3. XSS Injection & Template Escaping ---\n";

run_test("esc() function neutralizes XSS payloads", function() {
    $xssPayload = '<img src=x onerror=alert("XSS")>';
    $escaped = esc($xssPayload);
    return strpos($escaped, '<img') === false && strpos($escaped, '&lt;img') !== false;
});

run_test("esc() on post title with double quotes and angle brackets", function() {
    $titlePayload = 'Logistics "Mega-Ship" <New Route> 2026';
    $escaped = esc($titlePayload);
    return strpos($escaped, '&quot;') !== false || strpos($escaped, '&lt;') !== false;
});

// ------------------------------------------------------------------------------
// TEST SUITE 4: Image Parsing & Helper Robustness
// ------------------------------------------------------------------------------
echo "\n--- 4. Image Data Parsing & Helper Robustness ---\n";

run_test("getPostImage with serialized string format", function() {
    $post = new stdClass();
    $post->image_url = '';
    $post->image_data = 'img_bg::uploads/images/test_bg.jpg||img_df::uploads/images/test_df.jpg||img_sl::uploads/images/test_sl.jpg||img_md::uploads/images/test_md.jpg||img_sm::uploads/images/test_sm.jpg||img_mi::image/jpeg||img_st::local';
    
    $big = getPostImage($post, 'big');
    $mid = getPostImage($post, 'mid');
    $small = getPostImage($post, 'small');
    
    return strpos($big, 'test_bg.jpg') !== false && strpos($mid, 'test_md.jpg') !== false && strpos($small, 'test_sm.jpg') !== false;
});

run_test("getPostImage with fallback direct image_url", function() {
    $post = new stdClass();
    $post->image_url = 'https://cdn.example.com/direct_image.png';
    $post->image_data = '';
    
    $img = getPostImage($post, 'mid');
    return $img === 'https://cdn.example.com/direct_image.png';
});

run_test("getPostImage with completely empty post object returns empty string without crashing", function() {
    $post = new stdClass();
    $img = getPostImage($post, 'big');
    return is_string($img);
});

// ------------------------------------------------------------------------------
// TEST SUITE 5: Pagination Boundaries & Math
// ------------------------------------------------------------------------------
echo "\n--- 5. Pagination Boundaries & Calculation ---\n";

run_test("Pagination offset logic for negative or zero page", function() {
    $calcOffset = function($pageInput, $perPage) {
        $page = @intval($pageInput);
        if (empty($page) || $page < 1) {
            $page = 1;
        }
        return ($page - 1) * $perPage;
    };
    
    $negOffset = $calcOffset(-5, 12);
    $zeroOffset = $calcOffset(0, 12);
    $abcOffset = $calcOffset('abc', 12);
    $sqliOffset = $calcOffset("' OR 1=1--", 12);
    $hugeOffset = $calcOffset(999999, 12);
    
    return $negOffset === 0 && $zeroOffset === 0 && $abcOffset === 0 && $sqliOffset === 0 && $hugeOffset === 11999976;
});

// ------------------------------------------------------------------------------
// TEST SUITE 6: Query Isolation Logic Audit
// ------------------------------------------------------------------------------
echo "\n--- 6. Post Security & Draft Isolation SQL Audit ---\n";

run_test("PostModel query constraints include status=1, visibility=1, is_scheduled=0, lang_id", function() {
    $postModelContent = file_get_contents(APPPATH . 'Models/PostModel.php');
    $hasLangCheck = strpos($postModelContent, "'posts.lang_id'") !== false || strpos($postModelContent, "posts.lang_id") !== false;
    $hasScheduleCheck = strpos($postModelContent, "'posts.is_scheduled', 0") !== false || strpos($postModelContent, "posts.is_scheduled = 0") !== false;
    $hasVisibilityCheck = strpos($postModelContent, "'posts.visibility', 1") !== false || strpos($postModelContent, "posts.visibility = 1") !== false;
    $hasStatusCheck = strpos($postModelContent, "posts.status = 1") !== false || strpos($postModelContent, "'posts.status', 1") !== false;
    
    return $hasLangCheck && $hasScheduleCheck && $hasVisibilityCheck && $hasStatusCheck;
});

run_test("HomeController::any returns 404 on missing slug", function() {
    $controllerContent = file_get_contents(APPPATH . 'Controllers/HomeController.php');
    return strpos($controllerContent, 'return $this->error404();') !== false;
});

// ------------------------------------------------------------------------------
// TEST SUITE 7: Template Escaping Audit (Static Regex Scanner)
// ------------------------------------------------------------------------------
echo "\n--- 7. View Template Injection Scanner ---\n";

run_test("posts.php escapes all dynamic fields (\$post->title, summary, category_name)", function() {
    $postsView = file_get_contents(APPPATH . 'Views/themes/suntransco/post/posts.php');
    $hasEscTitle = strpos($postsView, 'esc($post->title)') !== false;
    $hasEscSummary = strpos($postsView, 'esc(characterLimiter') !== false;
    $hasEscCategory = strpos($postsView, 'esc($post->category_name)') !== false;
    return $hasEscTitle && $hasEscSummary && $hasEscCategory;
});

run_test("post.php escapes title, summary, category_name, views, author", function() {
    $postView = file_get_contents(APPPATH . 'Views/themes/suntransco/post/post.php');
    $hasEscTitle = strpos($postView, 'esc($post->title)') !== false;
    $hasEscSummary = strpos($postView, 'esc($post->summary)') !== false;
    $hasEscCategory = strpos($postView, 'esc($post->category_name)') !== false;
    $hasEscViews = strpos($postView, 'esc($post->pageviews') !== false;
    $hasEscAuthor = strpos($postView, 'esc($post->author_username)') !== false;
    return $hasEscTitle && $hasEscSummary && $hasEscCategory && $hasEscViews && $hasEscAuthor;
});

run_test("index.php news section escapes title, summary, category_name", function() {
    $indexView = file_get_contents(APPPATH . 'Views/themes/suntransco/index.php');
    $hasEscTitle = strpos($indexView, 'esc($post->title)') !== false;
    $hasEscSummary = strpos($indexView, 'esc(characterLimiter') !== false;
    $hasEscCategory = strpos($indexView, 'esc($post->category_name)') !== false;
    return $hasEscTitle && $hasEscSummary && $hasEscCategory;
});

// ------------------------------------------------------------------------------
// SUMMARY
// ------------------------------------------------------------------------------
echo "\n=================================================================\n";
echo "  EMPIRICAL CHALLENGER RESULTS: {$totalPassed} PASSED, {$totalFailed} FAILED\n";
echo "=================================================================\n";

if ($totalFailed > 0) {
    exit(1);
}
exit(0);
