# CSP Reporting

Enable endpoint and storage:

```php
'csp_reports' => [
  'enabled' => true,
  'route_path' => 'sentinel/csp-reports',
  'store_database' => true,
  'prune_days' => 30,
]
```

## Payload handling

- Accepts `csp-report`, `body`, or root payload formats.
- Invalid payloads are tolerated and return 204.
- Optional warning logs for parse/persist failures.

## Pruning

```bash
php artisan sentinel:prune-reports --days=30
```

Scheduler example:

```php
$schedule->command('sentinel:prune-reports')->daily();
```
