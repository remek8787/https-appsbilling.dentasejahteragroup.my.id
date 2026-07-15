# Admin Detail + Tenant Branding Logos — 2026-07-15

Live root: `https://appsbilling.dentasejahteragroup.my.id/`

## Admin Detail

New page:

`/superadmin/tenant_detail.php?id={tenant_id}`

Visible label remains Admin, but route remains `/superadmin/` for safety.

Admin can see:

- company/mitra name;
- No Akun;
- tenant UID;
- internal slug;
- PIC/contact;
- status;
- DB status/path/schema;
- simple stats;
- tenant users;
- recent activity;
- approve / disable / reactivate controls.

## Inactive Account Login Notice

If a tenant account is pending/disabled/deleted, tenant login shows:

`Silahkan lakukan konfirmasi ke sisi admin Ananta Satriya. WhatsApp: 085804783530`

## Tenant Branding

New page:

`/tenant/settings.php`

Tenant can upload:

- app logo;
- receipt logo.

Accepted formats:

- JPG
- PNG

Limits:

- max 5MB before processing;
- target stored logo max around 512KB;
- if GD is installed, image is resized/compressed;
- if GD is not installed, only already-small valid files are accepted.

Public logo path:

`/uploads/tenants/{tenant_uid}/...`

Copyright shown:

`PT Denta Sejahtera Group dan Ananta Satriya Ferdian`

## Backup

`/home/ubuntu/backups/appsbilling-root-before-admin-detail-logo-20260715-235651.tar.gz`

## Verification

Live E2E passed:

register → inactive login notice → admin detail → approve → tenant login → logo settings → upload logo → public logo URL HTTP 200.

`/v3` and `/nms` were not touched and were verified after deploy.
