# Internal AppsBilling DSG V3 — Versioned Source

Direktori ini menyimpan source kanonis untuk instance internal/private:

`https://appsbilling.dentasejahteragroup.my.id/v3/`

Ini **bukan** root commercial multi-tenant dan tidak boleh dicampur dengan database tenant/platform. Source diletakkan di direktori terpisah agar perubahan V3 dapat di-review, di-rollback, dan di-port ke tenant engine secara selektif.

## Deployment mapping

- `internal-v3/app/*` → live `/v3/app/*`
- `internal-v3/index.php` → live `/v3/index.php`
- `internal-v3/service-worker.js` → live `/v3/service-worker.js`
- `internal-v3/assets/adminlte-clone.css` → live `/v3/assets/adminlte-clone.css`
- `internal-v3/docs/*` → dokumentasi source/release; bukan public webroot

## Safety

- Database SQLite, credential, `.env`, upload pelanggan, backup, dan log runtime tidak boleh di-commit.
- Root commercial platform dan `/v2/` tidak ikut berubah saat deploy internal V3.
- Restore database penuh harus diputuskan bersama karena dapat menimpa transaksi setelah checkpoint.

## Current release

2026-07-19: Infrastruktur Jaringan terisolasi untuk ODC, ODP, Joint Closure, jalur kabel, sambungan core, dan penempatan pelanggan. Detail dan bukti deployment ada di `docs/NETWORK_INFRASTRUCTURE_MODULE_20260719.md` serta tracker.
