# Access Control

Shield dashboard must not be public.

## Recommended config

```php
'ui' => [
  'enabled' => true,
  'middleware' => ['web', 'auth'],
  'require_ability' => 'viewShieldDashboard',
],
```

## Gate definition

In `AppServiceProvider`:

```php
Gate::define('viewShieldDashboard', fn ($user) => $user->hasRole('super_admin') && $user->can('shield.view'));
```

## Permission model (Spatie)

1. Create permission `shield.view`.
2. Assign it to `super_admin`.
3. Optionally assign to security-specific roles if your Gate allows it.

## Verification matrix

- unauthenticated user: redirected to login (or denied)
- authenticated without role/permission: `403`
- `super_admin` with `shield.view`: allowed

## Sidebar visibility

For admin UX, show Shield menu item only for `super_admin` users.
This is a UI convenience and not a replacement for backend authorization.
