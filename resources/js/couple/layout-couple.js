const mobileMenuButton = document.querySelector('[data-mobile-menu-toggle]');
const sidebar = document.querySelector('[data-couple-sidebar]');

if (mobileMenuButton && sidebar) {
    mobileMenuButton.addEventListener('click', () => {
        sidebar.classList.toggle('is-open');
        document.body.classList.toggle('couple-sidebar-open');
    });

    document.addEventListener('click', (event) => {
        const clickInsideSidebar = sidebar.contains(event.target);
        const clickOnToggle = mobileMenuButton.contains(event.target);
        const isDesktop = window.matchMedia('(min-width: 1025px)').matches;

        if (!isDesktop && !clickInsideSidebar && !clickOnToggle) {
            sidebar.classList.remove('is-open');
            document.body.classList.remove('couple-sidebar-open');
        }
    });

    window.addEventListener('resize', () => {
        const isDesktop = window.matchMedia('(min-width: 1025px)').matches;
        if (isDesktop) {
            sidebar.classList.remove('is-open');
            document.body.classList.remove('couple-sidebar-open');
        }
    });
}
