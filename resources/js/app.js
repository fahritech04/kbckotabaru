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
