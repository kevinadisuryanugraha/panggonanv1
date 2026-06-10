<?php
/**
 * Fast targeted injector for tracker.js
 */

$parentDir = dirname(__DIR__);
$files = glob($parentDir . '/*/index.html');

foreach ($files as $f) {
    // Exclude scratch or admin-traffic
    if (strpos($f, 'scratch') !== false || strpos($f, 'admin-traffic') !== false) {
        continue;
    }
    
    $c = file_get_contents($f);
    if (strpos($c, 'tracker.js') === false) {
        $c = str_replace('</body>', "  <script src=\"../assets/js/tracker.js\" defer></script>\n</body>", $c);
        file_put_contents($f, $c);
        echo "Injected: " . basename(dirname($f)) . "/index.html\n";
    } else {
        echo "Already exists in: " . basename(dirname($f)) . "/index.html\n";
    }
}
echo "Done!\n";
