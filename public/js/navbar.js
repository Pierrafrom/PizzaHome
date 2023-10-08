document.addEventListener('DOMContentLoaded', function () {
    const navContent = document.getElementById('nav-content');
    const hamburgerIcon = document.getElementById('hamburger-icon');
    const hamburgerCheckBox = document.getElementById('hamburger-checkbox');
    const avatarImage = document.getElementById('avatar-img');
    const userDropdown = document.getElementById('user-dropdown');
    let isUserDropdownVisible = false;

    function toggleHamburgerMenu() {
        if (window.innerWidth < 768) {
            hamburgerIcon.classList.remove('hide');
            hamburgerCheckBox.checked = false;
            navContent.classList.remove('expand', 'show-flex');
            navContent.classList.add('collapse', 'hide');
        } else {
            hamburgerIcon.classList.add('hide');
            hamburgerCheckBox.checked = false;
            navContent.classList.remove('expand', 'hide');
            navContent.classList.add('collapse', 'show-flex');
        }
    }

    toggleHamburgerMenu();

    function toggleMobileMenu() {
        if (hamburgerCheckBox.checked) {
            navContent.classList.remove('collapse', 'hide');
            navContent.classList.add('expand', 'show-flex');
        } else {
            navContent.classList.remove('expand', 'show-flex');
            navContent.classList.add('collapse', 'hide');
        }
    }

    // Toggle user dropdown on avatar click
    if (avatarImage && userDropdown) {
        avatarImage.addEventListener('click', function () {
            if (isUserDropdownVisible) {
                userDropdown.classList.remove('show-flex');
                userDropdown.classList.add('hide');
            } else {
                userDropdown.classList.remove('hide');
                userDropdown.classList.add('show-flex');
            }
            isUserDropdownVisible = !isUserDropdownVisible;
        });
    }

    window.addEventListener('resize', toggleHamburgerMenu);
    hamburgerCheckBox.addEventListener('click', toggleMobileMenu);

    // remove the display none from the nav-content (we add it to prevent the content from flashing on the screen)
    if (navContent && window.innerWidth > 768)
        navContent.classList.remove('hide');
});
