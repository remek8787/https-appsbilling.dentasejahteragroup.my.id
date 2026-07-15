# Root Cutover — 2026-07-15

Commercial platform is now live at the main root:

`https://appsbilling.dentasejahteragroup.my.id/`

## Boundaries Kept

- `/v3` was not touched.
- `/nms` was not touched.
- `/v2` was preserved.
- `/commercial-preview` was preserved.

## Backup

Before cutover:

`/home/ubuntu/backups/appsbilling-root-before-commercial-cutover-20260715-232009.tar.gz`

Legacy root files moved to:

`/home/ubuntu/backups/appsbilling-root-legacy-20260715-232009/`

## Runtime Layout

Public root:

`/var/www/appsbilling.dentasejahteragroup.my.id/`

Commercial engine/storage outside public root:

`/var/www/appsbilling-commercial-platform/`

## Verified URLs

- `https://appsbilling.dentasejahteragroup.my.id/`
- `https://appsbilling.dentasejahteragroup.my.id/register.php`
- `https://appsbilling.dentasejahteragroup.my.id/superadmin/login.php`
- `https://appsbilling.dentasejahteragroup.my.id/tenant/login.php`
- `https://appsbilling.dentasejahteragroup.my.id/v3/login.php`
- `https://appsbilling.dentasejahteragroup.my.id/nms/`

## Smoke Test

Root flow tested successfully:

registration → superadmin login → approve tenant → tenant DB provisioning → tenant login → dashboard.

Current shell still only provides tenant dashboard shell. Next stage must implement full tenant billing modules from AppsBilling V3 without touching existing `/v3` and `/nms`.
