# Dashboard — `/dashboard`

> Draft to refine together.

## Purpose
Authenticated landing page for Users (agent or admin role).

## Access rules
- Auth required; guests are redirected to `/login`.

## Content (role-aware)
- Welcome heading with the signed-in user's name.
- Role indicator: "Administrator" or "Agent".
- Admin only: an "Open administration" button linking to `/admin`.

## Out of scope
- Role-specific feature panels (Leads, Properties) — added by later tickets.
