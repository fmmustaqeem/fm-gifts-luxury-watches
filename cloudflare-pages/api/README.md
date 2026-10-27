# Persistent API foundation

## Production boundary
- Public catalog: active/published products only.
- Orders: server-side API.
- Admin: authenticated role required.
- Supplier/vendor/source records: private.
- Pricing and delivery rules: server-side.
- Passwords: server-side hashing only.
- Secrets: environment/bindings only.

## Endpoints
- GET /api/products
- GET /api/products/:sku
- POST /api/orders
- GET /api/orders/:id
- PATCH /api/admin/orders/:id
- GET /api/admin/couriers
- GET /api/admin/catalog-sources

## Current state
Browser-local data remains the fallback until a database/API deployment is connected. No credentials or secrets are embedded.
