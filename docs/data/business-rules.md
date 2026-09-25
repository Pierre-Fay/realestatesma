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
an Admin cannot disable their own account, nor disable the last remaining active Admin.

### Property approval workflow
A property is only visible to the public after an Admin reviews and approves it. Until then it is inactive
(`is_active = false` by default). An Agent creates/edits a listing; an Admin approves it before it goes live.

### Property publishing, sold and featured status
Public visibility is governed by `is_active`, independently of `is_sold` and `is_featured`. Only an Admin can change
publishing state: `approve()` publishes a property and `unpublish()` withdraws it from the market (without marking it
sold). An Agent marks a property as sold (with a sold date), which also takes it off the market; only an Admin can
feature a property (`is_featured`).

### Lead lifecycle
A lead moves through a fixed status pipeline: `new -> contacted -> qualified -> closed`, or it ends `lost` whenever the
opportunity fails — no answer to contact attempts, criteria mismatch, or lost deal. An Agent records a successful
contact (`contacted`), qualifies the lead if budget and interest align (`qualified`), and ends it `closed` (deal won)
or `lost`.

### Lead assignment
A lead can exist without an assigned agent (`agent_id` nullable) from the moment it is created. An Admin assigns the
lead to an Agent; a lead may also be reassigned to another Agent (by an Admin or an Agent) so leads are not lost.

### GDPR consent for lead collection
A Visitor submitting a property inquiry or a general contact request must give explicit consent (checkbox) before
their personal data is collected. The site must publish a legal notice and a privacy policy explaining data usage.

### Agent account creation
Creating an Agent account atomically creates both the login (User with role `agent`) and the linked public Agent
profile. An Admin may also create a public Agent profile with no linked login, so a departed agent's listing history
stays attributed or someone can be listed publicly without system access.

### Agent profile visibility
An Admin controls whether an Agent's public profile is visible to visitors (`Agent.is_active`), independently of the
agent's login access (`User.is_enabled`). Unpublishing a profile keeps the agent's listing history attributed.

### Categories
Categories are managed by an Admin and drive search filters. A category belongs to one of five fixed group types:
`property_area`, `property_feature`, `property_label`, `property_status`, `property_type`.

### Dual currency pricing
A property has a price in USD and a price in MXN. An optional `show_both_prices` flag controls whether both prices are
displayed to the visitor.

### Open houses
An Agent can create, publish, or cancel open house events for their properties. Visitors can browse upcoming events.

### Property images
A property can have gallery images and a single featured image. Each image is either of type `featured_image` or
`gallery`, and at most one image per property is `featured_image`.
