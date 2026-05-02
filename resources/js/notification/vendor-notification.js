/**
 * Vendor Notification Management
 * Handles search, filtering, delete confirmation, and interactivity
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeNotificationSearch();
    initializeNotificationFilter();
    initializeDeleteConfirmation();
});

/**
 * Initialize search functionality for notifications
 */
function initializeNotificationSearch() {
    const searchInput = document.getElementById('notificationSearch');
    if (!searchInput) return;

    searchInput.addEventListener('input', debounce(function() {
        filterNotifications();
    }, 300));
}

/**
 * Initialize status filter for notifications
 */
function initializeNotificationFilter() {
    const statusFilter = document.getElementById('statusFilter');
    if (!statusFilter) return;

    statusFilter.addEventListener('change', function() {
        filterNotifications();
    });
}

/**
 * Filter notifications based on search term and status
 */
function filterNotifications() {
    const searchTerm = document.getElementById('notificationSearch')?.value.toLowerCase() || '';
    const statusFilter = document.getElementById('statusFilter')?.value || '';
    
    const cards = document.querySelectorAll('.notification-card');
    let visibleCount = 0;

    cards.forEach(card => {
        const title = card.querySelector('.notification-card__title')?.textContent.toLowerCase() || '';
        const message = card.querySelector('.notification-card__message')?.textContent.toLowerCase() || '';
        const readStatus = card.getAttribute('data-read');

        // Check search term match
        const matchesSearch = !searchTerm || title.includes(searchTerm) || message.includes(searchTerm);

        // Check status filter match
        const matchesStatus = !statusFilter || readStatus === statusFilter;

        // Show or hide card
        if (matchesSearch && matchesStatus) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    // Show empty state if no results
    const emptyState = document.querySelector('.notification-empty');
    if (emptyState) {
        if (visibleCount === 0) {
            emptyState.style.display = 'block';
        } else {
            emptyState.style.display = 'none';
        }
    }
}

/**
 * Initialize delete confirmation dialogs
 */
function initializeDeleteConfirmation() {
    const deleteForms = document.querySelectorAll('.delete-form');

    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Are you sure you want to delete this notification?')) {
                e.preventDefault();
            }
        });
    });
}

/**
 * Debounce function for search input
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
