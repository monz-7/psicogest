<?php

// ==========================================================================
// ARCHIVO: INTERFAZ DE PRÓXIMAS CITAS E HISTORIAL (APPOINTMENTS)
// ==========================================================================

// Middleware de seguridad y control de accesos
require_once("../php/auth.php");
require_once("../php/permissions.php");

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

    <title>PsicoGest | Próximas citas</title>
    <link rel="icon" href="../assets/icon.ico" type="image/x-icon">

    <link rel="stylesheet" href="../assets/css/main.css">
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
                    <use href="#appointments"></use>
                </svg>
                MIS CITAS
            </h2>
            <!-- Botón de retorno al inicio -->
            <a href="home.php" class="return-button">
                <svg class="icon">
                    <use href="#home"></use>
                </svg>
                VOLVER AL INICIO
            </a>
        </div>

        <!-- TARJETA CONTENEDORA -->
        <div class="card-appointments">
            
            <!-- Selector de pestañas (Tabs) -->
            <div class="tabs">
                <button class="tab-btn active" data-tab="tab1">PRÓXIMAS CITAS</button>
                <button class="tab-btn" data-tab="tab2">HISTORIAL DE CITAS</button>
            </div>

            <!-- Contenedor unificado de contenido -->
            <div class="tab-container">

                <!-- PESTAÑA 1: Próximas Citas (Activa por defecto) -->
                <div class="tab-content active" id="tab1">
                    
                    <!-- Tarjeta de cita (ejemplo estático) -->
                    <div class="appmnt-container">
                        <!--=============== PACIENTE ===============-->
                        <?php if ($role === "paciente"): ?>
                            <p>
                                <svg class="icon"><use href="#calendar-day"></use></svg>MIÉRCOLES, 27 DE MAYO DE 2026<br>
                                <svg class="icon"><use href="#time"></use></svg>09:30 A.M.<br>
                                <svg class="icon"><use href="#human-brain"></use></svg>SANTIAGO BOTERO CALLE<br>
                                <svg class="icon"><use href="#check-box"></use></svg>PRESENCIAL<br>
                                <svg class="icon"><use href="#location"></use></svg>CALLE 10 #43A-25, TORRE MÉDICA, CONSULTORIO 502 - EL POBLADO, MEDELLÍN, ANTIOQUIA<br>
                                <svg class="icon"><use href="#money-symbol"></use></svg>70.000<br>
                            </p>
                        <?php endif; ?>

                        <!--=============== PSICÓLOGO ===============-->
                        <?php if ($role === "psicologo"): ?>
                            <p>
                                [ DESARROLLO DE LA INTERFAZ PENDIENTE ]
                            </p>
                        <?php endif; ?>
                
                        <!-- Línea divisora -->
                        <hr class="divider-h-line">
                        
                        <!-- Acciones de gestión de cita -->
                        <div class="buttons-container">
                            <button class="appmts-button" id="view-ps-prof-button">VER PSICÓLOGO</button>
                            <button class="appmts-button" id="reschedule-button">REPROGRAMAR</button>
                            <button class="appmts-button" id="cancel-button">CANCELAR</button>
                        </div>
                    </div>

                    <!-- Mensajes informativos de estado -->
                    <span class="no-more-appmts-messsage">
                        <p>No tienes más citas.</p>
                    </span>      
                </div>

                <!-- PESTAÑA 2: Historial de citas pasadas -->
                <div class="tab-content" id="tab2">
                    
                    <!-- Filtro por mes -->
                    <div class="instruction">
                        <p>PUEDES FILTRAR POR MES AQUÍ:</p>
                    </div>
                    
                    <div class="month-filter">
                        <svg class="icon"><use href="#calendar-month"></use></svg>
                        <div class="filter-box">
                            
                            <!-- Dropdown personalizado para el filtro de meses -->
                            <div class="dropdown-filter" id="month-filter-dropdown">
                                <div class="dropdown-selected-month">
                                    <span class="dropdown-filter-text">ENERO</span>
                                    <svg class="arrow-icon"><use href="#down-arrow"></use></svg>
                                </div>
                                <ul class="dropdown-months-options">
                                    <li data-value="month">ENERO</li>
                                    <li data-value="month">FEBRERO</li>
                                    <li data-value="month">MARZO</li>
                                    <li data-value="month">ABRIL</li>
                                    <li data-value="month">MAYO</li>
                                    <li data-value="month">JUNIO</li>
                                    <li data-value="month">JULIO</li>
                                    <li data-value="month">AGOSTO</li>
                                    <li data-value="month">SEPTIEMBRE</li>
                                    <li data-value="month">OCTUBRE</li>
                                    <li data-value="month">NOVIEMBRE</li>
                                    <li data-value="month">DICIEMBRE</li>
                                </ul>
                            </div>

                        </div>
                    </div>

                    <hr class="divider-h-line">

                    <!-- REGISTROS DEL HISTORIAL (Ejemplos) -->
                    
                    <!-- Cita Historial 1 -->
                    <div class="appmnt-history-container" data-month="ENERO">
                        <div class="info-content">
                            <!--=============== PACIENTE ===============-->
                            <?php if ($role === "paciente"): ?>
                                <p>
                                    <svg class="icon"><use href="#calendar-day"></use></svg>LUNES, 12 DE ENERO DEL 2026<br>
                                    <svg class="icon"><use href="#time"></use></svg>09:30 A.M.<br>
                                    <svg class="icon"><use href="#human-brain"></use></svg>SANTIAGO BOTERO CALLE<br>
                                    <svg class="icon"><use href="#check-box"></use></svg>PRESENCIAL<br>
                                    <svg class="icon"><use href="#location"></use></svg>CALLE 10 #43A-25, TORRE MÉDICA, CONSULTORIO 502 - EL POBLADO, MEDELLÍN, ANTIOQUIA<br>
                                    <svg class="icon"><use href="#money-symbol"></use></svg>70.000<br>
                                </p>
                            <?php endif; ?>

                            <!--=============== PSICÓLOGO ===============-->
                            <?php if ($role === "psicologo"): ?>
                                <p>
                                    [ DESARROLLO DE LA INTERFAZ PENDIENTE ]
                                </p>
                            <?php endif; ?>
                            <!-- Línea divisora -->
                            <hr class="divider-v-line">
                            <!-- Estado de la cita -->
                            <span class="status" id="status-2">INCUMPLIDA</span>
                        </div>
                    </div>

                    <!-- Cita Historial 2 -->
                    <div class="appmnt-history-container" data-month="ENERO">
                        <div class="info-content">
                            <!--=============== PACIENTE ===============-->
                            <?php if ($role === "paciente"): ?>
                                <p>
                                    <svg class="icon"><use href="#calendar-day"></use></svg>MIÉRCOLES, 21 DE ENERO DEL 2026<br>
                                    <svg class="icon"><use href="#time"></use></svg>01:40 P.M.<br>
                                    <svg class="icon"><use href="#human-brain"></use></svg>AMELIA PEREZ MOLINA<br>
                                    <svg class="icon"><use href="#check-box"></use></svg>VIRTUAL<br>
                                    <svg class="icon"><use href="#money-symbol"></use></svg>70.000<br>
                                </p>
                            <?php endif; ?>

                            <!--=============== PSICÓLOGO ===============-->
                            <?php if ($role === "psicologo"): ?>
                                <p>
                                    [ DESARROLLO DE LA INTERFAZ PENDIENTE ]
                                </p>
                            <?php endif; ?>
                            <!-- Línea divisora -->
                            <hr class="divider-v-line">
                            <!-- Estado de la cita -->
                            <span class="status" id="status-1">CUMPLIDA</span>
                        </div>
                    </div>

                    <!-- Cita Historial 3 -->
                    <div class="appmnt-history-container" data-month="ENERO">
                        <div class="info-content">
                            <!--=============== PACIENTE ===============-->
                            <?php if ($role === "paciente"): ?>
                                <p>
                                    <svg class="icon"><use href="#calendar-day"></use></svg>MARTES, 26 DE ENERO DEL 2026<br>
                                    <svg class="icon"><use href="#time"></use></svg>04:00 P.M.<br>
                                    <svg class="icon"><use href="#human-brain"></use></svg>AMELIA PEREZ MOLINA<br>
                                    <svg class="icon"><use href="#check-box"></use></svg>VIRTUAL<br>
                                    <svg class="icon"><use href="#money-symbol"></use></svg>70.000<br>
                                </p>
                            <?php endif; ?>

                            <!--=============== PSICÓLOGO ===============-->
                            <?php if ($role === "psicologo"): ?>
                                <p>
                                    [ DESARROLLO DE LA INTERFAZ PENDIENTE ]
                                </p>
                            <?php endif; ?>
                            <!-- Línea divisora -->
                            <hr class="divider-v-line">
                            <!-- Estado de la cita -->
                            <span class="status" id="status-1">CUMPLIDA</span>
                        </div>
                    </div>

                    <!-- Mensajes dinámicos e informativos del historial -->
                    <span class="no-more-appmts-messsage" id="no-more-appmts-message">
                        <p>No tienes más citas.</p>
                    </span>    

                    <span class="no-appmts-message" id="no-appmts-message">
                        No tienes citas en el historial de este mes.
                    </span>

                </div> 
            </div> 
        </div>
    </main>

    <!-- Script para mostrar el rol del usuario en el userRole -->
    <script>
        window.USER_ROLE = "<?= $_SESSION['role'] ?? 'usuario' ?>";
    </script>

    <script src="../assets/js/icons.js"></script>
    <script src="../assets/js/header.js"></script>
    <script src="../assets/js/dropdowns.js"></script>
    <!-- Script específico para esta página -->
    <script src="../assets/js/appointments.js"></script>
</body>
</html>