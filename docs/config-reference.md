# Config Reference

Main file: `config/sentinel.php`

## Top-level keys

- `enabled`: master toggle for Sentinel behavior.
- `environments`: allowed environments where Sentinel is active.
- `exclude.paths` / `exclude.route_names`: global exclusions.
- `preset`: baseline profile (`web_compatible`, `api_strict`, etc).
- `strict_validation`: stricter validation for malformed CSP config.

## HTTPS

- `https.redirect`: redirect HTTP to HTTPS.
- `https.redirect_status`: `301` or `308`.
- `https.force_scheme`: force `https` in URL generator.
- `https.exclude_paths`, `https.exclude_route_names`: bypass redirects for specific routes.
- `https.only_in_environments`: environments where redirect applies.
- `https.trust_proxy_warning_enabled`: show warning when proxy trust looks wrong.

## Headers

- `headers.hsts.*`
- `headers.csp.*`
- `headers.x_content_type_options.*`
- `headers.referrer_policy.*`
- `headers.x_frame_options.*`
- `headers.permissions_policy.*`
- `headers.cross_origin.*`
- `headers.custom`

### CSP key points

- `headers.csp.enabled`
- `headers.csp.report_only`
- `headers.csp.report_uri`
- `headers.csp.report_to`
- `headers.csp.directives` (array-based builder)
- `headers.csp.nonce.*` (prepared architecture)

## UI Dashboard

- `ui.enabled`
- `ui.path`
- `ui.middleware` (recommended: `['web','auth']`)
- `ui.require_ability` (recommended for role/permission gate)
- `ui.theme` (`light|dark|auto`)
- `ui.show_csp_reports`
- `ui.endpoint_scan.*`

## CSP Reports

- `csp_reports.enabled`
- `csp_reports.route_path`
- `csp_reports.store_database`
- `csp_reports.prune_days`
- `csp_reports.middleware`
- `csp_reports.log_invalid_payloads`

## Audit

- `audit.enabled`
- `audit.perform_live_probe`
- `audit.internal_probe_path`
- `audit.warnings.allow_unsafe_inline_warning`
- `audit.warnings.require_frame_ancestors_warning`

## Health endpoint

- `health_endpoint.enabled`
- `health_endpoint.path`
- `health_endpoint.middleware`

## Views

- `views.publishable`
- `views.namespace`

## Presets

Implemented baseline presets:

- `web_compatible`
- `api_strict`

Preset works as baseline and manual config overrides it.
