# Production Readiness Checklist

Use this checklist before publishing `queopius/shield` to Packagist or using it in production apps.

## 1) Package portability

- [ ] No host-app namespaces in package code (`App\\*` not required by Shield internals).
- [ ] No project-specific URLs/emails in docs (use placeholders like `your-app.test`).
- [ ] Config defaults are safe and environment-aware.
- [ ] Service Provider uses `mergeConfigFrom`, publish tags, and route loading by feature flags.

## 2) Runtime compatibility matrix

- [ ] PHP versions tested: `8.2`, `8.3`, `8.4`.
- [ ] Laravel versions tested: `11.x`, `12.x`.
- [ ] HTTP server modes verified:
  - [ ] Direct app server
  - [ ] Reverse proxy (Nginx/ALB/Cloudflare)
- [ ] HTTPS detection validated with trusted proxies correctly configured in host app.

## 3) Security behavior matrix

- [ ] HSTS applied only on secure requests.
- [ ] CSP enforce mode emits `Content-Security-Policy`.
- [ ] CSP report-only mode emits `Content-Security-Policy-Report-Only`.
- [ ] Header exclusions by path and route name work.
- [ ] HTTPS redirect exclusions work.
- [ ] `301` and `308` redirect behavior validated.
- [ ] Security audit warnings shown for weak/conflicting configs.
- [ ] Dashboard requires auth middleware and optional ability gate.

## 4) CSP reports pipeline

- [ ] Endpoint enabled/disabled by config.
- [ ] Invalid payloads are tolerated without breaking requests.
- [ ] Parsed fields are persisted when DB storage is enabled.
- [ ] Pruning command removes old rows according to retention policy.

## 5) DX and operability

- [ ] `shield:install` outputs clear next steps.
- [ ] `shield:audit` supports table and JSON export.
- [ ] `shield:scan` works on default and custom paths.
- [ ] Views publish/override tested (`shield-views`).
- [ ] Config publish tested (`shield-config`).
- [ ] Migrations publish tested (`shield-migrations`).

## 6) Quality gates (must pass in CI)

- [ ] `composer validate --strict` (allow known non-blocking warnings only).
- [ ] `vendor/bin/pint --test`.
- [ ] `vendor/bin/phpunit`.
- [ ] `vendor/bin/phpstan analyse`.

Recommended CI environment requirements:

- `ext-pdo_sqlite` enabled (required by Testbench SQLite in-memory tests).
- `ext-json`, `ext-mbstring`, `ext-openssl`.

## 7) Known edge cases to test manually in host app

- [ ] App behind proxy but not trusted: verify warning appears and `isSecure()` behavior.
- [ ] Mixed HTTP/HTTPS traffic during rollout.
- [ ] CSP report floods: storage growth + prune schedule.
- [ ] UI access denied for non-authorized roles.
- [ ] Conflicting headers from upstream web server vs Shield middleware.

## 8) Release checklist

- [ ] Remove `"version"` from `composer.json` before Packagist release workflow.
- [ ] Tag release in VCS (e.g. `v1.0.0`).
- [ ] Update changelog/release notes.
- [ ] Verify README install commands from clean Laravel app.
- [ ] Verify docs build (`mkdocs build --strict`).

