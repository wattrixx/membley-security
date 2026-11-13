# Backlog

Items remain outstanding unless marked implemented in the current source.

## Public website

- Confirm and publish company history, vision, mission, formal values,
  admissions details and any separate organisation contact information.
- Populate course and news tables only with approved, explicitly published
  content; current course and post tables are empty.
- Confirm service scope and approved descriptions before expanding public
  claims. Keep proposed fees and internal planning notes private.
- Review image choices and final branding with the client.

## CMS foundation

- Test authentication and role restrictions in the target deployment
  environment; configure a verified email sender before enabling password
  reset or other email flows.
- Add controlled CRUD workflows for pages, courses, posts, announcements and
  site settings; public read-only models now exist for pages, courses and posts.
- Implement media upload and management with validated file handling.
- Add enquiry and application workflows, statuses and administrator views.
- Add foreign-key and status-integrity decisions to reviewed migrations.

## Quality and release

- Enable the SQLite3 PHP extension for database tests and a coverage driver
  for PHPUnit coverage reports.
- Add focused tests for routes, carousel markup, content rendering and future
  form/CMS behavior.
- Validate the responsive design with the client and supported browsers.
- Confirm production requirements and complete a cPanel deployment rehearsal.
- Decide whether to add a local Bootstrap 5 runtime; no Bootstrap assets or
  dependency were present in the existing frontend.
