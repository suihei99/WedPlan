const adminSidebar = document.querySelector('[data-admin-sidebar]');
const adminMenuButton = document.querySelector('[data-admin-mobile-menu-toggle]');

if (adminSidebar && adminMenuButton) {
    adminMenuButton.addEventListener('click', () => {
        const isOpen = adminSidebar.classList.toggle('is-open');
        document.body.classList.toggle('admin-sidebar-open', isOpen);
    });

    adminSidebar.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 1024) {
                adminSidebar.classList.remove('is-open');
                document.body.classList.remove('admin-sidebar-open');
            }
        });
    });
}

document.querySelectorAll('[data-admin-search-input]').forEach((input) => {
    const scopeSelector = input.dataset.adminSearchScope;
    const scope = scopeSelector ? document.querySelector(scopeSelector) : null;

    if (!scope) {
        return;
    }

    const searchableItems = Array.from(scope.querySelectorAll('[data-admin-searchable]'));

    input.addEventListener('input', () => {
        const query = input.value.trim().toLowerCase();

        searchableItems.forEach((item) => {
            const searchableText = (item.dataset.adminSearchText || item.textContent || '').toLowerCase();
            item.hidden = query.length > 0 && !searchableText.includes(query);
        });
    });
});