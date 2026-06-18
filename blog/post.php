<?php
require_once __DIR__ . '/../config/db.php';

$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM journals WHERE id = ? AND status = 'approved'");
$stmt->execute([$postId]);
$post = $stmt->fetch();

if (!$post) {
    header('Location: ../404/');
    exit;
}

$pageTitle = htmlspecialchars($post['quote']);
$pageDesc = htmlspecialchars(substr(strip_tags($post['text']), 0, 160)) . '...';
$mediaUrl = !empty($post['media_url']) ? 'https://panggonanresto.com/' . htmlspecialchars($post['media_url']) : 'https://panggonanresto.com/assets/images/panggonan_aset_ke_2/fotbar_malam.webp';
$postDate = !empty($post['created_at']) ? substr($post['created_at'], 0, 10) : date('Y-m-d');
?>
<!doctype html>
<html data-domain="panggonanresto.com" lang="id">
<head>
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
  <script>window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','G-XXXXXXXXXX');</script>
  <meta charset="utf-8" />
  <title><?= $pageTitle ?> — Jurnal Panggonan Resto</title>
  <meta content="<?= $pageDesc ?>" name="description" />
  <meta content="<?= $pageTitle ?> — Jurnal Panggonan Resto" property="og:title" />
  <meta content="<?= $pageDesc ?>" property="og:description" />
  <meta content="<?= $pageTitle ?> — Jurnal Panggonan Resto" property="twitter:title" />
  <meta content="<?= $pageDesc ?>" property="twitter:description" />
  <meta property="og:type" content="article" />
  <meta content="summary_large_image" name="twitter:card" />
  <meta content="width=device-width, initial-scale=1" name="viewport" />
  <meta name="robots" content="index, follow" />
  <meta name="theme-color" content="#1a1a1a" />
  <link rel="canonical" href="https://panggonanresto.com/blog/<?= $postId ?>/" />
  <meta property="og:image" content="<?= $mediaUrl ?>" />
  <meta property="og:image:alt" content="<?= $pageTitle ?>" />
  <meta property="og:url" content="https://panggonanresto.com/blog/<?= $postId ?>/" />
  <meta property="og:locale" content="id_ID" />
  <meta property="og:site_name" content="Panggonan Resto" />
  <meta property="article:published_time" content="<?= $postDate ?>" />
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@graph": [
      {
        "@type": "BreadcrumbList",
        "itemListElement": [
          { "@type": "ListItem", "position": 1, "name": "Beranda", "item": "https://panggonanresto.com/" },
          { "@type": "ListItem", "position": 2, "name": "Jurnal", "item": "https://panggonanresto.com/blog/" },
          { "@type": "ListItem", "position": 3, "name": "<?= str_replace('"', '\"', $post['quote']) ?>", "item": "https://panggonanresto.com/blog/<?= $postId ?>/" }
        ]
      },
      {
        "@type": "BlogPosting",
        "headline": "<?= str_replace('"', '\"', $post['quote']) ?>",
        "description": "<?= str_replace('"', '\"', $pageDesc) ?>",
        "author": { "@type": "Person", "name": "<?= htmlspecialchars($post['author']) ?>" },
        "datePublished": "<?= $postDate ?>",
        "publisher": { "@type": "Organization", "name": "Panggonan Resto", "logo": { "@type": "ImageObject", "url": "https://panggonanresto.com/assets/images/logo.webp" } },
        "mainEntityOfPage": { "@type": "WebPage", "@id": "https://panggonanresto.com/blog/<?= $postId ?>/" }
      }
    ]
  }
  </script>
  <link href="../assets/css/style.css" rel="stylesheet" type="text/css" />
  <link href="../assets/css/custom.css" rel="stylesheet" type="text/css" />
  <link href="https://fonts.googleapis.com" rel="preconnect" />
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin="anonymous" />
  <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js" type="text/javascript"></script>
  <script type="text/javascript">WebFont.load({google:{families:["Sora:regular"]}});</script>
  <script type="text/javascript">!(function(o,c){var n=c.documentElement,t=" w-mod-";n.className+=t+"js",("ontouchstart"in o||o.DocumentTouch&&c instanceof DocumentTouch)&&(n.className+=t+"touch")})(window,document);</script>
  <link href="../assets/images/logo.webp" rel="shortcut icon" type="image/x-icon" />
  <link href="../assets/images/logo.webp" rel="apple-touch-icon" />
  <style>
    .post-hero{padding:48px 0 24px 0;text-align:center}.post-hero .caption{margin-bottom:2rem}.post-title{font-size:2rem;line-height:1.2;color:var(--black);margin:0 0 16px 0}.post-meta{font-size:0.85rem;color:#888;margin-bottom:32px}.post-media{width:100%;max-width:800px;margin:0 auto 40px auto;border-radius:16px;overflow:hidden}.post-media img,.post-media video{width:100%;height:auto;display:block}.post-body{max-width:720px;margin:0 auto;font-size:1.05rem;line-height:1.8;color:#444}.post-body p{margin-bottom:16px}.post-back{text-align:center;padding:48px 0}.post-back a{display:inline-flex;align-items:center;gap:8px;color:#d4af37;text-decoration:none;font-weight:600;transition:gap 0.3s ease}.post-back a:hover{gap:12px}@media(max-width:767px){.post-title{font-size:1.5rem}}
  </style>
</head>
<body>
  <div data-w-id="c4cece30-ea7b-9bee-6ab3-e3f6d3fb5623" data-animation="default" data-collapse="medium"
    data-duration="400" data-easing="ease" data-easing2="ease" role="banner" class="navbar w-nav">
    <div class="container w-container">
      <div class="nav-wrapper">
        <a href="../" class="brand w-nav-brand"><h3 style="margin:0;color:#d4af37">Panggonan</h3></a>
        <nav role="navigation" class="nav-menu w-nav-menu">
          <div class="menu-wraper">
            <div data-w-id="c9554779-7a98-ebc3-6d3f-25bc12cf16bb" class="nav-outer">
              <a href="../" class="nav-link w-nav-link">Beranda</a><div class="nav-line"></div>
            </div>
            <div data-w-id="7a84d105-ac7b-73ee-9db2-22ef823bf66d" class="nav-outer">
              <a href="../about-us/" class="nav-link w-nav-link">Tentang Kami</a><div class="nav-line"></div>
            </div>
            <div data-w-id="0d59ca41-bfc7-676d-c79e-5915ca4d4009" class="nav-outer">
              <a href="../menu/" class="nav-link w-nav-link">Menu</a><div class="nav-line"></div>
            </div>
            <div data-w-id="2eedfb42-47ec-746e-339b-083a716ae9a9" class="nav-outer">
              <div data-hover="false" data-delay="0" data-w-id="bd1b9d5e-da99-33c1-ebf2-30f0a4ff637d" class="dropdown w-dropdown">
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
              <a href="../blog/" class="nav-link w-nav-link">Jurnal</a><div class="nav-line"></div>
            </div>
            <div class="tablet-button">
              <a data-w-id="482fbd4e-f018-7011-d00d-554d28c22612" href="../contact-us/" class="primary-button w-inline-block">
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
            <a data-w-id="482fbd4e-f018-7011-d00d-554d28c22612" href="../contact-us/" class="primary-button w-inline-block">
              <div>Hubungi Kami</div>
              <img src="../assets/images/icons/btn-icon.svg" loading="lazy" alt="Arrow Icon" class="primary-button-image" decoding="async" />
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
        <div class="footer-middle">
          <a href="" class="footer-logo w-inline-block w--current">
            <h3 style="margin:0;color:#d4af37">Panggonan</h3>
          </a>
          <div class="footer-contacts">
            <div class="contact-card">
              <div style="color:#d4af37;font-weight:600;margin-bottom:12px;font-size:1.1rem;text-transform:uppercase;letter-spacing:0.05em">Kirim Email</div>
              <a href="mailto:panggonanresto@gmail.com" class="footer-link" style="display:flex;align-items:center;gap:10px;font-size:0.9rem;line-height:1.6"><img src="../assets/images/icons/mail.svg" width="16" alt="Email" style="opacity:0.7" loading="lazy" decoding="async"> panggonanresto@gmail.com</a>
              <a href="mailto:panggonanciracas@gmail.com" class="footer-link" style="display:flex;align-items:center;gap:10px;font-size:0.9rem;line-height:1.6"><img src="../assets/images/icons/mail.svg" width="16" alt="Email" style="opacity:0.7" loading="lazy" decoding="async"> panggonanciracas@gmail.com</a>
              <a href="mailto:panggonangdc@gmail.com" class="footer-link" style="display:flex;align-items:center;gap:10px;font-size:0.9rem;line-height:1.6"><img src="../assets/images/icons/mail.svg" width="16" alt="Email" style="opacity:0.7" loading="lazy" decoding="async"> panggonangdc@gmail.com</a>
            </div>
            <div class="contact-card">
              <div style="color:#d4af37;font-weight:600;margin-bottom:12px;font-size:1.1rem;text-transform:uppercase;letter-spacing:0.05em">Telepon</div>
              <a href="tel:+6287828888538" class="footer-link" style="display:flex;align-items:center;gap:10px;font-size:0.9rem;line-height:1.6"><img src="../assets/images/icons/phone.svg" width="16" alt="Phone" style="opacity:0.7" loading="lazy" decoding="async"> Ciracas: 0878-2888-8538</a>
              <a href="tel:+6287845359184" class="footer-link" style="display:flex;align-items:center;gap:10px;font-size:0.9rem;line-height:1.6"><img src="../assets/images/icons/phone.svg" width="16" alt="Phone" style="opacity:0.7" loading="lazy" decoding="async"> GDC: 0878-4535-9184</a>
            </div>
            <div class="contact-card">
              <div style="color:#d4af37;font-weight:600;margin-bottom:12px;font-size:1.1rem;text-transform:uppercase;letter-spacing:0.05em">Instagram</div>
              <a href="https://www.instagram.com/panggonan.resto/?hl=id" target="_blank" class="footer-link" style="display:flex;align-items:center;gap:10px;font-size:0.9rem;line-height:1.6"><img src="../assets/images/icons/instagram-icon.svg" width="16" alt="IG" style="opacity:0.7" loading="lazy" decoding="async"> panggonan.resto</a>
              <a href="https://www.instagram.com/panggonan_gdc/?hl=id" target="_blank" class="footer-link" style="display:flex;align-items:center;gap:10px;font-size:0.9rem;line-height:1.6"><img src="../assets/images/icons/instagram-icon.svg" width="16" alt="IG" style="opacity:0.7" loading="lazy" decoding="async"> panggonan_gdc</a>
              <a href="https://www.instagram.com/panggonan_ciracas/?hl=id" target="_blank" class="footer-link" style="display:flex;align-items:center;gap:10px;font-size:0.9rem;line-height:1.6"><img src="../assets/images/icons/instagram-icon.svg" width="16" alt="IG" style="opacity:0.7" loading="lazy" decoding="async"> panggonan_ciracas</a>
            </div>
            <div class="contact-card">
              <div style="color:#d4af37;font-weight:600;margin-bottom:12px;font-size:1.1rem;text-transform:uppercase;letter-spacing:0.05em">TikTok</div>
              <a href="https://www.tiktok.com/@panggonan.resto" target="_blank" class="footer-link" style="display:flex;align-items:center;gap:10px;font-size:0.9rem;line-height:1.6"><img src="../assets/images/icons/tiktok-icon.svg" width="16" alt="TikTok" style="opacity:0.7" loading="lazy" decoding="async"> @panggonan.resto</a>
              <a href="https://www.tiktok.com/@panggonan_gdc" target="_blank" class="footer-link" style="display:flex;align-items:center;gap:10px;font-size:0.9rem;line-height:1.6"><img src="../assets/images/icons/tiktok-icon.svg" width="16" alt="TikTok" style="opacity:0.7" loading="lazy" decoding="async"> @panggonan_gdc</a>
              <a href="https://www.tiktok.com/@panggonan.ciracas" target="_blank" class="footer-link" style="display:flex;align-items:center;gap:10px;font-size:0.9rem;line-height:1.6"><img src="../assets/images/icons/tiktok-icon.svg" width="16" alt="TikTok" style="opacity:0.7" loading="lazy" decoding="async"> @panggonan.ciracas</a>
            </div>
          </div>
        </div>
        <div class="footer-bottom">
          <div class="body-small" style="color:rgba(255,255,255,0.4)">&copy; 2026 Panggonan. Seluruh hak cipta dilindungi.</div>
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
