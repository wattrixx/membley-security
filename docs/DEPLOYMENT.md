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
- The CMS stores images in `public/uploads/media/`. Confirm that the host uses
  Apache rules from that directory's `.htaccess` (including `AllowOverride`
  support), denies script execution and serves only validated image files.
  If the host does not honor these rules, configure an equivalent server-level
  policy or move media outside the document root before enabling uploads.
- Set PHP's `upload_max_filesize` to at least `5M` and `post_max_size` to a
  value above `5M`; lower PHP limits reject files before the application can
  apply its own size validation.

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

The public website, admin authentication and CMS content/media modules are
implemented. Applications, public enquiry forms and their administration
workflows are not. Email-based password reset is not configured pending a
verified production sender.
