# Security Concepts

This page explains the core HTTP security concepts behind Queopius Shield.

## CSP (Content Security Policy)

CSP controls which sources the browser can load for scripts, styles, images, fonts, frames, and network calls.

### Why CSP matters

- Reduces impact of XSS by restricting script execution.
- Prevents unexpected third-party injection.
- Gives visibility through violation reports.

### Report-only vs enforce

- **Report-only**: does not block, only reports violations.
- **Enforce**: actively blocks violations.

Recommended rollout:

1. Start report-only.
2. Collect reports.
3. Remove unsafe/unused sources.
4. Switch to enforce.

## HSTS (Strict-Transport-Security)

HSTS instructs browsers to use HTTPS only for your domain.

### Why HSTS matters

- Prevents SSL-stripping downgrade attacks.
- Reduces accidental HTTP access.

Important: enable HSTS only after HTTPS is consistently available.

## Referrer-Policy

Controls how much referrer information is sent to external origins.

Typical secure default:

- `strict-origin-when-cross-origin`

## X-Content-Type-Options

`nosniff` prevents MIME type sniffing behavior that can cause script execution risks.

## Frame protections

Two approaches:

- `X-Frame-Options` (legacy support)
- `frame-ancestors` in CSP (modern and more granular)

At least one should be configured.

## Permissions-Policy

Restricts high-risk browser capabilities (camera, microphone, geolocation, etc.).

## COOP / COEP / CORP

Cross-origin isolation and resource access controls:

- `Cross-Origin-Opener-Policy`
- `Cross-Origin-Embedder-Policy`
- `Cross-Origin-Resource-Policy`

These help reduce cross-origin data leakage risk and harden browser isolation boundaries.

## HTTPS enforcement

Application-level redirect is useful, but best practice is:

1. enforce HTTPS at edge (LB/proxy/web server)
2. keep app-level enforcement as defense-in-depth

## Reverse proxy trust

If Laravel does not trust proxy headers correctly, HTTPS detection may fail.

Symptoms:

- HSTS not emitted on HTTPS traffic
- wrong scheme in generated URLs

Fix trusted proxies first, then validate with `shield:audit` and dashboard checks.
