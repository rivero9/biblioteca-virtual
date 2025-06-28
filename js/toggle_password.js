"use strict";


const flashMessage = document.getElementById('flashMessage');
if (flashMessage) {
    const closeBtn = flashMessage.querySelector('.close-btn');
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            flashMessage.style.display = 'none';
        });
    }
}




// Referencias a elementos del DOM para el toggle de contraseña
const togglePassword = document.getElementById('togglePassword');
const passwordInput = document.getElementById('new_password');

const togglePasswordRepeat = document.getElementById('togglePasswordRepeat');
const passwordRepeatInput = document.getElementById('confirm_password');

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