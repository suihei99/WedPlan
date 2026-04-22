function setupTaskFilters() {
    const page = document.querySelector('[data-tasks-page]');

    if (!page) {
        return;
    }

    const searchInput = page.querySelector('[data-task-search]');
    const statusFilter = page.querySelector('[data-task-status-filter]');
    const allRows = Array.from(page.querySelectorAll('[data-task-row]'));
    const emptyState = page.querySelector('[data-task-empty]');

    const rowsPerPage = 5;
    let currentPage = 1;
    let filteredRows = [...allRows];

    const prevButton = page.querySelector('[data-task-page-prev]');
    const nextButton = page.querySelector('[data-task-page-next]');
    const currentPageLabel = page.querySelector('[data-task-page-current]');

    const updatePaginationControls = () => {
        const totalPages = Math.max(1, Math.ceil(filteredRows.length / rowsPerPage));

        if (currentPage > totalPages) {
            currentPage = totalPages;
        }

        if (currentPageLabel) {
            currentPageLabel.textContent = String(currentPage);
        }

        if (prevButton) {
            prevButton.disabled = currentPage <= 1;
        }

        if (nextButton) {
            nextButton.disabled = currentPage >= totalPages;
        }
    };

    const renderRows = () => {
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        allRows.forEach((row, index) => {
            const isVisible = filteredRows.includes(row) && index >= 0;
            row.style.display = 'none';

            if (isVisible) {
                const filteredIndex = filteredRows.indexOf(row);
                row.style.display = filteredIndex >= start && filteredIndex < end ? '' : 'none';
            }
        });

        if (emptyState) {
            emptyState.style.display = filteredRows.length ? 'none' : '';
        }

        updatePaginationControls();
    };

    const applyFilters = () => {
        const query = (searchInput?.value || '').trim().toLowerCase();
        const statusValue = statusFilter?.value || 'all';

        filteredRows = allRows.filter((row) => {
            const rowName = row.dataset.taskName || '';
            const rowStatus = row.dataset.taskStatus || 'pending';

            const matchesSearch = !query || rowName.includes(query);
            const matchesStatus = statusValue === 'all' || rowStatus === statusValue;

            return matchesSearch && matchesStatus;
        });

        currentPage = 1;
        renderRows();
    };

    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }

    if (statusFilter) {
        statusFilter.addEventListener('change', applyFilters);
    }

    if (prevButton) {
        prevButton.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage -= 1;
                renderRows();
            }
        });
    }

    if (nextButton) {
        nextButton.addEventListener('click', () => {
            const totalPages = Math.max(1, Math.ceil(filteredRows.length / rowsPerPage));

            if (currentPage < totalPages) {
                currentPage += 1;
                renderRows();
            }
        });
    }

    applyFilters();
}

document.addEventListener('DOMContentLoaded', () => {
    setupTaskFilters();
});
