# FM Gifts — Cloudflare Pages copy

This folder isolates a copy of the static FM Gifts storefront and its related site files from the repository-root/ByetHost workstream.

## Cloudflare Pages settings

- **Root directory:** `cloudflare-pages`
- **Build command:** leave empty for this plain static site
- **Build output directory:** `.`
- **Deployment status:** not deployed or tested by this commit

## Included

- `index.html`, `store.html`, and `policies.html`
- `assets/`, `data/`, and `store/`
- `admin/`, `functions/`, and the existing `api/` source files

## Important boundaries

- These files are a point-in-time copy; later edits to repository-root files do not automatically sync here.
- The repository-root files and ByetHost deployment workflow were intentionally left in place and unchanged by this folder copy.
- The `functions/` directory is the Cloudflare Pages Functions source location. Files under `api/` are included as source/reference files; they are not automatically treated as Pages Functions merely because that directory exists.
- The product catalogue is explicitly a demo catalogue, not verified inventory.
- Do not configure production secrets or activate payments based on this copy alone. Authentication, API routes, catalogue data and deployment configuration need a separate review and staging test before use.
