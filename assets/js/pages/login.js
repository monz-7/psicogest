// ==========================================================================
// ARCHIVO: CONTROL DEL LOGIN
// ==========================================================================

document.addEventListener("DOMContentLoaded", () => {
  // Inicializa únicamente la persistencia tras el ciclo post-submit
  restorePreviousSession();
});

// ==========================================================================
// RELLENO DE DATOS Y GESTIÓN DE ERRORES POST-SUBMIT
// ==========================================================================
function restorePreviousSession() {
  const oldRole = window.oldRole || "";
  const errorFields = window.errorFields || {};

  // Restaura el rol previamente seleccionado tras un fallo de validación
  if (oldRole) {
    const selectedText = document.querySelector(".dropdown-text");
    const selectedBox = document.querySelector(".dropdown-selected");
    const hiddenRoleInput = document.getElementById("role");

    let label = "";
    switch (oldRole) {
      case "admin":
        label = "ADMINISTRADOR";
        break;
      case "psicologo":
        label = "PSICÓLOGO";
        break;
      case "paciente":
        label = "PACIENTE";
        break;
    }

    if (selectedText) selectedText.textContent = label;
    if (selectedBox) selectedBox.classList.add("bold");

    // IMPORTANTE: Le añade al contenedor la clase "bold" para que el CSS sepa que ya hay algo seleccionado
    if (selectedBox) selectedBox.dataset.value = oldRole;
    if (hiddenRoleInput) hiddenRoleInput.value = oldRole;
  }

  // Inyecta visualmente estados de error (pone el borde rojo si faltó el rol)
  if (errorFields.role) {
    const roleDropdown = document.querySelector("#role-dropdown");
    if (roleDropdown) {
      roleDropdown.classList.add("input-error");
    }
  }
}
