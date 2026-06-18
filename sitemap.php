<?php
header('Content-Type: application/xml; charset=utf-8');

$staticPages = [
  ['loc' => 'https://panggonanresto.com/',                    'lastmod' => '2026-06-10', 'freq' => 'weekly',  'priority' => '1.0'],
  ['loc' => 'https://panggonanresto.com/about-us/',            'lastmod' => '2026-06-10', 'freq' => 'monthly', 'priority' => '0.8'],
  ['loc' => 'https://panggonanresto.com/menu/',                'lastmod' => '2026-06-10', 'freq' => 'weekly',  'priority' => '0.9'],
  ['loc' => 'https://panggonanresto.com/contact-us/',           'lastmod' => '2026-06-10', 'freq' => 'monthly', 'priority' => '0.8'],
  ['loc' => 'https://panggonanresto.com/services/',            'lastmod' => '2026-06-10', 'freq' => 'monthly', 'priority' => '0.7'],
  ['loc' => 'https://panggonanresto.com/faq/',                 'lastmod' => '2026-06-10', 'freq' => 'monthly', 'priority' => '0.6'],
  ['loc' => 'https://panggonanresto.com/blog/',                'lastmod' => '2026-06-10', 'freq' => 'weekly',  'priority' => '0.7'],
  ['loc' => 'https://panggonanresto.com/gallery/',             'lastmod' => '2026-06-10', 'freq' => 'monthly', 'priority' => '0.7'],
];

$blogPages = [];
try {
  require_once __DIR__ . '/config/db.php';
  $stmt = $pdo->query("SELECT id, created_at FROM journals WHERE status = 'approved' ORDER BY id");
  while ($row = $stmt->fetch()) {
    $date = !empty($row['created_at']) ? substr($row['created_at'], 0, 10) : '2026-06-10';
    $blogPages[] = [
      'loc'      => 'https://panggonanresto.com/blog/' . (int)$row['id'] . '/',
      'lastmod'  => $date,
      'freq'     => 'monthly',
      'priority' => '0.5',
    ];
  }
} catch (Exception $e) {
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($staticPages as $page): ?>
  <url>
    <loc><?= $page['loc'] ?></loc>
    <lastmod><?= $page['lastmod'] ?></lastmod>
    <changefreq><?= $page['freq'] ?></changefreq>
    <priority><?= $page['priority'] ?></priority>
  </url>
<?php endforeach; ?>
<?php foreach ($blogPages as $page): ?>
  <url>
    <loc><?= $page['loc'] ?></loc>
    <lastmod><?= $page['lastmod'] ?></lastmod>
    <changefreq><?= $page['freq'] ?></changefreq>
    <priority><?= $page['priority'] ?></priority>
  </url>
<?php endforeach; ?>
</urlset>
