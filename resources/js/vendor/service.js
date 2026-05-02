document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.querySelector('[data-service-search]');
    const typeFilter = document.querySelector('[data-service-type-filter]');
    const cards = document.querySelectorAll('[data-service-card]');
    const emptyState = document.querySelector('[data-service-empty-state]');

    const updateVisibleCards = function () {
        const query = (searchInput?.value ?? '').trim().toLowerCase();
        const typeValue = (typeFilter?.value ?? '').trim().toLowerCase();
        let visibleCount = 0;

        cards.forEach(function (card) {
            const name = (card.getAttribute('data-service-name') ?? '').toLowerCase();
            const serviceType = (card.getAttribute('data-service-type') ?? '').toLowerCase();
            const matchesQuery = query === '' || name.includes(query) || serviceType.includes(query);
            const matchesType = typeValue === '' || serviceType === typeValue;
            const isVisible = matchesQuery && matchesType;

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

    if (typeFilter) {
        typeFilter.addEventListener('change', updateVisibleCards);
    }

    updateVisibleCards();

    document.querySelectorAll('[data-service-delete]').forEach(function (button) {
        button.addEventListener('submit', function (event) {
            if (!window.confirm('Delete this service? This cannot be undone.')) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll('[data-service-image-input]').forEach(function (input) {
        const previewImage = document.querySelector(input.getAttribute('data-service-preview-target'));
        const defaultSrc = input.getAttribute('data-service-preview-default') || (previewImage ? previewImage.getAttribute('src') : '');

        const renderPreview = function () {
            if (!previewImage) {
                return;
            }

            const file = input.files && input.files.length > 0 ? input.files[0] : null;

            if (!file) {
                if (defaultSrc) {
                    previewImage.src = defaultSrc;
                }

                return;
            }

            const reader = new FileReader();

            reader.onload = function () {
                previewImage.src = String(reader.result);
            };

            reader.readAsDataURL(file);
        };

        input.addEventListener('change', renderPreview);
        renderPreview();
    });
});
