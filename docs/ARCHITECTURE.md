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
5. Public views extend `app/Views/layouts/main.php`, which provides the shared
   navigation, contact details, stylesheet and browser JavaScript.

## Current structure

- `app/Controllers/`: `Home` and the public-page `PublicSite` controller,
  extending the shared `BaseController`.
- `app/Models/`: read-only public queries for courses, posts and fixed pages.
- `app/Views/`: homepage, public pages, course/news listings and details,
  shared cards/layout and CodeIgniter error views.
- `app/Config/`: framework configuration, routes, filters and database groups.
- `app/Database/Migrations/`: the core schema migration.
- `app/Filters/`: no custom application filters yet.
- `public/assets/`: public CSS, JavaScript and image files.
- `writable/`: framework runtime storage; it must not be web-accessible.

## Database boundary

Course and post listings use the existing `courses` and `posts` tables.
The fixed About, College and Security Services pages use controlled views, with
a published row from `pages` able to override a matching page slug. Content
from database fields is escaped as text; stored HTML is not executed. Enquiry,
application and administration workflows remain future work.

## Design constraints

- Keep request handling in controllers, not views.
- Use CodeIgniter Query Builder and validation when data-backed modules are
  introduced.
- Keep public content sections controlled and reusable; do not add an
  unrestricted page builder.
- Keep credentials in the ignored environment file, never in `public/`.
