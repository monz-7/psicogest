// ==========================================================================
// ARCHIVO: PÁGINA DE PERFIL
// Maneja: cambio de modos (VIEW / PROFILE / PASS) y mensajes de UI
// Delega lógica a: change_password.js y update_profile.js
// ==========================================================================

document.addEventListener("DOMContentLoaded", function () {
  // --- Estado de la pantalla ---
  let activeMode = "VIEW"; // "VIEW" | "PASS" | "PROFILE"
  let itiInstance = null;

  // --- Botones del menú izquierdo ---
  const editProfBtn = document.getElementById("edit-prof-button");
  const changePassBtn = document.getElementById("change-pass-button");
  const lineEditProf = document.getElementById("line-edit-prof");
  const lineChangePass = document.getElementById("line-change-pass");

  // Salida si los botones del menú no existen (no es paciente/psicólogo)
  if (!editProfBtn || !changePassBtn) return;

  // --- Contenedores de la columna derecha ---
  const profileInfo = document.getElementById("profile-info");
  const editProfileForm = document.getElementById("edit-profile-form");
  const changePassForm = document.getElementById("change-password-form");
  const actionButtons = document.getElementById("action-buttons-profile");

  // --- Mensajes y modal ---
  const errorBox = document.getElementById("profile-error-box");
  const successBox = document.getElementById("profile-success-box");
  const successModal = document.getElementById("success-modal");
  const closeModalBtn = document.getElementById("close-modal");

  // --- Botones de acción inferior ---
  const cancelBtn = document.getElementById("cancel-button-profile");
  const saveBtn = document.getElementById("save-button-profile");

  // ==========================================================================
  // AUXILIARES DE INTERFAZ
  // ==========================================================================

  // Muestra inputs con errores
  function showError(message, affectedFields = []) {
    if (errorBox) {
      errorBox.textContent = message;
      errorBox.classList.remove("hidden");
    }
    affectedFields.forEach((field) => {
      document
        .getElementById(`container-${field}`)
        ?.classList.add("input-error");
    });
  }

  // Limpia inputs con errores
  function clearValidationsAndMessages() {
    if (errorBox) {
      errorBox.textContent = "";
      errorBox.classList.add("hidden");
    }
    if (successBox) successBox.classList.add("hidden");

    [
      "current_password",
      "new_password",
      "repeat_password",
      "names",
      "surnames",
      "country",
      "birth_date",
      "phone_number",
      "email",
    ].forEach((field) => {
      document
        .getElementById(`container-${field}`)
        ?.classList.remove("input-error");
    });
  }

  // Controla el estado visual y de interacción del botón de guardar
  function toggleSaveButton(disable) {
    saveBtn.disabled = disable;
    saveBtn.style.opacity = disable ? "0.6" : "1";
  }

  // ==========================================================================
  // CAMBIO DE MODO
  // ==========================================================================
  function changeMode(newMode) {
    clearValidationsAndMessages();
    activeMode = newMode;

    editProfBtn.classList.remove("active");
    changePassBtn.classList.remove("active");
    if (lineEditProf) lineEditProf.classList.remove("line-active");
    if (lineChangePass) lineChangePass.classList.remove("line-active");

    profileInfo.classList.add("hidden");
    if (editProfileForm) editProfileForm.classList.add("hidden");
    changePassForm.classList.add("hidden");
    actionButtons.classList.add("hidden");

    if (newMode === "VIEW") {
      profileInfo.classList.remove("hidden");
    } else if (newMode === "PROFILE") {
      editProfBtn.classList.add("active");
      if (lineEditProf) lineEditProf.classList.add("line-active");
      editProfileForm.classList.remove("hidden");
      actionButtons.classList.remove("hidden");

      // Cambiar texto e icono para actualización de perfil
      if (saveBtn) {
        saveBtn.innerHTML = `
          <svg class="icon"><use href="#save"></use></svg>
          ACTUALIZAR MIS DATOS
        `;
      }

      initProfileComponents();
    } else if (newMode === "PASS") {
      changePassBtn.classList.add("active");
      if (lineChangePass) lineChangePass.classList.add("line-active");
      changePassForm.classList.remove("hidden");
      actionButtons.classList.remove("hidden");

      // Cambiar texto e icono para cambio de contraseña
      if (saveBtn) {
        saveBtn.innerHTML = `
          <svg class="icon"><use href="#password"></use></svg>
          MODIFICAR CONTRASEÑA
        `;
      }
    }
  }

  // ==========================================================================
  // INICIALIZACIÓN DE COMPONENTES DEL FORMULARIO DE EDICIÓN
  // ==========================================================================
  function initProfileComponents() {
    const phoneInput = document.getElementById("edit-phone");
    const hiddenPhone = document.getElementById("phone-full");

    if (
      phoneInput &&
      !itiInstance &&
      typeof window.initPhoneInput === "function"
    ) {
      itiInstance = window.initPhoneInput(phoneInput, hiddenPhone);
    }

    const birthdateInput = document.getElementById("birth-date");
    if (birthdateInput && typeof initRegistrationBirthdate === "function") {
      initRegistrationBirthdate(birthdateInput);
    }

    const countryWrapper = document.getElementById("country-select-wrapper");
    if (countryWrapper) countryWrapper.classList.add("show");
  }

  // ==========================================================================
  // INSTANCIAS DE CHANGE_PASSWORD Y UPDATE_PROFILE
  // ==========================================================================

  // change_password.js
  const passwordManager = initChangePassword({
    fields: {
      current: "current-password",
      new: "new-password",
      repeat: "repeat-password",
    },
    apiPath: "../php/auth/change_password.php",
    onSuccess: () => {
      if (successModal) {
        successModal.classList.remove("hidden");
        setTimeout(() => {
          successModal.classList.add("hidden");
          changeMode("VIEW");
        }, 5000);
      }
    },
    onError: (message, fields) => showError(message, fields),
    onLoading: (isLoading) => toggleSaveButton(isLoading),
  });

  // update_profile.js
  const profileManager = initUpdateProfile({
    apiPath: "../php/users/update_profile.php",
    getIti: () => itiInstance,
    onSuccess: (data, payload) => {
      const role = window.USER_ROLE;

      // Actualizar vista estática común
      document.getElementById("view-fullname").textContent =
        `${payload.names} ${payload.surnames}`.toUpperCase();
      document.getElementById("view-phone").textContent = payload.phone_number;
      document.getElementById("view-email").textContent = payload.email;

      if (data.code_phone) {
        document.getElementById("view-flag-phone").className =
          `fi fi-${data.code_phone}`;
      }

      // Actualizar vista exclusiva de paciente
      if (role === "paciente") {
        if (document.getElementById("view-country"))
          document.getElementById("view-country").textContent = payload.country;
        if (document.getElementById("view-birthdate"))
          document.getElementById("view-birthdate").textContent =
            payload.birthdate;
        if (data.age && document.getElementById("view-age"))
          document.getElementById("view-age").textContent = `${data.age} años`;
        if (data.code_country && document.getElementById("view-flag-country"))
          document.getElementById("view-flag-country").className =
            `fi fi-${data.code_country}`;
      }

      // Actualizar vista exclusiva de psicólogo
      if (role === "psicologo" && data.specialties_names) {
        const viewSpecs = document.getElementById("view-specialties");
        if (viewSpecs)
          viewSpecs.textContent = data.specialties_names
            ? `${data.specialties_names}.`
            : "Ninguna.";
      }

      changeMode("VIEW");

      if (successBox) {
        successBox.textContent = "Actualización de datos exitosa.";
        successBox.classList.remove("hidden");
        setTimeout(() => successBox.classList.add("hidden"), 5000);
      }
    },
    onError: (message, fields) => showError(message, fields),
    onLoading: (isLoading) => toggleSaveButton(isLoading),
  });

  // ==========================================================================
  // EVENTOS
  // ==========================================================================
  editProfBtn.addEventListener("click", () => {
    if (activeMode !== "PROFILE") changeMode("PROFILE");
  });

  changePassBtn.addEventListener("click", () => {
    if (activeMode !== "PASS") changeMode("PASS");
  });

  if (cancelBtn) {
    cancelBtn.addEventListener("click", () => {
      if (
        confirm(
          "¿Seguro que deseas CANCELAR?\nSe perderán los cambios realizados.",
        )
      ) {
        changeMode("VIEW");
      }
    });
  }

  if (saveBtn) {
    saveBtn.addEventListener("click", () => {
      if (activeMode === "PASS") passwordManager.processPasswordChange();
      if (activeMode === "PROFILE") profileManager.processProfileUpdate();
    });
  }

  if (closeModalBtn) {
    closeModalBtn.addEventListener("click", () => {
      successModal.classList.add("hidden");
      changeMode("VIEW");
    });
  }
});
