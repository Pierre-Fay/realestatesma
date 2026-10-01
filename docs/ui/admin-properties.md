# Property approval — `/admin/properties`

> Draft to be refined together. Describes the desired UI and behaviour of the admin property approval page.

## Purpose
Admin-only area to review listings, publish/unpublish them and delete them. Rendered inside the back office
shell (see [`back-office.md`](back-office.md)).

## Access rules
- Auth + admin role required; agents get `403`.

## Page
- Table: listing (cover + name), agent(s), area, price (USD), status badge, actions.
- Status badge: Pending (warning), Live (success), Sold (neutral) — plus Featured (info) when set.
- Status filter: **All / Pending / Live / Sold** (pending listings listed first).
- Actions:
  - **Approve** (pending) → publishes (`is_active = true`) and makes the listing publicly visible.
  - **Unpublish** (live) → withdraws it (`is_active = false`).
  - **Delete** (confirmation dialog) → permanently deletes the listing **and its photo files**.
- Pagination.

## Rules
- Only admins can change publishing state or delete a listing (PROP-04).
- Featured / mark-as-sold = PROP-05 (later).

## Wanted changes
> Describe the desired UI changes here.
-

## Out of scope
- Mark as sold / feature a property (PROP-05), bulk actions.
