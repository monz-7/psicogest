// ==========================================================================
// ARCHIVO: CONTROL DE PAÍSES Y UBICACIÓN
// ==========================================================================

// ==========================================================================
// CONSTANTES Y LISTAS DE RESPALDO
// ==========================================================================

// LISTA DE RESPALDO (FALLBACK): Si la API local y externa fallan, se usará este backup
const backupCountries = [
  { name: { common: "Argentina" }, cca2: "AR" },
  { name: { common: "Bolivia" }, cca2: "BO" },
  { name: { common: "Chile" }, cca2: "CL" },
  { name: { common: "Ecuador" }, cca2: "EC" },
  { name: { common: "España" }, cca2: "ES" },
  { name: { common: "Estados Unidos" }, cca2: "US" },
  { name: { common: "México" }, cca2: "MX" },
  { name: { common: "Panamá" }, cca2: "PA" },
  { name: { common: "Perú" }, cca2: "PE" },
  { name: { common: "Venezuela" }, cca2: "VE" },
];

// ==========================================================================
// FUNCIONES DE RENDERIZADO Y VINCULACIÓN INTERNA (VISTAS)
// ==========================================================================

// Inyecta los países en el dropdown y conecta con el sistema de eventos global o local
function renderCountries(countriesList) {
  const countryOptions = document.getElementById("country-options");
  const countryHidden = document.getElementById("country");
  const countryText = document.querySelector(
    "#country-dropdown .dropdown-text",
  );

  if (!countryOptions) return;
  countryOptions.innerHTML = "";

  // Filtrar para no repetir Colombia (tiene su propio radio button) y ordenar alfabéticamente
  const filteredCountries = countriesList
    .filter((country) => country.name.common !== "Colombia")
    .sort((a, b) => a.name.common.localeCompare(b.name.common, "es"));

  // Inyección con sus respectivas banderas usando las clases CSS de flag-icons
  filteredCountries.forEach((country) => {
    const li = document.createElement("li");
    li.dataset.value = country.name.common;
    const countryCode = country.cca2.toLowerCase();

    li.innerHTML = `
      <span class="fi fi-${countryCode}"></span>
      <span>${country.name.common}</span>
    `;

    countryOptions.appendChild(li);
  });

  // Vinculación de eventos interactivos sobre el dropdown recién creado
  const countryDropdown = document.getElementById("country-dropdown");
  if (countryDropdown) {
    const selected = countryDropdown.querySelector(".dropdown-selected");

    // Intento A: Conectar con la función global de dropdowns.js si ya se encuentra en memoria
    if (typeof window.attachDropdownItemsEvents === "function") {
      window.attachDropdownItemsEvents(
        countryDropdown,
        countryOptions,
        selected,
      );
    } else {
      // Intento B (SOLUCIÓN DE RESPALDO): Manejo de click nativo aislado si dropdowns.js no ha cargado
      countryOptions.querySelectorAll("li").forEach((item) => {
        item.addEventListener("click", () => {
          if (countryText) countryText.textContent = item.textContent.trim();
          if (countryHidden) countryHidden.value = item.dataset.value;
          if (selected) selected.classList.add("bold");
          countryDropdown.classList.remove("active"); // Cierra el menú desplegable
        });
      });
    }
  }
}

// ==========================================================================
// FLUJO PRINCIPAL DE INICIALIZACIÓN
// ==========================================================================
document.addEventListener("DOMContentLoaded", async () => {
  const radios = document.querySelectorAll('input[name="location"]');
  const countryWrapper = document.getElementById("country-select-wrapper");
  const locationRow = document.querySelector(".location-row");
  const countryHidden = document.getElementById("country");
  const countryText = document.querySelector(
    "#country-dropdown .dropdown-text",
  );

  // ------------------------------------------------------------------------
  // BLOQUE DE CARGA
  // ------------------------------------------------------------------------
  try {
    // MÉTODO 1: Endpoint local estructurado en PHP interno de la plataforma
    const response = await fetch("../php/get_countries.php");
    if (!response.ok) throw new Error("Error en el servidor local PHP");

    const result = await response.json();

    if (result.success && result.data) {
      // Normalización de datos locales para igualar la API pública estándar
      const mappedCountries = result.data.map((c) => ({
        name: { common: c.name },
        cca2: c.code.toUpperCase(),
      }));

      renderCountries(mappedCountries);
    } else {
      throw new Error(result.message || "Estructura de datos inválida");
    }
  } catch (localError) {
    console.warn(
      "No se pudo cargar desde el archivo local PHP. Intentando con API externa...",
      localError,
    );

    // PLAN B: Consumo directo a RESTCountries en caso de fallo
    try {
      const response = await fetch(
        "https://restcountries.com/v3.1/all?fields=name,cca2",
      );
      if (!response.ok) throw new Error("API Externa caída");

      const countries = await response.json();
      renderCountries(countries);
    } catch (externalError) {
      console.error(
        "Ambos métodos fallaron. Usando backup de emergencia duro.",
        externalError,
      );

      // PLAN C: Uso del arreglo estático local integrado al inicio de este script
      renderCountries(backupCountries);
    }
  }

  // ------------------------------------------------------------------------
  // CONTROL DE UBICACIÓN
  // ------------------------------------------------------------------------
  radios.forEach((radio) => {
    radio.addEventListener("change", () => {
      if (!countryWrapper || !countryHidden || !countryText) return;

      if (radio.value === "otro" && radio.checked) {
        // Activación visual del país secundario
        countryWrapper.classList.add("show");
        locationRow.classList.add("show-divider");
        countryHidden.required = true;
      } else {
        // Desactivación, reseteo del elemento oculto y restauración del marcador de posición
        countryWrapper.classList.remove("show");
        locationRow.classList.remove("show-divider");
        countryHidden.required = false;
        countryHidden.value = "";
        countryText.textContent = "INDICA EL PAÍS";

        const selectedContainer = document.querySelector(
          "#country-dropdown .dropdown-selected",
        );
        if (selectedContainer) {
          selectedContainer.classList.remove("bold");
        }
      }
    });
  });
});
