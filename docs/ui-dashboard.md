# UI Dashboard

Enable UI via config:

```php
'ui' => [
  'enabled' => true,
  'path' => 'shield',
  'middleware' => ['web', 'auth'], // recommended and default
  'require_ability' => null,
  'theme' => 'light', // light|dark|auto
]
```

Access hardening:

- Use `auth` middleware (default).
- Set `require_ability` (for example `viewShieldDashboard`) to restrict dashboard to admin roles/permissions.

Dashboard includes:

- Security status cards
- Checks list with status
- Warnings list
- Language selector (ES/EN/FR/PT-BR via host locale flow)
- Native charts (no CDN dependency) for checks distribution and CSP trends
- Expected headers table
- Hardening plan with prioritized actions
- Endpoint consistency scanner with:
  - dynamic path input
  - missing + mismatched header diff per endpoint
  - per-endpoint security score (0-100) and severity semaphore (low/medium/high/critical)
  - JSON/CSV export from UI
- CSP learning suggestions
- Optional recent CSP reports table

Views are publishable with `shield-views` tag.

Layout:

- Reusable base layout at `shield::layouts.app`
- Dashboard view extends the layout and splits content by section with a sidebar
