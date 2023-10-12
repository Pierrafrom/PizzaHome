document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('.tab');
    const tabContents = document.querySelectorAll('.tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // annuler le comportement du lien par défaut
            event.preventDefault();
            // Masquer tous les contenus de tab
            tabContents.forEach(content => {
                content.classList.remove('show-flex');
                content.classList.add('hide');
            });

            // Afficher le contenu du tab correspondant
            const targetId = tab.getAttribute('href').substring(1);
            const targetContent = document.getElementById(targetId);
            targetContent.classList.remove('hide');
            targetContent.classList.add('show-flex');

            // Mettre à jour la classe active-tab
            tabs.forEach(t => t.classList.remove('active-tab'));
            tab.classList.add('active-tab');
        });
    });
});





