document.addEventListener('DOMContentLoaded', function () {
    var button = document.getElementById('button');

    // Appel de la fonction checkInputs lors de la saisie dans les champs
    document.getElementById('email').addEventListener('input', checkInputs);
    document.getElementById('password').addEventListener('input', checkInputs);

    // Fonction pour vérifier les champs d'entrée et activer/désactiver le bouton
    function checkInputs() {
        var emailInput = document.getElementById('email');
        var passwordInput = document.getElementById('password');

        // Activer le bouton si l'un des champs a du texte, sinon le désactiver
        if (emailInput.value.trim() !== '' && passwordInput.value.trim() !== '') {
            button.removeAttribute('disabled');
            button.classList.add('active'); // Ajout de la classe active pour activer le style de survol
        } else {
            button.setAttribute('disabled', 'true');
            button.classList.remove('active'); // Suppression de la classe active pour désactiver le style de survol
        }
    }
});


