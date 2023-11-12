/**
 * Event listener for the DOMContentLoaded event.
 * It ensures that the script runs after the initial HTML document has been completely loaded and parsed.
 */
document.addEventListener('DOMContentLoaded', function () {

    // Select all tab elements and tab content elements
    const tabs = document.querySelectorAll('.tab');
    const tabContents = document.querySelectorAll('.tab-content');

    /**
     * Toggle a CSS class on multiple elements.
     *
     * @param {NodeList} elements - The elements on which to toggle the class.
     * @param {string} className - The name of the class to toggle.
     * @param {boolean} [add=true] - Whether to add or remove the class; true adds it, false removes it.
     */
    const toggleClassOnElements = (elements, className, add = true) => {
        elements.forEach(el => add ? el.classList.add(className) : el.classList.remove(className));
    };

    /**
     * Event listener for the click event on the body element.
     * Enables the tab functionality: shows the clicked tab's content and hides others.
     */
    document.body.addEventListener('click', (event) => {

        // Check if the clicked element is a tab
        if (event.target.matches('.tab')) {
            event.preventDefault();

            // Hide all tab content elements
            toggleClassOnElements(tabContents, 'hide', true);
            toggleClassOnElements(tabContents, 'show-flex', false);

            // Show content for the clicked tab
            const targetId = event.target.getAttribute('href').substring(1);
            const targetContent = document.getElementById(targetId);
            targetContent.classList.remove('hide');
            targetContent.classList.add('show-flex');

            // Update the active tab
            toggleClassOnElements(tabs, 'active-tab', false);
            event.target.classList.add('active-tab');
        }
    });
});