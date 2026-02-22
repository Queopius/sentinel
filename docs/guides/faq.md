# FAQ

## Is Shield only for web apps?
No. You can use presets and config to harden APIs too.

## Should I enforce HTTPS only in app middleware?
No. Best practice is enforce at edge (LB/Nginx/Apache) and keep app middleware as defense-in-depth.

## Can I use Shield behind Cloudflare/ALB?
Yes. Configure trusted proxies so Laravel correctly detects HTTPS.

## How should I adopt CSP safely?
Start in report-only mode, collect reports, then progressively tighten and enforce.

## Can I customize dashboard UI?
Yes. Publish views with `shield-views` and override in `resources/views/vendor/shield`.

## Do I need CSP report storage in DB?
Optional. Enable only if you need diagnostics and learning insights.

## Is only one role allowed to see dashboard?
By default you can decide via `require_ability` Gate logic. In this project, policy is `super_admin` only.
