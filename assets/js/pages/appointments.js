// ==========================================================================
// ARCHIVO: LÓGICA DE INTERFAZ PARA LA PÁGINA DE CITAS (APPOINTMENTS)
// ==========================================================================
const appmtsHistory = document.querySelectorAll(".history-container");
const noAppmtsMessage = document.getElementById("history-message");
const noMoreMessage = document.getElementById("appointments-message");

// ==========================================================================
// MÓDULO: FILTRO DEL HISTORIAL POR MES
// ==========================================================================

// Ejecuta automáticamente dropdowns.js al hacer click en una opción
window.onDropdownSelect = function (dropdownId, value, label) {
  // Asegura que el cambio venga del dropdown de meses (por su ID o clase)
  if (
    dropdownId === "month-filter-dropdown" ||
    dropdownId === "month-dropdown"
  ) {
    filterAppointmentsByMonth(label);
  }
};

// Filtra las tarjetas del historial médico según el mes seleccionado
function filterAppointmentsByMonth(monthName) {
  const selectedMonthUpper = monthName.toUpperCase();
  let hasAppointments = false;

  // Evalúa coincidencia en cada tarjeta del historial
  appmtsHistory.forEach((appmt) => {
    const appmtMonth = appmt.dataset.month
      ? appmt.dataset.month.toUpperCase()
      : "";

    if (appmtMonth === selectedMonthUpper) {
      appmt.style.display = "block";
      hasAppointments = true;
    } else {
      appmt.style.display = "none";
    }
  });

  // Alterna visibilidad de mensajes de estado alternativos
  toggleFilterMessages(hasAppointments);
}

// Administra la visibilidad de los mensajes informativos del historial
function toggleFilterMessages(hasAppointments) {
  if (!hasAppointments) {
    if (noAppmtsMessage) noAppmtsMessage.style.display = "block";
    if (noMoreMessage) noMoreMessage.style.display = "none";
  } else {
    if (noAppmtsMessage) noAppmtsMessage.style.display = "none";
    if (noMoreMessage) noMoreMessage.style.display = "block";
  }
}

// --- INICIALIZACIÓN AUTOMÁTICA DEL FILTRO ---
// Ejecuta el filtro inicial con el mes que venga seleccionado por defecto en el HTML al cargar la página
const selectedMonthText = document.querySelector(
  ".dropdown-filter-text, .dropdown-text",
);
if (selectedMonthText) {
  const initialMonth = selectedMonthText.textContent.trim();
  if (initialMonth) {
    filterAppointmentsByMonth(initialMonth);
  }
}
