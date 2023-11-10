//titre animé
const text = 'Touiteur';

const createLetterArray = string => {
    return string.split('');
};

const createLetterLayers = array => {
    return array.map(letter => {
        let layer = '';
        for (let i = 1; i <= 2; i++) {
            if (letter === ' ') {
                layer += '<span class="space"></span>';
            } else {
                layer += '<span class="letter-' + i + '">' + letter + '</span>';
            }
        }
        return layer;
    });
};

const createLetterContainers = array => {
    return array.map(item => {
        let container = '';
        container += '<div class="wrapper">' + item + '</div>';
        return container;
    });
};

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('touiteur').innerHTML = createLetterContainers(createLetterLayers(createLetterArray(text))).join('');

    const spans = Array.from(document.getElementsByTagName('span'));
    spans.forEach(span => {
        setTimeout(() => {
            span.parentElement.style.width = span.offsetWidth + 'px';
            span.parentElement.style.height = span.offsetHeight + 'px';
        }, 250);
    });

    let time = 250;
    spans.forEach(span => {
        time += 75;
        setTimeout(() => {
            span.parentElement.style.top = '0px';
        }, time);
    });
});




(function() {
    document.addEventListener('DOMContentLoaded', function() {
        const boutonListeFollowers = document.querySelector(".modal-open");
        const modal = document.getElementById("followersModal");
        const closeModal = document.querySelector(".close-modal");

        boutonListeFollowers.addEventListener("click", function() {
            modal.classList.add("active");
        });

        closeModal.addEventListener("click", function() {
            modal.classList.remove("active");
        });

        window.addEventListener("click", function(event) {
            if (event.target === modal) {
                modal.classList.remove("active");
            }
        });
    });
})();





//afficher le menu profil
document.addEventListener('DOMContentLoaded', (event) => {
    const menu = document.querySelector('.menu');
    menu.addEventListener('click', function() {
        this.classList.toggle('active');
    });
});



function togglePasswordVisibility() {
    var passwordInput = document.getElementById('password');
    var togglePasswordIcon = document.querySelector('.toggle-password');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        togglePasswordIcon.classList.add('fa-eye');
        togglePasswordIcon.classList.remove('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        togglePasswordIcon.classList.remove('fa-eye');
        togglePasswordIcon.classList.add('fa-eye-slash');
    }
}



