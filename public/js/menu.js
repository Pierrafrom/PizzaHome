// selectors constants
const PIZZA_TAB_SELECTOR = 'pizza-tab';
const DRINK_TAB_SELECTOR = 'drink-tab';
const DESSERT_TAB_SELECTOR = 'dessert-tab';
const TABS_SELECTOR = '.tabs li a';

// DOM element selection
PIZZA_TAB = document.getElementById(PIZZA_TAB_SELECTOR);
DRINK_TAB = document.getElementById(DRINK_TAB_SELECTOR);
DESSERT_TAB = document.getElementById(DESSERT_TAB_SELECTOR);
TABS = document.querySelectorAll(TABS_SELECTOR);

TABS.forEach(tab => {
    tab.addEventListener('click', () => {
        TABS.forEach(tab => {
            tab.classList.remove('active-tab');
        });
        tab.classList.add('active-tab');
    });
});

window.addEventListener('load', () => {
    TABS[0].classList.add('active-tab');
});
