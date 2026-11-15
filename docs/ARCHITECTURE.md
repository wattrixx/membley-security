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
   filters before calling the admin controllers. CMS controllers validate
   writes, use the existing content tables, and audit mutations.
6. Public views extend `app/Views/layouts/main.php`; admin views extend
   `app/Views/layouts/admin.php`. The separate admin layout provides
   role-filtered navigation, sign-out and responsive menu behavior.
7. CMS-managed media is validated and stored with generated names in
   `public/uploads/media/`; media records and referenced images are managed
   through the existing `media` table.
8. Explicit public POST routes handle College applications, College enquiries
   and Security Services enquiries. The workflows validate input, store each
   type separately and redirect to a session-backed confirmation page.
9. Three separately authorized administration routes review those submissions.
   The shared controller implementation fixes each route's table, entity type
   and permissions in its concrete controller; College enquiry queries also
   require `enquiry_type = 'college'`. Filters are kept in the administrator's
   session instead of URLs. Private notes and audit history are retrieved only
   after the matching submission and permission have been checked.

## Current structure

- `app/Controllers/`: public controllers plus admin authentication,
  dashboard, administrator management, CMS content controllers and
  separate submission-management controllers.
- `app/Commands/`: the CLI-only first-Super-Admin provisioning command.
- `app/Models/`: public-content queries, CMS models and the audit-log model
  used for administrator and content changes.
- `app/Views/`: public pages and shared public layout, plus CMS editors,
  previews, media views, authentication, dashboard, account-management and
  submission-management views.
- `app/Config/`: framework configuration, routes, filters and database groups.
- `app/Database/Migrations/`: the core schema and additive CMS publishing
  and submission migrations; Shield and Settings migrations are supplied by their Composer
  packages.
- `app/Filters/`: Shield's session, permission, CSRF, and authentication-rate
  filters are configured through the framework and Shield.
- `public/assets/`: public assets and the separate admin stylesheet/script.
- `writable/`: framework runtime storage; it must not be web-accessible.

## Database boundary

Course, post and announcement public listings use the existing tables and
exclude draft, archived, future and otherwise ineligible records. The fixed
About, College and Security Services pages use controlled views, with a
published row from `pages` able to override a matching allowlisted slug.
Database content is escaped and rendered as text; stored markup is not
executed. College applications use `applications`, College enquiries use the
existing `enquiries` table with a College-only type, and Security Services
enquiries use `security_enquiries`. Shield owns administrator identity,
group and permission tables; the application does not add a parallel user
table or role schema.

## Administration boundary

The dashboard only queries module counts for permissions held by the current
administrator. User-list queries select usernames, email identities, status
and activity timestamps only; passwords and hashes are not selected or
rendered. Account creation, role assignment and status changes are protected
by route filters and are recorded in the existing `audit_logs` table.
Protected admin responses are not page-cached.

CMS permissions are enforced on routes and server-side actions: Admissions
Officers manage courses, Content Managers manage pages/news/announcements and
media, and Super Admins have all CMS permissions. Security Services Officers
cannot access these CMS or college modules. CMS records are audited through
the existing audit log.

Admissions Officers can view and manage applications and College enquiries;
Security Services Officers can view and manage only Security Services
enquiries. Content Managers have neither submission permission. Super Admins
have both. Each operation checks its permission, and note and audit queries are
scoped by both entity type and record ID. Status and note writes are
transactional with their audit entries. No submitted records are deleted.

## Design constraints

- Keep request handling in controllers, not views.
- Use CodeIgniter Query Builder and validation when data-backed modules are
  introduced.
- Keep public content sections controlled and reusable; do not add an
  unrestricted page builder.
- Keep public submission storage, confirmation and administration
  separate for applications, College enquiries and Security Services
  enquiries.
- Keep credentials in the ignored environment file, never in `public/`.
