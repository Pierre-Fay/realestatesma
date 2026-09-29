# Dashboard — `/dashboard`

## Purpose
Authenticated landing page for Users (agent or admin role).

## Access rules
- Auth required; guests are redirected to `/login`.

## Layout
- Left sidebar (BlatUI, `variant="inset"`):
  - Brand: "Real Estate SMA" with a building icon.
  - Navigation: Dashboard (active state per route), Administration (admin only).
  - Footer: current user (initials avatar, name, email) with a dropdown → Profile / Log out.
- Header bar: sidebar toggle, vertical separator, page title.
- Main content: role-aware welcome card.

## Content (role-aware)
- "Welcome, :name" heading with the signed-in user's name.
- Role indicator: "You are signed in as an Administrator." or "… as an Agent."
- Admin only: an "Open administration" button linking to `/admin`.

## Out of scope
- Role-specific feature panels (Leads, Properties) — added by later tickets.
