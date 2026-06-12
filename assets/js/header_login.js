// ==========================================================================
// ARCHIVO: TEMPLATE DEL HEADER EXCLUSIVO DEL LOGIN
// Incluye únicamente el logotipo y el control de temas (claro/oscuro)
// ==========================================================================

// Contenido HTML del header específico para la página de login
const loginHeader = `
<header class="header-login">
  <div class="header-container">

    <a href="login.php">
      <h1>PSIC🧠GEST</h1>
    </a>

    <div class="mode-buttons">
      <button class="mode-button" id="light-button">
        <svg class="icon-light">
          <use href="#light"></use>
        </svg>
      </button>

      <button class="mode-button" id="dark-button">
        <svg class="icon-dark">
          <use href="#dark"></use>
        </svg>
      </button>
    </div>

  </div>
</header>
`;

// Espera a que todo el DOM (HTML) esté completamente cargado antes de ejecutarse
document.addEventListener("DOMContentLoaded", () => {
  // Busca en el DOM el contenedor donde se inyectará el header
  const container = document.getElementById("header-container");

  // Si no existe el contenedor, corta la ejecución para evitar errores de tipo "null"
  if (!container) return;
  // Inserta el contenido HTML dentro del contenedor
  container.innerHTML = loginHeader;
  // Inicializa el sub-módulo de control del tema visual
  setupTheme();
});

// ==========================================================================
// SUB-MÓDULOS DE COMPORTAMIENTO
// ==========================================================================

// Gestión del tema visual (Claro / Oscuro) - Sincronizado con el sistema general
function setupTheme() {
  const lightButton = document.getElementById("light-button");
  const darkButton = document.getElementById("dark-button");
  const body = document.body;

  // Función interna para activar el modo claro
  const activateLight = () => {
    body.classList.remove("dark-mode");
    localStorage.setItem("theme", "light");
    lightButton?.classList.add("active");
    darkButton?.classList.remove("active");
  };

  // Función interna para activar el modo oscuro
  const activateDark = () => {
    body.classList.add("dark-mode");
    localStorage.setItem("theme", "dark-mode");
    darkButton?.classList.add("active");
    lightButton?.classList.remove("active");
  };

  // Añade los eventos al hacer click en cada botón de modo
  lightButton?.addEventListener("click", activateLight);
  darkButton?.addEventListener("click", activateDark);

  // Carga de preferencia guardada
  if (localStorage.getItem("theme") === "dark-mode") {
    // Aplica modo oscuro
    activateDark();
  } else {
    // Si no, marca el modo claro como activo
    activateLight();
  }
}
