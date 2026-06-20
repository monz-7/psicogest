// ==========================================================================
// ARCHIVO: REGISTRO DE PSICÓLOGOS
// ==========================================================================

// ==========================================================================
// LÓGICA DE ESPECIALIDADES (CUSTOM DROPDOWN & CHIPS)
// ==========================================================================

// Almacena objetos { id, name } para facilitar el manejo
let selectedSpecialties = [];

const specialtyDropdown = document.getElementById("specialty-dropdown");
const dropdownSelected = specialtyDropdown.querySelector(".dropdown-selected");
const dropdownText = specialtyDropdown.querySelector(".dropdown-text");
const dropdownOptions = specialtyDropdown.querySelector(".dropdown-options");

// Escucha los clicks en las opciones del dropdown para "pre-seleccionar" antes de agregar
dropdownOptions.addEventListener("click", (e) => {
  const li = e.target.closest("li");
  if (!li) return;

  e.stopPropagation(); // Evita duplicar eventos

  // Guarda el ID en el dataset del texto y actualiza la interfaz
  dropdownText.textContent = li.textContent;
  dropdownText.dataset.value = li.dataset.value;
  dropdownSelected.classList.add("bold");

  // Cierra el menú al seleccionar la opción
  specialtyDropdown.classList.remove("open");
  dropdownOptions.style.display = "none";
});

// Evento para añadir la especialidad pre-seleccionada al listado de chips
document.getElementById("add-specialty").addEventListener("click", () => {
  const id = dropdownText.dataset.value;
  const name = dropdownText.textContent;

  // Valida que haya una opción seleccionada válida y que no se haya agregado ya
  if (!id || id === "" || name === "ESPECIALIDADES") return;
  if (selectedSpecialties.some((item) => item.id === id)) return;

  // Añadir al arreglo de seleccionados
  selectedSpecialties.push({ id: id, name: name });

  // Resetear el texto del dropdown por defecto
  dropdownText.textContent = "ESPECIALIDADES";
  dropdownText.removeAttribute("data-value");
  dropdownSelected.classList.remove("bold");

  // Renderiza chips y actualizar visibilidad del dropdown
  renderSpecialties();
});

// Renderiza los CHIPS visuales en el contenedor y mapea el JSON al input oculto para PHP
function renderSpecialties() {
  const container = document.getElementById("selected-specialties");
  container.innerHTML = "";

  selectedSpecialties.forEach((specialty) => {
    const chip = document.createElement("div");
    chip.className = "specialty-chip";
    chip.style.display = "inline-flex";
    chip.style.alignItems = "center";
    chip.style.margin = "4px";

    chip.innerHTML = `
      <span>${specialty.name}</span>
      <button type="button" class="remove-chip-button" data-id="${specialty.id}">×</button>
    `;

    container.appendChild(chip);
  });

  // Asigna eventos de eliminación a los botones de los chips recién creados
  container.querySelectorAll(".remove-chip-button").forEach((button) => {
    button.addEventListener("click", (e) => {
      const idToRemove = e.target.dataset.id;
      removeSpecialty(idToRemove);
    });
  });

  // Actualiza el input hidden con los IDs correspondientes para procesar en PHP
  const input = document.getElementById("specialties-input");
  const idsOnly = selectedSpecialties.map((item) => item.id);
  input.value = idsOnly.length > 0 ? JSON.stringify(idsOnly) : "[]";

  // Actualiza qué elementos se muestran u ocultan en el Dropdown
  updateDropdownOptionsVisibility();
}

// Elimina una especialidad del listado por su ID
function removeSpecialty(id) {
  selectedSpecialties = selectedSpecialties.filter((item) => item.id !== id);
  renderSpecialties();
}

// Oculta o muestra los <li> del dropdown basándose en la selección actual para evitar duplicados
function updateDropdownOptionsVisibility() {
  const listItems = dropdownOptions.querySelectorAll("li");

  listItems.forEach((li) => {
    const id = li.dataset.value;
    const isSelected = selectedSpecialties.some((item) => item.id === id);

    if (isSelected) {
      li.style.display = "none";
    } else {
      li.style.display = ""; // Vuelve a su estado natural
    }
  });
}

// ==========================================================================
// PROCESAMIENTO PRINCIPAL DEL FORMULARIO Y PERSISTENCIA
// ==========================================================================
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("psychologist-form");
  const successModal = document.getElementById("success-modal");
  const modalBody = document.getElementById("modal-body");
  const closeModalBtn = document.getElementById("close-modal");
  const copyBtn = document.getElementById("copy-button");

  let textToCopy = "";

  // ------------------------------------------------------------------------
  // ENVÍO ASÍNCRONO DEL FORMULARIO (AJAX / FETCH)
  // ------------------------------------------------------------------------
  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    // === VALIDA Y SINCRONIZA EL INPUT DE TELÉFONO INTERNACIONAL ===
    const phoneInputField = document.querySelector("#phone-input");
    const hiddenPhone = document.querySelector("#phone-full");
    const phoneValue = (
      window.getFullPhoneNumber?.() ||
      phoneInputField?.value ||
      ""
    ).trim();

    if (hiddenPhone && typeof window.getFullPhoneNumber === "function") {
      hiddenPhone.value = window.getFullPhoneNumber();
    }

    if (!phoneValue) {
      alert("El número de teléfono es obligatorio.");
      return;
    }

    if (typeof window.isValidPhoneNumber === "function") {
      if (!window.isValidPhoneNumber()) {
        alert("Número de teléfono inválido");
        return;
      }
    }

    if (hiddenPhone && typeof window.getFullPhoneNumber === "function") {
      hiddenPhone.value = window.getFullPhoneNumber();
    }

    // === VALIDACIÓN DEL CORREO ELECTRÓNICO ===
    const emailInput = document.getElementById("email-input");
    const emailValue = (emailInput?.value || "").trim();

    if (!emailValue) {
      alert("El correo es obligatorio.");
      return;
    }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailValue)) {
      alert("Correo inválido.");
      return;
    }

    // === ENVÍO DE DATOS A LA API PHP ===
    const formData = new FormData(form);

    try {
      const response = await fetch(
        "../php/psychologists/insert_psychologist.php",
        {
          method: "POST",
          body: formData,
        },
      );

      const data = await response.json();

      if (data.success) {
        const fullName = `${data.psychologist.names} ${data.psychologist.surnames}`;

        textToCopy = `Usuario: ${data.credentials.username}\nCorreo: ${data.credentials.email}\nContraseña: ${data.credentials.password}`;

        if (modalBody) {
          modalBody.innerHTML = `
            <p class="modal-message">
              Psicólogo <strong>${fullName}</strong> registrado con éxito.
            </p>
            <div class="credentials-data">
              <strong>Usuario:</strong> ${data.credentials.username}<br>
              <strong>Correo:</strong> ${data.credentials.email}<br>
              <strong>Contraseña:</strong> ${data.credentials.password}
            </div>
          `;
        }

        if (successModal) successModal.classList.remove("hidden");
        form.reset();
        // Oculta el modal automáticamente después de 15 segundos
        setTimeout(() => {
          successModal?.classList.add("hidden");
        }, 15000);
      } else {
        alert(data.message || "Error desconocido");
      }
    } catch (err) {
      console.error(err);
      alert("Error inesperado al enviar el formulario");
    }
  });

  // ------------------------------------------------------------------------
  // ACCIÓN DE COPIADO EN PORTAPAPELES
  // ------------------------------------------------------------------------
  copyBtn?.addEventListener("click", () => {
    if (!textToCopy) return;

    navigator.clipboard.writeText(textToCopy).then(() => {
      const old = copyBtn.innerText;
      copyBtn.innerText = "COPIADO";

      setTimeout(() => {
        copyBtn.innerText = old;
      }, 2000); // Tiempo que tarda en cambiar el texto dentro del botón de copiado
    });
  });

  // ------------------------------------------------------------------------
  // CIERRE DE VENTANA MODAL (REFRESH)
  // ------------------------------------------------------------------------
  closeModalBtn?.addEventListener("click", () => {
    successModal?.classList.add("hidden");
    window.location.reload();
  });
});
