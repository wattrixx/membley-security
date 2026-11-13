# Security

## Current implementation

- The CodeIgniter application is served from `public/`; environment secrets
  must remain outside the public directory.
- `.env` is ignored by Git. Never commit production credentials or encryption
  keys.
- Public content has no POST forms, upload handlers, admin routes or
  authenticated actions.
- Database-backed page text, course fields, article content, titles, dates and
  metadata are escaped at render time. Article/page content is rendered as
  plain text with line breaks; stored markup is not trusted.
- Public models restrict pages, courses and posts to explicitly publishable
  states and dates. Detail routes apply the same restrictions as listings.
- Featured-image paths are restricted to existing local image files under
  `assets/images/` or `uploads/`; remote and traversal paths are rejected.
- External Google Maps links opened in a new tab use `rel="noopener noreferrer"`.
- The configured CSRF filter alias exists, but global CSRF filtering is
  currently commented out in `app/Config/Filters.php`.
- No application authentication, authorisation, role checks or upload
  validation are implemented.

## Before adding forms or CMS routes

1. Enable and verify CSRF protection for state-changing routes.
2. Implement authentication and explicit authorisation checks for each
   administrative action.
3. Validate request data on the server and escape output at render time.
4. Validate uploads by content, size and allowed type; store them outside
   executable/public paths where possible.
5. Add rate limiting and spam protections to public forms.
6. Log administrative changes without storing unnecessary sensitive data.

## Production

Use HTTPS, set `CI_ENVIRONMENT=production`, keep detailed errors out of public
responses, protect writable files and secrets, and verify the host's document
root points only to the application's `public/` directory. These production
settings have not yet been validated on the client's cPanel account.
