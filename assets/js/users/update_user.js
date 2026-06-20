// ==========================================================================
// ARCHIVO: LÓGICA DEL MODO EDICIÓN Y ACTUALIZACIÓN DE USUARIOS
// ==========================================================================

// ==========================================================================
// ESTADO GLOBAL Y CONFIGURACIÓN DE COMPONENTES
// ==========================================================================

let originalRows = {};
let activeDropdown = null;

const DOC_MAP = {
  cc: "Cédula de Ciudadanía",
  ce: "Cédula de Extranjería",
  ti: "Tarjeta de Identidad",
  pa: "Pasaporte",
  ot: "Otro (Extranjero)",
};

const DOC_OPTIONS = {
  patient: [
    { value: "cc", label: "Cédula de Ciudadanía" },
    { value: "ce", label: "Cédula de Extranjería" },
    { value: "ti", label: "Tarjeta de Identidad" },
    { value: "pa", label: "Pasaporte" },
    { value: "ot", label: "Otro (Extranjero)" },
  ],
  psychologist: [
    { value: "cc", label: "Cédula de Ciudadanía" },
    { value: "ce", label: "Cédula de Extranjería" },
    { value: "pa", label: "Pasaporte" },
    { value: "ot", label: "Otro (Extranjero)" },
  ],
};

// Creación e inyección del contenedor Dropdown flotante en el DOM
const dropdownGlobal = document.createElement("ul");
dropdownGlobal.className = "global-dropdown";
document.body.appendChild(dropdownGlobal);

function populateDropdown(userType) {
  const options = DOC_OPTIONS[userType] || DOC_OPTIONS.patient;
  dropdownGlobal.innerHTML = options
    .map((opt) => `<li data-value="${opt.value}">${opt.label}</li>`)
    .join("");
}

// ==========================================================================
// CONTROLADORES DE EVENTOS Y POSICIONAMIENTO DEL DROPDOWN (UI)
// ==========================================================================

function positionDropdown(element) {
  const rect = element.getBoundingClientRect();
  const dropdownHeight = dropdownGlobal.offsetHeight || 200;
  const spaceBelow = window.innerHeight - rect.bottom;
  const spaceAbove = rect.top;

  dropdownGlobal.style.left = `${rect.left}px`;
  dropdownGlobal.style.minWidth = `${rect.width}px`;

  if (spaceBelow < dropdownHeight && spaceAbove > spaceBelow) {
    // Abre hacia arriba si no queda espacio abajo
    dropdownGlobal.style.top = `${rect.top - dropdownHeight}px`;
  } else {
    // Abre hacia abajo (comportamiento normal)
    dropdownGlobal.style.top = `${rect.bottom}px`;
  }
}

// Cierra el dropdown
function closeDropdown() {
  if (activeDropdown) {
    activeDropdown.classList.remove("open");
    const selected = activeDropdown.querySelector(".dropdown-selected");
    if (selected) selected.blur?.();
  }
  dropdownGlobal.style.display = "none";
  activeDropdown = null;
}

// Clicks globales para manejar el dropdown interactivo
document.addEventListener("click", (e) => {
  const selected = e.target.closest(".doc-type-dropdown .dropdown-selected");
  const option = e.target.closest(".global-dropdown li");

  // Captura de selección de una opción
  if (option && activeDropdown) {
    const value = option.dataset.value;
    const label = option.textContent.trim();

    activeDropdown.querySelector(".dropdown-text").textContent = label;
    activeDropdown.querySelector('input[name="doc_type"]').value = value;

    closeDropdown();
    return;
  }

  // Apertura / Cierre del selector alternado
  if (selected) {
    const dropdown = selected.closest(".doc-type-dropdown");

    if (activeDropdown === dropdown) {
      closeDropdown();
      return;
    }

    closeDropdown(); // Cierra cualquier otro dropdown abierto

    activeDropdown = dropdown;
    populateDropdown(dropdown.dataset.userType); // ← acá adentro
    dropdown.classList.add("open");

    dropdownGlobal.style.display = "block";
    positionDropdown(selected);

    return;
  }

  // Cierre preventivo al hacer click en zonas vacías de la pantalla
  closeDropdown();
});

// Control de scroll y redimensión para evitar desalineación del menú flotante
const tableContainer = document.querySelector(".table-container");
if (tableContainer) {
  tableContainer.addEventListener("scroll", closeDropdown);
}
window.addEventListener("scroll", closeDropdown, true);
window.addEventListener("resize", closeDropdown);

document.addEventListener("keydown", (e) => {
  if (e.key === "Escape") closeDropdown();
});

// ==========================================================================
// FUNCIONES DE FORMATEO Y MÁSCARAS DE ENTRADA
// ==========================================================================

function formatDocType(type) {
  return DOC_MAP[type] || type;
}

function calculateAge(dateString) {
  const birth = new Date(dateString);
  const today = new Date();

  let age = today.getFullYear() - birth.getFullYear();
  const m = today.getMonth() - birth.getMonth();

  if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) {
    age--;
  }
  return age;
}

// Inicializa la máscara para la fecha de nacimiento

function initBirthdateInput(input) {
  let originalValue = input.value;
  let cleared = false;

  if (originalValue.includes("-") && originalValue.length === 10) {
    input.value = originalValue;
  }

  input.addEventListener("focus", () => {
    if (!cleared) {
      input.value = "";
      cleared = true;
    }
  });

  input.addEventListener("blur", () => {
    if (!input.value.trim()) {
      input.value = originalValue;
    }
    cleared = false;
  });

  // Forzar el formato YYYY-MM-DD
  input.addEventListener("input", (e) => {
    let value = e.target.value.replace(/\D/g, "");

    if (value.length > 8) value = value.slice(0, 8);

    if (value.length >= 5) {
      value = value.replace(/(\d{4})(\d{2})(\d{0,2})/, "$1-$2-$3");
    } else if (value.length >= 3) {
      value = value.replace(/(\d{4})(\d{0,2})/, "$1-$2");
    }

    e.target.value = value;
  });
}

// ==========================================================================
// ACCIONES CRUD (EDIT, SAVE, CANCEL)
// ==========================================================================

window.editUser = function (data, userType) {
  const row = document.getElementById("row-" + data.uuid_user_profile);
  if (!row) return;

  // Persistencia temporal del estado previo de la fila antes del renderizado del form
  originalRows[data.uuid_user_profile] = row.innerHTML;
  row.classList.add("editing-row");

  // CAMPOS COMUNES (PACIENTE O PSICÓLOGO)

  let html = `
    <td><input name="names" value="${data.names ?? ""}"></td>
    <td><input name="surnames" value="${data.surnames ?? ""}"></td>

    <td>
      <div class="dropdown doc-type-dropdown" data-user-type="${userType}">
        <div class="dropdown-selected">
          <span class="dropdown-text">
            ${DOC_MAP[data.doc_type] || data.doc_type}
          </span>
          <svg><use href="#down-arrow"></use></svg>
        </div>

        <input
          type="hidden"
          name="doc_type"
          value="${data.doc_type}"
        >
      </div>
    </td>

    <td>
      <input
        name="doc_number"
        value="${data.doc_number ?? ""}"
      >
    </td>
  `;

  // =====================================================
  // CAMPOS EXCLUSIVOS DEL PACIENTE
  // =====================================================

  if (userType === "patient") {
    html += `
      <td>
        <div class="date-wrapper">
          <input
            class="birth-date-input"
            name="birth_date"
            placeholder="aaaa-mm-dd"
            maxlength="10"
            value="${data.birth_date ?? ""}"
          >

          <svg class="calendar-icon">
            <use href="#calendar-day"></use>
          </svg>
        </div>
      </td>

      <td title="Este campo no puede modificarse.">
        ${calculateAge(data.birth_date)} años
      </td>

      <td>
        <input
          name="phone_number"
          value="${data.phone_number ?? ""}"
        >
      </td>

      <td>
        <input
          name="email"
          value="${data.email ?? ""}"
        >
      </td>

      <td title="Este campo no puede modificarse.">
        ${data.country ?? ""}
      </td>
    `;
  }

  // =====================================================
  // CAMPOS EXCLUSIVOS DEL PSICÓLOGO
  // =====================================================

  if (userType === "psychologist") {
    html += `
      <td>
        <input
          name="license_number"
          value="${data.license_number ?? ""}"
        >
      </td>

      <td>
        <input
          name="phone_number"
          value="${data.phone_number ?? ""}"
        >
      </td>

      <td>
        <input
          name="email"
          value="${data.email ?? ""}"
        >
      </td>
    `;
  }

  html += `
    <td>
      <span class="status-badge ${data.status}">
        ${data.status === "activo" ? "Activo" : "Inactivo"}
      </span>
    </td>

    <td>
      <button
        class="save-button"
        onclick="saveUser(
          '${data.uuid_user_profile}',
          '${userType}'
        )"
      >
        GUARDAR
      </button>

      <button
        class="cancel-button"
        onclick="cancelEdit(
          '${data.uuid_user_profile}'
        )"
      >
        CANCELAR
      </button>
    </td>
  `;

  row.innerHTML = html;

  // Inicializa el input de fecha
  if (userType === "patient") {
    setTimeout(() => {
      const input = row.querySelector(".birth-date-input");

      if (input) {
        initBirthdateInput(input);
      }
    }, 0);
  }
};

// Guardar los cambios realizados
window.saveUser = function (uuid, userType) {
  const ok = confirm("¿Seguro que quieres GUARDAR los cambios?");
  if (!ok) return;

  const row = document.getElementById("row-" + uuid);
  if (!row) return;

  const data = {
    user_type: userType,
    uuid_user_profile: uuid,

    names: row.querySelector('[name="names"]')?.value ?? "",
    surnames: row.querySelector('[name="surnames"]')?.value ?? "",
    doc_type: row.querySelector('[name="doc_type"]')?.value ?? "",
    doc_number: row.querySelector('[name="doc_number"]')?.value ?? "",

    phone_number: row.querySelector('[name="phone_number"]')?.value ?? "",

    email: row.querySelector('[name="email"]')?.value ?? "",
  };

  // =====================================================
  // PACIENTE
  // =====================================================

  if (userType === "patient") {
    data.birth_date = row.querySelector('[name="birth_date"]')?.value ?? "";
  }

  // =====================================================
  // PSICÓLOGO
  // =====================================================

  if (userType === "psychologist") {
    data.license_number =
      row.querySelector('[name="license_number"]')?.value ?? "";
  }

  fetch("../php/users/update_user.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  })
    .then((res) => res.json())
    .then((res) => {
      if (res.success) {
        location.reload();
      } else {
        alert("Error al guardar: " + (res.message || "Intente de nuevo."));
      }
    })
    .catch((err) => {
      console.error("Error en la petición:", err);
      alert("Error de comunicación con el servidor.");
    });
};

// Cancelar la edición y actualización
window.cancelEdit = function (uuid) {
  const ok = confirm("¿CANCELAR edición? Se perderán los cambios.");
  if (!ok) return;

  const row = document.getElementById("row-" + uuid);
  if (!row) return;

  // Restauración limpia del template HTML anterior
  row.classList.remove("editing-row");
  row.innerHTML = originalRows[uuid];

  delete originalRows[uuid];
};
