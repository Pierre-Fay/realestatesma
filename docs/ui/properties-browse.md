# Properties (browse) — `/properties`

> Draft to be refined together. Describes the desired UI and behaviour of the public property listing.

## Purpose
Public page where a Visitor searches and filters approved listings by type, area, status, price,
bedrooms and keyword. Rendered in the public shell (`x-public-layout`).

## Access rules
- Public (no authentication).
- Only **approved** (`is_active = true`) and **not sold** listings are shown.

## Filters (GET query)
| Filter | Param | Behaviour |
|---|---|---|
| Keyword | `q` | matches name / address / description |
| Type | `type` | category id (`property_type`) |
| Area | `area` | category id (`property_area`) |
| Status | `status` | category id (`property_status`) |
| Bedrooms | `bedrooms` | minimum |
| Price (USD) | `price_min` / `price_max` | range on `price_usd` |
| Sort | `sort` | `newest` (default) / `price_asc` / `price_desc` |

- Filters are a GET form; the query string is preserved across pagination (12 per page).
- Results: responsive card grid (cover image, name, type/area badges, price, beds/baths/m²).

## Notes
- `/` is still the placeholder home. **CMS-01 covers only static pages** (about, legal notice, privacy policy) — it is not the landing page.
- Cards are not yet links — the detail page is PROP-02.

## Wanted changes
> Describe the desired UI changes here.
-

## Out of scope
- Property detail (PROP-02), approval (PROP-04), sold/featured (PROP-05), dual-currency display (PROP-06).
