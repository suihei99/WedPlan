/**
 * Guest Management Page - Search, Filter, and Pagination
 */

// Get DOM elements
const guestPage = document.querySelector('[data-guests-page]');
const guestContainer = document.querySelector('[data-guests-container]');
const guestSearchInput = document.querySelector('[data-guest-search]');
const guestStatusFilter = document.querySelector('[data-guest-status-filter]');
const guestPaginationPrev = document.querySelector('[data-guest-page-prev]');
const guestPaginationNext = document.querySelector('[data-guest-page-next]');
const guestPaginationCurrent = document.querySelector('[data-guest-page-current]');

// Pagination settings
const itemsPerPage = 6;
let currentPage = 1;

// Initialize
if (guestPage) {
    initializeGuestManagement();
}

function initializeGuestManagement() {
    if (guestSearchInput) {
        guestSearchInput.addEventListener('input', debounce(filterAndRenderGuests, 300));
    }

    if (guestStatusFilter) {
        guestStatusFilter.addEventListener('change', filterAndRenderGuests);
    }

    if (guestPaginationPrev) {
        guestPaginationPrev.addEventListener('click', previousPage);
    }

    if (guestPaginationNext) {
        guestPaginationNext.addEventListener('click', nextPage);
    }

    renderGuests();
}

/**
 * Get all guest cards
 */
function getAllGuestCards() {
    return Array.from(document.querySelectorAll('[data-guest-card]') || []);
}

/**
 * Filter guests based on search and status
 */
function filterAndRenderGuests() {
    const searchTerm = (guestSearchInput?.value || '').toLowerCase();
    const statusFilter = guestStatusFilter?.value || 'all';

    const allCards = getAllGuestCards();
    const filteredCards = allCards.filter((card) => {
        const searchText = (card.dataset.searchText || '').toLowerCase();
        const inviteStatus = card.dataset.inviteStatus || '';
        const guestStatus = card.dataset.guestStatus || '';

        // Search filter
        const matchesSearch = searchText.includes(searchTerm);

        // Status filter
        let matchesStatus = true;
        if (statusFilter !== 'all') {
            if (statusFilter === 'invited') {
                matchesStatus = inviteStatus === 'invited';
            } else {
                matchesStatus = guestStatus === statusFilter;
            }
        }

        return matchesSearch && matchesStatus;
    });

    // Update pagination
    currentPage = 1;
    renderFilteredGuests(filteredCards);
}

/**
 * Render filtered guests with pagination
 */
function renderFilteredGuests(filteredCards) {
    const totalPages = Math.ceil(filteredCards.length / itemsPerPage);
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;

    // Hide all cards
    getAllGuestCards().forEach((card) => {
        card.style.display = 'none';
    });

    // Show filtered cards
    filteredCards.slice(startIndex, endIndex).forEach((card) => {
        card.style.display = 'block';
    });

    // Update pagination controls
    if (guestPaginationCurrent) {
        guestPaginationCurrent.textContent = `${currentPage} / ${Math.max(1, totalPages)}`;
    }

    if (guestPaginationPrev) {
        guestPaginationPrev.disabled = currentPage === 1;
    }

    if (guestPaginationNext) {
        guestPaginationNext.disabled = currentPage >= totalPages || totalPages === 0;
    }

    // Store total pages for navigation
    guestPage.dataset.totalPages = Math.max(1, totalPages);
    guestPage.dataset.filteredCount = filteredCards.length;
}

/**
 * Render guests (initial load)
 */
function renderGuests() {
    const allCards = getAllGuestCards();
    const totalPages = Math.ceil(allCards.length / itemsPerPage);
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;

    // Hide all cards
    allCards.forEach((card) => {
        card.style.display = 'none';
    });

    // Show current page cards
    allCards.slice(startIndex, endIndex).forEach((card) => {
        card.style.display = 'block';
    });

    // Update pagination controls
    if (guestPaginationCurrent) {
        guestPaginationCurrent.textContent = `${currentPage} / ${Math.max(1, totalPages)}`;
    }

    if (guestPaginationPrev) {
        guestPaginationPrev.disabled = currentPage === 1;
    }

    if (guestPaginationNext) {
        guestPaginationNext.disabled = currentPage >= totalPages || totalPages === 0;
    }

    guestPage.dataset.totalPages = Math.max(1, totalPages);
}

/**
 * Navigate to previous page
 */
function previousPage() {
    if (currentPage > 1) {
        currentPage--;
        if (guestSearchInput?.value || guestStatusFilter?.value !== 'all') {
            filterAndRenderGuests();
        } else {
            renderGuests();
        }
    }
}

/**
 * Navigate to next page
 */
function nextPage() {
    const totalPages = parseInt(guestPage.dataset.totalPages || 1);
    if (currentPage < totalPages) {
        currentPage++;
        if (guestSearchInput?.value || guestStatusFilter?.value !== 'all') {
            filterAndRenderGuests();
        } else {
            renderGuests();
        }
    }
}

/**
 * Debounce helper function
 */
function debounce(func, delay) {
    let timeoutId;
    return function (...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
}
