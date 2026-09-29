# Dashboard — `/dashboard`

> Draft to be refined together. Describes the desired UI and behaviour of the dashboard page.

## Purpose
Home page of the back office (see [`back-office.md`](back-office.md)): the authenticated landing
page for Users (agent or admin role).

## Access rules
- Auth required; guests are redirected to `/login`.
- Rendered inside the back office shell (sidebar + header).

## Content (role-aware)
- "Welcome, :name" heading with the signed-in user's name.
- Role indicator: "You are signed in as an Administrator." or "… as an Agent."
- Admin only: an "Open administration" button linking to `/admin`.

## Wanted changes
> Describe the desired UI changes here.
-

## Out of scope
- Role-specific feature panels (Leads, Properties) — added by later tickets.
