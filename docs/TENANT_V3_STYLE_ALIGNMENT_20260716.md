# Tenant V3 Style Alignment — 2026-07-16

Live root: `https://appsbilling.dentasejahteragroup.my.id/`

## Reason

The tenant area must not look like a separate shell. It should feel like the existing AppsBilling V3 app at `/v3`, with the key commercial-platform difference:

- tenant identity uses No Akun;
- each tenant has its own isolated empty DB;
- central admin can enter tenant workspaces for support;
- original `/v3` remains untouched.

## Implemented

Tenant area now uses a V3-style layout:

- yellow topbar;
- dark AdminLTE-like sidebar;
- e-Billing DSG branding;
- grouped V3 menu structure;
- dashboard stat cards;
- breadcrumb/content wrapper style;
- footer matching e-Billing DSG System.

Major menu groups mirrored from V3:

- Dashboard
- Kelola data
- Admin sistem
- Corporate
- Kelola pembayaran
- Kelola PPPoE

The tenant topbar shows `No Akun`, and the dashboard shows `Tenant kosong · DB terpisah`.

## Data Isolation

Tenant pages read from tenant-specific SQLite DB under the tenant UID folder.

At this stage, the shell/menu is aligned to V3 and selected views are safely connected to tenant DB:

- dashboard statistics;
- data pelanggan active list/empty state;
- branding/logo settings.

Other V3 modules are shown as V3-style empty/module placeholders until their full logic is ported to tenant DB.

## Compatibility

- `/tenant/dashboard.php?page=branding-settings` is the main V3-style branding route.
- `/tenant/settings.php` remains compatible and renders the same V3-style layout.
- Master admin login (`ananta` verified against central admin hash) remains supported.

## Safety

Original live `/v3` was not changed. `/nms` was not changed.

Backup before deploy:

`/home/ubuntu/backups/appsbilling-before-tenant-v3-style-20260716-002005.tar.gz`

## Verification

Live smoke test passed:

- tenant login with active No Akun + master admin;
- dashboard contains e-Billing DSG/V3-style markers;
- V3 menu groups appear;
- data pelanggan page reads tenant DB and shows empty state;
- branding page renders with copyright;
- direct `/tenant/settings.php` compatibility works;
- original `/v3/login.php` and `/nms/` still respond normally.
