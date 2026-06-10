<?php
require_once __DIR__ . '/../config/db.php';

try {
    // Fetch dynamic categories
    $stmt_cats = $pdo->query("SELECT * FROM gallery_categories ORDER BY sort_order ASC, id ASC");
    $gallery_categories = $stmt_cats->fetchAll();

    // Fetch dynamic gallery items joined with categories
    $stmt = $pdo->query("SELECT g.*, c.slug as category_slug, c.name as category_name FROM gallery g JOIN gallery_categories c ON g.category_id = c.id ORDER BY g.id ASC");
    $gallery_items = $stmt->fetchAll();
} catch (Exception $e) {
    $gallery_categories = [];
    $gallery_items = [];
}
?>
<!doctype html>
<html data-domain="panggonanresto.com" data-wf-site="672ca6f8f573a303d29afc7e" lang="id">

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
  <title>Galeri Visual Rumah Makan Jawa Klasik | Panggonan Resto</title>
  <meta
    content="Jelajahi galeri visual Panggonan Resto — arsitektur upcycle joglo klasik, masakan tradisional Jawa, dan momen kebersamaan yang tak terlupakan."
    name="description" />
  <meta content="Galeri Visual Rumah Makan Jawa Klasik | Panggonan Resto" property="og:title" />
  <meta
    content="Jelajahi galeri visual Panggonan Resto — arsitektur upcycle joglo klasik, masakan tradisional Jawa, dan momen kebersamaan yang tak terlupakan."
    property="og:description" />
  <meta content="Galeri Visual Rumah Makan Jawa Klasik | Panggonan Resto" property="twitter:title" />
  <meta
    content="Jelajahi galeri visual Panggonan Resto — arsitektur upcycle joglo klasik, masakan tradisional Jawa, dan momen kebersamaan yang tak terlupakan."
    property="twitter:description" />
  <meta property="og:type" content="website" />
  <meta content="summary_large_image" name="twitter:card" />
  <meta content="width=device-width, initial-scale=1" name="viewport" />
  <meta name="keywords"
    content="Galeri Panggonan, Foto Joglo Klasik, Foto Makanan Jawa, Arsitektur Upcycle, Restoran Estetik Depok, Kuliner Ciracas" />
  <meta name="robots" content="index, follow" />
  <meta name="theme-color" content="#1a1a1a" />
  <link rel="canonical" href="https://panggonanresto.com/gallery/" />
  <meta name="geo.region" content="ID-JK" />
  <meta name="geo.placename" content="Ciracas, Jakarta Timur" />
  <meta name="geo.position" content="-6.3290;106.8718" />
  <meta name="ICBM" content="-6.3290, 106.8718" />
  <meta property="og:image"
    content="https://panggonanresto.com/assets/images/panggonan_aset_ke_2/ambiance_malam.webp" />
  <meta property="og:image:alt" content="Galeri Visual Panggonan Resto" />
  <meta property="og:url" content="https://panggonanresto.com/gallery/" />
  <meta property="og:locale" content="id_ID" />
  <meta property="og:site_name" content="Panggonan Resto" />
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@graph": [
      {
        "@type": "BreadcrumbList",
        "itemListElement": [
          {
            "@type": "ListItem",
            "position": 1,
            "name": "Beranda",
            "item": "https://panggonanresto.com/"
          },
          {
            "@type": "ListItem",
            "position": 2,
            "name": "Galeri",
            "item": "https://panggonanresto.com/gallery/"
          }
        ]
      },
      {
        "@type": "ImageGallery",
        "name": "Galeri Visual Rumah Makan Jawa Klasik | Panggonan Resto",
        "description": "Koleksi foto visual suasana estetik Joglo klasik, kuliner otentik Jawa, arsitektur upcycle vintage, dan momen kebersamaan di Panggonan Resto.",
        "url": "https://panggonanresto.com/gallery/"
      }
    ]
  }
  </script>
  <link href="../assets/css/style.css" rel="stylesheet" type="text/css" />
  <link href="../assets/css/custom.css" rel="stylesheet" type="text/css" />
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
  <link href="../assets/images/logo.webp" rel="shortcut icon" type="image/x-icon" />
  <link href="../assets/images/logo.webp" rel="apple-touch-icon" />
  <style>
    /* ============================================
           GALLERY PAGE - Premium Masonry Design
        ============================================ */
    .gallery-hero {
      padding: 100px 0 60px 0;
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .gallery-hero::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: radial-gradient(ellipse at center,
          rgba(212, 175, 55, 0.04) 0%,
          transparent 70%);
      pointer-events: none;
    }

    .gallery-hero-title {
      font-size: 3.8rem;
      line-height: 1.08;
      margin: 0 0 20px 0;
      color: var(--black, #1a1a1a);
    }

    .gallery-hero-sub {
      max-width: 640px;
      margin: 0 auto;
      color: #666;
      font-size: 1.05rem;
      line-height: 1.7;
    }

    /* Filter Tabs */
    .gallery-filters {
      display: flex;
      justify-content: center;
      gap: 10px;
      flex-wrap: wrap;
      margin-bottom: 48px;
    }

    .gallery-filter-btn {
      padding: 10px 24px;
      border: 1px solid #ddd;
      border-radius: 100px;
      background: transparent;
      font-family: inherit;
      font-size: 0.85rem;
      font-weight: 600;
      color: #666;
      cursor: pointer;
      transition: all 0.3s ease;
      letter-spacing: 0.04em;
    }

    .gallery-filter-btn:hover,
    .gallery-filter-btn.active {
      background: #0e0d09;
      color: #d4af37;
      border-color: #0e0d09;
    }

    /* Masonry Grid */
    .gallery-masonry {
      columns: 3;
      column-gap: 20px;
    }

    .gallery-item {
      break-inside: avoid;
      margin-bottom: 20px;
      border-radius: 14px;
      overflow: hidden;
      position: relative;
      cursor: pointer;
      display: block;
    }

    .gallery-item img {
      width: 100%;
      display: block;
      transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    .gallery-item:hover img {
      transform: scale(1.06);
    }

    .gallery-overlay {
      position: absolute;
      inset: 0;
      background: linear-gradient(to top,
          rgba(0, 0, 0, 0.7) 0%,
          transparent 55%);
      opacity: 0;
      transition: opacity 0.4s ease;
      display: flex;
      flex-direction: column;
      justify-content: flex-end;
      padding: 28px;
    }

    .gallery-item:hover .gallery-overlay {
      opacity: 1;
    }

    .gallery-overlay-title {
      font-size: 1.05rem;
      font-weight: 700;
      color: #fff;
      margin: 0 0 4px 0;
    }

    .gallery-overlay-cat {
      font-size: 0.75rem;
      color: #d4af37;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.12em;
    }

    /* Lightbox */
    .lightbox-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.92);
      z-index: 9999;
      justify-content: center;
      align-items: center;
      backdrop-filter: blur(12px);
      animation: lbFadeIn 0.25s ease forwards;
    }

    .lightbox-overlay.show {
      display: flex;
    }

    @keyframes lbFadeIn {
      from {
        opacity: 0;
      }

      to {
        opacity: 1;
      }
    }

    .lightbox-content {
      position: relative;
      max-width: 85vw;
      max-height: 85vh;
    }

    .lightbox-content img {
      max-width: 85vw;
      max-height: 85vh;
      border-radius: 12px;
      object-fit: contain;
      box-shadow: 0 20px 80px rgba(0, 0, 0, 0.5);
    }

    .lightbox-close {
      position: fixed;
      top: 28px;
      right: 32px;
      width: 48px;
      height: 48px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.15);
      color: #fff;
      font-size: 1.5rem;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
      z-index: 10;
    }

    .lightbox-close:hover {
      background: #d4af37;
      color: #0e0d09;
      border-color: #d4af37;
    }

    .lightbox-caption {
      position: fixed;
      bottom: 32px;
      left: 50%;
      transform: translateX(-50%);
      color: rgba(255, 255, 255, 0.7);
      font-size: 0.9rem;
      text-align: center;
      max-width: 600px;
    }

    .lightbox-caption strong {
      color: #d4af37;
      display: block;
      font-size: 1.1rem;
      margin-bottom: 4px;
    }

    .lightbox-nav {
      position: fixed;
      top: 50%;
      transform: translateY(-50%);
      width: 48px;
      height: 48px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.15);
      color: #fff;
      font-size: 1.3rem;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
      z-index: 10;
    }

    .lightbox-nav:hover {
      background: #d4af37;
      color: #0e0d09;
      border-color: #d4af37;
    }

    .lightbox-prev {
      left: 28px;
    }

    .lightbox-next {
      right: 28px;
    }

    /* Stats strip */
    .gallery-stats {
      display: flex;
      justify-content: center;
      gap: 60px;
      padding: 48px 0;
      border-top: 1px solid var(--border, #e8e8e8);
      margin-top: 64px;
    }

    .gallery-stat {
      text-align: center;
    }

    .gallery-stat-num {
      font-size: 2.5rem;
      font-weight: 700;
      color: #d4af37;
      line-height: 1;
      margin-bottom: 6px;
    }

    .gallery-stat-label {
      font-size: 0.85rem;
      color: #888;
    }

    @media (max-width: 991px) {
      .gallery-masonry {
        columns: 2;
      }

      .gallery-hero-title {
        font-size: 2.8rem;
      }

      .gallery-stats {
        gap: 32px;
      }
    }

    @media (max-width: 600px) {
      .gallery-masonry {
        columns: 1;
        column-gap: 0;
      }

      .gallery-hero-title {
        font-size: 2.2rem;
      }

      .gallery-hero {
        padding: 80px 0 40px 0;
      }

      .gallery-stats {
        flex-direction: column;
        gap: 24px;
      }

      .lightbox-nav {
        display: none;
      }
    }
  </style>
</head>

<body>
  <!-- NAVBAR -->
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
              <a href="../menu/" class="nav-link w-nav-link">Menu</a>
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

  <!-- PAGE CONTENT -->
  <div class="page-wrap">
    <!-- HERO -->
    <section class="gallery-hero">
      <div class="w-layout-blockcontainer container w-container">
        <div class="caption" style="margin-bottom: 16px">
          [ Galeri Visual ]
        </div>
        <h1 class="gallery-hero-title">Potret Panggonan</h1>
        <p class="gallery-hero-sub">
          Setiap sudut, setiap cahaya, dan setiap momen di Panggonan memiliki
          cerita tersendiri. Jelajahi keindahan arsitektur upcycle, suasana
          hangat Jawa, dan kelezatan sajian kami.
        </p>
      </div>
    </section>

    <!-- GALLERY SECTION -->
    <section style="padding: 0 0 80px 0">
      <div class="w-layout-blockcontainer container w-container">
        <!-- Filter Buttons -->
        <div class="gallery-filters">
          <button class="gallery-filter-btn active" onclick="filterGallery('all', this)">
            Semua
          </button>
          <?php foreach ($gallery_categories as $cat): ?>
            <button class="gallery-filter-btn" onclick="filterGallery('<?= htmlspecialchars($cat['slug']) ?>', this)">
              <?= htmlspecialchars($cat['name']) ?>
            </button>
          <?php endforeach; ?>
        </div>

        <!-- Masonry Grid -->
        <div class="gallery-masonry" id="galleryGrid">
          <?php if (count($gallery_items) > 0): ?>
            <?php $i = 0; foreach ($gallery_items as $item): ?>
              <div class="gallery-item" data-category="<?= htmlspecialchars($item['category_slug']) ?>" onclick="openLightbox(<?= $i ?>)">
                <img src="../<?= htmlspecialchars($item['image_url']) ?>" loading="lazy" alt="<?= htmlspecialchars($item['title']) ?>" decoding="async" />
                <div class="gallery-overlay">
                  <div class="gallery-overlay-cat"><?= htmlspecialchars($item['category_name']) ?></div>
                  <div class="gallery-overlay-title"><?= htmlspecialchars($item['title']) ?></div>
                </div>
              </div>
            <?php $i++; endforeach; ?>
          <?php else: ?>
            <div style="text-align: center; color: #888; grid-column: 1/-1; padding: 48px 0; font-family: inherit;">
              Belum ada foto di galeri saat ini.
            </div>
          <?php endif; ?>
        </div>  </div>

        <!-- Stats -->
        <div class="gallery-stats">
          <div class="gallery-stat">
            <div class="gallery-stat-num"><?= count($gallery_items) ?>+</div>
            <div class="gallery-stat-label">Koleksi Foto</div>
          </div>
          <div class="gallery-stat">
            <div class="gallery-stat-num"><?= count($gallery_categories) ?></div>
            <div class="gallery-stat-label">Kategori</div>
          </div>
          <div class="gallery-stat">
            <div class="gallery-stat-num">8</div>
            <div class="gallery-stat-label">Cerita di Tiap Sudut</div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="section to-top">
      <div class="w-layout-blockcontainer container w-container">
        <div class="cta">
          <div class="cta-data">
            <div class="cta-top">
              <div class="title-wrapper">
                <div class="caption text-white">[ Kunjungi Kami ]</div>
                <h2 class="text-white">
                  Datang & Rasakan Sendiri Suasananya
                </h2>
              </div>
              <div>
                <a href="../contact-us/" class="primary-button w-inline-block">
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
                <h3 class="text-white">2</h3>
                <div class="text-light-gray">Cabang<br />Aktif</div>
              </div>
              <div class="cta-card">
                <h3 class="text-white">100%</h3>
                <div class="text-light-gray">
                  Rempah &amp; Bahan<br />Otentik Lokal
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

  <!-- LIGHTBOX MODAL -->
  <div class="lightbox-overlay" id="lightbox">
    <button class="lightbox-close" onclick="closeLightbox()">&times;</button>
    <button class="lightbox-nav lightbox-prev" onclick="navigateLightbox(-1)">
      &#8249;
    </button>
    <div class="lightbox-content">
      <img id="lightbox-img" src="" alt="" / loading="lazy" decoding="async">
    </div>
    <button class="lightbox-nav lightbox-next" onclick="navigateLightbox(1)">
      &#8250;
    </button>
    <div class="lightbox-caption" id="lightbox-caption"></div>
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
                <img src="../assets/images/icons/mail.svg" width="16" alt="Email" style="opacity: 0.7" / loading="lazy" decoding="async">
                panggonanresto@gmail.com
              </a>
              <a href="mailto:panggonanciracas@gmail.com" class="footer-link" style="
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    font-size: 0.9rem;
                    line-height: 1.6;
                  ">
                <img src="../assets/images/icons/mail.svg" width="16" alt="Email" style="opacity: 0.7" / loading="lazy" decoding="async">
                panggonanciracas@gmail.com
              </a>
              <a href="mailto:panggonangdc@gmail.com" class="footer-link" style="
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    font-size: 0.9rem;
                    line-height: 1.6;
                  ">
                <img src="../assets/images/icons/mail.svg" width="16" alt="Email" style="opacity: 0.7" / loading="lazy" decoding="async">
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
                <img src="../assets/images/icons/phone.svg" width="16" alt="Phone" style="opacity: 0.7" / loading="lazy" decoding="async">
                Ciracas: 0878-2888-8538
              </a>
              <a href="tel:+6287845359184" class="footer-link" style="
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    font-size: 0.9rem;
                    line-height: 1.6;
                  ">
                <img src="../assets/images/icons/phone.svg" width="16" alt="Phone" style="opacity: 0.7" / loading="lazy" decoding="async">
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
                <img src="../assets/images/icons/instagram-icon.svg" width="16" alt="IG" style="opacity: 0.7" / loading="lazy" decoding="async">
                panggonan.resto
              </a>
              <a href="https://www.instagram.com/panggonan_gdc/?hl=id" target="_blank" class="footer-link" style="
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    font-size: 0.9rem;
                    line-height: 1.6;
                  ">
                <img src="../assets/images/icons/instagram-icon.svg" width="16" alt="IG" style="opacity: 0.7" / loading="lazy" decoding="async">
                panggonan_gdc
              </a>
              <a href="https://www.instagram.com/panggonan_ciracas/?hl=id" target="_blank" class="footer-link" style="
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    font-size: 0.9rem;
                    line-height: 1.6;
                  ">
                <img src="../assets/images/icons/instagram-icon.svg" width="16" alt="IG" style="opacity: 0.7" / loading="lazy" decoding="async">
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
                <img src="../assets/images/icons/tiktok-icon.svg" width="16" alt="TikTok" style="opacity: 0.7" / loading="lazy" decoding="async">
                @panggonan.resto
              </a>
              <a href="https://www.tiktok.com/@panggonan_gdc" target="_blank" class="footer-link" style="
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    font-size: 0.9rem;
                    line-height: 1.6;
                  ">
                <img src="../assets/images/icons/tiktok-icon.svg" width="16" alt="TikTok" style="opacity: 0.7" / loading="lazy" decoding="async">
                @panggonan_gdc
              </a>
              <a href="https://www.tiktok.com/@panggonan.ciracas" target="_blank" class="footer-link" style="
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    font-size: 0.9rem;
                    line-height: 1.6;
                  ">
                <img src="../assets/images/icons/tiktok-icon.svg" width="16" alt="TikTok" style="opacity: 0.7" / loading="lazy" decoding="async">
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

  <script src="../assets/js/jquery-3.5.1.min.js" type="text/javascript" crossorigin="anonymous"  defer></script>
  <script src="../assets/js/script.js" type="text/javascript" defer></script>
  <script>
    // ====== Gallery Filter ======
    function filterGallery(cat, btn) {
      var items = document.querySelectorAll(".gallery-item");
      items.forEach(function (item, i) {
        if (cat === "all" || item.getAttribute("data-category") === cat) {
          item.style.display = "block";
        } else {
          item.style.display = "none";
        }
      });
      document.querySelectorAll(".gallery-filter-btn").forEach(function (b) {
        b.classList.remove("active");
      });
      btn.classList.add("active");
    }

    // ====== Lightbox ======
    var lbItems = [];
    var lbIndex = 0;

    function buildLightboxData() {
      lbItems = [];
      document.querySelectorAll(".gallery-item").forEach(function (item) {
        if (item.style.display !== "none") {
          var img = item.querySelector("img");
          var title = item.querySelector(".gallery-overlay-title");
          var cat = item.querySelector(".gallery-overlay-cat");
          lbItems.push({
            src: img.src,
            title: title ? title.textContent : "",
            cat: cat ? cat.textContent : "",
          });
        }
      });
    }

    function openLightbox(index) {
      buildLightboxData();
      // find real index in visible items
      var visibleItems = document.querySelectorAll(
        '.gallery-item:not([style*="display: none"])',
      );
      var clickedItem = document.querySelectorAll(".gallery-item")[index];
      lbIndex = Array.from(visibleItems).indexOf(clickedItem);
      if (lbIndex < 0) lbIndex = 0;
      showLightboxImage();
      document.getElementById("lightbox").classList.add("show");
      document.body.style.overflow = "hidden";
    }

    function closeLightbox() {
      document.getElementById("lightbox").classList.remove("show");
      document.body.style.overflow = "";
    }

    function navigateLightbox(dir) {
      lbIndex += dir;
      if (lbIndex < 0) lbIndex = lbItems.length - 1;
      if (lbIndex >= lbItems.length) lbIndex = 0;
      showLightboxImage();
    }

    function showLightboxImage() {
      var data = lbItems[lbIndex];
      document.getElementById("lightbox-img").src = data.src;
      document.getElementById("lightbox-caption").innerHTML =
        "<strong>" + data.title + "</strong>" + data.cat;
    }

    // Close on ESC / background click
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape") closeLightbox();
      if (e.key === "ArrowLeft") navigateLightbox(-1);
      if (e.key === "ArrowRight") navigateLightbox(1);
    });
    document
      .getElementById("lightbox")
      .addEventListener("click", function (e) {
        if (e.target === this) closeLightbox();
      });
  </script>
  <script src="../../assets/js/panggonan-nav-fix.js" type="text/javascript"></script>
  <script src="../assets/js/tracker.js" defer></script>
</body>

</html>