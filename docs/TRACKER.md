# AppsBilling Commercial Platform Tracker

## Stage 0 — Concept & Safety Grounding (2026-07-15)

Status: in progress.

- [x] Capture user request for commercial AppsBilling multi-mitra platform.
- [x] Decide not to overwrite/destroy root live before explicit final approval.
- [x] Create separate workspace and GitHub repo binding.
- [x] Draft SaaS/multi-tenant blueprint.
- [ ] Create new Coret board.
- [ ] Push initial repo.

## Stage 1 — MVP Architecture

Planned:

- [ ] Platform DB schema.
- [ ] Superadmin auth.
- [ ] Tenant registration.
- [ ] Approve/disable/delete tenant controls.
- [ ] Tenant DB provisioning from AppsBilling V3 template.
- [ ] Tenant login.
- [ ] Billing V3 tenant shell.

## Stage 2 — Safe Preview

Planned:

- [ ] Deploy to `/platform-preview/` or similar.
- [ ] Backup root live before any replacement.
- [ ] Smoke test registration → approval → tenant dashboard.
- [ ] Ask Tuan Besar for root cutover approval.

## Stage 0.1 — Coret Concept Board (2026-07-15)

Status: completed.

- [x] New Coret map created so commercial SaaS concept does not mix with DSG/Borneo V3 map.
- [x] Main branches: Product Vision, Superadmin Control, Mitra Registration, Tenant Billing V3, Data Isolation, Commercial UX, Deployment & Cutover, Roadmap.
- [x] Sketch added for landing → registration → superadmin approval → tenant DB provisioning → tenant billing V3.
- [x] Share link created and stored in `docs/CORET.md`.
