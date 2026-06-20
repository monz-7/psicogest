// ==========================================================================
// ARCHIVO: MODAL DE CAMBIO DE CONTRASEÑA DEL ADMIN
// Usa: change_password.js (debe cargarse antes)
// ==========================================================================

document.addEventListener("DOMContentLoaded", function () {
  // --- Elementos del modal ---
  const modal = document.getElementById("password-modal");
  const successModal = document.getElementById("password-success-modal");
  const errorBox = document.getElementById("modal-error-box");
  const openBtn = document.getElementById("open-modal-button");
  const cancelBtn = document.getElementById("cancel-changes-button");
  const saveBtn = document.getElementById("save-changes-button");
  const closeSuccessBtn = document.getElementById("btn-close-success-modal");

  if (!modal || !openBtn) return;

  // --- IDs de los campos del modal ---
  const FIELD_IDS = {
    current: "modal-current-password",
    new: "modal-new-password",
    repeat: "modal-repeat-password",
  };

  // ==========================================================================
  // AUXILIARES
  // ==========================================================================

  // Muestra inputs con errores
  function showError(message, affectedFields = []) {
    errorBox.textContent = message;
    errorBox.classList.remove("hidden");

    affectedFields.forEach((fieldId) => {
      // Busca directamente el contenedor usando el ID exacto del campo
      document
        .getElementById(`container-${fieldId}`)
        ?.classList.add("input-error");
    });
  }

  // Limpia inputs con errores
  function clearErrors() {
    errorBox.textContent = "";
    errorBox.classList.add("hidden");

    Object.values(FIELD_IDS).forEach((fieldId) => {
      document
        .getElementById(`container-${fieldId}`)
        ?.classList.remove("input-error");
    });
  }

  // Cierra el modal
  function closeModal() {
    modal.classList.add("hidden");
    clearErrors();
    Object.values(FIELD_IDS).forEach((fieldId) => {
      const input = document.getElementById(fieldId);
      if (input) input.value = "";
    });
  }

  // Controla el estado visual y de interacción del botón de guardar
  function toggleSaveButton(disable) {
    saveBtn.disabled = disable;
    saveBtn.style.opacity = disable ? "0.6" : "1";
  }

  // ==========================================================================
  // INSTANCIA DE CHANGE_PASSWORD
  // ==========================================================================
  const passwordManager = initChangePassword({
    fields: FIELD_IDS,
    apiPath: "../php/auth/change_password.php",
    onSuccess: () => {
      closeModal();
      successModal.classList.remove("hidden");
      setTimeout(() => successModal.classList.add("hidden"), 5000);
    },
    onError: (message, fields) => showError(message, fields),
    onLoading: (isLoading) => toggleSaveButton(isLoading),
  });

  // ==========================================================================
  // EVENTOS
  // ==========================================================================

  // Abrir el modal
  openBtn.addEventListener("click", (e) => {
    e.preventDefault();
    clearErrors();
    modal.classList.remove("hidden");
  });

  // Cancelar cambios
  cancelBtn.addEventListener("click", () => closeModal());

  // Guardar cambios
  saveBtn.addEventListener("click", () =>
    passwordManager.processPasswordChange(),
  );

  // Cerrar el mensaje de éxito
  closeSuccessBtn.addEventListener("click", () => {
    successModal.classList.add("hidden");
  });

  // Cerrar el modal al hacer clic fuera del contenido
  modal.addEventListener("click", (e) => {
    if (e.target === modal) closeModal();
  });
});
