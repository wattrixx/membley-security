# cPanel Deployment

## Hosting Requirements
- PHP 8.2 or newer, compatible with the application.
- MariaDB/MySQL.
- Required PHP extensions.
- HTTPS.
- SSH access where available.

## Deployment Principles
- The web document root must point to CodeIgniter's `public` directory.
- Application code, vendor files, writable files and environment secrets must
  not be directly accessible from the public web.
- Configure production database credentials separately from local credentials.
- Set `CI_ENVIRONMENT = production`.
- Disable public error detail and enable appropriate logging.
- Run migrations carefully during deployment.
- Back up files and database before updates.

Exact cPanel deployment steps will be added after confirming the client's
hosting layout.
