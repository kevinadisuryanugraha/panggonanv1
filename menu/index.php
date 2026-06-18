<?php
require_once __DIR__ . '/../config/db.php';

try {
    // 1. Fetch categories for left column
    $stmtLeft = $pdo->prepare("SELECT * FROM `menu_categories` WHERE `column_position` = 'left' ORDER BY `sort_order` ASC");
    $stmtLeft->execute();
    $leftCategories = $stmtLeft->fetchAll();

    // 2. Fetch categories for right column
    $stmtRight = $pdo->prepare("SELECT * FROM `menu_categories` WHERE `column_position` = 'right' ORDER BY `sort_order` ASC");
    $stmtRight->execute();
    $rightCategories = $stmtRight->fetchAll();
} catch (Exception $e) {
    // Fallback in case of database issues
    $leftCategories = [];
    $rightCategories = [];
}

// Batch fetch all available items once to avoid N+1 queries
$allItems = [];
try {
    $stmtAllItems = $pdo->query("SELECT * FROM `menu_items` WHERE `is_available` = 1 ORDER BY `category_id`, `sort_order` ASC");
    while ($row = $stmtAllItems->fetch()) {
        $allItems[$row['category_id']][] = $row;
    }
} catch (Exception $e) {
    $allItems = [];
}

// Construct JSON-LD Menu Schema dynamically
$menuSections = [];
foreach ($leftCategories as $cat) {
    $items = $allItems[$cat['id']] ?? [];
    if (count($items) > 0) {
        $sectionItems = [];
        foreach ($items as $item) {
            $sectionItems[] = [
                "@type" => "MenuItem",
                "name" => $item['name'],
                "description" => !empty($item['description']) ? $item['description'] : "Sajian khas tradisional Panggonan Resto.",
                "offers" => [
                    "@type" => "Offer",
                    "price" => (float)$item['price'],
                    "priceCurrency" => "IDR"
                ]
            ];
        }
        $menuSections[] = [
            "@type" => "MenuSection",
            "name" => $cat['name'],
            "hasMenuItem" => $sectionItems
        ];
    }
}
foreach ($rightCategories as $cat) {
    $items = $allItems[$cat['id']] ?? [];
    if (count($items) > 0) {
        $sectionItems = [];
        foreach ($items as $item) {
            $sectionItems[] = [
                "@type" => "MenuItem",
                "name" => $item['name'],
                "description" => !empty($item['description']) ? $item['description'] : "Sajian khas tradisional Panggonan Resto.",
                "offers" => [
                    "@type" => "Offer",
                    "price" => (float)$item['price'],
                    "priceCurrency" => "IDR"
                ]
            ];
        }
        $menuSections[] = [
            "@type" => "MenuSection",
            "name" => $cat['name'],
            "hasMenuItem" => $sectionItems
        ];
    }
}

$menuSchema = [
    "@context" => "https://schema.org",
    "@graph" => [
        [
            "@type" => "BreadcrumbList",
            "itemListElement" => [
                [
                    "@type" => "ListItem",
                    "position" => 1,
                    "name" => "Beranda",
                    "item" => "https://panggonanresto.com/"
                ],
                [
                    "@type" => "ListItem",
                    "position" => 2,
                    "name" => "Menu",
                    "item" => "https://panggonanresto.com/menu/"
                ]
            ]
        ],
        [
            "@type" => "Menu",
            "name" => "Menu Sajian Kuliner Tradisional Jawa | Panggonan Resto",
            "description" => "Daftar hidangan otentik Jawa di Panggonan Resto, mulai dari Ayam Panggang, Rica-Rica Entog, hingga Garang Asem.",
            "url" => "https://panggonanresto.com/menu/",
            "inLanguage" => "id-ID",
            "hasMenuSection" => $menuSections
        ]
    ]
];
?>
<!doctype html>

<html data-domain="panggonanresto.com" data-wf-page="673c30a404cb435e9b3e25d3" data-wf-site="672ca6f8f573a303d29afc7e"
  lang="id">

<head>
  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());

    gtag('config', 'G-XXXXXXXXXX');
  </script>
  <meta charset="utf-8" />
  <title>Menu Kuliner Tradisional Jawa Autentik | Panggonan Resto</title>
  <meta
    content="Nikmati hidangan otentik Nusantara yang dikurasi khusus untuk melengkapi momen istimewa Anda di bawah naungan arsitektur yang asri."
    name="description" />
  <meta name="keywords"
    content="Menu Panggonan, Kuliner Tradisional Jawa, Rica-Rica Entog, Ayam Panggang, Bebek Garam Asem, Wedang Ronde, Rumah Makan Jawa Ciracas, Restoran Depok" />
  <meta name="author" content="Panggonan Resto" />
  <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large" />
  <meta name="theme-color" content="#1a1a1a" />

  <!-- Canonical URL -->
  <link rel="canonical" href="https://panggonanresto.com/menu/" />

  <!-- Geo / Local SEO -->
  <meta name="geo.region" content="ID-JK" />
  <meta name="geo.placename" content="Ciracas, Jakarta Timur" />
  <meta name="geo.position" content="-6.3290;106.8718" />
  <meta name="ICBM" content="-6.3290, 106.8718" />

  <!-- Open Graph -->
  <meta property="og:type" content="restaurant" />
  <meta property="og:site_name" content="Panggonan Resto" />
  <meta property="og:locale" content="id_ID" />
  <meta property="og:url" content="https://panggonanresto.com/menu/" />
  <meta property="og:title" content="Menu Kuliner Tradisional Jawa Autentik | Panggonan Resto" />
  <meta property="og:description"
    content="Nikmati hidangan otentik Nusantara yang dikurasi khusus untuk melengkapi momen istimewa Anda di bawah naungan arsitektur yang asri." />
  <meta property="og:image"
    content="https://panggonanresto.com/assets/images/panggonan_aset_ke_2/ambiance_malam.webp" />
  <meta property="og:image:alt" content="Daftar Menu Tradisional Jawa Panggonan Resto" />
  <meta property="og:image:width" content="1200" />
  <meta property="og:image:height" content="630" />

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="Menu Kuliner Tradisional Jawa Autentik | Panggonan Resto" />
  <meta name="twitter:description"
    content="Nikmati hidangan otentik Nusantara yang dikurasi khusus untuk melengkapi momen istimewa Anda di bawah naungan arsitektur yang asri." />
  <meta name="twitter:image"
    content="https://panggonanresto.com/assets/images/panggonan_aset_ke_2/ambiance_malam.webp" />
  <meta content="width=device-width, initial-scale=1" name="viewport" />

  <!-- JSON-LD Structured Data -->
  <script type="application/ld+json">
    <?= json_encode($menuSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
  </script>
  <link href="../assets/css/style.css" rel="stylesheet" type="text/css" />
  <link href="../assets/css/custom.css" rel="stylesheet" type="text/css" />
  <link href="../assets/css/menu.css" rel="stylesheet" type="text/css" />
  <link href="https://fonts.googleapis.com" rel="preconnect" />
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin="anonymous" />
  <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js" type="text/javascript"></script>
  <script type="text/javascript">
    WebFont.load({ google: { families: ["Sora:regular"] } });
  </script>
  <script type="text/javascript">
    !(function (o, c) {
      var n = c.documentElement,
        t = " w-mod-";
      ((n.className += t + "js"),
        ("ontouchstart" in o ||
          (o.DocumentTouch && c instanceof DocumentTouch)) &&
        (n.className += t + "touch"));
    })(window, document);
  </script>
  <link href="../assets/images/logo.webp" rel="icon" type="image/webp" />
  <link href="../assets/images/logo.webp" rel="apple-touch-icon" />
</head>

<body>
  <div data-w-id="c4cece30-ea7b-9bee-6ab3-e3f6d3fb5623" data-animation="default" data-collapse="medium"
    data-duration="400" data-easing="ease" data-easing2="ease" role="banner" class="navbar w-nav">
    <div class="container w-container">
      <div class="nav-wrapper">
        <a href="../" class="brand w-nav-brand">
          <h3 style="margin: 0; color: #d4af37">Panggonan</h3>
        </a>
        <nav role="navigation" class="nav-menu w-nav-menu">
          <div class="menu-wraper">
            <div data-w-id="c9554779-7a98-ebc3-6d3f-25bc12cf16bb" class="nav-outer">
              <a href="../" class="nav-link w-nav-link">Beranda</a>
              <div class="nav-line"></div>
            </div>
            <div data-w-id="7a84d105-ac7b-73ee-9db2-22ef823bf66d" class="nav-outer">
              <a href="../about-us/" class="nav-link w-nav-link">Tentang Kami</a>
              <div class="nav-line"></div>
            </div>
            <div data-w-id="0d59ca41-bfc7-676d-c79e-5915ca4d4009" class="nav-outer">
              <a href="../menu/" aria-current="page" class="nav-link w-nav-link w--current">Menu</a>
              <div class="nav-line"></div>
            </div>
            <div data-w-id="2eedfb42-47ec-746e-339b-083a716ae9a9" class="nav-outer">
              <div data-hover="false" data-delay="0" data-w-id="bd1b9d5e-da99-33c1-ebf2-30f0a4ff637d"
                class="dropdown w-dropdown">
                <div class="navbar-dropdown w-dropdown-toggle">
                  <div>Halaman</div>
                  <img src="../assets/images/icons/dropdown-icon.svg" loading="lazy" alt="" class="dropdown-icon" decoding="async" />
                </div>
                <nav class="dropdown-list w-dropdown-list">
                  <div class="dropdown-wraper">
                    <a href="../services/" class="dropdown-link w-dropdown-link">Layanan</a>
                    <a href="../gallery/" class="dropdown-link w-dropdown-link">Galeri</a>
                    <a href="../faq/" class="dropdown-link w-dropdown-link">Tanya Jawab</a>
                  </div>
                </nav>
              </div>
              <div class="nav-line"></div>
            </div>
            <div data-w-id="f7736304-0fe0-85bb-bdba-d46abba42f16" class="nav-outer">
              <a href="../blog/" class="nav-link w-nav-link">Jurnal</a>
              <div class="nav-line"></div>
            </div>
            <div class="tablet-button">
              <a data-w-id="482fbd4e-f018-7011-d00d-554d28c22612" href="../contact-us/"
                class="primary-button w-inline-block">
                <div>Hubungi Kami</div>
                <img src="../assets/images/icons/btn-icon.svg" loading="lazy" alt="Arrow Icon" class="primary-button-image" decoding="async" />
              </a>
            </div>
          </div>
        </nav>
        <div>
          <div class="menu-button w-nav-button">
            <div class="menu-outer">
              <div data-w-id="c4cece30-ea7b-9bee-6ab3-e3f6d3fb5639" class="menu-bar"></div>
              <div data-w-id="c4cece30-ea7b-9bee-6ab3-e3f6d3fb563a" class="menu-bar"></div>
              <div data-w-id="c4cece30-ea7b-9bee-6ab3-e3f6d3fb563b" class="menu-bar last"></div>
            </div>
          </div>
          <div class="desktop-button">
            <a data-w-id="482fbd4e-f018-7011-d00d-554d28c22612" href="../contact-us/"
              class="primary-button w-inline-block">
              <div>Hubungi Kami</div>
              <img src="../assets/images/icons/btn-icon.svg" loading="lazy" alt="Arrow Icon" class="primary-button-image" decoding="async" />
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="page-wrap">
    <section class="hero-section-c">
      <div class="w-layout-blockcontainer container w-container">
        <div class="hero-c-wrapper">
          <div class="hero-c-content">
            <div class="title-top">
              <div class="caption">[ MENU PANGGONAN ]</div>
            </div>
            <h1 class="hero-c-title">Sajian Rasa, Racikan Jiwa</h1>
            <p class="body-large text-light-gray hero-c-subtitle">
              Nikmati hidangan otentik Nusantara yang dikurasi khusus untuk
              melengkapi momen istimewa Anda di bawah naungan arsitektur yang
              asri.
            </p>
          </div>
          <div class="hero-c-image-wrap">
            <img src="../assets/images/panggonan5.jpeg" alt="Sajian Utama" class="cover-image" style="border-radius: 16px" loading="lazy" decoding="async" />
          </div>
        </div>
      </div>
    </section>

    <section class="section menu-catalog" style="padding-top: 120px; padding-bottom: 100px">
      <div class="w-layout-blockcontainer container w-container">
        <div class="menu-intro-text" style="margin-bottom: 80px; text-align: center">
          <h2 style="
                font-family: serif;
                font-size: 3.5rem;
                color: #d4af37;
                margin-bottom: 20px;
                text-align: center;
              ">
            Daftar Menu
          </h2>
          <p style="
                color: #666;
                max-width: 600px;
                margin: 0 auto;
                font-size: 1.1rem;
                line-height: 1.6;
                text-align: center;
              ">
            Pilihan sajian otentik khas Nusantara, diracik dengan rempah
            pilihan dan disajikan hangat untuk Anda.
          </p>
        </div>

        <!-- The actual Menu Catalog -->
        <div class="menu-list-container">
          <!-- Left Column -->
          <div class="menu-list-col">
            <?php foreach ($leftCategories as $cat): ?>
              <?php $items = $allItems[$cat['id']] ?? []; ?>
              <?php if (count($items) > 0): ?>
                <div class="menu-list-category">
                  <h3 class="menu-list-title"><?= htmlspecialchars($cat['name']) ?></h3>
                  <div class="menu-list-items">
                    <?php foreach ($items as $item): ?>
                      <div class="list-item">
                        <div class="name-desc">
                          <span class="name"><?= htmlspecialchars($item['name']) ?></span>
                          <?php if (!empty($item['description'])): ?>
                            <span class="desc"><?= htmlspecialchars($item['description']) ?></span>
                          <?php endif; ?>
                        </div>
                        <div class="price">Rp. <?= number_format($item['price'], 0, ',', '.') ?></div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>

          <!-- Right Column -->
          <div class="menu-list-col">
            <?php foreach ($rightCategories as $cat): ?>
              <?php $items = $allItems[$cat['id']] ?? []; ?>
              <?php if (count($items) > 0): ?>
                <div class="menu-list-category">
                  <?php if ($cat['name'] !== 'Minuman Part 2'): ?>
                    <h3 class="menu-list-title"><?= htmlspecialchars($cat['name']) ?></h3>
                  <?php else: ?>
                    <div style="height: 12px"></div>
                  <?php endif; ?>
                  <div class="menu-list-items">
                    <?php foreach ($items as $item): ?>
                      <div class="list-item">
                        <div class="name-desc">
                          <span class="name"><?= htmlspecialchars($item['name']) ?></span>
                          <?php if (!empty($item['description'])): ?>
                            <span class="desc"><?= htmlspecialchars($item['description']) ?></span>
                          <?php endif; ?>
                        </div>
                        <div class="price">Rp. <?= number_format($item['price'], 0, ',', '.') ?></div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </section>

    <section class="section to-top">
      <div class="w-layout-blockcontainer container w-container">
        <div class="cta">
          <div class="cta-data">
            <div class="cta-top">
              <div class="title-wrapper">
                <div class="caption text-white">[ RESERVASI ]</div>
                <h2 class="text-white">
                  Nikmati Sajian Khas Nusantara di Panggonan
                </h2>
              </div>
              <div data-w-id="fd8dd0f6-4a32-89d8-e864-a183d565e00c">
                <a data-w-id="482fbd4e-f018-7011-d00d-554d28c22612" href="../contact-us/"
                  class="primary-button w-inline-block">
                  <div>Reservasi Sekarang</div>
                  <img src="../assets/images/icons/btn-icon.svg" loading="lazy" alt="Arrow Icon" class="primary-button-image" decoding="async" />
                </a>
              </div>
            </div>
            <div class="cta-bottom">
              <div class="cta-card">
                <h3 class="text-white">4.8</h3>
                <div class="text-light-gray">
                  Rating Bintang<br />di Google Maps
                </div>
              </div>
              <div class="cta-card">
                <h3 class="text-white">100+</h3>
                <div class="text-light-gray">Pilihan Menu<br />Tersedia</div>
              </div>
              <div class="cta-card">
                <h3 class="text-white">100%</h3>
                <div class="text-light-gray">
                  Rempah & Bahan<br />Otentik Lokal
                </div>
              </div>
            </div>
          </div>
          <div class="cta-image">
            <img alt="Panggonan Atmosphere" src="../assets/images/panggonan12.jpeg" loading="lazy" class="cover-image" decoding="async" />
          </div>
        </div>
      </div>
    </section>
  </div>
  <section class="footer bg-black">
    <div class="w-layout-blockcontainer container w-container">
      <div class="footer-block">
        <div class="footer-top">
          <div class="footer-left">
            <h3 class="text-white">Tetap Terhubung dengan Kami</h3>
            <div>
              Tidak menemukan jawaban yang Anda cari? Hubungi kami langsung
              dan tim Panggonan siap membantu dengan sepenuh hati.
            </div>
          </div>
          <div class="footer-right">
            <div class="footer-card">
              <div class="body-600">Perusahaan</div>
              <div class="footer-inner">
                <a href="../about-us/" class="footer-link body-small">Tentang</a><a href="../menu/"
                  class="footer-link body-small">Menu</a><a href="../services/"
                  class="footer-link body-small">Layanan</a><a href="../blog/"
                  class="footer-link body-small">Jurnal</a><a href="../contact-us/"
                  class="footer-link body-small">Kontak</a>
              </div>
            </div>
            <div class="footer-image-wrapper">
              <img src="../assets/images/panggonan_aset_ke_2/ambiance_luar_malam.webp" alt="Atmosfer Restoran Panggonan" loading="lazy" decoding="async" />
            </div>
          </div>
        </div>
        <div class="footer-middle">
          <a href="" aria-current="page" class="footer-logo w-inline-block w--current">
            <h3 style="margin: 0; color: #d4af37">Panggonan</h3>
          </a>
          <div class="footer-contacts">
            <div class="contact-card">
              <div style="
                    color: #d4af37;
                    font-weight: 600;
                    margin-bottom: 12px;
                    font-size: 1.1rem;
                    text-transform: uppercase;
                    letter-spacing: 0.05em;
                  ">
                Kirim Email
              </div>
              <a href="mailto:panggonanresto@gmail.com" class="footer-link" style="
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    font-size: 0.9rem;
                    line-height: 1.6;
                  ">
                <img src="../assets/images/icons/mail.svg" width="16" alt="Email" style="opacity: 0.7" loading="lazy" decoding="async" />
                panggonanresto@gmail.com
              </a>
              <a href="mailto:panggonanciracas@gmail.com" class="footer-link" style="
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    font-size: 0.9rem;
                    line-height: 1.6;
                  ">
                <img src="../assets/images/icons/mail.svg" width="16" alt="Email" style="opacity: 0.7" loading="lazy" decoding="async" />
                panggonanciracas@gmail.com
              </a>
              <a href="mailto:panggonangdc@gmail.com" class="footer-link" style="
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    font-size: 0.9rem;
                    line-height: 1.6;
                  ">
                <img src="../assets/images/icons/mail.svg" width="16" alt="Email" style="opacity: 0.7" loading="lazy" decoding="async" />
                panggonangdc@gmail.com
              </a>
            </div>
            <div class="contact-card">
              <div style="
                    color: #d4af37;
                    font-weight: 600;
                    margin-bottom: 12px;
                    font-size: 1.1rem;
                    text-transform: uppercase;
                    letter-spacing: 0.05em;
                  ">
                Telepon
              </div>
              <a href="tel:+6287828888538" class="footer-link" style="
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    font-size: 0.9rem;
                    line-height: 1.6;
                  ">
                <img src="../assets/images/icons/phone.svg" width="16" alt="Phone" style="opacity: 0.7" loading="lazy" decoding="async" />
                Ciracas: 0878-2888-8538
              </a>
              <a href="tel:+6287845359184" class="footer-link" style="
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    font-size: 0.9rem;
                    line-height: 1.6;
                  ">
                <img src="../assets/images/icons/phone.svg" width="16" alt="Phone" style="opacity: 0.7" loading="lazy" decoding="async" />
                GDC: 0878-4535-9184
              </a>
            </div>
            <div class="contact-card">
              <div style="
                    color: #d4af37;
                    font-weight: 600;
                    margin-bottom: 12px;
                    font-size: 1.1rem;
                    text-transform: uppercase;
                    letter-spacing: 0.05em;
                  ">
                Instagram
              </div>
              <a href="https://www.instagram.com/panggonan.resto/?hl=id" target="_blank" class="footer-link" style="
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    font-size: 0.9rem;
                    line-height: 1.6;
                  ">
                <img src="../assets/images/icons/instagram-icon.svg" width="16" alt="IG" style="opacity: 0.7" loading="lazy" decoding="async" />
                panggonan.resto
              </a>
              <a href="https://www.instagram.com/panggonan_gdc/?hl=id" target="_blank" class="footer-link" style="
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    font-size: 0.9rem;
                    line-height: 1.6;
                  ">
                <img src="../assets/images/icons/instagram-icon.svg" width="16" alt="IG" style="opacity: 0.7" loading="lazy" decoding="async" />
                panggonan_gdc
              </a>
              <a href="https://www.instagram.com/panggonan_ciracas/?hl=id" target="_blank" class="footer-link" style="
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    font-size: 0.9rem;
                    line-height: 1.6;
                  ">
                <img src="../assets/images/icons/instagram-icon.svg" width="16" alt="IG" style="opacity: 0.7" loading="lazy" decoding="async" />
                panggonan_ciracas
              </a>
            </div>
            <div class="contact-card">
              <div style="
                    color: #d4af37;
                    font-weight: 600;
                    margin-bottom: 12px;
                    font-size: 1.1rem;
                    text-transform: uppercase;
                    letter-spacing: 0.05em;
                  ">
                TikTok
              </div>
              <a href="https://www.tiktok.com/@panggonan.resto" target="_blank" class="footer-link" style="
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    font-size: 0.9rem;
                    line-height: 1.6;
                  ">
                <img src="../assets/images/icons/tiktok-icon.svg" width="16" alt="TikTok" style="opacity: 0.7" loading="lazy" decoding="async" />
                @panggonan.resto
              </a>
              <a href="https://www.tiktok.com/@panggonan_gdc" target="_blank" class="footer-link" style="
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    font-size: 0.9rem;
                    line-height: 1.6;
                  ">
                <img src="../assets/images/icons/tiktok-icon.svg" width="16" alt="TikTok" style="opacity: 0.7" loading="lazy" decoding="async" />
                @panggonan_gdc
              </a>
              <a href="https://www.tiktok.com/@panggonan.ciracas" target="_blank" class="footer-link" style="
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    font-size: 0.9rem;
                    line-height: 1.6;
                  ">
                <img src="../assets/images/icons/tiktok-icon.svg" width="16" alt="TikTok" style="opacity: 0.7" loading="lazy" decoding="async" />
                @panggonan.ciracas
              </a>
            </div>
          </div>
        </div>
        <div class="footer-bottom">
          <div class="body-small" style="color: rgba(255, 255, 255, 0.4)">
            &copy; 2026 Panggonan. Seluruh hak cipta dilindungi.
          </div>
        </div>
      </div>
    </div>
  </section>
  <script src="../assets/js/jquery-3.5.1.min.js" type="text/javascript" crossorigin="anonymous" defer></script>
  <script src="../assets/js/script.js" type="text/javascript" defer></script>
  <script src="../assets/js/panggonan-nav-fix.js" type="text/javascript"></script>
  <script src="../assets/js/tracker.js" defer></script>
</body>

</html>
