const vendorSidebar = document.querySelector('[data-vendor-sidebar]');
const vendorMenuButton = document.querySelector('[data-vendor-mobile-menu-toggle]');

if (vendorSidebar && vendorMenuButton) {
    vendorMenuButton.addEventListener('click', () => {
        const isOpen = vendorSidebar.classList.toggle('is-open');
        document.body.classList.toggle('vendor-sidebar-open', isOpen);
    });
}
