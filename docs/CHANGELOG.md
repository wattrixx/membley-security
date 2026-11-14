# Changelog

## 2026-11-13 — Milestones 1 and 2

- Inspected the existing CodeIgniter application and verified the core MariaDB
  migration and its tables.
- Added canonical project, architecture, database, setup, module, security,
  deployment, testing, changelog and backlog documentation.
- Integrated the supplied official logo and derived a near-black/antique-gold
  palette from it.
- Built a multi-resolution favicon from the official logo.
- Reworked the homepage into a responsive, accessible, multi-slide landing
  page with locally stored image assets and explicit placeholders for
  unconfirmed business information.
- Did not add application routes, CMS functionality, forms or database
  migrations.

## 2026-11-13 — Milestone 3

- Added public About, College, Security Services, Courses, News and Contact
  pages with SEO metadata and shared navigation.
- Added read-only, publication-gated database queries for fixed pages, courses
  and posts; the homepage now shows published featured courses and recent news.
- Added separate College and Security Services contact paths using the
  verified phone number and Google Maps location.
- Kept proposed fees, internal planning notes, unpublished content and
  unconfirmed company statements out of public copy.
- Made no database schema or data changes; the course and post tables remain
  empty and show their published-content empty states.
- Did not implement application/enquiry forms, authentication or CMS features.

## 2026-11-13 — Milestone 4

- Installed CodeIgniter Shield 1.4.1 and CodeIgniter Settings 2.4.0; applied
  their vendor migrations without changing the existing core migration.
- Added session-based admin login/logout, global session CSRF, authentication
  rate limiting and server-side session/permission route protection.
- Defined Super Admin, Content Manager, Admissions Officer and Security
  Services Officer groups with granular permissions.
- Added a responsive, brand-aligned administration dashboard with
  permission-filtered database counts and disabled future-module navigation.
- Added Super Admin account listing, creation, role assignment and
  activation/deactivation with self-change and last-active-Super-Admin
  safeguards. Changes are recorded in the existing audit log.
- Added a CLI-only initial Super Admin provisioning command that requires a
  process environment password and does not provide default credentials.
- Added role-protected HTTP 501 placeholders for future content pages,
  college applications and security enquiries; no later-milestone CRUD or
  forms were implemented.
- Updated the canonical architecture, database, security, setup, deployment,
  module, testing and backlog documentation.

## 2026-11-13 — Milestone 5

- Added one additive migration for course publication/verified-fee fields,
  post categories and optional page/announcement featured images; no tables
  were duplicated.
- Added role-protected course, news, fixed-page, announcement and media
  management with search/filtering, pagination, draft/published/archived
  states, scheduling, and previews where applicable.
- Connected published courses, news, fixed pages and time-valid announcements
  to public pages; output remains escaped plain text.
- Added validated JPEG/PNG/WebP media uploads with generated filenames,
  reference-protected deletion, and audit records.
- Verified role-specific route access in the local browser; full test and
  deployment-host upload checks remain subject to the limitations recorded in
  [Testing](TESTING.md) and [Backlog](BACKLOG.md).

## 2026-11-14 — Milestone 6

- Added public application, College enquiry and Security Services enquiry
  routes and forms with server-side validation, CSRF, honeypots, rate limits
  and session-backed one-time tokens.
- Added an additive migration for application country/privacy acknowledgement
  and a dedicated `security_enquiries` table; College enquiries continue to
  use the existing enquiries table, and applications remain separate.
- Restricted applications to published courses marked available and added
  random application references with session-backed confirmation.
- Added a public Privacy Notice and separate urgent/sensitive-information
  guidance for Security Services enquiries.
- Prepared optional email notifications using environment-backed config;
  notifications remain disabled until verified addresses and mail settings
  are supplied.
- Updated the dashboard's Security Services count to use the dedicated table.
- Public submissions can be stored and confirmed, but administrative review
  and follow-up remain Milestone 7 work.
