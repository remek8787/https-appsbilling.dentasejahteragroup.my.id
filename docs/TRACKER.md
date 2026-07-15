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

## Stage 1.3 — Admin Label Polish (2026-07-15)

Status: completed and live.

User request:
- Change visible `Superadmin` wording to `Admin` / `Login Admin`.

Implemented:
- Root nav label changed from `Superadmin` to `Admin`.
- Admin login page title changed to `Login Admin` and heading `Admin`.
- Registration/tutorial copy now says admin review/approval instead of superadmin.
- Admin dashboard heading uses `Admin Mitra`.
- Internal route remains `/superadmin/` to avoid unnecessary routing risk.

Backup before deploy:
- `/home/ubuntu/backups/appsbilling-root-before-admin-label-20260715-234627.tar.gz`

Verification:
- Syntax checks passed on live files.
- Root page contains visible `Admin` label and no nav `Superadmin` label.
- Admin login page shows `Admin` and `Panel admin`.
- Registration page mentions `review admin`.
- `/v3/login.php` still HTTP 200.
- `/nms/` still responds with expected redirect.

## Stage 1.4 — Admin Detail + Tenant Branding Logos (2026-07-15)

Status: completed and live.

User request:
- Admin can see concise account detail and disable accounts.
- If account/no akun is not active, tenant login should show confirmation info to admin Ananta Satriya with WhatsApp `085804783530`.
- Mitra can change app logo and receipt logo from dashboard.
- Logo upload accepts JPG/PNG and should avoid huge files; auto-compress if server supports GD, otherwise enforce small safe size.
- Copyright remains `PT Denta Sejahtera Group dan Ananta Satriya Ferdian`.

Implemented:
- New admin tenant detail page: `/superadmin/tenant_detail.php?id={tenant_id}`.
- Admin list links to detail and includes a Detail action.
- Detail page shows ringkasan akun, No Akun, tenant UID, internal slug, contact, DB state/path/schema, tenant stats, user list, activity events, and account controls.
- Admin can approve, disable, and reactivate from detail page.
- Tenant login now distinguishes inactive/disabled accounts and shows: `Silahkan lakukan konfirmasi ke sisi admin Ananta Satriya. WhatsApp: 085804783530`.
- New tenant branding page: `/tenant/settings.php`.
- Dashboard sidebar links to `Logo & Branding`.
- Tenant can upload app logo and receipt logo; accepted types JPG/PNG.
- Upload target file size is 512KB; server compresses/resizes if GD is available, otherwise only accepts already-small valid files.
- Public tenant logo files are stored under `/uploads/tenants/{tenant_uid}/`.
- Tenant DB settings store `app_logo`, `receipt_logo`, `office_brand`, and copyright.
- Copyright text is displayed and kept as `PT Denta Sejahtera Group dan Ananta Satriya Ferdian`.

Backup before deploy:
- `/home/ubuntu/backups/appsbilling-root-before-admin-detail-logo-20260715-235651.tar.gz`

Verification:
- Local syntax checks passed.
- Local flow passed: pending login notice, admin detail, approve, tenant settings, logo upload with valid PNG.
- Live deploy syntax checks passed.
- Live flow passed with `MRT NET FITUR LIVE ...`: register → pending login notice with WhatsApp admin → admin detail → approve → tenant login → logo settings → upload logo → public logo URL HTTP 200.
- `/v3/login.php` still verified with e-Billing DSG marker.
- `/nms/` still responds normally.

## Stage 1.5 — Master Admin Tenant Login (2026-07-16)

Status: completed and live.

User request:
- Admin username `ananta` with the central admin password can login to any tenant No Akun.

Implemented:
- Tenant login now supports master admin login:
  - No Akun Mitra: any active tenant account number.
  - Username: `ananta`.
  - Password: validated against central `platform_admins.password_hash`, not stored plaintext.
- Master admin login does not create tenant users.
- Session is marked as `tenant_master_admin` and dashboard shows `Mode Admin Pusat`.
- Login event is recorded as `tenant_master_login` with actor type `platform_admin`.
- Normal tenant user login still works.
- Pending/disabled tenants still cannot be accessed and continue showing admin contact notice.

Backup before deploy:
- `/home/ubuntu/backups/appsbilling-before-master-tenant-login-20260716-000449.tar.gz`

Verification:
- Local master login test passed.
- Live master login test passed using an active No Akun and `ananta`.
- Live dashboard showed `Mode Admin Pusat`.
- `/v3/login.php` remained verified with e-Billing DSG marker.
- `/nms/` remained responsive.

## Stage 1.6 — Tenant UI aligned to AppsBilling V3 (2026-07-16)

Status: completed and live.

Correction from user:
- Tenant app should look and behave like the existing AppsBilling V3 at `/v3`, not like a different platform shell.
- Each tenant should get the same functions/menus, with No Akun as identity and a separate empty DB.

Implemented safely:
- Tenant dashboard now uses an AppsBilling V3-like/AdminLTE layout: yellow topbar, dark sidebar, e-Billing DSG branding, grouped V3 menu structure, stat cards, breadcrumb/header, and footer.
- Tenant menu mirrors the major V3 groups:
  - Dashboard
  - Kelola data
  - Admin sistem
  - Corporate
  - Kelola pembayaran
  - Kelola PPPoE
- Tenant header/topbar shows No Akun.
- Dashboard explicitly shows tenant instance as `Tenant kosong · DB terpisah`.
- Data pelanggan page uses V3-style table and empty state while reading from tenant DB.
- Branding/logo settings moved visually into the V3-style Admin sistem flow via `page=branding-settings`.
- Direct `/tenant/settings.php` still works and renders with the V3-style layout.
- Existing master admin login remains supported and shows `Mode Admin Pusat`.

Important scope note:
- This stage aligns the tenant shell/UI/menus with AppsBilling V3 and keeps data isolated.
- Full V3 feature parity is still a staged port: each menu must be wired to the tenant DB without touching the original live `/v3` instance.
- Original `/v3` remains preserved and untouched.

Backup before deploy:
- `/home/ubuntu/backups/appsbilling-before-tenant-v3-style-20260716-002005.tar.gz`

Verification:
- Local syntax checks passed.
- Local master tenant login passed.
- Local tenant dashboard markers verified: e-Billing DSG, Kelola data, No Akun, Mode Admin Pusat, empty customer page, branding page.
- Live deploy syntax checks passed.
- Live master tenant login passed using active No Akun.
- Live tenant dashboard verified with V3-style markers: e-Billing DSG System, Billing Management System, Kelola data, Kelola pembayaran, Kelola PPPoE, No Akun, Tenant kosong.
- Live data-warga and branding pages verified.
- Original `/v3/login.php` still verified with e-Billing DSG marker.
- `/nms/` still responds normally.

## Stage 1.7 — Product Decision: Full V3 Feature Parity for Commercial Tenants (2026-07-16)

Status: documented; implementation roadmap created.

User decision:
- `/v3/` is the personal/internal AppsBilling instance.
- Root `https://appsbilling.dentasejahteragroup.my.id/` is the commercial multi-tenant platform.
- Every tenant in commercial platform must get all AppsBilling V3 features and the full admin tutorial.
- Each tenant uses its own No Akun, isolated empty DB, and custom app/receipt logo.
- Commercial tenant should not be a different app; it should be V3 feature parity adapted to tenant DB isolation.

Documentation updated:
- `docs/COMMERCIAL_PLATFORM_BLUEPRINT.md`
- `docs/V3_FEATURE_PARITY_ROADMAP_20260716.md`
- `docs/TRACKER.md`

Coret:
- Commercial platform Coret board must be updated to reflect V3 private vs commercial tenant separation and full V3 feature parity roadmap.

Implementation status:
- V3-style tenant shell/menu already live.
- Full module porting is pending and should be handled in safe phases:
  1. Core billing harian: pelanggan, paket, tagihan, pembayaran, kwitansi.
  2. Operasional ISP: tunggakan, free/off, diskon/deposit, penagihan.
  3. Admin/tutorial: users, office settings, full tutorial, activity log.
  4. Advanced: corporate, PPPoE, import/export, tenant-safe PWA.


## Stage 1.8 — Final Tenant Model Clarification: V3 Clone + Empty Operational DB (2026-07-16)

Status: documented.

User clarified:
- Tenant is not merely V3-style or loosely feature-parity.
- Tenant must be a clone of `https://appsbilling.dentasejahteragroup.my.id/v3` per No Akun/slug.
- UI, menus, functions, flows, validation, and admin tutorial should be the same as V3.
- The only difference is data context: each tenant gets its own empty DB and custom logos.

DB empty means:
- Operational data is empty per tenant: pelanggan, tipe pembayaran/paket, tagihan, pembayaran, router, rekening, lokasi, deposit, diskon, PPPoE, instalasi, tiket, corporate, collection/batch, etc.
- Minimal seed/settings are allowed only so the app runs: tenant identity, tenant user, schema version, default copyright/branding.

Implementation implication:
- Build/port a tenant-aware V3 clone engine.
- Do not invent a separate tenant UX.
- Keep private `/v3` separate and untouched.

## Stage 2.1 — Tenant V3 Core CRUD: Tipe Pembayaran + Rekening (2026-07-16)

Status: completed and live.

User concern:
- Tenant features were still not 100% functional; menus like `Tambah tipe pembayaran` and `Tambah rekening` were placeholders.
- User expects tenant system to function like `/v3`, with each No Akun/slug having its own empty DB.

Implemented:
- Tenant DB schema now migrates toward V3 core compatibility for:
  - `packages`: `name`, `speed`, `price`, `is_active`, `status`, `created_at`.
  - `bank_accounts`: `bank_name`, `account_number`, `account_name`, `label`, `is_active`, `notes`, `created_at`.
- Existing tenant DBs auto-migrate safely when tenant dashboard loads.
- `tenant/dashboard.php?page=add-package` is now a real V3-style form and saves to that tenant DB.
- `tenant/dashboard.php?page=data-tipe-pembayaran` now lists saved tenant packages/tipe pembayaran with active/inactive state and customer count.
- `tenant/dashboard.php?page=add-bank` is now a real V3-style form and saves to that tenant DB.
- `tenant/dashboard.php?page=data-rekening` now lists saved tenant bank accounts/rekening.
- Basic archive package and delete bank actions added.
- Actions log to platform tenant events.

Backup before deploy:
- `/home/ubuntu/backups/appsbilling-before-tenant-package-bank-20260716-004257.tar.gz`

Verification:
- Local syntax passed.
- Local master tenant login passed.
- Local add package → list package passed and data stored in tenant DB.
- Local add bank → list bank passed and data stored in tenant DB.
- Live deploy syntax passed.
- Live add package → list package passed using active tenant No Akun.
- Live add bank → list bank passed using active tenant No Akun.
- Original `/v3/login.php` remained verified.
- `/nms/` remained responsive.

Next porting priority:
- Data pelanggan + tambah/edit pelanggan.
- Tagihan.
- Pembayaran.
- Kwitansi/print with tenant logo.

## Stage 2.2 — Tenant V3 Core CRUD: Data Pelanggan + Tambah Pelanggan (2026-07-16)

Status: completed and live.

Implemented:
- Tenant customer schema migrated toward V3 fields:
  - `registered_at`, `due_day`, `is_active`, `customer_status`, `area_name`, `latitude`, `longitude`, `map_note`, `router_name`, `pppoe_username`, `onu_name`, `notes`, `updated_at`.
- Existing tenant DBs auto-migrate on dashboard load.
- Added `customer_events` for tenant customer audit events.
- Added real customer form for:
  - `/tenant/dashboard.php?page=add-warga`
  - `/tenant/dashboard.php?page=add-pelanggan-off`
- Added real customer lists for:
  - `/tenant/dashboard.php?page=data-warga`
  - `/tenant/dashboard.php?page=data-pelanggan-free`
  - `/tenant/dashboard.php?page=data-pelanggan-off`
- Customer list supports search by ID, name, address, phone, PPPoE username, and secret.
- Customer save/delete actions write to tenant DB only.
- ID customer auto-generates as `DN + 10 digits` when empty, matching V3 direction.

Backup before deploy:
- `/home/ubuntu/backups/appsbilling-before-tenant-customers-20260716-005916.tar.gz`

Verification:
- Local syntax passed.
- Local tenant login passed.
- Local add customer passed.
- Local customer list/search by PPPoE passed.
- Live deploy syntax passed.
- Live add customer passed using active tenant No Akun.
- Live customer list/search by PPPoE passed.
- `/v3/login.php` remained verified.
- `/nms/` remained responsive.

Next porting priority:
- Tagihan/invoice generation and manual add-tagihan.
- Pembayaran tenant with package price, bank account selection, duplicate guard, and receipt/print using tenant logo.
