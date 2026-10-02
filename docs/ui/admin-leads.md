# All leads — admin area

## Purpose
Let an Admin review all contact requests and property inquiries across the entire pipeline (LEAD-05).
Rendered inside the shared [back office shell](back-office.md).

## Access rules
- Authentication + admin role required; agents get `403` and guests are redirected to login.
- Access is enforced by admin middleware and `LeadPolicy`, not only by navigation visibility.
- All leads remain visible, including unassigned leads, completed opportunities, and leads assigned to
  Agent profiles with disabled login access or no linked login.

## Content — `/admin/leads`
- Header: **All leads**; reachable from the admin sidebar and the Administration page's **Manage leads** button.
- GET filters: Assignment (All / Unassigned / Assigned), Agent (all profiles), Status (all pipeline statuses).
- **Filter** applies the selected filters together; **Reset** returns to the complete list.
- Table: Name, Contact (email + phone), Interested in, Notes, Budget (USD), Status badge, Assigned agent, Submitted.
- Notes expand using a native `details` element to make the complete message readable without JavaScript.
- Missing values use `—`; an unassigned lead has an **Unassigned** badge; a zero budget is shown as `$0`.
- Unassigned leads first, newest first within each assignment group; ID descending breaks timestamp ties.
- Pagination: 15 per page; selected filters are preserved in page links.

## States
- Empty list / no filter matches: **No leads found.**
- Invalid filter values redirect back with validation errors displayed above the table.

## Localization
- Interface text uses Laravel's `__()` helper; submission timestamps use `Y-m-d H:i`.

## Accessibility
- Filters have explicit labels and use native selects; Filter and Reset are keyboard-accessible.
- Native expandable notes work without JavaScript; the table uses the shared responsive BlatUI wrapper.
- Visitor-provided content is HTML-escaped.

## Out of scope
- Assignment controls are added by LEAD-04, after this oversight ticket.
- Editing visitor contact details, lead history and pipeline analytics.
