# Database Design

The database uses MariaDB/MySQL with UTF-8 support (`utf8mb4`).

## Planned Entities
- Users and authentication groups
- Courses and course categories
- Applications
- Blog posts
- Announcements
- CMS pages
- Media
- General enquiries
- Employer training requests
- Site settings
- Audit logs

## Design Rules
- Use migrations for schema changes.
- Define appropriate foreign keys and indexes.
- Store publication status and timestamps where relevant.
- Separate confidential security enquiries from college applications.
- Do not store passwords in plain text.
- Review data retention requirements before production launch.

The final schema will be documented as each module is implemented.
