# AppsBilling Commercial Platform

Commercial multi-mitra AppsBilling platform for `https://appsbilling.dentasejahteragroup.my.id`.

This project is planned as a SaaS-style platform based on the proven AppsBilling V3 operator workflow, with:

- root landing + login;
- superadmin panel;
- mitra/client registration;
- approve/disable/delete account controls;
- isolated billing workspace and DB per mitra/client;
- AppsBilling V3 feature parity as the tenant billing engine.

See `docs/COMMERCIAL_PLATFORM_BLUEPRINT.md` and `docs/TRACKER.md`.

Security note: never commit real passwords, hashes, tenant databases, or runtime storage.

## Product Direction 2026-07-16

`/v3/` is the private/internal AppsBilling instance. The root commercial platform is the multi-tenant product. Every commercial tenant should receive AppsBilling V3 feature parity, but with its own No Akun, isolated empty DB, and custom app/receipt logo. Do not mix tenant data with the private `/v3/` instance.

Roadmap: `docs/V3_FEATURE_PARITY_ROADMAP_20260716.md`.

## Tenant Model Clarification

Commercial tenants are intended to be **AppsBilling V3 clones per No Akun/slug**. UI, features, flows, and tutorial should match `/v3`; each tenant simply starts with an isolated empty operational DB and can use its own app/receipt logo.

## Internal V3 Source

The private/internal `/v3/` source is versioned separately under `internal-v3/`. This preserves the commercial root architecture while allowing audited V3 releases and rollback. Runtime SQLite databases, credentials, uploads, and backups are excluded.
