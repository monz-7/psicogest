// ==========================================================================
// ARCHIVO: LÓGICA DEL MENU-SIDEBAR
// ==========================================================================

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
      href: "../pages/admin_psychologists.php?tab=tab1",
    },
    {
      text: "CERRAR SESIÓN",
      icon: "logout",
      href: "../php/auth/logout.php",
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
      href: "../pages/appointments.php?tab=tab1",
    },
    {
      text: "HISTORIAL DE CITAS",
      icon: "history",
      href: "../pages/appointments.php?tab=tab2",
    },
    {
      text: "DIRECTORIO",
      icon: "directory",
      href: "../pages/directory.php",
    },
    {
      text: "CERRAR SESIÓN",
      icon: "logout",
      href: "../php/auth/logout.php",
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
      href: "../pages/appointments.php?tab=tab1",
    },
    {
      text: "HISTORIAL DE CITAS",
      icon: "history",
      href: "../pages/appointments.php?tab=tab2",
    },
    {
      text: "PACIENTES",
      icon: "patients",
      href: "../pages/patients.php?tab=tab1",
    },
    {
      text: "ASISTENCIA",
      icon: "attendance",
      href: "../pages/patients.php?tab=tab2",
    },
    {
      text: "CERRAR SESIÓN",
      icon: "logout",
      href: "../php/auth/logout.php",
    },
  ],
};

// Renderiza los enlaces en el sidebar
function renderSidebarLinks(container, role) {
  const sidebarLinks = container.querySelector("#sidebar-links");
  if (sidebarLinks && menus[role]) {
    sidebarLinks.innerHTML = menus[role]
      .map(
        (item) => `
        <a href="${item.href}">
          <svg class="icon-menu"><use href="#${item.icon}"></use></svg>
          ${item.text}
        </a>`,
      )
      .join("");
  }
}

// Organiza el sidebar
function setupSidebar() {
  const menuButton = document.getElementById("menu-button");
  const closeMenu = document.getElementById("close-menu");
  const sidebar = document.getElementById("menu-sidebar");

  menuButton?.addEventListener("click", () => sidebar?.classList.add("active"));
  closeMenu?.addEventListener("click", () =>
    sidebar?.classList.remove("active"),
  );

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
