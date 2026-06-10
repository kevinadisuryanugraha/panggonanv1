<?php
/**
 * Temporary script to automatically inject tracker.js into all HTML files in subdirectories.
 */

$dir = __DIR__ . '/..';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
$html_files = [];

foreach ($iterator as $file) {
    if ($file->isFile() && strtolower($file->getExtension()) === 'html') {
        $path = $file->getPathname();
        
        // Exclude root index.html (already did it) and admin-traffic or scratch
        if (basename($path) === 'index.html' && 
            strpos($path, 'scratch') === false && 
            strpos($path, 'admin-traffic') === false &&
            realpath($path) !== realpath(__DIR__ . '/../index.html')) {
            $html_files[] = $path;
        }
    }
}

foreach ($html_files as $file) {
    $content = file_get_contents($file);
    
    // Check if tracker.js is already present
    if (strpos($content, 'tracker.js') !== false) {
        echo "Already injected: " . basename(dirname($file)) . "/" . basename($file) . "\n";
        continue;
    }
    
    // Inject before </body>
    $injected = preg_replace('/<\/body>/i', "  <script src=\"../assets/js/tracker.js\" defer></script>\n</body>", $content);
    
    if ($injected !== $content) {
        file_put_contents($file, $injected);
        echo "Injected successfully: " . basename(dirname($file)) . "/" . basename($file) . "\n";
    } else {
        echo "Failed to inject (no body tag found?): " . basename(dirname($file)) . "/" . basename($file) . "\n";
    }
}
echo "Injection complete!\n";
