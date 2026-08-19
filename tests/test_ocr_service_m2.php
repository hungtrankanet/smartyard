<?php
/**
 * Test Suite for OcrService (Milestone 2 - v2.0 Multilingual Upgrade)
 */

define('APPPATH', dirname(__DIR__) . '/app/');
define('SYSTEMPATH', dirname(__DIR__) . '/system/');
define('FCPATH', dirname(__DIR__) . '/');

require_once APPPATH . 'Libraries/OcrService.php';

use App\Libraries\OcrService;

echo "\n--- RUNNING OCR SERVICE UNIT TESTS ---\n";

$ocr = new OcrService();

// Test 1: Empty inputs
$empty = $ocr->extractBusinessCard('');
assert(count($empty) >= 20, 'Empty input should return at least 20 keys');
assert($empty['company_name'] === '', 'Empty company_name');
echo "✔ Test 1: Empty input returns normalized 20+ empty keys\n";

// Test 2: Deterministic fallback for image path
$res1 = $ocr->extractBusinessCard('uploads/cards/card_suntrans_front.jpg');
assert(count($res1) >= 20, 'Should return at least 20 keys');
assert(!empty($res1['company_name']), 'Should have company_name');
assert(is_string($res1['tax_code']), 'tax_code is string');
assert(is_string($res1['representative_name']), 'representative_name is string');
assert(is_string($res1['contact_name']), 'contact_name is string');
echo "✔ Test 2: Fallback extraction returns valid enterprise profile: {$res1['company_name']}\n";

// Test 3: Array of images (Front + Back)
$merged = $ocr->extractBusinessCard([
    'front' => 'uploads/cards/anhduong_front.jpg',
    'back'  => 'uploads/cards/anhduong_back.jpg'
]);
assert(count($merged) >= 20, 'Merged cards have at least 20 keys');
assert(!empty($merged['company_name']), 'Merged has company_name');
assert(!empty($merged['tax_code']), 'Merged has tax_code');
echo "✔ Test 3: Front + Back merged card extraction: {$merged['company_name']} (MST: {$merged['tax_code']})\n";

// Test 4: mergeOcrResults helper
$frontSample = [
    'company_name' => 'Công Ty Logistics Mẫu',
    'phone' => '0901234567',
    'email' => '',
    'tax_code' => '',
];
$backSample = [
    'company_name' => '',
    'phone' => '',
    'email' => 'contact@logistics.vn',
    'tax_code' => '0109988776',
];
$customMerged = $ocr->mergeOcrResults($frontSample, $backSample);
assert($customMerged['company_name'] === 'Công Ty Logistics Mẫu', 'Front company_name preserved');
assert($customMerged['phone'] === '0901234567', 'Front phone preserved');
assert($customMerged['email'] === 'contact@logistics.vn', 'Back email merged');
assert($customMerged['tax_code'] === '0109988776', 'Back tax_code merged');
echo "✔ Test 4: mergeOcrResults preserves non-empty fields from both sides\n";

// Test 5: Image encoding
$tmpImg = tempnam(sys_get_temp_dir(), 'test_card_') . '.jpg';
file_put_contents($tmpImg, 'fake-jpeg-binary-header-data-sample-content');
$encoded = $ocr->encodeImage($tmpImg);
assert(!empty($encoded['data']), 'Base64 data generated');
assert(!empty($encoded['mime_type']), 'Mime type generated');
@unlink($tmpImg);
echo "✔ Test 5: encodeImage generates valid base64 payload\n";

echo "\n--- ALL OCR SERVICE TESTS PASSED SUCCESSFULLY! ---\n\n";
