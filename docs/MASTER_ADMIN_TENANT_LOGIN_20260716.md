# Master Admin Tenant Login — 2026-07-16

Live root: `https://appsbilling.dentasejahteragroup.my.id/`

## Behavior

Central admin can login to any active tenant workspace from tenant login page:

- No Akun Mitra: any active tenant account number
- Username: `ananta`
- Password: central admin password, verified against `platform_admins.password_hash`

No plaintext password is stored in code or docs.

## Session

Master admin tenant session uses:

- `tenant_master_admin`
- `tenant_master_username`
- `tenant_master_tenant_id`

Dashboard displays:

`Mode Admin Pusat`

## Safety

- Does not create a tenant user record.
- Logs event as `tenant_master_login` with actor type `platform_admin`.
- Pending/disabled/deleted tenants remain blocked.
- Normal tenant user login remains unchanged.

## Backup

`/home/ubuntu/backups/appsbilling-before-master-tenant-login-20260716-000449.tar.gz`

## Verification

Live smoke test passed:

No Akun active tenant + `ananta` + central admin password → tenant dashboard → `Mode Admin Pusat` visible.

`/v3` and `/nms` were verified after deploy.
