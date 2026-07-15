---
title: "AppsBilling Commercial Platform Blueprint"
description: "Blueprint SaaS/multi-mitra untuk AppsBilling komersial di root appsbilling.dentasejahteragroup.my.id."
project: "https-appsbilling.dentasejahteragroup.my.id"
created: "2026-07-15"
updated: "2026-07-16"
tags: [appsbilling, commercial, saas, multi-tenant, mitra, billing-v3, php, sqlite]
---

# AppsBilling Commercial Platform Blueprint

## Ringkasan

Project baru untuk menjadikan AppsBilling V3 sebagai platform komersial multi-mitra pada domain:

- Domain utama: `https://appsbilling.dentasejahteragroup.my.id`
- Repo GitHub: `git@github.com:remek8787/https-appsbilling.dentasejahteragroup.my.id.git`
- Workspace lokal: `/root/.openclaw/workspace/projects/https-appsbilling.dentasejahteragroup.my.id`
- Server target saat ini: VM `43.134.122.109`
- Web root live saat ini: `/var/www/appsbilling.dentasejahteragroup.my.id`

Tujuan utama: platform billing komersial seperti AppsBilling V3 DSG/Borneo, tetapi setiap mitra/client punya akun, ruang kerja, branding, dan database masing-masing.

## Keputusan Arsitektur 2026-07-16 — V3 Pribadi vs Commercial Tenant

Tuan Besar menetapkan pemisahan produk yang wajib diikuti:

- **AppsBilling DSG V3 di `/v3/` adalah versi pribadi/internal** milik Tuan Besar/PT Denta Sejahtera Group.
- **Root `https://appsbilling.dentasejahteragroup.my.id/` adalah AppsBilling Commercial Platform** untuk mitra/tenant.
- Commercial platform tidak boleh hanya menjadi shell berbeda. Setiap tenant harus mendapatkan pengalaman dan fungsi yang sama seperti AppsBilling V3.
- Perbedaan tenant commercial hanya pada:
  - identitas login memakai **No Akun**;
  - setiap tenant punya **database sendiri** yang awalnya kosong;
  - setiap tenant bisa memakai **logo aplikasi dan logo kwitansi sendiri**;
  - admin pusat bisa masuk ke tenant aktif untuk bantuan/setup;
  - `/v3/` pribadi tetap tidak disentuh/dicampur.

Prinsip produk: **AppsBilling V3 pribadi menjadi referensi/master flow; commercial tenant adalah versi multi-tenant yang fiturnya dipindahkan lengkap ke DB tenant masing-masing.**


## Keputusan Final Tenant Model 2026-07-16 — Clone V3 dengan DB Kosong

Definisi tenant commercial yang benar:

- Tenant commercial adalah **clone AppsBilling V3 per No Akun/slug**, bukan aplikasi baru yang hanya mirip.
- UI, menu, fitur, alur kerja, validasi, halaman, dan tutorial admin harus sama seperti `https://appsbilling.dentasejahteragroup.my.id/v3`.
- Perbedaan hanya pada konteks data dan branding:
  - setiap tenant memakai **No Akun/slug sendiri**;
  - setiap tenant memakai **SQLite DB sendiri**;
  - DB tenant dibuat dari schema/template V3 tetapi **data operasional kosong**;
  - setiap tenant bisa upload logo aplikasi dan logo kwitansi sendiri;
  - admin pusat bisa login ke tenant aktif untuk bantuan/setup.

Yang dimaksud DB kosong:

- Kosong: data pelanggan, data tipe pembayaran/paket, tagihan, pembayaran, router, rekening, lokasi, deposit, diskon, user PPPoE, sesi PPPoE, instalasi, tiket, corporate, batch penagihan, dan data operasional lain.
- Boleh berisi seed minimal yang dibutuhkan aplikasi agar berjalan aman, misalnya setting dasar tenant, akun user tenant, schema version, dan default copyright.
- Data contoh/demo dari `/v3` pribadi tidak boleh ikut masuk ke tenant commercial kecuali nanti diminta eksplisit.

Prinsip implementasi: **copy/port V3 sebagai engine tenant-aware**, lalu semua akses database diarahkan ke DB tenant berdasarkan No Akun/slug/session tenant. `/v3` pribadi tetap menjadi instance terpisah dan tidak disentuh.

## Keputusan Awal

- AppsBilling DSG V3 (`/v3/`) dan Borneo V3 tetap menjadi referensi fitur/operator flow.
- Root domain akan menjadi platform komersial baru, bukan lagi project root lama.
- Root lama boleh diganti/destroy **setelah backup dan konfirmasi final dari Tuan Besar**.
- `/v3/` DSG yang sudah live tidak boleh rusak saat cutover root, kecuali nanti diputuskan untuk migrasi/rename jalur.
- Gunakan Coret baru agar konsep komersial tidak tercampur dengan Coret AppsBilling DSG/Borneo existing.

## Persona dan Role

### Superadmin Platform

- Akun pusat milik Tuan Besar.
- Username awal: `ananta`.
- Password awal diberikan lewat chat pribadi, tetapi **tidak boleh ditulis plaintext di repo/dokumen**.
- Password harus disimpan sebagai hash `password_hash()` PHP (`PASSWORD_DEFAULT`) atau Argon2id jika environment mendukung.
- Fitur:
  - login panel pusat;
  - lihat semua mitra/client;
  - accept/approve registrasi;
  - disable/suspend account;
  - hapus account dengan guard/konfirmasi keras;
  - impersonate/login-as bila nanti disetujui;
  - lihat ringkasan tenant: status, jumlah pelanggan, invoice, storage, last login.

### Mitra / Client

- Registrasi dari menu utama.
- Status awal: `pending`.
- Setelah disetujui superadmin, tenant workspace dibuat/diaktifkan.
- Punya Billing V3 sendiri:
  - pelanggan;
  - paket;
  - tagihan;
  - pembayaran;
  - invoice/kwitansi;
  - tim penagihan;
  - import/export;
  - analisa pendapatan;
  - PWA/branding tenant.

## Arsitektur Data

### Model yang Direkomendasikan: DB Terpisah per Tenant

Platform memiliki satu DB pusat untuk akun/tenant, lalu setiap tenant punya DB sendiri.

Contoh:

```text
storage/platform.sqlite
storage/tenants/{tenant_uid}/billing.sqlite
```

Alasan:

- Lebih mudah backup/restore per mitra.
- Risiko data bocor antar tenant lebih kecil.
- Lebih mudah suspend/delete satu tenant.
- Cocok dengan AppsBilling V3 yang sudah berbasis SQLite.

### Platform DB Minimal

Tabel awal yang dibutuhkan:

- `platform_admins`
  - id, username, password_hash, name, status, last_login_at
- `tenants`
  - id, tenant_uid, company_name, owner_name, phone, email, subdomain_or_slug, status, plan, created_at, approved_at, disabled_at
- `tenant_users`
  - id, tenant_id, username, password_hash, name, role, status
- `tenant_events`
  - id, tenant_id, actor_type, actor_id, event_type, notes, created_at
- `tenant_databases`
  - id, tenant_id, db_path, schema_version, last_backup_at

### Status Tenant

- `pending` — registrasi masuk, belum aktif.
- `active` — sudah disetujui dan bisa login.
- `disabled` — login diblokir sementara, data tetap aman.
- `deleted` — soft delete / archived dulu, hard delete hanya setelah backup.

## URL dan Routing Awal

- `/` — landing platform AppsBilling komersial.
- `/register.php` — registrasi mitra/client.
- `/superadmin/login.php` — login superadmin.
- `/superadmin/index.php` — dashboard superadmin.
- `/superadmin/tenants.php` — approve/disable/delete tenant.
- `/tenant/login.php` — login mitra/client.
- `/tenant/{tenant_slug}/` atau `/app.php?t={tenant_uid}` — masuk workspace tenant.

Catatan: pilihan final routing perlu melihat limit shared/VM/nginx. Untuk fase awal, route query/slug sederhana lebih aman daripada subdomain wildcard.

## Fitur Tenant Billing V3

Target feature parity dari AppsBilling V3:

- Dashboard operator.
- Data pelanggan.
- Paket dan master data.
- Tagihan bulanan otomatis saat bulan berjalan diakses.
- Pembayaran dan kwitansi.
- Belum lunas/tunggakan multi-periode.
- Tim penagihan.
- Import/export XLSX.
- Analisa pendapatan dan piutang.
- Branding kantor/kwitansi tenant.
- PWA icon/manifest tenant-safe.

## Branding / Komersial

Platform root harus terasa sebagai produk komersial, bukan dashboard internal biasa.

Arah UI:

- Premium ISP billing platform.
- Landing ringkas: solusi billing, multi-tenant, aman, cepat dipakai.
- CTA: Registrasi Mitra dan Login.
- Superadmin UI: operational control center.
- Tenant UI: tetap familiar seperti AppsBilling V3 agar operator tidak perlu belajar ulang.

## Security Boundary

- Jangan commit password plaintext.
- Jangan commit DB tenant.
- Jangan commit `.env` live.
- Semua tenant request wajib resolve tenant context dulu sebelum akses DB.
- Superadmin delete harus soft-delete + backup dulu.
- Disable tenant tidak boleh menghapus data.
- Hard delete wajib butuh konfirmasi eksplisit Tuan Besar atau UI guard bertingkat.
- Session superadmin dan tenant dipisah.

## Cutover Plan Root Domain

Root live saat ini masih berisi project lama, `/v2/`, `/v3/`, `nms/`, dan data lama.

Rencana aman:

1. Build platform baru di workspace lokal.
2. Push GitHub repo baru.
3. Deploy preview ke path aman dulu, misalnya `/commercial-preview/` atau `/platform-preview/`.
4. Backup penuh root live:
   - source tar.gz;
   - data SQLite lama;
   - nginx config snapshot.
5. Setelah Tuan Besar approve, root `/` diganti ke platform baru.
6. `/v3/` DSG tetap bisa dipertahankan sebagai tenant/reference atau dipindah sesuai keputusan.
7. Smoke test:
   - landing;
   - superadmin login;
   - registrasi mitra;
   - approve tenant;
   - tenant login;
   - tenant billing dashboard;
   - DB tenant terpisah.

## Open Questions

1. Root `/v3/` DSG existing mau tetap dipertahankan sebagai instance internal, atau nanti dimigrasikan menjadi tenant pertama?
2. Tenant access lebih disukai pakai slug path (`/t/borneo`) atau subdomain (`borneo.appsbilling...`) nanti?
3. Setelah registrasi, apakah tenant langsung dapat trial dashboard kosong setelah approve, atau superadmin yang buat username/password tenant?
4. Apakah billing komersial butuh plan/subscription platform di fase awal, atau cukup status active/disabled dulu?
5. Apakah fitur “hapus account” harus hard delete, atau soft delete + archive dulu sebagai default aman?

## Rekomendasi Alvii

Mulai dari **MVP SaaS aman**:

1. Superadmin platform.
2. Registrasi mitra pending.
3. Approve/disable tenant.
4. Create isolated SQLite DB per tenant from V3 schema/template.
5. Tenant login dan dashboard V3 kosong.
6. Import/export dan billing flow standar.
7. Baru setelah stabil: plan/subscription, custom domain/subdomain, billing platform, dan tenant PWA branding.

## Status Implementasi Tenant V3 — 2026-07-16 01:36 WITA

Arahan terakhir Tuan Besar:

- Fokus utama: **semua fungsi/fitur inti AppsBilling V3 bisa digunakan oleh tiap tenant**.
- Jangan polish kecil dulu sampai fitur utama tenant usable.
- Setelah batch admin-system selesai, project ini dipause dan akan pindah ke project berikutnya.

Status live saat ini:

- Root commercial platform live di `https://appsbilling.dentasejahteragroup.my.id/`.
- Engine/storage tetap di `/var/www/appsbilling-commercial-platform`.
- Public root tenant files di `/var/www/appsbilling.dentasejahteragroup.my.id/tenant`.
- `/v3/` pribadi/internal masih utuh dan wajib tetap tidak disentuh.
- `/nms/` masih utuh dan wajib tetap tidak disentuh.

Modul tenant V3 yang sudah usable/live:

1. **Commercial root + tenant provisioning**
   - registrasi mitra;
   - admin approval/disable/reactivate/detail;
   - tenant DB SQLite terpisah per No Akun;
   - tenant login by No Akun + username + password;
   - master admin pusat `ananta` bisa login ke tenant aktif memakai password admin pusat.

2. **Tenant V3 shell/menu**
   - layout V3-like/AdminLTE;
   - menu utama V3: Dashboard, Kelola data, Admin sistem, Corporate, Kelola pembayaran, Kelola PPPoE;
   - branding/logo tenant di flow admin sistem.

3. **Kelola data dasar**
   - paket/tipe pembayaran: `data-tipe-pembayaran`, `add-package`;
   - rekening: `data-rekening`, `add-bank`;
   - pelanggan aktif/free/off: `data-warga`, `add-warga`, `data-pelanggan-free`, `data-pelanggan-off`, `add-pelanggan-off`.

4. **Billing/payment core**
   - tagihan: `data-tagihan`, `add-tagihan`, generator invoice bulanan;
   - pembayaran: `data-ipl`, `add-laporan-ipl`, `data-sudah-bayar`, `data-belum-bayar-7121`;
   - kwitansi: `/tenant/print.php?type=receipt&id=...`;
   - status invoice unpaid/partial/paid dan balance otomatis dihitung ulang.

5. **Corporate core**
   - `corporate-customers`;
   - `add-corporate-customer`;
   - `corporate-invoices`;
   - `add-corporate-invoice`;
   - mark payment corporate sampai status lunas.

6. **Admin sistem core**
   - user tenant: `data-user`, `add-user`;
   - role tenant: owner/admin/operator/collector/viewer;
   - active/inactive toggle;
   - office setting tenant: `office-settings`;
   - logo/branding route preserved: `branding-settings` via `tenant/settings.php`;
   - activity log tenant: `activity-log`.

Backup live penting dari batch terakhir:

- `/home/ubuntu/backups/appsbilling-before-tenant-billing-20260716-010931.tar.gz`
- `/home/ubuntu/backups/appsbilling-before-tenant-corporate-20260716-012007.tar.gz`
- `/home/ubuntu/backups/appsbilling-before-tenant-admin-system-20260716-012732.tar.gz`

Commit GitHub penting:

- `903b544` — tenant customer CRUD.
- `58b8e19` — tenant billing/payment/receipt core.
- `7096989` — tenant corporate billing core.
- `31a1484` — tenant admin-system core.

Gotcha implementasi yang wajib diingat:

- Tenant DB lama sering sudah punya tabel, jadi **jangan mengandalkan `CREATE TABLE IF NOT EXISTS` saja**. Selalu tambahkan migrasi eksplisit/ALTER lewat helper schema.
- Schema baru harus masuk ke flow provisioning tenant baru **dan** `tenant_ensure_v3_core_schema()` agar tenant lama ikut termigrasi.
- Live tenant file memakai public root `/var/www/appsbilling.dentasejahteragroup.my.id/tenant`, sementara core bootstrap ada di `/var/www/appsbilling-commercial-platform/app/bootstrap.php`; file standalone seperti `print.php` perlu fallback path bootstrap.
- Jangan deploy AppsBilling commercial via FTP billing lama. Correct live target adalah VM `43.134.122.109`.

Next recommended when project resumes:

1. **Kelola PPPoE core**
   - `data-user-pppoe`;
   - `data-session-ppoe`;
   - `data-installasi`;
   - `dashboard-ticket-pelanggan`.
2. Lanjut modul operasional kecil V3:
   - diskon/deposit;
   - lokasi/router;
   - pembayaran umum;
   - collection team;
   - dashboard income summary / income analysis.
3. Baru setelah semua fungsi inti usable: polish kecil tenant/UI/tutorial lengkap.

