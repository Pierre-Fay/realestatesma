# Leads — agent area

> Draft to be refined together. Describes the desired UI and behaviour of the agent's lead pipeline.

## Purpose
Let an Agent see the leads assigned to them and track the pipeline by updating each lead's status.
Rendered inside the back office shell (see [`back-office.md`](back-office.md)).

## Access rules
- Auth + agent role required; administrators get `403` (admin oversight is **LEAD-05**).
- An agent only ever sees and updates **their own** leads (`LeadPolicy` via `agent_id`).

## Page — `/leads`
- Table: Name, Contact (email + phone), Interested in, Notes (truncated, full text on hover), Budget, Status badge, Update status.
- Status badge colours: New (info), Contacted (neutral), Qualified (warning), Closed (success), Lost (danger).
- **Update status**: a per-row native select that submits on change (`PATCH /leads/{lead}`); only **Contacted / Qualified / Closed / Lost** are offered — a lead cannot be moved back to `new`.
- Pagination (15 per page) + success alert.

## Rules
- Status only — name, email and budget are not edited here.
- Reassignment = LEAD-04; admin oversight = LEAD-05.

## Wanted changes
> Describe the desired UI changes here.
-

## Out of scope
- Reassignment (LEAD-04), admin oversight (LEAD-05), lead detail/history, editing lead fields.
