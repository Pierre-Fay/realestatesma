# All leads — admin area

## Purpose
Let an Admin review all contact requests and property inquiries across the entire pipeline (LEAD-05).
Assignment, reassignment and unassignment controls are provided by LEAD-04.
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
- Table: Name, Contact (email + phone), Interested in, Notes, Budget (USD), Status badge, Assigned agent, Submitted, Assignment actions.
- Notes expand using a native `details` element to make the complete message readable without JavaScript.
- Missing values use `—`; an unassigned lead has an **Unassigned** badge; a zero budget is shown as `$0`.
- Unassigned leads first, newest first within each assignment group; ID descending breaks timestamp ties.
- Pagination: 15 per page; selected filters are preserved in page links.

## Assignment controls — LEAD-04
- Each row has a labelled native Agent select and **Save assignment** button
  (`PATCH /admin/leads/{lead}/assignment`).
- Select an enabled agent account to assign/reassign; select **Unassigned** to clear the assignment.
- Eligible agents have an enabled linked User with the `agent` role. An unpublished public profile can still
  receive a lead. The eligibility check is repeated server-side when submitting.
- An existing ineligible owner stays visible and is shown as **Unavailable** in a disabled selected option;
  the admin can choose a replacement or **Unassigned**.
- If there are no eligible accounts, an explanation is shown; existing leads can still be unassigned.
- Only `agent_id` changes. Contact details, interest, budget, notes and pipeline status are preserved,
  including closed/lost leads. The former agent loses access and the new agent gains access.
- Successful submissions return to the unfiltered list with **Lead assignment updated.**

## States
- Empty list / no filter matches: **No leads found.**
- Invalid filter values or assignment submissions redirect back with validation errors displayed above the table.

## Localization
- Interface text uses Laravel's `__()` helper; submission timestamps use `Y-m-d H:i`.

## Accessibility
- Filters have explicit labels and use native selects; Filter and Reset are keyboard-accessible.
- Native expandable notes work without JavaScript; the table uses the shared responsive BlatUI wrapper.
- Visitor-provided content is HTML-escaped.

## Out of scope
- Editing visitor contact details, lead history and pipeline analytics.
