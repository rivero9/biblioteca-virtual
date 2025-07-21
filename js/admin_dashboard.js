"use strict";

// Selectores para los elementos del sidebar y las secciones de contenido
const adminSidebarLinks = document.querySelectorAll(".admin-sidebar-nav a");
const adminContentSections = document.querySelectorAll(".admin-content-section");

const loaderOverlay = document.getElementById("loader-overlay");
const messageModal = document.getElementById('message-modal');
const messageText = document.getElementById('message-text');
const closeModalBtn = document.getElementById('close-modal-btn');

// Selectores para el formulario de añadir recurso y sus elementos
const formAddResource = document.getElementById('form-add-resource');
const addAuthorBtn = document.getElementById('add-author-btn');
const authorFieldsContainer = document.getElementById('author-fields-container');
let authorIndex = 0; // Para el contador de autores en el formulario de añadir

// Selectores para el modal de edición de recursos y sus elementos
const editResourceModal = document.getElementById('edit-resource-modal');
const closeEditModalBtn = document.getElementById('close-edit-modal-btn');
const formEditResource = document.getElementById('form-edit-resource');
const editAuthorFieldsContainer = document.getElementById('edit-author-fields-container');
const editAddAuthorBtn = document.getElementById('edit-add-author-btn');
let editAuthorIndex = 0; // Para el contador de autores en el formulario de edición
    
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
    } else {
        console.log(`Mensaje (${type}): ${message}`);
    }
}

/**
 * Oculta el modal de mensajes.
 */
function hideUserMessage() {
    if (messageModal) {
        messageModal.style.display = 'none';
    }
}

// Event listener para cerrar el modal de mensajes manualmente
if (closeModalBtn) {
    closeModalBtn.addEventListener('click', hideUserMessage);
    messageModal.addEventListener('click', (e) => {
        if (e.target === messageModal) {
            hideUserMessage();
        }
    });
}

/**
 * Muestra un modal.
 * @param {HTMLElement} modalElement - El elemento del modal a mostrar.
 */
function showModal(modalElement) {
    if (modalElement) {
        modalElement.style.display = 'flex';
    }
}

/**
 * Oculta un modal.
 * @param {HTMLElement} modalElement - El elemento del modal a ocultar.
 */
function hideModal(modalElement) {
    if (modalElement) {
        modalElement.style.display = 'none';
    }
}

// Event listener para cerrar el modal de edición
if (closeEditModalBtn) {
    closeEditModalBtn.addEventListener('click', () => hideModal(editResourceModal));
    editResourceModal.addEventListener('click', (e) => {
        if (e.target === editResourceModal) {
            hideModal(editResourceModal);
        }
    });
}

/**
 * Limpia los mensajes de error de un formulario.
 * @param {HTMLElement} formElement - El elemento del formulario.
 */
function clearFormErrors(formElement) {
    formElement.querySelectorAll('.error-message').forEach(span => {
        span.textContent = '';
    });
    formElement.querySelectorAll('.form-input, .form-input-file, .form-select').forEach(input => {
        input.classList.remove('input-invalid');
    });
}

/**
 * Muestra los errores de validación en el formulario.
 * @param {HTMLElement} formElement - El elemento del formulario.
 * @param {object} errors - Objeto con los errores.
 */
function displayFormErrors(formElement, errors) {
    clearFormErrors(formElement); // Limpiar errores previos
    let firstErrorInput = null; // Para enfocar el primer campo con error

    for (const key in errors) {
        if (errors.hasOwnProperty(key)) {
            const errorSpan = formElement.querySelector(`#err${capitalizeFirstLetter(key.replace(/\[(\d+)\]/g, '$1').replace(/\[|\]/g, ''))}`);
            const inputElement = formElement.querySelector(`[name="${key}"]`);

            // Manejo especial para errores de autores (ej. authors[0][name])
            if (key.startsWith('authors[')) {
                const match = key.match(/authors\[(\d+)\]\[(.*?)\]/);
                if (match) {
                    const authorIdx = match[1];
                    const fieldName = match[2];
                    const specificErrorSpan = formElement.querySelector(`#errAddAuthor${authorIdx}, #errEditAuthor${authorIdx}`); // Buscar en ambos formularios
                    const specificInput = formElement.querySelector(`[name="authors[${authorIdx}][${fieldName}]"]`);

                    if (specificErrorSpan && fieldName === 'name') { // Mostrar error principal del autor en el span del nombre
                        specificErrorSpan.textContent = errors[key];
                    }
                    if (specificInput) {
                        specificInput.classList.add('input-invalid');
                        if (!firstErrorInput) firstErrorInput = specificInput;
                    }
                }
            } else if (key === 'authors') { // Error general de autores
                const generalAuthorsError = formElement.querySelector('#errAddAuthors, #errEditAuthors');
                if (generalAuthorsError) {
                    generalAuthorsError.textContent = errors[key];
                    if (!firstErrorInput) firstErrorInput = formElement.querySelector('.author-name-input'); // Enfocar el primer campo de autor
                }
            }
            else if (errorSpan) {
                errorSpan.textContent = errors[key];
            }
            if (inputElement) {
                inputElement.classList.add('input-invalid');
                if (!firstErrorInput) firstErrorInput = inputElement;
            }
        }
    }

    if (firstErrorInput) {
        firstErrorInput.scrollIntoView({ behavior: "smooth", block: "center" });
        firstErrorInput.focus();
    }
}

/**
 * Capitaliza la primera letra de una cadena.
 * @param {string} string - La cadena a capitalizar.
 * @returns {string} La cadena con la primera letra capitalizada.
 */
function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

/**
 * Función para actualizar la sección activa del dashboard.
 * @param {string} targetSectionId - El ID de la sección a activar.
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
    }

    // Si la sección de libros se activa, cargar los recursos
    if (targetSectionId === 'books') {
        loadResourcesTable();
    }
    // Asegurarse de que el primer campo de autor esté visible y sin errores al ir a añadir
    if (targetSectionId === 'add-resource') {
        formAddResource.reset(); // Resetear el formulario
        resetAuthorFields(authorFieldsContainer, 'add'); // Asegurar reset de autores
        clearFormErrors(formAddResource); // Limpiar errores al cambiar de sección
    }

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

// Añadir event listeners a los enlaces del sidebar
adminSidebarLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
        if (this.dataset.section) {
            e.preventDefault();
            updateAdminActiveSection(this.dataset.section);
        }
    });
});

// Establecer la sección inicial al cargar la página (por defecto 'overview')
updateAdminActiveSection("overview");

const adminUserProfile = document.querySelector('.admin-user-profile');
if (adminUserProfile) {
    adminUserProfile.addEventListener('click', function() {
        // Lógica para mostrar/ocultar menú de perfil
    });
}

// --- Lógica para Múltiples Autores (Añadir y Editar Recurso) ---
/**
 * Genera el HTML para un grupo de campos de autor.
 * @param {number} index - Índice para los nombres de los campos.
 * @param {object} [authorData={}] - Datos del autor para pre-llenar.
 * @param {boolean} [isEdit=false] - Si es para el formulario de edición.
 * @returns {HTMLElement} El div que contiene los campos del autor.
 */
function createAuthorInputGroup(index, authorData = {}, isEdit = false) {
    const group = document.createElement('div');
    group.classList.add('author-input-group', 'mb-10');
    group.dataset.index = index; // Para fácil referencia

    const nameValue = authorData.nombre && authorData.apellido ? `${authorData.nombre} ${authorData.apellido}` : (authorData.nombre || '');
    const isRemovable = index > 0 || (isEdit && index === 0 && (authorData.id_autor || Object.keys(authorData).length > 0)); // Permitir eliminar si no es el primer campo vacío
    const isExistingAuthor = authorData.id_autor !== undefined && authorData.id_autor !== null && authorData.id_autor !== ''; // Si tiene id_autor, es existente

    group.innerHTML = `
        <div class="author-name-input-wrapper">
            <input type="text" name="authors[${index}][name]" class="form-input author-name-input" placeholder="Nombre completo del autor" value="${nameValue}" ${isExistingAuthor ? 'readonly' : ''} autocomplete="off" required>
            <button type="button" class="clear-author-btn" style="display: ${isExistingAuthor ? 'block' : 'none'};" title="Limpiar selección de autor"><i class="fas fa-times-circle"></i></button>
        </div>
        <div class="autocomplete-suggestions"></div> <!-- Contenedor para sugerencias personalizadas -->        
        <input type="hidden" name="authors[${index}][id_autor]" class="author-id-input" value="${authorData.id_autor || ''}">
        <input type="email" name="authors[${index}][email_contacto_autor]" class="form-input mt-5" placeholder="Email de contacto (opcional)" value="${authorData.email_contacto_autor || ''}" ${isExistingAuthor ? 'readonly' : ''}>
        <input type="text" name="authors[${index}][telefono_contacto_autor]" class="form-input mt-5" placeholder="Teléfono de contacto (opcional)" value="${authorData.telefono_contacto_autor || ''}" ${isExistingAuthor ? 'readonly' : ''}>
        <div class="social-media-inputs mt-5">
            <div class="social-input-group">
                <i class="fab fa-linkedin social-icon"></i>
                <input type="url" name="authors[${index}][social_linkedin]" class="form-input" placeholder="Ej: https://www.linkedin.com/in/nombredeusuario" value="${authorData.social_linkedin || ''}" ${isExistingAuthor ? 'readonly' : ''}>
            </div>
            <div class="social-input-group mt-5">
                <i class="fab fa-twitter social-icon"></i>
                <input type="url" name="authors[${index}][social_twitter]" class="form-input" placeholder="Ej: https://twitter.com/nombredeusuario" value="${authorData.social_twitter || ''}" ${isExistingAuthor ? 'readonly' : ''}>
            </div>
            <div class="social-input-group mt-5">
                <i class="fab fa-github social-icon"></i>
                <input type="url" name="authors[${index}][social_github]" class="form-input" placeholder="Ej: https://github.com/nombredeusuario" value="${authorData.social_github || ''}" ${isExistingAuthor ? 'readonly' : ''}>
            </div>
            <div class="social-input-group mt-5">
                <i class="fab fa-facebook social-icon"></i>
                <input type="url" name="authors[${index}][social_facebook]" class="form-input" placeholder="Ej: https://www.facebook.com/nombredeusuario" value="${authorData.social_facebook || ''}" ${isExistingAuthor ? 'readonly' : ''}>
            </div>
        </div>
        <span class="error-message" id="err${isEdit ? 'Edit' : 'Add'}Author${index}"></span>
        <button type="button" class="button button-secondary button-remove-author mt-10" style="display: ${isRemovable ? 'block' : 'none'};"><i class="fas fa-minus"></i> Eliminar Autor</button>
    `;

    // Listener para eliminar el grupo
    if (isRemovable) {
        group.querySelector('.button-remove-author').addEventListener('click', () => {
            group.remove();
            // Asegurarse de que al menos un botón de eliminar se oculte si solo queda un campo
            const remainingAuthorGroups = group.parentNode.querySelectorAll('.author-input-group');
            if (remainingAuthorGroups.length === 1) {
                remainingAuthorGroups[0].querySelector('.button-remove-author').style.display = 'none';
            }
        });
    }

    // --- Lógica de autocompletado personalizada ---
    const authorNameInput = group.querySelector('.author-name-input');
    const authorIdInput = group.querySelector('.author-id-input');
    const autocompleteSuggestionsContainer = group.querySelector('.autocomplete-suggestions');
    const contactInputs = group.querySelectorAll('input[type="email"], input[type="text"][name*="telefono"], input[type="url"]');
    const clearAuthorBtn = group.querySelector('.clear-author-btn');

    let debounceTimer;
    let activeSuggestionIndex = -1; // Para navegación con teclado

    // Función para rellenar los campos del autor seleccionado
    const selectAuthorSuggestion = (suggestionElement) => {
        const authorId = suggestionElement.dataset.authorId;
        const authorName = suggestionElement.textContent; // Usar textContent para el nombre completo

        authorNameInput.value = authorName;
        authorIdInput.value = authorId;
        group.querySelector('[name$="[email_contacto_autor]"]').value = suggestionElement.dataset.authorEmail;
        group.querySelector('[name$="[telefono_contacto_autor]"]').value = suggestionElement.dataset.authorPhone;
        group.querySelector('[name$="[social_linkedin]"]').value = suggestionElement.dataset.authorLinkedin;
        group.querySelector('[name$="[social_twitter]"]').value = suggestionElement.dataset.authorTwitter;
        group.querySelector('[name$="[social_github]"]').value = suggestionElement.dataset.authorGithub;
        group.querySelector('[name$="[social_facebook]"]').value = suggestionElement.dataset.authorFacebook;

        // Hacer los campos de solo lectura
        contactInputs.forEach(input => input.readOnly = true);
        authorNameInput.readOnly = true;
        
        // Mostrar botón de limpiar
        if (clearAuthorBtn) {
            clearAuthorBtn.style.display = 'block';
        }

        autocompleteSuggestionsContainer.style.display = 'none'; // Ocultar sugerencias
        activeSuggestionIndex = -1; // Resetear índice
    };

    // Función para limpiar la selección del autor
    // shouldFocus: booleano para indicar si el input de nombre de autor debe recuperar el foco
    const clearAuthorSelection = (shouldFocus = true) => {
        authorNameInput.value = '';
        authorIdInput.value = '';
        contactInputs.forEach(input => {
            input.value = ''; // Limpiar valor
            input.readOnly = false; // Hacer editable
        });
        authorNameInput.readOnly = false;
        autocompleteSuggestionsContainer.innerHTML = '';
        autocompleteSuggestionsContainer.style.display = 'none';
        activeSuggestionIndex = -1;
        if (clearAuthorBtn) {
            clearAuthorBtn.style.display = 'none'; // Ocultar botón de limpiar
        }
        if (shouldFocus) { // Solo enfocar si se le indica
            authorNameInput.focus();
        }
    };

    // Event listener para el botón de limpiar
    if (clearAuthorBtn) {
        clearAuthorBtn.addEventListener('click', () => clearAuthorSelection(true)); // Siempre enfocar al hacer clic en el botón
    }

    // Evento 'input' para buscar sugerencias
    authorNameInput.addEventListener('input', async (e) => {
        clearTimeout(debounceTimer);
        const query = e.target.value.trim();

        // Si el usuario empieza a escribir después de tener un autor seleccionado, limpiar la selección
        // Pero no enfocar de nuevo, ya que el usuario ya está escribiendo
        if (authorIdInput.value !== '' && query !== authorNameInput.value) {
            clearAuthorSelection(false); // No enfocar al limpiar por escritura
            authorNameInput.value = query; // Mantener lo que el usuario está escribiendo
        }

        if (query.length < 2) {
            autocompleteSuggestionsContainer.innerHTML = '';
            autocompleteSuggestionsContainer.style.display = 'none';
            // No limpiar authorIdInput ni campos de contacto aquí si el usuario solo borra.
            // La lógica de blur se encargará si el campo queda vacío y pierde el foco.
            return;
        }

        debounceTimer = setTimeout(async () => {
            try {
                const response = await fetch(`process/search_authors.php?query=${encodeURIComponent(query)}`);
                if (!response.ok) {
                    throw new Error('Error al buscar autores.');
                }
                const data = await response.json();

                autocompleteSuggestionsContainer.innerHTML = '';
                if (data.success && data.authors.length > 0) {
                    data.authors.forEach((author, idx) => {
                        const suggestionDiv = document.createElement('div');
                        suggestionDiv.classList.add('autocomplete-suggestion-item');
                        suggestionDiv.textContent = `${author.nombre} ${author.apellido || ''}`.trim();
                        // Almacenar todos los datos del autor en el dataset
                        suggestionDiv.dataset.authorId = author.id_autor;
                        suggestionDiv.dataset.authorEmail = author.email_contacto_autor || '';
                        suggestionDiv.dataset.authorPhone = author.telefono_contacto_autor || '';
                        suggestionDiv.dataset.authorLinkedin = author.social_linkedin || '';
                        suggestionDiv.dataset.authorTwitter = author.social_twitter || '';
                        suggestionDiv.dataset.authorGithub = author.social_github || '';
                        suggestionDiv.dataset.authorFacebook = author.social_facebook || '';

                        suggestionDiv.addEventListener('click', () => {
                            selectAuthorSuggestion(suggestionDiv);
                        });
                        autocompleteSuggestionsContainer.appendChild(suggestionDiv);
                    });
                    autocompleteSuggestionsContainer.style.display = 'block';
                    activeSuggestionIndex = -1; // Resetear el índice activo
                } else {
                    autocompleteSuggestionsContainer.style.display = 'none';
                }
            } catch (error) {
                console.error('Error en autocompletado de autor:', error);
                autocompleteSuggestionsContainer.style.display = 'none';
            }
        }, 300);
    });

    // Evento 'keydown' para navegación con teclado
    authorNameInput.addEventListener('keydown', (e) => {
        const suggestions = autocompleteSuggestionsContainer.querySelectorAll('.autocomplete-suggestion-item');
        if (suggestions.length === 0) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault(); // Prevenir el scroll de la página
            activeSuggestionIndex = (activeSuggestionIndex + 1) % suggestions.length;
            highlightActiveSuggestion(suggestions);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault(); // Prevenir el scroll de la página
            activeSuggestionIndex = (activeSuggestionIndex - 1 + suggestions.length) % suggestions.length;
            highlightActiveSuggestion(suggestions);
        } else if (e.key === 'Enter') {
            e.preventDefault(); // Prevenir el envío del formulario
            if (activeSuggestionIndex > -1) {
                selectAuthorSuggestion(suggestions[activeSuggestionIndex]);
            }
        } else if (e.key === 'Escape') {
            autocompleteSuggestionsContainer.style.display = 'none';
            activeSuggestionIndex = -1;
        }
    });

    // Función para resaltar la sugerencia activa
    const highlightActiveSuggestion = (suggestions) => {
        suggestions.forEach((item, idx) => {
            item.classList.toggle('active', idx === activeSuggestionIndex);
            if (idx === activeSuggestionIndex) {
                item.scrollIntoView({ block: 'nearest' });
            }
        });
    };

    // Ocultar sugerencias cuando el input pierde el foco (con un pequeño retraso)
    authorNameInput.addEventListener('blur', () => {
        setTimeout(() => {
            // Solo ocultar si el foco no se movió a una sugerencia
            if (!autocompleteSuggestionsContainer.contains(document.activeElement)) {
                autocompleteSuggestionsContainer.style.display = 'none';
                activeSuggestionIndex = -1;
                // Eliminado: La llamada a clearAuthorSelection(false) aquí.
                // El valor del input DEBE persistir si el usuario lo escribió manualmente
                // y no seleccionó una sugerencia. Se tratará como un nuevo autor al enviar el formulario.
            }
        }, 150); // Pequeño retraso para permitir clics en las sugerencias
    });

    // Mostrar sugerencias de nuevo si el input gana el foco y hay texto
    authorNameInput.addEventListener('focus', async (e) => {
        const query = e.target.value.trim();
        // Solo mostrar si el campo no está readonly (es decir, no hay un autor seleccionado)
        if (!authorNameInput.readOnly && query.length >= 2 && autocompleteSuggestionsContainer.innerHTML !== '') {
            autocompleteSuggestionsContainer.style.display = 'block';
        }
    });

    // Inicializar el estado del botón de limpiar al crear el grupo de autor
    if (isExistingAuthor && clearAuthorBtn) {
        clearAuthorBtn.style.display = 'block';
    } else if (clearAuthorBtn) {
        clearAuthorBtn.style.display = 'none';
    }

    return group;
}

/**
 * Resetea los campos de autor a un estado inicial (un solo campo editable).
 * @param {HTMLElement} container - El contenedor de los campos de autor.
 * @param {string} formType - 'add' o 'edit'.
 */
function resetAuthorFields(container, formType) {
    container.innerHTML = '';
    // Al crear el primer grupo, siempre se le pasa shouldFocus = true para que enfoque el primer input
    const initialAuthorGroup = createAuthorInputGroup(0, {}, formType === 'edit');
    container.appendChild(initialAuthorGroup);
    // Asegurarse de que el input de nombre del primer autor esté enfocado al resetear
    initialAuthorGroup.querySelector('.author-name-input').focus();

    if (formType === 'add') {
        authorIndex = 1;
    } else if (formType === 'edit') {
        editAuthorIndex = 1;
    }
}

// Inicializar el primer campo de autor al cargar la página (para añadir recurso)
if (authorFieldsContainer) {
    resetAuthorFields(authorFieldsContainer, 'add');
}

// Event listener para añadir más autores (Añadir Recurso)
if (addAuthorBtn) {
    addAuthorBtn.addEventListener('click', () => {
        const newAuthorGroup = createAuthorInputGroup(authorIndex, {}, false); // Se puede eliminar
        authorFieldsContainer.appendChild(newAuthorGroup);
        authorIndex++;
    });
}

// Event listener para añadir más autores (Editar Recurso)
if (editAddAuthorBtn) {
    editAddAuthorBtn.addEventListener('click', () => {
        const newAuthorGroup = createAuthorInputGroup(editAuthorIndex, {}, true); // Se puede eliminar
        editAuthorFieldsContainer.appendChild(newAuthorGroup);
        editAuthorIndex++;
    });
}


// --- Lógica para Cargar y Mostrar Recursos ---
const resourcesTableBody = document.querySelector("#resources-table tbody");
const addNewResourceBtn = document.querySelector("#books .button[data-section='add-resource']");

if (addNewResourceBtn) {
    addNewResourceBtn.addEventListener('click', (e) => {
        e.preventDefault();
        updateAdminActiveSection('add-resource');
        formAddResource.reset();
        resetAuthorFields(authorFieldsContainer, 'add'); // Asegurar reset de autores
        clearFormErrors(formAddResource);
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
            throw new Error(errorData.message || 'Error al cargar recursos.', { cause: errorData });
        }
        const data = await response.json();

        hideLoader();

        if (data.success) {
            resourcesTableBody.innerHTML = '';
            if (data.resources.length === 0) {
                resourcesTableBody.innerHTML = '<tr><td colspan="10" style="text-align: center; padding: 20px;">No hay recursos disponibles.</td></tr>'; // Colspan ajustado
                return;
            }

            data.resources.forEach(resource => {
                const row = document.createElement('tr');
                const authorsDisplay = resource.autores_nombres || 'N/A';

                row.innerHTML = `
                    <td>${resource.id}</td>
                    <td class="truncate-text">${resource.titulo}</td>
                    <td class="truncate-text">${authorsDisplay}</td>
                    <td>${resource.tipo_recurso}</td>
                    <td>${resource.categoria}</td>
                    <td>${resource.anio_publicacion}</td>
                    <td>
                        ${resource.ruta_pdf ? `<a href="${resource.ruta_pdf}" target="_blank"><i class="fas fa-file-pdf"></i></a>` : 'N/A'}
                    </td>
                    <td>
                        ${resource.ruta_video ? `<a href="${resource.ruta_video}" target="_blank"><i class="fas fa-video"></i></a>` : 'N/A'}
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
        if (error.cause && error.cause.error === 'Authentication Failed') {
            showUserMessage('error', "Sesión inválida: " + error.cause.message + " Redirigiendo al login...");
            setTimeout(() => {
                window.location.href = error.cause.redirect;
            }, 2000);
        } else if (error.cause && error.cause.error) {
            showUserMessage('error', "Error del servidor: " + error.cause.message);
        } else {
            showUserMessage('error', "Ocurrió un error inesperado al cargar los recursos. Por favor, inténtalo de nuevo.");
        }
    }
}

/**
 * Añade listeners a los botones de editar y eliminar de la tabla de recursos.
 */
function addResourceTableActionListeners() {
    document.querySelectorAll('.edit-resource-btn').forEach(button => {
        button.removeEventListener('click', handleEditButtonClick); // Prevenir duplicados
        button.addEventListener('click', handleEditButtonClick);
    });
    document.querySelectorAll('.delete-resource-btn').forEach(button => {
        button.removeEventListener('click', handleDeleteButtonClick); // Prevenir duplicados
        button.addEventListener('click', handleDeleteButtonClick);
    });
}

// --- Lógica para el Modal de Edición de Recursos ---
const editResourceIdInput = document.getElementById('edit-resource-id');
const editCurrentPdfPathInput = document.getElementById('edit-current-pdf');
const editCurrentCoverPathInput = document.getElementById('edit-current-cover');
const editCurrentVideoPathInput = document.getElementById('edit-current-video');
const currentCoverPreview = document.getElementById('current-cover-preview');
const currentVideoPreview = document.getElementById('current-video-preview');

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
    clearFormErrors(formEditResource); // Limpiar errores del formulario de edición
    resetAuthorFields(editAuthorFieldsContainer, 'edit'); // Resetear campos de autor antes de cargar

    try {
        const response = await fetch(`process/get_resource_details.php?id=${resourceId}`);
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Error al obtener detalles del recurso.', { cause: errorData });
        }
        const data = await response.json();

        hideLoader();

        if (data.success && data.resource) {
            const resource = data.resource;
            editResourceIdInput.value = resource.id;
            document.getElementById('edit-resource-type').value = resource.tipo_recurso;
            document.getElementById('edit-book-title').value = resource.titulo;
            document.getElementById('edit-book-category').value = resource.categoria;
            document.getElementById('edit-book-year').value = resource.anio_publicacion;
            document.getElementById('edit-book-description').value = resource.descripcion;

            editCurrentPdfPathInput.value = resource.ruta_pdf || '';
            editCurrentCoverPathInput.value = resource.ruta_portada || '';
            editCurrentVideoPathInput.value = resource.ruta_video || '';

            // Mostrar preview de portada
            if (resource.ruta_portada && currentCoverPreview) {
                currentCoverPreview.innerHTML = `<p style="font-size: 13px; color: var(--color-medium-text); margin-bottom: 5px;">Portada actual:</p><img src="${resource.ruta_portada}" alt="Portada Actual" style="max-width: 100px; height: auto; border-radius: var(--border-radius-sm);">`;
            } else if (currentCoverPreview) {
                currentCoverPreview.innerHTML = '<p style="font-size: 13px; color: var(--color-light-text);">No hay portada actual.</p>';
            }

            // Mostrar preview de video
            if (resource.ruta_video && currentVideoPreview) {
                currentVideoPreview.innerHTML = `<p style="font-size: 13px; color: var(--color-medium-text); margin-bottom: 5px;">Video actual:</p><video controls style="max-width: 100%; height: auto; border-radius: var(--border-radius-sm);"><source src="${resource.ruta_video}" type="video/mp4">Tu navegador no soporta el elemento de video.</video>`;
            } else if (currentVideoPreview) {
                currentVideoPreview.innerHTML = '<p style="font-size: 13px; color: var(--color-light-text);">No hay video actual.</p>';
            }

            // Cargar autores
            if (resource.autores && resource.autores.length > 0) {
                editAuthorFieldsContainer.innerHTML = ''; // Limpiar el campo inicial
                resource.autores.forEach((author, index) => {
                    const authorGroup = createAuthorInputGroup(index, author, true); // true para isEdit
                    editAuthorFieldsContainer.appendChild(authorGroup);
                });
                editAuthorIndex = resource.autores.length; // Ajustar el índice para nuevos autores
            } else {
                // Si no hay autores, asegurar un campo vacío
                resetAuthorFields(editAuthorFieldsContainer, 'edit');
            }

            showModal(editResourceModal);
        } else {
            showUserMessage('error', data.message || 'Recurso no encontrado.');
        }

    } catch (error) {
        hideLoader();
        console.error('Error al abrir modal de edición:', error);
        if (error.cause && error.cause.error === 'Authentication Failed') {
            showUserMessage('error', "Sesión inválida: " + error.cause.message + " Redirigiendo al login...");
            setTimeout(() => {
                window.location.href = error.cause.redirect;
            }, 2000);
        } else if (error.cause && error.cause.error) {
            showUserMessage('error', "Error del servidor: " + error.cause.message);
        } else {
            showUserMessage('error', "Ocurrió un error inesperado al cargar los detalles para edición. Por favor, inténtalo de nuevo.");
        }
    }
}

if (formEditResource) {
    formEditResource.addEventListener('submit', async (e) => {
        e.preventDefault();
        showLoader();
        clearFormErrors(formEditResource); // Limpiar errores del formulario de edición

        const formData = new FormData(e.target);

        // Eliminar archivos del formData si no se seleccionó uno nuevo
        if (document.getElementById('edit-book-pdf').files.length === 0) {
            formData.delete('book_pdf');
        }
        if (document.getElementById('edit-book-cover').files.length === 0) {
            formData.delete('book_cover');
        }
        if (document.getElementById('edit-resource-video').files.length === 0) {
            formData.delete('resource_video');
        } else {
            // Validar tipo de archivo de video para edición
            const videoFile = document.getElementById('edit-resource-video').files[0];
            if (videoFile && !videoFile.type.startsWith('video/')) {
                displayFormErrors(formEditResource, {'resource_video': 'El archivo de video debe ser un formato de video válido.'});
                hideLoader();
                return;
            }
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
                hideModal(editResourceModal);
                loadResourcesTable(); // Recargar la tabla para ver los cambios
            } else {
                showUserMessage('error', data.message || 'Ocurrió un error al actualizar el recurso.');
                if (data.errors) {
                    displayFormErrors(formEditResource, data.errors);
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
                showUserMessage('error', "Ocurrió un error inesperado al editar el recurso. Por favor, inténtalo de nuevo.");
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

    // Usar un modal de confirmación personalizado en lugar de confirm()
    const confirmDelete = await new Promise(resolve => {
        const customConfirmModal = document.createElement('div');
        customConfirmModal.classList.add('message-modal');
        customConfirmModal.innerHTML = `
            <div class="message-content admin-modal-content">
                <h3>Confirmar Eliminación</h3>
                <p>¿Estás seguro de que deseas eliminar este recurso? Esta acción es irreversible y eliminará los archivos asociados (PDF, Video, Portada) y sus autores asociados.</p>
                <div style="display: flex; justify-content: center; gap: 15px; margin-top: 20px;">
                    <button id="confirm-delete-btn" class="button">Eliminar</button>
                    <button id="cancel-delete-btn" class="button button-secondary">Cancelar</button>
                </div>
            </div>
        `;
        document.body.appendChild(customConfirmModal);
        customConfirmModal.style.display = 'flex';

        document.getElementById('confirm-delete-btn').addEventListener('click', () => {
            customConfirmModal.remove();
            resolve(true);
        });
        document.getElementById('cancel-delete-btn').addEventListener('click', () => {
            customConfirmModal.remove();
            resolve(false);
        });
    });

    if (!confirmDelete) {
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
            showUserMessage('error', "Ocurrió un error inesperado al eliminar el recurso. Por favor, inténtalo de nuevo.");
        }
    }
}


// --- Lógica de Drag-and-Drop para Scroll Horizontal ---
// Seleccionar TODOS los elementos con la clase .table-responsive
const allTableResponsiveDivs = document.querySelectorAll('.table-responsive');

if (allTableResponsiveDivs.length > 0) {
    console.log(`Se encontraron ${allTableResponsiveDivs.length} elementos .table-responsive para drag-and-drop.`);

    allTableResponsiveDivs.forEach(tableResponsiveDiv => {
        let isDown = false;
        let startX;
        let scrollLeft;

        tableResponsiveDiv.addEventListener('mousedown', (e) => {
            isDown = true;
            tableResponsiveDiv.classList.add('active-drag');
            startX = e.pageX - tableResponsiveDiv.offsetLeft;
            scrollLeft = tableResponsiveDiv.scrollLeft;
            // console.log('Mouse Down en:', tableResponsiveDiv.id || tableResponsiveDiv.className); // DEBUG
        });

        tableResponsiveDiv.addEventListener('mouseleave', () => {
            if (isDown) {
                // console.log('Mouse Leave - Drag ended en:', tableResponsiveDiv.id || tableResponsiveDiv.className); // DEBUG
            }
            isDown = false;
            tableResponsiveDiv.classList.remove('active-drag');
        });

        tableResponsiveDiv.addEventListener('mouseup', () => {
            if (isDown) {
                // console.log('Mouse Up - Drag ended en:', tableResponsiveDiv.id || tableResponsiveDiv.className); // DEBUG
            }
            isDown = false;
            tableResponsiveDiv.classList.remove('active-drag');
        });

        tableResponsiveDiv.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - tableResponsiveDiv.offsetLeft;
            const walk = (x - startX) * 1.5;
            tableResponsiveDiv.scrollLeft = scrollLeft - walk;
        });

        // Añadir soporte táctil para dispositivos móviles
        tableResponsiveDiv.addEventListener('touchstart', (e) => {
            isDown = true;
            tableResponsiveDiv.classList.add('active-drag');
            startX = e.touches[0].pageX - tableResponsiveDiv.offsetLeft;
            scrollLeft = tableResponsiveDiv.scrollLeft;
            // console.log('Touch Start en:', tableResponsiveDiv.id || tableResponsiveDiv.className); // DEBUG
        });

        tableResponsiveDiv.addEventListener('touchend', () => {
            if (isDown) {
                // console.log('Touch End - Drag ended en:', tableResponsiveDiv.id || tableResponsiveDiv.className); // DEBUG
            }
            isDown = false;
            tableResponsiveDiv.classList.remove('active-drag');
        });

        tableResponsiveDiv.addEventListener('touchmove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.touches[0].pageX - tableResponsiveDiv.offsetLeft;
            const walk = (x - startX) * 1.5;
            tableResponsiveDiv.scrollLeft = scrollLeft - walk;
        }, { passive: false });
    });
} else {
    console.log('Ningún elemento .table-responsive encontrado para drag-and-drop.');
}
