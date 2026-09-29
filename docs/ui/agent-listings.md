# My listings — agent area

> Draft to be refined together. Describes the desired UI and behaviour of the agent's listing management.

## Purpose
Let an Agent create and edit **their own** property listings, including photos and
type/area/feature categories, so their portfolio stays accurate. Rendered inside the back office
shell (see [`back-office.md`](back-office.md)).

## Pages
- **Index — `/listings`**: the signed-in agent's own listings (cover, name, type, area, price, status badge).
- **Create — `/listings/create`** / **Edit — `/listings/{property}/edit`**: shared form.

## Access rules
- Auth + agent role required; administrators get `403`.
- An agent only ever sees and edits **their own** listings (enforced server-side by `PropertyPolicy` via the `agent_property` link).

## Form
| Field | Notes |
|---|---|
| Name | required; slug generated automatically |
| Address / ZIP | ZIP required; `city`/`state`/`country` are fixed defaults |
| Prices | USD + MXN required; "show both prices" checkbox |
| Lot / construction (m²) | required |
| Bedrooms / bathrooms / half baths / parking | integers |
| Latitude / longitude | optional (reserved for a future map) |
| Description | optional |
| Type / Area / Features | category checkboxes (agent-scoped) |
| Featured image | optional, single (uploading a new one replaces the previous) |
| Gallery | up to 10 images, 5 MB each (jpg/png/webp) |

## Rules
- New listings are created **inactive** (`is_active = false`), pending admin approval (PROP-04).
- The category **vocabulary** is admin-managed (CAT-01); the agent only **assigns** from it.
- Only `property_type`, `property_area`, `property_feature` categories are agent-assignable; **`property_status` and `property_label` are admin-only**.
- One featured image per listing (enforced by the app); files live on the `public` disk (`storage:link` required).

## Wanted changes
> Describe the desired UI changes here.
-

## Out of scope
- Approval/publish (PROP-04), mark sold/featured (PROP-05), deletion (admin-only, later), `description_es` (LOC-01), image reordering.
