# Troubleshooting

## Dashboard returns 404

- Confirm `ui.enabled=true` in `config/sentinel.php`.
- Run `php artisan optimize:clear`.

## Dashboard is public

- Ensure `ui.middleware` includes `auth`.
- Set `ui.require_ability` and define Gate.

## CSP headers not applied

- Verify `headers.csp.enabled=true`.
- Check route/path exclusions.
- If behind proxy, verify trusted proxies for correct HTTPS detection.

## HSTS not present

HSTS is emitted only when request is secure (`https`).

## Theme/section resets while navigating

- Use latest published Sentinel views.
- Clear cache: `php artisan optimize:clear`.
- Re-publish views if app overrides old templates.

## CSP reports not stored

- Enable `csp_reports.enabled`.
- Publish and run migrations.
- Verify report endpoint path and middleware.

## Reverse proxy or port issues

- Keep consistent base URL and trusted proxy setup.
- Prefer route-relative links for internal Sentinel navigation.
