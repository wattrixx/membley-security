# Membley Security Group Website

A responsive corporate website and content management system for Membley
Security Group, covering its college and security services.

## Technology Stack

- PHP 8.3+
- CodeIgniter 4
- MariaDB / MySQL
- Bootstrap 5
- HTML5, CSS3 and JavaScript
- Composer
- Git

## Local Development

1. Clone or open the project.
2. Install dependencies: `composer install`
3. Copy `env` to `.env` if necessary.
4. Configure database credentials in `.env`.
5. Run migrations: `php spark migrate`
6. Start the development server: `php spark serve`

The local website is available at http://localhost:8080/.

## Documentation

See the `docs/` directory for requirements, architecture, database design,
security, deployment and maintenance instructions.

## Important

- Never commit `.env` or production credentials.
- Do not expose application files outside the public web root.
- Document significant changes and database migrations.
- Test changes before deploying them to production.
