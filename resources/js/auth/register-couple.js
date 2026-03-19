const toggles = document.querySelectorAll('[data-toggle-password]');

toggles.forEach(function (toggleButton) {
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
            meterFill.style.background = 'linear-gradient(135deg, #f3b2c0, #ea6e89)';
            meterLabel.style.color = '#d14f6e';
        } else if (score === 2) {
            meterFill.classList.add('password-meter-fill-medium');
            meterLabel.classList.add('password-meter-label-medium');
            meterFill.style.background = 'linear-gradient(135deg, #f7c670, #f1a437)';
            meterLabel.style.color = '#cb7f1d';
        } else if (score === 3) {
            meterFill.classList.add('password-meter-fill-good');
            meterLabel.classList.add('password-meter-label-good');
            meterFill.style.background = 'linear-gradient(135deg, #c6dc76, #8faf2d)';
            meterLabel.style.color = '#7a9426';
        } else if (score === 4) {
            meterFill.classList.add('password-meter-fill-strong');
            meterLabel.classList.add('password-meter-label-strong');
            meterFill.style.background = 'linear-gradient(135deg, #7fd4a2, #2ca36a)';
            meterLabel.style.color = '#2d8759';
        } else {
            meterFill.style.background = 'linear-gradient(135deg, #d9c6cc, #b8a2aa)';
            meterLabel.style.color = '#8c6370';
        }

        if (score <= 1) {
            meterLabel.textContent = 'Weak password - add more complexity.';
        } else if (score <= 3) {
            meterLabel.textContent = 'Good progress - almost strong enough.';
        } else {
            meterLabel.textContent = 'Strong password ready.';
        }
    };

    passwordField.addEventListener('input', updatePasswordMeter);
    updatePasswordMeter();
}

const weddingDateInput = document.getElementById('wedding_date');

if (weddingDateInput && !weddingDateInput.min) {
    const today = new Date();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');

    weddingDateInput.min = today.getFullYear() + '-' + month + '-' + day;
}
