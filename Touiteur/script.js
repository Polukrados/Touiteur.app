document.addEventListener('DOMContentLoaded', (event) => {
    // Sélectionner le menu et le bouton
    const menu = document.querySelector('.menu');

    // Ajouter un écouteur d'événements sur le bouton
    menu.addEventListener('click', function() {
        // Basculer la classe 'active' sur le menu
        this.classList.toggle('active');
    });
});





// Assurez-vous que le DOM est chargé avant d'ajouter des gestionnaires d'événements
document.addEventListener('DOMContentLoaded', function() {
    var followIcon = document.getElementById('follow-icon');
    var isFollowing = false;

    followIcon.addEventListener('click', function(event) {
        event.preventDefault(); // Empêche la navigation

        // Toggle the follow state
        isFollowing = !isFollowing;

        // Change l'icône et la rotation
        if (isFollowing) {
            this.classList.remove('fa-plus');
            this.classList.add('fa-check', 'rotated-icon');
        } else {
            this.classList.remove('fa-check', 'rotated-icon');
            this.classList.add('fa-plus');
        }
    });
});
