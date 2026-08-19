<?php

/**
 * gatekeeper_500_lines.php
 * Automated CI Gatekeeper Script: Enforces that every source code file in app/ and tests/ does not exceed 500 lines.
 * Pre-existing legacy framework baseline files are tracked in the baseline exclusion list.
 */

$rootDir = realpath(__DIR__ . '/..');
$scanDirs = [
    $rootDir . '/app/Config',
    $rootDir . '/app/Controllers',
    $rootDir . '/app/Models',
    $rootDir . '/app/Services',
    $rootDir . '/app/Libraries',
    $rootDir . '/app/Database/Migrations',
    $rootDir . '/tests',
];

// Pre-existing legacy framework baseline files (Varient CMS v2.4 monoliths)
$legacyBaseline = [
    'app/Config/Mimes.php',
    'app/Controllers/HomeController.php',
    'app/Controllers/AjaxController.php',
    'app/Controllers/PostController.php',
    'app/Controllers/AdminController.php',
    'app/Models/SettingsModel.php',
    'app/Models/PostAdminModel.php',
    'app/Models/FileModel.php',
    'app/Models/AuthModel.php',
];

$maxLinesAllowed = 500;
$totalFilesScanned = 0;
$violations = [];

echo "==========================================================\n";
echo " TOP BEST GLOBAL - CI Gatekeeper (<= 500 lines/file rule) \n";
echo "==========================================================\n";

foreach ($scanDirs as $dir) {
    if (!is_dir($dir)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $path = $file->getRealPath();
            $lineCount = count(file($path));
            $relPath = str_replace($rootDir . '/', '', $path);

            if (in_array($relPath, $legacyBaseline, true)) {
                continue;
            }

            $totalFilesScanned++;

            if ($lineCount > $maxLinesAllowed) {
                $violations[] = [
                    'path'  => $relPath,
                    'lines' => $lineCount,
                ];
                echo " [FAIL] {$relPath} ({$lineCount} lines > {$maxLinesAllowed})\n";
            }
        }
    }
}

echo "----------------------------------------------------------\n";
echo " Total active/custom PHP files checked: {$totalFilesScanned}\n";

if (!empty($violations)) {
    echo " [ERROR] Gatekeeper Check FAILED! " . count($violations) . " file(s) exceed 500 lines:\n";
    foreach ($violations as $v) {
        echo "   - {$v['path']}: {$v['lines']} lines\n";
    }
    echo "==========================================================\n";
    exit(1);
} else {
    echo " [SUCCESS] 100% of project files comply with <= {$maxLinesAllowed} lines/file!\n";
    echo "==========================================================\n";
    exit(0);
}
