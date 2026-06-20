// =================================================================================
// ARCHIVO: LÓGICA GLOBAL DE VISIBILIDAD DE CONTRASEÑAS (BOTÓN MOSTRAR U OCULTAR)
// Escucha y controla automáticamente cualquier botón de alternancia de contraseña
// que comparta la clase ".toggle-password" en todo el sistema.
// =================================================================================

document.addEventListener("DOMContentLoaded", () => {
  initGlobalPasswordToggle();
});

function initGlobalPasswordToggle() {
  const toggleButtons = document.querySelectorAll(".toggle-password");

  toggleButtons.forEach((button) => {
    button.addEventListener("click", (e) => {
      e.preventDefault(); // Evita comportamientos inesperados si está dentro de un formulario

      // Busca el contenedor común del input relacionado (.field-input)
      const container = button.closest(".field-input");
      if (!container) return;

      // Obtiene los elementos internos requeridos
      const input = container.querySelector("input");
      const icon = button.querySelector("use");
      if (!input || !icon) return;

      // Determina el estado actual y lo invierte
      const isPassword = input.type === "password";
      input.type = isPassword ? "text" : "password";

      // Intercambia el identificador del recurso SVG (ID del sprite de iconos)
      icon.setAttribute("href", isPassword ? "#eye-hidden" : "#eye");

      // Modifica la propiedad de accesibilidad aria-label
      button.setAttribute(
        "aria-label",
        isPassword ? "Ocultar contraseña" : "Mostrar contraseña",
      );
    });
  });
}
