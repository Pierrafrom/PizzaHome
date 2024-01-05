// Wait for the HTML to be fully loaded
document.addEventListener("DOMContentLoaded", function () {
  // Cache DOM elements to avoid re-querying the DOM
  const navContent = document.getElementById("nav-content");
  const hamburgerIcon = document.getElementById("hamburger-icon");
  const hamburgerCheckBox = document.getElementById("hamburger-checkbox");
  const avatarImage = document.getElementById("avatar-img");
  const userDropdown = document.getElementById("user-dropdown");

  /**
   * Toggles visibility of a DOM element.
   *
   * @param {HTMLElement} element - The element whose visibility is to be toggled.
   */
  function toggleVisibility(element) {
    element.classList.toggle("hide"); // Toggle hide class
    element.classList.toggle("show-flex"); // Toggle show-flex class
  }

  /**
   * Toggle Hamburger Menu based on window width.
   * Show/Hide hamburger icon and adjust navContent classes accordingly.
   */
  function toggleHamburgerMenu() {
    const isMobileView = window.innerWidth < 768; // Check for mobile view
    // Toggle visibility of hamburgerIcon based on view
    hamburgerIcon.classList.toggle("hide", !isMobileView);
    // Toggle classes for navContent based on view
    navContent.classList.toggle("hide", isMobileView);
    navContent.classList.toggle("show-flex", !isMobileView);
    // Ensure the checkbox is not checked
    hamburgerCheckBox.checked = false;
  }

  /**
   * Toggle Mobile Menu visibility upon hamburgerCheckbox state change.
   * Utilizes the toggleVisibility function to perform the actual class toggling.
   */
  function toggleMobileMenu() {
    toggleVisibility(navContent);
  }

  // Initialize UI elements upon page load
  toggleHamburgerMenu();

  // Event Listeners

  // If avatarImage and userDropdown are available in DOM, toggle userDropdown on avatarImage click
  if (avatarImage && userDropdown) {
    avatarImage.addEventListener("click", () => toggleVisibility(userDropdown));
  }

  // Re-check window width and adjust UI on window resize
  window.addEventListener("resize", toggleHamburgerMenu);
  // Toggle mobile menu on checkbox state change
  hamburgerCheckBox.addEventListener("click", toggleMobileMenu);

  // Ensure navContent is not hidden for non-mobile views upon page load
  if (navContent && window.innerWidth > 768)
    navContent.classList.remove("hide");
});
