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
