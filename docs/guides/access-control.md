# Access Control

Sentinel dashboard must not be public.

## Recommended config

```php
'ui' => [
  'enabled' => true,
  'middleware' => ['web', 'auth'],
  'require_ability' => 'viewSentinelDashboard',
],
```

## Gate definition

In `AppServiceProvider`:

```php
Gate::define('viewSentinelDashboard', fn ($user) => $user->hasRole('super_admin') && $user->can('sentinel.view'));
```

## Permission model (Spatie)

1. Create permission `sentinel.view`.
2. Assign it to `super_admin`.
3. Optionally assign to security-specific roles if your Gate allows it.

## Verification matrix

- unauthenticated user: redirected to login (or denied)
- authenticated without role/permission: `403`
- `super_admin` with `sentinel.view`: allowed

## Sidebar visibility

For admin UX, show Sentinel menu item only for `super_admin` users.
This is a UI convenience and not a replacement for backend authorization.
