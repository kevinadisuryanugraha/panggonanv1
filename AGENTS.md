# Panggonan Resto — Agent Guide

## Project overview

Static restaurant website (HTML5/CSS3/Vanilla JS) with a PHP/MySQL admin dashboard.
**No package.json, build step, or CI**. Serve via any HTTP server (Apache, Netlify, etc.).

## Stack

- **Frontend**: HTML5, CSS3 (CSS vars, Flexbox, Grid), Vanilla JS, jQuery 3.5.1 (local copy)
- **Backend** (admin only): PHP 7+, MySQL (PDO), session-based auth
- **Font**: Sora (Google Fonts — the only external dependency)
- **PWA**: `sw.js` (service worker), `manifest.json`

## Directory structure

```
conc-panggonanv1/
├── index.html              # Homepage
├── about-us/               # "Tentang Kami" philosophy page
├── menu/                   # Menu catalog
├── contact-us/             # WhatsApp reservation form (PHP)
├── blog/                   # Jurnal entries (PHP)
├── services/               # Service categories
├── gallery/                # Photo gallery
├── faq/                    # FAQ page
├── admin/                  # PHP admin dashboard (protected)
├── assets/
│   ├── css/                # style.css, custom.css, home.css, menu.css, about.css, dashboard.css
│   ├── js/                 # script.js (bundled Panggonan lib), tracker.js, panggonan-nav-fix.js
│   └── images/             # .webp preferred; compress .jpg/.png before adding
├── config/db.php           # Database connection (PDO)
├── database.sql            # Schema + seed data
├── .vscode/sftp.json       # FTP deployment config
```

## Database setup

1. Create MySQL database (default: `panggonan_db`)
2. Import `database.sql` — creates `journals`, `reservations` tables
3. Run `admin/setup_traffic.php` — creates `visitor_logs` + 30 days seed data
4. Run `admin/setup_menu.php` — creates `menu_categories`, `menu_items` + seed
5. Edit `config/db.php` with real credentials

All setup scripts require admin session or CLI execution.

## Admin dashboard

- `admin/index.php` — login with credentials from `admins` table (session-based)
- Traffic tracking: `tracker.js` (frontend) → `track_visitor.php` (backend)
- Key admin pages: traffic reports, reservation management, journal CRUD, menu management

## WhatsApp reservations

- **Ciracas**: `0878-2888-8538`
- **GDC Depok**: `0878-4535-9184`
- WA link logic is in `contact-us/index.html` (inline `<script>` at bottom)

## Styling conventions

- **Gold**: `#d4af37` (primary accent)
- **Dark bg**: `#0e0d09`, `#121622`
- CSS variables defined in `assets/css/style.css` lines ~1–50
- Layout follows Webflow-style BEM naming (`w-container`, `w-nav`, `w-dyn-list`)

## Deployment

- FTP via `.vscode/sftp.json` (remote: `/public_html`)
- Or drag-drop entire folder to Netlify Drop / Vercel / GitHub Pages

## Key gotchas

- No modern framework — all pages are standalone HTML. Shared nav/footer are duplicated, not templated.
- Admin PHP pages use `require_once __DIR__ . '/../config/db.php'` — path matters.
- Service worker (`sw.js`) caches assets at install time. Update `CACHE_NAME` on deploy to bust cache.
- Images must be `.webp` for performance; JPEG/PNG should be compressed first.
- GA4 tag in `index.html` uses placeholder `G-XXXXXXXXXX` — replace with real ID.
- `robots.txt` disallows `/admin/`, `/401/`, `/404/`, `/coming-soon/`.
- `sitemap.xml` lists 8 URLs; update `lastmod` on content changes.
