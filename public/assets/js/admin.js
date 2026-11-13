(() => {
    const sidebar = document.getElementById('adminSidebar');
    const toggle = document.getElementById('adminMenuToggle');

    if (!sidebar || !toggle) {
        return;
    }

    const closeNavigation = () => {
        sidebar.classList.remove('is-open');
        document.body.classList.remove('admin-nav-open');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-label', 'Open administration navigation');
    };

    toggle.addEventListener('click', () => {
        const isOpen = sidebar.classList.toggle('is-open');
        document.body.classList.toggle('admin-nav-open', isOpen);
        toggle.setAttribute('aria-expanded', String(isOpen));
        toggle.setAttribute(
            'aria-label',
            isOpen ? 'Close administration navigation' : 'Open administration navigation',
        );
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeNavigation();
        }
    });

    document.addEventListener('click', (event) => {
        if (
            document.body.classList.contains('admin-nav-open')
            && !sidebar.contains(event.target)
            && !toggle.contains(event.target)
        ) {
            closeNavigation();
        }
    });
})();
