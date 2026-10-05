# Leads — agent area

> Draft to be refined together. Describes the desired UI and behaviour of the agent's lead pipeline.

## Purpose
Let an Agent see the leads assigned to them, track the pipeline by updating each lead's status, and edit the
lead's interest, budget and notes. Rendered inside the back office shell (see [`back-office.md`](back-office.md)).

## Access rules
- Auth + agent role required; administrators get `403` (admin oversight is **LEAD-05**).
- An agent only ever sees and edits **their own** leads (`LeadPolicy` via `agent_id`).

## List — `/leads`
- Table: Name, Contact (email + phone), Interested in, Notes, Budget (USD), Status badge, Submitted, Actions.
- Presentation matches the [admin lead list](admin-leads.md): interest text wraps instead of relying on hover;
  **View notes** expands the full note inline using the shared `x-lead-notes` native `details` component.
  Notes preserve line breaks and work with keyboard, touch and without JavaScript; missing notes use `—`.
- Missing budgets use `—`; a zero budget is displayed as `$0`. Submitted timestamps use `Y-m-d H:i`.
- Status badge colours: New (info), Contacted (neutral), Qualified (warning), Closed (success), Lost (danger).
- **Update status**: a per-row native select that submits on change (`PATCH /leads/{lead}/status`); only **Contacted / Qualified / Closed / Lost** are offered — a lead cannot be moved back to `new`.
- **Edit**: a per-row link to the edit page.
- Status selects have explicit accessible labels; the action group wraps when space is limited.
- Pagination (15 per page) + success alert; validation errors are displayed above the table.

## Edit — `/leads/{lead}/edit`
- Form (`PUT /leads/{lead}`) over the **pipeline fields**:
  - **Interested in** (required), **Budget (USD)** (optional), **Notes** (optional), **Status**.
  - Status options = the current status plus the valid targets; `new` is only offered while the lead is still `new`.
- Contact details (name, email, phone) are **not** editable here.

## Rules
- The agent maintains the pipeline fields (interest, budget, notes, status); a lead cannot move back to `new`.
- Assignment, reassignment and unassignment are admin-only actions (LEAD-04), available in the separate
  [admin lead area](admin-leads.md).

## Wanted changes
> Describe the desired UI changes here.
-

## Out of scope
- Admin oversight, assignment, reassignment and unassignment (separate admin area), editing visitor contact details,
  lead history.
