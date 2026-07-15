---
title: "AppsBilling Commercial Platform Blueprint"
description: "Blueprint SaaS/multi-mitra untuk AppsBilling komersial di root appsbilling.dentasejahteragroup.my.id."
project: "https-appsbilling.dentasejahteragroup.my.id"
created: "2026-07-15"
updated: "2026-07-15"
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

