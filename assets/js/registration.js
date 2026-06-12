// ==========================================================================
// ARCHIVO: LÓGICA PARA EL FORMULARIO DE REGISTRO
// ==========================================================================
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("registration-form");
  const successModal = document.getElementById("success-modal");
  const modalBody = document.getElementById("modal-body");
  const closeModalBtn = document.getElementById("close-modal");

  if (!form) return;

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    // VALIDACIONES:

    // ===================== TIPO DE DOCUMENTO =====================
    const docTypeInput = document.getElementById("doc-type");
    const docTypeValue = (docTypeInput?.value || "").trim();

    if (!docTypeValue) {
      alert("Por favor, selecciona un tipo de documento válido.");
      return; // Detiene el envío si está vacío
    }

    // ===================== TELÉFONO =====================
    const phoneInputField = document.querySelector("#phone-input");
    const hiddenPhone = document.querySelector("#phone-full");
    const phoneValue = (
      window.getFullPhoneNumber?.() ||
      phoneInputField?.value ||
      ""
    ).trim();

    if (hiddenPhone && typeof window.getFullPhoneNumber === "function") {
      hiddenPhone.value = window.getFullPhoneNumber();
    }

    if (!phoneValue) {
      alert("El número de teléfono es obligatorio.");
      return;
    }

    if (typeof window.isValidPhoneNumber === "function") {
      if (!window.isValidPhoneNumber()) {
        alert("Número de teléfono inválido");
        return;
      }
    }

    if (hiddenPhone && typeof window.getFullPhoneNumber === "function") {
      hiddenPhone.value = window.getFullPhoneNumber();
    }

    // ===================== EMAIL =====================
    const emailInput = document.getElementById("email-input");
    const emailConfirm = document.getElementById("email-confirm");
    const emailValue = (emailInput?.value || "").trim();
    const emailConfirmValue = (emailConfirm?.value || "").trim();

    if (!emailValue) {
      alert("El correo es obligatorio.");
      return;
    }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailValue)) {
      alert("Correo inválido.");
      return;
    }

    if (emailValue !== emailConfirmValue) {
      alert("Los correos electrónicos ingresados no coinciden.");
      return;
    }

    // ===================== CONTRASEÑAS =====================
    const passwordInput = document.getElementById("password-input");
    const passwordConfirm = document.getElementById("password-confirm");
    const passwordValue = passwordInput?.value || "";

    // Expresión regular: 1 minúscula, 1 mayúscula, 1 número, 1 carácter especial y mínimo 8 de largo
    const passwordRegex =
      /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&._\-\/#])[A-Za-z\d@$!%*?&._\-\/#]{8,}$/;

    if (!passwordRegex.test(passwordValue)) {
      alert(
        "La contraseña debe tener al menos 8 caracteres, incluir una letra mayúscula, una letra minúscula, un número y un carácter especial (ej: @, $, !, %, *, ?, &, _, -, ., /, #).",
      );
      return;
    }

    if (passwordInput.value !== passwordConfirm.value) {
      alert("Las contraseñas no coinciden.");
      return;
    }

    // ===================== ENVIAR FORMULARIO =====================
    const formData = new FormData(form);

    try {
      const response = await fetch("../php/insert_patient.php", {
        method: "POST",
        body: formData,
      });

      const data = await response.json();

      if (data.success) {
        if (modalBody) {
          modalBody.innerHTML = `
            <p class="modal-message">
                Puedes iniciar sesión con tu <strong>nuevo usuario</strong> 
                que es tu <strong>tipo de documento</strong> acompañado del 
                <strong>número de documento</strong> o con el <strong>correo electrónico</strong> 
                que acabas de registrar.
            </p>
            <div class="credentials-data">
              <strong>Usuario:</strong> ${data.credentials.username}<br>
              <strong>Correo:</strong> ${data.credentials.email}<br>
            </div>
          `;
        }

        if (successModal) successModal.classList.remove("hidden");
        form.reset();

        // REDIRECCIÓN AUTOMÁTICA: Si el usuario no hace nada, se va al login en 20 segundos
        setTimeout(() => {
          window.location.href = "../pages/login.php";
        }, 30000);
      } else {
        alert(data.message || "Error desconocido.");
      }
    } catch (err) {
      console.error(err);
      alert("Error inesperado al enviar el formulario");
    }
  });

  // Cerrar modal y redirigir al login
  closeModalBtn?.addEventListener("click", () => {
    successModal?.classList.add("hidden");
    // Redirige al login
    window.location.href = "../pages/login.php";
  });
});
