// ==========================================================================
// ARCHIVO: LÓGICA DE INTERFAZ DEL AGENDAMIENTO DE CITAS (SCHEDULING)
// ==========================================================================

const monthDropdown = document.getElementById("month-dropdown");
const dayDropdown = document.getElementById("day-dropdown");
const hourDropdown = document.getElementById("hour-dropdown");
const textarea = document.getElementById("reason");
const charHint = document.querySelector(".char-hint");

// Nombres completos de meses
const MONTH_NAMES = [
  "ENERO",
  "FEBRERO",
  "MARZO",
  "ABRIL",
  "MAYO",
  "JUNIO",
  "JULIO",
  "AGOSTO",
  "SEPTIEMBRE",
  "OCTUBRE",
  "NOVIEMBRE",
  "DICIEMBRE",
];

// Nombres de los días de la semana
const WEEKDAYS = [
  "DOMINGO",
  "LUNES",
  "MARTES",
  "MIÉRCOLES",
  "JUEVES",
  "VIERNES",
  "SÁBADO",
];

// ==========================================================================
// MÓDULO DEL CALENDARIO (MES, DÍA Y DISPONIBILIDAD)
// ==========================================================================

if (monthDropdown && dayDropdown && hourDropdown) {
  // Selectores internos del Picker de Mes
  const yearLabel = monthDropdown.querySelector(".year-label");
  const prevBtn = monthDropdown.querySelector("#prev-year");
  const nextBtn = monthDropdown.querySelector("#next-year");
  const monthItems = monthDropdown.querySelectorAll(".month-grid div");
  const monthSelected = monthDropdown.querySelector(".dropdown-selected");
  const monthText = monthDropdown.querySelector(".dropdown-text");

  // Selectores internos del Picker de Día
  const daySelected = dayDropdown.querySelector(".dropdown-selected");
  const dayText = dayDropdown.querySelector(".dropdown-text");
  const dayMonthLabel = dayDropdown.querySelector(".day-month-label");
  const dayGrid = dayDropdown.querySelector(".day-grid");

  // Estado del año base
  let currentYear = new Date().getFullYear();
  if (yearLabel) yearLabel.textContent = currentYear;

  // --- CONTROL DE NAVEGACIÓN DE AÑOS ---
  prevBtn?.addEventListener("click", (e) => {
    e.stopPropagation();
    currentYear--;
    yearLabel.textContent = currentYear;
  });

  nextBtn?.addEventListener("click", (e) => {
    e.stopPropagation();
    currentYear++;
    yearLabel.textContent = currentYear;
  });

  // --- SELECCIÓN DEL MES ---
  monthItems.forEach((monthItem) => {
    monthItem.addEventListener("click", (e) => {
      // Deja que el evento suba para que dropdowns.js detecte la selección y cierre de forma nativa
      const monthValue = parseInt(monthItem.dataset.value);

      // Actualizar vista del selector de mes
      if (monthText)
        monthText.textContent = `${MONTH_NAMES[monthValue]} / ${currentYear}`;
      if (monthSelected) {
        monthSelected.dataset.value = `${currentYear}-${monthValue.toString().padStart(2, "0")}`;
        monthSelected.classList.add("bold");
      }

      // Actualizar la cabecera del calendario de días y habilitarlo
      if (dayMonthLabel)
        dayMonthLabel.textContent = `${MONTH_NAMES[monthValue]} / ${currentYear}`;
      dayDropdown.classList.remove("disabled");

      // Renderizar días correspondientes
      generateDays(monthValue, currentYear);

      // Limpieza y reseteo del día y hora anteriores
      resetDaySelection();
    });
  });

  // Genera dinámicamente la cuadrícula de días según mes y año
  function generateDays(month, year) {
    if (!dayGrid) return;
    dayGrid.innerHTML = ""; // Limpiar grilla anterior

    const today = new Date();
    const currentDayReal = today.getDate();
    const currentMonthReal = today.getMonth();
    const currentYearReal = today.getFullYear();

    const firstDayIndex = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    // Transformar desfase para que la semana empiece en Lunes (0 = Lunes, 6 = Domingo)
    const startOffset = firstDayIndex === 0 ? 6 : firstDayIndex - 1;

    // Inyectar espacios vacíos
    for (let i = 0; i < startOffset; i++) {
      const emptyCell = document.createElement("div");
      emptyCell.classList.add("empty");
      dayGrid.appendChild(emptyCell);
    }

    // Inyectar días del mes
    for (let i = 1; i <= daysInMonth; i++) {
      const dayCell = document.createElement("div");
      dayCell.textContent = i.toString().padStart(2, "0");

      // Validar si es una fecha en el pasado
      const isPast =
        year < currentYearReal ||
        (year === currentYearReal && month < currentMonthReal) ||
        (year === currentYearReal &&
          month === currentMonthReal &&
          i < currentDayReal);

      // Validar si es el día de hoy
      const isToday =
        i === currentDayReal &&
        month === currentMonthReal &&
        year === currentYearReal;

      if (isPast) dayCell.classList.add("disabled");
      if (isToday) dayCell.classList.add("today");

      // Evento para selección del día
      dayCell.addEventListener("click", (e) => {
        if (dayCell.classList.contains("disabled")) {
          e.stopPropagation();
          return;
        }

        // Limpiar selección previa en la grilla
        dayGrid
          .querySelectorAll(".selected")
          .forEach((d) => d.classList.remove("selected"));
        dayCell.classList.add("selected");

        // Formatear texto descriptivo de la selección
        const selectedDate = new Date(year, month, i);
        const weekdayName = WEEKDAYS[selectedDate.getDay()];
        const formattedDayStr = i.toString().padStart(2, "0");

        if (dayText) dayText.textContent = `${weekdayName}, ${formattedDayStr}`;
        if (daySelected) {
          daySelected.dataset.value = `${year}-${(month + 1).toString().padStart(2, "0")}-${formattedDayStr}`;
          daySelected.classList.add("bold");
        }

        // Habilitar paso siguiente: Bloque de horas
        hourDropdown.classList.remove("disabled");
      });

      dayGrid.appendChild(dayCell);
    }
  }

  // Resetea el estado del dropdown de días y horas al cambiar el mes
  function resetDaySelection() {
    if (dayText) dayText.textContent = "DÍA";
    if (daySelected) {
      daySelected.classList.remove("bold");
      delete daySelected.dataset.value;
    }

    // Bloquear y resetear horas consecutivamente
    hourDropdown.classList.add("disabled");
    const hourText = hourDropdown.querySelector(".dropdown-text");
    const hourSelected = hourDropdown.querySelector(".dropdown-selected");
    if (hourText && hourSelected) {
      hourText.textContent = "HORA";
      hourSelected.classList.remove("bold");
      delete hourSelected.dataset.value;
    }
  }
}

// ==========================================================================
// MÓDULO AUXILIAR: CONTADOR DE CARACTERES (TEXTAREA DE MOTIVO)
// ==========================================================================
if (textarea && charHint) {
  const updateCharCount = () => {
    const currentLength = textarea.value.length;
    charHint.textContent = `${currentLength}/80 caracteres`;
  };

  updateCharCount();
  textarea.addEventListener("input", updateCharCount);
}
