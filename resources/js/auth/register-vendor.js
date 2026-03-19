const toggleButtons = document.querySelectorAll('[data-toggle-password]');

toggleButtons.forEach(function (toggleButton) {
    toggleButton.addEventListener('click', function () {
        const targetId = toggleButton.getAttribute('data-target');
        const targetInput = document.getElementById(targetId);

        if (!targetInput) {
            return;
        }

        const showIcon = toggleButton.querySelector('[data-eye-show]');
        const hideIcon = toggleButton.querySelector('[data-eye-hide]');
        const isHidden = targetInput.type === 'password';

        targetInput.type = isHidden ? 'text' : 'password';

        if (showIcon && hideIcon) {
            showIcon.style.display = isHidden ? 'none' : '';
            hideIcon.style.display = isHidden ? '' : 'none';
        }
    });
});

document.querySelectorAll('.field-input').forEach(function (input) {
    input.addEventListener('focus', function () {
        const wrapper = this.closest('.input-wrap');

        if (wrapper) {
            wrapper.classList.add('input-wrap-focus');
        }
    });

    input.addEventListener('blur', function () {
        const wrapper = this.closest('.input-wrap');

        if (wrapper) {
            wrapper.classList.remove('input-wrap-focus');
        }
    });
});

const passwordField = document.getElementById('password');
const meterFill = document.getElementById('passwordMeterFill');
const meterLabel = document.getElementById('passwordMeterLabel');

if (passwordField && meterFill && meterLabel) {
    const updatePasswordMeter = function () {
        const value = passwordField.value;
        let score = 0;

        if (value.length >= 8) {
            score += 1;
        }

        if (/[A-Z]/.test(value) && /[a-z]/.test(value)) {
            score += 1;
        }

        if (/\d/.test(value)) {
            score += 1;
        }

        if (/[^A-Za-z0-9]/.test(value)) {
            score += 1;
        }

        const percentageMap = [0, 25, 50, 75, 100];
        meterFill.style.width = percentageMap[score] + '%';
        meterFill.classList.remove('password-meter-fill-weak', 'password-meter-fill-medium', 'password-meter-fill-good', 'password-meter-fill-strong');
        meterLabel.classList.remove('password-meter-label-weak', 'password-meter-label-medium', 'password-meter-label-good', 'password-meter-label-strong');

        if (score <= 1 && value.length > 0) {
            meterFill.classList.add('password-meter-fill-weak');
            meterLabel.classList.add('password-meter-label-weak');
            meterLabel.textContent = 'Weak password - add more complexity.';
            return;
        }

        if (score === 2) {
            meterFill.classList.add('password-meter-fill-medium');
            meterLabel.classList.add('password-meter-label-medium');
            meterLabel.textContent = 'Good start - add uppercase, symbols, or numbers.';
            return;
        }

        if (score === 3) {
            meterFill.classList.add('password-meter-fill-good');
            meterLabel.classList.add('password-meter-label-good');
            meterLabel.textContent = 'Strong progress - one more improvement for best strength.';
            return;
        }

        if (score === 4) {
            meterFill.classList.add('password-meter-fill-strong');
            meterLabel.classList.add('password-meter-label-strong');
            meterLabel.textContent = 'Strong password ready.';
            return;
        }

        meterLabel.textContent = 'Use at least 8 characters with letters and numbers.';
    };

    passwordField.addEventListener('input', updatePasswordMeter);
    updatePasswordMeter();
}

const documentInput = document.getElementById('business_documents');
const documentLabel = document.getElementById('businessDocumentLabel');

if (documentInput && documentLabel) {
    const renderDocumentName = function () {
        if (!documentInput.files || documentInput.files.length === 0) {
            documentLabel.textContent = 'No file chosen';
            return;
        }

        documentLabel.textContent = documentInput.files[0].name;
    };

    documentInput.addEventListener('change', renderDocumentName);
    renderDocumentName();
}

const businessTypeSelect = document.getElementById('business_type');
const businessTypeHelper = document.getElementById('businessTypeHelper');

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

    const renderBusinessTypeHint = function () {
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
