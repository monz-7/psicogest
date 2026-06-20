// ==========================================================================
// ARCHIVO: LÓGICA DE CAMBIO DE CONTRASEÑA (REUTILIZABLE)
// Uso: perfil de paciente/psicólogo y modal del admin
// ==========================================================================

function initChangePassword(config) {
  const { fields, apiPath, onSuccess, onError, onLoading } = config;

  const PASS_REGEX = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$*]).{8,}$/;

  // ==========================================================================
  // Ejecuta el proceso de cambio de contraseña
  // ==========================================================================

  async function processPasswordChange() {
    const currentPass = document.getElementById(fields.current)?.value ?? "";
    const newPass = document.getElementById(fields.new)?.value ?? "";
    const repeatPass = document.getElementById(fields.repeat)?.value ?? "";

    // --- Validación: campos vacíos ---
    if (!currentPass || !newPass || !repeatPass) {
      const emptyFields = [];
      if (!currentPass) emptyFields.push(fields.current);
      if (!newPass) emptyFields.push(fields.new);
      if (!repeatPass) emptyFields.push(fields.repeat);
      onError(
        "Por favor, completa todos los campos del formulario.",
        emptyFields,
      );
      return;
    }

    // --- Validación: contraseñas no coinciden ---
    if (newPass !== repeatPass) {
      onError("Las contraseñas nuevas no coinciden.", [
        fields.new,
        fields.repeat,
      ]);
      return;
    }

    // --- Validación: requisitos mínimos de seguridad ---
    if (!PASS_REGEX.test(newPass)) {
      onError("La nueva contraseña no cumple con los requisitos mínimos.", [
        fields.new,
      ]);
      return;
    }

    if (!confirm("¿Seguro que deseas MODIFICAR tu contraseña actual?")) return;

    if (onLoading) onLoading(true);

    try {
      const response = await fetch(apiPath, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          current_password: currentPass,
          new_password: newPass,
          repeat_password: repeatPass,
        }),
      });

      const data = await response.json();

      if (data.success) {
        // Limpiar los campos tras el éxito
        document.getElementById(fields.current).value = "";
        document.getElementById(fields.new).value = "";
        document.getElementById(fields.repeat).value = "";
        onSuccess();
      } else {
        onError(data.message, data.error_fields || []);
      }
    } catch (err) {
      console.error("Error en cambio de contraseña:", err);
      onError("Error de conexión con el servidor. Inténtalo de nuevo.", []);
    } finally {
      if (onLoading) onLoading(false);
    }
  }

  // Expone la función para llamarla cuando se necesite
  return { processPasswordChange };
}
