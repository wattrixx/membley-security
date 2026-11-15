# Security

## Current implementation

- The CodeIgniter application is served from `public/`; environment secrets
  must remain outside the public directory.
- `.env` is ignored by Git. Never commit production credentials or encryption
  keys.
- Public forms accept College applications, College enquiries and Security
  Services enquiries through separate routes and separate persistence paths.
  CMS image uploads remain available only to authorized administrators.
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
- CMS routes enforce role permissions server-side. Admissions Officers manage
  courses; Content Managers manage pages, news, announcements and media;
  Security Services Officers cannot access these CMS or college modules.
  Public submissions do not expose record retrieval routes.
- Application, College enquiry and Security Services enquiry admin routes
  require their own view/manage permissions; concrete controllers repeat the
  authorization checks before reading or writing. College enquiries are
  restricted to the College type, and notes/history are scoped to the
  workflow and record. Numeric IDs and internal Security enquiry references
  do not grant access. Content Managers cannot access submission records.
- Admin submission status changes and note additions commit with audit events.
  Audit details contain only status values, not submitted details or note text.
  Internal notes are escaped, private to authorized administrators and have
  no edit/delete route. Session-backed filters avoid placing searches in URLs.
- CSRF protection is enabled globally in session mode. State-changing admin
  operations and public form submissions are POST-only.
- Public submissions are server-validated and escaped on output. Application
  course IDs are rechecked against published, currently available courses.
  Honeypot fields, per-form IP throttling (five attempts per ten minutes),
  CSRF and one-hour session-backed one-time form tokens protect submissions.
  Successful POSTs redirect to a confirmation page and do not expose submitted
  details in URLs or confirmation content.
- Public submission records are written with a generic audit event in one
  database transaction. Audit records omit applicant/enquirer details and IP
  addresses. Application references use random bytes and are not sequential.
- The public Privacy Notice describes current handling but still needs an
  organisation-approved retention period and privacy contact before
  production use. Do not treat it as legal approval.
- Email notifications are disabled by default. They require
  `publicSubmissions.notificationsEnabled = true`, verified
  `email.fromEmail`/`email.fromName`, the appropriate
  `publicSubmissions.collegeRecipient` or
  `publicSubmissions.securityRecipient`, and mail transport settings. Email
  settings can be supplied through the ignored `.env`; errors are logged
  without exposing submitted details and do not roll back a stored submission.
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
- CMS create/update/delete actions are recorded in the existing audit log.
- Image uploads accept JPEG, PNG and WebP only, with matching extension and
  server-detected MIME type, a 5 MB size limit, and bounded image dimensions.
  Names are generated from random bytes; client filenames are sanitized.
  Uploads are stored in `public/uploads/media/`, with an Apache `.htaccess`
  rule denying script-like extensions. Referenced media cannot be deleted.
  This Apache protection has not been verified on the production host; the
  directory must not permit execution or unsafe MIME handling.
- The first Super Admin is created by a CLI-only provisioning command using
  an externally supplied process environment password. No default credentials
  are hardcoded. Email/reset flows are not configured until a verified sender
  is provided.
## Before production

1. Keep permission checks on every new route and protected operation; do not
   rely on hiding navigation items.
2. Validate request data on the server and escape output at render time.
3. Keep upload validation in place and verify server configuration denies
   executable content in the public media directory. Prefer storage outside
   the document root if the deployment supports safe controlled delivery.
4. Confirm the Privacy Notice's retention schedule and privacy contact.
5. Configure and test verified notification sender/recipient details, or leave
   notifications disabled.
6. Log administrative changes without storing unnecessary sensitive data.

## Production

Use HTTPS, set `CI_ENVIRONMENT=production`, keep detailed errors out of public
responses, protect writable files and secrets, and verify the host's document
root points only to the application's `public/` directory. These production
settings have not yet been validated on the client's cPanel account.
