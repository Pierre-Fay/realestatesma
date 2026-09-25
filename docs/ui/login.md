# Login page — `/login`

> Draft to be refined together. Describes the desired UI and behaviour of the login page.

## Purpose
Let a User (agent or admin role) sign in with email + password and reach their dashboard.

## Access rules
- **Guest only**: authenticated users who visit `/login` are redirected to `/dashboard`.
- **Not advertised**: no login link/button is shown on public pages. The page is reachable only by typing `/login`.
- Unauthenticated access to any protected route redirects here.
- No public sign-up: accounts are created by an Admin (see `business-rules.md`).

## Layout
- Centered single card on a full-height page.
- Brand above the card: text logo `Real Estate SMA`.
- Card: title "Sign in", short description, then the form.

## Form
| Field | Type | Notes |
|---|---|---|
| Email | `email` | pre-filled with `old('email')`, required, autofocus, `autocomplete="username"` |
| Password | `password` | required, `autocomplete="current-password"`, show/hide toggle |
| Remember me | checkbox | optional, `name="remember"` |
| Forgot password? | link | goes to `/forgot-password` |
| Submit | button | full-width "Sign in" |

## States
- **Default**: empty form.
- **Validation error**: field-level error under the offending field (invalid credentials land on `email`).
- **Session status**: success alert when arriving from a password reset.
- **Disabled account**: *out of scope for AUTH-01 — handled by AUTH-04.*

## Redirects
- Success → `dashboard` (or the previously intended URL).
- Already authenticated → `dashboard`.
- Logout → `/`.

## Localization
- All strings wrapped in `__()` for future EN/ES (LOC-01). Currently English only.

## Accessibility
- Labels tied to inputs via `for`/`id`.
- Error text announced (`role="alert"` via BlatUI field-error).

## Out of scope
- Registration, email verification, role-specific dashboards (AUTH-03), disabled-account blocking (AUTH-04).
