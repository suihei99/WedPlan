const mobileMenuButton = document.querySelector('[data-mobile-menu-toggle]');
const sidebar = document.querySelector('[data-couple-sidebar]');

if (mobileMenuButton && sidebar) {
    mobileMenuButton.addEventListener('click', () => {
        sidebar.classList.toggle('is-open');
        document.body.classList.toggle('couple-sidebar-open');
    });

    document.addEventListener('click', (event) => {
        const clickInsideSidebar = sidebar.contains(event.target);
        const clickOnToggle = mobileMenuButton.contains(event.target);
        const isDesktop = window.matchMedia('(min-width: 1025px)').matches;

        if (!isDesktop && !clickInsideSidebar && !clickOnToggle) {
            sidebar.classList.remove('is-open');
            document.body.classList.remove('couple-sidebar-open');
        }
    });

    window.addEventListener('resize', () => {
        const isDesktop = window.matchMedia('(min-width: 1025px)').matches;
        if (isDesktop) {
            sidebar.classList.remove('is-open');
            document.body.classList.remove('couple-sidebar-open');
        }
    });
}

// Notification dropdown toggle
const notificationContainer = document.querySelector('[data-notification-container]');
const notificationToggle = document.querySelector('[data-notification-toggle]');
const notificationDropdownWrapper = document.querySelector('[data-notification-dropdown-wrapper]');

if (notificationToggle && notificationDropdownWrapper) {
    notificationToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        const isVisible = notificationDropdownWrapper.style.display !== 'none';
        notificationDropdownWrapper.style.display = isVisible ? 'none' : 'block';
    });

    document.addEventListener('click', (event) => {
        if (!notificationContainer.contains(event.target)) {
            notificationDropdownWrapper.style.display = 'none';
        }
    });
}

// Mark notification as read
const closeButtons = document.querySelectorAll('[data-close-notification]');
closeButtons.forEach((button) => {
    button.addEventListener('click', async (e) => {
        e.preventDefault();
        const notificationId = button.getAttribute('data-notification-id');
        const notificationItem = document.querySelector(`[data-notification-id="${notificationId}"]`);

        try {
            const response = await fetch(`/couple/notifications/${notificationId}/read`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
            });

            if (response.ok) {
                notificationItem.classList.remove('unread');
                button.remove();

                // Update unread count
                const badge = document.querySelector('[data-unread-count]');
                if (badge) {
                    const count = parseInt(badge.textContent) - 1;
                    if (count > 0) {
                        badge.textContent = count;
                    } else {
                        badge.remove();
                    }
                }
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    });
});

// Mark all as read
const markAllReadButton = document.querySelector('[data-mark-all-read]');
if (markAllReadButton) {
    markAllReadButton.addEventListener('click', async (e) => {
        e.preventDefault();

        try {
            const notificationItems = document.querySelectorAll('[data-notification-unread="true"]');
            const ids = Array.from(notificationItems).map((item) => item.getAttribute('data-notification-id'));

            // Mark each notification as read
            for (const id of ids) {
                await fetch(`/couple/notifications/${id}/read`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                });
            }

            // Reload notifications
            location.reload();
        } catch (error) {
            console.error('Error marking all notifications as read:', error);
        }
    });
}

