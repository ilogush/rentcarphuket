# CLAUDE.md

Guidance for Claude Code when working in this repository.

## Project Overview

**Rent Car Phuket / MonkeyCar** — PHP 8+ car rental site (Пхукет, Таиланд) with admin panel. No database — all data is stored as PHP arrays in `includes/data_*.php` files (file-based storage), read/written through Repositories.

- Production domain: **monkeycar.ru**
- Currency: ฿ (THB)
- Language: UI/content Russian, code/comments English

## Quick Start

```bash
composer install
composer serve   # local server on http://localhost:8001
```

Entry chain: `index.php` → `public/index.php` → `router.php`.

## Architecture (strict layering)

| Layer | Location | Responsibility |
|---|---|---|
| Router | `router.php` | Route dispatch |
| Pages | `pages/`, `admin/` | Compose components, minimal logic |
| Presenters | `app/Presenters/` | Prepare view data for pages |
| Services | `app/Services/` | Business logic (pricing, etc.) |
| Repositories | `app/Repositories/` | Data access — only layer that touches `data_*.php` |
| Components | `includes/components.php`, `includes/components/` | Reusable HTML blocks |
| Icons | `includes/icons.php` | SVG via `get_icon($name, $class)` |
| Data store | `includes/data_*.php` | PHP arrays written via `var_export` — NOT a database |
| Layout | `includes/layout.php`, `header.php`, `footer.php` | Client page shell |
| Admin | `admin/sidebar.php`, `admin/auth.php`, `admin/actions/`, `admin/partials/` | Admin shell & POST handlers |
| Config | `.env`, `config/settings.php`, `includes/config.php` | Settings & secrets |

**Do not break layering.** Business logic belongs in Services/Repositories, never in pages or components.

## Mandatory Rules

### Code style
- New PHP files: `declare(strict_types=1);` at top.
- Variables, functions, comments — English. UI text — Russian.
- No `die()`/`echo`/`exit` in pages unless necessary.
- No hardcoded secrets — use `.env`.

### Data
- Pages access data **only** through Repositories.
- Need new data? Create the Repository first.
- `data_*.php` files are rewritten via `var_export` — never hand-edit in code paths that should go through a Repo.

### UI
- Reuse components from `includes/components.php`. If one doesn't exist, create it before the page.
- Duplicated markup = missing component.
- User messages via the toast component — not `alert()`, not inline banners.
- Tailwind via `assets/css/tailwind.min.css` + `monkey-theme.css` (dark "mc-page" theme).
- Dates: flatpickr with Russian locale, `dateFormat: 'd.m.Y'`, `appendTo: document.body`.

### Admin
- CSRF token required on every POST form (already wired).
- Admin auth in `admin/auth.php`.

### Routes
- All routes go through `router.php`. Don't create ad-hoc entry files.

## Common Tasks

- **Add a page**: create `pages/foo.php` → add route in `router.php` → use existing components.
- **Add data**: create/extend Repository in `app/Repositories/` → back it with a `data_*.php` file.
- **Add an icon**: register in `includes/icons.php`, then use `get_icon('name', 'w-4 h-4')`.
- **Modify pricing**: logic lives in `app/Services/PricingService.php` — see `docs/PRICING_CALCULATOR.md`.
- **Deploy**: FTP upload per `.agent/DEPLOYMENT.md`. Credentials in `.env` (`FTP_*`).

## Key Files

- [router.php](router.php) — routing table
- [includes/components.php](includes/components.php) — all shared components + home/search JS
- [includes/layout.php](includes/layout.php) — page shell, asset loading
- [assets/css/monkey-theme.css](assets/css/monkey-theme.css) — dark theme
- [app/Services/PricingService.php](app/Services/PricingService.php) — pricing rules
- [app/Presenters/](app/Presenters/) — page data preparation

## Reference Docs


When `.agent/RULES.md` conflicts with anything here, **`.agent/RULES.md` wins**.

## Don'ts

- Don't commit `.env` or any secret.
- Don't bypass Repositories to read/write `data_*.php` directly from pages.
- Don't add a database — the project is intentionally file-based.
- Don't add `alert()` for user feedback — use toast.
- Don't skip `declare(strict_types=1)` on new PHP files.
- Don't add `altInput: true` to flatpickr here — it creates a duplicate input with the current markup.
