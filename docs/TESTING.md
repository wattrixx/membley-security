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
