<?php

// ==========================================================================
// ARCHIVO: INTERFAZ DE LA AGENDA PROFESIONAL / HORARIOS
// ==========================================================================

// Middleware de seguridad y control de accesos
require_once("../php/auth/auth.php");
require_once("../php/auth/permissions.php");

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
  <link rel="icon" href="../assets/img/icon.ico" type="image/x-icon" />

  <link rel="stylesheet" href="../assets/css/base/main.css" />
</head>

<body>
  <!-- HEADER -->
  <div id="header-container"></div>
  <!-- CONTENIDO PRINCIPAL -->
    <main class="main-content">
        <!-- BARRA SUPERIOR -->
        <div class="top-bar"> 
            <!-- Titulo de la página -->
            <h2 class="top-bar-title"> 
                <svg class="icon-title">
                    <use href="#time"></use>
                </svg>
                AGENDA PROFESIONAL / HORARIOS
            </h2>
            <!-- Botón de retorno al inicio -->
            <a class="return-button" href="home.php" > 
                <svg class="icon">
                    <use href="#home"></use>
                </svg>
                VOLVER AL INICIO
            </a>
        </div>
        <!-- TARJETA CONTENEDORA -->
        <div class="agenda-card">
            <!-- Contenedor de las tarjetas de horarios -->
            <div class="availability-container">
                <!-- Contenedor de cada tarjeta con horarios -->
                <div class="horary-container">
                    <!-- Tarjeta con horarios habituales -->
                    <span class="horary-title">HORARIOS HABITUALES</span> 
                    
                    <div class="horary-card">
                        <div class="horary-group">
                            <svg class="icon"><use href="#calendar-day"></use></svg><strong>LUNES, MIÉRCOLES, JUEVES Y VIERNES</strong><br>
                            <svg class="icon"><use href="#time"></use></svg>08:00 A.M. a 12:00 P.M. | 02:00 P.M. a 05:00 P.M.<br>
                        </div>  
                        
                        <div class="horary-group">
                            <svg class="icon"><use href="#calendar-day"></use></svg><strong>MARTES</strong><br>
                            <svg class="icon"><use href="#time"></use></svg>10:00 A.M. a 02:00 P.M.<br>
                        </div> 

                        <div class="horary-group">
                            <svg class="icon"><use href="#calendar-day"></use></svg><strong>SÁBADO Y DOMINGO</strong><br>
                            <svg class="icon"><use href="#time"></use></svg>No disponible.<br>
                        </div>      
                    </div>  
                </div>    

                <!-- Contenedor de cada tarjeta con horarios -->
                <div class="horary-container">
                    <!-- Tarjeta con horarios especiales -->
                    <span class="horary-title">FECHAS ESPECIALES</span> 
                    
                    <div class="horary-card">
                        <div class="horary-group">
                            <svg class="icon"><use href="#calendar-day"></use></svg><strong>VIERNES, 01 DE MAYO DE 2026</strong><br>
                            <svg class="icon"><use href="#time"></use></svg>No disponible.<br>
                        </div>  
                        
                        <div class="horary-group">
                            <svg class="icon"><use href="#calendar-day"></use></svg><strong>MARTES</strong><br>
                            <svg class="icon"><use href="#time"></use></svg>10:00 A.M. a 02:00 P.M.<br>
                        </div> 

                        <div class="horary-group">
                            <svg class="icon"><use href="#calendar-day"></use></svg><strong>SÁBADO Y DOMINGO</strong><br>
                            <svg class="icon"><use href="#time"></use></svg>No disponible.<br>
                        </div>      
                    </div>  
                </div>    
            </div>
            <!-- Línea horizontal divisora -->
            <hr class="line-h-agenda">

            <button class="edit-agenda-button" id="">
                <svg class="icon"><use href="#edit"></use></svg>
                EDITAR HORARIOS
            </button>
        </div>
    </main>

    <!-- Script para mostrar el rol del usuario en el userRole -->
    <script>
        window.USER_ROLE = "<?= $_SESSION['role'] ?? 'usuario' ?>";
    </script>

    <script src="../assets/js/components/icons.js"></script>
    <script src="../assets/js/components/menu_sidebar.js"></script>
    <script src="../assets/js/components/header.js"></script>
</body>
</html>
