# AppsBilling V3 eBilling-Compatible Tracker

## Stage 4 — Detail Customer Work Center
- [x] Audit current `detail.php` data availability.
- [x] Add operator summary cards.
- [x] Add unpaid invoice/tunggakan table.
- [x] Add payment history with totals.
- [x] Add customer event/follow-up timeline.
- [x] Add quick actions: edit customer, input payment, print invoice if invoice exists, back to data warga.
- [x] PHP syntax check.
- [x] Deploy with backup.
- [x] Live smoke test.

## Stage 5 — Master Data CRUD
- [x] Paket CRUD precision pass.
- [ ] Lokasi CRUD/settings. (sementara masih laporan turunan pelanggan)
- [ ] Router CRUD/settings. (sementara masih laporan turunan pelanggan)
- [x] Rekening CRUD.
- [x] User CRUD password update support.

## Stage 6 — Export + Print
- [ ] CSV export helper.
- [ ] Customer export.
- [ ] Paid export.
- [ ] Unpaid export.
- [ ] Invoice export.
- [ ] Receipt/invoice print precision pass.

## Stage 7 — Billing Operations
- [ ] Bulk generate invoice.
- [ ] Bulk arrears/follow-up update.
- [ ] Optional isolir planning field/action.

## Stage 8 — Visual Precision
- [ ] Table button density.
- [ ] Filter form consistency.
- [ ] Footer totals.
- [ ] Mobile/table scroll checks.

## Stage 11 — Customer-first Payment Picker — 2026-07-07
- [x] Tambah tombol **Bayar** langsung pada aksi pelanggan di tabel V3.
- [x] Tombol Input Pembayaran dari detail pelanggan membawa `customer_id` agar identitas otomatis terpilih.
- [x] Form Pembayaran Langganan dibuat customer-first: kolom find pelanggan, select fallback, hasil pencarian klik cepat.
- [x] Panel identitas otomatis menampilkan ID, nama, alamat, telepon, paket, tunggakan, lokasi/router, username/Secret.
- [x] Nominal dan periode otomatis disarankan dari tunggakan terbaru; tetap bisa diedit manual untuk bayar sebagian/kondisi khusus.
- [x] PHP syntax check dan JS syntax check lokal.

## Stage 12 — Flexible Year Filter — 2026-07-07
- [x] Filter Tahun Registrasi tidak lagi hanya bergantung pada tahun yang sudah ada di data.
- [x] Dropdown tahun dibuat fleksibel dari 2010 sampai beberapa tahun ke depan, plus tahun existing dari database.
- [x] Ditambah input manual tahun agar operator bisa mundur/maju ke tahun yang belum muncul di daftar.

## Stage 13 — Admin Login + Hidden Superadmin User Management — 2026-07-07
- [x] Login admin V3 disederhanakan menjadi username + password saja.
- [x] Superadmin utama `ananta` diset di `auth_users` dan tidak ditampilkan di menu Data User.
- [x] Menu Data User sekarang mengelola `auth_users`, sehingga user tambahan langsung bisa login admin.
- [x] Add user berisi nama, username, password, level.
- [x] Detail/Edit user tersedia tanpa menampilkan password lama; password hanya diganti bila diisi baru.
- [x] Delete user melindungi superadmin utama agar tidak terhapus dari menu.

## Stage 14 — Visible Add User Menu + Expired Text Cleanup — 2026-07-07
- [x] Menu Data User/Tambah User dipindah ke grup sidebar khusus **Admin Sistem** agar mudah ditemukan.
- [x] Tombol Add User dibuat lebih jelas di header panel Data User.
- [x] Tulisan `Expired 28-06-2026` di topbar dihapus.

## Stage 15 — DN Customer Code Rule — 2026-07-07
- [x] Auto-generate ID pelanggan sekarang memakai awalan `DN` tanpa tanda minus.
- [x] Input manual pelanggan dinormalisasi otomatis: jika belum diawali `DN`, sistem menambahkan `DN`.
- [x] Form tambah/edit pelanggan diberi hint/pattern DN.
- [x] JavaScript blur/submit membantu operator melihat prefix DN sebelum simpan.
- [x] Data existing non-DN disiapkan untuk normalisasi setelah backup.

## Stage 16 — Anti Double ID Pelanggan — 2026-07-07
- [x] Live DB diaudit: `customer_code` total sama dengan distinct, duplicate group 0.
- [x] Save pelanggan sekarang mengecek `customer_code` setelah normalisasi DN.
- [x] Jika ID sudah dipakai pelanggan lain, proses simpan ditolak dengan pesan jelas.
- [x] Constraint UNIQUE database tetap menjadi lapisan proteksi terakhir.

## Stage 17 — Dynamic Indonesian Month Labels — 2026-07-07
- [x] `month_long()` diganti ke nama bulan Indonesia: Januari, Februari, Maret, dst.
- [x] Judul Data Pembayaran dan Sudah Membayar mengikuti filter `month` yang dipilih.
- [x] Label dashboard seperti Pemasukan/Sudah Bayar/Income tidak lagi statis “Bulan Ini”; mengikuti bulan filter.
- [x] Contoh `month=2026-02` akan tampil `Februari 2026`, bukan `July 2026`.

## Stage 18 — Static Payment Titles — 2026-07-07
- [x] Judul halaman pembayaran dibuat statis agar tidak misleading saat filter AJAX berubah.
- [x] `Data Pembayaran` tidak lagi menampilkan bulan di judul.
- [x] `Data Pelanggan Sudah Membayar` tidak lagi menampilkan bulan di judul.
- [x] `Data Pelanggan Belum Lunas` tidak lagi menampilkan bulan di judul.
- [x] Filter bulan tetap bekerja lewat input `month` dan query data.

## Stage 19 — Grand Totals Ignore Pagination — 2026-07-07
- [x] Footer total Data Pembayaran sekarang memakai `SUM(pay.amount)` untuk seluruh hasil filter/search bulan aktif, bukan hanya rows halaman pagination.
- [x] Label footer diganti menjadi `Grand Total sesuai filter` agar operator paham total bukan subtotal halaman.
- [x] Data Pelanggan Belum Lunas diberi ringkasan `Grand Total Belum Lunas sesuai filter`, jumlah pelanggan, dan jumlah tagihan.
- [x] Ringkasan belum lunas memakai invoice filtered by `month` + search, bukan rows pagination.

## Stage 20 — DENTA NET Logo Replacement — 2026-07-07
- [x] Logo dari attachment user disimpan sebagai `v3/assets/denta-net-logo.jpg`.
- [x] Sidebar/brand aplikasi V3 memakai logo DENTA NET baru.
- [x] Login admin memakai logo DENTA NET baru.
- [x] Kwitansi/print receipt/invoice memakai logo DENTA NET baru.
- [x] CSS logo ditambah agar tampil rapi di sidebar/login.

## Stage 21 — Office Settings and Receipt Receiver Signature — 2026-07-07
- [x] Tambah tabel ringan `app_settings` untuk identitas kantor/kwitansi.
- [x] Tambah menu `Admin Sistem > Pengaturan Kantor`.
- [x] Form setting meliputi brand, perusahaan/grup, alamat kantor, telepon, dan catatan kwitansi.
- [x] Header kwitansi/invoice mengambil identitas kantor dari setting.
- [x] Label petugas/tanda tangan kwitansi diganti konsep menjadi `Penerima`, bukan sales.

## Stage 22 — Receipt Receiver Uses Current Login User — 2026-07-07
- [x] Nama `Penerima` pada kwitansi/invoice tidak lagi mengambil `payments.received_by` lama.
- [x] Saat print, nama penerima memakai `$_SESSION[v3_user][name]` dari user admin yang sedang login.
- [x] Jika session user tidak terbaca, field penerima dikosongkan; tidak fallback ke nama lama seperti Gunawan/ANANTA.

## Stage 23 — Manual Payment History Sync Controls — 2026-07-07
- [x] Detail pelanggan: Riwayat Pembayaran diberi tombol Tambah/Edit/Hapus/Kwitansi.
- [x] Detail pelanggan: Tagihan Belum Lunas diberi tombol Edit/Hapus/Cetak.
- [x] Edit/hapus pembayaran dari detail pelanggan mengembalikan admin ke halaman detail via `return_to`.
- [x] Hapus pembayaran menjalankan `v3_recalc_invoice()` sehingga tagihan/tunggakan sinkron ulang.
- [x] Hapus invoice juga mencoba recalc konteks customer/month.
- [x] Total tunggakan detail pelanggan memakai `balance_amount`/sisa tagihan, bukan nominal penuh invoice mentah.

## Stage 24 — In-App Admin Tutorial — 2026-07-07
- [x] Tambah menu `Admin Sistem > Tutorial Admin`.
- [x] Tutorial menjelaskan alur harian pembayaran, diskon/kurang bayar, koreksi manual, ID DN, filter/grand total, kwitansi, dan pembaruan V3.
- [x] Tutorial dibuat langsung di aplikasi agar admin/operator tidak lupa SOP.

## Stage 25 — Blueprint + GitHub Backup Repo — 2026-07-07
- [x] Blueprint V3 diperbarui dengan operational hardening 2026-07-07.
- [x] Repo backup source ditargetkan ke `git@github.com:remek8787/appsbilling-V3.git`.
- [x] Export repo bersih tanpa DB live/log/backup.
- [x] Commit dan push ke GitHub.

## Stage 26 — Bootstrap CDN UI Experiment — 2026-07-07
- [x] Eksperimen ringan Bootstrap 5.3.3 CDN dipasang sebelum CSS custom V3.
- [x] CSS custom V3 tetap menjadi lapisan override agar flow lama tidak pecah.
- [x] Tambah polish card/table/form/button/sidebar/login.
- [x] Deploy dan smoke test live.
- [ ] Jika jelek, rollback dari backup.

## Stage 27 — Soft Calm Theme Motion — 2026-07-07
- [x] Palet kuning/orange diganti menjadi soft blue/slate/mint.
- [x] Tambah animasi halus card, sidebar nav, table hover, button hover, icon float, dan login logo.
- [x] Menambahkan `prefers-reduced-motion` agar animasi bisa dimatikan otomatis oleh user/device.
- [x] Deploy dan smoke test live.

## Stage 28 — Payment Table Column Order — 2026-07-07
- [x] Urutan kolom tabel pembayaran dirapikan dari `Aksi | No` menjadi `No | Aksi`.
- [x] Tabel pelanggan sudah memakai `No | Aksi`, tidak perlu diubah.

## Stage 29 — Blueprint + GitHub Sync After UI Polish — 2026-07-07
- [x] Blueprint diperbarui untuk Bootstrap CDN experiment, soft calm motion, dan urutan kolom pembayaran.
- [x] Source V3 disinkronkan ke repo backup bersih.
- [x] Commit dan push ke `git@github.com:remek8787/appsbilling-V3.git`.
- [x] Verifikasi tidak ada DB/log/backup yang ikut repo.

## Stage 30 — DN12 Customer ID Rule — 2026-07-07
- [x] Generator ID pelanggan diubah menjadi `DN` + 10 angka random, total 12 karakter.
- [x] Generator mengecek `customers.customer_code` agar tidak double.
- [x] Form tambah/edit diberi pattern `DN[0-9]{10}` dan maxlength 12.
- [x] JavaScript input ID pelanggan menormalisasi tanpa spasi dan angka saja setelah DN.
- [x] Deploy dan smoke test live.
- [x] Push update source ke repo backup V3.

## Stage 31 — Receipt Slip Layout — 2026-07-08
- [x] Kwitansi `print.php` dibuat lebih mirip slip e-Billing Bukti Pembayaran sesuai contoh user.
- [x] Mempertahankan data pelanggan, invoice/payment, terbilang, metode, dan penerima dari flow V3.
- [x] Deploy dan smoke test live.
- [x] Push update source ke repo backup V3.

## Stage 32 — Tailwind CDN Experiment — 2026-07-08
- [x] Tailwind CDN dipasang dengan prefix `tw-` dan preflight off.
- [x] Header/content wrapper diberi polish utility Tailwind prefixed.
- [x] CSS bridge ditambah untuk menjaga tampilan tetap kalem dan tidak merusak flow V3.
- [x] Deploy dan smoke test live.
- [x] Push update source ke repo backup V3.

## Stage 33 — PWA DENTANET Billing OS — 2026-07-08
- [x] Manifest PWA dibuat dengan nama DENTANET Billing OS.
- [x] Service worker dibuat untuk app shell/offline fallback aman.
- [x] Offline page dan icon PWA ditambahkan.
- [x] Layout V3 register service worker dan manifest.
- [x] Deploy dan smoke test live.
- [x] Push update source ke repo backup V3.

## Stage 34 — Reset Pembayaran & History Billing Manual Input — 2026-07-08
- [x] Atas instruksi Tuan Besar, V3 live direset mode B: pembayaran, invoice/tagihan, payment logs, customer events/history billing, dan sync log dikosongkan.
- [x] Data pelanggan tetap dipertahankan: live tetap 1136 pelanggan.
- [x] Data admin/user tetap dipertahankan: `auth_users` 2 dan `users` 3.
- [x] Backup live sebelum reset tersimpan di server: `/home/ubuntu/backups/ebilling-v3-before-payment-history-reset-20260708-055253.sqlite`.
- [x] DB live selesai integrity check: `ok`.
- [x] Salinan lokal `v3/data/ebilling-v3.sqlite` sudah disinkronkan dari live setelah reset.
- [x] Catatan penting: jangan restore DB lokal lama 974 pelanggan ke live; sebelum reset live sudah punya 1136 pelanggan.

## Stage 35 — Penyeragaman Ejaan UI sesuai EYD — 2026-07-08
- [x] Label/menu/tabel V3 dirapikan dari gaya full kapital/title case berlebihan ke ejaan natural, contoh `ID pelanggan`, `Nama pelanggan`, `Data pembayaran`, `Data tagihan`.
- [x] Header tabel tidak lagi dipaksa kapital oleh CSS; tetap mempertahankan kapital teknis seperti `ID`, `DN`, `ODP`, `PPPoE`, `QRIS`, dan brand `DENTA NET`.
- [x] Template kwitansi/print dirapikan: `Bukti pembayaran`, `Nama pembayaran`, `Jumlah bayar`, `Total keseluruhan`, `Sisa tagihan`.
- [x] Perubahan hanya menyentuh file UI/label; database live tidak diubah.

## Stage 36 — Search/Filter Command Bar Polish — 2026-07-08
- [x] Form pencarian/filter utama V3 dibuat lebih tegas seperti command bar: panel gradient lembut, ikon pencarian, border/glow input lebih kuat, tombol Terapkan/Reset lebih jelas.
- [x] Copy pencarian diperjelas: admin diarahkan mencari ID, nama, alamat, telepon, username, atau Secret.
- [x] Loading state AJAX diganti menjadi `Mencari data...` agar lebih sesuai konteks.
- [x] Field cari pelanggan pada form pembayaran juga dipertegas dengan border 2px, glow fokus, dan dropdown hasil yang lebih rapi.
- [x] Perubahan hanya menyentuh UI/CSS/label; database live tidak diubah.

## 2026-07-08 — Activity log / audit trail admin

Status: deployed live to `https://appsbilling.dentasejahteragroup.my.id/v3/`.

Changes:
- Added `activity_logs` table with actor, action, entity, summary, before/after JSON, IP, user agent, timestamp.
- Added helper functions in `v3/app/db.php`: safe audit logging, password/hash masking, changed-field summary.
- Added menu `Admin sistem → Log aktivitas`.
- Added activity timeline page with filters by keyword, module/entity, and action.
- Logged login/logout and create/update/delete/generate actions for key V3 modules: customers, payments, invoices, packages, bank accounts, admin users, office settings.
- Deletion actions from `delete.php` now write audit log before removing records where applicable.

Verification:
- PHP lint passed for core V3 files.
- Live `activity_logs` table initialized successfully under web user.
- Live login page HTTP 200.
- Live admin login with superadmin succeeded.
- Live `index.php?page=activity-log` returned HTTP 200 and displayed `Log aktivitas` + `Audit trail admin`.

Server backup before deploy:
- `/home/ubuntu/backups/appsbilling-v3-before-activity-log-20260708-064853`

## 2026-07-08 — Customer table column preferences + richer last payment

Status: deployed live to `https://appsbilling.dentasejahteragroup.my.id/v3/`.

Changes:
- Added `user_table_preferences` table for per-admin/per-table column layout preferences.
- Added customer table column manager button: `Atur kolom`.
- Admins can choose which customer columns are visible and reorder them from left to right with ↑ / ↓.
- Preferences are saved per username and can be reset to the default layout.
- Improved `Pembayaran terakhir` column: shows last paid amount, paid date/time, invoice period, and payment method.
- Logged save/reset column preference actions into `activity_logs`.

Verification:
- PHP lint passed.
- Live `user_table_preferences` table initialized successfully under web user.
- Live admin login succeeded.
- Live `Data Seluruh Pelanggan` page showed `Atur kolom`, `Pembayaran terakhir`, and the column manager panel.
- Save column preference and reset preference were both tested successfully.

Server backup before deploy:
- `/home/ubuntu/backups/appsbilling-v3-before-column-preferences-20260708-065839`

## 2026-07-08 — Income track record by today/month/year

Status: deployed live to `https://appsbilling.dentasejahteragroup.my.id/v3/`.

Changes:
- Renamed sidebar item from `Ringkasan pendapatan` to `Track record pendapatan`.
- Rebuilt `dashboard-income-summary` page into a revenue tracking dashboard.
- Added KPI cards for today revenue, selected month revenue, selected year revenue, and average daily value for the selected month.
- Added month/year filter.
- Added monthly revenue bar list for the selected year.
- Added payment-method breakdown for the selected month.
- Added daily revenue table for the selected month with link to related payment detail page.

Verification:
- PHP lint passed.
- Live admin login succeeded.
- Live `index.php?page=dashboard-income-summary&year=2026&month=2026-07` returned HTTP 200.
- Verified page contains `Track record pendapatan`, `Hari ini`, `Pendapatan per bulan 2026`, `Track record harian`, and `income-hero` markup.
- Fixed PHP live runtime issue caused by `max()` spread with associative month keys.

Server backup before deploy:
- `/home/ubuntu/backups/appsbilling-v3-before-income-track-record-20260708-070420`

## 2026-07-08 — Income dashboard visual polish + flexible year filter

Status: deployed live to `https://appsbilling.dentasejahteragroup.my.id/v3/`.

Changes:
- Polished Track record pendapatan hero into a more premium finance dashboard summary.
- Highlighted selected month revenue as the main hero number.
- Added month navigation: previous month and next month.
- Added manual year input so admin can view years outside existing payment data, not only 2026.
- Added quick year dropdown covering current year + future/past range while still accepting manual years.
- Synced manual year input, year dropdown, and month input through `v3-ajax.js`.

Verification:
- PHP lint passed.
- Live tests passed for `year=2027&month=2027-01`, `year=2025&month=2025-12`, and `year=2026&month=2026-07`.
- Verified live page contains premium hero markup, manual year field, monthly yearly title, previous/next month links, and reset-to-current-month action.

Server backup before deploy:
- `/home/ubuntu/backups/appsbilling-v3-before-income-polish-year-flex-20260708-071019`

## 2026-07-08 — Column manager UI/UX polish

Status: deployed live to `https://appsbilling.dentasejahteragroup.my.id/v3/`.

Changes:
- Improved `Atur kolom` customer table UI into a clearer column manager panel.
- Added quick presets: Default, Ringkas, Billing, and Teknis.
- Added active column counter and per-admin label.
- Improved JavaScript binding so presets reorder and toggle columns automatically.
- Added clearer off-state for hidden columns.
- Hidden column manager from the dashboard mini table; it now appears on full customer data pages only.
- Kept per-username saved preferences and activity logging.

Verification:
- PHP lint passed.
- Live `Data Seluruh Pelanggan` page returned HTTP 200 and showed `column-manager-pro`, preset buttons, Billing preset, and Teknis preset.
- Live save using Billing column set succeeded.
- Live reset to default succeeded.
- Live Dashboard confirmed the column manager is hidden in the mini customer table.

Server backup before deploy:
- `/home/ubuntu/backups/appsbilling-v3-before-column-manager-uiux-20260708-071556`

## 2026-07-08 — Column manager visible panel fix

Status: deployed live to `https://appsbilling.dentasejahteragroup.my.id/v3/`.

Reason:
- User screenshot showed the Atur kolom area still looked unclear/empty and likely affected by old cached CSS/JS/collapse behavior.

Changes:
- Converted customer column manager from Bootstrap collapse into native `<details open>` visible panel.
- Panel is now open by default and clearly shows presets + column list without relying on Bootstrap collapse JS.
- Added stronger visual container, sticky action area, scrollable column list, and table horizontal-scroll hint.
- Hid column manager in dashboard mini table via both PHP render flag and CSS safety.
- Bumped frontend asset versions to `20260708-columnpanel` in layout to bypass stale browser/WebView cache.

Verification:
- PHP lint passed.
- Live `Data Seluruh Pelanggan` returned HTTP 200.
- Verified live HTML contains `column-manager-visible`, native `open><summary`, `Atur kolom tabel`, `column-preset`, `customer-dynamic-table`, and the new asset version `20260708-columnpanel`.

Server backup before deploy:
- `/home/ubuntu/backups/appsbilling-v3-before-column-visible-panel-20260708-073052`

## 2026-07-08 — Admin user last login tracking

Status: deployed live to `https://appsbilling.dentasejahteragroup.my.id/v3/`.

Changes:
- Added `auth_users` columns: `last_login_at`, `last_login_ip`, `last_login_agent`, `login_count`.
- Successful login now updates the user row with last login timestamp, IP, user agent, and increments login count.
- Data user Admin table now displays Login terakhir, IP terakhir, and Total login.
- Existing activity log login event remains active and now includes IP in the after payload.

Verification:
- PHP lint passed.
- Live schema migration initialized successfully under web user.
- Live login succeeded.
- Live `Data user Admin` page returned HTTP 200 and displayed `Login terakhir`, `IP terakhir`, `Total login`, and styled `login-last-cell` markup.

Server backup before deploy:
- `/home/ubuntu/backups/appsbilling-v3-before-user-last-login-20260708-073531`

## 2026-07-08 — Hide sidebar account number + current V3 concept note

Status: deployed live to `https://appsbilling.dentasejahteragroup.my.id/v3/`.

Changes:
- Removed account number display from sidebar user panel.
- Sidebar now shows admin name and level only, e.g. `Superadmin`, without `· 6778`.
- Kept internal account constant/database field intact for backend compatibility.

Verification:
- PHP lint passed.
- Live Dashboard returned HTTP 200.
- Verified `Superadmin · 6778` and `· 6778` are no longer present in live rendered HTML.
- Verified `<small>Superadmin</small>` is present.

Server backup before deploy:
- `/home/ubuntu/backups/appsbilling-v3-before-hide-sidebar-account-20260708-073832`

## 2026-07-09 — Custom tanggal pembayaran admin

Status: deployed live to `https://appsbilling.dentasejahteragroup.my.id/v3/`.

Changes:
- Form tambah pembayaran mengganti label `Waktu entri` menjadi `Tanggal pembayaran`.
- Field tanggal pembayaran memakai input `datetime-local`, sehingga admin bisa memilih tanggal/jam bayar secara manual.
- Edit pembayaran juga memakai `Tanggal pembayaran` yang bisa diubah kapan saja oleh admin.
- Backend menormalisasi input tanggal pembayaran menjadi format `YYYY-MM-DD HH:MM:SS` agar data tetap konsisten.
- Tabel Data pembayaran dan Detail pelanggan menampilkan tanggal pembayaran dari `payments.paid_at`.
- SOP UI diperjelas: tanggal pembayaran boleh berbeda dari jatuh tempo, contoh jatuh tempo tanggal 20 tapi bayar tanggal 1, atau sebaliknya.
- Tidak mengubah konsep jatuh tempo invoice/customer; `due_day` tetap sebagai tanggal tagihan, sedangkan `paid_at` menjadi tanggal real pembayaran.

Verification:
- PHP lint passed locally for `v3/app/db.php`, `v3/index.php`, `v3/edit.php`, `v3/detail.php`, and `v3/print.php`.
- Server-side PHP lint passed before deployment for `v3/app/db.php`, `v3/index.php`, `v3/edit.php`, and `v3/detail.php`.
- Live admin login succeeded.
- Live `index.php?page=add-laporan-ipl` returned HTTP 200 and contains `Tanggal pembayaran` + `datetime-local`; old label `Waktu entri` is absent.
- Live `index.php?page=data-ipl` returned HTTP 200 and contains `Tanggal pembayaran`; old label `Waktu entri` is absent.

Server backup before deploy:
- `/home/ubuntu/backups/appsbilling-v3-before-custom-payment-date-20260709-130126`

Local backup before patch:
- `backups/v3-custom-payment-date-20260709-125838`

## 2026-07-09 — Column manager default closed

Status: deployed live to `https://appsbilling.dentasejahteragroup.my.id/v3/`.

Reason:
- Tuan Besar requested the `Atur kolom` area not to appear expanded by default because it takes too much space on customer pages.

Changes:
- Removed the `open` attribute from the native `<details>` column manager, so it starts closed/auto-hidden.
- Kept the summary button visible with active column count and per-admin note.
- Bumped CSS/JS query versions to `20260709-columnclosed`.
- Bumped service worker cache name to force PWA/WebView refresh.

Verification target:
- Live HTML should contain `column-manager-visible` and `Atur kolom tabel`, but should not contain `column-manager-visible" open`.

## 2026-07-09 — Payment save stay on customer + confirmation panel

Status: deployed live to `https://appsbilling.dentasejahteragroup.my.id/v3/`.

Reason:
- Tuan Besar requested a smoother admin flow after saving a payment: show a confirmation while keeping the last customer selected, so the admin can print receipt, view customer detail, input another payment for the same customer, or choose another customer.

Changes:
- `save_payment` now stores a one-time `payment_success` session payload after insert/update.
- After saving, default redirect goes back to `index.php?page=add-laporan-ipl&customer_id={id}&month={month}&saved_payment={payment_id}` instead of going to the payment table.
- Payment form now renders a green success panel with:
  - saved customer code/name,
  - period, amount, method,
  - buttons: Cetak struk, Detail pelanggan, Input lagi pelanggan ini, Pilih pelanggan lain.
- Last customer stays selected after save.
- Safety: after a successful payment, the amount field is cleared so admin does not accidentally submit the same payment twice.
- Added responsive CSS for `.payment-success-panel`.
- Bumped frontend/PWA asset version to `20260709-payconfirm`.

Verification:
- PHP lint passed locally and on server for `v3/index.php` and `v3/app/layout.php`.
- Live files verified contain `payment_success`, `payment-success-panel`, `saved_payment`, and asset version `20260709-payconfirm`.
- CSS asset live HTTP 200 contains `.payment-success-panel`.
- Service worker live HTTP 200 contains `20260709-payconfirm`.
- No dummy payment transaction was created for testing, to keep live payment data clean.

Server backup before deploy:
- `/home/ubuntu/backups/appsbilling-v3-payment-stay-confirm-20260709-152809`

Local backup before patch:
- `backups/v3-payment-stay-confirm-20260709-152625`

## 2026-07-09 — Payment transfer bank destination selector

Status: deployed live to `https://appsbilling.dentasejahteragroup.my.id/v3/`.

Reason:
- Tuan Besar requested that when admin chooses payment method `Transfer`, the admin can also choose the destination bank/rekening.

Changes:
- Payment form `add-laporan-ipl` now has `Rekening / bank tujuan` selector.
- Selector is shown/enabled for `Transfer`, `QRIS`, and `Deposit`; hidden/disabled for `Cash`.
- Payment save now persists `bank_account_id` into existing `payments.bank_account_id` column.
- Edit payment screen now supports changing the destination bank/rekening.
- Payment tables, detail customer history, and receipt print now display method as `Transfer → Bank · No Rekening · a.n. ...` when bank account exists.
- Search in payment list also matches bank name, account number, and account name.
- PWA/frontend asset version bumped to `20260709-bankdest`.

Verification:
- PHP lint passed for `v3/app/db.php`, `v3/index.php`, `v3/edit.php`, `v3/detail.php`, `v3/print.php`, `v3/app/layout.php`, and `v3/login.php`.
- JS syntax check passed for `v3/assets/v3-ajax.js`.
- Server deploy completed after live backup.
- Live DB check as `www-data`: active bank accounts = 2.
- Live HTTP checks passed: login references `20260709-bankdest`, CSS contains `.payment-bank-field`, JS contains `bindPaymentBankDestination`, service worker cache is `dentanet-billing-os-v1-20260709-bankdest`.
- Logged-in render check for `index.php?page=add-laporan-ipl` confirmed `payment-bank-field`, BRI option, and `payment-method-select` exist. No payment transaction was created during verification.

Server backup before deploy:
- `/home/ubuntu/backups/appsbilling-v3-payment-bank-destination-20260709-160955`

Local backup before patch:
- `backups/v3-payment-bank-destination-20260709-160449`

## 2026-07-09 — Payment input discount nominal/percent

Status: deployed live to `https://appsbilling.dentasejahteragroup.my.id/v3/`.

Reason:
- Tuan Besar requested discount support directly in the payment input flow, with discount entered either as percentage or nominal amount.

Changes:
- `add-laporan-ipl` payment form now includes:
  - `Diskon` type selector: `Rp` or `%`
  - discount value field
  - realtime discount preview
  - discount note field
- JavaScript recalculates suggested payment amount live:
  - nominal: final = original - discount nominal
  - percent: final = original - rounded percentage discount
  - discount is capped so it cannot exceed original invoice amount
- `save_payment` now applies discount to the invoice for that customer/month:
  - preserves/sets `original_amount`
  - writes `discount_amount`
  - writes `discount_note`
  - sets final invoice `amount = original_amount - discount_amount`
  - then recalculates invoice status/balance via `v3_recalc_invoice()`
- Success confirmation now also mentions discount when used.
- Asset/PWA version bumped to `20260709-paydiscount`.

Verification:
- PHP lint passed for `v3/index.php`, `v3/app/layout.php`, and `v3/login.php`.
- JS syntax check passed for `v3/assets/v3-ajax.js`.
- Local formula sanity checks passed for nominal and percent discount.
- Live deploy completed after server backup.
- Logged-in render check for `index.php?page=add-laporan-ipl` confirmed `payment-discount-type`, `payment-discount-value`, and `payment-discount-preview` exist.
- Live CSS/JS/service-worker checks passed for version `20260709-paydiscount`.
- No payment transaction was created during verification.

Server backup before deploy:
- `/home/ubuntu/backups/appsbilling-v3-payment-discount-input-20260709-162416`

Local backup before patch:
- `backups/v3-payment-discount-input-20260709-162037`

## 2026-07-09 — Payment no-double guard per customer/month

Status: deployed live to `https://appsbilling.dentasejahteragroup.my.id/v3/`.

Reason:
- Tuan Besar requested prevention so the same customer/month cannot be input as multiple payment transactions by accident.

Changes:
- Server-side guard in `save_payment`:
  - before inserting/updating a payment, checks existing `payments` for the same `customer_id + invoice_month`.
  - new input is rejected if a payment already exists for that customer/month.
  - edit flow is still allowed for the same payment id.
  - rejection redirects back to payment form with a clear flash message instructing admin to edit the existing payment instead.
- Frontend guard in `add-laporan-ipl`:
  - customer JSON includes `paid_months` summary.
  - when admin selects a customer/month already paid, a warning panel appears.
  - Save button is disabled and renamed to `Pembayaran bulan ini sudah ada`.
  - quick button opens `Edit pembayaran lama`.
- Asset/PWA version bumped to `20260709-nodouble`.

Verification:
- PHP lint passed for `v3/index.php`, `v3/app/layout.php`, and `v3/login.php`.
- JS syntax check passed for `v3/assets/v3-ajax.js`.
- Live render check after login confirmed `payment-duplicate-slot`, `paid_months`, JS `checkDuplicate`, and CSS `.payment-duplicate-warning` exist.
- Backend guard test used existing tuple `customer_id=16`, `invoice_month=2026-06`, `amount=250000`; attempted duplicate POST was blocked and payment count stayed `1 -> 1`.
- No new duplicate transaction was created during verification.

Server backup before deploy:
- `/home/ubuntu/backups/appsbilling-v3-payment-no-double-month-20260709-165955`

Local backup before patch:
- `backups/v3-payment-no-double-month-20260709-165913`

## 2026-07-09 — Payment percent discount fix

Status: deployed live to `https://appsbilling.dentasejahteragroup.my.id/v3/`.

Reason:
- Tuan Besar reported nominal/Rupiah discount works, but percentage discount was not running correctly.

Changes:
- Backend discount type parsing is now tolerant:
  - `percent`
  - `%`
  - `persen`
  - `percentage`
  - `pct`
- Backend now uses `$discountIsPercent` instead of relying only on exact `discount_type === 'percent'`.
- Frontend JS uses the same tolerant list and preview now displays the percent value before the calculated Rupiah discount, e.g. `Diskon 10% Rp. 25.000`.
- Asset/PWA version bumped to `20260709-percentfix`.

Verification:
- PHP lint passed for `v3/index.php`, `v3/app/layout.php`, and `v3/login.php`.
- JS syntax check passed for `v3/assets/v3-ajax.js`.
- Local formula test: `10%` from `250000` = discount `25000`, final `225000`.
- Live render check confirmed percent option exists.
- Live JS and service worker are on `20260709-percentfix`.
- Live server formula test as `www-data` confirmed all variants (`percent`, `%`, `persen`, `percentage`, `pct`) produce `25000` discount and `225000` final from base `250000`.
- No payment transaction was created during verification.

Server backup before deploy:
- `/home/ubuntu/backups/appsbilling-v3-payment-percent-discount-fix-20260709-171515`

Local backup before patch:
- `backups/v3-payment-percent-discount-fix-20260709-171353`

## 2026-07-10 — Corporate Billing Stage 1

Status: deployed live to `https://appsbilling.dentasejahteragroup.my.id/v3/`.

Reason:
- Tuan Besar wanted a separate Corporate customer/invoice flow that does not mix with retail/main customer data.
- First stage should be safe and manual so admin can test input/cetak without disrupting retail billing.

Changes:
- Added sidebar menu **Corporate** with:
  - Data Corporate
  - Tambah Corporate
  - Tagihan Corporate
  - Buat Tagihan Corporate
- Added isolated tables:
  - `corporate_customers`
  - `corporate_invoices`
  - `corporate_payments`
- Added actions:
  - `save_corporate_customer`
  - `save_corporate_invoice`
  - `save_corporate_payment`
- Added manual Corporate invoice print support:
  - `print.php?type=corporate_invoice&id=...`
- Retail customer/payment/invoice tables and flow were not merged with Corporate.

Verification:
- Local PHP lint passed:
  - `v3/app/db.php`
  - `v3/app/layout.php`
  - `v3/index.php`
  - `v3/print.php`
- Local schema smoke test confirmed the three corporate tables exist.
- Live deploy completed via SSH to `43.134.122.109` path `/var/www/appsbilling.dentasejahteragroup.my.id/v3`.
- Live PHP lint passed for the same four files.
- Live schema smoke test as `www-data` confirmed:
  - `corporate_customers=ok`
  - `corporate_invoices=ok`
  - `corporate_payments=ok`
- Live unauthenticated route check for `index.php?page=corporate-customers` returns `302 Location: login.php`, so auth protection remains active.

Server backup before deploy:
- `/home/ubuntu/backups/appsbilling-v3-corporate-stage1-20260710-151519`

Next:
- Test manually through UI: add one dummy corporate, create one invoice, input payment, print invoice.
- If approved, continue with edit/nonaktif corporate, receipt print, filters, and B2B invoice polish.

## Update 2026-07-10 — Separate Pelanggan OFF / Nonaktif

Status: deployed live to `https://appsbilling.dentasejahteragroup.my.id/v3/`.

Changes:
- `Data pelanggan` is now focused on active customers only (`customer_status='active'`).
- Added separate menu/page `Pelanggan OFF / Nonaktif` via `page=data-pelanggan-off`.
- Added separate form `Tambah pelanggan OFF` via `page=add-pelanggan-off` with default status OFF.
- Saving customers now keeps `customer_status` and legacy `is_active` aligned: active = `is_active=1`, non-active/OFF/isolir = `is_active=0`.
- Bulk invoice candidate query tightened to active customers only: `customer_status='active' AND is_active=1`.
- UI styling differentiates active customer workspace and OFF/nonactive archive workspace.

Live verification:
- PHP lint passed for `index.php`, `app/layout.php`, and `app/db.php` on live server.
- Live DB check as `www-data`: `active=934`, `off=201`, `generate_candidates=925`.
- Public unauth route checks for `data-pelanggan-off` and `add-pelanggan-off` return expected login redirect (`HTTP/1.1 302 Found`).

Rollback backup:
- `/home/ubuntu/backups/appsbilling-v3-off-customers-20260710-164209`

## Update 2026-07-10 — Separate Pelanggan FREE / Gratis

Status: deployed live to `https://appsbilling.dentasejahteragroup.my.id/v3/`.

Changes:
- Added separate menu/page `Pelanggan FREE / Gratis` via `page=data-pelanggan-free`.
- `Data Pelanggan Aktif` is now treated as active paid customers only: `customer_status='active' AND package price > 0`.
- FREE customers are active customers whose package/service price is `0`, shown separately from paid active customers.
- OFF/nonactive customers remain in `page=data-pelanggan-off`; OFF customers with Rp0 package are not mixed into FREE.
- Price column now labels Rp0 packages as `FREE / Gratis`.
- Dashboard summary now separates active paid customers, FREE customers, and OFF/nonactive customers.
- Bulk invoice generation already remains limited to paid active customers only: `customer_status='active' AND is_active=1 AND price > 0`.

Live verification:
- PHP lint passed for `index.php` and `app/layout.php` on live server.
- Live DB check as `www-data`: `paid_active=925`, `free_active=9`, `off=201`, `generate_candidates=925`.
- Public unauth route checks for `data-pelanggan-free` and `data-warga` return expected login redirect (`HTTP/1.1 302 Found`).

Rollback backup:
- `/home/ubuntu/backups/appsbilling-v3-free-customers-20260710-165202`

## Update 2026-07-10 — Customer Action Center + Natural Operator Copy

Status: deployed live to `https://appsbilling.dentasejahteragroup.my.id/v3/`.

Changes:
- Added quick customer action flow via POST action `quick_customer_action`.
- Customer tables now show practical quick buttons beside normal actions:
  - `OFF-kan` — moves active customer to OFF and excludes from paid active list/billing generation.
  - `FREE` — moves active paid customer to the first active Rp0 package and keeps customer active as FREE/Gratis.
  - `Aktifkan` — reactivates OFF/isolir customers; if package is Rp0, they land in FREE.
  - `Isolir` — available from detail page for field/operator cases.
- Detail pelanggan now has an `Aksi cepat pelanggan` panel for common field workflows: berhenti, reconnect, digratiskan sementara, isolir.
- Added customer segment bar across customer list pages:
  - Berbayar
  - FREE / Gratis
  - OFF / Nonaktif
  - Belum bayar
- Copywriting was rewritten to feel like real operator/admin language, not generic AI/template text.
- All quick actions write to `customer_events` and `activity_logs` for auditability.

Safety:
- Quick actions do not delete customer data.
- FREE action requires at least one active Rp0 package.
- Existing invoice/payment history remains untouched.
- Billing generation remains restricted to active paid customers only.

Live verification:
- PHP lint passed for `index.php` and `detail.php` on live server.
- Live DB check as `www-data`: `paid_active=925`, `free_active=9`, `off=201`, `free_packages=2`.
- Public unauth route checks return expected login redirect (`HTTP/1.1 302 Found`).

Rollback backup:
- `/home/ubuntu/backups/appsbilling-v3-customer-action-center-20260710-235032`

## Update 2026-07-10 — Customer Button Spacing Refinement

Status: deployed live to `https://appsbilling.dentasejahteragroup.my.id/v3/`.

Changes:
- Customer table action buttons were simplified to reduce cramped rows.
- Main visible actions are now only:
  - `Bayar`
  - `Detail`
  - `Kelola ▾`
- Secondary and risky actions moved into the `Kelola` dropdown:
  - Edit pelanggan
  - Pindahkan ke OFF
  - Jadikan FREE / Gratis
  - Tandai isolir
  - Aktifkan lagi
  - Hapus data
- CSS spacing refined for desktop and mobile table rows.
- Detail page action panel remains available for expanded workflow.

Live verification:
- PHP lint passed for `index.php` on live server.
- Route smoke tests for `data-warga`, `data-pelanggan-free`, and `data-pelanggan-off` return expected login redirect (`HTTP/1.1 302 Found`).
- Live DB sanity check: `paid_active=925`, `free_active=9`.

Rollback backup:
- `/home/ubuntu/backups/appsbilling-v3-customer-button-spacing-20260710-235702`

## Update 2026-07-11 — Customer Button Order

Status: deployed live to `https://appsbilling.dentasejahteragroup.my.id/v3/`.

Change:
- Customer table visible action order adjusted to: `Bayar | Detail | Edit | Kelola`.
- `Edit` is visible again because it is a common operator action.
- `Kelola` now focuses on status/risky actions only: OFF, FREE, isolir, active again, delete.

Live verification:
- PHP lint passed for `index.php` on live server.
- Route smoke test for `data-warga` returns expected login redirect (`HTTP/1.1 302 Found`).

Rollback backup:
- `/home/ubuntu/backups/appsbilling-v3-customer-button-order-20260711-000019`

## Update 2026-07-11 — Tutorial Admin + Changelog

Status: deployed live to `https://appsbilling.dentasejahteragroup.my.id/v3/`.

Changes:
- `Admin Sistem → Tutorial Admin` redesigned into a practical operator guide.
- Added natural, field-friendly copy so the page does not feel like generic AI/manual text.
- Added quick anchor links:
  - Alur bayar
  - Kelola pelanggan
  - Changelog
  - Corporate
- Added sections for:
  - Daily payment workflow
  - Discount / partial payment / balance logic
  - Customer action button meaning: Bayar, Detail, Edit, Kelola
  - Active paid vs FREE vs OFF vs isolir
  - Manual correction workflow
  - DN customer ID and receipt identity
  - Corporate Billing basics
- Added `Changelog / Apa Itu Update Ini?` explaining that changelog is a change history for admins/operators.
- Changelog includes recent updates: button order, Customer Action Center, FREE customers, OFF customers, Corporate Billing Stage 1, flexible payment dates, PWA, DN12 ID.

Live verification:
- PHP lint passed for `index.php` on live server.
- Public route smoke test for `page=admin-tutorial` returns expected login redirect (`HTTP/1.1 302 Found`).
- DB sanity check: `customers=1135`, `activity_logs=503`.

Rollback backup:
- `/home/ubuntu/backups/appsbilling-v3-admin-tutorial-changelog-20260711-001608`

## Update 2026-07-11 — Direct Google Maps Coordinate Link

Status: deployed live to `https://appsbilling.dentasejahteragroup.my.id/v3/`.

Changes:
- Google Maps helper now uses direct coordinate search format:
  - `https://www.google.com/maps/search/?api=1&query=lat,lng`
- Detail Pelanggan Tikor row is now clickable and opens Google Maps directly.
- Detail map header now includes `Salin Tikor` button for quick copy.
- Empty map copy updated: admin can paste coordinates like `-8.30410006093805, 112.49401918132114` into Tikor/Koordinat.

Verification:
- PHP lint passed for `app/db.php` and `detail.php` on live server.
- Helper output test for `-8.30410006093805, 112.49401918132114`:
  - `https://www.google.com/maps/search/?api=1&query=-8.30410006093805%2C112.49401918132114`
- Public detail route returns expected login redirect (`HTTP/1.1 302 Found`).

Rollback backup:
- `/home/ubuntu/backups/appsbilling-v3-google-maps-direct-20260711-011303`

## Update 2026-07-11 — Customer Action Grid Fix

Status: deployed live to `https://appsbilling.dentasejahteragroup.my.id/v3/`.

Change:
- Fixed crowded customer action UI after visible order `Bayar | Detail | Edit | Kelola` was restored.
- Customer actions now render as a compact 2-column grid inside the action column so the table does not stretch sideways.
- `Kelola` dropdown remains for status/risky actions only.

Verification:
- PHP lint passed for `index.php` on live server.
- Route smoke test for `data-warga` returns expected login redirect (`HTTP/1.1 302 Found`).

Rollback backup:
- `/home/ubuntu/backups/appsbilling-v3-customer-action-grid-fix-20260711-011802`

## Update 2026-07-11 — Action Grid Cache Fix + Location Shortcut

Status: deployed live to `https://appsbilling.dentasejahteragroup.my.id/v3/`.

Changes:
- CSS asset version bumped from `20260710-mapwide2` to `20260711-actiongrid-location` to force browsers to load the latest action-grid styles.
- Customer action grid class changed to a stricter `customer-actions-grid-v2` with fixed two-column button widths to avoid intermittent messy layouts.
- Added `Pergi ke Lokasi` inside `Kelola` dropdown when the customer has valid latitude/longitude.
- `Pergi ke Lokasi` uses the existing direct Google Maps coordinate helper.

Verification:
- PHP lint passed for `index.php` and `app/layout.php` on live server.
- Live layout contains `adminlte-clone.css?v=20260711-actiongrid-location`.
- Live `index.php` contains `Pergi ke Lokasi`.
- Route smoke test for `data-warga` returns expected login redirect (`HTTP/1.1 302 Found`).

Rollback backup:
- `/home/ubuntu/backups/appsbilling-v3-actiongrid-location-cachefix-20260711-012423`

## Update 2026-07-11 — Action Stack V3 + Visible Location Button

Status: deployed live to `https://appsbilling.dentasejahteragroup.my.id/v3/`.

Reason:
- The previous 2-column action grid could still appear clipped in dense customer tables on some browsers/layout widths.

Changes:
- CSS asset version bumped to `20260711-actionstack-location2`.
- Customer action UI changed from grid to a safe vertical stack:
  - Bayar
  - Detail
  - Edit
  - Lokasi (only if coordinates exist)
  - Kelola
- `Kelola` remains for status/risky actions: OFF, FREE, isolir, active again, delete.
- Detail Pelanggan now has a visible `Pergi ke titik lokasi` panel above the map preview when coordinates exist.
- The visible `Lokasi` / `Pergi ke Lokasi` buttons use the direct Google Maps coordinate URL helper.

Verification:
- PHP lint passed for `index.php`, `detail.php`, and `app/layout.php` on live server.
- Live layout contains `adminlte-clone.css?v=20260711-actionstack-location2`.
- Live index contains `action-stack-v3` and `Lokasi` button.
- Live detail contains `Pergi ke titik lokasi` panel.
- Route smoke test for `data-warga` returns expected login redirect (`HTTP/1.1 302 Found`).

Rollback backup:
- `/home/ubuntu/backups/appsbilling-v3-action-stack-location2-20260711-013020`

## Update 2026-07-11 — Tikor Save Fallback + Clear Error

Status: deployed live to `https://appsbilling.dentasejahteragroup.my.id/v3/`.

Reason:
- Operator reported coordinates were filled but location buttons did not appear.
- Parser accepts the coordinate format, so the likely issue was save UX: coordinates may be pasted in the wrong location field or fail silently.

Changes:
- `save_customer` now reads coordinates from `tikor`; if `tikor` is empty but `area_name` contains coordinate-like text, it also parses `area_name` as fallback.
- If `tikor` is filled but cannot be parsed, the app now shows a clear flash message instead of silently saving empty map fields.
- Edit form hint now explicitly says to paste Google Maps coordinates, example `-8.30410006093805, 112.49401918132114`.

Verification:
- PHP lint passed for `index.php` and `edit.php` on live server.
- Live parser test: `-8.30410006093805, 112.49401918132114` -> `-8.3041001,112.4940192`.
- Live files contain tikor feedback and updated edit hint.

Rollback backup:
- `/home/ubuntu/backups/appsbilling-v3-tikor-save-fallback-20260711-014045`

## Update 2026-07-11 — Dashboard Announcement + Human Admin Tutorial Copy

Status: deployed live to `https://appsbilling.dentasejahteragroup.my.id/v3/`.

Reason:
- User asked to update admin announcement and tutorial admin, add fast dashboard links to trigger/remind admins, and keep copywriting natural/human (not AI-looking).

Changes:
- Dashboard now includes a visible admin announcement panel:
  - "Pengingat kerja hari ini"
  - reminder to check arrears first, input payment next, and not rush deleting old history.
- Dashboard now includes quick trigger cards:
  - Data aktif
  - Kejar tunggakan
  - Track income
  - SOP admin
  - Map pelanggan
  - Log aktivitas
- Existing PSB card header now shows actual PSB count and value, not a hardcoded-looking "6 PSB" title.
- Dashboard reminder card now nudges admin to open Detail Pelanggan before deleting/correcting old invoice/payment history.
- Tutorial Admin copy rewritten with a more natural operator tone:
  - no stiff manual tone
  - clearer daily payment flow
  - Tikor/location guide
  - safe correction workflow
  - dashboard quick link explanation
  - updated changelog
- CSS cache-busting version updated to `adminlte-clone.css?v=20260711-admin-announcement-tutorial`.

Verification:
- Local PHP lint passed: `index.php`, `app/layout.php`.
- Live PHP lint passed: `/var/www/appsbilling.dentasejahteragroup.my.id/v3/index.php`, `/var/www/appsbilling.dentasejahteragroup.my.id/v3/app/layout.php`.
- Live markers verified:
  - `dashboard_announcement=ok`
  - `tutorial_copy=ok`
  - `changelog=ok`
  - `css_version=ok`
  - `css_dashboard_triggers=ok`

Rollback backup:
- `/home/ubuntu/backups/appsbilling-v3-admin-announcement-tutorial-20260711-021200`

## Update 2026-07-11 — Dashboard Compact Action Buttons

Status: deployed live.

Reason:
- User reported dashboard UI looked messy because quick buttons around `Data sudah bayar` / `Input pembayaran` / `Tutorial` were too large.

Changes:
- Dashboard announcement action buttons reduced to compact pill sizing.
- PSB/customer card header actions are now compact pills with smaller font/padding.
- Card header now wraps cleanly and keeps the title readable.
- CSS cache-busting updated to `adminlte-clone.css?v=20260711-dashboard-compact-actions`.

Verification:
- Local lint passed: `app/layout.php`, `index.php`.
- Live lint passed: `/var/www/appsbilling.dentasejahteragroup.my.id/v3/app/layout.php`.
- Live markers: `css_version=ok`, `compact_css=ok`.

Rollback backup:
- `/home/ubuntu/backups/appsbilling-v3-dashboard-compact-actions-20260711-021652`

## Update 2026-07-11 — Dashboard Announcement Compact Strip

Status: deployed live.

Reason:
- User screenshot showed the dashboard announcement panel was too wide/tall with too much empty space.

Changes:
- Converted dashboard announcement from large hero-like panel into compact reminder strip.
- Reduced padding, border radius, heading size, paragraph size, and button size.
- Paragraph is clamped to avoid tall empty dashboard area.
- CSS cache-busting updated to `adminlte-clone.css?v=20260711-dashboard-reminder-strip`.

Verification:
- Local/live `app/layout.php` lint passed.
- Live markers: `css_version=ok`, `strip_css=ok`.

Rollback backup:
- `/home/ubuntu/backups/appsbilling-v3-dashboard-reminder-strip-20260711-021941`

## Stage 12 — Tim Penagihan / Kolektor (2026-07-11)
- [x] Master petugas penagih aktif/nonaktif.
- [x] Centang invoice pelanggan belum lunas dari halaman Belum Bayar.
- [x] Jasa kolektif fleksibel per pelanggan/batch (contoh Rp3.000 / Rp5.000).
- [x] Validasi invoice tidak dapat masuk ke dua batch aktif.
- [x] Daftar siap cetak per petugas dan periode.
- [x] Lembar penagihan mengikuti identitas kwitansi existing, tetapi tegas `BUKAN BUKTI LUNAS`.
- [x] Cetak tidak mengubah invoice dan tidak membuat payment.
- [x] Laporan item: pending, ditemui, uang dilaporkan, gagal, janji bayar.
- [x] Tombol Input pembayaran kembali ke form pembayaran normal V3.
- [x] Activity log untuk petugas, pembuatan batch, dan laporan.
- [x] Backup live sebelum implementasi: `/home/ubuntu/backups/appsbilling-v3-before-collection-team-20260711-160943`.
- [x] PHP syntax, schema migration, integrity test, authenticated live smoke test.

## Stage 12.1 — Koreksi Tim Penagihan + UI/Cetak (2026-07-11)
- [x] Lembar Penagihan mengikuti visual kwitansi existing dan menampilkan logo kantor.
- [x] Hapus label `BUKAN BUKTI LUNAS`; judul dokumen cukup `Lembar Penagihan`.
- [x] Edit petugas penagih; hapus jika belum dipakai, nonaktifkan jika sudah punya histori.
- [x] Edit petugas, jasa default, jasa per pelanggan, status, dan catatan batch.
- [x] Tambah/keluarkan pelanggan dari batch pada seluruh status.
- [x] Koreksi dan hapus batch tetap tersedia walaupun batch sudah ditutup.
- [x] Penghapusan batch/item hanya menyentuh data Tim Penagihan, tidak invoice/payment.
- [x] UI Siap Cetak dirombak menjadi operator workspace dengan hero CTA dan batch cards.
- [x] Tutorial Admin ditambah SOP Tim Penagihan dan koreksi manual.
- [x] Dashboard announcement diperbarui untuk fitur Tim Penagihan.
- [x] PHP syntax, authenticated live smoke test, forbidden-label test, dan integrity check lolos.
- [x] Backup: `/home/ubuntu/backups/appsbilling-v3-before-collection-correction-20260711-162830` dan DB pasangannya.

## Stage 12.2 — Pencarian Pelanggan Tim Penagihan (2026-07-11)
- [x] Live search saat membuat batch: ID, nama, alamat, telepon, periode, nominal.
- [x] Live search saat menambah pelanggan ke batch: ID, nama, periode, nominal.
- [x] Pilihan checkbox bertahan ketika kata pencarian berubah.
- [x] Indikator jumlah pelanggan dipilih dan jumlah hasil terlihat.
- [x] Tombol Pilih semua hasil pencarian dan Kosongkan pilihan.
- [x] Footer jumlah pelanggan siap ditambahkan.
- [x] Responsive toolbar untuk mobile.
- [x] Authenticated live smoke test dua halaman berhasil.
- [x] Backup: `/home/ubuntu/backups/appsbilling-v3-before-collection-search-20260711-163912` dan DB pasangannya.

## Stage 12.3 — Penyatuan UI/UX Tim Penagihan (2026-07-11)
- [x] Menyatukan tampilan halaman Belum Lunas, Tim Penagihan, dan Detail Batch melalui wrapper desain modul yang sama.
- [x] Menyeragamkan header kartu, form control, tombol primer/sekunder, alert, tabel, status, kartu batch, dan pencarian.
- [x] Menghapus satu blok CSS pencarian duplikat yang menyebabkan hasil visual kurang konsisten.
- [x] Memperjelas state terpilih, disabled, hover, dan hierarki aksi.
- [x] Meningkatkan layout tablet/mobile: kartu satu kolom, tombol aksi penuh, toolbar pencarian responsif, dan sticky save desktop yang aman.
- [x] Smoke test autentik halaman Belum Lunas dan Tim Penagihan berhasil; source detail batch lolos syntax/markup gate. Live saat tes belum memiliki batch sehingga halaman detail tidak dapat dirender dengan data nyata.
- [x] Invoice 1304, pembayaran 384, batch 0 — tidak ada mutasi data billing dari perubahan UI.
- [x] Backup live: `/home/ubuntu/backups/appsbilling-v3-before-collection-ui-unify-20260711-165050` dan database pasangannya.

## Stage 13 — Tutorial Admin & Changelog Refresh (2026-07-11)
- [x] Tutorial Admin dirombak menjadi Pusat Panduan AppsBilling V3 dengan hero, CTA, dan navigasi sticky.
- [x] Ringkasan kerja harian ditambahkan: Belum Lunas → Pembayaran → Tim Penagihan → Log Aktivitas.
- [x] Konten pembayaran, diskon, tombol pelanggan, segmentasi status, Tikor, koreksi, ID/kwitansi, dan Corporate disusun ulang menjadi kartu langkah yang seragam.
- [x] Panduan Tim Penagihan diperbarui mengikuti pencarian, pilihan persisten, koreksi batch, jasa terpisah, dan alur pembayaran normal.
- [x] Changelog diperbarui dan diubah menjadi timeline dengan kategori area serta penanda update terbaru.
- [x] UI mobile: navigasi horizontal, kartu satu kolom, alur Tim Penagihan vertikal, timeline responsif.
- [x] Semua anchor dan konten utama lolos authenticated smoke test live.
- [x] Data tetap utuh: invoice 1304, pembayaran 384.
- [x] Backup: `/home/ubuntu/backups/appsbilling-v3-before-tutorial-refresh-20260711-170013` dan database pasangannya.

## Stage 13.1 — Blueprint, Coret, dan Rollback Manifest (2026-07-11)
- [x] Blueprint Tim Penagihan dikoreksi agar sesuai implementasi final: judul Lembar Penagihan tanpa label BUKAN BUKTI LUNAS.
- [x] Menambahkan snapshot kanonis live, daftar file/path, lima checkpoint source+DB, prosedur rollback aman, dan aturan improve berikutnya.
- [x] Full Blueprint V3 ditambah baseline Tim Penagihan, Tutorial/Changelog, UI/UX, dan rollback.
- [x] Membuat `APPSBILLING_V3_ROLLBACK_MANIFEST_20260711.md` sebagai referensi cepat operator/developer.
- [x] Coret map kanonis diperbarui dari 36 menjadi 54 node, version 15.
- [x] Menambahkan cabang Tim Penagihan, Tutorial & Changelog, serta Improve & Rollback.
- [x] Sketch diganti menjadi AppsBilling V3 Live Flow & Rollback dengan 17 elemen.
- [x] Snapshot Coret sebelum perubahan disimpan di `state/appsbilling-v3-blueprint-refresh-20260711/`.
- [x] Share URL lama tetap dipertahankan.

## Stage 13.2 — Full Coret Information Architecture Redesign (2026-07-11)
- [x] Snapshot map/sketch version 15 sebelum redesign disimpan.
- [x] Struktur 54 node lama dibangun ulang menjadi 61 node dengan 7 root branches yang terorganisasi.
- [x] Root dan map title diubah menjadi Product, Operations & Recovery Map.
- [x] Billing boundaries dan collection/payment integrity dibuat eksplisit.
- [x] Recovery checkpoints dan verification gate ditempatkan di Data & Control Plane.
- [x] Sketch baru memakai journey + domain lanes + hard boundary + delivery/recovery plane.
- [x] Share URL tetap sama.
- [x] Final verification: version 38, 61 nodes, 7 root branches, 21 sketch elements.

## Stage 63 — Dashboard Rekap Rekening + Kelengkapan Data — 2026-07-13
- [x] Dashboard menampilkan rekap pembayaran bulan aktif per rekening/metode: nominal, jumlah transaksi, dan persentase dari pemasukan bulan tersebut.
- [x] Dashboard menampilkan persentase kelengkapan alamat pelanggan berdasarkan total pelanggan.
- [x] Dashboard menampilkan persentase kelengkapan Secret/ONU pelanggan berdasarkan total pelanggan.
- [x] Tambah styling panel rekap agar operator cepat membaca kesehatan data.

## Stage 64 — Track Record Pemasukan per Rekening — 2026-07-13
- [x] Filter Track Income mendukung bulan, tahun, dan rekening masuk.
- [x] KPI bulan/tahun, grafik bulanan, metode, serta track harian mengikuti rekening terpilih.
- [x] Komposisi semua rekening/Cash menampilkan nominal, transaksi, dan persentase periode.
- [x] Rincian transaksi rekening terpilih tersedia per bulan historis.
- [x] Navigasi bulan sebelumnya/berikutnya mempertahankan filter rekening.

## Stage 65 — Rollback Blueprint Refresh — 2026-07-13
- [x] Tambah rollback blueprint kanonis khusus AppsBilling V3.
- [x] Catat checkpoint cache fix, rekap dashboard, dan Track Income rekening.
- [x] Catat pemetaan file backup ke path live.
- [x] Tegaskan rollback tiga tahap terbaru tidak memerlukan restore SQLite.
- [x] Tambah perintah backup kondisi live sebelum rollback, restore source, lint, dan marker verifikasi.
- [x] Sinkronkan full blueprint dan rollback manifest proyek.

## Stage 66 — Corporate Branding e-Billing DSG — 2026-07-13
- [x] Tetapkan nama utama e-Billing DSG.
- [x] Tetapkan nama lengkap e-Billing PT Denta Sejahtera Group System.
- [x] Terapkan branding login, browser title, topbar, sidebar, footer, tutorial, changelog, dan print.
- [x] Perbarui manifest PWA, offline page, service-worker cache, serta asset version.
- [x] Pertahankan identitas DENTA NET pada data usaha/dokumen layanan yang relevan.
- [x] Backup source live/lokal dan DB safety copy.
- [x] PHP, JavaScript, dan JSON lint berhasil.
- [x] Authenticated smoke test dashboard, Tutorial, Track Income, Belum Lunas, Tim Penagihan, dan print berhasil.
- [x] Integritas data terverifikasi: customers 1140, invoices 1411, payments 492, collection_batches 0.

## Stage 67 — Hide Internal Map UI, Keep Tikor → Google Maps — 2026-07-14
- [x] Detail Pelanggan tidak lagi menampilkan map preview/internal map canvas.
- [x] Tombol `Mapping Lokasi` di header Detail Pelanggan di-hide.
- [x] Sidebar group `Kelola mapping` di-hide agar operator tidak diarahkan ke flow OpenStreetMap/workbench lama.
- [x] Flow lokasi kanonis sekarang: Tikor pelanggan di panel `Network / PPPoE` → klik langsung menuju Google Maps via `v3_google_maps_url(lat,lng)`.
- [x] Data koordinat tetap aman di `customers.latitude` dan `customers.longitude`; yang diubah hanya exposure UI.
- [x] Jangan reintroduce Leaflet/OpenStreetMap/Nominatim/customerMiniMap di detail pelanggan tanpa instruksi eksplisit Tuan Besar.
- [x] Backup lokal sebelum edit UI: `backups/v3-hide-osm-map-ui-20260714-061001`.
- [x] Backup live sebelum upload UI: `/home/ubuntu/backups/appsbilling-v3-hide-osm-map-ui-20260714-061018`.
- [x] Verifikasi live: `php -l detail.php` dan `php -l app/layout.php` lolos; grep aktif tidak menemukan `Kelola mapping`, `customerMiniMap`, `OpenStreetMap`, `nominatim`, atau `Mapping Lokasi` pada file aktif.
- [x] Blueprint full diperbarui agar aman ketika sesi/agent/project berpindah.

## Stage 69 — Tim Penagihan Multi-Periode per Pelanggan — 2026-07-15
- [x] Pemilihan batch berubah dari satu pelanggan/satu bulan menjadi pilihan invoice/periode yang rinci.
- [x] Satu pelanggan dapat membawa beberapa bulan tagihan dalam satu batch dan satu lembar penagihan.
- [x] Jasa kolektif disimpan/dihitung sekali per pelanggan; item periode berikutnya bernilai jasa 0.
- [x] Detail batch dikelompokkan per pelanggan dengan rincian invoice, periode, sisa tagihan, status billing, dan aksi pembayaran resmi per periode.
- [x] Cetak tersedia untuk semua pelanggan, rekap batch, satu pelanggan, dan pelanggan terpilih.
- [x] `collection_item` tetap kompatibel tetapi mengarah pada seluruh periode pelanggan yang sama dalam batch.
- [x] Invoice yang sama tetap ditolak bila sudah berada di batch aktif lain.
- [x] Pembuatan, koreksi, pencetakan, dan penghapusan batch tidak membuat pembayaran atau mengubah status invoice.
- [x] Tutorial Admin, changelog, blueprint, tracker, dan Coret diperbarui.
- [x] Fitur yang sama di-port ke instance Borneo tanpa mengubah branding, prefix ID, receipt note, atau database instance.



## Stage 70 — Dashboard Kelengkapan Data Click-Through + Secret Non-OFF — 2026-07-15
- [x] Dashboard Kelengkapan data pelanggan memiliki metrik Alamat, Secret pelanggan, dan Titik koordinat yang bisa diklik.
- [x] Klik `Detail belum lengkap` membuka Data Pelanggan dengan filter data_health agar admin langsung melihat pelanggan yang perlu dilengkapi.
- [x] Mode filter menampilkan judul `Cek Data Pelanggan Belum Lengkap` dan alert kontekstual sebelum tabel.
- [x] Persentase Secret pelanggan hanya menghitung pelanggan non-OFF; pelanggan OFF / Tidak aktif tidak ikut menurunkan persentase.
- [x] Filter `missing_secret` juga hanya menampilkan pelanggan non-OFF yang Secret / ONU kosong.
- [x] Tutorial Admin ditambah menu `Kelengkapan` dan SOP cek data dari dashboard.
- [x] Changelog in-app ditambah entri kelengkapan data clickable dan aturan Secret non-OFF.
- [x] Patch yang sama diterapkan ke instance Borneo tanpa mencampur database/branding.

## Stage 69 - Analisa Pendapatan & Piutang (2026-07-15)

Status: deployed live.

What changed:
- Dedicated sidebar menu: `Analisa pendapatan`.
- Dashboard summary panel: `Ringkasan pendapatan & piutang`.
- Filterable route: `index.php?page=income-analysis&month=YYYY-MM&from=YYYY-MM&to=YYYY-MM`.
- Operator metrics separated into:
  - estimated invoice value this month;
  - paid/realisasi value;
  - current-month unpaid amount;
  - older arrears amount/customer count;
  - potential double/overpayment audit list per customer-period.
- Comparison chart added for month-to-month reading.

Verification:
- Local `php -l`: OK.
- Local render smoke: OK for dashboard and income-analysis page.
- Live backup: `/home/ubuntu/backups/appsbilling.dentasejahteragroup.my.id-v3-before-income-analysis-20260715-154333.tar.gz`.
- Live `php -l`: OK.
- Live markers/menu/CSS: OK.
- HTTP smoke: `302 Found` to login as expected.


## Stage 70 - DENTANET Logo Branding / PWA Icon (2026-07-15)

Status: deployed live to `https://appsbilling.dentasejahteragroup.my.id/v3/`.

Scope:
- Replaced AppsBilling V3 DSG PWA/app icons with the exact DENTANET logo supplied by Tuan Besar.
- Replaced receipt/kwitansi branding asset `v3/assets/denta-net-logo.jpg` using the same logo, resized only.
- Added PNG PWA sizes: `pwa-icon.png` 512x512, `pwa-icon-192.png` 192x192, and `apple-touch-icon.png` 180x180.
- Updated manifest/layout/login/service worker references so browser/PWA cache points to the new logo assets.

Guardrail:
- Logo visual was not redesigned or redrawn; only resized/canvas-fitted.
- No database/schema changes.
- Root and `/v2/` untouched.

Verification:
- Live backup: `/home/ubuntu/backups/appsbilling-dsg-v3-before-dentanet-logo-20260715-161506.tar.gz`.
- Live PHP lint passed for `v3/app/layout.php` and `v3/login.php`.
- Live `manifest.webmanifest` JSON check passed.
- Live `service-worker.js` syntax check passed.
- HTTP 200 verified for logo, PWA icon, manifest, and login page.

### Stage 70 follow-up - PWA cache-bust logo fix (2026-07-15)

Status: deployed live.

Reason:
- Installed PWA icon can stay cached on Android/Chrome even after replacing same filename.

Follow-up change:
- Re-generated logo assets from the latest image supplied by Tuan Besar, with no visual/logo redesign.
- Added versioned filenames to force manifest/icon refresh:
  - `v3/assets/dentanet-logo-20260715.jpg`
  - `v3/assets/pwa-dentanet-20260715-512.png`
  - `v3/assets/pwa-dentanet-20260715-192.png`
  - `v3/assets/apple-dentanet-20260715.png`
- Updated manifest, layout, login, service worker, and `print.php`/kwitansi logo references.
- Compatibility alias files (`denta-net-logo.jpg`, `pwa-icon.png`, `pwa-icon-192.png`, `apple-touch-icon.png`) were also refreshed.

Verification:
- Live backup: `/home/ubuntu/backups/appsbilling-dsg-v3-before-dentanet-logo2-20260715-162235.tar.gz`.
- Live PHP lint passed for layout/login/print.
- Live manifest JSON and service worker syntax checks passed.
- HTTP 200 verified for versioned logo/PWA icon files, manifest, service worker, and login page.

Operator note:
- If an app was already installed before this change, Android/Chrome may still display the old home-screen icon until the PWA is removed and installed again, or browser/site storage cache is cleared.


### Stage 70 second follow-up - Versioned PWA manifest URL (2026-07-15)

Status: deployed live.

Reason:
- Installed Android/Chrome PWA still showed the old icon after icon file replacement, indicating the installed app metadata/manifest URL was cached.

Change:
- Added versioned manifest file `v3/manifest-20260715-logo2.webmanifest`.
- Updated authenticated layout and `login.php` to link the new manifest URL instead of the old `manifest.webmanifest` URL.
- Added `id` to the manifest: `/v3/?app=ebilling-dsg-logo2`.
- Added `v3/pwa-refresh.html` to help unregister old service workers and delete browser caches before reinstalling the PWA.

Verification:
- Live backup: `/home/ubuntu/backups/appsbilling-dsg-v3-before-versioned-manifest-20260715-162634.tar.gz`.
- Live PHP lint passed for layout/login.
- Live JSON checks passed for both manifest files.
- HTTP 200 verified for versioned manifest, refresh page, and login page.
- Login page publicly shows the new manifest and PWA icon references.

Operator note:
- If Android home-screen icon remains old, remove/uninstall the old PWA shortcut/app, open `https://appsbilling.dentasejahteragroup.my.id/v3/pwa-refresh.html`, tap refresh cache, then install again from Chrome.

### Stage 70 third follow-up - PWA icon whitespace crop (2026-07-15)

Status: deployed live.

Reason:
- The installed app icon appeared blank white because the supplied logo image had large white margins when fitted into a square launcher icon.

Change:
- Rebuilt PWA icon assets by cropping only the empty/near-white outer canvas and resizing the unchanged logo into the square icon area.
- Logo shape/content was not redesigned; only whitespace was removed for launcher visibility.
- Refreshed:
  - `pwa-dentanet-20260715-512.png`
  - `pwa-dentanet-20260715-192.png`
  - `apple-dentanet-20260715.png`
  - compatibility aliases `pwa-icon*.png` and `apple-touch-icon.png`
  - `dentanet-logo-20260715.jpg` / `denta-net-logo.jpg` for print/logo consistency.

Verification:
- Live backup: `/home/ubuntu/backups/appsbilling-dsg-v3-before-pwa-icon-crop-20260715-163035.tar.gz`.
- Public icon HTTP 200.
- Public 512 icon verified as 512x512 with roughly 36.2% non-white pixels, no longer a mostly blank white canvas.

### Stage 70 fourth follow-up - PWA icon uses mark-only symbol (2026-07-15)

Status: deployed live.

Reason:
- Full DENTANET logo with text looked like a small photo/sticker inside Android launcher icon after masking.

Change:
- PWA icon assets now use only the upper DENTANET mark/symbol area from the supplied logo image.
- Kwitansi/print logo remains the full DENTANET logo with text.
- Logo was not redesigned; icon generation only cropped the existing symbol area for launcher suitability.

Verification:
- Preview image sent to Tuan Besar via WhatsApp.
- Live backup: `/home/ubuntu/backups/appsbilling-dsg-v3-before-pwa-mark-icon-20260715-163524.tar.gz`.
- Public 512 icon verified 512x512 with roughly 31.23% non-white mark area.

### Stage 70 fifth follow-up - D-only PWA launcher icon (2026-07-15)

Status: deployed live.

Reason:
- The previous icon still appeared wrong on Android launcher. Tuan Besar requested the PWA app icon use only the D symbol.

Change:
- Generated new D-only icon assets from the latest supplied DENTANET image, with explicit white background to avoid black/blank masking:
  - `pwa-dentanet-d-20260715-512.png`
  - `pwa-dentanet-d-20260715-192.png`
  - `apple-dentanet-d-20260715.png`
- Updated manifest, versioned manifest, layout, login, service worker, and refresh page to use D-only icon filenames.
- Kwitansi/print remains full DENTANET logo, not D-only.

Verification:
- Live backup: `/home/ubuntu/backups/appsbilling-dsg-v3-before-pwa-donly-20260715-164155.tar.gz`.
- Public D-only 512 icon HTTP 200 and verified as 512x512 with roughly 34.32% non-white area.
- Versioned manifest and service worker publicly reference D-only icon filenames.

### Stage 70 sixth follow-up - Chrome-safe JSON manifest MIME fix (2026-07-15)

Status: deployed live.

Reason:
- Even on another device, Chrome showed blank/dark icon. Live inspection showed `.webmanifest` was served as `application/octet-stream` with `X-Content-Type-Options: nosniff`, so Chrome Android could ignore the manifest/icon metadata.

Change:
- Added Chrome-safe manifest file `v3/manifest-pwa-donly-20260715.json`, served by nginx as `application/json`.
- Updated layout, login, and PWA refresh page to link `/v3/manifest-pwa-donly-20260715.json`.
- Simplified manifest to only valid PNG icons with absolute paths and `purpose: any` (removed SVG/JPG/maskable entries to prevent bad fallback selection).
- Kept D-only PNG icon files and full kwitansi logo separation.

Verification:
- Live backup: `/home/ubuntu/backups/appsbilling-dsg-v3-before-json-manifest-20260715-165344.tar.gz`.
- `https://appsbilling.dentasejahteragroup.my.id/v3/manifest-pwa-donly-20260715.json` returns `Content-Type: application/json`.
- Login page references the JSON manifest and D-only 512 PNG icon.
- D-only 512 PNG returns `Content-Type: image/png`, 512x512, roughly 34.32% non-white area.

### Stage 70 seventh follow-up - Receipt-logo D icon for Android/iOS PWA (2026-07-15)

Status: deployed live.

Reason:
- Tuan Besar approved the current PWA approach but requested the icon use the same logo source as the kwitansi, taking only the D symbol.

Change:
- Generated new PWA icon assets from `dentanet-logo-20260715.jpg` (kwitansi logo source), cropped to the D symbol only:
  - `pwa-dentanet-receipt-d-20260715-512.png`
  - `pwa-dentanet-receipt-d-20260715-192.png`
  - `apple-dentanet-receipt-d-20260715.png`
  - extra iOS-safe exports: `apple-dentanet-receipt-d-20260715-167.png`, `apple-dentanet-receipt-d-20260715-152.png`
- Kept PNG opaque/RGB with white background for Chrome Android and iOS safety.
- Added/updated JSON manifest `manifest-pwa-receipt-d-20260715.json`, served as `application/json`, with PNG-only icons and absolute paths.
- Updated login/layout/pwa-refresh/service-worker to use the receipt-D manifest/icon.
- Kwitansi remains full logo; PWA icon is D-only.

Verification:
- Preview sent to Tuan Besar via WhatsApp.
- Live backup: `/home/ubuntu/backups/appsbilling-dsg-v3-before-receipt-d-pwa-20260715-165927.tar.gz`.
- Manifest JSON returns `Content-Type: application/json`.
- Android 512 PNG and iOS apple-touch PNG return `Content-Type: image/png`.
- Login page includes manifest, favicon, and apple-touch references to the receipt-D files.
- Live receipt-D 512 PNG verified 512x512, RGB/opaque, roughly 34.65% non-white area.

### Stage 70 final documentation sync - Blueprint, Changelog, Coret, GitHub (2026-07-15)

Status: completed.

Scope:
- Updated full blueprint with PWA icon contract: receipt-logo D-only icon for Android/iOS, full DENTANET logo for kwitansi, JSON manifest served as `application/json`, PNG-only absolute icon paths, and reinstall/cache-clear operator procedure.
- Updated in-app Tutorial Admin changelog with latest PWA entry: `Icon aplikasi dari D logo kwitansi`.
- Updated local Coret map note and live Coret board under `05 · Experience System`.
- Coret live result: map version 42, node count 67.
- Added Coret snapshot files in `state/appsbilling-v3-pwa-receipt-d-20260715/`.
- Live changelog deployed to `https://appsbilling.dentasejahteragroup.my.id/v3/`.

Verification:
- Live index PHP lint passed.
- Login remains HTTP 200.
- Manifest and icon verification from Stage 70 seventh follow-up remains valid.
- GitHub push completed after this documentation sync.


## Stage 68 - Customer Reward Points (2026-07-16)
- Added V3-only customer reward points system; root AppsBilling and `/v2/` untouched.
- New data layer: `customer_points` ledger and `customers.points_balance` cached balance.
- Automatic points are calculated from real payment date `payments.paid_at`:
  - day 1-5 = 10 points
  - day 6-10 = 5 points
  - day 11-20 = 3 points
  - day 21-end of month = 1 point
- Payment create/edit syncs the automatic point row; payment delete removes the related automatic point row and recalculates customer balance.
- Existing live payments were backfilled safely on first boot: 997 payment rows → 3,647 total points across 167 customers during verification.
- Admin UI updates:
  - Dashboard reward overview card.
  - Data Pelanggan Aktif includes Point column/badge by default, including existing admin column preferences.
  - Detail Pelanggan includes total reward point card, manual custom point adjustment form, and point history ledger.
- Live backup before deploy: `/home/ubuntu/backups/appsbilling-v3-before-customer-points-20260716-123655.tar.gz`.
- Verification passed: PHP syntax checks for `app/db.php`, `index.php`, `detail.php`, `delete.php`; live boot as `www-data`; HTTP login/dashboard/data/detail smoke checks.

## Stage 69 - Reward Points Rollout to Other V3 Instances (2026-07-16)
- Shared customer reward point concept documented in commercial platform blueprint and Coret board.
- Rolled out V3 point feature to:
  - `https://appsbilling.borneonetwork.my.id/v3/`
  - `https://billing.dsgtegal.my.id`
  - `https://billing.mrtnet.my.id/`
- Backups before deploy on VPS timestamp `20260716-130816`:
  - `/home/ubuntu/backups/appsbilling-borneonetwork-v3-before-points-20260716-130816.tar.gz`
  - `/home/ubuntu/backups/billing-dsgtegal-before-points-20260716-130816.tar.gz`
  - `/home/ubuntu/backups/billing-mrtnet-before-points-20260716-130816.tar.gz`
- Verification passed: PHP syntax, live boot as `www-data`, point rule 10/5/3/1, login/dashboard/data smoke tests, receipt check no visible point/reward.

### Stage 71 - Tutorial Admin + Changelog Sync for Reward Points and Operational Expenses (2026-07-17)

Status: ready for live/repo sync.

Scope:
- In-app `Admin Sistem → Tutorial Admin`.
- In-app changelog timeline.
- Documentation and GitHub source backup.

Changes:
- Tutorial hero date changed to `Diperbarui 17 Juli 2026`.
- Tutorial quick navigation now includes `Point` and `Operasional` anchors.
- Added `Reward Point Pelanggan` SOP:
  - automatic points use real payment date from `payments.paid_at`;
  - day 1-5 = 10 points, day 6-10 = 5 points, day 11-20 = 3 points, day 21-end = 1 point;
  - points are visible on Dashboard, Data Pelanggan, Detail Pelanggan, and ledger;
  - points do not change invoices/payments and receipt stays focused on nominal payment.
- Added `Pengeluaran Operasional Harian` SOP:
  - open from `Kelola pembayaran → Pengeluaran operasional`;
  - records daily operational cost fields such as date, category, description, user/technician, vendor, method, reference, amount, status, and notes;
  - monthly filter, subtotal, category/user recap, and daily track are used for review;
  - expenses stay in `expenses` and do not update `payments`, `invoices`, paid status, income balance, or main income dashboard automatically.
- Changelog latest entries added:
  - `2026-07-17 · Operasional · Pengeluaran Operasional Harian`;
  - `2026-07-16 · Reward · Reward Point Pelanggan`.
- Latest changelog highlight range expanded from top 4 to top 6 entries.

Files changed:
- `v3/index.php`
- `docs/EBILLING_COMPATIBLE_V3_TRACKER_20260706.md`
- `docs/EBILLING_COMPATIBLE_V3_FULL_BLUEPRINT_20260706.md`

Verification planned/performed:
- PHP lint for `v3/index.php`.
- Authenticated live Tutorial Admin smoke check should confirm `Reward Point Pelanggan`, `Pengeluaran Operasional Harian`, and `Diperbarui 17 Juli 2026` markers.

## Stage 72 — Kelengkapan Alamat & Tikor Hanya Pelanggan Aktif (2026-07-18)

Status: deployed live dan terverifikasi.

Scope:
- Dashboard `Kelengkapan data pelanggan` pada AppsBilling V3.
- Filter detail `Alamat belum lengkap` dan `Titik koordinat belum terisi`.
- Tidak mengubah perhitungan Secret pelanggan, data, tabel, route, atau modul lain.

Changes:
- Denominator Alamat lengkap dan Titik koordinat sekarang hanya pelanggan dengan `customer_status='active' AND COALESCE(is_active,1)=1`.
- Pembilang Alamat lengkap dan Titik koordinat memakai scope aktif yang sama.
- Angka kurang/belum lengkap memakai total pelanggan aktif, bukan seluruh pelanggan.
- Teks dashboard sekarang menegaskan `pelanggan aktif`.
- Klik detail Alamat/Titik koordinat juga hanya menampilkan pelanggan aktif yang datanya belum lengkap; pelanggan OFF/nonaktif tidak ikut muncul.
- Secret pelanggan tetap memakai aturan lama: pelanggan non-OFF.

Live result saat verifikasi:
- Pelanggan aktif dalam scope: `950`.
- Alamat lengkap: `926/950 = 97,5%`; belum lengkap: `24`.
- Titik koordinat lengkap: `60/950 = 6,3%`; belum lengkap: `890`.

Verification:
- Local dan live PHP lint `index.php`: passed.
- Marker source live untuk active scope dan label pelanggan aktif: passed.
- Public smoke test dashboard + kedua filter: HTTP 200 setelah redirect login yang memang diwajibkan.
- Backup sebelum deploy: `/home/ubuntu/backups/appsbilling-v3-index-before-active-data-health-20260718-123639.php`.
- Root AppsBilling, `/v2/`, `/nms`, commercial preview, database, dan file V3 lain tidak diubah.

## Stage 73 — Rollout Active Data Health ke MRTNET, Borneo, DSG Tegal (2026-07-18)

Status: deployed live dan terverifikasi.

- [x] Patch scope Alamat/Tikor pelanggan aktif pada tiga source lokal identik.
- [x] Tutorial Admin diperbarui menjadi 18 Juli 2026.
- [x] Changelog menambahkan `Alamat dan Tikor hanya pelanggan aktif`.
- [x] Backup dan deploy dilakukan terpisah untuk MRTNET, Borneo Network V3, dan DSG Tegal.
- [x] PHP lint lokal/live lolos pada seluruh instance.
- [x] Checksum lokal-live sama: `7f88bd6ccaaed9f915612450b9acdbfc9e6aec5e9170037045e3c03468464786`.
- [x] Marker Tutorial/Changelog terdeteksi pada source live.
- [x] Query DB terpisah menunjukkan ketiga instance saat rollout memiliki `0` pelanggan aktif; tidak ada data silang.
- [x] Public smoke test menghasilkan HTTP 200 melalui login normal pada seluruh domain.
- [x] Blueprint instance dan blueprint kanonis diperbarui.
- [x] Coret kanonis diperbarui: version 43, node count 68.

Backup:
- MRTNET: `/home/ubuntu/backups/billing-mrtnet-index-before-active-data-health-tutorial-20260718-124336.php`
- Borneo: `/home/ubuntu/backups/appsbilling-borneo-v3-index-before-active-data-health-tutorial-20260718-124356.php`
- DSG Tegal: `/home/ubuntu/backups/billing-dsgtegal-index-before-active-data-health-tutorial-20260718-124410.php`


## Stage 74 — Role Crew Read-Only Pelanggan (2026-07-18)

Status: implementasi dan pengujian lokal selesai; deployment live DSG V3 dilakukan selektif setelah backup terpisah.

Scope khusus:
- Hanya `https://appsbilling.dentasejahteragroup.my.id/v3/`.
- Tidak diterapkan ke root AppsBilling, `/v2/`, `/nms`, MRTNET, Borneo Network, atau DSG Tegal.

Kontrak akses Crew:
- Allowlist halaman hanya `data-warga` dan `data-pelanggan-free`.
- Data aktif berbayar memakai `customer_status='active'`, `COALESCE(is_active,1)=1`, dan harga paket di atas Rp0.
- Data FREE memakai scope aktif/enabled yang sama dengan harga paket Rp0.
- Kolom tetap: No, ID pelanggan, Nama pelanggan, Alamat, Telepon, Nama langganan, Status langganan, Secret, Username PPPoE, Aksi.
- Aksi hanya `Buka Lokasi` jika koordinat valid; jika tidak, tampil `Lokasi belum tersedia`.
- Menu, column manager, import/export, tambah/edit/hapus, pelanggan OFF, detail finansial, pembayaran, tagihan, print, sync, pengaturan, user, dan modul admin lain tidak tersedia.

Server-side enforcement:
- Setiap POST dari sesi Crew di `index.php` ditolak HTTP 403 dengan pesan `Akses ditolak. Role Crew bersifat read-only.`.
- Endpoint langsung `detail.php`, `edit.php`, `delete.php`, `import-export.php`, `print.php`, dan `sync.php` memakai `require_non_crew()`.
- GET endpoint/modul terlarang diarahkan kembali ke `index.php?page=data-warga`.
- Auto-sync source dan auto-generate invoice bulanan tidak berjalan pada sesi Crew.
- Nilai role user yang valid: Administrator, Operator, Sales, dan Crew; superadmin `ananta` tetap dilindungi.

Verification lokal:
- PHP lint seluruh file PHP V3: passed.
- Fixture aktif berbayar tampil hanya pada halaman aktif; fixture FREE tampil hanya pada halaman FREE.
- Fixture OFF dan active-disabled tidak bocor ke kedua halaman Crew.
- Lokasi valid menghasilkan tombol Google Maps; lokasi kosong menghasilkan placeholder.
- GET deny matrix seluruh route sensitif: HTTP 302 ke home Crew.
- POST deny matrix `index.php`, `delete.php`, `sync.php`, `import-export.php`, dan `print.php`: HTTP 403 dan pesan exact.
- Administrator tetap mendapat HTTP 200 untuk dashboard, data user, tambah user, edit pelanggan, dan detail pelanggan.
- Tidak ada mutasi customer akibat pengujian Crew.

Files feature-specific:
- `v3/app/auth.php`
- `v3/app/layout.php`
- `v3/index.php`
- `v3/edit.php`
- `v3/detail.php`
- `v3/delete.php`
- `v3/import-export.php`
- `v3/print.php`
- `v3/sync.php`

Coret:
- Map kanonis tetap memakai share URL yang sama.
- Update terverifikasi: version `44`, node count `71`.
- Node baru: `Role Crew · Server-Side Read-Only`, `Crew Field View`, `Crew Access Verification Gate`.
- Snapshot: `state/appsbilling-v3-crew-role-20260718/`.

Deployment live Stage 74:
- Status: deployed dan terverifikasi pada AppsBilling DSG V3.
- Backup source + SQLite konsisten: `/home/ubuntu/backups/appsbilling-v3-before-crew-role-20260718-060739`.
- 9 file feature-specific diunggah; ownership tetap `www-data:www-data`, mode `0644`.
- PHP lint live seluruh file target: passed.
- Checksum SHA-256 lokal-live seluruh file target: identical.
- Login Crew live sementara: passed; akun verifikasi sudah dihapus.
- Scope paid/FREE, heading minimum, UI read-only, GET deny 302, dan POST deny 403 exact: passed.
- Regresi Administrator untuk dashboard, data user, tambah user, detail pelanggan, dan import/export: HTTP 200.
- Counts customers/invoices/payments tidak berubah; `PRAGMA integrity_check=ok`.

### Stage 74.1 — Crew Table UI Polish (2026-07-18)

- Urutan kolom Crew diubah menjadi: No → Aksi → ID pelanggan → Nama → Alamat → Telepon → Paket → Status → Secret → Username PPPoE.
- CSS khusus `crew-customer-table` mengatur fixed column rhythm, wrapping untuk nama/alamat/paket/credential, tombol lokasi konsisten, sticky header, hover row, dan horizontal scroll yang jelas di mobile.
- CSS hanya aktif pada tabel sesi Crew; layout Administrator/Operator/Sales tidak berubah.
- Local render test, PHP lint, live checksum, live login Crew, heading order, read-only UI, dan public login smoke test: passed.
- Backup source sebelum deploy: `/home/ubuntu/backups/appsbilling-v3-before-crew-table-ui-20260718-061911`.

### Stage 74.2 — Documentation Sync Crew Table UI (2026-07-18)

- [x] Full Blueprint mencatat urutan tabel kanonis `No → Aksi → ID pelanggan → ...`.
- [x] Tutorial Admin Role Crew menjelaskan urutan kolom, Aksi lokasi di depan, wrapping data panjang, sticky/clear header, dan horizontal scroll pada HP.
- [x] Changelog terbaru menambahkan `UI Crew · Tabel Crew lebih rapi dan cepat dipakai`.
- [x] Perubahan dokumentasi menegaskan bahwa perapian UI tidak mengubah hak akses, query scope, atau data pelanggan.
- [x] Coret kanonis diperbarui dan diverifikasi: version 45, node count 74.
- [x] Backup, deploy Tutorial/Changelog live, checksum, dan authenticated smoke test.

Stage 74.2 evidence:
- Coret kanonis: version `45`, node count `74`.
- Backup live sebelum document sync: `/home/ubuntu/backups/appsbilling-v3-before-crew-doc-sync-20260718-062410`.
- PHP lint live dan checksum lokal-live `index.php`: passed.
- Authenticated Tutorial Admin markers dan changelog markers: passed.
- Akun verifikasi Administrator sudah dihapus.
- Public login smoke test: HTTP 200.

### Stage 75 — Payment Date Submit Reliability Fix (2026-07-18)

- [x] Input `paid_at` pada form tambah pembayaran diubah dari `datetime-local` menjadi `date` agar pengetikan tanggal yang belum lengkap tidak membuat browser diam-diam menahan submit.
- [x] Input tanggal dibuat `required` dan mendapat validasi inline serta pesan yang jelas jika tanggal belum lengkap/valid.
- [x] Tombol Simpan memiliki `type="submit"`, status `Menyimpan pembayaran…`, dan dikunci setelah submit valid untuk mencegah klik ganda.
- [x] Proteksi pembayaran ganda per pelanggan/periode tetap dipertahankan; tombol tetap dikunci dan tautan Edit pembayaran lama tetap tersedia.
- [x] Backend `v3_normalize_datetime()` tetap menjadi kontrak penyimpanan: tanggal-only dinormalisasi menjadi timestamp SQL tanpa mengubah struktur tabel atau transaksi lama.
- [x] Versi CSS/JS dan service worker dinaikkan agar browser/PWA tidak mempertahankan asset lama.
- [x] PHP lint, JavaScript syntax check, authenticated local form smoke test, date normalization test, live HTTP/assets, checksum lokal-live, ownership, dan mode file: passed.

Stage 75 evidence:
- Backup live sebelum deploy: `/home/ubuntu/backups/appsbilling-payment-date-before-20260718-165755.tar.gz`.
- File deploy selektif: `v3/index.php`, `v3/app/layout.php`, `v3/assets/v3-ajax.js`, `v3/assets/adminlte-clone.css`, `v3/service-worker.js`.
- Live asset marker: `20260718-payment-date-submit-fix`.
- Tidak ada pembayaran atau data pelanggan live yang dibuat/diubah selama verifikasi.

## Stage 76 — Infrastruktur Jaringan Terisolasi (2026-07-19)

Status: implementasi lokal selesai dan pengujian inti lulus; deployment dilakukan selektif setelah backup live.

Scope khusus:
- Hanya AppsBilling DSG V3 pada `https://appsbilling.dentasejahteragroup.my.id/v3/`.
- Tidak otomatis disalin ke root, `/v2/`, `/nms`, Borneo Network, MRTNET, atau DSG Tegal.
- Tidak mengubah billing, pembayaran, invoice, paket, customer profile, atau role Crew.

Implementasi:
- [x] Tabel idempotent `network_nodes`, `network_cable_routes`, `network_splices`, dan `network_customer_placements`.
- [x] Menu non-Crew: Ringkasan, ODC, ODP, Joint Closure, Jalur Kabel, Sambungan Core, Penempatan Pelanggan.
- [x] CRUD ODC/ODP/JC dengan ID custom, koordinat, kapasitas, status, upstream, dan catatan.
- [x] CRUD jalur kabel dengan endpoint, jumlah core, panjang, status, dan patokan.
- [x] CRUD sambungan core dengan validasi kapasitas jalur.
- [x] Penempatan pelanggan ke ODP/port tanpa mengubah tabel pelanggan.
- [x] Proteksi port aktif ganda, kapasitas shrink, core overflow, self-upstream, dan penghapusan aset yang masih direferensikan.
- [x] Google Maps link untuk koordinat valid; embedded map tidak dikembalikan.
- [x] Activity log untuk mutasi jaringan.
- [x] Tutorial Admin dan changelog diperbarui.
- [x] Cache-bust CSS/JS/service worker `20260719-network-infrastructure`.

Verification lokal:
- [x] PHP lint seluruh 14 file PHP V3: passed.
- [x] SQLite migration copy: `quick_check=ok` dan keempat tabel tersedia.
- [x] Fixture topologi: 3 node, 2 jalur, 1 sambungan, 1 penempatan: passed.
- [x] Duplicate active ODP port: blocked.
- [x] Oversized core dan capacity shrink helper: blocked.
- [x] Billing regression counts customers/payments/invoices/packages/bank_accounts: unchanged.

Rollback evidence:
- Lokal: `backups/network-infrastructure-before-20260719-005740`.
- Live: `/home/ubuntu/backups/appsbilling-v3-before-network-infrastructure-20260719-005740`.
- Restore DB penuh memerlukan keputusan eksplisit bersama karena dapat menimpa transaksi setelah checkpoint.

Files feature-specific:
- `v3/app/network.php`
- `v3/app/db.php`
- `v3/app/layout.php`
- `v3/index.php`
- `v3/assets/adminlte-clone.css`
- `v3/service-worker.js`
- `docs/EBILLING_COMPATIBLE_V3_FULL_BLUEPRINT_20260706.md`
- `docs/EBILLING_COMPATIBLE_V3_TRACKER_20260706.md`

Coret Stage 76:
- [x] Board kanonis tetap memakai share URL yang sama: https://coret.id/share/13359bbbbf7a0049023307cd7743a1e3fcc12cdeee87e2b4
- [x] Snapshot sebelum/sesudah disimpan di `state/appsbilling-v3-network-infrastructure-20260719/`.
- [x] Version berubah `45 → 46`; node count `74 → 78`.

### Stage 76 — Deployment dan verifikasi produksi

Deployment selektif selesai ke `/var/www/appsbilling.dentasejahteragroup.my.id/v3` tanpa mengubah root, `/v2/`, `/nms`, instance lain, atau file SQLite secara manual.

Bukti produksi:
- [x] Checksum enam file runtime live sama dengan working copy.
- [x] PHP lint live: `network.php`, `db.php`, `layout.php`, `index.php` passed.
- [x] Asset CSS dan service worker HTTP 200; marker cache `20260719-network-infrastructure` aktif.
- [x] Unauthenticated route jaringan diarahkan ke login.
- [x] Authenticated HTTP 200 untuk dashboard, tujuh halaman jaringan, Tutorial Admin, pembayaran, pelanggan, tagihan, dan operasional.
- [x] CRUD live fixture: 3 node, 2 jalur, 1 sambungan core, 1 penempatan pelanggan.
- [x] Percobaan port aktif ganda tetap menghasilkan hanya 1 relasi aktif.
- [x] Fixture jaringan dan akun verifikasi dihapus setelah tes; `network_total=0`, akun verifikasi `0`.
- [x] Counts billing sebelum/sesudah tetap: customers 1166, payments 1715, invoices 2546, packages 26, bank_accounts 4.
- [x] SQLite production `PRAGMA quick_check=ok`.

Backup deployment tambahan:
- `/home/ubuntu/backups/appsbilling-v3-network-deploy-20260718-171609`

Catatan rollback:
- Backup tambahan berisi source runtime sebelum upload dan salinan database pada waktu deployment.
- Untuk koreksi UI/source, restore hanya file runtime terkait.
- Restore database penuh harus diputuskan bersama karena dapat menghilangkan transaksi setelah waktu backup.
