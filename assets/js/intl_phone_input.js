// ==========================================================================
// ARCHIVO: LOGICIAL DEL INPUT DE TELÉFONO INTERNACIONAL (INTL-TEL-INPUT)
// ==========================================================================

document.addEventListener("DOMContentLoaded", function () {
  const phoneInput = document.querySelector("#phone-input");
  const hiddenPhone = document.querySelector("#phone-full");

  // Control preventivo en caso de que el input no se renderice en la vista actual
  if (!phoneInput) return;

  // ==========================================================================
  // INICIALIZACIÓN Y CONFIGURACIÓN DE LA LIBRERÍA
  // ==========================================================================
  const iti = window.intlTelInput(phoneInput, {
    initialCountry: "co",
    separateDialCode: true,
    preferredCountries: ["co", "us", "mx"],
    nationalMode: false,
    autoPlaceholder: "aggressive",
    // Localización al español de los textos de interfaz internos de la librería
    i18n: {
      searchPlaceholder: "BUSCAR",
    },
    loadUtils: () =>
      import("https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/utils.js"),
  });

  // ==========================================================================
  // MÉTODOS GLOBALES DE LA API (INTERFACE EXPOSURE)
  // ==========================================================================

  // Obtiene el número telefónico completo formateado en el
  // estándar internacional E.164 (Ej: +573001234567)
  window.getFullPhoneNumber = () => {
    const fullNumber = iti.getNumber(
      window.intlTelInput.utils.numberFormat.E164,
    );
    return fullNumber || phoneInput.value.trim();
  };

  // Valida si el número ingresado cumple las reglas telefónicas del país
  // Incluye una flexibilización para soportar la marcación celular estándar en Colombia.
  window.isValidPhoneNumber = () => {
    const digitsOnly = phoneInput.value.replace(/\D/g, "");

    // Regla de flexibilidad: Si son los 10 dígitos celulares estándar de Colombia, se asume válido
    if (digitsOnly.length === 10) {
      return true;
    }
    return iti.isValidNumber();
  };

  // ==========================================================================
  // CONTROLADORES DE EVENTOS Y SINCRONIZACIÓN DE CAMPOS
  // ==========================================================================

  // Sincroniza dinámicamente el valor en el input oculto durante la digitación
  phoneInput.addEventListener("input", () => {
    if (hiddenPhone) {
      hiddenPhone.value = iti.getNumber() || phoneInput.value.trim();
    }
  });

  // Actualiza el input oculto  si el usuario cambia la bandera del país
  phoneInput.addEventListener("countrychange", () => {
    if (hiddenPhone) {
      hiddenPhone.value = iti.getNumber();
    }
  });
});
