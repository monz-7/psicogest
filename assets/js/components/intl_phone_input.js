// ==========================================================================
// ARCHIVO: LÓGICA DEL INPUT DE TELÉFONO INTERNACIONAL (INTL-TEL-INPUT)
// ==========================================================================

// ==========================================================================
// FUNCIÓN GLOBAL: INICIALIZA EL COMPONENTE EN CUALQUIER INPUT INDICADO
// ==========================================================================
window.initPhoneInput = function (phoneInputEl, hiddenEl) {
  if (!phoneInputEl || typeof window.intlTelInput !== "function") return null;

  const iti = window.intlTelInput(phoneInputEl, {
    initialCountry: "co",
    separateDialCode: true,
    preferredCountries: ["co", "us", "mx"],
    nationalMode: false,
    autoPlaceholder: "aggressive",
    i18n: {
      searchPlaceholder: "BUSCAR",
    },
    loadUtils: () =>
      import("https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/utils.js"),
  });

  // Sincroniza el campo oculto en tiempo real
  phoneInputEl.addEventListener("input", () => {
    if (hiddenEl) hiddenEl.value = iti.getNumber() || phoneInputEl.value.trim();
  });

  phoneInputEl.addEventListener("countrychange", () => {
    if (hiddenEl) hiddenEl.value = iti.getNumber();
  });

  return iti;
};

// ==========================================================================
// MÉTODOS GLOBALES DE LA API
// Se sobreescriben en DOMContentLoaded con la instancia real del registro
// ==========================================================================
window.getFullPhoneNumber = () => "";
window.isValidPhoneNumber = () => false;

// ==========================================================================
// INICIALIZACIÓN AUTOMÁTICA PARA EL FORMULARIO DE REGISTRO
// Solo actúa si existe #phone-input; en profile.php retorna inmediatamente
// ==========================================================================
document.addEventListener("DOMContentLoaded", function () {
  const phoneInput = document.querySelector("#phone-input");
  const hiddenPhone = document.querySelector("#phone-full");

  if (!phoneInput) return;

  const iti = window.initPhoneInput(phoneInput, hiddenPhone);
  if (!iti) return;

  // Sobreescribe los helpers globales con la instancia concreta del registro
  window.getFullPhoneNumber = () => {
    try {
      return (
        iti.getNumber(window.intlTelInput.utils.numberFormat.E164) ||
        phoneInput.value.trim()
      );
    } catch {
      return iti.getNumber() || phoneInput.value.trim();
    }
  };

  window.isValidPhoneNumber = () => {
    const digitsOnly = phoneInput.value.replace(/\D/g, "");
    // Flexibilidad: 10 dígitos es el estándar colombiano
    if (digitsOnly.length === 10) return true;
    return iti.isValidNumber();
  };
});
