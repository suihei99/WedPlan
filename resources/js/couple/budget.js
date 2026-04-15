function animateProgressBars() {
    document.querySelectorAll('[data-budget-progress]').forEach((bar) => {
        const value = Math.max(0, Math.min(100, Number(bar.dataset.budgetProgress || 0)));
        requestAnimationFrame(() => {
            bar.style.width = `${value}%`;
        });
    });
}

function setupFilters() {
    const buttons = document.querySelectorAll('[data-budget-filter]');
    const cards = document.querySelectorAll('[data-budget-card]');

    if (!buttons.length || !cards.length) {
        return;
    }

    const applyFilter = (filter) => {
        cards.forEach((card) => {
            const status = card.dataset.budgetCardStatus || 'all';
            const shouldShow = filter === 'all' || status === filter;
            card.style.display = shouldShow ? '' : 'none';
        });

        buttons.forEach((button) => {
            button.classList.toggle('is-active', button.dataset.budgetFilter === filter);
        });
    };

    buttons.forEach((button) => {
        button.addEventListener('click', () => {
            applyFilter(button.dataset.budgetFilter || 'all');
        });
    });

    applyFilter('all');
}

document.addEventListener('DOMContentLoaded', () => {
    animateProgressBars();
    setupFilters();
});
