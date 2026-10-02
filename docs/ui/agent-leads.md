# Leads — agent area

> Draft to be refined together. Describes the desired UI and behaviour of the agent's lead pipeline.

## Purpose
Let an Agent see the leads assigned to them, track the pipeline by updating each lead's status, and edit the
lead's interest, budget and notes. Rendered inside the back office shell (see [`back-office.md`](back-office.md)).

## Access rules
- Auth + agent role required; administrators get `403` (admin oversight is **LEAD-05**).
- An agent only ever sees and edits **their own** leads (`LeadPolicy` via `agent_id`).

## List — `/leads`
- Table: Name, Contact (email + phone), Interested in, Notes (truncated, full text on hover), Budget, Status badge, Actions.
- Status badge colours: New (info), Contacted (neutral), Qualified (warning), Closed (success), Lost (danger).
- **Update status**: a per-row native select that submits on change (`PATCH /leads/{lead}/status`); only **Contacted / Qualified / Closed / Lost** are offered — a lead cannot be moved back to `new`.
- **Edit**: a per-row link to the edit page.
- Pagination (15 per page) + success alert.

## Edit — `/leads/{lead}/edit`
- Form (`PUT /leads/{lead}`) over the **pipeline fields**:
  - **Interested in** (required), **Budget (USD)** (optional), **Notes** (optional), **Status**.
  - Status options = the current status plus the valid targets; `new` is only offered while the lead is still `new`.
- Contact details (name, email, phone) are **not** editable here.

## Rules
- The agent maintains the pipeline fields (interest, budget, notes, status); a lead cannot move back to `new`.
- Reassignment = LEAD-04; admin oversight = LEAD-05.

## Wanted changes
> Describe the desired UI changes here.
-

## Out of scope
- Reassignment (LEAD-04), admin oversight (LEAD-05), editing visitor contact details, lead history.
