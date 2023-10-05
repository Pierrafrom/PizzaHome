// Selectors constants
const MENU_TOGGLE_SELECTOR = '.menu-toggle';
const HAMBURGER_CHECKBOX_SELECTOR = 'hamburger-menu';
const NAV_SELECTOR = 'nav';
const NAV_LIST_ITEMS_SELECTOR = 'li:not(.menu-toggle, .logo)';
const AVATAR_CONTAINER_SELECTOR = '.avatar-container';
const CONNEXION_CONTAINER_SELECTOR = '.connexion-container';
const LOGIN_BUTTON_ID = 'login-button';
const SIGNUP_BUTTON_ID = 'signup-button';
const LOGOUT_BUTTON_ID = 'logout-button';
const AVATAR_MENU= 'avatar-menu';

// DOM element selection
const menuToggle = document.querySelector(MENU_TOGGLE_SELECTOR);
const hamburgerCheckbox = document.getElementById(HAMBURGER_CHECKBOX_SELECTOR);
const nav = document.querySelector(NAV_SELECTOR);
const navListItems = nav.querySelectorAll(NAV_LIST_ITEMS_SELECTOR);
const avatarContainer = document.querySelector(AVATAR_CONTAINER_SELECTOR);
const connexionContainer = document.querySelector(CONNEXION_CONTAINER_SELECTOR);
const loginButton = document.getElementById(LOGIN_BUTTON_ID);
const signupButton = document.getElementById(SIGNUP_BUTTON_ID);
const logoutButton = document.getElementById(LOGOUT_BUTTON_ID);
const avatarMenu = document.getElementById(AVATAR_MENU);

let isConnected = false; // TODO: Replace with a call to the server to check if the user is connected

// Function to expand the menu
function toggleMenu() {
    nav.style.height = '350px'; // Expand the navigation height
    navListItems.forEach(item => {
        item.style.display = 'flex'; // Display each list item
    });
    if (isConnected) {
        avatarContainer.style.display = 'flex';
        connexionContainer.style.display = 'none';
    } else {
        avatarContainer.style.display = 'none';
        connexionContainer.style.display = 'flex';
    }
}

// Function to hide the menu
function hideMenu() {
    nav.style.height = '60px'; // Collapse the navigation height
    navListItems.forEach(item => {
        item.style.display = 'none'; // Hide each list item
    });
    updateNavigation();
}

// Function to reset the menu based on screen width
function resetMenu() {
    if (window.innerWidth > 768) {
        // If the screen width is greater than 768 pixels (PC view)
        nav.style.height = '60px'; // Collapse the navigation height
        hamburgerCheckbox.checked = false; // Uncheck the hamburger checkbox
        menuToggle.style.display = 'none'; // Hide the menu toggle button
        navListItems.forEach(item => {
            item.style.display = 'flex'; // Display each list item
        });
    } else {
        // If the screen width is 768 pixels or fewer (mobile view)
        menuToggle.style.display = 'flex'; // Display the menu toggle button
        hamburgerCheckbox.checked = false; // Uncheck the hamburger checkbox
        hideMenu(); // Hide the menu
    }
    updateNavigation();
}

// Function to update the navigation based on connection status
function updateNavigation() {
    if (isConnected && (window.innerWidth > 768 || hamburgerCheckbox.checked === true)) { // TODO: Replace with a call to the server to check if the user is connected
        avatarContainer.style.display = 'flex';
        connexionContainer.style.display = 'none';
    } else if (window.innerWidth > 768 || hamburgerCheckbox.checked === true) {
        avatarContainer.style.display = 'none';
        connexionContainer.style.display = 'flex';
    }
}

// Add event listeners
window.addEventListener('resize', resetMenu);
window.addEventListener('load', resetMenu);

hamburgerCheckbox.addEventListener('change', () => {
    if (hamburgerCheckbox.checked) {
        toggleMenu(); // If checkbox is checked, expand the menu
    } else {
        hideMenu(); // If checkbox is unchecked, hide the menu
    }
});

loginButton.addEventListener('click', () => {
    isConnected = true;
    updateNavigation();
});

signupButton.addEventListener('click', () => {
    isConnected = true;
    updateNavigation();
});

logoutButton.addEventListener('click', () => {
    isConnected = false;
    avatarMenu.checked = false;
    updateNavigation();
});

