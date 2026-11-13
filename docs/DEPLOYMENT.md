# Deployment

## Current status

cPanel deployment has not been executed or verified. Confirm the hosting
account's PHP version, extensions, database access and document-root layout
before scheduling production deployment.

## Deployment requirements

- Point the web document root at the project's `public/` directory.
- Keep `app/`, `vendor/`, `.env` and `writable/` outside web-accessible paths.
- Configure production database credentials and a unique encryption key in
  the host environment, not in source control.
- Set `CI_ENVIRONMENT=production`, enable HTTPS and disable public debug output.
- Ensure the configured writable directories are writable by the application
  user but are not directly served as executable content.

## Release checklist

1. Back up the deployed files and database.
2. Review migration status and deploy only reviewed pending migrations.
3. Install production dependencies with
   `composer install --no-dev --optimize-autoloader`.
4. Verify the home route, local assets, logging and error handling over HTTPS.
5. Confirm the public document root does not expose application or environment
   files.

Do not treat the current schema as a completed CMS deployment: authentication,
forms, administration workflows and their security controls are not yet
implemented.
