// ==========================================================================
// ARCHIVO: TEMPLATE PARA INYECTAR HEADER GENERAL
// - Manejo dinámico del encabezado
// - Menús adaptativos según el rol
// - Gestión de notificaciones (hardcodeadas por ahora)
// - Cambio de temas (claro / oscuro)
// ==========================================================================

// Contenido HTML del header general que se inyecta dinámicamente en el sistema
const fullHeader = `
<header class="header">

  <div class="header-top"> 

    <div class="header-l-top">

      <a href="../pages/home.php">
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

    <div class="header-r-top">

      <span class="user-role"></span>

      <a href="../php/auth/logout.php" class="logout-button">
        <svg class="icon">
          <use href="#logout"></use>
        </svg>
        SALIR
      </a>
    </div>

  </div>
  
  <div class="header-bottom">

    <div class="header-l-bottom">

      <button class="menu-button" id="menu-button">
        <svg class="icon">
          <use href="#menu"></use>
        </svg> 
        MENÚ
      </button>

      <nav class="menu-sidebar" id="menu-sidebar">

        <div class="header-menu">
          <h2>PSIC🧠GEST</h2>

          <button class="close-menu" id="close-menu">
            &times;
          </button>

        </div>
        
        <div id="sidebar-links"></div>

      </nav>

    </div>

    <div class="header-r-bottom"></div>
    
      <div class="action-buttons">
            
        <div class="notifications-container">

          <button class="action-button" id="notification-button">
            <svg class="icon">
              <use href="#bell"></use>
            </svg>
          </button>
            
          <div class="notifications-dropdown" id="notifications-dropdown">

            <div class="header-notifications">
              <span>NOTIFICACIONES</span>
            </div>

            <div class="notifications-list" id="notifications-list">

              <div class="notification-item" data-id="1">

                <svg><use href="#notification"></use></svg>

                <div class="notification-content">
                  <p>Se ha agendado una nueva cita.</p>
                  <small>Hace 1 hora</small>

                  <div class="notification-actions">
                    <a href="notifications.php">Ver detalles</a>
                    <p>  -  </p>
                    <button class="delete-one-dropdown">Limpiar</button>
                  </div>
                </div>

              </div>

              <div class="notification-item" data-id="2">

                <svg><use href="#notification"></use></svg>

                <div class="notification-content">
                  <p>Una cita ha sido CANCELADA.</p>
                  <small>Hace 2 hora</small>

                  <div class="notification-actions">
                    <a href="notifications.php">Ver detalles</a>
                    <p>  -  </p>
                    <button class="delete-one-dropdown">Limpiar</button>
                  </div>
                </div>

              </div>

              <div class="notifications-message">
                <p>No tienes más notificaciones.</p>
              </div>

            </div>
            <div class="notifications-footer">
              <a href="notifications.php">Ver todas</a>
              <button class="delete-all" id="delete-all-dropdown">Limpiar todas</button>
            </div>

          </div>
        
        </div>

        <a href="profile.php" class="action-button">
          <svg class="icon"><use href="#user"></use></svg>
        </a>

      </div>

    </div>
    
  </div>
</header>
`;

// Inicializa el DOM
document.addEventListener("DOMContentLoaded", () => {
  const container = document.getElementById("header-container");

  if (!container) return;

  // Inyectar la estructura HTML
  container.innerHTML = fullHeader;

  // Detectar rol y configurar etiquetas básicas
  const role = window.USER_ROLE || "usuario";
  const roleLabels = {
    admin: "ADMINISTRADOR",
    psicologo: "PSICÓLOGO",
    paciente: "PACIENTE",
    usuario: "USUARIO",
  };

  // Busca dentro del contenedor el elemento que muestra el rol
  const roleEl = container.querySelector(".user-role");
  // Verifica que el elemento exista antes de intentar modificarlo
  if (roleEl) {
    // Asigna el texto que se mostrará en el elemento
    // Si encuentra una coincidencia, muestra la etiqueta correspondiente
    // Si no encuentra ninguna, muestra "USUARIO" como valor por defecto.
    roleEl.textContent = roleLabels[role] || "USUARIO";
  }

  // Restricciones estructurales específicas para Administradores
  const actionsButtons = container.querySelector(".action-buttons");

  // Remueve los botones de perfil y notificaciones que el admin no requiere
  if (role === "admin" && actionsButtons) {
    actionsButtons.remove();
  }

  // Elimina del localStorage las notificaciones borradas (solo para pruebas)
  // Esto hace que siempre vuelvan a aparecer al recargar
  localStorage.removeItem("deletedNotifs"); // ESTA LÍNEA SE PUEDE COMENTAR O DESCOMENTAR

  // Inicializa sub-módulos de eventos
  renderSidebarLinks(container, role);
  setupTheme();
  setupSidebar();
  setupNotifications();

  // Aplica el estado de notificaciones eliminadas (por si hay algo guardado)
  applyDeleted();
});

// Escucha cambios en localStorage desde otras pestañas
// Si algo cambia, vuelve a aplicar eliminaciones
window.addEventListener("storage", applyDeleted);

// ==========================================================================
// SUB-MÓDULOS DE COMPORTAMIENTO
// ==========================================================================

// Gestión del tema visual (Claro / Oscuro)
function setupTheme() {
  const lightButton = document.getElementById("light-button");
  const darkButton = document.getElementById("dark-button");
  const body = document.body;

  // Activa modo claro
  const activateLight = () => {
    body.classList.remove("dark-mode");
    localStorage.setItem("theme", "light");
    lightButton?.classList.add("active");
    darkButton?.classList.remove("active");
  };

  // Activa modo oscuro
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

// Sistema de notificaciones (hardcodeadas)
function setupNotifications() {
  const notifButton = document.getElementById("notification-button");
  const dropdown = document.getElementById("notifications-dropdown");
  const deleteAllButton = document.getElementById("delete-all-dropdown");
  const deleteAllPageButton = document.getElementById("delete-all-button");

  // Evento para abrir/cerrar el dropdown de notificaciones
  notifButton?.addEventListener("click", (e) => {
    e.stopPropagation();
    // Alterna visibilidad del dropdown
    dropdown?.classList.toggle("show");
    // Cambia estado visual del botón
    notifButton.classList.toggle("active");
  });

  // Evento para cerrar el dropdown si se hace clic fuera
  document.addEventListener("click", (e) => {
    // Si el clic no fue dentro del botón ni del dropdown
    if (
      notifButton &&
      dropdown &&
      !notifButton.contains(e.target) &&
      !dropdown.contains(e.target)
    ) {
      // Cierra el dropdown
      dropdown.classList.remove("show");
      // Quita estado activo del botón
      notifButton.classList.remove("active");
    }
  });

  // Eventos globales para limpiar todo
  const clearAllHandler = () => {
    localStorage.setItem("deletedNotifs", "ALL");
    applyDeleted();
  };

  deleteAllButton?.addEventListener("click", clearAllHandler);
  deleteAllPageButton?.addEventListener("click", clearAllHandler);

  // Eventos para botones individuales de "Limpiar"
  document.addEventListener("click", (e) => {
    // Desde el dropdown
    if (e.target.classList.contains("delete-one-dropdown")) {
      const item = e.target.closest(".notification-item");
      if (item) saveDeletedId(item.dataset.id);
    }
    // Desde la página externa de notificaciones
    if (e.target.classList.contains("delete-one")) {
      const item = e.target.closest("[data-id]");
      if (item) saveDeletedId(item.dataset.id);
    }
  });
}

// ==========================================================================
// UTILIDADES DE ALMACENAMIENTO
// ==========================================================================

// Registra un ID de notificación como eliminado en LocalStorage
function saveDeletedId(id) {
  const current = localStorage.getItem("deletedNotifs");
  if (current === "ALL") return;

  const deletedIds = JSON.parse(current) || [];
  if (!deletedIds.includes(id)) {
    deletedIds.push(id);
    localStorage.setItem("deletedNotifs", JSON.stringify(deletedIds));
    applyDeleted();
  }
}

// Sincroniza el DOM real con los registros de eliminación locales
function applyDeleted() {
  const deleted = localStorage.getItem("deletedNotifs");
  const notifList = document.getElementById("notifications-list");
  const emptyMessage = document.getElementById("notifications-message");

  // Escenario 1: El usuario barrió con todas las notificaciones de golpe
  if (deleted === "ALL") {
    document
      .querySelectorAll(".notification-item, .notification-card")
      .forEach((el) => el.remove());
    if (emptyMessage) emptyMessage.style.display = "block";
    return;
  }

  // Escenario 2: El usuario eliminó elementos individuales
  const idsToEliminate = JSON.parse(deleted) || [];
  idsToEliminate.forEach((id) => {
    document.querySelectorAll(`[data-id="${id}"]`).forEach((el) => {
      // Elimina la card de la vista si existe, o el item directo del dropdown
      const target =
        el.closest(".notification-card") || el.closest(".notification-item");
      target?.remove();
    });
  });

  // Control dinámico de lista vacía: si no quedan items visibles, muestra el aviso
  if (notifList) {
    const activeItems = notifList.querySelectorAll(".notification-item");
    if (activeItems.length === 0 && emptyMessage) {
      emptyMessage.style.display = "block";
    }
  }
}
