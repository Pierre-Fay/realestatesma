# User management — `/admin/users`

> Draft to be refined together. Describes the desired UI and behaviour of the user management page.

## Purpose
Admin-only list of Users, with the ability to enable or disable each user's login access
independently of their public profile (see [`business-rules.md`](../data/business-rules.md)).
Rendered inside the back office shell (see [`back-office.md`](back-office.md)).

## Access rules
- Auth + admin role required; agents get `403`.
- Only admins see the "Users" entry point in the sidebar.

## Content
- Table: Name (avatar + initials), Email, Role (badge), Status (Enabled/Disabled badge), Actions.
- Action per row:
  - **Disable** — destructive button; opens a confirmation dialog; on confirm the user is disabled
    and their active sessions are revoked immediately.
  - **Enable** — direct button for a disabled user.
  - The Disable control is hidden for the current user and for the last active admin (safety rule),
    replaced by a disabled button with an explanatory tooltip.
- Pagination (15 per page).
- Success alert after a change.

## Safety rule
- An Admin cannot disable their own account, nor the last remaining active Admin. Enforced
  server-side by `UserPolicy::disable()`.

## Wanted changes
> Describe the desired UI changes here.
-

## Out of scope
- Creating accounts (AGT-01), editing profile data, role management.
