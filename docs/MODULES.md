# Modules

Status reflects the code currently in the repository.

| Module | Status | Notes |
| --- | --- | --- |
| Public homepage | Implemented | Responsive hero and public featured-course/latest-news highlights. |
| About the Group | Implemented | Approved introduction and two organisation paths; unconfirmed vision, mission and history are identified. |
| College | Implemented | Training overview, approach, employer training and admissions guidance. |
| Courses | Implemented | Database-backed listing and details; available and development states are separated. |
| Admissions | Implemented | Online application form accepts only published courses currently marked available; includes one-time confirmation reference. |
| College enquiries | Implemented | Separate public form and storage from applications; no admin workflow yet. |
| Security services | Implemented | Overview and dedicated Security Services enquiry form; warns against submitting sensitive security details. |
| News and updates | Implemented | Database-backed article listing, publication dates, detail routes and optional local images. |
| Contact | Implemented | General, College and Security Services pages use the verified phone/map and link to separate enquiry forms. |
| Course management | Implemented | Admissions Officers and Super Admins can search, filter, paginate, create, edit, preview and publish courses. Availability (`available`, `proposed`, `development`) is separate from publication state; fees require explicit verification. |
| News and blog management | Implemented | Content Managers and Super Admins can create, edit, preview, schedule and publish articles with category, author label, SEO metadata and optional featured image. |
| Fixed page management | Implemented | Content Managers and Super Admins can edit only the existing About, College and Security Services pages. Content is plain text; system routes and executable markup are not editable. |
| Announcements | Implemented | Content Managers and Super Admins can create, preview, schedule, publish and archive announcements with an optional image. |
| Media library | Implemented | Content Managers and Super Admins can upload JPEG, PNG and WebP files up to 5 MB, update alt text and delete unreferenced media. Course editors can select existing media where supported. |
| Administration authentication | Implemented | Shield session login/logout, CSRF, login rate limiting and role/permission filters. No public registration. |
| Admin dashboard | Implemented | Responsive branded layout and permission-filtered real record counts; future modules are marked unavailable. |
| Administrator accounts | Implemented | Super Admin can list, create, assign one of the four roles, and activate/deactivate accounts. Self-change and last-active-Super-Admin safeguards apply. |
| Public applications and enquiries | Implemented | Separate public workflows for applications, College enquiries and Security Services enquiries; no record-management screens yet. |

The public site does not publish proposed fees, unconfirmed credentials,
internal planning notes or undated/future content. Admin routes for college
applications and security enquiries remain role-protected placeholders
returning HTTP 501; they do not expose or modify submissions. Security Services
Officers have no course, application or CMS content permissions.
