# Best Practices

## 1) Start safely with report-only CSP

- Keep `headers.csp.report_only = true` initially.
- Gather violations.
- Remove weak directives like `unsafe-inline` when possible.
- Move to enforce after clean results.

## 2) Protect dashboard access

- Use `ui.middleware = ['web','auth']`.
- Use `ui.require_ability` and Gate checks.
- Restrict to privileged role(s), typically `super_admin`.

## 3) Use layered HTTPS enforcement

- Enforce HTTPS at edge (Nginx/Apache/LB/CDN).
- Keep Shield `https.redirect` enabled as backup.
- Add HSTS only when TLS is stable and complete.

## 4) Keep config explicit and minimal

- Use presets as baseline.
- Override only what you need.
- Avoid over-permissive `custom` headers without review.

## 5) Monitor continuously

- Run `shield:audit` in CI/CD.
- Run `shield:scan` against key endpoints.
- Review dashboard warnings after each release.

## 6) Manage CSP reports lifecycle

- Enable storage only when needed.
- Prune with `shield:prune-reports`.
- Schedule pruning daily.

## 7) Test in environments

- Validate behavior in staging before production.
- Compare expected and actual headers across endpoints.
- Confirm no route exclusions accidentally bypass protection.

## 8) Keep package and framework updated

- Update Laravel and Shield regularly.
- Re-run audit/scan after upgrades.
- Re-check published views when package dashboard changes.

## 9) Document security ownership

Define who owns:

- policy updates (CSP/HSTS)
- alert triage
- production rollout approvals

## 10) Treat findings as operational work

A warning is not just output. Convert it into:

- task
- owner
- due date
- verification step
