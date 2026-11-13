# Testing

## Commands

Run from the repository root:

```sh
composer validate --no-check-publish
composer test
php spark migrate:status
php spark routes
```

For syntax checks, run `php -l` on changed PHP files. For the public homepage,
run the CodeIgniter development server and request `http://localhost:8080/`.
Check the logo, hero images, stylesheet and script, and verify that navigation
links resolve to the implemented routes.

## Current baseline and milestone verification

### Milestone 3 — public pages

- The core migration reports as applied in batch 1.
- `php spark db:table --show` lists the eleven application tables and the
  CodeIgniter `migrations` table.
- `php spark routes` lists the homepage and ten public-page routes.
- PHP syntax checks passed for all 19 changed or created page/controller/model
  PHP files. `git diff --check` passed.
- Browser checks against `http://localhost:8080/` returned HTTP 200 for the
  homepage and all eight public pages/contact paths. Unknown course and news
  slugs returned 404.
- The mobile courses page displayed its empty state without horizontal
  overflow. Home and About loaded without browser console errors or failed
  requests. The homepage alone preloads its hero image. `pages`, `courses` and
  `posts` were confirmed empty in MariaDB.
- `composer test -- --no-coverage` ran five tests: three passed and two
  database tests errored because the active PHP runtime lacks `sqlite3`.
  Re-run those tests after enabling the extension. Coverage was intentionally
  disabled for this run.

The public routes were checked against the local development server after the
Milestone 3 implementation. Repeat these checks after changes to routes,
models, views or publication rules.

### Milestone 4 — administration foundation

- PHP syntax checks passed for all 23 changed PHP files.
- `composer validate --no-check-publish` passed.
- `php spark migrate:status` confirmed the existing application migration in
  batch 1 and Shield plus three Settings migrations in batch 2.
- `php spark routes` listed the new admin login/logout, dashboard,
  administrator-management and role-filtered future-module routes.
- Browser checks against `http://localhost:8080/` returned HTTP 200 for the
  homepage and all eight public pages/contact routes; the admin stylesheet,
  script and favicon also returned HTTP 200.
- Unauthenticated `/admin` access redirected to the login page. Invalid
  credentials displayed the generic error; valid login and POST logout worked.
- Super Admin account creation, role assignment, and activate/deactivate
  actions worked. A POST without a CSRF token returned HTTP 403.
- Direct URL checks returned HTTP 403 when Admissions accessed Security
  Services enquiries, Security Services accessed College applications, and a
  Content Manager accessed administrator accounts. Permitted but unimplemented
  modules returned HTTP 501 without displaying records.
- Admissions, Security Services and Content Manager dashboards displayed only
  their permitted module counts. At 390px viewport width, the admin dashboard
  had no horizontal overflow and the mobile navigation toggle opened.
- The temporary test accounts and their test records were removed after the
  browser checks.
- `composer test -- --no-coverage` ran five tests: three passed and two
  database tests errored because the active PHP runtime lacks `sqlite3`.
  No rate-limit exhaustion test was performed.

### Milestone 5 — CMS content management

- Applied `2026-11-13-111500_AddCmsPublishingFields` as migration batch 3;
  migration status and route registration were checked.
- Browser-tested public course, news, About-page and announcement publication:
  drafts and future/expired announcements stayed hidden; publishing exposed
  eligible records; reverting the page to draft restored its existing
  controlled template.
- Confirmed article markup-like text renders escaped as text, verified course
  fee rendering is gated, and checked a featured image and alternative text.
- Confirmed a valid JPEG upload succeeds, a text file renamed `.jpg` is
  rejected, media referenced by a course cannot be deleted, and an
  unreferenced upload can be deleted.
- The oversized-file attempt was rejected. The local PHP configuration uses
  `upload_max_filesize=2M` and `post_max_size=8M`, so PHP rejected it before
  the application's 5 MB limit; the controller now reports the server limit
  explicitly. Configure PHP to at least 5 MB to test the application-level
  boundary.
- A draft news preview returned HTTP 200 without creating a database record;
  script-like content was displayed as escaped text.
- Direct URL role checks returned HTTP 200 for Admissions Officer course
  access and HTTP 403 for pages and security enquiries. Content Manager
  pages/news/announcements/media returned 200; courses, administrator
  accounts and security enquiries returned 403. Security Services Officer
  course and college-application access returned 403; its not-yet-implemented
  security-enquiry route returned 501 without records.
- Public homepage, course listing, news listing, About page and admin login
  returned HTTP 200. The admin dashboard had no horizontal overflow at both
  390px and 1440px viewport widths.
- `php -l` passed for all 118 PHP files under `app/` and `tests/`.
  `composer validate --no-check-publish` passed. Migration status confirms
  the CMS migration in batch 3, and `php spark routes` lists its protected
  CRUD, preview and media routes.
- PHPUnit ran five tests: three passed and two errored because the PHP runtime
  lacks `sqlite3` (`ExampleDatabaseTest::testModelFindAll` and
  `ExampleDatabaseTest::testSoftDeleteLeavesRow`). The existing database
  tests could not be completed.
- The test course, post, announcement, page, media, audit entries, temporary
  administrator accounts and upload fixtures were removed. The corresponding
  content, media, user and audit tables were confirmed empty afterward.
- The changed files use CRLF line endings; whitespace validation passed with
  `git -c core.whitespace=cr-at-eol diff --check`.
