document.addEventListener('DOMContentLoaded', function () {
    const page = document.querySelector('[data-booking-page]');

    if (!page) {
        return;
    }

    const searchInput = page.querySelector('[data-booking-search]');
    const statusFilter = page.querySelector('[data-booking-status-filter]');
    const cards = page.querySelectorAll('[data-booking-card]');
    const emptyState = page.querySelector('[data-booking-empty-state]');

    const updateVisibleCards = function () {
        const query = (searchInput?.value ?? '').trim().toLowerCase();
        const statusValue = (statusFilter?.value ?? '').trim();
        let visibleCount = 0;

        cards.forEach(function (card) {
            const name = (card.getAttribute('data-booking-name') ?? '').toLowerCase();
            const bookingStatus = (card.getAttribute('data-booking-status') ?? '').trim();
            const matchesQuery = query === '' || name.includes(query);
            const matchesStatus = statusValue === '' || bookingStatus === statusValue;
            const isVisible = matchesQuery && matchesStatus;

            card.hidden = !isVisible;

            if (isVisible) {
                visibleCount += 1;
            }
        });

        if (emptyState) {
            emptyState.hidden = visibleCount !== 0;
        }
    };

    if (searchInput) {
        searchInput.addEventListener('input', updateVisibleCards);
    }

    if (statusFilter) {
        statusFilter.addEventListener('change', updateVisibleCards);
    }

    updateVisibleCards();

    page.querySelectorAll('[data-booking-delete]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!window.confirm('Delete this booking? This cannot be undone.')) {
                event.preventDefault();
            }
        });
    });

    page.querySelectorAll('.booking-field-input').forEach(function (input) {
        input.addEventListener('focus', function () {
            const wrapper = this.closest('.booking-input-wrap');

            if (wrapper) {
                wrapper.classList.add('input-wrap-focus');
            }
        });

        input.addEventListener('blur', function () {
            const wrapper = this.closest('.booking-input-wrap');

            if (wrapper) {
                wrapper.classList.remove('input-wrap-focus');
            }
        });
    });
});