# Human Polish + No Akun 4 Digit — 2026-07-15

Live root:

`https://appsbilling.dentasejahteragroup.my.id/`

## Summary

The commercial root has been redesigned to feel more like a real ISP billing product and less like a generic AI template.

## Changes

- Landing page copy and layout rewritten with a clearer ISP/operator tone.
- Added a registration tutorial section:
  1. Isi form pendaftaran
  2. Simpan No Akun
  3. Tunggu approval
  4. Masuk dashboard
- Examples changed to `MRT NET`.
- Tenant registration now creates a unique random 4-digit `No Akun`.
- Internal slug is now `acct-{account_no}`.
- Tenant login uses `No Akun Mitra` + username + password.
- Superadmin dashboard shows each tenant's No Akun.
- Tenant dashboard shows No Akun.

## Safety

- `/v3` was not touched.
- `/nms` was not touched.
- Backup created before deploy:
  `/home/ubuntu/backups/appsbilling-root-before-human-polish-20260715-234038.tar.gz`

## Verification

Live E2E passed:

registration → account no generated → approve tenant → tenant login by account no → dashboard.

Additional checks:

- `/` HTTP 200
- `/register.php` HTTP 200
- `/tenant/login.php` HTTP 200
- `/superadmin/login.php` HTTP 200
- `/v3/login.php` HTTP 200 with `e-Billing DSG` marker
- `/nms/` responds with expected redirect
- DB migration/backfill: all existing tenants now have account numbers.
