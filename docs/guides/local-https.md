# Local HTTPS Testing

Use local HTTPS to validate real behavior for:

- `Request::isSecure()` checks
- HSTS header emission
- HTTPS redirect logic
- CSP behavior under secure context

## Recommended setup (Sail host app)

1. Generate local certificate:

```bash
./scripts/generate-local-https-cert.sh
```

2. Ensure Docker service exposes HTTPS port (example `8443`).

3. Start containers:

```bash
./vendor/bin/sail up -d --build
```

4. Set app URL to HTTPS in host app `.env`:

```dotenv
APP_URL=https://your-app.test:8443
```

5. Clear config cache:

```bash
./vendor/bin/sail artisan optimize:clear
```

6. Open app on HTTPS URL:

- `https://your-app.test:8443`

7. Open Shield dashboard:

- `https://your-app.test:8443/shield`

## Notes

- Browser may warn for self-signed cert; trust locally for testing.
- Keep `trustProxies('*')` in Laravel middleware config when behind TLS proxy.
- Keep production TLS at edge (Nginx/ALB/Cloudflare) plus app-level defense-in-depth.

## Trust the local certificate

The generated files are:

- `docker/https/certs/<your-app>.crt`
- `docker/https/certs/<your-app>.key`

Use your actual certificate filenames if your generator script sets different names.

### Ubuntu / Debian (NSS + system stores)

```bash
sudo cp docker/https/certs/<your-app>.crt /usr/local/share/ca-certificates/<your-app>.crt
sudo update-ca-certificates
```

Restart browser after trust update.

### macOS

1. Open `Keychain Access`.
2. Import `docker/https/certs/<your-app>.crt` into `System` keychain.
3. Open certificate, set `Trust` to `Always Trust`.
4. Restart browser.

### Windows

1. Run `certmgr.msc`.
2. Import `docker/https/certs/<your-app>.crt` into `Trusted Root Certification Authorities`.
3. Restart browser.

## Quick verification

```bash
curl -k -I https://your-app.test:8443
curl -k -I https://your-app.test:8443/shield
```

If Shield headers are enabled, responses should include configured security headers.
