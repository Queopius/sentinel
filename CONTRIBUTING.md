# Contributing

Thanks for contributing to Queopius Shield.

## Development Setup

```bash
composer install
```

## Quality Checks

Run all checks before opening a PR:

```bash
vendor/bin/pint --test
vendor/bin/phpunit
vendor/bin/phpstan analyse
```

## Pull Request Guidelines

- Keep PRs focused and small when possible.
- Add tests for behavior changes.
- Update docs when config, commands, or behavior changes.
- Preserve backward compatibility for `1.x` unless the PR is explicitly for a major release.

## Commit and Versioning Notes

- Use clear commit messages.
- Follow SemVer expectations:
  - patch: bugfix/internal improvements
  - minor: backward-compatible features
  - major: breaking changes

## Security Issues

Do not open public issues for vulnerabilities.

See `SECURITY.md` for the private disclosure process.

