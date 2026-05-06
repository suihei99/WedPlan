/**
 * Vendor List Page Interactivity
 */

document.addEventListener('DOMContentLoaded', function () {
    const setupVendorListInteractions = () => {
        const vendorListPage = document.querySelector('[data-vendorlist-page]');

        if (!vendorListPage) {
            return;
        }

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
                }, 500);
            });
        }

        if (filterSelect) {
            filterSelect.addEventListener('change', function () {
                if (filterForm) {
                    filterForm.submit();
                }
            });
        }
    };

    const setupVendorBookingCalendar = () => {
        const bookingCalendar = document.querySelector('[data-vendorlist-detail-page]');

        if (!bookingCalendar) {
            return;
        }

        const calendarGrid = bookingCalendar.querySelector('[data-vendor-booking-grid]');
        const monthLabel = bookingCalendar.querySelector('[data-vendor-booking-label]');
        const prevButton = bookingCalendar.querySelector('[data-vendor-booking-prev]');
        const nextButton = bookingCalendar.querySelector('[data-vendor-booking-next]');

        if (!calendarGrid || !monthLabel || !prevButton || !nextButton) {
            return;
        }

        const bookingDates = new Set(JSON.parse(bookingCalendar.dataset.bookingDates || '[]'));
        const weekdayLabels = ['M', 'T', 'W', 'T', 'F', 'S', 'S'];

        const state = {
            month: new Date().getMonth(),
            year: new Date().getFullYear(),
        };

        const formatDateKey = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };

        const render = () => {
            calendarGrid.innerHTML = '';

            weekdayLabels.forEach((label) => {
                const weekdayCell = document.createElement('div');
                weekdayCell.className = 'vendorlist-booking-weekday';
                weekdayCell.textContent = label;
                calendarGrid.appendChild(weekdayCell);
            });

            const firstDay = new Date(state.year, state.month, 1);
            const lastDay = new Date(state.year, state.month + 1, 0);
            const firstWeekday = (firstDay.getDay() + 6) % 7;

            monthLabel.textContent = firstDay.toLocaleDateString(undefined, {
                month: 'long',
                year: 'numeric',
            });

            for (let index = 0; index < firstWeekday; index += 1) {
                const mutedCell = document.createElement('div');
                mutedCell.className = 'vendorlist-booking-cell is-muted';
                calendarGrid.appendChild(mutedCell);
            }

            for (let day = 1; day <= lastDay.getDate(); day += 1) {
                const cellDate = new Date(state.year, state.month, day);
                const dateKey = formatDateKey(cellDate);

                const dayCell = document.createElement('div');
                dayCell.className = 'vendorlist-booking-cell';

                const dayNumber = document.createElement('span');
                dayNumber.textContent = String(day);
                dayCell.appendChild(dayNumber);

                if (bookingDates.has(dateKey)) {
                    dayCell.classList.add('is-booked');
                    dayCell.title = 'Booked';

                    const bookedLabel = document.createElement('span');
                    bookedLabel.className = 'vendorlist-booking-cell-label';
                    bookedLabel.textContent = 'Booked';
                    dayCell.appendChild(bookedLabel);
                }

                calendarGrid.appendChild(dayCell);
            }
        };

        prevButton.addEventListener('click', () => {
            state.month -= 1;

            if (state.month < 0) {
                state.month = 11;
                state.year -= 1;
            }

            render();
        });

        nextButton.addEventListener('click', () => {
            state.month += 1;

            if (state.month > 11) {
                state.month = 0;
                state.year += 1;
            }

            render();
        });

        render();
    };

    setupVendorListInteractions();
    setupVendorBookingCalendar();
});
