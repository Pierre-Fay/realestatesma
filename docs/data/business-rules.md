# List of business rules

### User and Agent optional 1 to 1 relationship
A User can exist without an Agent if it has the role admin, and an Agent profile can exist without a User profile,
as a purely informational entity (A visitor can see agents, their profile, their properties). If an Agent requires a
User to have access to Agent backend functionalities, an admin can create an Agent's User profile with role agent.

### Roles and role-based access control
A User has exactly one role: `admin` or `agent`. Certain pages and actions are restricted to the `admin` role
(e.g. property approval, user management, category management). An `agent` can never perform an admin-only operation.
Authorization is enforced server-side (policies), never only by hiding UI controls.

### Account creation and self-registration
Accounts are created by an Admin; public self-registration is disabled. The login page is exposed at `/login` only —
there is no public sign-up and no login entry point advertised on public pages.

### Enable or disable a user's login independently of their public profile
An Admin can enable or disable a User's login access without deleting or unpublishing their Agent profile. Safety rule:
an Admin cannot disable their own account, nor disable the last remaining active Admin. Disabling a user immediately
revokes their active sessions and remember-me token.

### Account deletion
A signed-in User may delete their own login account (from the account settings page). In iteration 1, an **Admin
cannot delete their own account** — a simple guard that prevents removing the last Admin and is accepted as an
iteration-1 quirk. Deleting an Agent's login keeps the public Agent profile in place (attribution preserved, see
AGT-05) but clears `agent_id` on their leads, which become unassigned; this consequence is documented and will be
revisited with AGT-05.

### Property approval workflow
A property is only visible to the public after an Admin reviews and approves it. Until then it is inactive
(`is_active = false` by default). An Agent creates/edits a listing; an Admin approves it before it goes live.
An Admin may also **delete** a listing, permanently removing it and its photo files.

### Category vocabulary vs. assignment (design decision)
The category **vocabulary** is admin-managed — an Admin creates, edits and reorders categories (CAT-01).
**Assignment** is done by the listing's owner: an Agent may assign the structural, location and amenity
categories (`property_type`, `property_area`, `property_feature`) to **their own** listings; the market status
(`property_status`) and labels (`property_label`) remain admin-only. A listing may have a single featured image;
conditionally-shown prices and location coordinates are optional.

### Property publishing, sold and featured status
Public visibility is governed by `is_active`, independently of `is_sold` and `is_featured`. Only an Admin can change
publishing state: `approve()` publishes a property and `unpublish()` withdraws it from the market (without marking it
sold). An Agent marks a property as sold (with a sold date), which also takes it off the market; only an Admin can
feature a property (`is_featured`).

### Lead lifecycle
A lead moves through a status pipeline: `new -> contacted -> qualified -> closed`, or it ends `lost` whenever the
opportunity fails — no answer to contact attempts, criteria mismatch, or lost deal. The pipeline describes the
intended progression and is **not enforced as a strict sequence**: an Agent may move a lead to any other status to
correct a mistake. The only transition that is forbidden is moving a lead back to `new` once it has left that state.

### Lead creation
A lead is created through two paths: the general contact page creates an **unassigned** lead (an Admin assigns it
later), while a property inquiry creates a lead **assigned to the property's agent** (the first agent, ordered by
`agent_order`). For a property inquiry, the property name is stored in `interested_in` and the visitor's message in
`notes`. A property with no assigned agent yields an unassigned lead.

### Lead assignment
A lead can exist without an assigned agent (`agent_id` nullable) from the moment it is created. An Admin assigns the
lead to an Agent, may reassign any lead, and may unassign any lead by clearing `agent_id`. An Agent may reassign
only a lead currently assigned to their own profile, and must choose another Agent; agents cannot claim unassigned
leads or unassign leads. Assignment changes preserve the lead's status, contact details, interest, budget and notes.
The former Agent immediately loses access and the new Agent gains access through their existing lead area.

### Eligibility for manual lead assignment
New manual assignments and reassignments require an Agent linked to an enabled User with the `agent` role.
Public profile visibility (`Agent.is_active`) does not affect eligibility. Profiles with no login or a disabled
login cannot receive new manual assignments; their existing leads remain visible to Admins and can be reassigned
or unassigned. This eligibility rule applies to manual assignment, not the existing property-inquiry routing.

### Admin lead oversight
An Admin can view every lead from both creation paths, including unassigned leads, leads assigned to any Agent
(even profiles without a login or with disabled login access), and leads in every pipeline status, including `closed`
and `lost`. Assignment, Agent and status filters narrow the list only when selected; no leads are excluded by default.

### GDPR consent for lead collection
A Visitor submitting a property inquiry or a general contact request must give explicit consent (checkbox) before
their personal data is collected. The site must publish a legal notice and a privacy policy explaining data usage.
Consent is **recorded with a timestamp** (`consented_at`) as proof that explicit consent was given.

### Agent account creation
Creating an Agent account atomically creates both the login (User with role `agent`) and the linked public Agent
profile, in a single transaction. The login email is private (used to authenticate); the agent's public contact email
is a separate value and is never the auth identifier. The created login is enabled and pre-verified, so the agent can
sign in immediately. An Admin may also create a public Agent profile with no linked login, so a departed agent's
listing history stays attributed or someone can be listed publicly without system access.

### Agent profile visibility
An Admin controls whether an Agent's public profile is visible to visitors (`Agent.is_active`), independently of the
agent's login access (`User.is_enabled`). Unpublishing a profile keeps the agent's listing history attributed.

### Categories
Categories are managed by an Admin and drive search filters. A category belongs to one of five fixed group types:
`property_area`, `property_feature`, `property_label`, `property_status`, `property_type`. Until the admin category
management page ships (CAT-01), the category values are seeded defaults (`CategorySeeder`) rather than admin-curated.

### Dual currency pricing
A property has a price in USD and a price in MXN. An optional `show_both_prices` flag controls whether both prices are
displayed to the visitor.

### Open houses
An Agent can create, publish, or cancel open house events for their properties. Visitors can browse upcoming events.

### Property images
A property can have gallery images and a single featured image. Each image is either of type `featured_image` or
`gallery`, and at most one image per property is `featured_image`.
