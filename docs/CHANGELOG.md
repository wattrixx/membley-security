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
