# Architecture

## Runtime

The application uses CodeIgniter 4's MVC structure and server-rendered PHP
views. The web server document root is `public/`; `public/index.php` boots the
application.

## Current request flow

1. `app/Config/Routes.php` maps the homepage and explicit public-page routes
   to `Home` and `PublicSite`.
2. `Home` renders the homepage and queries public featured courses and recent
   posts.
3. `PublicSite` renders the About, College, Security Services, Courses, News
   and Contact pages.
4. `CourseModel`, `PostModel` and `PageModel` query only content that meets
   the public publication rules.
5. `/admin/*` routes use Shield's session filter and explicit permission
   filters before calling the admin controllers. The admin controllers render
   the dashboard, login, forbidden and administrator-account views.
6. Public views extend `app/Views/layouts/main.php`; admin views extend
   `app/Views/layouts/admin.php`. The separate admin layout provides
   role-filtered navigation, sign-out and responsive menu behavior.

## Current structure

- `app/Controllers/`: public controllers plus admin authentication,
  dashboard, administrator management and explicitly unimplemented module
  endpoints.
- `app/Commands/`: the CLI-only first-Super-Admin provisioning command.
- `app/Models/`: read-only public queries and the audit-log model used for
  administrator account changes.
- `app/Views/`: public pages and shared public layout, plus the separate admin
  layout, authentication, dashboard, account-management and future-module
  views.
- `app/Config/`: framework configuration, routes, filters and database groups.
- `app/Database/Migrations/`: the existing core schema migration; Shield and
  Settings migrations are supplied by their Composer packages.
- `app/Filters/`: Shield's session, permission, CSRF, and authentication-rate
  filters are configured through the framework and Shield.
- `public/assets/`: public assets and the separate admin stylesheet/script.
- `writable/`: framework runtime storage; it must not be web-accessible.

## Database boundary

Course and post listings use the existing `courses` and `posts` tables.
The fixed About, College and Security Services pages use controlled views, with
a published row from `pages` able to override a matching page slug. Content
from database fields is escaped as text; stored HTML is not executed. Enquiry,
application and content-management workflows remain future work. Shield owns
the administrator identity, group and permission tables; the application does
not add a parallel user table or role schema.

## Administration boundary

The dashboard only queries module counts for permissions held by the current
administrator. User-list queries select usernames, email identities, status
and activity timestamps only; passwords and hashes are not selected or
rendered. Account creation, role assignment and status changes are protected
by route filters and are recorded in the existing `audit_logs` table.
Protected admin responses are not page-cached.

## Design constraints

- Keep request handling in controllers, not views.
- Use CodeIgniter Query Builder and validation when data-backed modules are
  introduced.
- Keep public content sections controlled and reusable; do not add an
  unrestricted page builder.
- Keep credentials in the ignored environment file, never in `public/`.
