# AppsBilling DSG V3 — Infrastruktur Jaringan

Tanggal: 2026-07-19
Route: `/v3/index.php?page=network-dashboard`

## Alur operator

1. Buat ODC sebagai titik distribusi utama.
2. Buat Joint Closure dan/atau ODP, lalu pilih upstream yang sesuai.
3. Buat jalur kabel antar titik beserta jumlah core.
4. Di Joint Closure, hubungkan core masuk ke core keluar.
5. Tempatkan pelanggan ke ODP dan nomor port.
6. Lengkapi koordinat untuk membuka lokasi langsung di Google Maps.

## Batas aman

- Modul hanya menulis tabel `network_*` dan `activity_logs`.
- Pelanggan hanya direferensikan dengan `customer_id`; tidak ada update paket, tagihan, pembayaran, status billing, atau credential pelanggan.
- Melepas pelanggan dari ODP tidak menghapus pelanggan.
- Crew tidak memiliki akses modul.
- Jangan restore seluruh SQLite untuk rollback source biasa. Database restore penuh harus mempertimbangkan transaksi baru setelah backup.

## Rollback source

Salin kembali file feature-specific dari checkpoint live:

`/home/ubuntu/backups/appsbilling-v3-before-network-infrastructure-20260719-005740`

Setelah restore, jalankan PHP lint, `PRAGMA quick_check`, dan smoke login/dashboard/billing. Tabel `network_*` yang tersisa aman dan tidak digunakan source lama.

## Status deployment

Production rollout selesai dan diverifikasi 2026-07-19 UTC.

- Live: https://appsbilling.dentasejahteragroup.my.id/v3/index.php?page=network-dashboard
- Backup deployment: `/home/ubuntu/backups/appsbilling-v3-network-deploy-20260718-171609`
- CRUD fixture serta akun verifikasi sudah dibersihkan.
- Billing counts sebelum/sesudah identik.
- SQLite `quick_check=ok`.
- Coret kanonis: https://coret.id/share/13359bbbbf7a0049023307cd7743a1e3fcc12cdeee87e2b4 — version 46, 78 node.
