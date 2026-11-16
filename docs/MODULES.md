# Modules

Status reflects the code currently in the repository.

| Module | Status | Notes |
| --- | --- | --- |
| Public homepage | Implemented | Responsive hero and public featured-course/latest-news highlights. |
| About the Group | Implemented | Introduction, two organisation paths and supplied vision, mission, values and company background. |
| College | Implemented | Training overview, approach, employer training and admissions guidance. |
| Courses | Implemented | Database-backed listing and details; available and development states are separated. |
| Admissions | Implemented | Online application form accepts only published courses currently marked available; includes one-time confirmation reference. |
| College enquiries | Implemented | Separate public form, storage and Admissions management workflow. |
| Security services | Implemented | Overview and dedicated Security Services enquiry form; warns against submitting sensitive security details. |
| News and updates | Implemented | Database-backed article listing, publication dates, detail routes and optional local images. |
| Contact | Implemented | General, College and Security Services pages use the verified phone/map and link to separate enquiry forms. |
| Course management | Implemented | Admissions Officers and Super Admins can search, filter, paginate, create, edit, preview and publish courses. Availability (`available`, `proposed`, `development`) is separate from publication state; fees require explicit verification. |
| News and blog management | Implemented | Content Managers and Super Admins can create, edit, preview, schedule and publish articles with category, author label, SEO metadata and optional featured image. |
| Fixed page management | Implemented | Content Managers and Super Admins can edit only the existing About, College and Security Services pages. Content is plain text; system routes and executable markup are not editable. |
| Announcements | Implemented | Content Managers and Super Admins can create, preview, schedule, publish and archive announcements with an optional image. |
| Media library | Implemented | Content Managers and Super Admins can upload JPEG, PNG and WebP files up to 5 MB, update alt text and delete unreferenced media. Course editors can select existing media where supported. |
| Administration authentication | Implemented | Shield session login/logout, CSRF, login rate limiting and role/permission filters. No public registration. |
| Admin dashboard | Implemented | Responsive branded layout and permission-filtered real record counts. |
| Administrator accounts | Implemented | Super Admin can list, create, assign one of the four roles, and activate/deactivate accounts. Self-change and last-active-Super-Admin safeguards apply. |
| Public applications and enquiries | Implemented | Separate public workflows for applications, College enquiries and Security Services enquiries. |
| Application management | Implemented | Admissions and Super Admins can search, filter, paginate, review, change status and add private notes. |
| College enquiry management | Implemented | Admissions and Super Admins can search, filter, review, change status and record private follow-up notes. |
| Security enquiry management | Implemented | Security Services and Super Admins can search, filter, review, change status and record private follow-up notes. |

The public site does not publish proposed fees, unconfirmed credentials,
internal planning notes or undated/future content. Security Services Officers
have no course, application, College enquiry or CMS content permissions.

### Submission workflows

- Applications: `received` (displayed as Submitted), `under_review`,
  `awaiting_information`, `accepted`, `declined`, `withdrawn`.
- Both enquiry types: `new`, `in_progress`, `awaiting_response`, `resolved`,
  `closed`.
- Any listed status may be changed to any other listed status, including
  reopening a closed case. There is no assignment feature or assigned status.
  Status changes are recorded with their previous and new values.
- Follow-up dates are optional dates attached to individual internal notes;
  they do not create reminders. Notes show their author and creation date and
  cannot be edited or deleted through these interfaces.
- Dashboard application counts are total, `received` (new), and
  `received` plus `under_review` (awaiting review). College enquiries awaiting
  attention are `new`, `in_progress`, or `awaiting_response`; the Security
  Services card counts `new`. Counts are shown only with the corresponding
  view permission.
- Search terms and filters are submitted by POST and retained in the session.
  Remove values and submit the filter again to clear them. Security references
  are internal sequential `SEC-<id>` labels, not public tracking credentials.
