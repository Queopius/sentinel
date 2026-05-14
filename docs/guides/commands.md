# Commands

## `sentinel:install`

Publishes package resources and prints integration instructions.

```bash
php artisan sentinel:install --with-views
```

Options:

- `--with-views`
- `--force`

## `sentinel:audit`

Runs security checks and warnings.

```bash
php artisan sentinel:audit
php artisan sentinel:audit --format=json
php artisan sentinel:audit --format=csv
```

## `sentinel:scan`

Scans endpoints and compares expected headers vs actual responses.

```bash
php artisan sentinel:scan
php artisan sentinel:scan --json
php artisan sentinel:scan --paths=/,/login,/api
```

## `sentinel:prune-reports`

Deletes old CSP reports.

```bash
php artisan sentinel:prune-reports
php artisan sentinel:prune-reports --days=30
```

## Suggested scheduler

```php
$schedule->command('sentinel:prune-reports')->daily();
```
