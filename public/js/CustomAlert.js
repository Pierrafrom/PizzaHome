/**
 * Class representing a custom alert box.
 * This class creates a custom alert box with a title, message, and a close button.
 * It supports different types of alerts like information, error, and warning.
 */
export class CustomAlert {
    /**
     * Constructs a CustomAlert instance.
     * Initializes the alert box, title, message, and close button elements,
     * and appends them to the document body.
     */
    constructor() {
        // Create the main alert box div and set its class.
        this.alertBox = document.createElement('div');
        this.alertBox.className = 'custom-alert-box';

        // Create and append the title element to the alert box.
        this.alertTitle = document.createElement('h2');
        this.alertBox.appendChild(this.alertTitle);

        // Create and append the message paragraph to the alert box.
        this.alertMessage = document.createElement('p');
        this.alertBox.appendChild(this.alertMessage);

        // Create the close button, set its text, and define its click behavior.
        this.closeButton = document.createElement('button');
        this.closeButton.innerText = 'OK';
        this.closeButton.onclick = () => this.hide();
        this.alertBox.appendChild(this.closeButton);

        // Append the alert box to the document body.
        document.body.appendChild(this.alertBox);
    }

    // Static property defining the types of alerts.
    static Type = {
        INFO: "info",
        ERROR: "error",
        WARNING: "warning"
    };

    /**
     * Displays the alert box with a specified message and type.
     * The title is optional and defaults to a value based on the type if not provided.
     *
     * @param {string} message - The message to display in the alert box.
     * @param {CustomAlert.Type} [type=CustomAlert.Type.INFO] - The type of the alert (INFO, ERROR, WARNING).
     * @param {string} [title=""] - Optional title for the alert box. Defaults based on the type.
     */
    show(message, type = CustomAlert.Type.INFO, title = "") {
        // Set the alert title and message.
        this.alertTitle.innerText = title;
        this.alertMessage.innerText = message;
        // Display the alert box.
        this.alertBox.style.display = 'flex';

        // Apply specific styles and default titles based on the alert type.
        switch(type) {
            case CustomAlert.Type.ERROR:
                this.closeButton.classList.add('btn-error');
                if (title === "") {
                    this.alertTitle.innerText = "Error";
                }
                this.alertTitle.style.color = 'var(--secondary)';
                break;
            case CustomAlert.Type.WARNING:
                this.closeButton.classList.add('btn-warning');
                if (title === "") {
                    this.alertTitle.innerText = "Warning";
                }
                this.alertTitle.style.color = 'var(--warning)';
                break;
            default:
                this.closeButton.classList.add('btn-primary');
                if (title === "") {
                    this.alertTitle.innerText = "Information";
                }
                this.alertTitle.style.color = 'var(--primary)';
                break;
        }
    }

    /**
     * Displays a confirmation dialog with a specified message and title.
     *
     * @param {string} message - The message to display in the confirmation dialog.
     * @param {string} [title="Confirmation"] - Optional title for the confirmation dialog.
     * @param {Function} [onConfirm] - Callback function to execute when the user confirms.
     */
    confirm(message, title = "Confirmation", onConfirm) {
        // Set the alert title and message.
        this.alertTitle.innerText = title;
        this.alertMessage.innerText = message;

        // set to warning style
        this.closeButton.classList.add('btn-warning');

        // Update the confirm button text.
        this.closeButton.innerText = 'Confirm';

        // Show the confirmation dialog.
        this.alertBox.style.display = 'flex';

        // Define the click behavior for the confirm button.
        this.closeButton.onclick = () => {
            if (typeof onConfirm === 'function') {
                onConfirm();
            }
            this.hide();
        };
    }

    /**
     * Hides the alert box.
     */
    hide() {
        // Set the display of the alert box to 'none' to hide it.
        this.alertBox.style.display = 'none';
    }
}
