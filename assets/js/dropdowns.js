// ==========================================================================
// ARCHIVO: LÓGICA GLOBAL Y UNIFICADA DE TODOS LOS DROPDOWNS DEL SISTEMA
// ==========================================================================

// ==========================================================================
// DISPARADOR DE INICIALIZACIÓN
// ==========================================================================
document.addEventListener("DOMContentLoaded", () => {
  initGlobalDropdowns();
});

// ==========================================================================
// CONTROLADOR DE ESTADOS
// ==========================================================================
function initGlobalDropdowns() {
  const allDropdowns = document.querySelectorAll(".dropdown, .dropdown-filter");

  allDropdowns.forEach((dropdown) => {
    const selected = dropdown.querySelector(
      ".dropdown-selected, .dropdown-selected-month",
    );
    const options = dropdown.querySelector(
      ".dropdown-options, .dropdown-months-options",
    );

    if (!selected || !options) return;

    // ------------------------------------------------------------------------
    // EVENTO: ABRIR / CERRAR
    // ------------------------------------------------------------------------
    selected.addEventListener("click", (e) => {
      e.stopPropagation();

      // Si el componente está deshabilitado estáticamente, bloquea la acción
      if (dropdown.classList.contains("disabled")) return;

      const isOpen = dropdown.classList.contains("open");

      // Cierre mutuo: Oculta cualquier otro dropdown abierto antes de desplegar el actual
      allDropdowns.forEach((d) => {
        if (d !== dropdown) {
          d.classList.remove("open");
          const opts = d.querySelector(
            ".dropdown-options, .dropdown-months-options",
          );
          if (opts) opts.style.display = "none";
        }
      });

      // Cambio de estados visuales del dropdown actual
      if (isOpen) {
        dropdown.classList.remove("open");
        options.style.display = "none";
      } else {
        dropdown.classList.add("open");
        options.style.display = "block";
      }
    });

    // ------------------------------------------------------------------------
    // EXCLUSIÓN DE CONTROL DE DATOS PARA COMPONENTES DINÁMICOS
    // ------------------------------------------------------------------------
    // El calendario interactivo (agenda) y las especialidades manejan su propia lógica
    const isCustomComponent =
      dropdown.id === "month-dropdown" ||
      dropdown.id === "day-dropdown" ||
      dropdown.id === "specialty-dropdown";

    if (isCustomComponent) return;

    // ------------------------------------------------------------------------
    // EVENTO: SELECCIÓN DE OPCIONES ESTÁNDAR
    // ------------------------------------------------------------------------
    attachDropdownItemsEvents(dropdown, options, selected);
  });

  // ------------------------------------------------------------------------
  // EVENTO GLOBAL DE CIERRE
  // ------------------------------------------------------------------------
  document.addEventListener("mousedown", (e) => {
    allDropdowns.forEach((dropdown) => {
      if (!dropdown.contains(e.target)) {
        dropdown.classList.remove("open");
        const opts = dropdown.querySelector(
          ".dropdown-options, .dropdown-months-options",
        );
        if (opts) opts.style.display = "none";
      }
    });
  });
}

// ==========================================================================
// VINCULACIÓN DINÁMICA DE OPCIONES E INPUTS OCULTOS
// ==========================================================================

// Vincula los eventos de clic a los elementos <li> de un dropdown estándar
function attachDropdownItemsEvents(dropdown, options, selected) {
  const text = dropdown.querySelector(".dropdown-text, .dropdown-filter-text");
  const listItems = options.querySelectorAll("li");

  listItems.forEach((option) => {
    if (option.dataset.hasDropdownEvent) return;
    option.dataset.hasDropdownEvent = "true";

    option.addEventListener("click", (e) => {
      e.stopPropagation();

      const value = option.dataset.value;
      const html = option.innerHTML;

      // Sincronización visual del componente
      if (text) {
        text.innerHTML = html;
      }
      selected.classList.add("bold");
      selected.dataset.value = value;

      // --- Sincronización con inputs ocultos nativos para persistencia PHP ---
      const cleanId = dropdown.id
        .replace("-dropdown", "")
        .replace("-filter", "");
      let hiddenInput = document.getElementById(cleanId);

      // Fallback: Si no coincide por ID directo, busca el input de tipo hidden más cercano
      if (!hiddenInput && dropdown.parentElement) {
        hiddenInput = dropdown.parentElement.querySelector(
          'input[type="hidden"]',
        );
      }

      if (hiddenInput) {
        hiddenInput.value = value;
      }

      // Cierre del contenedor tras la selección
      dropdown.classList.remove("open");
      options.style.display = "none";

      // Disparador Callback opcional para dependencias de scripts externos
      if (typeof window.onDropdownSelect === "function") {
        window.onDropdownSelect(dropdown.id, value);
      }
    });
  });
}

// Exportación explícita al contexto global (Requerido por módulos asíncronos externos)
window.attachDropdownItemsEvents = attachDropdownItemsEvents;
