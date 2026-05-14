# Local Development (Monorepo)

Recommended structure:

- Host Laravel app at repository root
- Package at `packages/queopius/sentinel`

## Host `composer.json`

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "packages/queopius/sentinel",
      "options": { "symlink": true }
    }
  ],
  "require": {
    "queopius/sentinel": "^1.0"
  }
}
```

## Iteration flow

```bash
composer update queopius/sentinel
php artisan optimize:clear
php artisan sentinel:audit
php artisan sentinel:scan
```

## App integration checks

- Add middleware aliases/global in `bootstrap/app.php`
- Publish config/views/migrations
- Run migrations and open dashboard route
