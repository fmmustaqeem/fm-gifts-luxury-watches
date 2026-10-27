# FM Gifts & Luxury Watches

## FM Ecosystem Control Plane

**FM Home is the canonical security and governance control plane for this project.**  
**Canonical control plane:** https://github.com/fmmustaqeem/fm-home

FM Gifts & Luxury Watches keeps its local project history and implementation records here. For ecosystem-wide governance, security policy, approvals, audit expectations, and cross-project rules, follow FM Home first.

### Project Identity
- Project: FM Gifts & Luxury Watches
- Repository: `fmmustaqeem/fm-gifts-luxury-watches`
- Deployment target: ByetHost via FTP (pending a verified deployment)
- Production branch: `main`
- Control-plane status: Registered
- Default worker access: read-only
- Production changes: approval required
- Destructive actions: additional confirmation required
- Cross-project access: denied unless explicitly authorized

### Storefront Scope
The storefront is being built around watches, perfumes, gifts and accessories using a parent → subcategory → product structure. Product-level brand, photo, price and stock data will be added from the shop inventory rather than invented.

### Change Rule
One change → one commit → CI/evidence → deployment verification.
