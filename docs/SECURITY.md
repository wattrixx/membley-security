# Security

## Current implementation

- The CodeIgniter application is served from `public/`; environment secrets
  must remain outside the public directory.
- `.env` is ignored by Git. Never commit production credentials or encryption
  keys.
- Public content has no POST forms or upload handlers. The admin area is
  separate from public routes.
- Database-backed page text, course fields, article content, titles, dates and
  metadata are escaped at render time. Article/page content is rendered as
  plain text with line breaks; stored markup is not trusted.
- Public models restrict pages, courses and posts to explicitly publishable
  states and dates. Detail routes apply the same restrictions as listings.
- Featured-image paths are restricted to existing local image files under
  `assets/images/` or `uploads/`; remote and traversal paths are rejected.
- External Google Maps links opened in a new tab use `rel="noopener noreferrer"`.
- CodeIgniter Shield 1.4.1 provides session-based authentication and group/
  permission authorization. Registration, magic links and remember-me are
  disabled; there is no public admin registration route.
- Every protected admin route requires a Shield session and an explicit
  permission filter. Admissions and Security Services permissions are
  separate; direct requests to a restricted route are denied server-side.
- CSRF protection is enabled globally in session mode. State-changing admin
  operations are POST-only.
- Shield's `auth-rates` filter limits authentication-route requests to 10 per
  IP address per minute.
- Administrator passwords use Shield password hashing and the configured
  12-character minimum. Invalid email/password attempts display the same
  generic credential error. Passwords and hashes are not rendered or logged.
- Session IDs are regenerated with old session data destroyed. Session
  cookies are HttpOnly and SameSite=Lax; the Secure flag is enabled in the
  production environment.
- Admin routes are excluded from page caching. Admin responses also set
  private/no-store headers.
- Super Admin account changes are audited in `audit_logs`; the account change
  and audit record are committed together. Administrators cannot change their
  own role/status, and the last active Super Admin cannot be demoted or
  deactivated.
- The first Super Admin is created by a CLI-only provisioning command using
  an externally supplied process environment password. No default credentials
  are hardcoded. Email/reset flows are not configured until a verified sender
  is provided.
- No CMS uploads or public enquiry/application forms exist yet, so their
  validation and storage controls remain future work.

## Before adding forms or CMS routes

1. Keep permission checks on every new route and protected operation; do not
   rely on hiding navigation items.
2. Validate request data on the server and escape output at render time.
3. Validate uploads by content, size and allowed type; store them outside
   executable/public paths where possible.
4. Add rate limiting and spam protections to public forms.
5. Log administrative changes without storing unnecessary sensitive data.

## Production

Use HTTPS, set `CI_ENVIRONMENT=production`, keep detailed errors out of public
responses, protect writable files and secrets, and verify the host's document
root points only to the application's `public/` directory. These production
settings have not yet been validated on the client's cPanel account.
