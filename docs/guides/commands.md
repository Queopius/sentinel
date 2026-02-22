# Commands

## `shield:install`

Publishes package resources and prints integration instructions.

```bash
php artisan shield:install --with-views
```

Options:

- `--with-views`
- `--force`

## `shield:audit`

Runs security checks and warnings.

```bash
php artisan shield:audit
php artisan shield:audit --format=json
php artisan shield:audit --format=csv
```

## `shield:scan`

Scans endpoints and compares expected headers vs actual responses.

```bash
php artisan shield:scan
php artisan shield:scan --json
php artisan shield:scan --paths=/,/login,/api
```

## `shield:prune-reports`

Deletes old CSP reports.

```bash
php artisan shield:prune-reports
php artisan shield:prune-reports --days=30
```

## Suggested scheduler

```php
$schedule->command('shield:prune-reports')->daily();
```
