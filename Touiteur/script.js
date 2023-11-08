document.addEventListener('DOMContentLoaded', (event) => {
    // Sélectionner le menu et le bouton
    const menu = document.querySelector('.menu');

    // Ajouter un écouteur d'événements sur le bouton
    menu.addEventListener('click', function() {
        // Basculer la classe 'active' sur le menu
        this.classList.toggle('active');
    });
});