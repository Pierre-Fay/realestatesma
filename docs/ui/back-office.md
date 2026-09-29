# Back office — authenticated area

> Draft to be refined together. Describes the shared shell (layout + navigation) in which every
> authenticated page is rendered.

## Purpose
The back office is the authenticated area shared by all signed-in users (role `admin` or `agent`).
Every authenticated page — dashboard, administration, user management, profile — is rendered
inside this shell.

## Implementation
- Layout: `x-app-layout` → `resources/views/layouts/app.blade.php`.
- Built on BlatUI (shadcn-style) components; base template: BlatUI "Dashboard 02" (inset sidebar).
- Key components: `x-ui.sidebar*`, `x-ui.dropdown-menu`, `x-ui.avatar`, `x-ui.separator`.
- Replaced the default Breeze top navigation (`layouts/navigation.blade.php`, removed).

## Shell layout
- **Left sidebar** (inset variant): brand (top), navigation (middle), user menu (bottom).
- **Header bar**: sidebar toggle, vertical separator, page title.
- **Main content**: the current page's slot.

## Sidebar

### Brand
- Icon + `Real Estate SMA` + tagline.

### Navigation (role-aware)
| Item | Route | Visible to |
|---|---|---|
| Dashboard | `/dashboard` | all users |
| Administration | `/admin` | admin only |
| Users | `/admin/users` | admin only |
| Agents | `/admin/agents` | admin only |

### User menu (footer)
- Avatar (initials), name, email.
- Dropdown → Profile (`/profile`), Log out.

## Access rules
- The whole shell requires authentication + verified email; guests are redirected to `/login`.
- Admin-only navigation items are **hidden** for agents and **enforced server-side** (middleware + policies), never by hiding UI alone.

## Pages rendered in the shell
- Dashboard (home) → [`dashboard.md`](dashboard.md)
- Administration → [`admin.md`](admin.md)
- User management → [`admin-users.md`](admin-users.md)
- Agent management → [`admin-agents.md`](admin-agents.md)
- Profile → Breeze default (not yet documented)

## Wanted changes
> Describe the desired UI changes here.
-

## Out of scope
- Public site pages (welcome, login) — not part of the back office.
