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

## Stage 1.1 — Root Domain Cutover (2026-07-15)

Status: completed.

User instruction:
- Use the main domain root `https://appsbilling.dentasejahteragroup.my.id` for the commercial platform.
- Do not touch `/v3`.
- Do not touch `/nms`.

Actions:
- Full live root backup created before cutover.
- Legacy root files moved into server backup archive, not hard-deleted.
- Commercial public files deployed to root `/`.
- Engine/storage remains outside public web root at `/var/www/appsbilling-commercial-platform`.
- Preserved `/v2`, `/v3`, `/nms`, and `/commercial-preview` directories.

Backup:
- `/home/ubuntu/backups/appsbilling-root-before-commercial-cutover-20260715-232009.tar.gz`
- Legacy moved to `/home/ubuntu/backups/appsbilling-root-legacy-20260715-232009/`

Verification:
- `/` HTTP 200.
- `/register.php` HTTP 200.
- `/superadmin/login.php` HTTP 200.
- `/tenant/login.php` HTTP 200.
- `/v3/login.php` HTTP 200 and still contains e-Billing DSG marker.
- `/nms/` still responds with expected 302 to operator access.
- Root end-to-end test passed: register → superadmin approve → tenant DB provision → tenant login → dashboard.
- Platform DB after test: tenants=2, active=2, tenant DBs=2.

## Stage 1.2 — Human Polish + 4-Digit Account Number (2026-07-15)

Status: completed and live.

User request:
- Make root landing less AI/template-looking.
- Add registration tutorial.
- Use example brand `MRT NET`, not Borneo.
- Replace visible slug-login concept with random 4-digit numeric `No Akun` generated during registration.

Implemented:
- Redesigned root landing with a more product-like, human-friendly ISP billing narrative.
- Added tutorial section: Daftar → Simpan No Akun → Approval → Login dashboard.
- Updated registration form examples to `MRT NET` / `ops@mrtnet.id` / `adminmrt`.
- Added `tenants.account_no` migration with safe backfill for existing tenants.
- Registration now generates unique random 4-digit account number.
- Tenant slug is now internal `acct-{account_no}`.
- Tenant login now prefers No Akun Mitra + username + password.
- Superadmin table shows No Akun.
- Tenant dashboard shows No Akun.

Backup before live deploy:
- `/home/ubuntu/backups/appsbilling-root-before-human-polish-20260715-234038.tar.gz`

Verification:
- PHP lint passed locally and on server.
- Local E2E passed with `MRT NET`: registration → 4 digit account no → approve → tenant login by account no → dashboard.
- Live E2E passed with `MRT NET LIVE ...`: registration → account no generated (`^[0-9]{4}$`) → slug became `acct-{account_no}` → superadmin approve → tenant login by account no → dashboard.
- `/v3/login.php` remained HTTP 200 and e-Billing DSG marker was verified.
- `/nms/` remained available and redirected normally to operator access.
- After live migration: `tenants=3`, `with_account_no=3`, `active=3`.
