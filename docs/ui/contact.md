# Contact — `/contact`

> Draft to be refined together. Describes the desired UI and behaviour of the general contact page.

## Purpose
Public page where a Visitor shares a general interest and budget so an agent can reach out with
matching properties. Rendered in the public shell (`x-public-layout`).

## Access rules
- Public (no authentication).

## Form
Submitted to `POST /contact` (rate-limited `throttle:5,1`):

| Field | Notes |
|---|---|
| First name / Last name | required |
| Email | required (max 100 — matches the column) |
| Phone | optional |
| Interested in | required, free text |
| Budget (USD) | optional |
| Consent | explicit GDPR checkbox, required |

- A **honeypot** field (`website`, visually hidden) silently discards bot submissions.
- Creates an **unassigned `Lead`** (`status = new`, `agent_id = null`); an Admin assigns it later. No visitor-message field — "Interested in" is the free text.
- Success shows a flash alert on the page.

## Notes
- The consent label links to the **privacy policy** (`/privacy`).
- Reached from the public nav ("Contact").
- Creates an **unassigned** lead; a property inquiry (on a property detail page) creates a lead **assigned to that property's agent** instead.

## Wanted changes
> Describe the desired UI changes here.
-

## Out of scope
- Lead assignment/oversight (LEAD-04/05), a visitor message field, newsletter.
