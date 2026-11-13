# Modules

Status reflects the code currently in the repository.

| Module | Status | Notes |
| --- | --- | --- |
| Public homepage | Implemented | Responsive hero and public featured-course/latest-news highlights. |
| About the Group | Implemented | Approved introduction and two organisation paths; unconfirmed vision, mission and history are identified. |
| College | Implemented | Training overview, approach, employer training and admissions guidance. |
| Courses | Implemented | Database-backed listing and details; available and development states are separated. |
| Admissions | College section | Intake guidance and contact link; application form is not implemented. |
| Security services | Implemented | Overview and service enquiry paths, with scope confirmed per engagement. |
| News and updates | Implemented | Database-backed article listing, publication dates, detail routes and optional local images. |
| Contact | Implemented | General, College and Security Services pages use the verified phone and map; no enquiry forms. |
| Content schema | Read-only public queries | Existing `pages`, `courses` and `posts` tables are queried; no CRUD controllers. |
| Administration authentication | Implemented | Shield session login/logout, CSRF, login rate limiting and role/permission filters. No public registration. |
| Admin dashboard | Implemented | Responsive branded layout and permission-filtered real record counts; future modules are marked unavailable. |
| Administrator accounts | Implemented | Super Admin can list, create, assign one of the four roles, and activate/deactivate accounts. Self-change and last-active-Super-Admin safeguards apply. |
| CMS content management | Not implemented | Page/news/announcement CRUD routes and workflows are not present. |
| Media management | Schema only | No upload endpoint or media browser. |
| Enquiries and applications | Schema only | No public submission forms or admin workflows. |

The public site does not publish proposed fees, unconfirmed credentials,
internal planning notes or undated/future content. Admin routes for pages,
applications and security enquiries are role-protected placeholders returning
HTTP 501; they do not expose or modify records.
