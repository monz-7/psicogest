// ==========================================================================
// ARCHIVO: CONTROL DE PAÍSES Y UBICACIÓN
// ==========================================================================

// LISTA DE RESPALDO (FALLBACK): Si la API local y externa fallan
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

// Función única para actualizar la vista del dropdown con la bandera y la línea vertical
function updateSelectedUI(countryName, countryCode2) {
  const countryHidden = document.getElementById("country");
  const countryDropdown = document.getElementById("country-dropdown");
  const countrySelectedDiv =
    countryDropdown?.querySelector(".dropdown-selected");

  if (!countryHidden || !countrySelectedDiv) return;

  countryHidden.value = countryName;
  countrySelectedDiv.classList.add("bold");

  // Inyecta la estructura con el contenedor divisor de la bandera
  countrySelectedDiv.innerHTML = `
    <div class="selected-flag-container">
      <span class="fi fi-${countryCode2.toLowerCase()}"></span>
    </div>
    <span class="dropdown-text">${countryName}</span>
    <svg class="arrow-icon"><use href="#down-arrow"></use></svg>
  `;
}

// Inyecta los países en el dropdown y maneja la pre-selección en edición
function renderCountries(countriesList) {
  const countryOptions = document.getElementById("country-options");
  const countryHidden = document.getElementById("country");
  const countryDropdown = document.getElementById("country-dropdown");
  const countrySelectedDiv =
    countryDropdown?.querySelector(".dropdown-selected");

  if (!countryOptions || !countryHidden || !countrySelectedDiv) return;
  countryOptions.innerHTML = "";

  const excludeColombia =
    document.querySelectorAll('input[name="location"]').length > 0;

  const filteredCountries = countriesList
    .filter((country) => !excludeColombia || country.name.common !== "Colombia")
    .sort((a, b) => a.name.common.localeCompare(b.name.common, "es"));

  // Genera elementos de la lista desplegable
  filteredCountries.forEach((country) => {
    const li = document.createElement("li");
    li.dataset.value = country.name.common;
    li.dataset.code = country.cca2;
    const countryCode = country.cca2.toLowerCase();

    li.innerHTML = `
      <span class="fi fi-${countryCode}"></span>
      <span>${country.name.common}</span>
    `;
    countryOptions.appendChild(li);
  });

  // CASO EDICIÓN (Carga Inicial): Si el input ya viene con datos de la BD
  const currentValue = countryHidden.value.trim();
  if (currentValue && currentValue !== "Colombia") {
    const matchedLi = Array.from(countryOptions.querySelectorAll("li")).find(
      (li) => li.dataset.value.toLowerCase() === currentValue.toLowerCase(),
    );

    if (matchedLi) {
      updateSelectedUI(matchedLi.dataset.value, matchedLi.dataset.code);
    }
  }

  // CASO INTERACCIÓN: Evento Click al seleccionar un país de la lista
  countryOptions.querySelectorAll("li").forEach((item) => {
    item.addEventListener("click", (e) => {
      // Evita que el evento flote y vuelva a disparar el toggle de apertura
      e.stopPropagation();

      // Actualiza la interfaz inyectando la bandera, la línea y el texto
      updateSelectedUI(item.dataset.value, item.dataset.code);

      // FORZAR EL CIERRE: Quita la clase y el display en línea
      countryDropdown.classList.remove("open");
      countryOptions.style.display = "none";
    });
  });
}

// FLUJO PRINCIPAL DE INICIALIZACIÓN
document.addEventListener("DOMContentLoaded", async () => {
  const radios = document.querySelectorAll('input[name="location"]');
  const countryWrapper = document.getElementById("country-select-wrapper");
  const locationRow = document.querySelector(".location-row");
  const countryHidden = document.getElementById("country");
  const countryDropdown = document.getElementById("country-dropdown");
  const countrySelectedDiv =
    countryDropdown?.querySelector(".dropdown-selected");

  // CARGA DE PAÍSES
  try {
    const response = await fetch("../php/api/get_countries.php");
    if (!response.ok) throw new Error("Error en el servidor local PHP");

    const result = await response.json();
    if (result.success && result.data) {
      const mappedCountries = result.data.map((c) => ({
        name: { common: c.name },
        cca2: c.code.toUpperCase(),
      }));
      renderCountries(mappedCountries);
    } else {
      throw new Error(result.message || "Estructura inválida");
    }
  } catch (localError) {
    console.warn("Intentando con API externa...", localError);
    try {
      const response = await fetch(
        "https://restcountries.com/v3.1/all?fields=name,cca2",
      );
      if (!response.ok) throw new Error("API Externa caída");
      const countries = await response.json();
      renderCountries(countries);
    } catch (externalError) {
      console.error("Usando backup estático.", externalError);
      renderCountries(backupCountries);
    }
  }

  // LÓGICA DE CONTROL DE RADIOS
  if (radios.length > 0 && countryHidden) {
    // Verificar estado inicial (Caso: Modificar Perfil / Edición)
    const activeRadio = Array.from(radios).find((r) => r.checked);
    if (activeRadio && activeRadio.value === "otro") {
      countryWrapper?.classList.add("show");
      locationRow?.classList.add("show-divider");
      countryHidden.required = true;
    }

    radios.forEach((radio) => {
      radio.addEventListener("change", () => {
        if (!countryWrapper || !countryHidden || !countrySelectedDiv) return;

        if (radio.value === "otro" && radio.checked) {
          countryWrapper.classList.add("show");
          locationRow?.classList.add("show-divider");
          countryHidden.required = true;
        } else {
          // Si vuelve a "EN COLOMBIA", resetea todo a limpio
          countryWrapper.classList.remove("show");
          locationRow?.classList.remove("show-divider");
          countryHidden.required = false;
          countryHidden.value = radio.value === "colombia" ? "Colombia" : "";

          // Restaura el diseño original sin bandera ni línea
          countrySelectedDiv.classList.remove("bold");
          countrySelectedDiv.innerHTML = `
            <span class="dropdown-text">INDICA EL PAÍS</span>
            <svg class="arrow-icon"><use href="#down-arrow"></use></svg>
          `;
        }
      });
    });
  }
});
