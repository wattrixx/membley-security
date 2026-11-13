# Database

## Current schema status

The migration `2026-11-11-104518_CreateMembleyCoreTables` is applied in the
configured MariaDB database as batch 1. It creates eleven application tables.
CodeIgniter Shield's authentication migration and CodeIgniter Settings'
migrations are applied in batch 2. The database also has CodeIgniter's
`migrations` table.

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
| `enquiries` | General enquiry contact and message fields with a status. |
| `applications` | Course application contact fields and application status. |
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

## Public-content rules

- Fixed pages (`about`, `college`, `security-services`) are overridden only by
  a matching `pages` row with `status = 'published'`, a non-future
  `published_at`, and non-empty content. Unpublished rows are ignored.
- Courses are visible only for `available`, `proposed` or `development`
  statuses with `published_at` at or before the current time. Drafts, unknown
  statuses and undated/future rows are not exposed. The default `development`
  status alone does not publish a course.
- News articles require `status = 'published'` and a non-future
  `published_at`. Detail routes apply the same filter as listing routes.
- Queries use CodeIgniter Models and Query Builder predicates; route slugs are
  bound as values. Fees are not rendered from course records.
- Featured images are rendered only for existing local `assets/images/` or
  `uploads/` files with an approved image extension.

## Relationship and integrity notes

- `courses.category_id` is intended to identify a row in
  `course_categories`.
- `applications.course_id` is intended to identify a row in `courses`.
- The core application migration does not declare foreign-key constraints.
  `audit_logs.user_id` remains an optional, application-level reference to a
  Shield user.
- Status values are stored as strings; application-level validation and
  allowed-state transitions are not implemented yet.

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
