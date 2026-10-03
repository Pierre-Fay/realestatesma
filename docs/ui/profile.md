# Account settings — `/profile`

## Purpose
Let a signed-in User manage their login account, with the same BlatUI styling as the back office (UI-01).
This is account information, not the Agent's public profile planned in AGT-03.

## Access rules
- Authentication required; guests are redirected to login.
- Available to both agents and admins. The existing profile routes use `auth`, without requiring verified email.
- The user can manage only their own account; existing controllers and validation remain the source of behavior.

## Layout and content
- Rendered within `x-app-layout`, using the shell's padding and a `max-w-3xl` single-column stack.
- Header: **Account settings**; introduction: **Manage your account**.
- Reachable from the dashboard shortcut and the sidebar's user menu, both labelled **Account settings**.
- Three sectioned BlatUI cards use semantic theme tokens and consistent field spacing.

### Account information
- Name and Login email fields, prefilled with existing values or validation old input.
- **Save account information** posts to `profile.update` with the PATCH method.
- Default validation error bag; changing email keeps the existing email-verification reset behavior.
- Success alert: **Account information saved.**
- Existing verification support remains conditional on the User implementing `MustVerifyEmail`:
  an unverified account can resend using the separate `verification.send` form.
- A `verification-link-sent` status produces a persistent success alert.

### Update password
- Current password, New password, Confirm password; native autocomplete attributes are preserved.
- BlatUI password inputs include their existing show/hide control. Passwords are never repopulated from old input.
- **Update password** posts to `password.update` with the PUT method.
- Field errors use the separate `updatePassword` bag; success alert: **Password updated.**

### Delete account
- **Delete account** opens a BlatUI alert dialog with the existing password-confirmed DELETE action.
- The dialog contains a visible Current password label, field errors from `userDeletion`, Cancel, and
  a destructive submit button. Cancel is a non-submitting button.
- The dialog initially opens when deletion validation fails and focuses the password input when opened.
- Uses the shared alert-dialog focus trap, Escape handling and scroll locking.
- Existing deletion success behavior is preserved: delete the account, log out, invalidate the session, and redirect home.
- In iteration 1, an Admin cannot delete their own account: the action is blocked and a validation message is shown.

## States and accessibility
- Validation errors are displayed next to their own fields; input invalid state and error associations are provided.
- Success messages stay visible rather than disappearing after two seconds.
- Inputs have explicit labels, forms retain CSRF/method fields, and buttons declare their types.
- Cards use `variant="sectioned"` with card-header/card-content to avoid double padding.
- All interface strings use `__()`; user data and messages are escaped.

## Out of scope
- Public Agent bio/contact/photo editing (AGT-03), role changes, account enable/disable, and changes to authentication rules.
