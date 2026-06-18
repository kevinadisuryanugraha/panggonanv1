# Panggonan Resto — Agent Guide

## Project overview

PHP/MySQL database-driven restaurant website with an admin dashboard. Most public pages are `.php` (queries DB for menu, gallery, journals). A few are static `.html` (about-us, services, faq). No package.json, build step, or CI. Serve via any HTTP server with PHP 7+ and MySQL.

## Stack

- **Frontend**: HTML5, CSS3 (CSS vars, Flexbox, Grid), Vanilla JS, jQuery 3.5.1 (local copy)
- **Backend**: PHP 7+, MySQL (PDO), session-based auth
- **Font**: Sora (Google Fonts — the only external dependency)
- **PWA**: `sw.js` (service worker), `manifest.json`
- **Admin charts**: Chart.js (CDN), DataTables (CDN), Font Awesome (CDN)

## Pages — static vs dynamic

| Route | File | Type |
|---|---|---|
| `/` | `index.html` | static |
| `/about-us/` | `about-us/index.html` | static |
| `/services/` | `services/index.html` | static |
| `/faq/` | `faq/index.html` | static |
| `/menu/` | `menu/index.php` | **dynamic** — queries `menu_categories` + `menu_items` |
| `/contact-us/` | `contact-us/index.php` | **dynamic** — reservation form posts to `submit_reservation.php` |
| `/gallery/` | `gallery/index.php` | **dynamic** — queries `gallery_categories` + `gallery` |
| `/blog/` | `blog/index.php` | **dynamic** — queries `journals` |

All dynamic pages `require_once __DIR__ . '/../config/db.php'`.

## Database

### Setup order (no single migration exists)

1. Import `database.sql` — creates `panggonan_db`, `journals`, `reservations` tables + 3 seed journals
2. Run `admin/setup_traffic.php` (admin or CLI) — creates `visitor_logs`, `traffic_conversions` + 30 days seed data
3. Run `admin/setup_menu.php` (admin or CLI) — creates `menu_categories`, `menu_items` + full menu seed
4. Edit `config/db.php` with real credentials

**Missing from database.sql** — these tables must be created manually or via code:
- `admins` — used by admin login (`SELECT * FROM admins WHERE username = ?` with `password_hash` (bcrypt))
- `gallery_categories` — used by gallery/index.php + admin CRUD
- `gallery` — gallery photos, FK → `gallery_categories`
- `menu_categories` — created by `setup_menu.php`
- `menu_items` — created by `setup_menu.php`

### Table reference

| Table | Created by | Used in |
|---|---|---|
| `journals` | `database.sql` | blog/, admin |
| `reservations` | `database.sql` | contact-us/, admin |
| `admins` | **manual** | admin login |
| `visitor_logs` | `admin/setup_traffic.php` | admin traffic |
| `traffic_conversions` | `admin/setup_traffic.php` | admin traffic |
| `menu_categories` | `admin/setup_menu.php` | menu/, admin |
| `menu_items` | `admin/setup_menu.php` | menu/, admin |
| `gallery_categories` | **manual / code** | gallery/, admin |
| `gallery` | **manual / code** | gallery/, admin |

## Admin dashboard

- `admin/index.php` — login with credentials from `admins` table (session-based, 2h timeout)
- All admin PHP files use `require_once __DIR__ . '/../config/db.php'`
- Tabs: Dashboard, Reservations, Journals/Approvals, Menu (categories + items), Gallery, Traffic
- Traffic analytics: `tracker.js` (frontend) → `track_visitor.php` (backend); dashboard at `admin/index.php?tab=traffic`
- Admin files: `get_traffic_data.php`, `export_excel.php`, `print_report.php`, `reset_traffic.php`, `setup_traffic.php`, `setup_menu.php`

## WhatsApp reservations

- **Ciracas**: `0878-2888-8538`
- **GDC Depok**: `0878-4535-9184`
- WA link logic in `contact-us/index.php` (inline `<script>` at bottom, function builds `wa.me` URL)
- All pages use `tracker.js` to log WA clicks as conversions

## Styling conventions

- **Gold accent (frontend)**: `#c5a880` (`--primary-gold` in `assets/css/custom.css:18`)
- **Gold accent (admin)**: `#d4af37` (`--primary-gold` in `admin/index.php:731`)
- **Dark bg**: `#121622` (sidebar), `#0c0e17` (admin main), `#0f172a` (dashboard)
- **Cream bg (frontend)**: `#f9f6f0` (`--bg-cream`)
- CSS variables at `assets/css/style.css:2052` (Webflow defaults), `custom.css:17` (brand overrides), `dashboard.css:3`
- Layout follows Webflow-style BEM naming (`w-container`, `w-nav`, `w-dyn-list`)

## GA4 & SEO

- GA4 placeholder ID `G-XXXXXXXXXX` appears in all 9 pages — replace with real ID on deploy
- `sitemap.php` generates an XML sitemap dynamically (static 8 pages + dynamic blog posts from `journals` table)
- `sitemap.xml` is a static fallback (outdated `lastmod`)
- `robots.txt` disallows `/admin/`, `/401/`, `/404/`, `/coming-soon/`, `/chat_owner_hari_ini/`

## Key gotchas

- **No templating** — shared nav/footer are duplicated raw HTML in every page. Edit all pages to update.
- **`.htaccess`** has a localhost-specific path for the 404 error doc — replace before deployment.
- **Service worker** (`sw.js`) caches `CACHE_NAME = 'panggonan-pwa-static-v2'`. Bump version on deploy to bust cache. SW bypasses `/admin/` paths.
- **Images**: `.webp` preferred. Upload dirs are `assets/uploads/jurnal/` and `assets/uploads/gallery/` (auto-created by admin).
- **Admin login**: `admins` table is **not** in `database.sql`. You must create it manually (`id, username, password_hash, role`) and hash the password with `password_hash()`.
- **`database.sql`** only creates `journals` and `reservations` — the other 6 tables come from setup scripts or manual creation.
- All pages use `tracker.js` which auto-discovers the tracking endpoint URL relative to the `src` attribute. It also injects the PWA manifest + registers the service worker dynamically.
- **Deployment**: FTP via `.vscode/sftp.json` (remote: `/public_html`), or drag-drop to Netlify Drop / Vercel / GitHub Pages.
