// ==========================================================================
// ARCHIVO: MODAL PARA EDITAR LA CONFIGURACIÓN GLOBAL (ADMIN)
// ==========================================================================

const modal = document.getElementById("config-modal");
const modalTitle = document.getElementById("config-modal-title");
const modalIcon = document.getElementById("config-modal-icon");
const modalInput = document.getElementById("config-modal-input");
const modalError = document.getElementById("config-modal-error");
const cancelButton = document.getElementById("config-cancel-button");
const saveButton = document.getElementById("config-save-button");
// Elemento para mostrar mensajes de éxito
const successBox = document.getElementById("config-success-box");

// Formatea los precios con separador de miles (pone .)
function formatPrice(value) {
  const digits = String(value).replace(/\D/g, "");

  return digits ? Number(digits).toLocaleString("es-CO") : "";
}

// Guarda qué ajuste se está editando ("appointment_price" o "clinic_address")
let currentItem = null;

// Abre el modal con los datos del botón que se presionó
function openModal(button) {
  currentItem = button.dataset.item;

  modalTitle.textContent = `PSIC🧠GEST • ${button.dataset.title}`;
  modalIcon.setAttribute("href", button.dataset.icon);
  modalInput.type =
    currentItem === "appointment_price" ? "text" : button.dataset.type;

  modalInput.inputMode =
    currentItem === "appointment_price" ? "numeric" : "text";
  modalInput.placeholder = button.dataset.placeholder;
  modalInput.value = "";
  modalError.classList.add("hidden");
  modal.classList.remove("hidden");
  modalInput.focus();
}

function closeModal() {
  modal.classList.add("hidden");
  currentItem = null;
}

// Muestra el mensaje de éxito y lo oculta después de 5 segundos
function showSuccess(message) {
  successBox.textContent = message;
  successBox.classList.remove("hidden");

  setTimeout(() => {
    successBox.classList.add("hidden");
  }, 5000);
}

// Agrega automáticamente los separadores de miles al escribir la tarifa
modalInput.addEventListener("input", () => {
  if (currentItem === "appointment_price") {
    const digits = modalInput.value.replace(/\D/g, "");

    modalInput.value = digits ? Number(digits).toLocaleString("es-CO") : "";
  }
});

// Cada botón "Cambiar" abre el modal
document.querySelectorAll(".edit-config-button").forEach((button) => {
  button.addEventListener("click", () => openModal(button));
});

// Cerrar: botón cancelar, clic en el fondo oscuro, o tecla Escape
cancelButton.addEventListener("click", closeModal);

modal.addEventListener("click", (e) => {
  if (e.target === modal) closeModal();
});

document.addEventListener("keydown", (e) => {
  if (e.key === "Escape" && !modal.classList.contains("hidden")) closeModal();
});

// Envía los datos para el guardado
saveButton.addEventListener("click", async () => {
  let value = modalInput.value.trim();

  // Para la tarifa, elimina los puntos antes de enviarla al servidor
  if (currentItem === "appointment_price") {
    value = value.replace(/\./g, "");
  }

  if (value === "") {
    showError("Este campo no puede estar vacío.");
    return;
  }

  saveButton.disabled = true;

  try {
    const response = await fetch("../php/admin/update_config.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ item: currentItem, value }),
    });

    const result = await response.json();

    if (!result.success) {
      showError(result.message || "No se pudo guardar el cambio.");
      return;
    }

    // Guarda el ajuste que se acaba de modificar
    const updatedItem = currentItem;

    // Actualiza la tarjeta y el botón
    const elementId = updatedItem.replace("_", "-");

    document.getElementById(`value-${elementId}`).textContent =
      updatedItem === "appointment_price"
        ? formatPrice(result.value)
        : result.value;

    document.querySelector(
      `.edit-config-button[data-item="${updatedItem}"]`,
    ).dataset.value = result.value;

    // Cierra el modal de edición
    closeModal();

    // Muestra el mensaje de éxito
    if (updatedItem === "appointment_price") {
      showSuccess("La tarifa de la consulta se ha actualizado correctamente.");
    } else {
      showSuccess(
        "La dirección del consultorio se ha actualizado correctamente.",
      );
    }
  } catch (error) {
    console.error(error);
    showError("Error de conexión con el servidor. Intenta de nuevo.");
  } finally {
    saveButton.disabled = false;
  }
});

function showError(message) {
  modalError.textContent = message;
  modalError.classList.remove("hidden");
}
