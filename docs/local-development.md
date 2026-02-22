# Local Development (Monorepo)

Recommended structure:

- Host Laravel app at repository root
- Package at `packages/queopius/shield`

## Host `composer.json`

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "packages/queopius/shield",
      "options": { "symlink": true }
    }
  ],
  "require": {
    "queopius/shield": "*"
  }
}
```

## Iteration flow

```bash
composer update queopius/shield
php artisan optimize:clear
php artisan shield:audit
php artisan shield:scan
```

## App integration checks

- Add middleware aliases/global in `bootstrap/app.php`
- Publish config/views/migrations
- Run migrations and open dashboard route
