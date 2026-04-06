import './bootstrap';

const syncFixedNavbarSpacer = () => {
    const navbar = document.querySelector('[data-fixed-navbar]');
    const spacer = document.querySelector('[data-navbar-spacer]');

    if (!navbar || !spacer) {
        return;
    }

    spacer.style.height = `${navbar.offsetHeight}px`;
};

window.addEventListener('DOMContentLoaded', syncFixedNavbarSpacer);
window.addEventListener('load', syncFixedNavbarSpacer);
window.addEventListener('resize', syncFixedNavbarSpacer);

setTimeout(syncFixedNavbarSpacer, 150);

const parseJsonData = (rawValue, fallback = {}) => {
    if (!rawValue) {
        return fallback;
    }

    try {
        return JSON.parse(rawValue);
    } catch (error) {
        console.warn('Invalid JSON data attribute:', error);
        return fallback;
    }
};

const initTournamentSystemForm = () => {
    const form = document.querySelector('[data-tournament-system-form]');
    if (!form) {
        return;
    }

    const systemSelector = form.querySelector('[data-system-selector]');
    if (!systemSelector) {
        return;
    }

    const visibilityMap = parseJsonData(form.dataset.systemVisibility, {});
    const requiredMap = parseJsonData(form.dataset.systemRequired, {});
    const settingSections = Array.from(form.querySelectorAll('[data-system-field]'));

    const applyFieldVisibility = () => {
        const systemCode = systemSelector.value || '';
        const visibleFields = Array.isArray(visibilityMap[systemCode]) ? visibilityMap[systemCode] : [];
        const requiredFields = Array.isArray(requiredMap[systemCode]) ? requiredMap[systemCode] : [];

        settingSections.forEach((section) => {
            const fieldName = section.dataset.systemField || '';
            const isVisible = visibleFields.includes(fieldName);
            const isRequired = requiredFields.includes(fieldName);

            section.classList.toggle('hidden', !isVisible);

            const controls = section.querySelectorAll('input, select, textarea');
            controls.forEach((control) => {
                control.disabled = !isVisible;
                control.required = isVisible && isRequired;
            });
        });
    };

    applyFieldVisibility();
    systemSelector.addEventListener('change', applyFieldVisibility);
};

window.addEventListener('DOMContentLoaded', initTournamentSystemForm);
