# Coret

New Coret board for AppsBilling Commercial Platform.

- Map ID: `019f664d-ae78-7469-85cc-81228d0eb795`
- Share URL: https://coret.id/share/ed8991f34ddb65e0cd1937634f5c101d95971afaf3f0b69c
- Version after initial concept: `3`
- Node count: `48`

Purpose: keep the commercial multi-mitra/SaaS concept separate from existing AppsBilling DSG/Borneo V3 Coret board.

## Update 2026-07-16 — V3 Private vs Commercial Tenant

Coret board updated to reflect Tuan Besar's product decision:

- `/v3/` is the private/internal AppsBilling instance.
- Root `https://appsbilling.dentasejahteragroup.my.id/` is the commercial multi-tenant platform.
- Every commercial tenant must receive AppsBilling V3 feature parity and full admin tutorial.
- Each tenant uses No Akun, isolated empty DB, and custom app/receipt logo.
- V3 modules must be ported safely into tenant DB context without touching private `/v3` or `/nms`.

## Update 2026-07-16 — Tenant Clone V3 DB Kosong

Coret board updated again to clarify the tenant model more strictly:

- Tenant commercial is a clone of AppsBilling V3 per No Akun/slug.
- UI, features, flow, validation, pages, and admin tutorial should match `/v3`.
- Operational DB data starts empty per tenant: pelanggan, tipe pembayaran/paket, tagihan, pembayaran, router, rekening, lokasi, deposit, diskon, PPPoE, instalasi, tiket, corporate, batch penagihan, etc.
- Minimal seed is allowed only for settings/user/schema/copyright so the app can run.
