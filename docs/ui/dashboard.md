# Dashboard — `/dashboard`

> UI-01: back-office landing page and role-aware shortcuts.

## Purpose
Home page of the back office (see [`back-office.md`](back-office.md)): the authenticated landing
page for Users (agent or admin role).

## Access rules
- Auth required; guests are redirected to `/login`.
- The route also uses the existing `verified` middleware.
- Rendered inside the back office shell (sidebar + header).

## Content (role-aware)
- Compact BlatUI welcome card with "Welcome, :name" and the signed-in user's name.
- Role indicator: "You are signed in as an Administrator." or "… as an Agent."
- The welcome card uses `variant="sectioned"` so its header is not double-padded.
- **Quick access**: a responsive grid of shared `x-dashboard-shortcut` cards, each with an icon, title,
  description and a named-route link. The entire card is keyboard-accessible and clickable.
- Grid: one column on small screens, two from `sm`, three from `xl`; shared theme tokens support dark mode.

### Agent shortcuts
| Shortcut | Route |
|---|---|
| Leads | `/leads` |
| My listings | `/listings` |
| Create listing | `/listings/create` |
| Account settings | `/profile` |

### Admin shortcuts
| Shortcut | Route |
|---|---|
| All leads | `/admin/leads` |
| Properties | `/admin/properties` |
| Agents | `/admin/agents` |
| Users | `/admin/users` |
| Open administration | `/admin` |
| Account settings | `/profile` |

## Accessibility
- Page header, welcome and Quick access use ordered heading levels; shortcut titles are `h3`.
- Cards have visible keyboard focus, a readable link name, and decorative icons hidden from assistive technology.
- User names are HTML-escaped; interface text uses `__()`.
- Shortcuts follow existing role access rules; destination routes continue to enforce authorization server-side.

## Wanted changes
> Describe the desired UI changes here.
-

## Out of scope
- Live statistics, charts, activity feeds and shortcuts to features that have not shipped.
