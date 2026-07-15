# AppsBilling Commercial — V3 Feature Parity Roadmap

Live root: `https://appsbilling.dentasejahteragroup.my.id/`

## Keputusan Produk

- `/v3/` adalah AppsBilling pribadi/internal Tuan Besar/PT Denta Sejahtera Group.
- Root commercial platform adalah produk multi-tenant untuk mitra.
- Setiap tenant harus mendapatkan fitur dan tutorial admin seperti AppsBilling V3.
- Setiap tenant memakai No Akun, DB sendiri, dan logo sendiri.
- `/v3/` tidak boleh dicampur atau rusak saat fitur dipindahkan ke commercial.

## Target Akhir

Setelah tenant login, operator mitra melihat aplikasi yang terasa seperti AppsBilling V3 lengkap, bukan platform shell. Semua data masuk ke SQLite tenant masing-masing.

## Modul yang Harus Ada di Setiap Tenant

### 1. Dashboard

- Statistik pelanggan aktif/nonaktif/free.
- Statistik tagihan, pembayaran, tunggakan.
- Ringkasan pendapatan.
- Analisa pendapatan.
- Track record pendapatan.
- Empty state untuk tenant baru.

### 2. Kelola Data

- Data pelanggan aktif.
- Tambah pelanggan.
- Edit/detail/hapus aman pelanggan.
- Pelanggan FREE/Gratis.
- Pelanggan OFF/Nonaktif.
- Tambah pelanggan OFF.
- Data diskon.
- Data deposit pelanggan.
- Data lokasi.
- Data router.
- Data tipe pembayaran/paket.
- Tambah tipe pembayaran/paket.
- Data rekening.
- Tambah rekening.

### 3. Admin Sistem

- Data user tenant.
- Tambah user tenant.
- Pengaturan kantor.
- Logo aplikasi tenant.
- Logo kwitansi tenant.
- Tutorial admin lengkap.
- Log aktivitas tenant.
- Copyright tetap: `PT Denta Sejahtera Group dan Ananta Satriya Ferdian`.

### 4. Corporate

- Data corporate.
- Tambah corporate.
- Tagihan corporate.
- Buat tagihan corporate.

### 5. Kelola Pembayaran

- Pembayaran langganan.
- Tambah pembayaran.
- Pembayaran umum.
- Data tagihan.
- Tambah tagihan.
- Sudah bayar.
- Belum bayar/tunggakan multi-periode.
- Tim penagihan.
- Batch penagihan.

### 6. Kelola PPPoE

- Data user PPPoE.
- Data sesi PPPoE.
- Data instalasi.
- Tiket pelanggan.

### 7. Kwitansi / Print / Export

- Print kwitansi tenant dengan logo tenant.
- Invoice/kwitansi pelanggan.
- Export/import bila modul V3 mendukung.
- PWA/branding tenant-safe bila sudah stabil.

## Strategi Porting Aman

1. Jangan edit live `/v3/`.
2. Ambil fungsi V3 sebagai referensi source.
3. Pindahkan bertahap ke commercial tenant dengan resolver DB tenant.
4. Semua query tenant wajib menggunakan DB tenant, bukan DB V3 pribadi.
5. Setiap modul harus punya smoke test tenant baru dengan DB kosong.
6. Setiap deploy wajib preserve:
   - `/v3/`
   - `/nms/`
   - `.env`
   - `storage/`
   - `/uploads/tenants/`

## Prioritas Implementasi

### Phase A — Core Billing Harian

1. Data pelanggan aktif + tambah/edit/detail.
2. Paket/tipe pembayaran.
3. Tagihan.
4. Pembayaran.
5. Kwitansi/print dengan logo tenant.

### Phase B — Operasional ISP

1. Belum bayar/tunggakan.
2. Sudah bayar.
3. Pelanggan free/off.
4. Diskon/deposit.
5. Tim penagihan dan batch.

### Phase C — Admin dan Tutorial

1. Data user tenant.
2. Pengaturan kantor.
3. Tutorial admin lengkap berbasis alur kerja AppsBilling V3.
4. Log aktivitas.

### Phase D — Advanced

1. Corporate billing.
2. PPPoE/session/installasi/tiket.
3. Import/export.
4. PWA/manifest tenant-safe.

## Current Live State 2026-07-16

Already live:

- Root commercial platform.
- No Akun 4 digit.
- Admin approval/disable/reactivate.
- Admin detail tenant.
- Master admin login to active tenant No Akun.
- Tenant V3-style shell/menu/dashboard.
- Tenant logo app and logo kwitansi upload.
- Tenant DB isolated and initially empty.

Still pending:

- Full V3 module logic port into tenant DB.
- Complete tutorial admin inside tenant app.
- Complete kwitansi/logo integration across print flows.
