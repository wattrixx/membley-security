<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<section class="hero" id="home" aria-label="Membley Security Group highlights">
    <div class="hero-carousel"
         data-carousel
         role="group"
         aria-label="Featured content"
         aria-roledescription="carousel">
        <div class="hero-slides">
            <article class="hero-slide is-active"
                     data-slide
                     aria-hidden="false"
                     aria-label="1 of 3">
                <img class="hero-image"
                     src="<?= base_url('assets/images/security-technology.jpg') ?>"
                     alt=""
                     width="1200"
                     height="807"
                     fetchpriority="high"
                     decoding="async">
                <div class="hero-copy">
                    <span class="eyebrow">Membley Security Group</span>
                    <h1>Security and training, <span>from one group.</span></h1>
                    <p>
                        Explore the work of Membley Security Group and its
                        College through clear, current information.
                    </p>
                    <div class="hero-actions">
                        <a class="button button-primary" href="#security-services">Explore security services</a>
                        <a class="button button-ghost" href="#college">Discover the College</a>
                    </div>
                </div>
            </article>

            <article class="hero-slide"
                     data-slide
                     aria-hidden="true"
                     aria-label="2 of 3"
                     inert>
                <img class="hero-image"
                     src="<?= base_url('assets/images/training-campus.jpg') ?>"
                     alt=""
                     width="1200"
                     height="800"
                     loading="lazy"
                     decoding="async">
                <div class="hero-copy">
                    <span class="eyebrow">Membley Security College</span>
                    <h1>Make space for <span>learning.</span></h1>
                    <p>
                        Training information is being prepared. Course titles,
                        schedules and requirements will be published when confirmed.
                    </p>
                    <div class="hero-actions">
                        <a class="button button-primary" href="#courses">Explore course information</a>
                        <a class="button button-ghost" href="#admissions">Admissions updates</a>
                    </div>
                </div>
            </article>

            <article class="hero-slide"
                     data-slide
                     aria-hidden="true"
                     aria-label="3 of 3"
                     inert>
                <img class="hero-image"
                     src="<?= base_url('assets/images/hero-city.jpg') ?>"
                     alt=""
                     width="1600"
                     height="1067"
                     loading="lazy"
                     decoding="async">
                <div class="hero-copy">
                    <span class="eyebrow">Information for visitors</span>
                    <h1>Find the information <span>you need.</span></h1>
                    <p>
                        Learn about the Group, its training and security
                        services, and the updates currently available.
                    </p>
                    <div class="hero-actions">
                        <a class="button button-primary" href="#about">About the Group</a>
                        <a class="button button-ghost" href="#contact">Contact information</a>
                    </div>
                </div>
            </article>
        </div>

        <div class="hero-controls" role="group" aria-label="Carousel controls">
            <div class="carousel-pagination" role="group" aria-label="Choose a slide">
                <button class="carousel-dot"
                        type="button"
                        data-slide-to="0"
                        aria-label="Show slide 1"
                        aria-current="true"></button>
                <button class="carousel-dot"
                        type="button"
                        data-slide-to="1"
                        aria-label="Show slide 2"
                        aria-current="false"></button>
                <button class="carousel-dot"
                        type="button"
                        data-slide-to="2"
                        aria-label="Show slide 3"
                        aria-current="false"></button>
            </div>
            <button class="carousel-control"
                    type="button"
                    data-carousel-previous
                    aria-label="Previous slide">
                <span aria-hidden="true">←</span>
            </button>
            <button class="carousel-control"
                    type="button"
                    data-carousel-next
                    aria-label="Next slide">
                <span aria-hidden="true">→</span>
            </button>
            <button class="carousel-control"
                    type="button"
                    data-carousel-toggle
                    aria-label="Pause carousel">
                Pause
            </button>
        </div>
    </div>
</section>

<section class="section" id="about">
    <div class="container intro-grid">
        <div class="intro-mark">
            <img src="<?= base_url('assets/images/membley-logo.jpeg') ?>"
                 alt="Membley Security Group official emblem"
                 width="180"
                 height="180"
                 loading="lazy"
                 decoding="async">
        </div>
        <div class="intro-copy">
            <span class="eyebrow">About the Group</span>
            <h2>One group bringing security and professional training together.</h2>
            <p>
                Membley Security Group brings together security services and
                professional training. This website is being developed to make
                information about the Group, its College and its work easier to find.
            </p>
            <p>
                Some programme details are still being confirmed. We will
                publish them here when they are ready.
            </p>
        </div>
    </div>
</section>

<section class="section section-paper" id="college">
    <div class="container feature-grid">
        <div class="feature-media">
            <img src="<?= base_url('assets/images/training-campus.jpg') ?>"
                 alt="Learners working together in a library"
                 width="1200"
                 height="800"
                 loading="lazy"
                 decoding="async">
            <span class="feature-media-label">Membley Security College</span>
        </div>
        <div class="feature-copy">
            <span class="eyebrow">College</span>
            <h2>Training information, clearly presented.</h2>
            <p>
                The College section will share confirmed course information,
                admission details and updates for prospective learners.
            </p>
            <div class="placeholder-note">
                <p>
                    Course names, schedules, fees and entry requirements are
                    temporary placeholders until confirmed by Membley.
                </p>
            </div>
            <a class="button button-dark" href="#courses">View course information</a>
        </div>
    </div>
</section>

<section class="section section-dark" id="security-services">
    <div class="container feature-grid is-reversed">
        <div class="feature-media">
            <img src="<?= base_url('assets/images/security-technology.jpg') ?>"
                 alt="Security cameras mounted on a building"
                 width="1200"
                 height="807"
                 loading="lazy"
                 decoding="async">
            <span class="feature-media-label">Security services</span>
        </div>
        <div class="feature-copy">
            <span class="eyebrow">Security Services</span>
            <h2>Information about the Group's security work.</h2>
            <p>
                This section will describe confirmed service areas and how to
                request information, without publishing unverified operational
                details.
            </p>
            <div class="placeholder-note">
                <p>
                    Service descriptions, coverage and availability are
                    awaiting confirmation.
                </p>
            </div>
            <a class="button button-primary" href="#contact">Contact information</a>
        </div>
    </div>
</section>

<section class="section section-paper" id="courses">
    <div class="container">
        <div class="section-heading is-centered">
            <span class="eyebrow">Courses</span>
            <h2>Featured training</h2>
            <p>
                Course listings will appear here once titles and details are
                confirmed.
            </p>
        </div>
        <?php if ($featuredCourses !== []): ?>
            <div class="content-grid">
                <?php foreach ($featuredCourses as $course): ?>
                    <?= view('partials/course_card', ['course' => $course]) ?>
                <?php endforeach; ?>
            </div>
            <a class="button button-dark" href="<?= base_url('college/courses') ?>">View all courses</a>
        <?php else: ?>
            <div class="empty-state">
                <h3>Course information is being prepared</h3>
                <p>No course listings are published until the College confirms them.</p>
                <a class="button button-dark" href="<?= base_url('college/contact') ?>">Ask Admissions</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="section" id="admissions">
    <div class="container">
        <div class="admissions-band">
            <div>
                <span class="eyebrow">Admissions</span>
                <h2>Plan your next step with confirmed information.</h2>
                <p>
                    Admission requirements and application steps are being
                    confirmed and will be added here.
                </p>
            </div>
            <a class="button button-dark" href="#contact">Admissions information</a>
        </div>
    </div>
</section>

<section class="section section-dark" id="why-membley">
    <div class="container">
        <div class="section-heading is-centered">
            <span class="eyebrow">Why Membley</span>
            <h2>Principles named in the official emblem.</h2>
            <p>
                The supplied logo highlights these three words as part of the
                Group's visual identity.
            </p>
        </div>
        <div class="values-strip">
            <div class="value"><strong>Truth</strong><span>Named in the official emblem</span></div>
            <div class="value"><strong>Intelligence</strong><span>Named in the official emblem</span></div>
            <div class="value"><strong>Discretion</strong><span>Named in the official emblem</span></div>
        </div>
    </div>
</section>

<section class="section section-paper" id="news">
    <div class="container">
        <div class="section-heading is-centered">
            <span class="eyebrow">News &amp; Updates</span>
            <h2>Latest from Membley</h2>
            <p>
                Announcements and articles will be published here when
                confirmed content is available.
            </p>
        </div>
        <?php if ($latestPosts !== []): ?>
            <div class="content-grid">
                <?php foreach ($latestPosts as $post): ?>
                    <?= view('partials/post_card', ['post' => $post]) ?>
                <?php endforeach; ?>
            </div>
            <a class="button button-dark" href="<?= base_url('news') ?>">View all updates</a>
        <?php else: ?>
            <div class="empty-state">
                <h3>Updates coming soon</h3>
                <p>There are no published news items or announcements at this time.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="section contact-section" id="contact">
    <div class="container contact-grid">
        <div class="contact-copy">
            <span class="eyebrow">Contact</span>
            <h2>Connect with Membley Security Group.</h2>
            <p>
                Call us or open our location in Google Maps. Enquiry forms and
                additional contact details are not yet available.
            </p>
        </div>
        <div class="contact-card">
            <div class="contact-method">
                <strong>Phone</strong>
                <p><a href="tel:+254704084328">+254 704 084 328</a></p>
            </div>
            <div class="contact-method">
                <strong>Location</strong>
                <p>
                    <a href="https://maps.app.goo.gl/u1fQ5uNFDMz1g3cQ6"
                       target="_blank"
                       rel="noopener noreferrer">Open location in Google Maps</a>
                </p>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
