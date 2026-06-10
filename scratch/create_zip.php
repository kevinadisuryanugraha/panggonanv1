<?php
/**
 * Create clean production ZIP archive using ZipArchive
 */
$zipName = __DIR__ . '/../panggonan_release.zip';
if (file_exists($zipName)) {
    unlink($zipName);
}

$zip = new ZipArchive();
if ($zip->open($zipName, ZipArchive::CREATE) !== TRUE) {
    die("Gagal membuat zip file.");
}

$sourceDir = realpath(__DIR__ . '/../');

// Files and Folders to include
$includes = [
    'about-us', 'admin', 'assets', 'blog', 'contact-us', 'config', 'faq', 'gallery', 'menu', 'services',
    '401', '404', 'coming-soon', 'chat_owner_hari_ini',
    'index.html', 'manifest.json', 'sw.js', 'robots.txt', 'sitemap.xml', '.htaccess', 'database.sql',
    'laporan_progress_owner.txt', 'full_project_report.md'
];

foreach ($includes as $item) {
    $itemPath = $sourceDir . '/' . $item;
    if (!file_exists($itemPath)) continue;

    if (is_dir($itemPath)) {
        // Recursively add directory
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($itemPath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($sourceDir) + 1);
                // Standardize zip paths for Linux server
                $relativePath = str_replace('\\', '/', $relativePath);
                $zip->addFile($filePath, $relativePath);
            }
        }
    } else {
        // Add single file
        $relativePath = $item;
        $zip->addFile($itemPath, $relativePath);
    }
}

$zip->close();
echo "SUCCESS: Clean zip archive created at " . realpath($zipName) . "\n";
