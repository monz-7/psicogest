// ==========================================================================
// ARCHIVO: LÓGICA DE ACTUALIZACIÓN DE PERFIL
// Uso: perfil de paciente y psicólogo
// ==========================================================================

// FUNCIÓN GLOBAL: Inicializa la lógica para actualizar el perfil
function initUpdateProfile(config) {
  const { apiPath, onSuccess, onError, onLoading, getIti } = config;

  async function processProfileUpdate() {
    const role = window.USER_ROLE;

    // --- Campos comunes ---
    const names = document.getElementById("edit-names")?.value.trim() ?? "";
    const surnames =
      document.getElementById("edit-surnames")?.value.trim() ?? "";
    const phone = document.getElementById("edit-phone")?.value.trim() ?? "";
    const email = document.getElementById("edit-email")?.value.trim() ?? "";

    // --- Campos exclusivos del paciente ---
    const country = document.getElementById("country")?.value.trim() ?? "";
    const birthdate = document.getElementById("birth-date")?.value.trim() ?? "";

    // --- Validación: campos vacíos comunes ---
    const emptyFields = [];
    if (!names) emptyFields.push("names");
    if (!surnames) emptyFields.push("surnames");
    if (!phone) emptyFields.push("phone_number");
    if (!email) emptyFields.push("email");

    if (role === "paciente") {
      if (!country) emptyFields.push("country");
      if (!birthdate) emptyFields.push("birth_date");
    }

    if (emptyFields.length > 0) {
      onError("Por favor, completa todos los campos requeridos.", emptyFields);
      return;
    }

    // --- Validación: longitud de la máscara de fecha (paciente) ---
    if (role === "paciente" && birthdate.length < 14) {
      onError(
        "Por favor, ingresa una fecha de nacimiento válida (DD / MM / AAAA).",
        ["birth_date"],
      );
      return;
    }

    // --- Validación: teléfono con intlTelInput ---
    const itiInstance = getIti ? getIti() : null;
    if (itiInstance) {
      const digitsOnly = phone.replace(/\D/g, "");
      if (digitsOnly.length !== 10 && !itiInstance.isValidNumber()) {
        onError("El número de teléfono ingresado no es válido.", [
          "phone_number",
        ]);
        return;
      }
    }

    if (
      !confirm(
        "¿Seguro que deseas GUARDAR las modificaciones en tus datos personales?",
      )
    )
      return;

    if (onLoading) onLoading(true);

    try {
      const fullPhoneNumber = itiInstance ? itiInstance.getNumber() : phone;

      // Payload base
      const payload = { names, surnames, phone_number: fullPhoneNumber, email };

      // Campos exclusivos de paciente
      if (role === "paciente") {
        payload.country = country;
        payload.birthdate = birthdate;
      }

      // Campos exclusivos de psicólogo
      if (role === "psicologo") {
        payload.specialties = Array.from(
          document.querySelectorAll(
            '#container-specialties input[name="specialties[]"]:checked',
          ),
        ).map((cb) => cb.value);
      }

      const response = await fetch(apiPath, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(payload),
      });

      const data = await response.json();

      if (data.success) {
        onSuccess(data, payload);
      } else {
        onError(data.message, data.error_fields || []);
      }
    } catch (err) {
      console.error("Error en actualización de perfil:", err);
      onError("Error en la conexión con el servidor. Inténtalo de nuevo.", []);
    } finally {
      if (onLoading) onLoading(false);
    }
  }

  return { processProfileUpdate };
}
