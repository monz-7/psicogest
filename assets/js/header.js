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

      <a href="../php/logout.php" class="logout-button">
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

        <div class="logo-close-menu">
          <h2>PSIC🧠GEST</h2>

          <button class="close-menu" id="close-menu">
            &times;
          </button>

        </div>
        
        <div id="sidebar-links"></div>

      </nav>

    </div>

    <div class="header-r-bottom"></div>
    
      <div class="actions-buttons">
            
        <div class="notif-container">

          <button class="action-button" id="notif-button">
            <svg class="icon">
              <use href="#bell"></use>
            </svg>
          </button>
            
          <div class="notif-dropdown" id="notif-dropdown">

            <div class="notif-header">
              <span>NOTIFICACIONES</span>
            </div>

            <div class="notif-list" id="notif-list">

              <div class="notif-item" data-id="1">

                <svg><use href="#notification"></use></svg>

                <div class="notif-content-dropdown">
                  <p>Se ha agendado una nueva cita.</p>
                  <small>Hace 1 hora</small>

                  <div class="notif-actions-dropdown">
                    <a href="notifications.php">Ver detalles</a>
                    <p>  -  </p>
                    <button class="delete-one-dropdown">Limpiar</button>
                  </div>
                </div>

              </div>

              <div class="notif-item" data-id="2">

                <svg><use href="#notification"></use></svg>

                <div class="notif-content-dropdown">
                  <p>Una cita ha sido CANCELADA.</p>
                  <small>Hace 2 hora</small>

                  <div class="notif-actions-dropdown">
                    <a href="notifications.php">Ver detalles</a>
                    <p>  -  </p>
                    <button class="delete-one-dropdown">Limpiar</button>
                  </div>
                </div>

              </div>

              <div class="notif-empty-dropdown">
                <p>No tienes más notificaciones.</p>
              </div>

            </div>

            <div class="notif-footer">
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
// ==========================================================================
// Configuración de los menús dinámicos navegables según el rol del usuario
// ==========================================================================
const menus = {
  admin: [
    {
      text: "INICIO",
      icon: "home",
      href: "../pages/home.php",
    },
    {
      text: "PACIENTES",
      icon: "patients",
      href: "../pages/admin_patients.php",
    },
    {
      text: "PSICÓLOGOS",
      icon: "human-brain",
      href: "../pages/admin_psychologists.php",
    },
    {
      text: "CERRAR SESIÓN",
      icon: "logout",
      href: "../php/logout.php",
    },
  ],

  paciente: [
    {
      text: "INICIO",
      icon: "home",
      href: "../pages/home.php",
    },
    {
      text: "PERFIL",
      icon: "user",
      href: "../pages/profile.php",
    },
    {
      text: "NOTIFICACIONES",
      icon: "bell",
      href: "../pages/notifications.php",
    },
    {
      text: "AGENDAMIENTO",
      icon: "new-appointment",
      href: "../pages/scheduling.php",
    },
    {
      text: "PRÓXIMAS CITAS",
      icon: "appointments",
      href: "../pages/appointments.php",
    },
    {
      text: "HISTORIAL DE CITAS",
      icon: "history",
      href: "../pages/appointments.php",
    },
    {
      text: "DIRECTORIO",
      icon: "directory",
      href: "../pages/directory.php",
    },
    {
      text: "CERRAR SESIÓN",
      icon: "logout",
      href: "../php/logout.php",
    },
  ],

  psicologo: [
    {
      text: "INICIO",
      icon: "home",
      href: "../pages/home.php",
    },
    {
      text: "PERFIL",
      icon: "user",
      href: "../pages/profile.php",
    },
    {
      text: "NOTIFICACIONES",
      icon: "bell",
      href: "../pages/notifications.php",
    },
    {
      text: "HORARIOS",
      icon: "time",
      href: "../pages/agenda.php",
    },
    {
      text: "PRÓXIMAS CITAS",
      icon: "appointments",
      href: "../pages/appointments.php",
    },
    {
      text: "HISTORIAL DE CITAS",
      icon: "history",
      href: "../pages/appointments.php",
    },
    {
      text: "PACIENTES",
      icon: "patients",
      href: "../pages/patients.php",
    },
    {
      text: "ASISTENCIA",
      icon: "attendance",
      href: "../pages/patients.php",
    },
    {
      text: "CERRAR SESIÓN",
      icon: "logout",
      href: "../php/logout.php",
    },
  ],
};

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

  // Renderiza los links del sidebar mapeados por rol
  const sidebarLinks = container.querySelector("#sidebar-links");

  if (sidebarLinks && menus[role]) {
    sidebarLinks.innerHTML = menus[role]
      .map(
        (item) => `
          <a href="${item.href}">
            <svg class="icon-menu">
              <use href="#${item.icon}"></use>
            </svg>
            ${item.text}
          </a>
        `,
      )
      .join("");
  }

  // Restricciones estructurales específicas para Administradores
  const actionsButtons = container.querySelector(".actions-buttons");

  // Remueve los botones de perfil y notificaciones que el admin no requiere
  if (role === "admin" && actionsButtons) {
    actionsButtons.remove();
  }

  // Elimina del localStorage las notificaciones borradas (solo para pruebas)
  // Esto hace que siempre vuelvan a aparecer al recargar
  localStorage.removeItem("deletedNotifs"); // ESTA LÍNEA SE PUEDE COMENTAR O DESCOMENTAR

  // Inicializa sub-módulos de eventos
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

// Control del sidebar (menú lateral)

function setupSidebar() {
  const menuButton = document.getElementById("menu-button");
  const closeMenu = document.getElementById("close-menu");
  const sidebar = document.getElementById("menu-sidebar");

  // Evento para abrir el menú
  menuButton?.addEventListener("click", () => sidebar?.classList.add("active"));
  closeMenu?.addEventListener("click", () =>
    sidebar?.classList.remove("active"),
  );

  // Evento para cerrar el menú
  document.addEventListener("click", (e) => {
    if (
      sidebar &&
      menuButton &&
      !menuButton.contains(e.target) &&
      !sidebar.contains(e.target)
    ) {
      sidebar.classList.remove("active");
    }
  });
}

// Sistema de notificaciones (hardcodeadas)
function setupNotifications() {
  const notifButton = document.getElementById("notif-button");
  const dropdown = document.getElementById("notif-dropdown");
  const deleteAllButton = document.getElementById("delete-all-dropdown");
  const deleteAllPageButton = document.getElementById("del-all-button");

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
      const item = e.target.closest(".notif-item");
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
  const notifList = document.getElementById("notif-list");
  const emptyMessage = document.getElementById("notif-empty-message");

  // Escenario 1: El usuario barrió con todas las notificaciones de golpe
  if (deleted === "ALL") {
    document
      .querySelectorAll(".notif-item, .card-notif")
      .forEach((el) => el.remove());
    if (emptyMessage) emptyMessage.style.display = "block";
    return;
  }

  // Escenario 2: El usuario eliminó elementos individuales
  const idsToEliminate = JSON.parse(deleted) || [];
  idsToEliminate.forEach((id) => {
    document.querySelectorAll(`[data-id="${id}"]`).forEach((el) => {
      // Elimina la card de la vista si existe, o el item directo del dropdown
      const target = el.closest(".card-notif") || el.closest(".notif-item");
      target?.remove();
    });
  });

  // Control dinámico de lista vacía: si no quedan items visibles, muestra el aviso
  if (notifList) {
    const activeItems = notifList.querySelectorAll(".notif-item");
    if (activeItems.length === 0 && emptyMessage) {
      emptyMessage.style.display = "block";
    }
  }
}
