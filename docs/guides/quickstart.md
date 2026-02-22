# Quickstart

This quickstart assumes Laravel 12 and PHP 8.3+.

## 1) Install package

```bash
composer require queopius/shield
```

## 2) Run installer

```bash
php artisan shield:install --with-views
```

## 3) Migrate

```bash
php artisan migrate
```

## 4) Enable dashboard

In `config/shield.php`:

```php
'ui' => [
  'enabled' => true,
  'path' => 'shield',
  'middleware' => ['web', 'auth'],
  'require_ability' => 'viewShieldDashboard',
],
```

## 5) Add middleware aliases/global

In `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'shield.headers' => \Queopius\Shield\Http\Middleware\AddSecurityHeaders::class,
        'shield.https' => \Queopius\Shield\Http\Middleware\EnforceHttps::class,
    ]);

    // Optional global middleware
    $middleware->append(\Queopius\Shield\Http\Middleware\EnforceHttps::class);
    $middleware->append(\Queopius\Shield\Http\Middleware\AddSecurityHeaders::class);
})
```

## 6) Run initial audit

```bash
php artisan shield:audit
```

Then open `/shield` as an authorized user.
