"use strict";

const adminSidebarLinks = document.querySelectorAll(".admin-sidebar-nav a");
const adminContentSections = document.querySelectorAll(".admin-content-section");

const loaderOverlay = document.getElementById("loader-overlay");
const messageModal = document.getElementById('message-modal');
const messageText = document.getElementById('message-text');
const closeModalBtn = document.getElementById('close-modal-btn');

/**
 * Muestra el overlay de carga.
 */
function showLoader() {
    if (loaderOverlay) {
        loaderOverlay.classList.add("active");
    }
}

/**
 * Oculta el overlay de carga.
 */
function hideLoader() {
    if (loaderOverlay) {
        loaderOverlay.classList.remove("active");
    }
}

/**
 * Muestra un mensaje al usuario en un modal.
 * @param {string} type - 'success' o 'error'.
 * @param {string} message - Texto del mensaje.
 */
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

/**
 * Actualiza la sección activa del dashboard.
 * @param {string} targetSectionId - ID de la sección a activar.
 */
function updateAdminActiveSection(targetSectionId) {
    adminSidebarLinks.forEach((item) => item.classList.remove("active"));
    adminContentSections.forEach((section) => section.classList.remove("active"));

    const targetLink = document.querySelector(
        `.admin-sidebar-nav a[data-section="${targetSectionId}"]`
    );
    const targetSection = document.getElementById(targetSectionId);

    if (targetLink) {
        targetLink.classList.add("active");
    }

    if (targetSection) {
        targetSection.classList.add("active");
        // Cargar la tabla de recursos si la sección es 'books'
        if (targetSectionId === 'books') {
            loadResourcesTable();
        }
    }

    // Actualizar el título de la navbar
    const navbarTitle = document.querySelector('.admin-navbar-section-title');
    if (navbarTitle) {
        const sectionTitles = {
            'overview': 'Dashboard',
            'users': 'Gestión de Usuarios',
            'books': 'Gestión de Libros',
            'add-resource': 'Añadir Nuevo Recurso',
            'categories': 'Gestión de Categorías',
            'requests': 'Gestión de Solicitudes',
            'reports': 'Reportes y Estadísticas',
            'settings': 'Configuración del Sistema'
        };
        navbarTitle.textContent = sectionTitles[targetSectionId] || 'Panel de Administración';
    }
}

adminSidebarLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
        if (this.dataset.section) {
            e.preventDefault();
            updateAdminActiveSection(this.dataset.section);
        }
    });
});

updateAdminActiveSection("overview");

const adminUserProfile = document.querySelector('.admin-user-profile');
if (adminUserProfile) {
    adminUserProfile.addEventListener('click', function() {
        console.log('Menú de perfil de administrador clickeado');
    });
}

/**
 * Limpia mensajes de error y clases de validación de un formulario.
 * @param {object} errorSpansMap - Mapa de elementos span de error.
 * @param {HTMLElement} formElement - Elemento del formulario.
 */
function clearFormErrors(errorSpansMap, formElement) {
    for (const key in errorSpansMap) {
        if (errorSpansMap[key]) {
            errorSpansMap[key].innerText = "";
        }
    }
    formElement.querySelectorAll('.form-input, .form-input-file').forEach(input => {
        input.classList.remove('input-invalid');
    });
}

/**
 * Muestra mensajes de error en los spans correspondientes y marca inputs inválidos.
 * @param {object} errors - Objeto con mensajes de error.
 * @param {object} errorSpansMap - Mapa de elementos span de error.
 * @param {HTMLElement} formElement - Elemento del formulario.
 */
function displayFormErrors(errors, errorSpansMap, formElement) {
    clearFormErrors(errorSpansMap, formElement);
    let firstErrorInput = null;

    for (const field in errors) {
        if (errorSpansMap[field]) {
            errorSpansMap[field].innerText = errors[field];
            const inputElement = formElement.querySelector(`[name="${field}"]`);
            if (inputElement) {
                inputElement.classList.add('input-invalid');
                if (!firstErrorInput) {
                    firstErrorInput = inputElement;
                }
            }
        }
    }

    if (firstErrorInput) {
        firstErrorInput.scrollIntoView({ behavior: "smooth", block: "center" });
        firstErrorInput.focus();
    }
}

// --- Lógica para la Sección 'Añadir Recurso' ---
const formAddResource = document.getElementById("form-add-resource");
const addBookErrorSpans = {
    resource_type: document.getElementById("errAddResourceType"),
    title: document.getElementById("errAddTitle"),
    author: document.getElementById("errAddAuthor"),
    category: document.getElementById("errAddCategory"),
    publication_year: document.getElementById("errAddPublicationYear"),
    description: document.getElementById("errAddDescription"),
    book_pdf: document.getElementById("errAddBookPdf"),
    book_cover: document.getElementById("errAddBookCover")
};

if (formAddResource) {
    formAddResource.addEventListener("submit", async (e) => {
        e.preventDefault();
        showLoader();
        clearFormErrors(addBookErrorSpans, formAddResource);

        const formData = new FormData(e.target);

        try {
            const response = await fetch("process/admin_add_resource.php", {
                method: "POST",
                body: formData,
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Error desconocido del servidor', { cause: errorData });
            }

            const data = await response.json();

            hideLoader();

            if (data.success) {
                showUserMessage('success', data.message);
                formAddResource.reset();
                loadResourcesTable();
                updateAdminActiveSection('books');
            } else {
                showUserMessage('error', data.message || 'Ocurrió un error al añadir el recurso.');
                if (data.errors) {
                    displayFormErrors(data.errors, addBookErrorSpans, formAddResource);
                }
            }
        } catch (error) {
            hideLoader();
            console.error('Error en la solicitud Fetch (Añadir Recurso):', error);
            if (error.cause && error.cause.error === 'Authentication Failed') {
                showUserMessage('error', "Sesión inválida: " + error.cause.message + " Redirigiendo al login...");
                setTimeout(() => {
                    window.location.href = error.cause.redirect;
                }, 2000);
            } else if (error.cause && error.cause.error) {
                showUserMessage('error', "Error del servidor: " + error.cause.message);
            } else {
                showUserMessage('error', "Ocurrió un error de conexión inesperado. Por favor, inténtalo de nuevo.");
            }
        }
    });
}

// --- Lógica para la Sección 'Gestión de Libros/Recursos' ---
const resourcesTableBody = document.querySelector("#resources-table tbody");
const addNewResourceBtn = document.querySelector("#books .button[data-section='add-resource']");

if (addNewResourceBtn) {
    addNewResourceBtn.addEventListener('click', (e) => {
        e.preventDefault();
        updateAdminActiveSection('add-resource');
        formAddResource.reset();
        clearFormErrors(addBookErrorSpans, formAddResource);
    });
}

/**
 * Carga los recursos desde la base de datos y los muestra en la tabla.
 */
async function loadResourcesTable() {
    if (!resourcesTableBody) return;

    showLoader();
    try {
        const response = await fetch('process/get_resources.php');
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Error al cargar recursos.');
        }
        const data = await response.json();

        hideLoader();

        if (data.success) {
            resourcesTableBody.innerHTML = '';
            if (data.resources.length === 0) {
                resourcesTableBody.innerHTML = '<tr><td colspan="9" style="text-align: center; padding: 20px;">No hay recursos disponibles.</td></tr>';
                return;
            }

            data.resources.forEach(resource => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${resource.id}</td>
                    <td class="truncate-text">${resource.titulo}</td>
                    <td class="truncate-text">${resource.autor}</td>
                    <td>${resource.tipo_recurso}</td>
                    <td>${resource.categoria}</td>
                    <td>${resource.anio_publicacion}</td>
                    <td>
                        ${resource.ruta_pdf ? `<a href="${resource.ruta_pdf}" target="_blank"><i class="fas fa-file-pdf"></i></a>` : 'N/A'}
                    </td>
                    <td>
                        ${resource.ruta_portada ? `<img src="${resource.ruta_portada}" alt="Portada" class="resource-cover-thumbnail">` : 'N/A'}
                    </td>
                    <td>
                        <button class="action-btn edit-resource-btn" data-id="${resource.id}"><i class="fas fa-edit"></i></button>
                        <button class="action-btn delete-resource-btn" data-id="${resource.id}"><i class="fas fa-trash-alt"></i></button>
                    </td>
                `;
                resourcesTableBody.appendChild(row);
            });

            addResourceTableActionListeners();

        } else {
            showUserMessage('error', data.message || 'Error al obtener los recursos.');
        }

    } catch (error) {
        hideLoader();
        console.error('Error al cargar la tabla de recursos:', error);
        showUserMessage('error', 'No se pudieron cargar los recursos. Inténtalo de nuevo.');
    }
}

/**
 * Añade listeners a los botones de editar y eliminar de la tabla de recursos.
 */
function addResourceTableActionListeners() {
    document.querySelectorAll('.edit-resource-btn').forEach(button => {
        button.removeEventListener('click', handleEditButtonClick);
        button.addEventListener('click', handleEditButtonClick);
    });
    document.querySelectorAll('.delete-resource-btn').forEach(button => {
        button.removeEventListener('click', handleDeleteButtonClick);
        button.addEventListener('click', handleDeleteButtonClick);
    });
}

// --- Lógica para el Modal de Edición de Recursos ---
const editResourceModal = document.getElementById('edit-resource-modal');
const closeEditModalBtn = document.getElementById('close-edit-modal-btn');
const formEditResource = document.getElementById('form-edit-resource');
const editResourceErrorSpans = {
    resource_type: document.getElementById("errEditResourceType"),
    title: document.getElementById("errEditTitle"),
    author: document.getElementById("errEditAuthor"),
    category: document.getElementById("errEditCategory"),
    publication_year: document.getElementById("errEditPublicationYear"),
    description: document.getElementById("errEditDescription"),
    book_pdf: document.getElementById("errEditBookPdf"),
    book_cover: document.getElementById("errEditBookCover")
};

const editResourceIdInput = document.getElementById('edit-resource-id');
const editCurrentPdfPathInput = document.getElementById('edit-current-pdf');
const editCurrentCoverPathInput = document.getElementById('edit-current-cover');
const currentCoverPreview = document.getElementById('current-cover-preview');

if (closeEditModalBtn) {
    closeEditModalBtn.addEventListener('click', () => {
        if (editResourceModal) editResourceModal.style.display = 'none';
        clearFormErrors(editResourceErrorSpans, formEditResource);
    });
}

/**
 * Maneja el clic en el botón de "Editar" en la tabla.
 * Obtiene los detalles del recurso y rellena el modal de edición.
 * @param {Event} e - Evento de clic.
 */
async function handleEditButtonClick(e) {
    const resourceId = e.currentTarget.dataset.id;
    if (!resourceId) {
        showUserMessage('error', 'ID de recurso no encontrado para editar.');
        return;
    }

    showLoader();
    clearFormErrors(editResourceErrorSpans, formEditResource);

    try {
        const response = await fetch(`process/get_resource_details.php?id=${resourceId}`);
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Error al obtener detalles del recurso.');
        }
        const data = await response.json();

        hideLoader();

        if (data.success && data.resource) {
            const resource = data.resource;
            editResourceIdInput.value = resource.id;
            document.getElementById('edit-resource-type').value = resource.tipo_recurso;
            document.getElementById('edit-book-title').value = resource.titulo;
            document.getElementById('edit-book-author').value = resource.autor;
            document.getElementById('edit-book-category').value = resource.categoria;
            document.getElementById('edit-book-year').value = resource.anio_publicacion;
            document.getElementById('edit-book-description').value = resource.descripcion;

            editCurrentPdfPathInput.value = resource.ruta_pdf || '';
            editCurrentCoverPathInput.value = resource.ruta_portada || '';

            if (resource.ruta_portada && currentCoverPreview) {
                currentCoverPreview.innerHTML = `<p style="font-size: 13px; color: var(--color-medium-text); margin-bottom: 5px;">Portada actual:</p><img src="${resource.ruta_portada}" alt="Portada Actual" style="max-width: 100px; height: auto; border-radius: var(--border-radius-sm);">`;
            } else if (currentCoverPreview) {
                currentCoverPreview.innerHTML = '<p style="font-size: 13px; color: var(--color-light-text);">No hay portada actual.</p>';
            }

            editResourceModal.style.display = 'flex';
        } else {
            showUserMessage('error', data.message || 'Recurso no encontrado.');
        }

    } catch (error) {
        hideLoader();
        console.error('Error al abrir modal de edición:', error);
        showUserMessage('error', 'No se pudieron cargar los detalles del recurso. Inténtalo de nuevo.');
    }
}

if (formEditResource) {
    formEditResource.addEventListener('submit', async (e) => {
        e.preventDefault();
        showLoader();
        clearFormErrors(editResourceErrorSpans, formEditResource);

        const formData = new FormData(e.target);

        if (document.getElementById('edit-book-pdf').files.length === 0) {
            formData.delete('book_pdf');
        }
        if (document.getElementById('edit-book-cover').files.length === 0) {
            formData.delete('book_cover');
        }

        try {
            const response = await fetch('process/admin_edit_resource.php', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Error desconocido del servidor', { cause: errorData });
            }
            const data = await response.json();

            hideLoader();
            if (data.success) {
                showUserMessage('success', data.message);
                editResourceModal.style.display = 'none';
                loadResourcesTable();
            } else {
                showUserMessage('error', data.message || 'Ocurrió un error al actualizar el recurso.');
                if (data.errors) {
                    displayFormErrors(data.errors, editResourceErrorSpans, formEditResource);
                }
            }

        } catch (error) {
            hideLoader();
            console.error('Error en la solicitud Fetch (Editar Recurso):', error);
            if (error.cause && error.cause.error === 'Authentication Failed') {
                showUserMessage('error', "Sesión inválida: " + error.cause.message + " Redirigiendo al login...");
                setTimeout(() => {
                    window.location.href = error.cause.redirect;
                }, 2000);
            } else if (error.cause && error.cause.error) {
                showUserMessage('error', "Error del servidor: " + error.cause.message);
            } else {
                showUserMessage('error', "Ocurrió un error de conexión inesperado. Por favor, inténtalo de nuevo.");
            }
        }
    });
}

/**
 * Maneja el clic en el botón de "Eliminar" en la tabla.
 * Pide confirmación y elimina el recurso.
 * @param {Event} e - Evento de clic.
 */
async function handleDeleteButtonClick(e) {
    const resourceId = e.currentTarget.dataset.id;
    if (!resourceId) {
        showUserMessage('error', 'ID de recurso no encontrado para eliminar.');
        return;
    }

    if (!confirm('¿Estás seguro de que deseas eliminar este recurso? Esta acción es irreversible y eliminará los archivos asociados.')) {
        return;
    }

    showLoader();
    try {
        const response = await fetch('process/admin_delete_resource.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `resource_id=${resourceId}`
        });

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Error desconocido del servidor', { cause: errorData });
        }
        const data = await response.json();

        hideLoader();
        if (data.success) {
            showUserMessage('success', data.message);
            loadResourcesTable();
        } else {
            showUserMessage('error', data.message || 'Ocurrió un error al eliminar el recurso.');
        }

    } catch (error) {
        hideLoader();
        console.error('Error en la solicitud Fetch (Eliminar Recurso):', error);
        if (error.cause && error.cause.error === 'Authentication Failed') {
            showUserMessage('error', "Sesión inválida: " + error.cause.message + " Redirigiendo al login...");
            setTimeout(() => {
                window.location.href = error.cause.redirect;
            }, 2000);
        } else if (error.cause && error.cause.error) {
            showUserMessage('error', "Error del servidor: " + error.cause.message);
        } else {
            showUserMessage('error', "Ocurrió un error de conexión inesperado. Por favor, inténtalo de nuevo.");
        }
    }
}
