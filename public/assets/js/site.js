const menuToggle = document.getElementById('menuToggle');
const mainNav = document.getElementById('mainNav');

if (menuToggle && mainNav) {
    const closeMenu = () => {
        mainNav.classList.remove('is-open');
        menuToggle.setAttribute('aria-expanded', 'false');
        menuToggle.setAttribute('aria-label', 'Open navigation menu');
    };

    menuToggle.addEventListener('click', () => {
        const isOpen = mainNav.classList.toggle('is-open');

        menuToggle.setAttribute('aria-expanded', String(isOpen));
        menuToggle.setAttribute('aria-label', isOpen ? 'Close navigation menu' : 'Open navigation menu');
    });

    mainNav.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', closeMenu);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && mainNav.classList.contains('is-open')) {
            closeMenu();
            menuToggle.focus();
        }
    });
}

const carousel = document.querySelector('[data-carousel]');

if (carousel) {
    const slides = Array.from(carousel.querySelectorAll('[data-slide]'));
    const indicators = Array.from(carousel.querySelectorAll('[data-slide-to]'));
    const previousButton = carousel.querySelector('[data-carousel-previous]');
    const nextButton = carousel.querySelector('[data-carousel-next]');
    const toggleButton = carousel.querySelector('[data-carousel-toggle]');
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let activeIndex = slides.findIndex((slide) => slide.classList.contains('is-active'));
    let timer = null;
    let userPaused = false;

    const showSlide = (index) => {
        activeIndex = (index + slides.length) % slides.length;

        slides.forEach((slide, slideIndex) => {
            const isActive = slideIndex === activeIndex;
            slide.classList.toggle('is-active', isActive);
            slide.setAttribute('aria-hidden', String(!isActive));
            slide.inert = !isActive;
        });

        indicators.forEach((indicator, indicatorIndex) => {
            indicator.setAttribute('aria-current', String(indicatorIndex === activeIndex));
        });
    };

    const stopAutoplay = () => {
        if (timer !== null) {
            window.clearInterval(timer);
            timer = null;
        }
    };

    const startAutoplay = () => {
        if (
            timer !== null
            || userPaused
            || reduceMotion.matches
            || document.hidden
            || carousel.matches(':hover')
            || carousel.contains(document.activeElement)
            || slides.length < 2
        ) {
            return;
        }

        timer = window.setInterval(() => showSlide(activeIndex + 1), 7000);
    };

    const resetAutoplay = () => {
        stopAutoplay();
        startAutoplay();
    };

    previousButton?.addEventListener('click', () => {
        showSlide(activeIndex - 1);
        resetAutoplay();
    });

    nextButton?.addEventListener('click', () => {
        showSlide(activeIndex + 1);
        resetAutoplay();
    });

    indicators.forEach((indicator) => {
        indicator.addEventListener('click', () => {
            showSlide(Number(indicator.dataset.slideTo));
            resetAutoplay();
        });
    });

    toggleButton?.addEventListener('click', () => {
        userPaused = !userPaused;
        toggleButton.textContent = userPaused ? 'Play' : 'Pause';
        toggleButton.setAttribute('aria-label', userPaused ? 'Resume carousel' : 'Pause carousel');

        if (userPaused) {
            stopAutoplay();
        } else {
            startAutoplay();
        }
    });

    carousel.addEventListener('mouseenter', stopAutoplay);
    carousel.addEventListener('mouseleave', startAutoplay);
    carousel.addEventListener('focusin', stopAutoplay);
    carousel.addEventListener('focusout', (event) => {
        if (!carousel.contains(event.relatedTarget)) {
            startAutoplay();
        }
    });

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stopAutoplay();
        } else {
            startAutoplay();
        }
    });

    reduceMotion.addEventListener('change', () => {
        if (reduceMotion.matches) {
            stopAutoplay();
        } else {
            startAutoplay();
        }
    });

    showSlide(activeIndex < 0 ? 0 : activeIndex);
    startAutoplay();
}
