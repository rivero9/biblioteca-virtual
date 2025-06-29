"use strict";

const sidebarLinks = document.querySelectorAll(".sidebar-nav a");
const contentSections = document.querySelectorAll(".content-section");

// --- Funciones de Navegación del Dashboard ---
function updateActiveSection(targetSectionId) {
  sidebarLinks.forEach((item) => item.classList.remove("active"));
  contentSections.forEach((section) => section.classList.remove("active"));

  const targetLink = document.querySelector(
    `.sidebar-nav a[data-section="${targetSectionId}"]`
  );
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
const messageModal = document.getElementById("message-modal");
const messageText = document.getElementById("message-text");
const closeModalBtn = document.getElementById("close-modal-btn");

function showUserMessage(type, message) {
  if (messageModal && messageText) {
    messageText.textContent = message;
    messageModal.classList.remove("success", "error");
    messageModal.classList.add(type);
    messageModal.style.display = "flex";
    setTimeout(() => {
      messageModal.style.display = "none";
    }, 5000);
  } else {
    console.log(`Mensaje (${type}): ${message}`);
    alert(message);
  }
}

if (closeModalBtn) {
  closeModalBtn.addEventListener("click", () => {
    if (messageModal) messageModal.style.display = "none";
  });
}

// --- Referencias a los elementos de error del formulario de Datos Personales ---
const formUserData = document.getElementById("user-data");
const userDataErrorSpans = {
  // Renombrado para evitar conflicto
  name: document.getElementById("errName"),
  tel: document.getElementById("errTel"),
  email: document.getElementById("errEmail"),
  cedula: document.getElementById("errCedula"),
  pnf: document.getElementById("errPnf"),
  trayecto: document.getElementById("errTrayecto"),
};

// --- Referencias a los elementos de error del formulario de Contraseña  ---
const formPasswordUpdate = document.getElementById("form-password-update");
const passwordErrorSpans = {
  // Nuevas referencias para errores de contraseña
  current_password: document.getElementById("errCurrentPassword"),
  new_password: document.getElementById("errNewPassword"),
  confirm_password: document.getElementById("errConfirmPassword"),
};

// Función GENERAL para limpiar mensajes de error de CUALQUIER formulario
function clearFormErrors(errorSpansMap, formElement) {
  for (const key in errorSpansMap) {
    if (errorSpansMap[key]) {
      errorSpansMap[key].innerText = "";
    }
  }
  const formInputs = formElement.querySelectorAll(".form-input");
  formInputs.forEach((input) => input.classList.remove("input-invalid"));
}

// Función GENERAL para mostrar errores específicos en CUALQUIER formulario
function displayFormErrors(errors, errorSpansMap, formElement) {
  clearFormErrors(errorSpansMap, formElement); // Limpiar antes de mostrar nuevos
  let firstErrorInput = null;

  for (const field in errors) {
    if (errorSpansMap[field]) {
      errorSpansMap[field].innerText = errors[field];
      const inputElement = formElement.querySelector(`[name="${field}"]`);
      if (inputElement) {
        inputElement.classList.add("input-invalid");
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

// ---  Actualiza los campos del formulario de Datos Personales ---
function updateProfileForm(userData) {
  if (!formUserData || !userData) {
    console.error(
      "No se pudo actualizar el formulario: Elementos no encontrados o datos no válidos."
    );
    return;
  }

  const nameInput = formUserData.querySelector('[name="name"]');
  const telInput = formUserData.querySelector('[name="tel"]');
  const emailInput = formUserData.querySelector('[name="email"]');
  const cedulaInput = formUserData.querySelector('[name="cedula"]');
  const pnfSelect = formUserData.querySelector('[name="pnf"]');
  const trayectoSelect = formUserData.querySelector('[name="trayecto"]');

  if (nameInput) nameInput.value = userData.nombre || "";
  if (telInput) telInput.value = userData.telefono || "";
  if (emailInput) emailInput.value = userData.correo || "";
  if (cedulaInput) cedulaInput.value = userData.cedula || "";

  if (pnfSelect && userData.pnf) {
    pnfSelect.value = userData.pnf;
  }
  if (trayectoSelect && userData.trayecto) {
    trayectoSelect.value = userData.trayecto;
  }
}

// --- Actualización de DATOS PERSONALES ---
if (formUserData) {
  formUserData.addEventListener("submit", (e) => {
    e.preventDefault();
    showLoader();
    clearFormErrors(userDataErrorSpans, formUserData); // Usar las variables del formulario de datos personales

    let formData = new FormData(e.target);

    fetch("process/update_user_data.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        if (!response.ok) {
          return response.json().then((errorData) => {
            throw new Error(
              errorData.message || "Error desconocido del servidor",
              { cause: errorData }
            );
          });
        }
        return response.json();
      })
      .then((data) => {
        hideLoader();

        if (data.success) {
          showUserMessage("success", data.message);
          const userNameSpan = document.querySelector(".user-profile-name");
          if (userNameSpan && data.user_data && data.user_data.nombre) {
            userNameSpan.textContent = data.user_data.nombre;
          }
          if (data.user_data) {
            updateProfileForm(data.user_data);
          }
        } else {
          showUserMessage(
            "error",
            data.message || "Ocurrió un error en la validación de los datos."
          );
          if (data.errors) {
            displayFormErrors(data.errors, userDataErrorSpans, formUserData); // Pasar los argumentos correctos
          }
        }
      })
      .catch((error) => {
        hideLoader();
        console.error("Error en la solicitud Fetch (Datos Personales):", error); // Mensaje más específico

        if (error.cause && error.cause.error === "Authentication Failed") {
          showUserMessage(
            "error",
            "Sesión inválida: " +
              error.cause.message +
              " Redirigiendo al login..."
          );
          if (error.cause.redirect) {
            setTimeout(() => {
              window.location.href = error.cause.redirect;
            }, 2000);
          }
        } else if (error.cause && error.cause.error) {
          showUserMessage(
            "error",
            "Error del servidor: " + error.cause.message
          );
        } else {
          showUserMessage(
            "error",
            "Ocurrió un error de conexión inesperado. Por favor, inténtalo de nuevo."
          );
        }
      });
  });
}

if (formPasswordUpdate) {
  formPasswordUpdate.addEventListener("submit", (e) => {
    e.preventDefault();
    showLoader();
    clearFormErrors(passwordErrorSpans, formPasswordUpdate); // Usar las variables del formulario de contraseña

    let formData = new FormData(e.target);

    fetch("process/update_password.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        if (!response.ok) {
          return response.json().then((errorData) => {
            throw new Error(
              errorData.message || "Error desconocido del servidor",
              { cause: errorData }
            );
          });
        }
        return response.json();
      })
      .then((data) => {
        hideLoader();

        if (data.success) {
          showUserMessage("success", data.message);
          formPasswordUpdate.reset(); // Limpiar el formulario de contraseña en éxito
          showUserMessage(
            "success",
            "Contraseña actualizada. Por favor, inicia sesión con tu nueva contraseña."
          );

          setTimeout(() => {
            window.location.href = "login.php";
          }, 5000);
        } else {
          showUserMessage(
            "error",
            data.message || "Ocurrió un error al actualizar la contraseña."
          );
          if (data.errors) {
            displayFormErrors(
              data.errors,
              passwordErrorSpans,
              formPasswordUpdate
            ); // Pasar los argumentos correctos
          }
        }
      })
      .catch((error) => {
        hideLoader();
        console.error("Error en la solicitud Fetch (Contraseña):", error); // Mensaje más específico

        if (error.cause && error.cause.error === "Authentication Failed") {
          showUserMessage(
            "error",
            "Sesión inválida: " +
              error.cause.message +
              " Redirigiendo al login..."
          );
          if (error.cause.redirect) {
            setTimeout(() => {
              window.location.href = error.cause.redirect;
            }, 2000);
          }
        } else if (error.cause && error.cause.error) {
          showUserMessage(
            "error",
            "Error del servidor: " + error.cause.message
          );
        } else {
          showUserMessage(
            "error",
            "Ocurrió un error de conexión inesperado. Por favor, inténtalo de nuevo."
          );
        }
      });
  });
}

// --- alternar visibilidad de contraseñas ---
const togglePasswordIcons = document.querySelectorAll('.toggle-password');

togglePasswordIcons.forEach(icon => {
    icon.addEventListener('click', function() {
        const targetInputId = this.dataset.target; // Obtiene el ID del input del atributo data-target
        const passwordInput = document.getElementById(targetInputId);

        if (passwordInput) {
            // Alternar el tipo del input
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Alternar el icono (ojo abierto/cerrado)
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        }
    });
});