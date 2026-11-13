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
2. Review migration status, back up the database, and apply reviewed pending
   migrations with `php spark migrate --all`.
3. Install production dependencies with
   `composer install --no-dev --optimize-autoloader`.
4. Set the verified production environment, database credentials, encryption
   key and HTTPS configuration outside the public directory.
5. Provision the first Super Admin using the documented environment-variable
   method; do not deploy default credentials or test accounts.
6. Verify public routes, admin login, role restrictions, local assets, logging
   and error handling over HTTPS.
7. Confirm the public document root does not expose application or environment
   files.

The authentication/dashboard foundation is implemented, but content CRUD,
uploads, public forms and their administration workflows are not. Email-based
password reset is not configured pending a verified production sender.
