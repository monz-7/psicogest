// ==========================================================================
// ARCHIVO: MÁSCARA DE ENTRADA E INLINE PARA LA FECHA DE NACIMIENTO
// ==========================================================================

// ==========================================================================
// DISPARADOR DE INICIALIZACIÓN
// ==========================================================================
document.addEventListener("DOMContentLoaded", () => {
  const birthdateInput = document.getElementById("birth-date");

  if (birthdateInput) {
    initRegistrationBirthdate(birthdateInput);
  }
});

// ==========================================================================
// CONTROLADOR DE LA MÁSCARA Y VALIDACIÓN
// ==========================================================================
function initRegistrationBirthdate(input) {
  // GUARD: evita registrar los eventos más de una vez sobre el mismo elemento
  if (input._birthdateInitialized) return;
  input._birthdateInitialized = true;
  // ------------------------------------------------------------------------
  // CONSTRUCCIÓN DE MÁSCARA DINÁMICA
  // ------------------------------------------------------------------------
  input.addEventListener("input", (e) => {
    // Remover cualquier caracter que no sea un número
    let value = e.target.value.replace(/\D/g, "");

    // Limitar la cadena a un máximo de 8 dígitos (DDMMAAAA)
    if (value.length > 8) {
      value = value.slice(0, 8);
    }

    // Construir la máscara dinámica "DD / MM / AAAA"
    if (value.length >= 5) {
      // Si hay más de 4 dígitos: Separa los 2 del día, 2 del mes y el resto del año
      value = value.replace(/(\d{2})(\d{2})(\d{0,4})/, "$1 / $2 / $3");
    } else if (value.length >= 3) {
      // Si hay más de 2 dígitos: Separa los 2 del día y el resto del mes
      value = value.replace(/(\d{2})(\d{0,2})/, "$1 / $2");
    }

    // Devolver el valor formateado al input
    e.target.value = value;
  });

  // ------------------------------------------------------------------------
  // VALIDACIÓN AL SALIR DEL CAMPO
  // ------------------------------------------------------------------------
  input.addEventListener("blur", (e) => {
    const val = e.target.value;

    if (val) {
      // Evita fechas incompletas basadas en la longitud de la máscara ("DD / MM / AAAA")
      if (val.length < 14) {
        input.setCustomValidity(
          "Por favor, ingresa la fecha completa en formato DD / MM / AAAA",
        );
        input.reportValidity();
        return;
      }

      // Descomponer la máscara limpiando espacios para analizar los valores numéricos
      const parts = val.replace(/\s/g, "").split("/");
      const day = parseInt(parts[0], 10);
      const month = parseInt(parts[1], 10);
      const year = parseInt(parts[2], 10);

      // Validar si numéricamente la fecha tiene sentido básico (Día <= 31, Mes <= 12)
      if (
        day < 1 ||
        day > 31 ||
        month < 1 ||
        month > 12 ||
        year < 1920 ||
        year > new Date().getFullYear()
      ) {
        input.setCustomValidity("La fecha ingresada es inválida.");
        input.reportValidity();
      } else {
        input.setCustomValidity("");
      }
    } else {
      // Si el campo está vacío, resetea la validación
      input.setCustomValidity("");
    }
  });
}
