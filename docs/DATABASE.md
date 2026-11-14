# Database

## Current schema status

The migration `2026-11-11-104518_CreateMembleyCoreTables` is applied in the
configured MariaDB database as batch 1. It creates eleven application tables.
CodeIgniter Shield's authentication migration and CodeIgniter Settings'
migrations are applied in batch 2. The additive
`2026-11-13-111500_AddCmsPublishingFields` migration is applied in batch 3.
The additive `2026-11-14-090000_AddPublicSubmissionWorkflows` migration is
applied in batch 4; it adds public submission fields and a dedicated Security
Services enquiry table. The database also has CodeIgniter's `migrations` table.

The public website reads published content from `pages`, `courses` and
`posts`. The admin foundation uses Shield's user and authorization tables and
the existing `audit_logs` table. No custom migration or duplicate user table
was added in Milestone 4.

## Application tables

| Table | Current purpose |
| --- | --- |
| `course_categories` | Course category names, slugs, descriptions and status. |
| `courses` | Course details, optional category, fee, status and publication fields. |
| `posts` | Article content, author label, metadata and publication status. |
| `announcements` | Announcement content and optional start/end dates. |
| `pages` | Reusable page content, metadata and publication status. |
| `media` | Stored file names, paths, MIME type, size and alt text. |
| `enquiries` | College enquiries only; includes privacy acknowledgement and status. |
| `security_enquiries` | Separate Security Services contact, organization, category and message fields. |
| `applications` | Course application contact fields, country, privacy acknowledgement and status. |
| `employer_training_requests` | Organisation and training-request fields. |
| `site_settings` | Unique setting keys and optional text values. |
| `audit_logs` | Optional user reference, action, entity and request details. |

Shield adds `users`, `auth_identities`, `auth_logins`, `auth_token_logins`,
`auth_remember_tokens`, `auth_groups_users` and `auth_permissions_users`.
Registration, token login and remember-me are disabled for the application,
but the package schema remains intact. CodeIgniter Settings adds its
`settings` table, used for Shield configuration.

The migration adds an auto-incrementing `BIGINT` primary key and nullable
`created_at`/`updated_at` timestamps to each application table. It also declares
unique slugs for categories, courses, posts, announcements and pages; unique
application numbers; and the indexes declared in the migration.

The Milestone 5 migration adds `courses.publication_status` (default `draft`)
and `courses.fee_verified` (default false), `posts.category`, and optional
`featured_image` paths to `pages` and `announcements`. It does not create
tables. Existing courses with an allowed availability status and a past
publication date are marked published to preserve their prior public
visibility.

The Milestone 6 migration adds `applications.country` and
`applications.privacy_accepted`, adds `enquiries.privacy_accepted`, and creates
`security_enquiries`. New College enquiry rows use `enquiry_type = 'college'`;
Security Services enquiries never use that table. There is no shared public
submission table or workflow. Existing records, if any, retain the default
unacknowledged privacy value.

## Public-content rules

- Fixed pages (`about`, `college`, `security-services`) are overridden only by
  a matching `pages` row with `status = 'published'`, a non-future
  `published_at`, and non-empty content. Unpublished rows are ignored.
- Courses require `publication_status = 'published'`, a non-future
  `published_at`, and an availability status of `available`, `proposed` or
  `development`. Draft, archived, unknown, undated and future courses are not
  exposed. Course fees are rendered only when `fee_verified` is true.
- Applications can be submitted only for courses matching the public
  publication/date rules and `status = 'available'`. Proposed, development,
  draft, archived, future and unknown courses are rejected server-side.
- News articles and announcements require published state and a non-future
  publication date. Announcement start/end dates further limit visibility.
  Detail routes use the same publication rules as listings.
- Fixed-page editor writes are restricted to `about`, `college` and
  `security-services`; the public template only overrides those known routes
  with published content.
- Queries use CodeIgniter Models and Query Builder predicates; route slugs are
  bound as values. Database text is rendered as escaped plain text, not HTML.
- Featured images are rendered only for existing local `assets/images/` or
  `uploads/` files with an approved image extension.

## Relationship and integrity notes

- `courses.category_id` is intended to identify a row in
  `course_categories`.
- `applications.course_id` is intended to identify a row in `courses`.
- College enquiries are inserted into `enquiries` only with
  `enquiry_type = 'college'`; Security Services submissions are inserted into
  `security_enquiries`.
- The core application migration does not declare foreign-key constraints.
  `audit_logs.user_id` remains an optional, application-level reference to a
  Shield user.
- Status values are stored as strings and validated by the content-management
  controllers. Foreign-key constraints are not declared by the core schema.

Media rows reference generated filenames under `public/uploads/media/`.
Uploaded files are validated before insertion and references from courses,
posts, pages and announcements prevent deletion. Files remain public web
assets, so production web-server rules must prevent execution and be verified
on the deployment host.

## Administrator identity and audit

Administrator roles are Shield groups stored in `auth_groups_users`;
permissions and the role matrix are defined in `app/Config/AuthGroups.php`.
No password hashes or authentication identities are selected for the admin
account list. Account creation, role changes, activation and deactivation are
recorded in `audit_logs`. The mutation and its audit row share a database
transaction.

## Schema changes

Use new CodeIgniter migrations for schema changes. Do not edit an already
applied migration to alter a deployed database. Before production migration,
take a database backup and review the generated changes.
