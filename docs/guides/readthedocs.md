# Read the Docs Setup Guide

This package is already prepared for Read the Docs with MkDocs.

## Included files

- `.readthedocs.yaml`
- `mkdocs.yml`
- `docs/requirements.txt`

## Steps in Read the Docs

1. Create/import project in Read the Docs.
2. Point to repository containing `packages/queopius/sentinel`.
3. Ensure default branch is correct.
4. Trigger build.

Read the Docs will detect:

- config file: `.readthedocs.yaml`
- docs toolchain: MkDocs
- Python dependencies from `docs/requirements.txt`

## Local preview

From `packages/queopius/sentinel`:

```bash
python3 -m venv .venv
source .venv/bin/activate
pip install -r docs/requirements.txt
mkdocs serve
```

Open local URL shown by MkDocs.

## Build docs locally

```bash
mkdocs build --strict
```

## Common fixes

- Broken nav path: verify entries in `mkdocs.yml` are relative to `docs/`.
- Theme/plugin issues: pin versions in `docs/requirements.txt`.
- Missing page in RTD: ensure file is committed and included in `nav`.
