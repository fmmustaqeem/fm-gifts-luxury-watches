# Project Notes

## Run locally
- Static site: `python3 -m http.server 8080` from project root, then open http://localhost:8080.
- Cloudflare Pages Functions live under `functions/api/` and are served at `/api/*`.

## Pitfalls
- `functions/api/auth/login.js` and `register.js` import helpers from `../auth.js` (not `./auth.js`); wrong path breaks auth.
- `functions/api/auth/register.js` must issue the `fm_session` cookie (not clear it) to match login.
- Supplier feed is served only by `functions/api/admin/source-stores.js` after admin session verification; static `admin/store-feed.js` removed. Do not re-add it as a public static file.
- `functions/api/auth.js` exposes `verifySession(token, secret)` for cookie-based admin auth; admin pages call `/api/admin/check` and `/api/admin/source-stores`.
- `index.html` had a duplicate `render()` function and an undefined `storeGrid` reference; only one `render()` remains.
- Client auth in `index.html` stores only name+email in localStorage (never the password).
- `store.html` auth uses `credentials: 'include'` and reads `data.user` only; no token in localStorage.
- Order storage key is `fm-orders` everywhere (cart, checkout, order-success); do not use `fm-gifts-orders`.

## Security
- Admin supplier data is gated server-side via session cookie + role check (admin/owner). Client-side localStorage guard alone is insufficient.
- CodeQL workflow at `.github/workflows/codeql.yml`.
- Commit+push required for changes to reach `main`; local working tree edits are not on remote until pushed.
