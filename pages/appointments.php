<?php

// ==========================================================================
// ARCHIVO: INTERFAZ DE PRÓXIMAS CITAS E HISTORIAL (APPOINTMENTS)
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

    <title>PsicoGest | Próximas citas</title>
    <link rel="icon" href="../assets/img/icon.ico" type="image/x-icon">

    <link rel="stylesheet" href="../assets/css/base/main.css">
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
        <div class="appointments-card">
            
            <!-- Selector de pestañas (Tabs) -->
            <div class="tabs">
                <button class="tab-button" data-tab="tab1">PRÓXIMAS CITAS</button>
                <button class="tab-button" data-tab="tab2">HISTORIAL DE CITAS</button>
            </div>

            <!-- Contenedor unificado de contenido -->
            <div class="tab-container">

                <!-- PESTAÑA 1: Próximas citas -->
                <div class="tab-content" id="tab1">
                    
                    <!-- Tarjeta de cita (ejemplo estático) -->
                    <div class="appointment-container">
                        <p>
                            <svg class="icon"><use href="#calendar-day"></use></svg>MIÉRCOLES, 27 DE MAYO DE 2026<br>
                            <svg class="icon"><use href="#time"></use></svg>09:30 A.M.<br>
                            <!--=============== PACIENTE ===============-->
                            <?php if ($role === "paciente"): ?>
                                <svg class="icon"><use href="#human-brain"></use></svg>SANTIAGO BOTERO CALLE<br>
                            <?php endif; ?>
                            <!--=============== PSICÓLOGO ===============-->
                            <?php if ($role === "psicologo"): ?>
                                <svg class="icon"><use href="#patients"></use></svg>MARIANA ROBLEDO SUAZA<br>
                            <?php endif; ?>
                            <svg class="icon"><use href="#check-box"></use></svg>PRESENCIAL<br>
                            <svg class="icon"><use href="#location"></use></svg>CL 10 #43A - 25, TORRE MÉDICA / CONSULTORIO 502 - EL POBLADO, MEDELLÍN<br>
                            <svg class="icon"><use href="#money-symbol"></use></svg>70.000<br>
                        </p>
                
                        <!-- Línea divisora -->
                        <hr class="line-h-appointments">
                        
                        <!-- Acciones de gestión de cita -->
                        <div class="buttons-container">
                            
                            <button class="appointments-button" id="view-ps-prof-button">
                                <?php if ($role === "paciente"): ?>VER PSICÓLOGO<?php endif; ?>
                                <?php if ($role === "psicologo"): ?>VER PACIENTE<?php endif; ?>
                            </button>
                            <button class="appointments-button" id="reschedule-button">REPROGRAMAR</button>
                            <button class="appointments-button" id="cancel-btn">CANCELAR</button>
                        </div>
                    </div>

                    <!-- Mensajes informativos de estado -->
                    <span class="appointments-message">
                        <p>No tienes más citas.</p>
                    </span>      
                </div>

                <!-- PESTAÑA 2: Historial de citas pasadas -->
                <div class="tab-content" id="tab2">
                    
                    <!-- Filtro por mes -->
                    <div class="history-instruction">
                        <p>
                            <?php if ($role === "paciente"): ?>
                                PUEDES FILTRAR POR MES AQUÍ:
                            <?php endif; ?>

                            <?php if ($role === "psicologo"): ?>
                                PUEDES FILTRAR POR MES Y/O NOMBRE(S) O APELLIDOS DEL PACIENTE AQUÍ:
                            <?php endif; ?>
                        </p>
                    </div>
                    
                    <div class="filters-container">
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

                        <?php if ($role === "psicologo"): ?>
                            <div class="filter-bar">
                                <svg class="filter-icon">
                                    <use href="#search"></use>
                                </svg>
                                <input
                                    type="text"
                                    class="filter-input"
                                    data-target=""
                                    data-columns="0,1"
                                    data-message=""
                                    placeholder="BUSCAR POR NOMBRE O APELLIDO"
                                    autocomplete="off"
                                >
                            </div>
                        <?php endif; ?>  
                    </div>

                    <hr class="line-h-appointments">

                    <!-- REGISTROS DEL HISTORIAL (Ejemplos) -->
                    
                    <!-- Cita Historial 1 -->
                    <div class="history-container" data-month="ENERO">
                        <div class="history-content">  
                            <p>
                                <svg class="icon"><use href="#calendar-day"></use></svg>LUNES, 12 DE ENERO DEL 2026<br>
                                <svg class="icon"><use href="#time"></use></svg>09:30 A.M.<br>
                                <!--=============== PACIENTE ===============-->
                                <?php if ($role === "paciente"): ?>
                                    <svg class="icon"><use href="#human-brain"></use></svg>SANTIAGO BOTERO CALLE<br>
                                <?php endif; ?>
                                <!--=============== PSICÓLOGO ===============-->
                                <?php if ($role === "psicologo"): ?>
                                    <svg class="icon"><use href="#patients"></use></svg>MARIANA ROBLEDO SUAZA<br>
                                <?php endif; ?>
                                <svg class="icon"><use href="#check-box"></use></svg>PRESENCIAL<br>
                                <svg class="icon"><use href="#location"></use></svg>CL 10 #43A - 25, TORRE MÉDICA / CONSULTORIO 502 - EL POBLADO, MEDELLÍN<br>
                                <svg class="icon"><use href="#money-symbol"></use></svg>70.000<br>
                            </p>
                            <!-- Línea divisora -->
                            <hr class="line-v-history">
                            <!-- Estado de la cita -->
                            <span class="appointment-status" id="status-2">INCUMPLIDA</span>
                        </div>
                    </div>

                    <!-- Cita Historial 2 -->
                    <div class="history-container" data-month="ENERO">
                        <div class="history-content">
                            <p>
                                <svg class="icon"><use href="#calendar-day"></use></svg>MIÉRCOLES, 21 DE ENERO DEL 2026<br>
                                <svg class="icon"><use href="#time"></use></svg>01:40 P.M.<br>
                                <!--=============== PACIENTE ===============-->
                                <?php if ($role === "paciente"): ?>
                                    <svg class="icon"><use href="#human-brain"></use></svg>AMELIA PEREZ MOLINA<br>
                                <?php endif; ?>
                                <!--=============== PSICÓLOGO ===============-->
                                <?php if ($role === "psicologo"): ?>
                                    <svg class="icon"><use href="#patients"></use></svg>JUAN MONTERO LÓPEZ<br>
                                <?php endif; ?>
                                <svg class="icon"><use href="#check-box"></use></svg>VIRTUAL<br>
                                <svg class="icon"><use href="#money-symbol"></use></svg>70.000<br>
                            </p>

                            <!-- Línea divisora -->
                            <hr class="line-v-history">
                            <!-- Estado de la cita -->
                            <span class="appointment-status" id="status-1">CUMPLIDA</span>
                        </div>
                    </div>

                    <!-- Cita Historial 3 -->
                    <div class="history-container" data-month="ENERO">
                        <div class="history-content">
                            <p>
                                
                                <svg class="icon"><use href="#calendar-day"></use></svg>MARTES, 26 DE ENERO DEL 2026<br>
                                <svg class="icon"><use href="#time"></use></svg>04:00 P.M.<br>
                                <!--=============== PACIENTE ===============-->
                                <?php if ($role === "paciente"): ?>
                                    <svg class="icon"><use href="#human-brain"></use></svg>AMELIA PEREZ MOLINA<br>
                                <?php endif; ?>
                                <!--=============== PSICÓLOGO ===============-->
                                <?php if ($role === "psicologo"): ?>
                                    <svg class="icon"><use href="#patients"></use></svg>ANTONELLA MARÍA ROJAS CABRERA<br>
                                <?php endif; ?>
                                <svg class="icon"><use href="#check-box"></use></svg>VIRTUAL<br>
                                <svg class="icon"><use href="#money-symbol"></use></svg>70.000<br>
                            </p>
                            
                            <!-- Línea divisora -->
                            <hr class="line-v-history">
                            <!-- Estado de la cita -->
                            <span class="appointment-status" id="status-1">CUMPLIDA</span>
                        </div>
                    </div>

                    <!-- Mensajes dinámicos e informativos del historial -->
                    <span class="appointments-message" id="appointments-message">
                        No tienes más citas en el historial.
                    </span>    

                    <span class="history-message" id="history-message">
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

    <script src="../assets/js/components/icons.js"></script>
    <script src="../assets/js/components/menu_sidebar.js"></script>
    <script src="../assets/js/components/header.js"></script>
    <script src="../assets/js/components/dropdowns.js"></script>
    <!-- Script específico para esta página -->
    <script src="../assets/js/components/tabs_control.js"></script>
    <script src="../assets/js/pages/appointments.js"></script>
</body>
</html>