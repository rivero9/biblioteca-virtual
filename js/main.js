"use strict";

// Referencias a elementos del DOM para el modal
const bookModal = document.getElementById("bookModal");
const modalBookTitle = document.getElementById("modalBookTitle");
const modalBookAuthor = document.getElementById("modalBookAuthor");
const modalBookDescription = document.getElementById("modalBookDescription");

/**
 * Abre el modal de detalles del libro con la información proporcionada.
 * @param {string} title - El título del libro.
 * @param {string} author - El autor del libro.
 * @param {string} description - La descripción corta del libro.
 */
function openBookModal(title, author, description) {
  modalBookTitle.innerText = title;
  modalBookAuthor.innerText = "Autor: " + author;
  modalBookDescription.innerText = description;

  // Mostrar el modal
  bookModal.style.display = "flex";
  document.body.style.overflowY = "hidden";
}

/**
 * Cierra el modal de detalles del libro.
 */
function closeBookModal() {
  bookModal.style.display = "none";
  document.body.style.overflowY = "auto";
}

// Asignar evento al botón de cierre del modal
const closeButton = bookModal.querySelector(".close-button");
if (closeButton) {
  closeButton.addEventListener("click", closeBookModal);
}