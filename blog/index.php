<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// Fetch approved journals from database
$stmt = $pdo->query("SELECT * FROM journals WHERE status = 'approved' ORDER BY id DESC LIMIT 6");
$approved_journals = $stmt->fetchAll();

$total_stmt = $pdo->query("SELECT COUNT(*) FROM journals WHERE status = 'approved'");
$total_journals = $total_stmt->fetchColumn();

// Check if a story was just submitted
$story_submitted = false;
$upload_error = '';
$input_nama = '';
$input_ig = '';
$input_pesan = '';

if (isset($_GET['submitted']) && isset($_SESSION['story_submitted'])) {
    $story_submitted = true;
    unset($_SESSION['story_submitted']);
    if (isset($_SESSION['story_upload_error'])) {
        $upload_error = $_SESSION['story_upload_error'];
        unset($_SESSION['story_upload_error']);
    }
} elseif (isset($_SESSION['story_upload_error'])) {
    $upload_error = $_SESSION['story_upload_error'];
    unset($_SESSION['story_upload_error']);
}

if (isset($_SESSION['story_form_data'])) {
    $input_nama = $_SESSION['story_form_data']['nama'] ?? '';
    $input_ig = $_SESSION['story_form_data']['ig'] ?? '';
    $input_pesan = $_SESSION['story_form_data']['pesan'] ?? '';
    unset($_SESSION['story_form_data']);
}

// Generate Dynamic Blog Schema structured data
$journalSchemaList = [];
foreach ($approved_journals as $journal) {
    $journalSchemaList[] = [
        "@type" => "BlogPosting",
        "headline" => $journal['quote'],
        "description" => substr(strip_tags($journal['text']), 0, 160) . '...',
        "author" => [
            "@type" => "Person",
            "name" => !empty($journal['author']) ? $journal['author'] : "Pengunjung Panggonan"
        ],
        "datePublished" => !empty($journal['created_at']) ? substr($journal['created_at'], 0, 10) : '2026-05-23',
        "publisher" => [
            "@type" => "Organization",
            "name" => "Panggonan Resto",
            "logo" => [
                "@type" => "ImageObject",
                "url" => "https://panggonanresto.com/assets/images/logo.webp"
            ]
        ],
        "mainEntityOfPage" => [
            "@type" => "WebPage",
            "@id" => "https://panggonanresto.com/blog/"
        ]
    ];
}

$blogSchema = [
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
                    "name" => "Jurnal",
                    "item" => "https://panggonanresto.com/blog/"
                ]
            ]
        ],
        [
            "@type" => "Blog",
            "name" => "Jurnal Cerita, Kuliner & Filosofi Jawa | Panggonan Resto",
            "description" => "Kumpulan kisah hangat, filosofi arsitektur upcycle, resep legendaris Jawa, dan catatan cerita kebersamaan dari sahabat Panggonan.",
            "url" => "https://panggonanresto.com/blog/",
            "publisher" => [
                "@type" => "Organization",
                "name" => "Panggonan Resto",
                "logo" => [
                    "@type" => "ImageObject",
                    "url" => "https://panggonanresto.com/assets/images/logo.webp"
                ]
            ],
            "blogPost" => $journalSchemaList
        ]
    ]
];
?>
<!doctype html>
<html data-domain="panggonanresto.com" lang="id">

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
  <title>Jurnal Cerita, Kuliner & Filosofi Jawa | Panggonan Resto</title>
  <meta
    content="Kumpulan kisah hangat, filosofi arsitektur upcycle, resep legendaris Jawa, dan catatan cerita kebersamaan dari sahabat Panggonan."
    name="description" />
  <meta content="Jurnal Cerita, Kuliner & Filosofi Jawa | Panggonan Resto" property="og:title" />
  <meta content="Kumpulan kisah hangat, filosofi arsitektur upcycle, resep legendaris Jawa, dan catatan cerita kebersamaan dari sahabat Panggonan."
    property="og:description" />
  <meta property="og:type" content="website" />
  <meta content="summary_large_image" name="twitter:card" />
  <meta content="width=device-width, initial-scale=1" name="viewport" />
  <meta name="keywords"
    content="Jurnal Panggonan, Cerita Rakyat Jawa, Resep Klasik Jawa, Upcycle Joglo, Restoran Ciracas, Restoran Depok, Kuliner Jawa" />
  <meta name="robots" content="index, follow" />
  <meta name="theme-color" content="#1a1a1a" />
  <link rel="canonical" href="https://panggonanresto.com/blog/" />
  <meta name="geo.region" content="ID-JK" />
  <meta name="geo.placename" content="Ciracas, Jakarta Timur" />
  <meta name="geo.position" content="-6.3290;106.8718" />
  <meta name="ICBM" content="-6.3290, 106.8718" />
  <meta property="og:image"
    content="https://panggonanresto.com/assets/images/panggonan_aset_ke_2/ambiance_malam.webp" />
  <meta property="og:image:alt" content="Jurnal Cerita Panggonan Resto" />
  <meta property="og:url" content="https://panggonanresto.com/blog/" />
  <meta property="og:locale" content="id_ID" />
  <meta property="og:site_name" content="Panggonan Resto" />

  <!-- JSON-LD Structured Data -->
  <script type="application/ld+json">
    <?= json_encode($blogSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
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
      n.className += t + "js";
      ("ontouchstart" in o ||
        (o.DocumentTouch && c instanceof DocumentTouch)) &&
        (n.className += t + "touch");
    })(window, document);
  </script>
  <link href="../assets/images/logo.webp" rel="shortcut icon" type="image/x-icon" />
  <link href="../assets/images/logo.webp" rel="apple-touch-icon" />
  <style>
    /* Jurnal custom styles */
    .clue-hero {
      padding: 48px 0 24px 0;
      text-align: center;
    }

    .clue-title-wrap {
      display: inline-block;
      margin-bottom: 24px;
    }

    .clue-title {
      font-size: 3.5rem;
      line-height: 1.1;
      color: var(--black);
      margin: 0;
    }

    .clue-subtitle {
      font-size: 1.1rem;
      color: #555;
      max-width: 760px;
      margin: 0 auto 20px auto;
      line-height: 1.6;
    }

    .journal-grid {
      column-count: 3;
      column-gap: 32px;
      gap: 32px;
      margin-top: 0;
    }

    .journal-card {
      break-inside: avoid;
      margin-bottom: 32px;
      display: flex;
      flex-direction: column;
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 16px;
      overflow: hidden;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .journal-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
    }

    .journal-img-wrap {
      width: 100%;
      height: auto;
      background: #f5f5f5;
      overflow: hidden;
      display: flex;
    }

    .journal-img {
      width: 100%;
      height: auto;
      display: block;
      transition: transform 0.4s ease;
    }

    .journal-card:hover .journal-img {
      transform: scale(1.05);
    }

    .journal-content {
      padding: 32px;
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .journal-date {
      font-size: 0.8rem;
      color: var(--primary-gold, #d4af37);
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.1em;
    }

    .journal-quote {
      font-size: 1.35rem;
      font-weight: 600;
      color: var(--black);
      line-height: 1.4;
      font-style: italic;
    }

    .journal-text {
      color: #666;
      font-size: 0.95rem;
      line-height: 1.6;
    }

    /* User Story Form */
    .share-story {
      background-color: #faf9f6;
      padding: 80px 0;
      margin-bottom: 80px;
      border-top: 1px solid var(--border);
    }

    .story-container {
      max-width: 800px;
      margin: 0 auto;
      background: #fff;
      padding: 48px;
      border-radius: 20px;
      box-shadow: 0 12px 32px rgba(0, 0, 0, 0.04);
      border: 1px solid var(--border);
    }

    @media (max-width: 767px) {
      .journal-grid {
        column-count: 1;
      }

      .clue-title {
        font-size: 2.5rem;
      }

      .story-container {
        padding: 32px 20px;
      }
    }

    /* General form fields */
    .form-group {
      display: flex;
      flex-direction: column;
      gap: 8px;
      margin-bottom: 20px;
    }

    .input-field {
      width: 100%;
      padding: 14px 16px;
      border: 1px solid #ddd;
      border-radius: 10px;
      font-family: inherit;
      font-size: 0.95rem;
      background: #fafaf9;
      transition: all 0.2s;
      box-sizing: border-box;
    }

    .input-field:focus {
      outline: none;
      border-color: var(--primary-gold, #d4af37);
      background: #fff;
      box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.15);
    }

    .form-label {
      display: block;
      font-size: 0.88rem;
      font-weight: 600;
      color: #444;
    }

    .btn-whatsapp {
      background-color: #25d366 !important;
      color: #fff !important;
    }

    .btn-whatsapp:hover {
      background-color: #128c7e !important;
    }

    .btn-submit-story {
      background-color: #c2a67f !important; /* Warna krem pasir hangat bawaan */
      color: #fff !important;
      width: 100%;
      justify-content: center;
      align-items: center;
      border: none;
      cursor: pointer;
      margin-top: 12px;
      padding: 18px;
      border-radius: 6px;
      font-family: inherit;
      font-size: 1rem;
      font-weight: 600;
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
      display: flex;
      filter: none !important; /* Mencegah filter invert global */
    }

    .btn-submit-story:hover {
      background-color: #b0946d !important; /* Warna krem pasir yang sedikit lebih gelap proporsional */
      color: #fff !important;
      filter: none !important;
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(194, 166, 127, 0.25);
    }

    .btn-submit-story:active {
      transform: translateY(0);
      box-shadow: 0 4px 12px rgba(194, 166, 127, 0.15);
    }

    /* SUCCESS MODAL */
    .success-overlay {
      position: fixed; inset: 0;
      background: rgba(5, 6, 10, 0.85);
      backdrop-filter: blur(10px);
      display: flex; align-items: center; justify-content: center;
      z-index: 10002;
      font-family: 'Sora', sans-serif;
    }
    .success-modal {
      background: #fff;
      border: 2px solid rgba(212, 175, 55, 0.5);
      border-radius: 24px;
      padding: 48px;
      width: 100%;
      max-width: 480px;
      text-align: center;
      box-shadow: 0 24px 80px rgba(0,0,0,0.3);
      animation: popUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    @keyframes popUp {
      from { transform: scale(0.85); opacity: 0; }
      to { transform: scale(1); opacity: 1; }
    }
    .success-icon {
      width: 80px; height: 80px;
      background: rgba(212, 175, 55, 0.1);
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 28px auto;
      color: #d4af37;
    }
    .success-modal h3 {
      font-size: 1.5rem; color: #121622; margin-bottom: 16px;
      font-weight: 700; letter-spacing: -0.02em;
    }
    .success-modal p {
      color: #555; font-size: 0.95rem; line-height: 1.6; margin-bottom: 32px;
    }
    .btn-close-success {
      background: #d4af37; color: #fff; border: none;
      padding: 14px 40px; border-radius: 30px;
      font-family: 'Sora', sans-serif; font-weight: 700;
      font-size: 0.95rem; cursor: pointer; transition: all 0.2s;
      box-shadow: 0 4px 12px rgba(212, 175, 55, 0.2);
    }
    .btn-close-success:hover { background: #bda031; transform: translateY(-2px); }
    #btn-load-more:hover { background: var(--primary-gold, #d4af37) !important; color: #fff !important; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(212, 175, 55, 0.2); }

    /* ERROR MODAL */
    .error-overlay {
      position: fixed; inset: 0;
      background: rgba(5, 6, 10, 0.85);
      backdrop-filter: blur(10px);
      display: flex; align-items: center; justify-content: center;
      z-index: 10002;
      font-family: 'Sora', sans-serif;
    }
    .error-modal {
      background: #fff;
      border: 2px solid rgba(239, 68, 68, 0.5);
      border-radius: 24px;
      padding: 48px;
      width: 100%;
      max-width: 480px;
      text-align: center;
      box-shadow: 0 24px 80px rgba(0,0,0,0.3);
      animation: popUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .error-icon {
      width: 80px; height: 80px;
      background: rgba(239, 68, 68, 0.1);
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 28px auto;
      color: #ef4444;
    }
    .btn-close-error {
      background: #ef4444; color: #fff; border: none;
      padding: 14px 40px; border-radius: 30px;
      font-family: 'Sora', sans-serif; font-weight: 700;
      font-size: 0.95rem; cursor: pointer; transition: all 0.2s;
      box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
    }
    .btn-close-error:hover { background: #dc2626; transform: translateY(-2px); }
  </style>
  <!-- TinyMCE WYSIWYG Editor -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
  <script>
    tinymce.init({
      selector: '.text-area',
      plugins: 'lists link code',
      toolbar: 'undo redo | bold italic | bullist numlist | link | code',
      menubar: false,
      height: 200,
      setup: function (editor) {
        editor.on('change', function () {
          editor.save();
        });
      }
    });
  </script>
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
              <a href="../blog/" aria-current="page" class="nav-link w-nav-link w--current">Jurnal</a>
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
    <!-- HERO Jurnal Panggonan -->
    <section class="clue-hero">
      <div class="w-layout-blockcontainer container w-container">
        <div class="clue-title-wrap">
          <div class="caption" style="margin-bottom: 2rem">
            [ filosofi & cerita panggonan ]
          </div>
          <h1 class="clue-title">Jurnal Panggonan</h1>
        </div>
        <p class="clue-subtitle">
          Setiap sudut <strong>Panggonan</strong> menyimpan cerita. Dari arsitektur upcycle
          hingga senyuman yang tercipta di atas hidangan, kami mengundang Anda untuk menyelami
          makna di balik hal-hal sederhana. Tak ada ruang yang
          dipaksakan. Kami merajut keindahan dari benda yang terpinggirkan
          agar menjadi sudut yang punya resonansi dan ketenangan bagi
          siapa pun yang singgah.
        </p>
        <p class="clue-subtitle" style="margin-top: 0; font-style: italic; color: #888">
          Temukan inspirasi Anda di sini.
        </p>
      </div>
    </section>

    <!-- JOURNAL GRID (DYNAMIC FROM MySQL) -->
    <section style="padding: 24px 0 64px 0">
      <div class="w-layout-blockcontainer container w-container">
        <div class="journal-grid" id="journal-grid-container">
          <?php foreach ($approved_journals as $post): ?>
          <div class="journal-card">
            <?php if ($post['media_type'] === 'video' && !empty($post['media_url'])): ?>
            <div class="journal-img-wrap" style="position: relative">
              <video src="../<?= htmlspecialchars($post['media_url']) ?>#t=0.001" preload="metadata" controls muted loop
                playsinline style="width: 100%; height: auto; display: block;" onplay="
                    this.parentElement.querySelector('.vid-play-btn').style.opacity = '0';
                    this.parentElement.querySelector('.vid-play-btn').style.pointerEvents = 'none';
                  " onpause="
                    this.parentElement.querySelector('.vid-play-btn').style.opacity = '1';
                    this.parentElement.querySelector('.vid-play-btn').style.pointerEvents = 'auto';
                  "></video>
              <div class="vid-play-btn" style="
                    position: absolute; inset: 0;
                    display: flex; align-items: center; justify-content: center;
                    transition: opacity 0.3s ease; cursor: pointer;
                  " onclick="this.parentElement.querySelector('video').play()">
                <div style="
                      width: 64px; height: 64px; border-radius: 50%;
                      background: rgba(0, 0, 0, 0.55); backdrop-filter: blur(6px);
                      display: flex; align-items: center; justify-content: center;
                      border: 2px solid rgba(212, 175, 55, 0.5);
                    ">
                  <svg width="24" height="28" viewBox="0 0 24 28" fill="none">
                    <path d="M3 1.5L22 14L3 26.5V1.5Z" fill="#d4af37" />
                  </svg>
                </div>
              </div>
            </div>
            <?php elseif (!empty($post['media_url'])): ?>
            <div class="journal-img-wrap">
              <img src="../<?= htmlspecialchars($post['media_url']) ?>" loading="lazy" alt="Visual Panggonan" class="journal-img" decoding="async" />
            </div>
            <?php endif; ?>
            <div class="journal-content">
              <?php if (!empty($post['date_label'])): ?>
              <div class="journal-date"><?= htmlspecialchars($post['date_label']) ?></div>
              <?php else: ?>
              <div class="journal-date">Oleh <?= htmlspecialchars($post['author']) ?></div>
              <?php endif; ?>

              <div class="journal-text" style="font-size: 0.88rem">
                <?= $post['text'] ?>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php if ($total_journals > 6): ?>
        <div class="load-more-container" style="text-align: center; margin-top: 40px;">
          <button id="btn-load-more" style="padding: 12px 32px; font-size: 1rem; border: 1px solid var(--primary-gold, #d4af37); background: transparent; color: var(--primary-gold, #d4af37); border-radius: 8px; cursor: pointer; transition: all 0.3s ease; font-family: 'Sora', sans-serif; font-weight: 600;">
            Muat Lebih Banyak
          </button>
        </div>
        <?php endif; ?>
      </div>
    </section>

    <!-- INTERACTIVE USER STORY FORM -->
    <section class="share-story">
      <div class="w-layout-blockcontainer container w-container">
        <div class="story-container">
          <div class="title-wrapper text-center" style="
                display: flex;
                flex-direction: column;
                align-items: center;
                margin-bottom: 40px;
              ">
            <div class="caption">[ TUANGKAN CERITAMU ]</div>
            <h2 style="font-size: 2.2rem; margin-top: 12px; margin-bottom: 16px">
              Bagikan Cerita Anda di Panggonan
            </h2>
            <p style="
                  color: #666;
                  font-size: 1.05rem;
                  line-height: 1.6;
                  max-width: 600px;
                  text-align: center;
                ">
              Apakah sebuah sudut di Panggonan mengembalikan memori lama? Atau
              semangkuk hidangan mengingatkan Anda pada seseorang? Tulis
              kutipan indah Anda, dan bagikan agar kenangan tersebut hidup
              bersama kami.
            </p>
          </div>

          <form id="panggonan-story-form" action="submit_story.php" method="POST" enctype="multipart/form-data" onsubmit="return handleStorySubmit(this)">
            <div style="
                  display: grid;
                  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                  gap: 20px;
                ">
              <div class="form-group">
                <label class="form-label">Nama Anda / Inisial</label>
                <input type="text" name="nama" required class="input-field" placeholder="Nama..." value="<?= htmlspecialchars($input_nama) ?>" />
              </div>
              <div class="form-group">
                <label class="form-label">Username Instagram (Opsional)</label>
                <input type="text" name="ig" class="input-field" placeholder="@username" value="<?= htmlspecialchars($input_ig) ?>" />
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">Tulis Cerita / Kutipan Puitis Anda di Panggonan</label>
              <textarea name="pesan" class="input-field text-area" rows="4"
                placeholder="Ketik cerita atau quotes bermakna Anda saat berada di Panggonan..."><?= htmlspecialchars($input_pesan) ?></textarea>
            </div>

            <div class="form-group">
              <label class="form-label">Momen di Foto (Opsional, tautan foto atau deskripsi)</label>
              <input type="file" name="foto_file" class="input-field" accept="image/webp,image/jpeg,image/png,video/mp4,video/webm" style="padding: 10px 16px; background: #fff;" />
              <div style="
                    font-size: 0.85rem;
                    color: #666;
                    margin-top: 8px;
                    line-height: 1.4;
                  ">
                💡 <strong>Keamanan Tinggi:</strong> File akan dipindai otomatis. Maksimal ukuran 2MB. Opsional: Anda bisa mengosongkan ini jika hanya ingin mengirim teks cerita.
              </div>
            </div>

            <button type="submit" class="btn-submit-story">
              Kirim Cerita Anda
            </button>
            <p style="
                  text-align: center;
                  color: #888;
                  font-size: 0.85rem;
                  margin-top: 16px;
                ">
              Dengan mengirimkan pesan, Anda setuju kutipan Anda mungkin
              dibagikan ulang oleh tim Instagram Panggonan (dengan *credit*
              kepada Anda).
            </p>
          </form>
        </div>
      </div>
    </section>
  </div>

  <!-- FOOTER -->
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
              <div style="color: #d4af37; font-weight: 600; margin-bottom: 12px; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 0.05em;">
                Kirim Email
              </div>
              <a href="mailto:panggonanresto@gmail.com" class="footer-link" style="display: flex; align-items: center; gap: 10px; font-size: 0.9rem; line-height: 1.6;">
                <img src="../assets/images/icons/mail.svg" width="16" alt="Email" style="opacity: 0.7" / loading="lazy" decoding="async">
                panggonanresto@gmail.com
              </a>
              <a href="mailto:panggonanciracas@gmail.com" class="footer-link" style="display: flex; align-items: center; gap: 10px; font-size: 0.9rem; line-height: 1.6;">
                <img src="../assets/images/icons/mail.svg" width="16" alt="Email" style="opacity: 0.7" / loading="lazy" decoding="async">
                panggonanciracas@gmail.com
              </a>
              <a href="mailto:panggonangdc@gmail.com" class="footer-link" style="display: flex; align-items: center; gap: 10px; font-size: 0.9rem; line-height: 1.6;">
                <img src="../assets/images/icons/mail.svg" width="16" alt="Email" style="opacity: 0.7" / loading="lazy" decoding="async">
                panggonangdc@gmail.com
              </a>
            </div>
            <div class="contact-card">
              <div style="color: #d4af37; font-weight: 600; margin-bottom: 12px; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 0.05em;">
                Telepon
              </div>
              <a href="tel:+6287828888538" class="footer-link" style="display: flex; align-items: center; gap: 10px; font-size: 0.9rem; line-height: 1.6;">
                <img src="../assets/images/icons/phone.svg" width="16" alt="Phone" style="opacity: 0.7" / loading="lazy" decoding="async">
                Ciracas: 0878-2888-8538
              </a>
              <a href="tel:+6287845359184" class="footer-link" style="display: flex; align-items: center; gap: 10px; font-size: 0.9rem; line-height: 1.6;">
                <img src="../assets/images/icons/phone.svg" width="16" alt="Phone" style="opacity: 0.7" / loading="lazy" decoding="async">
                GDC: 0878-4535-9184
              </a>
            </div>
            <div class="contact-card">
              <div style="color: #d4af37; font-weight: 600; margin-bottom: 12px; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 0.05em;">
                Instagram
              </div>
              <a href="https://www.instagram.com/panggonan.resto/?hl=id" target="_blank" class="footer-link" style="display: flex; align-items: center; gap: 10px; font-size: 0.9rem; line-height: 1.6;">
                <img src="../assets/images/icons/instagram-icon.svg" width="16" alt="IG" style="opacity: 0.7" / loading="lazy" decoding="async">
                panggonan.resto
              </a>
              <a href="https://www.instagram.com/panggonan_gdc/?hl=id" target="_blank" class="footer-link" style="display: flex; align-items: center; gap: 10px; font-size: 0.9rem; line-height: 1.6;">
                <img src="../assets/images/icons/instagram-icon.svg" width="16" alt="IG" style="opacity: 0.7" / loading="lazy" decoding="async">
                panggonan_gdc
              </a>
              <a href="https://www.instagram.com/panggonan_ciracas/?hl=id" target="_blank" class="footer-link" style="display: flex; align-items: center; gap: 10px; font-size: 0.9rem; line-height: 1.6;">
                <img src="../assets/images/icons/instagram-icon.svg" width="16" alt="IG" style="opacity: 0.7" / loading="lazy" decoding="async">
                panggonan_ciracas
              </a>
            </div>
            <div class="contact-card">
              <div style="color: #d4af37; font-weight: 600; margin-bottom: 12px; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 0.05em;">
                TikTok
              </div>
              <a href="https://www.tiktok.com/@panggonan.resto" target="_blank" class="footer-link" style="display: flex; align-items: center; gap: 10px; font-size: 0.9rem; line-height: 1.6;">
                <img src="../assets/images/icons/tiktok-icon.svg" width="16" alt="TikTok" style="opacity: 0.7" / loading="lazy" decoding="async">
                @panggonan.resto
              </a>
              <a href="https://www.tiktok.com/@panggonan_gdc" target="_blank" class="footer-link" style="display: flex; align-items: center; gap: 10px; font-size: 0.9rem; line-height: 1.6;">
                <img src="../assets/images/icons/tiktok-icon.svg" width="16" alt="TikTok" style="opacity: 0.7" / loading="lazy" decoding="async">
                @panggonan_gdc
              </a>
              <a href="https://www.tiktok.com/@panggonan.ciracas" target="_blank" class="footer-link" style="display: flex; align-items: center; gap: 10px; font-size: 0.9rem; line-height: 1.6;">
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
  <script src="../assets/js/panggonan-nav-fix.js" type="text/javascript"></script>
  <script src="../assets/js/tracker.js" defer></script>

  <?php if ($story_submitted): ?>
  <!-- SUCCESS MODAL -->
  <div class="success-overlay" id="success-modal">
    <div class="success-modal">
      <div class="success-icon">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="currentColor">
          <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
        </svg>
      </div>
      <h3>Cerita Terkirim! ✨</h3>
      <p>
        Terima kasih! Cerita manis Anda telah berhasil dikirimkan ke tim marketing kami melalui WhatsApp dan otomatis tersimpan dalam antrean persetujuan.
        Cerita Anda akan tampil secara publik di Jurnal setelah disetujui oleh tim manajemen.
      </p>
      <?php if (!empty($upload_error)): ?>
        <p style="color: #ff3333; font-weight: bold; background: rgba(255, 51, 51, 0.08); padding: 10px 14px; border-radius: 8px; border: 1px solid rgba(255, 51, 51, 0.15); font-size: 0.85rem; margin-top: 12px; text-align: left; line-height: 1.4;">
          ⚠️ <?= htmlspecialchars($upload_error) ?>
        </p>
      <?php endif; ?>
      <button class="btn-close-success" onclick="document.getElementById('success-modal').remove();">Tutup</button>
    </div>
  </div>
  <?php endif; ?>

  <?php if (!empty($upload_error) && !$story_submitted): ?>
  <!-- ERROR MODAL (SERVER SIDE DETECTED) -->
  <div class="error-overlay" id="error-modal">
    <div class="error-modal">
      <div class="error-icon">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
        </svg>
      </div>
      <h3 style="color: #ef4444; font-weight: 700; margin-bottom: 16px;">Gagal Mengirim Cerita ⚠️</h3>
      <p style="color: #555; font-size: 0.95rem; line-height: 1.6; margin-bottom: 24px;">
        <?= htmlspecialchars($upload_error) ?>
      </p>
      <p style="color: #666; font-size: 0.85rem; line-height: 1.5; margin-bottom: 32px; background: #fef2f2; padding: 12px; border-radius: 12px; border: 1px solid #fee2e2; text-align: left;">
        Formulir tulisan Anda telah kami pertahankan. Silakan kompres foto Anda hingga <strong>di bawah 2MB</strong> untuk mengirim ulang.
      </p>
      <button class="btn-close-error" onclick="document.getElementById('error-modal').remove();">Tutup</button>
    </div>
  </div>
  <?php endif; ?>

  <script>
    // Client-side Error Modal Popup Function
    function showJsErrorModal(errorMessage) {
      const existing = document.getElementById('error-modal');
      if (existing) existing.remove();

      const modalHtml = `
        <div class="error-overlay" id="error-modal">
          <div class="error-modal">
            <div class="error-icon">
              <svg width="36" height="36" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
              </svg>
            </div>
            <h3 style="color: #ef4444; font-weight: 700; margin-bottom: 16px;">Gagal Mengirim Cerita ⚠️</h3>
            <p style="color: #555; font-size: 0.95rem; line-height: 1.6; margin-bottom: 24px;">
              ${errorMessage}
            </p>
            <p style="color: #666; font-size: 0.85rem; line-height: 1.5; margin-bottom: 32px; background: #fef2f2; padding: 12px; border-radius: 12px; border: 1px solid #fee2e2; text-align: left;">
              Formulir tulisan Anda tetap kami pertahankan. Silakan kompres foto Anda hingga <strong>di bawah 2MB</strong> untuk mengirim ulang.
            </p>
            <button class="btn-close-error" onclick="document.getElementById('error-modal').remove();">Tutup</button>
          </div>
        </div>
      `;
      document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    function handleStorySubmit(form) {
      // 1. JS Client-side size & format validation
      const fileInput = form.querySelector('[name="foto_file"]');
      if (fileInput && fileInput.files.length > 0) {
        const file = fileInput.files[0];
        const maxSize = 2 * 1024 * 1024; // Strict 2MB Limit
        
        if (file.size > maxSize) {
          showJsErrorModal('Foto gagal diunggah karena ukurannya melebihi batas 2MB (coba kompres foto Anda terlebih dahulu).');
          return false; // Intercept: Do NOT redirect to WhatsApp, Do NOT submit form
        }

        const allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'video/mp4'];
        if (!allowedMimes.includes(file.type)) {
          showJsErrorModal('Format file tidak diizinkan. Hanya menerima JPG, PNG, WEBP, atau MP4.');
          return false; // Intercept
        }
      }

      // 2. Open WhatsApp (Only on success)
      const nama = form.querySelector('[name="nama"]').value;
      const ig = form.querySelector('[name="ig"]').value || "Tidak ada";
      const pesan = form.querySelector('[name="pesan"]').value;

      const targetNumber = "6287845359184";
      const waText = `Halo admin Panggonan! Saya ingin berbagi cerita & kutipan pengalaman saya di Panggonan:\n\n*Dari:* ${nama}\n*Instagram:* \n${ig}\n\n*Quotes / Cerita Manis:* \n"${pesan}"\n\nMohon dilihat ya, terima kasih sangat terkesan dengan Panggonan! 😊`;

      const encodedText = encodeURIComponent(waText);
      window.open(`https://wa.me/${targetNumber}?text=${encodedText}`, "_blank");
      
      return true; // Continue with form submission to submit_story.php
    }

    // Load More Logic
    const btnLoadMore = document.getElementById('btn-load-more');
    if (btnLoadMore) {
      let currentOffset = 6;
      const totalJournals = <?= (int)$total_journals ?>;
      const gridContainer = document.getElementById('journal-grid-container');

      btnLoadMore.addEventListener('click', function() {
        btnLoadMore.innerText = 'Memuat...';
        btnLoadMore.style.opacity = '0.7';
        btnLoadMore.style.pointerEvents = 'none';

        fetch(`load_more.php?offset=${currentOffset}`)
          .then(res => res.text())
          .then(html => {
            if (html.trim() !== '') {
              gridContainer.insertAdjacentHTML('beforeend', html);
              const loadedCount = (html.match(/class="journal-card"/g) || []).length;
              currentOffset += loadedCount;
            }
            
            if (currentOffset >= totalJournals) {
              btnLoadMore.style.display = 'none';
            } else {
              btnLoadMore.innerText = 'Muat Lebih Banyak';
              btnLoadMore.style.opacity = '1';
              btnLoadMore.style.pointerEvents = 'auto';
            }
          })
          .catch(err => {
            console.error(err);
            btnLoadMore.innerText = 'Gagal memuat. Coba lagi.';
            btnLoadMore.style.opacity = '1';
            btnLoadMore.style.pointerEvents = 'auto';
          });
      });
    }
  </script>

</body>

</html>
