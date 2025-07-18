"use strict";

const url = "process/register.php";
let form = document.getElementById("form");
let inputsErr = document.getElementsByClassName("input-error");
let errName = document.getElementById("errName");
let errTel = document.getElementById("errTel");
let errEmail = document.getElementById("errEmail");
let errCedula = document.getElementById("errCedula");
let errPnf = document.getElementById("errPnf");
let errTrayecto = document.getElementById("errTrayecto");
let errPass = document.getElementById("errPass");
let errPassRepeat = document.getElementById("errPassRepeat");

// Loader
const loaderOverlay = document.getElementById('loader-overlay');

function showLoader() {
    if (loaderOverlay) {
        loaderOverlay.classList.add('active');
    }
}

function hideLoader() {
    if (loaderOverlay) {
        loaderOverlay.classList.remove('active');
    }
}


form.addEventListener('submit', e => {
    e.preventDefault();
    showLoader();

    let formData = new FormData(e.target);

    // request
    fetch(url, {
        method: "POST",
        body: formData,
        mode: 'cors'
    })
    .then(data => data.json())
    .then(data =>{
        hideLoader();

        // se limpian las etiquetas de error, para mostras nuevos mensajes
        errName.innerText = ""; 
        errTel.innerText = ""; 
        errEmail.innerText = ""; 
        errCedula.innerText = ""; 
        errPnf.innerText = ""; 
        errTrayecto.innerText = ""; 
        errPass.innerText = ""; 
        errPassRepeat.innerText = ""; 


        if (data.length == 0) window.location.href = "login.php";
        else {
            data.forEach(e => {
                let errorId = e[0];
                let errorMsg = e[1]

                switch (errorId) {
                    case "name": errName.innerText = errorMsg; break;
                    case "tel": errTel.innerText = errorMsg; break;
                    case "email": errEmail.innerText = errorMsg; break;
                    case "cedula": errCedula.innerText = errorMsg; break;
                    case "pnf": errPnf.innerText = errorMsg; break;
                    case "trayecto": errTrayecto.innerText = errorMsg; break;
                    case "password": errPass.innerText = errorMsg; break;
                    case "password-repeat": errPassRepeat.innerText = errorMsg; break;
                    case "main": errMain.innerText = errorMsg; break;
                }

                // enfocar y mostrar el elemento con el error
                for (let i = 0; i < inputsErr.length; i++) {
                    if (inputsErr[i].textContent.length > 0) {
                        // enfocar el elemento de arriba para que halla un especaio entre el error y el top vewport
                        let element = i == 0 ? inputsErr[i].parentElement : inputsErr[i-1].parentElement;

                        element.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        })

                        element.focus();
                        break;
                    }
                }
            });
        }
    })
})


// Referencias a elementos del DOM para el toggle de contraseña
const togglePassword = document.getElementById('togglePassword');
const passwordInput = document.getElementById('password');

const togglePasswordRepeat = document.getElementById('togglePasswordRepeat');
const passwordRepeatInput = document.getElementById('password-repeat');

/**
 * Función genérica para alternar la visibilidad de una contraseña.
 * @param {HTMLElement} inputElement - El elemento <input> de la contraseña.
 * @param {HTMLElement} toggleElement - El elemento <span> que contiene el icono.
 */
function setupPasswordToggle(inputElement, toggleElement) {
    if (inputElement && toggleElement) {
        toggleElement.addEventListener('click', function () {
            // Alternar el tipo de input
            const type = inputElement.getAttribute('type') === 'password' ? 'text' : 'password';
            inputElement.setAttribute('type', type);

            // Alternar el icono de ojo (Font Awesome)
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash'); // El icono de ojo tachado
        });
    }
}

// Configurar los toggles para ambos campos de contraseña
setupPasswordToggle(passwordInput, togglePassword);
setupPasswordToggle(passwordRepeatInput, togglePasswordRepeat);