# Queopius Sentinel — HTTP Security & HTTPS Hardening for Laravel

<p align="center">
  <img src="https://raw.githubusercontent.com/queopius/sentinel/main/.github/assets/logo-queopius-sentinel.png" alt="Queopius Sentinel logo" width="660" style="max-width: 100%; height: auto;">
</p>

<p align="center">
  <a href="https://github.com/queopius/sentinel/actions/workflows/ci.yml"><img src="https://img.shields.io/github/actions/workflow/status/queopius/sentinel/ci.yml?branch=main&label=ci" alt="CI"></a>
  <a href="https://github.com/queopius/sentinel/actions/workflows/docs.yml"><img src="https://img.shields.io/github/actions/workflow/status/queopius/sentinel/docs.yml?branch=main&label=docs" alt="Docs Build"></a>
  <a href="https://packagist.org/packages/queopius/sentinel"><img src="https://img.shields.io/packagist/v/queopius/sentinel" alt="Latest Version"></a>
  <a href="https://packagist.org/packages/queopius/sentinel"><img src="https://img.shields.io/packagist/dt/queopius/sentinel" alt="Total Downloads"></a>
  <a href="https://queopius-sentinel.readthedocs.io/"><img src="https://readthedocs.org/projects/queopius-sentinel/badge/?version=latest" alt="Documentation Status"></a>
  <a href="LICENSE"><img src="https://img.shields.io/badge/license-MIT-green" alt="License"></a>
  <a href="https://creativecommons.org/licenses/by/4.0/"><img src="https://img.shields.io/badge/docs%20license-CC%20BY%204.0-lightgrey" alt="Docs License"></a>
</p>

Queopius Sentinel is a production-ready Laravel package for HTTP security hardening with great DX:

- Security headers (HSTS, CSP, Referrer-Policy, and more)
- HTTPS enforcement middleware
- Optional dashboard UI for audit/inspection
- Dashboard metrics with CSP-safe native charts and hardening plan
- Optional CSP reports endpoint + storage
- Security audit, endpoint scan, and report pruning commands
- Publishable views for full UI customization

## Why Queopius Sentinel

- Safe-by-default with preset support
- Progressive rollout path (CSP report-only first)
- Works as reusable package and monorepo local package
- Built for Laravel 12 and compatible with 11/12

## Versioning and Laravel compatibility

Queopius Sentinel follows **SemVer** for package versions.

- `MAJOR`: breaking changes
- `MINOR`: new features, backward compatible
- `PATCH`: fixes and internal improvements

### Compatibility matrix

| Sentinel version | Laravel | PHP | Status |
|---|---|---|---|
| `1.x` | `11.x`, `12.x` | `>=8.2` | Active |

Composer constraints (current):

- `illuminate/*`: `^11.0|^12.0`
- `php`: `^8.2`

### Support policy

- Only actively maintained major versions receive fixes/features.
- Security fixes are prioritized for the latest maintained major.
- When a Laravel major reaches end-of-life, support can be dropped in the next Sentinel major.

### Upgrade guidance

- Use a stable constraint in host apps: `composer require queopius/sentinel:^1.0`
- Read release notes before any major upgrade (`1.x` -> `2.x`).
- Run: `php artisan sentinel:audit` after upgrades to validate effective runtime security.

## Quick start in 5 minutes

1. Install package:

```bash
composer require queopius/sentinel
```

2. Run installer:

```bash
php artisan sentinel:install --with-views
```

3. Migrate (for CSP reports table):

```bash
php artisan migrate
```

4. Add middleware aliases/global as needed (see below).

5. Run audit:

```bash
php artisan sentinel:audit
```

## Installation and publish

```bash
php artisan vendor:publish --tag=sentinel-config
php artisan vendor:publish --tag=sentinel-views
php artisan vendor:publish --tag=sentinel-migrations
```

## Middleware registration (Laravel 11/12)

Add aliases/global middleware in `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'sentinel.headers' => \Queopius\Sentinel\Http\Middleware\AddSecurityHeaders::class,
        'sentinel.https' => \Queopius\Sentinel\Http\Middleware\EnforceHttps::class,
    ]);

    // Optional global
    $middleware->append(\Queopius\Sentinel\Http\Middleware\EnforceHttps::class);
    $middleware->append(\Queopius\Sentinel\Http\Middleware\AddSecurityHeaders::class);
})
```

## Config basics

Config file: `config/sentinel.php`

Key areas:

- `preset`: baseline config (`web_compatible`, `api_strict`)
- `headers.*`: security headers setup
- `https.*`: redirect + force scheme
- `ui.*`: optional dashboard
- `csp_reports.*`: endpoint + DB storage
- `audit.*`: warnings and probe behavior
- `health_endpoint.*`: optional JSON endpoint

## Dashboard UI

Enable in config:

```php
'ui' => [
  'enabled' => true,
  'path' => 'sentinel',
  'middleware' => ['web', 'auth'],
  'require_ability' => 'viewSentinelDashboard',
  'theme' => 'light', // light|dark|auto
]
```

Then open `/sentinel`.

### Dashboard access control (recommended)

- Keep `ui.middleware` with `auth` (default in package).
- Set `ui.require_ability` and define the Gate in your app:

```php
Gate::define('viewSentinelDashboard', fn ($user) => $user->hasRole('super_admin'));
```

With Spatie Permission you can map it to a permission:

```php
Gate::define('viewSentinelDashboard', fn ($user) => $user->can('sentinel.view'));
```

Dashboard endpoint scan extras:

- Dynamic paths filter via `scan_paths` query/form
- Export scan results:
  - `/sentinel?export=endpoints&format=json`
  - `/sentinel?export=endpoints&format=csv`

## CSP reports

Enable:

```php
'csp_reports' => [
  'enabled' => true,
  'route_path' => 'sentinel/csp-reports',
  'store_database' => true,
]
```

Use report-only initially, inspect reports, then enforce.

## Commands

- `php artisan sentinel:install [--with-views] [--force]`
- `php artisan sentinel:audit [--format=table|json|csv]`
- `php artisan sentinel:scan [--json] [--paths=/,/login,/api]`
- `php artisan sentinel:prune-reports [--days=30]`

## Recommended rollout path (safe adoption)

1. Start with preset `web_compatible`
2. Keep CSP in `report_only`
3. Observe dashboard + reports
4. Tighten CSP directives and remove `unsafe-inline`
5. Enable HTTPS redirect and HSTS in production

## Reverse proxy notes

If app is behind Cloudflare / ALB / Nginx proxy, ensure Laravel trusted proxies are correctly configured so `Request::isSecure()` is reliable.

## Local HTTPS test (production-like)

For monorepo host apps:

```bash
./scripts/generate-local-https-cert.sh
./vendor/bin/sail up -d --build
```

Set in host `.env`:

```dotenv
APP_URL=https://your-app.test:8443
```

Then run:

```bash
./vendor/bin/sail artisan optimize:clear
```

Open:

- `https://your-app.test:8443`
- `https://your-app.test:8443/sentinel`

Full trust instructions are in `docs/guides/local-https.md`.

## Publishable views

Views namespace: `sentinel`.

You can override UI templates by publishing views:

```bash
php artisan vendor:publish --tag=sentinel-views
```

Output path: `resources/views/vendor/sentinel`

## Local development in a Laravel app (monorepo)

Host app `composer.json`:

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "packages/queopius/sentinel",
      "options": {
        "symlink": true
      }
    }
  ],
  "require": {
    "queopius/sentinel": "^1.0"
  }
}
```

Then:

```bash
composer require queopius/sentinel:^1.0
php artisan sentinel:install --with-views
php artisan migrate
php artisan sentinel:audit
php artisan sentinel:scan
```

## Package tests

Inside package directory:

```bash
composer install
vendor/bin/phpunit
vendor/bin/pint --test
vendor/bin/phpstan analyse
```

## Docs

See `docs/` for architecture, config reference, CSP reporting, dashboard and roadmap.

Release-hardening checklist: `docs/production-readiness.md`.

## Community and governance

- Contribution guide: `CONTRIBUTING.md`
- Security policy: `SECURITY.md`
- Release + Packagist automation: `docs/guides/release-and-packagist.md`

## Licensing

- Code: **MIT** (see `LICENSE`).
- Documentation and guides: **Creative Commons Attribution 4.0 International (CC BY 4.0)**.

### Read the Docs

This package includes:

- `.readthedocs.yaml`
- `mkdocs.yml`
- `docs/requirements.txt`

Local docs preview:

```bash
cd packages/queopius/sentinel
python3 -m venv .venv
source .venv/bin/activate
pip install -r docs/requirements.txt
mkdocs serve
```

Local strict build:

```bash
mkdocs build --strict
```

GitHub Actions docs workflow:

- validates docs on PR/push via `mkdocs build --strict`
- optional Read the Docs trigger on push to `main`

Required repository secrets for RTD trigger:

- `RTD_TOKEN`: Read the Docs API token
- `RTD_PROJECT`: Read the Docs project slug (example: `queopius-sentinel`)

### Branding and badges notes

- Logo placeholder path in this README:
  - `.github/assets/logo-queopius-sentinel.png`
- If repository owner/name changes, update badge URLs accordingly.
- If Read the Docs project slug changes, update:
  - `https://readthedocs.org/projects/<slug>/badge/?version=latest`
