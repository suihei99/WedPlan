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

function setupReceiptPreview() {
    const triggerSelector = '[data-receipt-preview]';
    const modal = document.querySelector('[data-receipt-modal]');

    if (!modal) {
        return;
    }

    const previewFrame = modal.querySelector('[data-receipt-frame]');
    const previewImage = modal.querySelector('[data-receipt-image]');
    const previewTitle = modal.querySelector('[data-receipt-title]');
    const previewDownload = modal.querySelector('[data-receipt-download]');
    const closeTargets = modal.querySelectorAll('[data-receipt-close]');

    const openModal = (url, label, type) => {
        if (!url) {
            return;
        }

        if (previewTitle) {
            previewTitle.textContent = label || 'Receipt preview';
        }

        if (previewDownload) {
            previewDownload.href = url;
        }

        if (type === 'image') {
            if (previewImage) {
                previewImage.src = url;
                previewImage.hidden = false;
            }

            if (previewFrame) {
                previewFrame.src = '';
                previewFrame.hidden = true;
            }
        } else {
            if (previewFrame) {
                previewFrame.src = url;
                previewFrame.hidden = false;
            }

            if (previewImage) {
                previewImage.src = '';
                previewImage.hidden = true;
            }
        }

        modal.setAttribute('aria-hidden', 'false');
        document.documentElement.style.overflow = 'hidden';
    };

    const closeModal = () => {
        modal.setAttribute('aria-hidden', 'true');

        if (previewFrame) {
            previewFrame.src = '';
        }

        if (previewImage) {
            previewImage.src = '';
        }

        document.documentElement.style.overflow = '';
    };

    document.querySelectorAll(triggerSelector).forEach((trigger) => {
        trigger.addEventListener('click', (event) => {
            const url = trigger.getAttribute('data-receipt-url');

            if (!url) {
                return;
            }

            event.preventDefault();
            openModal(url, trigger.getAttribute('data-receipt-label'), trigger.getAttribute('data-receipt-type'));
        });
    });

    closeTargets.forEach((target) => {
        target.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeModal();
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    animateProgressBars();
    setupFilters();
    setupReceiptPreview();
});
