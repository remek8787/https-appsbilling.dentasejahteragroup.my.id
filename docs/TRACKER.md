# AppsBilling Commercial Platform Tracker

## Stage 0 — Concept & Safety Grounding (2026-07-15)

Status: in progress.

- [x] Capture user request for commercial AppsBilling multi-mitra platform.
- [x] Decide not to overwrite/destroy root live before explicit final approval.
- [x] Create separate workspace and GitHub repo binding.
- [x] Draft SaaS/multi-tenant blueprint.
- [ ] Create new Coret board.
- [ ] Push initial repo.

## Stage 1 — MVP Architecture

Planned:

- [ ] Platform DB schema.
- [ ] Superadmin auth.
- [ ] Tenant registration.
- [ ] Approve/disable/delete tenant controls.
- [ ] Tenant DB provisioning from AppsBilling V3 template.
- [ ] Tenant login.
- [ ] Billing V3 tenant shell.

## Stage 2 — Safe Preview

Planned:

- [ ] Deploy to `/platform-preview/` or similar.
- [ ] Backup root live before any replacement.
- [ ] Smoke test registration → approval → tenant dashboard.
- [ ] Ask Tuan Besar for root cutover approval.

## Stage 0.1 — Coret Concept Board (2026-07-15)

Status: completed.

- [x] New Coret map created so commercial SaaS concept does not mix with DSG/Borneo V3 map.
- [x] Main branches: Product Vision, Superadmin Control, Mitra Registration, Tenant Billing V3, Data Isolation, Commercial UX, Deployment & Cutover, Roadmap.
- [x] Sketch added for landing → registration → superadmin approval → tenant DB provisioning → tenant billing V3.
- [x] Share link created and stored in `docs/CORET.md`.

## Stage 1 — MVP Registration to Tenant Dashboard (2026-07-15)

Status: deployed to preview and smoke-tested.

Boundary respected:
- Did not touch `/v3`.
- Did not touch `/nms`.
- Commercial preview deployed at `/commercial-preview/`.
- Engine/storage deployed outside public web path at `/var/www/appsbilling-commercial-platform`.

Implemented:
- Commercial landing page.
- Mitra registration form.
- Platform DB migration/seed.
- Superadmin login.
- Superadmin tenant list with approve, disable, reactivate, soft delete.
- Tenant DB provisioning with isolated SQLite per tenant.
- Tenant login.
- Tenant dashboard shell with separate DB stats.

Security:
- Superadmin password stored as server-side hash in `.env`, not committed.
- `.env`, storage, platform DB, and tenant DB are outside public preview path.
- Tenant delete is soft delete; DB is not automatically removed.

Verification:
- Local PHP lint passed for all files.
- Local end-to-end test passed: register → superadmin login → approve → tenant login → dashboard.
- Public preview HTTP 200 checked for landing, register, superadmin login, and tenant login.
- Public preview end-to-end test passed.
- Platform DB after smoke test: tenants=1, active=1, tenant DBs=1.

Preview URLs:
- Landing: `https://appsbilling.dentasejahteragroup.my.id/commercial-preview/`
- Registrasi mitra: `https://appsbilling.dentasejahteragroup.my.id/commercial-preview/register.php`
- Superadmin: `https://appsbilling.dentasejahteragroup.my.id/commercial-preview/superadmin/login.php`
- Tenant login: `https://appsbilling.dentasejahteragroup.my.id/commercial-preview/tenant/login.php`
