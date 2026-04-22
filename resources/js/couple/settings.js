const settingsPage = document.querySelector('[data-settings-page]');

if (settingsPage) {
    window.requestAnimationFrame(() => {
        settingsPage.classList.add('is-ready');
    });

    const profileToggle = settingsPage.querySelector('[data-profile-toggle]');
    const profilePanel = settingsPage.querySelector('[data-profile-panel]');
    const hasProfileErrors = settingsPage.dataset.profileErrors === '1';

    if (profileToggle && profilePanel && hasProfileErrors) {
        profileToggle.setAttribute('aria-expanded', 'true');
        profilePanel.hidden = false;

        window.requestAnimationFrame(() => {
            const firstInvalidField = profilePanel.querySelector('.is-invalid');

            if (firstInvalidField instanceof HTMLElement) {
                firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalidField.focus();
            }
        });
    }

    if (profileToggle && profilePanel) {
        profileToggle.addEventListener('click', () => {
            const isExpanded = profileToggle.getAttribute('aria-expanded') === 'true';
            profileToggle.setAttribute('aria-expanded', String(!isExpanded));
            profilePanel.hidden = isExpanded;
        });
    }

    const passwordInput = settingsPage.querySelector('[data-password-input]');
    const confirmInput = settingsPage.querySelector('[data-password-confirm]');
    const statusText = settingsPage.querySelector('[data-password-status]');

    const updatePasswordHint = () => {
        if (!passwordInput || !confirmInput || !statusText) {
            return;
        }

        const passwordValue = passwordInput.value;
        const confirmationValue = confirmInput.value;

        if (!passwordValue && !confirmationValue) {
            statusText.textContent = '';
            statusText.classList.remove('is-match', 'is-mismatch');
            return;
        }

        if (passwordValue.length < 8) {
            statusText.textContent = 'Password should be at least 8 characters.';
            statusText.classList.remove('is-match');
            statusText.classList.add('is-mismatch');
            return;
        }

        if (!confirmationValue) {
            statusText.textContent = 'Confirm your password to continue.';
            statusText.classList.remove('is-match', 'is-mismatch');
            return;
        }

        if (passwordValue === confirmationValue) {
            statusText.textContent = 'Passwords match.';
            statusText.classList.remove('is-mismatch');
            statusText.classList.add('is-match');
        } else {
            statusText.textContent = 'Passwords do not match yet.';
            statusText.classList.remove('is-match');
            statusText.classList.add('is-mismatch');
        }
    };

    if (passwordInput && confirmInput && statusText) {
        passwordInput.addEventListener('input', updatePasswordHint);
        confirmInput.addEventListener('input', updatePasswordHint);
    }
}
