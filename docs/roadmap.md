# Roadmap

## v1.0 (core)

- Security headers middleware
- HTTPS enforcement middleware
- CSP/HSTS builders
- Dashboard UI + publishable views
- CSP reports endpoint + DB storage
- Commands: install, audit, scan, prune
- Presets baseline

## v1.1

- Improved endpoint scanner profiles
- CSP learning mode richer recommendations
- Audit export improvements and external integrations
- Automated CSP report pruning scheduler helper

## v1.2+

- Full nonce manager integration for Blade helpers
- CSP hash tooling
- Presets packs (web/api/admin)
- Audit export bundles (JSON/CSV artifacts)
- Integrations with SIEM/observability tools

## Prepared stubs

- `NonceManager` implemented as base component
- `CspLearningService` implemented in baseline mode
- Package events emitted for extensibility

## Value-added ideas (PRO tier)

- Team workspaces and role-based dashboards (security officer / devops / auditor).
- Baseline policies by stack profile (Laravel web, API-only, admin panel).
- Header regression detector between deployments with alerting.
- Signed audit evidence bundles for compliance workflows.
- Optional webhook notifications for critical security regressions.
