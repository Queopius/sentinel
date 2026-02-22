# Release and Packagist Automation

Queopius Shield includes `.github/workflows/release.yml` with automated release steps.

## Trigger

The workflow runs when you push a SemVer tag:

```bash
git tag v1.0.0
git push origin v1.0.0
```

It can also be launched manually with `workflow_dispatch`.

## What it does

1. Runs quality gates:
   - PHPUnit
   - Pint (`--test`)
   - PHPStan
2. Builds docs with `mkdocs build --strict`
3. Creates GitHub Release notes automatically
4. Optionally triggers Packagist update webhook

## Required secrets

Optional but recommended:

- `PACKAGIST_WEBHOOK_URL`

Where to get it:

1. Open your package on Packagist (`queopius/shield`)
2. Go to package settings/update section
3. Copy the generated update webhook URL
4. Add it as GitHub secret in:
   - `Settings > Secrets and variables > Actions > New repository secret`

If this secret is missing, workflow keeps passing and skips Packagist trigger.

## Recommended release flow

1. Merge to `main`
2. Ensure CI and docs workflows are green
3. Create and push tag `vX.Y.Z`
4. Verify GitHub Release is created
5. Confirm Packagist picked up the new tag

