<?php

// ==========================================================================
// ARCHIVO: INTERFAZ DE PÁGINA DE NOTIFICACIONES (HARDCODEADA)
// ==========================================================================

// Middleware de seguridad y control de accesos
require_once("../php/auth/auth.php");
require_once("../php/auth/permissions.php");

// Middleware de seguridad y control de accesos
requireRoles(["paciente", "psicologo"]);

// Captura el rol activo del usuario en la sesión
$role = $_SESSION["role"] ?? "";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PsicoGest | Notificaciones</title>
    <link rel ="icon" href="../assets/img/icon.ico" type="image/x-icon">
    
    <link rel="stylesheet" href="../assets/css/base/main.css">
</head>

<body>

    <!-- HEADER -->
    <div id="header-container"></div> 

    <!-- CONTENIDO PRINCIPAL -->
    <main class ="main-content"> 
        <!-- BARRA SUPERIOR -->
        <div class="top-bar"> 
            <!-- Titulo de la página -->
            <h2 class="top-bar-title"> 
                <svg class="icon-title">
                    <use href="#bell"></use>
                </svg>
                NOTIFICACIONES
            </h2>
            <!-- Botón de retorno al inicio -->
            <a href="home.php" class="return-button"> 
                <svg class="icon">
                    <use href="#home"></use>
                </svg>
                VOLVER AL INICIO
            </a>
        </div>

        <!-- Botón para borrar todas las notificaciones -->
        <button class="delete-all-button" id="delete-all-button"> 
            <svg class="icon">
                <use href="#delete-all"></use>
            </svg>
            LIMPIAR TODAS
        </button>

        <!-- TARJETA CONTENEDORA DE CADA NOTIFICACIÓN -->
        <div class="notification-card"> 
            <!-- Detalles de la notificación -->
            <div class="notification-detail" data-id="1"> 
                <!-- Contenido de la notificación -->
                <svg><use href="#notification"></use></svg>
                <div class="notification-content-card"> 
                    
                    <p>
                        Agendaste una cita PRESENCIAL para el día 20/05/2026 a las 11:00 A.M. 
                        con el(la) psicólogo(a) SANTIAGO BOTERO CALLE.
                    </p>
                    <!-- Tiempo de las notificaciones y botón de limpieza individual -->
                    <div class="notification-actions-card"> 
                        <small>Hace 1 hora</small>
                        <p>  -  </p>
                        <button class="delete-one">Limpiar</button>
                    </div>

                </div>

            </div>
        </div>
        <!-- TARJETA CONTENEDORA DE CADA NOTIFICACIÓN -->
        <div class="notification-card"> 
            <!-- Detalles de la notificación -->
            <div class="notification-detail" data-id="2"> 
                <!-- Contenido de la notificacion -->
                <svg><use href="#notification"></use></svg>
                <div class="notification-content-card"> 
                    
                    <p>
                        Tu cita PRESENCIAL para el día 15/05/2026 a las 08:30 A.M. 
                        con el(la) psicólogo(a) SANTIAGO BOTERO CALLE ha sido CANCELADA.
                    </p>
                    <!-- Tiempo de las notificaciones y botón de limpieza individual -->
                    <div class="notification-actions-card"> 
                        <small>Hace 2 horas</small>
                        <p>  -  </p>
                        <button class="delete-one">Limpiar</button>
                    </div>

                </div>

            </div>
        </div>
        <!-- Mensaje que indica que ya no hay más notificaciones -->
        <div class="notifications-page-message"> 
            <p>No tienes más notificaciones.</p>
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