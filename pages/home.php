<?php

// ==========================================================================
// ARCHIVO: INTERFAZ DEL INICIO (HOME)
// Distribuye las funcionalidades del sistema según el rol del usuario
// ==========================================================================

// Middleware de seguridad y control de accesos
require_once("../php/auth/auth.php");
require_once("../php/auth/permissions.php");

// Restricción de acceso: solo usuarios con roles autorizados
requireRoles(["admin", "paciente", "psicologo"]);

// Captura el rol activo del usuario en la sesión
$role = $_SESSION["role"] ?? "";

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  
  <title>PsicoGest | Inicio</title>
  <link rel="icon" href="../assets/img/icon.ico" type="image/x-icon" />

  <link rel="stylesheet" href="../assets/css/base/main.css" />
</head>

<body>
  <!-- HEADER -->
  <div id="header-container"></div>
  <!-- CONTENIDO PRINCIPAL -->
  <main class="main-content">
    
    <!-- Mensaje de bienvenida -->
    <h2 class="welcome-message">
      BIENVENIDO(A) <?= mb_strtoupper(htmlspecialchars($_SESSION["names"]), 'UTF-8'); ?>,
      ¿QUÉ DESEAS REALIZAR EL DÍA DE HOY?
    </h2>
    

    <!--=============== ADMIN ===============-->
    <?php if ($role === "admin"): ?>
      <!-- TARJETA CONTENEDORA -->
      <div class="home-card">
        <!-- BOTÓN DE ENLACE A FUNCIONALIDAD PRINCIPAL -->
        <a href="admin_patients.php" class="main-function-button">
          <svg class="icon">
            <use href="#patients"></use>
          </svg>
          GESTIONAR<br>PACIENTES
        </a>
        
        <!-- LÍNEA VERTICAL DIVISORA -->
        <div class="line-v-home"></div>
        
        <!-- BOTÓN DE ENLACE A FUNCIONALIDAD PRINCIPAL -->
        <a href="admin_psychologists.php?tab=tab1" class="main-function-button">
          <svg class="icon">
            <use href="#human-brain"></use>
          </svg>
          GESTIONAR<br>PSICÓLOGOS
        </a>
      </div>

      <!-- PIE DE PÁGINA CON ENLACE AL CAMBIO DE CONTRASEÑA -->
      <footer class="change-password-link">
        ¿NECESITAS MODIFICAR TU CONTRASEÑA?
        <a href="#" id="open-modal-button">HAZLO AQUÍ.</a>
      </footer> 

      <!-- Modal -->
      <div id="password-modal" class="modal hidden">
          <div class="modal-content admin-change-password">
      
              <h1 class="modal-title">PSIC🧠GEST • CAMBIAR CONTRASEÑA</h1>
      
              <!-- Mensaje de error -->
              <div id="modal-error-box" class="error-box hidden"></div>

              <!-- Campo: contraseña actual -->
              <div class="field-input" id="container-modal-current-password">
                  <svg class="icon"><use href="#password-unlock"></use></svg>
                  <input
                      type="password"
                      id="modal-current-password"
                      class="password-input"
                      placeholder="CONTRASEÑA ACTUAL"
                      autocomplete="current-password"
                  >
                  <button type="button" class="toggle-password" data-target="modal-current-password" aria-label="Mostrar contraseña">
                      <svg class="icon"><use href="#eye"></use></svg>
                  </button>
              </div>
      
              <!-- Requisitos de la contraseña -->
              <p class="password-requirements">
                  La contraseña debe tener al menos 8 caracteres,
                  incluir una letra mayúscula, una letra minúscula,
                  un número y un carácter especial (@, #, $, *).
              </p>

              <!-- Campo: nueva contraseña -->
              <div class="field-input" id="container-modal-new-password">
                  <svg class="icon"><use href="#password"></use></svg>
                  <input
                      type="password"
                      id="modal-new-password"
                      class="password-input"
                      placeholder="NUEVA CONTRASEÑA"
                      autocomplete="new-password"
                  >
                  <button type="button" class="toggle-password" data-target="modal-new-password" aria-label="Mostrar contraseña">
                      <svg class="icon"><use href="#eye"></use></svg>
                  </button>
              </div>
      
              <!-- Campo: repetir nueva contraseña -->
              <div class="field-input" id="container-modal-repeat-password">
                  <svg class="icon"><use href="#password"></use></svg>
                  <input
                      type="password"
                      id="modal-repeat-password"
                      class="password-input"
                      placeholder="REPETIR NUEVA CONTRASEÑA"
                      autocomplete="new-password"
                  >
                  <button type="button" class="toggle-password" data-target="modal-repeat-password" aria-label="Mostrar contraseña">
                      <svg class="icon"><use href="#eye"></use></svg>
                  </button>
              </div>

              <!-- Botones -->
              <div class="modal-buttons admin-modal">
                  <button id="cancel-changes-button" class="close-button">
                      CANCELAR
                  </button>
                  <button id="save-changes-button" class="save-button-modal">
                      MODIFICAR CONTRASEÑA
                  </button>
              </div>
      
          </div>
      </div>
      
      <!-- Modal de éxito (reutiliza el mismo estilo que el perfil) -->
      <div id="password-success-modal" class="modal hidden">
          <div class="modal-content modal-change-pass">
              <h1 class="modal-title">PSIC🧠GEST • CAMBIO EXITOSO ✓</h1>
              <div class="modal-message">Tu contraseña ha sido actualizada correctamente.</div>
              <button class="ok-button" id="btn-close-success-modal">ACEPTAR</button>
          </div>
      </div>

    <?php endif; ?>

    <!--=============== PACIENTE ===============-->
    <?php if ($role === "paciente"): ?>
      <!-- TARJETA CONTENEDORA -->
      <div class="home-card">
        <!-- BOTÓN DE ENLACE A FUNCIONALIDAD PRINCIPAL -->
        <a href="scheduling.php" class="main-function-button">
          <svg class="icon">
            <use href="#new-appointment"></use>
          </svg>
          AGENDAR<br />NUEVA CITA
        </a>

        <!-- LÍNEA VERTICAL DIVISORA -->
        <div class="line-v-home"></div>

        <!-- BOTÓN DE ENLACE A FUNCIONALIDAD PRINCIPAL -->
        <a href="appointments.php?tab=tab1" class="main-function-button">
          <svg class="icon">
            <use href="#appointments"></use>
          </svg>
          VER MIS<br />CITAS
        </a>
      </div>

      <!-- PIE DE PÁGINA CON ENLACE AL DIRECTORIO -->
      <footer class="directory-link">
        ¿NECESITAS EL CONTACTO DE ALGUNO DE NUESTROS PSICÓLOGOS?
        <a href="directory.php">ACCEDE AL DIRECTORIO.</a>
      </footer> 
    <?php endif; ?>
    
    <!--=============== PSICÓLOGO ===============-->
    <?php if ($role === "psicologo"): ?>
      <!-- TARJETA CONTENEDORA -->
      <div class="home-card">
        <!-- BOTÓN DE ENLACE A FUNCIONALIDAD PRINCIPAL -->
        <a href="agenda.php" class="main-function-button">
          <svg class="icon">
            <use href="#time"></use>
          </svg>
          VER MIS<br />HORARIOS
        </a>

        <!-- LÍNEA VERTICAL DIVISORA -->
        <div class="line-v-home"></div>

        <!-- BOTÓN DE ENLACE A FUNCIONALIDAD PRINCIPAL -->
        <a href="patients.php?tab=tab1" class="main-function-button">
          <svg class="icon">
            <use href="#patients"></use>
          </svg>
          VER MIS<br />PACIENTES
        </a>

        <!-- LÍNEA VERTICAL DIVISORA -->
        <div class="line-v-home"></div>

        <a href="appointments.php?tab=tab1" class="main-function-button">
          <svg class="icon">
            <use href="#appointments"></use>
          </svg>
          VER MIS<br />CITAS
        </a>
      </div>
    <?php endif; ?>
  </main>

  <!-- Script para mostrar el rol del usuario en el userRole -->
  <script>
    window.USER_ROLE = "<?= $_SESSION['role'] ?? 'usuario' ?>";
  </script>

  <script src="../assets/js/components/icons.js"></script>
  <script src="../assets/js/components/menu_sidebar.js"></script>
  <script src="../assets/js/components/header.js"></script>

  <script src="../assets/js/components/toggle_password.js"></script>
  <script src="../assets/js/auth/change_password.js"></script>
  <script src="../assets/js/auth/admin_password_modal.js"></script>
</body>
</html>