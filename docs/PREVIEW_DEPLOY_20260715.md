# Preview Deploy — 2026-07-15

Commercial MVP preview is live at:

`https://appsbilling.dentasejahteragroup.my.id/commercial-preview/`

## Important Boundary

This preview does **not** touch:

- `/v3`
- `/nms`

Public files are in:

`/var/www/appsbilling.dentasejahteragroup.my.id/commercial-preview`

Engine, `.env`, storage, and tenant databases are outside public path:

`/var/www/appsbilling-commercial-platform`

## Working Flow

1. Mitra registers at `/commercial-preview/register.php`.
2. Tenant status starts as `pending`.
3. Superadmin logs in at `/commercial-preview/superadmin/login.php`.
4. Superadmin approves tenant.
5. Tenant DB is provisioned in `storage/tenants/{tenant_uid}/billing.sqlite`.
6. Tenant logs in at `/commercial-preview/tenant/login.php`.
7. Tenant sees dashboard shell with isolated DB stats.

## Superadmin

Username: `ananta`.
Password is set server-side in `.env` as a hash and is not committed.

## Smoke Test Result

Public end-to-end test passed:

- landing HTTP 200;
- register HTTP 200;
- superadmin login HTTP 200;
- tenant login HTTP 200;
- registration POST 302;
- superadmin login POST 302;
- approve POST 302;
- tenant login POST 302;
- tenant dashboard contains `Dashboard Billing Mitra` and test tenant name;
- DB summary after test: `tenants=1`, `active=1`, `dbs=1`.
