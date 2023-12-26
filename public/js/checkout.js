document.addEventListener('DOMContentLoaded', function () {

    const form = document.querySelector('.checkout-form');
    form.addEventListener('submit', handleFormSubmit);

    const cardInput = document.getElementById('card-number');
    if (cardInput) {
        cardInput.addEventListener('input', function (event) {
            formatCardNumber(event.target);
        });
    }
});

function formatCardNumber(input) {
    let formattedInput = input.value.split(' ').join(''); // Enlever les espaces
    if (formattedInput.length > 16) {
        formattedInput = formattedInput.substring(0, 16); // Limiter à 16 chiffres
    }

    if (formattedInput.length > 0) {
        formattedInput = formattedInput.match(new RegExp('.{1,4}', 'g')).join(' '); // Regrouper par 4 chiffres
    }

    input.value = formattedInput;
}

function fetchCoordinates(address) {
    return fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                const coords = data[0];
                document.getElementById('latitude').value = coords.lat;
                document.getElementById('longitude').value = coords.lon;
                return true; // Adresse trouvée
            } else {
                return false; // Adresse non trouvée
            }
        })
        .catch(error => {
            console.error('Error:', error);
            return false; // Erreur de récupération des coordonnées
        });
}

function handleFormSubmit(event) {
    event.preventDefault();

    // Récupérer les valeurs des champs d'adresse
    const streetNumber = document.getElementById('street-number').value;
    const street = document.getElementById('street').value;
    const city = document.getElementById('city').value;
    const postalCode = document.getElementById('postal-code').value;

    const fullAddress = `${streetNumber} ${street}, ${city}, ${postalCode}`;

    // Appeler fetchCoordinates et attendre le résultat
    fetchCoordinates(fullAddress).then(isAddressFound => {
        if (isAddressFound) {
            // Si l'adresse est trouvée, soumettre le formulaire
            event.target.submit();
        } else {
            // Sinon, afficher une alerte
            alert('Address not found. Please enter a valid address.');
        }
    });
}