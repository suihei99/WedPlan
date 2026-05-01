function setupVendorCalendar() {
    const dashboard = document.querySelector('[data-vendor-dashboard]');

    if (!dashboard) {
        return;
    }

    const calendarGrid = dashboard.querySelector('[data-vendor-calendar-grid]');
    const monthLabel = dashboard.querySelector('[data-vendor-calendar-label]');
    const prevButton = dashboard.querySelector('[data-vendor-calendar-prev]');
    const nextButton = dashboard.querySelector('[data-vendor-calendar-next]');

    if (!calendarGrid || !monthLabel || !prevButton || !nextButton) {
        return;
    }

    const bookingDates = new Set(JSON.parse(dashboard.dataset.bookingDates || '[]'));
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
            weekdayCell.className = 'vendor-calendar-weekday';
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
            mutedCell.className = 'vendor-calendar-cell is-muted';
            calendarGrid.appendChild(mutedCell);
        }

        for (let day = 1; day <= lastDay.getDate(); day += 1) {
            const cellDate = new Date(state.year, state.month, day);
            const dateKey = formatDateKey(cellDate);

            const dayCell = document.createElement('div');
            dayCell.className = 'vendor-calendar-cell';
            dayCell.textContent = String(day);

            if (bookingDates.has(dateKey)) {
                dayCell.classList.add('has-booking');
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
}

document.addEventListener('DOMContentLoaded', () => {
    setupVendorCalendar();
});
