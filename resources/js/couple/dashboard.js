const countdownPanel = document.querySelector('.countdown-panel');

function parseWeddingDate(dateString) {
    const parsed = new Date(dateString);
    return Number.isNaN(parsed.getTime()) ? null : parsed;
}

function setCountdownValue(id, value) {
    const element = document.getElementById(id);
    if (!element) {
        return;
    }

    const next = String(value).padStart(2, '0');
    if (element.textContent !== next) {
        element.textContent = next;
        element.classList.add('is-ticking');
        window.setTimeout(() => {
            element.classList.remove('is-ticking');
        }, 240);
    }
}

function runCountdown() {
    if (!countdownPanel) {
        return;
    }

    const weddingDateText = countdownPanel.getAttribute('data-wedding-date');
    const targetDate = parseWeddingDate(weddingDateText || '');

    if (!targetDate) {
        return;
    }

    const tick = () => {
        const now = new Date();
        const diff = targetDate.getTime() - now.getTime();

        if (diff <= 0) {
            setCountdownValue('days', 0);
            setCountdownValue('hours', 0);
            setCountdownValue('minutes', 0);
            setCountdownValue('seconds', 0);
            return;
        }

        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
        const minutes = Math.floor((diff / (1000 * 60)) % 60);
        const seconds = Math.floor((diff / 1000) % 60);

        setCountdownValue('days', days);
        setCountdownValue('hours', hours);
        setCountdownValue('minutes', minutes);
        setCountdownValue('seconds', seconds);
    };

    tick();
    window.setInterval(tick, 1000);
}

function animateMeters() {
    const meters = document.querySelectorAll('[data-meter-value]');
    meters.forEach((meter) => {
        const rawValue = Number(meter.getAttribute('data-meter-value') || 0);
        const bounded = Math.max(0, Math.min(100, rawValue));

        requestAnimationFrame(() => {
            meter.style.width = `${bounded}%`;
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    runCountdown();
    animateMeters();
});
