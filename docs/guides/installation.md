# Installation

## Composer install

```bash
composer require queopius/shield
```

## Publish resources

```bash
php artisan vendor:publish --tag=shield-config
php artisan vendor:publish --tag=shield-views
php artisan vendor:publish --tag=shield-migrations
```

## Installer command

```bash
php artisan shield:install --with-views
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

- `/shield`

## Verify commands

```bash
php artisan shield:audit
php artisan shield:scan
```

## Upgrade notes

1. `composer update queopius/shield`
2. Review `config/shield.php` changes.
3. Republish views only if needed.
4. Run migrations if package introduces new schema.
