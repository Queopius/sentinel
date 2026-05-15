# Quickstart

This quickstart assumes Laravel 11, 12, or 13. Use PHP 8.2+ for Laravel 11/12 and PHP 8.3+ for Laravel 13.

## 1) Install package

```bash
composer require queopius/sentinel
```

## 2) Run installer

```bash
php artisan sentinel:install --with-views
```

## 3) Migrate

```bash
php artisan migrate
```

## 4) Enable dashboard

In `config/sentinel.php`:

```php
'ui' => [
  'enabled' => true,
  'path' => 'sentinel',
  'middleware' => ['web', 'auth'],
  'require_ability' => 'viewSentinelDashboard',
],
```

## 5) Add middleware aliases/global

In `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'sentinel.headers' => \Queopius\Sentinel\Http\Middleware\AddSecurityHeaders::class,
        'sentinel.https' => \Queopius\Sentinel\Http\Middleware\EnforceHttps::class,
    ]);

    // Optional global middleware
    $middleware->append(\Queopius\Sentinel\Http\Middleware\EnforceHttps::class);
    $middleware->append(\Queopius\Sentinel\Http\Middleware\AddSecurityHeaders::class);
})
```

## 6) Run initial audit

```bash
php artisan sentinel:audit
```

Then open `/sentinel` as an authorized user.
