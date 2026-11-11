# System Architecture

## Architecture
Use CodeIgniter 4 MVC with server-rendered PHP views.

## Main Layers
- Controllers: receive requests and coordinate application actions.
- Models: handle database operations.
- Views: render public and administrative interfaces.
- Filters: enforce authentication, authorisation and request protections.
- Migrations: track database schema changes.

## Initial Modules
- Public website
- College and courses
- Security services
- Applications and enquiries
- Blog and announcements
- CMS pages and media
- Administration and permissions

## Architectural Principles
- Keep business logic out of views.
- Validate all incoming data on the server.
- Use framework database facilities and parameterised queries.
- Keep modules maintainable and avoid unnecessary duplication.
- Record significant architectural decisions in this directory.
