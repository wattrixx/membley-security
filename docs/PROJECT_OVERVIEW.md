# Project Overview

## Purpose

Membley Security Group is a CodeIgniter 4 website and planned content
management system for the Group's public information, security services and
professional training.

## Current scope

- A server-rendered public homepage at `/`.
- Public About, College, Security Services, Courses, News and Contact pages,
  with dedicated College and Security Services contact routes.
- Database-backed public course and news listings, article details and
  homepage highlights.
- A MariaDB schema for initial content, enquiries and applications.
- Official-logo branding and responsive local CSS and JavaScript.

The supplied website-content document informs the College and Security
Services copy. Proposed fees and internal planning notes are not public.
The About page includes the company background, vision, mission and values
wording supplied for publication.
Public listings use empty states until content is explicitly published. The
site includes separate application and enquiry forms and a role-protected
administration area.

## Technology

- PHP requirement in `composer.json`: `^8.2`; the current development runtime
  was PHP 8.3.6.
- CodeIgniter 4.7.6.
- MariaDB/MySQL through the CodeIgniter MySQLi driver.
- Composer-managed PHP dependencies.
- Server-rendered PHP views, CSS and browser JavaScript.
- Bootstrap 5 is part of the stated project stack, but no Bootstrap runtime
  asset was loaded by the existing public view; this milestone uses local
  responsive CSS and does not add a remote CDN dependency.

## Branding

The supplied `public/assets/images/membley-logo.jpeg` is the official emblem.
The website palette is derived from its near-black background and antique-gold
details: near-black `#101018`, antique gold `#B89860` and light gold `#E2CEA0`.
The emblem is displayed with its original square aspect ratio and is also used
for the multi-resolution site favicon.

The homepage and transport photos are illustrative stock imagery stored locally. They do
not represent confirmed Membley sites, staff, equipment or operations.
The images are sourced from Unsplash and should be replaced if the client
prefers approved, organisation-specific photography.
Transport sources: [container port](https://images.unsplash.com/photo-1494412519320-aa613dfb7738)
and [warehouse](https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d);
see the [Unsplash licence](https://unsplash.com/license).

## Current limitations

Static public pages use view copy unless a matching page row is published
with a publication date. The supplied Bootstrap setup is absent from the
existing frontend; the site continues to use its local stylesheet.
