# Architecture

## Core flow

1. Request enters app middleware stack.
2. `EnforceHttps` optionally redirects insecure traffic.
3. `AddSecurityHeaders` appends configured headers.
4. Optional Shield dashboard reads audit services for observability.

## Service Provider responsibilities

`ShieldServiceProvider` handles:

- config merge (`mergeConfigFrom`)
- resource publishing tags
- views namespace registration
- route loading (UI/CSP reports/health)
- command registration
- middleware aliases (`shield.headers`, `shield.https`)

## Middleware

- `AddSecurityHeaders`: applies configured security headers per request context.
- `EnforceHttps`: redirect enforcement and URL scheme forcing.

## Support services

- `ShieldPresetResolver`: merges baseline preset + manual overrides.
- `HeaderManager`: computes expected headers for request/config pair.
- `HeaderInspector`: compares expected vs actual response headers.
- `SecurityAuditService`: check engine (OK/Warning/Fail + warnings).
- `CspBuilder`: structured CSP directive builder.
- `HstsBuilder`: strict transport header builder.
- `ProxyDetector`: warning signals for proxy/trust inconsistencies.
- `EndpointScanner`: multi-endpoint consistency checks.
- `CspLearningService`: suggestions from stored CSP reports.
- `NonceManager`: nonce generation primitive for future CSP nonces.

## HTTP controllers

- `DashboardController`: UI data + optional exports.
- `CspReportController`: resilient CSP report ingestion endpoint.
- `HealthController`: optional JSON summary endpoint.

## Commands

- `shield:install`
- `shield:audit`
- `shield:scan`
- `shield:prune-reports`

## Events

- `ShieldAuditCompleted`
- `ShieldScanCompleted`
- `CspReportStored`

## Monorepo strategy

Package remains isolated under `packages/queopius/shield` and is consumed via Composer path repository in host app.

## Testing strategy

- Package-level tests: Orchestral Testbench (unit/feature)
- Host-app manual integration tests: middleware + dashboard + routes + commands
