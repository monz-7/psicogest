<?php

// ==========================================================================
// ARCHIVO: INTERFAZ DE LA AGENDA PROFESIONAL / HORARIOS
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
  
  <title>PsicoGest | Agenda profesional</title>
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
                    <use href="#time"></use>
                </svg>
                AGENDA PROFESIONAL / HORARIOS
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
        <div class="card-agenda">

            <p>
                [ DESARROLLO DE LA INTERFAZ PENDIENTE ]
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
