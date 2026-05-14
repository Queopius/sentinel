# Installation

## Composer install

```bash
composer require queopius/sentinel
```

## Publish resources

```bash
php artisan vendor:publish --tag=sentinel-config
php artisan vendor:publish --tag=sentinel-views
php artisan vendor:publish --tag=sentinel-migrations
```

## Installer command

```bash
php artisan sentinel:install --with-views
```

Useful options:

- `--with-views` publishes dashboard views.
- `--force` republishes resources if already published.

## Migrations

```bash
php artisan migrate
```

## Verify route

When `ui.enabled=true`, default dashboard route is:

- `/sentinel`

## Verify commands

```bash
php artisan sentinel:audit
php artisan sentinel:scan
```

## Upgrade notes

1. `composer update queopius/sentinel`
2. Review `config/sentinel.php` changes.
3. Republish views only if needed.
4. Run migrations if package introduces new schema.
