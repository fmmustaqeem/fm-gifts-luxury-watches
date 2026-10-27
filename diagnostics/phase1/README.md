# FM Gifts — Phase 1 ByetHost diagnostic

This is a temporary, isolated connectivity probe. It is not a storefront deployment.

## Safety boundary

- Upload only to `htdocs/fm-gifts-preview/`.
- Do not upload into `htdocs/` itself and do not overwrite WordPress files.
- Do not use the existing storefront deployment workflow for this diagnostic.
- The package intentionally does not contain a live token.
- Run the probe only over HTTPS and remove the endpoint and state files after testing.

## Package contents

- `check.php` — token-gated GET and JSON POST probe.
- `.htaccess` — disables directory listing and denies direct access to dotfiles.
- `state/.htaccess` — denies all direct HTTP access to state files.

## Prepare the temporary token

1. On your device, generate 32 lowercase hexadecimal characters using a trusted local random generator (for example, `openssl rand -hex 16` where OpenSSL is available).
2. Create `htdocs/fm-gifts-preview/.state/token` using your hosting File Manager.
3. Put exactly one line in the file: `TOKEN|YYYY-MM-DDTHH:MM:SSZ`, replacing TOKEN with the generated 32-character value and the timestamp with an actual near-future UTC expiry (for example, 30–60 minutes after setup). Do not copy the placeholder timestamp literally.
4. Create `htdocs/fm-gifts-preview/.state/.htaccess` from the package's `state/.htaccess`. If the host ignores `.htaccess`, stop and remove the diagnostic files; do not continue.

## Test

Replace TOKEN with the generated value; do not share it in chat or commit it.

```sh
curl -i 'https://giftsandwatch.byethost18.com/fm-gifts-preview/check.php?t=TOKEN'
curl -i -X POST 'https://giftsandwatch.byethost18.com/fm-gifts-preview/check.php?t=TOKEN' \
  -H 'Content-Type: application/json' --data '{"ping":"pong"}'
```

Expected: GET returns HTTP 200 JSON with `"ok":true`; POST returns HTTP 200 JSON with `"ok":true` and an incrementing count.

Also verify direct requests to `/.state/token` and `/.state/count` return 403 or 404, and an invalid token returns 404. If any state file is publicly readable, stop and remove the diagnostic.

## Cleanup

After recording the HTTP results, delete `check.php`, `.state/token`, and `.state/count` from the preview folder. Then request `/fm-gifts-preview/check.php` and verify HTTP 404. Do not delete any WordPress files or anything outside `fm-gifts-preview/`.

No server test or upload is implied by building this package.