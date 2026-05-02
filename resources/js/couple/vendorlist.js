/**
 * Vendor List Page Interactivity
 */

document.addEventListener('DOMContentLoaded', function () {
    const vendorListPage = document.querySelector('[data-vendorlist-page]');
    if (!vendorListPage) {
        return;
    }

    // Search functionality
    const searchForm = document.querySelector('[data-vendorlist-search-form]');
    const searchInput = document.querySelector('[data-vendor-search]');
    const filterForm = document.querySelector('[data-vendorlist-filter-form]');
    const filterSelect = document.querySelector('[data-vendor-type-filter]');

    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (searchForm) {
                    searchForm.submit();
                }
            }, 500); // Debounce search
        });
    }

    if (filterSelect) {
        filterSelect.addEventListener('change', function () {
            if (filterForm) {
                filterForm.submit();
            }
        });
    }
});
