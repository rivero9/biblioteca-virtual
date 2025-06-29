"use strict";

// Las referencias a elementos del DOM ahora pueden ser directas, ya que el script
// se ejecuta después de que el DOM esté completamente parseado (debido a 'defer').
const sidebarLinks = document.querySelectorAll(".sidebar-nav a");
const contentSections = document.querySelectorAll(".content-section");

// --- Funciones de Navegación del Dashboard ---
function updateActiveSection(targetSectionId) {
    sidebarLinks.forEach((item) => item.classList.remove("active"));
    contentSections.forEach((section) => section.classList.remove("active"));

    const targetLink = document.querySelector(`.sidebar-nav a[data-section="${targetSectionId}"]`);
    const targetSection = document.getElementById(targetSectionId);

    if (targetLink) {
        targetLink.classList.add("active");
    }
    if (targetSection) {
        targetSection.classList.add("active");
    }
}

sidebarLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
        if (this.dataset.section) {
            e.preventDefault();
            updateActiveSection(this.dataset.section);
        }
    });
});

updateActiveSection("profile"); // Establecer la sección inicial a 'profile' para las pruebas

// --- Funciones de Loader ---
const loaderOverlay = document.getElementById("loader-overlay");

function showLoader() {
    if (loaderOverlay) {
        loaderOverlay.classList.add("active");
    }
}

function hideLoader() {
    if (loaderOverlay) {
        loaderOverlay.classList.remove("active");
    }
}

// --- Funciones de Manejo de Mensajes ---
const messageModal = document.getElementById('message-modal');
const messageText = document.getElementById('message-text');
const closeModalBtn = document.getElementById('close-modal-btn');

function showUserMessage(type, message) {
    if (messageModal && messageText) {
        messageText.textContent = message;
        messageModal.classList.remove('success', 'error');
        messageModal.classList.add(type);
        messageModal.style.display = 'flex';
        setTimeout(() => {
            messageModal.style.display = 'none';
        }, 5000);
    } else {
        console.log(`Mensaje (${type}): ${message}`);
        alert(message);
    }
}

if (closeModalBtn) {
    closeModalBtn.addEventListener('click', () => {
        if (messageModal) messageModal.style.display = 'none';
    });
}

// --- Referencias a los elementos de error del formulario ---
const formUserData = document.getElementById("user-data");
const errorSpans = {
    name: document.getElementById("errName"),
    tel: document.getElementById("errTel"),
    email: document.getElementById("errEmail"),
    cedula: document.getElementById("errCedula"),
    pnf: document.getElementById("errPnf"),
    trayecto: document.getElementById("errTrayecto")
};

// Función para limpiar todos los mensajes de error del formulario
function clearFormErrors() {
    for (const key in errorSpans) {
        if (errorSpans[key]) {
            errorSpans[key].innerText = "";
        }
    }
    const formInputs = formUserData.querySelectorAll('.form-input');
    formInputs.forEach(input => input.classList.remove('input-invalid'));
}

// Función para mostrar errores específicos en el formulario
function displayFormErrors(errors) {
    clearFormErrors();
    let firstErrorInput = null;

    for (const field in errors) {
        if (errorSpans[field]) {
            errorSpans[field].innerText = errors[field];
            const inputElement = formUserData.querySelector(`[name="${field}"]`); // Usar name en lugar de id
            if (inputElement) {
                inputElement.classList.add('input-invalid');
                if (!firstErrorInput) {
                    firstErrorInput = inputElement;
                }
            }
        }
    }

    if (firstErrorInput) {
        firstErrorInput.scrollIntoView({
            behavior: "smooth",
            block: "center",
        });
        firstErrorInput.focus();
    }
}


// --- ¡NUEVA FUNCIÓN! Actualiza los campos del formulario con los nuevos datos ---
function updateProfileForm(userData) {
    if (!formUserData || !userData) {
        console.error("No se pudo actualizar el formulario: Elementos no encontrados o datos no válidos.");
        return;
    }

    // Actualiza los campos de texto
    const nameInput = formUserData.querySelector('[name="name"]');
    const telInput = formUserData.querySelector('[name="tel"]');
    const emailInput = formUserData.querySelector('[name="email"]');
    const cedulaInput = formUserData.querySelector('[name="cedula"]');
    const pnfSelect = formUserData.querySelector('[name="pnf"]');
    const trayectoSelect = formUserData.querySelector('[name="trayecto"]');

    if (nameInput) nameInput.value = userData.nombre || '';
    if (telInput) telInput.value = userData.telefono || '';
    if (emailInput) emailInput.value = userData.correo || '';
    if (cedulaInput) cedulaInput.value = userData.cedula || '';

    // Para los <select>, hay que establecer la opción seleccionada
    if (pnfSelect && userData.pnf) {
        pnfSelect.value = userData.pnf;
    }
    if (trayectoSelect && userData.trayecto) {
        trayectoSelect.value = userData.trayecto;
    }
}


// --- Event Listener para el Formulario de Actualización ---
if (formUserData) {
    formUserData.addEventListener("submit", (e) => {
        e.preventDefault();
        showLoader();
        clearFormErrors();

        let formData = new FormData(e.target);

        fetch("process/update_user_data.php", {
            method: "POST",
            body: formData,
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(errorData => {
                    throw new Error(errorData.message || 'Error desconocido del servidor', { cause: errorData });
                });
            }
            return response.json();
        })
        .then(data => {
            hideLoader();

            if (data.success) {
                showUserMessage('success', data.message);
                // Si el nombre de usuario se actualizó, reflejarlo en el header/navbar
                const userNameSpan = document.querySelector('.user-profile-name');
                if (userNameSpan && data.user_data && data.user_data.nombre) {
                    userNameSpan.textContent = data.user_data.nombre;
                }

                // --- Llamar a la función para actualizar el formulario con los datos devueltos ---
                if (data.user_data) {
                    updateProfileForm(data.user_data);
                }

            } else {
                showUserMessage('error', data.message || 'Ocurrió un error en la validación de los datos.');
                if (data.errors) {
                    displayFormErrors(data.errors);
                }
            }
        })
        .catch(error => {
            hideLoader();
            console.error('Error en la solicitud Fetch:', error);

            if (error.cause && error.cause.error === 'Authentication Failed') {
                showUserMessage('error', "Sesión inválida: " + error.cause.message + " Redirigiendo al login...");
                if (error.cause.redirect) {
                    setTimeout(() => {
                        window.location.href = error.cause.redirect;
                    }, 2000);
                }
            } else if (error.cause && error.cause.error) {
                showUserMessage('error', "Error del servidor: " + error.cause.message);
            } else {
                showUserMessage('error', "Ocurrió un error de conexión inesperado. Por favor, inténtalo de nuevo.");
            }
        });
    });
}