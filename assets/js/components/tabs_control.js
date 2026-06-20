// ==========================================================================
// ARCHIVO: LÓGICA PARA EL CONTROL DE PESTAÑAS (TABS)
// ==========================================================================

const tabButtons = document.querySelectorAll(".tab-button");
const tabContents = document.querySelectorAll(".tab-content");

const storageKey = `activeTab_${window.location.pathname}`;
const defaultTab = "tab1";

// Función única para activar pestaña
function activateTab(tabId) {
  tabButtons.forEach((b) => b.classList.remove("active"));
  tabContents.forEach((c) => c.classList.remove("active"));

  const button = document.querySelector(`[data-tab="${tabId}"]`);
  const content = document.getElementById(tabId);

  if (button) button.classList.add("active");
  if (content) content.classList.add("active");
}

// --- LÓGICA DE PRIORIDAD PARA QUE SE LLEVE A LAS PESTAÑAS RESPECTIVAS  ---

// Si viene un parámetro ?tab= en la URL
const urlParams = new URLSearchParams(window.location.search);
const tabFromUrl = urlParams.get("tab");

if (tabFromUrl) {
  // Vino desde un enlace externo (home o sidebar)
  activateTab(tabFromUrl);
  localStorage.setItem(storageKey, tabFromUrl);

  // Limpia el parámetro de la URL sin recargar la página
  // Así, si el usuario recarga después, no hay parámetro y localStorage toma el control
  const cleanUrl = window.location.pathname;
  history.replaceState(null, "", cleanUrl);
} else {
  // Recarga normal: usa localStorage o el tab por defecto
  const savedTab = localStorage.getItem(storageKey) || defaultTab;
  activateTab(savedTab);
}

// Eventos click en las pestañas
tabButtons.forEach((btn) => {
  btn.addEventListener("click", () => {
    const tabId = btn.dataset.tab;
    activateTab(tabId);
    localStorage.setItem(storageKey, tabId);
  });
});
