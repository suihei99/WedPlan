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

    const profilePhotoInput = settingsPage.querySelector('[data-profile-photo-input]');
    const profilePhotoPreview = settingsPage.querySelector('[data-profile-photo-preview]');

    if (profilePhotoInput && profilePhotoPreview) {
        profilePhotoInput.addEventListener('change', function () {
            const file = this.files && this.files.length > 0 ? this.files[0] : null;

            if (!file) {
                return;
            }

            const reader = new FileReader();

            reader.onload = function () {
                profilePhotoPreview.src = String(reader.result);
            };

            reader.readAsDataURL(file);
        });
    }

    const businessDocumentInput = settingsPage.querySelector('[data-business-document-input]');

    if (businessDocumentInput) {
        businessDocumentInput.addEventListener('change', function () {
            const file = this.files && this.files.length > 0 ? this.files[0] : null;

            if (!file) {
                return;
            }

            const statusText = settingsPage.querySelector('[data-password-status]');

            if (statusText) {
                statusText.textContent = `Selected document: ${file.name}`;
                statusText.classList.remove('is-match');
            }
        });
    }

    const businessTypeSelect = settingsPage.querySelector('#business_type');
    const businessTypeHelper = settingsPage.querySelector('#businessTypeHelper');

    if (businessTypeSelect && businessTypeHelper) {
        const helperText = {
            Venue: 'Great for halls, garden venues, and event spaces.',
            Catering: 'Highlight menu style and guest capacity in your profile after onboarding.',
            Photography: 'Showcase your portfolio style once your account is approved.',
            'Makeup Artist': 'Perfect for bridal beauty services and package details.',
            'Wedding Planner': 'Ideal for full coordination and timeline support services.',
            'Bridal Wear': 'Add dress collections, fittings, and custom package info.',
            'Decor & Styling': 'Feature themes, styling sets, and visual mood options.',
            Entertainment: 'Useful for bands, DJs, emcees, and live performance services.',
            Transportation: 'Suitable for bridal cars, buses, and guest transfer options.',
            Other: 'Use this if your service does not match the categories above.'
        };

        const renderBusinessTypeHint = () => {
            const selectedValue = businessTypeSelect.value;

            if (!selectedValue || !helperText[selectedValue]) {
                businessTypeHelper.textContent = 'Choose the primary service category couples will see first.';
                return;
            }

            businessTypeHelper.textContent = helperText[selectedValue];
        };

        businessTypeSelect.addEventListener('change', renderBusinessTypeHint);
        renderBusinessTypeHint();
    }

    // Inline PDF viewer modal
    const pdfLinks = settingsPage.querySelectorAll('[data-open-pdf]');
    const pdfModal = document.getElementById('pdf-modal');
    const pdfIframe = pdfModal ? pdfModal.querySelector('[data-pdf-iframe]') : null;
    const pdfCloseTargets = pdfModal ? pdfModal.querySelectorAll('[data-pdf-close]') : [];
    const pdfDownload = pdfModal ? pdfModal.querySelector('[data-pdf-download]') : null;

    const openPdf = (url) => {
        if (!pdfModal || !pdfIframe) return;
        pdfIframe.src = url;
        if (pdfDownload) pdfDownload.href = url;
        pdfModal.setAttribute('aria-hidden', 'false');
        document.documentElement.style.overflow = 'hidden';
    };

    const closePdf = () => {
        if (!pdfModal || !pdfIframe) return;
        pdfModal.setAttribute('aria-hidden', 'true');
        pdfIframe.src = '';
        document.documentElement.style.overflow = '';
    };

    pdfLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            // Only intercept same-origin PDF links; otherwise let browser open new tab
            const url = link.getAttribute('href');
            if (!url) return;
            // Prevent default and open modal
            e.preventDefault();
            openPdf(url);
        });
    });

    if (businessTypeSelect) {
        businessTypeSelect.addEventListener('focus', () => {
            const wrapper = businessTypeSelect.closest('.input-wrap');
            if (wrapper) wrapper.classList.add('input-wrap-focus');
        });

        businessTypeSelect.addEventListener('blur', () => {
            const wrapper = businessTypeSelect.closest('.input-wrap');
            if (wrapper) wrapper.classList.remove('input-wrap-focus');
        });
    }

    pdfCloseTargets.forEach(el => el.addEventListener('click', closePdf));

    // close on Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closePdf();
    });
}