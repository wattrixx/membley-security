<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= esc($pageTitle ?? 'Membley Security Group') ?></title>
    <meta name="description"
          content="<?= esc($metaDescription ?? 'Membley Security Group') ?>">
    <meta name="theme-color" content="#101018">

    <link rel="icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>?v=2">
    <link rel="stylesheet" href="<?= base_url('assets/css/site.css') ?>">
    <?php if (!empty($preloadImage)): ?>
        <link rel="preload"
              as="image"
              href="<?= esc(base_url($preloadImage), 'attr') ?>"
              fetchpriority="high">
    <?php endif; ?>
</head>

<body>
<a class="skip-link" href="#main-content">Skip to content</a>

<header class="site-header">
    <div class="container header-inner">
        <a href="<?= base_url('/') ?>" class="brand" aria-label="Membley Security Group home">
            <img class="brand-logo"
                 src="<?= base_url('assets/images/membley-logo.jpeg') ?>"
                 alt=""
                 width="68"
                 height="68">
            <span class="brand-copy">
                <strong>MEMBLEY</strong>
                <span>SECURITY GROUP</span>
            </span>
        </a>

        <button class="menu-toggle"
                id="menuToggle"
                type="button"
                aria-label="Open navigation menu"
                aria-controls="mainNav"
                aria-expanded="false">
            <span class="menu-icon" aria-hidden="true"></span>
        </button>

        <nav class="main-nav" id="mainNav" aria-label="Main navigation">
            <a href="<?= base_url('/') ?>">Home</a>
            <a href="<?= base_url('about') ?>">About the Group</a>
            <a href="<?= base_url('security-services') ?>">Security Services</a>
            <a href="<?= base_url('college') ?>">Training College</a>
            <a href="<?= base_url('transport-logistics') ?>">Transport &amp; Logistics Services</a>
            <a href="<?= base_url('contact') ?>" class="nav-contact">Contact</a>
        </nav>
    </div>
</header>

<main id="main-content">
    <?= $this->renderSection('content') ?>
</main>

<footer class="site-footer">
    <div class="container footer-main">
        <div class="footer-brand-block">
            <a href="<?= base_url('/') ?>" class="brand footer-brand" aria-label="Membley Security Group home">
                <img class="brand-logo"
                     src="<?= base_url('assets/images/membley-logo.jpeg') ?>"
                     alt=""
                     width="68"
                     height="68">
                <span class="brand-copy">
                    <strong>MEMBLEY</strong>
                    <span>SECURITY GROUP</span>
                </span>
            </a>
            <p class="footer-description">
                Security services, professional training and logistics, brought together
                under one group.
            </p>
        </div>

        <div>
            <h2 class="footer-heading">Explore</h2>
            <nav class="footer-links" aria-label="Footer navigation">
                <a href="<?= base_url('about') ?>">About the Group</a>
                <a href="<?= base_url('college') ?>">College</a>
                <a href="<?= base_url('college/courses') ?>">Courses</a>
                <a href="<?= base_url('college#admissions') ?>">Admissions</a>
                <a href="<?= base_url('security-services') ?>">Security Services</a>
                <a href="<?= base_url('transport-logistics') ?>">Transport &amp; Logistics Services</a>
                <a href="<?= base_url('news') ?>">News &amp; Updates</a>
                <a href="<?= base_url('contact') ?>">Contact</a>
            </nav>
        </div>

        <div>
            <h2 class="footer-heading">Contact</h2>
            <p class="footer-note">
                <a href="tel:+254704084328">+254 704 084 328</a><br>
                <a href="https://maps.app.goo.gl/u1fQ5uNFDMz1g3cQ6"
                   target="_blank"
                   rel="noopener noreferrer">Open location in Google Maps</a>
                <br><a href="<?= base_url('privacy') ?>">Privacy Notice</a>
            </p>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container">
            <span>&copy; <?= date('Y') ?> Membley Security Group. All rights reserved.</span>
        </div>
    </div>
</footer>

<script src="<?= base_url('assets/js/site.js') ?>" defer></script>
</body>
</html>
