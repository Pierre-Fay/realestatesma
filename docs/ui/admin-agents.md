# Agent management — `/admin/agents`

> Draft to be refined together. Describes the desired UI and behaviour of the agent management pages.

## Purpose
Admin-only area to create agent accounts. Creating an agent atomically creates both the login
(`User`, role `agent`) and the linked public profile (`Agent`) — see
[`business-rules.md`](../data/business-rules.md). Rendered inside the back office shell
(see [`back-office.md`](back-office.md)).

## Pages
- **List — `/admin/agents`**: table of agents (name with a default icon avatar, public email, phone,
  profile-visibility badge, linked login email) + a "New agent" button.
- **Create — `/admin/agents/create`**: form capturing the login and the public profile.

## Access rules
- Auth + admin role required; agents get `403`.
- Only admins see the "Agents" entry point in the sidebar.

## Create form
| Field | Notes |
|---|---|
| Full name | sets both the login name and the public profile name |
| Login email | private, unique, used to sign in — never shown publicly |
| Public email | unique, shown on the public profile (separate from the login email) |
| Password + confirmation | the admin sets the agent's initial password |
| Phone | optional, public |
| Bio | optional, public |
| Public profile visible | checkbox (`is_active`, checked by default) |

- No photo upload at creation; the UI shows a default agent icon (photo comes with AGT-03).

## Rules
- Login email and public email are **separate** (the auth identifier is never published) and each is unique.
- The login + profile pair is created in a **single transaction**; the account is created **verified and enabled**, so the agent can sign in immediately.

## Wanted changes
> Describe the desired UI changes here.
-

## Out of scope
- Agent self-profile editing (AGT-03), public agent directory (AGT-02), profile without login (AGT-05), photo upload.
