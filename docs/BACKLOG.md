# Backlog

Items remain outstanding unless marked implemented in the current source.

## Public website

- Confirm admissions details and any separate organisation contact
  information.
- Populate course and news tables only with approved, explicitly published
  content; current course and post tables are empty.
- Confirm service scope and approved descriptions before expanding public
  claims. Keep proposed fees and internal planning notes private.
- Review image choices and final branding with the client.
- Confirm Transport & Logistics operational capacity, coverage, contact
  arrangements and approved imagery; stock photos are illustrative.
- Have the organisation approve the public Privacy Notice, including a
  retention schedule and dedicated privacy contact, before production form use.
- Correct the pre-existing homepage and Security Services default page copy
  that says online enquiry forms are unavailable, although those forms exist.

## CMS follow-up

- Test authentication and role restrictions in the target deployment
  environment; configure a verified email sender before enabling password
  reset or other email flows.
- Verify the public media directory's non-executable rules on the cPanel host;
  move uploads outside the document root if Apache protections are unavailable.
- Decide whether to add managed website settings; settings CRUD is not part of
  the completed content modules.
- Consider explicit enquiry assignment and reminders separately; Milestone 7
  records optional follow-up dates on notes but does not assign cases or send
  reminders.
- Configure and verify email sender/recipients before enabling submission
  notifications; notifications are disabled by default.
- Review foreign-key and status-integrity constraints before a future migration.

## Quality and release

- Enable the SQLite3 PHP extension in the normal development/CI runtime and a
  coverage driver for PHPUnit coverage reports.
- Browser-test the Milestone 7 admin queues and detail forms on desktop and
  mobile, and verify workflows and role boundaries with an isolated MariaDB
  test database before release.
- Add focused tests for routes, carousel markup, content rendering and future
  form/CMS behavior.
- Validate the responsive design with the client and supported browsers.
- Confirm production requirements and complete a cPanel deployment rehearsal.
- Decide whether to add a local Bootstrap 5 runtime; no Bootstrap assets or
  dependency were present in the existing frontend.
