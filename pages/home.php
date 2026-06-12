<?php

// ==========================================================================
// ARCHIVO: INTERFAZ DEL INICIO (HOME)
// Distribuye las funcionalidades del sistema según el rol del usuario
// ==========================================================================

// Middleware de seguridad y control de accesos
require_once("../php/auth.php");
require_once("../php/permissions.php");

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
  <link rel="icon" href="../assets/icon.ico" type="image/x-icon" />

  <link rel="stylesheet" href="../assets/css/main.css" />
</head>

<body>
  <!-- HEADER -->
  <div id="header-container"></div>
  <!-- CONTENIDO PRINCIPAL -->
  <main class="main-content">
    
    <!-- Mensaje de bienvenida -->
    <h2>
      BIENVENIDO(A) <?= mb_strtoupper(htmlspecialchars($_SESSION["names"]), 'UTF-8'); ?>,
      ¿QUÉ DESEAS REALIZAR EL DÍA DE HOY?
    </h2>
    

    <!--=============== ADMIN ===============-->
    <?php if ($role === "admin"): ?>
      <!-- TARJETA CONTENEDORA -->
      <div class="card-home">
        <!-- BOTÓN DE ENLACE A FUNCIONALIDAD PRINCIPAL -->
        <a href="admin_patients.php" class="card-button">
          <svg class="icon">
            <use href="#patients"></use>
          </svg>
          GESTIONAR<br>PACIENTES
        </a>
        
        <!-- LÍNEA VERTICAL DIVISORA -->
        <div class="divider"></div>
        
        <!-- BOTÓN DE ENLACE A FUNCIONALIDAD PRINCIPAL -->
        <a href="admin_psychologists.php" class="card-button">
          <svg class="icon">
            <use href="#human-brain"></use>
          </svg>
          GESTIONAR<br>PSICÓLOGOS
        </a>
      </div>
    <?php endif; ?>

    <!--=============== PACIENTE ===============-->
    <?php if ($role === "paciente"): ?>
      <!-- TARJETA CONTENEDORA -->
      <div class="card-home">
        <!-- BOTÓN DE ENLACE A FUNCIONALIDAD PRINCIPAL -->
        <a href="scheduling.php" class="card-button">
          <svg class="icon">
            <use href="#new-appointment"></use>
          </svg>
          AGENDAR<br />NUEVA CITA
        </a>

        <!-- LÍNEA VERTICAL DIVISORA -->
        <div class="divider"></div>

        <!-- BOTÓN DE ENLACE A FUNCIONALIDAD PRINCIPAL -->
        <a href="appointments.php" class="card-button">
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
      <div class="card-home">
        <!-- BOTÓN DE ENLACE A FUNCIONALIDAD PRINCIPAL -->
        <a href="" class="card-button">
          <svg class="icon">
            <use href="#time"></use>
          </svg>
          VER MIS<br />HORARIOS
        </a>

        <!-- LÍNEA VERTICAL DIVISORA -->
        <div class="divider"></div>

        <!-- BOTÓN DE ENLACE A FUNCIONALIDAD PRINCIPAL -->
        <a href="" class="card-button">
          <svg class="icon">
            <use href="#patients"></use>
          </svg>
          VER MIS<br />PACIENTES
        </a>

        <!-- LÍNEA VERTICAL DIVISORA -->
        <div class="divider"></div>

        <a href="" class="card-button">
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

  <script src="../assets/js/icons.js"></script>
  <script src="../assets/js/header.js"></script>
</body>
</html>