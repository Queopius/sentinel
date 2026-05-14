# Queopius Sentinel

<p align="center">
  <img src="https://raw.githubusercontent.com/queopius/sentinel/main/.github/assets/logo-queopius-sentinel.png" alt="Queopius Sentinel logo" width="420" style="max-width: 100%; height: auto;">
</p>

HTTP Security & HTTPS Hardening for Laravel.

Queopius Sentinel helps teams apply production-grade HTTP security with:

- security headers middleware (HSTS, CSP, Referrer-Policy, and more)
- HTTPS enforcement middleware
- optional dashboard for audit and operations
- CSP reports endpoint + storage
- audit/scan/prune Artisan commands
- publishable views for customization

## Why use Sentinel

- Safe rollout path: start with report-only CSP, then enforce.
- Works in monorepo local development and reusable package mode.
- Clear DX: install command, config presets, dashboard visibility.
- Built for Laravel 12 and compatible with Laravel 11/12 workflows.

## Read this first

1. [Quickstart](guides/quickstart.md)
2. [Installation](guides/installation.md)
3. [Configuration](config-reference.md)
4. [Access Control](guides/access-control.md)

## Core commands

```bash
php artisan sentinel:install --with-views
php artisan sentinel:audit
php artisan sentinel:scan
php artisan sentinel:prune-reports
```

## Recommended production rollout

1. Enable preset `web_compatible`.
2. Keep CSP in `report_only` mode.
3. Review dashboard and CSP reports.
4. Tighten directives and remove unsafe sources.
5. Enable enforce CSP and strict HTTPS/HSTS policy.
