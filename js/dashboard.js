"use strict";

const sidebarLinks = document.querySelectorAll(".sidebar-nav a");
const contentSections = document.querySelectorAll(".content-section");
const navbarTitleElement = document.querySelector(".navbar .navbar-title"); // Este elemento no está en la imagen, el título está en el h2 de cada sección

// Función para actualizar la sección activa
function updateActiveSection(targetSectionId) {
  // Remover la clase 'active' de todos los enlaces y secciones
  sidebarLinks.forEach((item) => item.classList.remove("active"));
  contentSections.forEach((section) => section.classList.remove("active"));

  // Añadir la clase 'active' al enlace y la sección correspondiente
  const targetLink = document.querySelector(
    `.sidebar-nav a[data-section="${targetSectionId}"]`
  );
  const targetSection = document.getElementById(targetSectionId);

  if (targetLink) {
    targetLink.classList.add("active");
  }

  if (targetSection) {
    targetSection.classList.add("active");
    // Actualizar el título de la navbar (si tuvieras un elemento dedicado)
    // En este diseño, el título grande está dentro de cada sección de contenido (<h2>).
    // Si se desea el título en la navbar, habría que modificar HTML.
    // Para replicar la imagen, el título de "Overview" (o la sección activa) es el <h2> del content-section.
  }
}

sidebarLinks.forEach((link) => {
  link.addEventListener("click", function (e) {
    // Solo prevenir el default si el enlace tiene un data-section (es una navegación interna)
    if (this.dataset.section) {
      e.preventDefault();
      updateActiveSection(this.dataset.section);
    }
  });
});

// Establecer la sección inicial al cargar la página ('overview' como en la imagen)
updateActiveSection("overview");

// Lógica para el dropdown del perfil de usuario (opcional)
const userProfile = document.querySelector(".user-profile");
if (userProfile) {
  userProfile.addEventListener("click", function () {
    console.log("Menú de perfil clickeado");
    // Aquí podrías mostrar/ocultar un menú desplegable
  });
}
