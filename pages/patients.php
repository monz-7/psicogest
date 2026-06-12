<?php

// ==========================================================================
// ARCHIVO: INTERFAZ DE PACIENTES (VISTA DEL PSICÓLOGO)
// ==========================================================================

// Middleware de seguridad y control de accesos
require_once("../php/auth.php");
require_once("../php/permissions.php");

// Restricción de acceso: solo psicologos
requireRole("psicologo");

// Captura el rol activo del usuario en la sesión
$role = $_SESSION["role"] ?? "";

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  
  <title>PsicoGest | Mis pacientes</title>
  <link rel="icon" href="../assets/icon.ico" type="image/x-icon" />

  <link rel="stylesheet" href="../assets/css/main.css" />
</head>

<body>
  <!-- HEADER -->
  <div id="header-container"></div>
  <!-- CONTENIDO PRINCIPAL -->
    <main class="main-content">
        <!-- BARRA SUPERIOR -->
        <div class="top-bar"> 
            <!-- Titulo de la página -->
            <h2> 
                <svg class="icon-title">
                    <use href="#patients"></use>
                </svg>
                MIS PACIENTES
            </h2>
            <!-- Botón de retorno al inicio -->
            <a href="home.php" class="return-button"> 
                <svg class="icon">
                    <use xlink:href="#home"></use>
                </svg>
                VOLVER AL INICIO
            </a>
        </div>
        <!-- TARJETA CONTENEDORA -->
        <div class="card-patients-view">

            <p style="text-align: center;">
                [ DESARROLLO DE LA INTERFAZ PENDIENTE ]<br>
                <br>
                VAN DOS PESTAÑAS (COMO APPOINTMENTS) QUE CONTIENEN:<br> 
                PACIENTES Y REGISTRO DE ASISTENCIA
            </p>

        </div>
    </main>

    <!-- Script para mostrar el rol del usuario en el userRole -->
    <script>
        window.USER_ROLE = "<?= $_SESSION['role'] ?? 'usuario' ?>";
    </script>

    <script src="../assets/js/icons.js"></script>
    <script src="../assets/js/header.js"></script>
</body>
</html>
