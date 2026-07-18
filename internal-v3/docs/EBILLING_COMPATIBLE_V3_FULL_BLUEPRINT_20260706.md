# AppsBilling V3 Full eBilling-Compatible Blueprint

Date: 2026-07-06
Live preview: `https://appsbilling.dentasejahteragroup.my.id/v3/`

## Mission
Build AppsBilling V3 as an **eBilling-compatible operator workspace**: same practical billing workflow, menu mental model, page naming, table density, filters, detail/edit/print patterns, and day-to-day ISP/RTRW-net operations.

Safety boundary:
- Do not copy proprietary eBilling source/assets.
- Implement original PHP/SQLite code that follows the observed workflow and data shape.
- Keep root AppsBilling and `/v2/` untouched unless Tuan Besar explicitly approves cutover.

## Design Direction
- AdminLTE/eBilling-like shell: yellow topbar, dark sidebar, dense operational tables.
- Operator-first, not generic dashboard.
- Every table must expose fast action buttons: Detail, Edit, Cetak, Hapus when relevant.
- Detail Pelanggan is the work center: profile, package, router/lokasi, Secret, Catatan, tagihan, tunggakan, payment history, events.
- Labels should be familiar to eBilling users, but AppsBilling-specific where needed.

## Canonical Modules

### 1. Dashboard
- Total pelanggan, aktif/off/isolir.
- Pembayaran bulan berjalan.
- Belum bayar/tunggakan.
- Estimasi piutang.
- Income summary.
- Dashboard should act as the admin/operator trigger board, not only KPI cards.
- Keep a visible announcement/reminder panel with natural Indonesian copy:
  - start from `Belum Bayar`
  - input payment after checking customer context
  - open `Detail` first when data looks odd
  - do not rush deleting old invoice/payment history
- Quick trigger links should stay easy to reach:
  - Data aktif
  - Kejar tunggakan / Belum Bayar
  - Track income
  - Tutorial/SOP Admin
  - Map pelanggan
  - Log aktivitas
- Quick action buttons:
  - Tambah pelanggan
  - Input pembayaran
  - Generate tagihan
  - Lihat belum bayar

### 1A. Announcement + Tutorial Copywriting Standard
- Admin announcement and tutorial copy must feel written for real billing operators, not generic AI/manual text.
- Use short practical sentences, field terms, and light reminders.
- Avoid stiff promotional wording, inflated claims, and template-sounding explanations.
- Tutorial Admin should remain an in-app SOP covering:
  - daily payment flow
  - discount/partial payment
  - customer action buttons
  - active/FREE/OFF/isolir meanings
  - Tikor/location flow
  - safe correction workflow
  - ID DN + receipt rules
  - corporate billing
  - changelog/update notes

### 2. Data Pelanggan / Data Warga
- Full customer table eBilling-like.
- Columns: ID, nama, alamat, telepon, paket, harga, lokasi, router, koneksi, status, username, password, Secret, registrasi, jatuh tempo, aksi.
- Add/edit customer must include:
  - customer_code
  - name
  - address
  - phone
  - package
  - registered_at
  - due_day
  - status
  - lokasi/area
  - router
  - username PPPoE
  - Secret (former Nama ONU) optional
  - Catatan optional
- Detail page must show:
  - profile identity
  - network/account block
  - billing summary
  - unpaid invoices/tunggakan
  - payment history
  - duplicate payment warning
  - customer events/follow-up notes

### 3. Pembayaran / IPL
- Add/edit/delete payments.
- Paid list per month.
- Unpaid list per month.
- Receipt print.
- Filters: month, q, location, package/router later.
- Payment should update invoice status.

### 4. Tagihan
- Invoice table.
- Add/edit/delete invoice.
- Generate bulk monthly invoices later.
- Print invoice.
- Tunggakan view must handle multi-month unpaid.

### 5. Master Data
- Paket / Tipe Pembayaran.
- Router.
- Lokasi.
- Rekening.
- User.
- ODP/installasi placeholders can stay but should become real pages later.
- Legacy `Kelola mapping` / OpenStreetMap-style workbench is currently hidden from sidebar and should stay hidden unless Tuan Besar explicitly asks to reopen it.

### 5A. Customer Actions + Tikor / Google Maps
- Customer action UI must stay compact and operator-safe in dense tables.
- Current stable action layout is vertical stack (`action-stack-v3`):
  - Bayar
  - Detail
  - Edit
  - Lokasi (only when valid coordinates exist)
  - Kelola
- Do not return to a wide horizontal button row in customer tables; it clips on narrow/dense layouts.
- `Kelola` remains reserved for status/risky actions: OFF, FREE/Gratis, isolir, activate again, delete.
- Tikor input accepts pasted coordinates such as `-8.30410006093805, 112.49401918132114`.
- Save flow stores parsed coordinates into `customers.latitude` and `customers.longitude`.
- If `tikor` is empty but `area_name` contains coordinate-like text, save flow may parse it as fallback.
- If `tikor` is filled but cannot be parsed, show a clear flash message instead of silently saving empty map fields.
- Google Maps links must use direct coordinate search:
  - `https://www.google.com/maps/search/?api=1&query=lat,lng`
- Detail Pelanggan must use the Tikor → Google Maps flow only when coordinates exist:
  - `Network / PPPoE` keeps the visible clickable Tikor row.
  - Clicking Tikor opens Google Maps directly with `v3_google_maps_url(lat,lng)`.
  - Map preview cards, `customerMiniMap`, Leaflet/OpenStreetMap/Nominatim search, and internal map canvas must stay hidden/removed from Detail Pelanggan by default.
  - Do not reintroduce embedded OpenStreetMap/Leaflet maps in customer detail unless explicitly approved.
- `Kelola mapping` sidebar group is intentionally hidden; keep data/routes recoverable but do not expose the menu by default.
- Keep asset cache-busting version updated whenever action/location CSS changes; last known version: `adminlte-clone.css?v=20260711-actionstack-location2`.

### 6. Laporan
- Income summary.
- Payments by month.
- Arrears/tunggakan.
- Customers by status/location/package.
- Export CSV first, Excel later.
- Print-friendly summary.

### 7. Sync / Migration
- V3 has independent SQLite DB.
- `sync.php` can refresh from root AppsBilling DB when needed.
- Future eBilling scrape/import should map into V3 tables without overwriting manual changes blindly.

## Execution Stages

### Stage 4 — Detail Customer Work Center (priority now)
- Upgrade `detail.php` layout into operator work center.
- Add summary cards: unpaid count, unpaid amount, paid total, last paid.
- Add unpaid invoice table.
- Add events/follow-up timeline.
- Preserve payment history and duplicate-payment detection.

### Stage 5 — Master Data CRUD
- Add/edit/delete for package, lokasi, router, rekening.
- If no dedicated tables exist for router/location, start with distinct values from customers and optional settings tables.

### Stage 6 — Export + Print Precision
- CSV export for customers, paid, unpaid, invoices.
- Better eBilling-like receipt/invoice print.

### Stage 7 — Full Billing Operations
- Bulk generate invoices by month.
- Bulk mark/isolir status tools with confirmation.
- Better follow-up status for arrears.

### Stage 8 — Visual Precision Pass
- Compare against cached/observed eBilling screens.
- Tighten spacing, colors, table actions, labels, footer totals.

## Current Status Before Stage 4
Already done:
- V3 independent DB exists.
- Users/customers/payments/invoices/payment history migrated.
- Add/edit/delete/print basics exist for core rows.
- Secret + Catatan patch deployed.

Known gaps:
- Customer detail is still too basic.
- Master data CRUD is incomplete.
- Export endpoints not implemented.
- Bulk invoice generation not implemented.
- Some menu placeholders are not real modules yet.

## Non-negotiables
- Backup live files before deployment.
- Syntax check PHP before and after deploy.
- Live smoke test authenticated pages.
- Keep `Secret` optional.
- Do not touch root or `/v2/` during V3 work unless requested.

---

## Update 2026-07-07 — V3 Operational Hardening

### Customer/payment workflow
- Pembayaran memakai alur **pilih/cari pelanggan dulu** (`page=add-laporan-ipl&customer_id=...`).
- Admin bisa tambah pembayaran dari Data Pelanggan, Belum Lunas, dan Detail Pelanggan.
- Riwayat pembayaran di Detail Pelanggan sekarang menjadi pusat koreksi manual:
  - Tambah Pembayaran
  - Edit Pembayaran
  - Hapus Pembayaran
  - Cetak Kwitansi
- Edit/hapus pembayaran otomatis menjalankan `v3_recalc_invoice(customer_id, invoice_month)` agar status dan sisa tagihan sinkron ulang.

### Invoice, discount, partial payment
- Diskon mengurangi kewajiban bayar: **nominal asli - diskon = tagihan final**.
- Jika pembayaran mencapai tagihan final setelah diskon, status menjadi `paid` dan `balance_amount = 0`.
- Jika pembayaran masih di bawah tagihan final, status menjadi `partial` dan `balance_amount` menyimpan sisa sebenarnya.
- Total tunggakan harus memakai `balance_amount`/sisa tagihan, bukan nominal invoice mentah.
- Data Tagihan mendukung tambah/edit invoice manual dengan `original_amount`, `discount_amount`, `discount_note`, status `unpaid|partial|paid`.

### Grand totals and filters
- Judul halaman pembayaran dibuat statis agar tidak misleading saat AJAX/filter berubah:
  - Data Pembayaran
  - Data Pelanggan Sudah Membayar
  - Data Pelanggan Belum Lunas
- Filter bulan/tahun tetap bekerja pada query data.
- Grand total pembayaran dan belum lunas menghitung seluruh hasil filter/search, bukan hanya rows pagination.

### Customer ID and login
- `customer_code` wajib diawali `DN`.
- Input manual tanpa DN otomatis dinormalisasi.
- Duplicate customer code ditolak di sisi aplikasi dan tetap dijaga oleh UNIQUE DB.
- Login admin hanya username + password.
- Tabel admin adalah `auth_users`; superadmin `ananta` disembunyikan dari Data User.

### Receipt / office identity
- Logo aplikasi/login/kwitansi memakai `v3/assets/denta-net-logo.jpg`.
- Admin Sistem → Pengaturan Kantor menyimpan identitas kantor di `app_settings`:
  - `office_brand`
  - `office_company`
  - `office_address`
  - `office_phone`
  - `receipt_note`
- Header kwitansi/invoice mengambil data dari `app_settings`.
- Tanda tangan kwitansi memakai konsep **Penerima**, bukan sales.
- Nama penerima mengikuti user admin yang sedang login saat print; tidak mengambil nama lama dari `payments.received_by`.

### Tutorial admin
- Menu **Admin Sistem → Tutorial Admin** berisi SOP operator:
  - alur pembayaran harian
  - diskon/kurang bayar
  - koreksi manual history pembayaran
  - ID pelanggan DN
  - filter/grand total
  - kwitansi dan identitas kantor
  - ringkasan pembaruan V3

### Deployment and backup rule
- Live target V3 tetap: `https://appsbilling.dentasejahteragroup.my.id/v3/`.
- Server path: `/var/www/appsbilling.dentasejahteragroup.my.id/v3` on `43.134.122.109`.
- Jangan overwrite DB live dari lokal kecuali ada approval eksplisit.
- Untuk source backup GitHub, pakai repo khusus: `git@github.com:remek8787/appsbilling-V3.git`.
- Repo backup source harus mengecualikan database live, backup tarball, file log, dan artefak sementara.

---

## Update 2026-07-07 — UI/UX Bootstrap CDN Experiment

### Bootstrap CDN layer
- V3 memakai Bootstrap 5.3.3 via CDN sebagai eksperimen UI ringan.
- Bootstrap CSS dimuat sebelum `assets/adminlte-clone.css`, sehingga CSS custom V3 tetap menjadi override utama.
- Bootstrap JS bundle dimuat sebelum `assets/v3-ajax.js`.
- Perubahan ini bersifat reversible; rollback cukup mengembalikan `v3/app/layout.php`, `v3/login.php`, dan `v3/assets/adminlte-clone.css` dari backup.

### Soft calm visual direction
- Palet kuning/orange digeser ke arah **soft blue / slate / mint**.
- Tujuan visual: lebih kalem, bersih, modern, tetap cocok untuk aplikasi billing ISP.
- CSS menggunakan gradient lembut, card blur ringan, rounded card/button, dan table hover yang tidak agresif.

### Motion / animation
- Animasi yang ditambahkan bersifat halus dan CSS-only:
  - card fade-up
  - soft card glow
  - icon dashboard floating
  - nav sweep/glow
  - table row hover
  - button lift hover
  - login logo floating
- `prefers-reduced-motion: reduce` disediakan agar device/user yang mematikan animasi tidak dipaksa melihat motion.

### Table column order
- Tabel pembayaran dirapikan menjadi `No | Aksi | Cetak | ...`.
- Pola lama `Aksi | No | Cetak` tidak dipakai lagi.
- Tabel pelanggan sejak awal sudah memakai `No | Aksi`, jadi tidak perlu perubahan tambahan.

### Current UI rollback points
- Bootstrap CDN polish backup live: `/home/ubuntu/backups/appsbilling-v3-bootstrap-cdn-polish-20260707-133044.tar.gz`.
- Soft calm motion backup live: `/home/ubuntu/backups/appsbilling-v3-soft-calm-motion-20260707-133413.tar.gz`.
- Payment column order backup live: `/home/ubuntu/backups/appsbilling-v3-payment-column-order-20260707-160007.tar.gz`.

---

## Update 2026-07-07 — DN12 Customer ID Rule

- Format ID pelanggan baru: **DN + 10 angka random**, total **12 karakter** tanpa spasi.
- Contoh format valid: `DN1234567890`.
- Jika form tambah pelanggan dikosongkan, sistem otomatis generate ID random dengan format DN12.
- Generator melakukan cek ke tabel `customers.customer_code` sampai menemukan ID yang belum dipakai, sehingga tidak ada data double.
- Jika input manual tidak sesuai `DN[0-9]{10}`, sistem mengganti ke ID random valid agar data baru tetap rapi.
- Rule lama hanya prefix DN dinyatakan tidak cukup untuk data baru; data lama tidak diubah massal tanpa audit/approval terpisah.

---

## Update 2026-07-08 — Receipt Bukti Pembayaran e-Billing Slip Layout

- Template `v3/print.php?type=receipt` diarahkan mengikuti referensi slip e-Billing: tanggal kecil kiri atas, judul sistem kecil tengah, identitas kantor kiri, logo kanan, garis horizontal, judul besar **BUKTI PEMBAYARAN**, detail pelanggan ringkas, tabel item pembayaran, grand total, terbilang, metode pembayaran, dan penerima.
- Layout dibuat lebih minimal seperti contoh, tidak lagi terlalu dekoratif/kartu modern.
- Data tetap bersumber dari payment/invoice/customer/settings yang sama; tidak mengubah database atau flow pembayaran.

---

## Update 2026-07-08 — Tailwind CDN Prefixed Experiment

- Tailwind CDN dicoba di V3 dengan konfigurasi aman: `prefix: tw-` dan `preflight: false`.
- Tujuannya melihat rasa UI bila memakai utility Tailwind tanpa menabrak Bootstrap/AdminLTE/custom CSS yang sudah ada.
- Tailwind dipakai hanya sebagai layer polish/wrapper: background radial soft, content header glass, spacing, shadow soft.
- Bootstrap dan CSS custom V3 tetap menjadi struktur utama; eksperimen mudah rollback dari `v3/app/layout.php` dan `v3/assets/adminlte-clone.css`.

---

## Update 2026-07-08 — PWA — DENTANET Billing OS

- AppsBilling V3 ditambahkan sebagai PWA ringan dengan nama **DENTANET Billing OS**.
- File utama: `v3/manifest.webmanifest`, `v3/service-worker.js`, `v3/offline.php`, `v3/assets/pwa-icon.svg`, dan register service worker di `v3/app/layout.php`.
- Strategi offline dibuat aman: cache app shell/aset dasar dan halaman offline; transaksi, data pelanggan, invoice, dan pembayaran tetap online-first agar tidak menciptakan data lokal yang tidak sinkron.
- Scope PWA dibatasi ke `/v3/` supaya tidak mengganggu root AppsBilling atau `/v2/`.

## Update 2026-07-08 — Operator UI, audit, and reporting layer

Latest V3 direction:
- V3 remains a PHP + SQLite billing workspace under `/v3/`, preserving the live v1 root and existing hosting flow.
- Internal account identifiers can remain in backend/database for compatibility, but UI should not expose raw account number `6778` in sidebar/dashboard cards.
- Sidebar user panel should show admin name + level only.
- `activity_logs` is the canonical admin audit trail for login/logout and create/update/delete/generate actions.
- `auth_users` tracks `last_login_at`, `last_login_ip`, `last_login_agent`, and `login_count` for operator accountability.
- Customer table supports per-admin column preferences through `user_table_preferences`.
- Customer column manager UX standard: visible native details panel, quick presets (Default, Ringkas, Billing, Teknis), active column count, clear off-state, and no manager on dashboard mini table.
- Customer table `Pembayaran terakhir` should show last amount, paid date/time, invoice period, and method.
- Track record pendapatan is the income dashboard: today, selected month, selected year, monthly bars, daily track record, method breakdown, and flexible manual year filtering beyond existing data years.

Safety rules:
- Do not remove internal `V3_ACCOUNT` or `auth_users.account` unless a migration plan is approved.
- Do not expose credentials, password hashes, or raw sensitive backend identifiers in UI/log detail.
- Keep source changes backed up to workspace/GitHub and live files backed up before deploy.

## Update 2026-07-09 — Custom tanggal pembayaran admin

Requirement dari Tuan Besar: admin harus bisa mengubah tanggal pembayaran secara fleksibel. Contoh operasional: pelanggan jatuh tempo tanggal 20 bisa membayar tanggal 1; pelanggan dengan jatuh tempo awal bulan juga bisa dibayar di tanggal lain sesuai kondisi lapangan.

Implementation rule:
- `customers.due_day` / `invoices.due_day` = tanggal jatuh tempo / tanggal tagihan.
- `payments.paid_at` = tanggal real pembayaran dan wajib bisa diubah manual oleh admin.
- Form tambah/edit pembayaran harus menampilkan label **Tanggal pembayaran**, bukan sekadar `Waktu entri`.
- Laporan pendapatan harian/bulanan/tahunan tetap memakai `payments.paid_at`, jadi report mengikuti tanggal real pembayaran.
- Periode pembayaran (`invoice_month`) tetap terpisah dari tanggal pembayaran. Artinya pembayaran periode Juli bisa dicatat tanggal 1, 20, atau tanggal lain sesuai real transaksi.
- Perubahan pembayaran tetap masuk audit/activity log melalui flow `save_payment` yang sudah ada.

## Update 2026-07-10 — Corporate Billing Stage 1

Requirement dari Tuan Besar: AppsBilling V3 perlu punya pelanggan/tagihan **Corporate** yang tidak tercampur dengan pelanggan utama/retail, dan tahap awal cukup manual dulu agar bisa dicoba aman.

Implementation direction:
- Corporate dibuat sebagai modul terpisah di sidebar **Corporate**.
- Data corporate tidak memakai tabel `customers` retail.
- Tagihan corporate tidak ikut auto-generate retail dan tidak mengubah invoice/payment retail.
- Tahap 1 fokus pada input manual, pembayaran manual, dan cetak invoice manual.
- Root AppsBilling dan `/v2/` tetap tidak disentuh.

Tables added:
- `corporate_customers` — identitas perusahaan, PIC, kontak, alamat, layanan, nominal bulanan, due day, status, catatan.
- `corporate_invoices` — invoice manual corporate per periode, item/description, amount, discount, total, paid, balance, due date, status.
- `corporate_payments` — pembayaran corporate terpisah dari `payments` retail.

Routes/pages added in `v3/index.php`:
- `corporate-customers` — Data Corporate.
- `add-corporate-customer` — Tambah pelanggan corporate.
- `corporate-invoices` — Tagihan Corporate.
- `add-corporate-invoice` — Buat tagihan corporate manual.
- `corporate-payment` — Input pembayaran corporate.

Print support:
- `v3/print.php?type=corporate_invoice&id=...` prints a standalone Corporate Invoice template.
- `corporate_receipt` type is prepared in the same block for future receipt flow.

Safety/rollback:
- Retail tables and flows remain untouched except shared layout/menu and helper initialization.
- PHP lint passed on `app/db.php`, `app/layout.php`, `index.php`, and `print.php` locally and live.
- Live database migration verified by checking all three corporate tables exist.
- Live backup before deploy: `/home/ubuntu/backups/appsbilling-v3-corporate-stage1-20260710-151519`.

Next recommended Corporate stage:
- Add edit corporate.
- Add nonaktif/hapus aman corporate.
- Add print kwitansi corporate after payment.
- Add status filters and dashboard summary for corporate receivables.
- Improve B2B invoice format with formal company header, tax/NPWP/NIB fields, signature area, and optional bank destination.

## Update 2026-07-10 — Pelanggan Aktif vs OFF Dipisah

Untuk menjaga operasional harian tetap bersih, AppsBilling V3 memisahkan tampilan pelanggan aktif dan pelanggan OFF/nonaktif:

- `page=data-warga` dipakai sebagai daftar pelanggan aktif.
- `page=data-pelanggan-off` dipakai sebagai ruang arsip/monitoring pelanggan OFF atau nonaktif.
- `page=add-pelanggan-off` dipakai untuk input pelanggan OFF secara khusus, default status OFF.
- Data tetap berada di tabel `customers` agar histori pembayaran, invoice, detail pelanggan, dan event tetap utuh.
- `customer_status` adalah sumber status utama, sementara `is_active` disinkronkan untuk kompatibilitas legacy.
- Generate tagihan bulanan harus hanya mengambil pelanggan aktif (`customer_status='active' AND is_active=1`) agar pelanggan OFF tidak ikut tertagih otomatis.

Non-negotiable:
- Jangan hapus data pelanggan OFF hanya karena sudah dipisah; OFF adalah arsip operasional yang masih bisa dibuka detail/edit.
- Jika pelanggan reconnect, ubah status lewat edit pelanggan agar kembali muncul di Data Pelanggan Aktif.

## Update 2026-07-10 — Pelanggan FREE / Gratis Dipisah

AppsBilling V3 sekarang memisahkan pelanggan aktif berbayar dan pelanggan aktif gratis:

- `page=data-warga` = pelanggan aktif berbayar (`customer_status='active'` dan harga paket > 0).
- `page=data-pelanggan-free` = pelanggan aktif FREE/Gratis (`customer_status='active'` dan harga paket = 0).
- `page=data-pelanggan-off` tetap khusus pelanggan OFF/nonaktif; pelanggan OFF dengan paket Rp0 tidak dimasukkan ke FREE agar kategori tidak tumpang tindih.
- Label harga Rp0 ditampilkan sebagai `FREE / Gratis` supaya operator mudah membedakan layanan gratis, sponsor, internal, demo, kompensasi, atau free trial.
- Generate tagihan otomatis tetap hanya untuk pelanggan aktif berbayar: `customer_status='active' AND is_active=1 AND price > 0`.

Non-negotiable:
- Pelanggan FREE boleh tetap aktif, tetapi tidak boleh bercampur dengan daftar pelanggan berbayar dan tidak boleh ikut tagihan otomatis nominal bulanan.

## Update 2026-07-10 — Customer Action Center

AppsBilling V3 now includes a lightweight Customer Action Center for daily operator work.

Operator intent:
- Moving customers between paid active, FREE/Gratis, OFF/nonactive, and isolir should not require deep manual editing every time.
- The UI should use plain field/operator language, not stiff system wording.
- Actions should be fast but still auditable.

Implemented behavior:
- `quick_customer_action` handles safe status/package movement.
- Customer table actions include quick buttons where relevant.
- Detail Pelanggan includes an `Aksi cepat pelanggan` panel with practical copy:
  - `OFF-kan`
  - `Jadikan FREE`
  - `Isolir`
  - `Aktifkan lagi`
- Segment navigation appears across customer pages: paid, FREE, OFF, arrears.
- Each quick action logs a `customer_events` entry and `activity_logs` entry.

Rules:
- OFF/isolir customers remain in `customers`; do not delete them just to clean the active list.
- FREE customers remain active but must stay out of paid active list and monthly billing generation.
- When a customer is reactivated, category is determined by package price: Rp0 goes to FREE, >Rp0 goes to paid active.
- Copywriting should stay natural, concise, and operator-friendly.

## Tim Penagihan / Kolektor
Modul Tim Penagihan adalah lapisan operasional terpisah dari pembayaran. Sumber item adalah invoice `unpaid`/`partial`. Batch menyimpan petugas, periode, jasa kolektif, item invoice, dan laporan lapangan. Selama batch dibuat, dicetak, diserahkan, atau dilaporkan, sistem dilarang membuat record `payments` atau mengubah status/nominal invoice. Pelunasan/partial hanya diproses melalui form pembayaran normal V3.

Tabel: `collection_officers`, `collection_batches`, `collection_batch_items`. Status batch aktif yang mengunci invoice: `ready`, `assigned`, `in_progress`. Dokumen cetak wajib memakai istilah **Lembar Penagihan** dan label **BUKAN BUKTI LUNAS**.

## 12. Tim Penagihan, Tutorial, dan Rollback Baseline (2026-07-11)

Referensi detail kanonis: `/root/.openclaw/workspace/notes/appsbilling-v3-collection-team-blueprint.md`.

### Tim Penagihan
- Flow: Belum Lunas → pilih pelanggan → buat batch → cetak Lembar Penagihan → laporan lapangan → pembayaran normal.
- Search tersedia saat membuat dan menambah isi batch; pilihan bertahan ketika query berubah.
- Batch dapat dikoreksi pada semua status, termasuk closed, dengan audit trail.
- Jasa kolektif terpisah dari invoice dan pembayaran.
- Cetak memakai identitas/logo kantor dan judul `Lembar Penagihan`, tanpa label `BUKAN BUKTI LUNAS`.

### UI/UX baseline
- Halaman Belum Lunas, Tim Penagihan, Detail Batch, Tutorial Admin, dan Changelog memakai bahasa desain AppsBilling biru–teal yang konsisten.
- Tutorial Admin adalah pusat panduan operator dengan hero, navigasi sticky, alur kerja, kartu bernomor, panduan collection, dan changelog timeline.
- Changelog terbaru wajib berada di atas dan menjelaskan area serta dampak operasional.

### Rollback baseline
- Source rollback tidak otomatis berarti database rollback.
- Pulihkan database hanya dengan persetujuan eksplisit dan setelah membuat backup kondisi saat ini.
- Checkpoint terbaru sebelum Tutorial V2: `/home/ubuntu/backups/appsbilling-v3-before-tutorial-refresh-20260711-170013` dan DB `/home/ubuntu/backups/appsbilling-v3-db-before-tutorial-refresh-20260711-170013.sqlite`.
- Daftar checkpoint lengkap dan prosedur verifikasi berada di blueprint Tim Penagihan.

---

## Update 2026-07-13 — Cache Fix, Dashboard Data Health, dan Track Income Rekening

Referensi rollback kanonis terbaru:
`/root/.openclaw/workspace/notes/appsbilling-v3-rollback-blueprint.md`

### Cache/service worker
- Asset CSS/JS memakai version query agar perubahan tidak memerlukan Ctrl+F5.
- Service worker menggunakan cache version tersendiri, membersihkan cache lama saat activate, dan CSS/JS memakai network-first dengan fallback cache.
- Registration memakai `updateViaCache: 'none'` dan `reg.update()`.

### Dashboard
- Rekap pemasukan bulan aktif per rekening/metode: nominal, jumlah transaksi, dan persentase.
- Kelengkapan alamat dihitung dari alamat minimal 8 karakter.
- Kelengkapan Secret memakai `customers.onu_name` sebagai field Secret/ONU.
- Persentase dihitung dari total pelanggan tercatat.

### Track Record Pendapatan
- Filter mendukung bulan, tahun cepat/manual, dan rekening masuk.
- Rekening dapat dipilih per `bank_accounts.id`; Cash/tanpa rekening memakai `COALESCE(bank_account_id,0)=0`.
- KPI bulan/tahun, bar 12 bulan, metode, dan track harian mengikuti rekening terpilih.
- Komposisi semua kanal tetap menunjukkan nominal, transaksi, serta persentase periode.
- Rincian transaksi rekening terpilih ditampilkan maksimal 250 transaksi terbaru pada bulan tersebut.
- Navigasi bulan sebelumnya/berikutnya mempertahankan filter rekening.

### Batas integritas
- Tiga perubahan ini tidak mengubah schema atau data bisnis.
- Rollback normal adalah source/UI-only; jangan restore SQLite.
- File terkait: `v3/index.php`, `v3/app/layout.php`, `v3/login.php`, `v3/service-worker.js`, `v3/assets/adminlte-clone.css`.
- Asset version live terakhir tervalidasi: `20260713-income-bank-history`.

### Checkpoint live
- Sebelum Track Income historis rekening: `/home/ubuntu/backups/appsbilling-v3-income-bank-history-20260713-174917`.
- Sebelum rekap dashboard: `/home/ubuntu/backups/appsbilling-v3-dashboard-rekap-20260713-173650`.
- Sebelum cache fix: `/home/ubuntu/backups/appsbilling-v3-cachefix-20260713-141618`.
- Prosedur, pemetaan file, marker verifikasi, dan perintah restore berada di rollback blueprint kanonis.

---

## Update 2026-07-13 — Corporate Branding e-Billing DSG

Nama produk resmi V3:

- Nama utama: **e-Billing DSG**
- Nama lengkap: **e-Billing PT Denta Sejahtera Group System**
- Subjudul: **Billing Management System**
- Versi: **Version 3.0**, hanya sebagai metadata kecil.

Branding diterapkan pada login, title browser, topbar, sidebar, footer, Tutorial Admin, changelog, dokumen cetak, manifest PWA, offline page, dan service-worker cache identity. URL dan folder tetap `/v3/`; arsitektur, autentikasi, database, serta alur transaksi tidak berubah.

DENTA NET tetap dipertahankan sebagai identitas usaha/layanan pada office settings dan dokumen yang menggunakan data kantor. Branding aplikasi induknya adalah e-Billing DSG.

File terkait:

- `v3/app/db.php`
- `v3/app/layout.php`
- `v3/login.php`
- `v3/index.php`
- `v3/print.php`
- `v3/manifest.webmanifest`
- `v3/offline.php`
- `v3/service-worker.js`
- `v3/assets/adminlte-clone.css`

Asset/cache version: `20260713-ebilling-dsg-brand`.

Backup sebelum branding:

- Source live: `/home/ubuntu/backups/appsbilling-v3-before-ebilling-dsg-branding-20260713-233427`
- DB safety copy: `/home/ubuntu/backups/appsbilling-v3-db-safety-before-branding-20260713-233427.sqlite`
- Lokal: `/root/.openclaw/workspace/projects/appsbilling.dentasejahteragroup.my.id/backups/v3-branding-ebilling-dsg-20260713-233427`

Rollback normal adalah source-only. Jangan restore SQLite karena branding tidak mengubah schema atau data.

## Update 2026-07-15 — Tim Penagihan Multi-Periode per Pelanggan

Kontrak operasional terbaru modul Tim Penagihan:

- Unit pilihan adalah **invoice/periode**, bukan hanya pelanggan bulan aktif.
- Satu pelanggan dapat dipilih untuk beberapa bulan sekaligus dan dicetak sebagai **satu lembar penagihan multi-periode**.
- `collection_batch_items` tetap menyimpan satu baris per invoice untuk menjaga audit dan keterlacakan.
- Jasa kolektif adalah biaya terpisah dari invoice dan hanya dibebankan sekali per pelanggan dalam satu batch; item periode berikutnya menyimpan jasa 0.
- Status dan catatan laporan dikelola per pelanggan, lalu disinkronkan ke semua item pelanggan tersebut dalam batch.
- Pilihan cetak: semua lembar dalam batch, rekap batch landscape, satu pelanggan, atau pelanggan yang dicentang.
- Pembayaran tetap dilakukan melalui form pembayaran resmi untuk invoice/periode yang sesuai.
- Invoice yang sudah masuk batch aktif lain (`ready`, `assigned`, `in_progress`) tidak boleh dipilih ulang.
- Hapus batch/item hanya menghapus relasi operasional collection, tidak menghapus invoice atau pembayaran.

File fitur: `v3/index.php`, `v3/print.php`, `v3/assets/adminlte-clone.css`. Perubahan ini tidak memerlukan schema baru dan tidak boleh menimpa SQLite saat deploy.



## Update 2026-07-15 — Dashboard Kelengkapan Data Click-Through

Dashboard `Kelengkapan data pelanggan` sekarang menjadi alat kerja langsung, bukan hanya persentase.

- Kartu Alamat, Secret pelanggan, dan Titik koordinat dapat diklik.
- Klik `Detail belum lengkap` membuka `Data Pelanggan` dengan filter `data_health` sehingga admin melihat daftar pelanggan yang perlu dilengkapi.
- Halaman hasil filter memakai judul `Cek Data Pelanggan Belum Lengkap` dan tetap menyediakan tombol Detail/Edit per pelanggan.
- Secret pelanggan dihitung dari pelanggan **non-OFF** saja. Pelanggan `OFF / Tidak aktif` tidak ikut menurunkan persentase Secret dan tidak muncul di filter Secret kosong.
- Titik koordinat tetap dihitung dari `customers.latitude` dan `customers.longitude`, lalu dilengkapi melalui Tikor di Edit/Detail pelanggan.
- Tutorial Admin dan changelog wajib menyebut aturan ini agar operator tidak salah membaca persentase.

## Stage 69 - Analisa Pendapatan & Piutang (2026-07-15)

Status: deployed live to `https://appsbilling.dentasejahteragroup.my.id/v3/`.

Scope:
- Added dedicated menu `Analisa pendapatan` / route `page=income-analysis`.
- Added dashboard mini panel `Ringkasan pendapatan & piutang` so operators can see the money-control snapshot without leaving Dashboard.
- Metrics implemented:
  - estimasi tagihan bulan/periode utama from `invoices.invoice_month`;
  - realisasi masuk from `payments.invoice_month`;
  - belum bayar bulan ini from unpaid invoices for the selected month;
  - nunggak periode lama from unpaid invoices older than the selected month;
  - potensi double/lebih bayar by comparing total payment per customer-period against invoice amount.
- Added month-range comparison chart with filter `month`, `from`, and `to`.
- Overpay/double-pay section is intentionally labeled as audit warning only; operator must inspect customer history before correcting data.

Files changed:
- `v3/index.php`
- `v3/app/layout.php`
- `v3/assets/adminlte-clone.css`

Verification:
- Local PHP lint passed for `v3/index.php` and `v3/app/layout.php`.
- Local render smoke passed for `page=income-analysis` and Dashboard markers.
- Live backup before deploy: `/home/ubuntu/backups/appsbilling.dentasejahteragroup.my.id-v3-before-income-analysis-20260715-154333.tar.gz`.
- Live PHP lint passed after deploy.
- Live marker checks passed for route/menu/CSS.
- HTTP smoke returns `302` to login, expected for authenticated admin pages.

Rules preserved:
- No schema change.
- No SQLite/data/env deployment.
- Root and `/v2/` untouched.
- Internal map/Leaflet UI remains hidden per Stage 67 decision.

## Update 2026-07-15 — PWA Icon DENTANET dari Logo Kwitansi

Status branding PWA terbaru untuk `https://appsbilling.dentasejahteragroup.my.id/v3/`:

- Kwitansi/print tetap memakai **full logo DENTANET** (`dentanet-logo-20260715.jpg`) agar identitas dokumen resmi tetap lengkap.
- Icon PWA Android/iOS memakai **D symbol saja** yang dicrop dari logo kwitansi, bukan full logo dengan tulisan. Tujuannya agar launcher icon tetap terbaca setelah dimask Chrome Android/iOS.
- Manifest utama yang dipakai browser adalah JSON agar aman terhadap MIME + `nosniff`:
  - `v3/manifest-pwa-receipt-d-20260715.json`
  - Harus tersaji sebagai `Content-Type: application/json`.
- Manifest PWA hanya boleh merujuk PNG opaque/RGB dengan background putih dan path absolut:
  - `/v3/assets/pwa-dentanet-receipt-d-20260715-192.png`
  - `/v3/assets/pwa-dentanet-receipt-d-20260715-512.png`
- iOS home-screen icon memakai:
  - `/v3/assets/apple-dentanet-receipt-d-20260715.png`
  - extra export: `167` dan `152` bila nanti perlu dipakai eksplisit.
- Jangan memasukkan SVG/JPG/maskable ke manifest utama untuk saat ini karena sebelumnya Chrome Android sempat fallback ke blank/gelap saat manifest `.webmanifest` tersaji sebagai `application/octet-stream` dengan `nosniff`.
- `login.php`, `layout.php`, `pwa-refresh.html`, dan `service-worker.js` harus tetap mengarah ke manifest JSON dan icon D-only versi receipt-D.
- Jika icon di perangkat lama belum berubah, prosedur operator: hapus PWA lama, buka `/v3/pwa-refresh.html`, bersihkan cache PWA, tutup browser, lalu install ulang dari `/v3/login.php`.

File terkait:

- `v3/manifest-pwa-receipt-d-20260715.json`
- `v3/manifest-pwa-donly-20260715.json` (compatibility mirror)
- `v3/manifest.webmanifest` / `v3/manifest-20260715-logo2.webmanifest` (compatibility mirror)
- `v3/assets/pwa-dentanet-receipt-d-20260715-512.png`
- `v3/assets/pwa-dentanet-receipt-d-20260715-192.png`
- `v3/assets/apple-dentanet-receipt-d-20260715.png`
- `v3/app/layout.php`
- `v3/login.php`
- `v3/pwa-refresh.html`
- `v3/service-worker.js`

Backup penting:

- `/home/ubuntu/backups/appsbilling-dsg-v3-before-receipt-d-pwa-20260715-165927.tar.gz`

Verification gate sebelum klaim selesai:

- `curl -I /v3/manifest-pwa-receipt-d-20260715.json` harus `Content-Type: application/json`.
- `curl -I /v3/assets/pwa-dentanet-receipt-d-20260715-512.png` harus `Content-Type: image/png`.
- Login page harus memuat `manifest-pwa-receipt-d-20260715.json`, `pwa-dentanet-receipt-d-20260715-512.png`, dan `apple-dentanet-receipt-d-20260715.png`.
- Icon 512 harus 512x512, PNG opaque/RGB, background putih, dan bukan blank.


## Update 2026-07-17 — Pengeluaran Operasional Harian

- Menu baru V3: **Kelola pembayaran → Pengeluaran operasional** (`index.php?page=operational-expenses`).
- Tujuan: mencatat biaya harian seperti BBM, service, alat kerja, konsumsi, transport, fee teknisi, maintenance jaringan, dan biaya lain.
- Field utama: tanggal, jenis/kategori, keterangan/dibuat apa, siapa yang menggunakan (opsional/nama teknisi), vendor/tempat beli opsional, metode, nomor referensi/nota, nominal rupiah, status, catatan.
- Rekap tersedia per bulan dengan navigasi maju/mundur, subtotal sesuai filter, rekap kategori, rekap teknisi/pengguna, track harian, dan pencarian/find.
- Batas aman penting: data pengeluaran disimpan di tabel `expenses` dan **tidak otomatis mengurangi pemasukan**. Tidak ada update otomatis ke `payments`, `invoices`, status lunas, saldo pembayaran, atau dashboard pemasukan utama.
- Jika ada angka “analisa kasar”/estimasi bersih, itu hanya tampilan analisa bulan berjalan: pembayaran tercatat dikurangi total biaya operasional, bukan posting akuntansi otomatis.

### Coret Concept Board — Pengeluaran Operasional — 2026-07-17

- Coret visual board untuk shared module pengeluaran operasional: https://coret.id/share/3de9439650f407f7f92f3b819c5d19807856cf504339d1f2
- Board merangkum tujuan, alur kerja, data/DB, batas aman, rollout multi-instance, dan verifikasi.
- Prinsip tetap: pengeluaran operasional hanya pencatatan biaya/rekap di tabel `expenses`, tidak mengubah `payments`, `invoices`, status lunas, saldo pemasukan, atau dashboard pemasukan otomatis.

## Stage 71 — Tutorial Admin + Changelog Sync (2026-07-17)

Tutorial Admin is the operator-facing SOP and must track meaningful V3 module changes, not only code deployment notes.

Latest in-app Tutorial Admin additions:
- Hero update marker: `Diperbarui 17 Juli 2026`.
- Navigation anchors: `Point` and `Operasional`.
- `Reward Point Pelanggan` SOP:
  - points are generated from real payment date (`payments.paid_at`);
  - point rule is 10/5/3/1 for payment date ranges 1-5, 6-10, 11-20, and 21-end;
  - point visibility: Dashboard, Data Pelanggan, Detail Pelanggan, and point ledger;
  - points are loyalty metadata only and do not modify invoice/payment status or receipt totals.
- `Pengeluaran Operasional Harian` SOP:
  - route/menu: `Kelola pembayaran → Pengeluaran operasional`;
  - records operational cost in `expenses` with category, user/technician, vendor, reference, method, amount, status, and notes;
  - supports monthly filtering, subtotal, category/user recap, and daily review;
  - must remain separate from `payments`, `invoices`, paid status, income balance, and the main income dashboard automatic accounting.

Latest changelog entries now include:
- `2026-07-17 · Operasional · Pengeluaran Operasional Harian`.
- `2026-07-16 · Reward · Reward Point Pelanggan`.

Preserved constraints:
- Root and `/v2/` are untouched.
- No database or `.env` file is committed.
- Documentation and in-app SOP must remind operators that operational expenses are not automatic income deductions and reward points are not payments.

## Update 2026-07-18 — Rollout Kelengkapan Data Aktif ke Instance Turunan

Aturan data-health pelanggan aktif dari AppsBilling V3 kanonis telah disinkronkan ke tiga instance turunan:

- `https://billing.mrtnet.my.id/`
- `https://appsbilling.borneonetwork.my.id/v3/`
- `https://billing.dsgtegal.my.id/`

Kontrak yang wajib sama pada seluruh instance:

- Alamat lengkap dan Titik koordinat memakai `customer_status='active' AND COALESCE(is_active,1)=1` untuk pembilang, denominator, angka kurang, dan filter detail.
- Secret tetap memakai pelanggan non-OFF.
- Tutorial Admin menampilkan `Diperbarui 18 Juli 2026` dan menjelaskan perbedaan scope tersebut.
- Changelog terbaru mencatat `Alamat dan Tikor hanya pelanggan aktif`.
- Source fitur boleh identik, tetapi database SQLite, branding, konfigurasi, user, dan backup tidak boleh silang antar-instance.
- Setiap deployment wajib memakai backup, lint, checksum lokal-live, marker Tutorial/Changelog, query DB, dan smoke test login secara terpisah.

Pada saat rollout, ketiga instance turunan memiliki `0` pelanggan aktif sehingga metrik yang benar adalah `0%` sampai data aktif ditambahkan. Coret kanonis: version `43`, node count `68`.


## Update 2026-07-18 — Role Crew Read-Only untuk Tim Lapangan

AppsBilling DSG V3 memiliki role `Crew` sebagai akses lapangan minimum. Role ini bukan turunan admin dan tidak boleh memperoleh akses mutasi walaupun URL atau request dibuat manual.

### Allowlist Crew

- `index.php?page=data-warga`: pelanggan aktif, enabled, dan paket berbayar.
- `index.php?page=data-pelanggan-free`: pelanggan aktif, enabled, dan paket Rp0/FREE.
- Pencarian dan pagination tetap tersedia dalam dua scope tersebut.

### Data yang boleh terlihat

- ID pelanggan.
- Nama pelanggan.
- Alamat.
- Nomor telepon.
- Paket/nama langganan.
- Status langganan.
- Secret.
- Username PPPoE.
- Tombol Google Maps hanya jika latitude/longitude valid.

Crew tidak melihat riwayat pembayaran, nominal, tunggakan, point, router, catatan internal, password pelanggan, detail pelanggan, pelanggan OFF/nonaktif, atau data admin lainnya.

### Enforcement wajib

- Session role tetap berasal dari `$_SESSION['v3_user']['level']`.
- Helper kanonis: `current_user_is_crew()`, `require_non_crew()`, dan `enforce_crew_page()`.
- Semua POST Crew harus berhenti dengan HTTP `403` dan pesan `Akses ditolak. Role Crew bersifat read-only.`.
- Direct endpoint `detail.php`, `edit.php`, `delete.php`, `import-export.php`, `print.php`, dan `sync.php` wajib menolak Crew server-side.
- Menu/sidebar Crew hanya berisi Pelanggan aktif dan Pelanggan FREE / Gratis.
- Auto-sync dan auto-generate invoice tidak boleh berjalan dalam sesi Crew.
- UI tambah/edit user boleh memilih `Crew`; username `ananta` dan level `Superadmin` tetap memakai proteksi lama.

### Batas rollout

Role Crew tahap ini khusus instance private/internal DSG `/v3/`. Jangan otomatis menyalin fitur ke MRTNET, Borneo Network, DSG Tegal, root commercial platform, `/v2/`, atau `/nms`. Setiap rollout lintas instance membutuhkan keputusan, backup, dan verifikasi terpisah.

### Deployment evidence

- Live DSG V3 deployed 18 Juli 2026.
- Backup sebelum deploy: `/home/ubuntu/backups/appsbilling-v3-before-crew-role-20260718-060739`.
- Source lint, checksum local-live, login Crew, deny matrix, regresi Administrator, business table counts, dan SQLite integrity semuanya lulus.
- Akun Crew/Administrator sementara untuk smoke test dihapus setelah verifikasi.

### Crew Table Presentation Contract — 2026-07-18

Urutan visual kanonis untuk tabel Crew adalah:

`No → Aksi → ID pelanggan → Nama pelanggan → Alamat → Telepon → Nama langganan → Status langganan → Secret → Username PPPoE`

Aturan UI:
- Aksi ditempatkan tepat setelah No agar kebutuhan utama petugas lapangan—membuka lokasi—dapat dilakukan tanpa mencari kolom paling kanan.
- Tombol `Buka Lokasi` harus memiliki ukuran konsisten; jika koordinat tidak tersedia, tampil `Lokasi belum tersedia` pada ruang yang sama.
- ID pelanggan memakai treatment ringkas dan tidak terpotong.
- Nama, alamat, paket, Secret, dan username PPPoE boleh wrap agar baris tetap terbaca dan tidak saling bertumpuk.
- Header tabel dibuat jelas/sticky dalam table container; hover row membantu pembacaan horizontal.
- Pada layar kecil, tabel tetap berupa data grid dengan horizontal scrolling dan petunjuk geser, bukan memaksakan semua kolom menjadi sempit.
- Styling wajib di-scope melalui `crew-table-wrap` dan `crew-customer-table`, sehingga tabel Administrator, Operator, dan Sales tidak berubah.
- Perubahan presentasi ini tidak mengubah query scope, data pelanggan, maupun enforcement read-only Crew.

Tutorial Admin dan changelog in-app wajib menjelaskan urutan kolom serta perilaku mobile tersebut agar administrator dapat memberi pengarahan yang sama kepada tim lapangan.

### Documentation sync evidence — Crew Table UI

- Tutorial Admin dan changelog live disinkronkan pada 18 Juli 2026.
- Backup: `/home/ubuntu/backups/appsbilling-v3-before-crew-doc-sync-20260718-062410`.
- Coret kanonis terverifikasi pada version `45`, node count `74`.
- Authenticated smoke test memastikan marker urutan tabel, Aksi lokasi di depan, perapian desktop/mobile, dan read-only boundary tampil di halaman admin.

## Update 2026-07-18 — Keandalan Input Tanggal dan Submit Pembayaran

Form tambah pembayaran pada `index.php?page=add-laporan-ipl` memakai kontrak UI berikut:

- Field `paid_at` berupa `input type="date"`, bukan `datetime-local`, dan wajib diisi.
- Alasan: pada browser desktop/mobile, nilai `datetime-local` yang diketik tetapi belum memiliki bagian waktu lengkap dapat dianggap invalid sehingga browser menahan submit tanpa pesan aplikasi; pengguna melihatnya seperti tombol tidak bisa diklik.
- Jika tanggal kosong/tidak lengkap, JavaScript menampilkan pesan inline dan memfokuskan field tanggal.
- Setelah seluruh validasi lulus, tombol Simpan berubah menjadi `Menyimpan pembayaran…` dan dinonaktifkan untuk mencegah double-submit.
- Deteksi pembayaran yang sudah ada pada pelanggan/periode tetap menjadi penguncian terpisah; pengguna diarahkan ke Edit pembayaran lama agar tidak terjadi data ganda.
- Backend tetap menerima `paid_at` melalui `v3_normalize_datetime()`. Nilai tanggal-only dinormalisasi ke `Y-m-d H:i:s`, sehingga tidak diperlukan migrasi skema dan riwayat lama tetap kompatibel.
- Asset pembayaran wajib memakai cache-bust yang sinkron pada layout dan service worker agar perbaikan langsung diterima browser/PWA.

Deployment 18 Juli 2026:
- Backup: `/home/ubuntu/backups/appsbilling-payment-date-before-20260718-165755.tar.gz`.
- Marker asset live: `20260718-payment-date-submit-fix`.
- PHP lint, JavaScript syntax, authenticated local form smoke, normalisasi tanggal, live HTTP, checksum lokal-live, serta ownership/mode file lulus.
- Pengujian tidak membuat atau mengubah transaksi live.

## Update 2026-07-19 — Modul Infrastruktur Jaringan Terisolasi

AppsBilling DSG V3 menambahkan kelompok menu **Infrastruktur Jaringan** untuk mendokumentasikan topologi dan aset teknis tanpa mengubah domain billing.

### Cakupan modul

- Ringkasan Infrastruktur.
- ODC dengan ID custom, lokasi, koordinat, kapasitas distribusi, status, dan catatan.
- ODP dengan upstream, port upstream, koordinat, kapasitas port, status, dan pemakaian pelanggan.
- Joint Closure sebagai titik fisik sambungan core.
- Jalur Kabel antar titik jaringan dengan ID custom, tipe kabel, jumlah core, panjang, status, dan patokan rute.
- Sambungan Core: core masuk, warna, core keluar, warna, Joint Closure, arah dari/menuju titik, status penggunaan, dan catatan sinyal/redaman.
- Penempatan Pelanggan ke ODP/port sebagai relasi teknis tambahan.

### Kontrak data

Seluruh domain baru memakai tabel terpisah:

- `network_nodes`
- `network_cable_routes`
- `network_splices`
- `network_customer_placements`

Tabel lama `customers`, `packages`, `invoices`, `payments`, `bank_accounts`, dan transaksi lain tidak dimigrasikan atau ditulis ulang. Penempatan pelanggan hanya menyimpan `customer_id` sebagai referensi; melepas penempatan tidak menghapus atau mengubah pelanggan.

### Validasi dan integritas topologi

- ID node dan ID jalur bersifat unik.
- Koordinat memakai parser latitude/longitude AppsBilling dan membuka Google Maps langsung; embedded map pelanggan tetap tidak dipakai.
- ODP menolak nomor port melebihi kapasitas.
- Index unik menolak port aktif ganda pada ODP yang sama.
- Kapasitas node tidak dapat diturunkan di bawah pemakaian aktual.
- Nomor core sambungan tidak boleh melebihi jumlah core jalur kabel.
- Titik atau jalur yang masih direferensikan tidak dapat dihapus sampai relasinya dipindahkan/dihapus secara eksplisit.
- Semua create/update/delete masuk `activity_logs` dengan entity `network_*`.

### Hak akses

- Modul tahap pertama hanya tersedia untuk non-Crew melalui menu dan backend `require_non_crew()`.
- Role Crew tetap memakai allowlist read-only pelanggan aktif/FREE dan tidak mendapat menu atau endpoint mutasi jaringan.
- Tidak ada perubahan terhadap kontrak role Administrator, Operator, Sales, atau proteksi superadmin.

### UI/UX

- Arah visual berupa dashboard operasional jaringan: ringkasan aset, kapasitas ODP, cakupan pelanggan, dan pembaruan terakhir.
- Form dan tabel mempertahankan shell AppsBilling/AdminLTE yang ada; tidak ada migrasi stack, route utama, atau redesign global.
- Tabel mendukung horizontal scroll pada layar kecil.
- Asset cache marker: `20260719-network-infrastructure`.

### Rollback

Checkpoint sebelum implementasi:

- Lokal: `backups/network-infrastructure-before-20260719-005740`
- Live: `/home/ubuntu/backups/appsbilling-v3-before-network-infrastructure-20260719-005740`

Rollback source dapat mengembalikan file V3 dari checkpoint. Tabel `network_*` dapat dibiarkan kosong tanpa memengaruhi billing. Restore database penuh hanya dilakukan bersama Tuan Besar bila memang diperlukan karena dapat mengembalikan transaksi billing yang terjadi setelah checkpoint.

### Verification gate

Sebelum deployment final wajib membuktikan:

- PHP lint seluruh source V3.
- SQLite `quick_check=ok`.
- Fixture ODC → JC → ODP, dua jalur kabel, sambungan core, dan penempatan pelanggan berhasil pada salinan DB.
- Port aktif ganda ditolak.
- Core melebihi kapasitas ditolak.
- Kapasitas tidak dapat diperkecil di bawah pemakaian.
- Counts `customers`, `payments`, `invoices`, `packages`, dan `bank_accounts` tidak berubah selama pengujian.
- Authenticated smoke test semua route jaringan dan regresi route billing.

### Coret kanonis — Infrastruktur Jaringan

- Board: https://coret.id/share/13359bbbbf7a0049023307cd7743a1e3fcc12cdeee87e2b4
- Update 2026-07-19 terverifikasi pada version `46`, node count `78`.
- Node baru mendokumentasikan domain jaringan, UI operasional, isolasi/validasi `network_*`, dan rollback gate.
